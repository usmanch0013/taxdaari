<?php

/*=====================================================================================================================================
* Class: Plugin
*
* Description:  Active l'administration du plugin avec les droit d'Admin
*               Charge les fichiers CSS des composants
*               Enregistre les scripts JS des composants
*               Enregistre 'eac-components.js' avec le frontend Elementor
*               Enregistre les composants en fonction du paramétrage '$elements_keys'
*				Enregistre les fonctionnalités en fonction du paramétrage '$features_keys'
*               Enregistre la catégorie des composants du plugin
*
* @since 0.0.9
* @since 1.6.0  Ajout de script pour l'implémentation de l'éditeur en ligne du CSS 'Custom CSS' dans l'onglet 'Advanced'
*               Correctif ajout de la lib jqcloud pour le composant Instagram Location
* @since 1.6.2	Force le chargement de la Fancybox pour le 'Shortcode Image' et le Dynamic Tags 'External image'
* @since 1.6.3	Ajout de l'options 'all-components'
* @since 1.6.4	Ajout du composant 'syntax-highlight'
* @since 1.6.7	Correctif sur tous les repeater des widgets. Suppression de 'array_values'
*				Ajout du prefixe de debug sur les scripts
*				Fix: '_content_template' is soft deprecated Elementor 2.9.0
* @since 1.7.0	Ajout d'un fichier CSS pour styliser le panel de l'éditeur
* @since 1.7.1	Ajout du composant 'HTML Sitemap'
* @since 1.7.2	Décomposition de la class 'Eac_Helper_Utils' en deux class 'Eac_Helpers_Util' et 'Eac_Tools_Util'
* @since 1.X.X	Nomme le handler du script ISOTOPE 'isotope-js' pour éviter la collision avec 'PAFE' qui l'intègre aussi
* @since 1.7.70	Ajout du composant 'site-thumbnail'
* @since 1.7.80	Fix: 'Elementor\Scheme_Typography' is soft deprecated Elementor 3.2.2
*				Fix: 'Elementor\Scheme_Color' is soft deprecated Elementor 3.2.2
*				Fix: '_register_controls' is soft deprecated Elementor 3.1.0
*				Fix: 'elementor.config.settings.page' is soft deprecated Elementor 2.9.0 (eac-custom-css.js)
* @since 1.8.0	Ajout du composant 'table-content'
*				Test existence du slug du composant (isset)
* @since 1.8.1	Ajout d'une section (Advanced/EAC sticky effect) pour implémenter la propriété 'sticky'
* @since 1.8.2	Ajout du composant 'ACF relationship'
* @since 1.8.4	Ajout des états aux tableaux d'éléments '$elements_keys'
*				Ajout du tableau des fonctionnalités '$features_keys'
*				Activation/désactivation des fonctionnalités
*				Ajout des pages d'options pour ACF
*				Ajout de la fonctionnalité 'element-link'
* @since 1.8.5	Ajout du composant 'Off Canvas'
*				Déplacement du chargement de chacun des fichiers de style dans le widget correspondant
* @since 1.8.6	Ajout du composant 'Hotspots'
*				Changement de répertoire de la class des pages d'options
* @since 1.8.7	Ajout de la fonctionnalité 'acf-json'
*=====================================================================================================================================*/

namespace EACCustomWidgets;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Main Plugin Class
 *
 * Enregistre les nouveaux composants.
 *
 * @since 0.0.9
 */
class Plugin {
	
	/**
	 * La liste des composants par leur slug
	 * Ajouter/Supprimer parallèlement les composants
	 * dans admin/settings/eac-components.php méthode 'save_settings'
	 *
	 * @since 0.0.9
	 * @since 1.6.4
	 * @since 1.6.7
	 * @since 1.7.1
	 * @since 1.7.70
	 * @since 1.8.0
	 * @since 1.8.2
	 * @since 1.8.4
	 * @since 1.8.5
	 * @since 1.8.6
	 * 
	 * @access protected
	 */
	protected $elements_keys = array(
		'all-components' => true,
		'articles-liste' => true,
		'image-effects' => true,
		'image-galerie' => true,
		'image-promotion' => true,
		'image-ribbon' => true,
		'image-ronde' => true,
		'images-comparison' => true,
		'kenburn-slider' => true,
		'slider-pro' => true,
		'reseaux-sociaux' => true,
		'lecteur-rss' => true,
		'lecteur-audio' => true,
		'image-diaporama' => true,
		'pinterest-rss' => true,
		'instagram-explore' => true,
		'instagram-search' => true,
		'instagram-user' => true,
		'instagram-location' => true,
		'chart' => true,
		'modal-box' => true,
		'syntax-highlight' => true,
		'html-sitemap' => true,
		'site-thumbnail' => true,
		'table-content' => true,
		'acf-relationship' => true,
		'off-canvas' => true,
		'image-hotspots' => true,
	);
	
