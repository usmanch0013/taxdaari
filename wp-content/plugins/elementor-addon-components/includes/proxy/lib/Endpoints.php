<?php

/*============================================================================================================
* La class Endpoints construit et lance les requêtes vers les serveurs Instagram
* Retourne les données attendues par les proxy respectifs
* Renseigne le journal des erreurs et des codes erreurs HTTP
* Renseigne les traces pour le débuging
*
* @since 1.3.0
* @since 1.3.1	Ajout de la requête sur les stories d'un compte utilisateur
* @since 1.4.0	Ajout du composant lieu (Location)
*				Gestion des token de l'objet Endpoints (X-CSRFToken, X-Instagram-AJAX)
*				Ajout de la requête sur le service 'nominatim' (Open Street Map)
* @since 1.4.1	Ajout des requêtes parallèles (RollingCurlX) pour retrouver respectivement
*				- le nombre de followers composant 'search' (Paramètrage plugin)
*				- le nom des users contributeurs composant 'explore' (Paramètrage plugin)
* @since 1.4.2	Ajout de la requête sur les comptes suggérés d'un compte utilisateur
* @since 1.4.3  Ajout de la requête sur les posts qui ont été identifiés avec un/des comptes utilisateurs
* @since 1.4.4	Remplacement de la méthode 'getUserprofileByName' par 'getUserIdByName'
* @since 1.4.6	Changer 'file_get_contents' par des appels à lib cURL
*				Gestion des proxy
* @since 1.4.7	Correctif pour la recherche de l'identifiant d'un compte utilisateur
* @since 1.5.1	Ajout de la requête pour collecter le contenu d'un article par son Shortcode
* @since 1.5.4	Les données du service 'nominatim' sont enregistrées dans la BDD table des 'options'
*				2ème changement de requête pour charger la page shortcode
* @since 1.6.0	Changement du query_hash pour la requête d'exploration des Tags
*============================================================================================================*/
	
namespace EACCustomWidgets\Proxy\Lib;

// Ajout de la class RollingCurlX, des constantes et de la liste des proxy
require_once __DIR__ . '/Rollingcurlx.class.php';
require_once __DIR__ . '/EndpointsConstantes.php';

use EACCustomWidgets\Proxy\Lib\RollingCurlX;

class Endpoints {
	
	private $requestMediaCount;
	private $userAgent;
	private $csrfToken = ''; // X-CSRFToken
	private $rolloutHash = ''; // X-Instagram-AJAX
	private $accountDataByHash = ''; // Le query_hash pour lire les médias d'un user account par son ID
	private $hashtagDataByHash = ''; // Le query_hash pour lire les médias d'un hashtag par son nom
	private $writelog = false;
	private $resultParallelCurl = array();
	private $proxy = array();
	
	public function __construct() {
		$this->requestMediaCount = 50;
		$rand_ua = array_rand(USER_AGENTS);
		$this->userAgent = USER_AGENTS[$rand_ua];
		
		// Valeur du query_hash par défaut pour les médias d'un user account
		// La valeur objective est recherchée par la fonction getUserQueryId (désactivé)
		// f2405b236d85e8296cf30347c9f08c2a
		// e769aa130647d2354c40ea6a439bfc08
		/** @since 1.6.0 */
		$this->accountDataByHash = 'bfa387b2992c3a52dcbe447467b4b771';
		
		// Valeur du query_hash par défaut pour les médias d'un hashtag
		// La valeur objective est recherchée par la fonction getHashtagQueryId
		// 90cba7a4c91000cf16207e4f3bee2fa2
		/** @since 1.6.0 */
		$this->hashtagDataByHash = '9b498c08113f1e09617a1703c22b2f32';
		
		// @since 1.4.6 Charge un proxy pour la session
		/*$proxylist = getProxyJson();
		if(!empty($proxylist)) {
		    $rand_px = array_rand($proxylist);
		    $this->proxy = $proxylist[$rand_px];
		    $this->write_log("Endpoints::Get proxy list:" . $this->proxy . ":" . count($proxylist));
	    } else {
			$this->proxy = '';
		}*/
		if(!empty(PROXYS)) {
			$rand_px = array_rand(PROXYS);
			$this->proxy = PROXYS[$rand_px];
			$this->write_log("Endpoints::Get proxy constants:" . $this->proxy . ":" . count(PROXYS));
		} else {
			$this->proxy = null;
		}
	}
	
	// @since 1.4.6
	private function getNewProxy() {
		return $this->proxy;
	}
	
	// @since 1.4.6
	private function resetNullProxy() {
		if($this->proxy) {
			$this->write_log("Endpoints::resetNullProxy:" . $this->proxy . "::Using:" . $_SERVER['SERVER_NAME'] . "::" . $_SERVER['SERVER_ADDR']);
			$this->proxy = null;
		}
	}
	
	// @since 1.4.0 Active le debugging
	public function setDebug() {
		$this->writelog = true;
	}
	private function getWriteLog() {
		return $this->writelog;
	}
	/*----*/
	public function setAccountMediasCount($count) {
		$this->requestMediaCount = $count;
	}
	private function getAccountMediasCount() {
		return $this->requestMediaCount;
	}
	private function getUserAgent() {
		return $this->userAgent;
	}
	// 1.4.1 Affecte un nouvel agent-user
	private function getNewUserAgent() {
		$rand_ua = array_rand(USER_AGENTS);
		$this->userAgent = USER_AGENTS[$rand_ua];
		return $this->getUserAgent();
	}
	// @since 1.4.0
	private function setUserQueryHash($qhash) {
		$this->accountDataByHash = $qhash;
	}
	private function getUserQueryHash() {
		return $this->accountDataByHash;
	}
	private function setHashtagQueryHash($qhash) {
		$this->hashtagDataByHash = $qhash;
	}
	private function getHashtagQueryHash() {
		return $this->hashtagDataByHash;
	}
	/*----*/
	// @since 1.4.0 X-CSRFToken && X-Instagram-AJAX
	public function setHeaderToken($token, $rollout) {
		if(empty($this->csrfToken) && ! empty($token)) {
		    // Ecrire dans la log ??
		    if($this->getWriteLog()) { $this->write_log("Debug::Endpoints::setHeaderToken::csrfToken=$token::Rollout=$rollout"); }
			$this->csrfToken = $token;
			$this->rolloutHash = $rollout;
		}
	}
	private function getCsrfToken() {
		return $this->csrfToken;
	}
	private function getRolloutToken() {
		return $this->rolloutHash;
	}
	/*----*/
	
