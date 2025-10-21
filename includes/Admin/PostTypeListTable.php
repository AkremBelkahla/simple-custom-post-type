<?php
/**
 * Table de liste des Custom Post Types
 *
 * @package SimpleCustomPostType
 * @author Akrem Belkahla <contact@infinityweb.tn>
 */

namespace SimpleCustomPostType\Admin;

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Classe PostTypeListTable - Affichage de la liste des CPT
 */
class PostTypeListTable extends \WP_List_Table {
    
    /**
     * Constructeur
     */
    public function __construct() {
        parent::__construct([
            'singular' => __('Type de publication', 'simple-custom-post-type'),
            'plural'   => __('Types de publication', 'simple-custom-post-type'),
            'ajax'     => false
        ]);
    }

    /**
     * Obtenir les colonnes
     *
     * @return array
     */
    public function get_columns() {
        return [
            'cb'          => '<input type="checkbox" />',
            'name'        => __('Nom', 'simple-custom-post-type'),
            'slug'        => __('Clé', 'simple-custom-post-type'),
            'description' => __('Description', 'simple-custom-post-type'),
            'status'      => __('Statut', 'simple-custom-post-type'),
            'date'        => __('Date', 'simple-custom-post-type'),
        ];
    }

    /**
     * Obtenir les colonnes triables
     *
     * @return array
     */
    public function get_sortable_columns() {
        return [
            'name'   => ['name', false],
            'slug'   => ['slug', false],
            'status' => ['status', false],
            'date'   => ['date', true], // true = tri par défaut
        ];
    }

