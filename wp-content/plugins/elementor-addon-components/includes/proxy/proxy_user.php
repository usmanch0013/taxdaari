<?php

/*======================================================================================================
* Description: Collecte les médias d'un user account
* Ce module est appelé depuis la requête AJAX 'ajaxCallFeed' de 'eac-components.js'
*
* Utilisation des méthodes de la class 'Endpoints'
* Méthode 'setAccountMediasCount' fixe le nombre de posts à retourner
* Méthode 'getUserIdByName' collecte le profile du user account
* Méthode 'getAccountDataById' collecte les posts avec l'ID du profile du user account
* Méthode 'setHeaderToken' modifie les propriétés de l'objet Endpoints
* Méthode 'setDebug' active le debugging de l'objet Endpoints
* Méthode 'getTaggedPostsUserAccount' les posts tagués par un user (Activé dans les réglages)
* Méthode 'getSuggestedUserAccount' les suggestions pour un user account  (Activé dans les réglages)
*
* @param {string} $_REQUEST['instagram'] le libellé d'un user account
* @param {string} $_REQUEST['token'] csrf token de la session
* @param {string} $_REQUEST['rollout'] instagram-ajax token de la session
* @param {string} $_REQUEST['debug'] debugging
* @param {string} $_REQUEST['id'] identifiant du user account pour la pagination
* @param {string} $_REQUEST['cursor'] cursor pour la pagination
* @return Les posts encodés JSON
* @since 1.3.0
* @since 1.3.1	Gestion des stories
* @since 1.4.0	Gestion des tokens de l'objet Endpoints
* @since 1.4.2	Gestion des comptes suggérés d'un compte utilisateur
* @since 1.4.3	Gestion des posts partagés d'un compte utilisateur
*				Gestion des stories
* @since 1.4.4	Gestion des tagged users de chaque post
* @since 1.4.5	Gestion des coordonnées géographiques (Location) de chaque post
* @since 1.4.9	Implémente la pagination
* @since 1.5.2  Gestion des Hashtags associés
* @since 1.6.0	Évolution API Instagram. Ne gère plus le profil
* @since 1.6.2	Suppression de la méthode 'setUserProfile'
*=======================================================================================================*/

namespace EACCustomWidgets\Proxy;

//require_once('../../../../../wp-load.php');
$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
require_once($parse_uri[0] . 'wp-load.php');

if(!defined('ABSPATH')) exit; // Exit if accessed directly

if(!isset($_REQUEST['instagram'])) { exit; }

require_once __DIR__ . '/lib/Endpoints.php';
use EACCustomWidgets\Proxy\Lib\Endpoints;

// Construction de l'objet Endpoints
$endpoints = new Endpoints();

// @since 1.4.0 Même session, on valorise les tokens
if(isset($_REQUEST['token']) && isset($_REQUEST['rollout'])) {
	$endpoints->setHeaderToken($_REQUEST['token'], $_REQUEST['rollout']);
}

// Debug or not ?
if(!empty($_REQUEST['debug']) && $_REQUEST['debug'] === 'vrai') {
	$endpoints->setDebug();
}

// Caption length
define("CAPTION_LENGTH", "200");
// Les statistiques sont sur les 12 derniers posts
define("HEAD_MAX_POSTS", "12");
// Nombre de secondes par jour
define("SECONDS_IN_DAY", "86400");
// Poids des mentions
define("mentionWeight", "1");
// Poids des hashtags
define("hashtagWeight", "3");

// Les posts à afficher
$items = array();

/**
 * @param ($_REQUEST['id'], $_REQUEST['cursor']) Id du username et cursor
 * @since 1.4.9 Pagination
 */
if(!empty($_REQUEST['id']) && !empty($_REQUEST['cursor'])) {
	// Nombre de posts à retourner pour la pagination 36
	$endpoints->setAccountMediasCount(36);
	if(!setUserMedias($_REQUEST['id'], $_REQUEST['cursor'])) { exit; }
	if(!empty($items['mediasMentions'])) { unset($items['mediasMentions']); }
	if(!empty($items['mediasHashtags'])) { unset($items['mediasHashtags']); }
} else {
	// Nombre de posts à retourner par requête. Max : 50
	$endpoints->setAccountMediasCount(50);
	if(!setUserMedias($_REQUEST['instagram'])) { exit; }
	
	calcEngagement();
	calcDatePublication();
	convertHeaderInfos();
	setJqCloud();
}

unset($endpoints);
echo json_encode($items);

