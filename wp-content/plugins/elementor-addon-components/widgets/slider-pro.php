<?php

/*=================================================================================================
* Class: Slider_Pro_Widget
* Name: Slider Pro
* Slug: eac-addon-slider-pro
*
* Description: Slider_Pro_Widget affiche et anime des images défilantes
*
* @since 0.0.9
* @since 1.4.1  Forcer le chargement des images depuis le serveur
* @since 1.6.0  Activation de la propriété 'dynamic' des controls de l'image
*				Gestion des images avec des URLs externes par la balise dynamique du control MEDIA
* @since 1.6.3	Attribut ALT pour les images externes
* @since 1.8.7	Support des custom breakpoints
*==================================================================================================*/

namespace EACCustomWidgets\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Border;
use Elementor\Repeater;
use Elementor\Utils;

if (! defined('ABSPATH')) exit; // Exit if accessed directly
 
class Slider_Pro_Widget extends Widget_Base {

    /**
     * Retrieve widget name.
     *
     * @access public
     *
     * @return widget name.
     */
    public function get_name() {
        return 'eac-addon-slider-pro';
    }

    /**
     * Retrieve widget title.
     *
     * @access public
     *
     * @return widget title.
     */
    public function get_title() {
        return __("Slider Pro", 'eac-components');
    }

    /**
     * Retrieve widget icon.
     *
     * @access public
     *
     * @return widget icon.
     */
    public function get_icon() {
        return 'eicon-slider-push';
    }
	
	/**
     * Retrieve category name.
     *
     * @access public
     *
     * @return widget category name.
     */
	public function get_categories() {
		return ['eac-elements'];
	}
	
	/**
     * Retrieve javascript depends widget.
     *
     * @access public
     *
     * @return javascript list.
     */
	public function get_script_depends() {
		return ['eac-sliderpro', 'eac-imagesloaded', 'eac-transit'];
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
		return ['eac-slider-pro'];
	}
	
