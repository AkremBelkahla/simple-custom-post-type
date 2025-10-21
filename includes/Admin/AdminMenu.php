<?php
/**
 * Gestion du menu d'administration
 *
 * @package SimpleCustomPostType
 * @author Akrem Belkahla <contact@infinityweb.tn>
 */

namespace SimpleCustomPostType\Admin;

/**
 * Classe AdminMenu
 */
class AdminMenu {
    /**
     * Constructeur
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'register_menu']);
    }

    /**
     * Enregistrer le menu d'administration
     *
     * @return void
     */
    public function register_menu() {
        // Menu principal
        add_menu_page(
            __('Simple CPT', 'simple-custom-post-type'),
            __('Simple CPT', 'simple-custom-post-type'),
            'manage_options',
            'simple-cpt',
            [$this, 'render_main_page'],
            'dashicons-editor-table',
            30
        );

        // Sous-menu: Liste des post types
        add_submenu_page(
            'simple-cpt',
            __('Post Types', 'simple-custom-post-type'),
            __('Post Types', 'simple-custom-post-type'),
            'manage_options',
            'simple-cpt',
            [$this, 'render_main_page']
        );

        // Sous-menu: Ajouter un post type
        add_submenu_page(
            'simple-cpt',
            __('Ajouter un Post Type', 'simple-custom-post-type'),
            __('Ajouter', 'simple-custom-post-type'),
            'manage_options',
            'simple-cpt-add',
            [$this, 'render_add_page']
        );

        // Sous-menu: Paramètres
        add_submenu_page(
            'simple-cpt',
            __('Paramètres', 'simple-custom-post-type'),
            __('Paramètres', 'simple-custom-post-type'),
            'manage_options',
            'simple-cpt-settings',
            [$this, 'render_settings_page']
        );

        // Sous-menu: Logs
        add_submenu_page(
            'simple-cpt',
            __('Logs', 'simple-custom-post-type'),
            __('Logs', 'simple-custom-post-type'),
            'manage_options',
            'simple-cpt-logs',
            [$this, 'render_logs_page']
        );
    }

    /**
     * Afficher la page principale
     *
     * @return void
     */
    public function render_main_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <div id="scpt-app-root"></div>
        </div>
        <?php
    }

    /**
     * Afficher la page d'ajout
     *
     * @return void
     */
    public function render_add_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <div id="scpt-add-root"></div>
        </div>
        <?php
    }

    /**
     * Afficher la page des paramètres
     *
     * @return void
     */
    public function render_settings_page() {
        // Sauvegarder les paramètres si le formulaire est soumis
        if (isset($_POST['scpt_settings_nonce']) && wp_verify_nonce($_POST['scpt_settings_nonce'], 'scpt_save_settings')) {
            $this->save_settings();
        }

        $settings = get_option('scpt_settings', []);
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <form method="post" action="">
                <?php wp_nonce_field('scpt_save_settings', 'scpt_settings_nonce'); ?>
                
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="enable_logs"><?php _e('Activer les logs', 'simple-custom-post-type'); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" id="enable_logs" name="enable_logs" value="1" <?php checked(!empty($settings['enable_logs'])); ?>>
                                <p class="description"><?php _e('Enregistrer les actions dans la base de données', 'simple-custom-post-type'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="log_retention_days"><?php _e('Rétention des logs (jours)', 'simple-custom-post-type'); ?></label>
                            </th>
                            <td>
                                <input type="number" id="log_retention_days" name="log_retention_days" value="<?php echo esc_attr($settings['log_retention_days'] ?? 30); ?>" min="1" max="365" class="small-text">
                                <p class="description"><?php _e('Nombre de jours de conservation des logs', 'simple-custom-post-type'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="enable_rest_api"><?php _e('Activer REST API', 'simple-custom-post-type'); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" id="enable_rest_api" name="enable_rest_api" value="1" <?php checked(!empty($settings['enable_rest_api'])); ?>>
                                <p class="description"><?php _e('Exposer les post types via l\'API REST', 'simple-custom-post-type'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="enable_gutenberg"><?php _e('Activer Gutenberg', 'simple-custom-post-type'); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" id="enable_gutenberg" name="enable_gutenberg" value="1" <?php checked(!empty($settings['enable_gutenberg'])); ?>>
                                <p class="description"><?php _e('Utiliser l\'éditeur Gutenberg par défaut', 'simple-custom-post-type'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="delete_data_on_uninstall"><?php _e('Supprimer les données', 'simple-custom-post-type'); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" id="delete_data_on_uninstall" name="delete_data_on_uninstall" value="1" <?php checked(!empty($settings['delete_data_on_uninstall'])); ?>>
                                <p class="description"><?php _e('Supprimer toutes les données lors de la désinstallation', 'simple-custom-post-type'); ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <?php submit_button(__('Enregistrer les paramètres', 'simple-custom-post-type')); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Afficher la page des logs
     *
     * @return void
     */
    public function render_logs_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <div id="scpt-logs-root"></div>
        </div>
        <?php
    }

    /**
     * Sauvegarder les paramètres
     *
     * @return void
     */
    private function save_settings() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $settings = [
            'enable_logs' => !empty($_POST['enable_logs']),
            'log_retention_days' => absint($_POST['log_retention_days'] ?? 30),
            'enable_rest_api' => !empty($_POST['enable_rest_api']),
            'enable_gutenberg' => !empty($_POST['enable_gutenberg']),
            'delete_data_on_uninstall' => !empty($_POST['delete_data_on_uninstall']),
        ];

        update_option('scpt_settings', $settings);

        add_settings_error(
            'scpt_settings',
            'settings_updated',
            __('Paramètres enregistrés avec succès', 'simple-custom-post-type'),
            'success'
        );
    }
}
