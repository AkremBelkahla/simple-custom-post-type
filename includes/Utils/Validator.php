<?php
/**
 * Validation des données
 *
 * @package SimpleCustomPostType
 * @author Akrem Belkahla <contact@infinityweb.tn>
 */

namespace SimpleCustomPostType\Utils;

/**
 * Classe Validator
 */
class Validator {
    /**
     * Valider les données d'un post type
     *
     * @param array $data Données à valider
     * @return array|\WP_Error
     */
    public function validate_post_type_data($data) {
        $errors = new \WP_Error();

        // Valider le slug
        if (empty($data['slug'])) {
            $errors->add('missing_slug', __('Le slug est requis', 'simple-custom-post-type'));
        } else {
            $slug = sanitize_key($data['slug']);
            
            // Vérifier la longueur (max 20 caractères)
            if (strlen($slug) > 20) {
                $errors->add('slug_too_long', __('Le slug ne peut pas dépasser 20 caractères', 'simple-custom-post-type'));
            }

            // Vérifier les slugs réservés
            $reserved_slugs = $this->get_reserved_slugs();
            if (in_array($slug, $reserved_slugs, true)) {
                $errors->add('reserved_slug', __('Ce slug est réservé par WordPress', 'simple-custom-post-type'));
            }

            $data['slug'] = $slug;
        }

        // Valider les labels
        if (empty($data['labels']['name'])) {
            $errors->add('missing_name', __('Le nom est requis', 'simple-custom-post-type'));
        } else {
            $data['labels']['name'] = sanitize_text_field($data['labels']['name']);
        }

        if (empty($data['labels']['singular_name'])) {
            $errors->add('missing_singular_name', __('Le nom singulier est requis', 'simple-custom-post-type'));
        } else {
            $data['labels']['singular_name'] = sanitize_text_field($data['labels']['singular_name']);
        }

        // Valider les options booléennes
        $boolean_fields = [
            'public',
            'publicly_queryable',
            'show_ui',
            'show_in_menu',
            'show_in_nav_menus',
            'show_in_admin_bar',
            'show_in_rest',
            'hierarchical',
            'has_archive',
            'can_export',
            'delete_with_user',
        ];

        foreach ($boolean_fields as $field) {
            if (isset($data[$field])) {
                $data[$field] = (bool) $data[$field];
            }
        }

        // Valider menu_position
        if (isset($data['menu_position'])) {
            $data['menu_position'] = absint($data['menu_position']);
        }

        // Valider menu_icon
        if (!empty($data['menu_icon'])) {
            $data['menu_icon'] = sanitize_text_field($data['menu_icon']);
        }

        // Valider capability_type
        if (!empty($data['capability_type'])) {
            $data['capability_type'] = sanitize_key($data['capability_type']);
        }

        // Valider supports
        if (!empty($data['supports']) && is_array($data['supports'])) {
            $data['supports'] = array_map('sanitize_key', $data['supports']);
        }

        // Valider taxonomies
        if (!empty($data['taxonomies']) && is_array($data['taxonomies'])) {
            $data['taxonomies'] = array_map('sanitize_key', $data['taxonomies']);
        }

        // Valider rewrite
        if (!empty($data['rewrite'])) {
            if (is_array($data['rewrite'])) {
                if (isset($data['rewrite']['slug'])) {
                    $data['rewrite']['slug'] = sanitize_title($data['rewrite']['slug']);
                }
                if (isset($data['rewrite']['with_front'])) {
                    $data['rewrite']['with_front'] = (bool) $data['rewrite']['with_front'];
                }
            } else {
                $data['rewrite'] = (bool) $data['rewrite'];
            }
        }

        // Retourner les erreurs si présentes
        if ($errors->has_errors()) {
            return $errors;
        }

        return $data;
    }

