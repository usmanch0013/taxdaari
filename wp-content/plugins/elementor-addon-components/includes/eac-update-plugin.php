<?php

/*========================================================================================================
 * 
 * Description: Application des filtres nécessaires pour la mise à jour du plugin
 * Le fichier "info.xml" est chargé à partir du serveur de prod qui maintient toutes les clés/valeurs
 * nécessaires pour renseigner l'API plugin de wordpress à travers ses filtres
 *
 * Inspired by: https://rudrastyh.com/wordpress/self-hosted-plugin-update.html
 *
 * @since 1.6.5
 *=======================================================================================================*/

add_filter('plugin_auto_update_setting_html', 'eac_auto_update_setting_html', 10, 3);
add_filter('plugins_api', 'eac_plugin_info', 9, 3);
add_filter('site_transient_update_plugins', 'eac_push_update');
//add_filter('pre_set_site_transient_update_plugins', 'display_transient_update_plugins');
add_action('upgrader_process_complete', 'eac_after_update', 10, 2);

/**
 * eac_auto_update_setting_html
 *
 * Modifie le message de mise à jour automatique du plugin
 *
 */
function eac_auto_update_setting_html($html, $plugin_file, $plugin_data) {
    if('elementor-addon-components/elementor-addon-components.php' === $plugin_file) {
        $html = __('Les mises à jour automatiques ne sont pas disponibles pour ce plugin', 'eac-components');
    }
// Auto-updates are not available for this plugin
    return $html;
}


function display_transient_update_plugins($transient) {
    console_log("transient->PSSTUP::" . serialize($transient));
}

/**
 * eac_plugin_info
 *
 * $res empty at this step
 * $action 'plugin_information'
 * $args stdClass Object ([slug] => elementor-addon-components [is_ssl] => [fields] => Array ([banners] => 1 [reviews] => 1 [downloaded] => [active_installs] => 1) [per_page] => 24 [locale] => en_US)
 */
function eac_plugin_info($res, $action, $args) {
	// do nothing if this is not about getting plugin information
	if('plugin_information' !== $action) {
		return $res;
	}
 
	$plugin_slug = 'elementor-addon-components'; // le slug du plugin
	
	// On sort si ce n'est pas notre plugin
	if($plugin_slug !== $args->slug) {
		return $res;
	}
	
	// On recherche les infos dans la cache (transient) sinon on charge les données du serveur
	if(false == $obj = get_transient('eac_update_' . $plugin_slug)) {
		// info.xml fichier qui contient les informations sur le plugin situé sur le serveur
		$obj = wp_remote_get("https://elementor-addon-components.com/wp-content/uploads/info.xml",
			array(
				"timeout" => 10,
				"sslverify" => false,
				"headers" => array("Accept" => "application/xml")
			)
		);
		
		// Pas d'erreur on enregistre les données xml dans le cache
		if(!is_wp_error($obj) && isset($obj['response']['code']) && $obj['response']['code'] == 200 && !empty($obj['body'])) {
			set_transient('eac_update_' . $plugin_slug, $obj, 43200); // 12 heures de cache
		}
 
	}
	
	// Pas d'erreur
	if(!is_wp_error($obj) && isset($obj['response']['code']) && $obj['response']['code'] == 200 && !empty($obj['body'])) {
		$remote = SimpleXML_Load_String($obj['body'], 'SimpleXMLElement', LIBXML_NOCDATA);
		
		$res = new stdClass();
		$res->name = (string) $remote->document->name;
		$res->homepage = (string) $remote->document->homepage;
		$res->slug = $plugin_slug;
		$res->version = (string) $remote->document->new_version;
		$res->tested = (string) $remote->document->tested;
		$res->requires = (string) $remote->document->requires;
		$res->requires_elementor = (string) $remote->document->requires_elementor;
		$res->author = '<a href="https://elementor-addon-components.com">EAC Team</a>';
		$res->download_link = (string) $remote->document->download_url;
		$res->trunk = (string) $remote->document->download_url;
		$res->requires_php = (string) $remote->document->requires_php;
		$res->last_updated = (string) $remote->document->last_updated;
		$res->added = (string) $remote->document->added;
		$res->active_installs = (int) $remote->document->active_installs;
		
		$res->sections = array(
			'description' => $remote->document->sections->description,
			'installation' => $remote->document->sections->installation,
			'changelog' => $remote->document->sections->changelog,
			);
		
		$res->banners = array('high' => $remote->document->banners->high);
		
		return $res;
	}
	return $res;
}

