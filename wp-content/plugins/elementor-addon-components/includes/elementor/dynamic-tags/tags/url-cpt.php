<?php

/*============================================================================================
* Class: Url_Cpts_Tag
*
*
* @return affiche la liste des URL de tous les articles personnalisées (CPT)
* @since 1.6.0
* @since 1.6.2	Exclusion des types de post Elementor, Formulaires
* @since 1.6.6	Change le GUID de l'url du CPT par 'get_permalink()'
* @since 1.8.4	Utilisation de la méthode 'get_filter_post_types' de l'objet 'Eac_Tools_Util'
*===========================================================================================*/

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

use EACCustomWidgets\Includes\Eac_Tools_Util;
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
Class Eac_Cpts_Tag extends Data_Tag {
	
	public function get_name() {
		return 'eac-addon-cpt-url-tag';
	}

	public function get_title() {
		return __('Articles personnalisés', 'eac-components');
	}

	public function get_group() {
		return 'eac-url';
	}

	public function get_categories() {
		return [TagsModule::URL_CATEGORY];
	}
	
	public function get_panel_template_setting_key() {
		return 'single_cpt_url';
	}
	
	protected function register_controls() {
		$this->add_control('single_cpt_url',
			[
				'label' => __('Articles Url', 'eac-components'),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_keys_array(),
			]
		);
	}
	
	public function get_value(array $options = []) {
		$param_name = $this->get_settings('single_cpt_url');
		if(empty($param_name)) { return ''; }
		return wp_kses_post($param_name);
	}
	
    private function get_custom_keys_array() {
        $cpttaxos = [];
        $options = array('' => __('Select...', 'eac-components'));
        $right_posttype = array_keys(Eac_Tools_Util::get_filter_post_types()); // @since 1.8.4
		
        $cpttaxos = Eac_Dynamic_Tags::get_all_cpts_url();
        if(!empty($cpttaxos)) {
			foreach($cpttaxos as $cpttaxo) {
				if(in_array($cpttaxo->post_type, $right_posttype)) {
					//$options[$cpttaxo->guid] = $cpttaxo->post_type . "::" . $cpttaxo->post_title;
					$options[esc_url(get_permalink($cpttaxo->ID))] = $cpttaxo->post_type . "::" . esc_html($cpttaxo->post_title); // @since 1.6.6
				}
            }
		}
		return $options;
    }
}