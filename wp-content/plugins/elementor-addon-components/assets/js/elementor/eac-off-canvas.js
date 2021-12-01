
;(function($, elementor) {
	
	"use strict";
	
	var EacAddonsOffcanvas = {
		init: function() {
			elementor.hooks.addAction('frontend/element_ready/eac-addon-off-canvas.default', widgetOffCanvas);
		},
		
		/**
		* Description: Cette méthode est déclenchée lorsque la section 'eac-addon-off-canvas' est chargée dans la page
		*
		* @param {selector} $scope. Le contenu de la section
		* @since 1.8.5
		*/
		widgetOffCanvas: function($scope) {
			var $targetInstance = $scope.find('.eac-off-canvas'),
				$targetWrapper = $targetInstance.find('.oc-offcanvas__wrapper'),
				settings = $targetWrapper.data('settings') || {},
				$targetId = $('#' + settings.data_id);
			
			// Erreur settings
			if(Object.keys(settings).length === 0) {
				return;
			}
		},
	};
	

	/**
	* Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	*
	* @return (object) Initialise l'objet EacAddonsOffcanvas
	* @since 0.0.9
	*/
	$(window).on('elementor/frontend/init', EacAddonsOffcanvas.init);
	
}(jQuery, window.elementorFrontend));