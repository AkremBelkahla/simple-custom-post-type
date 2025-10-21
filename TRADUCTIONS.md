# 🌍 Guide des Traductions

## Vue d'ensemble

Le plugin **Simple Custom Post Type** est entièrement **translation ready** et supporte plusieurs langues :

- 🇫🇷 **Français** (fr_FR) - Langue par défaut
- 🇬🇧 **Anglais** (en_US) - Traduction complète
- 🌍 **Autres langues** - Facilement ajoutables

## 📋 Configuration

### Text Domain

Le plugin utilise le text domain : `simple-custom-post-type`

```php
// Défini dans l'en-tête du plugin
Text Domain: simple-custom-post-type
Domain Path: /languages
```

### Chargement des traductions

Les traductions sont chargées automatiquement au démarrage du plugin :

```php
// simple-custom-post-type.php
load_plugin_textdomain(
    'simple-custom-post-type',
    false,
    dirname(SCPT_PLUGIN_BASENAME) . '/languages'
);
```

## 📁 Structure des Fichiers

```
languages/
├── simple-custom-post-type.pot      # Template (fichier source)
├── simple-custom-post-type-fr_FR.po # Traduction française
├── simple-custom-post-type-fr_FR.mo # Traduction française compilée
├── simple-custom-post-type-en_US.po # Traduction anglaise
└── simple-custom-post-type-en_US.mo # Traduction anglaise compilée
```

## 🔧 Utilisation dans le Code

### PHP

```php
// Texte simple
__('Texte à traduire', 'simple-custom-post-type');

// Texte avec echo
_e('Texte à afficher', 'simple-custom-post-type');

// Texte avec variables
sprintf(
    __('Vous utilisez PHP %s', 'simple-custom-post-type'),
    PHP_VERSION
);

// Texte avec échappement
esc_html__('Texte sécurisé', 'simple-custom-post-type');
```

### JavaScript

Les traductions sont passées via `wp_localize_script` :

```php
// includes/Admin/Assets.php
wp_localize_script('scpt-admin', 'scptData', [
    'i18n' => [
        'label_plural' => __('Libellé au pluriel', 'simple-custom-post-type'),
        'label_singular' => __('Libellé au singulier', 'simple-custom-post-type'),
        // ...
    ],
]);
```

Utilisation en JavaScript :

```javascript
// assets/js/admin.js
const html = `
    <label>${scptData.i18n.label_plural} *</label>
    <input placeholder="${scptData.i18n.placeholder_plural}">
`;
```

## 📝 Chaînes Traduites

### Mode Simple

| Clé | Français | Anglais |
|-----|----------|---------|
| `label_plural` | Libellé au pluriel | Plural Label |
| `label_singular` | Libellé au singulier | Singular Label |
| `label_slug` | Clé du type de publication | Post Type Key |
| `label_taxonomies` | Taxonomies | Taxonomies |
| `label_public` | Public | Public |
| `label_hierarchical` | Hiérarchique | Hierarchical |
| `label_advanced` | Configuration avancée | Advanced Configuration |

### Placeholders

| Clé | Français | Anglais |
|-----|----------|---------|
| `placeholder_plural` | Films | Movies |
| `placeholder_singular` | Film | Movie |
| `placeholder_slug` | film | movie |

### Textes d'aide

| Clé | Français | Anglais |
|-----|----------|---------|
| `help_slug` | Lettres minuscules, tiret bas... | Lowercase letters, underscores... |
| `help_taxonomies` | Sélectionnez les taxonomies... | Select existing taxonomies... |
| `help_public` | Visible sur l'interface publique... | Visible on the public interface... |
| `help_hierarchical` | Les types de publication... | Hierarchical post types... |
| `help_advanced` | Je sais ce que je fais... | I know what I'm doing... |

### Boutons

| Clé | Français | Anglais |
|-----|----------|---------|
| `btn_create` | Créer le type de publication | Create Post Type |
| `btn_cancel` | Annuler | Cancel |

### Messages

| Clé | Français | Anglais |
|-----|----------|---------|
| `confirm_delete` | Êtes-vous sûr... | Are you sure... |
| `error_generic` | Une erreur est survenue | An error occurred |
| `success_saved` | Enregistré avec succès | Saved successfully |

## 🛠️ Créer une Nouvelle Traduction

### Étape 1 : Copier le fichier .pot

```bash
cd languages/
cp simple-custom-post-type.pot simple-custom-post-type-es_ES.po
```

### Étape 2 : Éditer l'en-tête

```po
"Language: es_ES\n"
"Plural-Forms: nplurals=2; plural=(n != 1);\n"
```

### Étape 3 : Traduire les chaînes

```po
msgid "Libellé au pluriel"
msgstr "Etiqueta plural"

msgid "Libellé au singulier"
msgstr "Etiqueta singular"
```