	// Ensemble de 'function' non utilisé
	private function getAccountPageUrl($username) {
		return str_replace('{username}', urlencode($username), ACCOUNT_PAGE);
	}
	private function getAccountJsonUrl($username) {
		return str_replace('{username}', urlencode($username), ACCOUNT_JSON_INFO);
	}
	private function getAccountDataUrlById($id, $cursor) {
		return str_replace(array('{userid}', '{count}', '{endcursor}'), array(urlencode($id), $this->getAccountMediasCount(), $cursor), ACCOUNT_BY_ID);
	}
	
	/*------------------ Shortcode ---------------------*/
	// @since 1.5.1
	private function getShortcodePageUrl($shortcode, $tag = '') {
		return str_replace(array('{shortcode}', '{tagname}'), array(urlencode($shortcode), urlencode($tag)), MEDIA_PAGE_JSON_INFO_2);
	}
	
	// Extrait les données qui se trouve dans la balise 'sharedData'
	// @param {string} $shortcode le libellé du shortcode
	// @return le contenu d'un article par son shortcode
	// @since 1.5.1 Collecte le contenu d'un article
	public function getShortcodePageJson_1($shortcode) {
		$url = filter_var($this->getShortcodePageUrl($shortcode), FILTER_SANITIZE_STRING);
		if(!$insta_source = $this->getFileContents($url)) { return false; }
		
		$shards = explode('window._sharedData = ', $insta_source);
		$insta_json = explode(';</script>', $shards[1]); 
		$insta_array = json_decode($insta_json[0], true);
		
		// Pas de données
		if(empty($insta_array['entry_data']['PostPage'][0]['graphql']['shortcode_media'])) {
			$this->write_log("Endpoints::getShortcodePageJson::$url::SHORTCODE_PAGE=Null");
			return false;
		}
		
		return $insta_array['entry_data']['PostPage'][0]['graphql']['shortcode_media'];
	}
	
	// @since 1.5.4
	public function getShortcodePageJson_2($shortcode) {
		$url = filter_var($this->getShortcodePageUrl($shortcode), FILTER_SANITIZE_STRING);
		if(!$insta_source = $this->getFileContents($url)) { return false; }
		
		$insta_array = json_decode($insta_source, true);
		
		// Pas de données
		if(empty($insta_array['graphql']['shortcode_media'])) {
			$this->write_log("Endpoints::getShortcodePageJson::$url::SHORTCODE_PAGE=Null");
			return false;
		}
		
		return $insta_array['graphql']['shortcode_media'];
	}
	
	// @since 1.5.4 2ème requête
	public function getShortcodePageJson($shortcode, $tag) {
		$url = filter_var($this->getShortcodePageUrl($shortcode, $tag), FILTER_SANITIZE_STRING);
		if(!$insta_source = $this->getFileContents($url)) { return false; }
		
		//$this->write_log("Endpoints::getShortcodePageJson::$insta_source");
		$insta_array = json_decode($insta_source, true);
		
		// Pas de données
		if(empty($insta_array['graphql']['shortcode_media'])) {
			$this->write_log("Endpoints::getShortcodePageJson::$url::SHORTCODE_PAGE=Null");
			return false;
		}
		
		return $insta_array['graphql']['shortcode_media'];
	}
	
	/*------------------ Locations ---------------------*/
	// @since 1.4.0
	private function getLocationJsonUrlByHash($loc, $cursor) {
		$newvar = str_replace(array('{locationid}', '{count}', '{endcursor}'), array($loc, $this->getAccountMediasCount(), $cursor), URI_EXPLORE_LOCATION_BY_HASH_VAR);
		return URI_EXPLORE_LOCATION_BY_HASH_2 . urlencode($newvar);
	}
	// @since 1.4.0
	private function getNominatimJsonUrl($lat, $lng) {
		$newvar = str_replace(array('{lat}', '{lng}'), array(urlencode($lat), urlencode($lng)), URI_NOMINATIM_VAR);
		return URI_NOMINATIM . $newvar;
	}
	
	// @param {long} $location l'id de la location
	// @param {string} $cursor l'identifiant du dernier post pour boucler sur une autre requête
	// @return un tableau de données format JSON d'une location
	// @since 1.4.0 Posts d'une location
	public function getExploreLocationJson($location, $cursor = '') {
		$url = filter_var($this->getLocationJsonUrlByHash($location, $cursor), FILTER_SANITIZE_STRING);
		if(!$insta_source = $this->getCurlData($url)) { return false; }
		//if(!$insta_source = $this->getFileContents($url)) { return false; }
        $data = json_decode($insta_source, true);
		
		if(isset($data['message'])) {
			$message = $data['message'];
			$this->write_log("Endpoints::getExploreLocationJson::$url::$message");
			return false;
		}
		
		if(empty($data['data']['location'])) {
			$this->write_log("Endpoints::getExploreLocationJson::$url::LOCATION=Null");
			return false;
		}
		
		return $data['data']['location'];
	}
	
	// @param {string} $idloc l'ID Instagram du lieu
	// @param {string} $lat la latitude du lieu
	// @param {string} $lng la longitude du lieu
	// @return les données extraites du service Nominatim OSM
	// @since 1.4.0 Appel de service 'nominatim'
	// @since 1.5.4 Récupère ou enregistre (Transient) les résultats de l'appel de service 'reverse nominatim'
	public function getNominatimData($idloc, $lat, $lng) {
		// Le transient existe
		$nomtransient = "eac_nominatim_$idloc" ;
		if(true === ($transient = get_transient($nomtransient))) {
			return $transient;
		}
		
		if(empty($lat) || empty($lng)) { return false; }
		$url = filter_var($this->getNominatimJsonUrl($lat, $lng), FILTER_SANITIZE_STRING);
		$opts = array('http' => array('header' => "User-Agent:" . $this->getNewUserAgent()));
		$context = stream_context_create($opts);
		if(!$insta_source = @file_get_contents($url, false, $context)) { 
			$this->write_log("Endpoints::getNominatimData::" . urldecode($url) . "::Lat_Lng=Null");
			return false;
		}
		$data = json_decode($insta_source, true);
		
		if(empty($data)) {
			$this->write_log("Endpoints::getNominatimData::$url::DATA=Empty");
			return false;
		}
		
		// Enregistre les données décodées dans la table options de la BDD pour un mois
		set_transient($nomtransient, $data, MONTH_IN_SECONDS);
		
		return $data;
	}
	
