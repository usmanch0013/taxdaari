<?php

/*===============================================================================
* Class: Eac_Chart_Tag
*
* 
* @return l'URL absolue d'un fichier MEDIA au format 'txt'
* Utilisé essentielement par le composant Chart
* @since 1.6.0
*===============================================================================*/

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

use EACCustomWidgets\Includes\Elementor\DynamicTags\Eac_Dynamic_Tags;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;

if(!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Post Url
 */
Class Eac_Chart_Tag extends Data_Tag {

	public function get_name() {
		return 'eac-addon-chart-url-tag';
	}

	public function get_title() {
		return __('Diagrammes', 'eac-components');
	}

	public function get_group() {
		return 'eac-url';
	}

	public function get_categories() {
		return [TagsModule::URL_CATEGORY];
	}
    
    public function get_panel_template_setting_key() {
		return 'chart_json_url';
	}
	
	protected function register_controls() {
		$this->add_control('chart_json_url',
			[
				'label' => __('Diagramme Url', 'eac-components'),
				'type' => Controls_Manager::SELECT,
				'options' => Eac_Dynamic_Tags::get_all_chart_url(),
			]
		);
	}
	
	protected function get_value(array $options = []) {
	    $param_name = $this->get_settings('chart_json_url');
		return wp_kses_post($param_name);
	}
}