    /**
     * Colonne par défaut
     *
     * @param array  $item        Élément
     * @param string $column_name Nom de la colonne
     * @return string
     */
    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'description':
                return !empty($item['description']) ? esc_html($item['description']) : '—';
            case 'status':
                return $item['is_active'] 
                    ? '<span class="scpt-status scpt-status-active">' . __('Actif', 'simple-custom-post-type') . '</span>'
                    : '<span class="scpt-status scpt-status-inactive">' . __('Inactif', 'simple-custom-post-type') . '</span>';
            case 'date':
                return date_i18n(get_option('date_format'), strtotime($item['created_at']));
            default:
                return isset($item[$column_name]) ? esc_html($item[$column_name]) : '';
        }
    }

    /**
     * Colonne checkbox
     *
     * @param array $item Élément
     * @return string
     */
    public function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="post_type[]" value="%s" />',
            esc_attr($item['slug'])
        );
    }

    /**
     * Colonne nom (avec actions)
     *
     * @param array $item Élément
     * @return string
     */
    public function column_name($item) {
        $edit_url = add_query_arg([
            'page'   => 'simple-cpt-edit',
            'slug'   => $item['slug'],
        ], admin_url('admin.php'));

        $delete_url = wp_nonce_url(
            add_query_arg([
                'page'   => 'simple-cpt',
                'action' => 'delete',
                'slug'   => $item['slug'],
            ], admin_url('admin.php')),
            'delete_post_type_' . $item['slug']
        );

        $actions = [
            'edit' => sprintf(
                '<a href="%s">%s</a>',
                esc_url($edit_url),
                __('Modifier', 'simple-custom-post-type')
            ),
            'delete' => sprintf(
                '<a href="%s" class="submitdelete" onclick="return confirm(\'%s\');">%s</a>',
                esc_url($delete_url),
                esc_js(__('Êtes-vous sûr de vouloir supprimer ce type de publication ?', 'simple-custom-post-type')),
                __('Supprimer', 'simple-custom-post-type')
            ),
        ];

        if ($item['is_active']) {
            $actions['deactivate'] = sprintf(
                '<a href="%s">%s</a>',
                wp_nonce_url(
                    add_query_arg([
                        'page'   => 'simple-cpt',
                        'action' => 'deactivate',
                        'slug'   => $item['slug'],
                    ], admin_url('admin.php')),
                    'deactivate_post_type_' . $item['slug']
                ),
                __('Désactiver', 'simple-custom-post-type')
            );
        } else {
            $actions['activate'] = sprintf(
                '<a href="%s">%s</a>',
                wp_nonce_url(
                    add_query_arg([
                        'page'   => 'simple-cpt',
                        'action' => 'activate',
                        'slug'   => $item['slug'],
                    ], admin_url('admin.php')),
                    'activate_post_type_' . $item['slug']
                ),
                __('Activer', 'simple-custom-post-type')
            );
        }

        $title = !empty($item['labels']['name']) ? $item['labels']['name'] : $item['slug'];

        return sprintf(
            '<strong><a href="%s" class="row-title">%s</a></strong>%s',
            esc_url($edit_url),
            esc_html($title),
            $this->row_actions($actions)
        );
    }

    /**
     * Colonne slug
     *
     * @param array $item Élément
     * @return string
     */
    public function column_slug($item) {
        return sprintf(
            '<code>%s</code>',
            esc_html($item['slug'])
        );
    }

    /**
     * Actions groupées
     *
     * @return array
     */
    public function get_bulk_actions() {
        return [
            'delete'     => __('Supprimer', 'simple-custom-post-type'),
            'activate'   => __('Activer', 'simple-custom-post-type'),
            'deactivate' => __('Désactiver', 'simple-custom-post-type'),
        ];
    }

    /**
     * Préparer les éléments
     *
     * @return void
     */
    public function prepare_items() {
        global $wpdb;

        // Colonnes
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = [$columns, $hidden, $sortable];

        // Traiter les actions groupées
        $this->process_bulk_action();

        // Pagination
        $per_page = $this->get_items_per_page('scpt_per_page', 20);
        $current_page = $this->get_pagenum();

        // Tri
        $orderby = !empty($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'created_at';
        $order = !empty($_GET['order']) ? sanitize_text_field($_GET['order']) : 'DESC';

        // Recherche
        $search = !empty($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';

        // Récupérer les données
        $table_name = $wpdb->prefix . 'scpt_post_types';
        
        $where = '';
        if (!empty($search)) {
            $where = $wpdb->prepare(
                " WHERE slug LIKE %s OR config LIKE %s",
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%'
            );
        }

        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM {$table_name}{$where}");

        $offset = ($current_page - 1) * $per_page;
        
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name}{$where} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d",
                $per_page,
                $offset
            ),
            ARRAY_A
        );

        // Décoder la config JSON
        $items = [];
        foreach ($results as $result) {
            $config = json_decode($result['config'], true);
            $items[] = array_merge($result, $config);
        }

        $this->items = $items;

        // Pagination
        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ]);
    }

    /**
     * Traiter les actions groupées
     *
     * @return void
     */
    public function process_bulk_action() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'scpt_post_types';

        // Action simple
        if ('delete' === $this->current_action()) {
            $slug = isset($_GET['slug']) ? sanitize_text_field($_GET['slug']) : '';
            if ($slug && check_admin_referer('delete_post_type_' . $slug)) {
                $wpdb->delete($table_name, ['slug' => $slug], ['%s']);
                wp_redirect(admin_url('admin.php?page=simple-cpt&deleted=1'));
                exit;
            }
        }

        if ('activate' === $this->current_action()) {
            $slug = isset($_GET['slug']) ? sanitize_text_field($_GET['slug']) : '';
            if ($slug && check_admin_referer('activate_post_type_' . $slug)) {
                $wpdb->update($table_name, ['is_active' => 1], ['slug' => $slug], ['%d'], ['%s']);
                wp_redirect(admin_url('admin.php?page=simple-cpt&activated=1'));
                exit;
            }
        }

        if ('deactivate' === $this->current_action()) {
            $slug = isset($_GET['slug']) ? sanitize_text_field($_GET['slug']) : '';
            if ($slug && check_admin_referer('deactivate_post_type_' . $slug)) {
                $wpdb->update($table_name, ['is_active' => 0], ['slug' => $slug], ['%d'], ['%s']);
                wp_redirect(admin_url('admin.php?page=simple-cpt&deactivated=1'));
                exit;
            }
        }

        // Actions groupées
        if (isset($_POST['post_type']) && is_array($_POST['post_type'])) {
            $slugs = array_map('sanitize_text_field', $_POST['post_type']);
            
            if ('delete' === $this->current_action()) {
                foreach ($slugs as $slug) {
                    $wpdb->delete($table_name, ['slug' => $slug], ['%s']);
                }
                wp_redirect(admin_url('admin.php?page=simple-cpt&deleted=' . count($slugs)));
                exit;
            }

            if ('activate' === $this->current_action()) {
                foreach ($slugs as $slug) {
                    $wpdb->update($table_name, ['is_active' => 1], ['slug' => $slug], ['%d'], ['%s']);
                }
                wp_redirect(admin_url('admin.php?page=simple-cpt&activated=' . count($slugs)));
                exit;
            }

            if ('deactivate' === $this->current_action()) {
                foreach ($slugs as $slug) {
                    $wpdb->update($table_name, ['is_active' => 0], ['slug' => $slug], ['%d'], ['%s']);
                }
                wp_redirect(admin_url('admin.php?page=simple-cpt&deactivated=' . count($slugs)));
                exit;
            }
        }
    }

    /**
     * Message quand aucun élément
     *
     * @return void
     */
    public function no_items() {
        _e('Aucun type de publication trouvé.', 'simple-custom-post-type');
    }
}
