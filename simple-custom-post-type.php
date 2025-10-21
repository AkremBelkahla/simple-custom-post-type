<?php
/**
 * Plugin Name: Simple Custom Post Type
 * Plugin URI: https://infinityweb.tn
 * Description: Plugin moderne et intuitif pour créer et gérer des Custom Post Types avec tous les champs possibles
 * Version: 1.0.0
 * Author: Akrem Belkahla
 * Author URI: https://infinityweb.tn
 * Text Domain: simple-custom-post-type
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package SimpleCustomPostType
 * @author Akrem Belkahla <contact@infinityweb.tn>
 * @copyright 2025 InfinityWeb
 */

// Sécurité : Empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Constantes du plugin
 */
define('SCPT_VERSION', '1.0.0');
define('SCPT_PLUGIN_FILE', __FILE__);
define('SCPT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SCPT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SCPT_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('SCPT_MIN_PHP_VERSION', '7.4');
define('SCPT_MIN_WP_VERSION', '6.0');

/**
 * Vérification des prérequis système
 *
 * @return bool
 */
function scpt_check_requirements() {
    $errors = [];

    // Vérifier la version PHP
    if (version_compare(PHP_VERSION, SCPT_MIN_PHP_VERSION, '<')) {
        $errors[] = sprintf(
            __('Simple Custom Post Type nécessite PHP %s ou supérieur. Vous utilisez actuellement PHP %s.', 'simple-custom-post-type'),
            SCPT_MIN_PHP_VERSION,
            PHP_VERSION
        );
    }

    // Vérifier la version WordPress
    global $wp_version;
    if (version_compare($wp_version, SCPT_MIN_WP_VERSION, '<')) {
        $errors[] = sprintf(
            __('Simple Custom Post Type nécessite WordPress %s ou supérieur. Vous utilisez actuellement WordPress %s.', 'simple-custom-post-type'),
            SCPT_MIN_WP_VERSION,
            $wp_version
        );
    }

    // Afficher les erreurs si nécessaire
    if (!empty($errors)) {
        add_action('admin_notices', function() use ($errors) {
            foreach ($errors as $error) {
                echo '<div class="notice notice-error"><p>' . esc_html($error) . '</p></div>';
            }
        });
        return false;
    }

    return true;
}

/**
 * Autoloader PSR-4
 *
 * @param string $class Nom de la classe à charger
 */
function scpt_autoloader($class) {
    $prefix = 'SimpleCustomPostType\\';
    $base_dir = SCPT_PLUGIN_DIR . 'includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
}

spl_autoload_register('scpt_autoloader');

/**
 * Initialisation du plugin
 */
function scpt_init() {
    // Vérifier les prérequis
    if (!scpt_check_requirements()) {
        return;
    }

    // Charger les traductions
    load_plugin_textdomain(
        'simple-custom-post-type',
        false,
        dirname(SCPT_PLUGIN_BASENAME) . '/languages'
    );

    // Initialiser le plugin principal
    try {
        $plugin = SimpleCustomPostType\Core\Plugin::get_instance();
        $plugin->init();
    } catch (Exception $e) {
        add_action('admin_notices', function() use ($e) {
            echo '<div class="notice notice-error"><p>';
            echo esc_html(sprintf(
                __('Erreur lors de l\'initialisation de Simple Custom Post Type : %s', 'simple-custom-post-type'),
                $e->getMessage()
            ));
            echo '</p></div>';
        });
        
        // Logger l'erreur
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Simple Custom Post Type Error: ' . $e->getMessage());
        }
    }
}

add_action('plugins_loaded', 'scpt_init');

/**
 * Activation du plugin
 */
function scpt_activate() {
    // Vérifier les prérequis avant l'activation
    if (!scpt_check_requirements()) {
        deactivate_plugins(SCPT_PLUGIN_BASENAME);
        wp_die(
            __('Simple Custom Post Type ne peut pas être activé car les prérequis système ne sont pas satisfaits.', 'simple-custom-post-type'),
            __('Erreur d\'activation', 'simple-custom-post-type'),
            ['back_link' => true]
        );
    }

    require_once SCPT_PLUGIN_DIR . 'includes/Core/Activator.php';
    SimpleCustomPostType\Core\Activator::activate();
}

register_activation_hook(__FILE__, 'scpt_activate');

/**
 * Désactivation du plugin
 */
function scpt_deactivate() {
    require_once SCPT_PLUGIN_DIR . 'includes/Core/Deactivator.php';
    SimpleCustomPostType\Core\Deactivator::deactivate();
}

register_deactivation_hook(__FILE__, 'scpt_deactivate');

/**
 * Désinstallation du plugin
 * Voir uninstall.php pour la logique de désinstallation
 */
