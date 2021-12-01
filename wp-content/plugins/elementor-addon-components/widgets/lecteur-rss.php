<?php

/*====================================================================================================
* Class: Lecteur_Rss_Widget
* Name: Lecteur RSS
* Slug: eac-addon-lecteur-rss
*
* Description: Lecteur_Rss_Widget affiche une liste de médias
* qui diffuse du contenu au format RSS
*
* @since 1.0.0
* @since 1.8.2	Ajout de la propriété 'prefix_class' pour modifier le style sans recharger le widget
*				Ajout d'un control pour poser l'URL sur l'image
* @since 1.8.7	Application des breakpoints
*				Suppression de la méthode 'init_settings'
*=====================================================================================================*/

namespace EACCustomWidgets\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Repeater;
use Elementor\Core\Breakpoints\Manager as Breakpoints_manager;
use Elementor\Plugin;

if (! defined('ABSPATH')) exit; // Exit if accessed directly

class Lecteur_Rss_Widget extends Widget_Base {

    /*
    * Retrieve widget name.
    *
    * @access public
    *
    * @return string widget name.
    */
    public function get_name() {
        return 'eac-addon-lecteur-rss';
    }

    /*
    * Retrieve widget title.
    *
    * @access public
    *
    * @return string widget title.
    */
    public function get_title() {
        return __("Flux RSS", 'eac-components');
    }

    /*
    * Retrieve widget icon.
    *
    * @access public
    *
    * @return string widget icon.
    */
    public function get_icon() {
        return 'eicon-alert';
    }
	
	/* 
	* Affecte le composant à la catégorie définie dans plugin.php
	* 
	* @access public
    *
    * @return widget category.
	*/
	public function get_categories() {
		return ['eac-elements'];
	}
	
	/* 
	* Load dependent libraries
	* 
	* @access public
    *
    * @return libraries list.
	*/
	public function get_script_depends() {
		return [''];
	}
	
	/** 
	 * Load dependent styles
	 * Les styles sont chargés dans le footer
	 * 
	 * @access public
	 *
	 * @return CSS list.
	 */
	public function get_style_depends() {
		return ['eac-lecteur-rss'];
	}
	