	/*------------------ Hashtags ---------------------*/
	private function getExplorePageUrl($tag) {
		return str_replace('{tagname}', urlencode($tag), URI_EXPLORE_PAGE);
	}
	private function getExploreJsonUrl($tag, $cursor) {
		if($cursor !== '') { return str_replace(array('{tagname}', '{endcursor}'), array(urlencode($tag), urlencode($cursor)), URI_EXPLORE_PAGE_JSON_1); }
		else { return str_replace('{tagname}', urlencode($tag), URI_EXPLORE_PAGE_JSON); }
		
	}
	private function getExploreJsonUrlByHash($tag, $cursor) {
		$newvar = str_replace(array('{tagname}', '{count}', '{endcursor}'), array($tag, $this->getAccountMediasCount(), $cursor), URI_EXPLORE_PAGE_JSON_BY_HASH_VAR);
		return str_replace('{queryhash}', $this->getHashtagQueryHash(), URI_EXPLORE_PAGE_JSON_BY_HASH_2) . urlencode($newvar);
	}
	private function getExploreJsonUrlById($tag, $cursor) {
		return str_replace(array('{tagname}', '{endcursor}'), array(urlencode($tag), $cursor), URI_EXPLORE_PAGE_JSON_BY_ID);
	}
	
	// Extrait les données qui se trouve dans la balise 'sharedData'
	// @param {string} $hashtag le libellé du hashtag
	// @return le profile d'une page par son hashtag (Module Explore)
	public function getExploreHashtagPage($hashtag) {
		$url = filter_var($this->getExplorePageUrl($hashtag), FILTER_SANITIZE_STRING);
		if(!$insta_source = $this->getFileContents($url)) { return false; }
		
		$shards = explode('window._sharedData = ', $insta_source);
		$insta_json = explode(';</script>', $shards[1]); 
		$insta_array = json_decode($insta_json[0], true);
		
		// @since 1.4.0 Gestion des tokens
		if(!empty($insta_array['config']['csrf_token'])) {
			$csrf = $insta_array['config']['csrf_token'];
			$rollout = $insta_array['rollout_hash'];
			$insta_array['entry_data']['TagPage'][0]['graphql']['hashtag']['config']['csrf_token'] = $csrf;
			$insta_array['entry_data']['TagPage'][0]['graphql']['hashtag']['config']['rollout_hash'] = $rollout;
			
			// Conserve les tokens
			$this->setHeaderToken($csrf, $rollout);
		}
		
		// Pas de données
		if(!isset($insta_array['entry_data']['TagPage'][0]['graphql']['hashtag'])) {
			$this->write_log("Endpoints::getExploreHashtagPage::$url::HASHTAG_PAGE=Null");
			return false;
		}
		
		// @since 1.5.5 Pas de Name ou d'ID
		if(!isset($insta_array['entry_data']['TagPage'][0]['graphql']['hashtag']['name']) || !isset($insta_array['entry_data']['TagPage'][0]['graphql']['hashtag']['id'])) {
		    $this->write_log("Endpoints::getExploreHashtagPage::$url::NAME_OR_ID=Null");
		    //$this->write_log("Endpoints::getExploreHashtagPage::$url::" . json_encode($insta_array));
			return false;
		}
		
		// On conserve le query_hash de la session
		$hash = $this->getHashtagQueryId($insta_source);
		if($hash) {
			$this->setHashtagQueryHash($hash);
			$insta_array['entry_data']['TagPage'][0]['graphql']['hashtag']['config']['query_hash'] = $hash;
		}
		
		return $insta_array['entry_data']['TagPage'][0]['graphql']['hashtag'];
	}
	
	// @param {string} $hashtag le libellé du hashtag
	// @param {mixed} $cursor l'identifiant du dernier post pour boucler sur une autre requête
	// @return un tableau de posts format JSON d'un hashtag (Non utilisé)
	public function getExploreHashtagJson($hashtag, $cursor = '') {
		//$url = filter_var($this->getExploreJsonUrlById($hashtag, $cursor), FILTER_SANITIZE_STRING);
		//$url = filter_var($this->getExploreJsonUrl($hashtag, $cursor), FILTER_SANITIZE_STRING);
		
		$url = filter_var($this->getExploreJsonUrlByHash($hashtag, $cursor), FILTER_SANITIZE_STRING);
		if(!$insta_source = $this->getFileContents($url)) { return false; }
		$data = json_decode($insta_source, true);
		
		// Pas de données
		if(empty($data['data']['hashtag'])) {
			$this->write_log("Endpoints::getExploreHashtagJson::$url::DATA=Null");
			return false;
		}
		
		return $data['data']['hashtag'];
	}
	
	// Charge le fichier 'TagPageContainer.js' et prélève le query_hash pour les requêtes de la session
	// @param {file} le contenu du fichier html d'un hashtag
	// @return le query_hash
	// @since 1.4.0
	protected function getHashtagQueryId($html) {
		$re = '#href="((.*)TagPageContainer\.js/([0-9a-zA-Z]+)\.js)#';
	    preg_match($re, $html, $matches);
	  	$file = INSTA_BASE . $matches[1];
		if(!$insta_source = @file_get_contents($file)) {
			return false;
		}
		
	    // 4 queryId (query_hash) dans le fichier JS. On matche le 3ème (.tagMedia.byTagName.get.... s.pagination},queryId:)
		preg_match_all('/queryId\:"([a-zA-Z0-9]+){1}/', $insta_source, $matches);
		return $matches[1][2];
	}
	
	/*------------------ Search Users, Hashtags and Places ---------------------*/
	private function getSearchUrl($name, $ctx = 'blended') {
		return str_replace(array('{context}', '{username}'), array($ctx, urlencode($name)), URI_SEARCH_UHP);
	}
	
