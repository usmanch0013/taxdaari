<?php

/*====================================================================================================
* Description: Gère l'interface d'administration des composantrs EAC 'EAC Components'
* et des options de la BDD.
* Cette class est instanciée dans 'plugin.php' par le rôle administrateur.
* 
* Charge le css 'eac-admin' et le script 'eac-admin' d'administration des composants.
* Ajoute l'item 'EAC Components' dans les menus de la barre latérale
* Charge le formulaire HTML de la page d'admin.
*
* Pour ajouter/supprimer un élément :
* - Le tableau '$elements_keys' dans 'plugin.php' doit être modifié
* - Ajouter/Supprimer une entrée dans le formulaire respectif 'eac-components_tabX.php'
* - Modifier la méthode 'save_settings'
*
* @since 0.0.9
* @since 1.4.0	Amélioration de la gestion des options
* @since 1.4.1	Gestion des options Instagram
* @since 1.6.2	Suppression de la gestion et des options Instagram
* @since 1.6.3	Ajout de l'option 'all-components' et 'modal-box'
* @since 1.6.4	Ajout de l'option 'syntax-highlight'
* @since 1.7.1	Ajout de l'option 'html-sitemap'
* @since 1.7.70	Ajout de l'option 'site-thumbnail'
* @since 1.8.0	Ajout de l'option 'table-content'
*				Change le type de valeur des options de int => bool
* @since 1.8.2	Ajout de l'option 'acf-relationship'
* @since 1.8.4	Traitement des options des fonctionnalités
* @since 1.8.5	Ajout du composant 'off-canvas'
* @since 1.8.6	Ajout du composant 'image-hotspots'
* @since 1.8.7	Ajout et vérification des nonces des formulaires
*				Ajout de la fonctionnalité 'acf_json'
*====================================================================================================*/

namespace EACCustomWidgets\Admin\Settings;

if(! defined('ABSPATH')) exit(); // Exit if accessed directly

class EAC_Admin_Settings {
    
    private $page_slug = 'eac-components';	// Le slug du plugin
	private $options_settings = 'eac_options_settings';
	private $options_features = 'eac_options_features';
	private $options_instagram = 'eac_options_instagram';
	private $components_nonce = 'eac_settings_components_nonce';	// @since 1.8.7 nonce pour le formulaire des composants
	private $features_nonce = 'eac_settings_features_nonce';		// @since 1.8.7 nonce pour le formulaire des fonctionnalités
	
	private $elements_keys = array();	// La liste des composants par leur slug
	private $features_keys = array();	// @since 1.8.4 La liste des fonctionnalités par leur slug
	
    private $get_settings_elements;		// Inputs de la page HTML 'eac-components_tab1.php'
	private $get_settings_features;		// Inputs de la page HTML 'eac-components_tab2.php'
	
	/**
	 * Constructor
	 *
	 * @param La liste des composants par leur slug
	 * 
	 * @since 0.0.9
	 */
    public function __construct(array $elements_keys = [], array $features_keys = []) {
		if(empty($elements_keys)) {
			exit;
		}
		// Affecte les tableaux d'éléments
		$this->elements_keys = $elements_keys;
		$this->features_keys = $features_keys;
		
		// @since 1.6.2 Suppression des options Instagram si elles existent
		if(get_option($this->options_instagram)) { delete_option($this->options_instagram); }
		
		// Lancement des actions
		add_action('admin_menu', array($this, 'admin_menu'));
		add_action('admin_enqueue_scripts', array($this, 'admin_page_scripts'));
		add_action('wp_ajax_save_settings', array($this, 'save_settings'));
		add_action('wp_ajax_save_features', array($this, 'save_features'));
    }
	
	/**
	 * admin_menu
	 *
	 * Création du nouveau menu dans la barre latérale
	 *
	 * @since 0.0.9
	 */
    public function admin_menu() {
		$plugin_name = __('EAC composants', 'eac-components');
        add_menu_page($plugin_name, $plugin_name , 'manage_options', $this->page_slug, array($this , 'admin_page'), 'dashicons-admin-tools', 100);
    }
	