### Étape 4 : Compiler le fichier .mo

```bash
msgfmt simple-custom-post-type-es_ES.po -o simple-custom-post-type-es_ES.mo
```

## 🔄 Mettre à Jour les Traductions

### 1. Extraire les nouvelles chaînes

Utiliser un outil comme **Poedit** ou **WP-CLI** :

```bash
wp i18n make-pot . languages/simple-custom-post-type.pot
```

### 2. Mettre à jour les fichiers .po

```bash
msgmerge --update languages/simple-custom-post-type-fr_FR.po languages/simple-custom-post-type.pot
msgmerge --update languages/simple-custom-post-type-en_US.po languages/simple-custom-post-type.pot
```

### 3. Traduire les nouvelles chaînes

Ouvrir les fichiers .po et traduire les nouvelles entrées.

### 4. Recompiler les fichiers .mo

```bash
msgfmt languages/simple-custom-post-type-fr_FR.po -o languages/simple-custom-post-type-fr_FR.mo
msgfmt languages/simple-custom-post-type-en_US.po -o languages/simple-custom-post-type-en_US.mo
```

## 🌐 Détection Automatique de la Langue

WordPress détecte automatiquement la langue selon :

1. **Langue du site** : Définie dans `Réglages > Général > Langue du site`
2. **Langue de l'utilisateur** : Définie dans le profil utilisateur
3. **Constante WPLANG** : Définie dans `wp-config.php`

### Exemples

```php
// Site en français
define('WPLANG', 'fr_FR');
// → Charge simple-custom-post-type-fr_FR.mo

// Site en anglais
define('WPLANG', 'en_US');
// → Charge simple-custom-post-type-en_US.mo

// Site en espagnol (pas de traduction)
define('WPLANG', 'es_ES');
// → Utilise les chaînes par défaut (français)
```

## 🧪 Tester les Traductions

### Méthode 1 : Changer la langue du site

1. Aller dans `Réglages > Général`
2. Changer `Langue du site`
3. Enregistrer
4. Recharger la page du plugin

### Méthode 2 : Utiliser un plugin

- **Loco Translate** - Éditer les traductions dans l'admin
- **WPML** - Support multilingue complet
- **Polylang** - Gestion multilingue

### Méthode 3 : Forcer la langue en code

```php
// wp-config.php
define('WPLANG', 'en_US');
```

## 📊 Statistiques de Traduction

### Français (fr_FR)
- ✅ **100%** traduit
- 🔢 **50+** chaînes
- 👤 Traducteur : Akrem Belkahla

### Anglais (en_US)
- ✅ **100%** traduit
- 🔢 **50+** chaînes
- 👤 Traducteur : Akrem Belkahla

## 🎯 Bonnes Pratiques

### 1. Toujours utiliser le text domain

```php
// ✅ Bon
__('Texte', 'simple-custom-post-type');

// ❌ Mauvais
__('Texte');
```

### 2. Éviter les concaténations

```php
// ✅ Bon
sprintf(__('Vous avez %d éléments', 'simple-custom-post-type'), $count);

// ❌ Mauvais
__('Vous avez', 'simple-custom-post-type') . ' ' . $count . ' ' . __('éléments', 'simple-custom-post-type');
```

### 3. Utiliser des contextes si nécessaire

```php
// Pour différencier des chaînes identiques
_x('Post', 'nom', 'simple-custom-post-type');
_x('Post', 'verbe', 'simple-custom-post-type');
```

### 4. Échapper les sorties

```php
// ✅ Bon
echo esc_html__('Texte', 'simple-custom-post-type');

// ❌ Mauvais
echo __('Texte', 'simple-custom-post-type');
```

## 🔧 Outils Recommandés

### Poedit
- **Site** : https://poedit.net/
- **Usage** : Éditer et compiler les fichiers .po/.mo
- **Prix** : Gratuit (version Pro disponible)

### Loco Translate (Plugin WordPress)
- **Site** : https://wordpress.org/plugins/loco-translate/
- **Usage** : Gérer les traductions depuis l'admin WordPress
- **Prix** : Gratuit

### WP-CLI
- **Site** : https://wp-cli.org/
- **Usage** : Générer et mettre à jour les fichiers de traduction
- **Prix** : Gratuit

## 📞 Support

Pour toute question sur les traductions :
- 📧 Email : contact@infinityweb.tn
- 🌐 Site : https://infinityweb.tn

## 🤝 Contribuer

Vous souhaitez ajouter une traduction ?

1. Fork le projet
2. Créer le fichier .po pour votre langue
3. Traduire toutes les chaînes
4. Compiler le fichier .mo
5. Soumettre une Pull Request

---

**🌍 Le plugin parle votre langue !**

Made with ❤️ by InfinityWeb
