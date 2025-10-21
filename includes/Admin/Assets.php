<?php
/**
 * Gestion des assets (CSS/JS)
 *
 * @package SimpleCustomPostType
 * @author Akrem Belkahla <contact@infinityweb.tn>
 */

namespace SimpleCustomPostType\Admin;

/**
 * Classe Assets
 */
class Assets {
    /**
     * Constructeur
     */
    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    /**
     * Charger les scripts et styles
     *
     * @param string $hook Page actuelle
     * @return void
     */
    public function enqueue_scripts($hook) {
        // Charger uniquement sur nos pages
        if (strpos($hook, 'simple-cpt') === false) {
            return;
        }

        self::enqueue_admin_styles();
        self::enqueue_admin_scripts();
    }

    /**
     * Charger les styles admin
     *
     * @return void
     */
    public static function enqueue_admin_styles() {
        wp_enqueue_style(
            'scpt-admin',
            SCPT_PLUGIN_URL . 'assets/css/admin.css',
            [],
            SCPT_VERSION
        );
    }

    /**
     * Charger les scripts admin
     *
     * @return void
     */
    public static function enqueue_admin_scripts() {
        wp_enqueue_script(
            'scpt-admin',
            SCPT_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery', 'wp-api'],
            SCPT_VERSION,
            true
        );

        // Localiser le script
        wp_localize_script('scpt-admin', 'scptData', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('scpt_nonce'),
            'restUrl' => rest_url('scpt/v1/'),
            'restNonce' => wp_create_nonce('wp_rest'),
            'i18n' => [
                // Messages génériques
                'confirm_delete' => __('Êtes-vous sûr de vouloir supprimer ce post type ?', 'simple-custom-post-type'),
                'error_generic' => __('Une erreur est survenue', 'simple-custom-post-type'),
                'success_saved' => __('Enregistré avec succès', 'simple-custom-post-type'),
                
                // Mode Simple - Labels
                'label_plural' => __('Libellé au pluriel', 'simple-custom-post-type'),
                'label_singular' => __('Libellé au singulier', 'simple-custom-post-type'),
                'label_slug' => __('Clé du type de publication', 'simple-custom-post-type'),
                'label_taxonomies' => __('Taxonomies', 'simple-custom-post-type'),
                'label_public' => __('Public', 'simple-custom-post-type'),
                'label_hierarchical' => __('Hiérarchique', 'simple-custom-post-type'),
                'label_advanced' => __('Configuration avancée', 'simple-custom-post-type'),
                
                // Mode Simple - Placeholders
                'placeholder_plural' => __('Films', 'simple-custom-post-type'),
                'placeholder_singular' => __('Film', 'simple-custom-post-type'),
                'placeholder_slug' => __('film', 'simple-custom-post-type'),
                
                // Mode Simple - Aide
                'help_slug' => __('Lettres minuscules, tiret bas et tiret uniquement, maximum 20 caractères.', 'simple-custom-post-type'),
                'help_taxonomies' => __('Sélectionnez les taxonomies existantes pour classer les éléments du type de publication.', 'simple-custom-post-type'),
                'help_public' => __('Visible sur l\'interface publique et dans le tableau de bord de l\'administration.', 'simple-custom-post-type'),
                'help_hierarchical' => __('Les types de publication hiérarchiques peuvent avoir des descendants (comme les pages).', 'simple-custom-post-type'),
                'help_advanced' => __('Je sais ce que je fais, affiche-moi toutes les options.', 'simple-custom-post-type'),
                
                // Taxonomies
                'taxonomy_categories' => __('Catégories', 'simple-custom-post-type'),
                'taxonomy_tags' => __('Étiquettes', 'simple-custom-post-type'),
                
                // Boutons
                'btn_create' => __('Créer le type de publication', 'simple-custom-post-type'),
                'btn_cancel' => __('Annuler', 'simple-custom-post-type'),
                
                // Onglets
                'tab_general' => __('Général', 'simple-custom-post-type'),
                'tab_post_type' => __('Type de Publication', 'simple-custom-post-type'),
                'tab_labels' => __('Libellés', 'simple-custom-post-type'),
                'tab_options' => __('Options', 'simple-custom-post-type'),
                'tab_visibility' => __('Visibilité', 'simple-custom-post-type'),
                'tab_permalinks' => __('Permaliens', 'simple-custom-post-type'),
                'tab_capabilities' => __('Capacités', 'simple-custom-post-type'),
                'tab_rest_api' => __('API REST', 'simple-custom-post-type'),
                
                // Onglet Général
                'label_function_name' => __('Nom de la fonction', 'simple-custom-post-type'),
                'placeholder_function_name' => __('custom_post_type', 'simple-custom-post-type'),
                'help_function_name' => __('La fonction utilisée dans le code.', 'simple-custom-post-type'),
                'label_text_domain' => __('Domaine de texte', 'simple-custom-post-type'),
                'placeholder_text_domain' => __('text_domain', 'simple-custom-post-type'),
                'help_text_domain' => __('Domaine de texte du fichier de traduction. Optionnel.', 'simple-custom-post-type'),
                
                // Onglet Post Type
                'label_post_type_key' => __('Clé du type de publication', 'simple-custom-post-type'),
                'placeholder_post_type_key' => __('post_type', 'simple-custom-post-type'),
                'help_post_type_key' => __('Clé utilisée dans le code. Jusqu\'à 20 caractères, minuscules, sans espaces.', 'simple-custom-post-type'),
                'label_name_singular' => __('Nom (Singulier)', 'simple-custom-post-type'),
                'placeholder_name_singular' => __('Type de Publication', 'simple-custom-post-type'),
                'help_name_singular' => __('Nom singulier du type de publication. Ex: Produit, Événement ou Film.', 'simple-custom-post-type'),
                'label_description' => __('Description', 'simple-custom-post-type'),
                'placeholder_description' => __('Description du type de publication', 'simple-custom-post-type'),
                'help_description' => __('Un court résumé descriptif du type de publication.', 'simple-custom-post-type'),
                'label_name_plural' => __('Nom (Pluriel)', 'simple-custom-post-type'),
                'placeholder_name_plural' => __('Types de Publication', 'simple-custom-post-type'),
                'help_name_plural' => __('Nom pluriel du type de publication. Ex: Produits, Événements ou Films.', 'simple-custom-post-type'),
                
                // Onglet Labels
                'label_menu_name' => __('Nom du menu', 'simple-custom-post-type'),
                'label_add_new' => __('Ajouter', 'simple-custom-post-type'),
                'label_add_new_item' => __('Ajouter un élément', 'simple-custom-post-type'),
                'label_edit_item' => __('Modifier l\'élément', 'simple-custom-post-type'),
                'label_new_item' => __('Nouvel élément', 'simple-custom-post-type'),
                'label_view_item' => __('Voir l\'élément', 'simple-custom-post-type'),
                'label_view_items' => __('Voir les éléments', 'simple-custom-post-type'),
                'label_search_items' => __('Rechercher des éléments', 'simple-custom-post-type'),
                
                // Onglet Options
                'label_supports' => __('Prend en charge', 'simple-custom-post-type'),
                'support_title' => __('Titre', 'simple-custom-post-type'),
                'support_editor' => __('Contenu (éditeur)', 'simple-custom-post-type'),
                'support_excerpt' => __('Extrait', 'simple-custom-post-type'),
                'support_author' => __('Auteur', 'simple-custom-post-type'),
                'support_thumbnail' => __('Image mise en avant', 'simple-custom-post-type'),
                'support_comments' => __('Commentaires', 'simple-custom-post-type'),
                'support_trackbacks' => __('Rétroliens', 'simple-custom-post-type'),
                'support_revisions' => __('Révisions', 'simple-custom-post-type'),
                'support_custom_fields' => __('Champs personnalisés', 'simple-custom-post-type'),
                'support_page_attributes' => __('Attributs de page', 'simple-custom-post-type'),
                'support_post_formats' => __('Formats de publication', 'simple-custom-post-type'),
                
                // Onglet Visibility
                'label_public' => __('Public', 'simple-custom-post-type'),
                'help_public' => __('Visible sur l\'interface publique et dans le tableau de bord de l\'administration.', 'simple-custom-post-type'),
                'label_show_ui' => __('Afficher l\'interface', 'simple-custom-post-type'),
                'help_show_ui' => __('Afficher l\'interface d\'administration pour ce type de publication.', 'simple-custom-post-type'),
                'label_show_in_menu' => __('Afficher dans le menu', 'simple-custom-post-type'),
                'help_show_in_menu' => __('Afficher dans le menu d\'administration.', 'simple-custom-post-type'),
                'label_show_in_nav_menus' => __('Afficher dans les menus de navigation', 'simple-custom-post-type'),
                'help_show_in_nav_menus' => __('Rendre disponible pour la sélection dans les menus de navigation.', 'simple-custom-post-type'),
                
                // Onglet Permalinks
                'label_has_archive' => __('Possède une archive', 'simple-custom-post-type'),
                'help_has_archive' => __('Active les archives pour ce type de publication.', 'simple-custom-post-type'),
                'label_rewrite' => __('Réécriture', 'simple-custom-post-type'),
                'help_rewrite' => __('Personnaliser la structure des permaliens.', 'simple-custom-post-type'),
                
                // Onglet Capabilities
                'label_capability_type' => __('Type de capacité', 'simple-custom-post-type'),
                'help_capability_type' => __('Le type de capacité à utiliser lors de la construction des capacités de lecture, d\'édition et de suppression.', 'simple-custom-post-type'),
                
                // Onglet REST API
                'label_show_in_rest' => __('Afficher dans REST API', 'simple-custom-post-type'),
                'help_show_in_rest' => __('Exposer ce type de publication dans l\'API REST WordPress.', 'simple-custom-post-type'),
                'label_rest_base' => __('Base REST', 'simple-custom-post-type'),
                'help_rest_base' => __('Personnaliser la base de l\'URL REST pour ce type de publication.', 'simple-custom-post-type'),
            ],
        ]);
    }
}
