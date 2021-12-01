<?php

/**
 * Plugin Name: Elementor Addon Components
 * Description: Ajouter des composants et des fonctionnalités avancées pour la version gratuite d'Elementor
 * Plugin URI: https://elementor-addon-components.com/
 * Author: Team EAC
 * Version: 1.8.7
 * Elementor tested up to: 3.4.6
 * Author URI: https://elementor-addon-components.com/
 * Text Domain: eac-components
 * Domain Path: /languages
 * License: GPLv2+
 * 'Elementor Addon Components' is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GPL General Public License for more details.
 */
if(!defined('ABSPATH')) exit; // Exit if accessed directly

define('EAC_CUSTOM_FILE', __FILE__);
define('EAC_ADDONS_URL', plugins_url('/', __FILE__));
define('EAC_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('EAC_ADDONS_VERSION', '1.8.7');
define('EAC_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
define('EAC_PATH_ACF_JSON', EAC_PLUGIN_DIR_PATH . 'includes/acf/acf-json');
define('EAC_ELEMENTOR_VERSION_REQUIRED', '3.3.0');
define('EAC_MINIMUM_PHP_VERSION', '7.0');
define('EAC_SCRIPT_DEBUG', false);				// true = .js ou false = .min.js
define('EAC_GET_POST_ARGS_IN', false);			// get_display_for_settings de la page en entrée
define('EAC_GET_POST_ARGS_OUT', false);			// arguments formatés pour WP_Query en sortie
define('EAC_GET_META_FILTER_QUERY', false);

final class EAC_Components_Plugin {
	
	private static $_instance = null;
	
	// Singleton
	public static function instance() {
		if(is_null( self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Lance divers tests et charge le plugin
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action('plugins_loaded', array($this, 'eac_custom_widgets_load'));
	}
	
	/**
	* Charge le plugin après le chargement d'Elementor
	* 
	* @since 1.0.0
	*/
	public function eac_custom_widgets_load() {
		
		/** 
		 * Elementor est chargé
		 * 
		 *  @since 1.0.0
		 */
		if(! did_action('elementor/loaded')) {
			add_action('admin_notices', array($this, 'eac_components_loaded'));
			return;
		}
		
		/** 
		 * Test de la version d'Elementor
		 * 
		 *  @since 1.0.0
		 */
		if(! version_compare(ELEMENTOR_VERSION, EAC_ELEMENTOR_VERSION_REQUIRED, '>=')) {
			add_action('admin_notices', array($this, 'eac_components_bad_version'));
			return;
		}
		
		/** 
		 * Test de la version PHP
		 * 
		 *  @since 1.4.9
		 */
		if(version_compare(PHP_VERSION, EAC_MINIMUM_PHP_VERSION, '<' )) {
			add_action('admin_notices', array($this, 'admin_notice_minimum_php_version'));
			return;
		}
		
		/**
		 * Ajout de la page de réglages du plugin
		 *
		 * @since 1.3.0
		 */
		add_filter('plugin_action_links_' . EAC_PLUGIN_BASENAME, array($this, 'add_settings_action_links'), 10);
		
		/**
		 * Ajout du lien vers le Help center
		 *
		 * @since 1.8.3
		 */
		add_filter('plugin_row_meta', array($this, 'add_row_meta_links'), 10, 2);
		
		/**
		 * Charge le plugin et instancie la class
		 * 
		 *  @since 1.0.0
		 */
		require_once(__DIR__ . '/plugin.php');
		
		/** 
		 * Charge le fichier language
		 * 
		 *  @since 1.0.0
		 */
		if(! load_plugin_textdomain('eac-components', false, basename(dirname(__FILE__)) . '/languages')) {
			if(get_locale() === 'fr_FR') {
				return;
			}
			add_action('admin_notices', array($this, 'eac_components_textdomaine'));
			return;
		}
	}

	/**
	 * Ajout du lien vers la page de réglages du plugin
	 *
	 * @since 1.3.0
	 */
	public function add_settings_action_links($links) {
		$settings_link = array('<a href="' . admin_url('admin.php?page=eac-components') . '">' . __('Réglages', 'eac-components') . '</a>');
		return array_merge($settings_link, $links);
	}
	
	/**
	 * Ajout du lien vers la page du centre d'aide
	 *
	 * @since 1.8.3
	 */
	public function add_row_meta_links($meta_links, $plugin_file) {
		if(EAC_PLUGIN_BASENAME == $plugin_file) {
            // Help Center
            $settings_link = array('<a href="https://elementor-addon-components.com/help-center/" target="_blank">' . __("Centre d'aide", "eac-components") . '</a>');
			$meta_links = array_merge($meta_links, $settings_link);
		}
		return $meta_links;
	}
	
	/**
	 * Fonctions de notifications
	 * 
	 * @since 1.0.0
	 */
	public function eac_components_activated() { ?>
		<div class="notice notice-info is-dismissible">
			<p><?php _e("EAC : Plugin activé.", 'eac-components'); ?></p>
		</div>
	<?php }
		
	public function eac_components_loaded() { ?>
		<div class="notice notice-error is-dismissible">
			<p><?php _e("EAC : Elementor n'est pas chargé !", 'eac-components'); ?></p>
		</div>
	<?php }

	public function eac_components_bad_version() { ?>
		<div class="notice notice-error is-dismissible">
			<p><?php _e("EAC : Version minimum Elementor : " . EAC_ELEMENTOR_VERSION_REQUIRED, 'eac-components'); ?></p>
		</div>
	<?php }

	public function admin_notice_minimum_php_version() { ?>
		<div class="notice notice-error is-dismissible">
			<p><?php _e("EAC : Version minimum PHP : " . EAC_MINIMUM_PHP_VERSION, 'eac-components'); ?></p>
		</div>
	<?php }

	public function eac_components_textdomaine() { ?>
		<div class="notice notice-warning is-dismissible">
			<p><?php echo "EAC : Unable to find the language file: '" . get_locale() ."'"; ?></p>
		</div>
	<?php }

}
EAC_Components_Plugin::instance();