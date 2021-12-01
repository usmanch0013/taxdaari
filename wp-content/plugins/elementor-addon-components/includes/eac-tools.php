<?php

/*===============================================================================================================
* Class: Eac_Tools_Util
*
* Description: Met à disposition un ensemble de méthodes utiles pour les Widgets
*
* Méthode 'get_filter_post_types'
* Méthode 'get_all_post_types'
* Méthode 'get_post_excerpt' 
* Méthode 'get_thumbnail_sizes'
* Méthode 'get_post_orderby'
* Méthode 'get_all_taxonomies'
* Méthode 'get_all_taxonomies_by_name'
* Méthode 'get_pages_by_name'
* Méthode 'get_pages_by_id'
* Méthode 'get_palette_colors'
* Méthode 'set_meta_value_date'
* Méthode 'set_wp_format_date'
* Méthode 'get_wp_format_date'
* Méthode 'get_operateurs_comparaison'
* Méthode 'get_all_terms'
* Méthode 'get_acf_supported_fields'
* Méthode 'get_unwanted_char'
* Méthode 'get_all_social_networks'
* Méthode 'get_menus_list'
*
* @since 0.0.9
* @since 1.6.0	Filtre sur les métadonnées. Query 'meta_query'
*               La méthode 'get_taxonomies' de 'get_all_taxonomies' retourne un object
*				Filtre sur la taxonomie
*				Filtre sur les post_type
* @since 1.6.4	Ajout de la méthode 'get_palette_colors'
* @since 1.7.0	Ajout de la méthode 'get_pages_by_id'
*				Ajout de la méthode 'get_all_post_types'
*				Ajour de la méthode 'get_all_taxonomies_by_name'
*				Ajout de la méthode 'set_meta_value_date'
*               Ajour de la méthode 'set_wp_format_date'
*				Implémente 'Type de données' et 'Opérateur de comparaison' pour les valeurs
*				Ajout de la méthode 'get_operateurs_comparaison'
*				Ajout de la méthode 'get_all_terms'
* @since 1.7.2	Décomposition de la class 'Eac_Helper_Utils' en deux class 'Eac_Helpers_Util' et 'Eac_Tools_Util'
*				Ajout de la méthode 'get_wp_format_date'
*				Fix: Renvoie la liste complète des post_types
* @since 1.7.3	Ajout de la méthode 'get_unwanted_char' + filtre
*				Ajout des opérateurs de comparaison REGEXP et NOT REGEXP
*				Ajout d'un filtre pour les métadonnées d'un Auteur/User
* @since 1.7.5	Ajout de la liste des champs ACF supportés pour le composant 'Post Grid'
*				Ajout de la méthode 'get_acf_supported_fields'
* @since 1.7.6	Ajout de la méthode 'get_all_social_networks'
* @since 1.8.4	Ajout de la méthode 'get_menus_list'
*				Transfert de la méthode 'get_elementor_templates' de l'objet 'Eac_Dynamic_Tags'
* @since 1.8.5	Ajout de la méthode 'get_widgets_list' ainsi que du tableau des widgets utiles
*===============================================================================================================*/

namespace EACCustomWidgets\Includes;

// Exit if accessed directly
if(!defined('ABSPATH')) { exit; }

class Eac_Tools_Util {
    
	public static $instance = null;
    
	/**
	 * @var $user_meta_fields
	 *
	 * Liste des metas acceptés pour les informations Auteur et User
	 *
	 * @since 1.6.0
	 */
	private static $user_meta_fields = array(
		'locale',
		'syntax_highlighting',
		'avatar',
		'nickname',
		'first_name',
		'last_name',
		'description',
		'rich_editing',
		'role',
		'twitter',
		'facebook',
		'instagram',
		'linkedin',
		'youtube',
		'pinterest',
		'tumblr',
		'flickr',
		'adrs_address',
		'adrs_city',
		'adrs_zipcode',
		'adrs_country',
		'adrs_occupation',
		'adrs_full',
		'show_admin_bar_front',
	);
	