	// @param {string} $keyword le libellé du user account, du hahstag ou de la place
	// @param {string} $context le contexte de recherche
	// @return les profiles user account, hashtag ou place (Module Search)
	// @comment Instagram renvoie toujours des tableaux qui peuvent être vides
	public function getProfilUserHashtagPlace($keyword, $context = '') {
		$url = filter_var($this->getSearchUrl($keyword, $context), FILTER_SANITIZE_STRING);
		if(!$insta_source = $this->getFileContents($url)) { return false; }
		//$this->write_log("Endpoints::getProfilUserHashtagPlace::$url::" . json_encode($insta_source));
		return json_decode($insta_source, true);
	}
	
	/*------------------ Likes, Comments et Slidecar/Video ---------------------*/
	private function getLikeUrlById($shortcode) {
		return str_replace('{shortcode}', urlencode($shortcode), URI_LIKE_POST_BY_ID);
	}
	private function getLikeUrlByHash($shortcode) {
		$newvar = str_replace('{shortcode}', $shortcode, URI_LIKE_POST_BY_HASH_VAR);
		return URI_LIKE_POST_BY_HASH . urlencode($newvar);
	}
	private function getCommentUrlById($shortcode) {
		return str_replace('{shortcode}', urlencode($shortcode), URI_COMMENT_POST_BY_ID);
	}
	private function getCommentUrlByHash($shortcode) {
		$newvar = str_replace('{shortcode}', $shortcode, URI_COMMENT_POST_BY_HASH_VAR);
		return URI_COMMENT_POST_BY_HASH . urlencode($newvar);
	}
	private function getEmbedUrl($shortcode) {
		return str_replace('{shortcode}', urlencode($shortcode), URI_EMBEDED_POST_WITHOUT_SCRIPT);
	}
	
	// @param {mixed} $sc shortcode du post
	// @return les likes (50) associés à un post
	public function getPostLikes($sc) {
		//$url = filter_var($this->getLikeUrlById($sc), FILTER_SANITIZE_STRING);
		$url = filter_var($this->getLikeUrlByHash($sc), FILTER_SANITIZE_STRING);
		if(!$insta_source = $this->getFileContents($url)) { return false; }
		$data = json_decode($insta_source, true);
		
		if(empty($data['data']['shortcode_media']['edge_liked_by']['edges'])) {
			return false;
		}
		
		return $data['data']['shortcode_media']['edge_liked_by']['edges'];
	}
	
	// @param {mixed} $sc shortcode du post
	// @return les commentaires (50) associés à un post
	public function getPostComments($sc) {
		//$url = filter_var($this->getCommentUrlById($sc), FILTER_SANITIZE_STRING);
		$url = filter_var($this->getCommentUrlByHash($sc), FILTER_SANITIZE_STRING);
		if(!$insta_source = $this->getFileContents($url)) { return false; }
		$data = json_decode($insta_source, true);
		
		if(empty($data['data']['shortcode_media']['edge_media_to_comment']['edges'])) {
			return false;
		}
		
		return $data['data']['shortcode_media']['edge_media_to_comment']['edges'];
	}
	
	// Code d'intégration du post
	// @param {mixed} $sc le shortcode du post
	// @return les données html '<blockquote></blockquote><script></script>'
	public function getEmbedPost($sc) {
		$url = filter_var($this->getEmbedUrl($sc), FILTER_SANITIZE_STRING);
		if(!$insta_source = $this->getFileContents($url)) { return false; }
		$data = json_decode($insta_source, true);
		
		if(isset($data['html'])) {
			return $data['html'];
		}
		
		return false;
	}
	
	/*------------------ Multi Usernames ---------------------*/
	// Gestion des options pour les requêtes RollingCurlX
	private function cUrlOptionHandler() {
		$proxy = $this->getNewProxy();
		
		// add additional curl options here
		return array(CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => false, CURLOPT_FOLLOWLOCATION => true, CURLOPT_ENCODING => "", CURLOPT_TIMEOUT => OPT_TIMEOUT,
		CURLOPT_CONNECTTIMEOUT => OPT_C_TIMEOUT, CURLOPT_MAXREDIRS => 3, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_USERAGENT => $this->getNewUserAgent(),			
		CURLOPT_HTTPPROXYTUNNEL => $proxy !== null ? true : false, CURLOPT_PROXY => $proxy !== null ? $proxy : "",
		);
	}
	
	private function getAccountInfoById($id) {
		$newvar = str_replace('{userid}', $id, ACCOUNT_JSON_INFO_BY_ID_BY_HASH_VAR);
		return ACCOUNT_JSON_INFO_BY_ID_BY_HASH . urlencode($newvar);
	}
	
	// Affecte le username et son user picture au tableau global
	// @param {} Voir les specs RollingCurlX
	// @since 1.4.1
	public function onRequestDoneUserName($data, $url, $request_info, $idx, $time) {
		$httpcode = $request_info['http_code'];
		if($httpcode !== 200) {
			if($httpcode === 429) { // Trop de requêtes
				$this->write_log('Endpoints::onRequestDoneUserName: ' . __("Erreur 429: Trop de requêtes.", "eac-components"));
			} else {
				$this->write_log('Endpoints::onRequestDoneUserName HTTP CODE=' . $httpcode . "::Message=" . $request_info['curle_msg']);
			}
			return;
		}
		
		$data_username = json_decode($data, true);
		$userid = $data_username['data']['user']['reel']['user']['id'];
		$username = $data_username['data']['user']['reel']['user']['username'];
		$userpic = $data_username['data']['user']['reel']['user']['profile_pic_url'];
		// On affecte le username et son user picture au tableau global
		$this->resultParallelCurl['user'][$idx['index']]['username'] = $username;
		$this->resultParallelCurl['user'][$idx['index']]['username_pic'] = $userpic;
	}
	
	// @param {array} $ids des user account
	// @return tableau de username et username picture
	// @since 1.4.1
	public function getMultiUsernameById($ids = array()) {
		$urls = array();
		foreach($ids as $id) {
			$url = filter_var($this->getAccountInfoById($id), FILTER_SANITIZE_STRING);
			array_push($urls, $url);
		}
		
		// Instance RollingCurlX
		$parallel_curl = new RollingCurlX(5);

		foreach($urls as $key => $url) {
			// Set cURL header
			$parallel_curl->setHeaders(['Content-Type:application/x-www-form-urlencoded', 'X-Requested-With:XMLHttpRequest']);
			// Place la requête dans la file
			$parallel_curl->addRequest($url, null, array($this, 'onRequestDoneUserName'),  array('index' => $key), $this->cUrlOptionHandler());
		}

		// On attend que toutes les requêtes soient terminées
		$parallel_curl->execute();
		
		return ! empty($this->resultParallelCurl) ? $this->resultParallelCurl : false;
	}
	
