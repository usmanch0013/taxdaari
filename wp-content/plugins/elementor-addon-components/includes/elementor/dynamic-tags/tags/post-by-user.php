<?php

/*=========================================================================================
* Class: Eac_Post_User
*
* 
* @return un tableau d'options de la liste de tous les auteurs (display_name) par leur ID
* @since 1.6.0
*=========================================================================================*/

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

use EACCustomWidgets\Includes\Elementor\DynamicTags\Eac_Dynamic_Tags;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;

if(!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Eac_Post_User extends Tag {

	public function get_name() {
		return 'eac-addon-post-user';
	}

	public function get_title() {
		return __('Auteurs', 'eac-components');
	}

	public function get_group() {
		return 'eac-post';
	}

	public function get_categories() {
		return [
			TagsModule::POST_META_CATEGORY,
		];
	}

	public function get_panel_template_setting_key() {
		return 'author_custom_field';
	}

	protected function register_controls() {
		
		$this->add_control('author_custom_field',
			[
				'label' => __('Clé', 'eac-components'),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => $this->get_custom_keys_array(),
			]
		);
	}
    
    public function render() {
    //public function get_value(array $options = []) {
		$key = $this->get_settings('author_custom_field');
		
		if(empty($key)) { return ''; }
		//return implode(',', $key);
		echo implode(',', $key);
	}
    
    private function get_custom_keys_array() {
		$metadatas = [];
		$options = [];
		
		$metadatas = Eac_Dynamic_Tags::get_all_authors(); // Authors

		if(!empty($metadatas)) {
	        foreach($metadatas as $key => $value) {
		        $options[$key] = $value; // $options[ID de l'author] = display_name
            }
		}
		return $options;
	}
}