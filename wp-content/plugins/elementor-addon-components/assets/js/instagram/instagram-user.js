/**
* Description: Cette méthode est déclenchée lorsque la section 'eac-addon-instagram-user' est chargée dans la page
*
* @param {selector} $scope. Le contenu de la section
* @since 1.3.0
* @since 1.3.1 (28/09/2019) Gestion des stories
*							Gestion de l'event 'change' sur l'input text
* @since 1.4.0 (20/10/2019) Gestion des cookies
* @since 1.4.1 (16/11/2019) Gestion de l'affichage des posts avec la lib Masonry en mode grille ou mosaïque
*							Ajout de l'event afterClose sur la fancybox
* @since 1.4.2  Ajout de la requête sur les comptes suggéré d'un compte utilisateur
* @since 1.4.3  Ajout de la requête sur les posts partagés d'un compte utilisateur
* @since 1.4.4	Gestion des tagged user de chaque post
* @since 1.4.5	Gestion des lieux (Location) de chaque post
* @since 1.4.6	Contrôle et affichage des comptes 'vérifiés'
*				(26/01/2020) Changement de librairie Isotope vs Masonry
* @since 1.4.9	Implémente la pagination
* @since 1.5.0	Gestion du download de la video
* @since 1.5.2  Ajout du traitement des Hashtags
*               Gestion 'Enter ou Return' dans l'input text
*               Correctif suppression du test sur 'item.video_view_count'
* @since 1.6.0	Évolution API Instagram. Gestion du profil de l'utilisateur
*				Cache le bouton '$targetButtonNext' sur l'événement click
* @since 1.6.1	Test du callback des requêtes AJAX
*				Suppression des pictos Vidéo et Diaporama et du traitement des événements afférents
*				"The endpoints documented on this page were deprecated on October 24, 2020 and now return an error code 400"
*				https://developers.facebook.com/docs/instagram/oembed-legacy
* @since 1.6.2	Ajout de la fonction 'getInstagramUserProfileByName' pour lire le profile d'un utilisateur Instagram
*				Suppression de la délégation des événements sur les liens likes et commentaires
*/

