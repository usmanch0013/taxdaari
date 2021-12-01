<?php

/*===================================================================================================
* Class: Modal_Box_Widget
* Name: Boîte Modale
* Slug: eac-addon-modal-box
*
* Description: Construit et affiche une popup avec différents contenus (Texte, Formulaire, Templates)
* déclenchée par un bouton, une image, du texte ou automatiquement
*
* 
* @since 1.6.1
* @since 1.6.5	Suppression de la valeur de l'attribut ALT par défaut
* @since 1.7.0	Ajout de l'URL du Help center
*				Ajout du contenu HTML dans une iframe
* @since 1.7.61	Check si une URL existe pour le trigger Image
*===================================================================================================*/

namespace EACCustomWidgets\Widgets;

use EACCustomWidgets\Includes\Eac_Tools_Util;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;

if(! defined('ABSPATH')) exit; // Exit if accessed directly

class Modal_Box_Widget extends Widget_Base {

	/*
	* Retrieve widget name.
	*
	* @access public
	*
	* @return widget name.
	*/
	public function get_name() {
		return 'eac-addon-modal-box';
	}

	/*
	* Retrieve widget title.
	*
	* @access public
	*
	* @return widget title.
	*/
	public function get_title() {
		return __("Boîte modale", 'eac-components');
	}