	/**
	 * admin_page_scripts
	 *
	 * Charge le css 'eac-admin' et le script 'eac-admin' d'administration des composants
	 * Lance le chargement des options
	 *
	 * @since 0.0.9
	 * @since 1.8.4 Simplification du chargement des options
	 * @since 1.8.7	Chargement du script de la boîte de dialogue 'acf-json'
	 */
	public function admin_page_scripts() {
		wp_enqueue_style('eac-admin', plugins_url('/admin/css/eac-admin.css', EAC_CUSTOM_FILE));
		// @since 1.8.7
		wp_enqueue_style('wp-jquery-ui-dialog');
		
		wp_register_script('eac-admin', EAC_ADDONS_URL . 'admin/js/eac-admin.js', array('jquery'), EAC_ADDONS_VERSION, true);
		// @since 1.8.7
		wp_register_script('eac-admin-acf-json', EAC_ADDONS_URL . 'admin/js/eac-acf-json.js', array('jquery', 'jquery-ui-dialog'), EAC_ADDONS_VERSION, true);
		
		wp_enqueue_script('eac-admin');
		wp_enqueue_script('eac-admin-acf-json');
		
		// Enregistre les options des éléments si elles n'existent pas
		if(get_option($this->options_settings) == false) {
			update_option($this->options_settings, $this->elements_keys);
		}
		
		// Enregistre les options des fonctionnalités si elles n'existent pas
		if(get_option($this->options_features) == false) {
			update_option($this->options_features, $this->features_keys);
		}
		
		// Met à jour les options pour le template 'tab1'
		$this->load_elements();
		
		// Met à jour les options pour le template 'tab2'
		$this->load_features();
	}
	
	/**
	 * admin_page
	 *
	 * Passe les paramètres au script 'eac-admin => eac-admin.js'
	 * Charge les templates de la page d'administration
	 *
	 * @since 0.0.9
	 * @since 1.8.7	Ajout des nonces
	 */
    public function admin_page() {
		// Paramètres passés au script Ajax
        $settings_components = array(
			'ajax_url'		=> admin_url('admin-ajax.php'),	// Le chemin 'admin-ajax.php'
			'ajax_action'	=> 'save_settings',				// Action/Méthode appelé par le script Ajax
			'ajax_nonce'	=> wp_create_nonce($this->components_nonce), // Creation du nonce
		);
		wp_localize_script('eac-admin', 'components', $settings_components);
		
		/** ----------- */
		
		// @since 1.8.4 Options features
		$settings_features = array(
			'ajax_url'		=> admin_url('admin-ajax.php'),
			'ajax_action'	=> 'save_features',
			'ajax_nonce'	=> wp_create_nonce($this->features_nonce), // Creation du nonce
		);
		wp_localize_script('eac-admin', 'features', $settings_features);
		
		/** ----------- */
		
		// Charge les templates
		include_once('eac-components_header.php');
		include_once('eac-components_tabs-nav.php');
	?>
		<div class="tabs-stage">
			<?php include_once('eac-components_tab1.php'); ?>
			<?php include_once('eac-components_tab2.php'); ?>
		</div>
		<?php include_once('eac-admin_popup-acf.php'); ?>
	<?php
	}
	
	/**
	 * load_elements
	 *
	 * Charge les options des éléments
	 * Méthode appelée au chargement de l'administration et ouverture de la page de configuration
	 *
	 * @since 1.4.0
	 */
	private function load_elements() {
		
		// Toutes les options
        $default_settings = $this->elements_keys;
		
		// Récupère les options dans la BDD
        $bdd_settings = get_option($this->options_settings, $default_settings);
		
		// Compare les options par défaut et celles de la BDD
        $new_settings = array_diff_key($default_settings, $bdd_settings);
		
		// Si c'est différent, mets à jour la BDD
        if(! empty($new_settings)) {
            $updated_settings = array_merge($bdd_settings, $new_settings);
            update_option($this->options_settings, $updated_settings);
        }
		// Maintenant on charge les options pour le template 'tab1'
		$this->get_settings_elements = get_option($this->options_settings, $default_settings);
	}
	
