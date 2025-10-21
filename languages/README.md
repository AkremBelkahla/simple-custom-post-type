# 🌍 Fichiers de Traduction

Ce dossier contient les fichiers de traduction pour le plugin Simple Custom Post Type.

## 📁 Fichiers

- **simple-custom-post-type.pot** - Template de traduction (fichier source)
- **simple-custom-post-type-fr_FR.po** - Traduction française (source)
- **simple-custom-post-type-fr_FR.mo** - Traduction française (compilée)
- **simple-custom-post-type-en_US.po** - Traduction anglaise (source)
- **simple-custom-post-type-en_US.mo** - Traduction anglaise (compilée)

## 🔧 Compiler les fichiers .mo

Les fichiers .mo doivent être compilés à partir des fichiers .po.

### Méthode 1 : Avec Poedit (Recommandé)

1. Télécharger Poedit : https://poedit.net/
2. Ouvrir le fichier .po
3. Cliquer sur "Fichier" > "Compiler en MO"
4. Le fichier .mo est généré automatiquement

### Méthode 2 : Avec msgfmt (Ligne de commande)

```bash
# Compiler le français
msgfmt simple-custom-post-type-fr_FR.po -o simple-custom-post-type-fr_FR.mo

# Compiler l'anglais
msgfmt simple-custom-post-type-en_US.po -o simple-custom-post-type-en_US.mo
```

### Méthode 3 : Avec WP-CLI

```bash
wp i18n make-mo . --allow-root
```

## 📝 Ajouter une Nouvelle Langue

1. Copier le fichier .pot :
   ```bash
   cp simple-custom-post-type.pot simple-custom-post-type-es_ES.po
   ```

2. Éditer le fichier avec Poedit ou un éditeur de texte

3. Traduire toutes les chaînes

4. Compiler le fichier .mo

5. Placer les fichiers dans ce dossier

## 🔄 Mettre à Jour les Traductions

Après avoir modifié le code du plugin :

1. Régénérer le fichier .pot :
   ```bash
   wp i18n make-pot .. simple-custom-post-type.pot
   ```

2. Mettre à jour les fichiers .po :
   ```bash
   msgmerge --update simple-custom-post-type-fr_FR.po simple-custom-post-type.pot
   msgmerge --update simple-custom-post-type-en_US.po simple-custom-post-type.pot
   ```

3. Traduire les nouvelles chaînes

4. Recompiler les fichiers .mo

## 📊 Langues Disponibles

- 🇫🇷 Français (fr_FR) - 100%
- 🇬🇧 Anglais (en_US) - 100%

## 🤝 Contribuer

Pour ajouter une traduction, consultez le fichier `TRADUCTIONS.md` à la racine du plugin.

---

Made with ❤️ by InfinityWeb
