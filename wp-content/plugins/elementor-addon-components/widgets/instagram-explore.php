<?php

/*======================================================================
* Class: Instagram_Explore_Widget
* Name: Lecteur RSS
* Slug: eac-addon-instagram-explore
*
* Description: Instagram_Explore_Widget affiche des photos
* stockées sur instagram avec un hashtag défini
*
* @since 1.3.0
* @since 1.4.1	Layout type masonry & grid
* @since 1.4.5	Affiche les hashtags associés
* @since 1.6.0	Correctif ajout de la lib jqCloud
* @since 1.6.1	Modification du label 'Vidéo/Diaporama'
*				Suppression du paragraphe Vidéo/Diaporama
*				dans la div 'insta-explore__container-hidden-content'
* @since 1.8.7	Support des custom breakpoints
*=======================================================================*/

namespace EACCustomWidgets\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Repeater;
use Elementor\Core\Breakpoints\Manager as Breakpoints_manager;
use Elementor\Plugin;

if (! defined('ABSPATH')) exit; // Exit if accessed directly

class Instagram_Explore_Widget extends Widget_Base {

    /*
    * Retrieve widget name.
    *
    * @access public
    *
    * @return string widget name.
    */
    public function get_name() {
        return 'eac-addon-instagram-explore';
    }

