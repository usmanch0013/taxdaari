<?php

/*================================================================================
* Class: Images_Comparison_Widget
* Name: Comparaison d'images
* Slug: eac-addon-images-comparison
*
* Description: Images_Comparison_Widget affiche deux images à titre de comparaison
*
* @since 0.0.9
* @since 1.7.0	Active les Dynamic Tags pour les images
* @since 1.8.7	Refonte complète de l'interface
*=================================================================================*/
 
namespace EACCustomWidgets\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Control_Media;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

if(! defined('ABSPATH')) exit; // Exit if accessed directly

class Images_Comparison_Widget extends Widget_Base {

    /*
    * Retrieve widget name.
    *
    * @access public
    *
    * @return widget name.
    */
    public function get_name() {
        return 'eac-addon-images-comparison';
    }

    /*
    * Retrieve widget title.
    *
    * @access public
    *
    * @return widget title.
    */
    public function get_title() {
        return __("Comparaison d'images", 'eac-components');
    }

    /*
    * Retrieve widget icon.
    *
    * @access public
    *
    * @return widget icon.
    */
    public function get_icon() {
        return 'eicon-image-before-after';
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
		return ['eac-images-comparison', 'eac-imagesloaded'];
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
		return ['eac-images-comparison'];
	}
	
    /*
    * Register widget controls.
    *
    * Adds different input fields to allow the user to change and customize the widget settings.
    *
    * @access protected
    */
    protected function register_controls() {
        
		$this->start_controls_section('ic_gallery_content_left',
				[
					'label'     => __('Image de gauche', 'eac-components'),
					'tab'	=> Controls_Manager::TAB_CONTENT,
				]
		);
			
			// @since 1.7.0
			$this->add_control('ic_img_content_modified',
				[
					'name' => 'img_modified',
					'label' => __("Image", 'eac-components'),
					'type' => Controls_Manager::MEDIA,
					'dynamic' => ['active' => true],
					'default'       => [
						'url'	=> Utils::get_placeholder_image_src(),
					],
					'separator' => 'before',
				]
			);
			
			$this->add_control('ic_img_name_original',
				[
					'name' => 'name_original',
					'label' =>  __("Étiquette", 'eac-components'),
					'type' => Controls_Manager::TEXT,
					'default' => __('Étiquette de gauche', 'eac-components'),
					'placeholder' => __('Gauche', 'eac-components'),
					'label_block' => true,
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('ic_gallery_content_right',
				[
					'label'     => __('Image de droite', 'eac-components'),
					'tab'	=> Controls_Manager::TAB_CONTENT,
				]
		);
			
			// @since 1.7.0
			$this->add_control('ic_img_content_original',
				[
					'name' => 'img_original',
					'label' => __("Image", 'eac-components'),
					'type' => Controls_Manager::MEDIA,
					'dynamic' => ['active' => true],
					'default'       => [
						'url'	=> Utils::get_placeholder_image_src(),
					],
					'separator' => 'before',
				]
			);
			
			$this->add_control('ic_img_name_modified',
				[
					'name' => 'name_modified',
					'label' => __("Étiquette", 'eac-components'),
					'type' => Controls_Manager::TEXT,
					'default' => __('Étiquette de droite', 'eac-components'),
					'placeholder' => __('Droite', 'eac-components'),
					'label_block'   => true,
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('ic_gallery_content_size',
				[
					'label'     => __('Réglages', 'eac-components'),
					'tab'	=> Controls_Manager::TAB_CONTENT,
				]
		);
			
			// @since 1.8.7 Ajout de la taille de l'image
			$this->add_group_control(
			Group_Control_Image_Size::get_type(),
				[
					'name' => 'ic_image_size',
					'default' => 'medium',
					'exclude' => ['medium_large'],
				]
			);
			
			// @since 1.8.7 Ajout de l'aligneùent du container
			/*$this->add_control('ic_image_alignement',
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
					'selectors_dictionary' => [
						'left' => '0 auto 0 0',
						'center' => '0 auto',
						'right' => '0 0 0 auto',
					],
					'selectors' => ['{{WRAPPER}} .eac-images-comparison' => 'margin: {{VALUE}};'],
				]
			);*/
			
		$this->end_controls_section();
		
		/**
		 * Generale Style Section
		 */
		$this->start_controls_section('etiquette_section_style',
			[
               'label' => __("Étiquettes", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('ic_etiquette_color',
				[
					'label' => __("Couleur du texte", 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_3,
					],
					'default' => '#FFF',
					'selectors' => [
						'{{WRAPPER}} .b-diff__title_before, {{WRAPPER}} .b-diff__title_after' => 'color: {{VALUE}};',
					],
					'separator' => 'none',
				]
			);
			
			$this->add_control('ic_etiquette_bgcolor',
				[
					'label' => __('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' 	=> Color::get_type(),
						'value' => Color::COLOR_1,
					],
					'default' => '#919ca7',
					'selectors' => [
						'{{WRAPPER}} .b-diff__title_before, {{WRAPPER}} .b-diff__title_after' => 'background-color: {{VALUE}};',
					]
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'ic_etiquette_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .b-diff__title_before, {{WRAPPER}} .b-diff__title_after',
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
		
		if(empty($settings['ic_img_content_original']['url']) || empty($settings['ic_img_content_modified']['url'])) {	return;	}
		
		$id = "a" . uniqid();
		$this->add_render_attribute('data_diff', 'class', 'images-comparison');
		$this->add_render_attribute('data_diff', 'data-diff', $id);
		$this->add_render_attribute('data_diff', 'data-settings', $this->get_settings_json($id));
	?>
		<div class="eac-images-comparison">
			<div <?php echo $this->get_render_attribute_string('data_diff'); ?>>
				<?php $this->render_galerie(); ?>
			</div>
		</div>
		
	<?php
    }
	
	protected function render_galerie() {
		$settings = $this->get_settings_for_display();
		
		/*if($settings['ic_image_size_size'] === 'custom') { console_log($settings['ic_image_size_custom_dimension']['width']); }
		else { console_log($settings['ic_image_size_size']); }*/
		?>
		<div>
			<?php echo Group_Control_Image_Size::get_attachment_image_html($settings, 'ic_image_size', 'ic_img_content_original'); ?>
		</div>
		<div>
			<?php echo Group_Control_Image_Size::get_attachment_image_html($settings, 'ic_image_size', 'ic_img_content_modified'); ?>
		</div>
		<?php
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
	 * @access	protected
 	 * @since 0.0.9
	 * @since 1.0.7
	 * @since 1.8.7	Passe les titres en paramètres au javascript
	 */
	protected function get_settings_json($ordre) {
		$module_settings = $this->get_settings_for_display();
		
		$settings = array(
			"data_diff" => "[data-diff=" . $ordre . "]",
			"data_title_left" => esc_html($module_settings['ic_img_name_original']),
			"data_title_right" => esc_html($module_settings['ic_img_name_modified']),
		);
		
		$settings = json_encode($settings);
		return $settings;
	}
	
	protected function content_template() {}
}