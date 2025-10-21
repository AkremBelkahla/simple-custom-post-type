<?php
/**
 * Gestion de l'activation du plugin
 *
 * @package SimpleCustomPostType
 * @author Akrem Belkahla <contact@infinityweb.tn>
 */

namespace SimpleCustomPostType\Core;

/**
 * Classe d'activation
 */
class Activator {
    /**
     * Actions à effectuer lors de l'activation
     *
     * @return void
     */
    public static function activate() {
        global $wpdb;

        // Vérifier les capacités
        if (!current_user_can('activate_plugins')) {
            return;
        }

        // Version de la base de données
        $db_version = '1.0.0';
        $installed_version = get_option('scpt_db_version', '0');

        // Créer les tables si nécessaire
        if (version_compare($installed_version, $db_version, '<')) {
            self::create_tables();
            update_option('scpt_db_version', $db_version);
        }

        // Créer les options par défaut
        self::create_default_options();

        // Créer les capacités
        self::add_capabilities();

        // Enregistrer la date d'activation
        if (!get_option('scpt_activation_date')) {
            add_option('scpt_activation_date', current_time('mysql'));
        }

        // Flush les règles de réécriture
        flush_rewrite_rules();

        // Logger l'activation
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Simple Custom Post Type: Plugin activé');
        }
    }

    /**
     * Créer les tables de la base de données
     *
     * @return void
     */
    private static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();

        // Table des post types
        $table_post_types = $wpdb->prefix . 'scpt_post_types';
        $sql_post_types = "CREATE TABLE IF NOT EXISTS $table_post_types (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            slug varchar(20) NOT NULL,
            config longtext NOT NULL,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY slug (slug),
            KEY is_active (is_active)
        ) $charset_collate;";

        // Table des champs
        $table_fields = $wpdb->prefix . 'scpt_fields';
        $sql_fields = "CREATE TABLE IF NOT EXISTS $table_fields (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_type_slug varchar(20) NOT NULL,
            field_key varchar(100) NOT NULL,
            field_config longtext NOT NULL,
            field_order int(11) DEFAULT 0,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY field_key (post_type_slug, field_key),
            KEY post_type_slug (post_type_slug),
            KEY is_active (is_active)
        ) $charset_collate;";

        // Table des logs
        $table_logs = $wpdb->prefix . 'scpt_logs';
        $sql_logs = "CREATE TABLE IF NOT EXISTS $table_logs (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            level varchar(20) NOT NULL,
            message text NOT NULL,
            context longtext,
            user_id bigint(20) UNSIGNED,
            ip_address varchar(45),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY level (level),
            KEY user_id (user_id),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_post_types);
        dbDelta($sql_fields);
        dbDelta($sql_logs);
    }

    /**
     * Créer les options par défaut
     *
     * @return void
     */
    private static function create_default_options() {
        $default_settings = [
            'enable_logs' => true,
            'log_retention_days' => 30,
            'delete_data_on_uninstall' => false,
            'enable_rest_api' => true,
            'enable_gutenberg' => true,
            'ui_theme' => 'light',
        ];

        if (!get_option('scpt_settings')) {
            add_option('scpt_settings', $default_settings);
        }

        // Initialiser le tableau des post types
        if (!get_option('scpt_custom_post_types')) {
            add_option('scpt_custom_post_types', []);
        }
    }

    /**
     * Ajouter les capacités aux rôles
     *
     * @return void
     */
    private static function add_capabilities() {
        $role = get_role('administrator');
        
        if ($role) {
            $capabilities = [
                'manage_custom_post_types',
                'edit_custom_post_types',
                'delete_custom_post_types',
            ];

            foreach ($capabilities as $cap) {
                $role->add_cap($cap);
            }
        }
    }
}