// Lecture et valorisation des médias
function setUserMedias($id, $cursor = '') {
	global $endpoints;
	global $items;
	$arrayUserMedias = array();
	
	// Recherche des posts par l'ID d'un username
	if(!$arrayUserMedias = $endpoints->getAccountDataById($id, $cursor)) {
		echo __("Rien à afficher...", "eac-components");
		return false;
	}
	
	// Nombre de posts
	$items['profile']['publication'] = $endpoints->convertNumber($arrayUserMedias['count'], 1);
	
	// Page supplémentaire et chaine du curseur de la dernière photo
	$items['profile']['has_next_page'] = $arrayUserMedias['page_info']['has_next_page'];
	$items['profile']['end_cursor'] = $arrayUserMedias['page_info']['end_cursor'];
	
	// @since 1.4.9 Pagination. Initialisation des variables
	if(!isset($items['profile']['id'])) { $items['profile']['id'] = $id; }
	if(!isset($items['profile']['headTotalLikes'])) { $items['profile']['headTotalLikes'] = 0; }
	if(!isset($items['profile']['headTotalComments'])) { $items['profile']['headTotalComments'] = 0; }
	if(!isset($items['profile']['headDateDebut'])) { $items['profile']['headDateDebut'] = 0; }
	if(!isset($items['profile']['headDateFin'])) { $items['profile']['headDateFin'] = 0; }
	if(!isset($items['profile']['headTotalMentions'])) { $items['profile']['headTotalMentions'] = 0; }
	if(!isset($items['profile']['headTotalHashtags'])) { $items['profile']['headTotalHashtags'] = 0; }
	if(!isset($items['profile']['headTotalVideos'])) { $items['profile']['headTotalVideos'] = 0; }
	
	// Boucle sur tous les medias
	foreach($arrayUserMedias['edges'] as $key => $edge) {
		$items['medias'][$key]['nodeId'] = $edge['node']['id'];
		$items['medias'][$key]['nodeType'] = $edge['node']['__typename'];
		$items['medias'][$key]['linkNode'] = $edge['node']['shortcode'];
		
		if(isset($edge['node']['edge_liked_by'])) { // by query_id
			$items['medias'][$key]['likeCount'] =  isset($edge['node']['edge_liked_by']['count']) ? $endpoints->convertNumber($edge['node']['edge_liked_by']['count'], 0) : 0;
			$items['medias'][$key]['likeCount_sort'] = isset($edge['node']['edge_liked_by']['count']) ? $edge['node']['edge_liked_by']['count'] : 0; // Pour le tri sur les likes
			$items['profile']['headTotalLikes'] += ($key < HEAD_MAX_POSTS && isset($edge['node']['edge_liked_by']['count'])) ? intval($edge['node']['edge_liked_by']['count']) : 0;
		} else { // by query_hash
			$items['medias'][$key]['likeCount'] =  isset($edge['node']['edge_media_preview_like']['count']) ? $endpoints->convertNumber($edge['node']['edge_media_preview_like']['count'], 0) : 0;
			$items['medias'][$key]['likeCount_sort'] = isset($edge['node']['edge_media_preview_like']['count']) ? $edge['node']['edge_media_preview_like']['count'] : 0; // Pour le tri sur les likes
			$items['profile']['headTotalLikes'] += ($key < HEAD_MAX_POSTS && isset($edge['node']['edge_media_preview_like']['count'])) ? intval($edge['node']['edge_media_preview_like']['count']) : 0;
		}
		
		$items['medias'][$key]['commentCount'] = isset($edge['node']['edge_media_to_comment']['count']) ? $endpoints->convertNumber($edge['node']['edge_media_to_comment']['count'], 0) : 0;
		$items['medias'][$key]['commentCount_sort'] = isset($edge['node']['edge_media_to_comment']['count']) ? $edge['node']['edge_media_to_comment']['count'] : 0; // Pour le tri sur les commentaires
		$items['profile']['headTotalComments'] += ($key < HEAD_MAX_POSTS && isset($edge['node']['edge_media_to_comment']['count'])) ? intval($edge['node']['edge_media_to_comment']['count']) : 0;
		
		$items['medias'][$key]['caption'] = isset($edge['node']['edge_media_to_caption']['edges'][0]['node']['text']) ? $edge['node']['edge_media_to_caption']['edges'][0]['node']['text'] : '';
		$items['medias'][$key]['ownerId'] = $edge['node']['owner']['id'];
		$items['medias'][$key]['username'] = null;
		
		$items['medias'][$key]['image240px'] = $edge['node']['thumbnail_resources'][1]['src'];
		$items['medias'][$key]['image480px'] = $edge['node']['thumbnail_resources'][3]['src'];
		$items['medias'][$key]['img_med'] = $edge['node']['thumbnail_resources'][4]['src'];
		$items['medias'][$key]['thumbnail_src'] = $edge['node']['thumbnail_src'];
		$items['medias'][$key]['img_standard'] = $edge['node']['display_url'];
		
		$items['medias'][$key]['video'] = $edge['node']['is_video'];
		if($items['medias'][$key]['video']) {
			$items['medias'][$key]['video_view_count'] = isset($edge['node']['video_view_count']) ? $endpoints->convertNumber($edge['node']['video_view_count'], 0) : 1;
			$items['medias'][$key]['video_url'] = isset($edge['node']['video_url']) ? $edge['node']['video_url'] : '';
			$items['profile']['headTotalVideos'] += 1;
		}
		
		$items['medias'][$key]['update'] = $edge['node']['taken_at_timestamp'];
		// Date de publication en nombre de jours
		$nbJour = round(((time() - $edge['node']['taken_at_timestamp']) / SECONDS_IN_DAY), 0) . __(' jours', 'eac-components');
		$items['medias'][$key]['updateEnJours'] = $nbJour;
		
		// Enregistre les dates de début et de fin de l'extraction pour les 12 derniers posts
		if($key < HEAD_MAX_POSTS) {
			if($edge['node']['taken_at_timestamp'] > $items['profile']['headDateFin']) {
				$items['profile']['headDateFin'] = $edge['node']['taken_at_timestamp'];
			}
			if($edge['node']['taken_at_timestamp'] < $items['profile']['headDateFin']) {
				$items['profile']['headDateDebut'] = $edge['node']['taken_at_timestamp'];
			}
		}
		
		// Compte et affecte les mentions
		preg_match_all("/@[^\s@#:)’\"”,;\]!'…]*/u", $items['medias'][$key]['caption'], $resultsm);
		$resultsm[0] = preg_replace("/\.$/", "", $resultsm[0]);
		$uniqMentions = array_unique($resultsm[0]); // keyword unique pour chaque post
		sort($uniqMentions, SORT_NATURAL | SORT_FLAG_CASE);
		
		$items['medias'][$key]['mentionCount'] = count($uniqMentions);
		$items['medias'][$key]['mentionList'] = implode(',', $uniqMentions);
		$items['profile']['headTotalMentions'] += count($resultsm[0]);
		
		foreach($resultsm[0] as $result) {
			if(!isset($items['mediasMentions'][$result])) {
				$items['mediasMentions'][$result]['text'] = $result;
				$items['mediasMentions'][$result]['weight'] = intval(1);
				$items['mediasMentions'][$result]['html']['title'] = intval(1);
			} else {
				$items['mediasMentions'][$result]['weight'] += intval(1);
				$items['mediasMentions'][$result]['html']['title'] += intval(1);
			}
		}
		
		// @since 1.5.2 Compte et affecte les hashtags
		preg_match_all("/#[^\s@#:)’\"”,;\]!'…]*/u", $items['medias'][$key]['caption'], $resultsh);
		$resultsh[0] = preg_replace("/\.$/", "", $resultsh[0]);
		$uniqHashtags = array_unique($resultsh[0]); // keyword unique pour chaque post
		sort($uniqHashtags, SORT_NATURAL | SORT_FLAG_CASE);
		
		$items['medias'][$key]['hashtagCount'] = count($uniqHashtags);
		$items['medias'][$key]['hashtagList'] = implode(',', $uniqHashtags);
		$items['profile']['headTotalHashtags'] += count($resultsh[0]);
		
		foreach($resultsh[0] as $result) {
			if(!isset($items['mediasHashtags'][$result])) {
				$items['mediasHashtags'][$result]['text'] = $result;
				$items['mediasHashtags'][$result]['weight'] = intval(1);
				$items['mediasHashtags'][$result]['html']['title'] = intval(1);
			} else {
				$items['mediasHashtags'][$result]['weight'] += intval(1);
				$items['mediasHashtags'][$result]['html']['title'] += intval(1);
			}
		}
		
		// On ne prend que 200 caractères dans le caption à cause du sessionStorage
		if(!empty($items['medias'][$key]['caption']) && mb_strlen($items['medias'][$key]['caption'], 'UTF-8') > CAPTION_LENGTH) {
			$items['medias'][$key]['caption'] = mb_substr($items['medias'][$key]['caption'], 0, CAPTION_LENGTH, 'UTF-8');
		}
		
		// @since 1.4.4 Tagged user
		if(!empty($edge['node']['edge_media_to_tagged_user']['edges'])) {
			$tmpTU = array();
			foreach($edge['node']['edge_media_to_tagged_user']['edges'] as $edge_tu) {
				array_push($tmpTU, '@' . $edge_tu['node']['user']['username']);
			}
			sort($tmpTU, SORT_NATURAL | SORT_FLAG_CASE);
			$items['medias'][$key]['edge_media_to_tagged_user'] = implode(',', $tmpTU);
		}
		
		// @since 1.4.5 les coordonnées du lieu (Location) sont renseignées
		if(!empty($edge['node']['location']) && $edge['node']['location']['has_public_page']) {
			$items['medias'][$key]['place']['id'] = $edge['node']['location']['id'];
			$items['medias'][$key]['place']['name'] = $edge['node']['location']['name'];
			$items['medias'][$key]['place']['slug'] = $edge['node']['location']['slug'];
		} else {
			$items['medias'][$key]['place'] = null;
		}
	}
	return true;
}

