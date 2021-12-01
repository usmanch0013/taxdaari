<?php

/*====================================================================================
* Class: Eac_Author_Social_network
*
* To ensure the best compatibility with previous versions, you must use the same meta name as described in the first chapter 
* @return La liste formatées des URL des médias sociaux pour l'utilisateur courant
* @since 1.6.0
* @since 1.7.6	Tranférer la liste des réseaux sociaux dans la class 'Eac_Tools_Util'
*====================================================================================*/

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

use EACCustomWidgets\Includes\Eac_Tools_Util;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if(!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Eac_Author_Social_network extends Tag {

	public function get_name() {
		return 'eac-addon-author-social-network';
	}

	public function get_title() {
		return __('Auteur réseaux sociaux', 'eac-components');
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
		return 'author_social_network';
	}

	protected function register_controls() {
		$this->add_control('author_social_network',
			[
				'label' => __('Champs', 'eac-components'),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'default' => '',
				'options' => Eac_Tools_Util::get_all_social_networks(),	// @since 1.7.6
			]
		);
	}
	
	public function render() {
		global $authordata;
		
		if(!isset($authordata->ID)) {	// La variable globale n'est pas définie
			$post = get_post();			// L'article courant
			$authordata = get_userdata($post->post_author);
		}
		
		$keys = $this->get_settings('author_social_network');
		if(empty($keys)) { return; }
	    
		$values = '<div class="dynamic-tags_social-container">';
		
		foreach($keys as $key) {
		    $value = get_the_author_meta($key, $authordata->ID);
		    if($value !== '') {
				$href = '<a href="' . $value .'" rel="nofollow">';
				$span = '<span class="dynamic-tags_social-icon ' . $key . '"' . ' title="' . ucfirst($key) . '">';
				$faw = $key === 'tiktok' ? '<i class="fab fa-' . $key .'"></i>' : '<i class="fa fa-' . $key .'"></i></span></a>';
    		    
				$values .= $href . $span . $faw;
		    }
		}
		$values .= '</div>';
		
		echo wp_kses_post($values);
	}
}