	/**
	 * La liste des fonctionnalités par leur slug
	 * Ajouter/Supprimer parallèlement les fonctionnalités
	 * dans admin/settings/eac-components.php méthode 'save_features'
	 *
	 * @since 1.8.4
	 * @since 1.8.7
	 *
	 * @access protected
	 */
	protected $features_keys = array(
		'dynamic-tag' => true,
		'acf-dynamic-tag' => true,
		'custom-css' => true,
		'custom-attribute' => true,
		'acf-option-page' => false,
		'element-sticky' => true,
		'element-link' => false,
		'alt-attribute' => true,
		'acf-json' => false,
	);
	
	
	// Instance de la page des réglages
	private $admin_settings;
	
	/**
	 * Constructor
	 *
	 * @since 0.0.9
	 *
	 * @access public
	 */
	public function __construct() {
		//  C'est un role Admin, chargement de la page de configuration des composants
		if(is_admin()) {
			require_once(__DIR__ . '/admin/settings/eac-components.php');
			$this->admin_settings = new \EACCustomWidgets\Admin\Settings\EAC_Admin_Settings($this->elements_keys, $this->features_keys);
		}
		
		// Charge les outils, helper, shortcode et les extensions
		$this->load_features();
		
		// Charge les styles, scripts et les composants
		$this->add_actions();
	}
	
	/**
	 * Actions principales pour charger les styles, les scripts et les composants
	 *
	 * @since 0.0.9
	 *
	 * @access private
	 */
	private function add_actions() {
		// Crée le groupe de composants
		add_action('elementor/init', array($this, 'register_groups_components'));
		
		/**
		 * Actions pour enregistrer les scripts des composants
		 * 
		 * @since 0.0.9
		 */
		add_action('elementor/frontend/after_register_scripts', array($this, 'register_scripts'));
		
		/**
		 * Actions pour insérer les scripts obligatoires dans la file
		 * 
		 * @since 0.0.9
		 */
		add_action('elementor/frontend/before_enqueue_scripts', array($this, 'enqueue_scripts'));
		
		/**
		 * Actions pour enregistrer les styles et les mettre dans la file
		 * 
		 * @since 0.0.9
		 */
		add_action('elementor/frontend/after_register_styles', array($this, 'register_styles'));
		add_action('elementor/frontend/after_enqueue_styles', array($this, 'enqueue_styles'));
		//add_action('elementor/preview/enqueue_styles', array($this, 'enqueue_styles'));
		
		/**
		 * Enqueue les styles du panel Elementor
		 *
		 * @since 1.7.0
		 */
		add_action('elementor/editor/wp_head', array($this, 'add_panel_style'));
		
		/**
		 * Charge les outils et les extensions
		 * Enregistre les classes des composants
		 *
		 * @since 0.0.9
		 */
		$this->register_controls();
		add_action('elementor/widgets/widgets_registered', array($this, 'widgets_registered'));
	}
	
	public function register_controls() {
		//Plugin::$instance->inspector->add_log('Page Template', Plugin::$instance->inspector->parse_template_path( $template ), $document->get_edit_url() );
		//require_once(__DIR__ . '/extensions/off-canvas.php');
	}
	
	/**
	 * Enregistre les styles dans le panel de l'éditeur Elementor
	 * Propriété 'content_classes' du control
	 * 
	 * @since 1.7.0
	 */
    public function add_panel_style(){
	    wp_enqueue_style('eac-editor-panel', plugins_url('/assets/css/eac-editor-panel.css', EAC_CUSTOM_FILE), false, '1.7.0');
    }
	
