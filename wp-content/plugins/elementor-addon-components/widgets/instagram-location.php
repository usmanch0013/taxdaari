<?php

/*========================================================================
* Class: Instagram_Location_Widget
* Name: Lieu Instagram
* Slug: eac-addon-instagram-location
*
* Description: Instagram_Location_Widget affiche les posts 
* stockées sur instagram par leuirs coordonnées (lat, lng)
*
* @since 1.4.0
* @since 1.4.1	Layout type masonry & grid
* @since 1.6.0	Correctif ajout de la lib jqCloud
* @since 1.6.1	Modification du label 'Vidéo/Diaporama'
*				Suppression du paragraphe Vidéo/Diaporama
*				dans la div 'insta-location__container-hidden-content'
* @since 1.8.7	Support des custom breakpoints
*=========================================================================*/

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

class Instagram_Location_Widget extends Widget_Base {

    /*
    * Retrieve widget name.
    *
    * @access public
    *
    * @return string widget name.
    */
    public function get_name() {
        return 'eac-addon-instagram-location';
    }

    /*
    * Retrieve widget title.
    *
    * @access public
    *
    * @return string widget title.
    */
    public function get_title() {
        return __("Lieu Instagram", 'eac-components');
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
		return ['isotope-js', 'eac-jqcloud', 'eac-leaflet', 'eac-imagesloaded', 'eac-instagram-location'];
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
		return ['eac-instagram-location', 'eac-jqcloud', 'eac-leaflet'];
	}
	
