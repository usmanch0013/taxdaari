<?php

/*=============================================================================================
* Description: Collecte un tableau de médias et top médias d'un hashtag
* Constitue un tableau pour le nuage de tags avec leurs occurrences
* Ce module est appelé depuis la requête AJAX 'ajaxCallFeed' de 'eac-components.js'
*
* Utilisation des méthodes de la class 'Endpoints'
* Méthode 'getExploreHashtagPage' récupère le profile du hashtag (désactivé)
* Méthode 'getExploreHashtagJson' récupère les posts d'un hashtag
* Méthode 'setDebug' active le debugging de l'objet Endpoints
* Méthode 'getMultiUsernameById' collecte le nom des user des posts par leurs ID
*
* @param {string} $_REQUEST['instagram'] le libellé du hashatg
* @param {string} $_REQUEST['token'] csrf token de la session
* @param {string} $_REQUEST['rollout'] instagram-ajax token de la session
* @param {string} $_REQUEST['debug'] debugging
* @param {string} $_REQUEST['cursor'] cursor pour la pagination
* @return Les posts encodés JSON
* @since 1.3.0
* @since 1.4.0	Gestion des tokens de l'objet Endpoints
* @since 1.4.1	Gestion des options Instagram
*				Recherche le nom du user de chaque post et top post par son ID
* @since 1.4.5	(01/01/2020) Ajout des hashtags associés (Related Hashtags)
*				L'appel de la méthode 'getExploreHashtagJson' est suspendu
* @since 1.4.9	Implémente la pagination
*				Méthode 'getExploreHashtagJson' réactivée
* @since 1.6.0  Suppression de l'appel de la méthode 'getExploreHashtagPage'
*               Remplacer par un appel unique à la méthode 'getExploreHashtagJson'
* @since 1.6.2	Suppression du traitement des options 'check_component_insta'
*============================================================================================*/

namespace EACCustomWidgets\Proxy;

$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
require_once($parse_uri[0] . 'wp-load.php');

if(!defined('ABSPATH')) exit; // Exit if accessed directly

if(!isset($_REQUEST['instagram'])) { exit; }

require_once __DIR__ . '/lib/Endpoints.php';
use EACCustomWidgets\Proxy\Lib\Endpoints;


// Construction de l'objet Endpoints
$endpoints = new Endpoints();

// Nombre de posts à retourner * 3. Ex: 20 * 3 ~= 60 posts
$endpoints->setAccountMediasCount(1);

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
define("ACCESS_CAPTION_LENGTH", "50");
// Les statistiques sont sur les 12 derniers posts
define("HEAD_MAX_POSTS", "12");
// Nombre de secondes par jour
define("SECONDS_IN_DAY", "86400");
// Poids des hashtags
define("hashtagWeight", "3");

// Les items à afficher
$items = array();

// Les nodes medias et topmedias
$edges = array();
$topEdges = array();

// @since 1.4.9 Pagination. hashtag et cursor
if(!empty($_REQUEST['instagram']) && !empty($_REQUEST['cursor'])) {
	$endpoints->setAccountMediasCount(6); // Nombre de posts à retourner pour la pagination 36 = 12*3 /** @since 1.8.7 */
	if(!setUserMedias($_REQUEST['instagram'], $_REQUEST['cursor'])) { exit; }
	if(!empty($items['mediasHashtags'])) { unset($items['mediasHashtags']); }
} else {
	$endpoints->setAccountMediasCount(6); // Nombre de posts à retourner pour la pagination 75 = 25*3 /** @since 1.8.7 */
	if(!setUserProfile()) { exit; }
	if(!setUserMedias($_REQUEST['instagram'])) { exit; }
	setTopUserMedias();
	calcDatePublication();
	convertHeaderInfos();
	setJqCloud();
}

unset($endpoints);
echo json_encode($items);

