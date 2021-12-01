<?php

/*=====================================================================================
* Class: Eac_Injection_Widget_Sticky
*
* Description: Injecte la section et les controls dans les sections/Colonnes/Widgets 
* après la section 'Motion effects' sous l'onglet 'Advanced'
*
*
* @since 1.8.1
* @since 1.8.7	Support des custom breakpoints
*=====================================================================================*/

namespace EACCustomWidgets\Includes\Elementor\Injection;

use Elementor\Controls_Manager;
use Elementor\Element_Base;
use Elementor\Plugin;

// Exit if accessed directly
if(!defined('ABSPATH')) { exit; }

class Eac_Injection_Widget_Sticky {
	
	/**
	 * @var $active_breakpoints
	 *
	 * La liste des breakpoints actifs
	 *
	 * @since 1.8.7
	 */
	private $active_breakpoints = [];
	
	/**
	 * @var $active_devices
	 *
	 * La liste ordonnée des breakpoints actifs
	 *
	 * @since 1.8.7
	 */
	private $active_devices = [];
	
	/**
	 * @var $device_options
	 *
	 * La liste des breakpoints actifs pour les options du control
	 * $device_options[$device] = $label;
	 *
	 * @since 1.8.7
	 */
	private $device_options = [];
	
	/**
	 * Constructeur de la class
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action('elementor/element/after_section_end', array($this, 'inject_section'), 10, 3);
		
		add_action('elementor/frontend/section/before_render', array($this, 'eac_render_sticky'));
		add_action('elementor/frontend/column/before_render', array($this, 'eac_render_sticky'));
		add_action('elementor/frontend/widget/before_render', array($this, 'eac_render_sticky'));
		
		add_action('elementor/frontend/before_enqueue_scripts', array($this, 'eac_enqueue_scripts'));
	}
	
	/**
	 * eac_enqueue_scripts
	 *
	 * Mets le script dans le file
	 *
	 *  @since 1.8.1
	 */
	public function eac_enqueue_scripts() {
		wp_enqueue_script('eac-sticky', EAC_ADDONS_URL . 'assets/js/elementor/eac-element-sticky.js',	array('jquery', 'elementor-frontend'), EAC_ADDONS_VERSION, true);
	}
	
