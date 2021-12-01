<?php

/*======================================================================================
*
* Constantes de la class Endpoints
*
*======================================================================================*/

/*===================================================================
Get location à partir d'un shortcode d'une image
1) https://www.instagram.com/p/BwSYgZ-Hkwb/?__a=1	==> ['graphql']['shortcode_media']['location']['id'] Exemple : 2999938
													==> ['graphql']['shortcode_media']['owner']['username'] Exemple : sohakouraytem
=====================================================================*/

define("OPT_TIMEOUT", "15");
define("OPT_C_TIMEOUT", "15");

// @since 1.4.6
const PROXYS = [
	//'200.111.182.6:443',		// un peu lent
	//'117.1.16.130:8080',		// un peu lent
	//'163.172.190.160:8811',	// rapide
	//'51.158.123.35:8811',		// rapide OK
	//'201.54.31.14:55282',		// très lent
];
	
// 25 Instagram user-agent
const USER_AGENTS = [
	'Mozilla/5.0 (Linux; Android 8.1.0; motorola one Build/OPKS28.63-18-3; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/70.0.3538.80 Mobile Safari/537.36 Instagram 72.0.0.21.98 Android (27/8.1.0; 320dpi; 720x1362; motorola; motorola one; deen_sprout; qcom; pt_BR; 132081645)',
	'Mozilla/5.0 (Linux; Android 7.0; Lenovo K33b36 Build/NRD90N; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/65.0.3325.109 Mobile Safari/537.36 Instagram 41.0.0.13.92 Android (24/7.0; 480dpi; 1080x1920; LENOVO/Lenovo; Lenovo K33b36; K33b36; qcom; pt_BR; 103516666)',
	'Mozilla/5.0 (Linux; Android 6.0.1; SM-J700M Build/MMB29K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/70.0.3538.110 Mobile Safari/537.36 Instagram 73.0.0.22.185 Android (23/6.0.1; 320dpi; 720x1280; samsung; SM-J700M; j7elte; samsungexynos7580; pt_BR; 133633069)',
	'Mozilla/5.0 (Linux; Android 6.0.1; ZUK Z2131 Build/MMB29M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/48.0.2564.106 Mobile Safari/537.36 Instagram 62.0.0.19.93 Android (23/6.0.1; 480dpi; 1080x1920; ZUK; ZUK Z2131; z2_plus; qcom; pt_BR; 123790722)',
	'Mozilla/5.0 (Linux; Android 7.0; SM-G610M Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/69.0.3497.100 Mobile Safari/537.36 Instagram 65.0.0.12.86 Android (24/7.0; 480dpi; 1080x1920; samsung; SM-G610M; on7xelte; samsungexynos7870; es_US; 126223536)',
	'Mozilla/5.0 (Linux; Android 8.1.0; SM-J530G Build/M1AJQ; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/69.0.3497.100 Mobile Safari/537.36 Instagram 66.0.0.11.101 Android (27/8.1.0; 320dpi; 720x1280; samsung; SM-J530G; j5y17lte; samsungexynos7870; pt_BR; 127049016)',
	'Mozilla/5.0 (Linux; Android 7.0; SM-G610M Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/69.0.3497.91 Mobile Safari/537.36 Instagram 62.0.0.19.93 Android (24/7.0; 480dpi; 1080x1920; samsung; SM-G610M; on7xelte; samsungexynos7870; pt_BR; 123790722)',
	'Mozilla/5.0 (iPhone; CPU iPhone OS 11_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15G77 Instagram 57.0.0.9.79 (iPhone9,4; iOS 11_4_1; pt_BR; pt-BR; scale=2.61; gamut=wide; 1080x1920)',
	'Mozilla/5.0 (iPhone; CPU iPhone OS 11_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15F79 Instagram 51.0.0.31.168 (iPhone10,2; iOS 11_4; pt_BR; pt-BR; scale=2.61; gamut=wide; 1080x1920)',
	'Mozilla/5.0 (Linux; Android 5.0.1; LG-H342 Build/LRX21Y; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/65.0.3325.109 Mobile Safari/537.36 Instagram 40.0.0.14.95 Android (21/5.0.1; 240dpi; 480x786; LGE/lge; LG-H342; c50ds; c50ds; pt_BR; 102221277)',
	'Mozilla/5.0 (Linux; Android 7.0; SM-G935F Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/67.0.3396.87 Mobile Safari/537.36 Instagram 55.0.0.12.79 Android (24/7.0; 480dpi; 1080x1920; samsung; SM-G935F; hero2lte; samsungexynos8890; pt_BR; 118342010)',
	'Mozilla/5.0 (Linux; Android 7.1.1; ASUS_X00LD Build/NMF26F; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/66.0.3359.158 Mobile Safari/537.36 Instagram 44.0.0.9.93 Android (25/7.1.1; 320dpi; 720x1280; asus; ASUS_X00LD; ASUS_X00LD_1; qcom; pt_BR; 107092318)',
	'Mozilla/5.0 (iPhone; CPU iPhone OS 12_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 Instagram 105.0.0.11.118 (iPhone11,8; iOS 12_3_1; en_US; en-US; scale=2.00; 828x1792; 165586599)',
	'Mozilla/5.0 (iPhone; CPU iPhone OS 12_0_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/16A404 Instagram 68.0.0.10.99 (iPhone9,3; iOS 12_0_1; pt_BR; pt-BR; scale=2.00; gamut=wide; 750x1334; 128202899)',
	'Mozilla/5.0 (Linux; Android 8.0.0; SM-A520F Build/R16NW; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/66.0.3359.158 Mobile Safari/537.36 Instagram 46.0.0.15.96 Android (26/8.0.0; 480dpi; 1080x1920; samsung; SM-A520F; a5y17lte; samsungexynos7880; pt_BR; 109556226)',
	'Mozilla/5.0 (Linux; Android 5.1.1; Lenovo A6020l36 Build/LMY47V; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/66.0.3359.158 Mobile Safari/537.36 Instagram 45.0.0.17.93 Android (22/5.1.1; 480dpi; 1080x1920; LENOVO/Lenovo; Lenovo A6020l36; A6020l36; qcom; pt_BR; 108357722)',
	'Mozilla/5.0 (iPhone; CPU iPhone OS 12_0_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/16A404 Instagram 64.0.0.12.96 (iPhone8,1; iOS 12_0_1; pt_BR; pt-BR; scale=2.00; gamut=normal; 750x1334; 124976489)',
	'Mozilla/5.0 (iPhone; CPU iPhone OS 11_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15F79 Instagram 52.0.0.14.164 (iPhone8,2; iOS 11_4; pt_BR; pt-BR; scale=2.61; gamut=normal; 1080x1920)',
	'Mozilla/5.0 (Linux; Android 7.1.1; Moto G (5S) Build/NPPS26.102-49-11; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/68.0.3440.91 Mobile Safari/537.36 Instagram 58.0.0.12.73 Android (25/7.1.1; 480dpi; 1080x1776; motorola; Moto G (5S); montana; qcom; pt_BR; 120662550)',
	'Mozilla/5.0 (iPhone; CPU iPhone OS 11_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15F79 Instagram 55.0.0.8.78 (iPhone8,1; iOS 11_4; pt_BR; pt-BR; scale=2.00; gamut=normal; 750x1334)',
	'Mozilla/5.0 (Linux; Android 6.0.1; SM-J700M Build/MMB29K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/56.0.2924.87 Mobile Safari/537.36 Instagram 22.0.0.17.68 Android (23/6.0.1; 320dpi; 720x1280; samsung; SM-J700M; j7elte; samsungexynos7580; pt_BR)',
	'Mozilla/5.0 (Linux; Android 7.1.1; Moto G (5S) Plus Build/NPSS26.116-45-5; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/63.0.3239.111 Mobile Safari/537.36 Instagram 27.0.0.11.97 Android (25/7.1.1; 480dpi; 1080x1776; motorola; Moto G (5S) Plus; sanders_nt; qcom; pt_BR)',
	'Mozilla/5.0 (Linux; Android 6.0.1; SM-G532M Build/MMB29T; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/69.0.3497.100 Mobile Safari/537.36 Instagram 65.0.0.12.86 Android (23/6.0.1; 240dpi; 540x960; samsung; SM-G532M; grandpplte; mt6735; es_US; 126223513)',
	'Mozilla/5.0 (iPhone; CPU iPhone OS 11_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15F79 Instagram 49.0.0.14.178 (iPhone8,4; iOS 11_4; pt_BR; pt-BR; scale=2.00; gamut=normal; 640x1136)',
	'Mozilla/5.0 (iPhone; CPU iPhone OS 9_3_5 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Mobile/13G36 Instagram 56.0.0.10.75 (iPad2,2; iPhone OS 9_3_5; pt_BR; pt-BR; scale=2.00; gamut=normal; 640x960)',
];
	
