
(function($) {
    "use strict";
    
	// Événement sur la checkbox Dynamic Tag
	// Change la valeur d'autres checkbox en relation
    $('#dynamic-tag').on('click', function() {
		if($(this).prop('checked') == false){
			$('#alt-attribute').prop('checked', 0);
		}
		$('#eac-features-saved').css('display', 'none');
		$('#eac-features-notsaved').css('display', 'none');
    });
	
	// Événement sur la checkbox ACF Dynamic Tag
	// Change la valeur d'autres checkbox en relation
    /*$('#acf-dynamic-tag').on('click', function() {
		if($(this).prop('checked') == false){
			$('#acf-option-page').prop('checked', 0);
		}
		$('#eac-features-saved').css('display', 'none');
		$('#eac-features-notsaved').css('display', 'none');
    });*/
	
	// Événement sur la checkbox global
	// Change la valeur de tous les checkbox
    $('#all-components').on('click', function() {
		if($(this).prop('checked') == true) {
			$('.eac-elements-table input').prop('checked', 1);
		} else if($(this).prop('checked') == false){
			$('.eac-elements-table input').prop('checked', 0);
		}
		$('#eac-elements-saved').css('display', 'none');
		$('#eac-elements-notsaved').css('display', 'none');
    });
	
	// L'état d'un checkbox a changé
	$('.switch').on('change', ':checkbox', function() {
		$('#eac-elements-saved').css('display', 'none');
		$('#eac-elements-notsaved').css('display', 'none');
		$('#eac-features-saved').css('display', 'none');
		$('#eac-features-notsaved').css('display', 'none');
	});
	
	/**
	 * Le formulaire des options des composants est soumis
	 * @since 1.8.7	Ajout du nonce dans les données
	 */
    $('form#eac-form-settings').on('submit', function(e) {
		e.preventDefault();
		$.ajax({
			url: components.ajax_url,
			type: 'post',
			data: {
				action: components.ajax_action,
				nonce: components.ajax_nonce,
				fields: $('form#eac-form-settings').serialize(),
			},
		}).done(function(response) {
			if(response.success === false) {
				console.log('Error components: ' + response.data);
				$('#eac-elements-notsaved').css('display', 'block');
			} else {
				//console.log('Success components: ' + response.data);
				$('#eac-elements-saved').css('display', 'block');
			}
		});
	});
	
	/**
	 * Le formulaire des options des fonctionnalités est soumis
	 * @since 1.8.7	Ajout du nonce dans les données
	 */
    $('form#eac-form-features').on('submit', function(e) {
		e.preventDefault();
		$.ajax({
			url: features.ajax_url,
			type: 'post',
			data: {
				action: features.ajax_action,
				nonce: features.ajax_nonce,
				fields: $('form#eac-form-features').serialize(),
			},
		}).done(function(response) {
			if(response.success === false) {
				console.log('Error features: ' + response.data);
				$('#eac-features-notsaved').css('display', 'block');
			} else {
				//console.log('Success features: ' + response.data);
				$('#eac-features-saved').css('display', 'block');
			}
		});
	});
	
	// Gestion des events sur les tabs
	$('.tabs-nav a').on('click', function(event) {
		event.preventDefault();
		$('.tab-active').removeClass('tab-active');
		$(this).parent().addClass('tab-active');
		$('.tabs-stage > div').hide();
		$($(this).attr('href')).show();
		$('#eac-elements-saved').css('display', 'none');
		$('#eac-elements-notsaved').css('display', 'none');
		$('#eac-features-saved').css('display', 'none');
		$('#eac-features-notsaved').css('display', 'none');
	});

	$('.tabs-nav a:first').trigger('click'); // Default
	
})(jQuery);