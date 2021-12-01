<?php

/*==================================================================================
* Class: Instagram_Search_Widget
* Name: Recherche sur Instagram par Tags/keyword/Usrname
* Slug: eac-addon-instagram-search
*
* Description: Instagram_Search_Widget affiche en format réduit
* les liens vers des pages Instagram à partir d'une requête
* sur un username et/ou un hashtag
*
* @since 1.3.0
* @since 1.3.1 (28/09/2019) Les propriétés d'un user account 'follower' et 'byline'
* n'apparaissent plus dans le résultat de la requête
*=================================================================================*/

namespace EACCustomWidgets\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Repeater;

if (! defined('ABSPATH')) exit; // Exit if accessed directly

class Instagram_Search_Widget extends Widget_Base {

    /*
    * Retrieve widget name.
    *
    * @access public
    *
    * @return string widget name.
    */
    public function get_name() {
        return 'eac-addon-instagram-search';
    }

    /*
    * Retrieve widget title.
    *
    * @access public
    *
    * @return string widget title.
    */
    public function get_title() {
        return __("Recherche Instagram", 'eac-components');
    }

    /*
    * Retrieve widget icon.
    *
    * @access public
    *
    * @return string widget icon.
    */
    public function get_icon() {
        return 'eicon-social-icons';
    }
	
	/* 
	* Affecte le composant à la catégorie définie dans plugin.php
	* 
	* @access public
    *
    * @return widget category.
	*/
	public function get_categories() {
		return ['eac-elements'];
	}
	
	/* 
	* Load dependent libraries
	* 
	* @access public
    *
    * @return libraries list.
	*/
	public function get_script_depends() {
		return ['isotope-js', 'eac-imagesloaded', 'eac-instagram-search'];
	}
	
	/** 
	 * Load dependent styles
	 * Les styles sont chargés dans le footer
	 * 
	 * @access public
	 *
	 * @return CSS list.
	 */
	public function get_style_depends() {
		return ['eac-instagram-search'];
	}
	
    /*
    * Register widget controls.
    *
    * Adds different input fields to allow the user to change and customize the widget settings.
    *
    * @access protected
    */
    protected function register_controls() {
		
		$this->start_controls_section('instasearch_items_content',
				[
					'label'     => __('Contenu', 'eac-components'),
				]
			);
			/**
			* Plus de tri
			* @since 1.3.1
			*/
			/*
			$this->add_control('instasearch_item_sort',
				[
					'label'   	=> __('Trier par', 'eac-components'),
					'type'    		=> Controls_Manager::SELECT,
					'description'	=> __('Mot-dièse: tri automatique sur le nombre de photos<br>Place: tri automatique sur le nom', 'eac-components'),
					'default' => 'user',
					'options' => [
						'user'					=> __('Utilisateur', 'eac-components'),
						'follower_count_sort'	=> __("Nombre d'abonnés", 'eac-components'),
					],
					'separator'	=> 'after',
				]
			);
			*/
			$this->add_control('instasearch_meta_users',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => __('<span style="text-decoration:underline;">Utilisateur</span>', 'eac-components'),
				]
			);
			
			$this->add_control('instasearch_meta_fullname',
				[
					'label'        => __('Nom complet', 'eac-components'),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __('oui', 'eac-components'),
					'label_off'    => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			);
			/**
			* Plus de switcher nombre d'abonnés
			* @since 1.3.1
			*/
			/*
			$this->add_control('instasearch_meta_followers',
				[
					'label'        => __("Nombre d'abonnés", 'eac-components'),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __('oui', 'eac-components'),
					'label_off'    => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			);
			
			$this->add_control('instasearch_meta_byline',
				[
					'label'        => __("Nombre d'abonnés", 'eac-components'),
					'description'	=> __("En milliers", 'eac-components'),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __('oui', 'eac-components'),
					'label_off'    => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			);
			*/
			$this->add_control('instasearch_meta_private',
				[
					'label'        => __("Compte privé", 'eac-components'),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __('oui', 'eac-components'),
					'label_off'    => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			);
			
			$this->add_control('instasearch_meta_id',
				[
					'label'        => __("Identifiant", 'eac-components'),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __('oui', 'eac-components'),
					'label_off'    => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default'      => '',
					'separator'	=> 'after',
				]
			);
			
			$this->add_control('instasearch_meta_hashtag',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => __('<span style="text-decoration:underline;">Mot-dièse</span>', 'eac-components'),
				]
			);
			
			$this->add_control('instasearch_meta_pub',
				[
					'label'        	=> __("Nombre de publications", 'eac-components'),
					'type'         	=> Controls_Manager::SWITCHER,
					'label_on'     	=> __('oui', 'eac-components'),
					'label_off'    	=> __('non', 'eac-components'),
					'return_value' 	=> 'yes',
					'default'      	=> 'yes',
				]
			);
			
			$this->add_control('instasearch_meta_pub_lang',
				[
					'label'        	=> __("Nombre de publications", 'eac-components'),
					'description'	=> __("En milliers", 'eac-components'),
					'type'         	=> Controls_Manager::SWITCHER,
					'label_on'     	=> __('oui', 'eac-components'),
					'label_off'    	=> __('non', 'eac-components'),
					'return_value' 	=> 'yes',
					'default'      	=> 'yes',
					'separator'	=> 'after',
				]
			);
			
			$this->add_control('instasearch_meta_place',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => __('<span style="text-decoration:underline;">Lieu</span>', 'eac-components'),
				]
			);
			
