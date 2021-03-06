<?php

/*=================================================================
* Class: Image_Diaporama_Widget
* Name: Diaporama d'arrière-plan
* Slug: eac-addon-image-diaporama
*
* Description: Image_Diaporama_Widget affiche et anime 6 background images
* 
* @since 1.0.0
* @since 1.7.0	Active les Dynamic Tags pour les images
* @since 1.8.7	Support des custom breakpoints
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
 
class Image_Diaporama_Widget extends Widget_Base {

    /*
    * Retrieve widget name.
    *
    * @access public
    *
    * @return widget name.
    */
    public function get_name() {
        return 'eac-addon-image-diaporama';
    }

    /*
    * Retrieve widget title.
    *
    * @access public
    *
    * @return widget title.
    */
    public function get_title() {
        return __("Diaporama d'arrière-plan", 'eac-components');
    }

    /*
    * Retrieve widget icon.
    *
    * @access public
    *
    * @return string Widget icon.
    */
    public function get_icon() {
        return 'eicon-slideshow';
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
		return ['eac-diaporama'];
	}
	
    /*
    * Register widget controls.
    *
    * Adds different input fields to allow the user to change and customize the widget settings.
    *
    * @access protected
    */
    protected function register_controls() {
		
		$this->start_controls_section('dia_galerie_settings',
			[
				'label'     => __('Galerie', 'eac-components'),
			]
		);
			
			$this->add_control('dia_item_attention',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => __('<label style="color:#d5dadf; font-size:12px; font-style:italic;">Attention. Six images, pas plus pas moins.</label>', 'eac-components'),
				]
			);
			
			$repeater = new Repeater();
			
			// @since 1.7.0
			$repeater->add_control('dia_item_image',
				[
					'label'   => __('Image', 'eac-components'),
					'type'    => Controls_Manager::MEDIA,
					'dynamic' => ['active' => true],
					'default' => [
						'url' => Utils::get_placeholder_image_src(),
					],
				]
			);

			$repeater->add_control('dia_item_title',
				[
					'label'   => __('Titre', 'eac-components'),
					'type'    => Controls_Manager::TEXT,
					'default' => __('Image #', 'eac-components'),
				]
			);
			
			$this->add_control('dia_image_list',
				[
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => [
						[
							'dia_item_image'       => ['url' => Utils::get_placeholder_image_src()],
							'dia_item_title'       => __('Sé-ré-nité', 'eac-components'),
						],
						[
							'dia_item_image'       => ['url' => Utils::get_placeholder_image_src()],
							'dia_item_title'       => __('Équ-a-nimité', 'eac-components'),
						],
						[
							'dia_item_image'       => ['url' => Utils::get_placeholder_image_src()],
							'dia_item_title'       => __('Rel-a-xation', 'eac-components'),
						],
						[
							'dia_item_image'       => ['url' => Utils::get_placeholder_image_src()],
							'dia_item_title'       => __('Com-pos-ition', 'eac-components'),
						],
						[
							'dia_item_image'       => ['url' => Utils::get_placeholder_image_src()],
							'dia_item_title'       => __('Qui-ét-ude', 'eac-components'),
						],
						[
							'dia_item_image'       => ['url' => Utils::get_placeholder_image_src()],
							'dia_item_title'       => __('Tranq-uil-lité', 'eac-components'),
						],
					],
					'title_field' => '{{{ dia_item_title }}}',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('dia_settings',
			[
				'label'     => __('Réglages', 'eac-components'),
			]
		);
			
			$this->add_control('dia_image_overlay',
				[
					'label' => __("Cacher le calque", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('dia_settings_height',
				[
					'label' => __("Hauteur min.", 'eac-components'),
					'type'  => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['unit' => 'px', 'size' => 450],
					'tablet_default' => ['unit' => 'px', 'size' => 300],
					'mobile_default' => ['unit' => 'px', 'size' => 200],
					'tablet_extra_default' => ['unit' => 'px', 'size' => 300],
					'mobile_extra_default' => ['unit' => 'px', 'size' => 200],
					'range' => ['px' => ['min' => 150, 'max' => 1000, 'step' => 50]],
					'selectors' => ['{{WRAPPER}} .cb-slideshow, {{WRAPPER}} .cb-slideshow:after' => 'min-height: {{SIZE}}{{UNIT}};'],
				]
			);
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('dia_settings_position',
				[
					'label' => __("Position verticale de l'image", 'eac-components'),
					'type'  => Controls_Manager::SLIDER,
					'size_units' => ['%'],
					'default' => ['unit' => '%', 'size' => 50],
					'range' => ['%' => ['min' => 0, 'max' => 100, 'step' => 5]],
					'selectors' => ['{{WRAPPER}} .cb-slideshow div span' => 'background-position: 50% {{SIZE}}%;'],
				]
			);
			
		$this->end_controls_section();
		
		/**
		 * Generale Style Section
		 */
		$this->start_controls_section('dia_section_general_style',
			[
				'label'      => __('Global', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);
		
			$this->add_control('dia_img_style',
				[
					'label'			=> __("Style", 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'default'		=> 'style-1',
					'options'       => [
						'style-1' => 'Style 1',
                        'style-2' => 'Style 2',
                        'style-3' => 'Style 3',
						'style-4' => 'Style 4',
                    ],
				]
			);
			
		$this->end_controls_section();		
		
		$this->start_controls_section('dia_titre_section_style',
			[
               'label' => __("Titre", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('dia_title_tag',
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
                    ],
				]
			);
			
			$this->add_control('dia_titre_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => ['{{WRAPPER}} .cb-slideshow div div :first-child' => 'color: {{VALUE}};'],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'dia_titre_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .cb-slideshow div div :first-child',
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
		$maxItems = 6; // Max 6 items
		
		if(! $settings['dia_image_list'] || count($settings['dia_image_list']) != $maxItems) {
			return;
		}
		
		$id = "image_diaporama_" . uniqid();
		$overlay = $settings['dia_image_overlay'] === 'yes' ? ' no-after' : '';
		$this->add_render_attribute('diaporama__instance', 'class', 'diaporama cb-slideshow cb-slideshow-' . $settings['dia_img_style'] . $overlay);
		$this->add_render_attribute('diaporama__instance', 'id', $id);
		$this->add_render_attribute('diaporama__instance', 'data-settings', $this->get_settings_json($id));
		?>
		<div class="eac-image-diaporama">
			<div <?php echo $this->get_render_attribute_string('diaporama__instance'); ?>>
				<?php $this->render_galerie(); ?>
			</div>
		</div>
		<?php
    }
	
    protected function render_galerie() {
		$settings = $this->get_settings_for_display();
		$title_tag = $settings['dia_title_tag'];
		$open_title = '<'. $title_tag .'>';
		$close_title = '</'. $title_tag .'>';
		$maxItems = 6; // Max 6 items
		
		foreach($settings['dia_image_list'] as $key => $item) {
			if(! empty($item['dia_item_image']['url'])) { // Il y a une image
				esc_url($src = $item['dia_item_image']['url']);
				esc_html($title = $item['dia_item_title']);
				$image_title = Control_Media::get_image_alt($item['dia_item_image']);
				?>
				<div title="<?php echo $image_title; ?>">
					<span style="background-image:url(<?php echo $src; ?>)"></span>
					<div><?php echo $open_title . $title . $close_title; ?></div>
				</div>
				<?php
			}
		}
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
	
	protected function get_settings_json($id) {
		$module_settings = $this->get_settings_for_display();
		
		$settings = array(
			"data_id"		=> $id,
			"data_items"    => count($module_settings['dia_image_list']),
			"data_style"	=> $module_settings['dia_img_style'],
		);

		$settings = json_encode($settings);
		return $settings;
	}
	
	protected function content_template() {}
	
}