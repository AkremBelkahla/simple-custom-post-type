# Simple Custom Post Type

Plugin WordPress moderne et professionnel pour créer et gérer des Custom Post Types avec une interface intuitive.

## 📋 Description

**Simple Custom Post Type** est un plugin WordPress qui permet de créer et gérer facilement des Custom Post Types (CPT) avec tous les champs possibles. Il offre une interface moderne, une architecture robuste et respecte tous les standards WordPress.

## ✨ Fonctionnalités

- 🎨 **Interface moderne à onglets** - Design inspiré de CPT UI avec navigation intuitive
- 🔧 **Configuration complète** - 8 onglets organisés (General, Post Type, Labels, Options, Visibility, Permalinks, Capabilities, Rest API)
- 🛡️ **Sécurité renforcée** - Validation stricte et sanitization
- 📊 **Gestion des champs** - Support de tous les types de champs
- 🔍 **Système de logs** - Traçabilité complète des actions
- 🌐 **Multilingue** - Support i18n/l10n
- ⚡ **Performance optimisée** - Cache et lazy loading
- 📱 **Responsive** - Compatible mobile et tablette (grille adaptative)
- 🔌 **REST API** - Exposition des CPT via l'API REST
- 📝 **Documentation complète** - Code documenté et testé

## 🚀 Installation

1. Télécharger le plugin
2. Décompresser dans `/wp-content/plugins/`
3. Activer depuis l'administration WordPress
4. Accéder au menu "Simple CPT"

### Prérequis

- WordPress 6.0 ou supérieur
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur

## 📁 Structure du Plugin

```
simple-custom-post-type/
├── simple-custom-post-type.php # Fichier principal
├── uninstall.php               # Désinstallation
├── README.md                   # Documentation
├── CHANGELOG.md                # Historique des versions
├── includes/                   # Classes PHP
│   ├── Core/                   # Noyau du plugin
│   │   ├── Plugin.php          # Classe principale (Singleton)
│   │   ├── Activator.php       # Activation
│   │   └── Deactivator.php     # Désactivation
│   ├── PostTypes/              # Gestion des CPT
│   │   └── Manager.php         # Gestionnaire de CPT
│   ├── Admin/                  # Interface d'administration
│   │   ├── AdminMenu.php       # Menus admin
│   │   └── Assets.php          # Chargement des assets
│   └── Utils/                  # Utilitaires
│       ├── Logger.php          # Système de logs
│       ├── Validator.php       # Validation des données
│       └── Cache.php           # Gestion du cache
├── assets/                     # Ressources front-end
│   ├── css/
│   │   └── admin.css           # Styles admin
│   └── js/
│       └── admin.js            # Scripts admin
├── languages/                  # Traductions
│   └── simple-custom-post-type.pot
└── tests/                      # Tests unitaires
    └── phpunit.xml
```

## 🎯 Architecture

### Principes de conception

1. **Séparation des responsabilités** - Chaque classe a une responsabilité unique
2. **Singleton Pattern** - Classe principale en singleton
3. **PSR-4 Autoloading** - Chargement automatique des classes
4. **Namespace** - Organisation avec `SimpleCustomPostType\`
5. **Hooks WordPress** - Utilisation des actions et filtres
6. **Sécurité** - Validation, sanitization, nonces, capabilities

### Composants principaux

#### Core\Plugin
Classe principale du plugin (Singleton). Gère l'initialisation et coordonne les autres composants.

#### PostTypes\Manager
Gestionnaire des Custom Post Types. Enregistre, sauvegarde, supprime et récupère les CPT.

#### Utils\Logger
Système de logging avec différents niveaux (debug, info, warning, error, critical).

#### Utils\Validator
Validation et sanitization des données entrantes.

#### Admin\AdminMenu
Gestion des menus d'administration.

## 💻 Utilisation

### Créer un Custom Post Type

1. Aller dans **Simple CPT > Ajouter**
2. Naviguer entre les onglets pour configurer :
   - **General** : Function Name, Text Domain
   - **Post Type** : Slug (max 20 caractères), Noms (pluriel/singulier), Description
   - **Labels** : Personnaliser tous les labels (Menu Name, Add New, Edit Item, etc.)
   - **Options** : Supports (titre, éditeur, image, etc.), Archives, Export
   - **Visibility** : Visibilité dans l'admin, menu, navigation
   - **Permalinks** : Configuration des URLs
   - **Capabilities** : Permissions de base
   - **Rest API** : Exposition via l'API REST
3. Cliquer sur "Créer le post type"
4. Redirection automatique vers la liste avec message de succès

### Configuration

Accéder à **Simple CPT > Paramètres** pour configurer :

- **Logs** : Activer/désactiver l'enregistrement des logs
- **Rétention** : Durée de conservation des logs (jours)
- **REST API** : Exposer les CPT via l'API REST
- **Gutenberg** : Activer l'éditeur Gutenberg
- **Désinstallation** : Supprimer les données à la désinstallation

### Consulter les logs

Accéder à **Simple CPT > Logs** pour voir l'historique des actions.

## 🔒 Sécurité

Le plugin implémente plusieurs couches de sécurité :

- **Nonces** : Vérification des requêtes AJAX
- **Capabilities** : Vérification des permissions utilisateur
- **Sanitization** : Nettoyage de toutes les données entrantes
- **Validation** : Vérification de la validité des données
- **Prepared Statements** : Protection contre les injections SQL
- **Escaping** : Protection XSS sur les sorties

## 📊 Base de données

Le plugin crée 3 tables :

### wp_scpt_post_types
Stocke les configurations des Custom Post Types.

```sql
id, slug, config, is_active, created_at, updated_at
```

### wp_scpt_fields
Stocke les champs personnalisés.

```sql
id, post_type_slug, field_key, field_config, field_order, is_active, created_at, updated_at
```

### wp_scpt_logs
Stocke les logs d'activité.

```sql
id, level, message, context, user_id, ip_address, created_at
```

## 🔌 API

### Actions WordPress

```php
// Avant l'enregistrement d'un post type
do_action('scpt_before_register_post_type', $slug, $args);

