<?php

/*======================================================================================================
* Class: Image_Galerie_Widget
* Name: Galerie d'Images
* Slug: eac-addon-image-galerie
*
* Description: Image_Galerie_Widget affiche des images dans différents modes
* grille, mosaïque et justifiées
*
* @since 0.0.9
* @since 1.4.1  Forcer le chargement des images depuis le serveur
* @since 1.5.3  Modifie l'affectation de 'layoutType'
* @since 1.6.0	Activation de la propriété 'dynamic' des controls de l'image
*				Gestion des images avec des URLs externes par la balise dynamique du control MEDIA
*				Ajout du ratio Image pour le mode Grid
*				La visionneuse peut être activée avec tous les modes
* @since 1.6.5	Ajoute le control Attribut ALT pour les images externes
* @since 1.6.7	Check 'justify' layout type for the grid parameters
*				Ajour du mode Metro
*				La taille de la fonte de l'icone pour le lien image est fixe dans le css
* @since 1.6.8	Ajouter la fonctionnalité des filtres
* @since 1.7.0	Les Custom Fields Keys et Values peuvent être sélectionnés
* @since 1.7.2	Ajout d'une section Image sous l'onglet style
*				Ajout d'un control pour positionner l'image verticalement avec le ratio Image appliqué
*				Fix: Alignement du filtre pour les mobiles
* @since 1.8.2	Ajout de la propriété 'prefix_class' pour modifier le style sans recharger le widget
* @since 1.8.4	Ajout des controles pour modifier le style du filtre
* @since 1.8.7	Support des custom breakpoints
*				Suppression de la méthode 'init_settings'
*======================================================================================================*/

namespace EACCustomWidgets\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Repeater;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Core\Breakpoints\Manager as Breakpoints_manager;
use Elementor\Plugin;

if (! defined('ABSPATH')) exit; // Exit if accessed directly

class Image_Galerie_Widget extends Widget_Base {

    /*
    * Retrieve widget name.
    *
    * @access public
    *
    * @return string widget name.
    */
    public function get_name() {
        return 'eac-addon-image-galerie';
    }

    /*
    * Retrieve widget title.
    *
    * @access public
    *
    * @return string widget title.
    */
    public function get_title() {
        return __("Galerie d'Images", 'eac-components');
    }

    /*
    * Retrieve widget icon.
    *
    * @access public
    *
    * @return string widget icon.
    */
    public function get_icon() {
        return 'eicon-gallery-masonry';
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
		return ['isotope-js', 'eac-imagesloaded', 'eac-collageplus'];
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
		return ['eac-image-galerie'];
	}
	