	/*------------------ Multi Followers ---------------------*/
	// @since 1.4.1
	private function getFollowerById($id) {
		$newvar = str_replace('{userid}', $id, URI_FOLLOWERS_BY_HASH_VAR);
		return URI_FOLLOWERS_BY_HASH . urlencode($newvar);
	}
	
	// Affecte le nombre de followers au tableau global
	// @param {} Voir les specs RollingCurlX
	// @since 1.4.1
	public function onRequestDoneFollower($data, $url, $request_info, $idx, $time) {
		$httpcode = $request_info['http_code'];
		if($httpcode !== 200) {
			if($httpcode === 429) { // Trop de requêtes
				$this->write_log('Endpoints::onRequestDoneFollower: ' . __("Erreur 429: Trop de requêtes.", "eac-components"));
			} else {
				$this->write_log('Endpoints::onRequestDoneFollower HTTP CODE=' . $httpcode . "::Message=" . $request_info['curle_msg']);
			}
			// On affecte l'indice à 0
			$this->resultParallelCurl[$idx['index']] = 0;
			return;
		}
		
		$data_follower = json_decode($data, true);
		$followers = isset($data_follower['data']['user']['edge_followed_by']['count']) ? $data_follower['data']['user']['edge_followed_by']['count'] : 0;
		// On affecte le nombre de followers au tableau global
		$this->resultParallelCurl[$idx['index']] = $followers;
	}
	
	// @param {array} $ids des user account
	// @return tableau du nombre de followers
	// @since 1.4.1
	public function getMultiFollowerById($ids = array()) {
		$urls = array();
		foreach($ids as $id) {
			$url = filter_var($this->getFollowerById($id), FILTER_SANITIZE_STRING);
			array_push($urls, $url);
		}
		
		// Instance RollingCurlX
		$parallel_curl = new RollingCurlX(5);

		foreach($urls as $key => $url) {
			// Set cURL header
			$parallel_curl->setHeaders(['Content-Type:application/x-www-form-urlencoded', 'X-Requested-With:XMLHttpRequest']);
			// Place la requête est dans la file
			$parallel_curl->addRequest($url, null, array($this, 'onRequestDoneFollower'),  array('index' => $key), $this->cUrlOptionHandler());
		}

		// On attend que toutes les requêtes soient terminées
		$parallel_curl->execute();
		
		return ! empty($this->resultParallelCurl) ? $this->resultParallelCurl : false;
	}
	
	/*------------------ User account ---------------------*/
	private function getAccountInfoByShortcode($sc) {
		return str_replace('{shortcode}', urlencode($sc), ACCOUNT_JSON_INFO_BY_SC);
	}
	// @since 1.4.4
	private function getAccountIdByUsername($username) {
		return str_replace('{username}', urlencode($username), ACCOUNT_JSON_INFO_BY_NAME);
	}
	private function getAccountDataUrlByHash($id, $cursor) {
		$newvar = str_replace(array('{userid}', '{count}', '{endcursor}'), array($id, $this->getAccountMediasCount(), $cursor), ACCOUNT_BY_HASH_VAR);
		return str_replace('{queryhash}', $this->getUserQueryHash(), ACCOUNT_BY_HASH_2) . urlencode($newvar);
	}
	// @since 1.3.1
	private function getAccountStoriesByHash($id) {
		$newvar = str_replace('{userid}', $id, ACCOUNT_STORIES_BY_HASH_VAR);
		return ACCOUNT_STORIES_BY_HASH . urlencode($newvar);
	}
	// @since 1.4.2
	private function getSuggestedProfileById($id) {
		$newvar = str_replace('{userid}', $id, ACCOUNT_SUGGESTED_BY_HASH_VAR);
		return ACCOUNT_SUGGESTED_BY_HASH . urlencode($newvar);
	}
	// @since 1.4.3
	private function getAccountTaggedPostsById($id) {
		$newvar = str_replace('{userid}', $id, ACCOUNT_TAGGED_POSTS_BY_HASH_VAR);
		return ACCOUNT_TAGGED_POSTS_BY_HASH . urlencode($newvar);
	}
	
	// @param {mixed} $sc le sortcode du post
	// @return le nom du user qui a créé le post
	public function getUsernameByShortcode($sc) {
		$url = filter_var($this->getAccountInfoByShortcode($sc), FILTER_SANITIZE_STRING);
		if(!$insta_source = $this->getFileContents($url)) { return false; }
		$data = json_decode($insta_source, true);
		
		return !empty($data['author_name']) ? $data['author_name'] : false;
	}
	
	// @param {long} $id du user account
	// @return un username
	public function getUsernameById($id) {
		$url = filter_var($this->getAccountInfoById($id), FILTER_SANITIZE_STRING);
		if(!$insta_source = $this->getFileContents($url)) { return false; }
		$data = json_decode($insta_source, true);
		
		return $data['data']['user']['reel']['user']['username'];
	}
	
	// La méthode 'getUserprofileByName' retourne la page de login et plus le contenu de la balise 'sharedData'
	// @param {string} $username du user account
	// @return le profile d'un user account (Module User Account)
	// @since 1.4.4
	public function getUserIdByName($username) {
		$dataUser;
		$url = filter_var($this->getAccountIdByUsername($username), FILTER_SANITIZE_STRING);
		if(!$insta_source = $this->getFileContents($url)) {
			//echo __("La requête a échouée<br>", "eac-components");
			return false;
		}
		$data = json_decode($insta_source, true);
		
		// @since 1.4.7 On matche le bon username. Il ne doit pas être privé
		if(!empty($data['users']) && is_array($data['users'])) {
			foreach($data['users'] as $user) {
				if($user['user']['username'] === $username && $user['user']['is_private'] == false) {
					$dataUser = $user;
					break;
					//echo $dataUser['user']['username'] . "::" . $dataUser['user']['pk'];
				}
			}
		}
		
		// Tester l'id avant le file_get_contents
		if(isset($dataUser['user']['pk'])) {
			$followers = @file_get_contents($this->getFollowerById($dataUser['user']['pk']));
			$followersCount = json_decode($followers, true);
			$dataUser['user']['edge_followed_by']['count'] = isset($followersCount['data']['user']['edge_followed_by']['count']) ? $followersCount['data']['user']['edge_followed_by']['count'] : 0;
			//echo "==>" . $dataUser['user']['username'] . "::" . $dataUser['user']['pk'] . "::" . $dataUser['user']['edge_followed_by']['count'];
		} else {
			echo __("ID de l'utilisateur non trouvé ou compte privé<br>", "eac-components");
			$this->write_log("Endpoints::getUserIdByName:$url::ID=Not found");
			return false;
		}
		
		return $dataUser['user'];
	}
	