	/**
	 * Enregistre tous les styles
	 * 
	 * @since 0.0.9
	 */
	public function register_styles() {
		wp_register_style('eac', plugins_url('/assets/css/eac-components.css', EAC_CUSTOM_FILE), false, '0.0.9');
		wp_register_style('eac-image-fancybox', plugins_url('/assets/css/jquery.fancybox.css', EAC_CUSTOM_FILE), array('eac'), '3.5.7');
		//wp_register_style('eac-tooltip', plugins_url('/assets/css/spiketip.min.css', EAC_CUSTOM_FILE), array('eac'), '1.0.6');
		
		wp_register_style('eac-articles-liste', plugins_url('/assets/css/articles-liste.css', EAC_CUSTOM_FILE), array('eac'), '0.0.9');
		wp_register_style('eac-image-ribbon', plugins_url('/assets/css/image-ribbon.css', EAC_CUSTOM_FILE), array('eac'), '0.0.9');
		wp_register_style('eac-images-comparison', plugins_url('/assets/css/images-comparison.css', EAC_CUSTOM_FILE), array('eac'), '0.0.9');
		wp_register_style('eac-image-effects', plugins_url('/assets/css/image-effects.css', EAC_CUSTOM_FILE), array('eac'), '0.0.9');
		wp_register_style('eac-image-promotion', plugins_url('/assets/css/image-promotion.css', EAC_CUSTOM_FILE), array('eac'), '0.0.9');
		wp_register_style('eac-image-ronde', plugins_url('/assets/css/image-ronde.css', EAC_CUSTOM_FILE), array('eac'), '0.0.9');
		wp_register_style('eac-image-galerie', plugins_url('/assets/css/image-galerie.css', EAC_CUSTOM_FILE), array('eac'), '0.0.9');
		wp_register_style('eac-smoothslides', plugins_url('/assets/css/kenburn-slider.css', EAC_CUSTOM_FILE), array('eac'), '0.0.9');
		wp_register_style('eac-slider-pro', plugins_url('/assets/css/slider-pro.css', EAC_CUSTOM_FILE), array('eac'), '0.0.9');
		wp_register_style('eac-reseaux-sociaux', plugins_url('/assets/css/reseaux-sociaux.css', EAC_CUSTOM_FILE), array('eac'), '0.0.9');
		wp_register_style('eac-lecteur-rss', plugins_url('/assets/css/lecteur-rss.css', EAC_CUSTOM_FILE), array('eac'), '1.0.0');
		wp_register_style('eac-pinterest-rss', plugins_url('/assets/css/pinterest-rss.css', EAC_CUSTOM_FILE), array('eac'), '1.2.0');
		wp_register_style('eac-audioplayer', plugins_url('/assets/css/lecteur-audio.css', EAC_CUSTOM_FILE), array('eac'), '1.0.0');
		wp_register_style('eac-diaporama', plugins_url('/assets/css/image-diaporama.css', EAC_CUSTOM_FILE), array('eac'), '1.0.0');
		wp_register_style('eac-instagram-explore', plugins_url('/assets/css/instagram-explore.css', EAC_CUSTOM_FILE), array('eac'), '1.3.0');
		wp_register_style('eac-instagram-search', plugins_url('/assets/css/instagram-search.css', EAC_CUSTOM_FILE), array('eac'), '1.3.0');
		wp_register_style('eac-instagram-user', plugins_url('/assets/css/instagram-user.css', EAC_CUSTOM_FILE), array('eac'), '1.3.0');
		wp_register_style('eac-instagram-location', plugins_url('/assets/css/instagram-location.css', EAC_CUSTOM_FILE), array('eac'), '1.4.0');
		wp_register_style('eac-jqcloud', plugins_url('/assets/css/jqcloud.css', EAC_CUSTOM_FILE), false, '2.0.3');
		wp_register_style('eac-leaflet', plugins_url('/assets/css/leaflet.css', EAC_CUSTOM_FILE), array('eac'), '1.5.1');
		wp_register_style('eac-chart', plugins_url('/assets/css/chart.css', EAC_CUSTOM_FILE), array('eac'), '2.9.3');
		wp_register_style('eac-modalbox', plugins_url('/assets/css/modal-box.css', EAC_CUSTOM_FILE), array('eac'), '1.6.1');
		wp_register_style('eac-syntax-highlight', plugins_url('/assets/css/prism.css', EAC_CUSTOM_FILE), array('eac'), '1.22.0');
		wp_register_style('eac-html-sitemap', plugins_url('/assets/css/html-sitemap.css', EAC_CUSTOM_FILE), array('eac'), '1.7.1');
		wp_register_style('eac-site-thumbnail', plugins_url('/assets/css/site-thumbnail.css', EAC_CUSTOM_FILE), array('eac'), '1.7.70');
		wp_register_style('eac-table-content', plugins_url('/assets/css/toctoc.css', EAC_CUSTOM_FILE), array('eac'), '1.8.0');
		wp_register_style('eac-off-canvas', plugins_url('/assets/css/off-canvas.css', EAC_CUSTOM_FILE), array('eac'), '1.8.5');
		wp_register_style('eac-acf-relation', plugins_url('/assets/css/acf-relationship.css', EAC_CUSTOM_FILE), array('eac'), '1.8.2');
		wp_register_style('eac-image-hotspots', plugins_url('/assets/css/image-hotspots.css', EAC_CUSTOM_FILE), array('eac'), '1.8.6');
	}
	