    /*
    * Retrieve widget title.
    *
    * @access public
    *
    * @return string widget title.
    */
    public function get_title() {
        return __("Hashtag Instagram", 'eac-components');
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
    * @since 1.6.0 Ajout de la lib jqCloud
	*/
	public function get_script_depends() {
		return ['isotope-js', 'eac-jqcloud', 'eac-imagesloaded', 'eac-instagram-explore'];
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
		return ['eac-instagram-explore', 'eac-jqcloud'];
	}
	
    /*
    * Register widget controls.
    *
    * Adds different input fields to allow the user to change and customize the widget settings.
    *
    * @access protected
    */
    protected function register_controls() {
		
		$this->start_controls_section('instaexpl_explore_settings',
			[
				'label'     => __('Hashtag Instagram', 'eac-components'),
			]
		);
			
			$repeater = new Repeater();
			
			$repeater->add_control('instaexpl_item_title',
				[
					'label'   => __('Titre', 'eac-components'),
					'type'    => Controls_Manager::TEXT,
				]
			);
			
			$repeater->add_control('instaexpl_item_tag',
				[
					'label'	=> __('TAG', 'eac-components'),
					'type'	=> Controls_Manager::TEXT,
					'placeholder' => 'Hashtag',
				]
			);
			
			$this->add_control('instaexpl_tags_list',
				[
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => [
						[
							'instaexpl_item_title'	=> 'Armchairs',
							'instaexpl_item_tag'	=> 'cestpascommode',
						],
						[
							'instaexpl_item_title'	=> 'Journal - Le Canard enchaîné',
							'instaexpl_item_tag'	=> 'canardenchainé',
						],
						[
							'instaexpl_item_title'	=> 'Paris - France',
							'instaexpl_item_tag'	=> 'parismaville',
						],
						[
							'instaexpl_item_title'	=> 'Paris - Musée du louvre',
							'instaexpl_item_tag'	=> 'museedulouvre',
						],
						[
							'instaexpl_item_title'	=> 'Galerie - Tate Gallery',
							'instaexpl_item_tag'	=> 'tategallery',
						],
						[
							'instaexpl_item_title'	=> 'Érudit - Umberto Eco',
							'instaexpl_item_tag'	=> 'umbertoeco',
						],
						[
							'instaexpl_item_title'	=> 'Cinéma - Bandes annonces',
							'instaexpl_item_tag'	=> 'movie_cinema_one_trailer',
						],
						[
							'instaexpl_item_title'	=> 'Actrice - Meryl Streep',
							'instaexpl_item_tag'	=> 'merylstreep',
						],
						[
							'instaexpl_item_title'	=> 'Actrice - Sigourney Weaver',
							'instaexpl_item_tag'	=> 'sigourneyweaver',
						],
						[
							'instaexpl_item_title'	=> 'Actrice - Tilda Swinton',
							'instaexpl_item_tag'	=> 'tildaswinton',
						],
						[
							'instaexpl_item_title'	=> 'Actrice - Frances McDormand',
							'instaexpl_item_tag'	=> 'francesmcdormand',
						],
						[
							'instaexpl_item_title'	=> 'Actrice - Rita Hayworth',
							'instaexpl_item_tag'	=> 'ritahayworth',
						],
						[
							'instaexpl_item_title'	=> 'Acteur - Jean-Pierre Léaud',
							'instaexpl_item_tag'	=> 'jeanpierreleaud',
						],
						[
							'instaexpl_item_title'	=> 'Acteur - Jack Nicolson',
							'instaexpl_item_tag'	=> 'jacknicolson',
						],
						[
							'instaexpl_item_title'	=> 'Acteur - Bill Murray',
							'instaexpl_item_tag'	=> 'billmurray',
						],
						[
							'instaexpl_item_title'	=> 'Acteur - Jeff Bridges',
							'instaexpl_item_tag'	=> 'jeffbridges',
						],
						[
							'instaexpl_item_title'	=> 'Acteur - Steve Buscemi',
							'instaexpl_item_tag'	=> 'stevebuscemi',
						],
						[
							'instaexpl_item_title'	=> 'Acteur - Mads Mikkelsen',
							'instaexpl_item_tag'	=> 'theofficialmads',
						],
						[
							'instaexpl_item_title'	=> 'Acteur - Song Kang-ho',
							'instaexpl_item_tag'	=> 'songkangho',
						],
						[
							'instaexpl_item_title'	=> 'Acteur - Gian Maria Volontè',
							'instaexpl_item_tag'	=> 'gianmariavolontè',
						],
						[
							'instaexpl_item_title'	=> 'Acteur - Jack Palance',
							'instaexpl_item_tag'	=> 'jackpalance',
						],
						[
							'instaexpl_item_title'	=> 'Acteur - Peter Cushing',
							'instaexpl_item_tag'	=> 'petercushing',
						],
						[
							'instaexpl_item_title'	=> 'Réalisateurs - Frères Coen',
							'instaexpl_item_tag'	=> 'coenbrothers',
						],
						[
							'instaexpl_item_title'	=> 'Réalisateur - Bong Joon-ho',
							'instaexpl_item_tag'	=> 'bongjoonho',
						],
						[
							'instaexpl_item_title'	=> 'Réalisateur - Billy Wilder',
							'instaexpl_item_tag'	=> 'billywilder',
						],
						[
							'instaexpl_item_title'	=> 'Réalisateur - Jean-Pierre Melville',
							'instaexpl_item_tag'	=> 'jeanpierremelville',
						],
						[
							'instaexpl_item_title'	=> 'Peintre - Séraphine Louis',
							'instaexpl_item_tag'	=> 'seraphinedesenlis',
						],
						[
							'instaexpl_item_title'	=> 'Peintre - André Lothe',
							'instaexpl_item_tag'	=> 'andrelothe',
						],
						[
							'instaexpl_item_title'	=> 'Peintre - René Magritte',
							'instaexpl_item_tag'	=> 'renemagrittemuseum',
						],
						[
							'instaexpl_item_title'	=> 'Peintre - Camille Pissarro',
							'instaexpl_item_tag'	=> 'camillepissarogallery',
						],
						[
							'instaexpl_item_title'	=> 'Peintre - Paul Signac',
							'instaexpl_item_tag'	=> 'paulsignac',
						],
						[
							'instaexpl_item_title'	=> 'Peintre - Georges Seurat',
							'instaexpl_item_tag'	=> 'georgesseurat',
						],
						[
							'instaexpl_item_title'	=> 'Peintre - Henry Edmond Cross',
							'instaexpl_item_tag'	=> 'HenryEdmondCross',
						],
						[
							'instaexpl_item_title'	=> 'Peintre - Henri Eugène Augustin le Sidaner',
							'instaexpl_item_tag'	=> 'henrilesidaner',
						],
						[
							'instaexpl_item_title'	=> 'Peintre - Maximilien Luce',
							'instaexpl_item_tag'	=> 'maximilienluce',
						],
						[
							'instaexpl_item_title'	=> 'Peintre - Théo Van Rysselberghe',
							'instaexpl_item_tag'	=> 'rysselberghe',
						],
						[
							'instaexpl_item_title'	=> 'Peintre - Rembrandt van Rijn',
							'instaexpl_item_tag'	=> 'rembrandtvanrijn',
						],
						[
							'instaexpl_item_title'	=> 'Peintre - Edvard Munch',
							'instaexpl_item_tag'	=> 'MunchMuseum',
						],
						[
							'instaexpl_item_title'	=> 'Peintre - Surréalisme',
							'instaexpl_item_tag'	=> 'surrealism',
						],
						[
							'instaexpl_item_title'	=> 'Images - Photoshop',
							'instaexpl_item_tag'	=> 'photoshopmanipulation',
						],
						[
							'instaexpl_item_title'	=> 'Tomates - Les tomates tueuses',
							'instaexpl_item_tag'	=> 'killertomatoes',
						],
					],
					'title_field' => '{{{ instaexpl_item_title }}}',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('instaexpl_items_content',
				[
					'label'     => __('Contenu', 'eac-components'),
				]
			);
			
			/*
			$this->add_control('instaexpl_item_nombre',
				[
					'label' => __("Nombre d'articles", 'eac-components'),
					'type' => Controls_Manager::NUMBER,
					'min' => 5,
					'max' => 75,
					'step' => 5,
					'default' => 60,
				]
			);
			*/
			$this->add_control('instaexpl_caption_length',
				[
					'label'   => __('Nombre de caractères', 'eac-components'),
					'description'   => __('Légende', 'eac-components'),
					'type'    => Controls_Manager::NUMBER,
					'default' => 100,
					'min'     => 50,
					'max'     => 200,
					'step'    => 10,
				]
			);
			
			$this->add_control('instaexpl_item_sort',
				[
					'label'   => __('Trier par', 'eac-components'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'update',
					'options' => [
						'update'			=> __('Date de publication', 'eac-components'),
						'likeCount_sort'	=> __('Likes', 'eac-components'),
						'commentCount_sort'	=> __('Commentaires', 'eac-components'),
					],
					'separator'	=> 'after',
				]
			);
			
			$this->add_control('instaexpl_post_caption',
				[
					'label'        => __('Montrer la légende', 'eac-components'),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __('oui', 'eac-components'),
					'label_off'    => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default'      => '',
				]
			);
			
			$this->add_control('instaexpl_post_likes_count',
				[
					'label'        => __('Nombre de Likes', 'eac-components'),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __('oui', 'eac-components'),
					'label_off'    => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			);
			
			$this->add_control('instaexpl_post_comments_count',
				[
					'label'        => __('Nombre de commentaires', 'eac-components'),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __('oui', 'eac-components'),
					'label_off'    => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			);
			
			// @since 1.6.1 Modification du label
			$this->add_control('instaexpl_post_video_count',
				[
					'label'        => __('Téléchargement vidéo', 'eac-components'),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __('oui', 'eac-components'),
					'label_off'    => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			);
			
			$this->add_control('instaexpl_post_hashtag_count',
				[
					'label'        => __('Nombre de hashtags', 'eac-components'),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __('oui', 'eac-components'),
					'label_off'    => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			);
			
			$this->add_control('instaexpl_post_date',
				[
					'label'        => __('Date de publication', 'eac-components'),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __('oui', 'eac-components'),
					'label_off'    => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default'      => '',
				]
			);
			
			$this->add_control('instaexpl_post_link',
				[
					'label'			=> __('Icône lien Instagram', 'eac-components'),
					'description'	=> __("Désactivé, le lien est sur l'image", 'eac-components'),
					'type'			=> Controls_Manager::SWITCHER,
					'label_on'		=> __('oui', 'eac-components'),
					'label_off'		=> __('non', 'eac-components'),
					'return_value'	=> 'yes',
					'default'		=> 'yes',
					'separator'		=> 'after',
				]
			);
			
			$this->add_control('instaexpl_photo_size',
				[
					'label'   => __('Dimension des images', 'eac-components'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'thumbnail_src',
					'options' => [
						'image240px'	=> __('Miniature (240px)', 'eac-components'),
						'image480px'	=> __('Basse (480px)', 'eac-components'),
						'img_med'		=> __('Moyenne (640px)', 'eac-components'),
						'thumbnail_src'	=> __('Carré (640x640)', 'eac-components'),
						'img_standard'	=> __('Haute', 'eac-components'),
					],
				]
			);
			
			$this->add_control('instaexpl_image_lightbox',
				[
					'label' => __("Visionneuse", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
					'condition' => ['instaexpl_post_link' => 'yes'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('instaexpl_layout_settings',
			[
				'label' => __('Disposition', 'eac-components'),
			]
		);
			
			// @since 1.4.1 Layout type masonry & grid
			$this->add_control('instaexpl_layout_type',
				[
					'label'   => __('Mode', 'eac-components'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'masonry',
					'options' => [
						'masonry'	=> __('Mosaïque', 'eac-components'),
						'fitRows'	=> __('Grille', 'eac-components'),
					],
				]
			);
			
			// @since 1.8.7 Add default values for all active breakpoints.
			$active_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();
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
			$this->add_responsive_control('instaexpl_layout_columns',
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
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('instaexpl_general_style',
			[
				'label'      => __('Global', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);
		
			$this->add_control('instaexpl_wrapper_style',
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
			
			$this->add_control('instaexpl_wrapper_bg_color',
				[
					'label' => __('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#e4efe9',
					'selectors' => [ '{{WRAPPER}} .eac-insta-explore' => 'background-color: {{VALUE}};' ],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('instaexpl_img_style',
			[
               'label' => __("Image", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('instaexpl_item_margin',
				[
					'label' => __('Marge entre les images', 'eac-components'),
					'type'  => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 5,	'unit' => 'px'],
					'range' => ['px' => ['min' => -1, 'max' => 10, 'step' => 2]],
					'selectors' => ['{{WRAPPER}} .insta-explore__item-content' => 'margin: {{SIZE}}{{UNIT}};'],
				]
			);
			
			$this->add_control('instaexpl_img_effect_sepia',
				[
					'label' => __('Effet Sepia', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['%'],
					'range' => ['%' => ['min' => 0, 'max' => 100, 'step' => 10]],
					'default' => ['unit' => '%', 'size' => 0],
					'selectors' => ['{{WRAPPER}} .insta-explore__item-image img' => '-webkit-filter:sepia({{SIZE}}%); filter:sepia({{SIZE}}%);'],
				]
			);
			
			$this->add_control('instaexpl_img_border_radius',
				[
					'label' => __('Rayon de la bordure (%)', 'eac-components'),
					'type' => Controls_Manager::DIMENSIONS,
					'allowed_dimensions' => ['top', 'right', 'bottom', 'left'],
					'default' => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => '%', 'isLinked' => true],
					'selectors' => ['{{WRAPPER}} .insta-explore__item-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
				]
			);
			
			$this->add_control('instaexpl_img_bg_color',
				[
					'label' => __('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#fff',
					'selectors' => ['{{WRAPPER}} .insta-explore__item-content.style-0,
									{{WRAPPER}} .insta-explore__item-content.style-1,
									{{WRAPPER}} .insta-explore__item-content.style-2,
									{{WRAPPER}} .insta-explore__item-content.style-3,
									{{WRAPPER}} .insta-explore__item-content.style-4,
									{{WRAPPER}} .insta-explore__item-content.style-5,
									{{WRAPPER}} .insta-explore__item-content.style-6,
									{{WRAPPER}} .insta-explore__item-content.style-7,
									{{WRAPPER}} .insta-explore__item-content.style-8' => 'background-color: {{VALUE}};'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('instaexpl_caption_style',
			[
               'label' => __("Légende", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['instaexpl_post_caption' => 'yes'],
			]
		);
			
			$this->add_control('instaexpl_caption_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => [ '{{WRAPPER}} .insta-explore__item-description p' => 'color: {{VALUE}};' ],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'instaexpl_caption_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .insta-explore__item-description p',
				]
			);
			
		$this->end_controls_section();
		
		/**
		 * Pictograms Style Section
		 */
		$this->start_controls_section('instaexpl_icon_style',
			[
				'label'      => __('Pictogrammes', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						['name' => 'instaexpl_post_comments_count', 'operator' => '===', 'value' => 'yes'],
						['name' => 'instaexpl_post_likes_count', 'operator' => '===', 'value' => 'yes'],
						['name' => 'instaexpl_post_video_count', 'operator' => '===', 'value' => 'yes'],
						['name' => 'instaexpl_post_date', 'operator' => '===', 'value' => 'yes'],
						['name' => 'instaexpl_post_link', 'operator' => '===', 'value' => 'yes'],
					]
				]
			]
		);
		
			$this->add_control('instaexpl_icon_color',
				[
					'label'  => __('Couleur', 'eac-components'),
					'type'   => Controls_Manager::COLOR,
					'default'	=> "#919CA7",
					'selectors' => ['{{WRAPPER}} .insta-explore__meta-item span, 
									{{WRAPPER}} .insta-explore__header-content span i,
									{{WRAPPER}} .insta-explore__download-video-icon i' => 'color: {{VALUE}} !important;'],
				]
			);
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('instaexpl_icon_size',
				[
					'label' => __("Dimension (px)", 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 20,	'unit' => 'px'],
					'range' => ['px' => ['min' => 10, 'max' => 30, 'step' => 2]],
					'conditions' => [
						'relation' => 'or',
						'terms' => [
							['name' => 'instaexpl_post_comments_count', 'operator' => '===', 'value' => 'yes'],
							['name' => 'instaexpl_post_likes_count', 'operator' => '===', 'value' => 'yes'],
							['name' => 'instaexpl_post_date', 'operator' => '===', 'value' => 'yes'],
							['name' => 'instaexpl_post_link', 'operator' => '===', 'value' => 'yes'],
						]
					],
					'selectors'  => ['{{WRAPPER}} .insta-explore__meta-item' => 'font-size: {{SIZE}}{{UNIT}};'],
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
		if(! $settings['instaexpl_tags_list']) {
			return;
		}
		$class = vsprintf('insta-explore desktop-column-%s tablet-column-%s mobile-column-%s', $this->init_settings());
		$this->add_render_attribute('insta_explore', 'class', $class);
		$this->add_render_attribute('insta_explore', 'data-settings', $this->get_settings_json());
		
		?>
		<div class="eac-insta-explore">
			<?php $this->render_galerie(); ?>
			<div <?php echo $this->get_render_attribute_string('insta_explore'); ?>>
				<div class="insta-explore__item-sizer"></div>
			</div>
			<div class="insta-explore__container-jqcloud">
				<p><?php echo __("Le tag sélectionné sera enregistré dans un cookie pour améliorer votre expérience utilisateur", 'eac-components'); ?></p>
			    <div class="insta-explore__jqcloud"></div>
			</div>
			<div class="insta-explore__error"></div>
			<div class="eac__button">
				<button id="insta-explore__read-button-next" class="eac__read-button"><?php _e("Plus d'articles", 'eac-components'); ?><span class="insta-explore__read-button-next-paged">0</span></button>
			</div>
			<div id="insta-explore__loader-wheel-next" class="eac__loader-spin"></div>
		</div>
		<?php
    }
	
    protected function render_galerie() {
		$settings = $this->get_settings_for_display();
		
		?>
		<div class="insta-explore__select-item-list">
			<div class="insta-explore__options-items-info">
				<h2><?php echo __('Photos Instagram publiées avec le hashtag', 'eac-components'); ?></h2>
				<p><?php echo __('Les pictogrammes des images affichent plus de contenu', 'eac-components'); ?></p>
			</div>
			<div class="insta-explore__container-cb">
				<label  class="insta-explore__items-cb-label" for="insta-explore__items-cb">
					<input type="checkbox" id="insta-explore__items-cb" name="insta-explore__checkbox" value="1">
					<span><?php echo __('Top publications', 'eac-components'); ?></span>
				</label>
				<label class="insta-explore__items-cb2-label" for="insta-explore__items-cb2">
					<input type="checkbox" id="insta-explore__items-cb2" name="insta-explore__checkbox" value="1">
					<span><?php echo __('#Nuage de tags', 'eac-components'); ?></span>
				</label>
			</div>
			<div class="insta-explore__options-items-list">
				<select id="insta-explore__options-items" class="insta-explore__options-items">
					<?php foreach($settings['instaexpl_tags_list'] as $key => $item) { ?>
						<?php if(! empty($item['instaexpl_item_tag'])) : ?>
					        <option value="<?php echo $item['instaexpl_item_tag']; ?>"><?php echo $item['instaexpl_item_title']; ?></option>
						<?php endif; ?>
					<?php } ?>
				</select>
			</div>
			<div class="insta-explore__select-item">
				<input type="text" id="insta-explore__item-name" name="insta-explore__item-name" required minlength="4" maxlength="100" size="20">
			</div>
			<div class="eac__button">
				<button id="insta-explore__read-button" class="eac__read-button"><?php _e('Lire le flux', 'eac-components'); ?></button>
			</div>
			<div id="insta-explore__loader-wheel" class="eac__loader-spin"></div>
		</div>
		<div class="insta-explore__header"></div>
		<div class="insta-explore__container-hidden-content">
			<p class="insta-explore__hd-mention"><?php echo __("Hashtags associés à la photo", "eac-components"); ?></p>
			<p class="insta-explore__hd-likes"><?php echo __("Personnes qui aiment la photo", "eac-components"); ?></p>
			<p class="insta-explore__hd-comments"><?php echo __("Personnes qui ont commenté la photo", "eac-components"); ?></p>
			<p class="insta-explore__hd-related"><?php echo __('Hashtags associés', 'eac-components'); ?></p>
			<div class="insta-explore__hidden-content"></div>
		</div>
		<div class="eac__container-head-button"> <!-- @since 1.4.5 -->
			<span id="insta-explore__related-hashtags" class="eac__head-button"><p><?php echo __('Hashtags associés', 'eac-components'); ?></p></span>
		</div>
		<?php
	}
	
	protected function init_settings() {
		$module_settings = $this->get_settings_for_display();
		$columns = isset($module_settings['instaexpl_layout_columns']) ? $module_settings['instaexpl_layout_columns'] : 5;
		$columns_tab = isset($module_settings['instaexpl_layout_columns_tablet']) ? $module_settings['instaexpl_layout_columns_tablet'] : 3;
		$columns_mob = isset($module_settings['instaexpl_layout_columns_mobile']) ? $module_settings['instaexpl_layout_columns_mobile'] : 1;
		
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
			//"data_nombre"		=> $module_settings['instaexpl_item_nombre'],
			"data_sort"			=> $module_settings['instaexpl_item_sort'],
			"data_style"		=> $module_settings['instaexpl_wrapper_style'],
			"data_layout"    	=> in_array($module_settings['instaexpl_layout_type'], ['masonry', 'fitRows']) ? $module_settings['instaexpl_layout_type'] : 'fitRows',
			"data_photo_size"	=> $module_settings['instaexpl_photo_size'],
			"data_length"		=> $module_settings['instaexpl_caption_length'],
			"data_lightbox"		=> $module_settings['instaexpl_image_lightbox'] === 'yes' ? true : false,
			"data_video"		=> $module_settings['instaexpl_post_video_count'] === 'yes' ? true : false,
			"data_caption"		=> $module_settings['instaexpl_post_caption'] === 'yes' ? true : false,
			"data_comments"		=> $module_settings['instaexpl_post_comments_count'] === 'yes' ? true : false,
			"data_likes"		=> $module_settings['instaexpl_post_likes_count'] === 'yes' ? true : false,
			"data_hashtag"		=> $module_settings['instaexpl_post_hashtag_count'] === 'yes' ? true : false,
			"data_date"			=> $module_settings['instaexpl_post_date'] === 'yes' ? true : false,
			"data_link"			=> $module_settings['instaexpl_post_link'] === 'yes' ? true : false,
		);
		
		$settings = json_encode($settings);
		return $settings;
	}
	
	protected function content_template() {}
	
}