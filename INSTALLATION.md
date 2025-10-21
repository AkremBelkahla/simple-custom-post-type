# Guide d'installation - Simple Custom Post Type

## 📦 Installation

### Méthode 1 : Installation manuelle

1. **Télécharger le plugin**
   - Télécharger l'archive ZIP du plugin
   - Ou cloner le dépôt Git

2. **Décompresser dans WordPress**
   ```bash
   cd wp-content/plugins/
   unzip simple-custom-post-type.zip
   # ou
   git clone [URL_DU_REPO] simple-custom-post-type
   ```

3. **Activer le plugin**
   - Aller dans WordPress Admin > Extensions
   - Trouver "Simple Custom Post Type"
   - Cliquer sur "Activer"

### Méthode 2 : Via l'administration WordPress

1. Aller dans **Extensions > Ajouter**
2. Cliquer sur **Téléverser une extension**
3. Choisir le fichier ZIP
4. Cliquer sur **Installer maintenant**
5. Cliquer sur **Activer**

## ⚙️ Configuration initiale

### 1. Vérifier les prérequis

Le plugin vérifie automatiquement :
- ✅ PHP 7.4 ou supérieur
- ✅ WordPress 6.0 ou supérieur
- ✅ Permissions d'écriture dans la base de données

### 2. Accéder à l'interface

Après activation, un nouveau menu **"Simple CPT"** apparaît dans l'administration WordPress.

### 3. Configurer les paramètres

Aller dans **Simple CPT > Paramètres** :

- **Activer les logs** : Recommandé pour le débogage
- **Rétention des logs** : 30 jours par défaut
- **REST API** : Activer si vous utilisez l'API WordPress
- **Gutenberg** : Activer pour l'éditeur de blocs
- **Supprimer les données** : Désactiver pour conserver les données lors de la désinstallation

## 🚀 Premier Custom Post Type

### Création rapide

1. Aller dans **Simple CPT > Ajouter**

2. Remplir les informations de base :
   ```
   Slug: portfolio
   Nom (pluriel): Portfolios
   Nom (singulier): Portfolio
   ```

3. Configurer les options :
   - ✅ Public
   - ✅ Afficher dans le menu
   - ✅ Afficher dans REST API
   - ✅ Activer l'archive

4. Choisir les supports :
   - ✅ Titre
   - ✅ Éditeur
   - ✅ Image à la une
   - ✅ Extrait
   - ✅ Champs personnalisés

5. Cliquer sur **Enregistrer**

### Vérification

Un nouveau menu "Portfolios" devrait apparaître dans l'administration WordPress.

## 🔧 Configuration avancée

### Personnaliser l'icône du menu

```php
// Dans votre thème ou plugin
add_filter('scpt_post_type_args', function($args, $config) {
    if ($config['slug'] === 'portfolio') {
        $args['menu_icon'] = 'dashicons-portfolio';
    }
    return $args;
}, 10, 2);
```

### Ajouter des taxonomies personnalisées

```php
// Créer une taxonomie
register_taxonomy('portfolio_category', 'portfolio', [
    'label' => 'Catégories Portfolio',
    'hierarchical' => true,
]);
```

### Modifier les capacités

```php
add_filter('scpt_post_type_args', function($args, $config) {
    if ($config['slug'] === 'portfolio') {
        $args['capability_type'] = ['portfolio', 'portfolios'];
        $args['map_meta_cap'] = true;
    }
    return $args;
}, 10, 2);
```

## 🛠️ Développement

### Installation pour développeurs

1. **Cloner le dépôt**
   ```bash
   git clone [URL_DU_REPO] simple-custom-post-type
   cd simple-custom-post-type
   ```

2. **Installer les dépendances**
   ```bash
   composer install
   ```

3. **Configurer l'environnement**
   ```bash
   # Activer WP_DEBUG dans wp-config.php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   define('WP_DEBUG_DISPLAY', false);
   ```

4. **Lancer les tests**
   ```bash
   composer test
   ```

5. **Vérifier le code**
   ```bash
   composer phpcs
   ```

### Structure de développement

```
simple-custom-post-type/
├── includes/           # Code source PHP
├── assets/            # CSS/JS
├── tests/             # Tests unitaires
└── languages/         # Traductions
```

## 📊 Base de données

### Tables créées automatiquement

Le plugin crée 3 tables lors de l'activation :