;(function($) {
	"use strict";
	
	var widgetInstagramUser = window.widgetInstagramUser = function($scope) {
		var $targetInstance = $scope.find('.eac-insta-user'),
			$target = $scope.find('.insta-user'),
			$targetSelect = $scope.find('#insta-user__options-items'),
			$targetSelectedItem = $scope.find('#insta-user__item-name'),
			$targetCheckBox1 = $scope.find('#insta-user__items-cb'),
			$targetCheckBox2 = $scope.find('#insta-user__items-cb2'),
			$targetCheckBox3 = $scope.find('#insta-user__items-cb3'),
			$targetButton = $scope.find('#insta-user__read-button'),
			$targetLoader = $scope.find('#insta-user__loader-wheel'),
			$targetButtonNext = $scope.find('#insta-user__read-button-next'),
			$targetLoaderNext = $scope.find('#insta-user__loader-wheel-next'),
			$targetItemsCount = $scope.find('.insta-user__read-button-next-paged'),
			isNextPage = false,
			$targetHeader = $scope.find('.insta-user__header'),
			$targetError = $scope.find('.insta-user__error'),
			$targetJqCloud = $scope.find('.insta-user__jqcloud'),
			$targetJqCloudSpan = $scope.find('.insta-user__jqcloud span'),
			$targetJqCloudPara = $scope.find('.insta-user__container-jqcloud p'),
			$targetContainerHiddenContent = $scope.find('.insta-user__container-hidden-content'),
			$targetHiddenContent = $targetContainerHiddenContent.find('.insta-user__hidden-content'),
			$targetTaggedUserPara = $targetContainerHiddenContent.find('.insta-user__hd-taggeduser'),
			$targetMentionPara = $targetContainerHiddenContent.find('.insta-user__hd-mention'),
			$targetHashtagPara = $targetContainerHiddenContent.find('.insta-user__hd-hashtag'),
			$targetLikesPara = $targetContainerHiddenContent.find('.insta-user__hd-likes'),
			$targetCommentsPara = $targetContainerHiddenContent.find('.insta-user__hd-comments'),
			$targetSuggestedPara = $targetContainerHiddenContent.find('.insta-user__hd-suggested'),
			$targetTaggedPostPara = $targetContainerHiddenContent.find('.insta-user__hd-taggedpost'),
			$targetStoriesPara = $targetContainerHiddenContent.find('.insta-user__hd-stories'),
			$targetStories = $scope.find('#insta-user__stories'),
			$targetSuggestedAccount = $scope.find('#insta-user__suggested-account'),
			$targetTaggedPostsAccount = $scope.find('#insta-user__tagged-posts'),
			$targetLocationSelectOption = $(document).find('.insta-location__options-items'),
			$targetLocationSelectedItem = $(document).find('#insta-location__item-name'),
			settings = $target.data('settings') || {},
			instanceAjax = {},
			instagram_uriUser = 'https://www.instagram.com/',
			instagram_uriShortcode = 'https://www.instagram.com/p/',
			instagram_uriExplore = 'https://www.instagram.com/explore/tags/',
			instagram_uriSearchUser = 'https://www.instagram.com/web/search/topsearch/?context=user&query=',
			proxy_user = 'proxy_user.php',
			proxy_stories = 'proxy_stories.php',
			ajaxOptionsStories = 'userStories',
			proxy_suggested = 'proxy_suggesteduser.php',
			ajaxOptionsSuggested = 'userSuggested',
			proxy_taggedposts = 'proxy_taggedposts.php',
			ajaxOptionsTaggedPosts = 'userTaggedPosts',
			prefixCache = 'user_',
			postfixCache = '.json',
			nbTopPosts = 9,
			graphSidecar = 'GraphSidecar',
			userCookie = 'eac-uselect#',
			isotopeActive = false,
			isotopeOptions = {
				itemSelector: '.insta-user__item', 
				percentPosition: true,
				masonry: {
					columnWidth: '.insta-user__item-sizer',
					horizontalOrder: true,
				},
				layoutMode: settings.data_layout,
				sortBy: 'original-order',
				visibleStyle: { transform: 'scale(1)', opacity: 1 }, // Transition
				//hiddenStyle: { transform: 'scale(0.001)', opacity: 0 },
			};
		
		if(!settings.data_length) {
			return;
		}
		
		// Construction de l'objet de la requête Ajax
		instanceAjax = new ajaxCallFeed();
		
		// @since 1.4.0 Recherche des cookies pour les ajouter dans les options du select
		setSelectOptionsCookies(userCookie, $targetSelect);
			
		// Première valeur de la liste par défaut
		$targetSelect.find('option:first').attr('selected', 'selected');
		$targetSelectedItem.val($targetSelect.eq(0).val());
			
		// Event change sur la liste des flux
		$targetSelect.on('change', function(e) {
			e.preventDefault();
			$targetSelectedItem.val($(this).val());
			$('.insta-user__item', $target).remove();
			$targetJqCloud.empty().css({width:0, height:0}).jQCloud('destroy');
			$targetCheckBox1.add($targetCheckBox2).add($targetCheckBox3).prop('checked', false);
			$targetHeader.add($targetHiddenContent).add($targetError).html('');
			$targetContainerHiddenContent.removeClass('fancybox-content').removeAttr('style');
			$targetStories.add($targetSuggestedAccount).add($targetTaggedPostsAccount).add($targetButtonNext).add($targetJqCloudPara).hide();
			if(isotopeActive) {
				$target.isotope('destroy');
				isotopeActive = !isotopeActive;
			}
			$target.css('height', '');
			instanceAjax.resetNextPage();
			isNextPage = false;
			$targetItemsCount.text('0');
		});
			
		// Event click sur l'input checkbox 'Top posts' ou 'Mentions'
		$targetCheckBox1.add($targetCheckBox2).add($targetCheckBox3).on('click', function(e) {
			$('.insta-user__item', $target).remove();
			$targetJqCloud.empty().css({width:0, height:0}).jQCloud('destroy');
			$targetHeader.add($targetHiddenContent).add($targetError).html('');
			$targetContainerHiddenContent.removeClass('fancybox-content').removeAttr('style');
			$targetStories.add($targetSuggestedAccount).add($targetTaggedPostsAccount).add($targetButtonNext).add($targetJqCloudPara).hide();
			if(isotopeActive) {
				$target.isotope('destroy');
				isotopeActive = !isotopeActive;
			}
			$target.css('height', '');
			instanceAjax.resetNextPage();
			isNextPage = false;
			$targetItemsCount.text('0');
		});
		
		// Désactive les checkbox 'Top posts', 'Mentions' et 'Nuage de tags'
		$targetCheckBox1.on('click', function(e) { $targetCheckBox2.add($targetCheckBox3).prop('checked', false); });
		$targetCheckBox2.on('click', function(e) { $targetCheckBox1.add($targetCheckBox3).prop('checked', false); });
		$targetCheckBox3.on('click', function(e) { $targetCheckBox1.add($targetCheckBox2).prop('checked', false); });
		
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
		$targetButton.on('click touch', async function(e) {
			e.preventDefault();
			$('.insta-user__item', $target).remove();
			$targetHeader.add($targetHiddenContent).add($targetError).html('');
			$targetJqCloud.empty().css({width:0, height:0}).jQCloud('destroy');
			$targetContainerHiddenContent.removeClass('fancybox-content').removeAttr('style');
			$targetStories.add($targetSuggestedAccount).add($targetTaggedPostsAccount).add($targetButtonNext).add($targetJqCloudPara).hide();
			if(isotopeActive) {
				$target.isotope('destroy');
				isotopeActive = !isotopeActive;
			}
			
			if($targetSelectedItem.val().length !== 0) {
				var instaTarget = $targetSelectedItem.val().replace(/\s+/g, '').toLocaleLowerCase();
				var cache = prefixCache + instaTarget + postfixCache;
				var findingUser = false;
				var userdata = {};
				$targetLoader.show();
				
				// Les données sont dans le cache sessionStorage
				var localCache = sessionStorage && sessionStorage.getItem(cache) ? sessionStorage.getItem(cache) : false;
				if(localCache) {
					isNextPage = false;
					$targetItemsCount.text('0');
					publishDataUser(JSON.parse(localCache));
				} else {
					/**
					 * Recherche le profile de l'utilisateur et notamment son ID
					 * Enregistre le profile dans l'instance courante 'ajaxCallFeed'
					 * Lance la recherche les items (Images) relatifs à l'ID utilisateur
					 * Requête synchrone
					 * 
					 * @since 1.6.0
					 * @since 1.6.2 Appel à la méthode 'getInstagramUserProfileByName'
					 */
					var profile = await getInstagramUserProfileByName(instaTarget);
					
					//console.log("Profile user:" + JSON.stringify(profile));
					
					if(profile) {
						// Affecte les données du profile dans l'instance courante 'ajaxCallFeed'
						instanceAjax.setUserProfilAccount(profile);
						
						// Lance la récupération des items du profile par son ID
						instanceAjax.init('', proxy_user, '', profile.id);
					} else {
						$targetLoader.hide();
						$targetError.html('<span>Unknown key:: ' + instaTarget + '</span>');
					}
				}
			}
		});
		
		/** @since 1.4.9 Event click sur le bouton 'Plus d'articles' */
		$targetButtonNext.on('click touch', function(e) {
			e.preventDefault();
			$targetError.html('');
			var instaTarget = $targetSelectedItem.val().replace(/\s+/g, '').toLocaleLowerCase();
			isNextPage = true;
			$targetLoaderNext.show();
			
			/** @since 1.6.0 Chrome garde le focus sur le bouton
			 * Les items sont poussés vers le haut et non vers le bas
			 * Le bouton est caché
			 */
			$targetButtonNext.hide();
			
			// Initialisation de l'objet Ajax avec le username et le proxy à utiliser
			instanceAjax.init('', proxy_user, '', instaTarget);
		});
		
		/** Délégation event sur les span de JQCloud qui ne sont pas encore créés
		* @since 1.4.0 (20/10/2019) Le tag sélectionné dans le nuage est enregistré dans un cookie
		*/
		$targetInstance.on('click touch', '.insta-user__jqcloud.insta-user__jqcloud-mentions span', function(e) {
			e.preventDefault();
			
			var jqcloudval = $(this).text().substring(1, 9999).trim();
			var jqcloudvalclean = jqcloudval.replace(/[\.,;:\'\"\*\(\)]/g, ' ').toLocaleLowerCase();
			var cookiename = userCookie + jqcloudvalclean;
			// cookie user
			if (navigator.cookieEnabled) {
				if (document.cookie.indexOf(cookiename) === -1) {
					document.cookie = cookiename + '=' + jqcloudval + ' ;max-age=2462435;sameSite=strict';
					// Affecte la valeur du champ text
					$targetSelectedItem.val(jqcloudval);
					// Ajout de l'option dans le select
					$targetSelect.append('<option value="' + jqcloudval + '" selected="selected">' + jqcloudvalclean + '</option>');
				} else {
					return; // Le cookie existe, on sort de l'event. Pas de reset des champs
				}
			}
			
			$('.insta-user__item', $target).remove();
			$targetJqCloud.empty().css({width:0, height:0}).jQCloud('destroy');
			$targetHeader.add($targetHiddenContent).add($targetError).html('');
			$targetContainerHiddenContent.removeClass('fancybox-content').removeAttr('style');
			$targetCheckBox1.add($targetCheckBox2).add($targetCheckBox3).prop('checked', false);
			$targetStories.add($targetSuggestedAccount).add($targetTaggedPostsAccount).add($targetJqCloudPara).hide();
			if(isotopeActive) {
				$target.isotope('destroy');
				isotopeActive = !isotopeActive;
			}
		});
		
		/** @since 1.6.2 Suppression de la délégation event sur les liens likes qui ne sont pas encore créés */
		/** @since 1.6.2 Suppression de la délégation event sur les liens commentaires qui ne sont pas encore créés */
		/** @since 1.6.1 Suppression de la délégation de l'événement sur les picto Vidéo/Diaporama */
		
		// Délégation event sur les liens mentions qui ne sont pas encore créés
		$targetInstance.on('click touch', '.insta-user__meta-item.insta-user__mentions-count', function(e) {
			//var mention = $(this).attr('data-mentions').split(',').join().replace(/,/g, '<br>');
			var mention = $(this).attr('data-mentions').split(',');
			if(mention.length > 0) {
				// Map chaque keyword en lien
				var mentions = $.map(mention, function(n, i) { return ('<div><a href="' + instagram_uriUser + n.substring(1, 9999) + '/" target="_blank" rel="nofollow">' + n + '</a></div>'); });
				// Display flex de la div parent en colonne
				$targetHiddenContent.css('flex-direction', 'column');
				// Affiche les mentions
				$targetHiddenContent.html(mentions);
				$targetContainerHiddenContent.removeClass('fancybox-content').removeAttr('style');
				$targetTaggedUserPara.add($targetStoriesPara).add($targetHashtagPara).add($targetLikesPara).add($targetCommentsPara).add($targetSuggestedPara).add($targetTaggedPostPara).hide();
				$targetMentionPara.show();
				/** @since 1.4.1 gère l'événement de fermeture de la fancybox */
				$.fancybox.open([{src:$targetContainerHiddenContent, type:'inline', opts:{smallBtn:true, buttons:[''], afterClose:function() { $targetHiddenContent.html(''); }}}]);
			}
			return false;
		});
		
		// @since 1.5.2 Délégation event sur les liens hashtags qui ne sont pas encore créés
		$targetInstance.on('click touch', '.insta-user__meta-item.insta-user__hashtags-count', function(e) {
			var hashtag = $(this).attr('data-hashtags').split(',');
			if(hashtag.length > 0) {
				// Map chaque keyword en lien
				var hashtags = $.map(hashtag, function(n, i) { return ('<div><a href="' + instagram_uriExplore + n.substring(1, 9999) + '/" target="_blank" rel="nofollow">' + n + '</a></div>'); });
				// Display flex de la div parent en colonne
				$targetHiddenContent.css('flex-direction', 'column');
				// Affiche les hashtags
				$targetHiddenContent.html(hashtags);
				$targetContainerHiddenContent.removeClass('fancybox-content').removeAttr('style');
				$targetTaggedUserPara.add($targetStoriesPara).add($targetMentionPara).add($targetLikesPara).add($targetCommentsPara).add($targetSuggestedPara).add($targetTaggedPostPara).hide();
				$targetHashtagPara.show();
				/** @since 1.4.1 gère l'événement de fermeture de la fancybox */
				$.fancybox.open([{src:$targetContainerHiddenContent, type:'inline', opts:{smallBtn:true, buttons:[''], afterClose:function() { $targetHiddenContent.html(''); }}}]);
			}
			return false;
		});
		
		/** @since 1.3.1 Ajout event click sur la div des stories */
		$targetStories.on('click touch', function(e) {
			e.preventDefault();
			// Display flex de la div parent en ligne
			$targetHiddenContent.html('').css('flex-direction', 'row');
			$targetContainerHiddenContent.removeClass('fancybox-content').removeAttr('style');
			$targetTaggedUserPara.add($targetSuggestedPara).add($targetHashtagPara).add($targetMentionPara).add($targetLikesPara).add($targetCommentsPara).add($targetTaggedPostPara).hide();
			$targetStoriesPara.show();
			var id = $(this).attr('data-id');
			instanceAjax.init('', proxy_stories, ajaxOptionsStories, id);
		});
		
		/** @since 1.4.2 Ajout event click sur le bouton des suggested account */
		$targetSuggestedAccount.on('click touch', function(e) {
			e.preventDefault();
			// Display flex de la div parent en ligne
			$targetHiddenContent.html('').css('flex-direction', 'row');
			$targetContainerHiddenContent.removeClass('fancybox-content').removeAttr('style');
			$targetTaggedUserPara.add($targetStoriesPara).add($targetHashtagPara).add($targetMentionPara).add($targetLikesPara).add($targetCommentsPara).add($targetTaggedPostPara).hide();
			$targetSuggestedPara.show();
			var id = $(this).attr('data-id');
			instanceAjax.init('', proxy_suggested, ajaxOptionsSuggested, id);
		});
		
		/** @since 1.4.3 Ajout event click sur le bouton des tagged posts */
		$targetTaggedPostsAccount.on('click touch', function(e) {
			e.preventDefault();
			// Display flex de la div parent en ligne
			$targetHiddenContent.html('').css('flex-direction', 'row');
			$targetContainerHiddenContent.removeClass('fancybox-content').removeAttr('style');
			$targetTaggedUserPara.add($targetStoriesPara).add($targetHashtagPara).add($targetMentionPara).add($targetLikesPara).add($targetCommentsPara).add($targetSuggestedPara).hide();
			$targetTaggedPostPara.show();
			var id = $(this).attr('data-id');
			instanceAjax.init('', proxy_taggedposts, ajaxOptionsTaggedPosts, id);
		});
		
		/** @since 1.4.4 Délégation event sur les liens tagged user qui n'ont pas encore été créés */
		$targetInstance.on('click touch', '.insta-user__tagged-user', function(e) {
			e.preventDefault();
			var taggeduser = $(this).attr('data-taggeduser').split(',');
			if(taggeduser.length > 0) {
				// Map chaque keyword en lien
				var taggedusers = $.map(taggeduser, function(n, i) { return ('<div><a href="' + instagram_uriUser + n.substring(1, 9999) + '/" target="_blank" rel="nofollow">' + n + '</a></div>'); });
				// Display flex de la div parent en colonne
				$targetHiddenContent.css('flex-direction', 'column');
				// Affiche les hashtags
				$targetHiddenContent.html(taggedusers);
				$targetContainerHiddenContent.removeClass('fancybox-content').removeAttr('style');
				$targetHashtagPara.add($targetStoriesPara).add($targetMentionPara).add($targetLikesPara).add($targetCommentsPara).add($targetSuggestedPara).add($targetTaggedPostPara).hide();
				$targetTaggedUserPara.show();
				/** @since 1.4.1 gère l'événement de fermeture de la fancybox */
				$.fancybox.open([{src:$targetContainerHiddenContent, type:'inline', opts:{smallBtn:true, buttons:[''], afterClose:function() { $targetHiddenContent.html(''); }}}]);
			}
		});
		
		/** @since 1.4.5 Délégation event sur les picto place (location) qui n'ont pas encore été créés */
		$targetInstance.on('click touch', '.insta-user__place', function(e) {
			e.preventDefault();
			var place = $(this).attr('data-place').split('::')[0].trim();
			var nom = $(this).attr('data-place').split('::')[1].replace(/[\.,;:\'\"\*\(\)]/g, '').trim();
			if(place.length > 0) {
				// Affecte la valeur du champ text
				$targetLocationSelectedItem.val(place);
				// Ajout de l'option dans le select
				$targetLocationSelectOption.append('<option value="' + place + '" selected="selected">' + nom + '</option>');
				
				$('html, body').animate({ scrollTop: $targetLocationSelectedItem.offset().top - 90}, 1000);
				// Déclenche l'event change qui sera intercepté par le composant location
				$targetLocationSelectedItem.change();
			}
		});
		
		/** @since 1.5.0 Gestion du click sur le picto download de la video */
		$targetInstance.on('click touch', '.insta-user__download-video', function(e) {
			e.preventDefault();
			var urlvideo = $(this).attr('data-downloadvideo');
			instanceAjax.callFetch(urlvideo);
		});
		
		/** @since 1.3.1 Ajout de l'event 'change' sur l'input text.
		* L'événement est déclenché par le composant 'search' ou par une modification directe dans l'input text
		*/
		$targetSelectedItem.on('change', function(e) {
			e.preventDefault();
			$('.insta-user__item', $target).remove();
			$targetJqCloud.empty().css({width:0, height:0}).jQCloud('destroy');
			$targetCheckBox1.add($targetCheckBox2).add($targetCheckBox3).prop('checked', false);
			$targetHeader.add($targetHiddenContent).add($targetError).html('');
			$targetContainerHiddenContent.removeClass('fancybox-content').removeAttr('style');
			$targetStories.add($targetSuggestedAccount).add($targetTaggedPostsAccount).add($targetButtonNext).add($targetJqCloudPara).hide();
			if(isotopeActive) {
				$target.isotope('destroy');
				isotopeActive = !isotopeActive;
			}
			$target.css('height', '');
			instanceAjax.resetNextPage();
			isNextPage = false;
			$targetItemsCount.text('0');
		});
		
		// L'appel Ajax est asynchrone, ajaxComplete, event global, est déclenché
		$(document).ajaxComplete(function(event, xhr, ajaxSettings) {
			if(ajaxSettings.ajaxOptions && ajaxSettings.ajaxOptions === instanceAjax.getOptions()) { // Le même random number généré lors de la création de l'objet Ajax
				event.stopImmediatePropagation();
				// Les items à afficher
				var Items = instanceAjax.getItems();
				
				// @since 1.6.1 Une erreur Ajax. La clé 'headError' est renseignée
				if(Items.headError) {
					$targetLoader.add($targetLoaderNext).hide();
					$targetError.html('<span>' + Items.headError + '</span>');
					return false;
				}
				
				// Les données des pictogrammes
				if(ajaxSettings.ajaxRequestedData && ajaxSettings.ajaxRequestedData === instanceAjax.getRequestedData()) {
					// Affichage des likeurs et des commenteurs
					if(Items.likesComments && Items.likesComments.length > 0) {
						// Parse les données likes et comments
						showLikesComments(Items.likesComments, $targetHiddenContent, instagram_uriUser);
						// Ouverture de la Fancybox avec le contenu de la div targetContainerHiddenContent
						/** @since 1.4.1 gère l'événement de fermeture de la fancybox */
						$.fancybox.open([{src:$targetContainerHiddenContent, type:'inline', opts:{smallBtn:true, buttons:[''], afterClose:function() { $targetHiddenContent.html(''); }}}]);
					
					/** @since 1.6.1 Suppression embed Vidéo/Diaporama */
					
					/** @since 1.3.1 Affichage des stories */
					} else if(Items.stories && Items.stories.length > 0) {
						// Parse les données 
						showStories(Items.stories, $targetHiddenContent);
						// Ouverture de la Fancybox avec le contenu de la div targetContainerHiddenContent
						/** @since 1.4.1 gère l'événement de fermeture de la fancybox */
						$.fancybox.open([{src:$targetContainerHiddenContent, type:'inline', opts:{smallBtn:true, buttons:[''], afterClose:function() { $targetHiddenContent.html(''); }}}]);
					
					/** @since 1.4.2 Affichage des comptes utilisateur suggérés */
					} else if(Items.suggesteduser && Items.suggesteduser.length > 0) {
						// Parse les données 
						showSuggestedUser(Items.suggesteduser, $targetHiddenContent);
						// Ouverture de la Fancybox avec le contenu de la div targetContainerHiddenContent
						/** @since 1.4.1 gère l'événement de fermeture de la fancybox */
						$.fancybox.open([{src:$targetContainerHiddenContent, type:'inline', opts:{smallBtn:true, buttons:[''], afterClose:function() { $targetHiddenContent.html(''); }}}]);
					
					/** @since 1.4.3 Affichage des posts partagés */
					} else if(Items.taggedposts && Items.taggedposts.length > 0) {
						// Parse les données 
						showTaggedPosts(Items.taggedposts, $targetHiddenContent);
						// Ouverture de la Fancybox avec le contenu de la div targetContainerHiddenContent
						/** @since 1.4.1 gère l'événement de fermeture de la fancybox */
						$.fancybox.open([{src:$targetContainerHiddenContent, type:'inline', opts:{smallBtn:true, buttons:[''], afterClose:function() { $targetHiddenContent.html(''); }}}]);
					}
				} else { // Affichage normal des posts
						publishDataUser(Items);
				}
			}
		});
	
		
		// Procède aux tests. Construit et affiche le code HTML
		function publishDataUser(allItems) {
			// Cacher les loaders
			$targetLoader.add($targetLoaderNext).hide();
				
			// Une erreur Ajax ??
			if(allItems.headError) {
				$targetError.html('<span>' + allItems.headError + '</span>');
				return false;
			}
			
			var topPosts = $targetCheckBox1.is(':checked') ? true : false; // Top posts sélectionnés
			var mentionCloud = $targetCheckBox2.is(':checked') ? true : false; // Mentions sélectionné
			var hashtagCloud = $targetCheckBox3.is(':checked') ? true : false; // Nuage de tags sélectionné
			
			// Pas de médias à afficher pour le username
			if(! allItems.medias) {
				$targetError.html('<span>Nothing to display</span>');
				return false;
			}
			
			// Data dans le cache
			var cache = prefixCache + $targetSelectedItem.val().replace(/\s+/g, '').toLocaleLowerCase() + postfixCache;
			if(sessionStorage && !sessionStorage.getItem(cache)) {
				try {
					sessionStorage.setItem(cache, JSON.stringify(allItems));
				} catch(ex) {
					console.log("EAC sessionStorage: dépassement de quota!");
				}
			}
			
			// Notre boucle sur les items
			var Items = allItems.medias;
			
			// Le profile du user account
			var Profile = allItems.profile;
			
			// Affichage mentions
			if(mentionCloud === true) {
				if(allItems.jqcloudMention) {
					if(!$targetJqCloud.hasClass('insta-user__jqcloud-mentions')) { $targetJqCloud.addClass('insta-user__jqcloud-mentions');  }
					var containerWidth = $targetJqCloud.parent().width();
					var colorsReverse = ['#e41a1c','#377eb8','#4daf4a','#984ea3','#ff7f00','#c7cf00','#a65628','#f781bf','#999999']; //.reverse();
				    $targetJqCloudPara.show();
					$targetJqCloud.css({width:containerWidth, height:containerWidth/2});
					$targetJqCloud.jQCloud(allItems.jqcloudMention, {
						//shape: 'rectangular',
						autoResize: true,
						colors: colorsReverse,
						fontSize: {from: 0.05, to: 0.01}
					});
				} else {
					$targetError.html('<span>Nothing to display</span>');
				}
				return false;
			} else if(hashtagCloud === true) {
				if($targetJqCloud.hasClass('insta-user__jqcloud-mentions')) { $targetJqCloud.removeClass('insta-user__jqcloud-mentions');  }
				if(allItems.jqcloudHashtag) {
					var containWidth = $targetJqCloud.parent().width();
					var colorReverse = ['#e41a1c','#377eb8','#4daf4a','#984ea3','#ff7f00','#c7cf00','#a65628','#f781bf','#999999']; //.reverse();
				    $targetJqCloudPara.show();
					$targetJqCloud.css({width:containWidth, height:containWidth/2});
					$targetJqCloud.jQCloud(allItems.jqcloudHashtag, {
						//shape: 'rectangular',
						autoResize: true,
						colors: colorReverse,
						fontSize: {from: 0.05, to: 0.01}
					});
				} else {
					$targetError.html('<span>Nothing to display</span>');
				}
				return false;
			}
			
			/** @since 1.4.9 Montre ou cache le bouton 'Plus d'articles' */
			if(Profile.has_next_page && Profile.end_cursor && Profile.id) {
				instanceAjax.setNextPage(Profile.id, Profile.end_cursor);
				if(! topPosts) { $targetButtonNext.show(); }
			} else {
				instanceAjax.resetNextPage();
				$targetButtonNext.hide();
			}
			
			/** @since 1.4.9 page suivante pour le même username. On ne publie pas le header */
			if(! isNextPage) {
				publishHeaderUser(Profile);
			}
			
			// Affiche le contenu des posts
			publishContentUser(Items);
		}
			
		// Construit et affiche le code HTML du header
		function publishHeaderUser(Profile) {
			// Affiche l'image du profile
			if(Profile.profile_pic_url) {
				var imgProfile =
					'<div class="insta-user__header-img">' +
						'<a href="' + instagram_uriUser + Profile.username + '" target="_blank" rel="nofollow">' +
							'<img class="eac-image-loaded" src="' + Profile.profile_pic_url + '" alt="' + Profile.username + '">' +
						'</a>' +
					'</div>';
				$targetHeader.append(imgProfile);
			}
			
			// Affiche l'entête
			var $wrapperHeadContent = $('<div/>', { class: 'insta-user__header-content' });
			
			/** @since 1.4.6 Le compte est vérifié */
			var verif = Profile.is_verified === true ? '<i class="fas fa-check-circle insta-user__isverified" aria-hidden="true"></i>' : '';
			
			$wrapperHeadContent.append(
				'<div>' +
					'<span class="insta-user__header-info"><i class="fas fa-at" aria-hidden="true"></i>' +
						'<a href="' + instagram_uriUser + Profile.username + '" target="_blank" rel="nofollow">' + 
							Profile.username + '</a>' + verif + 
					'</span>' +
				'</div>');
			
			$wrapperHeadContent.append(
				'<div>' +
					'<span class="insta-user__header-info"><i class="fa fa-user" aria-hidden="true"></i>' + Profile.full_name + '</span>' +
				'</div>');
			
			if(Profile.biographie) {
			    $wrapperHeadContent.append('<div><span class="insta-user__header-info"><i class="fa fa-address-card-o" aria-hidden="true"></i>' + removeEmojis(Profile.biographie) + '</span></div>');
			}
			
			$wrapperHeadContent.append(
				'<div>' +
					'<span class="insta-user__header-info"><i class="fas fa-camera" aria-hidden="true"></i>' + Profile.publication + '</span>' +
					'<span class="insta-user__header-info"><i class="fa fa-users" aria-hidden="true"></i>' + Profile.follower_count + '</span>' +
					'<span class="insta-user__header-info"><i class="fa fa-film" aria-hidden="true"></i>' + Profile.headTotalVideos + '</span>' +
					'<span class="insta-user__header-info"><i class="fas fa-at" aria-hidden="true"></i>' + Profile.headTotalMentions + '</span>' +
					'<span class="insta-user__header-info"><i class="fas fa-hashtag" aria-hidden="true"></i>' + Profile.headTotalHashtags + '</span>' +
				'</div>');
			
			$wrapperHeadContent.append(
				'<div>' +
					'<span class="insta-user__header-info"><i class="far fa-heart" aria-hidden="true"></i>' + Profile.headAvgLikes + '</span>' +  // headTotalLikes / headAvgLikes
					'<span class="insta-user__header-info"><i class="far fa-comment" aria-hidden="true"></i>' + Profile.headAvgComments + '</span>' + // headTotalComments / headAvgComments
				'</div>');
			
			if(Profile.headEngagement) {
				$wrapperHeadContent.append(
					'<div>' +
						'<span class="insta-user__header-info"><a href="https://www.hivency.com/fr/2018/09/28/taux-dengagement-instagram-on-vous/" target="_blank" rel="nofollow">- TE </a>' + 
						Profile.headEngagement + '</span>' +
					'</div>');
			}
			
			$wrapperHeadContent.append('<div><span class="insta-user__header-info">' + Profile.headDateDiff + '</span></div>');
			
			if(Profile.siteWeb) {
				$wrapperHeadContent.append(
					'<div>' +
						'<span class="insta-user__header-info">' +
							'<i class="fas fa-globe-europe" aria-hidden="true"></i>' +
							'<a href="' + Profile.siteWeb + '" target="_blank" rel="nofollow">Site Web</a>' +
						'</span>' +
					'</div>');
			}
			
			// Affiche les données de l'entête
			$targetHeader.append($wrapperHeadContent);
			
			/** @since 1.3.1 Ajout de la gestion des stories */
			if(parseInt(Profile.highlight_reel_count) > 0) {
				$targetStories.attr('data-id', Profile.id);
				$targetStories.css('display', 'block');
			}
			
			/** @since 1.4.2 Ajout de la gestion des suggested account */
			if(Profile.edge_related_profiles && parseInt(Profile.edge_related_profiles) > 0) {
				$targetSuggestedAccount.attr('data-id', Profile.id);
				$targetSuggestedAccount.css('display', 'block');
			}
			
			/** @since 1.4.3 Ajout de la gestion des tagged posts */
			if(Profile.tagged_posts && parseInt(Profile.tagged_posts) > 0) {
				$targetTaggedPostsAccount.attr('data-id', Profile.id);
				$targetTaggedPostsAccount.css('display', 'block');
			}
		}
		
		//Construit et affiche le code HTML du contenu. Les posts
		function publishContentUser(Items) {
			
			var topPosts = $targetCheckBox1.is(':checked') ? true : false; // Top posts sélectionné
			
			// Tri des objets
			if(topPosts === false) {
				Items.sort(sort_by(settings.data_sort, true, parseInt));
			} else {
				Items.sort(sort_by('likeCount_sort', true, parseInt));
			}
			
			/** @since 1.4.6 Instance Isotope avant imagesLoaded */
			$target.isotope(isotopeOptions);
			
			// Parcours de tous les items à afficher
			$.each(Items, function(indice, item) {
				if(topPosts && indice >= nbTopPosts) { return true; }
				
				var $wrapperItem = $('<div/>', { class: 'insta-user__item'}); // Le container global d'un item
				var $wrapperContent = $('<div/>', { class: 'insta-user__item-content ' + settings.data_style}); // Le container de l'image
				var $wrapperIcones = $('<div/>', { class: 'insta-user__meta-items' }); // Le container des likes comment...
				var $wrapperHeadIcones = $('<div/>', { class: 'insta-user__head-icon' }); // Le container des tagged user, du slideshow ou de la video
				
				// Ajout des pictos Location et tagged user en haut de la div
				if(settings.data_place) {
					/** @since 1.4.5 Ajout du picto location */
					if(item.place && $targetLocationSelectedItem.length > 0) { // Le composant Location est dans la page
						var pictoLocation =
							'<div class="insta-user__meta-item insta-user__place" data-place="' + item.place.id + '::' + item.place.name + '">' +
								'<span class="insta_user__place-icon">' +
									'<i class="fa fa-map-marker" aria-hidden="true" title="Place: ' + item.place.name + '"></i>' +
								'</span>' +
							'</div>';
						$wrapperHeadIcones.append(pictoLocation);
					}
					
					/** @since 1.4.4 Ajout du picto des tagged user */
					if(item.edge_media_to_tagged_user) {
						var pictoTaggedUser =
							'<div class="insta-user__meta-item insta-user__tagged-user" data-taggeduser="' + item.edge_media_to_tagged_user + '">' +
								'<span class="insta-user__tagged-user-icon">' +
									'<i class="fa fa-user" aria-hidden="true" title="Show tagged users"></i>' +
								'</span>' +
							'</div>';
						$wrapperHeadIcones.append(pictoTaggedUser);
					}				
				}
				
				// Ajout des pictos slideshow et video en haut de la div
				if(settings.data_video) {
					/** @since 1.6.1 Suppression du picto video */
					
					/** @since 1.5.0 Ajout du picto download pour la video. Doit supporter l'API fetch */
					/** @since 1.5.2 Correctif suppression de 'item.video_view_count) > 0' */
					if(item.video && window.fetch) {
						var pictoDownloadVideo =
							'<div class="insta-user__meta-item insta-user__download-video" data-downloadvideo="' + item.video_url + '">' + 
								'<span class="insta-user__download-video-icon">' +
									'<i class="fas fa-download" aria-hidden="true" title="Download video"></i>' +
								'</span>' +
							'</div>';
						$wrapperHeadIcones.append(pictoDownloadVideo);
					}
					
					/** @since 1.6.1 Suppression du picto diaporama */
				}
				
				// Lien page Instagram si les pictos video ou place sont configurer
				if(settings.data_link && (settings.data_place || settings.data_video)) {
					var dataLink = instagram_uriShortcode + item.linkNode + '/';
					var readMore = 
						'<div class="insta-user__meta-item insta-user__link">' +
							'<span class="insta-user__link-icon">' +
								'<a href="' + dataLink + '" target="_blank" rel="nofollow">' +
									'<i class="fab fa-instagram fa-lg" aria-hidden="true"></i>' +
								'</a>' +
							'</span>' +
						'</div>';
					$wrapperHeadIcones.append(readMore);
				}
						
				// Il y a des pictos en haut de la div
				if($wrapperHeadIcones.html().length !== 0) {
					$wrapperContent.append($wrapperHeadIcones);
				}
				
				// Ajout de l'image avec ou sans visionneuse
				var img = '';
				var imgsrcset = 'srcset="' + item.img_thumb + ' 150w,' + item.image240px + ' 240w,' + item.img_low + ' 320w,' + item.image480px + ' 480w,' + item.img_med + ' 640w" sizes="293px"';
				//var imageLayout = eval('item.' + settings.data_layout); /** @since 1.4.1 Affichage des images en mode grille ou mosaïque */
				var imageLayout = eval('item.' + settings.data_photo_size);
				
				if(settings.data_lightbox) {
					var dataCaption = item.caption ? item.caption.replace(/"/g, "'").substring(0, 100) + '...' : '...';
					img =	'<div class="insta-user__item-image">' +
								'<a href="' + eval('item.' + settings.data_photo_size) + '" data-elementor-open-lightbox="no" data-fancybox="insta-user-gallery" data-caption="' + dataCaption + '">' +
									'<img class="eac-image-loaded" src="' + imageLayout + '" alt="Hooops!!!">' +
								'</a>' +
							'</div>';
				} else { // Pas de bouton lien vers l'image Instagram. On place le lien sur l'image
					if(! settings.data_link) {
						var instaLink = instagram_uriShortcode + item.linkNode + '/';
						img =	'<div class="insta-user__item-image">' +
									'<a href="' + instaLink + '" target="_blank" rel="nofollow">' +
										'<img class="eac-image-loaded" src="' + imageLayout + '" alt="Hooops!!!">' +
									'</a>' +
								'</div>';
					} else {
						img =	'<div class="insta-user__item-image">' +
									'<img class="eac-image-loaded" src="' + imageLayout + '" alt="Hooops!!!">' +
								'</div>';
					}
				}
				$wrapperContent.append(img);
				
				// Ajout des likes & comments & hashtags & nombre de jours publié & vidéo/slideshow
				if(settings.data_likes && parseInt(item.likeCount) > 0) {
					var likes =
						'<div class="insta-user__meta-item insta-user__likes-count" data-likes="' + item.linkNode + '">' +
							'<span class="insta-user__likes-icon">' +
								'<i class="far fa-heart" aria-hidden="true"></i>' +
								'<span>' + item.likeCount + '</span>' +
							'</span>' +
						'</div>';
					$wrapperIcones.append(likes);
				}
				
				if(settings.data_comments && parseInt(item.commentCount) > 0) {
					var comments =
						'<div class="insta-user__meta-item insta-user__comments-count" data-comments="' + item.linkNode + '">' +
							'<span class="insta-user__comments-icon">' +
								'<i class="far fa-comment" aria-hidden="true"></i>' +
								'<span>' + item.commentCount +	'</span>' +
							'</span>' +
						'</div>';
					$wrapperIcones.append(comments);
				}
				
				// Traitement des mentions
				if(settings.data_mention && parseInt(item.mentionCount) > 0) {
					var mention =
						'<div class="insta-user__meta-item insta-user__mentions-count" data-mentions="' + item.mentionList + '">' +
							'<span class="insta-user__mentions-icon" title="Show mentions">' +
								'<i class="fas fa-at" aria-hidden="true"></i>' +
								'<span>' + item.mentionCount + '</span>' +
							'</span>' +
						'</div>';
					$wrapperIcones.append(mention);
				}
				
				/** @since 1.5.2 Traitement des Hashtags */
				if(settings.data_hashtag && parseInt(item.hashtagCount) > 0) {
					var hashtag =
						'<div class="insta-user__meta-item insta-user__hashtags-count" data-hashtags="' + item.hashtagList + '">' +
							'<span class="insta-user__hashtags-icon" title="Show hashtags">' +
								'<i class="fas fa-hashtag" aria-hidden="true"></i>' +
								'<span>' + item.hashtagCount + '</span>' +
							'</span>' +
						'</div>';
					$wrapperIcones.append(hashtag);
				}
				
				if(settings.data_date) {
					var update =
						'<div class="insta-user__meta-item insta-user__published">' +
							//'<span class="insta-user__published-icon">' +
								//'<i class="fa fa-calendar" aria-hidden="true"></i>' +
							//'</span>' +
							//'<span>' + new Date(item.update * 1000).toLocaleDateString() + '</span>' +
							'<span>' + item.updateEnJours + '</span>' +
						'</div>';
					$wrapperIcones.append(update);
				}
				
				// Lien page Instagram si les pictos video et place ne sont pas configurer
				if(settings.data_link && !settings.data_place && !settings.data_video) {
					var dataLink = instagram_uriShortcode + item.linkNode + '/';
					var readMore = 
						'<div class="insta-user__meta-item">' +
							'<span class="insta-user__link-icon">' +
								'<a href="' + dataLink + '" target="_blank" rel="nofollow">' +
									'<i class="fab fa-instagram fa-lg" aria-hidden="true"></i>' +
								'</a>' +
							'</span>' +
						'</div>';
					$wrapperIcones.append(readMore);
				}
				
				// Il y a des pictos
				if($wrapperIcones.html().length !== 0) {
					$wrapperContent.append($wrapperIcones);
				}
				
				// Ajout du nombre de mots du caption
				if(settings.data_caption) {
					if(item.caption !== '') {
						// Peut pas découper en mots because les sinogrammes (chinois) Grrrr !!
						item.caption = removeEmojis(item.caption);
						item.caption = item.caption.substring(0, settings.data_length) + '[...]';
					} else {
						item.caption = '[...]';
					}
					// Ajout du caption
					var caption = '<div class="insta-user__item-description"><p>' + item.caption + '</p></div>';
					$wrapperContent.append(caption);
				}
				
				// Ajout dans les wrappers
				$wrapperItem.append($wrapperContent);
				
				/**  @since 1.4.6 Instancie la lib Isotope, charge les images et redessine le layout */
				$target.append($wrapperItem).isotope('appended', $wrapperItem);
			});
			
			// Redessine après imagesLoaded
			$target.imagesLoaded(function() { $target.isotope('layout'); });
				
			/** @since 1.4.9 Inscrit le nombre de posts chargés, dans le bouton Next */
			var valueButton = parseInt($targetItemsCount.text()) + parseInt(Items.length);
			$targetItemsCount.text(' ' + valueButton);
			
			isotopeActive = !isotopeActive;
		}
		
		/**
		 * Object getInstagramUserProfileByName 
		 * Lance une requête sur les serveurs Instagram et récupère le profile d'un utilisateur
		 *
		 * @param {username} Le nom de l'utilisateur objet de la requête
		 * @return {object} Profile d'un utilisateur au format JSON
		 * @since 1.6.2
		 */
		var getInstagramUserProfileByName = async function(username) {
			var uriUser = instagram_uriUser + username + "/?__a=1";
			var resp = false;
			var headers = new Headers({
				"Accept": "application/json;charset=utf-8",
				"Content-Type": "application/json;charset=utf-8",
			});
			
			await fetch(uriUser, headers)
			.then(function(response) {
				if(response.status >= 400 && response.status < 600) {
					throw new Error('HTTP Error::' + response.status);
				}
				return response.json();
			})
			.then(function(json) {
				resp = json.graphql.user;
			}).catch(function(error) {
				console.log(error);
			});
			
			return resp;
		};
	};
	
})(jQuery);