	/**
	 * Enqueue les styles pour les composants activés
	 * 
	 * @since 0.0.9
	 * @since 1.8.5	Chargés comme dépendance de chaque widget. Méthode 'get_style_depends'
	 */
	public function enqueue_styles() {
		// Pour tous les composants
		wp_enqueue_style('eac');
		wp_enqueue_style('eac-image-fancybox');
		//wp_enqueue_style('eac-tooltip');
	}
	
	/**
	 * Enregistre les scripts pour les composants activés
	 * Chargés comme dépendance de chaque widget. Méthode 'get_script_depends'
	 * 
	 * @since 0.0.9
	 * @since 1.6.4 Le script "prism.js" est directement chargé comme "Syntaxe Heredoc" dans la class "Syntax_Highlighter_Widget"
	 * @since 1.6.7	Ajout du mode debug pour les scripts javascript
	 */
	public function register_scripts() {
		// @since 1.6.7 Ajout du suffix aux scripts
		$suffix = EAC_SCRIPT_DEBUG ? '.js' : '.min.js';
		
		$default_settings = $this->elements_keys;
		$check_component_active = get_option('eac_options_settings', $default_settings);
		
		// Le gestionnaires d'image est toujours enregistré
		wp_register_script('eac-imagesloaded', EAC_ADDONS_URL . 'assets/js/isotope/imagesloaded.pkgd.min.js', array('jquery'), '4.1.4', true);
		
		if(isset($check_component_active['articles-liste']) && $check_component_active['articles-liste']) {
			wp_register_script('isotope-js', EAC_ADDONS_URL . 'assets/js/isotope/isotope.pkgd.min.js', array('jquery'), '3.0.6', true);
			wp_register_script('eac-infinite-scroll', EAC_ADDONS_URL . 'assets/js/isotope/infinite-scroll.pkgd.min.js', array('jquery'), '3.0.5', true);
		}
		if(isset($check_component_active['images-comparison']) && $check_component_active['images-comparison']) {
			wp_register_script('eac-images-comparison', EAC_ADDONS_URL . 'assets/js/comparison/images-comparison' . $suffix, array('jquery'), EAC_ADDONS_VERSION, true);
		}
		if(isset($check_component_active['image-galerie']) && $check_component_active['image-galerie']) {
			wp_register_script('isotope-js', EAC_ADDONS_URL . 'assets/js/isotope/isotope.pkgd.min.js', array('jquery'), '3.0.6', true);
			wp_register_script('eac-collageplus', EAC_ADDONS_URL . 'assets/js/isotope/jquery.collagePlus.min.js', array('jquery'), '0.3.3', true);
		}
		if(isset($check_component_active['kenburn-slider']) && $check_component_active['kenburn-slider']) {
			wp_register_script('eac-smoothslides', EAC_ADDONS_URL . 'assets/js/kenburnslider/smoothslides.min.js', array('jquery'), '2.2.1', true);
		}
		if(isset($check_component_active['slider-pro']) && $check_component_active['slider-pro']) {
			wp_register_script('eac-sliderpro', EAC_ADDONS_URL . 'assets/js/sliderpro/jquery.sliderPro.min.js', array('jquery'), '1.4.0', true);
			wp_register_script('eac-transit', EAC_ADDONS_URL . 'assets/js/transit/jquery.transit.min.js', array('jquery'), '0.9.12', true);
		}
		if(isset($check_component_active['reseaux-sociaux']) && $check_component_active['reseaux-sociaux']) {
			wp_register_script('eac-reseaux-sociaux', EAC_ADDONS_URL . 'assets/js/socialshare/floating-social-share.min.js', array('jquery'),  EAC_ADDONS_VERSION, true);
		}
		if(isset($check_component_active['lecteur-audio']) && $check_component_active['lecteur-audio']) {
			wp_register_script('eac-lecteur-audio', EAC_ADDONS_URL . 'assets/js/audioplayer/player' . $suffix, array('jquery'),  EAC_ADDONS_VERSION, true);
		}
		if(isset($check_component_active['instagram-search']) && $check_component_active['instagram-search']) {
			wp_register_script('isotope-js', EAC_ADDONS_URL . 'assets/js/isotope/isotope.pkgd.min.js', array('jquery'), '3.0.6', true);
			wp_register_script('eac-instagram-search', EAC_ADDONS_URL . 'assets/js/instagram/instagram-search' . $suffix, array('jquery'), EAC_ADDONS_VERSION, true);
		}
		if(isset($check_component_active['instagram-explore']) && $check_component_active['instagram-explore']) {
			wp_register_script('isotope-js', EAC_ADDONS_URL . 'assets/js/isotope/isotope.pkgd.min.js', array('jquery'), '3.0.6', true);
			wp_register_script('eac-jqcloud', EAC_ADDONS_URL . 'assets/js/jqcloud/jqcloud.min.js', array('jquery'), '2.0.3', true);
			wp_register_script('eac-instagram-explore', EAC_ADDONS_URL . 'assets/js/instagram/instagram-explore' . $suffix, array('jquery'), EAC_ADDONS_VERSION, true);
		}
		if(isset($check_component_active['instagram-user']) && $check_component_active['instagram-user']) {
			wp_register_script('isotope-js', EAC_ADDONS_URL . 'assets/js/isotope/isotope.pkgd.min.js', array('jquery'), '3.0.6', true);
			wp_register_script('eac-jqcloud', EAC_ADDONS_URL . 'assets/js/jqcloud/jqcloud.min.js', array('jquery'), '2.0.3', true);
			wp_register_script('eac-instagram-user', EAC_ADDONS_URL . 'assets/js/instagram/instagram-user' . $suffix, array('jquery'), EAC_ADDONS_VERSION, true);
		}
		if(isset($check_component_active['instagram-location']) && $check_component_active['instagram-location']) {
			wp_register_script('isotope-js', EAC_ADDONS_URL . 'assets/js/isotope/isotope.pkgd.min.js', array('jquery'), '3.0.6', true);
			wp_register_script('eac-jqcloud', EAC_ADDONS_URL . 'assets/js/jqcloud/jqcloud.min.js', array('jquery'), '2.0.3', true);
			wp_register_script('eac-leaflet', EAC_ADDONS_URL . 'assets/js/leaflet/leaflet.js', array(), '1.5.1', true);
			wp_register_script('eac-instagram-location', EAC_ADDONS_URL . 'assets/js/instagram/instagram-location' . $suffix, array('jquery'), EAC_ADDONS_VERSION, true);
		}
		if(isset($check_component_active['chart']) && $check_component_active['chart']) {
			wp_register_script('eac-chart-src', EAC_ADDONS_URL . 'assets/js/chart/chart.min.js', '', '2.9.3', true);
			wp_register_script('eac-chart-color', EAC_ADDONS_URL . 'assets/js/color/randomColor.min.js', '', '2.9.3', true);
			wp_register_script('eac-chart-label', EAC_ADDONS_URL . 'assets/js/chart/chartjs-plugin-datalabels.min.js', '', '0.7.0', true);
			wp_register_script('eac-chart-style', EAC_ADDONS_URL . 'assets/js/chart/chartjs-plugin-style.min.js', '', '0.5.0', true);
			wp_register_script('eac-chart', EAC_ADDONS_URL . 'assets/js/chart/eac-chart' . $suffix, array('jquery'), EAC_ADDONS_VERSION, true);
		}
		if(isset($check_component_active['table-content']) && $check_component_active['table-content']) {
			wp_register_script('eac-table-content', EAC_ADDONS_URL . 'assets/js/toc/toctoc' . $suffix, array('jquery'), EAC_ADDONS_VERSION, true);
		}
	}
	