	/**
	 * load_features
	 *
	 * Charge les options des features
	 * Méthode appelée au chargement de l'administration et ouverture de la page de configuration
	 *
	 * @since 1.8.4
	 */
	private function load_features() {
		
		// Toutes les options
        $default_settings = $this->features_keys;
		
		// Récupère les options dans la BDD
        $bdd_settings = get_option($this->options_features, $default_settings);
		
		// Compare les options par défaut et celles de la BDD
        $new_settings = array_diff_key($default_settings, $bdd_settings);
		
		// Si c'est différent, mets à jour la BDD
        if(! empty($new_settings)) {
            $updated_settings = array_merge($bdd_settings, $new_settings);
            update_option($this->options_features, $updated_settings);
        }
		// Maintenant on charge les options pour le template 'tab2'
		$this->get_settings_features = get_option($this->options_features, $default_settings);
	}
	
	/**
	 * save_features
	 *
	 * Méthode appelée depuis le script 'eac-admin'
	 * Sauvegarde les options dans la table Options de la BDD
	 *
	 * @since 1.8.4
	 * @since 1.8.7		Vérification du nonce
	 */
    public function save_features() {
		// @since 1.8.7 Vérification du nonce pour cette action
		if(!isset($_POST["nonce"]) || !wp_verify_nonce($_POST["nonce"], $this->features_nonce)) {
			wp_send_json_error(__('Nonce erroné', 'eac-components'));
		}
		
		// Les champs 'fields' sont serializés dans 'eac-admin'
		if(isset($_POST['fields'])) {
			parse_str($_POST['fields'], $settings);
		} else {
			wp_send_json_error(__('Champs non retournés', 'eac-components'));
		}
		/*$settings_features = array();
		$keys = array_keys($this->features_keys);
		
		// La liste des fonctionnalités activés ou pas (true ou false)
		foreach($keys as $key) {
			array_push($settings_features, [$key => boolval(isset($settings[$key]) ? 1 : 0)]);
		}*/
		
		// La liste des options de tous les composants activés ou pas (true ou false
		$settings_features = array(
			'dynamic-tag'			=> boolval(isset($settings['dynamic-tag']) ? 1 : 0),
			'acf-dynamic-tag'		=> boolval(isset($settings['acf-dynamic-tag']) ? 1 : 0),
			'custom-css'			=> boolval(isset($settings['custom-css']) ? 1 : 0),
			'custom-attribute'		=> boolval(isset($settings['custom-attribute']) ? 1 : 0),
			'acf-option-page'		=> boolval(isset($settings['acf-option-page']) ? 1 : 0),
			'element-sticky'		=> boolval(isset($settings['element-sticky']) ? 1 : 0),
			'element-link'			=> boolval(isset($settings['element-link']) ? 1 : 0),
			'alt-attribute'			=> boolval(isset($settings['alt-attribute']) ? 1 : 0),
			'acf-json'				=> boolval(isset($settings['acf-json']) ? 1 : 0),
		);
            
		update_option($this->options_features, $settings_features);
		
		// // Met à jour les options du template 'tab2'
		$this->get_settings_features = get_option($this->options_features);
		
		wp_send_json_success(__('Sauvegarde effectuée', 'eac-components'));
	}
	