/**
 * eac_push_update
 *
 * Teste la version installée et la version du fichier de configuration 'info.xml'
 *
 * Retourne un transient vide si c'est la même version pour valoriser la propriété 'no_update'
 * Ou un transient modifié avec les infos du fichier de configuration
 * ATTENTION: Il faut utiliser le transient pour éviter le code 429 (Too many request) du serveur distant
 * Le filtre est déclenché pour tous les plugins !!
 */
function eac_push_update($transient){
	
	if(empty($transient->checked)) {
		return $transient;
	}
	//var_dump($transient);
	// On recherche les infos dans la cache (transient) sinon on charge les données du serveur
	if(false == $obj = get_transient('eac_upgrade_elementor-addon-components')) {
		// info.xml fichier qui contient les informations sur le plugin situé sur le serveur
		$obj = wp_remote_get("https://elementor-addon-components.com/wp-content/uploads/info.xml",
			array(
				"timeout" => 10,
				"sslverify" => false,
				"headers" => array("Accept" => "application/xml")
			)
		);
		
		// Pas d'erreur on enregistre les données xml dans le cache
		if(!is_wp_error($obj) && isset($obj['response']['code']) && $obj['response']['code'] == 200 && !empty($obj['body'])) {
			set_transient('eac_upgrade_elementor-addon-components', $obj, 43200); // 12 hours cache
		}
 
	}
	
	// @since 1.8.5 Pas d'erreur
	if(!is_wp_error($obj) && isset($obj['response']['code']) && $obj['response']['code'] == 200 && !empty($obj['body'])) {
	//if($obj) {
		$remote = SimpleXML_Load_String($obj['body'], 'SimpleXMLElement', LIBXML_NOCDATA);
		//console_log("transient::" . $remote->asXML());
		
		/**
		 * La version installée est la même que la version du transient 'new_version'
		 * On retourne un transient vide pour garantir que le lien 'view details' et 'auto-update' soient bien affichés
		 * en valorisant la propriété 'no_update' (https://make.wordpress.org/core/tag/5-5+dev-notes/) du transient
		 */
		if(version_compare($transient->checked['elementor-addon-components/elementor-addon-components.php'], (string) $remote->document->new_version, '>=')) {
			$item = (object) array(
				'id'            => 'elementor-addon-components/elementor-addon-components.php',
				'slug'          => 'elementor-addon-components',
				'plugin'        => 'elementor-addon-components/elementor-addon-components.php',
				'new_version'   => (string) $remote->document->new_version,
				'url'           => '',
				'package'       => '',
				'icons'         => array(),
				'banners'       => array(),
				'banners_rtl'   => array(),
				'tested'        => '',
				'requires_php'  => '',
				'compatibility' => new stdClass(),
			);
			
			// Adding the "mock" item to the `no_update` property is required
			// for the enable/disable auto-updates links to correctly appear in UI.
			$transient->no_update['elementor-addon-components/elementor-addon-components.php'] = $item;
			//console_log("transient->no_update::" . serialize($transient->no_update['elementor-addon-components/elementor-addon-components.php']));
			
			return $transient;
		}
		
		// La version du plugin stockée dans le cache (transient) est plus élevée que la version courante
		if(version_compare('1.0', (string) $remote->document->new_version, '<') && version_compare((string) $remote->document->requires, get_bloginfo('version'), '<')) {
			$res = new stdClass();
			$res->slug = 'elementor-addon-components';
			$res->plugin = 'elementor-addon-components/elementor-addon-components.php';
			$res->new_version = (string) $remote->document->new_version;
			$res->tested = (string) $remote->document->tested;
			$res->package = (string) $remote->document->download_url;
			$res->url = (string) $remote->document->homepage;
			
			$transient->response[$res->plugin] = $res;
		}
	}
	
	return $transient;
}

/**
 * eac_after_update
 *
 * $upgrader_object
 * $options['action'] == 'update'
 * $options['type'] == 'plugin'
 * 
 * Supprime le transient 'cache' du slug
 */
function eac_after_update($upgrader_object, $options) {
	if($options['action'] == 'update' && $options['type'] === 'plugin') {
		// just clean the cache when new plugin version is installed
		delete_transient('eac_upgrade_elementor-addon-components');
	}
}