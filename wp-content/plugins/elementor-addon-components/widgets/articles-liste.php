<?php

/*============================================================================================================================
* Class: Articles_Liste_Widget
* Name: Grille d'articles
* Slug: eac-addon-articles-liste
*
* Description: Affiche les articles, les CPT et les pages
* dans différents modes, masonry ou grille et avec différents filtres
*
* @since 0.0.9
* @since 1.4.1	Forcer le chargement des images depuis le serveur
* @since 1.6.0	Implémentation des balises dynamiques (Dynamic Tags)
*				Filtres sur les Auteurs et les Champs personnalisés
*				Ajout des listes (Select/Option) Auteurs/Champs personnalisés visibles pour les mobiles
*				Gestion de l'avatar
*				Utilisation de la méthode 'post_class' pour les articles
* @since 1.6.8	Alignement des filtres
* @since 1.7.0	Ajout de la liste des Custom Fields par leurs valeurs et activation des 'Dynamic Tags'
*				Sélection des types de données des clés ou des valeurs
*				Sélection de l'opérateur de comparaison pour les valeurs
*				Sélection de la relation entre les clés
*				Suppression du control 'al_content_metadata_display_values'
*				Les champs personnalisés peuvent être aussi filtrés par les auteurs d'articles
*				Simplifie le calcul et l'affichage des filtres
*				Suppression de l'overlay sur les images
*				Ajout de la liste des étiquettes pour sélection
*				Ajout du ratio Image pour le mode Grid
*				Remplace 'post_type' par l'ID du widget dans la class de l'article pour filtrer/paginer les articles
* @since 1.7.1	Supprime les callbacks du filtre 'eac/tools/post_orderby'
* @since 1.7.2	Ajout du control de positionnement vertical avec le ration de l'image
*				Ajout du control pour afficher les arguments de la requête
*				Fix: Les controls 'Texte à droite/gauche' ne sont pas cachés lorsque le control 'excerpt' est désactivé
*				Fix: Alignement du filtre pour les mobiles
*				Fix: ACF multiples valeurs d'une clé force 'get_post_meta' param $single = true pour renvoyer une chaine
* @since 1.7.3	Recherche des meta_value avec la méthode 'get_post_custom_values'
* @since 1.8.0	Le lien du post peut être ajouté à l'image
*				Le bouton 'Read more' peut être caché 
* @since 1.8.2	Ajout de la propriété 'prefix_class' pour modifier le style sans recharger le widget
* @since 1.8.4	Ajout des controles pour modifier le style du filtre
*				Ajout du mode responsive pour les marges
* @since 1.8.7	Support des custom breakpoints
*				Suppression de la méthode 'init_settings'
*============================================================================================================================*/

namespace EACCustomWidgets\Widgets;

use EACCustomWidgets\Includes\Eac_Helpers_Util;
use EACCustomWidgets\Includes\Eac_Tools_Util;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Core\Breakpoints\Manager as Breakpoints_manager;
use Elementor\Plugin;

if(! defined('ABSPATH')) exit; // Exit if accessed directly