	/*
    * Register widget controls.
    *
    * Adds different input fields to allow the user to change and customize the widget settings.
    *
    * @access protected
    */
    protected function register_controls() {
		
		$this->start_controls_section('spro_images_settings',
			[
				'label'     => __('Galerie', 'eac-components'),
			]
		);
			
			$repeater = new Repeater();
            
            /** @since 1.6.0 Ajout de la propriété 'dynamic' */
			$repeater->add_control('spro_item_image',
				[
					'label'   => __('Images', 'eac-components'),
					'type'    => Controls_Manager::MEDIA,
					'dynamic' => ['active' => true],
					'default' => [
						'url' => Utils::get_placeholder_image_src(),
					],
				]
			);

			$repeater->add_control('spro_item_title',
				[
					'label'   => __('Titre', 'eac-components'),
					'type'    => Controls_Manager::TEXT,
				]
			);
			
			$repeater->add_control('spro_item_desc',
				[
					'label'   => __('Texte', 'eac-components'),
					'type'    => Controls_Manager::TEXT,
					'placeholder' => __('En savoir plus', 'eac-components'),
					'label_block'	=> true,
				]
			);
            
            /** @since 1.6.0 Ajout de la propriété 'dynamic' */
			$repeater->add_control('spro_item_url',
				[
					'label'       => __('Lien sur le texte', 'eac-components'),
					'type'        => Controls_Manager::URL,
					'placeholder' => 'http://your-link.com',
					'dynamic' => ['active' => true],
					'default' => [
						'url' => '',
						'is_external' => true,
						'nofollow' => true,
					],
				]
			);

			$this->add_control('spro_image_list',
				[
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => [
						[
							'spro_item_image'       => ['url' => Utils::get_placeholder_image_src()],
							'spro_item_title'       => __('Slide #1', 'eac-components'),
						],
						[
							'spro_item_image'       => ['url' => Utils::get_placeholder_image_src()],
							'spro_item_title'       => __('Slide #2', 'eac-components'),
						],
						[
							'spro_item_image'       => ['url' => Utils::get_placeholder_image_src()],
							'spro_item_title'       => __('Slide #3', 'eac-components'),
						],
						[
							'spro_item_image'       => ['url' => Utils::get_placeholder_image_src()],
							'spro_item_title'       => __('Slide #4', 'eac-components'),
						]
					],
					'title_field' => '{{{ spro_item_title }}}',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('spro_slider_settings',
			[
				'label'     => __('Réglages', 'eac-components'),
			]
		);
			
			$this->add_control('spro_slider_title',
				[
					'label' => __("Afficher le titre", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('spro_slider_desc',
				[
					'label' => __("Afficher le texte", 'eac-components'),
					'description' => __("Défaut : Attribut 'ALT ou TITLE' de l'image", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('spro_slider_animation',
				[
					'label'		=> __("Animation Titre/Texte", 'eac-components'),
					'type'		=> Controls_Manager::SELECT,
					'default' => 'ct',
					'tablet_default' => 'ct',
					'mobile_default' => 'ct',
					'tablet_extra_default' => 'ct',
					'mobile_extra_default' => 'ct',
					'options'	=> [
						'hc'	=> __('Haut', 'eac-components'),
						'ct'	=> __('Centre', 'eac-components'),
						'bc'	=> __('Bas', 'eac-components'),
			
                    ],
					'label_block' => true,
					'conditions' => [
						'relation' => 'or',
						'terms' => [
							['name' => 'spro_slider_title', 'operator' => '===', 'value' => 'yes'],
							['name' => 'spro_slider_desc', 'operator' => '===', 'value' => 'yes'],
						],
					],
				]
			);
			
			$this->add_control('spro_slider_multi_images',
				[
					'label' => __("Voir plusieurs slides", 'eac-components'),
					'description' => __("Afficher les slides 'Suivant' et 'Précédent'.", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'separator' => 'before',
				]
			);
			
			$this->add_control('spro_slider_fullwidth',
				[
					'label' => __("Pleine largeur", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'condition' => ['spro_slider_multi_images' => 'yes', 'spro_slider_transition!' => 'vertical'],
				]
			);
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_control('spro_slider_width',
				[
					'label' => __("Largeur du slider (px)", 'eac-components'),
					'type'  => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 600, 'unit' => 'px'],
					'range' => ['px' => ['min' => 200, 'max' => 1100, 'step' => 50]],
					'separator' => 'before',
					'condition' => ['spro_slider_multi_images!' => 'yes'],
				]
			);
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_control('spro_slider_height',
				[
					'label' => __("Hauteur du slider (px)", 'eac-components'),
					'type'  => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 450, 'unit' => 'px'],
					'range' => ['px' => ['min' => 150, 'max' => 1000, 'step' => 50]],
				]
			);
			
			$this->add_control('spro_slider_autoplay',
				[
					'label' => __("Lecture automatique", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
					'separator' => 'before',
				]
			);
			
			$this->add_control('spro_slider_delay',
				[
					'label' => __("Interval d'affichage (ms)", 'eac-components'),
					'type' => Controls_Manager::NUMBER,
					'min' => 1000,
					'max' => 10000,
					'step' => 500,
					'default' => 5000,
					'label_block'	=> true,
					'condition' => ['spro_slider_autoplay' => 'yes'],
				]
			);
			
		$this->end_controls_section();
		
			$this->start_controls_section('spro_controls_settings',
			[
				'label'     => __('Controls', 'eac-components'),
			]
		);
			
			$this->add_control('spro_control_arrows',
				[
					'label' => __("Flèches", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
			$this->add_control('spro_control_bullets',
				[
					'label' => __("Puces", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('spro_control_thumbs',
				[
					'label' => __("Miniatures", 'eac-components'),
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
		$this->start_controls_section('spro_slider_style',
			[
				'label'      => __('Galerie', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
			]
		);			
			
			$this->add_control('spro_slider_transition',
				[
					'label'			=> __('Transition des images', 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'default'		=> 'horizontal',
					'options'       => [
                        'horizontal'	=> __('Défilement horizontal', 'eac-components'),
                        'vertical'		=> __('Défilement vertical', 'eac-components'),
                        //'fade'			=> __('Estompée', 'eac-components'),
                    ],
					'label_block' => true,
				]
			);
			
			$this->add_control('spro_slider_rtl',
				[
					'label' => __("Défilement Droite/Gauche", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
					'condition' => ['spro_slider_transition' => 'horizontal'],
				]
			);					
			
			$this->add_control('spro_wrapper_bg_color',
				[
					'label' => __('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#FBFBE8',
					'selectors' => ['{{WRAPPER}} .sp-slides-container' => 'background-color: {{VALUE}};'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('spro_titre_style',
			[
				'label'      => __('Titre', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'condition' => ['spro_slider_title' => 'yes'],
			]
		);			
			
			$this->add_control('spro_titre_tag',
				[
					'label'			=> __('Étiquette', 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'default'		=> 'h2',
					'options'       => [
						'h1'    => 'H1',
                        'h2'    => 'H2',
                        'h3'    => 'H3',
                        'h4'    => 'H4',
                        'h5'    => 'H5',
                        'h6'    => 'H6',
						'div'	=> 'div',
						'p'		=> 'p'
                    ],
				]
			);
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('spro_titre_padding',
				[
					'label' => __('Marge interne', 'eac-components'),
					'type' => Controls_Manager::DIMENSIONS,
					'allowed_dimensions' => ['top', 'right', 'bottom', 'left'],
					'default' => ['top' => 5, 'right' => 50, 'bottom' => 5, 'left' => 50, 'unit' => 'px', 'isLinked' => true],
					'selectors' => ['{{WRAPPER}} .spro-slide-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
				]
			);
		
			$this->add_control('spro_titre_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_3,
					],
					'default' => '#FFF',
					'selectors' => ['{{WRAPPER}} .spro-slide-title .spro-slide-title-texte' => 'color: {{VALUE}};'],
				]
			);
			
			$this->add_control('spro_titre_bgcolor',
				[
					'label' => __('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' 	=> Color::get_type(),
						'value' => Color::COLOR_1,
					],
					'default' => 'rgba(0,0,0,0.3)',
					'selectors' => ['{{WRAPPER}} .spro-slide-title' => 'background-color: {{VALUE}};'],
					'label_block'	=> false,
				]
			);
			
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'spro_titre_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .spro-slide-title .spro-slide-title-texte'
				]
			);
			
			$this->add_group_control(
			Group_Control_Border::get_type(),
				[
					'name' => 'spro_titre_border',
					'fields_options' => [
						'border' => ['default' => 'solid'],
						'width' => [
							'default' => [
								'top' => 2,
								'right' => 2,
								'bottom' => 2,
								'left' => 2,
								'unit' => 'px',
								'isLinked' => true,
							],
						],
						'color' => ['default' => '#FFF'],
					],
					'separator' => 'before',
					'selector' => '{{WRAPPER}} .spro-slide-title'
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('spro_texte_style',
			[
				'label'      => __('Texte', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'condition' => ['spro_slider_desc' => 'yes'],
			]
		);			
			
			$this->add_control('spro_texte_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_3,
					],
					'default' => '#FFF',
					'selectors' => [
						'{{WRAPPER}} .spro-slide-desc .spro-slide-desc-texte, {{WRAPPER}} .spro-slide-desc i' => 'color: {{VALUE}};',
						'{{WRAPPER}} .spro-slide-desc::before' => 'border-top-color: {{VALUE}};',
						'{{WRAPPER}} .spro-slide-desc a:hover p, {{WRAPPER}} .spro-slide-desc a:hover i' => 'color: #bab305;',
					],
				]
			);
			
			$this->add_control('spro_texte_bgcolor',
				[
					'label' => __('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' 	=> Color::get_type(),
						'value' => Color::COLOR_1,
					],
					'default' => 'rgba(0,0,0,0.3)',
					'selectors' => ['{{WRAPPER}} .spro-slide-desc' => 'background-color: {{VALUE}};'],
					'label_block'	=> false,
				]
			);
			
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'spro_texte_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .spro-slide-desc .spro-slide-desc-texte, {{WRAPPER}} .spro-slide-desc .spro-slide-desc i'
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('spro_control_style_fleches',
			[
				'label'      => __('Control flèches', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'condition' => ['spro_control_arrows' => 'yes']
			]
		);			
			
			$this->add_control('spro_control_visible',
				[
					'label' => __("Toujours visibles", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('spro_control_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_1,
					],
					'selectors' => [
						'{{WRAPPER}} .sp-previous-arrow::before, {{WRAPPER}} .sp-previous-arrow::after, {{WRAPPER}} .sp-next-arrow::before, {{WRAPPER}} .sp-next-arrow::after' => 'background-color: {{VALUE}};'
					],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('spro_control_style_thumbs',
			[
				'label'      => __('Control miniatures', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'condition' => ['spro_control_thumbs' => 'yes']
			]
		);			
			$this->add_control('spro_control_thumbs_width',
				[
					'label' => __("Largeur", 'eac-components'),
					'type'  => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'range' => ['px' => ['min' => 50, 'max' => 200, 'step' => 10]],
					'default' => ['size' => 120, 'unit' => 'px'],
				]
			);
			
			$this->add_control('spro_control_thumbs_height',
				[
					'label' => __("Hauteur", 'eac-components'),
					'type'  => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'range' => ['px' => ['min' => 50, 'max' => 200, 'step' => 10]],
					'default' => ['size' => 80, 'unit' => 'px'],
				]
			);
			
			$this->add_control('spro_control_thumbs_pos',
				[
					'label'   => __('Position', 'eac-components'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'h',
					'options' => [
						'h'	=> __('Horizontale', 'eac-components'),
						'v'	=> __('Verticale', 'eac-components'),
					],
				]
			);
			
			$this->add_control('spro_control_thumbs_point',
				[
					'label' => __("Afficher le pointer", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
		$this->end_controls_section();
	}
	
	/**
    * Render widget output on the frontend.
    *
    * Written in PHP and used to generate the final HTML.
    *
    * @access protected
    */
	 
    protected function render() {
		$settings = $this->get_settings_for_display();
		if(! $settings['spro_image_list']) {
			return;
		}
		
		$id = "sliderpro_" . uniqid();
		$this->add_render_attribute('spro_slide', 'class', "slider-pro");
		$this->add_render_attribute('spro_slide', 'id', $id);
		$this->add_render_attribute('spro_slide', 'data-settings', $this->get_settings_json());
		$this->add_render_attribute('spro_slide', 'data-settingsanime', $this->get_settingsanime_json($id));
		?>
		<div class="eac-slider-pro">
			<div <?php echo $this->get_render_attribute_string('spro_slide'); ?>>
				<div class="sp-slides">
					<?php $this->render_slider(); ?>
				</div>
			</div>
		</div>
		<?php
    }

	/*
	* Render widget output on the frontend.
	*
	* Written in PHP and used to generate the final HTML.
	*
	* @access protected
	*/
    protected function render_slider() {
		$settings = $this->get_settings_for_display();
		
		$hasTitre = $settings['spro_slider_title'] === 'yes' ? true : false;
		$hasDesc = $settings['spro_slider_desc'] === 'yes'? true : false;
		$hasthumbs = $settings['spro_control_thumbs'] === 'yes'? true : false;
		$titre_tag = $settings['spro_titre_tag'];
		$open_tag = '<'. $titre_tag .' class="spro-slide-title-texte">';
		$close_tag = '</'. $titre_tag .'>';
		$html = '';
		$indice = 0;
		
		foreach($settings['spro_image_list'] as $item) {
		    if(!empty($item['spro_item_image']['url'])) { // Il y a une image
		        $link_url = esc_url($item['spro_item_url']['url']);
				/**
				 * Affecte le titre à l'attribut ALT pour les images externes
				 *
				 * @since 1.6.3 Gestion des images externes 'External image'
				 */
				$image_alt = !empty($item['spro_item_title']) ? $item['spro_item_title'] : __('Pas de titre', 'eac-components');
		        
		        /**
				 * Gestion des images externes
				 * La balise dynamique du control MEDIA ne renvoie pas l'ID de l'image
				 *
				 * @since 1.6.0
				 */
				// Récupère les propriétés de l'image
				if(!empty($item['spro_item_image']['id'])) {
				    $image_data = wp_get_attachment_image_src($item['spro_item_image']['id'], 'full');
		    		$url = esc_url($image_data[0]);
			    	$image_alt = Control_Media::get_image_alt($item['spro_item_image']); // 'get_image_alt' renvoie toujours une chaine par défaut
				    // @since 1.4.1 Ajout du paramètre 'ver' à l'image avec un identifiant unique pour forcer le chargement de l'image du serveur et non du cache
				    $image = sprintf('<img class="eac-image-loaded sp-image" src="%s?ver=%s" alt="%s" />', $url, uniqid(), $image_alt);
				} else {
				    $url = esc_url($item['spro_item_image']['url']);
				    // @since 1.4.1 Ajout du paramètre 'ver' à l'image avec un identifiant unique pour forcer le chargement de l'image du serveur et non du cache
				    $image = sprintf('<img class="eac-image-loaded sp-image" src="%s?ver=%s" alt="%s" />', $url, uniqid(), $image_alt);
				}
				
				$titre = !empty($item['spro_item_title']) ? esc_html($item['spro_item_title']) : __('Pas de titre', 'eac-components');
				$caption = !empty($item['spro_item_desc']) ? esc_html($item['spro_item_desc']) : $image_alt;
				    
				if($link_url) {
					$this->add_render_attribute('spro-link-to', 'href', $link_url);
					if($item['spro_item_url']['is_external']) {
						$this->add_render_attribute('spro-link-to', 'target', '_blank');
					}
					if($item['spro_item_url']['nofollow']) {
						$this->add_render_attribute('spro-link-to', 'rel', 'nofollow');
					}
				}
				
				// On construit le DOM
				$html .= '<div class="sp-slide spro-slide-' . $indice . '">';
					$html .= $image;
					
					// Les miniatures doivent être affichées
					if($hasthumbs) {
						$html .= '<div class="sp-thumbnail">';
							$html .= '<img class="eac-image-loaded sp-thumbnail-image" src="' . $url . '"/>';
						$html .= '</div>';
					}
					
					// Le titre doit être affiché
					if($hasTitre) {
						$html .= '<div class="spro-slide-title">' . $open_tag . $titre . $close_tag . '</div>';
					}
					
					// Le champ description doit être affiché
					if($hasDesc) {
						$html .= '<div class="spro-slide-desc">';
						if($link_url) { // Le lien, s'il existe, doit encadré la description
							$html .= '<a ' . $this->get_render_attribute_string('spro-link-to') . '>';
							$html .= '<i class="fa fa-arrow-right" aria-hidden="true"></i>';
						}
						$html .= '<p class="spro-slide-desc-texte">' . $caption . '</p>';
						if($link_url) { $html .= '</a>'; }
						$html .= '</div>';
					}
					
				$html .= '</div>';
				$indice++;
		    }
			// Vide les attributs html
			$this->set_render_attribute('spro-link-to', 'href', null);
			$this->set_render_attribute('spro-link-to', 'target', null);
			$this->set_render_attribute('spro-link-to', 'rel', null);
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
	* @since     1.0.0
	* @updated   1.0.7
	*/
	protected function get_settings_json() {
		$module_settings = $this->get_settings_for_display();
		
		// Pleine largeur
		$datalayout = $module_settings['spro_slider_fullwidth'] === "yes" ? "fullWidth" : "none";
		// Largeur et hauteur du slider
		$sliderwidth = isset($module_settings['spro_slider_width']['size']) ? $module_settings['spro_slider_width']['size'] : 600;
		$sliderheight = isset($module_settings['spro_slider_height']['size']) ? $module_settings['spro_slider_height']['size'] : 450;
		// durée d'affichage d'un slide
		$datadelay = $module_settings['spro_slider_delay'];
		// Autoplay
		$hasautoplay = $module_settings['spro_slider_autoplay'] === "yes" ? true : false;
		// Défilement estompée
		//$hasfade = $module_settings['spro_slider_transition'] === "fade" ? true : false;
		// Défilement horizontal ou vertical
		//$dataorientation = $hasfade == true ? "horizontal" : $module_settings['spro_slider_transition'] === "horizontal" ? "horizontal" : "vertical";
		$dataorientation = $module_settings['spro_slider_transition'];
		// Flèches. visible au hover
		$hasarrows = $module_settings['spro_control_arrows'] === "yes" ? true : false;
		$hasarrowsvis = $module_settings['spro_control_visible'] === "yes" ? false : true;
		// Puces
		$hasbullets = $module_settings['spro_control_bullets'] === "yes" ? true : false;
		// Plusieurs slides. 100% de largeur de la section. recadrage auto des images
		$hasmulti = $module_settings['spro_slider_multi_images'] === "yes" ? true : false;
		$datavisiblesize = $hasmulti == true ? "100%" : "auto";
		$dataslidesize = $hasmulti == true ? true : false;
		// Défilement droite/gauche ou gauche/droite
		$rtl = $module_settings['spro_slider_rtl'] === "yes" ? false : true;
		// Largeur, hauteur et position des thumbnails
		$thumbswidth = isset($module_settings['spro_control_thumbs_width']['size']) ? $module_settings['spro_control_thumbs_width']['size'] : 120;
		$thumbsheight = isset($module_settings['spro_control_thumbs_height']['size']) ? $module_settings['spro_control_thumbs_height']['size'] : 80;
		$thumbspos = $module_settings['spro_control_thumbs_pos'] == 'v' ? "right" : "bottom";
		$thumbspointer = $module_settings['spro_control_thumbs_point'] === 'yes' ? true : false;
		
		$settings = array(
			"forceSize" => $datalayout,
			"autoplay" => $hasautoplay,
			"width" => $sliderwidth,
			"height" => $sliderheight,
			"autoplayDelay" => $datadelay,
			//"fade" => $hasfade,
			"orientation" => $dataorientation,
			"arrows" => $hasarrows,
			"fadeArrows" => $hasarrowsvis,
			"buttons" => $hasbullets,
			"visibleSize" => $datavisiblesize,
			"autoSlideSize" => $dataslidesize,
			"rightToLeft" => $rtl,
			"thumbnailWidth" => $thumbswidth,
			"thumbnailHeight" => $thumbsheight,
			"thumbnailsPosition" => $thumbspos,
			"thumbnailPointer" => $thumbspointer
		);

		$settings = json_encode($settings);
		return $settings;
	}
	
	/*
	* get_settingsanime_json()
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
	protected function get_settingsanime_json($dataid) {
		$module_settings = $this->get_settings_for_display();
		
		$settings = array(
			"data_id" => $dataid,
			"data_titre" => $module_settings['spro_slider_title'] === 'yes' ? true : false,
			"data_desc" => $module_settings['spro_slider_desc'] === 'yes'? true : false,
			"data_anmpos" => isset($module_settings['spro_slider_animation']) ? $module_settings['spro_slider_animation'] : 'ct',
			"data_anmpos_tab" => isset($module_settings['spro_slider_animation_tablet']) ? $module_settings['spro_slider_animation_tablet'] : 'ct',
			"data_anmpos_mob" => isset($module_settings['spro_slider_animation_mobile']) ? $module_settings['spro_slider_animation_mobile'] : 'ct',
			"data_width_tab" => isset($module_settings['spro_slider_width_tablet']['size']) ? $module_settings['spro_slider_width_tablet']['size'] : 500,
			"data_width_mob" => isset($module_settings['spro_slider_width_mobile']['size']) ? $module_settings['spro_slider_width_mobile']['size'] : 400,
			"data_height_tab" => isset($module_settings['spro_slider_height_tablet']['size']) ? $module_settings['spro_slider_height_tablet']['size'] : 350,
			"data_height_mob" => isset($module_settings['spro_slider_height_mobile']['size']) ? $module_settings['spro_slider_height_mobile']['size'] : 250,
		);

		$settings = json_encode($settings);
		return $settings;
	}
	
	protected function content_template() {}
}