	// @param {long} $id du user account
	// @param {mixed} $cursor l'identifiant du dernier post pour boucler sur une autre requête
	// @return un tableau posts format JSON d'un user account
	public function getAccountDataById($id, $cursor = '') {
		$url = filter_var($this->getAccountDataUrlByHash($id, $cursor), FILTER_SANITIZE_STRING);
		if(!$insta_source = $this->getFileContents($url)) { return false; }
		$data = json_decode($insta_source, true);
		
		// Pas de données
		if(empty($data['data']['user']['edge_owner_to_timeline_media'])) {
			$this->write_log("Endpoints::getAccountDataById:$url::DATA=Null");
			return false;
		}
		
		return $data['data']['user']['edge_owner_to_timeline_media'];
	}
	
	// @param {long} $id du user account
	// @param {boolean} $count
	// @return le nombre d'éléments ou le tableau des users accounts
	// @since 1.4.3 Gestion des posts d'un user account tagués par d'autres users account
	public function getTaggedPostsUserAccount($id, $countNb = false) {
		$url = filter_var($this->getAccountTaggedPostsById($id), FILTER_SANITIZE_STRING);
		if($this->getWriteLog()) { $this->write_log("Debug::getTaggedPostsUserAccount:$url"); }
		if(!$insta_source = @file_get_contents($url)) { return false; }
		$data = json_decode($insta_source, true);
		
		// Nombre ou liste des comptes
		if($countNb) {
			return !empty($data['data']['user']['edge_user_to_photos_of_you']['count']) ? $data['data']['user']['edge_user_to_photos_of_you']['count'] : false;
		} else {
			return !empty($data['data']['user']['edge_user_to_photos_of_you']) ? $data['data']['user']['edge_user_to_photos_of_you'] : false;
		}

	}
	
	// @param {long} $id du user account
	// @param {boolean} $count
	// @return le nombre d'éléments ou le tableau de suggested user account
	// @since 1.4.2 Gestion des comptes utilisateurs suggérés
	public function getSuggestedUserAccount($id, $countNb = false) {
		$url = filter_var($this->getSuggestedProfileById($id), FILTER_SANITIZE_STRING);
		if($this->getWriteLog()) { $this->write_log("Debug::getSuggestedUserAccount:$url"); }
		if(!$insta_source = @file_get_contents($url)) { return false; }
		$data = json_decode($insta_source, true);
		
		// Nombre ou liste des comptes
		if($countNb) {
			return !empty($data['data']['user']['edge_related_profiles']['edges']) ? count($data['data']['user']['edge_related_profiles']['edges']) : false;
		} else {
			return !empty($data['data']['user']['edge_related_profiles']) ? $data['data']['user']['edge_related_profiles'] : false;
		}

	}
	
	// @param {long} $id du user account
	// @return les tableaux de données 'reel' et 'edge_highlight_reels' au format JSON
	// @since 1.3.1 Stories d'un user account
	public function getUserAccountStories($id) {
		$url = filter_var($this->getAccountStoriesByHash($id), FILTER_SANITIZE_STRING);
		if($this->getWriteLog()) { $this->write_log("Debug::getUserAccountStories:$url"); }
		if(!$insta_source = @file_get_contents($url)) { return false; }
		$data = json_decode($insta_source, true);
		
		if(empty($data['data']['user'])) {
			$this->write_log("Endpoints::getUserAccountStories::$url::$id=Null");
			return false;
		}
		
		return $data['data']['user'];
	}
	
	/*------------------ Utils ---------------------*/
	
