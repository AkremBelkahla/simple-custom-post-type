<?php
/**
 * Système de logging
 *
 * @package SimpleCustomPostType
 * @author Akrem Belkahla <contact@infinityweb.tn>
 */

namespace SimpleCustomPostType\Utils;

/**
 * Classe Logger
 */
class Logger {
    /**
     * Niveaux de log
     */
    const LEVEL_DEBUG = 'debug';
    const LEVEL_INFO = 'info';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR = 'error';
    const LEVEL_CRITICAL = 'critical';

    /**
     * Vérifier si les logs sont activés
     *
     * @return bool
     */
    private function is_enabled() {
        $settings = get_option('scpt_settings', []);
        return !empty($settings['enable_logs']);
    }

    /**
     * Logger un message
     *
     * @param string $level Niveau de log
     * @param string $message Message
     * @param array $context Contexte additionnel
     * @return bool
     */
    private function log($level, $message, $context = []) {
        if (!$this->is_enabled()) {
            return false;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'scpt_logs';

        $user_id = get_current_user_id();
        $ip_address = $this->get_client_ip();

        $result = $wpdb->insert(
            $table,
            [
                'level' => $level,
                'message' => $message,
                'context' => !empty($context) ? wp_json_encode($context) : null,
                'user_id' => $user_id ?: null,
                'ip_address' => $ip_address,
                'created_at' => current_time('mysql'),
            ],
            ['%s', '%s', '%s', '%d', '%s', '%s']
        );

        // Logger aussi dans le error_log si WP_DEBUG est activé
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log(sprintf('[SCPT][%s] %s', strtoupper($level), $message));
        }

        return $result !== false;
    }

    /**
     * Log debug
     *
     * @param string $message Message
     * @param array $context Contexte
     * @return bool
     */
    public function debug($message, $context = []) {
        return $this->log(self::LEVEL_DEBUG, $message, $context);
    }

    /**
     * Log info
     *
     * @param string $message Message
     * @param array $context Contexte
     * @return bool
     */
    public function info($message, $context = []) {
        return $this->log(self::LEVEL_INFO, $message, $context);
    }

    /**
     * Log warning
     *
     * @param string $message Message
     * @param array $context Contexte
     * @return bool
     */
    public function warning($message, $context = []) {
        return $this->log(self::LEVEL_WARNING, $message, $context);
    }

    /**
     * Log error
     *
     * @param string $message Message
     * @param array $context Contexte
     * @return bool
     */
    public function error($message, $context = []) {
        return $this->log(self::LEVEL_ERROR, $message, $context);
    }

    /**
     * Log critical
     *
     * @param string $message Message
     * @param array $context Contexte
     * @return bool
     */
    public function critical($message, $context = []) {
        return $this->log(self::LEVEL_CRITICAL, $message, $context);
    }

    /**
     * Récupérer les logs
     *
     * @param array $args Arguments de filtrage
     * @return array
     */
    public function get_logs($args = []) {
        global $wpdb;
        $table = $wpdb->prefix . 'scpt_logs';

        $defaults = [
            'level' => null,
            'limit' => 100,
            'offset' => 0,
            'order' => 'DESC',
        ];

        $args = wp_parse_args($args, $defaults);

        $where = '1=1';
        $prepare_args = [];

        if ($args['level']) {
            $where .= ' AND level = %s';
            $prepare_args[] = $args['level'];
        }

        $prepare_args[] = (int) $args['limit'];
        $prepare_args[] = (int) $args['offset'];

        $query = "SELECT * FROM $table WHERE $where ORDER BY created_at {$args['order']} LIMIT %d OFFSET %d";

        if (!empty($prepare_args)) {
            $query = $wpdb->prepare($query, $prepare_args);
        }

        return $wpdb->get_results($query, ARRAY_A);
    }

    /**
     * Nettoyer les anciens logs
     *
     * @return int Nombre de logs supprimés
     */
    public function cleanup_old_logs() {
        $settings = get_option('scpt_settings', []);
        $retention_days = $settings['log_retention_days'] ?? 30;

        global $wpdb;
        $table = $wpdb->prefix . 'scpt_logs';

        $date = date('Y-m-d H:i:s', strtotime("-{$retention_days} days"));

        $deleted = $wpdb->query(
            $wpdb->prepare("DELETE FROM $table WHERE created_at < %s", $date)
        );

        return $deleted;
    }

    /**
     * Récupérer l'adresse IP du client
     *
     * @return string
     */
    private function get_client_ip() {
        $ip_keys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER)) {
                $ip = explode(',', $_SERVER[$key]);
                $ip = trim($ip[0]);

                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return '0.0.0.0';
    }
}
