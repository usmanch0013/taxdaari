<?php

/*===============================================================================
* Class: Eac_Author_Picture
*
*
* @return l'URL de l'avatar/gravatar de l'auteur de l'article courant
* @since 1.6.0
*===============================================================================*/

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if(!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Eac_Author_Picture extends Data_Tag {

	public function get_name() {
		return 'eac-addon-author-profile-picture';
	}

	public function get_title() {
		return __("Photo de l'auteur", 'eac-components');
	}

	public function get_group() {
		return 'eac-author-groupe';
	}

	public function get_categories() {
		return [TagsModule::IMAGE_CATEGORY];
	}

	protected function register_controls() {
		$this->add_control('author_picture_size',
			[
				'label' => __('Dimension', 'eac-components'),
				'type' => Controls_Manager::SELECT,
				'default' => '96',
				'options' => [
					'80' => __('80', 'eac-components'),
					'96' => __('96', 'eac-components'),
					'120' => __('120', 'eac-components'),
					'140' => __('140', 'eac-components'),
				],
			]
		);
	}
	
	public function get_value(array $options = []) {
        global $authordata;
        $size = $this->get_settings('author_picture_size');
        
		if(!isset($authordata->ID)) { // La variable globale n'est pas définie
			$post = get_post();
			$authordata = get_userdata($post->post_author);
		}
		
		return [
		    'url' => get_avatar_url((int) get_the_author_meta('ID'), ['size' => $size]),
			'id' => '',
		];
	}
}