// Après l'enregistrement d'un post type
do_action('scpt_after_register_post_type', $slug, $args);

// Avant la sauvegarde
do_action('scpt_before_save_post_type', $data);

// Après la sauvegarde
do_action('scpt_after_save_post_type', $data);
```

### Filtres WordPress

```php
// Modifier les arguments d'un post type
add_filter('scpt_post_type_args', function($args, $config) {
    // Modifier $args
    return $args;
}, 10, 2);

// Modifier les slugs réservés
add_filter('scpt_reserved_slugs', function($slugs) {
    $slugs[] = 'mon-slug-reserve';
    return $slugs;
});
```

### REST API

Endpoints disponibles :

- `GET /wp-json/scpt/v1/post-types` - Liste des post types
- `POST /wp-json/scpt/v1/post-types` - Créer un post type
- `GET /wp-json/scpt/v1/post-types/{slug}` - Récupérer un post type
- `PUT /wp-json/scpt/v1/post-types/{slug}` - Mettre à jour un post type
- `DELETE /wp-json/scpt/v1/post-types/{slug}` - Supprimer un post type

## 🧪 Tests

### Tests unitaires

```bash
# Installer PHPUnit
composer install

# Lancer les tests
./vendor/bin/phpunit
```

### Tests manuels

1. Créer un post type
2. Vérifier l'affichage dans le menu admin
3. Créer un post de ce type
4. Vérifier l'affichage public
5. Modifier le post type
6. Supprimer le post type

## 📝 Standards de code

Le plugin respecte :

- **WordPress Coding Standards** - PHPCS avec les règles WordPress
- **PSR-4** - Autoloading des classes
- **PSR-12** - Style de code
- **Documentation** - PHPDoc pour toutes les fonctions
- **Sécurité** - OWASP Top 10

## 🔄 Versioning

Le plugin utilise le **Semantic Versioning** (SemVer) :

- **MAJOR** : Changements incompatibles
- **MINOR** : Nouvelles fonctionnalités compatibles
- **PATCH** : Corrections de bugs

## 👨‍💻 Développement

### Contribuer

1. Fork le projet
2. Créer une branche (`git checkout -b feature/AmazingFeature`)
3. Commit les changements (`git commit -m 'Add AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

### Conventions de commit

- `feat:` Nouvelle fonctionnalité
- `fix:` Correction de bug
- `docs:` Documentation
- `style:` Formatage
- `refactor:` Refactoring
- `test:` Tests
- `chore:` Maintenance

## 📄 Licence

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html

## 👤 Auteur

**Akrem Belkahla**
- Email: contact@infinityweb.tn
- Website: https://infinityweb.tn

## 🏢 Agence

**InfinityWeb**
- Website: https://infinityweb.tn

## 📞 Support

Pour toute question ou problème :
- Créer une issue sur GitHub
- Contacter le support : contact@infinityweb.tn

## 🙏 Remerciements

- WordPress Community
- Contributors

---

Made with ❤️ by InfinityWeb
