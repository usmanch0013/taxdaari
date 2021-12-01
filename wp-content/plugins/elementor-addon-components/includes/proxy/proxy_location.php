<?php

/*=============================================================================================
* Description: Collecte un tableau de médias et top médias d'un lieu (Location)
* Constitue un tableau pour le nuage de tags avec leurs occurrences
* Ce module est appelé depuis la requête AJAX 'ajaxCallFeed' de 'eac-components.js'
*
* Utilisation des méthodes de la class 'Endpoints'
* Méthode 'setAccountMediasCount' fixe le nombre de posts à retourner
* Méthode 'getExploreLocationJson' récupère les posts d'une location
* Méthode 'getNominatimData' Valorise le libellé du popup pour la carte Open Street Map (OSM)
* Méthode 'setDebug' active le debugging de l'objet Endpoints
*
* @param {string} $_REQUEST['instagram'] l'identifiant de la location
* @param {string} $_REQUEST['debug'] debugging
* @param {string} $_REQUEST['cursor'] cursor pour la pagination
* @return Les posts encodés JSON
* @since 1.4.0
* @since 1.5.2 Implémente la pagination
* @since 1.5.4 Méthode 'getNominatimData', ajout de l'ID location dans les paramètres
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

// Les items à afficher
$items = array();

// Les nodes topmedias
$topEdges = array();

// Tableau d'id du propriétaire. Conserver pour les requêtes Rollingcurlx
$idUsernames = array();
$idUsernamesTop = array();

// @since 1.5.2 Pagination. ID de la Location et cursor
if(!empty($_REQUEST['instagram']) && !empty($_REQUEST['cursor'])) {
	// Nombre de posts à retourner pour la pagination
	$endpoints->setAccountMediasCount(16);
	if(!setUserMedias($_REQUEST['instagram'], $_REQUEST['cursor'])) { exit; }
	if(!empty($items['mediasHashtags'])) { unset($items['mediasHashtags']); }
} else {
	// Nombre de posts à retourner
    $endpoints->setAccountMediasCount(16);
	if(!setUserProfile()) { exit; }
	if(!setUserMedias($_REQUEST['instagram'])) { exit; }
	setNominatimData();
	setTopUserMedias();
	calcDatePublication();
	convertHeaderInfos();
	//setJqCloud();
	if(!empty($items['mediasHashtags'])) { unset($items['mediasHashtags']); }
}

unset($endpoints);
echo json_encode($items);

function setUserProfile() {
	global $endpoints;
	global $items;
	global $topEdges;
	
    // Recherche des posts pour une location
    $results_array = $endpoints->getExploreLocationJson($_REQUEST['instagram']);
	
	if(isset($results_array['message'])) {
		echo __("La requête a échoué. Recommencer plus tard", "eac-components" . $_REQUEST['instagram']); // execution failure
		return false;
	}
	
	if($results_array == false) {
    	echo __("Erreur lors de la recherche du profile:: ", "eac-components") . $_REQUEST['instagram'];
    	return false;
    }
    
    // // Il n'y a pas de données
    if(empty($results_array['edge_location_to_media']['edges'])) {
    	echo __("Rien à afficher...", "eac-components");
    	return false;
    }
    
    $topEdges = $results_array['edge_location_to_top_posts']['edges'];
    
    $items['profile']['headTitle'] = $results_array['name'];
    $items['profile']['id'] = $results_array['id'];
    $items['profile']['lat'] = $results_array['lat'];
    $items['profile']['lng'] = $results_array['lng'];
    $items['profile']['slug'] = $results_array['slug'];
	$items['profile']['blurb'] = $results_array['blurb'];
	$items['profile']['website'] = isset($results_array['website']) ? $results_array['website'] : "";
	
	/** @since 1.8.7 Contourner image CORS des navigateurs */
	$img_src = 'data:image/jpg;base64,' . base64_encode(file_get_contents($results_array['profile_pic_url']));
	$items['profile']['headLogo'] = $img_src;
    //$items['profile']['headLogo'] = $results_array['profile_pic_url'];
    
	$items['profile']['headTotalCount'] = $endpoints->convertNumber($results_array['edge_location_to_media']['count'], 1);
    $items['profile']['headTotalCountTop'] = count($results_array['edge_location_to_top_posts']['edges']); // Nombre d'éléments du tableau top edges
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

