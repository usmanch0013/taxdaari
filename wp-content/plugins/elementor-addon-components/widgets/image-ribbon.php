<?php

/*=================================================================
* Class: Image_Ribbon_Widget
* Name: Ruban
* Slug: eac-addon-ribbon
*
* Description: Image_Ribbon_Widget affiche une image et un ruban
*
* @since 0.0.9
* @since 1.8.7	Support des custom breakpoints
*==================================================================*/

namespace EACCustomWidgets\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Utils;

if (! defined('ABSPATH')) exit; // Exit if accessed directly
 
class Image_Ribbon_Widget extends Widget_Base {

    /*
    * Retrieve widget name.
    *
    * @access public
    *
    * @return string widget name.
    */
    public function get_name() {
        return 'eac-addon-ribbon';
    }

    /*
    * Retrieve widget title.
    *
    * @access public
    *
    * @return string widget title.
    */
    public function get_title() {
        return __('Ruban', 'eac-components');
    }

    /*
    * Retrieve widget icon.
    *
    * @access public
    *
    * @return string widget icon.
    */
    public function get_icon() {
        return 'eicon-meta-data';
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
	
	/** 
	 * Load dependent styles
	 * Les styles sont chargés dans le footer
	 * 
	 * @access public
	 *
	 * @return CSS list.
	 */
	public function get_style_depends() {
		return ['eac-image-ribbon'];
	}
	
    /*
    * Register widget controls.
    *
    * Adds different input fields to allow the user to change and customize the widget settings.
    *
    * @access protected
    */
    protected function register_controls() {
		
        $this->start_controls_section('ribbon_global_settings',
				[
					'label'	=> __('Ruban', 'eac-components')
				]
			);
		
			$this->add_control('ribbon_position',
				[
					'label' => __('Position', 'eac-components'),
					'type' => Controls_Manager::SELECT,
					'default' => 'left',
					'options' => [
						'left' => __('Gauche', 'eac-components'),
						'right' => __('Doite', 'eac-components'),
					],
					'description' => __("En haut à doite ou à gauche", 'eac-components')
				]
			);

			$this->add_control('ribbon_text',
				[
					'label' => __('Texte', 'eac-components'),
					'type' => Controls_Manager::TEXT,
					'default' => __('Ribbon', 'eac-components'),
					'placeholder' => __('Ribbon', 'eac-components'),
					'description'	=> __('Le texte du ruban', 'eac-components')
				]
			);

			$this->add_control('ribbon_link',
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
				]
			);
		$this->end_controls_section();
		
		$this->start_controls_section('ribbon_image_settings',
			[
				'label'     => __('Image', 'eac-components'),
			]
		);
	
			$this->add_control('ribbon_image_content',
				[
					'name' => 'Ribbon image',
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
			
			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name' => 'image_size', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `Image_size_size` and `Image_size_custom_dimension`.
					'exclude' => ['custom'],
					'default' => 'full',
					'separator' => 'none',
				]
			);
			
			$this->add_control('ribbon_image_align',
                [
                    'label' => __("Alignement", 'eac-components'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left'      => [
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
					'separator' => 'none',
                    'default' => 'center',
                    'selectors' => ['{{WRAPPER}} .image-ribbon-img, {{WRAPPER}} .image-ribbon-img figure, {{WRAPPER}} .image-ribbon-img figcaption' => 'text-align: {{VALUE}};'],
                ]
            );
			
			$this->add_control('ribbon_caption',
				[
					'label' => __('Légende', 'eac-components'),
					'type' => Controls_Manager::TEXT,
					'default' => '',
					'placeholder' => __("Légende de l'image", 'eac-components'),
				]
			);
			
		$this->end_controls_section();
	
		$this->start_controls_section('ribbon_section_style',
           [
               'label' => __('Ruban', 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
           ]
		);
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('ribbon_margin',
				[
					'label' => __('Position (px)', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 35, 'unit' => 'px'],
					'range' => ['px' => ['min' => 35, 'max' => 65, 'step' => 5]],
					'selectors' => [
						'{{WRAPPER}} .image-ribbon-inner' => 'margin-top: {{SIZE}}{{UNIT}};
						-webkit-transform:translateY(-50%) translateX(-50%) translateX({{SIZE}}{{UNIT}}) rotate(-45deg);
						-ms-transform:translateY(-50%) translateX(-50%) translateX({{SIZE}}{{UNIT}}) rotate(-45deg);
						transform:translateY(-50%) translateX(-50%) translateX({{SIZE}}{{UNIT}}) rotate(-45deg);',
					],
				]
			);
			
			$this->add_control('ribbon_inner_color',
				[
					'label' => __('Couleur du texte', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_3,
					],
					'default' => '#FFF',
					'selectors' => [
						'{{WRAPPER}} .image-ribbon-inner' => 'color: {{VALUE}};'
					],
				]
			);
		
			$this->add_control('ribbon_inner_bg_color',
				[
					'label' => __('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' 	=> Color::get_type(),
						'value' => Color::COLOR_1,
					],
					'default' => '#106d00',
					'selectors' => [
						'{{WRAPPER}} .image-ribbon-inner' => 'background-color: {{VALUE}};',
					]
				]
			);
		
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'typography_ribbon_texte',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .image-ribbon-inner',
				]
			);
		
		$this->end_controls_section();
		
		$this->start_controls_section('ribbon_image_section_style',
           [
               'label' => __('Image', 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
           ]
		);
			
			$this->add_control('ribbon_image_opacity',
				[
					'label' => __('Opacité', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 1, 'unit' => 'px'],
					'range' => ['px' => ['min' => 0, 'max' => 1, 'step' => .1]],
					'selectors' => ['{{WRAPPER}} .image-ribbon-img img' => 'opacity: {{SIZE}};'],
				]
			);
			
			$this->add_control('hover_animation',
				[
					'label' => __('Animation de survol', 'eac-components'),
					'type' => Controls_Manager::HOVER_ANIMATION,
				]
			);
		
		$this->end_controls_section();
		
		$this->start_controls_section('ribbon_legende_style',
           [
               'label' => __('Légende', 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['ribbon_caption!' => ''],
           ]
		);
			
			$this->add_control('ribbon_legende_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_3,
					],
					'selectors' => [ '{{WRAPPER}} .wp-caption-text' => 'color: {{VALUE}};', '{{WRAPPER}} .wp-caption-text:after' => 'border-top: 2px solid {{VALUE}};' ],
				]
			);
			
			$this->add_control('ribbon_legende_bg_color',
				[
					'label' => __('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#FFF',
					'selectors' => ['{{WRAPPER}} .image-ribbon-img figcaption' => 'background-color: {{VALUE}};'],
				]
			);
			
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'typography_legende',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .wp-caption-text'
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
		<div class="eac-image-ribbon">
			<?php $this->render_galerie(); ?>
		</div>
		<?php
    }
	
    /*
    * <div class="image-ribbon image-ribbon-right">
	* 	<div class="image-ribbon-inner">ribbon</div>
    * </div>
	* <div class="image-ribbon-img">
	*  <img src="...." />
	* </div>
    */
    protected function render_galerie() {
		$settings = $this->get_settings_for_display();
		
		// Un lien ?
		$link = esc_url($settings['ribbon_link']['url']);
		if($link) {
            $this->add_render_attribute('ribbon-link', 'href', $link);
            // Ouverture sur un autre onglet
            if($settings['ribbon_link']['is_external']) {
                $this->add_render_attribute('ribbon-link', 'target', '_blank');
            }
			// Pas d'indexation
            if($settings['ribbon_link']['nofollow']) {
                $this->add_render_attribute('ribbon-link', 'rel', 'nofollow');
            }
        }
		
		// la position du ribbon
		$this->add_render_attribute('ribbon', 'class', "image-ribbon image-ribbon-" . $settings['ribbon_position']);
		
		// la légende
		$has_caption = ! empty($settings['ribbon_caption']);
		
		// l'animation
		$has_animation = ! empty($settings['hover_animation']);
		
		// l'image et son lien
		if(! empty($settings['ribbon_image_content']['url'])) {
			$image_url = esc_url($settings['ribbon_image_content']['url']);
			$this->add_render_attribute('ribbon_image_content', 'src', $image_url);
			$this->add_render_attribute('ribbon_image_content', 'alt', Control_Media::get_image_alt($settings['ribbon_image_content']));
			$this->add_render_attribute('ribbon_image_content', 'title', Control_Media::get_image_title($settings['ribbon_image_content']));
			if($has_animation) {
				$this->add_render_attribute('ribbon_image_content', 'class', 'elementor-animation-' . $settings['hover_animation']);
			}
		}
		
	?>
		<?php if($link) : ?>
			<a <?php echo $this->get_render_attribute_string('ribbon-link'); ?>>
		<?php endif; ?>
			<span <?php echo $this->get_render_attribute_string('ribbon'); ?>>
				<span class="image-ribbon-inner"><?php echo $settings['ribbon_text']; ?></span>
			</span>
		<?php if($link) : ?>
			</a>
		<?php endif; ?>
			
		<div class="image-ribbon-img">
			<?php if($has_caption) : ?>
			<figure>
			<?php endif; ?>
					<?php if($link) : ?>
						<a <?php echo $this->get_render_attribute_string('ribbon-link'); ?>>
					<?php endif; ?>
					<?php echo Group_Control_Image_Size::get_attachment_image_html($settings, 'image_size', 'ribbon_image_content'); ?>
					<?php if($link) : ?>
						</a>
					<?php endif; ?>
			<?php if($has_caption) : ?>
				<figcaption class="widget-image-caption wp-caption-text"><?php echo esc_html($settings['ribbon_caption']); ?></figcaption>
			</figure>
			<?php endif; ?>
		</div>
	<?php
    }
	
	protected function content_template() {}
	
}