    /**
     * Valider les données d'un champ
     *
     * @param array $data Données à valider
     * @return array|\WP_Error
     */
    public function validate_field_data($data) {
        $errors = new \WP_Error();

        // Valider field_key
        if (empty($data['field_key'])) {
            $errors->add('missing_field_key', __('La clé du champ est requise', 'simple-custom-post-type'));
        } else {
            $data['field_key'] = sanitize_key($data['field_key']);
        }

        // Valider field_type
        $allowed_types = $this->get_allowed_field_types();
        if (empty($data['field_type']) || !in_array($data['field_type'], $allowed_types, true)) {
            $errors->add('invalid_field_type', __('Type de champ invalide', 'simple-custom-post-type'));
        }

        // Valider label
        if (empty($data['label'])) {
            $errors->add('missing_label', __('Le label est requis', 'simple-custom-post-type'));
        } else {
            $data['label'] = sanitize_text_field($data['label']);
        }

        // Valider description
        if (!empty($data['description'])) {
            $data['description'] = sanitize_textarea_field($data['description']);
        }

        // Valider required
        if (isset($data['required'])) {
            $data['required'] = (bool) $data['required'];
        }

        // Valider default_value
        if (isset($data['default_value'])) {
            $data['default_value'] = $this->sanitize_field_value($data['default_value'], $data['field_type']);
        }

        // Retourner les erreurs si présentes
        if ($errors->has_errors()) {
            return $errors;
        }

        return $data;
    }

    /**
     * Récupérer les slugs réservés par WordPress
     *
     * @return array
     */
    private function get_reserved_slugs() {
        return [
            'post', 'page', 'attachment', 'revision', 'nav_menu_item',
            'custom_css', 'customize_changeset', 'oembed_cache',
            'user_request', 'wp_block', 'wp_template', 'wp_template_part',
            'wp_global_styles', 'wp_navigation', 'action', 'author',
            'order', 'theme',
        ];
    }

    /**
     * Récupérer les types de champs autorisés
     *
     * @return array
     */
    private function get_allowed_field_types() {
        return [
            'text', 'textarea', 'number', 'email', 'url', 'tel',
            'date', 'time', 'datetime', 'color', 'checkbox',
            'radio', 'select', 'multiselect', 'image', 'file',
            'gallery', 'wysiwyg', 'code', 'password', 'range',
            'repeater', 'group', 'relationship', 'taxonomy',
            'user', 'google_map', 'date_picker', 'time_picker',
            'color_picker', 'true_false', 'link', 'post_object',
        ];
    }

    /**
     * Sanitizer une valeur selon son type
     *
     * @param mixed $value Valeur à sanitizer
     * @param string $type Type de champ
     * @return mixed
     */
    private function sanitize_field_value($value, $type) {
        switch ($type) {
            case 'text':
            case 'tel':
                return sanitize_text_field($value);
            
            case 'textarea':
            case 'wysiwyg':
                return sanitize_textarea_field($value);
            
            case 'email':
                return sanitize_email($value);
            
            case 'url':
                return esc_url_raw($value);
            
            case 'number':
            case 'range':
                return is_numeric($value) ? floatval($value) : 0;
            
            case 'checkbox':
            case 'true_false':
                return (bool) $value;
            
            case 'color':
            case 'color_picker':
                return sanitize_hex_color($value);
            
            case 'date':
            case 'time':
            case 'datetime':
            case 'date_picker':
            case 'time_picker':
                return sanitize_text_field($value);
            
            case 'select':
            case 'radio':
                return sanitize_key($value);
            
            case 'multiselect':
                return is_array($value) ? array_map('sanitize_key', $value) : [];
            
            case 'image':
            case 'file':
                return absint($value);
            
            case 'gallery':
                return is_array($value) ? array_map('absint', $value) : [];
            
            case 'code':
                return wp_kses_post($value);
            
            default:
                return sanitize_text_field($value);
        }
    }

    /**
     * Valider un nonce
     *
     * @param string $nonce Nonce à valider
     * @param string $action Action du nonce
     * @return bool
     */
    public function verify_nonce($nonce, $action = 'scpt_nonce') {
        return wp_verify_nonce($nonce, $action) !== false;
    }

    /**
     * Vérifier les permissions utilisateur
     *
     * @param string $capability Capacité requise
     * @return bool
     */
    public function check_permission($capability = 'manage_options') {
        return current_user_can($capability);
    }
}