	/**
	 * inject_section
	 *
	 * Inject le control après la section 'section_effects' Advanced tab
	 * pour les sections et widgets
	 *
	 * @param Element_Base	$element	The edited element.
	 * @param String		$section_id	L'ID de la section
	 * @param array 		$args		Section arguments.
	 * @since 1.8.1
	 * @since 1.8.7			Custom breakpoints
	 */
	public function inject_section($element, $section_id, $args) {
	
		if(!$element instanceof Element_Base) {
			return;
		}
		
		if('section_effects' === $section_id && in_array($element->get_type(), ['section', 'column', 'widget'])) {
			
			/**
			 * @since 1.8.7 Application des breakpoints
			 */
			// Les breakpoints actifs
			$this->active_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();
			
			if(version_compare(ELEMENTOR_VERSION, '3.4.0', '>=')) {
				// Les arguments pour ajouter le device 'desktop'
				$args = ['add_desktop' => true, 'reverse' => true];
				
				// La liste des devices
				$this->active_devices = Plugin::$instance->breakpoints->get_active_devices_list($args);
			} else {
				// Devices need to be ordered from largest to smallest.
				$this->active_devices = array_reverse(array_keys($this->active_breakpoints));
				
				// Add desktop in the correct position.
				if(in_array('widescreen', $this->active_devices, true)) {
					$this->active_devices = array_merge(array_slice($this->active_devices, 0, 1), ['desktop'], array_slice($this->active_devices, 1));
				} else {
					$this->active_devices = array_merge(['desktop'], $this->active_devices);
				}
			}
			
			// Les options du control
			foreach($this->active_devices as $device) {
				$label = 'desktop' === $device ? esc_html__('Desktop', 'eac-components') : $this->active_breakpoints[$device]->get_label();
				$this->device_options[$device] = $label;
			}
			
			$element->start_controls_section('eac_element_sticky_advanced',
				[
					'label' => __('EAC effet sticky', 'eac-components'),
					'tab' => Controls_Manager::TAB_ADVANCED,
				]
			);

				$element->add_control('eac_element_sticky',
					[
						'label' => __("Activer l'effect sticky", 'eac-components'),
						'type' => Controls_Manager::SWITCHER,
						'label_on' => __('oui', 'eac-components'),
						'label_off' => __('non', 'eac-components'),
						'return_value' => 'yes',
						'default' => '',
					]
				);			
				
				$element->add_control('eac_element_sticky_warning',
					[
						'type' => Controls_Manager::RAW_HTML,
						'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
						'raw'  => __("<strong>Attention:</strong> La zone de déplacement est à l'intérieur du conteneur parent. Section: viewport. Colonne & Widget: section.", "eac-components"),
						'condition' => ['eac_element_sticky' => 'yes'],
					]
				);
				
				/** @since 1.8.7 Application des breakpoints */
				$element->add_control('eac_element_sticky_devices',
					[
						'label'			=> __('Actif avec', 'eac-components'),
						'type'			=> Controls_Manager::SELECT2,
						'multiple'		=> true,
						'label_block'	=> true,
						'default'		=> $this->active_devices,
						'options'		=> $this->device_options,
						'condition'		=> ['eac_element_sticky' => 'yes'],
					]
				);
				
				$element->add_control('eac_element_sticky_up',
					[
						'label' => __('Seuil supérieur de déclenchement', 'eac-components'),
						'type'  => Controls_Manager::NUMBER,
						'min' => 0,
						'max' => 500,
						'step' => 10,
						'default' => 50,
						'condition' => ['eac_element_sticky' => 'yes'],
					]
				);
				
				$element->add_control('eac_element_sticky_down',
					[
						'label' => __('Seuil inférieur de déclenchement', 'eac-components'),
						'type'  => Controls_Manager::NUMBER,
						'min' => 0,
						'max' => 500,
						'step' => 10,
						'default' => 50,
						'condition' => ['eac_element_sticky' => 'yes'],
					]
				);
			
				$element->add_control('eac_element_sticky_zindex',
					[
						'label' => __("Ordre de l'élément (z-index)", 'eac-components'),
						'type'  => Controls_Manager::NUMBER,
						'min' => 0,
						'max' => 10000,
						'step' => 1,
						'default' => 9900,
						'condition' => ['eac_element_sticky' => 'yes'],
						'selectors' => ['{{WRAPPER}}' => 'z-index: {{VALUE}};'],
					]
				);
				
			$element->end_controls_section();
		}
	}
	
	/**
	 * eac_render_sticky
	 *
	 * Ajoute la class et les propriétés dans l'objet avant le rendu
	 * 
	 * * @param $element	Element_Base
	 * @since 1.8.1
	 */
	public function eac_render_sticky($element) {
		$settings = $element->get_settings_for_display();
		
		// Le control existe et il est renseigné
		if(isset($settings['eac_element_sticky']) && 'yes' === $settings['eac_element_sticky']) {
			
			$element_settings = array(
				"id"		=> $element->get_data('id'),
				"widget"	=> $element->get_name(),
				"sticky"	=> $settings['eac_element_sticky'],
				"up"		=> isset($settings['eac_element_sticky_up']) ? $settings['eac_element_sticky_up'] : 50,
				"down"		=> isset($settings['eac_element_sticky_down']) ? $settings['eac_element_sticky_down'] : 50,
				"devices"	=> isset($settings['eac_element_sticky_devices']) ? $settings['eac_element_sticky_devices'] : ['desktop'],
			);
			
			// Elementor utilise data-settings dans les sections
			$element->add_render_attribute('_wrapper', array(
				'class' => 'eac-element-sticky-class',
				'data-eac_settings' => json_encode($element_settings),
			));
		}
	}
}
new Eac_Injection_Widget_Sticky();