// Instagram URL
const INSTA_BASE = 'https://www.instagram.com';
// Instagram query_hash URL
const INSTA_QUERY_HASH = 'https://www.instagram.com/graphql/query/?query_hash=';
// Instagram query_id URL
const INSTA_QUERY_ID = 'https://www.instagram.com/graphql/query/?query_id=';
	
// Page d'un user account & user account JSON
const ACCOUNT_PAGE = INSTA_BASE . '/{username}/';
const ACCOUNT_JSON_INFO = INSTA_BASE . '/{username}/?__a=1';
	
// @since 1.3.1 Les stories publiées d'un userid
const ACCOUNT_STORIES_BY_HASH = INSTA_QUERY_HASH . 'aec5501414615eca36a9acf075655b1e&variables=';
const ACCOUNT_STORIES_BY_HASH_VAR = '{"user_id":"{userid}","include_chaining":false,"include_reel":true,"include_highlight_reels":true}';
	
// Les informations d'un post sur le propriétaire username, id, comment, location...
const MEDIA_PAGE_INFO = INSTA_BASE . '/p/{shortcode}/';
const MEDIA_PAGE_JSON_INFO = INSTA_BASE . '/p/{shortcode}/?__a=1';
const MEDIA_PAGE_JSON_INFO_2 = INSTA_BASE . '/p/{shortcode}/?tagged={tagname}&__a=1';