	/**
	 * @var $filtered_taxonomies
	 *
	 * Exclusion de catégories
	 *
	 * @since 1.6.0
	 */
	private static $filtered_taxonomies = array(
		// CORE
		'nav_menu',
		'link_category',
		'post_format',
		// ELEMENTOR
		'elementor_library_type',
		'elementor_library_category',
		'elementor_font_type',
		// YOAST
		'yst_prominent_words',
		// WOOCOMMERCE
		'product_shipping_class',
		'product_visibility',
		'action-group',
		// LOCO
		'translation_priority',
		// FLAMINGO
		'flamingo_contact_tag',
		'flamingo_inbound_channel',
		// SDM
		'sdm_categories',
		'sdm_tags',
		//WPForms
		'wpforms_log_type',
	);
	
	/**
	 * @var $filtered_posttypes
	 *
	 * Exclusion de types de post
	 *
	 * @since 1.6.0
	 */
	private static $filtered_posttypes = array(
		'attachment',
		'revision',
		'nav_menu_item',
		'ae_global_templates',
		'sdm_downloads',
		'mailpoet_page',
		'custom_css',
		'customize_changeset',
		'oembed_cache',
		'user_request',
		'wp_block',
		'custom-css-js',
		// ELEMENTOR
		'elementor_library',
		'e-landing-page',
		// FLAMINGO
		'flamingo_contact',
		'flamingo_inbound',
		'flamingo_outbound',
		// WPFORMS
		'wpforms',
		'wpforms_log',
		// WPCF7
		'wpcf7_contact_form',
		// FORMINATOR
		'forminator_forms',
		'forminator_polls',
		'forminator_quizzes',
		// ACF
		'acf-field-group',
		'acf-field',
		'eac_options_page',
	);
	
	/**
	 * @var $operateurs_comparaison
	 *
	 * Les options des opérateurs de comparaison
	 *
	 * @since 1.7.0
	 * @since 1.7.3	Ajout des opérateurs REGEXP et NOT REGEXP
	 */
	private static $operateurs_comparaison = array(
		'IN'		=> 'IN',
		'NOT IN'	=> 'NOT IN',
		'BETWEEN'	=> 'BETWEEN',
		'NOT BETWEEN' => 'NOT BETWEEN',
		'LIKE' => 'LIKE',
		'NOT LIKE' => 'NOT LIKE',
		'REGEXP' => 'REGEXP',
		'NOT REGEXP' => 'NOT REGEXP',
		'='			=> '=',
		'!='		=> '!=',
		'>'			=> '>',
		'>='		=> '>=',
		'<'			=> '<',
		'<='		=> '<=',
	);
	
	/**
	 * @var $acf_field_types
	 *
	 * Les champs ACF supportés
	 *
	 * @since 1.7.5
	 */
	private static $acf_field_types = array(
		'text',
		'textarea',
		'wysiwyg',
		'select',
		'radio',
		'date_picker',
		'number',
		'true_false',
		'range',
		'checkbox',
	);
	
	/**
	 * @var $unwanted_char_array
	 *
	 * Remplacement des caractères accentués et diacritiques
	 *
	 * @since 1.7.0
	 */
	private static $unwanted_char_array = array(
		'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'AE', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
		'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
		'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
		'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
		'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'Ğ'=>'G', 'İ'=>'I', 'Ş'=>'S', 'ğ'=>'g', 'ı'=>'i', 'ş'=>'s',
		'ü'=>'u', 'ă'=>'a', 'Ă'=>'A', 'ș'=>'s', 'Ș'=>'S', 'ț'=>'t', 'Ț'=>'T',
	);
	
	/**
	 * @var $social_networks
	 *
	 * La liste des réseaux sociaux
	 *
	 * @since 1.7.6
	 */
	private static $social_networks = array(
		'twitter'	=> 'Twitter',
		'facebook'	=> 'Facebook',
		'instagram'	=> 'Instagram',
		'linkedin'	=> 'Linkedin',
		'youtube'	=> 'Youtube',
		'pinterest'	=> 'Pinterest',
		'tumblr'	=> 'Tumblr',
		'flickr'	=> 'Flickr',
		'reddit'	=> 'Reddit',
		'tiktok'	=> 'TikTok',
		'telegram'	=> 'Telegram',
		'quora'		=> 'Quora',
		'twitch'	=> 'Twitch',
	);
	
