<?php

/*============================================================================================================================
* Class: Table_Of_Content_Widget
* Name: Table des matières
* Slug: eac-addon-toc
*
* Description: Génère et formate automatiquement une Table des matières
* 
*
* @since 1.8.0
* @since 1.8.1	Ajout du control 'trailer' pour différencier les titres homonymes par un numéro d'ordre
*				Sélection des niveaux de titres
*				Choix du titre de l'ancre 'généré automatiquement' ou titre de la balise titre cible
* @since 1.8.7	Application des breakpoints
*============================================================================================================================*/

namespace EACCustomWidgets\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Icons_Manager;

if(! defined('ABSPATH')) exit; // Exit if accessed directly

class Table_Of_Contents_Widget extends Widget_Base {
	
    /*
    * Retrieve widget name.
    *
    * @access public
    *
    * @return widget name.
    */
    public function get_name() {
        return 'eac-addon-toc';
    }

    /*
    * Retrieve widget title.
    *
    * @access public
    *
    * @return widget title.
    */
    public function get_title() {
        return __("Table des matières", 'eac-components');
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
        return 'eicon-table-of-contents';
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
		return ['eac-table-content'];
	}
	
	/* 
	 * Load dependent styles
	 * 
	 * Les styles sont chargés dans le footer !!
     *
     * @return CSS list.
	 */
	public function get_style_depends() {
		return ['eac-table-content'];
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
		return ['table of content'];
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
        return 'https://elementor-addon-components.com/create-and-display-the-table-of-contents-of-your-posts/';
    }
	