// Calcul de l'engagement
function calcEngagement() {
	global $items;
	
	if(!empty($items['medias']) && (isset($items['profile']['follower_by_count']) && $items['profile']['follower_by_count'] > 0)) {
		$items['profile']['headEngagement'] = number_format(((($items['profile']['headTotalLikes'] + $items['profile']['headTotalComments']) / HEAD_MAX_POSTS) / $items['profile']['follower_by_count']) * 100, 2) . '%';
	}
}

// Calcul du range de date des publications
function calcDatePublication() {
	global $items;
	
	if(!empty($items['medias'])) {
		$nbPosts = count($items['medias']) > HEAD_MAX_POSTS ? HEAD_MAX_POSTS : count($items['medias']);
		$nbJours = round((($items['profile']['headDateFin'] - $items['profile']['headDateDebut']) / SECONDS_IN_DAY), 0); // Différence entre date de première et dernière publication du résultat
		$nbJourDateduJour = round(((time() - $items['profile']['headDateDebut']) / SECONDS_IN_DAY), 0); // Différence entre date du jour et première publication du résultat
		if($nbJourDateduJour == 0) { $nbJourDateduJour = 1; }
		$items['profile']['headDateDiff'] =  $nbPosts . __(' articles en ', 'eac-components') . $nbJourDateduJour . __(' jours', 'eac-components');
	}
}

