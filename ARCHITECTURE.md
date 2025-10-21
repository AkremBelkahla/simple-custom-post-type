# Architecture du Plugin Simple Custom Post Type

## 📐 Vue d'ensemble

Le plugin **Simple Custom Post Type** suit une architecture modulaire basée sur les principes SOLID et les meilleures pratiques WordPress.

## 🏗️ Structure des dossiers

```
simple-custom-post-type/
│
├── simple-custom-post-type.php     # Point d'entrée principal
├── uninstall.php                   # Script de désinstallation
│
├── includes/                       # Code source PHP
│   ├── Core/                       # Noyau du plugin
│   │   ├── Plugin.php              # Classe principale (Singleton)
│   │   ├── Activator.php           # Logique d'activation
│   │   └── Deactivator.php         # Logique de désactivation
│   │
│   ├── PostTypes/                  # Gestion des Custom Post Types
│   │   └── Manager.php             # CRUD des post types
│   │
│   ├── Admin/                      # Interface d'administration
│   │   ├── AdminMenu.php           # Menus et pages admin
│   │   └── Assets.php              # Chargement des assets
│   │
│   └── Utils/                      # Utilitaires
│       ├── Logger.php              # Système de logs
│       ├── Validator.php           # Validation des données
│       └── Cache.php               # Gestion du cache
│
├── assets/                         # Ressources front-end
│   ├── css/
│   │   └── admin.css               # Styles d'administration
│   └── js/
│       └── admin.js                # Scripts d'administration
│
├── languages/                      # Fichiers de traduction
│   └── simple-custom-post-type.pot
│
├── tests/                          # Tests unitaires
│   └── phpunit.xml
│
└── docs/                           # Documentation
    ├── README.md
    ├── CHANGELOG.md
    ├── CONTRIBUTING.md
    └── ARCHITECTURE.md (ce fichier)
```

## 🔧 Composants principaux

### 1. Core\Plugin (Singleton)

**Responsabilité** : Orchestrer l'initialisation et coordonner tous les composants.

**Méthodes clés** :
- `get_instance()` : Récupérer l'instance unique
- `init()` : Initialiser le plugin
- `init_components()` : Instancier les composants
- `register_hooks()` : Enregistrer les hooks WordPress

**Pattern** : Singleton pour garantir une seule instance.

### 2. PostTypes\Manager

**Responsabilité** : Gérer le cycle de vie des Custom Post Types.

**Méthodes clés** :
- `register_all()` : Enregistrer tous les CPT actifs
- `register_single()` : Enregistrer un CPT unique
- `get_all()` : Récupérer tous les CPT
- `save()` : Sauvegarder un CPT
- `delete()` : Supprimer un CPT
- `toggle_active()` : Activer/désactiver un CPT

**Stockage** : Base de données (table `wp_scpt_post_types`)

### 3. Utils\Logger

**Responsabilité** : Enregistrer les événements et erreurs.

**Niveaux de log** :
- `debug` : Informations de débogage
- `info` : Informations générales
- `warning` : Avertissements
- `error` : Erreurs
- `critical` : Erreurs critiques

**Stockage** : Base de données (table `wp_scpt_logs`)

### 4. Utils\Validator

**Responsabilité** : Valider et sanitizer les données entrantes.

**Méthodes clés** :
- `validate_post_type_data()` : Valider les données d'un CPT
- `validate_field_data()` : Valider les données d'un champ
- `verify_nonce()` : Vérifier un nonce
- `check_permission()` : Vérifier les permissions

### 5. Utils\Cache

**Responsabilité** : Gérer le cache pour optimiser les performances.

**Méthodes clés** :
- `get()` : Récupérer une valeur
- `set()` : Définir une valeur
- `delete()` : Supprimer une valeur
- `remember()` : Pattern remember (get or set)

### 6. Admin\AdminMenu

**Responsabilité** : Créer et gérer les menus d'administration.

**Pages** :
- Liste des post types
- Ajouter un post type
- Paramètres
- Logs

### 7. Admin\Assets

**Responsabilité** : Charger les CSS et JS dans l'admin.

**Assets** :
- `admin.css` : Styles personnalisés
- `admin.js` : Scripts AJAX et interactions

## 🔄 Flux de données

### Création d'un Custom Post Type

```
Interface Admin
    ↓
Formulaire HTML
    ↓
AJAX Request (scpt_save_post_type)
    ↓
Core\Plugin::ajax_save_post_type()
    ↓
Utils\Validator::validate_post_type_data()
    ↓
PostTypes\Manager::save()
    ↓
Base de données (wp_scpt_post_types)
    ↓
Utils\Logger::info()
    ↓
Response JSON
```

### Enregistrement des CPT au chargement

