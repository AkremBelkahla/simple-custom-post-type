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
                'confirm_delete' => __('Êtes-vous sûr de vouloir supprimer ce post type ?', 'simple-custom-post-type'),
                'error_generic' => __('Une erreur est survenue', 'simple-custom-post-type'),
                'success_saved' => __('Enregistré avec succès', 'simple-custom-post-type'),
            ],
        ]);
    }
}
