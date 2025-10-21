# 🎯 Mode Simple & Mode Avancé

## Vue d'ensemble

Le formulaire d'ajout de post type propose maintenant **2 modes** :
- **Mode Simple** (par défaut) - Pour les utilisateurs débutants
- **Mode Avancé** - Pour les utilisateurs expérimentés

## 🌟 Mode Simple (Par Défaut)

### Caractéristiques

Le mode simple affiche uniquement les champs essentiels pour créer un post type rapidement :

#### Champs disponibles

1. **Libellé au pluriel** * (requis)
   - Exemple : "Films"
   - Le nom affiché dans le menu admin

2. **Libellé au singulier** * (requis)
   - Exemple : "Film"
   - Le nom d'un seul élément

3. **Clé du type de publication** * (requis)
   - Exemple : "film"
   - Identifiant unique (max 20 caractères, minuscules, tirets)

4. **Taxonomies**
   - Sélection multiple
   - Options : Catégories, Étiquettes
   - Permet de classer les éléments

5. **Public** (toggle switch)
   - Activé par défaut
   - Rend le post type visible publiquement

6. **Hiérarchique** (toggle switch)
   - Désactivé par défaut
   - Permet d'avoir des éléments parents/enfants

7. **Configuration avancée** (toggle switch)
   - Bascule vers le mode avancé
   - Texte : "Je sais ce que je fais, affiche-moi toutes les options"

### Interface

```
┌─────────────────────────────────────────────────┐
│ Ajouter un nouveau type de publication          │
├─────────────────────────────────────────────────┤
│                                                  │
│  Libellé au pluriel *                           │
│  ┌────────────────────────────────────────────┐ │
│  │ Films                                       │ │
│  └────────────────────────────────────────────┘ │
│                                                  │
│  Libellé au singulier *                         │
│  ┌────────────────────────────────────────────┐ │
│  │ Film                                        │ │
│  └────────────────────────────────────────────┘ │
│                                                  │
│  Clé du type de publication *                   │
│  ┌────────────────────────────────────────────┐ │
│  │ film                                        │ │
│  └────────────────────────────────────────────┘ │
│  Lettres minuscules, tiret bas et tiret...     │
│                                                  │
│  Taxonomies                                     │
│  ┌────────────────────────────────────────────┐ │
│  │ Catégories                                  │ │
│  │ Étiquettes                                  │ │
│  └────────────────────────────────────────────┘ │
│  Sélectionnez les taxonomies existantes...     │
│                                                  │
│  ◉ Public                                       │
│  Visible sur l'interface publique...           │
│                                                  │
│  ○ Hiérarchique                                 │
│  Les types de publication hiérarchiques...     │
│                                                  │
│  ─────────────────────────────────────────────  │
│                                                  │
│  ○ Configuration avancée                        │
│  Je sais ce que je fais, affiche-moi...        │
│                                                  │
│  [Créer le type de publication]  [Annuler]     │
│                                                  │
└─────────────────────────────────────────────────┘
```

### Avantages

- ✅ **Simple et rapide** - Seulement 3 champs requis
- ✅ **Interface épurée** - Pas de surcharge d'options
- ✅ **Guidé** - Textes d'aide pour chaque champ
- ✅ **Valeurs par défaut** - Configuration optimale automatique
- ✅ **Accessible** - Parfait pour les débutants

## 🚀 Mode Avancé

### Activation

Pour activer le mode avancé :
1. Cocher le toggle "Configuration avancée"
2. L'interface bascule automatiquement
3. Les données saisies en mode simple sont conservées

### Caractéristiques

Le mode avancé affiche l'interface complète à onglets avec **8 catégories** :

1. **General** - Function Name, Text Domain
2. **Post Type** - Slug, Noms, Description
3. **Labels** - 8+ labels personnalisables
4. **Options** - 11 supports, Archives, Export
5. **Visibility** - Visibilité complète
6. **Permalinks** - Configuration URLs
7. **Capabilities** - Permissions
8. **Rest API** - Configuration API

### Interface

```
┌─────────────────────────────────────────────────────────────┐
│ [🔧 General] [📝 Post Type] [🏷️ Labels] [⚙️ Options] ...   │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│                                                              │
│  [Contenu de l'onglet actif avec grille 3 colonnes]        │
│                                                              │
│  ──────────────────────────────────────────────────────────│
│  [Créer le post type]  [Annuler]                           │
└─────────────────────────────────────────────────────────────┘
```

### Avantages

- ✅ **Contrôle total** - Toutes les options disponibles
- ✅ **Organisation** - Onglets par catégories
- ✅ **Professionnel** - Interface complète
- ✅ **Flexible** - Personnalisation maximale

## 🔄 Basculement entre Modes

### Mode Simple → Mode Avancé

**Déclencheur** : Cocher le toggle "Configuration avancée"

**Actions automatiques** :
1. Copie des données saisies :
   - Libellé pluriel → Name (Plural)
   - Libellé singulier → Name (Singular)
   - Clé → Post Type Key
2. Masquage du mode simple
3. Affichage du mode avancé
4. Focus sur l'onglet "Post Type"