// Médias récents
function setUserMedias($idlocation, $cursor = '') {
    global $endpoints;
	global $items;
	global $idUsernames;
	$edges = array();
	
	// Recherche des posts pour une location
    $results_array = $endpoints->getExploreLocationJson($idlocation, $cursor);
	
	if(isset($results_array['message'])) {
		echo __("La requête a échoué. Recommencer plus tard", "eac-components" . $idlocation); // execution failure
		return false;
	}
	
	if($results_array == false) {
    	echo __("Erreur lors de la recherche du profile: ", "eac-components") . $idlocation;
    	return false;
    }
    
    // // Il n'y a pas de données
    if(empty($results_array['edge_location_to_media']['edges'])) {
    	echo __("Rien à afficher...", "eac-components");
    	return false;
    }
    
    $edges = $results_array['edge_location_to_media']['edges'];
    // Page supplémentaire et chaine du curseur de la dernière photo
    $items['profile']['has_next_page'] = $results_array['edge_location_to_media']['page_info']['has_next_page'];
    $items['profile']['end_cursor'] = $results_array['edge_location_to_media']['page_info']['end_cursor'];
    
    // @since 1.5.2 Pagination. Initialisation des variables
	if(!isset($items['profile']['headTotalLikes'])) { $items['profile']['headTotalLikes'] = 0; }
	if(!isset($items['profile']['headTotalComments'])) { $items['profile']['headTotalComments'] = 0; }
	if(!isset($items['profile']['headDateFin'])) { $items['profile']['headDateFin'] = 0; }
	if(!isset($items['profile']['headTotalHashtags'])) { $items['profile']['headTotalHashtags'] = 0; }
	if(!isset($items['profile']['headTotalVideos'])) { $items['profile']['headTotalVideos'] = 0; }
	
    foreach($edges as $key => $edge) {
    	$items['medias'][$key]['nodeId'] = $edge['node']['id'];
    	//$items['medias'][$key]['nodeType'] = $edge['node']['__typename'];
    	$items['medias'][$key]['caption'] = isset($edge['node']['edge_media_to_caption']['edges'][0]['node']['text']) ? $edge['node']['edge_media_to_caption']['edges'][0]['node']['text'] : '';
    	$items['medias'][$key]['linkNode'] = $edge['node']['shortcode'];
    	
    	$items['medias'][$key]['likeCount'] = isset($edge['node']['edge_liked_by']['count']) ? $endpoints->convertNumber($edge['node']['edge_liked_by']['count'], 0) : 0;
    	$items['medias'][$key]['likeCount_sort'] = isset($edge['node']['edge_liked_by']['count']) ? $edge['node']['edge_liked_by']['count'] : 0;
    	
    	$items['medias'][$key]['commentCount'] = isset($edge['node']['edge_media_to_comment']['count']) ? $endpoints->convertNumber($edge['node']['edge_media_to_comment']['count'], 0) : 0;
    	$items['medias'][$key]['commentCount_sort'] = isset($edge['node']['edge_media_to_comment']['count']) ? $edge['node']['edge_media_to_comment']['count'] : 0;
    	
    	$items['profile']['headTotalLikes'] += isset($edge['node']['edge_liked_by']['count']) ? intval($edge['node']['edge_liked_by']['count']) : 0;
    	$items['profile']['headTotalComments'] += isset($edge['node']['edge_media_to_comment']['count']) ? intval($edge['node']['edge_media_to_comment']['count']) : 0;
    	
    	$items['medias'][$key]['ownerId'] = $edge['node']['owner']['id'];
    	$items['medias'][$key]['username'] = null;
    	
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
    	
    	// Enregistre les dates de début et de fin de l'extraction
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
    	
    	// On stocke tous les ID pour l'object RollingCurlX
    	array_push($idUsernames, $edge['node']['owner']['id']);
    	
    	// On ne prend que 200 caractères dans le caption à cause du sessionStorage
    	if(!empty($items['medias'][$key]['caption']) && mb_strlen($items['medias'][$key]['caption'], 'UTF-8') > CAPTION_LENGTH) {
    		$items['medias'][$key]['caption'] = mb_substr($items['medias'][$key]['caption'], 0, CAPTION_LENGTH, 'UTF-8');
    	}
    }
    return true;
}

