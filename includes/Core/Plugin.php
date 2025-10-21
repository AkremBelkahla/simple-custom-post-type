<?php
/**
 * Classe principale du plugin
 *
 * @package SimpleCustomPostType
 * @author Akrem Belkahla <contact@infinityweb.tn>
 */

namespace SimpleCustomPostType\Core;

use SimpleCustomPostType\Admin\AdminMenu;
use SimpleCustomPostType\Admin\Assets;
use SimpleCustomPostType\PostTypes\Manager;
use SimpleCustomPostType\Utils\Logger;
use SimpleCustomPostType\Utils\Validator;

/**
 * Classe principale - Singleton
 */
class Plugin {
    /**
     * Instance unique du plugin
     *
     * @var Plugin|null
     */
    private static $instance = null;

    /**
     * Gestionnaire de post types
     *
     * @var Manager
     */
    private $post_type_manager;

    /**
     * Logger
     *
     * @var Logger
     */
    private $logger;

    /**
     * Constructeur privé (Singleton)
     */
    private function __construct() {
        $this->logger = new Logger();
    }

    /**
     * Récupérer l'instance unique
     *
     * @return Plugin
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialiser le plugin
     *
     * @return void
     */
    public function init() {
        try {
            // Initialiser les composants
            $this->init_components();

            // Enregistrer les hooks
            $this->register_hooks();

            // Logger l'initialisation
            $this->logger->info('Plugin initialisé avec succès');
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'initialisation : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Initialiser les composants
     *
     * @return void
     */
    private function init_components() {
        // Gestionnaire de post types
        $this->post_type_manager = new Manager();

        // Interface d'administration
        if (is_admin()) {
            new AdminMenu();
            new Assets();
        }
    }

    /**
     * Enregistrer les hooks WordPress
     *
     * @return void
     */
    private function register_hooks() {
        // Hook d'initialisation
        add_action('init', [$this, 'register_post_types']);

        // Hook pour les scripts admin
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);

        // Hook AJAX
        add_action('wp_ajax_scpt_save_post_type', [$this, 'ajax_save_post_type']);
        add_action('wp_ajax_scpt_delete_post_type', [$this, 'ajax_delete_post_type']);
        add_action('wp_ajax_scpt_get_post_types', [$this, 'ajax_get_post_types']);
    }

    /**
     * Enregistrer les custom post types
     *
     * @return void
     */
    public function register_post_types() {
        try {
            $this->post_type_manager->register_all();
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'enregistrement des post types : ' . $e->getMessage());
        }
    }

    /**
     * Charger les assets admin
     *
     * @param string $hook Page actuelle
     * @return void
     */
    public function enqueue_admin_assets($hook) {
        // Charger uniquement sur nos pages
        if (strpos($hook, 'simple-cpt') === false) {
            return;
        }

        Assets::enqueue_admin_scripts();
        Assets::enqueue_admin_styles();
    }

    /**
     * AJAX: Sauvegarder un post type
     *
     * @return void
     */
    public function ajax_save_post_type() {
        check_ajax_referer('scpt_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission refusée', 'simple-custom-post-type')]);
        }

        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Valider les données
            $validator = new Validator();
            $validated = $validator->validate_post_type_data($data);

            if (is_wp_error($validated)) {
                wp_send_json_error(['message' => $validated->get_error_message()]);
            }

            // Sauvegarder
            $result = $this->post_type_manager->save($validated);

            if ($result) {
                $this->logger->info('Post type sauvegardé : ' . $validated['slug']);
                wp_send_json_success(['message' => __('Post type sauvegardé avec succès', 'simple-custom-post-type')]);
            } else {
                wp_send_json_error(['message' => __('Erreur lors de la sauvegarde', 'simple-custom-post-type')]);
            }
        } catch (\Exception $e) {
            $this->logger->error('Erreur AJAX save_post_type : ' . $e->getMessage());
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    /**
     * AJAX: Supprimer un post type
     *
     * @return void
     */
    public function ajax_delete_post_type() {
        check_ajax_referer('scpt_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission refusée', 'simple-custom-post-type')]);
        }

        try {
            $slug = sanitize_key($_POST['slug'] ?? '');

            if (empty($slug)) {
                wp_send_json_error(['message' => __('Slug invalide', 'simple-custom-post-type')]);
            }

            $result = $this->post_type_manager->delete($slug);

            if ($result) {
                $this->logger->info('Post type supprimé : ' . $slug);
                wp_send_json_success(['message' => __('Post type supprimé avec succès', 'simple-custom-post-type')]);
            } else {
                wp_send_json_error(['message' => __('Erreur lors de la suppression', 'simple-custom-post-type')]);
            }
        } catch (\Exception $e) {
            $this->logger->error('Erreur AJAX delete_post_type : ' . $e->getMessage());
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    /**
     * AJAX: Récupérer les post types
     *
     * @return void
     */
    public function ajax_get_post_types() {
        check_ajax_referer('scpt_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission refusée', 'simple-custom-post-type')]);
        }

        try {
            $post_types = $this->post_type_manager->get_all();
            wp_send_json_success(['post_types' => $post_types]);
        } catch (\Exception $e) {
            $this->logger->error('Erreur AJAX get_post_types : ' . $e->getMessage());
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    /**
     * Récupérer le logger
     *
     * @return Logger
     */
    public function get_logger() {
        return $this->logger;
    }

    /**
     * Empêcher le clonage
     */
    private function __clone() {}

    /**
     * Empêcher la désérialisation
     */
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}
