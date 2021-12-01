<?php

/*===========================================================================
* Class: Image_Ronde_Widget
* Name: Image ronde
* Slug: eac-addon-image-ronde
*
* Description: Image_Ribbon_Widget affiche une image de forme ronde
* avec lien sur une page et une visionneuse embarquée
*
* @since 0.0.9
* @since 1.7.80	Migration du contol 'ICON' par le nouveau control 'ICONS'
* @since 1.8.7	Support des custom breakpoints
*============================================================================*/
 
namespace EACCustomWidgets\Widgets;

use EACCustomWidgets\Includes\Eac_Tools_Util;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Border;
use Elementor\Utils;

if (! defined('ABSPATH')) exit; // Exit if accessed directly

class Image_Ronde_Widget extends Widget_Base {

    /*
    * Retrieve widget name.
    *
    * @access public
    *
    * @return widget name.
    */
    public function get_name() {
        return 'eac-addon-image-ronde';
    }

   /*
    * Retrieve widget title.
    *
    * @access public
    *
    * @return widget title.
    */
    public function get_title() {
        return __("Image ronde", 'eac-components');
    }

    /*
    * Retrieve widget icon.
    *
    * @access public
    *
    * @return widget icon.
    */
    public function get_icon() {
        return 'eicon-hypster';
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
		return ['eac-image-ronde'];
	}
	
    /**
     * Register widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @access protected
     */
    protected function register_controls() {
		
		$this->start_controls_section('ir_image_settings',
			[
				'label'	=> __('Image', 'eac-components'),
				'tab'	=> Controls_Manager::TAB_CONTENT,
			]
		);
			
			$this->add_control('ir_image_content',
				[
					'label' => __("Choix de l'image", 'eac-components'),
					'type' => Controls_Manager::MEDIA,
					'dynamic' => [
						'active' => true,
					],
					'default' => [
						'url'	=> Utils::get_placeholder_image_src(),
					],
				]
			);
			
			$this->add_control('ir_image_align',
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
					'selectors' => [
						'{{WRAPPER}} .image-ronde .image-wrapper, {{WRAPPER}} .image-ronde .icon-wrapper, {{WRAPPER}} .image-ronde .titre-wrapper' => 'text-align: {{VALUE}};',
					],
					'default' => 'center',
				]
			);
		
			$this->add_group_control(
			Group_Control_Image_Size::get_type(),
				[
					'name' => 'ir_image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `ir_image_size` and `ir_image_custom_dimension`.
					'default' => 'custom',
				]
			);
			
