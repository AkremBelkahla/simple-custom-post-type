<?php
/**
 * Désinstallation du plugin
 *
 * @package SimpleCustomPostType
 * @author Akrem Belkahla <akrem.belkahla@infinityweb.tn>
 */

// Sécurité : Vérifier que la désinstallation est légitime
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Nettoyage des données du plugin
 */
function scpt_uninstall_cleanup() {
    global $wpdb;

    // Supprimer les options du plugin
    $options = [
        'scpt_version',
        'scpt_settings',
        'scpt_custom_post_types',
        'scpt_db_version',
        'scpt_activation_date',
    ];

    foreach ($options as $option) {
        delete_option($option);
        delete_site_option($option); // Pour multisite
    }

    // Supprimer les tables personnalisées si elles existent
    $table_name = $wpdb->prefix . 'scpt_post_types';
    $wpdb->query("DROP TABLE IF EXISTS {$table_name}");

    $table_name = $wpdb->prefix . 'scpt_fields';
    $wpdb->query("DROP TABLE IF EXISTS {$table_name}");

    $table_name = $wpdb->prefix . 'scpt_logs';
    $wpdb->query("DROP TABLE IF EXISTS {$table_name}");

    // Supprimer les transients
    $wpdb->query(
        "DELETE FROM {$wpdb->options} 
        WHERE option_name LIKE '_transient_scpt_%' 
        OR option_name LIKE '_transient_timeout_scpt_%'"
    );

    // Supprimer les métadonnées orphelines
    $wpdb->query(
        "DELETE FROM {$wpdb->postmeta} 
        WHERE meta_key LIKE '_scpt_%'"
    );

    // Nettoyer le cache
    wp_cache_flush();
}

// Exécuter le nettoyage uniquement si l'option est activée
$settings = get_option('scpt_settings', []);
if (isset($settings['delete_data_on_uninstall']) && $settings['delete_data_on_uninstall']) {
    scpt_uninstall_cleanup();
}