    /*
    * Register widget controls.
    *
    * Adds different input fields to allow the user to change and customize the widget settings.
    *
    * @access protected
    */
    protected function register_controls() {
		
		$this->start_controls_section('instalocation_settings',
			[
				'label'     => __('Lieux Instagram', 'eac-components'),
			]
		);
			
			$repeater = new Repeater();
			
			$repeater->add_control('instalocation_item_title',
				[
					'label'   => __('Titre', 'eac-components'),
					'type'    => Controls_Manager::TEXT,
				]
			);
			
			$repeater->add_control('instalocation_item_loc',
				[
					'label'	=> __('Identifiant', 'eac-components'),
					'type'	=> Controls_Manager::TEXT,
					'placeholder' => 'Location',
				]
			);
			
			$this->add_control('instalocation_tags_list',
				[
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => [
						[
							'instalocation_item_title'	=> 'Paris, France',
							'instalocation_item_loc'	=> '6889842',
						],
						[
							'instalocation_item_title'	=> 'New York, New York',
							'instalocation_item_loc'	=> '212988663',
						],
						[
							'instalocation_item_title'	=> 'Venice - Banksy',
							'instalocation_item_loc'	=> '292286488342988',
						],
						[
							'instalocation_item_title'	=> 'Melbourne - Banksy',
							'instalocation_item_loc'	=> '1756167187997251',
						],
						[
							'instalocation_item_title'	=> 'London - Banksy',
							'instalocation_item_loc'	=> '230186693',
						],
						[
							'instalocation_item_title'	=> 'Paris - Banksy',
							'instalocation_item_loc'	=> '439952106847528',
						],
						[
							'instalocation_item_title'	=> 'Suisse - Glacier Aletsch',
							'instalocation_item_loc'	=> '220121137',
						],
						[
							'instalocation_item_title'	=> 'Argentine - Glacier Perito Moreno',
							'instalocation_item_loc'	=> '504885426',
						],
						[
							'instalocation_item_title'	=> 'France - Mer de glace',
							'instalocation_item_loc'	=> '243351346375833',
						],
						[
							'instalocation_item_title'	=> 'France - Glacier du Taconnaz',
							'instalocation_item_loc'	=> '965587303',
						],
						[
							'instalocation_item_title'	=> 'Islande - Glacier Vatnajökull',
							'instalocation_item_loc'	=> '236559293',
						],
						[
							'instalocation_item_title'	=> 'Groenland - Glacier Jakobshavn',
							'instalocation_item_loc'	=> '662200831',
						],
						[
							'instalocation_item_title'	=> 'Groenland - Glacier Russel',
							'instalocation_item_loc'	=> '1014880879',
						],
						[
							'instalocation_item_title'	=> 'Antarctique - Glacier Thwaites',
							'instalocation_item_loc'	=> '116619285052443',
						],
						[
							'instalocation_item_title'	=> 'Norvège - Glacier Austfonna',
							'instalocation_item_loc'	=> '618752578',
						],
						[
							'instalocation_item_title'	=> 'Nouvelle-Zélande - Glacier Fox',
							'instalocation_item_loc'	=> '330215',
						],
						[
							'instalocation_item_title'	=> 'Nouvelle-Zélande - Glacier Tasman',
							'instalocation_item_loc'	=> '373648641',
						],
						[
							'instalocation_item_title'	=> 'Karakoram - Glacier du Baltoro',
							'instalocation_item_loc'	=> '1500709613391706',
						],
						[
							'instalocation_item_title'	=> 'Népal - Glacier Khumbu',
							'instalocation_item_loc'	=> '266611389',
						],
						[
							'instalocation_item_title'	=> 'Alaska - Glacier Hubbard',
							'instalocation_item_loc'	=> '292327923',
						],
					],
					'title_field' => '{{{ instalocation_item_title }}}',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('instalocation_items_content',
				[
					'label'     => __('Contenu', 'eac-components'),
				]
			);
			
			/*
			$this->add_control('instalocation_item_nombre',
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
			$this->add_control('instalocation_caption_length',
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
			
			$this->add_control('instalocation_item_sort',
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
			
			$this->add_control('instalocation_post_caption',
				[
					'label'        => __('Montrer la légende', 'eac-components'),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __('oui', 'eac-components'),
					'label_off'    => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default'      => '',
				]
			);
			
			$this->add_control('instalocation_post_likes_count',
				[
					'label'        => __('Nombre de Likes', 'eac-components'),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __('oui', 'eac-components'),
					'label_off'    => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			);
			
			$this->add_control('instalocation_post_comments_count',
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
			$this->add_control('instalocation_post_video_count',
				[
					'label'        => __('Téléchargement vidéo', 'eac-components'),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __('oui', 'eac-components'),
					'label_off'    => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			);
			
			$this->add_control('instalocation_post_hashtag_count',
				[
					'label'        => __('Nombre de hashtags', 'eac-components'),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __('oui', 'eac-components'),
					'label_off'    => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			);
			
			$this->add_control('instalocation_post_date',
				[
					'label'        => __('Date de publication', 'eac-components'),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __('oui', 'eac-components'),
					'label_off'    => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default'      => '',
				]
			);
			
			$this->add_control('instalocation_post_link',
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
			
			$this->add_control('instalocation_photo_size',
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
			
			$this->add_control('instalocation_image_lightbox',
				[
					'label' => __("Visionneuse", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
					'condition' => ['instalocation_post_link' => 'yes'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('instalocation_layout_settings',
			[
				'label' => __('Disposition', 'eac-components'),
			]
		);
			
			// @since 1.4.1 Layout type masonry & grid
			$this->add_control('instalocation_layout_type',
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
					} else if($breakpoint_name === Breakpoints_manager::BREAKPOINT_KEY_MOBILE_EXTRA) {
						$columns_device_args[$breakpoint_name] = ['default' => '1'];
					} else {
						$columns_device_args[$breakpoint_name] = ['default' => '2'];
					}
				}
			}
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('instalocation_layout_columns',
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
		
		$this->start_controls_section('instalocation_general_style',
			[
				'label'      => __('Global', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);
		
			$this->add_control('instalocation_wrapper_style',
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
			
			$this->add_control('instalocation_wrapper_bg_color',
				[
					'label' => __('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#f6d365',
					'selectors' => [ '{{WRAPPER}} .eac-insta-location' => 'background-color: {{VALUE}};' ],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('instalocation_img_style',
			[
               'label' => __("Image", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('instalocation_item_margin',
				[
					'label' => __('Marge entre les images', 'eac-components'),
					'type'  => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 5,	'unit' => 'px'],
					'range' => ['px' => ['min' => -1, 'max' => 10, 'step' => 2]],
					'selectors' => ['{{WRAPPER}} .insta-location__item-content' => 'margin: {{SIZE}}{{UNIT}};'],
				]
			);
			
			$this->add_control('instalocation_img_border_radius',
				[
					'label' => __('Rayon de la bordure (%)', 'eac-components'),
					'type' => Controls_Manager::DIMENSIONS,
					'allowed_dimensions' => ['top', 'right', 'bottom', 'left'],
					'default' => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => '%', 'isLinked' => true],
					'selectors' => ['{{WRAPPER}} .insta-location__item-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
				]
			);
			
			$this->add_control('instalocation_img_bg_color',
				[
					'label' => __('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#fff',
					'selectors' => ['{{WRAPPER}} .insta-location__item-content.style-0,
									{{WRAPPER}} .insta-location__item-content.style-1,
									{{WRAPPER}} .insta-location__item-content.style-2,
									{{WRAPPER}} .insta-location__item-content.style-3,
									{{WRAPPER}} .insta-location__item-content.style-4,
									{{WRAPPER}} .insta-location__item-content.style-5,
									{{WRAPPER}} .insta-location__item-content.style-6,
									{{WRAPPER}} .insta-location__item-content.style-7,
									{{WRAPPER}} .insta-location__item-content.style-8' => 'background-color: {{VALUE}};'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('instalocation_caption_style',
			[
               'label' => __("Légende", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['instalocation_post_caption' => 'yes'],
			]
		);
			
			$this->add_control('instalocation_caption_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => [ '{{WRAPPER}} .insta-location__item-description p' => 'color: {{VALUE}};' ],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'instalocation_caption_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .insta-location__item-description p',
				]
			);
			
		$this->end_controls_section();
		
		/**
		 * Pictograms Style Section
		 */
		$this->start_controls_section('instalocation_icon_style',
			[
				'label'      => __('Pictogrammes', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
			]
		);
		
			$this->add_control('instalocation_icon_color',
				[
					'label'  	=> __('Couleur', 'eac-components'),
					'type'   	=> Controls_Manager::COLOR,
					'default'	=> "#919CA7",
					'selectors' => ['{{WRAPPER}} .insta-location__meta-item span, {{WRAPPER}} .insta-location__header-content span i' => 'color: {{VALUE}} !important'],
				]
			);
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('instalocation_icon_size',
				[
					'label' => __("Dimension (px)", 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 20,	'unit' => 'px'],
					'range' => ['px' => ['min' => 10, 'max' => 30, 'step' => 2]],
					'conditions' => [
						'relation' => 'or',
						'terms' => [
							['name' => 'instalocation_post_comments_count', 'operator' => '===', 'value' => 'yes'],
							['name' => 'instalocation_post_likes_count', 'operator' => '===', 'value' => 'yes'],
							['name' => 'instalocation_post_date', 'operator' => '===', 'value' => 'yes'],
							['name' => 'instalocation_post_link', 'operator' => '===', 'value' => 'yes'],
						]
					],
					'selectors'  => ['{{WRAPPER}} .insta-location__meta-item' => 'font-size: {{SIZE}}{{UNIT}}'],
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
		if(! $settings['instalocation_tags_list']) {
			return;
		}
		$class = vsprintf('insta-location desktop-column-%s tablet-column-%s mobile-column-%s', $this->init_settings());
		$this->add_render_attribute('insta_location', 'class', $class);
		$this->add_render_attribute('insta_location', 'data-settings', $this->get_settings_json());
		
		?>
		<div class="eac-insta-location">
			<?php $this->render_galerie(); ?>
			<div <?php echo $this->get_render_attribute_string('insta_location'); ?>>
				<div class="insta-location__item-sizer"></div>
			</div>
			<div class="insta-location__error"></div>
			<div class="eac__button">
				<button id="insta-location__read-button-next" class="eac__read-button"><?php _e("Plus d'articles", 'eac-components'); ?><span class="insta-location__read-button-next-paged">0</span></button>
			</div>
			<div id="insta-location__loader-wheel-next" class="eac__loader-spin"></div>
		</div>
		<?php
    }
	
    protected function render_galerie() {
		$settings = $this->get_settings_for_display();
		
		?>
		<div class="insta-location__select-item-list">
			<div class="insta-location__options-items-info">
				<h2><?php echo __("Photos d'un lieu publiées sur Instagram", 'eac-components'); ?></h2>
				<p><?php echo __('Les pictogrammes des images affichent plus de contenu', 'eac-components'); ?></p>
			</div>
			<div class="insta-location__container-cb">
				<label  class="insta-location__items-cb-label" for="insta-location__items-cb">
					<input type="checkbox" id="insta-location__items-cb" name="insta-location__checkbox" value="1">
					<span><?php echo __('Top publications', 'eac-components'); ?></span>
				</label>
			</div>
			<div class="insta-location__options-items-list">
				<select id="insta-location__options-items" class="insta-location__options-items">
					<?php foreach($settings['instalocation_tags_list'] as $key => $item) { ?>
						<?php if(! empty($item['instalocation_item_loc'])) : ?>
					        <option value="<?php echo $item['instalocation_item_loc']; ?>"><?php echo $item['instalocation_item_title']; ?></option>
						<?php endif; ?>
					<?php } ?>
				</select>
			</div>
			<div class="insta-location__select-item">
				<input type="text" id="insta-location__item-name" name="insta-location__item-name" required minlength="4" maxlength="100" size="20">
			</div>
			<div class="eac__button">
				<button id="insta-location__read-button" class="eac__read-button"><?php _e('Lire le flux', 'eac-components'); ?></button>
			</div>
			<div id="insta-location__loader-wheel" class="eac__loader-spin"></div>
		</div>
		<div class="insta-location__header"></div>
		<div class="insta-location__container-hidden-content">
			<p class="insta-location__hd-mention"><?php echo __("Hashtags associés à la photo", "eac-components"); ?></p>
			<p class="insta-location__hd-likes"><?php echo __("Personnes qui aiment la photo", "eac-components"); ?></p>
			<p class="insta-location__hd-comments"><?php echo __("Personnes qui ont commenté la photo", "eac-components"); ?></p>
			<div class="insta-location__hidden-content"></div>
		</div>
		<div class="insta-location__container-map">
			<div id="insta-location__map" class="insta-location__map"></div>
		</div>
		<?php
	}
	
	protected function init_settings() {
		$module_settings = $this->get_settings_for_display();
		$columns = isset($module_settings['instalocation_layout_columns']) ? $module_settings['instalocation_layout_columns'] : 5;
		$columns_tab = isset($module_settings['instalocation_layout_columns_tablet']) ? $module_settings['instalocation_layout_columns_tablet'] : 3;
		$columns_mob = isset($module_settings['instalocation_layout_columns_mobile']) ? $module_settings['instalocation_layout_columns_mobile'] : 1;
		
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
	* @since     1.4.0
	*/
	
	protected function get_settings_json() {
		$module_settings = $this->get_settings_for_display();
		
		$settings = array(
			//"data_nombre"		=> $module_settings['instalocation_item_nombre'],
			"data_sort"			=> $module_settings['instalocation_item_sort'],
			"data_style"		=> $module_settings['instalocation_wrapper_style'],
			"data_layout"    	=> in_array($module_settings['instalocation_layout_type'], ['masonry', 'fitRows']) ? $module_settings['instalocation_layout_type'] : 'fitRows',
			"data_photo_size"	=> $module_settings['instalocation_photo_size'],
			"data_length"		=> $module_settings['instalocation_caption_length'],
			"data_lightbox"		=> $module_settings['instalocation_image_lightbox'] === 'yes' ? true : false,
			"data_video"		=> $module_settings['instalocation_post_video_count'] === 'yes' ? true : false,
			"data_caption"		=> $module_settings['instalocation_post_caption'] === 'yes' ? true : false,
			"data_comments"		=> $module_settings['instalocation_post_comments_count'] === 'yes' ? true : false,
			"data_likes"		=> $module_settings['instalocation_post_likes_count'] === 'yes' ? true : false,
			"data_hashtag"		=> $module_settings['instalocation_post_hashtag_count'] === 'yes' ? true : false,
			"data_date"			=> $module_settings['instalocation_post_date'] === 'yes' ? true : false,
			"data_link"			=> $module_settings['instalocation_post_link'] === 'yes' ? true : false,
		);
		
		$settings = json_encode($settings);
		return $settings;
	}
	
	protected function content_template() {}
	
}