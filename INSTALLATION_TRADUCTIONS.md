# 🚀 Installation des Traductions

## Guide Rapide

### Étape 1 : Compiler les fichiers .mo

Les fichiers .po doivent être compilés en .mo pour être utilisés par WordPress.

#### Option A : Script Automatique (Recommandé)

**Windows (PowerShell)** :
```powershell
.\compile-translations.ps1
```

**Linux/Mac (Bash)** :
```bash
chmod +x compile-translations.sh
./compile-translations.sh
```

#### Option B : Manuellement avec msgfmt

```bash
cd languages/
msgfmt simple-custom-post-type-fr_FR.po -o simple-custom-post-type-fr_FR.mo
msgfmt simple-custom-post-type-en_US.po -o simple-custom-post-type-en_US.mo
```

#### Option C : Avec Poedit

1. Ouvrir `simple-custom-post-type-fr_FR.po` avec Poedit
2. Menu : `Fichier` > `Compiler en MO`
3. Répéter pour `simple-custom-post-type-en_US.po`

#### Option D : Avec WP-CLI

```bash
wp i18n make-mo languages/ --allow-root
```

### Étape 2 : Vérifier les fichiers

Après compilation, vous devriez avoir :

```
languages/
├── simple-custom-post-type.pot
├── simple-custom-post-type-fr_FR.po
├── simple-custom-post-type-fr_FR.mo  ✅
├── simple-custom-post-type-en_US.po
└── simple-custom-post-type-en_US.mo  ✅
```

### Étape 3 : Changer la langue de WordPress

#### Méthode 1 : Via l'Admin

1. Aller dans `Réglages` > `Général`
2. Changer `Langue du site`
3. Enregistrer les modifications

#### Méthode 2 : Via wp-config.php

```php
// Français
define('WPLANG', 'fr_FR');

// Anglais
define('WPLANG', 'en_US');
```

### Étape 4 : Tester

1. Aller dans `Simple CPT` > `Ajouter`
2. Vérifier que l'interface est dans la bonne langue

## 🧪 Tests de Vérification

### Test Français 🇫🇷

**Configuration** :
- Langue du site : `Français`
- Fichier : `simple-custom-post-type-fr_FR.mo` présent

**Vérifications** :
- [ ] "Libellé au pluriel" s'affiche
- [ ] "Libellé au singulier" s'affiche
- [ ] "Clé du type de publication" s'affiche
- [ ] "Créer le type de publication" sur le bouton
- [ ] "Annuler" sur le bouton secondaire

### Test Anglais 🇬🇧

**Configuration** :
- Langue du site : `English (United States)`
- Fichier : `simple-custom-post-type-en_US.mo` présent

**Vérifications** :
- [ ] "Plural Label" s'affiche
- [ ] "Singular Label" s'affiche
- [ ] "Post Type Key" s'affiche
- [ ] "Create Post Type" sur le bouton
- [ ] "Cancel" sur le bouton secondaire

## 🔧 Dépannage

### Problème : Les traductions ne s'affichent pas

**Solutions** :

1. **Vérifier que le fichier .mo existe** :
   ```bash
   ls -la languages/*.mo
   ```

2. **Vérifier la langue du site** :
   - Admin : `Réglages` > `Général` > `Langue du site`
   - Code : Vérifier `WPLANG` dans `wp-config.php`

3. **Vider le cache** :
   - Cache WordPress
   - Cache navigateur (Ctrl+F5)
   - Cache serveur (si applicable)

4. **Recompiler les fichiers .mo** :
   ```bash
   ./compile-translations.sh
   ```

5. **Vérifier les permissions** :
   ```bash
   chmod 644 languages/*.mo
   ```

### Problème : Erreur lors de la compilation

**Solutions** :

1. **Installer msgfmt** :
   - Windows : https://mlocati.github.io/articles/gettext-iconv-windows.html
   - Ubuntu/Debian : `sudo apt-get install gettext`
   - macOS : `brew install gettext`

2. **Utiliser Poedit** :
   - Télécharger : https://poedit.net/
   - Ouvrir le .po et compiler

3. **Utiliser WP-CLI** :
   ```bash
   wp i18n make-mo languages/
   ```

### Problème : Certaines chaînes ne sont pas traduites

**Solutions** :

1. **Vérifier le fichier .po** :
   - Ouvrir avec un éditeur de texte
   - Chercher la chaîne manquante
   - Vérifier qu'elle a une traduction (msgstr)

2. **Recompiler après modification** :
   ```bash
   msgfmt simple-custom-post-type-fr_FR.po -o simple-custom-post-type-fr_FR.mo
   ```

3. **Vider le cache** :
   - WordPress
   - Navigateur
   - Serveur

## 📊 Checklist d'Installation

- [ ] Fichiers .po présents dans `languages/`
- [ ] Fichiers .mo compilés
- [ ] Permissions correctes (644)
- [ ] Langue WordPress configurée
- [ ] Cache vidé
- [ ] Tests effectués (FR et EN)
- [ ] Interface traduite correctement

## 🎯 Résultat Attendu

### Français (fr_FR)
```
┌─────────────────────────────────────┐
│ Libellé au pluriel *                │
│ ┌─────────────────────────────────┐ │
│ │ Films                            │ │
│ └─────────────────────────────────┘ │
│                                     │
│ Libellé au singulier *              │
│ ┌─────────────────────────────────┐ │
│ │ Film                             │ │
│ └─────────────────────────────────┘ │
│                                     │
│ [Créer le type de publication]     │
└─────────────────────────────────────┘
```

### Anglais (en_US)
```
┌─────────────────────────────────────┐
│ Plural Label *                      │
│ ┌─────────────────────────────────┐ │
│ │ Movies                           │ │
│ └─────────────────────────────────┘ │
│                                     │
│ Singular Label *                    │
│ ┌─────────────────────────────────┐ │
│ │ Movie                            │ │
│ └─────────────────────────────────┘ │
│                                     │
│ [Create Post Type]                  │
└─────────────────────────────────────┘
```

## 📞 Support

En cas de problème :

1. Consulter `TRADUCTIONS.md` pour plus de détails
2. Vérifier les logs WordPress (si WP_DEBUG activé)
3. Contacter le support : contact@infinityweb.tn

---

**🌍 Profitez du plugin dans votre langue !**

Made with ❤️ by InfinityWeb