function setUserProfile() {
	global $endpoints;
	global $items;
	global $edges;
	global $topEdges;
	
	// Recherche du profile pour un hashtag
	if(!$profile = $endpoints->getExploreHashtagJson($_REQUEST['instagram'])) {
		echo __("Erreur lors de la recherche du profile:: ", "eac-components") . $_REQUEST['instagram'];
		return false;
	}
	
	// Publications et meilleurs publications
	$edges = $profile['edge_hashtag_to_media']['edges'];
	$topEdges = $profile['edge_hashtag_to_top_posts']['edges'];
	
	// Page supplémentaire et chaine du curseur de la dernière photo
	$items['profile']['has_next_page'] = $profile['edge_hashtag_to_media']['page_info']['has_next_page'];
	$items['profile']['end_cursor'] = $profile['edge_hashtag_to_media']['page_info']['end_cursor'];
	
	$items['profile']['headTitle'] = $profile['name'];
	$items['profile']['id'] = $profile['id'];
	$items['profile']['allow_following'] = $profile['allow_following'];
	$items['profile']['is_following'] = $profile['is_following'];
	
	/** @since 1.8.7 Contourner image CORS des navigateurs */
	$img_src = 'data:image/jpg;base64,' . base64_encode(file_get_contents($profile['profile_pic_url']));
	$items['profile']['headLogo'] = $img_src;
	//$items['profile']['headLogo'] = $profile['profile_pic_url'];
	
	$items['profile']['headTotalCount'] = $endpoints->convertNumber($profile['edge_hashtag_to_media']['count'], 1);
	$items['profile']['headTotalCountTop'] = count($profile['edge_hashtag_to_top_posts']['edges']); // Nombre d'éléments du tableau top edges
	$items['profile']['headTotalLikes'] = 0;
	$items['profile']['headTotalComments'] = 0;
	$items['profile']['headTotalVideos'] = 0;
	$items['profile']['headTotalHashtags'] = 0;
	$items['profile']['headTotalLikesTop'] = 0;
	$items['profile']['headTotalCommentsTop'] = 0;
	$items['profile']['headTotalVideosTop'] = 0;
	$items['profile']['headTotalHashtagsTop'] = 0;
	$items['profile']['headDateDebut'] = 0;
	$items['profile']['headDateFin'] = 0;
	$items['profile']['headDateDiff'] = '';
	
	return true;
}

