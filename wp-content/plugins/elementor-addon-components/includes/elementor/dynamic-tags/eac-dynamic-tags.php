<?php
/*=============================================================================================
* Class: Eac_Dynamic_Tags
*
* Description: Enregistre les Balises Dynamiques (Dynamic Tags)
* Met à disposition un ensemble de méthodes pour valoriser les options des listes de Tag
* Ref: https://gist.github.com/iqbalrony/7ee129379965082fb6c62cf5db372752
*
* Méthodes	'get_all_meta_post'		Requête SQL sur les metadatas
*			'get_all_posts_url'		Liste des URLs des articles/pages
*			'get_all_cpts_url'		Liste des URLs des articles personnalisés CPT
*			'get_user_metas'		Liste des métadonnées de l'utilisateur courant
*			'get_author_metas'		Liste des métadonnées de l'auteur de l'article courant
*           'get_all_authors'		Liste de tous les users du log
*			'get_all_chart_url'		Liste des URLs des fichiers TXT des medias
*
* @since 1.6.0 
* @since 1.6.2	Ajout du Dynamic Tags 'Eac_External_Image_Url'
* @since 1.6.3	Suppression du Dynamic Tags 'Shortcode media'
* @since 1.7.0	Ajout du Dynamic Tag 'Eac_Post_Custom_Field_Values'
* @since 1.7.5	Ajout des Dynamic Tags ACF pour le composant Post Grid
* @since 1.7.6	Ajout des Dynamic Tags ACF
* @since 1.8.0	Ajout des types ACF Relationship et Post_object
* @since 1.8.2	Déplacement de l'instanciation de la class 'eac-acf-tags' dans 'plugin.php'
*				Fix des champs ACF non affichés en mode preview
* @since 1.8.3	Ajout du type ACF field Group
* @since 1.8.4	L'enregistrement des tags ACF est transféré dans l'objet 'Eac_Acf_Tags'
*				La méthode 'get_elementor_templates' est tranférée dans l'objet 'Eac_Tools_Util'
*=============================================================================================*/

namespace EACCustomWidgets\Includes\Elementor\DynamicTags;

use EACCustomWidgets\Includes\Eac_Tools_Util;

// Exit if accessed directly
if(!defined('ABSPATH')) { exit; }

// Version PRO Elementor, on sort
if(defined('ELEMENTOR_PRO_VERSION')) { return; }

class Eac_Dynamic_Tags {
	const VALS_LENGTH = 25;
	
	public function __construct() {
		add_action('elementor/dynamic_tags/register_tags', array($this, 'register_tags'));
	}
	
	/**
	* Enregistre les groupes et les balises dynamiques (Dynamic Tags)
	*
	* @since 1.6.0
	*/
	public function register_tags($dynamic_tags) {
		// Enregistre les nouveaux groupes avant d'enregistrer les Tags
		\Elementor\Plugin::$instance->dynamic_tags->register_group('eac-url', ['title' => __('URLs', 'eac-components')]);
		\Elementor\Plugin::$instance->dynamic_tags->register_group('eac-site-groupe', ['title' => __('Site', 'eac-components')]);
		\Elementor\Plugin::$instance->dynamic_tags->register_group('eac-author-groupe', ['title' => __('Auteur', 'eac-components')]);
		\Elementor\Plugin::$instance->dynamic_tags->register_group('eac-post', ['title' => __('Article', 'eac-components')]);
		
		/** ----- URLs -----*/
		
		require_once(__DIR__ . '/tags/url-post.php');
		// Enregistre le tag des URLs des articles
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Posts_Tag());
		