1. **wp_scpt_post_types** : Configurations des CPT
2. **wp_scpt_fields** : Champs personnalisés
3. **wp_scpt_logs** : Logs d'activité

### Vérifier les tables

```sql
SHOW TABLES LIKE 'wp_scpt_%';
```

### Réinitialiser les tables

```sql
DROP TABLE IF EXISTS wp_scpt_post_types;
DROP TABLE IF EXISTS wp_scpt_fields;
DROP TABLE IF EXISTS wp_scpt_logs;
```

Puis désactiver et réactiver le plugin.

## 🔍 Dépannage

### Le plugin ne s'active pas

**Vérifier** :
- Version PHP >= 7.4
- Version WordPress >= 6.0
- Permissions de la base de données

**Solution** :
```bash
# Vérifier la version PHP
php -v

# Vérifier les logs WordPress
tail -f wp-content/debug.log
```

### Les post types n'apparaissent pas

**Vérifier** :
- Le post type est actif dans Simple CPT
- Vider le cache WordPress
- Régénérer les permaliens

**Solution** :
1. Aller dans **Réglages > Permaliens**
2. Cliquer sur **Enregistrer** (sans rien modifier)

### Erreur 404 sur les archives

**Solution** :
```php
// Régénérer les règles de réécriture
flush_rewrite_rules();
```

### Les logs ne s'enregistrent pas

**Vérifier** :
- Les logs sont activés dans les paramètres
- La table wp_scpt_logs existe
- Les permissions de la base de données

### Erreur de permissions

**Solution** :
```php
// Ajouter les capacités manuellement
$role = get_role('administrator');
$role->add_cap('manage_custom_post_types');
$role->add_cap('edit_custom_post_types');
$role->add_cap('delete_custom_post_types');
```

## 🔄 Migration depuis un autre plugin

### Depuis CPT UI

1. Exporter vos post types depuis CPT UI
2. Noter les configurations
3. Recréer dans Simple Custom Post Type
4. Vérifier que tout fonctionne
5. Désactiver CPT UI

### Depuis code personnalisé

Si vous avez des `register_post_type()` dans votre thème :

1. Noter toutes les configurations
2. Créer les équivalents dans Simple Custom Post Type
3. Commenter le code dans le thème
4. Tester
5. Supprimer le code commenté

## 📱 Compatibilité

### Thèmes testés

- ✅ Twenty Twenty-Four
- ✅ Twenty Twenty-Three
- ✅ Astra
- ✅ GeneratePress
- ✅ OceanWP

### Plugins compatibles

- ✅ Advanced Custom Fields (ACF)
- ✅ Yoast SEO
- ✅ WooCommerce
- ✅ Elementor
- ✅ WPBakery

### Environnements testés

- ✅ Apache
- ✅ Nginx
- ✅ PHP 7.4, 8.0, 8.1, 8.2
- ✅ MySQL 5.7, 8.0
- ✅ MariaDB 10.3+

## 🆘 Support

### Documentation

- [README.md](README.md) - Vue d'ensemble
- [ARCHITECTURE.md](ARCHITECTURE.md) - Architecture technique
- [CONTRIBUTING.md](CONTRIBUTING.md) - Guide de contribution
- [CHANGELOG.md](CHANGELOG.md) - Historique des versions

### Obtenir de l'aide

1. **Documentation** : Lire les fichiers ci-dessus
2. **Issues GitHub** : Créer une issue
3. **Email** : contact@infinityweb.tn
4. **Site web** : https://infinityweb.tn

### Signaler un bug

Créer une issue avec :
- Description du problème
- Étapes pour reproduire
- Version de WordPress
- Version de PHP
- Autres plugins actifs
- Captures d'écran si pertinent

## ✅ Checklist post-installation

- [ ] Plugin activé avec succès
- [ ] Aucune erreur dans les logs
- [ ] Menu "Simple CPT" visible
- [ ] Paramètres configurés
- [ ] Premier post type créé
- [ ] Post type visible dans le menu
- [ ] Création d'un post de test réussie
- [ ] Affichage public fonctionnel
- [ ] Permaliens régénérés

## 🎉 Prêt à utiliser !

Votre plugin Simple Custom Post Type est maintenant installé et configuré. Vous pouvez commencer à créer vos Custom Post Types !

---

**Besoin d'aide ?** Contactez-nous : contact@infinityweb.tn  
**Site web** : https://infinityweb.tn
