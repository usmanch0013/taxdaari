<?php

/*========================================================================================================
* Class: Acf_Relationship_Widget
* Name: ACF Relationship
* Slug: eac-addon-acf-relationship
*
* Description: Affiche et formate les entrées sélectionnées dans le champ Relationship ou Post object
* d'un articles. Les articles sont affichées sous forme de grille.
*
* 
* @since 1.8.2
* @since 1.8.5	Fix: ACF field 'Select multiple values' === 'no' pour le champ 'post_object'
*				Force le changement du type de données en array
* @since 1.8.7	Support des custom breakpoints
*========================================================================================================*/

namespace EACCustomWidgets\Widgets;

use EACCustomWidgets\Includes\Eac_Tools_Util;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Breakpoints\Manager as Breakpoints_manager;
use Elementor\Plugin;

if(! defined('ABSPATH')) exit; // Exit if accessed directly

class Acf_Relationship_Widget extends Widget_Base {
	
    /*
    * Retrieve widget name.
    *
    * @access public
    *
    * @return widget name.
    */
    public function get_name() {
        return 'eac-addon-acf-relationship';
    }

    /*
    * Retrieve widget title.
    *
    * @access public
    *
    * @return widget title.
    */
    public function get_title() {
        return __("ACF Relationship", 'eac-components');
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
        return 'eicon-post-list';
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
	
	/* 
	 * Load dependent styles
	 * 
	 * Les styles sont chargés dans le footer !!
     *
     * @return CSS list.
	 */
	public function get_style_depends() {
		return ['eac-acf-relation'];
	}
	
	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.7.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return ['acf', 'relation ship', 'post object', 'grid'];
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
        return 'https://elementor-addon-components.com/how-to-display-acf-relationship-posts-in-a-grid';
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
		
		/**
		 * Generale content Section
		 */
        $this->start_controls_section('acf_relation_settings',
			[
				'label'     => __('Réglages', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
			]
		);
			
			$this->add_control('acf_relation_settings_origine',
				[
					'label' => __('Champ relationnel', 'eac-components'),
					'type' => Controls_Manager::SELECT,
					'options' => $this->get_acf_fields_options($this->get_acf_supported_fields(), get_the_ID()),
					'label_block' => true,
				]
			);
			
			$this->add_control('acf_relation_settings_include_type',
				[
					'label' => __("Sélectionner les types d'articles", 'eac-components'),
					'type' => Controls_Manager::SELECT2,
					'options' => Eac_Tools_Util::get_filter_post_types(),
					'default' => ['post', 'page'],
					'multiple' => true,
					'label_block' => true,
				]
			);
			
			$this->add_control('acf_relation_settings_nombre',
				[
					'label' => __("Nombre d'articles", 'eac-components'),
					'description' => __('-1 = Tous','eac-components'),
					'type' => Controls_Manager::NUMBER,
					'default' => 3,
				]
			);
			
			/*$this->add_control('acf_relation_settings_duplicates',
				[
					'label' => __("Conserver les doublons", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);*/
			
		$this->end_controls_section();
		
		$this->start_controls_section('acf_relation_content',
			[
				'label'     => __('Contenu', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
			]
		);
			
			/*$this->add_control('acf_relation_content_parent',
				[
					'label' => __("Le titre de l'article parent", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);*/
			
			$this->add_control('acf_relation_content_date',
				[
					'label' => __("Date", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('acf_relation_content_excerpt',
				[
					'label' => __("Résumé", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('acf_relation_content_image',
				[
					'label' => __("Image en avant", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('acf_relation_image',
			[
               'label' => __("Image", 'eac-components'),
               'tab' => Controls_Manager::TAB_CONTENT,
			   'condition' => ['acf_relation_content_image' => 'yes'],
			]
		);
			
			$this->add_control('acf_relation_content_image_dimension',
				[
					'label'   => __('Dimension', 'eac-components'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'medium',
					'options'       => [
						'thumbnail'		=> __('Miniature', 'eac-components'),
						'medium'		=> __('Moyenne', 'eac-components'),
						'medium_large'	=> __('Moyenne-large', 'eac-components'),
						'large'			=> __('Large', 'eac-components'),
						'full'			=> __('Originale', 'eac-components'),
					],
					
				]
			);
			
			$this->add_control('acf_relation_content_image_link',
				[
					'label' => __("Lien de l'article sur l'image", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('acf_relation_title',
			[
               'label' => __("Titre", 'eac-components'),
               'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
			
			$this->add_control('acf_relation_title_tag',
				[
					'label'			=> __('Étiquette', 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'default'		=> 'h3',
					'options'       => [
						'h1'    => 'H1',
                        'h2'    => 'H2',
                        'h3'    => 'H3',
                        'h4'    => 'H4',
                        'h5'    => 'H5',
                        'h6'    => 'H6',
						'div'	=> 'div',
						'p'		=> 'p',
                    ],
				]
			);
			
			$this->add_control('acf_relation_title_align',
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
					'default' => 'center',
					'toggle' => true,
					'selectors' => [
						'{{WRAPPER}} .acf-relation_title, {{WRAPPER}} .acf-relation_title-parent' => 'text-align: {{VALUE}};',
					],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('acf_relation_excerpt',
			[
               'label' => __("Résumé", 'eac-components'),
               'tab' => Controls_Manager::TAB_CONTENT,
			   'condition' => ['acf_relation_content_excerpt' => 'yes'],
			]
		);
			
			$this->add_control('acf_relation_excerpt_length',
				[
					'label' => __('Nombre de mots', 'eac-components'),
					'type'  => Controls_Manager::NUMBER,
					'min' => 10,
					'max' => 100,
					'step' => 5,
					'default' => apply_filters('excerpt_length', 25), /** Ce filtre est documenté dans wp-includes/formatting.php */
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('acf_relation_layout',
			[
				'label' => __('Disposition', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
			]
		);
			
			
			// @since 1.8.7 Add default values for all active breakpoints.
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
			
			/**
			 * 'prefix_class' ne fonctionnera qu'avec les flexbox
			 * @since 1.8.7 Application des breakpoints
			 */
			$this->add_responsive_control('acf_relation_layout_columns',
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
				]
			);
			
		$this->end_controls_section();
		
		/**
		 * Generale Style Section
		 */
		$this->start_controls_section('acf_relation_general_style',
			[
				'label'      => __('Global', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('acf_relation_wrapper_style',
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
						'style-9' => 'Style 9',
						'style-10' => 'Style 10',
						'style-11' => 'Style 11',
						'style-12' => 'Style 12',
                    ],
					'prefix_class' => 'acf-relation_wrapper-',
				]
			);
			
			/**
			 * @since 1.8.7 Application des breakpoints
			 */
			$this->add_responsive_control('acf_relation_wrapper_margin',
				[
					'label' => __("Marge entre les items", 'eac-components'),
					'type'  => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 10, 'unit' => 'px'],
					'mobile_extra_default' => ['size' => 5, 'unit' => 'px'],
					'range' => ['px' => ['min' => 0, 'max' => 20, 'step' => 1]],
					'selectors' => ['{{WRAPPER}} article' => 'margin-bottom: {{SIZE}}{{UNIT}};'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('acf_relation_image_style',
			[
               'label' => __("Image", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['acf_relation_content_image' => 'yes'],
			]
		);
			$this->add_control('acf_relation_image_border_radius',
				[
					'label' => __('Rayon de la bordure', 'eac-components'),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'allowed_dimensions' => ['top', 'right', 'bottom', 'left'],
					'default' => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px', 'isLinked' => true],
					'selectors' => [
						'{{WRAPPER}} .acf-relation_img img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			
			$this->add_control('acf_relation_image_style_ratio_enable',
				[
					'label' => __("Activer le ratio image", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
					'separator' => 'before',
					//'condition' => ['acf_relation_layout_texte_droite!' => 'show'],
				]
			);
			
			/**
			 * @since 1.8.7 Application des breakpoints
			 */
			$this->add_responsive_control('acf_relation_image_style_ratio',
				[
					'label' => __('Ratio', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['%'],
					'default' => ['size' => 0.6, 'unit' => '%'],
					'range' => ['%' => ['min' => 0.1, 'max' => 2, 'step' => 0.1]],
					'selectors' => ['{{WRAPPER}} .acf-relation_container.acf-relation_img-ratio .acf-relation_img' => 'padding-bottom:calc({{SIZE}} * 100%);'],
					//'condition' => ['acf_relation_image_style_ratio_enable' => 'yes', 'acf_relation_layout_texte_droite!' => 'show'],
					'condition' => ['acf_relation_image_style_ratio_enable' => 'yes'],
				]
			);
			
			/**
			 * @since 1.8.7 Application des breakpoints
			 */
			$this->add_responsive_control('acf_relation_image_ratio_position_y',
				[
					'label' => __('Position verticale', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['%'],
					'default' => ['size' => 50, 'unit' => '%'],
					'range' => ['%' => ['min' => 0, 'max' => 100, 'step' => 5]],
					'selectors' => ['{{WRAPPER}} .acf-relation_container.acf-relation_img-ratio .acf-relation_img img' => 'object-position: 50% {{SIZE}}%;'],
					//'condition' => ['acf_relation_image_style_ratio_enable' => 'yes', 'acf_relation_layout_texte_droite!' => 'show'],
					'condition' => ['acf_relation_image_style_ratio_enable' => 'yes'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('acf_relation_title_style',
			[
               'label' => __("Titre", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('acf_relation_title_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => ['{{WRAPPER}} .acf-relation_title .acf-relation_title-content' => 'color: {{VALUE}};',],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'acf_relation_title_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .acf-relation_title .acf-relation_title-content',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('acf_relation_date_style',
			[
               'label' => __("Date", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['acf_relation_content_date' => 'yes'],
			]
		);
			
			$this->add_control('acf_relation_date_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => [
						'{{WRAPPER}} .acf-relation_date' => 'color: {{VALUE}};',
					],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'acf_relation_date_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .acf-relation_date',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('acf_relation_excerpt_style',
			[
               'label' => __("Résumé", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['acf_relation_content_excerpt' => 'yes'],
			]
		);
			
			$this->add_control('acf_relation_excerpt_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => [
						'{{WRAPPER}} .acf-relation_excerpt' => 'color: {{VALUE}};',
					],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'acf_relation_excerpt_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .acf-relation_excerpt',
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
		if(empty($settings['acf_relation_settings_origine'])) { return; }
		?>
		<div class="eac-acf-relationship">
			<?php $this->get_relation_by_id(); ?>
		</div>
		<?php
	}
	
	/**
	 * get_relation_by_id
	 *
	 * 
	 *
	 * @access protected
	 */
	protected function get_relation_by_id() {
		$settings = $this->get_settings_for_display();
		$key = $settings['acf_relation_settings_origine'];
		$items = array();
		$parent_id = get_the_ID();
		$items = $this->get_relations($key, $parent_id);
		
		if(!empty($items)) {
			$this->render_relationship_content($items);
		}
	}
	
	/**
	 * get_relations
	 *
	 * Crée la liste des relationship d'un article
	 *
	 * @access protected
	 */
	protected function get_relations($key, $parent_id) {
		/**
		 * @var $items Array d'articles en relation avec l'article courant
		 */
		$items = array();
		
		/**
		 * @var $items_id Array des articles analysés par leur ID
		 */
		$items_id = array();
		
		/**
		 * @var $loop Variable pour compter le nombre de boucle
		 */
		$loop = 1;
		
		/**
		* @var $max_loops Variable pour limiter le nombre de boucle
		*
		* Nombre de boucle max pour éviter une boucle sans fin
		*/
		$max_loops = 1;
		
		$settings = $this->get_settings_for_display();
		$has_excerpt = $settings['acf_relation_content_excerpt'] === 'yes' ? true : false;
		$has_duplicate = false; //$settings['acf_relation_settings_duplicates'] === 'yes' ? true : false;
		$excerpt_length = $settings['acf_relation_excerpt_length'];
		$include_posttypes = $settings['acf_relation_settings_include_type'];
		$field_value = '';
		
		list($field_key, $meta_key) = array_pad(explode('::', $key), 2, '');
		
		if(empty($field_key)) { return; }
		
		$field = get_field_object($field_key, $parent_id);
		
		if($field && !empty($field['value'])) {
			// Le texte est à droite, on force la taille de l'image à thumbnail
			//$image_size = $settings['acf_relation_layout_texte_droite'] === 'show' ? 'thumbnail' : $settings['acf_relation_content_image_dimension'];
			$image_size = $settings['acf_relation_content_image_dimension'];
			$field_value = $field['value'];
			
			switch($field['type']) {
				case 'relationship':
				case 'post_object':
					$values = array();
					$featured = true;
					$img = '';
					if($field['type'] == 'relationship') {
						$featured = is_array($field['elements']) && !empty($field['elements'][0]) && $field['elements'][0] == 'featured_image' ? true : false;
					}
					/** @since 1.8.5 Fix cast $field_value dans le type tableau */
					$field_value = is_array($field_value) ? $field_value : array($field_value);
					
					// Première boucle on ajoute l'ID du post courant
					if($loop == 1) { $items_id[$parent_id] = $parent_id; }
					
					// Boucle sur tous les relationship posts
					foreach($field_value as $value) {
						$item = array();
						$id = $field['return_format'] == 'object' ? (int) $value->ID : (int) $value;
						
						// Le post_type n'est pas dans la liste
						if(!in_array(get_post_type($id), $include_posttypes)) { continue; }
						
						// Ne conserve pas les doublons et l'ID de l'article est déjà analysé ou c'est l'article courant
						if(!$has_duplicate && in_array($id, $items_id)) { continue; }
						
						// Enregistre les données
						$item[$id]['post_id'] = $id;
						$item[$id]['post_parent_id'] = $parent_id;
						$item[$id]['post_parent_title'] = get_post($parent_id)->post_title;
						$item[$id]['post_type'] = get_post_type($id);
						$item[$id]['post_title'] = $field['return_format'] == 'object' ? esc_html($value->post_title) : esc_html(get_post($id)->post_title);
						$item[$id]['link'] = esc_url(get_permalink(get_post($id)->ID));
						$item[$id]['img'] = $featured ? get_the_post_thumbnail($id, $image_size) : '';
						$item[$id]['post_date'] = get_the_modified_date(get_option('date_format'), $id);
						$item[$id]['post_excerpt'] = in_array(get_post_type($id), ['page', 'attachment']) || !$has_excerpt ? "[...]" : Eac_Tools_Util::get_post_excerpt($id, $excerpt_length);
						$item[$id]['class'] = esc_attr(implode(' ', get_post_class('', $id)));
						$item[$id]['id'] = 'post-' . $id;
						$item[$id]['processed'] = false;
						
						// ID du relationship post + ID du parent pour conserver les doublons
						if($has_duplicate) {
							$items[$id . '-' . $parent_id] = $item[$id];
						} else {
							$items[$id] = $item[$id];
						}
						
						// Ajout de l'ID de l'article à la liste des ID déjà analysé
						$items_id[] = $id;
						
						// Ajout d'une boucle récursive. Plus tard
						$loop++;
					}
					
					if($loop > $max_loops) { return $items; }
			
					// Boucle sur tous les items 
					foreach($items as $post_key => $post_val) {
						//$exp = $items[$post_key]['post_title']."::".$items[$post_key]['processed'];
						
						// L'article n'a pas été analysé
						if($post_val['processed'] == false) {
							$items[$post_key]['processed'] = true;
							
							// Champs ACF relationship (Field-key::Field-name) pour cet article
							$key = $this->get_acf_fields_options($this->get_acf_supported_fields(), $post_val['post_id']);
							
							// Récursivité on analyse l'ID pour chercher les articles en relationship
							if(is_array($key) && !empty($key)) {
								$this->get_relations(array_keys($key)[0], $post_val['post_id']);
							}
						}
					}
				break;
			}
		}
		
		return $items;
	}
	
	/**
	 * render_relationship_content
	 *
	 * Mis en forme des relationship mode grille
	 *
	 * @access protected
	 */
	protected function render_relationship_content($items = array()) {
		$settings = $this->get_settings_for_display();
		$has_image = $settings['acf_relation_content_image'] === 'yes' ? true : false;
		$has_ratio = $settings['acf_relation_image_style_ratio_enable'] === 'yes' ? true : false;
		$has_date = $settings['acf_relation_content_date'] === 'yes' ? true : false;
		$has_excerpt = $settings['acf_relation_content_excerpt'] === 'yes' ? true : false;
		$has_link = $settings['acf_relation_content_image_link'] === 'yes' ? true : false;
		$has_parent_title = false; //$settings['acf_relation_content_parent'] === 'yes' ? true : false;
		$nb_posts = !empty($settings['acf_relation_settings_nombre']) ? $settings['acf_relation_settings_nombre'] : -1;
		$nb_displayed = 0;
		
		// Formate le titre avec son tag
		$title_tag = $settings['acf_relation_title_tag'];
		$open_title = '<'. $title_tag .' class="acf-relation_title-content">';
		$close_title = '</'. $title_tag .'>';
		
		$id = $this->get_id();
		
		// Le wrapper du container et la class pour le ratio d'image
		$class = vsprintf("acf-relation_container %s", $has_ratio ? 'acf-relation_img-ratio' : '');
		$this->add_render_attribute('container_wrapper', 'class', $class);
		$this->add_render_attribute('container_wrapper', 'id', $id);
		$container = "<div " . $this->get_render_attribute_string('container_wrapper') . ">";
		
		$values = array();
		
		foreach($items as $item) {
			if($nb_posts != -1 && $nb_displayed >= $nb_posts) { break; }
			$value = '';
			
			$value .= "<article id='" . $item['id'] . "' class='" . $item['class'] . "'>";
					
				/** Affichage de l'image */
				if($has_image && !empty($item['img'])) {
					/** Le lien sur l'image */
					if($has_link) {
						$value .= "<div class='acf-relation_img'><a href='" . $item['link'] . "'>" . $item['img'] . "</a></div>";
					} else {
						$value .= "<div class='acf-relation_img'>" . $item['img'] . "</div>";
					}
				}
					
				/** Affichage du contenu */
				$value .= "<div class='acf-relation_content'>";
				
					/** Affichage du titre */
					$value .= "<div class='acf-relation_title'>";
						$value .= "<a href='" . $item['link'] . "'>" . $open_title . $item['post_title'] . $close_title . "</a>";
					$value .= "</div>";
					
					/** Affichage du titre du parent */
					if($has_parent_title) {
						$value .= "<div class='acf-relation_title-parent'>";
							$value .= $open_title . $item['post_parent_title'] . $close_title;
						$value .= "</div>";
					}
						
					/** Affichage de la date */
					if($has_date) {
						$value .= "<div class='acf-relation_date'>" . $item['post_date'] . "</div>";
					}
						
					/** Affichage du résumé */
					if($has_excerpt) {
						$value .= "<div class='acf-relation_excerpt'>" . $item['post_excerpt'] . "</div>";
					}
				$value .= "</div>"; // Fin div contenu
					
			$value .= "</article>"; // Fin article
			
			$values[] =  $value;
			$nb_displayed++;
		}
		echo $container . implode(' ', $values) . "</div>";
	}
	
	/**
	 * get_acf_fields_options
	 *
	 * Retourne Field_id et Field_name pour un article
	 *
	 * @access protected
	 */
	protected function get_acf_fields_options($field_type, $post_id) {
		$groups = array();
		$options = array('' => __('Select...', 'eac-components'));
		
		// Les groupes pour l'article
		$acf_groups = acf_get_field_groups(array('post_id' => $post_id));
		
		foreach($acf_groups as $group) {
			// Le groupe n'est pas désactivé
			if(!$group['active']) {
				continue;
			}
			
			if(isset($group['ID']) && !empty($group['ID'])) {
				$fields = acf_get_fields($group['ID']);
			} else {
				$fields = acf_get_fields($group);
			}
			
			// Pas de champ
			if(!is_array($fields)) {
				continue;
			}
			
			foreach($fields as $field) {
				// C'est le bon type de champ ACF
				if(!in_array($field['type'], $field_type, true)) {
					continue;
				}
				
				// Clé unique et slug comme indice du tableau
				$key = $field['key'] . '::' . $field['name'];
				$options[$key] = $group['title'] . "::" . $field['label'];
			}
		}
		
		return $options;
	}
	
	/**
	 * get_acf_supported_fields
	 *
	 * La liste des champs supportés
	 *
	 * @access protected
	 */
	protected function get_acf_supported_fields() {
		return [
		'relationship',
		'post_object',
		];
	}

	protected function content_template() {}
	
}