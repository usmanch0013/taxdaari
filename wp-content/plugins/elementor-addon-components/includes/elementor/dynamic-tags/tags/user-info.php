<?php

/*===============================================================================
* Class: Eac_User_Info
*
* 
* @return affiche la valeur d'une métadonnée pour l'utilisateur courant logué
* @since 1.6.0
* @since 1.6.1 Ajout du rôle dans la liste des informations de l'utilisateur
*===============================================================================*/

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

use EACCustomWidgets\Includes\Elementor\DynamicTags\Eac_Dynamic_Tags;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if(!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Eac_User_Info extends Tag {

	public function get_name() {
		return 'eac-addon-user-info';
	}

	public function get_title() {
		return __('Info Utilisateur', 'eac-components');
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
		return 'user_info_type';
	}

	protected function register_controls() {
		$this->add_control('user_info_type',
			[
				'label' => __('Champ', 'eac-components'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Select...', 'eac-components'),
					'id' => __('ID', 'eac-components'),
					'role' => __('Rôle', 'eac-components'),	// @since 1.6.1
					'nickname' => __('Surnom', 'eac-components'),
					'login' => __('Identifiant de login', 'eac-components'),
					'first_name' => __('Prénom', 'eac-components'),
					'last_name' => __('Nom', 'eac-components'),
					'description' => __('Bio', 'eac-components'),
					'email' => __('Email', 'eac-components'),
					'url' => __('Site Web', 'eac-components'),
					'meta' => __('Meta utilisateur', 'eac-components'),
				],
			]
		);

		$this->add_control('user_info_meta_key',
			[
				'label' => __('Clé', 'eac-components'),
				'type' => Controls_Manager::SELECT,
				'options' => Eac_Dynamic_Tags::get_user_metas(),
				'default' => 'nickname',
				'condition' => ['user_info_type' => 'meta'],
			]
		);
	}
	
	public function render() {
		$type = $this->get_settings('user_info_type');
		$user = wp_get_current_user();
		
		// User non logué
		if(empty($type) || 0 === $user->ID) {
		    echo wp_kses_post(__('Non logué', 'eac-components'));
			return;
		}

		$value = '';
		switch($type) {
			case 'login':
			case 'email':
			case 'url':
			case 'nicename':
				$field = 'user_' . $type;
				$value = isset($user->$field) ? $user->$field : '';
				break;
			case 'id':
			case 'description':
			case 'first_name':
			case 'last_name':
			case 'nickname':
				$value = isset($user->$type) ? $user->$type : '';
				break;
			case 'meta':
				$key = $this->get_settings('user_info_meta_key');
				if (!empty($key)) {
					$value = get_user_meta($user->ID, $key, true);
				}
				break;
			case 'role': // @since 1.6.1
				$userInfo = get_userdata($user->ID);
				$value = implode(', ', $userInfo->roles);
				break;
		}

		echo wp_kses_post($value);
	}
}