	// @param (string) $url des données à collecter
	// @return les données collectées
	// @since 1.4.6 Changer 'file_get_contents' par des appels à lib cURL
	private function getCurlData($url) {
		$proxy = $this->getNewProxy();
		$newagent = $this->getNewUserAgent();
		$csrf = $this->getCsrfToken();
		$rollout = $this->getRolloutToken();
		$caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
		$referer = "http://www.google.com/";
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, OPT_TIMEOUT);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, OPT_C_TIMEOUT);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		
		if($proxy !== null) { 
			curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
		}
		curl_setopt($ch, CURLOPT_USERAGENT, $newagent);
		if(!empty($this->getCsrfToken())) { curl_setopt($ch, CURLOPT_HTTPHEADER, array(
												"Content-Type:application/x-www-form-urlencoded",
												"X-Requested-With:XMLHttpRequest",
												"X-CSRFToken:$csrf",
												"X-Instagram-AJAX:$rollout"
											));
		} else { curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					"Content-Type:application/x-www-form-urlencoded",
					"X-Requested-With:XMLHttpRequest"
				));
		}
		
		// Ecrire dans la log
		if($this->getWriteLog()) {
			$this->write_log("Debug::Endpoints::getCurlData::$url\r\nProxy=$proxy::Caller=$caller");
		}
		
		$data_source = curl_exec($ch);
		$errno = curl_errno($ch);
		$error = curl_error($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		// Code HTTP <> 200 et 0
		if($http_code !== 200 && $http_code !== 0) {
			$this->sendHttpException("Endpoints::getCurlData::Caller=$caller::$url::HttpCode=$http_code");
			return false;
		}
		
		// Gestion des erreurs cURL
		if($errno) {
			$errnoInfo = $this->getCurlError($errno);
			if($proxy) {
				$this->write_log("Endpoints::getCurlData::$url\r\nProxy=$proxy::Error=$error::Errno=$errno::Info=$errnoInfo::Caller=$caller");
				// On supprime le proxy de la session
				$this->resetNullProxy();
			} else {
				$this->write_log("Endpoints::getCurlData::$url\r\nErrno=$errno::Info=$errnoInfo::Caller=$caller");
			}
			return false;
		}
		
		return $data_source;
	}
	
	// Gestions des appels de service vers Instagram
	// @param {string} URL de la requête
	// @return données non décodées
	private function getFileContents($url) {
		$caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
		// Ecrire dans la log ??
		if($this->getWriteLog()) { $this->write_log("Debug::Endpoints::getFileContents:$url::Caller=$caller"); }
		$this->setInfoHeader();
		if(!$insta_source = @file_get_contents($url)) {
			$error = error_get_last();
			$this->sendHttpException("Endpoints::getFileContents::Caller=$caller::" . $error['message'] . "::HttpCode=" . $http_response_header[0]);
			error_clear_last();
			return false;
		}
		//print_r(get_headers($url, 0));
		//var_dump($http_response_header);
		//console_log($this->parseHeaders($http_response_header));
		return $insta_source;
	}
	
	// @return Modifie le header d'une requête
	/*private function setInfoHeader() {
		$proxy = $this->getNewProxy();
		$newagent = $this->getNewUserAgent();
		$csrf = $this->getCsrfToken();
		$rollout = $this->getRolloutToken();
		
		stream_context_set_default(
			array(
				"http" => array(
					"method" => "GET",
					//"proxy" => "tcp://$proxy",
					//"request_fulluri" => true,
					"header" =>	array(
						"Content-Type:application/x-www-form-urlencoded",
						"User-Agent:$newagent",
						"X-Requested-With:XMLHttpRequest",
						"X-CSRFToken:$csrf",
						"X-Instagram-AJAX:$rollout"
					)
				),
				"ssl" => array(
					"verify_peer" => false,
					"verify_peer_name" => false
				)
			)
		);
	}*/
	
	// @return Modifie le header d'une requête
	private function setInfoHeader() {
		$newagent = $this->getUserAgent();
		$csrf = $this->getCsrfToken();
		$rollout = $this->getRolloutToken();
		// Création d'une adresse IP aléatoire
		$dynamic_ip = "" . mt_rand(0, 255) . "." . mt_rand(0, 255) . "." . mt_rand(0, 255) . "." . mt_rand(0, 255);
		
		header("Content-Type:application/x-www-form-urlencoded");
		//header("Content-Type:application/json; charset=utf-8");
		header("User-Agent:$newagent");
		header("X-Requested-With:XMLHttpRequest");
		header("HTTP_CLIENT_IP:$dynamic_ip");
		header("REMOTE_ADDR:$dynamic_ip");
		header("HTTP_X_FORWARDED_FOR:$dynamic_ip");
		//header("Cross-Origin-Resource-Policy:*"); // @since 1.8.7
		if(!empty($csrf)) {
			header("X-CSRFToken:$csrf");
			header("X-Instagram-AJAX:$rollout");
		}
	}
	
	private function parseHeaders($headers) {
		//echo json_encode($this->parseHeaders($http_response_header));
		$head = array();
		foreach($headers as $k => $v) {
			$t = explode(':', $v, 2);
			if(isset($t[1]))
				$head[trim($t[0])] = trim($t[1]) . "<br>";
			else {
				$head[] = $v;
				if(preg_match("#HTTP/[0-9\.]+\s+([0-9]+)#",$v, $out))
					$head['reponse_code'] = intval($out[1]) . "<br>";
			}
		}
		return $head;
	}
	
	// Gestions des exceptions HTTP
	// @param {string} le contenu de l'erreur HTTP
	// @return nothing
	private function sendHttpException($erreur) {
		$this->echoHttpError($erreur);
		$this->write_log("Endpoints::sendHttpException::$erreur");
	}
	
	// Gestion du dossier et du fichier d'erreur
	private function write_log($log_msg) {
        $log_filename = EAC_PLUGIN_DIR_PATH . "/includes/eac-log";
        if (!file_exists($log_filename)) {
            // create directory/folder uploads.
            mkdir($log_filename, 0777, true);
        }
        $log_file_data = $log_filename . '/log_' . date('d-M-Y') . '.log';
		file_put_contents($log_file_data, urldecode($log_msg) . "\n-----\n", FILE_APPEND);
    }
	
	private function echoHttpError($errstr) {
		if(preg_match("/(404)/", $errstr)) {
			echo __("Erreur 404: La page demandée n'existe pas<br>", "eac-components"); // File not found.
		} else if(preg_match("/(403)/", $errstr)) {
			echo __("Erreur 403: Accès refusé<br>", "eac-components"); // Unauthorized.
		} else if(preg_match("/(401)/", $errstr)) {
			echo __("Erreur 401: Non autorisé<br>", "eac-components"); // Forbidden.
		} else if(preg_match("/(503)/", $errstr)) {
			echo __("Erreur 503: Service indisponible. Réessayer plus tard<br>", "eac-components"); // Service unavailable. Retry later.
		} else if(preg_match("/(405)/", $errstr)) {
			echo __("Erreur 405: Méthode non autorisée<br>", "eac-components"); // Method not allowed.
		} else if(preg_match("/(500)/", $errstr)) {
			echo __("Erreur 500: Erreur Interne du Serveur<br>", "eac-components"); // Internal Server Error.
		} else if(preg_match("/(400)/", $errstr)) {
			echo __("Erreur 400: La requête est erronée<br>", "eac-components"); // Bad Request.
		} else if(preg_match("/(429)/", $errstr)) {
			echo __("Erreur 429: Trop de requêtes<br>", "eac-components"); // Too many requests.
		} else {
			echo __("Erreur inconnue. Recommencer plus tard<br>", "eac-components"); // Request failed. Retry later.
		}
	}
	
	// @return conversion des nombres en Kilos et Millions
	public function convertNumber($n, $lng) {
		$n = (0 + str_replace(",", "", $n));
		if($n > 1000000) return round(($n / 1000000), $lng).'m';
		else if($n > 1000) return round(($n / 1000), $lng).'k';
		else return $n;
	}
	
	// @return la différence formatée entre deux dates
	public function timestampDiff($qw, $saw) {
		$datetime1 = new \DateTime("@$qw");
		$datetime2 = new \DateTime("@$saw");
		$interval = $datetime1->diff($datetime2);
		return $interval->format('%y Ans %m Mois %d Jours');
		//return $interval->format('%m Mois %d Jours');
	}
	
	// @return le libellé de l'erreur cUrl
	private function getCurlError($err) {
		
		$error_codes = array(
			1 => 'CURLE_UNSUPPORTED_PROTOCOL',	2 => 'CURLE_FAILED_INIT', 3 => 'CURLE_URL_MALFORMAT', 4 => 'CURLE_URL_MALFORMAT_USER', 5 => 'CURLE_COULDNT_RESOLVE_PROXY', 6 => 'CURLE_COULDNT_RESOLVE_HOST',
			7 => 'CURLE_COULDNT_CONNECT', 8 => 'CURLE_FTP_WEIRD_SERVER_REPLY', 9 => 'CURLE_REMOTE_ACCESS_DENIED', 11 => 'CURLE_FTP_WEIRD_PASS_REPLY', 13 => 'CURLE_FTP_WEIRD_PASV_REPLY',
			14 =>'CURLE_FTP_WEIRD_227_FORMAT', 15 => 'CURLE_FTP_CANT_GET_HOST', 17 => 'CURLE_FTP_COULDNT_SET_TYPE', 18 => 'CURLE_PARTIAL_FILE', 19 => 'CURLE_FTP_COULDNT_RETR_FILE',
			21 => 'CURLE_QUOTE_ERROR', 22 => 'CURLE_HTTP_RETURNED_ERROR', 23 => 'CURLE_WRITE_ERROR', 25 => 'CURLE_UPLOAD_FAILED', 26 => 'CURLE_READ_ERROR', 27 => 'CURLE_OUT_OF_MEMORY',
			28 => 'CURLE_OPERATION_TIMEDOUT', 30 => 'CURLE_FTP_PORT_FAILED', 31 => 'CURLE_FTP_COULDNT_USE_REST', 33 => 'CURLE_RANGE_ERROR', 34 => 'CURLE_HTTP_POST_ERROR', 35 => 'CURLE_SSL_CONNECT_ERROR',
			36 => 'CURLE_BAD_DOWNLOAD_RESUME', 37 => 'CURLE_FILE_COULDNT_READ_FILE', 38 => 'CURLE_LDAP_CANNOT_BIND', 39 => 'CURLE_LDAP_SEARCH_FAILED', 41 => 'CURLE_FUNCTION_NOT_FOUND', 42 => 'CURLE_ABORTED_BY_CALLBACK',
			43 => 'CURLE_BAD_FUNCTION_ARGUMENT', 45 => 'CURLE_INTERFACE_FAILED', 47 => 'CURLE_TOO_MANY_REDIRECTS', 48 => 'CURLE_UNKNOWN_TELNET_OPTION', 49 => 'CURLE_TELNET_OPTION_SYNTAX',
			51 => 'CURLE_PEER_FAILED_VERIFICATION', 52 => 'CURLE_GOT_NOTHING', 53 => 'CURLE_SSL_ENGINE_NOTFOUND', 54 => 'CURLE_SSL_ENGINE_SETFAILED', 55 => 'CURLE_SEND_ERROR', 56 => 'CURLE_RECV_ERROR',
			58 => 'CURLE_SSL_CERTPROBLEM', 59 => 'CURLE_SSL_CIPHER', 60 => 'CURLE_SSL_CACERT', 61 => 'CURLE_BAD_CONTENT_ENCODING', 62 => 'CURLE_LDAP_INVALID_URL', 63 => 'CURLE_FILESIZE_EXCEEDED',
			64 => 'CURLE_USE_SSL_FAILED', 65 => 'CURLE_SEND_FAIL_REWIND', 66 => 'CURLE_SSL_ENGINE_INITFAILED', 67 => 'CURLE_LOGIN_DENIED', 68 => 'CURLE_TFTP_NOTFOUND', 69 => 'CURLE_TFTP_PERM',
			70 => 'CURLE_REMOTE_DISK_FULL', 71 => 'CURLE_TFTP_ILLEGAL', 72 => 'CURLE_TFTP_UNKNOWNID', 73 => 'CURLE_REMOTE_FILE_EXISTS', 74 => 'CURLE_TFTP_NOSUCHUSER', 75 => 'CURLE_CONV_FAILED',
			76 => 'CURLE_CONV_REQD', 77 => 'CURLE_SSL_CACERT_BADFILE', 78 => 'CURLE_REMOTE_FILE_NOT_FOUND', 79 => 'CURLE_SSH', 80 => 'CURLE_SSL_SHUTDOWN_FAILED', 81 => 'CURLE_AGAIN',
			82 => 'CURLE_SSL_CRL_BADFILE', 83 => 'CURLE_SSL_ISSUER_ERROR', 84 => 'CURLE_FTP_PRET_FAILED', 84 => 'CURLE_FTP_PRET_FAILED', 85 => 'CURLE_RTSP_CSEQ_ERROR',
			86 => 'CURLE_RTSP_SESSION_ERROR', 87 => 'CURLE_FTP_BAD_FILE_LIST', 88 => 'CURLE_CHUNK_FAILED');
			
		return $error_codes[$err];
	}
	
	public function sanitizeplace($placename) {
		/* Force the place name in UTF-8 (encoding Windows / OS X / Linux) */
		$placename = mb_convert_encoding($placename, "UTF-8");

		$char_not_clean = array('/À/','/Á/','/Â/','/Ã/','/Ä/','/Å/','/Ç/','/È/','/É/','/Ê/','/Ë/','/Ì/','/Í/','/Î/','/Ï/','/Ò/','/Ó/','/Ô/','/Õ/','/Ö/','/Ù/','/Ú/','/Û/','/Ü/','/Ý/','/à/','/á/','/â/','/ã/','/ä/','/å/','/ç/','/è/','/é/','/ê/','/ë/','/ì/','/í/','/î/','/ï/','/ð/','/ò/','/ó/','/ô/','/õ/','/ö/','/ù/','/ú/','/û/','/ü/','/ý/','/ÿ/', '/©/');
		$clean = array('a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','o','o','o','o','o','u','u','u','u','y','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','o','o','o','o','o','o','u','u','u','u','y','y','copy');

		$friendly_place = preg_replace($char_not_clean, $clean, $placename);

		/* After replacement, we destroy the last residues */
		$friendly_place = utf8_decode($friendly_place);
		$friendly_place = preg_replace('/\?/', '', $friendly_place);

		/* Lowercase */
		$friendly_place = strtolower($friendly_place);
		$friendly_place = sanitize_title($friendly_place);
		
		return $friendly_place;
	}
}