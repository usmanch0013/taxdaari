<?php

/*=================================================================================
* Description: Collecte les données pour intégrer un post
* Ce module est appelé depuis la requête AJAX 'ajaxCallFeed' de 'eac-components.js'
*
* Utilisation des méthodes de la class 'Endpoints'
* Méthode 'getEmbedPost' retourne le code HTML (blockquote) d'un shortcode
* Méthode 'setHeaderToken' modifie les propriétés de l'objet Endpoints
*
* @param {string} $_REQUEST['instagram'] le 'shortcode' du post
* @param {string} $_REQUEST['token'] csrf token de la session
* @param {string} $_REQUEST['rollout'] instagram-ajax token de la session
* @return Les posts encodés JSON
* @since 1.3.0
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

// Le contenu blockquote à afficher
$items = array();

// Récupère le code html (blockquote) d'un shortcode pour intégration
if(! $blockquote = $endpoints->getEmbedPost($_REQUEST['instagram'])) {
	exit;
}

$items['embedPost'] = $blockquote;

unset($endpoints);

echo json_encode($items);