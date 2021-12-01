<?php
/*******************************************************************************
* File with setting registration
*******************************************************************************/

/**
* Generates a submenu page
* @param void
* @return HTML
**/
add_action('admin_menu', 'clarity_submenu_page_generation');
function clarity_submenu_page_generation(){
  add_submenu_page('options-general.php', 'Clarity', 'Clarity', 'manage_options', 'clarity_settings', 'clarity_admin_settings_page');
}

/**
* Register Plugin settings
* @param void
* @return HTML
**/
add_action('admin_init', 'clarity_register_settings');
function clarity_register_settings(){
  register_setting('clarity_settings_fields', 'clarity_project_id');
  add_settings_section('clarity_section_project_id','Welcome to Clarity!','clarity_section_project_id_callback','clarity_settings');
  add_settings_field('clarity_settings_field_project_id','Project Id:','clarity_settings_field_project_id_callback','clarity_settings','clarity_section_project_id');
}

add_action('admin_notices', 'setup_clarity_notice');
function setup_clarity_notice() {
	global $pagenow;
	$url = get_admin_url() . 'options-general.php?page=clarity_settings';
	
	if (  (  get_option('clarity_project_id')=="" || get_option("display_clarity_notice",true) ) && $pagenow!="options-general.php"  && $_GET['page']!='clarity_settings' ) 
	{
         echo '<div class="notice notice-info is-dismissible">
             <p style="font-weight:700">Please setup Clarity to start understanding user behavior on your site. </p><p><a class="button-primary" href="'. $url .'">Setup Clarity</a></p>
         </div>';
    }
}
