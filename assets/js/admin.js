/**
 * Scripts d'administration
 *
 * @package SimpleCustomPostType
 * @author Akrem Belkahla <contact@infinityweb.tn>
 */

(function($) {
    'use strict';

    const SCPT = {
        /**
         * Initialisation
         */
        init: function() {
            this.bindEvents();
            this.loadPostTypes();
        },

        /**
         * Lier les événements
         */
        bindEvents: function() {
            $(document).on('click', '.scpt-delete-btn', this.deletePostType);
            $(document).on('click', '.scpt-toggle-active', this.toggleActive);
            $(document).on('submit', '#scpt-form', this.savePostType);
        },

        /**
         * Charger les post types
         */
        loadPostTypes: function() {
            const $container = $('#scpt-app-root');
            
            if (!$container.length) return;

            $container.html('<div class="scpt-loading"><div class="scpt-spinner"></div></div>');

            $.ajax({
                url: scptData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'scpt_get_post_types',
                    nonce: scptData.nonce
                },
                success: function(response) {
                    if (response.success) {
                        SCPT.renderPostTypes(response.data.post_types);
                    } else {
                        SCPT.showError(response.data.message);
                    }
                },
                error: function() {
                    SCPT.showError(scptData.i18n.error_generic);
                }
            });
        },

        /**
         * Afficher les post types
         */
        renderPostTypes: function(postTypes) {
            const $container = $('#scpt-app-root');
            
            if (!postTypes || postTypes.length === 0) {
                $container.html('<div class="scpt-alert scpt-alert-warning">Aucun post type personnalisé trouvé.</div>');
                return;
            }

            let html = '<div class="scpt-card"><table class="scpt-table"><thead><tr>';
            html += '<th>Nom</th>';
            html += '<th>Slug</th>';
            html += '<th>Statut</th>';
            html += '<th>Actions</th>';
            html += '</tr></thead><tbody>';

            postTypes.forEach(function(pt) {
                const statusClass = pt.is_active ? 'success' : 'danger';
                const statusText = pt.is_active ? 'Actif' : 'Inactif';
                
                html += '<tr>';
                html += '<td>' + pt.labels.name + '</td>';
                html += '<td><code>' + pt.slug + '</code></td>';
                html += '<td><span class="scpt-badge scpt-badge-' + statusClass + '">' + statusText + '</span></td>';
                html += '<td>';
                html += '<button class="scpt-btn scpt-btn-secondary scpt-toggle-active" data-slug="' + pt.slug + '">Basculer</button> ';
                html += '<button class="scpt-btn scpt-btn-danger scpt-delete-btn" data-slug="' + pt.slug + '">Supprimer</button>';
                html += '</td>';
                html += '</tr>';
            });

            html += '</tbody></table></div>';
            $container.html(html);
        },

        /**
         * Supprimer un post type
         */
        deletePostType: function(e) {
            e.preventDefault();
            
            if (!confirm(scptData.i18n.confirm_delete)) {
                return;
            }

            const slug = $(this).data('slug');

            $.ajax({
                url: scptData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'scpt_delete_post_type',
                    nonce: scptData.nonce,
                    slug: slug
                },
                success: function(response) {
                    if (response.success) {
                        SCPT.showSuccess(response.data.message);
                        SCPT.loadPostTypes();
                    } else {
                        SCPT.showError(response.data.message);
                    }
                },
                error: function() {
                    SCPT.showError(scptData.i18n.error_generic);
                }
            });
        },

        /**
         * Basculer l'état actif
         */
        toggleActive: function(e) {
            e.preventDefault();
            const slug = $(this).data('slug');
            
            // TODO: Implémenter l'appel AJAX
            console.log('Toggle active:', slug);
        },

        /**
         * Sauvegarder un post type
         */
        savePostType: function(e) {
            e.preventDefault();
            
            const formData = $(this).serialize();

            $.ajax({
                url: scptData.ajaxUrl,
                type: 'POST',
                data: formData + '&action=scpt_save_post_type&nonce=' + scptData.nonce,
                success: function(response) {
                    if (response.success) {
                        SCPT.showSuccess(scptData.i18n.success_saved);
                    } else {
                        SCPT.showError(response.data.message);
                    }
                },
                error: function() {
                    SCPT.showError(scptData.i18n.error_generic);
                }
            });
        },

        /**
         * Afficher un message de succès
         */
        showSuccess: function(message) {
            const html = '<div class="scpt-alert scpt-alert-success">' + message + '</div>';
            $('.wrap h1').after(html);
            setTimeout(function() {
                $('.scpt-alert-success').fadeOut();
            }, 3000);
        },

        /**
         * Afficher un message d'erreur
         */
        showError: function(message) {
            const html = '<div class="scpt-alert scpt-alert-error">' + message + '</div>';
            $('.wrap h1').after(html);
            setTimeout(function() {
                $('.scpt-alert-error').fadeOut();
            }, 5000);
        }
    };

    // Initialiser au chargement du DOM
    $(document).ready(function() {
        SCPT.init();
    });

})(jQuery);
