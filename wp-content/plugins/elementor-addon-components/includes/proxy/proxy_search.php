<?php

/*============================================================================================================
* Description: Module de recherche des données d'un user account, d'un hashtag ou d'un lieu (place)
* Ce module est appelé depuis la requête AJAX 'ajaxCallFeed' de 'eac-components.js'
*
* Utilisation des méthodes de la class 'Endpoints'
* Méthode 'getProfilUserHashtagPlace' cherche les user account, hashtags et places
* Méthode 'setDebug' active le debugging de l'objet Endpoints
* Méthode 'getMultiFollowerById' collecte le nombre de followers par ID du compte utilisateur
*
* @param {string} $_REQUEST['instagram'] le libellé à rechercher
* @param {string} $_REQUEST['reqsearch'] le type de recherche (user, hashtag, place)
* @param {string} $_REQUEST['debug'] debugging
* @return Les données encodées JSON
* @since 1.3.0
* @since 1.3.1 (28/09/2019)
* @since 1.4.1	Gestion des options Instagram
*				Recherche le nombre de followers de chaque posts par l'ID d'un user account (Voir paramètrage)
* @since 1.6.2	Suppression du traitement des options 'check_component_insta'
*=============================================================================================================*/

namespace EACCustomWidgets\Proxy;

$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
require_once($parse_uri[0] . 'wp-load.php');

if(! defined('ABSPATH')) exit; // Exit if accessed directly

if(! isset($_REQUEST['instagram']) && ! isset($_REQUEST['reqsearch'])) { exit; }

require_once __DIR__ . '/lib/Endpoints.php';
use EACCustomWidgets\Proxy\Lib\Endpoints;

// Construction de l'objet Endpoints
$endpoints = new Endpoints();

// Debug or not ?
if(!empty($_REQUEST['debug']) && $_REQUEST['debug'] === 'vrai') {
	$endpoints->setDebug();
}

// Les items à afficher
$items = array();

// Récupère les données
if(!$results_array = $endpoints->getProfilUserHashtagPlace($_REQUEST['instagram'], $_REQUEST['reqsearch'])) {
	echo __("Rien à afficher...", "eac-components");
	exit;
}

$users = isset($results_array['users']) ? $results_array['users'] : '';
$hashtags = isset($results_array['hashtags']) ? $results_array['hashtags'] : '';
$places = isset($results_array['places']) ? $results_array['places'] : '';

/**
* Les propriétés d'un user account 'follower' et 'byline' n'apparaissent plus dans le résultat de la requête
* @since 1.3.1
*/

if(!empty($users)) {
	foreach($users as $key => $user) {
		$items['users'][$key]['position'] = $user['position'];
		$items['users'][$key]['pk'] = $user['user']['pk'];
		$items['users'][$key]['username'] = $user['user']['username'];
		$items['users'][$key]['full_name'] = $user['user']['full_name'];
		$items['users'][$key]['is_private'] = $user['user']['is_private'];
		$items['users'][$key]['is_private_fill'] = $user['user']['is_private'] == true ? __('Compte privé', 'eac-components') : __('Compte public', 'eac-components');
		$items['users'][$key]['is_verified'] = $user['user']['is_verified'];
		$items['users'][$key]['follower_count'] = isset($user['user']['follower_count']) ? $endpoints->convertNumber($user['user']['follower_count'], 0) : 0;
		$items['users'][$key]['follower_count_sort'] = isset($user['user']['follower_count']) ? $user['user']['follower_count'] : 0; // Utilisé pour le tri sur les followers
		$items['users'][$key]['byline'] = isset($user['user']['byline']) ? $user['user']['byline'] : 0;
		$items['users'][$key]['profile_pic_url'] = $user['user']['profile_pic_url'];
	}
}

if(!empty($hashtags)) {
	foreach($hashtags as $key => $hashtag) {
		$items['hashtags'][$key]['position'] = $hashtag['position'];
		$items['hashtags'][$key]['username'] = $hashtag['hashtag']['name'];
		$items['hashtags'][$key]['media_count'] = number_format($hashtag['hashtag']['media_count'], 0, ',', '.');
		$items['hashtags'][$key]['media_count_sort'] = $hashtag['hashtag']['media_count']; // Utilisé pour le tri sur le nombre de médias
		$items['hashtags'][$key]['search_result_subtitle'] = $hashtag['hashtag']['search_result_subtitle'];
	}
}

if(!empty($places)) {
	foreach($places as $key => $place) {
		$items['places'][$key]['position'] = $place['position'];
		$items['places'][$key]['pk'] = $place['place']['location']['pk'];
		$items['places'][$key]['name'] = $place['place']['location']['name'];
		$items['places'][$key]['short_name'] = $place['place']['location']['short_name'];
		$items['places'][$key]['lng'] = isset($place['place']['location']['lng']) ? $place['place']['location']['lng'] : '';
		$items['places'][$key]['lat'] = isset($place['place']['location']['lat']) ? $place['place']['location']['lat'] : '';
		$items['places'][$key]['address'] = $place['place']['location']['address'];
		$items['places'][$key]['city'] = $place['place']['location']['city'];
		$items['places'][$key]['external_source'] = $place['place']['location']['external_source'];
		$items['places'][$key]['facebook_places_id'] = $place['place']['location']['facebook_places_id'];
		$items['places'][$key]['title'] = $place['place']['title'];
		$items['places'][$key]['subtitle'] = $place['place']['subtitle'];
		$items['places'][$key]['slug'] = $place['place']['slug'];
	}
}

unset($endpoints);
echo json_encode($items);