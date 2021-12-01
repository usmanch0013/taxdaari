<?php

/*=================================================================================
* Description: Collecte les données (stories) d'un user account par son ID
* Ce module est appelé depuis la requête AJAX 'ajaxCallFeed' de 'eac-components.js'
*
* Utilisation des méthodes de la class 'Endpoints'
* Méthode 'getUserAccountStories' collecte les stories d'un user id account
* Méthode 'setHeaderToken' modifie les propriétés de l'objet Endpoints
*
* @param {string} $_REQUEST['instagram'] le 'id' de l'account
* @param {string} $_REQUEST['token'] csrf token de la session
* @param {string} $_REQUEST['rollout'] instagram-ajax token de la session
* @return Les posts encodés JSON
* @since 1.3.1
* @since 1.4.0 Gestion des tokens de l'objet Endpoints
*================================================================================*/

namespace EACCustomWidgets\Proxy;

$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
require_once($parse_uri[0] . 'wp-load.php');

if(! defined('ABSPATH')) exit; // Exit if accessed directly

if(! isset($_REQUEST['instagram'])) { exit; }

require_once __DIR__ . '/lib/Endpoints.php';
use EACCustomWidgets\Proxy\Lib\Endpoints;

// Construction de l'objet Endpoints
$endpoints = new Endpoints();

// Debug or not ?
if(! empty($_REQUEST['debug']) && $_REQUEST['debug'] === 'vrai') {
	$endpoints->setDebug();
}

// @since 1.4.0 Même session, on valorise les tokens
if(isset($_REQUEST['token']) && isset($_REQUEST['rollout'])) {
	$endpoints->setHeaderToken($_REQUEST['token'], $_REQUEST['rollout']);
}

// Les items à afficher
const URI_STORIES = 'https://www.instagram.com/stories/highlights/';
const INDEX_MAX = 50; // 50 entrées
$items = array();
$edges_reel = array();
$index = 0;

if(! $arrayUserStories = $endpoints->getUserAccountStories($_REQUEST['instagram'])) {
	exit;
}

$edges_reel = $arrayUserStories['edge_highlight_reels']['edges'];

// Boucle sur toutes les stories
foreach($edges_reel as $key => $edge_reel) {
	$items['stories'][$key]['pic_url'] = $edge_reel['node']['cover_media_cropped_thumbnail']['url'];
	$items['stories'][$key]['id'] = $edge_reel['node']['id'];
	$items['stories'][$key]['title'] = $edge_reel['node']['title'];
	$items['stories'][$key]['url'] = URI_STORIES . $edge_reel['node']['id'];
	$index++;
	if($index >= INDEX_MAX) { break; }
}

unset($endpoints);

echo json_encode($items);