// Boucle sur les médias récents
function setUserMedias($tagname, $cursor = '') {
	global $endpoints;
	global $items;
	global $edges;
	$arrayUserMedias;
	
	// Pagination
	if(empty($edges)) {
        // @since 1.6.0 Recherche des données pour un hashtag
    	if(!$arrayUserMedias = $endpoints->getExploreHashtagJson($tagname, $cursor)) {
    		echo __("Erreur lors de la recherche du profile: ", "eac-components") . $tagname;
    		return false;
    	} else {
    	    $edges = $arrayUserMedias['edge_hashtag_to_media']['edges'];
    	    
    	    // Page supplémentaire et chaine du curseur de la dernière photo
	        $items['profile']['has_next_page'] = $arrayUserMedias['edge_hashtag_to_media']['page_info']['has_next_page'];
	        $items['profile']['end_cursor'] = $arrayUserMedias['edge_hashtag_to_media']['page_info']['end_cursor'];
    	}
	}
	
	// @since 1.4.9 Pagination. Initialisation des variables
	if(!isset($items['profile']['headTotalLikes'])) { $items['profile']['headTotalLikes'] = 0; }
	if(!isset($items['profile']['headTotalComments'])) { $items['profile']['headTotalComments'] = 0; }
	if(!isset($items['profile']['headDateFin'])) { $items['profile']['headDateFin'] = 0; }
	if(!isset($items['profile']['headTotalHashtags'])) { $items['profile']['headTotalHashtags'] = 0; }
	if(!isset($items['profile']['headTotalVideos'])) { $items['profile']['headTotalVideos'] = 0; }
	
	foreach($edges as $key => $edge) {
		$items['medias'][$key]['nodeId'] = $edge['node']['id'];
		$items['medias'][$key]['nodeType'] = $edge['node']['__typename'];
		$items['medias'][$key]['caption'] = isset($edge['node']['edge_media_to_caption']['edges'][0]['node']['text']) ? $edge['node']['edge_media_to_caption']['edges'][0]['node']['text'] : '';
		$items['medias'][$key]['linkNode'] = $edge['node']['shortcode'];
		
		$items['medias'][$key]['likeCount'] = isset($edge['node']['edge_liked_by']['count']) ? $endpoints->convertNumber($edge['node']['edge_liked_by']['count'], 0) : 0;
		$items['medias'][$key]['likeCount_sort'] = isset($edge['node']['edge_liked_by']['count']) ? $edge['node']['edge_liked_by']['count'] : 0;
		
		$items['medias'][$key]['commentCount'] = isset($edge['node']['edge_media_to_comment']['count']) ? $endpoints->convertNumber($edge['node']['edge_media_to_comment']['count'], 0) : 0;
		$items['medias'][$key]['commentCount_sort'] = isset($edge['node']['edge_media_to_comment']['count']) ? $edge['node']['edge_media_to_comment']['count'] : 0;
		
		$items['profile']['headTotalLikes'] += isset($edge['node']['edge_liked_by']['count']) ? intval($edge['node']['edge_liked_by']['count']) : 0;
		$items['profile']['headTotalComments'] += isset($edge['node']['edge_media_to_comment']['count']) ? intval($edge['node']['edge_media_to_comment']['count']) : 0;
		
		$items['medias'][$key]['ownerId'] = $edge['node']['owner']['id'];
		$items['medias'][$key]['username'] = '';
		$items['medias'][$key]['username_pic'] = '';
		
		$items['medias'][$key]['accessibility_caption'] = '';
		$items['medias'][$key]['place'] = "";
		
		if(!empty($edge['node']['accessibility_caption'])) {
			preg_match("#(.*?)(\.|@)#u", $edge['node']['accessibility_caption'], $accaption);
			if(isset($accaption[1]) && !is_null($accaption[1])) {
		        $items['medias'][$key]['accessibility_caption'] = $accaption[1];
			}
			
			preg_match("#\sin\s(.*?)(\.|\swith\s)#u", $edge['node']['accessibility_caption'], $place);
			if(isset($place[1]) && !is_null($place[1])) {
				//$items['medias'][$key]['place'] = sanitize_title(trim($place[1]));
				$items['medias'][$key]['place'] = $endpoints->sanitizeplace(trim($place[1]));
			}
		}
		
		/** @since 1.8.7 Contourner image CORS des navigateurs */
		$img_src = 'data:image/jpg;base64,' . base64_encode(file_get_contents($edge['node']['thumbnail_src']));
		$items['medias'][$key]['thumbnail_src'] = $img_src;
		
		/*$items['medias'][$key]['image240px'] = $edge['node']['thumbnail_resources'][1]['src'];
		$items['medias'][$key]['image480px'] = $edge['node']['thumbnail_resources'][3]['src'];
		$items['medias'][$key]['img_med'] = $edge['node']['thumbnail_resources'][4]['src'];
		$items['medias'][$key]['thumbnail_src'] = $edge['node']['thumbnail_src'];
		$items['medias'][$key]['img_standard'] = $edge['node']['display_url'];*/
		
		$items['medias'][$key]['video'] = $edge['node']['is_video'];
		if($items['medias'][$key]['video']) {
			$items['medias'][$key]['video_view_count'] = isset($edge['node']['video_view_count']) ? $endpoints->convertNumber($edge['node']['video_view_count'], 0) : 1;
			$items['profile']['headTotalVideos'] += 1;
		}
		
		$items['medias'][$key]['update'] = $edge['node']['taken_at_timestamp'];
		// Date de publication en nombre de jours
		$nbJour = round(((time() - $edge['node']['taken_at_timestamp']) / SECONDS_IN_DAY), 0) . __(' jours', 'eac-components');
		$items['medias'][$key]['updateEnJours'] = $nbJour;
		
		// Enregistre les dates de début et de fin de l'extraction des 12 derniers posts
		if($key < HEAD_MAX_POSTS) {
			if($edge['node']['taken_at_timestamp'] > $items['profile']['headDateFin']) {
				$items['profile']['headDateFin'] = $edge['node']['taken_at_timestamp'];
			}
			if($edge['node']['taken_at_timestamp'] < $items['profile']['headDateFin']) {
				$items['profile']['headDateDebut'] = $edge['node']['taken_at_timestamp'];
			}
		}
		
		// On récupère tous les hashtags pour les compter
		preg_match_all('/#[^\s#@.)!]*/u', $items['medias'][$key]['caption'], $results); // (#\S+)
		$uniqHashtag = array_unique($results[0]); // Trie et keyword unique pour chaque post
		sort($uniqHashtag, SORT_NATURAL | SORT_FLAG_CASE);
		
		$items['medias'][$key]['hashtagCount'] = count($uniqHashtag);
		$items['medias'][$key]['hashtagList'] = implode(',', $uniqHashtag);
		$items['profile']['headTotalHashtags'] += count($results[0]);
		
		foreach($results[0] as $result) {
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
	}
	return true;
}

// Boucle sur les médias meilleures publications
function setTopUserMedias() {
	global $endpoints;
	global $items;
	global $topEdges;
	
	if(empty($topEdges)) { return; }
	
	foreach($topEdges as $key => $edge) {
		$items['mediasTop'][$key]['nodeId'] = $edge['node']['id'];
		$items['mediasTop'][$key]['nodeType'] = $edge['node']['__typename'];
		$items['mediasTop'][$key]['caption'] = isset($edge['node']['edge_media_to_caption']['edges'][0]['node']['text']) ? $edge['node']['edge_media_to_caption']['edges'][0]['node']['text'] : '';
		$items['mediasTop'][$key]['linkNode'] = $edge['node']['shortcode'];
		
		$items['mediasTop'][$key]['likeCount'] = isset($edge['node']['edge_liked_by']['count']) ? $endpoints->convertNumber($edge['node']['edge_liked_by']['count'], 0) : 0;
		$items['profile']['headTotalLikesTop'] += isset($edge['node']['edge_liked_by']['count']) ? intval($edge['node']['edge_liked_by']['count']) : 0;
		
		$items['mediasTop'][$key]['commentCount'] = isset($edge['node']['edge_media_to_comment']['count']) ? $endpoints->convertNumber($edge['node']['edge_media_to_comment']['count'], 0) : 0;
		$items['profile']['headTotalCommentsTop'] += isset($edge['node']['edge_media_to_comment']['count']) ? intval($edge['node']['edge_media_to_comment']['count']) : 0;
		
		$items['mediasTop'][$key]['ownerId'] = $edge['node']['owner']['id'];
		$items['mediasTop'][$key]['username'] = '';
		$items['mediasTop'][$key]['username_pic'] = '';
		$items['mediasTop'][$key]['accessibility_caption'] = isset($edge['node']['accessibility_caption']) ? $edge['node']['accessibility_caption'] : "";
		
		/** @since 1.8.7 Contourner image CORS des navigateurs */
		$img_src = 'data:image/jpg;base64,' . base64_encode(file_get_contents($edge['node']['thumbnail_src']));
		$items['mediasTop'][$key]['thumbnail_src'] = $img_src;
		
		/*$items['mediasTop'][$key]['image240px'] = $edge['node']['thumbnail_resources'][1]['src'];
		$items['mediasTop'][$key]['image480px'] = $edge['node']['thumbnail_resources'][3]['src'];
		$items['mediasTop'][$key]['img_med'] = $edge['node']['thumbnail_resources'][4]['src'];
		$items['mediasTop'][$key]['thumbnail_src'] = $edge['node']['thumbnail_src'];
		$items['mediasTop'][$key]['img_standard'] = $edge['node']['display_url'];*/
		
		$items['mediasTop'][$key]['video'] = $edge['node']['is_video'];
		if($items['mediasTop'][$key]['video']) {
			$items['mediasTop'][$key]['video_view_count'] = isset($edge['node']['video_view_count']) ? $endpoints->convertNumber($edge['node']['video_view_count'], 0) : 1;
			$items['profile']['headTotalVideosTop'] += 1;
		}
		
		$items['mediasTop'][$key]['update'] = $edge['node']['taken_at_timestamp'];
		// Date de publication en nombre de jours
		$nbJour = round(((time() - $edge['node']['taken_at_timestamp']) / SECONDS_IN_DAY), 0) . __(' jours', 'eac-components');
		$items['mediasTop'][$key]['updateEnJours'] = $nbJour;
		
		// On récupère tous les hashtags pour les compter
		preg_match_all('/#[^\s#@.)!]*/u', $items['mediasTop'][$key]['caption'], $results);
		$uniqHashtag = array_unique($results[0]); // keyword unique pour chaque post
		sort($uniqHashtag, SORT_NATURAL | SORT_FLAG_CASE);
		
		$items['mediasTop'][$key]['hashtagCount'] = count($uniqHashtag);
		$items['mediasTop'][$key]['hashtagList'] = implode(',', $uniqHashtag);
		$items['profile']['headTotalHashtagsTop'] += count($results[0]);
		
		// On ne prend que 200 caractères dans le caption à cause du sessionStorage
		if(!empty($items['mediasTop'][$key]['caption']) && mb_strlen($items['mediasTop'][$key]['caption'], 'UTF-8') > CAPTION_LENGTH) {
			$items['mediasTop'][$key]['caption'] = mb_substr($items['mediasTop'][$key]['caption'], 0, CAPTION_LENGTH, 'UTF-8');
		}
	}
}

// Calcul du range de date des publications
function calcDatePublication() {
	global $items;
	
	if(!empty($items['medias'])) {
		$nbPosts = count($items['medias']) > HEAD_MAX_POSTS ? HEAD_MAX_POSTS : count($items['medias']);
		$nbJours = round((($items['profile']['headDateFin'] - $items['profile']['headDateDebut']) / SECONDS_IN_DAY), 0); // Différence entre date de première et dernière publication
		$nbJourDateduJour = round(((time() - $items['profile']['headDateDebut']) / SECONDS_IN_DAY), 0); // Différence entre date du jour et première publication
		if($nbJourDateduJour == 0) { $nbJourDateduJour = 1; }
		$items['profile']['headDateDiff'] = $nbPosts . __(' articles en ', 'eac-components') . $nbJourDateduJour . __(' jours', 'eac-components');
	}
}

// Conversion des nombres en Kilos et Millions
function convertHeaderInfos() {
	global $endpoints;
	global $items;
	
	if(!empty($items['medias'])) {
		$items['profile']['headTotalLikes'] = $endpoints->convertNumber($items['profile']['headTotalLikes'], 1);
		$items['profile']['headTotalComments'] = $endpoints->convertNumber($items['profile']['headTotalComments'], 1);
		$items['profile']['headTotalHashtags'] = $endpoints->convertNumber($items['profile']['headTotalHashtags'], 1);
		
		$items['profile']['headTotalLikesTop'] = $endpoints->convertNumber($items['profile']['headTotalLikesTop'], 1);
		$items['profile']['headTotalCommentsTop'] = $endpoints->convertNumber($items['profile']['headTotalCommentsTop'], 1);
		$items['profile']['headTotalHashtagsTop'] = $endpoints->convertNumber($items['profile']['headTotalHashtagsTop'], 1);
	}
}

// On ne réserve que les hashtags qui ont un poids (weight) supérieur à..
// pour le nuage de tags
function setJqCloud() {
	global $items;
	
	$index = 0;
	if(!empty($items['mediasHashtags'])) {
		foreach($items['mediasHashtags'] as $mhh) {
			if($mhh['weight'] > intval(hashtagWeight)) {
				$items['jqcloud'][$index]['text'] = $mhh['text'];
				$items['jqcloud'][$index]['weight'] = $mhh['weight'];
				$items['jqcloud'][$index]['html']['title'] = $mhh['html']['title'];
				$index++;
			}
		}
	unset($items['mediasHashtags']);
	}
}