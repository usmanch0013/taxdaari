<?php

/*=================================================================
* Class: KenBurn_Slider_Widget
* Name: Carrousel Ken Burn
* Slug: eac-addon-kenburn-slider
*
* Description: KenBurn_Slider_Widget affiche des images animées
* avec effet Ken Burn
*
* @since 0.0.9
*==================================================================*/
 
namespace EACCustomWidgets\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Control_Media;

if (! defined('ABSPATH')) exit; // Exit if accessed directly

class KenBurn_Slider_Widget extends Widget_Base {

    /*
    * Retrieve widget name.
    *
    * @access public
    *
    * @return widget name.
    */
    public function get_name() {
        return 'eac-addon-kenburn-slider';
    }

    /*
    * Retrieve widget title.
    *
    * @access public
    *
    * @return widget title.
    */
    public function get_title() {
        return __("Carrousel Ken Burn", 'eac-components');
    }

    /*
    * Retrieve widget icon.
    *
    * @access public
    *
    * @return widget icon.
    */
    public function get_icon() {
        return 'eicon-media-carousel';
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
		return ['eac-smoothslides'];
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
		return ['eac-smoothslides'];
	}
	
    /*
    * Register widget controls.
    *
    * Adds different input fields to allow the user to change and customize the widget settings.
    *
    * @access protected
    */
    protected function register_controls() {
		
		$this->start_controls_section('kbs_images_settings',
			[
				'label'     => __('Galerie', 'eac-components'),
			]
		);
			
			$this->add_control('kbs_galerie',
				[
					'label' => __('Ajouter des images', 'eac-components'),
					'type' => Controls_Manager::GALLERY,
					'default' => [],
				]
			);
			
			$this->add_control('kbs_galerie_attention',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => __("<b>Les images doivent être de mêmes dimensions.<br>Le container (Section - Largeur du contenu) doit être plus petit que les images !!</b>", 'eac-components'),
				]
			);
		
		$this->end_controls_section();
		
		$this->start_controls_section('kbs_slider_settings',
			[
				'label'     => __('Réglages', 'eac-components'),
			]
		);
			
			$this->add_control('kbs_slides_duree',
				[
					'label' => __("Durée de l'effet (millisecondes)", 'eac-components'),
					'type' => Controls_Manager::NUMBER,
					'min' => 5000,
					'max' => 10000,
					'step' => 1000,
					'default' => 6000,
					'label_block'	=> false,
				]
			);
			
			$this->add_control('kbs_slides_zoom',
				[
					'label' => __('Facteur de zoom', 'eac-components'),
					'description'	=> __("Si les images se chevauchent, augmenter le facteur de zoom.", 'eac-components'),
					'type' => Controls_Manager::NUMBER,
					'min' => 1,
					'max' => 2,
					'step' => 0.1,
					'default' => 1.4,
					'label_block'	=> false,
				]
			);
			
			$this->add_control('kbs_slides_ease',
				[
					'label'			=> __('Transition', 'eac-components'),
					'description'	=> __("Vitesse de transition. Début/Fin", 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'default'		=> 'ease-in-out',
					'options'       => [
						'linear'    => __('Linéaire', 'eac-components'),
						'ease'    => __('Lente, rapide et lente', 'eac-components'),
						'ease-in'    => __('Lente et rapide', 'eac-components'),
						'ease-out'    => __('Rapide et lente', 'eac-components'),
						'ease-in-out'    => __('Lente et lente', 'eac-components'),
                    ],
					'label_block'	=> false,
				]
			);
			
			$this->add_control('kbs_slides_navigation',
				[
					'label' => __("Navigation", 'eac-components'),
					'description'	=> __("Afficher la navigation.", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
			$this->add_control('kbs_slides_caption',
				[
					'label' => __("Légende", 'eac-components'),
					'description'	=> __("Attribut 'ALT' ou 'Légende' de l'image", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
		$this->end_controls_section();
		
		/**
		 * Generale Style Section
		 */
		$this->start_controls_section('kbs_general_style',
			[
				'label'      => __('Effets', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
			]
		);			
			$this->add_control('kbs_select_effect',
			[
				'label' => __("Sélection", 'eac-components'),
				'description' => __('Très consommateur de ressources...', 'eac-components'),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => [
					'panUp' => __('Panoramique haut', 'eac-components'),
					'panDown' => __('Panoramique bas', 'eac-components'),
					'panLeft' => __('Panoramique gauche', 'eac-components'),
					'panRight' => __('Panoramique droit', 'eac-components'),
					'zoomIn' => __('Zoom interne', 'eac-components'),
					'zoomOut' => __('Zoom externe', 'eac-components'),
					'diagTopLeftToBottomRight' => __('Bas Droit', 'eac-components'),
					'diagTopRightToBottomLeft' => __('Bas Gauche', 'eac-components'),
					'diagBottomRightToTopLeft' => __('Haut Gauche', 'eac-components'),
					'diagBottomLeftToTopRight' => __('Haut Droit', 'eac-components'),
				],
				'default' => ['panUp'],
				'label_block' => true,
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
		if(! $settings['kbs_galerie']) {
			return;
		}
		
		$id = "kbs_slides_" . uniqid();
		$this->add_render_attribute('kbs_slide', 'class', "kbs-slides");
		$this->add_render_attribute('kbs_slide', 'id', $id);
		$this->add_render_attribute('kbs_slide', 'data-settings', $this->get_settings_json($id));
		?>
		<div class="eac-kenburn-slider">
			<div <?php echo $this->get_render_attribute_string('kbs_slide'); ?>>
				<?php $this->render_galerie(); ?>
			</div>
		</div>
		<?php
    }
	
    protected function render_galerie() {
		$settings = $this->get_settings_for_display();
		$html = '';
		
		foreach($settings['kbs_galerie'] as $image) {
			$attachment = get_post($image['id']);
			$image_alt = !empty($attachment->post_excerpt) ? $attachment->post_excerpt : Control_Media::get_image_alt($image);
			$image_data = wp_get_attachment_image_src($image['id'], 'full');
			$current_image = sprintf('<img class="eac-image-loaded" src="%s" alt="%s" width="%d" height="%d" />', esc_url($image_data[0]), $image_alt, $image_data[1], $image_data[2]);
			$html .= $current_image;
		}
		
	echo $html;
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
	* @since     0.0.9
	* @updated   1.0.7
	*/
	protected function get_settings_json($dataid) {
		$module_settings = $this->get_settings_for_display();
		
		// Les effets sélectionnés
		if(empty($module_settings['kbs_select_effect'])) { $effets = "panUp"; }
		else { $effets = implode(',', $module_settings['kbs_select_effect']); }
		
		$settings = array(
			"effect" => $effets,
			"data_id" => $dataid,
			"effectDuration" => empty($module_settings['kbs_slides_duree']) ? "6000" : $module_settings['kbs_slides_duree'],
			"effectModifier" => empty($module_settings['kbs_slides_zoom']) ? "1.4" : $module_settings['kbs_slides_zoom'],
			"effectEasing" => $module_settings['kbs_slides_ease'],
			"navigation" => $module_settings['kbs_slides_navigation'] === 'yes' ? "true" : "false",
			"captions" => $module_settings['kbs_slides_caption'] === 'yes' ? "true" : "false",
		);

		$settings = json_encode($settings);
		return $settings;
	}
	
	protected function content_template() {}
	
}