    /*
    * Register widget controls.
    *
    * Adds different input fields to allow the user to change and customize the widget settings.
    *
    * @access protected
    */
	protected function register_controls() {
		
		// @since 1.8.7 Récupère tous les breakpoints actifs
		$active_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();
		
		$this->start_controls_section('ig_galerie_settings',
			[
				'label'     => __('Galerie', 'eac-components'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
			
			$repeater = new Repeater();
			
			/** @since 1.6.0 */
			$repeater->add_control('ig_item_image',
				[
					'label'   => __('Image', 'eac-components'),
					'type'    => Controls_Manager::MEDIA,
					'dynamic' => ['active' => true],
					'default' => [
						'url' => Utils::get_placeholder_image_src(),
					],
				]
			);
			
			/** @since 1.6.5 Ajoute le control Attribut ALT */
			$repeater->add_control('ig_item_alt',
				[
					'label' => __('Attribut ALT', 'eac-components'),
					'type' => Controls_Manager::TEXT,
					'dynamic' => ['active' => true],
					'default' => '',
					'description' => __("Valoriser l'attribut 'ALT' pour une image externe (SEO)", 'eac-components'),
					'label_block'	=> true,
					'render_type' => 'none',
					//'required' => true,
					//'hide_in_inner' => true,
				]
			);
			
			/**
			 * @since 1.6.8	Ajoute le champ dans lequel sont saisies les filtres
			 * @since 1.7.0	Dynamic Tags activés
			 */
			$repeater->add_control('ig_item_filter',
				[
					'label' => __('Labels du filtre', 'eac-components'),
					'type' => Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
						'categories' => [
							TagsModule::POST_META_CATEGORY,
						],
					],
					'default' => '',
					'description' => __("Labels séparés par une virgule", 'eac-components'),
					'label_block'	=> true,
					//'render_type' => 'ui',
					//'required' => true,
					//'hide_in_inner' => true,
				]
			);
			
			/** @since 1.6.0 */
			$repeater->add_control('ig_item_title',
				[
					'label'   => __('Titre', 'eac-components'),
					'type'    => Controls_Manager::TEXT,
					'dynamic' => ['active' => true],
					'default' => __('Image #', 'eac-components'),
					'separator' => 'before',
				]
			);
			
			/** @since 1.6.0 */
			$repeater->add_control('ig_item_desc',
				[
					'label'   => __('Texte', 'eac-components'),
					'type'    => Controls_Manager::TEXTAREA,
					'dynamic' => ['active' => true],
					'default' => __("Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components'),
					'label_block'	=> true,
				]
			);
			
			/** @since 1.6.0 */
			$repeater->add_control('ig_item_url',
				[
					'label'       => __('URL', 'eac-components'),
					'type'        => Controls_Manager::URL,
					'placeholder' => 'http://your-link.com',
					'dynamic' => ['active' => true],
					'default' => [
						'url' => '#',
						'is_external' => true,
						'nofollow' => true,
					],
				]
			);
			
			$this->add_control('ig_image_list',
				[
					'label'       => __('Liste des images', 'eac-components'),
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => [
						[
							'ig_item_image'       => ['url' => Utils::get_placeholder_image_src()],
							'ig_item_title'       => __('Image #1', 'eac-components'),
							'ig_item_desc'        => __("Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components'),
						],
						[
							'ig_item_image'       => ['url' => Utils::get_placeholder_image_src()],
							'ig_item_title'       => __('Image #2', 'eac-components'),
							'ig_item_desc'        => __("Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components'),
						],
						[
							'ig_item_image'       => ['url' => Utils::get_placeholder_image_src()],
							'ig_item_title'       => __('Image #3', 'eac-components'),
							'ig_item_desc'        => __("Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components'),
						],
						[
							'ig_item_image'       => ['url' => Utils::get_placeholder_image_src()],
							'ig_item_title'       => __('Image #4', 'eac-components'),
							'ig_item_desc'        => __("Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components'),
						],
						[
							'ig_item_image'       => ['url' => Utils::get_placeholder_image_src()],
							'ig_item_title'       => __('Image #5', 'eac-components'),
							'ig_item_desc'        => __("Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components'),
						],
						[
							'ig_item_image'       => ['url' => Utils::get_placeholder_image_src()],
							'ig_item_title'       => __('Image #6', 'eac-components'),
							'ig_item_desc'        => __("Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components'),
						],
					],
					'title_field' => '{{{ ig_item_title }}}',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('al_layout_type_settings',
			[
				'label' => __('Disposition', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
			]
		);
			
			// @since 1.5.3
			$this->add_control('ig_layout_type',
				[
					'label'   => __('Mode', 'eac-components'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'masonry',
					'options' => [
						'masonry'	=> __('Mosaïque', 'eac-components'),
						'fitRows'	=> __('Grille', 'eac-components'),
						'justify'	=> __('Justifier', 'eac-components'),
					],
				]
			);
			
			$this->add_control('ig_layout_ratio_image_warning',
				[
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'eac-editor-panel_warning',
					'raw'  => __("Vous pouvez activer/désactiver un ratio sur les images 'Style/Image'", "eac-components"),
					'condition' => ['ig_layout_type' => 'fitRows'],
				]
			);
			
			// @since 1.8.7 Add default values for all active breakpoints.
			$columns_device_args = [];
			foreach($active_breakpoints as $breakpoint_name => $breakpoint_instance) {
				if(!in_array($breakpoint_name, [Breakpoints_manager::BREAKPOINT_KEY_WIDESCREEN, Breakpoints_manager::BREAKPOINT_KEY_LAPTOP])) {
					if($breakpoint_name === Breakpoints_manager::BREAKPOINT_KEY_MOBILE) {
						$columns_device_args[$breakpoint_name] = ['default' => '1'];
					}  else if($breakpoint_name === Breakpoints_manager::BREAKPOINT_KEY_MOBILE_EXTRA) {
						$columns_device_args[$breakpoint_name] = ['default' => '1'];
					} else {
						$columns_device_args[$breakpoint_name] = ['default' => '2'];
					}
				}
			}
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('ig_columns',
				[
					'label'   => __('Nombre de colonnes', 'eac-components'),
					'type'    => Controls_Manager::SELECT,
					'default' => '3',
					'device_args' => $columns_device_args,
					'options'       => [
						'1'    => '1',
						'2'    => '2',
						'3'    => '3',
						'4'    => '4',
						'5'    => '5',
						'6'    => '6',
					],
					'prefix_class' => 'responsive%s-',
					'render_type' => 'template',
					'condition' => ['ig_layout_type!' => 'justify'],
				]
			);
			
			/** @since 1.6.7 Active le mode metro */
			$this->add_control('ig_layout_type_metro',
				[
					'label' => __("Activer le mode Metro", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'description' => __('Est appliqué uniquement à la première image', 'eac-components'),
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'condition' => ['ig_layout_type' => 'masonry'],
				]
			);
			
			$this->add_control('ig_overlay_inout',
				[
					'label'			=> __("Position Titre/Texte", 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'default'		=> 'overlay-in',
					'options'       => [
                        'overlay-in'    => __("À l'intérieur", 'eac-components'),
                        'overlay-out'   => __("À l'extérieur", 'eac-components'),
                    ],
					'condition' => ['ig_layout_type!' => 'justify'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('ig_title_settings',
			[
				'label' => __('Titre', 'eac-components'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
			
			$this->add_control('ig_title_tag',
				[
					'label'			=> __('Étiquette du titre', 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'default'		=> 'h2',
					'options'       => [
						'h1'    => 'H1',
                        'h2'    => 'H2',
                        'h3'    => 'H3',
                        'h4'    => 'H4',
                        'h5'    => 'H5',
                        'h6'    => 'H6',
						'div'   => 'div',
						'p'		=> 'p',
                    ],
					'separator' => 'before',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('al_image_settings',
			[
				'label' => __('Image', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
			]
		);
			
			/** @since 1.8.7 Suppression du mode responsive */
			$this->add_control('ig_image_size',
				[
					'label'   => __('Dimension des images', 'eac-components'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'medium',
					'options'       => [
						'thumbnail'		=> __('Miniature', 'eac-components'),
						'medium'		=> __('Moyenne', 'eac-components'),
						'medium_large'	=> __('Moyenne-large', 'eac-components'),
						'large'			=> __('Large', 'eac-components'),
						'full'			=> __('Originale', 'eac-components'),
					],
					'separator' => 'before',
				]
			);
			
			/**
			 * Layout type justify. Gère la hauteur des images
			 * @since 1.8.7 Application des breakpoints
			 */
			$this->add_responsive_control('ig_justify_height',
				[
					'label' => __("Hauteur de l'image", 'eac-components'),
					'type'  => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 300, 'unit' => 'px'],
					'range' => ['px' => ['min' => 100, 'max' => 500, 'step' => 10]],
					'condition' => ['ig_layout_type' => 'justify'],
				]
			);
			
			/** @since 1.6.0 La visionneuse peut être activée pour tous les modes */
			$this->add_control('ig_image_lightbox',
				[
					'label' => __("Visionneuse", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
		
		$this->end_controls_section();
		
		$this->start_controls_section('ig_filter_settings',
			[
				'label' => __('Filtre', 'eac-components'),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => ['ig_layout_type!' => 'justify'],
			]
		);
			
			/** @since 1.6.8 Ajoute la gestion des filtres */
			$this->add_control('ig_content_filter_display',
				[
					'label' => __("Afficher les filtres", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'separator' => 'before',
				]
			);
			
			/** 1.7.2 Ajout de la class 'ig-filters__wrapper-select' pour l'alignement du select sur les mobiles */
			$this->add_control('ig_content_filter_align',
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
					'default' => 'left',
					'toggle' => true,
					'selectors' => [
						'{{WRAPPER}} .ig-filters__wrapper, {{WRAPPER}} .ig-filters__wrapper-select' => 'text-align: {{VALUE}};',
					],
					'condition' => ['ig_content_filter_display' => 'yes'],
				]
			);
			
		$this->end_controls_section();
		
		/**
		 * Generale Style Section
		 */
		$this->start_controls_section('ig_section_general_style',
			[
				'label'      => __('Global', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);
			
			/** @since 1.8.2 */
			$this->add_control('ig_img_style',
				[
					'label'			=> __("Style", 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'default'		=> 'style-1',
					'options'       => [
						'style-0' => __('Défaut', 'eac-components'),
                        'style-1' => 'Style 1',
                        'style-2' => 'Style 2',
						'style-3' => 'Style 3',
						'style-4' => 'Style 4',
						'style-5' => 'Style 5',
						'style-6' => 'Style 6',
						'style-7' => 'Style 7',
						'style-8' => 'Style 8',
						'style-9' => 'Style 9',
						'style-10' => 'Style 10',
						'style-11' => 'Style 11',
						'style-12' => 'Style 12',
                    ],
					'prefix_class' => 'image-galerie_wrapper-',
				]
			);
			
			/**
			 * Layout type masonry & grid
			 * @since 1.8.7 Application des breakpoints
			 */
			$this->add_responsive_control('ig_item_margin',
				[
					'label' => __('Marge entre les images', 'eac-components'),
					'type'  => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 6,	'unit' => 'px'],
					'range' => ['px' => ['min' => 0, 'max' => 20, 'step' => 1]],
					'selectors' => ['{{WRAPPER}} .image-galerie__inner' => 'margin: {{SIZE}}{{UNIT}};'],
					'condition' => ['ig_layout_type' => ['masonry', 'fitRows']],
				]
			);
			
			$this->add_control('ig_bg_color',
				[
					'label' => __('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#F0DDD5',
					'selectors' => ['{{WRAPPER}} .eac-image-galerie' => 'background-color: {{VALUE}};'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('ig_titre_section_style',
			[
               'label' => __("Titre", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			/** @since 1.6.0 Applique la couleur à l'icone de la visionneuse */
			$this->add_control('ig_titre_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#919CA7',
					'selectors' => [
						'{{WRAPPER}} .image-galerie__titre,
						{{WRAPPER}} .image-galerie__item .image-galerie__content.overlay-in .image-galerie__lightbox-icon' => 'color: {{VALUE}};'
					],
				]
			);
			
			/**
			 * @since 1.6.0 Applique la fonte à l'icone de la visionneuse
			 * @since 1.6.7 Suppression de la fonte de l'icone de la visionneuse
			 */
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'ig_titre_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .image-galerie__titre',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('ig_texte_section_style',
			[
               'label' => __("Texte", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('ig_texte_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => ['{{WRAPPER}} .image-galerie__texte' => 'color: {{VALUE}};'],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'ig_texte_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .image-galerie__texte',
				]
			);
			
		$this->end_controls_section();
		
		/** @since 1.7.2 Ajout de la section Image */
		$this->start_controls_section('ig_image_section_style',
			[
               'label' => __("Image", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['ig_layout_type' => 'fitRows'],
			]
		);
			
			/** @since 1.6.0 Active le ratio image */
			$this->add_control('ig_enable_image_ratio',
				[
					'label' => __("Activer le ratio image", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					
				]
			);
			
			/**
			 * @since 1.6.0 Le ratio appliqué à l'image
			 * @since 1.8.7 Préparation pour les breakpoints
			 */
			$this->add_responsive_control('ig_image_ratio',
				[
					'label' => __('Ratio', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['%'],
					'default' => ['size' => 0.6, 'unit' => '%'],
					'range' => ['%' => ['min' => 0.1, 'max' => 2, 'step' => 0.1]],
					'selectors' => ['{{WRAPPER}} .image-galerie.image-galerie__ratio .image-galerie__image' => 'padding-bottom:calc({{SIZE}} * 100%);'],
					'condition' => ['ig_enable_image_ratio' => 'yes'],
				]
			);
			
			/**
			 * @since 1.7.2 Positionnement vertical de l'image
			 * @since 1.8.7 Préparation pour les breakpoints
			 */
			$this->add_responsive_control('ig_image_ratio_position_y',
				[
					'label' => __('Position verticale', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['%'],
					'default' => ['size' => 50, 'unit' => '%'],
					'range' => ['%' => ['min' => 0, 'max' => 100, 'step' => 5]],
					'selectors' => ['{{WRAPPER}} .image-galerie.image-galerie__ratio .image-galerie__image .image-galerie__image-instance' => 'object-position: 50% {{SIZE}}%;'],
					'condition' => ['ig_enable_image_ratio' => 'yes'],
				]
			);
			
		$this->end_controls_section();
		
		/** @since 1.8.4 Modification du style du filtre */
		$this->start_controls_section('ig_filter_style',
			[
               'label' => __("Filtre", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['ig_layout_type!' => 'justify', 'ig_content_filter_display' => 'yes'],
			]
		);
			
			$this->add_control('ig_filter_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => [
						'{{WRAPPER}} .ig-filters__wrapper .ig-filters__item, {{WRAPPER}} .ig-filters__wrapper .ig-filters__item a' => 'color: {{VALUE}};',
					],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'ig_filter_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .ig-filters__wrapper .ig-filters__item, {{WRAPPER}} .ig-filters__wrapper .ig-filters__item a',
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
		if(! $settings['ig_image_list']) {
			return;
		}
		
		$id = "image_galerie_" . $this->get_id();
		
		$class = vsprintf('image-galerie %s layout-type-%s', $this->init_settings());
		$this->add_render_attribute('galerie__instance', 'class', $class);
		$this->add_render_attribute('galerie__instance', 'id', $id);
		$this->add_render_attribute('galerie__instance', 'data-settings', $this->get_settings_json($id));
		?>
		<div class="eac-image-galerie">
			<?php echo $this->render_filters(); /** @since 1.6.8 */?>
			<div <?php echo $this->get_render_attribute_string('galerie__instance'); ?>>
				<div class="image-galerie__item-sizer"></div>
				<?php $this->render_galerie(); ?>
			</div>
		</div>
		<?php
    }
	
    protected function render_galerie() {
		$settings = $this->get_settings_for_display();
		
		// ID de l'article
		$unique_id = uniqid();
		
		// Variable du rendu final
		$html = '';
		
		// Format du titre
		$title_tag = $settings['ig_title_tag'];
		
		// Layout == justify, overlay interne par défaut
		$overlay = $settings['ig_layout_type'] === 'justify' ? 'overlay-in' : $settings['ig_overlay_inout'];
		
		// Position de l'overlay 
		$overlay_pos = 'center';
		
		// Visionneuse
		$has_lb = $settings['ig_image_lightbox'] === 'yes' ? true : false;
		
		// Filtres activés
		$has_filter = $settings['ig_content_filter_display'] === 'yes' ? true : false;
		
		// La classe du contenu de l'item, image+titre+texte
		$this->add_render_attribute('galerie__inner', 'class', 'image-galerie__inner');
		
		// La classe du titre/texte
		$this->add_render_attribute('galerie__content', 'class', 'image-galerie__content ' . $overlay);
		
		// Boucle sur tous les items
		foreach($settings['ig_image_list'] as $item) {
			
			// Il y a une image
			if(!empty($item['ig_item_image']['url'])) {
				
				/**
				 * @since 1.6.8 Filtres activés
				 * @since 1.7.0 Check Custom Fields values Format = key::value
				 */
				if($has_filter && !empty($item['ig_item_filter'])) {
					$sanized = array();
					$filters = explode(',', $item['ig_item_filter']);
					foreach($filters as $filter) {
						if(strpos($filter, '::') != false) {
							$filter = explode('::', $filter)[1];
						}
						$sanized[] = sanitize_title(mb_strtolower($filter, 'UTF-8'));
					}
					// La classe de l'item + filtres
					$this->add_render_attribute('galerie__item', 'class', 'image-galerie__item ' . implode(' ', $sanized));
				} else {
					// La classe de l'item
					$this->add_render_attribute('galerie__item', 'class', 'image-galerie__item');
				}
				
				// Une URL
				$link_url = $item['ig_item_url']['url'] && $item['ig_item_url']['url'] !== '#' ? esc_url($item['ig_item_url']['url']) : '';
				
				// Le titre
				$title_with_tag = '<' . $title_tag . ' class="image-galerie__titre">' . esc_html($item['ig_item_title']) . '</' . $title_tag . '>';
				
				// Formate le titre avec ou sans icone
				if($link_url) {
					$title = '<span class="image-galerie__titre"><i class="fas fa-link" aria-hidden="true"></i> ' . $title_with_tag . '</span>';
				} else {
					$title = '<span class="image-galerie__titre">' . $title_with_tag . '</span>';
				}
				
				/**
				 * @since 1.6.5 Affecte le titre à l'attribut ALT des images externes si le control 'ig_item_alt' n'est pas valorisé
				 */
				$image_alt = isset($item['ig_item_alt']) && $item['ig_item_alt'] !== '' ? esc_html($item['ig_item_alt']) : esc_html($item['ig_item_title']);
				
				/**
				 *
				 * @since 1.4.1 Ajout du paramètre 'ver' à l'image avec un identifiant unique
				 * pour forcer le chargement de l'image du serveur et non du cache pour les MEDIAS
				 *
				 * @since 1.6.0 Gestion des images externes
				 * La balise dynamique 'External image' ne renvoie pas l'ID de l'image
				 */
				// Récupère les propriétés de l'image avec la version pour les MEDIAS
				if(!empty($item['ig_item_image']['id'])) {
					$image_data = wp_get_attachment_image_src($item['ig_item_image']['id'], $settings['ig_image_size']);
					$image_url = sprintf("%s?ver=%s", esc_url($image_data[0]), $unique_id);
					$image_alt = Control_Media::get_image_alt($item['ig_item_image']); // 'get_image_alt' renvoie toujours une chaine par défaut
				} else { // Image avec Url externe sans paramètre version
					$image_url = esc_url($item['ig_item_image']['url']);
				}
				
				// La visionneuse est activée et pas d'overlay
				if($has_lb && $overlay === 'overlay-out') {
					$image = sprintf('<a href="%s" data-elementor-open-lightbox="no" data-fancybox="%s" data-caption="%s">
					<img class="eac-image-loaded image-galerie__image-instance" src="%s" alt="%s" /></a>', $image_url, $unique_id, esc_html($item['ig_item_title']), $image_url, $image_alt);
				} else {
					$image = sprintf('<img class="eac-image-loaded image-galerie__image-instance" src="%s" alt="%s" />', $image_url, $image_alt);
				}
				
				// Formate les paramètres de l'URL
				if($link_url) {
					$this->add_render_attribute('ig-link-to', 'href', $link_url);
					if($item['ig_item_url']['is_external']) {
						$this->add_render_attribute('ig-link-to', 'target', '_blank');
					}
					if($item['ig_item_url']['nofollow']) {
						$this->add_render_attribute('ig-link-to', 'rel', 'nofollow');
					}
				}
		
				// On construit le DOM
				$html .= '<div '. $this->get_render_attribute_string('galerie__item') . '>';
					$html .= '<div ' . $this->get_render_attribute_string('galerie__inner') . '>';
						$html .= '<div class="image-galerie__image">';
							$html .= $image;
						$html .= '</div>';
						
						$html .= '<div ' . $this->get_render_attribute_string('galerie__content') . '>';
							// La visionneuse est activée et l'overlay est sur l'image
							if($has_lb && $overlay === 'overlay-in') {
								$html .= '<a href="' . $image_url . '" data-elementor-open-lightbox="no" data-fancybox="' . $unique_id . '" data-caption="' . $image_alt . '">';
									$html .= '<span class="image-galerie__lightbox-icon"><i class="far fa-image" aria-hidden="true"></i></span>';
								$html .= '</a>';
							}
							if($link_url) { // Un lien on l'affiche
								$html .= '<a ' . $this->get_render_attribute_string('ig-link-to') . '>';
							}
								//$html .= '<span class="image-galerie__overlay center">';
								$html .= '<span class="image-galerie__overlay ' . $overlay_pos . '">';
									$html .= $title;
									$html .= '<span class="image-galerie__texte">' . esc_html($item['ig_item_desc']) . '</span>';
								$html .= '</span>';
							if($link_url) {
								$html .= '</a>';
							}
						$html .= '</div>';	// galerie__content
					$html .= '</div>';		// galerie__inner
				$html .= '</div>';			// galerie__item
			}
			
			// Vide les attributs html du lien
			$this->set_render_attribute('ig-link-to', 'href', null);
			$this->set_render_attribute('ig-link-to', 'target', null);
			$this->set_render_attribute('ig-link-to', 'rel', null);
			// Vide la class du wrapper
			$this->set_render_attribute('galerie__item', 'class', null);
		}
	// Affiche le rendu		
	echo $html;
	}
	
	/**
	 * init_settings
	 * 
	 * Description: Détermine le type d'affichage et les dimensions responsive des trois modes
	 * Desktop, Tablet et Mobile
	 * 
	 * @since 0.0.9
	 * @since 1.8.7 Application des breakpoints et suppression des dimensions responsive des trois modes
	 */
	protected function init_settings() {
		$module_settings = $this->get_settings_for_display();
		
		$layout_type = in_array($module_settings['ig_layout_type'], ['masonry', 'fitRows', 'justify']) ? $module_settings['ig_layout_type'] : 'fitRows';
		$ratio = $module_settings['ig_enable_image_ratio'] === 'yes' ? ' image-galerie__ratio' : '';
		
		return array($ratio, $layout_type);
	}
	
	/**
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
	 * @since 0.0.9
	 * @since 1.5.3	Modifie l'affectation de 'layoutType'
	 * 				Suppression de 'order' du control 'ig_image_order'
	 * @since 1.6.7	Check 'justify' layout type for the grid parameters
	 *				Le mode Metro est activé
	 */
	protected function get_settings_json($id) {
		$module_settings = $this->get_settings_for_display();
		$layout_type = in_array($module_settings['ig_layout_type'], ['masonry', 'fitRows', 'justify']) ? $module_settings['ig_layout_type'] : 'fitRows';
		$grid_height = !empty($module_settings['ig_justify_height']['size']) ? $module_settings['ig_justify_height']['size'] : 300; // justify Desktop
		
		$settings = array(
			"data_id"		=> $id,
			"layoutType"    => $layout_type,
			"gridHeight" 	=> $grid_height,
			"gridHeightT" 	=> !empty($module_settings['ig_justify_height_tablet']['size']) ? $module_settings['ig_justify_height_tablet']['size'] : $grid_height, // justify Tab
			"gridHeightM" 	=> !empty($module_settings['ig_justify_height_mobile']['size']) ? $module_settings['ig_justify_height_mobile']['size'] : $grid_height, // justify Mob
			"posoverlay" 	=> $layout_type === 'justify' ? 'overlay-in' : $module_settings['ig_overlay_inout'],
			"data_metro"	=> $module_settings['ig_layout_type_metro'] === 'yes' ? true : false,
			"data_filtre"	=> $module_settings['ig_content_filter_display'] === 'yes' ? true : false,
		);

		$settings = json_encode($settings);
		return $settings;
	}
	
	/**
	 * render_filters
	 * 
	 * Description: Retourne les filtres formaté en HTML en ligne
	 * ou sous forme de liste pour les media query
	 * 
	 * @since 1.6.8
	 * @since 1.7.0 Check Custom Fields values Format = key::value
	 */
	protected function render_filters() {
		$settings = $this->get_settings_for_display();
		// Filtres activés
		$has_filter = $settings['ig_content_filter_display'] === 'yes' ? true : false;
		
		// Filtre activé
		if($has_filter) {
			$filtersName = array();
			$htmFiltres = '';
			
			foreach($settings['ig_image_list'] as $item) {
				if(!empty($item['ig_item_image']['url']) && !empty($item['ig_item_filter'])) {
					$currentFilters = explode(',', $item['ig_item_filter']);
					foreach($currentFilters as $currentFilter) {
						/** @since 1.7.0 */
						if(strpos($currentFilter, '::') != false) {
							$currentFilter = explode('::', $currentFilter)[1];
						}
						$filtersName[sanitize_title(mb_strtolower($currentFilter, 'UTF-8'))] = sanitize_title(mb_strtolower($currentFilter, 'UTF-8'));
					}
				}
			}
			
			// Des filtres
			if(!empty($filtersName)) {
				ksort($filtersName, SORT_FLAG_CASE|SORT_NATURAL);
				
				$htmFiltres .= "<div id='ig-filters__wrapper' class='ig-filters__wrapper'>";
				$htmFiltres .= "<div class='ig-filters__item ig-active'><a href='#' data-filter='*'>" . __('Tous', 'eac-components') . "</a></div>";
					foreach($filtersName as $filterName) {
						$htmFiltres .= "<div class='ig-filters__item'><a href='#' data-filter='." . sanitize_title($filterName) . "'>" . ucfirst($filterName) . "</a></div>";
					}
				$htmFiltres .= "</div>";
				
				// Filtre dans une liste pour les media query
				$htmFiltres .= "<div id='ig-filters__wrapper-select' class='ig-filters__wrapper-select'>";
				$htmFiltres .= "<select class='ig-filter__select'>";
					$htmFiltres .= "<option value='*' selected>" . __('Tous', 'eac-components') . "</option>";
					foreach($filtersName as $filterName) {
						$htmFiltres .= "<option value='." . sanitize_title($filterName) . "'>" . ucfirst($filterName) . "</option>";
					}
				$htmFiltres .= "</select>";
				$htmFiltres .= "</div>";
				
				return $htmFiltres;
			}
		}
		return;
	}
	
	protected function content_template() {}
}