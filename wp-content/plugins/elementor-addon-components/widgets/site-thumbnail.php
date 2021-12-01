<?php

/*=================================================================
* Class: Site_Thumbnails_Widget
* Name: Miniature de site
* Slug: eac-addon-site-thumbnail
*
* Description: Affiche la miniature d'un site web local ou distant
* 
*
* @since 1.7.70
*==================================================================*/
 
namespace EACCustomWidgets\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Box_Shadow;

if (! defined('ABSPATH')) exit; // Exit if accessed directly

class Site_Thumbnails_Widget extends Widget_Base {

    /*
    * Retrieve widget name.
    *
    * @access public
    *
    * @return widget name.
    */
    public function get_name() {
        return 'eac-addon-site-thumbnail';
    }

   /*
    * Retrieve widget title.
    *
    * @access public
    *
    * @return widget title.
    */
    public function get_title() {
        return __("Miniature de site", 'eac-components');
    }

    /*
    * Retrieve widget icon.
    *
    * @access public
    *
    * @return widget icon.
    */
    public function get_icon() {
        return 'eicon-thumbnails-right';
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
		return ['eac-site-thumbnail'];
	}
	
	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return ['site', 'thumbnail'];
	}
	
    /**
     * Register widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @access protected
     */
    protected function register_controls() {
		
		$this->start_controls_section('st_site_settings',
			[
				'label'     => __('Réglages', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
			]
		);
			
			$this->add_control('st_site_url',
				[
					'label' => __('URL', 'eac-components'),
					'type' => Controls_Manager::URL,
					'description' => __("Coller l'URL complète/relative du site", 'eac-components'),
					'placeholder' => 'http://your-link.com',
					'dynamic' => ['active' => true],
					'default' => [
						'url' => '',
						'is_external' => true,
						'nofollow' => true,
					],
				]
			);
			
			$this->add_control('st_site_url_warning',
				[
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'eac-editor-panel_warning',
					'raw'  => __("<strong>SAMEORIGIN:</strong> Certains sites interdisent le chargement de la ressource dans une iframe en dehors de leur domaine.", "eac-components"),
				]
			);
			
			$this->add_control('st_add_link',
				[
					'label' => __("Ajouter le lien vers le site", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
			
			$this->add_control('st_add_caption',
				[
					'label' => __("Ajouter une légende", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'separator' => 'before',
				]
			);
			
			$this->add_control('st_site_caption',
				[
					'label' => __("Légende", 'eac-components'),
					'type' => Controls_Manager::TEXT,
					'dynamic' => ['active' => true],
					'description' => __("Coller la légende", 'eac-components'),
					'label_block' => true,
					'condition' => ['st_add_caption' => 'yes'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('st_site_container_style',
			[
				'label'     => __('Global', 'eac-components'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('st_site_container_margin',
				[
					'label' => __('Marges', 'eac-components'),
					'type' => Controls_Manager::DIMENSIONS,
					'allowed_dimensions' => ['top', 'bottom'],
					'size_units' => ['px'],
					'default' => ['top' => 0, 'bottom' => 0, 'unit' => 'px', 'isLinked' => true],
					'range' => ['px' => ['min' => 0, 'max' => 50, 'step' => 5]],
					'selectors' => ['{{WRAPPER}} .eac-site-thumbnail' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
				]
			);
			
			$this->add_group_control(
    			Group_Control_Box_Shadow::get_type(),
    			[
    				'name' => 'st_site_container_shadow',
    				'label' => __('Ombre', 'eac-components'),
    				'selector' => '{{WRAPPER}} .site-thumbnail-container',
    			]
    		);
			
		$this->end_controls_section();
		
		$this->start_controls_section('st_site_legende_style',
			[
				'label'     => __('Légende', 'eac-components'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => ['st_add_caption' => 'yes'],
			]
		);
			
			$this->add_responsive_control('st_site_legende_margin',
				[
					'label' => __('Espacement', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 10, 'unit' => 'px'],
					'range' => ['px' => ['min' => 0, 'max' => 50, 'step' => 5]],
					'selectors' => ['{{WRAPPER}} .thumbnail-caption' => 'padding-top: {{SIZE}}{{UNIT}};'],
				]
			);
			
			$this->add_control('st_site_legende_color',
				[
					'label' => __("Couleur", 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'selectors' => ['{{WRAPPER}} .thumbnail-caption' => 'color: {{VALUE}};'],
					'scheme' => [
						'type' =>Color::get_type(),
						'value' => Color::COLOR_1,
					],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'st_site_legende_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .thumbnail-caption',
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
		if(empty($settings['st_site_url']['url'])) { return; }
		
		$has_url = $settings['st_add_link'] === 'yes' ? true : false;
		$url = esc_url($settings['st_site_url']['url']);
		$this->add_render_attribute('st-link-to', 'href', $url);
		if($settings['st_site_url']['is_external']) {
			$this->add_render_attribute('st-link-to', 'target', '_blank');
		}
		if($settings['st_site_url']['nofollow']) {
			$this->add_render_attribute('st-link-to', 'rel', 'nofollow');
		}
		
		$has_caption = $settings['st_add_caption'] === 'yes' && !empty($settings['st_site_caption']);
		?>
		<div class="eac-site-thumbnail">
			<div class="site-thumbnail-container">
				<div class="thumbnail-container" title="<?php echo $url; ?>">
					<?php if($has_url) {?>
						<a <?php echo $this->get_render_attribute_string('st-link-to'); ?>>
					<?php }?>
					<div class="thumbnail">
						<iframe src="<?php echo $url; ?>" frameborder="0" onload="var that=this;setTimeout(function() { that.style.opacity=1 }, 500)"></iframe>
					</div>
					<?php if($has_url) {?>
						</a>
					<?php }?>
					
				</div>
			</div>
			<?php if($has_caption) {?>
				<p class="thumbnail-caption"><?php echo esc_html($settings['st_site_caption']); ?></p>
			<?php }?>
		</div>
		<?php
    }
	
	protected function content_template() {}
	
}