<?php
/**
 * Gestion du cache
 *
 * @package SimpleCustomPostType
 * @author Akrem Belkahla <contact@infinityweb.tn>
 */

namespace SimpleCustomPostType\Utils;

/**
 * Classe Cache
 */
class Cache {
    /**
     * Préfixe des clés de cache
     *
     * @var string
     */
    private $prefix = 'scpt_';

    /**
     * Récupérer une valeur du cache
     *
     * @param string $key Clé du cache
     * @return mixed|false
     */
    public function get($key) {
        return wp_cache_get($this->prefix . $key, 'scpt');
    }

    /**
     * Définir une valeur dans le cache
     *
     * @param string $key Clé du cache
     * @param mixed $value Valeur à cacher
     * @param int $expiration Durée en secondes
     * @return bool
     */
    public function set($key, $value, $expiration = 3600) {
        return wp_cache_set($this->prefix . $key, $value, 'scpt', $expiration);
    }

    /**
     * Supprimer une valeur du cache
     *
     * @param string $key Clé du cache
     * @return bool
     */
    public function delete($key) {
        return wp_cache_delete($this->prefix . $key, 'scpt');
    }

    /**
     * Vider tout le cache du plugin
     *
     * @return bool
     */
    public function flush() {
        return wp_cache_flush();
    }

    /**
     * Récupérer ou définir une valeur (remember pattern)
     *
     * @param string $key Clé du cache
     * @param callable $callback Fonction à exécuter si le cache n'existe pas
     * @param int $expiration Durée en secondes
     * @return mixed
     */
    public function remember($key, $callback, $expiration = 3600) {
        $value = $this->get($key);

        if ($value !== false) {
            return $value;
        }

        $value = call_user_func($callback);
        $this->set($key, $value, $expiration);

        return $value;
    }
}
