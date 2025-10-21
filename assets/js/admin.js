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
            this.renderAddForm();
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
            
            const $form = $(this);
            const $submitBtn = $form.find('button[type="submit"]');
            const originalText = $submitBtn.text();
            
            // Désactiver le bouton pendant la soumission
            $submitBtn.prop('disabled', true).text('Enregistrement...');
            
            // Préparer les données du formulaire
            const formData = new FormData($form[0]);
            const data = {
                action: 'scpt_save_post_type',
                nonce: scptData.nonce
            };
            
            // Traiter les supports (checkboxes)
            const supports = [];
            $form.find('input[name^="supports["]:checked').each(function() {
                const name = $(this).attr('name');
                const match = name.match(/supports\[(\w+)\]/);
                if (match) {
                    supports.push(match[1]);
                }
            });
            
            // Construire l'objet data
            for (let [key, value] of formData.entries()) {
                if (!key.startsWith('supports[')) {
                    data[key] = value;
                }
            }
            
            // Ajouter les supports
            if (supports.length > 0) {
                supports.forEach((support, index) => {
                    data['supports[' + index + ']'] = support;
                });
            }

            $.ajax({
                url: scptData.ajaxUrl,
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        SCPT.showSuccess(scptData.i18n.success_saved);
                        // Rediriger vers la liste après 1 seconde
                        setTimeout(function() {
                            window.location.href = 'admin.php?page=simple-cpt';
                        }, 1000);
                    } else {
                        SCPT.showError(response.data.message);
                        $submitBtn.prop('disabled', false).text(originalText);
                    }
                },
                error: function() {
                    SCPT.showError(scptData.i18n.error_generic);
                    $submitBtn.prop('disabled', false).text(originalText);
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
        },

        /**
         * Afficher le formulaire d'ajout
         */
        renderAddForm: function() {
            const $container = $('#scpt-add-root');
            
            if (!$container.length) return;

            const html = `
                <div class="scpt-tabs-wrapper">
                    <nav class="scpt-tabs-nav">
                        <button type="button" class="scpt-tab-btn active" data-tab="general">
                            <span class="dashicons dashicons-admin-generic"></span>
                            ${scptData.i18n.tab_general}
                        </button>
                        <button type="button" class="scpt-tab-btn" data-tab="labels">
                            <span class="dashicons dashicons-tag"></span>
                            ${scptData.i18n.tab_labels}
                        </button>
                        <button type="button" class="scpt-tab-btn" data-tab="options">
                            <span class="dashicons dashicons-admin-settings"></span>
                            ${scptData.i18n.tab_options}
                        </button>
                        <button type="button" class="scpt-tab-btn" data-tab="visibility">
                            <span class="dashicons dashicons-visibility"></span>
                            ${scptData.i18n.tab_visibility}
                        </button>
                        <button type="button" class="scpt-tab-btn" data-tab="permalinks">
                            <span class="dashicons dashicons-admin-links"></span>
                            ${scptData.i18n.tab_permalinks}
                        </button>
                        <button type="button" class="scpt-tab-btn" data-tab="capabilities">
                            <span class="dashicons dashicons-admin-users"></span>
                            ${scptData.i18n.tab_capabilities}
                        </button>
                        <button type="button" class="scpt-tab-btn" data-tab="rest-api">
                            <span class="dashicons dashicons-rest-api"></span>
                            ${scptData.i18n.tab_rest_api}
                        </button>
                    </nav>

                    <div class="scpt-tabs-content">
                        <form id="scpt-form" class="scpt-form">
                            <!-- Tab: General (fusionné avec Post Type) -->
                            <div class="scpt-tab-panel active" data-tab="general">
                                <div class="scpt-form-grid">
                                    <div class="scpt-form-group">
                                        <label for="scpt-slug">${scptData.i18n.label_post_type_key} *</label>
                                        <input type="text" id="scpt-slug" name="slug" required pattern="[a-z0-9_-]+" 
                                               placeholder="${scptData.i18n.placeholder_post_type_key}" class="scpt-input">
                                        <p class="scpt-help-text">${scptData.i18n.help_post_type_key}</p>
                                    </div>
                                    <div class="scpt-form-group">
                                        <label for="scpt-singular-name">${scptData.i18n.label_name_singular} *</label>
                                        <input type="text" id="scpt-singular-name" name="labels[singular_name]" required 
                                               placeholder="${scptData.i18n.placeholder_name_singular}" class="scpt-input">
                                        <p class="scpt-help-text">${scptData.i18n.help_name_singular}</p>
                                    </div>
                                    <div class="scpt-form-group">
                                        <label for="scpt-name">${scptData.i18n.label_name_plural} *</label>
                                        <input type="text" id="scpt-name" name="labels[name]" required 
                                               placeholder="${scptData.i18n.placeholder_name_plural}" class="scpt-input">
                                        <p class="scpt-help-text">${scptData.i18n.help_name_plural}</p>
                                    </div>
                                    <div class="scpt-form-group">
                                        <label for="scpt-description">${scptData.i18n.label_description}</label>
                                        <input type="text" id="scpt-description" name="description" 
                                               placeholder="${scptData.i18n.placeholder_description}" class="scpt-input">
                                        <p class="scpt-help-text">${scptData.i18n.help_description}</p>
                                    </div>
                                    <div class="scpt-form-group">
                                        <label for="scpt-function-name">${scptData.i18n.label_function_name}</label>
                                        <input type="text" id="scpt-function-name" name="function_name" 
                                               placeholder="${scptData.i18n.placeholder_function_name}" class="scpt-input">
                                        <p class="scpt-help-text">${scptData.i18n.help_function_name}</p>
                                    </div>
                                    <div class="scpt-form-group">
                                        <label for="scpt-text-domain">${scptData.i18n.label_text_domain}</label>
                                        <input type="text" id="scpt-text-domain" name="text_domain" 
                                               placeholder="${scptData.i18n.placeholder_text_domain}" class="scpt-input">
                                        <p class="scpt-help-text">${scptData.i18n.help_text_domain}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: Labels -->
                            <div class="scpt-tab-panel" data-tab="labels">
                                <div class="scpt-form-grid scpt-grid-4">
                                    <div class="scpt-form-group">
                                        <label for="scpt-label-menu-name">${scptData.i18n.label_menu_name}</label>
                                        <input type="text" id="scpt-label-menu-name" name="labels[menu_name]" 
                                               placeholder="${scptData.i18n.placeholder_name_plural}" class="scpt-input">
                                    </div>
                                    <div class="scpt-form-group">
                                        <label for="scpt-label-add-new">${scptData.i18n.label_add_new}</label>
                                        <input type="text" id="scpt-label-add-new" name="labels[add_new]" 
                                               placeholder="${scptData.i18n.label_add_new}" class="scpt-input">
                                    </div>
                                    <div class="scpt-form-group">
                                        <label for="scpt-label-add-new-item">${scptData.i18n.label_add_new_item}</label>
                                        <input type="text" id="scpt-label-add-new-item" name="labels[add_new_item]" 
                                               placeholder="${scptData.i18n.label_add_new_item}" class="scpt-input">
                                    </div>
                                    <div class="scpt-form-group">
                                        <label for="scpt-label-edit-item">${scptData.i18n.label_edit_item}</label>
                                        <input type="text" id="scpt-label-edit-item" name="labels[edit_item]" 
                                               placeholder="${scptData.i18n.label_edit_item}" class="scpt-input">
                                    </div>
                                    <div class="scpt-form-group">
                                        <label for="scpt-label-new-item">${scptData.i18n.label_new_item}</label>
                                        <input type="text" id="scpt-label-new-item" name="labels[new_item]" 
                                               placeholder="${scptData.i18n.label_new_item}" class="scpt-input">
                                    </div>
                                    <div class="scpt-form-group">
                                        <label for="scpt-label-view-item">${scptData.i18n.label_view_item}</label>
                                        <input type="text" id="scpt-label-view-item" name="labels[view_item]" 
                                               placeholder="${scptData.i18n.label_view_item}" class="scpt-input">
                                    </div>
                                    <div class="scpt-form-group">
                                        <label for="scpt-label-view-items">${scptData.i18n.label_view_items}</label>
                                        <input type="text" id="scpt-label-view-items" name="labels[view_items]" 
                                               placeholder="${scptData.i18n.label_view_items}" class="scpt-input">
                                    </div>
                                    <div class="scpt-form-group">
                                        <label for="scpt-label-search-items">${scptData.i18n.label_search_items}</label>
                                        <input type="text" id="scpt-label-search-items" name="labels[search_items]" 
                                               placeholder="${scptData.i18n.label_search_items}" class="scpt-input">
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: Options -->
                            <div class="scpt-tab-panel" data-tab="options">
                                <div class="scpt-form-grid">
                                    <div class="scpt-form-group">
                                        <label>${scptData.i18n.label_supports}</label>
                                        <div class="scpt-checkbox-group">
                                            <label><input type="checkbox" name="supports[title]" value="1" checked> ${scptData.i18n.support_title}</label>
                                            <label><input type="checkbox" name="supports[editor]" value="1" checked> ${scptData.i18n.support_editor}</label>
                                            <label><input type="checkbox" name="supports[excerpt]" value="1"> ${scptData.i18n.support_excerpt}</label>
                                            <label><input type="checkbox" name="supports[author]" value="1"> ${scptData.i18n.support_author}</label>
                                            <label><input type="checkbox" name="supports[thumbnail]" value="1"> ${scptData.i18n.support_thumbnail}</label>
                                            <label><input type="checkbox" name="supports[comments]" value="1"> ${scptData.i18n.support_comments}</label>
                                            <label><input type="checkbox" name="supports[trackbacks]" value="1"> ${scptData.i18n.support_trackbacks}</label>
                                            <label><input type="checkbox" name="supports[revisions]" value="1"> ${scptData.i18n.support_revisions}</label>
                                            <label><input type="checkbox" name="supports[custom-fields]" value="1"> ${scptData.i18n.support_custom_fields}</label>
                                            <label><input type="checkbox" name="supports[page-attributes]" value="1"> ${scptData.i18n.support_page_attributes}</label>
                                            <label><input type="checkbox" name="supports[post-formats]" value="1"> ${scptData.i18n.support_post_formats}</label>
                                        </div>
                                    </div>
                                    <div class="scpt-form-group">
                                        <label for="scpt-exclude-search">Exclude From Search</label>
                                        <select id="scpt-exclude-search" name="exclude_from_search" class="scpt-input">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                        <p class="scpt-help-text">Posts of this type should be excluded from search results.</p>
                                    </div>
                                    <div class="scpt-form-group">
                                        <label for="scpt-enable-export">Enable Export</label>
                                        <select id="scpt-enable-export" name="can_export" class="scpt-input">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                        <p class="scpt-help-text">Enables post type export.</p>
                                    </div>
                                    <div class="scpt-form-group">
                                        <label for="scpt-has-archive">Enable Archives</label>
                                        <select id="scpt-has-archive" name="has_archive" class="scpt-input">
                                            <option value="1">Yes (use default slug)</option>
                                            <option value="0">No</option>
                                        </select>
                                        <p class="scpt-help-text">Enables post type archives. Post type key is used as default archive slug.</p>
                                    </div>
                                    <div class="scpt-form-group">
                                        <label for="scpt-custom-archive-slug">Custom Archive Slug</label>
                                        <input type="text" id="scpt-custom-archive-slug" name="archive_slug" 
                                               placeholder="" class="scpt-input">
                                        <p class="scpt-help-text">Set custom archive slug.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: Visibility -->
                            <div class="scpt-tab-panel" data-tab="visibility">
                                <div class="scpt-form-grid">
                                    <div class="scpt-form-group">
                                        <label for="scpt-public">Public</label>
                                        <select id="scpt-public" name="public" class="scpt-input">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                        <p class="scpt-help-text">Show post type in the admin UI.</p>
                                    </div>
                                    <div class="scpt-form-group">
                                        <label for="scpt-show-ui">Show UI</label>
                                        <select id="scpt-show-ui" name="show_ui" class="scpt-input">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                        <p class="scpt-help-text">Show post type UI in the admin.</p>
                                    </div>
                                    <div class="scpt-form-group">
                                        <label for="scpt-show-in-admin-bar">Show in Admin Bar</label>
                                        <select id="scpt-show-in-admin-bar" name="show_in_admin_bar" class="scpt-input">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                        <p class="scpt-help-text">Show post type in admin bar.</p>
                                    </div>
                                    <div class="scpt-form-group">
                                        <label for="scpt-show-in-nav-menus">Show in Navigation Menus</label>
                                        <select id="scpt-show-in-nav-menus" name="show_in_nav_menus" class="scpt-input">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                        <p class="scpt-help-text">Show post type in Navigation Menus.</p>
                                    </div>
                                    <div class="scpt-form-group">
                                        <label for="scpt-menu-icon">Admin Sidebar Icon</label>
                                        <input type="text" id="scpt-menu-icon" name="menu_icon" 
                                               placeholder="i.e. dashicons-admin-post" class="scpt-input">
                                        <p class="scpt-help-text">Post type icon. Use dashicon name or full icon URL (http://.../icon.png).</p>
                                    </div>
                                    <div class="scpt-form-group">
                                        <label for="scpt-menu-position">Show in Admin Sidebar</label>
                                        <select id="scpt-menu-position" name="menu_position" class="scpt-input">
                                            <option value="">5 - below Posts</option>
                                            <option value="10">10 - below Media</option>
                                            <option value="15">15 - below Links</option>
                                            <option value="20">20 - below Pages</option>
                                            <option value="25">25 - below Comments</option>
                                            <option value="60">60 - below first separator</option>
                                            <option value="65">65 - below Plugins</option>
                                            <option value="70">70 - below Users</option>
                                            <option value="75">75 - below Tools</option>
                                            <option value="80">80 - below Settings</option>
                                            <option value="100">100 - below second separator</option>
                                        </select>
                                        <p class="scpt-help-text">Show post type in admin sidebar.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: Permalinks -->
                            <div class="scpt-tab-panel" data-tab="permalinks">
                                <div class="scpt-form-grid">
                                    <div class="scpt-form-group">
                                        <label for="scpt-rewrite">Permalink Rewrite</label>
                                        <select id="scpt-rewrite" name="rewrite" class="scpt-input">
                                            <option value="1">Default permalink (post type key)</option>
                                            <option value="0">No permalinks</option>
                                        </select>
                                        <p class="scpt-help-text">Use Default Permalinks (using post type key), prevent automatic URL rewriting (no pretty permalinks), or set custom permalinks.</p>
                                    </div>
                                    <div class="scpt-form-group">
                                        <label for="scpt-rewrite-slug">URL Slug</label>
                                        <input type="text" id="scpt-rewrite-slug" name="rewrite_slug" 
                                               placeholder="post_type" class="scpt-input">
                                        <p class="scpt-help-text">Pretty permalink base text. i.e. www.example.com/product/</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: Capabilities -->
                            <div class="scpt-tab-panel" data-tab="capabilities">
                                <div class="scpt-form-grid">
                                    <div class="scpt-form-group">
                                        <label for="scpt-capability-type">Base Capability Type</label>
                                        <select id="scpt-capability-type" name="capability_type" class="scpt-input">
                                            <option value="post">Post</option>
                                            <option value="page">Page</option>
                                        </select>
                                        <p class="scpt-help-text">Used as a base to construct capabilities.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: Rest API -->
                            <div class="scpt-tab-panel" data-tab="rest-api">
                                <div class="scpt-form-grid">
                                    <div class="scpt-form-group">
                                        <label for="scpt-show-in-rest">Show in Rest</label>
                                        <select id="scpt-show-in-rest" name="show_in_rest" class="scpt-input">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                        <p class="scpt-help-text">Whether to add the post type route in the REST API 'wp/v2' namespace.</p>
                                    </div>
                                    <div class="scpt-form-group">
                                        <label for="scpt-rest-base">Rest Base</label>
                                        <input type="text" id="scpt-rest-base" name="rest_base" 
                                               placeholder="" class="scpt-input">
                                        <p class="scpt-help-text">To change the base url of REST API route. Default is the post type key.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="scpt-form-actions">
                                <button type="submit" class="button button-primary button-large">${scptData.i18n.btn_create}</button>
                                <a href="admin.php?page=simple-cpt" class="button button-large">${scptData.i18n.btn_cancel}</a>
                            </div>
                        </form>
                    </div>
                </div>
            `;

            $container.html(html);
            
            // Gérer les clics sur les onglets
            $(document).on('click', '.scpt-tab-btn', function() {
                const tab = $(this).data('tab');
                
                // Activer l'onglet
                $('.scpt-tab-btn').removeClass('active');
                $(this).addClass('active');
                
                // Afficher le panel correspondant
                $('.scpt-tab-panel').removeClass('active');
                $(`.scpt-tab-panel[data-tab="${tab}"]`).addClass('active');
            });
        }
    };

    // Initialiser au chargement du DOM
    $(document).ready(function() {
        SCPT.init();
    });

})(jQuery);