**Code JavaScript** :
```javascript
// Copier les données
const simpleName = $('#scpt-name-simple').val();
const simpleSingular = $('#scpt-singular-name-simple').val();
const simpleSlug = $('#scpt-slug-simple').val();

$('#scpt-name').val(simpleName);
$('#scpt-singular-name').val(simpleSingular);
$('#scpt-slug').val(simpleSlug);

// Basculer l'affichage
$('.scpt-simple-mode').removeClass('active');
$('.scpt-advanced-mode').addClass('active');
```

### Mode Avancé → Mode Simple

**Déclencheur** : Décocher le toggle "Configuration avancée"

**Actions automatiques** :
1. Masquage du mode avancé
2. Affichage du mode simple
3. Conservation des données de base

## 🎨 Composants UI

### Toggle Switch

Le toggle switch est utilisé pour les options on/off :

**HTML** :
```html
<label class="scpt-toggle-label">
    <input type="checkbox" name="public" value="1" checked class="scpt-toggle-input">
    <span class="scpt-toggle-slider"></span>
    <span class="scpt-toggle-text">Public</span>
</label>
```

**CSS** :
```css
.scpt-toggle-slider {
    width: 44px;
    height: 24px;
    background-color: #8c8f94; /* Gris par défaut */
    border-radius: 24px;
}

.scpt-toggle-input:checked + .scpt-toggle-slider {
    background-color: #2271b1; /* Bleu quand activé */
}
```

**États** :
- ⚪ **Désactivé** : Fond gris (#8c8f94), bouton à gauche
- 🔵 **Activé** : Fond bleu (#2271b1), bouton à droite
- 🔵 **Focus** : Bordure bleue visible

### Select Multiple

Pour les taxonomies :

```html
<select name="taxonomies[]" multiple class="scpt-input" size="3">
    <option value="category">Catégories</option>
    <option value="post_tag">Étiquettes</option>
</select>
```

## 📋 Workflow Utilisateur

### Scénario 1 : Utilisateur Débutant

1. Arrive sur la page → Mode simple affiché
2. Remplit les 3 champs requis
3. Laisse les options par défaut
4. Clique sur "Créer"
5. Post type créé avec succès

**Temps estimé** : 30 secondes

### Scénario 2 : Utilisateur Avancé

1. Arrive sur la page → Mode simple affiché
2. Remplit les champs de base
3. Coche "Configuration avancée"
4. Bascule vers le mode avancé
5. Configure tous les détails
6. Clique sur "Créer"
7. Post type créé avec configuration complète

**Temps estimé** : 2-3 minutes

### Scénario 3 : Utilisateur qui Change d'Avis

1. Commence en mode simple
2. Remplit quelques champs
3. Active le mode avancé
4. Réalise que c'est trop complexe
5. Désactive le mode avancé
6. Retourne au mode simple
7. Termine rapidement

**Temps estimé** : 1 minute

## 🎯 Valeurs par Défaut

### Mode Simple

Les valeurs par défaut optimales sont appliquées automatiquement :

```javascript
{
    public: true,              // Visible publiquement
    hierarchical: false,       // Pas hiérarchique
    show_ui: true,            // Afficher dans l'admin
    show_in_menu: true,       // Afficher dans le menu
    show_in_admin_bar: true,  // Afficher dans la barre admin
    show_in_nav_menus: true,  // Afficher dans les menus de navigation
    has_archive: true,        // Activer les archives
    supports: ['title', 'editor', 'thumbnail'], // Supports de base
}
```

### Mode Avancé

L'utilisateur peut tout personnaliser manuellement.

## 🔍 Validation

### Mode Simple

- **Champs requis** : 3 champs (libellés + clé)
- **Pattern** : Clé du type (a-z, 0-9, -, _)
- **Longueur** : Max 20 caractères pour la clé

### Mode Avancé

- **Champs requis** : Identiques + options supplémentaires
- **Validation étendue** : Tous les champs du mode avancé

## 📱 Responsive

### Mode Simple

- **Desktop** : Formulaire centré, max-width 600px
- **Mobile** : Pleine largeur, champs empilés

### Mode Avancé

- **Desktop** : Grille 3 colonnes
- **Tablet** : Grille 2 colonnes
- **Mobile** : Grille 1 colonne

## 🎨 Design System

### Couleurs

| Élément | État | Couleur |
|---------|------|---------|
| Toggle | Désactivé | #8c8f94 |
| Toggle | Activé | #2271b1 |
| Toggle | Focus | #2271b1 (shadow) |
| Toggle avancé | Texte | #2271b1 |

### Espacements

- Padding mode simple : 30px
- Margin entre champs : 25px
- Gap toggle : 12px

## 🚀 Améliorations Futures

- [ ] Sauvegarde automatique des données
- [ ] Prévisualisation en temps réel
- [ ] Templates prédéfinis en mode simple
- [ ] Import/Export de configuration
- [ ] Mode "Expert" avec code PHP généré

## 📊 Statistiques d'Utilisation (Prévisions)

- **Mode Simple** : 70% des utilisateurs
- **Mode Avancé** : 30% des utilisateurs
- **Basculement** : 15% basculent du simple vers l'avancé

---

**🎨 Deux modes, une seule interface, tous les besoins couverts !**

Made with ❤️ by InfinityWeb
