<?php

/*=================================================================
* Class: Reseaux_Sociaux_Widget
* Name: Partager
* Slug: eac-addon-reseaux-sociaux
*
* Description: Reseaux_Sociaux_Widget affiche une liste de réseaux sociaux
* sur une page, qui peut être partager
*
* @since 0.0.9
*==================================================================*/

namespace EACCustomWidgets\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (! defined('ABSPATH')) exit; // Exit if accessed directly

class Reseaux_Sociaux_Widget extends Widget_Base {

    /*
    * Retrieve widget name.
    *
    * @access public
    *
    * @return widget name.
    */
    public function get_name() {
        return 'eac-addon-reseaux-sociaux';
    }

    /*
    * Retrieve widget title.
    *
    * @access public
    *
    * @return widget title.
    */
    public function get_title() {
        return __('Partager', 'eac-components');
    }

    /*
    * Retrieve widget icon.
    *
    * @access public
    *
    * @return widget icon.
    */
    public function get_icon() {
        return 'eicon-share';
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
		return ['eac-reseaux-sociaux'];
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
		return ['eac-reseaux-sociaux'];
	}
	
    /*
    * Register widget controls.
    *
    * Adds different input fields to allow the user to change and customize the widget settings.
    *
    * @access protected
    */
    protected function register_controls() {
		
		$this->start_controls_section('rs_share_select',
			[
				'label'     => __('Réseaux sociaux', 'eac-components'),
			]
		);
			$this->add_control('rs_share_with',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => __("<b>Partager cette page/article avec les réseaux sociaux.</b>", 'eac-components'),
				]
			);
			
			$this->add_control('rs_item_facebook',
				[
					'label' => 'Facebook',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('rs_item_twitter',
				[
					'label' => 'Twitter',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('rs_item_google_plus',
				[
					'label' => 'Google+',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('rs_item_linkedin',
				[
					'label' => 'Linkedin',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('rs_item_odnoklassniki',
				[
					'label' => 'Odnoklassniki',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
			$this->add_control('rs_item_pinterest',
				[
					'label' => 'Pinterest',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
			$this->add_control('rs_item_reddit',
				[
					'label' => 'Reddit',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
			$this->add_control('rs_item_telegram',
				[
					'label' => 'Telegram',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
			$this->add_control('rs_item_tumblr',
				[
					'label' => 'Tumblr',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
			$this->add_control('rs_item_whatsapp',
				[
					'label' => 'Whatsapp',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
			$this->add_control('rs_item_mail',
				[
					'label' => __("Courriel", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('rs_share_settings',
			[
				'label' => __('Réglages', 'eac-components'),
			]
		);
			
			$this->add_control('rs_share_place',
				[
					'label'   => __('Position', 'eac-components'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'top-left',
					'options'       => [
						'top-left'	=>  __('Gauche', 'eac-components'),
						'top-right'	=>  __('Droite', 'eac-components'),
					],
				]
			);
			
			$this->add_control('rs_share_text',
				[
					'label' => __('Texte', 'eac-components'),
					'description' => __('Texte du partage', 'eac-components'),
					'type' => Controls_Manager::TEXT,
					'default' => __('Partager avec:', 'eac-components'),
					'placeholder' => __('Partager avec:', 'eac-components'),
				]
			);
			
			$this->add_control('rs_item_target',
				[
					'label' => __("Ouvrir dans une nouvelle fenêtre", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			/*
			$this->add_responsive_control('rs_item_margin',
				[
					'label' => __('Marge supérieure (%)', 'eac-components'),
					'type'  => Controls_Manager::SLIDER,
					'size_units' => ['%', 'px'],
					'range' => ['%' => ['min' => 0, 'max' => 50, 'step' => 5]],
					'default' => ['unit' => '%', 'size' => 25],
					'selectors' => ['{{WRAPPER}} #floatingSocialShare .top-left, {{WRAPPER}} #floatingSocialShare .top-right' => 'top: {{SIZE}}{{UNIT}};'],
				]
			);
			*/
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
		
		$this->add_render_attribute('rs_items_list', 'class', 'rs-items-list');
		$this->add_render_attribute('rs_items_list', 'data-settings', $this->get_settings_json());
		
		?>
		<div class="eac-reseaux-sociaux">
			<div <?php echo $this->get_render_attribute_string('rs_items_list'); ?>></div>
		</div>
		<?php
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
		$networks = [];
		if($module_settings['rs_item_facebook'] === 'yes') { $networks[] = 'facebook'; };
		if($module_settings['rs_item_twitter'] === 'yes') { $networks[] = 'twitter'; };
		if($module_settings['rs_item_google_plus'] === 'yes') { $networks[] = 'google-plus'; };
		if($module_settings['rs_item_linkedin'] === 'yes') { $networks[] = 'linkedin'; };
		if($module_settings['rs_item_odnoklassniki'] === 'yes') { $networks[] = 'odnoklassniki'; };
		if($module_settings['rs_item_pinterest'] === 'yes') { $networks[] = 'pinterest'; };
		if($module_settings['rs_item_reddit'] === 'yes') { $networks[] = 'reddit'; };
		if($module_settings['rs_item_telegram'] === 'yes') { $networks[] = 'telegram'; };
		if($module_settings['rs_item_tumblr'] === 'yes') { $networks[] = 'tumblr'; };
		if($module_settings['rs_item_whatsapp'] === 'yes') { $networks[] = 'whatsapp'; };
		if($module_settings['rs_item_mail'] === 'yes') { $networks[] = 'mail'; };
		
		$settings = array(
			"data_place" => $module_settings['rs_share_place'],
			"data_text"	=> esc_html($module_settings['rs_share_text']),
			"data_buttons" => $networks,
			"data_popup" => $module_settings['rs_item_target'] === 'yes' ? true : false,
		);
		
		$settings = json_encode($settings);
		return $settings;
	}
	
	protected function content_template() {}
	
}