    /*
    * Register widget controls.
    *
    * Adds different input fields to allow the user to change and customize the widget settings.
    *
    * @access protected
    */
	protected function register_controls() {
        
		/**
		 * Generale Content Section
		 */
		$this->start_controls_section('toc_content_settings',
			[
				'label'      => __('Réglages', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
			]
		);
			
		    $this->add_control('toc_header_title',
				[
					'label' => __("Titre", 'eac-components'),
			        'type' => Controls_Manager::TEXT,
					'default' => __("Table des Matières", 'eac-components'),
			        'dynamic' => ['active' => true],
					'label_block' => true,
				]
			);
			
			$this->add_control('toc_content_target',
				[
					'label'			=> __('Cible', 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'description'	=> __("Cible de l'analyse", 'eac-components'),
					'options'		=> [
						'body'						=> 'Body',
						'.site-content'				=> 'Site-content',
						'.site-main'				=> 'Site-main',
						'.entry-content'			=> 'Entry-content',
						'.entry-content article'	=> 'Article',
					],
					'label_block'	=>	true,
					'default'		=> 'body',
				]
			);
			
			/** @since 1.8.1 Sélection des niveaux de titres */
			$this->add_control('toc_content_heading',
				[
					'label'			=> __('Balises de titre', 'eac-components'),
					'type'			=> Controls_Manager::SELECT2,
					'options'		=> [
						'h1'		=> 'H1',
						'h2'		=> 'H2',
						'h3'		=> 'H3',
						'h4'		=> 'H4',
						'h5'		=> 'H5',
						'h6'		=> 'H6',
					],
					'label_block'	=>	true,
					'default'		=> ['h1','h2','h3','h4','h5','h6'],
					'multiple'		=> true,
				]
			);
		
		$this->end_controls_section();
		
		$this->start_controls_section('toc_content_anchor',
			[
				'label'      => __('Ancres', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
			]
		);
			
			/** @since 1.8.1 Ajout création de l'ancre automatiquement */
			$this->add_control('toc_content_anchor_auto',
				[
					'label' => __("Ancre générée automatiquement", 'eac-components'),
					'description'	=> __("'toc-heading-anchor-X' sinon le titre est utilisé", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
					'separator' => 'before',
				]
			);
			
			/** @since 1.8.1 Ajout d'un numéro d'ordre */
			$this->add_control('toc_content_anchor_trailer',
				[
					'label' => __("Ajouter un numéro de rang", 'eac-components'),
					'description'	=> __("Si les titres ne sont pas uniques dans la page", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
					'condition' => ['toc_content_anchor_auto!' => 'yes'],
				]
			);
		
		$this->end_controls_section();
		
		$this->start_controls_section('toc_content_content',
			[
				'label'      => __('Contenu', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
			]
		);
			
			$this->add_control('toc_content_toggle',
				[
					'label' => __("Réduire le contenu au chargement", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
					'separator' => 'before',
				]
			);
			
			$this->add_control('toc_content_picto',
				[
					'label' => __("Pictogramme du contenu", 'eac-components'),
					'type' => Controls_Manager::ICONS,
					'default' => ['value' => 'fas fa-arrow-right', 'library' => 'fa-solid',],
					'skin' => 'inline',
					'exclude_inline_options' => ['svg'],
				]
			);
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('toc_content_width',
				[
					'label' => __('Largeur', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['unit' => 'px', 'size' => 500],
					'range' => ['px' => ['min' => 200, 'max' => 1000, 'step' => 10]],
					'label_block' => true,
					'selectors' => ['{{WRAPPER}} #toctoc' => 'max-width: {{SIZE}}{{UNIT}}; width: 100%'],
					'separator' => 'before',
				]
			);
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('toc_content_align',
				[
					'label' => __('Alignement', 'eac-components'),
					'type' => Controls_Manager::CHOOSE,
					'default' => 'center',
					'options' => [
						'start' => [
							'title' => __('Gauche', 'eac-components'),
							'icon' => 'eicon-text-align-left',
						],
						'center' => [
							'title' => __('Centre', 'eac-components'),
							'icon' => 'eicon-text-align-center',
						],
						'end' => [
							'title' => __('Droite', 'eac-components'),
							'icon' => 'eicon-text-align-right',
						],
					],
					'selectors'	=> ['{{WRAPPER}} .eac-table-of-content' => 'justify-content: {{VALUE}};',],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('toc_header_style',
			[
				'label'      => __('TOC Entête', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('toc_header_color',
				[
					'label' => __('Couleur du titre', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_1,
					],
					'default' => '#fff',
					'selectors' => ['{{WRAPPER}} #toctoc #toctoc-head span' => 'color: {{VALUE}};',],
				]
			);
			
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'tox_header_typography',
					'label' => __('Typographie du titre', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} #toctoc #toctoc-head span',
				]
			);
			
			$this->add_control('toc_header_background_color',
				[
					'label' => __('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_2,
					],
					'default' => '#000',
					'selectors' => ['{{WRAPPER}} #toctoc #toctoc-head' => 'background-color: {{VALUE}};'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('toc_body_style',
			[
				'label'      => __('TOC Contenu', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('toc_body_color',
				[
					'label' => __('Couleur des entrées', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_1,
					],
					'default' => '#000',
					'selectors' => ['{{WRAPPER}} #toctoc #toctoc-body .link' => 'color: {{VALUE}};',],
				]
			);
			
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'tox_body_typography',
					'label' => __('Typographie des entrées', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} #toctoc #toctoc-body .link',
				]
			);
			
			$this->add_control('toc_body_background_color',
				[
					'label' => __('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_2,
					],
					'default' => '#F5F5F5',
					'selectors' => ['{{WRAPPER}} #toctoc #toctoc-body' => 'background-color: {{VALUE}};'],
				]
			);
			
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'toc_body_border',
					'selector' => '{{WRAPPER}} #toctoc #toctoc-body',
					'separator' => 'before',
				]
			);
			
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'toc_body_shadow',
					'label' => __('Ombre', 'eac-components'),
					'selector' => '{{WRAPPER}} #toctoc',
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
		$this->add_render_attribute('wrapper', 'class', 'eac-table-of-content');
		$this->add_render_attribute('wrapper', 'data-settings', $this->get_settings_json());
		?>
		<div <?php echo $this->get_render_attribute_string('wrapper') ?>>
			<div id="toctoc">
				<div id="toctoc-head">
					<span id="toctoc-title"><?php echo esc_html($settings['toc_header_title']); ?></span>
				</div>
				<div id="toctoc-body"></div>
			</div>
		</div>
		<?php
    }
	
	/*
	* get_settings_json
	*
	* Retrieve fields values to pass at the widget container
	* Convert on JSON format
	* Read by 'eac-components.js' file when the component is loaded on the frontend
	*
	* @uses		 json_encode()
	*
	* @return	 JSON oject
	*
	* @access	 protected
	* @since	 0.0.9
	*/
	protected function get_settings_json() {
		$module_settings = $this->get_settings_for_display();
		$numbering = $module_settings['toc_content_anchor_trailer'] === 'yes' ? true : false;
		
		$settings = array(
			"data_opened" => $module_settings['toc_content_toggle'] === 'yes' ? false : true,
			"data_target" => $module_settings['toc_content_target'],
			"data_fontawesome" => !empty($module_settings['toc_content_picto']['value']) ? $module_settings['toc_content_picto']['value'] : '',
			"data_title" => !empty($module_settings['toc_content_heading']) ? implode(',', $module_settings['toc_content_heading']) : 'h2',
			"data_trailer" => $module_settings['toc_content_anchor_auto'] === 'yes' ? true : $numbering,
			"data_anchor" => $module_settings['toc_content_anchor_auto'] === 'yes' ? true : false,
			"data_topmargin" => 0, //$module_settings['toc_content_margin_top']['size'],
		);
		
		$settings = json_encode($settings);
		return $settings;
	}
	
	protected function content_template() {}
	
}