			$this->add_control('instasearch_meta_city',
				[
					'label'        	=> __("Ville", 'eac-components'),
					'type'         	=> Controls_Manager::SWITCHER,
					'label_on'     	=> __('oui', 'eac-components'),
					'label_off'    	=> __('non', 'eac-components'),
					'return_value' 	=> 'yes',
					'default'      	=> 'yes',
				]
			);
			
			$this->add_control('instasearch_meta_address',
				[
					'label'        	=> __("Adresse", 'eac-components'),
					'type'         	=> Controls_Manager::SWITCHER,
					'label_on'     	=> __('oui', 'eac-components'),
					'label_off'    	=> __('non', 'eac-components'),
					'return_value' 	=> 'yes',
					'default'      	=> 'yes',
				]
			);
			
			$this->add_control('instasearch_meta_idplace',
				[
					'label'        	=> __("Identifiant", 'eac-components'),
					'type'         	=> Controls_Manager::SWITCHER,
					'label_on'     	=> __('oui', 'eac-components'),
					'label_off'    	=> __('non', 'eac-components'),
					'return_value' 	=> 'yes',
					'default'      	=> '',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('instasearch_layout_settings',
			[
				'label' => __('Disposition', 'eac-components'),
			]
		);
		
			$this->add_responsive_control('instasearch_layout_columns',
				[
					'label'   => __('Nombre de colonnes', 'eac-components'),
					'type'    => Controls_Manager::SELECT,
					'default' => '3',
					'tablet_default' => '2',
					'mobile_default' => '1',
					'options'       => [
						'1'    => '1',
						'2'    => '2',
						'3'    => '3',
						'4'    => '4',
						'5'    => '5',
						'6'    => '6',
					],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('instasearch_general_style',
			[
				'label'      => __('Global', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);
		
			$this->add_control('instasearch_wrapper_style',
				[
					'label'			=> __("Style", 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'default'		=> 'style-1',
					'options'       => [
						'style-0' => __("Défaut", 'eac-components'),
                        'style-1' => 'Style 1',
                        'style-2' => 'Style 2',
						'style-3' => 'Style 3',
						'style-4' => 'Style 4',
						'style-5' => 'Style 5',
						'style-6' => 'Style 6',
						'style-7' => 'Style 7',
						'style-8' => 'Style 8',
                    ],
				]
			);
			
			$this->add_responsive_control('instasearch_item_margin',
				[
					'label' => __('Marge entre les images', 'eac-components'),
					'type'  => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 5,	'unit' => 'px'],
					'tablet_default' => ['size' => 3, 'unit' => 'px'],
					'mobile_default' => ['size' => 1, 'unit' => 'px'],
					'range' => ['px' => ['min' => -1, 'max' => 10, 'step' => 2]],
					'selectors' => ['{{WRAPPER}} .insta-search__item-content' => 'margin: {{SIZE}}{{UNIT}};'],
				]
			);
			
			$this->add_control('instasearch_wrapper_bg_color',
				[
					'label' => __('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#e7f0fd',
					'selectors' => ['{{WRAPPER}} .eac-insta-search' => 'background-color: {{VALUE}};'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('instasearch_excerpt_style',
			[
               'label' => __("Légende", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('instasearch_excerpt_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => [ '{{WRAPPER}} .insta-search__item-description' => 'color: {{VALUE}};' ],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'instasearch_excerpt_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .insta-search__item-description',
				]
			);
		
		$this->end_controls_section();
		
		/**
		 * Pictograms Style Section
		 */
		$this->start_controls_section('instasearch_icon_style',
			[
				'label'      => __('Pictogrammes', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
			]
		);
		
			$this->add_control('instasearch_icon_color',
				[
					'label'  => __('Couleur', 'eac-components'),
					'type'   => Controls_Manager::COLOR,
					'selectors' => ['{{WRAPPER}} .insta-search__item-description div > i' => 'color: {{VALUE}};',
									'{{WRAPPER}} .insta-search__item-description .insta-search__isverified' => 'color: #00bfff;'],
				]
			);
			
		$this->end_controls_section();
    }
	
	/*
    * Render widget output on the frontend.
    *
    * Written in PHP and used to generate the final HTML.
    *
    * @access protected
    */
    protected function render() {
		$settings = $this->get_settings_for_display();
		
		$class = vsprintf('insta-search desktop-column-%s tablet-column-%s mobile-column-%s', $this->init_settings());
		$this->add_render_attribute('insta_search', 'class', $class);
		$this->add_render_attribute('insta_search', 'data-settings', $this->get_settings_json());
		
		?>
		<div class="eac-insta-search">
			<?php $this->render_galerie(); ?>
			<div <?php echo $this->get_render_attribute_string('insta_search'); ?>>
				<div class="insta-search__item-sizer"></div>
			</div>
			<div class="insta-search__error"></div>
		</div>
		<?php
    }
	
    protected function render_galerie() {
		$settings = $this->get_settings_for_display();
		
		?>
		<div class="insta-search__select-item-list">
			<div class="insta-search__options-items-info">			
				<h2><?php echo __('Recherche profils Instagram (Utilisateur, Hashtag, Lieu)', 'eac-components'); ?></h2>
				<p><?php echo __('Les résultats sont stockés pendant la session', 'eac-components'); ?></p>
			</div>
			<div class="insta-search__container-rb">
				<label for="insta-search__rdo1">
					<input type="radio" id="insta-search__rdo1" name="insta-search__radio" value="user" checked>
					<span class="rdo"></span>
					<span><?php echo __('Utilisateur', 'eac-components'); ?></span>
				</label>
				<label for="insta-search__rdo2">
					<input type="radio" id="insta-search__rdo2" name="insta-search__radio" value="hashtag">
					<span class="rdo"></span>
					<span><?php echo __('Mot-dièse', 'eac-components'); ?></span>
				</label>
				<label for="insta-search__rdo3">
					<input type="radio" id="insta-search__rdo3" name="insta-search__radio" value="place">
					<span class="rdo"></span>
					<span><?php echo __('Lieu', 'eac-components'); ?></span>
				</label>
			</div>
			
			
			<div class="insta-search__select-item">
				<input type="text" id="insta-search__item-name" name="insta-search__item-name" maxlength="150" placeholder="<?php echo __('Mots-clés', 'eac-components'); ?>" required>
			</div>
			<div class="eac__button">
				<button id="insta-search__read-button" class="eac__read-button"><?php _e('Lire le flux', 'eac-components'); ?></button>
			</div>
			<div id="insta-search__loader-wheel" class="eac__loader-spin"></div>
		</div>
		<div class="insta-search__header"></div>
		<?php
	}
	
	protected function init_settings() {
		$module_settings = $this->get_settings_for_display();
		$columns = isset($module_settings['instasearch_layout_columns']) ? $module_settings['instasearch_layout_columns'] : 3;
		$columns_tab = isset($module_settings['instasearch_layout_columns_tablet']) ? $module_settings['instasearch_layout_columns_tablet'] : 2;
		$columns_mob = isset($module_settings['instasearch_layout_columns_mobile']) ? $module_settings['instasearch_layout_columns_mobile'] : 1;
		
		return array($columns, $columns_tab, $columns_mob);
	}
	
	/*
	* get_settings_json()
	*
	* Retrieve fields values to pass at the widget container
    * Convert on JSON format
    * Read by 'eac-components.js' file when the component is loaded on the frontend
	*
	* @uses      json_encode()
	*
	* @return    JSON oject
	*
	* @access    protected
	* @since     1.0.0
	* @updated   1.0.7
	*/
	
	protected function get_settings_json() {
		$module_settings = $this->get_settings_for_display();
		
		$settings = array(
			"data_style"		=> $module_settings['instasearch_wrapper_style'],
			//"data_sort"			=> $module_settings['instasearch_item_sort'],
			"data_sort"			=> "user",
			"data_fullname"		=> $module_settings['instasearch_meta_fullname'] === 'yes' ? true : false,
			//"data_followers"	=> $module_settings['instasearch_meta_followers'] === 'yes' ? true : false,
			//"data_byline"		=> $module_settings['instasearch_meta_byline'] === 'yes' ? true : false,
			"data_followers"	=> true,
			"data_byline"		=> false,
			"data_private"		=> $module_settings['instasearch_meta_private'] === 'yes' ? true : false,
			"data_id"			=> $module_settings['instasearch_meta_id'] === 'yes' ? true : false,
			"data_pub"			=> $module_settings['instasearch_meta_pub'] === 'yes' ? true : false,
			"data_publang"		=> $module_settings['instasearch_meta_pub_lang'] === 'yes' ? true : false,
			"data_city"			=> $module_settings['instasearch_meta_city'] === 'yes' ? true : false,
			"data_address"		=> $module_settings['instasearch_meta_address'] === 'yes' ? true : false,
			"data_placeid"		=> $module_settings['instasearch_meta_idplace'] === 'yes' ? true : false,
		);
		
		$settings = json_encode($settings);
		return $settings;
	}
	
	protected function content_template() {}
	
}