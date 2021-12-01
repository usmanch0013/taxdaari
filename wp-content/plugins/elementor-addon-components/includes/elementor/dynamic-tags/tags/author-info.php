<?php

/*===========================================================================================
* Class: Eac_Author_Info
*
*
* @return affiche selon la sélection, la bio, l'email, l'URL du site web ou une méta donnée
* de l'auteur de l'article courant
* @since 1.6.0
* @since 1.6.1 Ajout du rôle dans la liste des informations de l'auteur
*============================================================================================*/

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

use EACCustomWidgets\Includes\Elementor\DynamicTags\Eac_Dynamic_Tags;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if(!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Eac_Author_Info extends Tag {

	public function get_name() {
		return 'eac-addon-author-info';
	}

	public function get_title() {
		return __('Info Auteur', 'eac-components');
	}

	public function get_group() {
		return 'eac-author-groupe';
	}

	public function get_categories() {
		return [
			TagsModule::TEXT_CATEGORY,
			TagsModule::POST_META_CATEGORY,
		];
	}
    
	public function get_panel_template_setting_key() {
		return 'author_info_type';
	}

	protected function register_controls() {
		$this->add_control('author_info_type',
			[
				'label' => __('Champ', 'eac-components'),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
				    '' => __('Select...', 'eac-components'),
					'role' => __('Rôle', 'eac-components'),	// @since 1.6.1
					'description' => __('Bio', 'eac-components'),
					'email' => __('Email', 'eac-components'),
					'url' => __('Site Web', 'eac-components'),
					'meta' => __('Meta auteur', 'eac-components'),
				],
			]
		);
		
		$this->add_control('author_info_meta_key',
			[
				'label' => __('Clé', 'eac-components'),
				'type' => Controls_Manager::SELECT,
				'options' => Eac_Dynamic_Tags::get_author_metas(),
				'default' => 'nickname',
				'condition' => ['author_info_type' => 'meta'],
			]
		);
	}
	
	public function render() {
	    // Allow HTML in author bio section 
        //remove_filter('pre_user_description', 'wp_filter_kses');
        $value = '';
        
		$key = $this->get_settings('author_info_type');

		if(empty($key)) { return; }
		
		if($key === 'meta') {
		    $meta = $this->get_settings('author_info_meta_key');
			if(!empty($meta)) {
				$value = get_the_author_meta($meta);
			}
		} else if($key === 'role') { // @since 1.6.1
			$userInfo = new \WP_User(get_the_author_meta('ID'));
			if(!empty($userInfo->roles) && is_array($userInfo->roles)) {
				$value = implode(', ', $userInfo->roles);
			};
		} else {
			$value = get_the_author_meta($key);
		}

		echo wp_kses_post($value);
	}
}