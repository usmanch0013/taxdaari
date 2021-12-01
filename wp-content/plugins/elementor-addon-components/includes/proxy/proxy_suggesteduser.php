<?php

/*===================================================================================
* Description: Collecte les données (Suggested Account) d'un user account par son ID
* Ce module est appelé depuis la requête AJAX 'ajaxCallFeed' de 'eac-components.js'
*
* Utilisation des méthodes de la class 'Endpoints'
* Méthode 'getSuggestedUserAccount' collecte les stories d'un user id account
* Méthode 'setHeaderToken' modifie les propriétés de l'objet Endpoints
*
* @param {string} $_REQUEST['instagram'] le 'id' de l'account
* @param {string} $_REQUEST['token'] csrf token de la session
* @param {string} $_REQUEST['rollout'] instagram-ajax token de la session
* @return Les posts encodés JSON
* @since 1.4.2
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
const URI_SUGGESTED = 'https://www.instagram.com/';
const INDEX_MAX = 50; // 50 entrées
$items = array();
$edges = array();
$index = 0;

if(! $arraySuggestedUser = $endpoints->getSuggestedUserAccount($_REQUEST['instagram'])) {
	exit;
}

$edges = $arraySuggestedUser['edges'];

// Boucle sur toutes les comptes utilisateurs suggérés
foreach($edges as $key => $edge) {
	if(!$edge['node']['is_private']) {
		$items['suggesteduser'][$key]['username'] = URI_SUGGESTED . $edge['node']['username'];
		$items['suggesteduser'][$key]['profile_pic_url'] = $edge['node']['profile_pic_url'];
		$items['suggesteduser'][$key]['is_private'] = $edge['node']['is_private'];
		$items['suggesteduser'][$key]['full_name'] = $edge['node']['full_name'];
		$items['suggesteduser'][$key]['id'] = $edge['node']['id'];
		$index++;
	}
	if($index >= INDEX_MAX) { break; }
}

unset($endpoints);

echo json_encode($items);