	/*
	* Retrieve widget icon.
	*
	* @access public
	*
	* @return widget icon.
	* https://char-map.herokuapp.com/
	*/
	public function get_icon() {
		return 'eicon-lightbox';
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
		return [''];
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
		return ['eac-modalbox'];
	}
	
	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return ['modalbox', 'popup'];
	}
	
	/**
	 * Get help widget get_custom_help_url.
	 *
	 * 
	 *
	 * @since 1.7.0
	 * @access public
	 *
	 * @return URL help center
	 */
	public function get_custom_help_url() {
        return 'https://elementor-addon-components.com/elementor-modal-box-doc/';
    }
	
	/**
	 * Whether the reload preview is required or not.
	 *
	 * Used to determine whether the reload preview is required.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool Whether the reload preview is required.
	 */
	/*public function is_reload_preview_required() {
		return true;
	}*/
	
	/*
	* Register widget controls.
	*
	* Adds different input fields to allow the user to change and customize the widget settings.
	*
	* @access protected
	*/
	protected function register_controls() {
		
		$this->start_controls_section('mb_param_content',
			[
				'label'		=> __('Contenu', 'eac-components'),
				'tab'		 => Controls_Manager::TAB_CONTENT,
			]
		);
			
			
			$this->add_control('mb_enable_header',
				[
					'label' => __("Afficher l'entête", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
			$this->add_control('mb_texte_header',
				[
					'label'   => __("Titre", 'eac-components'),
					'type'    => Controls_Manager::TEXT,
					'dynamic' => ['active' => true],
					'placeholder' => __("Texte de l'entête", 'eac-components'),
					'label_block' => true,
					'condition' => ['mb_enable_header' => 'yes'],
				]
			);
			
			// @since 1.7.0 Ajout du contenu HTML
			$this->add_control('mb_type_content',
				[
					'label'			=> __('Type de contenu', 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'description'	=> __('Type de contenu à afficher', 'eac-components'),
					'default'		=> 'texte',
					'options'		=> [
						'links'		=> __('Lien Vidéo ou Carte', 'eac-components'),
						'html'		=> __('Lien HTML', 'eac-components'),
						'texte'		=> __('Texte personnalisé', 'eac-components'),
						'formulaire'=> __('Formulaire', 'eac-components'),
						'tmpl_sec'	=> __('Elementor modèle de section', 'eac-components'),
						'tmpl_page'	=> __('Elementor modèle de page', 'eac-components'),
					],
					'label_block'	=>	true,
					'separator' => 'before',
				]
			);
			
			$this->add_control('mb_shortcode_content',
				[
					'label' => __('Entrer le shortcode du formulaire', 'eac-components'),
					'type' => Controls_Manager::TEXTAREA,
					/*'dynamic' => [
						'active' => true,
					],*/
					'placeholder' => '[contact-form-7 id="XXXX""]',
					'default' => '',
					'condition' => ['mb_type_content' => 'formulaire'],
				]
			);
			
			$this->add_control('mb_url_content',
				[
					'label' => __('URL', 'eac-components'),
					'type' => Controls_Manager::URL,
					'placeholder' => 'http://your-link.com',
					'dynamic' => ['active' => true],
					'default' => [
						'url' => '',
						'is_external' => true,
						'nofollow' => true,
					],
					'condition' => ['mb_type_content' => ['links', 'html']],
				]
			);
			
			$this->add_control('mb_texte_content',
				[
					'label' => __('Description', 'eac-components'),
					'type' => Controls_Manager::WYSIWYG,
					'default' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.",
					'condition' => ['mb_type_content' => 'texte'],
				]
			);
			
			$this->add_control('mb_tmpl_sec_content',
				[
					'label'   => __("Elementor modèle de section", 'eac-components'),
					'type'    => Controls_Manager::SELECT,
					'options'	=> Eac_Tools_Util::get_elementor_templates('section'),
					'condition' => ['mb_type_content' => 'tmpl_sec'],
					'label_block' => true,
				]
			);
			
			$this->add_control('mb_tmpl_page_content',
				[
					'label'   => __("Elementor modèle de page", 'eac-components'),
					'type'    => Controls_Manager::SELECT,
					'options'	=> Eac_Tools_Util::get_elementor_templates('page'),
					'condition' => ['mb_type_content' => 'tmpl_page'],
					'label_block' => true,
				]
			);
		
		$this->end_controls_section();
		
		/**
		 * Generale Content Section
		 */
		$this->start_controls_section('mb_param_trigger',
			[
				'label'		=> __('Options de déclenchement', 'eac-components'),
				'tab'		 => Controls_Manager::TAB_CONTENT,
			]
		);
			
			$this->add_control('mb_origin_trigger',
				[
					'label'			=> __('Déclencheur', 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'description'	=> __('Sélectionner le déclencheur', 'eac-components'),
					'options'		=> [
						'button'		=> __('Bouton', 'eac-components'),
						'image'			=> __('Image', 'eac-components'),
						'text'			=> __('Texte', 'eac-components'),
						'pageloaded'	=> __('Ouverture automatique', 'eac-components'),
					],
					'label_block'	=>	true,
					'default'		=> 'button',
				]
			);
			
			$this->add_control('mb_display_text_button',
				[
					'label'			=> __('Texte du bouton', 'eac-components'),
					'default'		=> __('EAC boîte modale', 'eac-components'),
					'type'			=> Controls_Manager::TEXT,
					'dynamic'		=> ['active' => true],
					'label_block'	=> true,
					'condition'		=> ['mb_origin_trigger' => 'button'],
					'separator' => 'before',
				]
			);
			
			$this->add_control('mb_icon_activated',
				[
					'label' => __("Ajouter un pictogramme", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'condition' => ['mb_origin_trigger' => 'button'],
				]
			);
			
			$this->add_control('mb_display_icon_button',
				[
					'label'             => __("Pictogrammes", 'eac-components'),
					'type'              => Controls_Manager::ICONS,
					'default' => ['value' => 'fas fa-arrow-right', 'library' => 'fa-solid',],
					'skin' => 'inline',
					'exclude_inline_options' => ['svg'],
					'condition' => ['mb_origin_trigger' => 'button', 'mb_icon_activated' => 'yes'],
				]
			);
			
			$this->add_control('mb_position_icon_button',
				[
					'label'			=> __('Position', 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'default'		=> 'before',
					'options'		=> [
						'before'	=> __('Avant', 'eac-components'),
						'after'	=> __('Après', 'eac-components'),
					],
					'condition'		=> ['mb_origin_trigger' => 'button', 'mb_icon_activated' => 'yes'],
				]
			);
			
			$this->add_control('mb_marge_icon_button',
				[
					'label' => __('Marges', 'eac-components'),
					'type' => Controls_Manager::DIMENSIONS,
					'allowed_dimensions' => ['left', 'right'],
					'default' => ['left' => 0, 'right' => 0, 'unit' => 'px', 'isLinked' => false],
					'range' => ['px' => ['min' => 0, 'max' => 20, 'step' => 1]],
					'selectors' => ['{{WRAPPER}} .mb-modalbox__wrapper-btn i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
					'condition'	=> ['mb_origin_trigger' => 'button', 'mb_icon_activated' => 'yes'],
				]
			);
			
			$this->add_control('mb_display_size_button',
				[
					'label'			=> __('Dimension du bouton', 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'default'		=> 'md',
					'options'		=> [
						'sm'	=> __('Petit', 'eac-components'),
						'md'	=> __('Moyen', 'eac-components'),
						'lg'	=> __('Large', 'eac-components'),
						'block' => __('Bloc', 'eac-components'),
					],
					'label_block'	=> true,
					'condition'		=> ['mb_origin_trigger' => 'button'],
					'separator' => 'before',
				]
			);
			
			$this->add_control('mb_display_image',
				[
					'label'	  => __('Image', 'eac-components'),
					'type'	  => Controls_Manager::MEDIA,
					'dynamic' => ['active' => true],
					'default' => [
						'url' => Utils::get_placeholder_image_src(),
					],
					'condition' => ['mb_origin_trigger' => 'image'],
				]
			);
			
			$this->add_control('mb_image_dimension',
				[
					'label'   => __('Dimension', 'eac-components'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'medium_large',
					'options'       => [
						'thumbnail'		=> __('Miniature', 'eac-components'),
						'medium'		=> __('Moyenne', 'eac-components'),
						'medium_large'	=> __('Moyenne-large', 'eac-components'),
						'large'			=> __('Large', 'eac-components'),
						'full'			=> __('Originale', 'eac-components'),
					],
					'condition' => ['mb_origin_trigger' => 'image'],
				]
			);
			
			$this->add_control('mb_caption_source',
				[
					'label' => __('Légende', 'eac-components'),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'none' => __('Aucune', 'eac-components'),
						'attachment' => __('Attachement', 'eac-components'),
						'custom' => __('Légende personnalisée', 'eac-components'),
					],
					'default' => 'none',
					'condition' => ['mb_origin_trigger' => 'image'],
				]
			);

			$this->add_control('mb_caption_texte',
				[
					'label' => __('Légende personnalisée', 'eac-components'),
					'type' => Controls_Manager::TEXT,
					'dynamic' => ['active' => true,],
					'default' => '',
					'placeholder' => __('Votre légende personnalisée', 'eac-components'),
					'condition' => ['mb_origin_trigger' => 'image', 'mb_caption_source' => 'custom',],
					'label_block' => true,
				]
			);

			$this->add_control('mb_align_image',
				[
					'label' => __('Alignement', 'eac-components'),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'left' => [
							'title' => __('Gauche', 'eac-components'),
							'icon' => 'eicon-h-align-left',
						],
						'center' => [
							'title' => __('Centre', 'eac-components'),
							'icon' => 'eicon-h-align-center',
						],
						'right' => [
							'title' => __('Droite', 'eac-components'),
							'icon' => 'eicon-h-align-right',
						],
					],
					'default'		=> 'center',
					'selectors'     => ['{{WRAPPER}} .mb-modalbox__wrapper' => 'text-align: {{VALUE}};',],
					'condition'		=> ['mb_origin_trigger!' => 'pageloaded',],
				]
			);
			
			$this->add_control('mb_display_texte',
				[
					'label'         => __('Texte', 'eac-components'),
					'type'          => Controls_Manager::TEXT,
					'dynamic'       => ['active' => true],
					'label_block'   => true,
					'default'       => __('EAC Boîte modale', 'eac-components'),
					'condition'     => ['mb_origin_trigger' => 'text'],
				]
			);

			$this->add_control('mb_popup_delay',
				[
					'label'         => __("Délai d'affichage (Sec)", 'eac-components'),
					'type'          => Controls_Manager::NUMBER,
					'description'   => __('Quand le popup doit-il apparaître ? (En secondes)', 'eac-components'),
					'default'       => 5,
					'label_block'   => true,
					'condition' => ['mb_origin_trigger' => 'pageloaded'],
				]
			);
			
			$this->add_control('mb_popup_activated',
				[
					'label' => __("Actif dans l'éditeur", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'description' => __('Désactiver cette option avant de quitter la page', 'eac-components'),
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'condition' => ['mb_origin_trigger' => 'pageloaded'],
				]
			);
			
		$this->end_controls_section();
		
		/**
		 * Generale Style Section
		 */
		$this->start_controls_section('mb_modal_box_style',
			[
				'label'		=> __('Boîte modale', 'eac-components'),
				'tab'		 => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('mb_modal_box_width',
				[
					'label' => __('Largeur', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['unit' => 'px', 'size' => 640],
					'range' => ['px' => ['min' => 50, 'max' => 1000, 'step' => 10]],
					'label_block' => true,
					'selectors' => ['#modalbox-hidden-{{ID}}.fancybox-content, .modalbox-visible-{{ID}} .fancybox-content' => 'max-width: {{SIZE}}{{UNIT}}; width: 100%;'],
				]
			);
			
			$this->add_control('mb_modal_box_height',
				[
					'label' => __('Hauteur', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['unit' => 'px', 'size' => 360,],
					'range' => ['px' => ['min' => 10, 'max' => 1000, 'step' => 10]],
					'label_block' => true,
					'selectors' => ['.modalbox-visible-{{ID}} .fancybox-content' => 'max-height: {{SIZE}}{{UNIT}}; height: 100%;'],
					'condition' => ['mb_type_content' => ['links', 'html']],
				]
			);
			
			$this->start_controls_tabs('mb_modal_box_style_tabs');
				
				$this->start_controls_tab('mb_modal_box_style_effet',
					[
						'label'         => __('Effets', 'eac-components'),
					]
				);
			
					$this->add_control('mb_modal_box_effect',
						[
							'label'			=> __("Effet d'entrée", 'eac-components'),
							'type'			=> Controls_Manager::SELECT,
							'default'		=> 'zoom-in-out',
							'options'		=> [
								'zoom-in-out'			=> __('Défaut', 'eac-components'),
								'fade'					=> __('Fondu', 'eac-components'),
								'slide-in-out-top'		=> __('Vers le bas', 'eac-components'),
								'slide-in-out-bottom'	=> __('Vers le haut', 'eac-components'),
								'slide-in-out-right'	=> __('Vers la gauche', 'eac-components'),
								'slide-in-out-left'		=> __('Vers la droite', 'eac-components'),
								'tube'					=> __('Tube', 'eac-components'),
							],
							'label_block'	=>	true,
						]
					);
					
					$this->add_control('mb_modal_box_position',
						[
							'label'			=> __('Position', 'eac-components'),
							'type'			=> Controls_Manager::SELECT,
							'default'		=> 'default',
							'options'		=> [
								'default'		=> __('Défaut', 'eac-components'),
								'topleft'		=> __('Haut gauche', 'eac-components'),
								'topright'		=> __('Haut droite', 'eac-components'),
								'bottomleft'	=> __('Bas gauche', 'eac-components'),
								'bottomright'	=> __('Bas droite', 'eac-components'),
							],
							'label_block'	=>	true,
						]
					);
			
				$this->end_controls_tab();
			
				$this->start_controls_tab('mb_modal_box_style_background',
					[
						'label'         => __('Arrière-plan', 'eac-components'),
						'condition' => ['mb_type_content!' => ['links','html']],
					]
				);
				
					$this->add_group_control(
						Group_Control_Background::get_type(),
						[
							'name' => 'mb_modal_box_bg',
							'types' => ['classic', 'gradient'],
							'fields_options' => [
								'size' => ['default' => 'cover'],
								'position' => ['default' => 'center center'],
								'repeat' => ['default' => 'no-repeat'],
							],
							'selector' => '{{WRAPPER}} .mb-modalbox__hidden-content-body-bg',
							'condition' => ['mb_type_content!' => 'links'],
						]
					);
					
					$this->add_control('mb_modal_box_blend',
						[
							'label'			=> __("Mode de fusion", 'eac-components'),
							'description'	=> __("Vous avez sélectionné une couleur et une image", 'eac-components'),
							'type'			=> Controls_Manager::SELECT,
							'default'		=> 'normal',
							'options'		=> [
								'normal'	=> 'Normal',
								'screen'	=> 'Screen',
								'overlay'	=> 'Overlay',
								'darken'	=> 'Darken',
								'lighten'	=> 'Lighten',
								'color-dodge'	=> 'Color-dodge',
								'color-burn'	=> 'Color-burn',
								'hard-light'	=> 'Hard-light',
								'soft-light'	=> 'Soft-light',
								'difference'	=> 'Difference',
								'exclusion'		=> 'Exclusion',
								'hue'			=> 'Hue',
								'saturation'	=> 'Saturation',
								'color'			=> 'Color',
								'luminosity'	=> 'Luminosity',
							],
							'label_block'	=>	true,
							'selectors' => ['{{WRAPPER}} .mb-modalbox__hidden-content-body-bg' => 'background-blend-mode: {{VALUE}};'],
							'separator' => 'before',
							'condition' => ['mb_modal_box_bg_background' => 'classic', 'mb_type_content!' => 'links'],
						]
					);
					
					$this->add_control('mb_modal_box_bg_opacity',
						[
							'label' => __("Opacitée", 'eac-components'),
							'type' => Controls_Manager::SLIDER,
							'default' => ['size' => 0.2],
							'range' => ['px' => ['max' => 1, 'min' => 0.1, 'step' => 0.1]],
							'selectors' => ['{{WRAPPER}} .mb-modalbox__hidden-content-body-bg' => 'opacity: {{SIZE}};'],
							'condition' => ['mb_modal_box_bg_background' => 'classic', 'mb_type_content!' => 'links'],
						]
					);
				
				$this->end_controls_tab();
				
			$this->end_controls_tabs();
			
			$this->add_control('mb_modal_box_close_color',
				[
					'label' => __('Couleur du bouton de fermeture', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => ['#modalbox-hidden-{{ID}}.fancybox-content button.fancybox-close-small,
									.modalbox-visible-{{ID}} .fancybox-content button.fancybox-close-small' => 'color: {{VALUE}};',],
					'separator' => 'before',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('mb_header_style',
			[
               'label' => __("Entête boîte modale", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['mb_enable_header' => 'yes', 'mb_texte_header!' => '', 'mb_type_content!' => ['links','html']],
			]
		);
			
			$this->add_control('mb_header_color',
				[
					'label' => __('Couleur du titre', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#000',
					'selectors' => ['{{WRAPPER}} .mb-modalbox__hidden-content-title' => 'color: {{VALUE}};',],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'mb_header_typography',
					'label' => __('Typographie du titre', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .mb-modalbox__hidden-content-title h3',
				]
			);
			
			$this->add_control('mb_header_padding',
				[
					'label'         => __('Marges internes', 'eac-components'),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => ['px', 'em'],
					'default'       => [
						'unit'  => 'px',
						'top'   => 7,
						'right' => 0,
						'bottom' => 5,
						'left'  => 0,
					],
					'selectors'     => [
						'{{WRAPPER}} .mb-modalbox__hidden-content-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			
			$this->add_control('mb_header_background',
				[
					'label'         => __('Couleur du fond', 'eac-components'),
					'type'          => Controls_Manager::COLOR,
					'selectors'     => ['{{WRAPPER}} .mb-modalbox__hidden-content-title'  => 'background-color: {{VALUE}};',],
				]
			);

			$this->add_group_control(
			Group_Control_Border::get_type(),
				[
					'name'          => 'mb_header_border',
					'selector'      => '{{WRAPPER}} .mb-modalbox__hidden-content-title',
					'separator' => 'before',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('mb_texte_content_style',
			[
               'label' => __("Contenu boîte modale", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['mb_type_content' => 'texte'],
			]
		);
			
			$this->add_control('mb_texte_content_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#919CA7',
					'selectors' => ['	{{WRAPPER}} .mb-modalbox__hidden-content-body div,
										{{WRAPPER}} .mb-modalbox__hidden-content-body a i' => 'color: {{VALUE}};',],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'mb_texte_content_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_1,
					'selector' => '	{{WRAPPER}} .mb-modalbox__hidden-content-body div,
									{{WRAPPER}} .mb-modalbox__hidden-content-body a i',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('mb_button_style',
			[
               'label' => __("Bouton déclencheur", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['mb_origin_trigger' => 'button'],
			]
		);
			
			$this->add_control('mb_button_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#FFF',
					'selectors' => ['{{WRAPPER}} .mb-modalbox__wrapper-btn' => 'color: {{VALUE}} !important;',],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'mb_button_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .mb-modalbox__wrapper-btn',
				]
			);
			
			$this->add_control('mb_button_background',
				[
					'label'         => __('Couleur du fond', 'eac-components'),
					'type'          => Controls_Manager::COLOR,
					'selectors'     => ['{{WRAPPER}} .mb-modalbox__wrapper-btn'  => 'background-color: {{VALUE}};',],
				]
			);
			
			$this->add_group_control(
    			Group_Control_Box_Shadow::get_type(),
    			[
    				'name' => 'mb_button_shadow',
    				'label' => __('Ombre', 'eac-components'),
    				'selector' => '{{WRAPPER}} .mb-modalbox__wrapper-btn',
    			]
    		);
			
			$this->add_group_control(
			Group_Control_Border::get_type(),
				[
					'name' => 'mb_button_border',
					'selector' => '{{WRAPPER}} .mb-modalbox__wrapper-btn',
					'separator' => 'before',
				]
			);
			
			$this->add_control('mb_button_radius',
				[
					'label' => __('Rayon de la bordure', 'eac-components'),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'allowed_dimensions' => ['top', 'right', 'bottom', 'left'],
					'default' => ['top' => 8, 'right' => 8, 'bottom' => 8, 'left' => 8, 'unit' => 'px', 'isLinked' => true],
					'selectors' => [
						'{{WRAPPER}} .mb-modalbox__wrapper-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'separator' => 'before',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('mb_image_style',
			[
               'label' => __("Image déclencheur", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['mb_origin_trigger' => 'image'],
			]
		);
			
			$this->start_controls_tabs('mb_image_style_normal');
				
				$this->start_controls_tab('mb_image_tab_style_normal',
					[
						'label'         => __('Normal', 'eac-components'),
					]
				);
				
					
					$this->add_control('mb_image_padding',
						[
							'label'         => __('Marges internes', 'eac-components'),
							'type'          => Controls_Manager::DIMENSIONS,
							'size_units'    => ['px', 'em'],
							'default'       => [
								'unit'  => 'px',
								'top'   => 5,
								'right' => 5,
								'bottom' => 5,
								'left'  => 5,
							],
							'selectors'     => [
								'{{WRAPPER}} .mb-modalbox__wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
					Group_Control_Border::get_type(),
						[
							'name' => 'mb_image_border',
							'selector' => '{{WRAPPER}} .mb-modalbox__wrapper-img',
							'separator' => 'before',
						]
					);
					
					$this->add_control('mb_image_radius',
						[
							'label' => __('Rayon de la bordure', 'eac-components'),
							'type' => Controls_Manager::DIMENSIONS,
							'size_units' => ['px', '%'],
							'allowed_dimensions' => ['top', 'right', 'bottom', 'left'],
							'selectors' => [
								'{{WRAPPER}} .mb-modalbox__wrapper-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
							'separator' => 'before',
						]
					);
					
					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'mb_image_shadow',
							'label' => __('Ombre', 'eac-components'),
							'selector' => '{{WRAPPER}} .mb-modalbox__wrapper-img',
							'separator' => 'before',
						]
					);
				
				$this->end_controls_tab();
			
				$this->start_controls_tab('mb_image_tab_style_hover',
					[
						'label'         => __('Au Survol', 'eac-components'),
					]
				);
				
					$this->add_control('mb_image_opacity_hover',
						[
							'label' => __('Opacitée', 'eac-components'),
							'type' => Controls_Manager::SLIDER,
							'default' => ['size' => 0.2],
							'range' => ['px' => ['max' => 1, 'min' => 0.1, 'step' => 0.1]],
							'selectors' => ['{{WRAPPER}} .mb-modalbox__wrapper-img:hover' => 'opacity: {{SIZE}};'],
						]
					);

					$this->add_group_control(
					Group_Control_Css_Filter::get_type(),
						[
							'name' => 'mb_image_css_filters_hover',
							'selector' => '{{WRAPPER}} .mb-modalbox__wrapper-img:hover',
						]
					);
					
					$this->add_control('mb_image_hover_animation',
						[
							'label' => __('Animation', 'eac-components'),
							'type' => Controls_Manager::HOVER_ANIMATION,
						]
					);
				
				$this->end_controls_tab();
				
			$this->end_controls_tabs();
			
		$this->end_controls_section();
		
		$this->start_controls_section('mb_texte_style',
			[
               'label' => __("Texte déclencheur", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['mb_origin_trigger' => 'text'],
			]
		);
			
			$this->add_control('mb_texte_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#000',
					'selectors' => ['{{WRAPPER}} .mb-modalbox__wrapper-text span' => 'color: {{VALUE}} !important;',],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'mb_texte_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .mb-modalbox__wrapper-text span',
				]
			);
			
			$this->add_control('mb_texte_background',
				[
					'label'         => __('Couleur du fond', 'eac-components'),
					'type'          => Controls_Manager::COLOR,
					'selectors'     => ['{{WRAPPER}} .mb-modalbox__wrapper-text'  => 'background-color: {{VALUE}};',],
				]
			);
		
		$this->end_controls_section();
		
		$this->start_controls_section('mb_legende_style',
			[
               'label' => __("Légende de l'image", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['mb_origin_trigger' => 'image', 'mb_caption_source' => 'custom']
			]
		);
		
			$this->add_control('mb_legende_margin',
				[
					'label' => __('Espacement', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['em', 'px'],
					'default' => ['size' => 1, 'unit' => 'em'],
					'range' => [
						'em' => ['min' => 0, 'max' => 5, 'step' => 0.1],
						'px' => ['min' => 0, 'max' => 100, 'step' => 5],
					],
					'selectors' => ['{{WRAPPER}} .mb-modalbox__wrapper figure figcaption' => 'padding-top: {{SIZE}}{{UNIT}};'],
				]
			);
			
			$this->add_control('mb_legende_color',
				[
					'label' => __("Couleur", 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'separator' => 'none',
					'selectors' => ['{{WRAPPER}} .mb-modalbox__wrapper figure figcaption' => 'color: {{VALUE}};'],
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_1,
					],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'mb_legende_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .mb-modalbox__wrapper figure figcaption',
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
		
	?>
		<div class="eac-modal-box">
			<?php
			$this->render_modal();
			?>
		</div>
	<?php
	}
	
	protected function render_modal() {
		$settings = $this->get_settings_for_display();
		/*highlight_string("<?php\n\$settings =\n" . var_export($settings, true) . ";\n?>");*/
		$trigger = $settings['mb_origin_trigger'];		// Button, Text ou Openload
		$content = $settings['mb_type_content'];
		$link_url = !empty($settings['mb_url_content']['url']) ? esc_url($settings['mb_url_content']['url']) : false;
		$short_code = $settings['mb_shortcode_content'];
		$icon_button = false;
		$type_inline = false;
		
		// Quelques tests
		if((('links' === $content || 'html' === $content) && !$link_url) || ('formulaire' === $content && empty($short_code))) {
			return;
		}
		
		/**
		 * ID principal du document voir "data-elementor-id" class de la div section
		 * peut être différent de l'ID du post courant get_the_id() de WP
		 * Si le post a été créé dans un template, il faut conserver ID du template
		 * pour que le CSS défini soit bien appliqué au widget
		 */
		$main_id = \Elementor\Plugin::$instance->documents->get_current()->get_main_id();
		
		// Unique ID du widget
		$id = $this->get_id();
		
		// Le déclencheur est un bouton
		if('button' === $trigger) {
			if($settings['mb_icon_activated'] === 'yes' && !empty($settings['mb_display_icon_button'])) {
				$icon_button = true;
			}
			$this->add_render_attribute('trigger', 'type', 'button');
			$this->add_render_attribute('trigger', 'class', ['mb-modalbox__wrapper-trigger mb-modalbox__wrapper-btn', 'mb-modalbox__btn-' . $settings['mb_display_size_button']]);
		
		// Le déclencheur est une image
		} else if('image' === $trigger) {
			/**
			 * @since 1.6.5 Suppression de la valeur de l'attribut ALT par défaut
			 */
			$image_alt = '';
			$imgClass = 'mb-modalbox__wrapper-trigger mb-modalbox__wrapper-img';
			if('' !== $settings['mb_image_hover_animation']) {
				$imgClass = $imgClass . ' elementor-animation-' . $settings['mb_image_hover_animation'];
			}
			$this->add_render_attribute('trigger', 'class', $imgClass);
			if(!empty($settings['mb_display_image']['url'])) { // @since 1.7.61 Check si une URL existe
				$this->add_render_attribute('trigger', 'src', esc_url($settings['mb_display_image']['url']));
			}
			
			// Image vient de la lib des médias. ID existe
			if(!empty($settings['mb_display_image']['id'])) {
				$image = wp_get_attachment_image_src($settings['mb_display_image']['id'], $settings['mb_image_dimension']);
				$this->add_render_attribute('trigger', 'width', $image[1]);
				$this->add_render_attribute('trigger', 'height', $image[2]);
				$image_alt = Control_Media::get_image_alt($settings['mb_display_image']); // 'get_image_alt' renvoie toujours une chaine par défaut
			}
			$this->add_render_attribute('trigger', 'alt', $image_alt); // Image externe
		
		// Le déclencheur est du texte
		} else if('text' === $trigger) {
			$this->add_render_attribute('trigger', 'class', 'mb-modalbox__wrapper-trigger mb-modalbox__wrapper-text');
		}
		
		// Le wrapper global du composant
		$this->add_render_attribute('mb_wrapper', 'class', 'mb-modalbox__wrapper');
		$this->add_render_attribute('mb_wrapper', 'id', $id);
		$this->add_render_attribute('mb_wrapper', 'data-settings', $this->get_settings_json($id));
		?>
		
		<div <?php echo $this->get_render_attribute_string('mb_wrapper') ?>>
			<?php
			$header = $settings['mb_enable_header'] === 'yes' && !empty($settings['mb_texte_header']) ? htmlspecialchars($settings['mb_texte_header'], ENT_QUOTES) : '';
			$caption = '';
			$has_caption = !empty($settings['mb_caption_source']) && 'none' !== $settings['mb_caption_source'] ? true : false;
			if($has_caption) {
				if('attachment' === $settings['mb_caption_source'] && !empty($settings['mb_display_image']['id'])) {
					$caption = wp_get_attachment_caption($settings['mb_display_image']['id']);
				} else {
					$caption = !empty($settings['mb_caption_texte']) ? esc_html($settings['mb_caption_texte']) : '';
				}
			}
			
			// @since 1.7.0 Affichage du contenu HTML dans une iframe
			if('html' === $content) : // Html ?>
				<a data-fancybox data-options='{"type":"iframe", "caption":"<?php echo $header; ?>", "slideClass":"modalbox-visible-<?php echo $id; ?>", "src":"<?php echo $link_url; ?>"}' href="javascript:;">
			<?php elseif('links' === $content) : // Vidéo, Carte ?>
				<a data-fancybox data-options='{"caption":"<?php echo $header; ?>", "slideClass":"modalbox-visible-<?php echo $id; ?>"}' href="<?php echo $link_url; ?>">
			<?php else : // Texte, Formulaire ou Template
				$type_inline = true; ?>
				<a data-fancybox data-options='{"type":"inline", "src":"#modalbox-hidden-<?php echo $id; ?>"}' href="javascript:;">
			<?php endif; ?>
					<?php if('button' === $trigger) : ?>
						<button <?php echo $this->get_render_attribute_string('trigger'); ?>>
						<?php
							if($icon_button && $settings['mb_position_icon_button'] === 'before') {
								Icons_Manager::render_icon($settings['mb_display_icon_button'], ['aria-hidden' => 'true']);
							}
							echo $settings['mb_display_text_button'];
							if($icon_button && $settings['mb_position_icon_button'] === 'after') {
								Icons_Manager::render_icon($settings['mb_display_icon_button'], ['aria-hidden' => 'true']);
							}
						?>
						</button>
					<?php elseif('image' === $trigger) : ?>
						<?php if(!empty($caption)) : ?><figure><?php endif; ?>
						<img <?php echo $this->get_render_attribute_string('trigger'); ?>>
							<?php if(!empty($caption)) : ?>
								<figcaption><?php echo $caption; ?></figcaption>
							<?php endif; ?>
						<?php if(!empty($caption)) : ?></figure><?php endif; ?>
					<?php elseif('text' === $trigger) : ?>
						<div <?php echo $this->get_render_attribute_string('trigger'); ?>>
							<span><?php echo $settings['mb_display_texte']; ?></span>
						</div>
					<?php else:													// déclencheur automatique 'On page load'
						$type_inline = true;
					endif; ?>
				</a>
			
			<!-- Affichage en ligne pour les contenus 'automatique, texte, template, formulaire' -->
			<?php if($type_inline) { ?>
				<div id="modalbox-hidden-<?php echo $id; ?>" class="mb-modalbox__hidden-content-wrapper elementor-<?php echo $main_id; ?>">
					<div class="elementor-element elementor-element-<?php echo $id; ?>">
						<div class="mb-modalbox__hidden-content-body-bg"></div>
						<div fancybox-title class="mb-modalbox__hidden-content-title"><h3><?php echo htmlspecialchars_decode($header, ENT_QUOTES); ?></h3></div>
						<div fancybox-body class="mb-modalbox__hidden-content-body">
							<?php
							if('texte' === $content) { ?>
								<div><?php echo $settings['mb_texte_content']; ?></div>
							<?php
								//global $shortcode_tags;
								//echo '<pre>'; print_r($shortcode_tags); echo '</pre>';
							} else if('tmpl_sec' === $content) { // ID du template section
								$tmplsec = $settings['mb_tmpl_sec_content'] !== '' ? $settings['mb_tmpl_sec_content'] : 0;
								if($tmplsec != 0) {
									// Filtre wpml
									$tmplsec = apply_filters('wpml_object_id', $tmplsec, 'elementor_library', true);
									echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($tmplsec, true);
								} else {
									echo __('Rien à afficher', 'eac-components');
								}
							} else if('tmpl_page' === $content) { // ID du template Page
								$tmplpage = $settings['mb_tmpl_page_content'] !== '' ? $settings['mb_tmpl_page_content'] : 0;
								if($tmplpage != 0) {
									// Filtre wpml
									$tmplpage = apply_filters('wpml_object_id', $tmplpage, 'elementor_library', true);
									echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($tmplpage, true);
								} else {
									echo __('Rien à afficher', 'eac-components');
								}
							} else if('formulaire' === $content) { // Exécute un shortcode
								echo do_shortcode(shortcode_unautop($short_code));
							} else {
								echo __('Ouverture automatique ne supporte pas les formats HTML, Vidéo ou Carte', 'eac-components');
							}
							?>
						</div>
					</div>
				</div>
			<?php }	?>
		</div>
	<?php
	}
	
	/*
	* get_settings_json
	*
	* Retrieve fields values to pass at the widget container
	* Convert on JSON format
	* Read by 'eac-components.js' file when the component is loaded on the frontend
	* Modification de la règles 'data_filtre'
	*
	* @uses		 json_encode()
	*
	* @return	 JSON oject
	*
	* @access	 protected
	* @since	 0.0.9
	*/
	protected function get_settings_json($dataid) {
		$module_settings = $this->get_settings_for_display();
		
		$settings = array(
			"data_id" => $dataid,
			"data_declanche" => $module_settings['mb_origin_trigger'],
			"data_delay" => $module_settings['mb_popup_delay'],
			//"data_editor" => \Elementor\Plugin::$instance->editor->is_edit_mode(),
			"data_active" => $module_settings['mb_popup_activated'] === 'yes' ? true : false,
			"data_effet" => $module_settings['mb_modal_box_effect'],
			"data_position" => $module_settings['mb_modal_box_position'],
			"data_modal" => true, //$module_settings['mb_enable_modal'] === 'yes' ? true : false,
		);
		
		$settings = json_encode($settings);
		return $settings;
	}
}