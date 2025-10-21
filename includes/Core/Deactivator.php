<?php
/**
 * Gestion de la désactivation du plugin
 *
 * @package SimpleCustomPostType
 * @author Akrem Belkahla <contact@infinityweb.tn>
 */

namespace SimpleCustomPostType\Core;

/**
 * Classe de désactivation
 */
class Deactivator {
    /**
     * Actions à effectuer lors de la désactivation
     *
     * @return void
     */
    public static function deactivate() {
        // Vérifier les capacités
        if (!current_user_can('activate_plugins')) {
            return;
        }

        // Flush les règles de réécriture
        flush_rewrite_rules();

        // Nettoyer les transients
        self::clear_transients();

        // Logger la désactivation
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Simple Custom Post Type: Plugin désactivé');
        }
    }

    /**
     * Nettoyer les transients du plugin
     *
     * @return void
     */
    private static function clear_transients() {
        global $wpdb;

        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
            WHERE option_name LIKE '_transient_scpt_%' 
            OR option_name LIKE '_transient_timeout_scpt_%'"
        );

        // Nettoyer le cache objet
        wp_cache_flush();
    }
}
