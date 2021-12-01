<?php

/*=================================================================================
* Description: Collecte l'URL d'une vidéo d'un post
* Ce module est appelé depuis la requête AJAX 'ajaxCallFeed' de 'eac-components.js'
*
* Utilisation des méthodes de la class 'Endpoints'
* Méthode 'getShortcodePageJson' retourne le contenu d'une page au format JSON
* Méthode 'setHeaderToken' modifie les propriétés de l'objet Endpoints
*
* @param {string} $_REQUEST['instagram'] le 'shortcode' du post
* @param {string} $_REQUEST['tag'] le 'tagname' auquel appartient du post
* @param {string} $_REQUEST['token'] csrf token de la session
* @param {string} $_REQUEST['rollout'] instagram-ajax token de la session
* @return L'URL de la vidéo du post
* @since 1.5.1
* @since 1.5.4 Ajout du paramètre 'tag'
*================================================================================*/

namespace EACCustomWidgets\Proxy;

$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
require_once($parse_uri[0] . 'wp-load.php');

if(!defined('ABSPATH')) { exit; } // Exit if accessed directly

if(!isset($_REQUEST['instagram']) || !isset($_REQUEST['tag'])) { exit; }

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

// @since 1.4.0 Même session, on valorise les tokens
if(isset($_REQUEST['token']) && isset($_REQUEST['rollout'])) {
	$endpoints->setHeaderToken($_REQUEST['token'], $_REQUEST['rollout']);
}

// @since 1.5.4 Ajout du paramètre 'tag' à la requête
// Lecture du contenu du post
if(!$shortcodepost = $endpoints->getShortcodePageJson($_REQUEST['instagram'], $_REQUEST['tag'])) {
	exit;
}

// Affectation de l'url de la video
$items['videoUrl']['url'] = $shortcodepost['video_url'];

unset($endpoints);

echo json_encode($items);