	/**
	 * save_settings
	 *
	 * Méthode appelée depuis le script 'eac-admin'
	 * Sauvegarde les options dans la table Options de la BDD
	 *
	 * @since 0.0.9
	 * @since 1.5.4		Composant 'chart'
	 * @since 1.6.3		Composant 'modal-box'
	 * @since 1.6.4		Composant 'syntax-highlight'
	 * @since 1.7.1		Composant 'html-sitemap'
	 * @since 1.7.70	Composant 'site-thumbnail'
	 * @since 1.8.0		Composant 'table-content'
	 *					Change intval en boolval
	 * @since 1.8.2		Composant 'acf-relationship'
	 * @since 1.8.5		Composant 'off-canvas'
	 * @since 1.8.6		Composant 'image-hotspots'
	 * @since 1.8.7		Vérification du nonce
	 */
    public function save_settings() {
		// @since 1.8.7 Vérification du nonce pour cette action
		if(!isset($_POST["nonce"]) || !wp_verify_nonce($_POST["nonce"], $this->components_nonce)) {
			wp_send_json_error(__('Nonce erroné', 'eac-components'));
		}
		
		// Les champs 'fields' sont serializés dans 'eac-admin'
		if(isset($_POST['fields'])) {
			parse_str($_POST['fields'], $settings);
		} else {
			wp_send_json_error(__('Champs non retournés', 'eac-components'));
		}
		/*$settings_keys = array();
		$keys = array_keys($this->elements_keys);
		
		// La liste des options de tous les composants activés ou pas (true ou false)
		foreach($keys as $key) {
			array_push($settings_keys, [$key => boolval(isset($settings[$key]) ? 1 : 0)]);
		}*/
		
		// La liste des options de tous les composants activés ou pas (true ou false)
		$settings_keys = array(
			'all-components'		=> boolval(isset($settings['all-components']) ? 1 : 0),
			'articles-liste'		=> boolval(isset($settings['articles-liste']) ? 1 : 0),
			'image-effects'			=> boolval(isset($settings['image-effects']) ? 1 : 0),
			'image-galerie'			=> boolval(isset($settings['image-galerie']) ? 1 : 0),
			'image-promotion'		=> boolval(isset($settings['image-promotion']) ? 1 : 0),
			'image-ribbon'			=> boolval(isset($settings['image-ribbon']) ? 1 : 0),
			'image-ronde'			=> boolval(isset($settings['image-ronde']) ? 1 : 0),
			'images-comparison'		=> boolval(isset($settings['images-comparison']) ? 1 : 0),
			'kenburn-slider'		=> boolval(isset($settings['kenburn-slider']) ? 1 : 0),
			'slider-pro'			=> boolval(isset($settings['slider-pro']) ? 1 : 0),
			'reseaux-sociaux'		=> boolval(isset($settings['reseaux-sociaux']) ? 1 : 0),
			'lecteur-rss'			=> boolval(isset($settings['lecteur-rss']) ? 1 : 0),
			'lecteur-audio'			=> boolval(isset($settings['lecteur-audio']) ? 1 : 0),
			'image-diaporama'		=> boolval(isset($settings['image-diaporama']) ? 1 : 0),
			'pinterest-rss'			=> boolval(isset($settings['pinterest-rss']) ? 1 : 0),
			'instagram-explore'		=> boolval(isset($settings['instagram-explore']) ? 1 : 0),
			'instagram-search'		=> boolval(isset($settings['instagram-search']) ? 1 : 0),
			'instagram-user'		=> boolval(isset($settings['instagram-user']) ? 1 : 0),
			'instagram-location'	=> boolval(isset($settings['instagram-location']) ? 1 : 0),
			'chart'					=> boolval(isset($settings['chart']) ? 1 : 0),
			'modal-box'				=> boolval(isset($settings['modal-box']) ? 1 : 0),
			'syntax-highlight'		=> boolval(isset($settings['syntax-highlight']) ? 1 : 0),
			'html-sitemap'			=> boolval(isset($settings['html-sitemap']) ? 1 : 0),
			'site-thumbnail'		=> boolval(isset($settings['site-thumbnail']) ? 1 : 0),
			'table-content'			=> boolval(isset($settings['table-content']) ? 1 : 0),
			'acf-relationship'		=> boolval(isset($settings['acf-relationship']) ? 1 : 0),
			'off-canvas'			=> boolval(isset($settings['off-canvas']) ? 1 : 0),
			'image-hotspots'		=> boolval(isset($settings['image-hotspots']) ? 1 : 0),
		);
            
		update_option($this->options_settings, $settings_keys);
		
		// Met à jour les options du template 'tab1'
		$this->get_settings_elements = get_option($this->options_settings);
            
		wp_send_json_success(__('Sauvegarde effectuée', 'eac-components'));
	}
}