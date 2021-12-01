<?php

/*===================================================================================
* Description: Collecte les posts qui intègrent (Tagged Posts) un user account
* Ce module est appelé depuis la requête AJAX 'ajaxCallFeed' de 'eac-components.js'
*
* Utilisation des méthodes de la class 'Endpoints'
* Méthode 'getTaggedPostsUserAccount' collecte les stories d'un user id account
* Méthode 'setHeaderToken' modifie les propriétés de l'objet Endpoints
*
* @param {string} $_REQUEST['instagram'] le 'id' de l'account
* @param {string} $_REQUEST['token'] csrf token de la session
* @param {string} $_REQUEST['rollout'] instagram-ajax token de la session
* @return Les posts encodés JSON
* @since 1.4.3
*==================================================================================*/

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
const URI_TAGGEDPOSTS = 'https://www.instagram.com/';
// Nombre de secondes par jour
const SECONDS_IN_DAY = "86400";
$items = array();
$edges = array();

if(! $arrayTaggedPosts = $endpoints->getTaggedPostsUserAccount($_REQUEST['instagram'])) {
	exit;
}

$edges = $arrayTaggedPosts['edges'];

// Boucle sur toutes les posts qui intègre le user name account
foreach($edges as $key => $edge) {
	$nbJourDateduJour = round(((time() - $edge['node']['taken_at_timestamp']) / SECONDS_IN_DAY), 0) . __(' jours', 'eac-components');
	$items['taggedposts'][$key]['nodeType'] = $edge['node']['__typename'];
	$items['taggedposts'][$key]['caption'] = isset($edge['node']['edge_media_to_caption']['edges'][0]['node']['text']) ? $edge['node']['edge_media_to_caption']['edges'][0]['node']['text'] : '';
	$items['taggedposts'][$key]['shortcode'] = $edge['node']['shortcode'];
	$items['taggedposts'][$key]['update'] = $nbJourDateduJour;
	$items['taggedposts'][$key]['ownerId'] = $edge['node']['owner']['id'];
	$items['taggedposts'][$key]['username'] = $edge['node']['owner']['username'];
	$items['taggedposts'][$key]['linkNode'] = URI_TAGGEDPOSTS . $edge['node']['owner']['username'];
	$items['taggedposts'][$key]['thumbnail_src'] = $edge['node']['thumbnail_src'];
}

unset($endpoints);

echo json_encode($items);