			$this->add_control('ir_image_border_radius',
				[
					'label' => __('Rayon de la bordure (%)', 'eac-components'),
					'type' => Controls_Manager::DIMENSIONS,
					'allowed_dimensions' => ['top', 'right', 'bottom', 'left'],
					'default' => ['top' => 50, 'right' => 50, 'bottom' => 50, 'left' => 50, 'unit' => '%', 'isLinked' => true],
					'selectors' => ['{{WRAPPER}} .image-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
				]
			);
		
		$this->end_controls_section();
		
		$this->start_controls_section('ir_titre_settings',
			[
				'label'	=> __('Titre', 'eac-components'),
				'tab'	=> Controls_Manager::TAB_CONTENT,
			]
		);
			
			$this->add_control('ir_title',
				[
				'label'			=> __('Titre', 'eac-components'),
				'placeholder'	=> __('Renseigner le titre', 'eac-components'),
				'type'			=> Controls_Manager::TEXT,
				'default'		=> __("Titre du composant", 'eac-components'),
				'label_block'	=> false,
				'separator' => 'before',
				]
			);
			
			$this->add_control('ir_title_tag',
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
						'p'		=> 'p',
                    ],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('ir_links_settings',
			[
  				'label' => __("Liens", 'eac-components'),
				'tab'	=> Controls_Manager::TAB_CONTENT,
			]
		);
			
			$this->add_control('ir_link_to',
				[
					'label' => __('Type de lien', 'eac-components'),
					'type' => Controls_Manager::SELECT,
					'default' => 'none',
					'options' => [
						'none' => __('Aucun', 'eac-components'),
						'custom' => __('URL', 'eac-components'),
						'file' => __('Fichier média', 'eac-components'),
					],
				]
			);
			
			$this->add_control('ir_link_switcher',
				[
					'label' => __("Le lien est global", 'eac-components'),
					'description'	=> __("Lien global au composant", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
					'condition' => ['ir_link_to!' => 'none'],
				]
			);
			
			$this->add_control('ir_link_url',
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
					'condition' => ['ir_link_to' => 'custom'],
				]
			);
			
			$this->add_control('ir_link_page',
				[
					'label' => __('Lien de page', 'eac-components'),
					'type' => Controls_Manager::SELECT,
					'default' => '',
					'options' => Eac_Tools_Util::get_pages_by_name(),
					'condition' => ['ir_link_to' => 'file'],
				]
			);
			
			// Ajout ou non d'un icône
			$this->add_control('ir_icon_switcher',
				[
					'label' => __("Pictogramme du lien", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'separator' => 'before',
					'condition' => ['ir_link_to!' => 'none', 'ir_link_switcher!' => 'yes'],
				]
			);
			
			/** 1.7.80 Utilisation du control ICONS */
			$this->add_control('ir_icon_for_url_new',
				[
					'label' => __("Choix du pictogramme", 'eac-components'),
					'type' => Controls_Manager::ICONS,
					'fa4compatibility' => 'ir_icon_for_url',
					'default' => [
						'value' => 'fas fa-plus-square',
						'library' => 'solid',
					],
					'condition' => ['ir_icon_switcher' => 'yes', 'ir_link_switcher!' => 'yes', 'ir_link_to!' => 'none'],
				]
			);
			
			$this->add_control('ir_lightbox',
				[
					'label' => __("Visionneuse", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					//'condition' => ['ir_icon_switcher' => 'yes'],
				]
			);
		$this->end_controls_section();
		
		$this->start_controls_section('ir_image_section_style',
			[
               'label' => __("Image", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_group_control(
			Group_Control_Border::get_type(),
				[
					'name' => 'ir_image_border',
					'fields_options' => [
						'border' => ['default' => 'solid'],
						'width' => [
							'default' => [
								'top' => 10,
								'right' => 10,
								'bottom' => 10,
								'left' => 10,
								'isLinked' => true,
							],
						],
						'color' => ['default' => '#7fadc5'],
					],
					'selector' => '{{WRAPPER}} .image-ronde figure img, {{WRAPPER}} .image-ronde figure img:hover',
				]
			);
			
			$this->add_control('hover_animation',
				[
					'label' => __("Animation", 'eac-components'),
					'type' => Controls_Manager::HOVER_ANIMATION,
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('ir_titre_section_style',
			[
               'label' => __("Titre", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
			$this->add_responsive_control('ir_titre_margin',
				[
					'label' => __('Espacement', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['em', 'px'],
					'default' => ['size' => 1, 'unit' => 'em'],
					'range' => [
						'em' => ['min' => 0, 'max' => 5, 'step' => 0.1],
						'px' => ['min' => 0, 'max' => 100, 'step' => 5],
					],
					'selectors' => ['{{WRAPPER}} .titre-wrapper' => 'padding-top: {{SIZE}}{{UNIT}};'],
				]
			);
			
			$this->add_control('ir_titre_color',
				[
					'label' => __("Couleur", 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'default' => '#000',
					'separator' => 'none',
					'selectors' => ['{{WRAPPER}} .titre-wrapper-format' => 'color: {{VALUE}};'],
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_1,
					],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'ir_titre_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .titre-wrapper-format',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('ir_icon_section_style',
			[
               'label' => __("Pictogrammes", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'conditions' => [
					'relation' => 'or',
					'terms' => [
						['name' => 'ir_icon_switcher', 'operator' => '===', 'value' => 'yes'],
						['name' => 'ir_lightbox', 'operator' => '===', 'value' => 'yes'],
					],
				],
			]
		);
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('ir_icon_size',
				[
					'label' => __("Dimension (px)", 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 40,	'unit' => 'px'],
					'range' => ['px' => ['min' => 20, 'max' => 70, 'step' => 5]],
					'separator' => 'none',
					'selectors' => ['{{WRAPPER}} .image-ronde .icon-wrapper i' => 'font-size: {{SIZE}}{{UNIT}};'],
				]
			);
			
			$this->add_control('ir_icon_color',
				[
					'label' => __("Couleur", 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'default' => '#ffc72f',
					'separator' => 'none',
					'selectors' => ['{{WRAPPER}} .image-ronde .icon-wrapper i' => 'color: {{VALUE}}; border-color: {{VALUE}};'],
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_1,
					],
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
		if(empty($settings['ir_image_content']['url'])) { return; }
		?>
		<div class="eac-image-ronde">
			<div class="image-ronde">
				<?php $this->render_galerie(); ?>
			</div>
		</div>
		<?php
    }
	
    protected function render_galerie() {
		$settings = $this->get_settings_for_display();
		$html = '';
		$title_tag = $settings['ir_title_tag'];
		$open_title = '<'. $title_tag .' class="titre-wrapper-format">';
		$close_title = '</'. $title_tag .'>';
		$link_switcher = $settings['ir_link_switcher'];
		$icon_switcher = $settings['ir_icon_switcher'];
		$link_url = '';
		$link_icon = false;
		$link_lightbox = false;
		
		// l'image src et class
		if(! empty($settings['ir_image_content']['url'])) {
			$image_url = esc_url($settings['ir_image_content']['url']);
			$this->add_render_attribute('ir_image_content', 'src', $image_url);
			if($settings['hover_animation']) {
				$this->add_render_attribute('ir_image_content', 'class', 'elementor-animation-' . $settings['hover_animation']);
			}
			$image_alt = Control_Media::get_image_alt($settings['ir_image_content']);
			$this->add_render_attribute('ir_image_content', 'alt', $image_alt);
			$this->add_render_attribute('ir_image_content', 'title', Control_Media::get_image_title($settings['ir_image_content']));
		}
		
		// les liens
		if($settings['ir_link_to'] == 'custom') {
			$link_url = esc_url($settings['ir_link_url']['url']);
            $this->add_render_attribute('ir-link-to', 'href', $link_url);
			
            if($settings['ir_link_url']['is_external']) {
                $this->add_render_attribute('ir-link-to', 'target', '_blank');
            }

            if($settings['ir_link_url']['nofollow']) {
                $this->add_render_attribute('ir-link-to', 'rel', 'nofollow');
            }
        } elseif($settings['ir_link_to'] == 'file') {
			$link_url = $settings['ir_link_page'];
            $this->add_render_attribute('ir-link-to', 'href', esc_url(get_permalink(get_page_by_title($link_url))));
		}
		
		/** 1.7.80 Migration du control ICONS */
		if('yes' === $icon_switcher) {
			if(! empty($settings['ir_icon_for_url_new'])) {
				$link_icon = true;
				
				// Check if its already migrated
				$migrated = isset($settings['__fa4_migrated']['ir_icon_for_url_new']);
				// Check if its a new widget without previously selected icon using the old Icon control
				$is_new = empty($settings['ir_icon_for_url']);
				if($is_new || $migrated) {
					$this->add_render_attribute('icon', 'class', $settings['ir_icon_for_url_new']['value']);
					$this->add_render_attribute('icon', 'aria-hidden', 'true');
				}
			}
		}
		
		// Une icone pour le lightbox
		if('yes' === $settings['ir_lightbox']) {
			$link_lightbox = true;
			$this->add_render_attribute('ir-lightbox', 'class', 'elementor-icon link-lightbox');
			$this->add_render_attribute('ir-lightbox', ['href' => $image_url, 'data-elementor-open-lightbox' => 'no']);
			$this->add_render_attribute('ir-lightbox', 'data-fancybox', 'ir-gallery');
			$this->add_render_attribute('ir-lightbox', 'data-caption', $image_alt);
			$this->add_render_attribute('icon-lb', 'class', 'fa fa-arrows-alt');
			$this->add_render_attribute('icon-lb', 'aria-hidden', 'true');
		}
		
		// le lien est global image/icône/titre
		$html .= '<div class="image-ronde-wrapper">';
			$html .= '<figure class="image-wrapper">';
				if('yes' === $link_switcher && ! empty($link_url)) {
					$html .= '<a '. $this->get_render_attribute_string('ir-link-to') .'>';
				}
				$html .= Group_Control_Image_Size::get_attachment_image_html($settings, 'ir_image', 'ir_image_content');
				if('yes' === $link_switcher && ! empty($link_url)) {
					$html .= '</a>';
				}
			$html .= '</figure>';
			
			if($link_icon || $link_lightbox) {
				$html .= '<div class="icon-wrapper">';
					if($link_icon && ! empty($link_url)) {
						$html .= '<a class="elementor-icon"'. $this->get_render_attribute_string('ir-link-to') .'>';
						$html .= '<i ' . $this->get_render_attribute_string('icon') . '></i>';
						$html .= '</a>';
					}
			
					if($link_lightbox) {
						$html .= '<a ' . $this->get_render_attribute_string('ir-lightbox') . '>';
						$html .= '<i ' . $this->get_render_attribute_string('icon-lb') . '></i>';
						$html .= '</a>';
					}
				$html .= '</div>';
			}
		$html .= '</div>';
		
		// Ajout du titre avec ou sans lien
		if('yes' === $link_switcher && ! empty($link_url)) { $html .= '<a '. $this->get_render_attribute_string('ir-link-to') .'>';	}
			$html .= '<span class="titre-wrapper">' . $open_title . esc_html($settings['ir_title']) . $close_title . '</span>';
		if('yes' === $link_switcher && ! empty($link_url)) { $html .= '</a>'; }
				
	echo $html;
	}
	
	protected function content_template() {}
	
}