// Médias meilleures publications
function setTopUserMedias() {
    global $endpoints;
	global $items;
	global $idUsernamesTop;
	global $topEdges;
	
	if(empty($topEdges)) { return; }
	
    foreach($topEdges as $key => $edge) {
    	$items['mediasTop'][$key]['nodeId'] = $edge['node']['id'];
    	//$items['mediasTop'][$key]['nodeType'] = $edge['node']['__typename'];
    	$items['mediasTop'][$key]['caption'] = isset($edge['node']['edge_media_to_caption']['edges'][0]['node']['text']) ? $edge['node']['edge_media_to_caption']['edges'][0]['node']['text'] : '';
    	$items['mediasTop'][$key]['linkNode'] = $edge['node']['shortcode'];
    	
    	$items['mediasTop'][$key]['likeCount'] = isset($edge['node']['edge_liked_by']['count']) ? $endpoints->convertNumber($edge['node']['edge_liked_by']['count'], 0) : 0;
    	$items['profile']['headTotalLikesTop'] += isset($edge['node']['edge_liked_by']['count']) ? intval($edge['node']['edge_liked_by']['count']) : 0;
    	
    	$items['mediasTop'][$key]['commentCount'] = isset($edge['node']['edge_media_to_comment']['count']) ? $endpoints->convertNumber($edge['node']['edge_media_to_comment']['count'], 0) : 0;
    	$items['profile']['headTotalCommentsTop'] += isset($edge['node']['edge_media_to_comment']['count']) ? intval($edge['node']['edge_media_to_comment']['count']) : 0;
    	
    	$items['mediasTop'][$key]['ownerId'] = $edge['node']['owner']['id'];
    	$items['mediasTop'][$key]['username'] = null;
    	
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
    	
    	// On stocke tous les ID pour l'object RollingCurlX
    	array_push($idUsernamesTop, $edge['node']['owner']['id']);
    	
    	// On ne prend que 200 caractères dans le caption à cause du sessionStorage
    	if(!empty($items['mediasTop'][$key]['caption']) && mb_strlen($items['mediasTop'][$key]['caption'], 'UTF-8') > CAPTION_LENGTH) {
    		$items['mediasTop'][$key]['caption'] = mb_substr($items['mediasTop'][$key]['caption'], 0, CAPTION_LENGTH, 'UTF-8');
    	}
    }
}

// Appel du service nominatim et valorisation de la donnée corespondante
function setNominatimData() {
    global $endpoints;
	global $items;
	$city = 'No city name.';
	$road = '';
	
	// @since 1.5.4 Ajout de l'ID location dans les paramètres
    if(!$nominatim = $endpoints->getNominatimData($items['profile']['id'], $items['profile']['lat'], $items['profile']['lng'])) {
    	$items['profile']['nominatim'] = $items['profile']['headTitle'];
    } else {
		$adrs = isset($nominatim['address']['address29']) ? $nominatim['address']['address29'] : '';
		$num = isset($nominatim['address']['house_number']) ? $nominatim['address']['house_number'] : '';
		
		if(isset($nominatim['address']['road'])) { $road = $nominatim['address']['road']; }
		else if(isset($nominatim['address']['pedestrian'])) { $road = $nominatim['address']['pedestrian']; }
		else if(isset($nominatim['address']['locality'])) { $road = $nominatim['address']['locality']; }
		else if(isset($nominatim['address']['suburb'])) { $road = $nominatim['address']['suburb']; }
		
		if(isset($nominatim['address']['city'])) { $city = $nominatim['address']['city']; }
		else if(isset($nominatim['address']['town'])) { $city = $nominatim['address']['town']; }
		else if(isset($nominatim['address']['county'])) { $city = $nominatim['address']['county']; }
		
		$country = isset($nominatim['address']['country']) ? $nominatim['address']['country'] : '';
		
		$items['profile']['nominatim'] = "$adrs $num $road<br/>$city $country";
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