	/**
	 * Enregistre les scripts obligatoires
	 * 
	 * @since 0.0.9
	 */
	public function enqueue_scripts() {
		// @since 1.6.7
		$suffix = EAC_SCRIPT_DEBUG ? '.js' : '.min.js';
		
		/**
		 * @since 1.6.2 La Fancybox est toujours chargée pour le 'Shortcode Image' et le Dynamic Tags 'External image'
		 * qui peuvent être insérés dans un article/page sans composant
		 */
		wp_enqueue_script('eac-fancybox', EAC_ADDONS_URL . 'assets/js/fancybox/jquery.fancybox' . $suffix, array('jquery'), '3.5.7', true);
		
		// Le script principal qui exécute le code de chaque composant quand il est affiché dans la page
		wp_enqueue_script('eac-elements', EAC_ADDONS_URL . 'assets/js/eac-components' . $suffix, array('jquery', 'elementor-frontend'), EAC_ADDONS_VERSION, true);
		
		// Passe l'url absolue du plugin aux objects javascript -> ajaxCallRss
		wp_localize_script('eac-elements', 'eacElements', array('pluginsUrl' => plugins_url('', __FILE__)));
	}
    
	/**
	 * Event On Widgets Registered
	 *
	 * @since 0.0.9
	 *
	 * @access public
	 */
	public function widgets_registered() {
		$this->register_components();
	}
    
