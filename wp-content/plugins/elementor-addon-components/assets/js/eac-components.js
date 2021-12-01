
"use strict";

(function($, elementor) {
	
	var EacAddonsElements = {
		init: function() {
			
			var editMode = self !== top;
			var instagramExplore = typeof widgetInstagramExplore === 'function';
			var instagramSearch = typeof widgetInstagramSearch === 'function';
			var instagramUser = typeof widgetInstagramUser === 'function';
			var instagramLocation = typeof widgetInstagramLocation === 'function';
			var multiWidgetChart = typeof eacWidgetChart === 'function';
			
			/**
			 * @since 1.8.7	Implémente les custom breakpoints
			 */
			var activeBreakpoints = elementor.config.responsive.activeBreakpoints;
			var windowWidthMob = 0,	windowWidthMobExtra = 0, windowWidthTab = 0, windowWidthTabExtra = 0, windowWidthLaptop = 0, windowWidthWidescreen = 0;
			
			// Il y a des activeBreakpoints
			if(Object.keys(activeBreakpoints).length > 0) {
				$.each(elementor.config.responsive.activeBreakpoints, function(device) {
					if(device === 'mobile') { windowWidthMob = activeBreakpoints.mobile.default_value; } // value
					else if(device === 'mobile_extra') { windowWidthMobExtra = activeBreakpoints.mobile_extra.default_value; }
					else if(device === 'tablet') { windowWidthTab = activeBreakpoints.tablet.default_value; }
					else if(device === 'tablet_extra') { windowWidthTabExtra = activeBreakpoints.tablet_extra.default_value; }
					else if(device === 'laptop') { windowWidthLaptop = activeBreakpoints.laptop.default_value; }
					else if(device === 'widescreen') { windowWidthWidescreen = activeBreakpoints.widescreen.default_value; }
				});
				//console.log(windowWidthMob+"::"+windowWidthMobExtra+"::"+windowWidthTab+"::"+windowWidthTabExtra+"::"+windowWidthLaptop+"::"+windowWidthWidescreen);
				//console.log(activeBreakpoints);
			}
			
			var widgets = {
				/** Nom du widget (function get_name() de chaque widget) + callback */
				'eac-addon-images-comparison.default': EacAddonsElements.widgetImageComparison,
				'eac-addon-image-galerie.default': EacAddonsElements.widgetImageGalerie,
				'eac-addon-image-effects.default': EacAddonsElements.widgetImageEffects,
				'eac-addon-image-promo.default': EacAddonsElements.widgetImagePromo,
				'eac-addon-kenburn-slider.default': EacAddonsElements.widgetKenBurnSlider,
				'eac-addon-slider-pro.default': EacAddonsElements.widgetSliderPro,
				'eac-addon-articles-liste.default': EacAddonsElements.widgetArticlesListe,
				'eac-addon-lecteur-rss.default': EacAddonsElements.widgetLecteurRss,
				'eac-addon-reseaux-sociaux.default': EacAddonsElements.widgetReseauxSociaux,
				'eac-addon-lecteur-audio.default': EacAddonsElements.widgetLecteurAudio,
				'eac-addon-pinterest-rss.default': EacAddonsElements.widgetPinterestRss,
				'eac-addon-instagram-explore.default': instagramExplore ? widgetInstagramExplore : '',
				'eac-addon-instagram-search.default': instagramSearch ? widgetInstagramSearch : '',
				'eac-addon-instagram-user.default': instagramUser ? widgetInstagramUser : '',
				'eac-addon-instagram-location.default': instagramLocation ? widgetInstagramLocation : '',
				'eac-addon-chart.default': multiWidgetChart ? eacWidgetChart : '',
				'eac-addon-modal-box.default': EacAddonsElements.widgetModalBox,
				'eac-addon-toc.default': EacAddonsElements.widgetTableOfContent,
				'eac-addon-off-canvas.default': EacAddonsElements.widgetOffCanvas,
			};
			
			// Affectation des widgets chargées à un callback 
			$.each(widgets, function(widget, callback) {
				if(callback) {
					elementor.hooks.addAction('frontend/element_ready/' + widget, callback);
				}
			});
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
				$targetOverlay = $targetInstance.find('.oc-offcanvas__wrapper-overlay'),
				settings = $targetWrapper.data('settings') || {},
				$triggerId = $('#' + settings.data_id + ' .oc-offcanvas__wrapper-trigger'),
				$targetId = $('#' + settings.data_id + '.oc-offcanvas__wrapper-canvas'),
				$targetHeader = $targetId.find('.oc-offcanvas__canvas-header'),
				$targetCloseId = $targetId.find('.oc-offcanvas__canvas-close span'),
				$targetContent = $targetId.find('.oc-offcanvas__canvas-content'),
				$targetTitleContent = $targetId.find('.oc-offcanvas__canvas-content .widget .widgettitle, .oc-offcanvas__canvas-content .widget .widget-title'),
				$targetAllCanvas = $('.oc-offcanvas__wrapper-canvas');
			
			// Erreur settings
			if(Object.keys(settings).length === 0) {
				return;
			}
			
			// Le canvas est gauche, on inverse la direction du flex de l'entête
			if(settings.data_position === 'left') {
				$targetHeader.css({'flex-direction': 'row-reverse'});
			}
			
			// Click sur le bouton ou le texte pour ouvrir/fermer le canvas
			/*$targetTitleContent.on('click', function(evt) {
				evt.preventDefault();
				$(this).parent().slideToggle(300);
			});*/
			
			// Click sur le bouton ou le texte pour ouvrir/fermer le canvas
			$triggerId.on('click', function(evt) {
				evt.preventDefault();
				
				// Fermeture des autres canvas
				/*if($targetAllCanvas.length > 0) {
					$.each($targetAllCanvas, function(indice, target) {
						if($(target).id != settings.data_id && $(target).css('display') === 'block') {
							$(target).slideToggle(300);
						}
					});
				}*/
				
				/* Cache le contenu systématiquement avant l'ouverture/fermeture */
				if($targetContent.css('display') === 'block') {
					$targetContent.css({'display': 'none'});
				}
				
				if(settings.data_position === 'top' || settings.data_position === 'bottom') {
					$targetId.animate({height: 'toggle'}, 300, function() {
						$targetContent.css({'display': 'block'});
						$targetOverlay.css({'display': 'block'});
					});
				} else {
					$targetId.animate({width: 'toggle'}, 300, function() {
						$targetContent.css({'display': 'block'});
						$targetOverlay.css({'display': 'block'});
					});
				}
				
				
			});
			
			// Bouton supérieur de fermeture du canvas
			$targetCloseId.on('click', function(evt) {
				evt.preventDefault();
				
				$targetContent.css({'display': 'none'});
				$targetOverlay.css({'display': 'none'});
				
				if(settings.data_position === 'top' || settings.data_position === 'bottom') {
					$targetId.animate({height: "toggle"}, 300);
				} else {
					$targetId.animate({width: "toggle"}, 300);
				}
			});
			
			// Click sur l'overlay
			$targetOverlay.on('click', function(evt) {
				evt.preventDefault();
				
				$targetContent.css({'display': 'none'});
				$targetOverlay.css({'display': 'none'});
				
				if(settings.data_position === 'top' || settings.data_position === 'bottom') {
					$targetId.animate({height: "toggle"}, 300);
				} else {
					$targetId.animate({width: "toggle"}, 300);
				}
			});
			
			// Touche échappement ESC de fermeture du canvas
			$('body').on('keydown', function(evt) {
				if(evt.which === 27 && $targetId.css('display') === 'block') {
					$targetContent.css({'display': 'none'});
					$targetOverlay.css({'display': 'none'});
					
					if(settings.data_position === 'top' || settings.data_position === 'bottom') {
						$targetId.animate({height: "toggle"}, 300);
					} else {
						$targetId.animate({width: "toggle"}, 300);
					}
				}
			});
		},
		
		/**
		* Description: Cette méthode est déclenchée lorsque la section 'eac-addon-toc' est chargée dans la page
		*
		* @param {selector} $scope. Le contenu de la section
		* @since 1.8.0
		* @since 1.8.1	Ajout des propriétés 'trailer', 'titles', 'ancreAuto' et 'topMargin'
		*/
		widgetTableOfContent: function($scope) {
			var $targetInstance = $scope.find('.eac-table-of-content'),
				settings = $targetInstance.data('settings') || {};
			
			// Erreur settings
			if(Object.keys(settings).length === 0) {
				return;
			}
			
			$.toctoc({
				fontawesome: settings.data_fontawesome,
				target: settings.data_target,
				opened: settings.data_opened,
				headPicto: ['▼', '▲', '▶️'],
				titles: settings.data_title,
				trailer: settings.data_trailer,
				ancreAuto: settings.data_anchor,
				topMargin: settings.data_topmargin,
			});
		},
		
		/**
		* Description: Cette méthode est déclenchée lorsque la section 'eac-addon-modal-box' est chargée dans la page
		*
		* @param {selector} $scope. Le contenu de la section
		* @since 1.6.1
		*/
		widgetModalBox: function($scope) {
			var $targetInstance = $scope.find('.eac-modal-box'),
				$targetWrapper = $targetInstance.find('.mb-modalbox__wrapper'),
				settings = $targetWrapper.data('settings') || {};
				
			// Erreur settings
			if(Object.keys(settings).length === 0) {
				return;
			}
			
			var $targetId = $('#' + settings.data_id),
				$targetTrigger = $targetId.find('.mb-modalbox__wrapper-trigger'),
				$targetHiddenContent = $targetId.find('#modalbox-hidden-' + settings.data_id),
				$titleHiddenContent = $targetHiddenContent.find('.mb-modalbox__hidden-content-title'),
				$bodyHiddenContent = $targetHiddenContent.find('.mb-modalbox__hidden-content-body'),
				$fbButtonClose = $targetHiddenContent.find('#my-fb-button'),
				// CF7
				$targetCF7Div = $targetId.find('#modalbox-hidden-' + settings.data_id + ' .wpcf7'),
				$targetCF7Form = $targetCF7Div.find('.wpcf7-form'),
				$targetCF7Response = $targetCF7Form.find('.wpcf7-response-output'),
				// Forminator
				$targetForminatorForm = $targetId.find('#modalbox-hidden-' + settings.data_id + ' div.mb-modalbox__hidden-content-body form.forminator-custom-form'),
				$targetForminatorField = $targetForminatorForm.find('div.forminator-field'),
				$targetForminatorError = $targetForminatorForm.find('span.forminator-error-message'),
				$targetForminatorResponse = $targetForminatorForm.find('.forminator-response-message'),
				// WPForms
				$targetWPformsDiv = $targetId.find('#modalbox-hidden-' + settings.data_id + ' .wpforms-container'),
				$targetWPFormsForm = $targetWPformsDiv.find('.wpforms-form'),
				$targetWPFormsFieldContainer = $targetWPFormsForm.find('.wpforms-field-container'),
				// Mailpoet
				$targetMailpoetDiv = $targetId.find('#modalbox-hidden-' + settings.data_id + ' .mailpoet_form'),
				$targetMailpoetForm = $targetMailpoetDiv.find('form.mailpoet_form'),
				$targetMailpoetPara = $targetMailpoetForm.find('.mailpoet_paragraph'),
				
				options = {
					baseClass: 'modal-' + settings.data_position,
					smallBtn: true,
					buttons: [''],
					autoFocus: false,
					idleTime: false,
					animationDuration: 600,
					animationEffect: settings.data_effet,
					beforeLoad: function(instance, current) {
						 // Reset Contact Mailpoet
						if($targetMailpoetDiv.length > 0) {
							$targetMailpoetForm.trigger('reset');
					        $targetMailpoetForm.find('p.mailpoet_validate_success').css('display', 'none');
					        $targetMailpoetForm.find('p.mailpoet_validate_error').css('display', 'none');
					        $targetMailpoetPara.find('ul.parsley-errors-list').remove();
						}
						
						// Reset Contact Form 7
						if($targetCF7Div.length > 0) {
							$targetCF7Form.trigger('reset');
							$targetCF7Response.hide().empty().removeClass('wpcf7-mail-sent-ok wpcf7-mail-sent-ng wpcf7-validation-errors wpcf7-spam-blocked eac-wpcf7-SUCCESS eac-wpcf7-FAILED');
							$targetCF7Form.find('span.wpcf7-not-valid-tip').remove();
						}
						
						// Reset WPForms
						if($targetWPformsDiv.length > 0) {
							$targetWPFormsForm.trigger('reset');
							$targetWPFormsFieldContainer.find('div.wpforms-has-error').removeClass('wpforms-has-error');
							$targetWPFormsFieldContainer.find('input.wpforms-error, textarea.wpforms-error').removeClass('wpforms-error');
							$targetWPFormsFieldContainer.find('label.wpforms-error').remove();
						}
						
						// Reset Forminator
						if($targetForminatorForm.length > 0) {
							$targetForminatorForm.trigger('reset');
							$targetForminatorField.removeClass('forminator-has_error');
							$targetForminatorError.remove();
							//$targetForminatorResponse.remove();
						}
						//$(':input', $targetForminatorForm).not(':button, :submit, :reset, :hidden').removeAttr('checked').removeAttr('selected').not(':checkbox, :radio, select').val('');
					},
					afterLoad: function(instance, current) {
						/*if(current.opts.title) {
							current.$content.append('<div fancybox-title class="mb-modalbox__hidden-content-title"><h3>' + current.opts.title + '</h3></div>');
						}*/
					},
					beforeShow: function(instance, current) {
						// Pour les mobiles force overflow du Body
						$('body.fancybox-active').css({'overflow':'hidden'});
						
						if(!settings.data_modal) {
							var srcOwith = $(current.src).outerWidth();
							var slideOwidth = current.$slide.outerWidth();
							var slidewidth = current.$slide.width();
							instance.$refs.container.width(srcOwith + (slideOwidth - slidewidth));
							instance.$refs.container.height($(current.src).outerHeight() + (current.$slide.outerHeight() - current.$slide.height()));
						}
					},
					afterClose: function() {
						// Reset overflow du Body
						$('body.fancybox-active').css({'overflow':'initial'});
					},
					clickContent : function( current, event ) {
						//return current.type === 'image' ? 'close' : '';
						//if(current.type === 'image') { return false; }
					},
				},
				optionsNoModal = {
					baseClass: 'mb-modalbox_no-modal no-modal_' + settings.data_position,
					hideScrollbar: false,
					clickSlide: 'close',
					//clickOutside: 'close',
					touch: false,
					backFocus: false,
				};
			
			// Réservé pour d'éventuelle non modalbox
			if(!settings.data_modal) {
				$.extend(options, optionsNoModal);
			}
			
			// 
			$targetCF7Div.on('wpcf7invalid wpcf7spam wpcf7mailfailed', function (evt) {  
				$targetCF7Response.addClass('eac-wpcf7-FAILED'); 
			});
			
			$targetCF7Div.on('wpcf7mailsent', function (evt) {  
				$targetCF7Response.addClass('eac-wpcf7-SUCCESS');
				setTimeout(function() { $.fancybox.close(true); }, 3000);
			});
			
			/**
			 * Affichage automatique différé de la boîte modale après chargement de la page
			 * Actif ou non dans l'éditeur
			 */
			if((settings.data_declanche === 'pageloaded' && elementor.isEditMode() && settings.data_active) ||
				(settings.data_declanche === 'pageloaded' && !elementor.isEditMode())) {
				setTimeout(function() {
					$.fancybox.open([{ src: $targetHiddenContent, type: 'inline', opts: options }]);
				}, settings.data_delay * 1000);
			}
			
			// Code pour le bouton 'close me' de la page de démonstration
			$fbButtonClose.on('click touch', function(e) {
				e.preventDefault();
				$.fancybox.close(true);
			});
			
			/** Applique les options spécifiques à l'instance de la boîte courante */
			$('[data-fancybox]', $targetId).fancybox(options);
		},
		
		/**
		* Description: Cette méthode est déclenchée lorsque la section 'eac-addon-lecteur-audio' est chargée dans la page
		*
		* @param {selector} $scope. Le contenu de la section
		* @since 0.0.9
		* @since 1.7.61	Ajout de l'option 'thisSelector' passées au plugin 'mediaPlayer'
		*/
		widgetLecteurAudio: function($scope) {
			var $target = $scope.find('.eac-lecteur-audio'),
				$targetId = $scope.find('.la-lecteur-audio'),
				$targetSelect = $scope.find('#la_options_items'),
				selectedUrl = '';

			// Valeur par défaut de la liste par défaut
			selectedUrl = $targetSelect.eq(0).val();

			/**
			 * Instancie mediaPlayer
			 * @since 1.7.7	Ajout de l'option 'thisSelector' dans l'appel au plugin 'mediaPlayer'
			 */
			$('.la-lecteur-audio', $target).mediaPlayer({thisSelector: $targetId});
			
			$targetSelect.on('change', function(e) {
				e.preventDefault();
				selectedUrl = $(this).val();
				$('audio', $targetId).remove();
				$('svg', $targetId).remove();
				var $wrapperAudio = $('<audio/>', { class: 'listen', preload: 'none', 'data-size': '150', src:	selectedUrl });
				$targetId.prepend($wrapperAudio);
				$('.la-lecteur-audio', $target).mediaPlayer({thisSelector: $targetId}); /* @since 1.7.61 */
			});
			
		},
		
		/**
		* Description: Cette méthode est déclenchée lorsque la section 'eac-addon-reseaux-sociaux' est chargée dans la page
		*
		* @param {selector} $scope. Le contenu de la section
		* @since 0.0.9
		*/
		widgetReseauxSociaux: function($scope) {
			var $target = $scope.find('.eac-reseaux-sociaux'),
				$targetItems = $scope.find('.rs-items-list'),
				settings = $targetItems.data('settings') || {},
				rxOptions = {
					buttons: settings.data_buttons,
					place: settings.data_place,
					counter: true,
					text: settings.data_text,
					popup: settings.data_popup,
				};
			
			// Erreur settings
			if(Object.keys(settings).length === 0) {
				return;
			}
			
			$target.floatingSocialShare(rxOptions);
		},

		/**
		* Description: Cette méthode est déclenchée lorsque la section 'eac-addon-image-effects' est chargée dans la page
		*
		* @param {selector} $scope. Le contenu de la section
		* @since 0.0.9
		*/
		widgetImageEffects: function($scope) {
			var $target = $scope.find('.eac-image-effects');
		},

		/**
		* Description: Cette méthode est déclenchée lorsque la section 'eac-addon-image-promo' est chargée dans la page
		*
		* @param {selector} $scope. Le contenu de la section
		* @since 0.0.9
		*/
		widgetImagePromo: function($scope) {
			var $target = $scope.find('.eac-image-promo');
		},
		
		/**
		* Description: Cette méthode est déclenchée lorsque la section 'eac-addon-pinterest-rss' est chargée dans la page
		*
		* @param {selector} $scope. Le contenu de la section
		* @since 0.0.9
		* @since 1.5.2 Gestion 'Enter ou Return' dans l'input text
		*/
		widgetPinterestRss: function($scope) {
			var $targetInstance = $scope.find('.eac-pin-galerie'),
				$targetSelect = $scope.find('#pin_options_items'),
				$targetSelectedItem = $scope.find('#pin_item_name'),
				$targetButton = $scope.find('#pin__read-button'),
				$targetHeader = $scope.find('.pin-item-header'),
				$targetLoader = $scope.find('#pin__loader-wheel'),
				$target = $scope.find('.pin-galerie'),
				settings = $target.data('settings') || {},
				instanceAjax,
				proxy = 'proxy_rss.php';
				
			if(!settings.data_nombre || !settings.data_longueur) {
				return;
			}
			
			// Construction de l'objet de la requête Ajax
			instanceAjax = new ajaxCallFeed();
			
			// Première valeur de la liste par défaut
			$targetSelect.find('option:first').attr('selected', 'selected');
			$targetSelectedItem.val($targetSelect.eq(0).val());

			// Event change sur la liste des flux
			$targetSelect.on('change', function(e) {
				e.preventDefault();
				$targetSelectedItem.val($(this).val());
				$('.pin-galerie__item', $target).remove();
				$targetHeader.html('');
			});
			
			// @since 1.5.2 Enter ou return dans l'input text
			$targetSelectedItem.on("keyup", function(e) {
				if(e.which === 13) {
					e.preventDefault();
					$targetSelectedItem.blur();
					$targetButton.click();
				}
			});
		
			// Event click sur le bouton 'lire le flux'
			$targetButton.on('click touch', function(e) {
				e.preventDefault();
				$('.pin-galerie__item', $target).remove();
				$targetHeader.html('');
				
				if($targetSelectedItem.val().length !== 0) {
					// Initialisation de l'objet Ajax avec l'url du flux et le nom du proxy comme paramètres
					instanceAjax.init($targetSelectedItem.val().replace(/\s+/g, ''), proxy);
					$targetLoader.show();
				}
			});
			
			// L'appel Ajax est asynchrone, ajaxComplete, event global, est déclenché
			$(document).ajaxComplete(function(event, xhr, ajaxSettings) {
				if(ajaxSettings.ajaxOptions && ajaxSettings.ajaxOptions === instanceAjax.getOptions()) { // Le même random number généré lors de la création de l'objet Ajax
					event.stopImmediatePropagation();
					$targetLoader.hide();				
					
					// Les items à afficher
					var allItems = instanceAjax.getItems();
					
					// Une erreur Ajax ??
					if(allItems.headError) {
						$targetHeader.html('<span style="text-align:center; word-break:break-word;"><p>' + allItems.headError + '</p></span>');
						return false;
					}
					
					// Pas d'item
					if(! allItems.rss) {
						$targetHeader.html('<span style="text-align: center">Nothing to display</span>');
						return false;
					}
					
					var Items = allItems.rss;
					var Profile = allItems.profile;
					
					if(Profile.headLogo) { $targetHeader.html('<img class="eac-image-loaded" src="' + Profile.headLogo + '">'); }
					$targetHeader.append('<span><a href="' + Profile.headLink + '" target="_blank" rel="nofollow"><h2>' + Profile.headTitle + '</h2></a></span>');
					$targetHeader.append('<span>' + Profile.headDescription + '</span>');
							
					// Parcours de tous les items à afficher
					$.each(Items, function(index, item) {
						if(index >= settings.data_nombre) { // Nombre d'items à afficher
							return false;
						}
						
						var $wrapperItem = $('<div/>', { class :'pin-galerie__item ' + settings.data_style});
						var $wrapperContent = $('<div/>', { class : 'pin-galerie__content' });
						
						// Ajout du titre
						item.title = removeEmojis(item.title);
						item.title = item.title.split(' ', 12).join().replace(/,/g, " ") + '...'; // Afficher 12 mots dans le titre
						var titre = '<div class="pin-galerie__item-link-post"><a href="' + item.lien + '" target="_blank" rel="nofollow"><h2 class="pin-galerie__item-titre">' + item.title + '</h2></a></div>';
						$wrapperContent.append(titre);
						
						// Ajout de l'image
						if(item.img && settings.data_img) {
							var img = '';
							if(settings.data_lightbox) {
								// Suppression des " par des ' dans le titre
								img = '<div class="pin-galerie__item-image"><a href="' + item.imgLink + 
									'" data-elementor-open-lightbox="no" data-fancybox="pin-gallery" data-caption="' + item.title.replace(/"/g, "'") + '"><img class="eac-image-loaded" src="' + item.img + '"></a></div>';
							} else {
								img = '<div class="pin-galerie__item-image"><img class="eac-image-loaded" src="' + item.img + '"></div>';
							}
						$wrapperContent.append(img);
						}
						
						// Ajout du nombre de mots de la description
						item.description = removeEmojis(item.description);
						item.description = item.description.split(' ', settings.data_longueur).join().replace(/,/g, " ") + '[...]';
						//item.description = item.description.substring(0, settings.data_longueur) + '[...]';
						
						// Ajout de la description
						var description = '<div class="pin-galerie__item-description"><p>' + item.description + '</p></div>';
						$wrapperContent.append(description);
						
						// Ajout de la date de publication/Auteur article
						if(settings.data_date) {
							var dateUpdate =  '<div class="pin-galerie__item-date"><i class="fa fa-calendar" aria-hidden="true"></i>' + new Date(item.update).toLocaleDateString() + '</div>';
							var Auteur =  '<div class="pin-galerie__item-auteur"><i class="fa fa-user" aria-hidden="true"></i>' + item.author + '</div>';
							$wrapperContent.append(dateUpdate);
							if(item.author) {
								$wrapperContent.append(Auteur);
							}
						}
						
						// Ajout dans les wrappers
						$wrapperItem.append($wrapperContent);
						$target.append($wrapperItem);
					});	
					
					if($.fn.fancybox) {
						//console.info("fancyBox already initialized");
						$.fancybox.close();
					}
					
					// Modifie les dimensions des images après leur chargement
					$('[data-fancybox="pin-gallery"]', $target).fancybox({
						afterLoad : function(instance, current) {
							var pixelRatio = window.devicePixelRatio || 1;
							//if(pixelRatio < 1.5) {
								current.width  = current.width	* (pixelRatio * 1.5);
								current.height = current.height * (pixelRatio * 1.5);
							//}
							//console.log('PixelRatio:' + pixelRatio + ":W:" + current.width + "/" + current.$image[0].naturalWidth + ":H:" + current.height + "/" + current.$image[0].naturalHeight);
						}
					});
					setTimeout(function(){ $('.pin-galerie__item', $target).css({transition: 'all 500ms linear', transform: 'scale(1)'}); }, 200);
				}
			});
		},

		/**
		* Description: Cette méthode est déclenchée lorsque la section 'eac-addon-lecteur-rss' est chargée dans la page
		*
		* @param {selector} $scope. Le contenu de la section
		* @since 0.0.9
		* @since 1.3.1	Support audio, vidéo et PDF
		* @since 1.5.2	Gestion 'Enter ou Return' dans l'input text
		* @since 1.8.2	Suppression du style qui est mis en oeuvre dans le composant
		*				Ajout du lien de la page sur l'image
		*/
		widgetLecteurRss: function($scope) {
			var $targetInstance = $scope.find('.eac-rss-galerie'),
				$targetSelect = $scope.find('#rss__options-items'),
				$targetSelectedItem = $scope.find('#rss__item-name'),
				$targetButton = $scope.find('#rss__read-button'),
				$targetHeader = $scope.find('.rss-item__header'),
				$targetLoader = $scope.find('#rss__loader-wheel'),
				$target = $scope.find('.rss-galerie'),
				settings = $target.data('settings') || {},
				instanceAjax,
				proxy = 'proxy_rss.php',
				is_ios = /(Macintosh|iPhone|iPod|iPad).*AppleWebKit.*Safari/i.test(navigator.userAgent);
				
			if(!settings.data_nombre || !settings.data_longueur) {
				return;
			}
			
			// Construction de l'objet de la requête Ajax
			instanceAjax = new ajaxCallFeed();
			
			// Première valeur de la liste par défaut
			$targetSelect.find('option:first').attr('selected', 'selected');
			$targetSelectedItem.val($targetSelect.eq(0).val());
			
			// Event change sur la liste des flux
			$targetSelect.on('change', function(e) {
				e.preventDefault();
				$targetSelectedItem.val($(this).val());
				$('.rss-galerie__item', $target).remove();
				$targetHeader.html('');
			});
			
			// @since 1.5.2 Enter ou return dans l'input text
			$targetSelectedItem.on("keyup", function(e) {
				if(e.which === 13) {
					e.preventDefault();
					$targetSelectedItem.blur();
					$targetButton.click();
				}
			});
			
			// Event click sur le bouton 'lire le flux'
			$targetButton.on('click touch', function(e) {
				e.preventDefault();
				$('.rss-galerie__item', $target).remove();
				$targetHeader.html('');
				
				if($targetSelectedItem.val().length !== 0) {
					// Initialisation de l'objet Ajax avec l'url du flux et le nom du proxy comme paramètres
					instanceAjax.init($targetSelectedItem.val().replace(/\s+/g, ''), proxy);
					$targetLoader.show();
				}
			});
			
			// L'appel Ajax est asynchrone, ajaxComplete est déclenché
			$(document).ajaxComplete(function(event, xhr, ajaxSettings) {
				if(ajaxSettings.ajaxOptions && ajaxSettings.ajaxOptions === instanceAjax.getOptions()) { // Le même random number généré lors de la création de l'objet Ajax
					event.stopImmediatePropagation();
					$targetLoader.hide();
					
					// Les items à afficher
					var allItems = instanceAjax.getItems();
					
					// Une erreur Ajax ??
					if(allItems.headError) {
						$targetHeader.html('<span style="text-align:center; word-break:break-word;"><p>' + allItems.headError + '</p></span>');
						return false;
					}
					
					// Pas d'item
					if(! allItems.rss) {
						$targetHeader.html('<span style="text-align: center">Nothing to display</span>');
						return false;
					}
					
					var Items = allItems.rss;
					var Profile = allItems.profile;
					var $wrapperHeadContent = $('<div/>', { class: 'rss-item__header-content' });
					
					if(Profile.headLogo) {
						$targetHeader.append('<div class="rss-item__header-img"><a href="' + Profile.headLink + '" target="_blank" rel="nofollow"><img class="eac-image-loaded" src="' + Profile.headLogo + '"></a></div>');
					}
					$wrapperHeadContent.append('<span><a href="' + Profile.headLink + '" target="_blank" rel="nofollow"><h2>' + Profile.headTitle.substring(0, 27) + '...</h2></a></span>');
					$wrapperHeadContent.append('<span>' + Profile.headDescription + '</span>');
					$targetHeader.append($wrapperHeadContent);
					
					// Parcours de tous les items à afficher
					$.each(Items, function(index, item) {
						if(index >= settings.data_nombre) { // Nombre d'item à afficher
							return false;
						}
						
						/** @since 1.8.2 */
						var $wrapperItem = $('<div/>', { class :'rss-galerie__item'});
						var $wrapperContent = $('<div/>', { class : 'rss-galerie__content' });
						
						// Ajout du titre
						item.title = item.title.split(' ', 12).join().replace(/,/g, " ") + '...'; // Afficher 12 mots dans le titre
						var titre = '<div class="rss-galerie__item-link-post"><a href="' + item.lien + '" target="_blank" rel="nofollow"><h2 class="rss-galerie__item-titre">' + item.title + '</h2></a></div>';
						$wrapperContent.append(titre);
						
						/** @since 1.3.1 Ajout du support de l'audio, de la vidéo et du PDF */
						if(item.img && settings.data_img) {
							var img = '';
							var videoattr = '';
							if(item.img.match(/\.mp3|\.m4a/)) { // Flux mp3
								img =	'<div class="rss-galerie__item-image">' +
											'<audio controls preload="none" src="' + item.img + '" type="audio/mp3"></audio>' +
										'</div>';
							} else if(item.img.match(/\.mp4|\.m4v/)) { // Flux mp4
								videoattr = is_ios ? '<video controls preload="metadata" type="video/mp4">' : '<video controls preload="none" type="video/mp4">';
								img =	'<div class="rss-galerie__item-image">' +
											 videoattr +
												'<source src="' + item.img + '">' +
												"Your browser doesn't support embedded videos" +
											'</video>' +
										'</div>';
							} else if(item.img.match(/\.pdf/)) { // Fichier PDF
								img = '<div class="rss-galerie__item-image"><a href="' + item.imgLink + 
									'" data-elementor-open-lightbox="no" data-fancybox="rss-gallery" data-caption="' + item.title.replace(/"/g, "'") + '"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a></div>';
							} else if(settings.data_lightbox) { // Fancybox activée. Suppression des " par des ' dans le titre
								img = '<div class="rss-galerie__item-image"><a href="' + item.imgLink + 
									'" data-elementor-open-lightbox="no" data-fancybox="rss-gallery" data-caption="' + item.title.replace(/"/g, "'") + '"><img class="eac-image-loaded" src="' + item.img + '"></a></div>';
							} else if(settings.data_image_link) { // @since 1.8.2 Lien de l'article sur l'image
								img = '<div class="rss-galerie__item-image"><a href="' + item.lien + '" target="_blank" rel="nofollow"><img class="eac-image-loaded" src="' + item.img + '"></a></div>';
							} else {
								img = '<div class="rss-galerie__item-image"><img class="eac-image-loaded" src="' + item.img + '"></div>';
							}
						$wrapperContent.append(img);
						}
						
						// Ajout du nombre de mots de la description
						item.description = item.description.split(' ', settings.data_longueur).join().replace(/,/g, " ") + '[...]';
						// Ajout de la description
						var description = '<div class="rss-galerie__item-description"><p>' + item.description + '</p></div>';
						$wrapperContent.append(description);
						
						// Ajout de la date de publication/Auteur article
						if(settings.data_date) {
							var dateUpdate =  '<span class="rss-galerie__item-date"><i class="fa fa-calendar" aria-hidden="true"></i>' + new Date(item.update).toLocaleDateString() + '</span>';
							var Auteur =  '<span class="rss-galerie__item-auteur"><i class="fa fa-user" aria-hidden="true"></i>' + item.author + '</span>';
							$wrapperContent.append(dateUpdate);
							if(item.author) {
								$wrapperContent.append(Auteur);
							}
						}
						
						// Ajout dans les wrappers
						$wrapperItem.append($wrapperContent);
						$target.append($wrapperItem);
					});
					setTimeout(function(){ $('.rss-galerie__item', $target).css({transition: 'all 500ms linear', transform: 'scale(1)'}); }, 200);
				}
			});
		},

		/**
		* Description: Cette méthode est déclenchée lorsque la section 'eac-addon-articles-liste' est chargée dans la page
		*
		* @param {selector} $scope. Le contenu de la section
		* @since 0.0.9
		* @since 1.4.6	InfiniteScroll Supprime le chargement automatique des pages suivantes
		* @since 1.5.2	Correctif du chevauchement des items
		* @since 1.6.0	Événement 'change' sur la liste des filtres
		*				Supression de la méthode 'layout'
		* @since 1.7.0	La class 'al-image-loaded' est déjà charger dans le code PHP
		*/
		widgetArticlesListe: function($scope) {
			var $targetInstance = $scope.find('.eac-articles-liste'),
				$targetWrapper = $targetInstance.find('.al-posts__wrapper'),
				$imagesInstance = $targetWrapper.find('img'),
				settings = $targetWrapper.data('settings') || {},
				$targetId = $('#' + settings.data_id),
				$paginationId = $('#' + settings.data_pagination_id),
				targetStatus = '#' + settings.data_pagination_id + ' .al-page-load-status',
				targetButton = '#' + settings.data_pagination_id + ' button',
				instance = null,
				setIntervalIsotope = null,
				isotopeOptions = {
					itemSelector: '.al-post__wrapper', 
					percentPosition: true,
					masonry: {
						columnWidth: '.al-posts__wrapper-sizer',
						horizontalOrder: true,
					},
					layoutMode: settings.data_layout,
					sortBy: 'original-order',
					visibleStyle: { transform: 'scale(1)', opacity: 1 }, // Transition
					//hiddenStyle: { transform: 'scale(0.001)', opacity: 0 },
				};
			
			if($().isotope === undefined || $targetId.length === 0) {
				return;
			}
            
			// On est dans l'éditeur on ajoute un float left au bouton
			if(elementor.isEditMode()) {
			    //console.log("widgetArticlesListe::Instance: " + settings.data_pagination_id);
				//$('.al-pagination', $targetInstance).css('float', 'left');
			}
			
			// Force l'affichage des images pour contourner le lazyload
			$imagesInstance.each(function() {
				$(this).attr('src', $(this).data('src'));
				if($(this).complete) {
					$(this).load();
				}
			});
			
			// Init Isotope, charge les images et redessine le layout
			$targetId.isotope(isotopeOptions);
			// Get Isotope instance 
			var iso = $targetId.data('isotope')
			
			/** @since 1.7.0 */
			$targetId.imagesLoaded().progress(function(instance, image) {
				if(image.isLoaded) {
					//$(image.img).addClass('al-image-loaded');
					//console.log($targetId.selector + ":" + instance.progressedCount);
				}
			}).done(function(instance) {
				if(iso) {
					/** @since 1.6.0 Supression de la méthode 'layout' */
					$targetId.isotope();
					//console.log('Post Grid::Isotope initialized');
				} else {
					//console.log('Post Grid::Isotope DONE::NOT initialized');
				}
				
				// @since 1.5.2 Chevauchement des items. Redessine tous les items après 5 secondes
				if(navigator.userAgent.match(/SAMSUNG|SGH-[I|N|T]|GT-[I|P|N]|SM-[N|P|T|Z|G]|SHV-E|SCH-[I|J|R|S]|SPH-L/i))  {
					// Pas très élégant
					// Test Samsung phone UA: Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-G960U) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/10.2 Chrome/71.0.3578.99 Mobile Safari/537.36
					// https://developers.whatismybrowser.com/useragents/explore/software_name/samsung-browser/
					setIntervalIsotope = window.setInterval(function() {	$targetId.isotope(); /*console.log('Samsung phone::' + $targetId.selector);*/}, 5000);
				}
			}).fail(function(instance) {
				 console.log('Post Grid::Imagesloaded::All images loaded, at least one is broken');
			});
			
			// Les filtres sont affichés
			if(settings.data_filtre) {
				// Événement click sur les filtres par défaut
				$('#al-filters__wrapper a', $targetInstance).on('click', function(e) {
					var $this = $(this);
					// L'item du filtre est déjà sélectionné
					if($this.parents('.al-filters__item').hasClass('al-active')) {
						return false;
					}
					
					var $optionSet = $this.parents('#al-filters__wrapper');
					$optionSet.find('.al-active').removeClass('al-active');
					$this.parents('.al-filters__item').addClass('al-active');
					// Applique le filtre
					var selector = $this.attr('data-filter');
					$targetId.isotope({filter: selector}); // Applique le filtre
					return false;
				});
				
				// @since 1.6.0 Lier les filtres select/option de la liste à l'événement 'change'
				$('.al-filter__select', $targetInstance).on('change', function() {
					// Récupère la valeur du filtre avec l'option sélectionnée
					var filterValue = this.value;
					// Applique le filtre
					$targetId.isotope({filter: filterValue});
					return false;
				});
			}
			
			// La div status est affichée
			if($paginationId.length > 0) {
				if(top.location.href !== self.location.href) {
					//console.log('Top # self IFRAME:' + top.location.href + ":" + self.location.href);
					//top.location.href = self.location.href;
				}
				
				// Initialisation infiniteScroll
				$targetId.infiniteScroll({
					path: function() { return location.pathname.replace(/\/?$/, '/') + "page/" + parseInt(this.pageIndex + 1); },
					debug: false,
					button: targetButton,	// load pages on button click
					scrollThreshold: false,	// enable loading on scroll @since 1.4.6. false for disabling loading on scroll
					status: targetStatus,
					history: false,
					horizontalOrder: false,
				});
				
				// get infiniteScroll instance
				var infScroll = $targetId.data('infiniteScroll');
				
				// Les nouveaux articles sont chargés
				$targetId.on('load.infiniteScroll', function(event, response, path) {
					var selectedItems = '.' + settings.data_article + '.al-post__wrapper';
					//console.log("load.infiniteScroll: " + path + "::Class: " + selectedItems + "::height: " + window.innerHeight / 2);
					
					// Recherche les nouveaux items
					var $items = $(response).find(selectedItems);
					//console.info($items);
					$targetId.append($items).isotope('appended', $items);
					$targetId.imagesLoaded(function(){ $targetId.isotope('layout');	});
					
					// On teste l'égalité entre le nombre de page totale et celles chargées dans infiniteScroll
					// lorsque le pagging s'applique sur une 'static page' ou 'front page'
					if(parseInt(infScroll.pageIndex) >= parseInt(settings.data_max_pages)) {
						$targetId.infiniteScroll('destroy'); // Destroy de l'instance
						$paginationId.remove(); // Supprime la div status
					} else {
						$('.al-more-button-paged', $targetInstance).text(infScroll.pageIndex); // modifie l'index courant du bouton 'MORE'
					}
				});
			}
		},

		/**
		* Description: Cette méthode est déclenchée lorsque la section 'eac-addon-slider-pro' est chargée dans la page
		*
		* @param {selector} $scope. Le contenu de la section
		* @since 0.0.9
		* @since 1.8.7	Implémente les custom breakpoints
		*/
		widgetSliderPro: function($scope) {
			var $target = $scope.find('.slider-pro'),
				$imagesInstance = $target.find('img'),
				settingsAnime = $target.data('settingsanime') || {},
				instance = null,
				instanceTransition = {},
				$targetId,
				slideIndex = 0,
				defaultTexteAnimation,	// Animation Titre/Texte par défaut
				defaultSettings = {
					width: 600,
					height: 450,
					arrows: false,
					fadeArrows: true,
					buttons: true,
					waitForLayers: true,
					fade: false,
					autoplay: false,
					visibleSize: 'auto',
					forceSize: 'none',
					orientation: 'horizontal',
					thumbnailsPosition: 'bottom',
					thumbnailWidth: 120,
					thumbnailHeight: 80,
					thumbnailPointer: false,
					autoplayDelay: 5000,
					autoSlideSize: true,
					rightToLeft: false,
					//imageScaleMode: 'contain',
					slideDistance: 5,  // Défaut 10
					breakpoints: {
						1024: {
								//forceSize: 'none',
								//visibleSize: 'auto',
								thumbnailsPosition: 'bottom',
								thumbnailWidth: 120,
								thumbnailHeight: 80
						},
						767: {
								//forceSize: 'none',
								//visibleSize: 'auto',
								orientation: 'horizontal',
								thumbnailsPosition: 'bottom',
								thumbnailWidth: 80,
								thumbnailHeight: 50,
								thumbnailPointer: false
						}
					},
					init: function() {
						// Instance objet animation titre/texte
						instanceTransition = new createTransition();
						
						// On positionne l'animation Titre/Texte
						if($(window).width() <= EacAddonsElements.windowWidthMob) { defaultTexteAnimation = settingsAnime.data_anmpos_mob; }
						else if($(window).width() <= EacAddonsElements.windowWidthTab) { defaultTexteAnimation = settingsAnime.data_anmpos_tab; }
						else { defaultTexteAnimation = settingsAnime.data_anmpos; }
						
						// On charge les deux container Titre/Texte
						var $containerTitle = $targetId.find('.spro-slide-' + slideIndex + ' .spro-slide-title');
						var $containerDesc = $targetId.find('.spro-slide-' + slideIndex + ' .spro-slide-desc');
						
						// Ils existent, on lance le reset de la position et on lance l'animation pour le Titre/Texte
						if($containerTitle.length) {
							$containerTitle.transition(instanceTransition.resetTransitionTitle($targetId, slideIndex))
							.transition(instanceTransition.getTransitionTitle($targetId, slideIndex, defaultTexteAnimation), 700, 'cubic-bezier(0.175, 0.885, 0.32, 1.275)');
						}
						
						if($containerDesc.length) {
							$containerDesc.transition(instanceTransition.resetTransitionDesc($targetId, slideIndex, defaultTexteAnimation))
							.transition(instanceTransition.getTransitionDesc($targetId, slideIndex, defaultTexteAnimation), 1500, 'easeOutBack');
						}
					}
				},
				instanceSettings = $target.data('settings') || {},
				settings = $.extend(defaultSettings, instanceSettings);
			
			// L'ID cible
			$targetId = $('#' + settingsAnime.data_id);
			if($targetId.length === 0) {
				return; // Aucun item à charger
			}
			
			// Aucune image à charger
			if($imagesInstance.length === 0) {
				return;
			}
			
			// Force l'affichage des images pour contourner le lazyload
			$imagesInstance.each(function() {
				$(this).attr('src', $(this).data('src'));
				if($(this).complete) {
					$(this).load();
				}
			});
			
			// Chargement des images
			$target.imagesLoaded().progress(function(instance, image) {
				if(image.isLoaded) {
					if($(image.img).hasClass('sp-image')) {
						$(image.img).addClass('spro-image-loaded');
					}
				}
			}).done(function(instance) {
				$target.addClass('spro-slider-loaded');
				// Instance de sliderPro
				$targetId.sliderPro(settings);
			});
			
			// Intercepte l'événement début d'affichage du slide
			$targetId.on('gotoSlide', function(event) {
				slideIndex = event.index;					// L'index du slide courant
				var slideIndexPrec = event.previousIndex;	// L'index du slide précédent
				
				// Afficher le titre
				if(settingsAnime.data_titre) {
					var $containerTitle = $targetId.find('.spro-slide-' + slideIndex + ' .spro-slide-title');
					
					// Renvoie le titre du slide courant à ses coordonnées d'attente
					$containerTitle.transition(instanceTransition.resetTransitionTitle($targetId, slideIndex));
					
					// Renvoie le titre du slide précédent à ses coordonnées d'attente
					var $containerTitlePrec = $targetId.find('.spro-slide-' + slideIndexPrec + ' .spro-slide-title');
					$containerTitlePrec.transition(instanceTransition.resetTransitionTitle($targetId, slideIndexPrec));
				}
				// Afficher la description
				if(settingsAnime.data_desc) {
					var $containerDesc = $targetId.find('.spro-slide-' + slideIndex + ' .spro-slide-desc');
					
					// Renvoie le texte du slide courant à ses coordonnées d'attente
					$containerDesc.transition(instanceTransition.resetTransitionDesc($targetId, slideIndex, defaultTexteAnimation));
					
					// Renvoie le texte du slide précédent à ses coordonnées d'attente
					var $containerDescPrec = $targetId.find('.spro-slide-' + slideIndexPrec + ' .spro-slide-desc');
					$containerDescPrec.transition(instanceTransition.resetTransitionDesc($targetId, slideIndexPrec, defaultTexteAnimation));
				}
			});
			
			// Intercepte l'événement fin d'affichage du slide
			$targetId.on('gotoSlideComplete', function(event) {
				// Affiche le titre
				if(settingsAnime.data_titre) {
					var $containerTitle = $targetId.find('.spro-slide-' + slideIndex + ' .spro-slide-title');
					$containerTitle.transition(instanceTransition.getTransitionTitle($targetId, slideIndex, defaultTexteAnimation), 700, 'cubic-bezier(0.175, 0.885, 0.32, 1.275)');
				}
				
				// Affiche la description
				if(settingsAnime.data_desc) {
					var $containerDesc = $targetId.find('.spro-slide-' + slideIndex + ' .spro-slide-desc');
					$containerDesc.transition(instanceTransition.getTransitionDesc($targetId, slideIndex, defaultTexteAnimation), 1500, 'easeOutBack');
				}
			});
			
			// Intercepte l'événement resize du slider
			$targetId.on('sliderResize', function(event) {
				// On change la position de l'animation Titre/Texte
				if($(window).width() <= EacAddonsElements.windowWidthMob) { defaultTexteAnimation = settingsAnime.data_anmpos_mob; }
				else if($(window).width() <= EacAddonsElements.windowWidthTab) { defaultTexteAnimation = settingsAnime.data_anmpos_tab; }
				else { defaultTexteAnimation = settingsAnime.data_anmpos; }
			});
			
			// Intercepte les points de rupture et force les largeurs/hauteurs des images
			$targetId.on('breakpointReach', function(event) {
				if(event.size === EacAddonsElements.windowWidthTab) { // Tablette
					event.settings.width = settingsAnime.data_width_tab;
					event.settings.height = settingsAnime.data_height_tab;
				} else if(event.size === EacAddonsElements.windowWidthMob) { // Mobile
					event.settings.width = settingsAnime.data_width_mob;
					event.settings.height = settingsAnime.data_height_mob;
				}
				//console.log('Break-point reach::' + event.size + '::' + event.settings.width + '::' + event.settings.height);
			});
			
		},

		/**
		* Description: Cette méthode est déclenchée lorsque la section 'eac-addon-kenburn-slider' est chargée dans la page
		*
		* @param {selector} $scope. Le contenu de la section
		* @since 0.0.9
		*/
		widgetKenBurnSlider: function($scope) {
			var $target = $scope.find('.kbs-slides'),
				$imagesInstance = $target.find('img'),
				settings = $target.data('settings'),
				KBOptions = {
					effectDuration:	6000,
					effectModifier:	1.4,
					effect:	'panUp',
					effectEasing: 'ease-in-out',
					captions: 'false',
					navigation: 'false',
				};
			
			if (! $target.length || ! settings) {
				return;
			}
			
			$.extend(KBOptions, settings);
			
			$imagesInstance.each(function() {
				$(this).attr('src', $(this).data('src'));
				if($(this).complete) {
					$(this).load();
				}
			});
			
			$target.imagesLoaded().progress(function(instance, image) {
				if(image.isLoaded) {
					$(image.img).addClass('kbs-image-loaded');
				}
			}).done(function() {
				// Création de l'objet de l'effet KB
				$('#' + settings.data_id).smoothSlides(KBOptions);
			});
		},

		/**
		 * Description: Cette méthode est déclenchée lorsque la section 'eac-addon-images-comparison' est chargée dans la page
		 *
		 * @param {selector} $scope. Le contenu de la section
		 * @since 0.0.9
		 * @since 1.8.7	Passage de paramètres au plugin
		 */
		widgetImageComparison: function($scope) {
			var $target = $scope.find('.images-comparison'),
				$imagesInstance = $target.find('img'),
				settings = $target.data('settings'),
				instance = null,
				targetInstanceWidth,
				leftTitle = 'Default title',
				rightTitle = 'Default title';
			
			// Erreur settings
			if(Object.keys(settings).length === 0) {
				return;
			}
			
			// Les libellé des titres gauche et droite
			leftTitle = settings.data_title_left;
			rightTitle = settings.data_title_right;
			
			// Contourner les lazy-load
			$imagesInstance.each(function() {
				$(this).attr('src', $(this).data('src'));
				if($(this).complete) {
					$(this).load();
				}
			});
			
			$target.imagesLoaded().progress(function(instance, image) {
				if(image.isLoaded) {
					$(image.img).addClass('eac-image-loaded ic-image-loaded');
				}
			}).done(function() {
				/**
				 * Toutes les images sont chargées, on instancie l'object
				 * @since 1.8.7 Passage des paramètres Title left et right
				 */
				$(settings.data_diff).simpleImageDiff({titles: {before: leftTitle, after: rightTitle}});
			});
		},

		/**
		* Description: Cette méthode est déclenchée lorsque la section 'eac-addon-image-galerie' est chargée dans la page
		*
		* @param {selector} $scope. Le contenu de la section
		* @since 0.0.9
		* @since 1.5.3	Gestion responsive du mode 'justify' pour les mobiles
		* @since 1.6.0	Supression de la méthode 'layout'
		* @since 1.6.7	Ajout du mode Metro
		*				Suppression de l'option 'isotopeOptions = masonry:horizontalOrder'
		* @since 1.8.7	Implémente les custom breakpoints
		*/	
		widgetImageGalerie: function($scope) {
			var $targetInstance = $scope.find('.eac-image-galerie'),
				$target = $targetInstance.find('.image-galerie'),
				$imagesInstance = $targetInstance.find('.image-galerie__image-instance'),
				$overlayInstance = $targetInstance.find('.image-galerie__item'),
				$targetSizer = $targetInstance.find('.image-galerie__item-sizer'),
				instance = null,
				settings = $target.data('settings'),
				$targetId = $('#' + settings.data_id),
				isotopeOptions = {
					itemSelector: '.image-galerie__item', 
					percentPosition: true,
					masonry: {
						columnWidth: '.image-galerie__item-sizer',
					},
					layoutMode: settings.layoutType,
					sortBy: 'original-order',
					visibleStyle: { transform: 'scale(1)', opacity: 1 }, // Transition
				},
				resizeTimer = null;
			
			EacAddonsElements.widgetImageGalerie.collage = function() {
				$targetId.imagesLoaded().progress(function(instance, image) {
					if(image.isLoaded) {
						if($(image.img).hasClass('image-galerie__image-instance')) {
							$(image.img).addClass('ig-image-loaded');
						}
					}	
				}).done(function() { /** @since 1.5.3 Calcul de la hauteur du mode 'justify' notamment pour les mobiles */
					var justifyHeight = $(window).width() <= EacAddonsElements.windowWidthMob ? settings.gridHeightM : $(window).width() <= EacAddonsElements.windowWidthTab ? settings.gridHeightT : settings.gridHeight;
					$targetId.collagePlus({'targetHeight':justifyHeight, 'allowPartialLastRow':true});
				});
			};
			
			// Pas d'image
			if ($imagesInstance.length === 0 || $targetId.length === 0) {
				return;
			}
			
			/** @since 1.6.7 Applique le mode Metro à la première image */
			if(settings.data_metro) {
				$overlayInstance.eq(0).addClass('layout-type-metro');
			}
			
			// Contourner les lazy-load
			$imagesInstance.each(function() {
				$(this).attr('src', $(this).data('src'));
				if($(this).complete) {
					$(this).load();
				}
			});
			
			// Mode justify
			if('justify' === settings.layoutType) {
				// Supprime la div sizer utilisée uniquement pour les modes Masonry
				$targetSizer.remove();
				// Appel de la fonction collage
				EacAddonsElements.widgetImageGalerie.collage();
				
				$(window).on('resize.collageplus', function() {
					// set a timer to re-apply the plugin
					if(resizeTimer) clearTimeout(resizeTimer);
					resizeTimer = setTimeout(EacAddonsElements.widgetImageGalerie.collage(), 1000);
				});
			} else { // Mode masonry et grille
				if(resizeTimer) {
					clearTimeout(resizeTimer);
					$(window).off('resize.collageplus');
				}
				
				/** @since 1.4.6 Instance Isotope avant imagesLoaded */
				$targetId.isotope(isotopeOptions);
				
				// Get Isotope instance 
				var iso = $targetId.data('isotope')
			
				// Redessine isotope après imagesLoaded
				$targetId.imagesLoaded().progress(function(instance, image) {
					if(image.isLoaded) {
						$(image.img).addClass('ig-image-loaded');
						//console.log($targetId.selector + ":" + instance.progressedCount);
					}
				}).done(function(instance) {
					if(iso) {
						/** @since 1.6.0 Supression de la méthode 'layout' */
						$targetId.isotope();
						//console.log('Image Gallery::Isotope initialized');
					} else {
						//console.log('Image Gallery::Isotope DONE::NOT initialized');
					}
				}).fail(function(instance) {
					console.log('Image Gallery::Imagesloaded::All images loaded, at least one is broken');
				});
			}
			
			// Les filtres sont affichés
			if(settings.data_filtre && 'justify' !== settings.layoutType) {
				// Évènement click sur les filtres par défaut
				$('#ig-filters__wrapper a', $targetInstance).on('click', function(e) {
					var $this = $(this);
					// L'item du filtre est déjà sélectionné
					if($this.parents('.ig-filters__item').hasClass('ig-active')) {
						return false;
					}
					
					var $optionSet = $this.parents('#ig-filters__wrapper');
					$optionSet.find('.ig-active').removeClass('ig-active');
					$this.parents('.ig-filters__item').addClass('ig-active');
					// Applique le filtre
					var selector = $this.attr('data-filter');
					$targetId.isotope({filter: selector}); // Applique le filtre
					return false;
				});
				
				// @since 1.6.0 Lier les filtres select/option de la liste à l'événement 'change'
				$('.ig-filter__select', $targetInstance).on('change', function() {
					// Récupère la valeur du filtre avec l'option sélectionnée
					var filterValue = this.value;
					// Applique le filtre
					$targetId.isotope({filter: filterValue});
					return false;
				});
			}
			
			// On est dans l'éditeur et les titre/texte sont sur l'image
			if(elementor.isEditMode() && settings.posoverlay === 'overlay-in') {
				// Ajout du bouton toggle overlay
				$targetInstance.append('<div style="text-align:center; margin-top: 10px;"><button id="toggle_image_galerie">Toggle Overlay</button><div>');
				$("#toggle_image_galerie", $targetInstance).click(function(e) { e.preventDefault(); $overlayInstance.toggleClass("hovered"); });
			}
		},
	};
	
	/**
	* Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	*
	* @return (object) Initialise l'objet EacAddonsElements
	* @since 0.0.9
	*/
	$(window).on('elementor/frontend/init', EacAddonsElements.init);
	
}(jQuery, window.elementorFrontend));

	/**----------------------------------------------------------------------------------------------*/

	var $ = jQuery;
	
	// Après le chargement de la page. Lazyload, Fancybox, fitText (Thème Hueman)
	window.addEventListener("load", function(event) {
		if(typeof lazyload === 'function' || typeof Lazyload === 'function' || typeof lazyLoad === 'function' || typeof LazyLoad === 'function') {
			console.log('Lazyloaded...');
		}
		
		// Pu.... de gestion des font-size dans le theme Hueman
		if($().fitText) {
			//console.log('Events Window =>', $._data($(window)[0], "events"));
			$(':header').each(function() {
				$(this).removeAttr('style');
				$(window).off('resize.fittext orientationchange.fittext');
				$(window).unbind('resize.fittext orientationchange.fittext');
			});
		}
		
		// Implémente le proto startsWith pour IE11
		if (!String.prototype.startsWith) {
			String.prototype.startsWith = function(searchString, position) {
				position = position || 0;
				return this.substr(position, searchString.length) === searchString;
			};
		}
		
		if($.fancybox) { eacInitFancyBox(); }
		
		/*if($('body').hasClass('elementor-editor-active')) {
		    $('body').find('.elementor-tags-list__teaser').remove();
		}*/
	});
	
	/**------------------------------- Functions partagées par toutes les functions anonymes ---------------------*/
	
	var isMobile = function() {
		return (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent));
	};
				
	/**
	* Object eacInitFancyBox 
	* Initialise la fancybox pour télécharger les images au format 'jpg' uniquement
	*
	* @return {nothing}
	* @since 1.5.3
	*/
	var eacInitFancyBox = function() {
		var language = window.navigator.userLanguage || window.navigator.language;
		var lng = language.split("-");
		$.fancybox.defaults.lang = lng[0];
	};

	var eacInitFancyBoxDownloadButton = function() {
		
		$.fancybox.defaults.buttons = ["zoom","thumbs","close"]; // Pb sur le fetch cros-origin. Supprime le bouton downloadEac
		
		// Affecte l'url de l'image au bouton download avant son affichage
		$(document).on('beforeShow.fb', function(e, instance, slide) {
			$('#downloadEac').attr('href', instance.current.src);
		});
		
		// Délégation d'event sur le bouton download
		// Récupère l'url 'a href' du bouton positionnée avec l'événement 'beforeShow.fb'
		$(document).on('click', '#downloadEac', function(e) {
			e.preventDefault();
			var filename = '';
			var src = $(this).attr('href');
			var ext = (src.match(/\.([^.]*?)(?=\?|#|$)/) || [])[1];
			if($.inArray(ext, ['jpg','jpeg','png']) !== -1) { filename = "eac-media_" + Math.random().toString(36).substr(2, 10) + "." + ext; }
			else { filename = "eac-media_" + Math.random().toString(36).substr(2, 10) + ".jpg" }
			var initFetch = { method:'GET', headers:{ "Content-Type":"application/x-www-form-urlencoded;charset=UTF-8"} };
			
			if(window.fetch && src) {
				window.fetch(src, initFetch)
				.then(function(response) { return response.blob(); })
				.then(function(blob) {
					var url = window.URL.createObjectURL(blob);
					var ahref = document.createElement('a');
					ahref.style.display = 'none';
					ahref.href = url;
					// Le nom du fichier de la sauvegarde
					ahref.download = filename;
					document.body.appendChild(ahref);
					ahref.click();
					window.URL.revokeObjectURL(url);
					document.body.removeChild(ahref);
					//alert('your file has downloaded!');
				}).catch(function(error) { window.alert('Ooops! ' + error.message); });
			}
		});
	};
	
	/**
	* Object setSelectOptionsCookies 
	* Lit tous les cookies et ajoute les options dans le select correspondant
	*
	* @param (string) debcookie le début du nom du cookie
	* @param (string) target le selector de la liste à amender
	* @return {nothing}
	* @since 1.4.0
	*/
	var setSelectOptionsCookies = function(debcookie, $target) {
		if (navigator.cookieEnabled) {
			var ca = document.cookie.split(';');
			for(var i = 0; i < ca.length; i++) {
				var rec = ca[i].trim();
				if(rec.startsWith(debcookie)) {
					var cook = rec.split('=');
					var name = cook[0].split('#')[1];
					var valeur = cook[1];
					$target.append('<option value="' + valeur + '" selected="selected">' + name + '</option>');
					//console.log('Cookie: ' + name + ' :: ' + valeur);
				}
			}
		}
	};

	/**
	* Object showOsmMap 
	* Construit la map OSM dans la div target de l'objet passé en paramètre
	*
	* @param mapData {json} les paramètres de la map au format JSON
	* @return {nothing}
	* @since 1.4.0
	*/
	var showOsmMap = function(mapData) {
		var map = L.map(mapData.mapDiv).setView([mapData.lat, mapData.lng], 12);
		
		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
		}).addTo(map);
		
		L.marker([mapData.lat, mapData.lng]).addTo(map)
			.bindPopup(mapData.popupContent)
			.openPopup();
		
		// Supprime le zoom roulette de la souris
		map.scrollWheelZoom.disable();
		// Supprime le fond de carte draggable
		map.dragging.disable();
		
		return map;
	};
	
	/**
	* Object getCurrentLocation 
	* Récupère la position du client (son adresse IP)
	*
	* @return {object coordonnées - Lat & Lng & accuracy}
	* @since 1.5.4
	*/
	var getCurrentLocation = function() { // Récupère la position.
		var options = { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 };
		return new Promise(function(resolve, reject) { navigator.geolocation.getCurrentPosition(resolve, reject, options); });
	};
	
	/**
	* Object getGeoPosition 
	* Calcule la distance Km et Miles entre la position du client et les coordonnées passées en paramètre
	*
	* @param mapLat La latitude du point recherché
	* @param mapLng La longitude du point recherché
	* @return {object {string, string}
	* @since 1.5.4
	*/
	var getGeoPosition = async function(mapLat, mapLng) {
		// Le host n'est pas HTTPS et 127.0.0.1 ou ne supporte pas la geo localisation
		if((window.location.protocol !== 'https:' && window.location.host !== '127.0.0.1') || !navigator.geolocation) {
			return false;
		}
		
		// Récupère les info avec Nominatim (Adresse, localité et pays)
		var pos = await getCurrentLocation();
		
		var frome = L.latLng(pos.coords.latitude, pos.coords.longitude);
		var toe = L.latLng(mapLat, mapLng);
		var distanceK = Math.round(frome.distanceTo(toe).toFixed() / 1000);
		var distanceM = Math.round(Math.round(frome.distanceTo(toe).toFixed() / 1000) / 1.609);
		//console.log("Distance func getGeoPosition : " + distanceK + " Kms" + "::" + distanceM + " Miles");
		
		return { km: distanceK, miles: distanceM };
	};
	
	/**
	* Object showLikesComments
	* Extrait (Items) et affecte les données likes et les commentaires dans la div target
	*
	* @return {nothing}
	* @since 1.3.0
	*/
	var showLikesComments = function(Items, $target, prefixUrl) {
		$target.html('');
		Items.sort(sort_by('update', true, parseInt));
		
		$.each(Items, function(indice, item) {
			var $wrapperItem = $('<span/>');
			
			var date = item.update !== '' ? new Date(item.update * 1000).toLocaleDateString() : '';
			var fullname = item.full_name.substring(0, 10) + '..';
			//var comment = item.comment !== '' ? item.comment.substring(0, 42) + '...' : '';
			var comment = item.comment;
			var img;
			if(comment !== '') {
				img =
					'<figure>' + 
						'<a href="' + prefixUrl + item.username + '/" target="_blank" rel="nofollow">' +
							'<span>' + fullname + '</span>' +
							'<img src="' + item.profile_pic_url + '" title="' + comment + '" alt="' + item.full_name + '">' +
							'<figcaption>' + date + '</figcaption>' +
						'</a>' +
					'</figure>';
			} else {
				img =
					'<figure>' + 
						'<a href="' + prefixUrl + item.username + '/" target="_blank" rel="nofollow">' +
							'<span>' + fullname + '</span>' +
							'<img src="' + item.profile_pic_url + '" title="' + item.full_name + '" alt="' + item.full_name + '">' +
						'</a>' +
					'</figure>';
			}
			
			$wrapperItem.append(img);
			$target.append($wrapperItem);
		});
	};

	/**
	* Object showStories
	* Extrait (Items) et affecte les données stories dans la div target
	*
	* @return {nothing}
	* @since 1.3.1
	*/
	var showStories = function(Items, $target) {
		$target.html('');
				
		$.each(Items, function(indice, item) {
			var $wrapperItem = $('<span/>');
			
			var title = item.title.substring(0, 10) + '..';
			var url = item.url;
			var img =
					'<figure>' + 
						'<a href="' + url + '/" target="_blank" rel="nofollow">' +
							'<img src="' + item.pic_url + '" alt="' + title + '">' +
							'<figcaption>' + title + '</figcaption>' +
						'</a>' +
					'</figure>';
					
			$wrapperItem.append(img);
			$target.append($wrapperItem);
		});
	};
	
	/**
	* Object showSuggestedUser
	* Extrait (Items) et affecte les données suggested user dans la div target
	*
	* @return {nothing}
	* @since 1.4.2
	*/
	var showSuggestedUser = function(Items, $target) {
		$target.html('');
				
		$.each(Items, function(indice, item) {	
			var $wrapperItem = $('<span/>');
			
			var title = item.full_name.substring(0, 10) + '..';
			var url = item.username;
			var pic = item.profile_pic_url;
			var img =
					'<figure>' + 
						'<a href="' + url + '/" target="_blank" rel="nofollow">' +
							'<img src="' + pic + '" alt="' + title + '">' +
							'<figcaption>' + title + '</figcaption>' +
						'</a>' +
					'</figure>';
					
			$wrapperItem.append(img);
			$target.append($wrapperItem);
		});
	};
	
	/**
	* Object showTaggedPosts
	* Extrait (Items) et affecte les données tagged posts dans la div target
	*
	* @return {nothing}
	* @since 1.4.3
	*/
	var showTaggedPosts = function(Items, $target) {
		$target.html('');
		Items.sort(sort_by('update', false, parseInt));
		
		$.each(Items, function(indice, item) {	
			var $wrapperItem = $('<span/>');
			
			var title = item.username.substring(0, 10) + '..';
			var url = item.linkNode;
			var pic = item.thumbnail_src;
			var date = item.update;
			var img =
					'<figure>' + 
						'<a href="' + url + '/" target="_blank" rel="nofollow">' +
							'<span>' + date + '</span>' +
							'<img src="' + pic + '" alt="' + title + '">' +
							'<figcaption>' + title + '</figcaption>' +
						'</a>' +
					'</figure>';
					
			$wrapperItem.append(img);
			$target.append($wrapperItem);
		});
	};
	
	/**
	* Object getBackgroundImageSize
	* Récupère les dimensions du style 'background-image' d'une image
	*
	* @return la largueur et la hauteur de l'image
	* @since 0.0.9
	*/
	var getBackgroundImageSize = function(el) {
		var imageUrl = $(el).css('background-image').match(/^url\(["']?(.+?)["']?\)$/);
		var dfd = new $.Deferred();
		
		if(imageUrl) {
			var image = new Image();
			image.onload = dfd.resolve;
			image.onerror = dfd.reject;
			image.src = imageUrl[1];
		} else {
			dfd.reject();
		}

		return dfd.then(function() {
			return { width: this.width, height: this.height };
		});
	};

	/**
	* Object sort_by
	* Objet de trie des données. Trie sur la date pas implémenté
	* Tri sur un entier descendant : Array.sort(sort_by('price', true, parseInt));
	* Tri sur une chaine, insensible à la casse: Array.sort(sort_by('city', false, function(a){return a.toUpperCase()}));
	*
	* @return {object[]} le tableau de données JSON trié
	* @since 0.0.9
	*/
	var sort_by = function(field_name, reverse, initial) {
		var key = initial ? function(x) { return initial(x[field_name]); } : function(x) { return x[field_name]; };
		reverse = !reverse ? 1 : -1;
		return function(x, y) { return x = key(x), y = key(y), reverse * ((x > y) - (y > x)); };
	};

	/**
	* Object removeEmojis
	* Suppression de tous les emojis d'une chaine de caratères
	*
	* @return {string} nettoyée de tous les emojis
	* @since 0.0.9
	*/
	var removeEmojis = function(myString) {
		if(!myString) { return ''; }
		return myString.replace(/([#0-9]\u20E3)|[\xA9\xAE\u203C\u2047-\u2049\u2122\u2139\u3030\u303D\u3297\u3299][\uFE00-\uFEFF]?|[\u2190-\u21FF][\uFE00-\uFEFF]?|[\u2300-\u23FF][\uFE00-\uFEFF]?|[\u2460-\u24FF][\uFE00-\uFEFF]?|[\u25A0-\u25FF][\uFE00-\uFEFF]?|[\u2600-\u27BF][\uFE00-\uFEFF]?|[\u2900-\u297F][\uFE00-\uFEFF]?|[\u2B00-\u2BF0][\uFE00-\uFEFF]?|(?:\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDEFF])[\uFE00-\uFEFF]?/g, '');
	};

	/**
	* Object ajaxCallFeed
	*
	* Appel Jquery Ajax pour lire les flux Rss, Pinterest et Instagram
	*
	* @return {object[]} Tableau d'objets au format JSON
	* @since 0.0.9
	* @since 1.4.0	Gestion des tokens Instagram (Token et Rollout)
	*				Mode debugging
	* @since 1.4.9	Implémente la pagination pour les requêtes Instagram User et Explore
	* @since 1.5.1	Implémente le téléchargement des vidéos
	* @since 1.5.4	Modification de la requête de téléchargement des vidéos84
	* @since 1.6.0	Ajout de la méthode 'setUserProfilAccount' pour stocker une partie du profil d'un utilisateur
	* @since 1.6.2	Modification des propriétés du profile d'un utilisateur
	*/
	var ajaxCallFeed = function() {
		var self = this,
			allItems = [],
			item = {},
			profilData = {profile:{}},	// @since 1.6.0
			acr_opts = Math.random().toString(36).substr(2, 10), // Génère un nombre aléatoire unique pour l'instance courante
			acr_debug = (location.search.split('dbug=')[1] || '').split('?')[0], // @since 1.4.0
			acr_proxy,
			acr_url,
			acr_requestedData,
			acr_instagram,
			acr_search,
			acr_tagname,        // @since 1.5.4
			acr_token = '',		// @since 1.4.0
			acr_rollout = '',	// @since 1.4.0
			acr_cursor = '',	// @since 1.4.9
			acr_pageid = '',	// @since 1.4.9
			acr_videoSize = 20; // @since 1.5.1
		
		/**
		 * @since 1.6.0 Création du profile utilisateur
		 * @since 1.6.2 Profile utilisateur modifié
		 */
		self.setUserProfilAccount = function(userData) {
			profilData.profile.id = userData.pk;
			profilData.profile.username = userData.username;
			profilData.profile.full_name = userData.full_name;
			profilData.profile.is_private = userData.is_private;
			profilData.profile.profile_pic_url = userData.profile_pic_url;
			profilData.profile.is_verified = userData.is_verified;
			profilData.profile.publication = userData.publication;
			profilData.profile.mutual_followed_count = userData.mutual_followed_count;
			
			var fbc = parseFloat(userData.edge_followed_by.count);
			var fc = fbc > 1000000 ? (fbc / 1000000).toFixed(1) + "m" : fbc > 1000 ? (fbc / 1000).toFixed(1) + "k" : fbc;
			profilData.profile.follower_count = fc;
			profilData.profile.follower_by_count = userData.edge_followed_by.count;
			
			profilData.profile.biographie = userData.biography;
			profilData.profile.siteWeb = userData.external_url;
			
			profilData.profile.highlight_reel_count = userData.highlight_reel_count;
			profilData.profile.edge_related_profiles = userData.edge_related_profiles.edges.length;
			//profilData.profile.tagged_posts = userData.edge_user_to_photos_of_you.length;
		};
		
		self.getItems = function() {
			return allItems[0];
		};
		
		self.getOptions = function() {
			return acr_opts;
		};
		
		self.getRequestedData = function() {
			return acr_requestedData;
		};
		
		/** @since 1.4.9 */
		self.resetNextPage = function() {
			acr_cursor = '';
			acr_pageid = '';
		};
		
		/** @since 1.4.9 */
		self.setNextPage = function(id, cursor) {
			acr_pageid = id;
			acr_cursor = cursor;
		};
		
		self.init = function(url, proxy, optRequestedData, instaTarget, searchWhat, tagname) {
			acr_url = (typeof url !== 'undefined') ? encodeURIComponent(url) : '';
			acr_proxy = eacElements.pluginsUrl + '/includes/proxy/' + proxy, // eacElements est initialisé dans plugin.php
			acr_requestedData = (typeof optRequestedData !== 'undefined') ? optRequestedData : '';
			acr_instagram = (typeof instaTarget !== 'undefined') ? instaTarget : '';
			acr_search = (typeof searchWhat !== 'undefined') ? searchWhat : '';
			acr_tagname = (typeof tagname !== 'undefined') ? tagname : '';
			allItems = [];
			item = {};
			self.callRss();
		};
		
		// Appel Ajax à travers un 'proxy'
		// pour contourner le CORS 'cross-origin resource sharing'
		self.callRss = function() {
			var data = {};
			if(acr_url) { data.url = acr_url; }
			if(acr_instagram) { data.instagram = acr_instagram; }
			if(acr_search) { data.reqsearch = acr_search; }
			if(acr_tagname) { data.tag = acr_tagname; }             // @since 1.5.4
			if(acr_token) { data.token = acr_token; }
			if(acr_rollout) { data.rollout = acr_rollout; }
			if(acr_debug) { data.debug = acr_debug; }
			if(acr_pageid) { data.id = acr_pageid; }                // @since 1.4.9
			if(acr_cursor) { data.cursor = acr_cursor; }            // @since 1.4.9
			
			$.ajax({
				url: acr_proxy,
				type: 'GET',
				data: data,
				dataType: 'json',
				ajaxOptions: acr_opts,
				ajaxRequestedData: acr_requestedData,
			}).done(function(data, textStatus, jqXHR) { // les proxy echo des données 'encodées par json_encode', mais il peut être vide
				
				if(jqXHR.responseJSON === null) {
					item.headError = 'Nothing to display...';
					allItems.push(item);
					return false;
				}
				
				/** @since 1.6.0 Merge les données du profile avec les items images */
				if(Object.keys(profilData.profile).length !== 0) {
					 var objectdata = $.extend(true, {}, data, profilData);
					 allItems.push(objectdata);
				} else {
					allItems.push(data);
				}
				
				/** @since 1.4.0 Gestion du CSRF Token et du Rollout_hash */
				if(data.profile && data.profile.csrf_token && acr_token === '') {
					acr_token = data.profile.csrf_token;
					acr_rollout = data.profile.rollout_hash;
					//console.log(acr_instagram + ":" + " Token:" + acr_token + " Rollout:" + acr_rollout + " Query_hash:" + data.profile.query_hash);
				}
				
			}).fail(function(jqXHR, textStatus) { // Les proxy echo des données 'non encodées par json_encode'. textStatus == parseerror
				item.headError = jqXHR.responseText;
				allItems.push(item);
				return false;
			});
		};
		
		/** @since 1.5.1 Téléchargement d'une vidéo */
		self.callFetch = function(url) {
			if(!url) { return false; }
			
			var ext = (url.match(/\.([^.]*?)(?=\?|#|$)/) || [])[1];
			//if(!ext) { return false; }
			var filename = "eac-video_" + Math.random().toString(36).substr(2, 10) + ".mp4";
			var initFetch = { method:'GET', mode:'cors', headers:{ "Content-Type":"application/x-www-form-urlencoded;charset=UTF-8"} };
			
			// A CORS-safelisted response-header name
			// Content-Length header in CORS requests with firefox
			if(window.fetch) {
				window.fetch(url, initFetch)
				.then(function(response) {
					//response.headers.forEach(function(val, key) { console.log(key + ' -> ' + val); });
					var contentType = response.headers.has("content-type") ? response.headers.get("content-type") : "";
					var contentLength = response.headers.has("content-length") ? response.headers.get("content-length") : 0;
					//if(!response.headers.has("content-length")) { window.alert("No Content-Length!!"); }
					var aSize = Math.abs(parseInt(contentLength, 10));
					var def = [1024*1024, 'Mo ??'];
					var taille = parseInt(aSize / def[0]);
					if((taille > acr_videoSize && window.confirm("Téléchargement::Download==> " + Math.ceil(taille) + def[1])) || taille <= acr_videoSize) {
						return response.blob();
					} else {
						return null;
					}
				}).then(function(blob) {
					if(blob) {
						var url = window.URL.createObjectURL(blob);
						var ahref = document.createElement('a');
						ahref.style.display = 'none';
						ahref.href = url;
						// Le nom du fichier
						ahref.download = filename;
						document.body.appendChild(ahref);
						ahref.click();
						window.URL.revokeObjectURL(url);
						document.body.removeChild(ahref);
					}
				}).catch(function(error) { window.alert('Ooops! ' + error.message); });
			}
		};
	};

	/**
	* Object createTransition
	*
	* Object pour créer les transitions Titre et Texte du composant Slider-pro
	*
	* @return {object} Un objet au format JSON
	* @since 0.0.9
	*/
	var createTransition = function() {
		var self = this,
		position = {	// Les positions Titre/Texte en %
			Xhc:50, // X en haut/centre
			Yhc:20, // Y en haut/centre
			Xct:50, // X centre
			Yct:50, // Y centre
			Xbc:50, // X en bas/centre
			Ybc:80	// Y en bas/centre
		};
		
		// Repositionne le Titre à ses coordonnées en haut à gauche
		self.resetTransitionTitle = function($targetId, indice) {
			var transit = { x: 0, y: 0, opacity: 0 };
			return transit;
		};
		
		// Lance la transition du Titre en X et Y depuis le coin haut/gauche
		self.getTransitionTitle = function($targetId, indice, pos) {
			var $containerTitle = $targetId.find('.spro-slide-' + indice + ' .spro-slide-title');
			var $containerImg = $targetId.find('.spro-slide-' + indice);
			
			var currentImgWidth = $containerImg[0].offsetWidth;
			var currentImgHeight = $containerImg[0].offsetHeight;
			
			var currentTitleWidth = $containerTitle[0].offsetWidth;
			var currentTitleHeight = $containerTitle[0].offsetHeight;
			// Calcul des offset X et Y de placement
			var offsetTitleX = ((currentImgWidth * position['X'+pos]) / 100) - (currentTitleWidth / 2);
			var offsetTitleY = ((currentImgHeight * position['Y'+pos]) / 100) - (currentTitleHeight / 2);
			
			var transit = { x: offsetTitleX, y: offsetTitleY, opacity: 1, delay: 400 };
			
			return transit;
		};
		
		// Repositionne le Texte à gauche et à l'offset Y de la sélection 'position'
		self.resetTransitionDesc = function($targetId, indice, pos) {
			var $containerDesc = $targetId.find('.spro-slide-' + indice + ' .spro-slide-desc');
			var $containerTitle = $targetId.find('.spro-slide-' + indice + ' .spro-slide-title');
			var $containerImg = $targetId.find('.spro-slide-' + indice);
			
			var currentImgWidth = $containerImg[0].offsetWidth;
			var currentImgHeight = $containerImg[0].offsetHeight;
			
			// Le titre est affiché ou non - +30px de décalage
			var currentTitleHeight = $containerTitle.length ? $containerTitle[0].offsetHeight + 30 : 0;
			
			var currentDescWidth = $containerDesc[0].offsetWidth;
			var currentDescHeight = $containerDesc[0].offsetHeight;
			
			var offsetDescY = (((currentImgHeight * position['Y'+pos]) / 100) - currentDescHeight) + currentTitleHeight;
			
			var transit = { x: 0, y: offsetDescY, opacity: 0 };
			return transit;
		};
		
		// Lance la transition du Texte. Se déplace latéralement depuis la gauche
		self.getTransitionDesc = function($targetId, indice, pos) {
			var $containerDesc = $targetId.find('.spro-slide-' + indice + ' .spro-slide-desc');
			var $containerTitle = $targetId.find('.spro-slide-' + indice + ' .spro-slide-title');
			var $containerImg = $targetId.find('.spro-slide-' + indice);
			
			var currentImgWidth = $containerImg[0].offsetWidth;
			var currentImgHeight = $containerImg[0].offsetHeight;
			
			// Le titre est affiché ou non - +30px de décalage
			var currentTitleHeight = $containerTitle.length ? $containerTitle[0].offsetHeight + 30 : 0;
			
			var currentDescWidth = $containerDesc[0].offsetWidth;
			var currentDescHeight = $containerDesc[0].offsetHeight;
			
			var offsetDescX = ((currentImgWidth * position['X'+pos]) / 100) - (currentDescWidth / 2);
			
			var transit = { x: offsetDescX, opacity: 1, delay: 700 };
			
			return transit;
		};
	};

	/** ----------------------------------------- */