    /*
    * Register widget controls.
    *
    * Adds different input fields to allow the user to change and customize the widget settings.
    *
    * @access protected
    */
    protected function register_controls() {
		
		$this->start_controls_section('rss_galerie_settings',
			[
				'label'     => __('Flux RSS', 'eac-components'),
			]
		);
			
			$this->add_control('rss_unique_instance',
				[
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'eac-editor-panel_info',
					'raw'  => __("Atlas des flux RSS des journaux de langue Française - <a href='http://atlasflux.saynete.net/' target='_blank' rel='nofolow'>Consulter ce site</a>", 'eac-components'),
				]
			);
			
			$repeater = new Repeater();
			
			$repeater->add_control('rss_item_title',
				[
					'label'   => __('Titre', 'eac-components'),
					'type'    => Controls_Manager::TEXT,
				]
			);
			
			$repeater->add_control('rss_item_url',
				[
					'label'       => __('URL', 'eac-components'),
					'type'        => Controls_Manager::URL,
					'placeholder' => 'http://your-link.com/xml/',
					'default' => [
						'is_external' => true,
						'nofollow' => true,
					],
				]
			);
			
			$this->add_control('rss_image_list',
				[
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => [
						[
							'rss_item_title'	=> 'Reuters - Politique',
							'rss_item_url'	=> ['url' => 'http://feeds.reuters.com/Reuters/PoliticsNews'],
						],
						[
							'rss_item_title'	=> 'Reuters Videocast - World News',
							'rss_item_url'	=> ['url' => 'http://feeds.reuters.com/reuters/USVideoWorldNews'],
						],
						[
							'rss_item_title'	=> 'Twitter User - AFP',
							'rss_item_url'	=> ['url' => 'http://twitrss.me/twitter_user_to_rss/?user=afpfr'],
						],
						[
							'rss_item_title'	=> 'Twitter User - CNN France',
							'rss_item_url'	=> ['url' => 'http://twitrss.me/twitter_user_to_rss/?user=CNNFrancePR'],
						],
						[
							'rss_item_title'	=> 'Twitter - ONU',
							'rss_item_url'	=> ['url' => 'http://twitrss.me/twitter_user_to_rss/?user=ONU_fr'],
						],
						[
							'rss_item_title'	=> "Youtube Playlist - L'univers",
							'rss_item_url'	=> ['url' => 'https://www.youtube.com/feeds/videos.xml?playlist_id=PLFs4vir_WsTwEd-nJgVJCZPNL3HALHHpF'],
						],
						[
							'rss_item_title'	=> "Youtube Channel - Arte vidéo",
							'rss_item_url'	=> ['url' => 'https://www.youtube.com/feeds/videos.xml?channel_id=UCL_cZf5sHKQHMRIEax5o3sg'],
						],
						[
							'rss_item_title'	=> "Youtube Channel - Arte cinéma",
							'rss_item_url'	=> ['url' => 'https://www.youtube.com/feeds/videos.xml?channel_id=UClo03hULFynpoX3w1Jv7fhw'],
						],
						[
							'rss_item_title'	=> "Youtube Channel - Euronews",
							'rss_item_url'	=> ['url' => 'https://www.youtube.com/feeds/videos.xml?channel_id=UCW2QcKZiU8aUGg4yxCIditg'],
						],
						[
							'rss_item_title'	=> "Vimeo Channel - Skydive Kalifornia",
							'rss_item_url'	=> ['url' => 'https://vimeo.com/channels/skydivekalifornia/videos/rss'],
						],
						[
							'rss_item_title'	=> "Vimeo Channel - Staff Picks",
							'rss_item_url'	=> ['url' => 'https://vimeo.com/channels/1406523/videos/rss'],
						],
						[
							'rss_item_title'	=> "Vimeo User - La cabane",
							'rss_item_url'	=> ['url' => 'https://vimeo.com/user1755454/videos/rss'],
						],
						[
							'rss_item_title'	=> 'Libération',
							'rss_item_url'	=> ['url' => 'http://rss.liberation.fr/rss/latest/'],
						],
						[
							'rss_item_title'	=> 'Le Figaro',
							'rss_item_url'	=> ['url' => 'https://www.lefigaro.fr/rss/figaro_une.xml'],
						],
						[
							'rss_item_title'	=> 'Le Parisien',
							'rss_item_url'	=> ['url' => 'http://www.leparisien.fr/politique/rss.xml'],
						],
						[
							'rss_item_title'	=> "L'Express",
							'rss_item_url'	=> ['url' => 'https://www.lexpress.fr/rss/alaune.xml'],
						],
						[
							'rss_item_title'	=> "Mediapart",
							'rss_item_url'	=> ['url' => 'https://www.mediapart.fr/articles/feed'],
						],
						[
							'rss_item_title'	=> "Arrêt sur images",
							'rss_item_url'	=> ['url' => 'https://api.arretsurimages.net/api/public/rss/all-content'],
						],
						[
							'rss_item_title'	=> "Courrier International",
							'rss_item_url'	=> ['url' => 'https://www.courrierinternational.com/feed/all/rss.xml'],
						],
						[
							'rss_item_title'	=> "20 Minutes",
							'rss_item_url'	=> ['url' => 'https://www.20minutes.fr/feeds/rss-une.xml'],
						],
						[
							'rss_item_title'	=> "L'Équipe",
							'rss_item_url'	=> ['url' => 'https://www.lequipe.fr/rss/actu_rss.xml'],
						],
						[
							'rss_item_title'	=> 'France TV - Info',
							'rss_item_url'	=> ['url' => 'https://www.francetvinfo.fr/titres.rss'],
						],
						[
							'rss_item_title'	=> 'France 3 - Régions',
							'rss_item_url'	=> ['url' => 'https://france3-regions.francetvinfo.fr/politique/rss'],
						],
						[
							'rss_item_title'	=> 'BFM TV',
							'rss_item_url'	=> ['url' => 'https://www.bfmtv.com/rss/info/flux-rss/flux-toutes-les-actualites/'],
						],
						[
							'rss_item_title'	=> 'LCI',
							'rss_item_url'	=> ['url' => 'https://storage.googleapis.com/rss-lci/rss.xml'],
						],
						[
							'rss_item_title'	=> "Airparif - Qualité de l'air",
							'rss_item_url'	=> ['url' => 'https://www.airparif.asso.fr/rss/indices'],
						],
						[
							'rss_item_title'	=> 'Sciences et Avenir - Espace',
							'rss_item_url'	=> ['url' => 'https://www.sciencesetavenir.fr/espace/rss.xml'],
						],
						[
							'rss_item_title'	=> 'RTBF - Info',
							'rss_item_url'	=> ['url' => 'https://rss.rtbf.be/article/rss/highlight_rtbfinfo_info-accueil.xml'],
						],
						[
							'rss_item_title'	=> 'Huffington Post',
							'rss_item_url'	=> ['url' => 'https://www.huffingtonpost.fr/feeds/index.xml'],
						],
						[
							'rss_item_title'	=> 'The Times - News',
							'rss_item_url'	=> ['url' => 'https://feeds.feedburner.com/time/newsfeed'],
						],
						[
							'rss_item_title'	=> 'BBC News - World',
							'rss_item_url'	=> ['url' => 'https://feeds.bbci.co.uk/news/world/rss.xml'],
						],
						[
							'rss_item_title'	=> 'The Gardian - World',
							'rss_item_url'	=> ['url' => 'https://www.theguardian.com/world/rss'],
						],
						[
							'rss_item_title'	=> 'Clarìn - Argentina',
							'rss_item_url'	=> ['url' => 'https://www.clarin.com/rss/lo-ultimo/'],
						],
						[
							'rss_item_title'	=> 'Corriere della Sera',
							'rss_item_url'	=> ['url' => 'https://xml2.corriereobjects.it/rss/homepage.xml'],
						],
						[
							'rss_item_title'	=> 'El Paìs',
							'rss_item_url'	=> ['url' => 'https://ep00.epimg.net/rss/internacional/portada.xml'],
						],
						[
							'rss_item_title'	=> 'Mali - Dépêches du Mali',
							'rss_item_url'	=> ['url' => 'http://www.depechesdumali.com/?format=feed&amp;type=rss'],
						],
						[
							'rss_item_title'	=> 'Sénégal - APS',
							'rss_item_url'	=> ['url' => 'http://aps.sn/spip.php?page=backend'],
						],
						[
							'rss_item_title'	=> 'Die Welt',
							'rss_item_url'	=> ['url' => 'https://www.welt.de/feeds/latest.rss'],
						],
						[
							'rss_item_title'	=> 'CNN World',
							'rss_item_url'	=> ['url' => 'http://rss.cnn.com/rss/edition_world.rss'],
						],
						[
							'rss_item_title'	=> 'New York Times - World',
							'rss_item_url'	=> ['url' => 'https://rss.nytimes.com/services/xml/rss/nyt/World.xml'],
						],
						[
							'rss_item_title'	=> 'USA TODAY - News',
							'rss_item_url'	=> ['url' => 'http://rssfeeds.usatoday.com/usatoday-NewsTopStories'],
						],
						[
							'rss_item_title'	=> 'Allociné - Prochainement',
							'rss_item_url'	=> ['url' => 'http://rss.allocine.fr/ac/bandesannonces/prochainement'],
						],
						[
							'rss_item_title'	=> 'Première - Actu Cinéma',
							'rss_item_url'	=> ['url' => 'http://www.premiere.fr/rss/actu-cinema'],
						],
						[
							'rss_item_title'	=> 'UFSF',
							'rss_item_url'	=> ['url' => 'https://muaddib-sci-fi.blogspot.com/feeds/posts/default/'],
						],
						[
							'rss_item_title'	=> 'Le Primate indiscipliné',
							'rss_item_url'	=> ['url' => 'https://primatemaster.blogspot.com/feeds/posts/default'],
						],
						[
							'rss_item_title'	=> 'Warning Zone',
							'rss_item_url'	=> ['url' => 'http://stalkerjany.blogspot.com/feeds/posts/default/'],
						],
						[
							'rss_item_title'	=> 'Ciné Bis Art',
							'rss_item_url'	=> ['url' => 'https://humungus-cinebisart.blogspot.com/feeds/posts/default'],
						],
						[
							'rss_item_title'	=> 'Ciné Séance',
							'rss_item_url'	=> ['url' => 'http://cineseance.blogspot.com/feeds/posts/default'],
						],
						[
							'rss_item_title'	=> 'WP Formation',
							'rss_item_url'	=> ['url' => 'https://wpformation.com/feed/'],
						],
						[
							'rss_item_title'	=> 'WP Marmite',
							'rss_item_url'	=> ['url' => 'http://feedpress.me/WPMarmite'],
						],
						[
							'rss_item_title'	=> 'Youtube Channel France Inter - Par Jupiter',
							'rss_item_url'	=> ['url' => 'https://www.youtube.com/feeds/videos.xml?channel_id=UCoTH3pnemiwrngkSD2Z735w'],
						],
						[
							'rss_item_title'	=> 'Podcast France Inter - Sur les épaules de Darwin',
							'rss_item_url'	=> ['url' => 'http://radiofrance-podcast.net/podcast09/rss_11549.xml'],
						],
						[
							'rss_item_title'	=> 'Podcast France Inter - Le 7/9',
							'rss_item_url'	=> ['url' => 'http://radiofrance-podcast.net/podcast09/rss_10241.xml'],
						],
						[
							'rss_item_title'	=> 'Podcast France Inter - Monsieur X',
							'rss_item_url'	=> ['url' => 'http://radiofrance-podcast.net/podcast09/rss_14726.xml'],
						],
						[
							'rss_item_title'	=> 'Podcast France Inter - Ça peut pas faire de mal',
							'rss_item_url'	=> ['url' => 'http://radiofrance-podcast.net/podcast09/rss_11262.xml'],
						],
						[
							'rss_item_title'	=> 'Podcast France Inter - Le masque et la plume',
							'rss_item_url'	=> ['url' => 'http://radiofrance-podcast.net/podcast09/rss_14007.xml'],
						],
						[
							'rss_item_title'	=> 'Podcast France Culture - La méthode scientifique',
							'rss_item_url'	=> ['url' => 'http://radiofrance-podcast.net/podcast09/rss_14312.xml'],
						],
						[
							'rss_item_title'	=> 'Podcast France Culture - Du grain à moudre',
							'rss_item_url'	=> ['url' => 'http://radiofrance-podcast.net/podcast09/rss_19870.xml'],
						],
						[
							'rss_item_title'	=> 'Podcast France Culture - Juke-Box',
							'rss_item_url'	=> ['url' => 'http://radiofrance-podcast.net/podcast09/rss_16999.xml'],
						],
						[
							'rss_item_title'	=> 'Podcast Collège de France - Tous les podcasts',
							'rss_item_url'	=> ['url' => 'http://podcast.college-de-france.fr/xml/general.xml'],
						],
						[
							'rss_item_title'	=> 'Podcast Collège de France - Histoire des religions',
							'rss_item_url'	=> ['url' => 'http://podcast.college-de-france.fr/xml/histoirereligions.xml'],
						],
						[
							'rss_item_title'	=> 'Podcast Collège de France - Sciences et technologies',
							'rss_item_url'	=> ['url' => 'http://podcast.college-de-france.fr/xml/sciencestechno.xml'],
						],
						[
							'rss_item_title'	=> 'Podcast BBC - BBC World Service',
							'rss_item_url'	=> ['url' => 'https://podcasts.files.bbci.co.uk/p02nq0gn.rss'],
						],
						[
							'rss_item_title'	=> 'Podcast VOA - Learning English Broadcast',
							'rss_item_url'	=> ['url' => 'https://learningenglish.voanews.com/podcast/?count=20&zoneId=1689'],
						],
						[
							'rss_item_title'	=> 'Podcast VOA - As it is',
							'rss_item_url'	=> ['url' => 'https://learningenglish.voanews.com/podcast/?count=20&zoneId=3521'],
						],
						[
							'rss_item_title'	=> 'Podcast Spanish - Learn Spanish',
							'rss_item_url'	=> ['url' => 'https://learnrealspanish.libsyn.com/rss'],
						],
					],
					'title_field' => '{{{ rss_item_title }}}',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('rss_items_content',
				[
					'label'     => __('Contenu', 'eac-components'),
				]
			);
			
			$this->add_control('rss_item_nombre',
				[
					'label' => __("Nombre d'articles", 'eac-components'),
					'type' => Controls_Manager::NUMBER,
					'min' => 5,
					'max' => 50,
					'step' => 5,
					'default' => 20,
				]
			);
			
			$this->add_control('rss_item_length',
				[
					'label' => __('Nombre de mots', 'eac-components'),
					'description' => __('Résumé', 'eac-components'),
					'type'  => Controls_Manager::NUMBER,
					'min' => 5,
					'max' => 50,
					'step' => 5,
					'default' => 20,
				]
			);
			
			$this->add_control('rss_item_date',
				[
					'label' => __("Date de Publication/Auteur", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
					'separator' => 'before',
				]
			);
			
			$this->add_control('rss_item_image',
				[
					'label' => __("Image", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
					'separator' => 'before',
				]
			);
			
			$this->add_control('rss_item_lightbox',
				[
					'label' => __("Visionneuse", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
					'condition' => ['rss_item_image' => 'yes', 'rss_item_image_link!' => 'yes'],
				]
			);
			
			/** @since 1.8.2 Ajout du control switcher pour mettre le lien du post sur l'image */
			$this->add_control('rss_item_image_link',
				[
					'label' => __("Lien de l'article sur l'image", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'condition' => ['rss_item_image' => 'yes', 'rss_item_lightbox!' => 'yes'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('rss_layout_type_settings',
			[
				'label' => __('Disposition', 'eac-components'),
			]
		);
			
			// @since 1.8.7 Add default values for all active breakpoints.
			$active_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();
			$columns_device_args = [];
			foreach($active_breakpoints as $breakpoint_name => $breakpoint_instance) {
				if(!in_array($breakpoint_name, [Breakpoints_manager::BREAKPOINT_KEY_WIDESCREEN, Breakpoints_manager::BREAKPOINT_KEY_LAPTOP])) {
					if($breakpoint_name === Breakpoints_manager::BREAKPOINT_KEY_MOBILE) {
						$columns_device_args[$breakpoint_name] = ['default' => '1'];
					}  else if ($breakpoint_name === Breakpoints_manager::BREAKPOINT_KEY_MOBILE_EXTRA) {
						$columns_device_args[$breakpoint_name] = ['default' => '1'];
					} else {
						$columns_device_args[$breakpoint_name] = ['default' => '2'];
					}
				}
			}
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('rss_columns',
				[
					'label'   => __('Nombre de colonnes', 'eac-components'),
					'type'    => Controls_Manager::SELECT,
					'default' => '3',
					'device_args' => $columns_device_args,
					'options'       => [
						'1'    => '1',
						'2'    => '2',
						'3'    => '3',
						'4'    => '4',
						'5'    => '5',
						'6'    => '6',
					],
					'prefix_class' => 'responsive%s-',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('rss_general_style',
			[
				'label'      => __('Global', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);
			
			/** @since 1.8.2 */
			$this->add_control('rss_wrapper_style',
				[
					'label'			=> __("Style", 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'default'		=> 'style-1',
					'options'       => [
						'style-0' => __("Défaut", 'eac-components'),
                        'style-1' => 'Style 1',
                        'style-2' => 'Style 2',
						'style-3' => 'Style 3',
						'style-4' => 'Style 4',
						'style-5' => 'Style 5',
						'style-6' => 'Style 6',
						'style-7' => 'Style 7',
						'style-8' => 'Style 8',
						'style-9' => 'Style 9',
                    ],
					'prefix_class' => 'rss-galerie_wrapper-',
				]
			);
			
			$this->add_control('rss_wrapper_margin',
				[
					'label' => __('Marge entre les colonnes', 'eac-components'),
					'type'  => Controls_Manager::SLIDER,
					'size_units' => ['em'],
					'range' => ['em' => ['min' => 0, 'max' => 5, 'step' => .2]],
					'default' => ['unit' => 'em', 'size' => .5],
					'selectors' => ['{{WRAPPER}} .rss-galerie' => 'column-gap: {{SIZE}}{{UNIT}};'],
				]
			);
			
			$this->add_control('rss_wrapper_bg_color',
				[
					'label' => __('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => ['{{WRAPPER}} .eac-rss-galerie' => 'background-color: {{VALUE}};'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('rss_title_style',
			[
               'label' => __("Titre", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('rss_titre_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => ['{{WRAPPER}} .rss-galerie__item-titre' => 'color: {{VALUE}};'],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'rss_titre_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .rss-galerie__item-titre',
				]
			);
			
			
		$this->end_controls_section();
		
		$this->start_controls_section('rss_excerpt_style',
			[
               'label' => __("Résumé", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('rss_excerpt_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => ['{{WRAPPER}} .rss-galerie__item-description p' => 'color: {{VALUE}};'],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'rss_excerpt_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .rss-galerie__item-description p',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('rss_icone_style',
			[
               'label' => __("Pictogrammes", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['rss_item_date' => 'yes'],
			]
		);
			
			$this->add_control('rss_icone_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => ['{{WRAPPER}} .rss-galerie__item-date i, {{WRAPPER}} .rss-galerie__item-auteur i' => 'color: {{VALUE}};'],
				]
			);
			
		$this->end_controls_section();
    }
	
	/*
    * Render widget output on the frontend.
    *
    * Written in PHP and used to generate the final HTML.
    *
    * @access protected
    */
    protected function render() {
		$settings = $this->get_settings_for_display();
		if(! $settings['rss_image_list']) {
			return;
		}
		$this->add_render_attribute('rss_galerie', 'class', 'rss-galerie');
		$this->add_render_attribute('rss_galerie', 'data-settings', $this->get_settings_json());
		
		?>
		<div class="eac-rss-galerie">
			<?php $this->render_galerie(); ?>
			<div <?php echo $this->get_render_attribute_string('rss_galerie'); ?>></div>
		</div>
		<?php
    }
	
    protected function render_galerie() {
		$settings = $this->get_settings_for_display();
		
		?>
		<div class="rss-select__item-list">
			<div class="rss-options__items-list">
				<select id="rss__options-items" class="rss__options-items">
					<?php foreach($settings['rss_image_list'] as $item) { ?>
						<?php if(! empty($item['rss_item_url']['url'])) : ?>
							<option value="<?php echo esc_url($item['rss_item_url']['url']); ?>"><?php echo esc_html($item['rss_item_title']); ?></option>
						<?php endif; ?>
					<?php } ?>
				</select>
			</div>
			<div class="rss-select__item">
			    <?php //wp_nonce_field('_nonce_rss__item-name', 'nonce_rss__item-name'); ?>
				<input type="text" id="rss__item-name" name="rss__item-name" required minlength="4" maxlength="100" size="20">
			</div>
			<div class="eac__button">
				<button id="rss__read-button" class="eac__read-button"><?php _e('Lire le flux', 'eac-components'); ?></button>
			</div>
			<div id="rss__loader-wheel" class="eac__loader-spin"></div>
		</div>
		<div class="rss-item__header"></div>
		<?php
	}
	
	/*
	* get_settings_json()
	*
	* Retrieve fields values to pass at the widget container
    * Convert on JSON format
    * Read by 'eac-components.js' file when the component is loaded on the frontend
	*
	* @uses      json_encode()
	*
	* @return    JSON oject
	*
	* @access    protected
	* @since 1.0.0
	* @since 1.0.7
	* @since 1.8.2	Suppression de la propriété du style
	*				Ajout du paramètre 'data_image_link'
	*/
	
	protected function get_settings_json() {
		$module_settings = $this->get_settings_for_display();
		
		$settings = array(
			"data_nombre"	=> $module_settings['rss_item_nombre'],
			"data_longueur"	=> $module_settings['rss_item_length'],
			//"data_style"	=> $module_settings['rss_wrapper_style'],
			"data_date"		=> $module_settings['rss_item_date'] === 'yes' ? true : false,
			"data_img"		=> $module_settings['rss_item_image'] === 'yes' ? true : false,
			"data_lightbox"	=> $module_settings['rss_item_lightbox'] === 'yes' ? true : false,
			"data_image_link"	=> $module_settings['rss_item_image_link'] === 'yes' ? true : false,
		);
		
		$settings = json_encode($settings);
		return $settings;
	}
	
	protected function content_template() {}
	
}