```
WordPress Init
    ↓
Core\Plugin::register_post_types()
    ↓
PostTypes\Manager::register_all()
    ↓
Utils\Cache::get() ou DB query
    ↓
PostTypes\Manager::register_single()
    ↓
register_post_type() (WordPress)
```

## 🗄️ Base de données

### Table: wp_scpt_post_types

Stocke les configurations des Custom Post Types.

```sql
CREATE TABLE wp_scpt_post_types (
    id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(20) NOT NULL UNIQUE,
    config LONGTEXT NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (is_active)
);
```

### Table: wp_scpt_fields

Stocke les champs personnalisés (future fonctionnalité).

```sql
CREATE TABLE wp_scpt_fields (
    id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_type_slug VARCHAR(20) NOT NULL,
    field_key VARCHAR(100) NOT NULL,
    field_config LONGTEXT NOT NULL,
    field_order INT(11) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (post_type_slug, field_key),
    INDEX (post_type_slug),
    INDEX (is_active)
);
```

### Table: wp_scpt_logs

Stocke les logs d'activité.

```sql
CREATE TABLE wp_scpt_logs (
    id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    level VARCHAR(20) NOT NULL,
    message TEXT NOT NULL,
    context LONGTEXT,
    user_id BIGINT(20) UNSIGNED,
    ip_address VARCHAR(45),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (level),
    INDEX (user_id),
    INDEX (created_at)
);
```

## 🔒 Sécurité

### Niveaux de sécurité

1. **Vérification des capacités**
   ```php
   if (!current_user_can('manage_options')) {
       wp_die('Permission refusée');
   }
   ```

2. **Nonces**
   ```php
   check_ajax_referer('scpt_nonce', 'nonce');
   wp_verify_nonce($_POST['nonce'], 'scpt_nonce');
   ```

3. **Sanitization**
   ```php
   $slug = sanitize_key($data['slug']);
   $name = sanitize_text_field($data['name']);
   ```

4. **Validation**
   ```php
   $validator = new Validator();
   $validated = $validator->validate_post_type_data($data);
   ```

5. **Prepared Statements**
   ```php
   $wpdb->prepare("SELECT * FROM $table WHERE slug = %s", $slug);
   ```

6. **Escaping**
   ```php
   echo esc_html($name);
   echo esc_url($url);
   echo esc_attr($value);
   ```

## ⚡ Performance

### Stratégies d'optimisation

1. **Cache WordPress**
   - Utilisation de `wp_cache_*` pour les données fréquemment accédées
   - TTL de 1 heure par défaut

2. **Lazy Loading**
   - Chargement des assets uniquement sur les pages du plugin
   - Chargement conditionnel des composants

3. **Requêtes optimisées**
   - Index sur les colonnes fréquemment recherchées
   - Limitation des résultats avec LIMIT

4. **Transients**
   - Nettoyage automatique des transients expirés

## 🧪 Tests

### Structure des tests

```
tests/
├── bootstrap.php           # Bootstrap PHPUnit
├── Core/
│   ├── PluginTest.php
│   └── ActivatorTest.php
├── PostTypes/
│   └── ManagerTest.php
└── Utils/
    ├── LoggerTest.php
    ├── ValidatorTest.php
    └── CacheTest.php
```

### Lancer les tests

```bash
composer test
```

## 🔌 Hooks et Filtres

### Actions

```php
// Avant l'enregistrement
do_action('scpt_before_register_post_type', $slug, $args);

// Après l'enregistrement
do_action('scpt_after_register_post_type', $slug, $args);

// Avant la sauvegarde
do_action('scpt_before_save_post_type', $data);

// Après la sauvegarde
do_action('scpt_after_save_post_type', $data);
```

### Filtres

```php
// Modifier les arguments d'un post type
add_filter('scpt_post_type_args', function($args, $config) {
    return $args;
}, 10, 2);

// Modifier les slugs réservés
add_filter('scpt_reserved_slugs', function($slugs) {
    return $slugs;
});
```

## 🚀 Extensibilité

Le plugin est conçu pour être facilement extensible :

1. **Hooks WordPress** : Actions et filtres à chaque étape
2. **Classes modulaires** : Faciles à étendre ou remplacer
3. **Interfaces** : Possibilité d'implémenter des interfaces personnalisées
4. **Autoloading PSR-4** : Ajout facile de nouvelles classes

## 📚 Ressources

- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [PSR-4 Autoloading](https://www.php-fig.org/psr/psr-4/)
- [SOLID Principles](https://en.wikipedia.org/wiki/SOLID)

---

**Auteur** : Akrem Belkahla <contact@infinityweb.tn>  
**Agence** : InfinityWeb - https://infinityweb.tn  
**Version** : 1.0.0