// Conversion des nombres en Kilos et Millions
function convertHeaderInfos() {
	global $endpoints;
	global $items;
	
	if(!empty($items['medias'])) {
		$items['profile']['headAvgLikes'] = $endpoints->convertNumber(round(($items['profile']['headTotalLikes'] / HEAD_MAX_POSTS), 0), 1);
		$items['profile']['headAvgComments'] = $endpoints->convertNumber(round(($items['profile']['headTotalComments'] / HEAD_MAX_POSTS), 0), 1);
		if($items['profile']['headAvgComments'] == 0) { $items['profile']['headAvgComments'] = 1; }
		
		$items['profile']['headTotalMentions'] = $endpoints->convertNumber($items['profile']['headTotalMentions'], 1);
		$items['profile']['headTotalHashtags'] = $endpoints->convertNumber($items['profile']['headTotalHashtags'], 1);
		
		$items['profile']['headTotalLikes'] = $endpoints->convertNumber($items['profile']['headTotalLikes'], 1);
		$items['profile']['headTotalComments'] = $endpoints->convertNumber($items['profile']['headTotalComments'], 1);
	}
}

// On ne réserve que les arobasques qui ont un poids (weight) supérieur à..
// pour le nuage de tags
function setJqCloud() {
	global $items;
	
	// Les mentions
	$index = 0;
	if(!empty($items['mediasMentions'])) {
		foreach($items['mediasMentions'] as $mention) {
			if($mention['weight'] > intval(mentionWeight)) {
				$items['jqcloudMention'][$index]['text'] = $mention['text'];
				$items['jqcloudMention'][$index]['weight'] = $mention['weight'];
				$items['jqcloudMention'][$index]['html']['title'] = $mention['html']['title'];
				$index++;
			}
		}
	unset($items['mediasMentions']);
	}
	
	// @since 1.5.2 Les hashtags
	$index = 0;
	if(!empty($items['mediasHashtags'])) {
		foreach($items['mediasHashtags'] as $hashtag) {
			if($hashtag['weight'] > intval(hashtagWeight)) {
				$items['jqcloudHashtag'][$index]['text'] = $hashtag['text'];
				$items['jqcloudHashtag'][$index]['weight'] = $hashtag['weight'];
				$items['jqcloudHashtag'][$index]['html']['title'] = $hashtag['html']['title'];
				$index++;
			}
		}
	unset($items['mediasHashtags']);
	}
}