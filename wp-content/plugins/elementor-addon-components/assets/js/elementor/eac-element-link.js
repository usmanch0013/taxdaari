
/**
 * Description: Cette méthode est déclenchée lorsque le control 'eac_element_link' est chargée dans la page
 *
 * @param {selector} $scope. Le contenu de la section/colonne
 * 
 * @since 1.8.4
 */
;(function($, elementor) {

	'use strict';

	var EacAddonsElementLink = {

		init: function() {
			elementor.hooks.addAction('frontend/element_ready/section', EacAddonsElementLink.elementLink);
			elementor.hooks.addAction('frontend/element_ready/column', EacAddonsElementLink.elementLink);
		},
		
		elementLink: function($scope) {
			var configLink = {
				$target: $scope,
				isEditMode: Boolean(elementor.isEditMode()),
				settings: $scope.data('eac_settings_link') || {},
				
				/**
				 * init
				 *
				 * @since 1.8.4
				 */
				init: function() {
					// Erreur settings et dans l'éditeur
					if(Object.keys(this.settings).length === 0 || this.isEditMode ) { return; }
					
					var url = decodeURIComponent(this.settings.url);
					var isExternal = this.settings.is_external ? " target='_blank'" : '';
					var isFollow = this.settings.nofollow ? " rel='nofollow'" : '';
					
					// URL vide
					if(url === '' || url.match("^#")) { return; }
					
					this.$target.append("<a href='" + url + "'" + isExternal + isFollow + "><span class='eac-element-link'></span></a>");
					
				},
			};
			
			configLink.init();
		},
	};
	
	
	/**
	* Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	*
	* @return (object) Initialise l'objet EacAddonsElementLink
	* @since 0.0.9
	*/
	$(window).on('elementor/frontend/init', EacAddonsElementLink.init);
	
}(jQuery, window.elementorFrontend));