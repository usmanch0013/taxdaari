<?php
/**
 * Plugin Name:       Microsoft Clarity
 * Plugin URI:        https://clarity.microsoft.com/
 * Description:       With data and session replay from Clarity, you'll see how people are using your site — where they get stuck and what they love.
 * Version:           0.6.1
 * Author:            Microsoft
 * Author URI:        https://www.microsoft.com/en-us/
 * License:           MIT
 * License URI:       https://docs.opensource.microsoft.com/content/releasing/license.html
 */

/**
* Require files only if is admin area
* @param void
* @return HTML
**/
if(is_admin()){
  require_once plugin_dir_path(__FILE__).'/admin/settings_page.php';
  require_once plugin_dir_path(__FILE__).'/admin/settings_callbacks.php';
}

function clarity_plugin_settings_link( $links ) {
	$url = get_admin_url() . 'options-general.php?page=clarity_settings';
	$settings_link = '<a href="' . $url . '">' . __('Settings', 'textdomain') . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}

function clarity_after_setup_theme() {
	 add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'clarity_plugin_settings_link');
}
add_action ('after_setup_theme', 'clarity_after_setup_theme');

/**
* Add origins for CORS
* @param void
* @return HTML
**/
add_filter('allowed_http_origins', 'clarity_add_origins');
function clarity_add_origins( $origins ) {
  $origins[] = 'https://www.clarity.ms';
  return $origins;
}

/**
* Runs when Clarity Plugin is activated
* @param void
* @return void
**/
register_activation_hook(__FILE__, 'clarity_on_activation');
function clarity_on_activation() {
	add_option("display_clarity_notice", true);
	update_option("display_clarity_notice", true);
}

/**
* Runs when Clarity Plugin is deactivated
* @param void
* @return void
**/
register_deactivation_hook( __FILE__, 'clarity_on_deactivation');
function clarity_on_deactivation() {
  	remove_menu_page( 'clarity_settings' );
	update_option("display_clarity_notice", false);
	update_option('clarity_project_id', '');
	  
  	return;
}

register_uninstall_hook( 'uninstall.php', 'clarity_on_uninstallation');
function clarity_on_uninstallation() {
	delete_option("display_clarity_notice");
	delete_option('clarity_project_id');
  	return;
}

/**
* Adds the script to run clarity
* @param void
* @return void
**/
add_action('wp_head', 'clarity_add_script_to_header');
function clarity_add_script_to_header(){
    $p_id_option = get_option('clarity_project_id');
		if ($p_id_option) {
		?>
			<script type="text/javascript">
					(function(c,l,a,r,i,t,y){
						c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;
						t.src="https://www.clarity.ms/tag/"+i+"?ref=wordpress";y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
					})(window, document, "clarity", "script", "<?=$p_id_option ?>");
			</script>
		<?php
		}
}
