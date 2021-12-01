<?php

/*=================================================================
* Class: Instagram_User_Widget
* Name: Lecteur RSS
* Slug: eac-addon-instagram-user
*
* Description: Instagram_User_Widget affiche des photos
* stockées sur instagram pour un UserAccount
*
* @since 1.3.0
* @since 1.4.1  Layout type masonry & grid
* @since 1.5.2  Traitement des Hashtags
* @since 1.6.1	Modification du label 'Vidéo/Diaporama'
*				Suppression du paragraphe Vidéo/Diaporama
*				dans la div 'insta-user__container-hidden-content'
*==================================================================*/

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

class Instagram_User_Widget extends Widget_Base {

    /*
    * Retrieve widget name.
    *
    * @access public
    *
    * @return string widget name.
    */
    public function get_name() {
        return 'eac-addon-instagram-user';
    }

    /*
    * Retrieve widget title.
    *
    * @access public
    *
    * @return string widget title.
    */
    public function get_title() {
        return __("Utilisateur Instagram", 'eac-components');
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
		return ['isotope-js', 'eac-jqcloud', 'eac-imagesloaded', 'eac-instagram-user'];
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
		return ['eac-instagram-user', 'eac-jqcloud'];
	}
	
    /*
    * Register widget controls.
    *
    * Adds different input fields to allow the user to change and customize the widget settings.
    *
    * @access protected
    */
    protected function register_controls() {
		
		$this->start_controls_section('instauser_settings',
			[
				'label'     => __('Comptes utilisateur Instagram', 'eac-components'),
			]
		);
			
			$repeater = new Repeater();
			
			$repeater->add_control('instauser_item_title',
				[
					'label'   => __('Titre', 'eac-components'),
					'type'    => Controls_Manager::TEXT,
				]
			);
			
			$repeater->add_control('instauser_item_tag',
				[
					'label'	=> __('TAG', 'eac-components'),
					'type'	=> Controls_Manager::TEXT,
					'placeholder' => 'Hashtag',
				]
			);
			
			$this->add_control('instauser_tags_list',
				[
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => [
						[
							'instauser_item_title'	=> 'Armchairs',
							'instauser_item_tag'	=> 'cestpascommode',
						],
						[
							'instauser_item_title'	=> 'Coach Instagram',
							'instauser_item_tag'	=> '3zestesdecitron',
						],
						[
							'instauser_item_title'	=> 'Marketing - Tanke',
							'instauser_item_tag'	=> 'tankeagency',
						],
						[
							'instauser_item_title'	=> 'Paris - France',
							'instauser_item_tag'	=> 'paris',
						],
						[
							'instauser_item_title'	=> 'Paris - Restaurants',
							'instauser_item_tag'	=> 'topparisresto',
						],
						[
							'instauser_item_title'	=> 'Paris - Hotels',
							'instauser_item_tag'	=> 'topparishotels',
						],
						[
							'instauser_item_title'	=> 'Paris - Musée du louvre',
							'instauser_item_tag'	=> 'museelouvre',
						],
						[
							'instauser_item_title'	=> "Paris - Musée d'Orsay",
							'instauser_item_tag'	=> 'museeorsay',
						],
						[
							'instauser_item_title'	=> "Paris - Musée Maillol",
							'instauser_item_tag'	=> 'museemaillol',
						],
						[
							'instauser_item_title'	=> "Paris - Musée d'Art Moderne",
							'instauser_item_tag'	=> 'museedartmodernedeparis',
						],
						[
							'instauser_item_title'	=> "Audiovisuel - INA",
							'instauser_item_tag'	=> 'ina.fr',
						],
						[
							'instauser_item_title'	=> "Journal - Le Monde",
							'instauser_item_tag'	=> 'lemondefr',
						],
						[
							'instauser_item_title'	=> 'London - England',
							'instauser_item_tag'	=> 'london',
						],
						[
							'instauser_item_title'	=> 'New York - MoMa',
							'instauser_item_tag'	=> 'themuseumofmodernart',
						],
						[
							'instauser_item_title'	=> 'Amsterdam - Musée Van Gogh',
							'instauser_item_tag'	=> 'vangoghmuseum',
						],
						[
							'instauser_item_title'	=> 'Londres - British Museum',
							'instauser_item_tag'	=> 'britishmuseum',
						],
						[
							'instauser_item_title'	=> 'Norvège - Munch Museum',
							'instauser_item_tag'	=> 'munchmuseet.no',
						],
						[
							'instauser_item_title'	=> 'England - Banksy',
							'instauser_item_tag'	=> 'banksy',
						],
						[
							'instauser_item_title'	=> 'Chili - San Pedro de Atacama',
							'instauser_item_tag'	=> 'portalsanpedro',
						],
						[
							'instauser_item_title'	=> 'Télévision - Arte Invitation au Voyage',
							'instauser_item_tag'	=> 'arte_invitation',
						],
						[
							'instauser_item_title'	=> 'Cinéma - Bandes annonces',
							'instauser_item_tag'	=> 'bandeannoncecinema',
						],
						[
							'instauser_item_title'	=> 'Cinéma - Films noirs',
							'instauser_item_tag'	=> 'onlyfilmnoir',
						],
						[
							'instauser_item_title'	=> 'Cinéma - Films noirs Hollywood',
							'instauser_item_tag'	=> 'filmnoirhollywood',
						],
						[
							'instauser_item_title'	=> 'Cinéma - Affiches de films détournées',
							'instauser_item_tag'	=> 'affiches_de_flims',
						],
						[
							'instauser_item_title'	=> 'Cinéma - Palette de couleurs',
							'instauser_item_tag'	=> 'colorpalette.cinema',
						],
						[
							'instauser_item_title'	=> 'Production films - Wild Side',
							'instauser_item_tag'	=> 'wildsidevideo',
						],
						[
							'instauser_item_title'	=> 'Actrice - Sigourney Weaver',
							'instauser_item_tag'	=> 'sigourney.weaver.official',
						],
						[
							'instauser_item_title'	=> 'Actrice - Tippi Hedren',
							'instauser_item_tag'	=> 'tippihedrenofficial',
						],
						[
							'instauser_item_title'	=> 'Actrice - Lauren Bacall',
							'instauser_item_tag'	=> 'laurenbogart',
						],
						[
							'instauser_item_title'	=> 'Acteur - Mads Mikkelsen',
							'instauser_item_tag'	=> 'theofficialmads',
						],
						[
							'instauser_item_title'	=> 'Acteur - Toni Servillo',
							'instauser_item_tag'	=> 'toniservillo',
						],
						[
							'instauser_item_title'	=> 'Acteur - Christopher Lloyd',
							'instauser_item_tag'	=> 'mrchristopherlloyd',
						],
						[
							'instauser_item_title'	=> "Acteur - Peter O'Toole",
							'instauser_item_tag'	=> 'thepeterotoole',
						],
						[
							'instauser_item_title'	=> 'Projet - Burning Man Project',
							'instauser_item_tag'	=> 'burningman',
						],
						[
							'instauser_item_title'	=> 'Photographer - Justin Labadie',
							'instauser_item_tag'	=> 'justintophotos',
						],
						[
							'instauser_item_title'	=> 'Hard Rock - AC/DC',
							'instauser_item_tag'	=> 'acdc',
						],
						[
							'instauser_item_title'	=> 'Chanteur - Jacques brel',
							'instauser_item_tag'	=> 'jacquesbreldaily',
						],
					],
					'title_field' => '{{{ instauser_item_title }}}',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('instauser_items_content',
				[
					'label'     => __('Contenu', 'eac-components'),
				]
			);
			
			$this->add_control('instauser_caption_length',
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
			
			$this->add_control('instauser_item_sort',
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
			
			$this->add_control('instauser_post_caption',
				[
					'label'        => __('Montrer la légende', 'eac-components'),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __('oui', 'eac-components'),
					'label_off'    => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default'      => '',
				]
			);
			
			$this->add_control('instauser_post_likes_count',
				[
					'label'        => __('Nombre de Likes', 'eac-components'),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __('oui', 'eac-components'),
					'label_off'    => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			);
			
			$this->add_control('instauser_post_comments_count',
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
			$this->add_control('instauser_post_video_count',
				[
					'label'        => __('Téléchargement vidéo', 'eac-components'),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __('oui', 'eac-components'),
					'label_off'    => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			);
			
			$this->add_control('instauser_post_place_count',
				[
					'label'        => __('Lieu/Tagged user', 'eac-components'),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __('oui', 'eac-components'),
					'label_off'    => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			);
			
			$this->add_control('instauser_post_mention_count',
				[
					'label'        => __('Mentions', 'eac-components'),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __('oui', 'eac-components'),
					'label_off'    => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			);
			
			$this->add_control('instauser_post_hashtag_count',
				[
					'label'        => __('Hashtags', 'eac-components'),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __('oui', 'eac-components'),
					'label_off'    => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			);
			
			$this->add_control('instauser_post_date',
				[
					'label'        => __('Date de publication', 'eac-components'),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __('oui', 'eac-components'),
					'label_off'    => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default'      => '',
				]
			);
			
			$this->add_control('instauser_post_link',
				[
					'label'			=> __('Icône lien Instagram', 'eac-components'),
					'description'	=> __("Désactivé: Le lien est sur l'image", 'eac-components'),
					'type'			=> Controls_Manager::SWITCHER,
					'label_on'		=> __('oui', 'eac-components'),
					'label_off'		=> __('non', 'eac-components'),
					'return_value'	=> 'yes',
					'default'		=> 'yes',
					'separator'		=> 'after',
				]
			);
			
			$this->add_control('instauser_photo_size',
				[
					'label'   => __('Dimension des images', 'eac-components'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'img_med',
					'options' => [
						'image240px'	=> __('Miniature (240px)', 'eac-components'),
						'image480px'	=> __('Basse (480px)', 'eac-components'),
						'img_med'		=> __('Moyenne (640px)', 'eac-components'),
						'thumbnail_src'	=> __('Carré (640x640)', 'eac-components'),
						'img_standard'	=> __('Haute', 'eac-components'),
					],
				]
			);

			$this->add_control('instauser_image_lightbox',
				[
					'label' => __("Visionneuse", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
					'condition' => ['instauser_post_link' => 'yes'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('instauser_layout_settings',
			[
				'label' => __('Disposition', 'eac-components'),
			]
		);
			
			// @since 1.4.1 Layout type masonry & grid
			$this->add_control('instauser_layout_type',
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
			
			$this->add_responsive_control('instauser_layout_columns',
				[
					'label'   => __('Nombre de colonnes', 'eac-components'),
					'type'    => Controls_Manager::SELECT,
					'default' => '4',
					'tablet_default' => '3',
					'mobile_default' => '2',
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
		
		$this->start_controls_section('instauser_general_style',
			[
				'label'      => __('Global', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);
		
			$this->add_control('instauser_wrapper_style',
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
			
			$this->add_control('instauser_wrapper_bg_color',
				[
					'label' => __('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#ffffff',
					'selectors' => [ '{{WRAPPER}} .eac-insta-user' => 'background-color: {{VALUE}};' ],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('instauser_img_style',
			[
               'label' => __("Image", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			// @since 1.4.1 Layout type masonry & grid
			$this->add_responsive_control('instauser_item_margin',
				[
					'label' => __('Marge entre les images', 'eac-components'),
					'type'  => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 5,	'unit' => 'px'],
					'tablet_default' => ['size' => 3, 'unit' => 'px'],
					'mobile_default' => ['size' => 1, 'unit' => 'px'],
					'range' => ['px' => ['min' => -1, 'max' => 10, 'step' => 2]],
					'selectors' => ['{{WRAPPER}} .insta-user__item-content' => 'margin: {{SIZE}}{{UNIT}};'],
				]
			);
			
			$this->add_control('instauser_img_border_radius',
				[
					'label' => __('Rayon de la bordure (%)', 'eac-components'),
					'type' => Controls_Manager::DIMENSIONS,
					'allowed_dimensions' => ['top', 'right', 'bottom', 'left'],
					'default' => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => '%', 'isLinked' => true],
					'selectors' => ['{{WRAPPER}} .insta-user__item-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
				]
			);
			
			$this->add_control('instauser_img_bg_color',
				[
					'label' => __('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#13547A',
					'selectors' => ['{{WRAPPER}} .insta-user__item-content.style-0,
									{{WRAPPER}} .insta-user__item-content.style-1,
									{{WRAPPER}} .insta-user__item-content.style-2,
									{{WRAPPER}} .insta-user__item-content.style-3,
									{{WRAPPER}} .insta-user__item-content.style-4,
									{{WRAPPER}} .insta-user__item-content.style-5,
									{{WRAPPER}} .insta-user__item-content.style-6,
									{{WRAPPER}} .insta-user__item-content.style-7,
									{{WRAPPER}} .insta-user__item-content.style-8' => 'background-color: {{VALUE}};'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('instauser_caption_style',
			[
               'label' => __("Légende", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['instauser_post_caption' => 'yes'],
			]
		);
			
			$this->add_control('instauser_caption_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => [ '{{WRAPPER}} .insta-user__item-description p' => 'color: {{VALUE}};' ],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'instauser_caption_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .insta-user__item-description p',
				]
			);
			
		$this->end_controls_section();
		
		/**
		 * Pictograms Style Section
		 */
		$this->start_controls_section('instauser_icon_style',
			[
				'label'      => __('Pictogrammes', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
			]
		);
		
			$this->add_control('instauser_icon_color',
				[
					'label'  => __('Couleur', 'eac-components'),
					'type'   => Controls_Manager::COLOR,
					'default'	=> "#919CA7",
					'selectors' =>	['{{WRAPPER}} .insta-user__meta-item span,
									{{WRAPPER}} .insta-user__header-content span i,
									{{WRAPPER}} .insta-user__tagged-user-icon i,
									{{WRAPPER}} .insta_user__place-icon i,
									{{WRAPPER}} .insta-user__download-video-icon i' => 'color: {{VALUE}};',
									'{{WRAPPER}} .insta-user__header-content .insta-user__isverified' => 'color: #00bfff;'],
				]
			);

			$this->add_responsive_control('instauser_icon_size',
				[
					'label' => __("Dimension (px)", 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 20,	'unit' => 'px'],
					'tablet_default' => ['size' => 18, 'unit' => 'px'],
					'mobile_default' => ['size' => 14, 'unit' => 'px'],
					'range' => ['px' => ['min' => 10, 'max' => 30, 'step' => 2]],
					'conditions' => [
						'relation' => 'or',
						'terms' => [
							['name' => 'instauser_post_comments_count', 'operator' => '===', 'value' => 'yes'],
							['name' => 'instauser_post_likes_count', 'operator' => '===', 'value' => 'yes'],
							['name' => 'instauser_post_place_count', 'operator' => '===', 'value' => 'yes'],
							['name' => 'instauser_post_date', 'operator' => '===', 'value' => 'yes'],
							['name' => 'instauser_post_link', 'operator' => '===', 'value' => 'yes'],
						]
					],
					'selectors'  => ['{{WRAPPER}} .insta-user__meta-item' => 'font-size: {{SIZE}}{{UNIT}};'],
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
		if(! $settings['instauser_tags_list']) {
			return;
		}
		$class = vsprintf('insta-user desktop-column-%s tablet-column-%s mobile-column-%s', $this->init_settings());
		$this->add_render_attribute('insta_user', 'class', $class);
		$this->add_render_attribute('insta_user', 'data-settings', $this->get_settings_json());
		
		?>
		<div class="eac-insta-user">
			<?php $this->render_galerie(); ?>
			<div <?php echo $this->get_render_attribute_string('insta_user'); ?>>
				<div class="insta-user__item-sizer"></div>
			</div>
			<div class="insta-user__container-jqcloud">
				<p><?php echo __("Le tag sélectionné sera enregistré dans un cookie pour améliorer votre expérience utilisateur", 'eac-components'); ?></p>
			    <div class="insta-user__jqcloud"></div>
			</div>
			<div class="insta-user__error"></div>
			<div class="eac__button">
				<button id="insta-user__read-button-next" class="eac__read-button"><?php _e("Plus d'articles", 'eac-components'); ?>
					<span class="insta-user__read-button-next-paged">0</span>
				</button>
			</div>
			<div id="insta-user__loader-wheel-next" class="eac__loader-spin"></div>
		</div>
		<?php
    }
	
    protected function render_galerie() {
		$settings = $this->get_settings_for_display();
		
		?>
		<div class="insta-user__select-item-list">
			<div class="insta-user__options-items-info">
				<h2><?php echo __("Photos Instagram publiées d'un compte utilisateur", 'eac-components'); ?></h2>
				<p><?php echo __('Les pictogrammes des images affichent plus de contenu', 'eac-components'); ?></p>
			</div>
			<div class="insta-user__container-cb">
				<label  class="insta-user__items-cb-label" for="insta-user__items-cb">
					<input type="checkbox" id="insta-user__items-cb" name="insta-user__checkbox" value="1">
					<span><?php echo __('Top publications', 'eac-components'); ?></span>
				</label>
				<label class="insta-user__items-cb2-label" for="insta-user__items-cb2">
					<input type="checkbox" id="insta-user__items-cb2" name="insta-user__checkbox" value="1">
					<span><?php echo __('@Mentions', 'eac-components'); ?></span>
				</label>
				<!-- @since 1.5.2  Traitement des Hashtags -->
				<label class="insta-user__items-cb3-label" for="insta-user__items-cb3">
					<input type="checkbox" id="insta-user__items-cb3" name="insta-user__checkbox" value="1">
					<span><?php echo __('#Nuage de tags', 'eac-components'); ?></span>
				</label>
			</div>
			<div class="insta-user__options-items-list">
				<select id="insta-user__options-items" class="insta-user__options-items">
					<?php foreach($settings['instauser_tags_list'] as $key => $item) { ?>
						<?php if(! empty($item['instauser_item_tag'])) : ?>
					        <option value="<?php echo $item['instauser_item_tag']; ?>"><?php echo $item['instauser_item_title']; ?></option>
						<?php endif; ?>
					<?php } ?>
				</select> 
			</div>
			<div class="insta-user__select-item">
				<input type="text" id="insta-user__item-name" name="insta-user__item-name" required minlength="4" maxlength="100" size="20">
			</div>
			<div class="eac__button">
				<button id="insta-user__read-button" class="eac__read-button"><?php _e('Lire le flux', 'eac-components'); ?></button>
			</div>
			<div id="insta-user__loader-wheel" class="eac__loader-spin"></div>
		</div>
		<div class="insta-user__header"></div>
		<div class="insta-user__container-hidden-content">
			<p class="insta-user__hd-taggeduser"><?php echo __("Personnes identifiées dans la photo", "eac-components"); ?></p>
			<p class="insta-user__hd-mention"><?php echo __("Personnes mentionnées dans l'article", "eac-components"); ?></p>
			<p class="insta-user__hd-hashtag"><?php echo __("Hashtags associés à la photo", "eac-components"); ?></p>
			<p class="insta-user__hd-likes"><?php echo __("Personnes qui aiment la photo", "eac-components"); ?></p>
			<p class="insta-user__hd-comments"><?php echo __("Personnes qui ont commenté la photo", "eac-components"); ?></p>
			<p class="insta-user__hd-suggested"><?php echo __("Comptes utilisateurs suggérés", "eac-components"); ?></p>
			<p class="insta-user__hd-taggedpost"><?php echo __("Articles qui identifient ce compte", "eac-components"); ?></p>
			<p class="insta-user__hd-stories"><?php echo __("Stories", "eac-components"); ?></p>
			<div class="insta-user__hidden-content"></div>
		</div>
		<div class="eac__container-head-button">
			<span id="insta-user__stories" class="eac__head-button"><p><?php echo __('Stories', 'eac-components'); ?></p></span>
			<span id="insta-user__suggested-account" class="eac__head-button"><p><?php echo __('Suggestions', 'eac-components'); ?></p></span>
			<span id="insta-user__tagged-posts" class="eac__head-button"><p><?php echo __('Tagged Posts', 'eac-components'); ?></p></span>
		</div>
		<?php
	}
	
	protected function init_settings() {
		$module_settings = $this->get_settings_for_display();
		$columns = isset($module_settings['instauser_layout_columns']) ? $module_settings['instauser_layout_columns'] : 5;
		$columns_tab = isset($module_settings['instauser_layout_columns_tablet']) ? $module_settings['instauser_layout_columns_tablet'] : 3;
		$columns_mob = isset($module_settings['instauser_layout_columns_mobile']) ? $module_settings['instauser_layout_columns_mobile'] : 1;
		
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
			"data_sort"			=> $module_settings['instauser_item_sort'],
			"data_style"		=> $module_settings['instauser_wrapper_style'],
			"data_layout"    	=> in_array($module_settings['instauser_layout_type'], ['masonry', 'fitRows']) ? $module_settings['instauser_layout_type'] : 'fitRows',
			"data_photo_size"	=> $module_settings['instauser_photo_size'],
			"data_length"		=> $module_settings['instauser_caption_length'],
			"data_lightbox"		=> $module_settings['instauser_image_lightbox'] === 'yes' ? true : false,
			"data_video"		=> $module_settings['instauser_post_video_count'] === 'yes' ? true : false,
			"data_place"		=> $module_settings['instauser_post_place_count'] === 'yes' ? true : false,
			"data_caption"		=> $module_settings['instauser_post_caption'] === 'yes' ? true : false,
			"data_comments"		=> $module_settings['instauser_post_comments_count'] === 'yes' ? true : false,
			"data_likes"		=> $module_settings['instauser_post_likes_count'] === 'yes' ? true : false,
			"data_mention"		=> $module_settings['instauser_post_mention_count'] === 'yes' ? true : false,
			"data_hashtag"		=> $module_settings['instauser_post_hashtag_count'] === 'yes' ? true : false,
			"data_date"			=> $module_settings['instauser_post_date'] === 'yes' ? true : false,
			"data_link"			=> $module_settings['instauser_post_link'] === 'yes' ? true : false,
		);
		
		$settings = json_encode($settings);
		return $settings;
	}
	
	protected function content_template() {}
	
}