// Le nom du user account qui a posté l'image
const ACCOUNT_JSON_INFO_BY_SC = 'https://api.instagram.com/oembed/?url=https://www.instagram.com/p/{shortcode}/';
	
// Le profile d'un user account par son ID.
const ACCOUNT_JSON_INFO_BY_ID = 'https://i.instagram.com/api/v1/users/{userid}/info/';
// Le profile d'un user account par son ID par query_hash
const ACCOUNT_JSON_INFO_BY_ID_BY_HASH = INSTA_QUERY_HASH . '7c16654f22c819fb63d1183034a5162f&variables=';
// Les variables
const ACCOUNT_JSON_INFO_BY_ID_BY_HASH_VAR = '{"user_id":"{userid}","include_reel":true}';

// Le profile d'un user account par son ID par query_hash
// Ce query_hash retrouve les stories (include_highlight_reels) et les profiles suggérés (include_related_profiles)
// TODO Lancer une seule requête au lieu des 2 actuelles (1.3.1 & 1.4.2)
const ACCOUNT_JSON_INFO_BY_ID_BY_HASH_2 = INSTA_QUERY_HASH . 'e74d51c10ecc0fe6250a295b9bb9db74&variables=';
// Les variables
const ACCOUNT_JSON_INFO_BY_ID_BY_HASH_VAR_2 = '{"user_id":"{userid}","include_chaining":false,"include_reel":false,"include_suggested_users":false,"include_logged_out_extras":false,"include_highlight_reels":true,"include_related_profiles":true}';

// @since 1.4.4 Le profile d'un user account par son nom
const ACCOUNT_JSON_INFO_BY_NAME = 'https://www.instagram.com/web/search/topsearch/?context=user&query={username}&count=0';
	
// URL des données d'un user account par son ID :: proxy_user
const ACCOUNT_BY_ID = 'https://www.instagram.com/graphql/query/?query_id=17880160963012870&id={userid}&first={count}&after={endcursor}';
const ACCOUNT_BY_HASH = INSTA_QUERY_HASH . '472f257a40c653c64c666ce877d59d2b&variables=';
// new query_hash plus riche
const ACCOUNT_BY_HASH_2 = INSTA_QUERY_HASH . '{queryhash}&variables=';
// Les variables
const ACCOUNT_BY_HASH_VAR = '{"id":"{userid}","first":"{count}","after":"{endcursor}"}';
	
// URL pour explorer un tagname
const URI_EXPLORE_PAGE = 'https://www.instagram.com/explore/tags/{tagname}/';				// Page HTML
const URI_EXPLORE_PAGE_JSON = 'https://www.instagram.com/explore/tags/{tagname}/?__a=1';	// JSON data
const URI_EXPLORE_PAGE_JSON_1 = 'https://www.instagram.com/explore/tags/{tagname}/?__a=1&max_id={endcursor}';	// JSON data cursor doit être valorisé sinon error
// URLs pour explorer un tagname par ID ou HASH :: Ne retourne pas l'entête du tagname. Nb posts = first * 3
const URI_EXPLORE_PAGE_JSON_BY_ID = 'https://www.instagram.com/graphql/query/?query_id=17882293912014529&tag_name={tagname}&first=50&after={endcursor}';
const URI_EXPLORE_PAGE_JSON_BY_HASH = INSTA_QUERY_HASH . '298b92c8d7cad703f7565aa892ede943&variables=';
// New query_hash avec entête du tagname. Nb posts = first * 3
const URI_EXPLORE_PAGE_JSON_BY_HASH_2 = INSTA_QUERY_HASH . '{queryhash}&variables=';
// Moins riche
const URI_EXPLORE_PAGE_JSON_BY_HASH_3 = INSTA_QUERY_HASH . '3e7706b09c6184d5eafd8b032dbcf487&variables=';
// Les variables
const URI_EXPLORE_PAGE_JSON_BY_HASH_VAR = '{"tag_name":"{tagname}","first":"{count}","after":"{endcursor}"}';
	
