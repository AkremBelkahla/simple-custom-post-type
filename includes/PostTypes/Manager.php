<?php
/**
 * Gestionnaire des Custom Post Types
 *
 * @package SimpleCustomPostType
 * @author Akrem Belkahla <contact@infinityweb.tn>
 */

namespace SimpleCustomPostType\PostTypes;

use SimpleCustomPostType\Utils\Logger;
use SimpleCustomPostType\Utils\Cache;

/**
 * Classe Manager - Gestion des CPT
 */
class Manager {
    /**
     * Logger
     *
     * @var Logger
     */
    private $logger;

    /**
     * Cache
     *
     * @var Cache
     */
    private $cache;

    /**
     * Constructeur
     */
    public function __construct() {
        $this->logger = new Logger();
        $this->cache = new Cache();
    }

    /**
     * Enregistrer tous les post types actifs
     *
     * @return void
     */
    public function register_all() {
        $post_types = $this->get_all();

        foreach ($post_types as $post_type) {
            if (!empty($post_type['is_active'])) {
                $this->register_single($post_type);
            }
        }
    }

    /**
     * Enregistrer un post type unique
     *
     * @param array $config Configuration du post type
     * @return bool|\WP_Error
     */
    public function register_single($config) {
        try {
            $slug = $config['slug'] ?? '';
            $args = $this->prepare_args($config);

            $result = register_post_type($slug, $args);

            if (is_wp_error($result)) {
                $this->logger->error('Erreur lors de l\'enregistrement du post type : ' . $result->get_error_message());
                return $result;
            }

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Exception lors de l\'enregistrement du post type : ' . $e->getMessage());
            return new \WP_Error('registration_failed', $e->getMessage());
        }
    }

    /**
     * Préparer les arguments pour register_post_type()
     *
     * @param array $config Configuration du post type
     * @return array
     */
    private function prepare_args($config) {
        $labels = [
            'name' => $config['labels']['name'] ?? '',
            'singular_name' => $config['labels']['singular_name'] ?? '',
            'menu_name' => $config['labels']['menu_name'] ?? $config['labels']['name'] ?? '',
            'all_items' => $config['labels']['all_items'] ?? sprintf(__('Tous les %s', 'simple-custom-post-type'), $config['labels']['name'] ?? ''),
            'add_new' => $config['labels']['add_new'] ?? __('Ajouter', 'simple-custom-post-type'),
            'add_new_item' => $config['labels']['add_new_item'] ?? sprintf(__('Ajouter %s', 'simple-custom-post-type'), $config['labels']['singular_name'] ?? ''),
            'edit_item' => $config['labels']['edit_item'] ?? sprintf(__('Modifier %s', 'simple-custom-post-type'), $config['labels']['singular_name'] ?? ''),
            'new_item' => $config['labels']['new_item'] ?? sprintf(__('Nouveau %s', 'simple-custom-post-type'), $config['labels']['singular_name'] ?? ''),
            'view_item' => $config['labels']['view_item'] ?? sprintf(__('Voir %s', 'simple-custom-post-type'), $config['labels']['singular_name'] ?? ''),
            'search_items' => $config['labels']['search_items'] ?? sprintf(__('Rechercher %s', 'simple-custom-post-type'), $config['labels']['name'] ?? ''),
            'not_found' => $config['labels']['not_found'] ?? __('Aucun élément trouvé', 'simple-custom-post-type'),
            'not_found_in_trash' => $config['labels']['not_found_in_trash'] ?? __('Aucun élément dans la corbeille', 'simple-custom-post-type'),
        ];

        $args = [
            'labels' => $labels,
            'public' => $config['public'] ?? true,
            'publicly_queryable' => $config['publicly_queryable'] ?? true,
            'show_ui' => $config['show_ui'] ?? true,
            'show_in_menu' => $config['show_in_menu'] ?? true,
            'show_in_nav_menus' => $config['show_in_nav_menus'] ?? true,
            'show_in_admin_bar' => $config['show_in_admin_bar'] ?? true,
            'show_in_rest' => $config['show_in_rest'] ?? true,
            'rest_base' => $config['rest_base'] ?? $config['slug'],
            'menu_position' => $config['menu_position'] ?? 5,
            'menu_icon' => $config['menu_icon'] ?? 'dashicons-admin-post',
            'capability_type' => $config['capability_type'] ?? 'post',
            'hierarchical' => $config['hierarchical'] ?? false,
            'supports' => $config['supports'] ?? ['title', 'editor', 'thumbnail'],
            'has_archive' => $config['has_archive'] ?? true,
            'rewrite' => $config['rewrite'] ?? ['slug' => $config['slug'], 'with_front' => false],
            'query_var' => $config['query_var'] ?? true,
            'can_export' => $config['can_export'] ?? true,
            'delete_with_user' => $config['delete_with_user'] ?? false,
        ];

        // Taxonomies
        if (!empty($config['taxonomies'])) {
            $args['taxonomies'] = $config['taxonomies'];
        }

        return apply_filters('scpt_post_type_args', $args, $config);
    }

    /**
     * Récupérer tous les post types
     *
     * @return array
     */
    public function get_all() {
        // Essayer de récupérer depuis le cache
        $cached = $this->cache->get('all_post_types');
        if ($cached !== false) {
            return $cached;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'scpt_post_types';

        $results = $wpdb->get_results(
            "SELECT * FROM $table ORDER BY created_at DESC",
            ARRAY_A
        );

        $post_types = [];
        foreach ($results as $row) {
            $config = json_decode($row['config'], true);
            $config['id'] = $row['id'];
            $config['slug'] = $row['slug'];
            $config['is_active'] = (bool) $row['is_active'];
            $config['created_at'] = $row['created_at'];
            $config['updated_at'] = $row['updated_at'];
            $post_types[] = $config;
        }

        // Mettre en cache
        $this->cache->set('all_post_types', $post_types, 3600);

        return $post_types;
    }

    /**
     * Récupérer un post type par son slug
     *
     * @param string $slug Slug du post type
     * @return array|null
     */
    public function get($slug) {
        global $wpdb;
        $table = $wpdb->prefix . 'scpt_post_types';

        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table WHERE slug = %s", $slug),
            ARRAY_A
        );

        if (!$row) {
            return null;
        }

        $config = json_decode($row['config'], true);
        $config['id'] = $row['id'];
        $config['slug'] = $row['slug'];
        $config['is_active'] = (bool) $row['is_active'];
        $config['created_at'] = $row['created_at'];
        $config['updated_at'] = $row['updated_at'];

        return $config;
    }

    /**
     * Sauvegarder un post type
     *
     * @param array $data Données du post type
     * @return bool|int
     */
    public function save($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'scpt_post_types';

        $slug = $data['slug'];
        unset($data['slug']);
        unset($data['id']);

        $config = wp_json_encode($data);

        // Vérifier si le post type existe déjà
        $exists = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM $table WHERE slug = %s", $slug)
        );

        if ($exists) {
            // Mise à jour
            $result = $wpdb->update(
                $table,
                ['config' => $config, 'updated_at' => current_time('mysql')],
                ['slug' => $slug],
                ['%s', '%s'],
                ['%s']
            );
        } else {
            // Insertion
            $result = $wpdb->insert(
                $table,
                [
                    'slug' => $slug,
                    'config' => $config,
                    'is_active' => 1,
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql'),
                ],
                ['%s', '%s', '%d', '%s', '%s']
            );
        }

        // Invalider le cache
        $this->cache->delete('all_post_types');

        // Flush les règles de réécriture
        flush_rewrite_rules();

        return $result !== false;
    }

    /**
     * Supprimer un post type
     *
     * @param string $slug Slug du post type
     * @return bool
     */
    public function delete($slug) {
        global $wpdb;
        $table = $wpdb->prefix . 'scpt_post_types';

        $result = $wpdb->delete($table, ['slug' => $slug], ['%s']);

        if ($result) {
            // Supprimer aussi les champs associés
            $fields_table = $wpdb->prefix . 'scpt_fields';
            $wpdb->delete($fields_table, ['post_type_slug' => $slug], ['%s']);

            // Invalider le cache
            $this->cache->delete('all_post_types');

            // Flush les règles de réécriture
            flush_rewrite_rules();
        }

        return $result !== false;
    }

    /**
     * Activer/désactiver un post type
     *
     * @param string $slug Slug du post type
     * @param bool $active État actif
     * @return bool
     */
    public function toggle_active($slug, $active) {
        global $wpdb;
        $table = $wpdb->prefix . 'scpt_post_types';

        $result = $wpdb->update(
            $table,
            ['is_active' => $active ? 1 : 0],
            ['slug' => $slug],
            ['%d'],
            ['%s']
        );

        if ($result !== false) {
            $this->cache->delete('all_post_types');
            flush_rewrite_rules();
        }

        return $result !== false;
    }
}