class Articles_Liste_Widget extends Widget_Base {
	
	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);
		
		// @since 1.7.1 Supprime les callbacks du filtre de la liste 'orderby'
		remove_all_filters('eac/tools/post_orderby');
	}
	
    /*
    * Retrieve widget name.
    *
    * @access public
    *
    * @return widget name.
    */
    public function get_name() {
        return 'eac-addon-articles-liste';
    }

    /*
    * Retrieve widget title.
    *
    * @access public
    *
    * @return widget title.
    */
    public function get_title() {
        return __("Grille d'articles", 'eac-components');
    }

    /*
    * Retrieve widget icon.
    *
    * @access public
    *
    * @return widget icon.
	* https://char-map.herokuapp.com/
    */
    public function get_icon() {
        return 'eicon-post-list';
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
		return ['isotope-js', 'eac-imagesloaded', 'eac-infinite-scroll'];
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
		return ['eac-articles-liste'];
	}
	
	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.7.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return ['post', 'custom post_type', 'query', 'filter', 'advanced', 'category', 'authors', 'poat_tag', 'custom fields', 'acf'];
	}
	
	/**
	 * Get help widget get_custom_help_url.
	 *
	 * 
	 *
	 * @since 1.7.0
	 * @access public
	 *
	 * @return URL help center
	 */
	public function get_custom_help_url() {
        return 'https://elementor-addon-components.com/how-to-create-advanced-queries-for-the-component-post-grid/';
    }
	
    /*
    * Register widget controls.
    *
    * Adds different input fields to allow the user to change and customize the widget settings.
    *
    * @access protected
    */
	protected function register_controls() {
        
		// @since 1.8.7 Récupère tous les breakpoints actifs
		$active_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();
		$has_active_breakpoints = Plugin::$instance->breakpoints->has_custom_breakpoints();
		
		$this->start_controls_section('al_post_filter',
			[
				'label'	=> __('Filtre de requête', 'eac-components'),
				'tab'	=> Controls_Manager::TAB_CONTENT,
			]
		);
			
			$this->start_controls_tabs('al_article_tabs');
				
				$this->start_controls_tab('al_article_post_tab',
					[
						'label'         => __("Type d'article", 'eac-components'),
					]
				);
					
					$this->add_control('al_article_type',
						[
							'label' => __("Type d'article", 'eac-components'),
							'type' => Controls_Manager::SELECT2,
							'label_block' => true,
							'options' => Eac_Tools_Util::get_filter_post_types(),  // tools.php sous le rep. includes
							'default' => ['post'],
							'multiple' => true,
						]
					);
					
					$this->add_control('al_article_taxonomy',
						[
							'label' => __("Sélectionner les catégories", 'eac-components'),
							'type' => Controls_Manager::SELECT2,
							'label_block' => true,
							'description' => __("Associées au type d'article", 'eac-components'),
							'options' => Eac_Tools_Util::get_all_taxonomies(),
							'default' => ['category'],
							'multiple' => true,
						]
					);
					
					// @since 1.7.0 Intègre les étiquettes (Tags)
					$this->add_control('al_article_term',
						[
							'label' => __("Sélectionner les étiquettes", 'eac-components'),
							'type' => Controls_Manager::SELECT2,
							'label_block' => true,
							'description' => __("Associées aux catégories", 'eac-components'),
							'options' => Eac_Tools_Util::get_all_terms(),
							'multiple' => true,
						]
					);
					
					$this->add_control('al_content_filter_display',
						[
							'label' => __("Afficher les filtres", 'eac-components'),
							'type' => Controls_Manager::SWITCHER,
							'label_on' => __('oui', 'eac-components'),
							'label_off' => __('non', 'eac-components'),
							'return_value' => 'yes',
							'default' => 'yes',
							'separator' => 'before',
						]
					);
					
					/**
					 * @since 1.6.8 Position du filtre
					 * @since 1.7.2 Ajout de la class 'al-filters__wrapper-select' pour l'alignement du select sur les mobiles
					 */
					$this->add_control('al_content_filter_align',
						[
							'label' => __('Alignement', 'eac-components'),
							'type' => Controls_Manager::CHOOSE,
							'options' => [
								'left' => [
									'title' => __('Gauche', 'eac-components'),
									'icon' => 'eicon-h-align-left',
								],
								'center' => [
									'title' => __('Centre', 'eac-components'),
									'icon' => 'eicon-h-align-center',
								],
								'right' => [
									'title' => __('Droite', 'eac-components'),
									'icon' => 'eicon-h-align-right',
								],
							],
							'default' => 'left',
							'toggle' => true,
							'selectors' => [
								'{{WRAPPER}} .al-filters__wrapper, {{WRAPPER}} .al-filters__wrapper-select' => 'text-align: {{VALUE}};',
							],
							'condition' => ['al_content_filter_display' => 'yes'],
						]
					);
					
				$this->end_controls_tab();
				
				$this->start_controls_tab('al_article_query_tab',
					[
						'label'         => __("Requêtes", 'eac-components'),
					]
				);
					
					// @since 1.6.0 Liste des auteurs et activation des 'Dynamic Tags'
					$this->add_control('al_content_user',
						[
							'label' => __("Selection des auteurs", 'eac-components'),
							'description' => __("Balises dynamiques 'Article/Auteurs'", 'eac-components'),
							'type' => Controls_Manager::TEXT,
							'dynamic' => [
								'active' => true,
								'categories' => [
									TagsModule::POST_META_CATEGORY,
								],
							],
							'label_block' => true,
						]
					);
					
					$repeater = new Repeater();
					
					$repeater->add_control('al_content_metadata_title',
						[
							'label'   => __('Titre', 'eac-components'),
							'type'    => Controls_Manager::TEXT,
							'dynamic' => ['active' => true],
						]
					);
					
					// @since 1.6.0 Liste des Custom Fields par leurs clés et activation des 'Dynamic Tags'
					$repeater->add_control('al_content_metadata_keys',
						[
							'label' => __("Sélectionner UNE seule clé", 'eac-components'),
							'description' => __("Balises dynamiques 'Article|ACF Clés' ou entrer la clé dans le champ (sensible à la casse).", 'eac-components'),
							'type' => Controls_Manager::TEXT,
							'dynamic' => [
								'active' => true,
								'categories' => [
									TagsModule::POST_META_CATEGORY,
								],
							],
							'label_block' => true,
						]
					);
					
					// @since 1.7.0 Type de données
					$repeater->add_control('al_content_metadata_type',
						[
							'label' => __('Type des données', 'eac-components'),
							'type' => Controls_Manager::SELECT,
							'options' => [
								'CHAR'		=> __('Caractère', 'eac-components'),
								'NUMERIC'	=> __('Numérique', 'eac-components'),
								'DECIMAL(10,2)'	=> __('Décimal', 'eac-components'),
								'DATE'		=> __('Date', 'eac-components'),
							],
							'default' => 'CHAR',
						]
					);
					
					// @since 1.7.0 Comparaison entre les valeurs
					$repeater->add_control('al_content_metadata_compare',
						[
							'label' => __('Opérateur de comparaison', 'eac-components'),
							'type' => Controls_Manager::SELECT,
							'options' => Eac_Tools_Util::get_operateurs_comparaison(),
							'default' => 'IN',
						]
					);
					
					// @since 1.7.0 Liste des Custom Fields par leurs valeurs et activation des 'Dynamic Tags'
					$repeater->add_control('al_content_metadata_values',
						[
							'label' => __("Sélection des valeurs", 'eac-components'),
							'description' => __("Balises dynamiques 'Article|ACF Valeurs' ou entrer les valeurs dans le champ (insensible à la casse) et utiliser le pipe '|' comme séparateur.", 'eac-components'),
							'type' => Controls_Manager::TEXT,
							'dynamic' => [
								'active' => true,
								'categories' => [
									TagsModule::POST_META_CATEGORY,
								],
							],
							'label_block' => true,
						]
					);
					
					$this->add_control('al_content_metadata_list',
						[
							'label'       => __('Requêtes', 'eac-components'),
							'type'        => Controls_Manager::REPEATER,
							'fields'      => $repeater->get_controls(),
							'default'     => [
								[
									'al_content_metadata_title'       => __('Requête #1', 'eac-components'),
								],
							],
							'title_field' => '{{{ al_content_metadata_title }}}',
						]
					);
					
					// @since 1.7.0 Sélection de la relation entre les clés
					$this->add_control('al_content_metadata_keys_relation',
						[
							'label' => __("Relation entre les requêtes", 'eac-components'),
							'type' => Controls_Manager::SWITCHER,
							'label_on' => 'AND',
							'label_off' => 'OR',
							'return_value' => 'yes',
							'default' => '',
						]
					);
					
					/** @since 1.7.2 Affiche les arguments de la requête */
					$this->add_control('al_display_content_args',
						[
							'label' => __("Afficher la requête", 'eac-components'),
							'type' => Controls_Manager::SWITCHER,
							'label_on' => __('oui', 'eac-components'),
							'label_off' => __('non', 'eac-components'),
							'return_value' => 'yes',
							'default' => '',
							'separator' => 'before',
						]
					);
			
				$this->end_controls_tab();
				
			$this->end_controls_tabs();
		
		$this->end_controls_section();
		
        $this->start_controls_section('al_article_param',
			[
				'label'     => __('Réglages', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
			]
		);
			
			$this->add_control('al_article_id',
				[
					'label' => __("Afficher les IDs", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'separator' => 'before',
				]
			);
			
			$this->add_control('al_article_exclude',
				[
					'label' => __('Exclure IDs', 'eac-components'),
					'description' => __('Les ID séparés par une virgule sans espace','eac-components'),
					'type' => Controls_Manager::TEXT,
					'label_block' => true,
					'default' => '',
				]
			);
			
			$this->add_control('al_article_include',
				[
					'label' => __("Inclure les enfants", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'conditions' => [
						'terms' => [
							['name' => 'al_article_type', 'operator' => '!contains', 'value' => 'post']
						]
					],
				]
			);
			
			$this->add_control('al_article_nombre',
				[
					'label' => __("Nombre d'articles", 'eac-components'),
					'description' => __('-1 = Tous','eac-components'),
					'type' => Controls_Manager::NUMBER,
					'default' => 10,
					'separator' => 'before',
				]
			);
			
			$this->add_control('al_content_pagging_display',
				[
					'label' => __("Pagination", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'conditions' => [
						'terms' => [
							['name' => 'al_article_nombre', 'operator' => '!in', 'value' => [-1, '']]
						]
					],
				]
			);
			
			$this->add_control('al_content_pagging_warning',
				[
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'raw'  => __("<strong>Pagination:</strong> Dans l'éditeur, vous devez systématiquement enregistrer la page pour voir les modifications apportées au widget", "eac-components"),
					'conditions' => [
						'terms' => [
							['name' => 'al_article_nombre', 'operator' => '!in', 'value' => [-1, '']],
							['name' => 'al_content_pagging_display', 'operator' => '===', 'value' => 'yes']
						]
					],
				]
			);
			
			$this->add_control('al_article_orderby',
				[
					'label' => __('Triés par', 'eac-components'),
					'type' => Controls_Manager::SELECT,
					'options' => Eac_Tools_Util::get_post_orderby(),
					'default' => 'title',
					'separator' => 'before',
				]
			);

			$this->add_control('al_article_order',
				[
					'label' => __('Affichage', 'eac-components'),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'asc' => __('Ascendant', 'eac-components'),
						'desc' => __('Descendant', 'eac-components'),
					],
					'default' => 'asc',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('al_article_content',
			[
				'label'     => __('Contenu', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
			]
		);
			
			$this->add_control('al_content_title',
				[
					'label' => __("Titre", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('al_content_excerpt',
				[
					'label' => __("Résumé", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			/** @since 1.8.0 Ajout du control bouton 'Read more' */
			$this->add_control('al_content_readmore',
				[
					'label' => __("Bouton 'En savoir plus'", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
					'condition' => ['al_content_excerpt' => 'yes'],
				]
			);
			
			$this->add_control('al_content_term',
				[
					'label' => __("Étiquettes", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('al_content_author',
				[
					'label' => __("Auteur", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('al_content_date',
				[
					'label' => __("Date", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('al_content_comment',
				[
					'label' => __("Commentaires", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			// @since 1.6.0 Affichage de l'Avatar de l'auteur de l'article
			$this->add_control('al_content_avatar',
				[
					'label' => __("Avatar de l'auteur", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
			$this->add_control('al_content_image',
				[
					'label' => __("Image en avant", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('al_image_settings',
			[
				'label' => __('Image', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
				'condition' => ['al_content_image' => 'yes'],
			]
		);
			
			/** @since 1.8.7 Suppression du mode responsive */
			$this->add_control('al_image_dimension',
				[
					'label'   => __('Dimension', 'eac-components'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'medium',
					'options'       => [
						'thumbnail'		=> __('Miniature', 'eac-components'),
						'medium'		=> __('Moyenne', 'eac-components'),
						'medium_large'	=> __('Moyenne-large', 'eac-components'),
						'large'			=> __('Large', 'eac-components'),
						'full'			=> __('Originale', 'eac-components'),
					],
				]
			);
			
			$this->add_control('al_image_lightbox',
				[
					'label' => __("Visionneuse", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'condition' => ['al_image_link!' => 'yes'],
				]
			);
			
			/** @since 1.8.0 Ajout du control switcher pour mettre le lien du post sur l'image */
			$this->add_control('al_image_link',
				[
					'label' => __("Lien de l'article sur l'image", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'condition' => ['al_image_lightbox!' => 'yes'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('al_layout_type_settings',
			[
				'label' => __('Disposition', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
			]
		);
			
			$this->add_control('al_layout_type',
				[
					'label'   => __('Mode', 'eac-components'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'masonry',
					'options' => [
						'masonry'	=> __('Mosaïque', 'eac-components'),
						'fitRows'	=> __('Grille', 'eac-components'),
					],
				]
			);
			
			/** Ajout de la condition sur l'image */
			$this->add_control('al_layout_ratio_image_warning',
				[
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'eac-editor-panel_warning',
					'raw'  => __("Vous pouvez activer/désactiver un ratio sur les images 'Style/Image'", "eac-components"),
					'condition' => ['al_layout_type' => 'fitRows', 'al_content_image' => 'yes'],
				]
			);
			
			// @since 1.8.7 Add default values for all active breakpoints.
			$columns_device_args = [];
			foreach($active_breakpoints as $breakpoint_name => $breakpoint_instance) {
				if(!in_array($breakpoint_name, [Breakpoints_manager::BREAKPOINT_KEY_WIDESCREEN, Breakpoints_manager::BREAKPOINT_KEY_LAPTOP])) {
					if($breakpoint_name === Breakpoints_manager::BREAKPOINT_KEY_MOBILE) {
						$columns_device_args[$breakpoint_name] = ['default' => '1'];
					}  else if($breakpoint_name === Breakpoints_manager::BREAKPOINT_KEY_MOBILE_EXTRA) {
						$columns_device_args[$breakpoint_name] = ['default' => '1'];
					} else {
						$columns_device_args[$breakpoint_name] = ['default' => '2'];
					}
				}
			}
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('al_columns',
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
					'render_type' => 'template',
				]
			);
			
			/** @since 1.7.2 Cache le control lorsque le control 'al_content_excerpt' est désactivé */
			$this->add_control('al_layout_texte',
				[
					'label' => __("Texte à droite", 'eac-components'),
					'description' => __("Attention à la dimension des images. (Contenu)", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'separator' => 'before',
					'condition' => ['al_layout_type' => 'masonry', 'al_layout_texte_left!' => 'yes', 'al_content_image' => 'yes', 'al_content_excerpt' => 'yes'],
				]
			);
			
			/** @since 1.7.2 Cache le control lorsque le control 'al_content_excerpt' est désactivé */
			$this->add_control('al_layout_texte_left',
				[
					'label' => __("Texte à gauche", 'eac-components'),
					'description' => __("Attention à la dimension des images. (Contenu)", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'condition' => ['al_layout_type' => 'masonry', 'al_layout_texte!' => 'yes', 'al_content_image' => 'yes', 'al_content_excerpt' => 'yes'],
				]
			);
			
		$this->end_controls_section();
		
		/**
		 * Generale Style Section
		 */
		$this->start_controls_section('al_general_style',
			[
				'label'      => __('Global', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
			]
		);
			
			/** @since 1.8.2 */
			$this->add_control('al_wrapper_style',
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
						'style-10' => 'Style 10',
						'style-11' => 'Style 11',
						'style-12' => 'Style 12',
                    ],
					'prefix_class' => 'al-post_wrapper-',
				]
			);
			
			/**
			 * @since 1.8.4 Ajout du mode responsive
			 * @since 1.8.7 Application des breakpoints
			 */
			$this->add_responsive_control('al_wrapper_margin',
				[
					'label' => __('Marge entre les items', 'eac-components'),
					'type'  => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 6, 'unit' => 'px'],
					'range' => ['px' => ['min' => 0, 'max' => 20, 'step' => 1]],
					'selectors' => ['{{WRAPPER}} .al-post__wrapper-inner' => 'margin: {{SIZE}}{{UNIT}};'],
				]
			);
			
			$this->add_control('al_wrapper_bg_color',
				[
					'label' => __('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#BDF4CB',
					'selectors' => ['{{WRAPPER}} .al-posts__wrapper' => 'background-color: {{VALUE}};'],
				]
			);
			
		$this->end_controls_section();
		
		/** @since 1.8.4 Modification du style du filtre */
		$this->start_controls_section('al_filter_style',
			[
               'label' => __("Filtre", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['al_content_filter_display' => 'yes'],
			]
		);
			
			$this->add_control('al_filter_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => [
						'{{WRAPPER}} .al-filters__wrapper .al-filters__item, {{WRAPPER}} .al-filters__wrapper .al-filters__item a' => 'color: {{VALUE}};',
					],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'al_filter_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .al-filters__wrapper .al-filters__item, {{WRAPPER}} .al-filters__wrapper .al-filters__item a',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('al_title_style',
			[
               'label' => __("Titre", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('al_title_tag',
				[
					'label'			=> __('Étiquette', 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'default'		=> 'h2',
					'options'       => [
						'h1'    => 'H1',
                        'h2'    => 'H2',
                        'h3'    => 'H3',
                        'h4'    => 'H4',
                        'h5'    => 'H5',
                        'h6'    => 'H6',
						'div'	=> 'div',
						'p'		=> 'p',
                    ],
				]
			);
			
			$this->add_control('al_titre_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => ['{{WRAPPER}} .al-title__content a' => 'color: {{VALUE}};',],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'al_titre_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .al-title__content',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('al_image_style',
			[
               'label' => __("Image", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['al_content_image' => 'yes'],
			]
		);
			$this->add_control('al_image_border_radius',
				[
					'label' => __('Rayon de la bordure', 'eac-components'),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'allowed_dimensions' => ['top', 'right', 'bottom', 'left'],
					'default' => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px', 'isLinked' => true],
					'selectors' => [
						'{{WRAPPER}} .al-image__wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			
			/** @since 1.7.0 Active le ratio image */
			$this->add_control('al_enable_image_ratio',
				[
					'label' => __("Activer le ratio image", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('oui', 'eac-components'),
					'label_off' => __('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'separator' => 'before',
					'condition' => ['al_layout_type' => 'fitRows'],
				]
			);
			
			/**
			 * @since 1.6.0 Le ratio appliqué à l'image
			 * @since 1.8.7 Application des breakpoints
			 */
			$this->add_responsive_control('al_image_ratio',
				[
					'label' => __('Ratio', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['%'],
					'default' => ['size' => 0.6, 'unit' => '%'],
					'range' => ['%' => ['min' => 0.1, 'max' => 2, 'step' => 0.1]],
					'selectors' => ['{{WRAPPER}} .al-posts__wrapper.al-posts__image-ratio .al-image__loaded' => 'padding-bottom:calc({{SIZE}} * 100%);'],
					//'selectors' => ['{{WRAPPER}} .al-posts__wrapper.al-posts__image-ratio .al-image__loaded' => 'padding-bottom:calc({{SIZE}} / 100 * 100%);'],
					'condition' => ['al_enable_image_ratio' => 'yes', 'al_layout_type' => 'fitRows'],
				]
			);
			
			/**
			 * @since 1.7.2 Positionnement vertical de l'image
			 * @since 1.8.7 Application des breakpoints
			 */
			$this->add_responsive_control('al_image_ratio_position_y',
				[
					'label' => __('Position verticale', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['%'],
					'default' => ['size' => 50, 'unit' => '%'],
					'range' => ['%' => ['min' => 0, 'max' => 100, 'step' => 5]],
					'selectors' => ['{{WRAPPER}} .al-posts__wrapper.al-posts__image-ratio .al-image-loaded' => 'object-position: 50% {{SIZE}}%;'],
					'condition' => ['al_enable_image_ratio' => 'yes', 'al_layout_type' => 'fitRows'],
				]
			);
			
		$this->end_controls_section();
		
		/**
		 * Style de l'avatar
		 *
		 * @since 1.6.0
		 */
		$this->start_controls_section('al_avatar_style',
			[
               'label' => __("Avatar", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['al_content_avatar' => 'yes'],
			]
		);
		
			$this->add_control('al_avatar_size',
				[
					'label' => __('Dimension', 'eac-components'),
					'description' => __('Uniquement pour les Gravatars', 'eac-components'),
					'type'  => Controls_Manager::NUMBER,
					'min' => 40,
					'max' => 150,
					'default' => 60,
					'step' => 5,
				]
			);
			
			$this->add_control('al_avatar_border_radius',
				[
					'label' => __('Rayon de la bordure', 'eac-components'),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'allowed_dimensions' => ['top', 'right', 'bottom', 'left'],
					'default' => ['top' => 50, 'right' => 50, 'bottom' => 50, 'left' => 50, 'unit' => '%', 'isLinked' => true],
					'selectors' => [
						'{{WRAPPER}} .al-avatar__wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'separator' => 'before',
				]
			);
		    
		    $this->add_group_control(
			Group_Control_Border::get_type(),
				[
					'name' => 'al_avatar_image_border',
					'fields_options' => [
						'border' => ['default' => 'solid'],
						'width' => [
							'default' => [
								'top' => 5,
								'right' => 5,
								'bottom' => 5,
								'left' => 5,
								'isLinked' => true,
							],
						],
						'color' => ['default' => '#ededed'],
					],
					'selector' => '{{WRAPPER}} .al-avatar__wrapper img',
					'separator' => 'before',
				]
			);
			
			$this->add_group_control(
    			Group_Control_Box_Shadow::get_type(),
    			[
    				'name' => 'al_avatar_box_shadow',
    				'label' => __('Ombre', 'eac-components'),
    				'selector' => '{{WRAPPER}} .al-avatar__wrapper img',
    			]
    		);
			
		$this->end_controls_section();
		
		$this->start_controls_section('al_excerpt_style',
			[
               'label' => __("Résumé", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['al_content_excerpt' => 'yes'],
			]
		);
			
			$this->add_control('al_excerpt_length',
				[
					'label' => __('Nombre de mots', 'eac-components'),
					'type'  => Controls_Manager::NUMBER,
					'min' => 10,
					'max' => 100,
					'step' => 5,
					'default' => apply_filters('excerpt_length', 25), /** Ce filtre est documenté dans wp-includes/formatting.php */
				]
			);
			
			$this->add_control('al_excerpt_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => [
						'{{WRAPPER}} .al-excerpt__content p' => 'color: {{VALUE}};',
					],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'al_excerpt_typography',
					'label' => __('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .al-excerpt__content p, {{WRAPPER}} .al-tags__content,
					{{WRAPPER}} .al-author__content, {{WRAPPER}} .al-date__content, {{WRAPPER}} .al-comment__content, {{WRAPPER}} .al-link__content',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('al_icone_style',
			[
               'label' => __("Pictogrammes", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('al_icone_color',
				[
					'label' => __('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => [
						'{{WRAPPER}} .al-date__content i, {{WRAPPER}} .al-author__content i, {{WRAPPER}} .al-comment__content i,
						{{WRAPPER}} .al-tags__content i, {{WRAPPER}} .al-category-content i' => 'color: {{VALUE}};',
					],
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
		
		?>
		<div class="eac-articles-liste">
			<?php $this->render_articles(); ?>
		</div>
		<?php
    }
	
	protected function render_articles() {
		//global $wp_query, $paged;
		$settings = $this->get_settings_for_display();
		
		// Affichage du contenu titre/image/auteur...
		$has_titre = $settings['al_content_title'] === 'yes' ? true : false;
		$has_image = $settings['al_content_image'] === 'yes' ? true : false;
		$has_avatar = $settings['al_content_avatar'] === 'yes' ? true : false; // @since 1.6.0
		$avatar_size = (int) $settings['al_avatar_size'];
		$has_lb = $settings['al_image_lightbox'] === 'yes' ? true : false;
		$has_image_link = !$has_lb && $settings['al_image_link'] === 'yes' ? true : false;
		$has_term = $settings['al_content_term'] === 'yes' ? true : false;
		$has_auteur = $settings['al_content_author'] === 'yes' ? true : false;
		$has_date = $settings['al_content_date'] === 'yes' ? true : false;
		$has_resum = $settings['al_content_excerpt'] === 'yes' ? true : false;
		$has_readmore = $has_resum && $settings['al_content_readmore'] === 'yes' ? true : false;
		$has_comment = $settings['al_content_comment'] === 'yes' ? true : false;
		
		// Filtre Users. Champ TEXT
		$has_users = !empty($settings['al_content_user']) ? true : false;
		$user_filters = $settings['al_content_user'];
		
		// Filtre Taxonomie. Champ SELECT2
		$has_filters = $settings['al_content_filter_display'] === 'yes' ? true : false;
		$taxonomy_filters = $settings['al_article_taxonomy'];			// Le champ taxonomie est renseigné
		
		// Filtre Étiquettes, on prélève le slug. Champ SELECT2
		$term_slug_filters = array();
		// Extrait les slugs du tableau de terms
		if(!empty($settings['al_article_term'])) {						// Le champ étiquette est renseigné
			foreach($settings['al_article_term'] as $term_filter) {
				$term_slug_filters[] = explode('::', $term_filter)[1];	// Format term::term->slug
			}
		}
		
		// Pagination
		$has_pagging = $settings['al_content_pagging_display'] === 'yes' ? true : false;
		
		// Formate le titre avec son tag
		$title_tag = $settings['al_title_tag'];
		$open_title = '<'. $title_tag .' class="al-title__content">';
		$close_title = '</'. $title_tag .'>';
		
		// Ajoute l'ID de l'article au titre
		$has_id = $settings['al_article_id'] === 'yes' ? true : false; 
		
		// Formate les arguments et exécute la requête WP_Query, instance principale de WP_Query
		$post_args = Eac_Helpers_Util::get_post_args($settings);
        $the_query = new \WP_Query($post_args);
		
		// La liste des meta_query
		$meta_query_list = Eac_Helpers_Util::get_meta_query_list($post_args);
		$has_keys = !empty($meta_query_list) ? true : false;
		
		// Wrapper de la liste des posts et du bouton de pagination avec l'ID du widget Elementor
		$unique_id = $this->get_id();
		$id = "al_posts_wrapper_" . $unique_id;
		$pagination_id = "al_pagination_" . $unique_id;
		
		// La div wrapper
		$class = vsprintf("al-posts__wrapper %s layout-type-%s", $this->init_settings());
		$this->add_render_attribute('posts_wrapper', 'class', $class);
		$this->add_render_attribute('posts_wrapper', 'id', $id);
		$this->add_render_attribute('posts_wrapper', 'data-settings', $this->get_settings_json($unique_id, $id, $pagination_id, $the_query->max_num_pages));
		
		// Wrapper du contenu. Image et texte + texte à droite/gauche si sélectionné
		$layoutTexte = '';
		if($settings['al_layout_texte'] === 'yes') {
		    $layoutTexte = ' text-align-right';
	    } else if($settings['al_layout_texte_left'] === 'yes') {
	        $layoutTexte = ' text-align-left';
	    }
		$this->add_render_attribute('content_wrapper', 'class', 'al-content__wrapper' . $layoutTexte);
		
		// Bouton 'Load more'
		$button_text = '<button class="al-more-button">' . __("Plus d'articles", 'eac-components') . ' ' . '<span class="al-more-button-paged">' . $the_query->query_vars['paged'] . '</span>' . '/' . $the_query->max_num_pages . '</button>';
		
		/** @since 1.7.2 Affiche les arguments de la requête */
		if($settings['al_display_content_args'] === 'yes' && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
		?>
			<div class="al-posts_query-args">
				<?php highlight_string("<?php\nQuery Args =\n" . var_export(Eac_Helpers_Util::get_posts_query_args(), true) . ";\n?>"); ?>
			</div>
		<?php
		}
		
		ob_start();
		if($the_query->have_posts()) {
			// Création et affichage des filtres
			if($has_filters) {
				// Champ user renseigné et pas de clé. Affiche les auteurs formatés
				if($has_users && !$has_keys) { echo Eac_Helpers_Util::get_user_filters($user_filters); }
				
				// Filtre sélectionné et champ métadonnée renseigné. Affiche les metadonnées formatées
				else if($has_keys) { echo Eac_Helpers_Util::get_meta_query_filters($post_args); }
				
				// Filtre sélectionné et champs catégories et étiquettes renseignés. Affiche les catégories/étiquettes formatées
				else if(!empty($taxonomy_filters)) { echo Eac_Helpers_Util::get_taxo_tag_filters($taxonomy_filters, $term_slug_filters); }
			}
			?>
			<div <?php echo $this->get_render_attribute_string('posts_wrapper'); ?>>
				<div class="al-posts__wrapper-sizer"></div>
				<?php
				// Le loop
				while($the_query->have_posts()) {
					$the_query->the_post();
					
					$termsSlug = array(); // Tableau de slug concaténé avec la class de l'article
					$termsName = array(); // Tableau du nom des slugs Concaténé pour les étiquettes
					
					// @since 1.6.0 Champ user renseigné
					if($has_users && !$has_keys) {
                        $user = get_the_author_meta($field = 'display_name');
                        $termsSlug[$user] = sanitize_title($user);
                        $termsName[$user] = ucfirst($user);
					
					/**
					 * @since 1.6.0 Champ meta keys renseigné
					 * @since 1.7.0	Traitement des meta values
					 * @since 1.7.2 'get_post_meta' param $single = true
					 * @since 1.7.3 Méthode 'get_post_custom_values'
					 */
					} else if($has_keys) {
						$array_post_meta_values = array();
						
						foreach($meta_query_list as $meta_query) {														// Boucle sur chaque métadonnée
							$termTmp = array();
							$array_post_meta_values = get_post_custom_values($meta_query['key'], get_the_id());			// Récupère les meta_value
							
							if(!is_wp_error($array_post_meta_values) && !empty($array_post_meta_values)) {				// Il y a au moins une valeur et pas d'erreur
								$termTmp = Eac_Helpers_Util::compare_meta_values($array_post_meta_values, $meta_query);	// Compare meta_value (post ID) et les slugs meta_query
								if(!empty($termTmp)) {
									foreach($termTmp as $idx => $tmp) {
										$termsSlug = array_replace($termsSlug, [$idx => sanitize_title($tmp)]);
										$termsName = array_replace($termsName, [$idx => ucfirst($tmp)]);
									}
								}
							}
						}
					
					// Champ taxonomie renseigné
					} else if(!empty($taxonomy_filters)) {
						$termsArray = array();
						foreach($taxonomy_filters as $postterm) {
							$termsArray = wp_get_post_terms(get_the_id(), $postterm);			// Récupère toutes les catégories/étiquettes pour l'article courant
							if(!is_wp_error($termsArray) && !empty($termsArray)) {				// Il y a au moins une étiquette et pas d'erreur
								foreach($termsArray as $term) {									// Boucle sur chaque étiquette
									if(!empty($term_slug_filters)) {							// Champ étiquettes renseigné
										if(in_array($term->slug, $term_slug_filters)) {
											$termsSlug[$term->slug] = $term->slug;				// Concatène les slugs des terms avec la class de l'article
											$termsName[$term->name] = ucfirst($term->name);		// Concatène le nom du slug pour les étiquettes
										}
									} else {
										$termsSlug[$term->slug] = $term->slug;
										$termsName[$term->name] = ucfirst($term->name);
									}
								}
							}
						}
					}
					
					/**
					 * @since 1.7.0 Ajout de l'ID Elementor du widget et de la liste des slugs dans la class pour gérer les filtres et le pagging. Voir eac-elements.js:selectedItems
					 * Surtout ne pas utiliser la fonction 'post_class'
					 */
				?>	
					<article id="<?php echo 'post-' . get_the_id(); ?>" class="<?php echo $unique_id; ?> al-post__wrapper <?php echo implode(' ', $termsSlug); ?>">
						<div class="al-post__wrapper-inner">
						    <?php if($has_titre) : ?>
        						<!-- Le titre -->
        						<?php if($has_titre) : ?>
        							<!-- Affiche les IDs -->
        							<?php if($has_id) : ?>
        								<?php echo $open_title; ?><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php echo get_the_id() . ' : ' . esc_html(get_the_title()); ?></a><?php echo $close_title; ?>
        							<?php else : ?>
        								<?php echo $open_title; ?><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php echo esc_html(get_the_title()); ?></a><?php echo $close_title; ?>
        							<?php endif; ?>
        						<?php endif; ?>
    						<?php endif; ?>
    						
							<div <?php echo $this->get_render_attribute_string('content_wrapper'); ?>>
								<!-- L'image -->
								<?php if($has_image && has_post_thumbnail()) : ?>
									<div class="al-image__wrapper">
										<div class="al-image__loaded">
											<!-- @since 1.4.1 Ajout du paramètre 'ver' à l'image avec un identifiant unique pour forcer le chargement de l'image du serveur et non du cache -->
											<?php $image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_id()), $settings['al_image_dimension'], false); ?>
											
											<!-- La lightbox sur l'image -->
											<?php if($has_lb) : ?>
											<a href="<?php echo get_the_post_thumbnail_url(); ?>" data-elementor-open-lightbox="no" data-fancybox="al-gallery-<?php echo $unique_id; ?>" data-caption="<?php echo esc_html(get_the_title()); ?>">
											<?php endif; ?>
											
											<!-- @since 1.8.0 Le lien du post est sur l'image -->
											<?php if($has_image_link) : ?>
											<a href="<?php the_permalink(); ?>">
											<?php endif; ?>
											
											<img class="eac-image-loaded al-image-loaded" src="<?php echo esc_url($image[0]); ?>?ver=<?php echo uniqid(); ?>" alt="<?php echo esc_html(get_the_title()); ?>" />
											
											<?php if($has_lb || $has_image_link) : ?>
											</a>
											<?php endif; ?>
										</div>
									</div>
								<?php endif; ?>
								
								<div class="al-text__content-wrapper">
									<!-- Le résumé de l'article. fonction dans helper.php -->
									<?php if($has_resum) : ?>
										<span class="al-excerpt__content">
											<p><?php echo Eac_Tools_Util::get_post_excerpt(get_the_ID(), $settings['al_excerpt_length']); ?></p>
										</span>
									<?php endif; ?>
									
									<!-- @since 1.8.0 Le lien pour ouvrir l'article/page -->
									<?php if($has_readmore) : ?>
										<span  class="al-link__content">
											<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php _e("En savoir plus", 'eac-components'); ?></a>
										</span>
									<?php endif; ?>
								</div>
							</div>
							
							<div class="al-meta__content-wrapper">
								<!-- @since 1.6.0 Avatar -->
								<?php if($has_avatar) : ?>
									<?php $avatar = get_avatar_url(get_the_author_meta('ID'), ['size' => $avatar_size]); ?>
									<div class="al-avatar__wrapper"><img class="eac-image-loaded avatar photo" src="<?php echo $avatar; ?>" alt="Avatar photo"/></div>
								<?php endif; ?>
										
								<div class="al-post__meta">
									<!-- Les étiquettes -->
									<?php if($has_term) : ?>
										<span class="al-tags__content">
											<i class="fa fa-tags" aria-hidden="true"></i><?php echo implode('|', $termsName); ?>
										</span>
									<?php endif; ?>
										
									<!-- L'auteur de l'article -->
									<?php if($has_auteur) : ?>
										<span class="al-author__content">
											<i class="fa fa-user" aria-hidden="true"></i><?php echo the_author_meta('display_name'); ?>
										</span>
									<?php endif; ?>
											
									<!-- Le date de création ou de dernière modification -->
									<?php if($has_date) : ?>
										<span class="al-date__content">
											<?php if($settings['al_article_orderby'] === 'modified') : ?>
												<i class="fa fa-calendar" aria-hidden="true"></i><?php echo get_the_modified_date(get_option('date_format')); ?>
											<?php else : ?>
												<i class="fa fa-calendar" aria-hidden="true"></i><?php echo get_the_date(get_option('date_format')); ?>
											<?php endif; ?>
										</span>
									<?php endif; ?>
										
									<!-- Le nombre de commentaire -->
									<?php if($has_comment) : ?>
										<span class="al-comment__content">
											<i class="fa fa-comments" aria-hidden="true"></i><?php echo get_comments_number(); ?>
										</span>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</article>
				<?php
				}
				?>
			</div>
			<?php if($has_pagging && $the_query->post_count < $the_query->found_posts) : ?>
				<div class="al-pagination" id="<?php echo $pagination_id; ?>">
					<div class="al-pagination-next"><a href="#"><?php echo $button_text; ?></a></div>
					<div class="al-page-load-status">
						<div class="infinite-scroll-request eac__loader-spin"></div>
						<p class="infinite-scroll-last"><?php _e("Plus d'article", 'eac-components'); ?></p>
						<p class="infinite-scroll-error"><?php _e("Aucun article à charger", 'eac-components'); ?></p>
					</div>
				</div>
			<?php endif;
			
			wp_reset_postdata();
		}
		$output = ob_get_contents();
        ob_end_clean();
        echo $output;
	}
	
	/**
	 * init_settings
	 * 
	 * Description: Détermine le type d'affichage et les dimensions responsive des trois modes
	 * Desktop, Tablet et Mobile
	 * 
	 * @since 0.0.9
	 * @since 1.8.7 Application des breakpoints et suppression des dimensions responsive des trois modes
	 */
	protected function init_settings() {
		$module_settings = $this->get_settings_for_display();
		
		$layout = $module_settings['al_layout_type'];
		$ratio = $module_settings['al_enable_image_ratio'] === 'yes' ? ' al-posts__image-ratio' : '';
		
		return array($ratio, $layout);
	}
	
	/*
	* get_settings_json
	*
	* Retrieve fields values to pass at the widget container
    * Convert on JSON format
    * Read by 'eac-components.js' file when the component is loaded on the frontend
	* Modification de la règles 'data_filtre'
	*
	* @uses      json_encode()
	*
	* @return    JSON oject
	*
	* @access    protected
	* @since     0.0.9
	* @updated   1.7.0	Ajout de l'unique ID
	*/
	protected function get_settings_json($unique_id, $dataid, $pagingid, $dmp) {
		$module_settings = $this->get_settings_for_display();
		
		$settings = array(
			"data_id" => $dataid,
			"data_pagination_id" => $pagingid,
			"data_layout" => $module_settings['al_layout_type'],
			"data_article" => $unique_id,
			"data_filtre" => $module_settings['al_content_filter_display'] === 'yes' ? true : false,
			"data_max_pages" => $dmp,
		);
		
		$settings = json_encode($settings);
		return $settings;
	}
	
	protected function content_template() {}
	
}