		require_once(__DIR__ . '/tags/url-cpt.php');
		// Enregistre le tag des URLs des articles personnalisés
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Cpts_Tag());
		
		/** URLs */
		require_once(__DIR__ . '/tags/url-page.php');
		// Enregistre le tag des URLs des pages
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Pages_Tag());
		
		require_once(__DIR__ . '/tags/url-chart.php');
		// Enregistre le tag des URLs des fichiers TXT pour les diagrammes
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Chart_Tag());
		
		require_once(__DIR__ . '/tags/featured-image-url.php');
		// Enregistre le tag de l'URL de l'image en avant
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Featured_Image_Url());
		
		require_once(__DIR__ . '/tags/author-website-url.php');
		// Enregistre le tag de l'URL du site web de l'auteur de l'article
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Author_Website_Url());
		
		require_once(__DIR__ . '/tags/url-image-widget.php');
		// Enregistre le tag pour lier l'URL d'une image externe avec le widget media
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_External_Image_Url());
		
		/** ----- Articles - Posts -----*/
		
		require_once(__DIR__ . '/tags/post-by-user.php');
		// Enregistre le tag pour filtrer les articles sur les auteurs
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Post_User());
		
		require_once(__DIR__ . '/tags/post-custom-field-keys.php');
		// Enregistre le tag pour filtrer les articles sur la clé des champs personnalisés
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Post_Custom_Field_Keys());
		
		require_once(__DIR__ . '/tags/post-custom-field-values.php');
		// @since 1.7.0 Enregistre le tag pour filtrer les articles sur les valeurs des champs personnalisés
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Post_Custom_Field_Values());
		
		require_once(__DIR__ . '/tags/post-elementor-tmpl.php');
		// Enregistre le tag des modèles Elementor
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Elementor_Template());
		
		require_once(__DIR__ . '/tags/post-excerpt.php');
		// Enregistre le tag du résumé d'un article
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Post_Excerpt());
		
		require_once(__DIR__ . '/tags/featured-image.php');
		// Enregistre le tag de l'image en avant
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Featured_Image());
		
		require_once(__DIR__ . '/tags/user-info.php');
		// Enregistre le tag des information d'un utilisateur
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_User_Info());
		
		/** ----- Site -----*/
		
		require_once(__DIR__ . '/tags/site-url.php');
		// Enregistre le tag de l'URL du site
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Site_URL());
		
		require_once(__DIR__ . '/tags/site-server.php');
		// Enregistre le tag pour les données du serveur
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Server_Var());
		
		require_once(__DIR__ . '/tags/site-title.php');
		// Enregistre le tag du titre du site
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Site_Title());
		
		require_once(__DIR__ . '/tags/site-tagline.php');
		// Enregistre le tag du slogan
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Site_Tagline());
		
		require_once(__DIR__ . '/tags/site-logo.php');
		// Enregistre le tag du media logo
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Site_Logo());
		
		require_once(__DIR__ . '/tags/site-stats.php');
		// Enregistre le tag des statistiques du site
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Post_Stats());
		
		require_once(__DIR__ . '/tags/cookies.php');
		// Enregistre le tag pour lire les cookies
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Cookies_Var());
		
		/** ----- Auteur - Author -----*/
		
		require_once(__DIR__ . '/tags/author-info.php');
		// Enregistre le tag des infos de l'auteur de l'article
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Author_Info());
		
		require_once(__DIR__ . '/tags/author-name.php');
		// Enregistre le nom de l'auteur de l'article
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Author_Name());
		
		require_once(__DIR__ . '/tags/author-picture.php');
		// Enregistre le tag de l'avatar de l'auteur de l'article
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Author_Picture());
		
		require_once(__DIR__ . '/tags/author-social-network.php');
		// Enregistre le tag des réseaux sociaux de l'auteur de l'article
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Author_Social_network());
		
		/** ----- Image - Featured -----*/
		
		require_once(__DIR__ . '/tags/featured-image-data.php');
		// Enregistre le tag des données de l'image en avant
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Featured_Image_Data());
		
		require_once(__DIR__ . '/tags/user-picture.php');
		// Enregistre le tag de l'avatar de l'auteur de l'article
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_User_Picture());
		
		require_once(__DIR__ . '/tags/shortcode-image.php');
		// Enregistre le tag pour exécuter un shortcode qui intégrer un image externe au site
		$dynamic_tags->register_tag(new \EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Eac_Shortcode_Image());
	}
	
	/**
	 * Requête SQL sur les metadatas des POSTS/PAGES/CPT
	 *
	 * @since 1.6.0
	 */
	public static function get_all_meta_post($posttype = 'post') {
        global $wpdb;
        $result = $wpdb->get_results($wpdb->prepare(
        "SELECT p.post_type, p.post_title, pm.post_id, pm.meta_key, pm.meta_value
            FROM {$wpdb->prefix}posts p,{$wpdb->prefix}postmeta pm 
            WHERE p.post_type = %s
            AND p.ID = pm.post_id
			AND p.post_title != ''
			AND p.post_status = 'publish'
            AND pm.meta_key NOT LIKE '\\_%'
			AND pm.meta_key NOT LIKE 'sdm_%'
			AND pm.meta_key NOT LIKE 'rank_%'
            AND pm.meta_value IS NOT NULL
            AND pm.meta_value != ''
            ORDER BY pm.meta_key", $posttype));
			
        return $result;
    }
	
	/**
	* Retourne la liste des URLs des articles/pages
	*
	* @Param {$posttype} Le type d'article 'post' ou 'page'
	* @Return Un tableau "URL du post" => "Titre du post"
	* 
	* @since 1.6.0
	*/
	public static function get_all_posts_url($posttype = 'post') {
		$post_list = array('' => __('Select...', 'eac-components'));
		
		$data = get_posts(array(
			'post_type' => $posttype,
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC'
		));
		
		if(!empty($data) && !is_wp_error($data)) {
			foreach($data as $key) {
				//if(!function_exists('pll_the_languages')) {
				    $post_list[esc_url(get_permalink($key->ID))] = $key->post_title;
				/*} else { // PolyLang
				    $post_id_pll = pll_get_post($key->ID);
		            if($post_id_pll) {
			            $post_list[get_permalink($post_id_pll)] = $key->post_title;
		            }
				}*/
			}
		}
		return $post_list;
	}
	
	/**
	* Retourne la liste des URLs des articles personnalisés CPT
	*
	* @return ID, post_type, post_title, post_name, guid
	* @since 1.6.0
	*/
	public static function get_all_cpts_url() {
	    global $wpdb;
        $result = $wpdb->get_results(
		"SELECT ID, post_type, post_title, post_name, guid
            FROM {$wpdb->prefix}posts
            WHERE post_type NOT IN ('post','page')
            AND post_title != ''
            AND post_status = 'publish'
            ORDER BY post_type ASC");
            
        return $result;
	}
	
	public static function get_all_cpts_url_OLD() {
		$post_list = array('' => __('Select...', 'eac-components'));
		$args = array('public' => true, '_builtin' => false);
		
		$data = get_post_types($args, 'objects');
  
        if($data) {
            foreach($data as $key) {
                $post_list[$key->name] = $key->label.":".$key->labels->singular_name;
            }
        }
		
		return $post_list;
	}
	
	/**
	* Retourne la liste des métadonnées de l'utilisateur courant si il est logué
	*
	* @since 1.6.0
	*/
	public static function get_user_metas() {
		$list = array();
		$current_user = wp_get_current_user();
		$user_meta_fields = Eac_Tools_Util::get_supported_user_meta_fields();
		
		// User non logué
		if(0 === $current_user->ID) {
			return $list;
		}

		$usermetas = array_map(function($a) { return $a[0]; }, get_user_meta($current_user->ID, '', true));
		
		foreach($usermetas as $key => $vals) {
			if(!is_serialized($vals) && $vals !== '' && $key[0] !== '_' && in_array($key, $user_meta_fields)) {
				if(mb_strlen($vals, 'UTF-8') > self::VALS_LENGTH) {
					$list[$key] = $key . "::" . mb_substr($vals, 0, self::VALS_LENGTH, 'UTF-8') . "...";
				} else {
					$list[$key] = $key . "::" . $vals;
				}
			}
		}
		ksort($list);
		return $list;
	}
	
	/**
	* Retourne la liste des métadonnées de l'auteur de l'article courant
	*
	* @since 1.6.0
	*/
	public static function get_author_metas() {
		global $authordata;
		$list = array();
		$user_meta_fields = Eac_Tools_Util::get_supported_user_meta_fields();
		
		if(!isset($authordata->ID)) { // La variable globale n'est pas définie
			$post = get_post();
			$authordata = get_userdata($post->post_author);
		}
		
		$authormetas = array_map(function($a) { return $a[0]; }, get_user_meta($authordata->ID, '', true));
		
		foreach($authormetas as $key => $vals) {
			if(!is_serialized($vals) && $vals !== '' && $key[0] !== '_' && in_array($key, $user_meta_fields)) {
				if(mb_strlen($vals, 'UTF-8') > self::VALS_LENGTH) {
						$list[$key] = $key . "::" . mb_substr($vals, 0, self::VALS_LENGTH, 'UTF-8') . "...";
				} else {
					$list[$key] = $key . "::" . $vals;
				}
			}
		}
		ksort($list);
		return $list;
	}
	
	/**
	* Retourne la liste de tous les users du blog
	*
	* @since 1.6.0
	* Vérifier le niveau des droits (roles)
	*/
	public static function get_all_authors() {
	    $list = array();
        $users = get_users(array('fields' => array('ID', 'user_nicename', 'display_name')));
        
        // Boucle sur Array of stdClass objects.
        foreach($users as $user) {
            //print_r($user->ID.":".$user->display_name);
            $list[$user->ID] = esc_html($user->display_name);
        }
		ksort($list);
        return $list;
	}
	
	/**
	* Liste des URLs des fichiers TXT des medias (composant Chart)
	*
	* @since 1.6.0
	*/
	public static function get_all_chart_url($posttype = 'attachment') {
		$post_list = array('' => __('Select...', 'eac-components'));
		
		$attachments = get_posts(array(
			'post_type'      => $posttype,
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'post_mime_type' => 'text/plain',
			'post_parent'    => null,
			'orderby' => 'title',
			'order' => 'ASC'
		));
		
		if(!empty($attachments) && !is_wp_error($attachments)) {
			foreach($attachments as $post) {
				$post_list[$post->guid] = $post->post_title;
			}
		}
		return $post_list;
	}
}
new Eac_Dynamic_Tags();