// URL des infos user account qui ont commenté un post
const URI_COMMENT_POST_BY_ID = 'https://www.instagram.com/graphql/query/?query_id=17852405266163336&shortcode={shortcode}&first=50&after=';
const URI_COMMENT_POST_BY_HASH = INSTA_QUERY_HASH . '33ba35852cb50da46f5b5e889df7d159&variables=';
// Les variables
const URI_COMMENT_POST_BY_HASH_VAR = '{"shortcode":"{shortcode}","first":"50","after":""}';
	
// URL des infos user account qui ont liké un post
const URI_LIKE_POST_BY_ID = 'https://www.instagram.com/graphql/query/?query_id=17864450716183058&shortcode={shortcode}&first=50&after=';
const URI_LIKE_POST_BY_HASH = INSTA_QUERY_HASH . 'e0f59e4a1c8d78d0161873bc2ee7ec44&variables=';
// Les variables
const URI_LIKE_POST_BY_HASH_VAR = '{"shortcode":"{shortcode}","include_reel":false,"first":"50","after":""}';
	
// URL d'une recherche par le contexte sur un user account (user), un hashtag (hashtag) ou une place (place). context global = blended
const URI_SEARCH_UHP = 'https://www.instagram.com/web/search/topsearch/?context={context}&query={username}';
	
// URL pour explorer une location. searchpk = $place['place']['location']['pk'] de la requête URI_SEARCH_UHP
const URI_EXPLORE_LOCATION = 'https://www.instagram.com/explore/locations/{locationid}/';
// URL pour explorer une location par query_hash. Max 50
const URI_EXPLORE_LOCATION_BY_HASH = INSTA_QUERY_HASH . 'ac38b90f0f3981c42092016a37c59bf7&variables=';
const URI_EXPLORE_LOCATION_BY_HASH_2 = INSTA_QUERY_HASH . '1b84447a4d8b6d6d0426fefb34514485&variables=';
// Les variables
const URI_EXPLORE_LOCATION_BY_HASH_VAR = '{"id":"{locationid}","first":"{count}","after":"{endcursor}"}';
	
// URL d'intégration d'un post, avec ou sans caption et avec ou sans sans script
const URI_EMBEDED_POST = 'https://api.instagram.com/oembed/?url=https://www.instagram.com/p/{shortcode}/';
const URI_EMBEDED_POST_WITHOUT_CAPTION = 'https://api.instagram.com/oembed/?hidecaption=true&maxwidth=450&url=https://www.instagram.com/p/{shortcode}/';
const URI_EMBEDED_POST_WITHOUT_SCRIPT = 'https://api.instagram.com/oembed/?omitscript=true&maxwidth=450&url=https://www.instagram.com/p/{shortcode}/';
	
// URL nominatim pour la recherche des données d'une location OSM
const URI_NOMINATIM = 'https://nominatim.openstreetmap.org/reverse?format=json&zoom=16&lat=';
const URI_NOMINATIM_VAR = '{lat}&lon={lng}';
	
// URL pour retrouver les followers par l'ID d'un user account
const URI_FOLLOWERS_BY_HASH = INSTA_QUERY_HASH . '37479f2b8209594dde7facb0d904896a&variables=';
const URI_FOLLOWERS_BY_HASH_2 = INSTA_QUERY_HASH . 'c76146de99bb02f6415203be841dd25a&variables=';
const URI_FOLLOWERS_BY_HASH_VAR = '{"id":"{userid}","first":"5","after":""}';
	
//  @since 1.4.2 URL pour retrouver les profiles suggérés
const ACCOUNT_SUGGESTED_BY_HASH = INSTA_QUERY_HASH . 'c9100bf9110dd6361671f113dd02e7d6&variables=';
// Les variables
const ACCOUNT_SUGGESTED_BY_HASH_VAR = '{"user_id":"{userid}","include_chaining":false,"include_reel":false,"include_suggested_users":false,"include_logged_out_extras":true,"include_highlight_reels":false,"include_related_profiles":true}';
	
// @since 1.4.3 URL pour retrouver les posts taguées avec un user account par d'autres users accounts
const ACCOUNT_TAGGED_POSTS_BY_HASH = INSTA_QUERY_HASH . 'ff260833edf142911047af6024eb634a&variables=';
// Les variables
const ACCOUNT_TAGGED_POSTS_BY_HASH_VAR = '{"id":"{userid}","first":"50","after":""}';