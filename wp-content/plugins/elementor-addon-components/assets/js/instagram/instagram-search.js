
/**
* Description: Cette méthode est déclenchée lorsque la section 'eac-addon-instagram-search' est chargée dans la page
*
* @param {selector} $scope. Le contenu de la section
* @since 1.3.0
* @since 1.3.1  (28/09/2019) Les followers d'un user account ne sont plus disponibles dans le résultat de la requête
*							Event pour ajouter une option dans le select des composants user account et explore
* @since 1.4.0  (15/10/2019) Event pour ajouter une option dans le select du composant location
* @since 1.4.1  (16/11/2019) Gestion de l'affichage des posts avec la lib Masonry en mode grille ou mosaïque
* @since 1.4.6	Contrôle et affichage des comptes 'vérifiés'
*				(26/01/2020) Changement de librairie Isotope vs Masonry
* @since 1.5.2  Gestion 'Enter ou Return' dans l'input text
* @since 1.6.2	Ajout de la fonction 'getInstagramSearchUserHashtagPlace'
*				Lance une requête de recherche 'Contexte/Mot-clé' sur les serveurs Instagram
*/

;(function($) {
    "use strict";
	
	var widgetInstagramSearch = window.widgetInstagramSearch = function($scope) {
		var $targetInstance = $scope.find('.eac-insta-search'),
			$targetSelectedItem = $scope.find('#insta-search__item-name'),
			$targetButton = $scope.find('#insta-search__read-button'),
			$targetHeader = $scope.find('.insta-search__header'),
			$targetError = $scope.find('.insta-search__error'),
			$targetLoader = $scope.find('#insta-search__loader-wheel'),
			$target = $scope.find('.insta-search'),
			settings = $target.data('settings') || {},
			instanceAjax = {},
			valueRadioButton = '',
			usernameRB = 'user',
			hashtagRB = 'hashtag',
			placeRB = 'place',
			instagram_uriUser = 'https://www.instagram.com/',
			instagram_uriExplore = 'https://www.instagram.com/explore/tags/',
			instagram_uriPlace = 'https://www.instagram.com/explore/locations/',
			instagram_uriSearch = 'https://www.instagram.com/web/search/topsearch/?context={context}&query={keyword}',
			instagram_uri,
			prefixCache = 'search_',
			postfixCache = '.json',
			$targetUserSelectOption = $(document).find('.insta-user__options-items'),
			$targetUserSelectedItem = $(document).find('#insta-user__item-name'),
			$targetExploreSelectOption = $(document).find('.insta-explore__options-items'),
			$targetExploreSelectedItem = $(document).find('#insta-explore__item-name'),
			$targetLocationSelectOption = $(document).find('.insta-location__options-items'),
			$targetLocationSelectedItem = $(document).find('#insta-location__item-name'),
			isotopeActive = false,
			isotopeOptions = {
				itemSelector: '.insta-search__item', 
				percentPosition: true,
				masonry: {
					columnWidth: '.insta-search__item-sizer',
					horizontalOrder: true,
				},
				layoutMode: 'fitRows', // masonry
				sortBy: 'original-order',
				visibleStyle: { transform: 'scale(1)', opacity: 1 }, // Transition
			};
		
		// Construction de l'objet de la requête Ajax
		instanceAjax = new ajaxCallFeed();
		
		// Par défaut
		valueRadioButton = $('input[name="insta-search__radio"]:checked').val();
		
		// Affecte le radio bouton sélectionné et nettoie les éléments
		$('input[name="insta-search__radio"]').on('click', function() {
			valueRadioButton = $('input[name="insta-search__radio"]:checked').val();
			$('.insta-search__item', $target).remove();
			$targetHeader.add($targetError).html('');
			if(isotopeActive) {
				$target.isotope('destroy');
				isotopeActive = !isotopeActive;
			}
		});
		
		// @since 1.5.2 Enter ou return dans l'input text
		$targetSelectedItem.on("keyup", function(e) {
            if(e.which === 13) {
                e.preventDefault();
                $targetSelectedItem.blur();
                $targetButton.click();
            }
		});
		
		/**
		 * Event click sur le bouton 'lire le flux'
		 *
		 * @since 1.6.2 Ajout async/await 
		 */
		$targetButton.on('click', async function(e) {
			e.preventDefault();
			$('.insta-search__item', $target).remove();
			$targetHeader.add($targetError).html('');
			if(isotopeActive) {
				$target.isotope('destroy');
				isotopeActive = !isotopeActive;
			}
			
			if($targetSelectedItem.val().length !== 0) {
				// On ne supprime pas tous les espaces pour rechercher dans le 'user account' et le 'username'
				var instaTarget = $targetSelectedItem.val().trim();
				var cache = valueRadioButton + prefixCache + $targetSelectedItem.val().replace(/\s+/g, '').toLocaleLowerCase() + postfixCache;
				$targetLoader.show();
				
				// Les données sont dans le cache sessionStorage
				var localCache = sessionStorage && sessionStorage.getItem(cache) ? sessionStorage.getItem(cache) : false;
				if(localCache) { 
					publishDataSearch(JSON.parse(localCache));
				} else {
					/**
					 * Recherche d'un mot-clé par son contexte (user, hashtag, place)
					 * La requête est synchrone
					 * 
					 * @since 1.6.2 Appel à la méthode 'getInstagramSearchUserHashtagPlace'
					 */
					var searchItems = await getInstagramSearchUserHashtagPlace(valueRadioButton, instaTarget);
					
					if(searchItems == false || Object.keys(searchItems).length === 0) {
						$targetLoader.hide();
						$targetError.html('<span>Unknown key:: ' + instaTarget + '</span>');
						return false;
					}
					
					//console.log("SEARCH:" + JSON.stringify(searchItems));
					var Items = [];
					
					if(valueRadioButton === usernameRB) { // Le contexte de recherche est un utilisateur
						$.each(searchItems, function(index, item) {
							
							Items.push({
								'position': item.position,
								'id': item.user.pk,
								'username': item.user.username,
								'full_name': item.user.full_name,
								'is_private': item.user.is_private,
								'is_private_fill': item.user.is_private === true ? "Private account" : "Public account",
								'is_verified': item.user.is_verified,
								'follower_count': 0,
								'follower_count_sort': 0,
								'byline': 0,
								'profile_pic_url': item.user.profile_pic_url,
							});
						});
					} else if(valueRadioButton === hashtagRB) {// Le contexte de recherche est un hashtag
						$.each(searchItems, function(index, item) {
							Items.push({
								'position': item.position,
								'username': item.hashtag.name,
								'media_count': item.hashtag.media_count,
								'media_count_sort': item.hashtag.media_count,
								'search_result_subtitle': item.hashtag.search_result_subtitle,
							});
						});
					} else { // Le contexte de recherche est une place (location)
						$.each(searchItems, function(index, item) {
							Items.push({
								'position': item.position,
								'id': item.place.location.pk,
								'name': item.place.location.name,
								'short_name': item.place.location.short_name,
								'lng': item.place.location.lng,
								'lat': item.place.location.lat,
								'address': item.place.location.address,
								'city': item.place.location.city,
								'external_source': item.place.location.external_source,
								'facebook_places_id': item.place.location.facebook_places_id,
								'title': item.place.location.title,
								'subtitle': item.place.location.subtitle,
								'slug': item.place.location.slug,
							});
						});
					}
					// Publication des items
					publishDataSearch(Items);
				}
			}
		});
		
		/** @since 1.3.1 Ajout de l'event sur la div sendto pour valoriser le select et le champ 'input text' du composant user account */
		$targetInstance.on('click touch', '.insta-search__sendto-user', function(e) {
			var sendto = $(this).attr('data-sendto').trim();
			if(sendto.length > 0) {
				// Affecte la valeur du champ text
				$targetUserSelectedItem.val(sendto);
				// Ajout de l'option dans le select
				$targetUserSelectOption.append('<option value="' + sendto + '" selected="selected">' + sendto.replace(/[\.,;:\'\"\*\(\)]/g, ' ') + '</option>');
				
				$('html, body').animate({ scrollTop: $targetUserSelectedItem.offset().top - 90}, 1000);
				// Déclenche l'event change qui sera intercepté par le composant user
				$targetUserSelectedItem.change();
			}
			return false;
		});
		
		/** @since 1.3.1 Ajout de l'event sur la div sendto pour valoriser le select et le champ 'input text' du composant explore */
		$targetInstance.on('click touch', '.insta-search__sendto-explore', function(e) {
			var sendto = $(this).attr('data-sendto').trim();
			if(sendto.length > 0) {
				// Affecte la valeur du champ text
				$targetExploreSelectedItem.val(sendto);
				// Ajout de l'option dans le select
				$targetExploreSelectOption.append('<option value="' + sendto + '" selected="selected">' + sendto.replace(/[\.,;:\'\"\*\(\)]/g, '') + '</option>');
				
				$('html, body').animate({ scrollTop: $targetExploreSelectedItem.offset().top - 90}, 1000);
				// Déclenche l'event change qui sera intercepté par le composant explore
				$targetExploreSelectedItem.change();
			}
			return false;
		});
		
		/** @since 1.4.0 Ajout de l'event sur la div sendto pour valoriser le select et le champ 'input text' du composant location */
		$targetInstance.on('click touch', '.insta-search__sendto-location', function(e) {
			var sendto = $(this).attr('data-sendto').trim();
			var optionselect = $(this).attr('data-option').replace(/[\.,;:\'\"\*\(\)]/g, '').trim();
			if(sendto.length > 0) {
				// Affecte la valeur du champ text
				$targetLocationSelectedItem.val(sendto);
				// Ajout de l'option dans le select
				$targetLocationSelectOption.append('<option value="' + sendto + '" selected="selected">' + optionselect + '</option>');
				
				$('html, body').animate({ scrollTop: $targetLocationSelectedItem.offset().top - 90}, 1000);
				// Déclenche l'event change qui sera intercepté par le composant location
				$targetLocationSelectedItem.change();
			}
			return false;
		});
		
		/** @since 1.6.2 Suppression du traitement de l'événement 'ajaxComplete' */
		
		// Procède aux tests. Construit et affiche le code HTML
		function publishDataSearch(allItems) {
			$targetLoader.hide();
			var Items = allItems;
			
			// Data dans le cache
			var cache = valueRadioButton + prefixCache + $targetSelectedItem.val().replace(/\s+/g, '').toLocaleLowerCase() + postfixCache;
			if(sessionStorage && !sessionStorage.getItem(cache)) {
				try {
					sessionStorage.setItem(cache, JSON.stringify(allItems));
				} catch(ex) {
					console.log("EAC sessionStorage: dépassement de quota!");
				}
			}
			
			// Affectation et tri des données
			if(valueRadioButton === usernameRB) {
				instagram_uri = instagram_uriUser;
				/*if(settings.data_sort === usernameRB) { Items.sort(sort_by('username', false, function(a) { return a.toUpperCase()})); }
				else { Items.sort(sort_by(settings.data_sort, true, parseInt)) }*/
				Items.sort(sort_by('position', false, parseInt));
			} else if(valueRadioButton === hashtagRB) {
				instagram_uri = instagram_uriExplore;
				Items.sort(sort_by('media_count_sort', true, parseInt));
			} else {
				instagram_uri = instagram_uriPlace;
				//Items.sort(sort_by('name', false, function(a){return a.toUpperCase()}));
				Items.sort(sort_by('position', false, parseInt));
			}
			
			/** @since 1.4.6 Instance Isotope avant imagesLoaded */
			$target.isotope(isotopeOptions);
			
			// Parcours de tous les items à afficher
			$.each(Items, function(indice, item) {
				var $wrapperItem = $('<div/>', { class: 'insta-search__item'});
				var $wrapperContent = $('<div/>', { class: 'insta-search__item-content ' + settings.data_style });
				
				if(valueRadioButton === usernameRB) { // Ajout de l'image pour username
					var imgUser =
							'<div class="insta-search__item-image">' +
								'<a href="' + instagram_uri + item.username + '/" target="_blank" rel="nofollow">' +
									'<img class="eac-image-loaded" src="' + item.profile_pic_url + '">' +
								'</a>' +
							'</div>';
					$wrapperContent.append(imgUser);
				} else if(valueRadioButton === hashtagRB) { // Ajout de la font awesome pour hashtag
					var imgHash =
							'<div class="insta-search__item-image font-awesome">' +
								'<a href="' + instagram_uri + item.username + '/" target="_blank" rel="nofollow">' +
									'<span>' +
										'<i class="fa fa-hashtag" aria-hidden="true"></i>' +
									'</span>' +
								'</a>' +
							'</div>';
					$wrapperContent.append(imgHash);
				} else {  // Ajout de la font awesome pour place
					var imgPlace =
							'<div class="insta-search__item-image font-awesome">' +
								'<a href="' + instagram_uri + item.id + '/" target="_blank" rel="nofollow">' +
									'<span>' +
										'<i class="fa fa-map-marker" aria-hidden="true"></i>' +
									'</span>' +
								'</a>' +
							'</div>';
					$wrapperContent.append(imgPlace);
				}
				
				var contentText = '<div class="insta-search__item-description">';
				/** @since 1.4.6 Le compte est vérifié */
				var verif = item.is_verified === true ? '<span><i class="fas fa-check-circle insta-search__isverified" aria-hidden="true"></i></span>' : '';
				
				// Username ou Hashtag pour créer l'URL
				var divUrl = valueRadioButton === usernameRB ? 
					'<div>' +
						'<i class="fas fa-at" aria-hidden="true"></i>' +
						'<a href="' + instagram_uri + item.username + '/" target="_blank" rel="nofollow">' + item.username + '</a>' + verif +
					'</div>' :
				valueRadioButton === hashtagRB ?
					'<div>' +
						'<i class="fa fa-hashtag" aria-hidden="true"></i>' +
						'<a href="' + instagram_uri + item.username + '/" target="_blank" rel="nofollow">' + item.username + '</a>' +
					'</div>' :
					'<div>' +
						'<i class="fa fa-map-marker" aria-hidden="true"></i><a href="' + instagram_uri + item.id + '/" target="_blank" rel="nofollow">' + item.name + '</a>' +
					'</div>';
					
				// Fields username pour username et hashtag, field name pour place
				if(item.username || item.name) { contentText +=  divUrl; }
				
				/** @since 1.3.1 Ajout d'une div et de l'event pour affecter le select et le champ 'input text' correspondant avec le username, hashtag */
				/** @since 1.4.0 Ajout d'une div et de l'event pour affecter le select et le champ 'input text' correspondant avec la place */
				if(valueRadioButton === usernameRB) {
					// Fields username
					if(item.full_name && settings.data_fullname) { contentText += '<div><i class="fa fa-user" aria-hidden="true"></i>' + removeEmojis(item.full_name) + '</div>'; }
					if(item.follower_count && settings.data_followers) { contentText += '<div><i class="fa fa-users" aria-hidden="true"></i>' + item.follower_count + '</div>'; }
					if(item.byline && settings.data_byline) { contentText += '<div><i class="fa fa-users" aria-hidden="true"></i>' + item.byline + '</div>'; }
					if(settings.data_private) { contentText += '<div><i class="fa fa-user-secret" aria-hidden="true"></i>' + item.is_private_fill + '</div>'; }
					if(item.id && settings.data_id) { contentText += '<div><i class="fa fa-indent" aria-hidden="true"></i>' + item.id + '</div>'; }
					if($targetUserSelectedItem.length > 0 && item.is_private == false) { // le composant instagram user existe dans la page
						contentText += '<div class="insta-search__sendto-user" data-sendto="' + item.username + '">' +
						'<i class="fa fa-paper-plane-o" aria-hidden="true"></i><span>Send to User component</span></div>';
					}
					// Position
					//contentText += '<div><i class="fas fa-crosshairs" aria-hidden="true"></i>' + item.position + '</div>';
				} else if(valueRadioButton === hashtagRB) {
					// Fields hashtag
					if(item.media_count && settings.data_pub) { contentText += '<div><i class="fas fa-camera" aria-hidden="true"></i>' + item.media_count + '</div>'; }
					if(item.search_result_subtitle && settings.data_publang) { contentText += '<div><i class="fas fa-camera" aria-hidden="true"></i>' + item.search_result_subtitle + '</div>'; }
					if($targetExploreSelectedItem.length > 0) { // le composant instagram explore existe dans la page
						contentText += '<div class="insta-search__sendto-explore" data-sendto="' + item.username + '">' +
						'<i class="fa fa-paper-plane-o" aria-hidden="true"></i><span>Send to Hashtag component</span></div>';
					}
					// Position
					//contentText += '<div><i class="fas fa-crosshairs" aria-hidden="true"></i>' + item.position + '</div>';
				} else {
					// Fields place
					if(item.city && settings.data_city) { contentText += '<div><i class="fa fa-location-arrow" aria-hidden="true"></i>' + item.city + '</div>'; }
					if(item.address && settings.data_address) { contentText += '<div><i class="fa fa-road" aria-hidden="true"></i>' + item.address + '</div>'; }
					if(item.id && settings.data_placeid) { contentText += '<div><i class="fa fa-indent" aria-hidden="true"></i>' + item.id + '</div>'; }
					if($targetLocationSelectedItem.length > 0) { // le composant instagram location existe dans la page
						contentText += '<div class="insta-search__sendto-location" data-sendto="' + item.id + '" data-option="' + item.name + '">' +
						'<i class="fa fa-paper-plane-o" aria-hidden="true"></i><span>Send to Location component</span></div>';
					}
					//contentText += '<div>' + item.lat + ':::' + item.lng + '</div>';
					// Position
					//contentText += '<div><i class="fas fa-crosshairs" aria-hidden="true"></i>' + item.position + '</div>';
				}
				contentText += '</div>';										
				$wrapperContent.append(contentText);
				
				// Ajout dans les wrappers
				$wrapperItem.append($wrapperContent);
				
				/**  @since 1.4.6 Instancie la lib Isotope, charge les images et redessine le layout */
				$target.append($wrapperItem).isotope('appended', $wrapperItem);
			});
			
			// Redessine après imagesLoaded
			$target.imagesLoaded(function() { $target.isotope('layout'); });
				
			isotopeActive = !isotopeActive;
		}
		
		/**
		 * Object getInstagramSearchUserHashtagPlace 
		 * Lance une requête de recherche sur les serveurs Instagram
		 *
		 * @param {context} Le contexte de la recherche (user, hashtag, place)
		 * @param {keyword} Le nom de l'utilisateur objet de la requête
		 * @return {object} Le contenu du résultat au format JSON
		 * @since 1.6.2
		 */
		var getInstagramSearchUserHashtagPlace = async function(context, keyword) {
			var res = instagram_uriSearch.replace("{context}", context);
			var res1 = res.replace("{keyword}", keyword);
			var resp = false;
			var headers = new Headers({
				"Accept": "application/json;charset=utf-8",
				"Content-Type": "application/json;charset=utf-8",
			});
			
			await fetch(res1, headers)
			.then(function(response) {
				if(response.status >= 400 && response.status < 600) {
					throw new Error('HTTP Error::' + response.status);
				}
				return response.json();
			})
			.then(function(json) {
				if(context === usernameRB) {
					resp = json.users;
				} else if(context === hashtagRB) {
					resp = json.hashtags;
				} else {
					resp = json.places;
				}
			}).catch(function(error) {
				console.log(error)
			});
			
			return resp;
		};
	};
	
})(jQuery);