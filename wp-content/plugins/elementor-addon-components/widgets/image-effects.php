<?php

/*===========================================================================
* Class: Image_Effects_Widget
* Name: Effets d'image
* Slug: eac-addon-image-effects
*
* Description: Image_Effects_Widget affiche et anime des images
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
use Elementor\Utils;

if (! defined('ABSPATH')) exit; // Exit if accessed directly

class Image_Effects_Widget extends Widget_Base {

    /*
    * Retrieve widget name.
    *
    * @access public
    *
    * @return string widget name.
    */
    public function get_name() {
        return 'eac-addon-image-effects';
    }

    /*
    * Retrieve widget title.
    *
    * @access public
    *
    * @return string widget title.
    */
    public function get_title() {
        return __("Effets d'image", 'eac-components');
    }

    /*
    * Retrieve widget icon.
    *
    * @access public
    *
    * @return string widget icon.
    */
    public function get_icon() {
        return 'eicon-image-rollover';
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
		return ['eac-image-effects'];
	}
	
    /*
    * Register widget controls.
    *
    * Adds different input fields to allow the user to change and customize the widget settings.
    *
    * @access protected
    */
    protected function register_controls() {
		
		$this->start_controls_section('ie_image_settings',
			[
				'label'	=> __('Image', 'eac-components'),
				'tab'	=> Controls_Manager::TAB_CONTENT,
			]
		);
	
			$this->add_control('ie_image_content',
				[
					'label' => __("Choix de l'image", 'eac-components'),
					'type' => Controls_Manager::MEDIA,
					'dynamic' => ['active' => true],
					'default' => ['url'	=> Utils::get_placeholder_image_src()],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('ie_texte_content',
			[
  				'label' => __("Titre et texte", 'eac-components'),
				'tab'	=> Controls_Manager::TAB_CONTENT,
			]
		);
        
			$this->add_control('ie_title',
				[
				'label'			=> __('Titre', 'eac-components'),
				'placeholder'	=> __('Renseigner le titre', 'eac-components'),
				'type'			=> Controls_Manager::TEXT,
				'default'		=> __("Effets d'Image", 'eac-components'),
				'label_block'	=> false,
				]
			);
        
			$this->add_control('ie_title_tag',
				[
					'label'			=> __('Étiquette de titre', 'eac-components'),
					'description'	=> __('Sélectionner une étiquette pour le titre.', 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'default'		=> 'h2',
					'options'       => [
						'h1'    => 'H1',
                        'h2'    => 'H2',
                        'h3'    => 'H3',
                        'h4'    => 'H4',
                        'h5'    => 'H5',
                        'h6'    => 'H6',
                    ],
					'label_block'	=> true
				]
			);
        
        
			$this->add_control('ie_description_hint',
				[
					'label'			=> __('Description', 'eac-components'),
					'type'			=> Controls_Manager::HEADING,
				]
			);
        
			$this->add_control('ie_description',
				[
					'description'	=> __("Résumé", 'eac-components'),
					'type'			=> Controls_Manager::TEXTAREA,
					'default'		=> __("Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components'),
					'placeholder' => __('Votre texte', 'eac-components'),
					'separator' => 'none',
					'label_block'	=> true
				]
			);
        
		$this->end_controls_section();
		
		$this->start_controls_section('ie_links',
			[
  				'label' => __("Liens", 'eac-components'),
				'tab'	=> Controls_Manager::TAB_CONTENT,
			]
		);
			
			$this->add_control('ie_link_to',
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
			
			$this->add_control('ie_link_url',
				[
					'label' => __('URL', 'eac-components'),
					'type' => Controls_Manager::URL,
					'dynamic' => ['active' => true],
					'placeholder' => 'http://your-link.com',
					'default' => [
						'url' => '',
						'is_external' => true,
						'nofollow' => true,
					],
					'condition' => ['ie_link_to' => 'custom'],
				]
			);
			
			$this->add_control('ie_link_page',
				[
					'label' => __('Lien de page', 'eac-components'),
					'type' => Controls_Manager::SELECT,
					'default' => '',
					'options' => Eac_Tools_Util::get_pages_by_name(),
					'condition' => ['ie_link_to' => 'file'],
				]
			);
			
			/** 1.7.80 Utilisation du control ICONS */
			$this->add_control('ie_icon_for_url_new',
				[
					'label' => __("Choix du pictogramme", 'eac-components'),
					'type' => Controls_Manager::ICONS,
					'fa4compatibility' => 'ie_icon_for_url',
					'default' => [
						'value' => 'fas fa-plus-square',
						'library' => 'solid',
					],
					'condition' => ['ie_link_to!' => 'none'],
				]
			);
			
			$this->add_control('ie_lightbox',
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
		
		$this->start_controls_section('ie_image_effects_section_style',
			[
               'label' => __("Image", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
			$this->add_control('ie_image_animation',
				[
					'label'			=> __('Effet', 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'default'		=> 'view-first',
					'description'	=> __("Sélectionner l'effet d'image", 'eac-components'),
					'options'		=> [
						'view-first'	=> __('Effet 1', 'eac-components'),
						'view-second'	=> __('Effet 2', 'eac-components'),
						'view-third'	=> __('Effet 3', 'eac-components'),
						'view-fourth'	=> __('Effet 4', 'eac-components'),
						'view-fifth'	=> __('Effet 5', 'eac-components'),
						'view-sixth'	=> __('Effet 6', 'eac-components'),
						'view-seventh'	=> __('Effet 7', 'eac-components'),
						'view-eighth'	=> __('Effet 8', 'eac-components'),
						'view-tenth'	=> __('Effet 10', 'eac-components')
					]
				]
			);
		
		$this->end_controls_section();
		
		$this->start_controls_section('ie_overlay_section_style',
			[
               'label' => __("Calque", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('ie_overlay_position',
				[
					'label'			=> __('Position Texte/Liens', 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'default'		=> 'center',
					'options'       => [
						'top'		=> __('Haut', 'eac-components'),
                        'center'	=> __('Centre', 'eac-components'),
                        'bottom'	=> __('Bas', 'eac-components'),
                    ],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('ie_position_titre_section_style',
			[
               'label' => __("Titre", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('ie_titre_margin',
				[
					'label' => __('Position verticale (%)', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['%'],
					'default' => ['size' => 25,	'unit' => '%'],
					'range' => ['%' => ['min' => 0, 'max' => 100, 'step' => 5]],
					'selectors' => ['{{WRAPPER}} .ie-protected-font-size' => 'top: {{SIZE}}%; transform: translateY(-{{SIZE}}%);'],
				]
			);
			
			$this->add_control('ie_titre_align',
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
						'{{WRAPPER}} .ie-protected-font-size' => 'text-align: {{VALUE}};',
					],
					'default' => 'center',
				]
			);
			
			$this->add_control('ie_titre_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_3,
					],
					'default' => '#ffc72f',
					'selectors' => [
						'{{WRAPPER}} .ie-protected-font-size' => 'color: {{VALUE}};',
					],
					'separator' => 'none',
				]
			);
			
			$this->add_control('ie_bg_color',
				[
					'label' => __('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' 	=> Color::get_type(),
						'value' => Color::COLOR_1,
					],
					'default' => '#919ca7',
					'selectors' => [
						'{{WRAPPER}} .ie-protected-font-size' => 'background-color: {{VALUE}};',
					]
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'ie_titre_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .ie-protected-font-size',
				]
			);
			
		$this->end_controls_section();
			
		$this->start_controls_section('ie_position_texte_section_style',
			[
               'label' => __("Texte", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('ie_texte_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#FFF',
					'selectors' => [
						'{{WRAPPER}} .view-effect p' => 'color: {{VALUE}};'
					],
					'separator' => 'none',
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'ie_texte_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .view-effect p',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('ie_icon_section_style',
			[
               'label' => __("Pictogrammes", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['ie_link_to!' => 'none'],
			]
		);
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('ie_icon_size',
				[
					'label' => __("Dimension (px)", 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 40,	'unit' => 'px'],
					'tablet_default' => ['size' => 35, 'unit' => 'px'],
					'mobile_default' => ['size' => 40, 'unit' => 'px'],
					'tablet_extra_default' => ['size' => 35, 'unit' => 'px'],
					'mobile_extra_default' => ['size' => 40, 'unit' => 'px'],
					'range' => ['px' => ['min' => 20, 'max' => 70, 'step' => 5]],
					'selectors' => ['{{WRAPPER}} .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};'],
				]
			);
			
			$this->add_control('ie_icon_color',
				[
					'label' => __("Couleur", 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'default' => '#ffc72f',
					'separator' => 'none',
					'selectors' => [
						'{{WRAPPER}} .elementor-icon' => 'color: {{VALUE}}; border-color: {{VALUE}};',
					],
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
		if(empty($settings['ie_image_content']['url'])) {
			return;
		}
		
		$this->add_render_attribute('wrapper', 'class', 'view-effect ' . $settings['ie_image_animation']);
	?>
		<div class="eac-image-effects">
			<div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
				<?php $this->render_galerie(); ?>
			</div>
		</div>
	<?php
	}
	
	protected function render_galerie() {
		$settings = $this->get_settings_for_display();
		$title_tag = $settings['ie_title_tag'];
		$open_title = '<'. $title_tag .' class="ie-protected-font-size">';
		$close_title = '</'. $title_tag .'>';
		$link_lightbox = false;
		$link_url = '';
		
		// l'image src et class
		if(! empty($settings['ie_image_content']['url'])) {
			$image_url = esc_url($settings['ie_image_content']['url']);
			$this->add_render_attribute('ie_image_content', 'src', $image_url);
			
			$image_alt = Control_Media::get_image_alt($settings['ie_image_content']);
			$this->add_render_attribute('ie_image_content', 'alt', $image_alt);
			$this->add_render_attribute('ie_image_content', 'title', Control_Media::get_image_title($settings['ie_image_content']));
		}
		
		// les liens
		if($settings['ie_link_to'] === 'custom') {
			$link_url = esc_url($settings['ie_link_url']['url']);
            $this->add_render_attribute('ie-link-to', 'href', $link_url);
            $this->add_render_attribute('ie-link-to', 'class', 'info-effect');
			
            if($settings['ie_link_url']['is_external']) {
                $this->add_render_attribute('ie-link-to', 'target', '_blank');
            }

            if($settings['ie_link_url']['nofollow']) {
                $this->add_render_attribute('ie-link-to', 'rel', 'nofollow');
            }
        } elseif ($settings['ie_link_to'] === 'file') {
			$link_url = $settings['ie_link_page'];
            $this->add_render_attribute('ie-link-to', 'href', esc_url(get_permalink(get_page_by_title($link_url))));
            $this->add_render_attribute('ie-link-to', 'class', 'info-effect');
		}	
		
		if('yes' === $settings['ie_lightbox']) {
			$link_lightbox = true;
			$this->add_render_attribute('ie-lightbox', 'class', 'info-effect elementor-icon link-lightbox');
			$this->add_render_attribute('ie-lightbox', ['href' => $image_url, 'data-elementor-open-lightbox' => 'no']);
			$this->add_render_attribute('ie-lightbox', 'data-fancybox', 'ie-gallery');
			$this->add_render_attribute('ie-lightbox', 'data-caption', $image_alt);
			$this->add_render_attribute('icon-lb', 'class', 'fa fa-arrows-alt');
			$this->add_render_attribute('icon-lb', 'aria-hidden', 'true');
		}
		
		/** 1.7.80 Migration du control ICONS */
		if(! empty($settings['ie_icon_for_url_new'])) {
			$this->add_render_attribute('ie-link-to', 'class', 'elementor-icon');
			
			// Check if its already migrated
			$migrated = isset($settings['__fa4_migrated']['ie_icon_for_url_new']);
			
			// Check if its a new widget without previously selected icon using the old Icon control
			$is_new = empty($settings['ie_icon_for_url']);
			
			if($is_new || $migrated) {
				$this->add_render_attribute('icon', 'class', $settings['ie_icon_for_url_new']['value']);
				$this->add_render_attribute('icon', 'aria-hidden', 'true');
			}
		}
		
		// Position de l'overlay
		$overlay_pos = 'mask-content-position ' . $settings['ie_overlay_position'];
	?>
		<figure>
			<?php echo Group_Control_Image_Size::get_attachment_image_html($settings, '', 'ie_image_content'); ?>
		</figure>
		<?php echo $open_title; ?><?php echo esc_html($settings['ie_title']); ?><?php echo $close_title; ?>
		<div class="mask-effect">
			<div class="<?php echo $overlay_pos; ?>">
				<p><?php echo esc_html($settings['ie_description']); ?></p>
				<?php if($link_url) : ?>
					<a <?php echo $this->get_render_attribute_string('ie-link-to'); ?>>
						<i <?php echo $this->get_render_attribute_string('icon'); ?>></i>
					</a>
				<?php endif; ?>
				<?php if($link_lightbox) : ?>
					<a <?php echo $this->get_render_attribute_string('ie-lightbox'); ?>>
						<i <?php echo $this->get_render_attribute_string('icon-lb'); ?>></i>
					</a>
				<?php endif; ?>
			</div>
		</div>
		
	<?php
    }
	
	protected function content_template() {}
	
}