	/**
	 * Exclusion Lazyload de WP Rocket des images portants la class 'eac-image-loaded'
	 * 
	 * @since 1.0.0
	 */
	public function rocket_lazyload_exclude_class($attributes) {
		$attributes[] = 'class="eac-image-loaded'; // Ne pas fermer les doubles quotes
		//add_filter('wp_lazy_loading_enabled', '__return_false');
		return $attributes;
	}
	
	/**
	 * Ce filtre n'est pas actif
	 * Conserver pour mémoire
	 * 
	 * @since 1.8.2
	 */
	public function my_acf_fields_relationship_query($args, $field, $post_id) {
		// Show 40 posts per AJAX call.
		$args['posts_per_page'] = 10;
		// Restrict results to children of the current post only.
		$args['post_type'] = 'post';
		return $args;
	}
	
	/**
	 * Charge les outils, helper, les shortcodes et les extensions (CSS, Dynamic Tags, Attributs)
	 *
	 * @since 1.7.3
	 * @since 1.8.4	Ajout des tests pour l'activation/désactivation des fonctionnalités
	 * @since 1.8.7	Ajout de la fonctionnalité 'acf-json'
	 */
	private function load_features() {
		$default_settings = $this->features_keys;
		$check_component_active = get_option('eac_options_features', $default_settings);
		
		/** @since 1.8.4 */
		$custom_css = isset($check_component_active['custom-css']) && $check_component_active['custom-css'];
		$custom_attribute = isset($check_component_active['custom-attribute']) && $check_component_active['custom-attribute'];
		$element_sticky = isset($check_component_active['element-sticky']) && $check_component_active['element-sticky'];
		
		$dynamic_tag = isset($check_component_active['dynamic-tag']) && $check_component_active['dynamic-tag'];
		$alt_attribute = $dynamic_tag && isset($check_component_active['alt-attribute']) && $check_component_active['alt-attribute'];
		
		// ACF Dynamic tags V5
		$dynamic_tag_acf = function_exists('acf_get_field_groups') && isset($check_component_active['acf-dynamic-tag']) && $check_component_active['acf-dynamic-tag'];
		
		// Les pages d'options sont activés indépendamment de ACF Dynamic tags V5
		$option_page = function_exists('acf_get_field_groups') && isset($check_component_active['acf-option-page']) && $check_component_active['acf-option-page'];
		
		/** @since 1.8.7 Création du répertoire 'acf-json' */
		$acf_json = function_exists('acf_get_field_groups') && isset($check_component_active['acf-json']) && $check_component_active['acf-json'];
		
		$element_link = isset($check_component_active['element-link']) && $check_component_active['element-link'];
		
		/**
		 * Filtre Lazyload de WP Rocket
		 * 
		 * @since 1.0.0
		 */
		add_filter('rocket_lazyload_excluded_attributes', array($this, 'rocket_lazyload_exclude_class'));
		
		/**
		 * Ajout des shortcodes Image externe, Templates Elementor et colonne vue Templates Elementor
		 * 
		 * @since 1.5.3	Instagram
		 * @since 1.6.0	Image externe, Image media et Templates Elementor
		 * @since 1.6.1 Suppression du shortcode 'Instagram'
		 * @since 1.6.3 Suppression du shortcode 'Image media'
		 */
		require_once(__DIR__ . '/includes/eac-shortcode.php');
		
		/**
		 * Implémente la mise à jour du plugin ainsi que sa fiche détails
		 * 
		 * @since 1.6.5
		 */
		require_once(__DIR__ . '/includes/eac-update-plugin.php');
		
		/**
		 * Utils pour tous les composants et les extensions
		 * 
		 * @since 1.7.2
		 */
		require_once(__DIR__ . '/includes/eac-tools.php');
		
		/**
		 * Helper pour le composant Post Grid
		 * 
		 * @since 1.7.2
		 */
		require_once(__DIR__ . '/includes/eac-helpers.php');
		
		/**
		 * Ajout de l'éditeur CSS en ligne à Elementor (Textarea Custom CSS 'Onglet Avancé')
		 * L'éditeur sera toujours actif même en désactivant tous les composants
		 * 
		 * @since 1.6.0
		 * @since 1.8.4	Check settings
		 */
		if($custom_css) {
			require_once(__DIR__ . '/includes/elementor/custom-css/eac-custom-css.php');
		}
		
		/**
		 * Implémente les balises dynamiques (Dynamic Tags for Elementor)
		 * 
		 * @since 1.6.0
		 * @since 1.8.4	Check settings
		 */
		if($dynamic_tag) {
			require_once(__DIR__ . '/includes/elementor/dynamic-tags/eac-dynamic-tags.php');
		}
		
		/**
		 * Implémente les balises dynamiques ACF pour la v5 de ACF
		 * 
		 * @since 1.8.2
		 * @since 1.8.4	Check settings
		 */
		if($dynamic_tag_acf) {
			require_once(__DIR__ . '/includes/elementor/dynamic-tags/acf/eac-acf-tags.php');
			//add_filter('acf/fields/relationship/query', array($this, 'my_acf_fields_relationship_query'), 10, 3);
		}
		
		/**
		 * Implémente les pages d'options pour ACF
		 * 
		 * @since 1.8.4
		 * @since 1.8.6	Changement de répertoire
		 */
		if($option_page) {
			require_once(__DIR__ . '/includes/acf/options-page/eac-acf-options-page.php');
		}
		
		/**
		 * Injection d'un champ texte pour valoriser l'attribut ALT notamment du Dynamic Tags 'External image'
		 * Champ injecté dans les widgets 'Image', 'Image-box' et 'Modalbox'
		 * 
		 * @since 1.6.3
		 * @since 1.6.6
		 * @since 1.8.1
		 * @since 1.8.4	Check settings
		 */
		if($alt_attribute) {
			require_once(__DIR__ . '/includes/elementor/injection/eac-injection-image.php');
		}
		
		/**
		 * Injection d'une section pour appliquer la propriété 'sticky' à une Section/Colonne ou un Widget
		 * Onglet 'Advanced' section 'EAC sticky effect'
		 * 
		 * 
		 * @since 1.8.1
		 * @since 1.8.4	Check settings
		 */
		if($element_sticky) {
			require_once(__DIR__ . '/includes/elementor/injection/eac-injection-sticky.php');
		}
		
		/**
		 * Ajout d'un champ 'Attributs' onglet 'Advanced' à Elementor pour les Sections, Columns et Widgets
		 * 
		 * @since 1.6.6
		 * @since 1.8.4	Check settings
		 */
		if($custom_attribute) {
			require_once(__DIR__ . '/includes/elementor/custom-attributes/eac-custom-attributes.php');
		}
		
		/**
		 * Ajout d'un control pour appliquer un lien sur une colonne ou une section
		 * 
		 * @since 1.8.4
		 */
		if($element_link) {
			require_once(__DIR__ . '/includes/elementor/injection/eac-injection-links.php');
		}
		
		/**
		 * Changement de localisation du répertoire de sauvegarde des groupes ACF format JSON
		 * Répertoire /includes/acf/acf-json
		 * 
		 * @since 1.8.7
		 */
		if($acf_json) {
			require_once(__DIR__ . '/includes/acf/eac-acf-json.php');
		}
	}
	
