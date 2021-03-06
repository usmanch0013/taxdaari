<?php

/*========================================================================================================
* Class: Off_Canvas_Widget
* Name: Barre latérale
* Slug: eac-addon-off-canvas
*
* Description: Construit et affiche une barre létérale avec un contenu défini, à une position déterminée
* ouverte par un bouton ou un texte
*
* 
* @since 1.8.5
* @since 1.8.7	Application des breakpoints
*========================================================================================================*/

namespace EACCustomWidgets\Widgets;

use EACCustomWidgets\Includes\Eac_Tools_Util;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;

if(! defined('ABSPATH')) exit; // Exit if accessed directly

class Off_Canvas_Widget extends Widget_Base {
	
	/*
	* Retrieve widget name.
	*
	* @access public
	*
	* @return widget name.
	*/
	public function get_name() {
		return 'eac-addon-off-canvas';
	}

	/*
	* Retrieve widget title.
	*
	* @access public
	*
	* @return widget title.
	*/
	public function get_title() {
		return __("Barre latérale", 'eac-components');
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
		return 'eicon-sidebar';
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
	 * 
	 * Les styles sont chargés dans le footer
	 *
	 * @return CSS list.
	 */
	public function get_style_depends() {
		return ['eac-off-canvas'];
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
		return ['off-canvas', 'Menu'];
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
        return 'https://elementor-addon-components.com/create-off-canvas-menu-with-elementor/';
    }
	
	/*
	* Register widget controls.
	*
	* Adds different input fields to allow the user to change and customize the widget settings.
	*
	* @access protected
	*/
	protected function register_controls() {
		
		$this->start_controls_section('oc_settings',
            [
                'label' => __('Réglages', 'eac-components'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
			
			$this->add_control('oc_content_position',
						[
							'label' => __('Position', 'eac-components'),
							'type' => Controls_Manager::CHOOSE,
							'default' => 'left',
							'options' => [
								'left' => [
									'title' => __('Gauche', 'eac-components'),
									'icon' => 'eicon-h-align-left',
								],
								'top' => [
									'title' => __('Haut', 'eac-components'),
									'icon' => 'eicon-v-align-top',
								],
								'bottom' => [
									'title' => __('Bas', 'eac-components'),
									'icon' => 'eicon-v-align-bottom',
								],
								'right' => [
									'title' => __('Droit', 'eac-components'),
									'icon' => 'eicon-h-align-right',
								],
							],
						]
					);
					
					$this->add_control('oc_content_overlay',
						[
							'label' => __("Activer l'overlay", 'eac-components'),
							'type' => Controls_Manager::SWITCHER,
							'label_on' => __('oui', 'eac-components'),
							'label_off' => __('non', 'eac-components'),
							'return_value' => 'yes',
							'default' => 'yes',
						]
					);
			
			$this->start_controls_tabs('oc_content_settings');
				
				$this->start_controls_tab('oc_content',
					[
						'label'		=> __('Contenu', 'eac-components'),
					]
				);
					
					$this->add_control('oc_content_title',
						[
							'label'   => __("Titre", 'eac-components'),
							'type'    => Controls_Manager::TEXT,
							'dynamic' => ['active' => true],
							'default' => __("Texte de l'entête", 'eac-components'),
							'placeholder' => __("Texte de l'entête", 'eac-components'),
							//'label_block'	=>	true,
						]
					);
					
					$this->add_control('oc_content_type',
						[
							'label'			=> __('Type de contenu', 'eac-components'),
							'type'			=> Controls_Manager::SELECT,
							'description'	=> __('Type de contenu à afficher', 'eac-components'),
							'default'		=> 'texte',
							'options'		=> [
								'form'		=> __('Formulaire', 'eac-components'),
								'menu'		=> __('Menu', 'eac-components'),
								'texte'		=> __('Texte personnalisé', 'eac-components'),
								'tmpl_sec'	=> __('Elementor modèle de section', 'eac-components'),
								'tmpl_page'	=> __('Elementor modèle de page', 'eac-components'),
								'widget'	=> __('Widget', 'eac-components'),
							],
							//'label_block'	=>	true,
							'separator'     => 'before',
						]
					);
					
					$this->add_control('oc_content_shortcode',
						[
							'label' => __('Entrer le shortcode du formulaire', 'eac-components'),
							'type' => Controls_Manager::TEXTAREA,
							'placeholder' => '[contact-form-7 id="XXXX"]',
							'default' => '',
							'condition' => ['oc_content_type' => 'form'],
						]
					);
					
					$this->add_control('oc_content_text',
						[
							'label' => __('Description', 'eac-components'),
							'type' => Controls_Manager::WYSIWYG,
							'default' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.",
							'condition' => ['oc_content_type' => 'texte'],
						]
					);
					
					$this->add_control('oc_content_section',
						[
							'label'   => __("Elementor modèle de section", 'eac-components'),
							'type'    => Controls_Manager::SELECT,
							'options'	=> Eac_Tools_Util::get_elementor_templates('section'),
							'condition' => ['oc_content_type' => 'tmpl_sec'],
							'label_block' => true,
						]
					);
					
					$this->add_control('oc_content_page',
						[
							'label'   => __("Elementor modèle de page", 'eac-components'),
							'type'    => Controls_Manager::SELECT,
							'options'	=> Eac_Tools_Util::get_elementor_templates('page'),
							'condition' => ['oc_content_type' => 'tmpl_page'],
							'label_block' => true,
						]
					);
					
					$this->add_control('oc_content_menu',
						[
							'label'        => __('Menu', 'eac-components'),
							'type'         => Controls_Manager::SELECT,
							'options'      => Eac_Tools_Util::get_menus_list(),
							//'default'      => array_keys($menus)[0],
							//'save_default' => true,
							'description'  => sprintf(__('Aller à <a href="%s" target="_blank">Apparence/Menus</a> pour gérer vos menus.', 'eac-components' ), admin_url( 'nav-menus.php')),
							'condition' => ['oc_content_type' => 'menu'],
						]
					);
					
					$this->add_control('oc_content_menu_level',
						[
							'label'			=> __('Nombre de niveaux', 'eac-components'),
							'description'	=> __('0 = Tous', 'eac-components'),
							'default'		=> 0,
							'type'			=> Controls_Manager::TEXT,
							'condition'		=> ['oc_content_type' => 'menu'],
						]
					);
					
					$this->add_control('oc_content_widget',
						[
							'label'        => __('Widgets', 'eac-components'),
							'type'         => Controls_Manager::SELECT2,
							'options'      => Eac_Tools_Util::get_widgets_list(),
							'multiple'     => true,
							'label_block'	=>	true,
							'condition' => ['oc_content_type' => 'widget'],
						]
					);
					
				$this->end_controls_tab();
			
				$this->start_controls_tab('oc_trigger',
					[
						'label'		=> __('Déclencheur', 'eac-components'),
					]
				);
					
					$this->add_control('oc_trigger_type',
						[
							'label'			=> __('Déclencheur', 'eac-components'),
							'type'			=> Controls_Manager::SELECT,
							'description'	=> __('Sélectionner le déclencheur', 'eac-components'),
							'options'		=> [
								'button'		=> __('Bouton', 'eac-components'),
								'text'			=> __('Texte', 'eac-components'),
							],
							'default'		=> 'button',
						]
					);
					
					$this->add_control('oc_display_text_button',
						[
							'label'			=> __('Libellé du bouton', 'eac-components'),
							'default'		=> __('Ouvrir la barre latérale', 'eac-components'),
							'type'			=> Controls_Manager::TEXT,
							'dynamic'		=> ['active' => true],
							//'label_block'	=> true,
							'condition'		=> ['oc_trigger_type' => 'button'],
						]
					);
					
					$this->add_control('oc_display_size_button',
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
							'separator' => 'before',
							'condition'		=> ['oc_trigger_type' => 'button'],
						]
					);
					
					$this->add_control('oc_align_button',
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
							'selectors'     => ['{{WRAPPER}} .oc-offcanvas__wrapper' => 'text-align: {{VALUE}};'],
							'condition'		=> ['oc_trigger_type' => 'button'],
						]
					);
					
					$this->add_control('oc_icon_sticky',
						[
							'label' => __("Bouton collant", 'eac-components'),
							'type' => Controls_Manager::SWITCHER,
							'label_on' => __('oui', 'eac-components'),
							'label_off' => __('non', 'eac-components'),
							'return_value' => 'yes',
							'default' => '',
							'condition' => ['oc_trigger_type' => 'button', 'oc_content_position!' => ['top', 'bottom']],
						]
					);
					
					$this->add_control('oc_icon_activated',
						[
							'label' => __("Ajouter un pictogramme", 'eac-components'),
							'type' => Controls_Manager::SWITCHER,
							'label_on' => __('oui', 'eac-components'),
							'label_off' => __('non', 'eac-components'),
							'return_value' => 'yes',
							'default' => '',
							'condition' => ['oc_trigger_type' => 'button'],
						]
					);
					
					$this->add_control('oc_display_icon_button',
						[
							'label' => __("Pictogrammes", 'eac-components'),
							'type' => Controls_Manager::ICONS,
							'default' => ['value' => 'fas fa-angle-double-right', 'library' => 'fa-solid',],
							'skin' => 'inline',
							'exclude_inline_options' => ['svg'],
							'condition' => ['oc_trigger_type' => 'button', 'oc_icon_activated' => 'yes'],
							'separator' => 'before',
						]
					);
					
					$this->add_control('oc_position_icon_button',
						[
							'label'			=> __('Position', 'eac-components'),
							'type'			=> Controls_Manager::SELECT,
							'default'		=> 'before',
							'options'		=> [
								'before'	=> __('Avant', 'eac-components'),
								'after'	=> __('Après', 'eac-components'),
							],
							'condition'		=> ['oc_trigger_type' => 'button', 'oc_icon_activated' => 'yes'],
						]
					);
					
					$this->add_control('oc_marge_icon_button',
						[
							'label' => __('Marges', 'eac-components'),
							'type' => Controls_Manager::DIMENSIONS,
							'allowed_dimensions' => ['left', 'right'],
							'default' => ['left' => 0, 'right' => 0, 'unit' => 'px', 'isLinked' => false],
							'range' => ['px' => ['min' => 5, 'max' => 50, 'step' => 1]],
							'selectors' => ['{{WRAPPER}} .oc-offcanvas__wrapper-btn i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
							'condition'	=> ['oc_trigger_type' => 'button', 'oc_icon_activated' => 'yes'],
						]
					);
					
					$this->add_control('oc_display_text',
						[
							'label'			=> __('Texte', 'eac-components'),
							'default'		=> __('Ouvrir la barre latérale', 'eac-components'),
							'type'			=> Controls_Manager::TEXT,
							'dynamic'		=> ['active' => true],
							'label_block'	=> true,
							'condition'		=> ['oc_trigger_type' => 'text'],
						]
					);
					
					$this->add_control('oc_align_text',
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
							'selectors'     => ['{{WRAPPER}} .oc-offcanvas__wrapper' => 'text-align: {{VALUE}};',],
							'condition'		=> ['oc_trigger_type' => 'text'],
						]
					);
					
				$this->end_controls_tab();
				
			$this->end_controls_tabs();
			
		$this->end_controls_section();
		
		/**
		 * Generale Style Section
		 */
		$this->start_controls_section('oc_offcanvas_style',
			[
				'label'		=> 'Container',
				'tab'		 => Controls_Manager::TAB_STYLE,
			]
		);
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('oc_content_width',
				[
					'label' => __('Largeur (px)', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 350, 'unit' => 'px'],
					'range' => ['px' => ['min' => 100, 'max' => 1000, 'step' => 10]],
					'label_block' => true,
					'selectors' => ['{{WRAPPER}} .oc-offcanvas__canvas-left, {{WRAPPER}} .oc-offcanvas__canvas-right' => 'max-width: {{SIZE}}{{UNIT}}; width: 100%;',
					'{{WRAPPER}} .oc-offcanvas__canvas-bottom, {{WRAPPER}} .oc-offcanvas__canvas-top' => 'max-height: {{SIZE}}{{UNIT}}; height: 100%; width: 100%;'],
				]
			);
			
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'oc_content_bgcolor',
					'types' => ['classic', 'gradient'],
					'fields_options' => [
						'size' => ['default' => 'cover'],
						'position' => ['default' => 'center center'],
						'repeat' => ['default' => 'no-repeat'],
					],
					'separator' => 'before',
					'selector' => '{{WRAPPER}} .oc-offcanvas__wrapper-canvas',
				]
			);
					
			$this->add_control('oc_content_box_blend',
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
					'separator' => 'before',
					'selectors' => ['{{WRAPPER}} .oc-offcanvas__wrapper-canvas' => 'background-blend-mode: {{VALUE}};'],
					'condition' => ['oc_content_bgcolor_background' => 'classic'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('oc_header_style',
			[
               'label' => __("Entête du container", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('oc_header_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#000',
					'selectors' => ['{{WRAPPER}} .oc-offcanvas__canvas-title h2' => 'color: {{VALUE}};',],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'oc_header_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .oc-offcanvas__canvas-title h2',
				]
			);
			
			$this->add_control('oc_header_background',
				[
					'label'         => __('Couleur du fond', 'eac-components'),
					'type'          => Controls_Manager::COLOR,
					'selectors'     => ['{{WRAPPER}} .oc-offcanvas__canvas-title h2'  => 'background-color: {{VALUE}};',],
				]
			);

			$this->add_group_control(
			Group_Control_Border::get_type(),
				[
					'name'          => 'oc_header_border',
					'selector'      => '{{WRAPPER}} .oc-offcanvas__canvas-title h2',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('oc_content_menu_style',
			[
               'label' => __("Contenu menu", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['oc_content_type' => 'menu'],
			]
		);
			
			$this->add_control('oc_content_menu_color',
				[
					'label' => __('Couleur du texte', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#000',
					'selectors' => ['{{WRAPPER}} .oc-offcanvas__menu-wrapper ul li, {{WRAPPER}} .oc-offcanvas__menu-wrapper ul li a' => 'color: {{VALUE}};'],
				]
			);
			
			$this->add_control('oc_content_menu_color_hover',
				[
					'label' => __('Couleur du texte Hover', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#bab305',
					'selectors' => ['{{WRAPPER}} .oc-offcanvas__menu-wrapper ul li a:hover' => 'color: {{VALUE}};'],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'oc_content_menu_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_1,
					'fields_options' => [
						'font_size' => ['default' => ['unit' => 'em', 'size' => 0.85]],
					],
					'selector' => '{{WRAPPER}} .oc-offcanvas__menu-wrapper > ul',
				]
			);
			
			$this->add_control('oc_content_menu_bgcolor',
				[
					'label' => __('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'selectors' => ['{{WRAPPER}} .oc-offcanvas__menu-wrapper'  => 'background-color: {{VALUE}};'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('oc_content_text_style',
			[
               'label' => __("Contenu texte", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['oc_content_type' => 'texte'],
			]
		);
			
			$this->add_control('oc_content_text_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#000',
					'selectors' => ['{{WRAPPER}} .oc-offcanvas__canvas-content .oc-offcanvas__content-text' => 'color: {{VALUE}};'],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'oc_content_text_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .oc-offcanvas__canvas-content .oc-offcanvas__content-text',
				]
			);
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('oc_content_text_margin',
				[
					'label' => __('Position (%)', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['%'],
					'default' => ['size' => 10, 'unit' => '%'],
					'range' => ['%' => ['min' => 0, 'max' => 95, 'step' => 5]],
					'label_block' => true,
					'selectors' => ['{{WRAPPER}} .oc-offcanvas__canvas-content .oc-offcanvas__content-text' => 'margin-top: {{SIZE}}%;'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('oc_content_widget_style',
			[
               'label' => __("Contenu widget", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['oc_content_type' => 'widget'],
			]
		);
			
			$this->add_control('oc_content_widget_title_color',
				[
					'label' => __('Couleur du titre', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#000',
					'selectors' => ['{{WRAPPER}} .oc-offcanvas__canvas-content .widget .widgettitle,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget .widget-title,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget.widget_calendar caption' => 'color: {{VALUE}};'],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'oc_content_widget_title_typography',
					'label' => __('Typographie du titre', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_1,
					'fields_options' => [
						'font_size' => ['default' => ['unit' => 'em', 'size' => 1.1]],
					],
					'selector' => '{{WRAPPER}} .oc-offcanvas__canvas-content .widget .widgettitle,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget .widget-title,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget.widget_calendar caption',
				]
			);
			
			$this->add_control('oc_content_widget_title_bgcolor',
				[
					'label' => __('Couleur du fond du titre', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'default' => 'antiquewhite',
					'selectors' => ['{{WRAPPER}} .oc-offcanvas__canvas-content .widget .widgettitle,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget .widget-title'  => 'background-color: {{VALUE}};'],
				]
			);
			
			$this->add_control('oc_content_widget_text_color',
				[
					'label' => __('Couleur du texte', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#000',
					'separator' => 'before',
					'selectors' => ['{{WRAPPER}} .oc-offcanvas__canvas-content .widget ul li,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget ul li a,
					{{WRAPPER}} .oc-offcanvas__canvas-content aside.widget ul li,
					{{WRAPPER}} .oc-offcanvas__canvas-content aside.widget ul li a,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget.widget_calendar td,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget.widget_calendar th,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget .custom-html-widget,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget .tagcloud .tag-cloud-link' => 'color: {{VALUE}};'],
				]
			);
			
			$this->add_control('oc_content_widget_text_color_hover',
				[
					'label' => __('Couleur du lien au survol', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#bab305',
					'selectors' => ['{{WRAPPER}} .oc-offcanvas__canvas-content .widget ul li a:hover' => 'color: {{VALUE}};'],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'oc_content_widget_text_typography',
					'label' => __('Typographie du texte', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_1,
					'fields_options' => [
						'font_size' => ['default' => ['unit' => 'em', 'size' => 0.85]],
					],
					'selector' => '{{WRAPPER}} .oc-offcanvas__canvas-content .widget > ul,
					{{WRAPPER}} .oc-offcanvas__canvas-content aside.widget ul,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget.widget_calendar td,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget.widget_calendar th,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget .custom-html-widget',
				]
			);
			
			$this->add_control('oc_content_widget_bgcolor',
				[
					'label' => __('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'separator' => 'before',
					'selectors' => ['{{WRAPPER}} .oc-offcanvas__canvas-content .widget'  => 'background-color: {{VALUE}};'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('oc_button_style',
			[
               'label' => __("Bouton déclencheur", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['oc_trigger_type' => 'button'],
			]
		);
			
			$this->add_control('oc_button_position',
				[
					'label' => __('Position', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['%'],
					'default' => ['size' => 50, 'unit' => '%'],
					'range' => ['%' => ['min' => 5, 'max' => 95, 'step' => 1]],
					'label_block' => true,
					'selectors' => ['{{WRAPPER}} .oc-offcanvas__wrapper-btn.sticky-button-left' => 'top: {{SIZE}}%; transform: rotate(-90deg) translateX(-{{SIZE}}%);', 
					'{{WRAPPER}} .oc-offcanvas__wrapper-btn.sticky-button-right' => 'top: {{SIZE}}%; transform: rotate(90deg) translateX({{SIZE}}%);'],
					'condition' => ['oc_icon_sticky' => 'yes'],
				]
			);
			
			$this->add_control('oc_button_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#FFF',
					'selectors' => ['{{WRAPPER}} .oc-offcanvas__wrapper-btn' => 'color: {{VALUE}} !important;',],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'oc_button_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .oc-offcanvas__wrapper-btn',
				]
			);
			
			$this->add_control('oc_button_background',
				[
					'label' => __('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'default' => '#1569AE',
					'selectors' => ['{{WRAPPER}} .oc-offcanvas__wrapper-btn'  => 'background-color: {{VALUE}};',],
				]
			);
			
			$this->add_group_control(
    			Group_Control_Box_Shadow::get_type(),
    			[
    				'name' => 'oc_button_shadow',
    				'label' => __('Ombre', 'eac-components'),
    				'selector' => '{{WRAPPER}} .oc-offcanvas__wrapper-btn',
    			]
    		);
			
			$this->add_group_control(
			Group_Control_Border::get_type(),
				[
					'name' => 'oc_button_border',
					'selector' => '{{WRAPPER}} .oc-offcanvas__wrapper-btn',
					'separator' => 'before',
				]
			);
			
			$this->add_control('oc_button_radius',
				[
					'label' => __('Rayon de la bordure', 'eac-components'),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'allowed_dimensions' => ['top', 'right', 'bottom', 'left'],
					'default' => ['top' => 8, 'right' => 8, 'bottom' => 8, 'left' => 8, 'unit' => 'px', 'isLinked' => true],
					'selectors' => [
						'{{WRAPPER}} .oc-offcanvas__wrapper-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'separator' => 'before',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('oc_texte_style',
			[
               'label' => __("Texte déclencheur", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['oc_trigger_type' => 'text'],
			]
		);
			
			$this->add_control('oc_texte_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#000',
					'selectors' => ['{{WRAPPER}} .oc-offcanvas__wrapper-text span' => 'color: {{VALUE}} !important;',],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'oc_texte_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .oc-offcanvas__wrapper-text span',
				]
			);
			
			$this->add_control('oc_texte_background',
				[
					'label'         => __('Couleur du fond', 'eac-components'),
					'type'          => Controls_Manager::COLOR,
					'selectors'     => ['{{WRAPPER}} .oc-offcanvas__wrapper-text'  => 'background-color: {{VALUE}};',],
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
		<div class="eac-off-canvas">
			<?php
			$this->render_offcanvas();
			?>
		</div>
	<?php
	}
	
	protected function render_offcanvas() {
		$settings = $this->get_settings_for_display();
		
		$trigger = $settings['oc_trigger_type']; // Button, Text
		$has_overlay = $settings['oc_content_overlay'];
		$content = $settings['oc_content_type'];
		$short_code = $settings['oc_content_shortcode'];
		$menu = $settings['oc_content_menu'];
		$texte = $settings['oc_content_text'];
		$widget_classes = $settings['oc_content_widget'];
		$tmpl_sec = $settings['oc_content_section'];
		$tmpl_page = $settings['oc_content_page'];
		
		// Quelques tests
		if(('widget' === $content && empty($widget_classes)) || ('texte' === $content && empty($texte)) || ('menu' === $content && empty($menu)) || ('form' === $content && empty($short_code || ('tmpl_sec' === $content && empty($tmpl_sec)) || ('tmpl_page' === $content && empty($tmpl_page))))) {
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
		
		// Une icone avec le texte du bouton
		$icon_button = false;
		
		// Le bouton est collant
		$sticky_button = 'button' === $trigger && $settings['oc_icon_sticky'] === 'yes' ? 'sticky-button-' . $settings['oc_content_position'] : '';
		
		// Le déclencheur est un bouton
		if('button' === $trigger) {
			if($settings['oc_icon_activated'] === 'yes' && !empty($settings['oc_display_icon_button'])) {
				$icon_button = true;
			}
			$this->add_render_attribute('trigger', 'type', 'button');
			$this->add_render_attribute('trigger', 'class', ['oc-offcanvas__wrapper-trigger oc-offcanvas__wrapper-btn', 'oc-offcanvas__btn-' . $settings['oc_display_size_button'], $sticky_button]);
		} else if('text' === $trigger) {
			$this->add_render_attribute('trigger', 'class', 'oc-offcanvas__wrapper-trigger oc-offcanvas__wrapper-text');
		}
		
		// Le wrapper global du composant
		$this->add_render_attribute('oc_wrapper', 'class', 'oc-offcanvas__wrapper');
		$this->add_render_attribute('oc_wrapper', 'id', $id);
		$this->add_render_attribute('oc_wrapper', 'data-settings', $this->get_settings_json($id));
		?>
		
		<div <?php echo $this->get_render_attribute_string('oc_wrapper') ?>>
			<?php if('button' === $trigger) : ?>
				<button <?php echo $this->get_render_attribute_string('trigger'); ?>>
				<?php
					if($icon_button && $settings['oc_position_icon_button'] === 'before') {
						Icons_Manager::render_icon($settings['oc_display_icon_button'], ['aria-hidden' => 'true']);
					}
					echo $settings['oc_display_text_button'];
					if($icon_button && $settings['oc_position_icon_button'] === 'after') {
						Icons_Manager::render_icon($settings['oc_display_icon_button'], ['aria-hidden' => 'true']);
					}
				?>
				</button>
			<?php elseif('text' === $trigger) : ?>
				<div <?php echo $this->get_render_attribute_string('trigger'); ?>>
					<span><?php echo esc_html($settings['oc_display_text']); ?></span>
				</div>
			<?php endif; ?>
		</div>
		
		<?php if($has_overlay) : ?>
			<div class="oc-offcanvas__wrapper-overlay"></div>
		<?php  endif; ?>
		
		<div id="<?php echo $id; ?>" class="oc-offcanvas__wrapper-canvas oc-offcanvas__canvas-<?php echo $settings['oc_content_position']; ?> elementor-<?php echo $main_id; ?>">
			<div class="elementor-element elementor-element-<?php echo $id; ?>">
				<div class="oc-offcanvas__canvas-header">
					<div class="oc-offcanvas__canvas-close"><span>X</span></div>
					<div class="oc-offcanvas__canvas-title"><h2><?php echo htmlspecialchars_decode($settings['oc_content_title'], ENT_QUOTES); ?></h2></div>
				</div>
				<div class="oc-offcanvas__canvas-content">
					<?php
					if('texte' === $content) { ?>
						<div class="oc-offcanvas__content-text"><?php echo $texte; ?></div>
						
					<?php
					} else if('tmpl_sec' === $content) { // ID du template section
							// Filtre wpml
						$tmpl_sec = apply_filters('wpml_object_id', $tmpl_sec, 'elementor_library', true);
						echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($tmpl_sec, true);
						
					} else if('tmpl_page' === $content) { // ID du template Page
							// Filtre wpml
						$tmpl_page = apply_filters('wpml_object_id', $tmpl_page, 'elementor_library', true);
						echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($tmpl_page, true);
						
					} else if('form' === $content) { // Exécute un shortcode
						echo do_shortcode(shortcode_unautop($short_code));
						
					} else if('menu' === $content) { // Affiche un menu
						$args = array(
							'menu' => $menu,
							'container_class' => 'oc-offcanvas__menu-wrapper',
							'depth' => $settings['oc_content_menu_level'],
						);
						
						// Affiche le menu
						wp_nav_menu($args);
						
					} else { // Affiche les widgets
						ob_start();
						foreach($widget_classes as $widget_class) {
							$args = array('before_title' => '<h3 class="widgettitle">', 'after_title' => '</h3>');
							$instance = array();
							list($classname, $title) = array_pad(explode('::', $widget_class), 2, '');
							
							// Widgets standards
							if(empty($title)) {
								if($classname === 'WP_Widget_Calendar') { $instance = array('title' => __('Calendrier', 'eac-components')); }
								else if($classname === 'WP_Widget_Search') { $instance = array('title' => __('Rechercher', 'eac-components')); }
								else if($classname === 'WP_Widget_Tag_Cloud') { $instance = array('title' => __('Nuage de Tags', 'eac-components')); }
								else if($classname === 'WP_Widget_Recent_Posts') { $instance = array('title' => __('Articles récents', 'eac-components')); }
								else if($classname === 'WP_Widget_Recent_Comments') { $instance = array('title' => __('Derniers commentaires', 'eac-components')); }
								else if($classname === 'WP_Widget_RSS') { $instance = array('title' => __('Flux RSS', 'eac-components'), 'url' => get_bloginfo('rss2_url')); }
								else if($classname === 'WP_Widget_Pages') { $instance = array('title' => 'Pages'); }
								else if($classname === 'WP_Widget_Archives') { $instance = array('title' => 'Archives'); }
								else if($classname === 'WP_Widget_Meta') { $instance = array('title' => 'Meta'); }
								else if($classname === 'WP_Widget_Categories') { $instance = array('title' => __('Catégories', 'eac-components')); }
								
								// Affiche le widget
								the_widget($classname, $instance, $args);
							
							} else { // Sidebar
								dynamic_sidebar($classname);
							}
						}
						$output = ob_get_contents();
						ob_end_clean();
						echo $output;
					}
					?>
				</div>
			</div>
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
			"data_position" => $module_settings['oc_content_position'],
		);
		
		$settings = json_encode($settings);
		return $settings;
	}
	
	protected function content_template() {}
	
}