	/**
	 * @var $wp_widgets
	 *
	 * La liste des widgets autorisés pour le composant Off-canvas
	 *
	 * @since 1.8.5
	 */
	private static $wp_widgets = array(
		'WP_Widget_Search',
		'WP_Widget_Pages',
		'WP_Widget_Calendar',
		'WP_Widget_Archives',
		'WP_Widget_Meta',
		'WP_Widget_Categories',
		'WP_Widget_Recent_Posts',
		'WP_Widget_Recent_Comments',
		'WP_Widget_RSS',
		'WP_Widget_Tag_Cloud',
	);
	
	/** Constructeur */
	public function __construct() {
	    Eac_Tools_Util::instance();
	}
	
	/**
	* Retourne la liste des templates Elementor
	*
	* @param type de la taxonomie ('page' ou 'section')
	* @since 1.6.0
	*/
	public static function get_elementor_templates($type = 'page') {
        $post_list = array('' => __('Select...', 'eac-components'));
        
        $data = get_posts(array(
                'cache_results' => false,
                'post_type' => 'elementor_library',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'orderby' => 'title',
	            'sort_order' => 'ASC',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'elementor_library_type',
                        'field' => 'slug',
                        'terms' => $type,
                    )
                )
            )
        );
		
		if(!empty($data) && !is_wp_error($data)) {
			foreach($data as $key) {
                $post_list[$key->ID] = esc_html($key->post_title);
			}
			ksort($post_list);
		}
		
		return $post_list;
	}
	
	/**
     * get_widgets_list
     * 
     * Retourne la liste des widgets standards
     * 
	 * https://gist.github.com/kingkool68/3418186
	 *
     * @since 1.8.5
     */
	public static function get_widgets_list() {
		global $wp_widget_factory, $wp_registered_sidebars;
		// global $wp_registered_widgets;
		$widgets = self::$wp_widgets;
		$options = array();
		
		//console_log($wp_registered_sidebars);
		//console_log($wp_registered_widgets['media_image-2']);
		
		// Boucle sur les Wigets standards
		foreach($wp_widget_factory->widgets as $key => $widget) {
			if(in_array($key, $widgets)) {
				//$options[$key . "::" . $widget->widget_options['description']] = $widget->name;
				$options[$key] = $widget->name;
			}
		}
		
		// Boucle sur les sidebars
		$sidebars = get_option('sidebars_widgets');
		
		// Boucle sur les sidebars actives et non vides
		foreach($sidebars as $sidebar_id => $sidebar_widgets) {
			if('wp_inactive_widgets' !== $sidebar_id && is_array($sidebar_widgets) && !empty($sidebar_widgets)) {
				$sidebar_name = isset($wp_registered_sidebars[$sidebar_id]['name']) ? $wp_registered_sidebars[$sidebar_id]['name'] : 'No name';
				$options[$sidebar_id . "::" . $sidebar_name] = "Sidebar" . "::" . $sidebar_name;
				
				/*foreach($sidebar_widgets as $widget) {
					$name = $wp_registered_widgets[$widget]['callback'][0]->name;
					$option_name = $wp_registered_widgets[$widget]['callback'][0]->option_name;
					$id_base = $wp_registered_widgets[$widget]['callback'][0]->id_base;
					$key = $wp_registered_widgets[$widget]['params'][0]['number'];
					
					$widget_data = get_option($option_name);
					$data = $widget_data[$key];
					$title = !empty($data['title']) ? $data['title'] : 'Empty title';
					//console_log($title."::".$widget."::".$name."::".$option_name."::".$id_base);
					//console_log($wp_registered_widgets[$widget]);
				}*/
			}
		}
		
		// Widget Search premier indice du tableau
		$search = 'WP_Widget_Search';
		$options = array($search => $options[$search]) + $options;
		
		return $options;
	}
	
	/**
     * get_menus_list
     * 
     * Retourne la liste des menus
     * 
     * @since 1.8.4
     */
	public static function get_menus_list() {
		$menus = wp_get_nav_menus();
		$options = array('' => __('Select...', 'eac-components'));

		foreach($menus as $menu) {
			$options[$menu->slug] = $menu->name;
		}

		return $options;
	}
	
	/**
     * Checks if a plugin is installed
     * 
     * @since 1.0.0
     * @access public
     * 
     * @param $plugin_path string plugin path
     * 
     * @return boolean
     */
	public static function is_plugin_installed($plugin_path) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$plugins = get_plugins();
		return isset($plugins[ $plugin_path]);
	}
    
	/**
	 * get_all_social_networks
	 *
	 * Retourne la liste des réseaux sociaux
	 *
	 *
	 * @since 1.7.6
	 */
	public static function get_all_social_networks() {
		$options = self::$social_networks;
		
		/**
		 * Liste des réseaux sociaux
		 *
		 * Filtrer la liste des réseaux sociaux
		 *
		 * @since 1.7.6
		 *
		 * @param array $options Liste des réseaux sociaux
		 */
		$options = apply_filters('eac/tools/social_networks', $options);
		
		return $options;
	}
	
	/**
	 * get_unwanted_char
	 *
	 * Retourne la liste des metadonnées supportées par les auteurs/users
	 *
	 * @since 1.6.0
	 * @since 1.7.3	Ajout d'un filtre
	 */
	public static function get_unwanted_char() {
		$unwanted_char = self::$unwanted_char_array;
		
		/**
		 * Liste des caractères de remplacement
		 *
		 * Filtre pour ajouter des caractères de remplacement
		 *
		 * @since 1.7.0
		 *
		 * @param array $unwanted_char Liste des caractères
		 */
		$unwanted_char = apply_filters('eac/tools/unwanted_char', $unwanted_char);
		
		return $unwanted_char;
	}
	
	/**
	 * get_supported_user_meta_fields
	 *
	 * Retourne la liste des metadonnées supportées par les auteurs/users
	 *
	 * @since 1.6.0
	 * @since 1.7.3	Ajout d'un filtre
	 */
	public static function get_supported_user_meta_fields() {
		$user_fields = self::$user_meta_fields;
		
		/**
		 * Liste des métadonnées supportées pour un auteur/user
		 *
		 * Filtrer/Ajouter métadonnées
		 *
		 * @since 1.7.3
		 *
		 * @param array $user_fields Liste des métadonnées
		 */
		$user_fields = apply_filters('eac/tools/user_meta_fields', $user_fields);
		
		return $user_fields;
	}
	
	/**
	 * get_acf_supported_fields
	 *
	 * Retourne la liste des champs ACF supportés
	 * 
	 * @since 1.7.5
	 */
	public static function get_acf_supported_fields() {
		$acf_fields = self::$acf_field_types;
		
		/**
		 * Liste des types de champs supportés
		 *
		 * Filtrer/Ajouter des champ ACF
		 *
		 * @since 1.7.5
		 *
		 * @param array $acf_fields Liste des champs par leur slug
		 */
		$acf_fields = apply_filters('eac/tools/acf_field_types', $acf_fields);
		
		return $acf_fields;
	}
	
	/**
	 * get_operateurs_comparaison
	 *
	 * Retourne la liste des opérateur de comparaison
	 * 
	 *
	 * @since 1.7.0
	 */
	public static function get_operateurs_comparaison() {
		$operateurs = self::$operateurs_comparaison;
		
		/**
		 * Liste des opérateurs de comparaison des meta_query
		 *
		 * Filtrer/Ajouter des opérateurs de comparaison
		 *
		 * @since 1.7.0
		 *
		 * @param array $operateurs Liste des opérateurs de comparaison.
		 */
		$operateurs = apply_filters('eac/tools/operateurs_by_key', $operateurs);
		
		return $operateurs;
	}
	
	/**
	 * get_palette_colors
	 * 
	 * Retourne une liste de toutes les couleurs personnalisées et système
	 * Couleur format hexadecimal sans #
	 * Les 10 premières couleurs personnalisées
	 * 
	 * @param {$custom}	Bool: Ajouter les couleurs personnalisées
	 * @param {$system}	Bool: Ajouter les couleurs système
	 *
	 * @since 1.6.4
	 */
	public static function get_palette_colors($custom = true, $system = false) {
		$palette = array();
		
		// Option de la base de données 'elementor_active_kit'
		$elementor_active_kit = get_option('elementor_active_kit', $default = false);
		
		// Post meta qui contient les réglages du Kit avec la clé '_elementor_page_settings'
		$active_kit_settings = get_post_meta($elementor_active_kit, '_elementor_page_settings', $single = true);
		
		// Les custom_color existent
		if(is_array($active_kit_settings) && maybe_unserialize($active_kit_settings) && isset($active_kit_settings['custom_colors']) && $custom) {
			$custom_colors = $active_kit_settings['custom_colors'];
			// Boucle sur les couleurs personnalisées
			foreach($custom_colors as $key => $custom_color) {
				if($key < 10) { // Pas plus de 10
					$palette[] = $custom_color['color'];
				}
			}
		}
		
		// Les system_colors existent
		if(is_array($active_kit_settings) && maybe_unserialize($active_kit_settings) && isset($active_kit_settings['system_colors']) && $system) {
			$system_colors = $active_kit_settings['system_colors'];
			// Boucle sur les couleurs système
			foreach($system_colors as $system_color) {
				$palette[] = $system_color['color'];
			}
		}
		
		/*highlight_string("<?php\n\CColor =\n" . var_export(implode(',', $palette), true) . ";\n?>");*/
		if(empty($palette)) { return $palette; }
		
		return implode(',', $palette);
	}
	
	/**
	 * get_filter_post_types
	 *
	 * Retourne tous les types d'articles publics filtrés
	 *
	 * @since 1.0.0
	 * @since 1.6.0	Affiche le couple 'name::label' dans la liste
	 *				Exclusion de certain post_type Ex: Elementor
	 * @since 1.7.1	Ajout d'un filtre pour Ajouter/Supprimer des types d'article
	 * @since 1.7.2	Fix: Changer "get_post_types(array(), 'objects')" en "get_post_types('', 'objects')"
	 * pour obtenir la liste complète des post_types 
	 */
	public static function get_filter_post_types() {
	    $options = array();
		$posttypes = self::$filtered_posttypes;
		
		/**
		 * Liste des opérateurs de comparaison des meta_query
		 *
		 * Ajouter/Supprimer des types d'articles
		 *
		 * @since 1.7.1
		 *
		 * @param array $posttypes Liste des types d'articles
		 */
		$posttypes = apply_filters('eac/tools/post_types', $posttypes);
		
		$post_types = get_post_types('', 'objects');
		
		foreach($post_types as $post_type) {
			if(is_array($posttypes) && !in_array(esc_attr($post_type->name), $posttypes)) {
				$options[esc_attr($post_type->name)] = esc_attr($post_type->name) . "::" . esc_attr($post_type->label);
			}
		}
		return $options;
	}

	/**
	 * get_all_post_types
	 *
	 * Retourne tous les types d'articles publics non filtrés
	 *
	 * @since 1.7.0
	 */
	public static function get_all_post_types() {
	    $options = array();
		$post_types = get_post_types('', 'objects');
		
		foreach($post_types as $post_type) {
			$options[esc_attr($post_type->name)] = esc_attr($post_type->name) . "::" . esc_attr($post_type->label);
		}
		return $options;
	}
	
	/**
	 * get_post_excerpt
	 *
	 * Lecture du résumé ou du contenu pour un post et réduction au nombre de mots
	 *
	 * @param {$post_id} ID du post
	 * @param {$excerpt_length}	Le nombre de mots à extraire
	 *
	 * @since 1.0.0
	 */
	public static function get_post_excerpt($post_id, $excerpt_length) {
		$the_post = get_post($post_id); // Post ID
		$the_excerpt = null;
		
		/* Il y a un résumé sinon on récupère le contenu du post/page */
		if($the_post) {
			$the_excerpt = $the_post->post_excerpt ? $the_post->post_excerpt : $the_post->post_content;
		}
		
		//On supprime tous les tags html du résumé
		$the_excerpt = strip_tags(strip_shortcodes($the_excerpt));
		$words = explode(' ', $the_excerpt, $excerpt_length + 1);

		if(count($words) > $excerpt_length) :
			 array_pop($words);
			 //array_push($words, '…');
			 $the_excerpt = implode(' ', $words);
			 $the_excerpt .= '[...]';	 // Aucun espace avant
		 endif;

		 return $the_excerpt;
	}
	
	/**
	 * get_thumbnail_sizes
	 *
	 * Format des images
	 *
	 * @since 1.0.0
	 */
	public static function get_thumbnail_sizes() {
	    $options = array();
		$sizes = get_intermediate_image_sizes();
		foreach($sizes as $s){
			$options[$s] = ucfirst($s);
		}
		return $options;
	}

	/**
	 * get_post_orderby
	 *
	 * Les options de tri des articles
	 *
	 * @since 1.0.0
	 * @since 1.7.0	Ajout d'un filtre pour les options du control 'orderby'
	 */
	public static function get_post_orderby() {
		$options = array(
			'ID' =>				__('Id', 'eac-components'),
			'author' =>			__('Auteur', 'eac-components'),
			'title' =>			__('Titre', 'eac-components'),
			'date' =>			__('Date', 'eac-components'),
			'modified' =>		__('Dernière modification', 'eac-components'),
			'comment_count' =>	__('Nombre de commentaires', 'eac-components'),
			'meta_value_num' =>	__('Valeur meta numérique', 'eac-components'),
		);
		
		/**
		 * Liste des options de tri
		 *
		 * Filtrer les options de tri
		 *
		 * @since 1.7.0
		 *
		 * @param array $options Liste des options de tri
		 */
		$options = apply_filters('eac/tools/post_orderby', $options);
		
		return $options;
	}
	
	/**
	 * get_all_terms
	 *
	 * Retourne un tableau filtré de tous les terms de WP
	 * 
	 *
	 * @since 1.7.0
	 */
	public static function get_all_terms() {
		$all_terms = array();
		$taxos = array();
		$filtered_taxo = self::$filtered_taxonomies;
		
		$taxonomies = get_taxonomies(array(), 'objects'); // Retourne un tableau d'objets
		
		// Boucle sur les taxonomies
		foreach($taxonomies as $taxonomy) {
			if(is_array($filtered_taxo) && !in_array(esc_attr($taxonomy->name), $filtered_taxo)) {
				$taxos[] = esc_attr($taxonomy->name);
			}
		}
		
		// Boucle sur les terms d'une taxonomie
		if(!empty($taxos)) {
			foreach($taxos as $taxo) {
				$terms = get_terms(array('taxonomy' => $taxo, 'hide_empty' => true));
				
				if(!is_wp_error($terms) && count($terms) > 0) {
					foreach($terms as $term) {
						$all_terms[$taxo . "::" . $term->slug] = $taxo . "::" . esc_attr($term->name);
					}
				}
			}
		}
		return $all_terms;
	}
	
	/**
	 * get_all_taxonomies
	 *
	 * Retourne un tableau filtré de toutes les taxonomies de WP
	 * Méthode 'get_taxonomies' retourne 'objects' vs 'names' et affiche le couple 'name::label' dans la liste
	 * 
	 * @since 1.0.0
	 * @since 1.6.0	Filtre la taxonomie
	 * @since 1.7.0	Ajout d'un filtre
	 */
	public static function get_all_taxonomies() {
		$options = array();
		$filtered_taxo = self::$filtered_taxonomies;
		
		$taxonomies = get_taxonomies('', 'objects'); // Retourne un objet
		
		foreach($taxonomies as $taxonomy) {
			if(is_array($filtered_taxo) && !in_array(esc_attr($taxonomy->name), $filtered_taxo)) {
				$options[esc_attr($taxonomy->name)] = esc_attr($taxonomy->name) . "::" . esc_attr($taxonomy->label);
			}
		}
		return $options;
	}
	
	/**
	 * get_all_taxonomies_by_name
	 *
	 * Retourne un tableau filtré de toutes les taxonomies par leur nom
	 * 
	 *
	 * @since 1.7.0
	 */
	public static function get_all_taxonomies_by_name() {
		$options = array();
		$filtered_taxo = self::$filtered_taxonomies;
		
		/**
		 * Liste des taxonomies
		 *
		 * Filtre pour ajouter des taxonomies à exclure
		 *
		 * @since 1.7.0
		 *
		 * @param array $filtered_taxo Liste des taxonomies
		 */
		$filtered_taxo = apply_filters('eac/tools/taxonomies_by_name', $filtered_taxo);
		
		$taxonomies = get_taxonomies('', 'objects'); // Retourne un objet
		
		foreach($taxonomies as $taxonomy) {
			if(is_array($filtered_taxo) && !in_array(esc_attr($taxonomy->name), $filtered_taxo)) {
				$options[] = esc_attr($taxonomy->name);
			}
		}
		return $options;
	}
	
	/**
	 * get_pages_by_name
	 *
	 * Retourne un array de toutes les pages avec le titre pour clé
	 *
	 * @since 1.0.0
	 */
	public static function get_pages_by_name() {
		$select_pages = array('' => __('Select...', 'eac-components'));
		$args = array('sort_order' => 'ASC', 'sort_column' => 'post_title');
		$pages = get_pages($args);
		
		foreach($pages as $page) {
			$select_pages[$page->post_title] = esc_html(ucfirst($page->post_title));
		}
		return $select_pages;
	}
	
	/**
	 * get_pages_by_id
	 *
	 * Retourne un array de toutes les pages avec l'ID pour clé 
	 *
	 * @since 1.7.0
	 */
	public static function get_pages_by_id() {
		$select_pages = array('' => __('Select...', 'eac-components'));
		$args = array('sort_order' => 'DESC', 'sort_column' => 'post_title');
		$pages = get_pages($args);
		
		foreach($pages as $page) {
			$select_pages[$page->ID] = esc_html(ucfirst($page->post_title));
		}
		return $select_pages;
	}
	
	/**
	 * set_wp_format_date
	 *
	 * La date à convertir au format des réglages WP
	 *
	 * @param {$ori_date}	(string) La date à convertir
	 * @since 1.7.0
	 */
	public static function set_wp_format_date($ori_date) {
		if(!strtotime($ori_date)) { return $ori_date; }
		return date_i18n(get_option('date_format'), strtotime($ori_date));
	}
	
	/**
	 * get_wp_format_date
	 *
	 * Recherche le format de la date
	 *
	 * @param {$ori_date}	(string) La date dont on recherche le format
	 * @since 1.7.2
	 */
	public static function get_wp_format_date($ori_date) {
		if(preg_match("/^[0-9]{4}(0[1-9]|1[0-2])(0[1-9]|[1-2][0-9]|3[0-1])$/", $ori_date)) {
			return 'Ymd';
		} else if(preg_match("/^[0-9]{4}[\/]{1}(0[1-9]|1[0-2])[\/]{1}(0[1-9]|[1-2][0-9]|3[0-1])$/", $ori_date)) {
			return 'Y/m/d';
		} else if(preg_match("/^[0-9]{4}[\-]{1}(0[1-9]|1[0-2])[\-]{1}(0[1-9]|[1-2][0-9]|3[0-1])$/", $ori_date)) {
			return 'Y-m-d';
		}
		// Format WP définit dans le paramétrage
		return get_option('date_format');
	}
	/**
	 * set_meta_value_date
	 *
	 * La date à convertir au format attendu (YYYY-MM-DD) par la propriété 'value' d'un 'meta_query'
	 *
	 * @param {$ori_date}	(string) La date à convertir
	 * @since 1.7.0
	 */
	public static function set_meta_value_date($ori_date) {
		$wp_format_entree = get_option('date_format');	// Settings/General/Date Format (m-d-Y, m/d/Y, d-m-Y, d/m/Y, n/j/y=7/23/21)
		$wp_format_sortie = 'Y-m-d';					// Format sortie attendu: AAAA-MM-DD
		
		$dateMAJ = date_create_from_format($wp_format_entree, $ori_date);
		if($dateMAJ == false) {
			return $ori_date;
		}
		
		return $dateMAJ->format($wp_format_sortie);
	}
	 
    /**
	 * instance.
	 *
	 * Garantir une seule instance de la class
	 *
	 * @since 1.6.0
	 *
	 * @return Eac_Tools_Util une instance de la class
	 */
	public static function instance() {
		if(is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}