	/**
	 * Enregistre les composants actifs
	 *
	 * @since 0.0.9
	 *
	 * @access private
	 */
	private function register_components() {
		$default_settings = $this->elements_keys;
		$check_component_active = get_option('eac_options_settings', $default_settings);
		
		if(isset($check_component_active['articles-liste']) && $check_component_active['articles-liste']) {
			require_once(__DIR__ . '/widgets/articles-liste.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Articles_Liste_Widget());
		}
		/** @since 1.8.2 Check v5 de ACF */
		if(isset($check_component_active['acf-relationship']) && $check_component_active['acf-relationship'] && function_exists('acf_get_field_groups')) {
			require_once(__DIR__ . '/widgets/acf-relationship.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Acf_Relationship_Widget());
		}
		if(isset($check_component_active['image-galerie']) && $check_component_active['image-galerie']) {
			require_once(__DIR__ . '/widgets/image-galerie.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Image_Galerie_Widget());
		}
		if(isset($check_component_active['slider-pro']) && $check_component_active['slider-pro']) {
			require_once(__DIR__ . '/widgets/slider-pro.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Slider_Pro_Widget());
		}
		if(isset($check_component_active['chart']) && $check_component_active['chart']) {
			require_once(__DIR__ . '/widgets/chart.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Chart_Widget());
		}
		if(isset($check_component_active['modal-box']) && $check_component_active['modal-box']) {
			require_once(__DIR__ . '/widgets/modal-box.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Modal_Box_Widget());
		}
		/** @since 1.8.5 */
		if(isset($check_component_active['off-canvas']) && $check_component_active['off-canvas']) {
			require_once(__DIR__ . '/widgets/off-canvas.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Off_Canvas_Widget());
		}
		/** @since 1.8.6 */
		if(isset($check_component_active['image-hotspots']) && $check_component_active['image-hotspots']) {
			require_once(__DIR__ . '/widgets/image-hotspots.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Image_Hotspots_Widget());
		}
		if(isset($check_component_active['syntax-highlight']) && $check_component_active['syntax-highlight']) {
			require_once(__DIR__ . '/widgets/syntax-highlighter.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Syntax_Highlighter_Widget());
		}
		if(isset($check_component_active['instagram-search']) && $check_component_active['instagram-search']) {
			require_once(__DIR__ . '/widgets/instagram-search.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Instagram_Search_Widget());
		}
		if(isset($check_component_active['instagram-user']) && $check_component_active['instagram-user']) {
			require_once(__DIR__ . '/widgets/instagram-user.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Instagram_User_Widget());
		}
		if(isset($check_component_active['instagram-explore']) && $check_component_active['instagram-explore']) {
			require_once(__DIR__ . '/widgets/instagram-explore.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Instagram_Explore_Widget());
		}
		if(isset($check_component_active['instagram-location']) && $check_component_active['instagram-location']) {
			require_once(__DIR__ . '/widgets/instagram-location.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Instagram_Location_Widget());
		}
		/** @since 1.7.1 */
		if(isset($check_component_active['html-sitemap']) && $check_component_active['html-sitemap']) {
			require_once(__DIR__ . '/widgets/html-sitemap.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Html_Sitemap_Widget());
		}
		/** @since 1.8.0 */
		if(isset($check_component_active['table-content']) && $check_component_active['table-content']) {
			require_once(__DIR__ . '/widgets/table-of-contents.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Table_Of_Contents_Widget());
		}
		/** @since 1.7.70 */
		if(isset($check_component_active['site-thumbnail']) && $check_component_active['site-thumbnail']) {
			require_once(__DIR__ . '/widgets/site-thumbnail.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Site_Thumbnails_Widget());
		}
		if(isset($check_component_active['lecteur-rss']) && $check_component_active['lecteur-rss']) {
			require_once(__DIR__ . '/widgets/lecteur-rss.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Lecteur_Rss_Widget());
		}
		if(isset($check_component_active['lecteur-audio']) && $check_component_active['lecteur-audio']) {
			require_once(__DIR__ . '/widgets/lecteur-audio.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Lecteur_Audio_Widget());
		}
		if(isset($check_component_active['pinterest-rss']) && $check_component_active['pinterest-rss']) {
			require_once(__DIR__ . '/widgets/pinterest-rss.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Pinterest_Rss_Widget());
		}
		if(isset($check_component_active['image-diaporama']) && $check_component_active['image-diaporama']) {
			require_once(__DIR__ . '/widgets/image-diaporama.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Image_Diaporama_Widget());
		}
		if(isset($check_component_active['kenburn-slider']) && $check_component_active['kenburn-slider']) {
			require_once(__DIR__ . '/widgets/kenburn-slider.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\KenBurn_Slider_Widget());
		}
		if(isset($check_component_active['images-comparison']) && $check_component_active['images-comparison']) {
			require_once(__DIR__ . '/widgets/images-comparison.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Images_Comparison_Widget());
		}
		if(isset($check_component_active['image-effects']) && $check_component_active['image-effects']) {
			require_once(__DIR__ . '/widgets/image-effects.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Image_Effects_Widget());
		}
		if(isset($check_component_active['image-ribbon']) && $check_component_active['image-ribbon']) {
			require_once(__DIR__ . '/widgets/image-ribbon.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Image_Ribbon_Widget());
		}
		if(isset($check_component_active['image-ronde']) && $check_component_active['image-ronde']) {
			require_once(__DIR__ . '/widgets/image-ronde.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Image_Ronde_Widget());
		}
		if(isset($check_component_active['image-promotion']) && $check_component_active['image-promotion']) {
			require_once(__DIR__ . '/widgets/image-promotion.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Image_Promotion_Widget());
		}
		if(isset($check_component_active['reseaux-sociaux']) && $check_component_active['reseaux-sociaux']) {
			require_once(__DIR__ . '/widgets/reseaux-sociaux.php');
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \EACCustomWidgets\Widgets\Reseaux_Sociaux_Widget());
		}
	}
	
	/**
	 * Crée la catégorie des composants
	 * 
	 * @since 0.0.9
	 */
	public function register_groups_components() {
		\Elementor\Plugin::instance()->elements_manager->add_category('eac-elements',
			array('title' => __('EAC Composants', 'eac-components'), 'icon' => 'fa fa-plug'), 1);
	}
}

new Plugin();