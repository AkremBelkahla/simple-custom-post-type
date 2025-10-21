# 🧪 Guide de Test - Design Moderne à Onglets

## 📋 Prérequis

- WordPress installé et fonctionnel
- Plugin Simple Custom Post Type activé
- Accès administrateur
- Navigateur moderne (Chrome, Firefox, Safari, Edge)

## 🎯 Objectif des Tests

Vérifier que le nouveau design à onglets fonctionne correctement sur tous les aspects :
- Affichage
- Navigation
- Validation
- Sauvegarde
- Responsive

## 🔍 Tests Détaillés

### Test 1 : Affichage Initial ✅

**Objectif** : Vérifier que la page s'affiche correctement

**Étapes** :
1. Se connecter à WordPress Admin
2. Aller dans le menu **Simple CPT > Ajouter**
3. URL : `/wp-admin/admin.php?page=simple-cpt-add`

**Résultat attendu** :
- ✅ La page se charge sans erreur
- ✅ Le formulaire s'affiche avec les onglets
- ✅ L'onglet "General" est actif par défaut
- ✅ Les icônes Dashicons sont visibles
- ✅ Pas d'erreur dans la console (F12)

**Capture d'écran** : Prendre une capture de la page

---

### Test 2 : Navigation entre Onglets ✅

**Objectif** : Vérifier que la navigation fonctionne

**Étapes** :
1. Cliquer sur l'onglet "Post Type"
2. Cliquer sur l'onglet "Labels"
3. Cliquer sur l'onglet "Options"
4. Revenir sur "General"

**Résultat attendu** :
- ✅ L'onglet cliqué devient actif (bordure bleue)
- ✅ Le contenu change instantanément
- ✅ L'ancien onglet se désactive
- ✅ Pas de clignotement ou de bug visuel
- ✅ Transition fluide

**Vérifications** :
- La classe `active` est ajoutée au bon onglet
- Le panel correspondant s'affiche
- Les autres panels sont masqués

---

### Test 3 : Affichage des Champs ✅

**Objectif** : Vérifier que tous les champs sont présents

**Étapes** :
Parcourir chaque onglet et vérifier les champs :

#### Onglet General
- [ ] Function Name (input text)
- [ ] Text Domain (input text)

#### Onglet Post Type
- [ ] Post Type Key (input text, required)
- [ ] Name (Singular) (input text, required)
- [ ] Name (Plural) (input text, required)
- [ ] Description (input text)

#### Onglet Labels
- [ ] Menu Name
- [ ] Add New
- [ ] Add New Item
- [ ] Edit Item
- [ ] New Item
- [ ] View Item
- [ ] View Items
- [ ] Search Items

#### Onglet Options
- [ ] Supports (11 checkboxes)
- [ ] Exclude From Search (select)
- [ ] Enable Export (select)
- [ ] Enable Archives (select)
- [ ] Custom Archive Slug (input)

#### Onglet Visibility
- [ ] Public (select)
- [ ] Show UI (select)
- [ ] Show in Admin Bar (select)
- [ ] Show in Navigation Menus (select)
- [ ] Admin Sidebar Icon (input)
- [ ] Show in Admin Sidebar (select)

#### Onglet Permalinks
- [ ] Permalink Rewrite (select)
- [ ] URL Slug (input)

#### Onglet Capabilities
- [ ] Base Capability Type (select)

#### Onglet Rest API
- [ ] Show in Rest (select)
- [ ] Rest Base (input)

**Résultat attendu** :
- ✅ Tous les champs sont présents
- ✅ Les labels sont corrects
- ✅ Les placeholders sont visibles
- ✅ Les textes d'aide s'affichent

---

### Test 4 : Validation des Champs ✅

**Objectif** : Vérifier la validation HTML5

**Étapes** :
1. Aller sur l'onglet "Post Type"
2. Laisser les champs vides
3. Cliquer sur "Créer le post type"

**Résultat attendu** :
- ✅ Message de validation HTML5 : "Veuillez remplir ce champ"
- ✅ Focus automatique sur le premier champ requis
- ✅ Le formulaire ne se soumet pas

**Test du pattern** :
1. Remplir "Post Type Key" avec "Mon Post Type" (avec espaces)
2. Tenter de soumettre

**Résultat attendu** :
- ✅ Message : "Veuillez respecter le format demandé"
- ✅ Le formulaire ne se soumet pas

---

### Test 5 : Soumission du Formulaire ✅

**Objectif** : Créer un post type complet

**Étapes** :
1. Aller sur "Post Type"
2. Remplir :
   - Post Type Key : `produit`
   - Name (Singular) : `Produit`
   - Name (Plural) : `Produits`
   - Description : `Gestion des produits`
3. Aller sur "Options"
4. Cocher : Title, Editor, Featured Image
5. Aller sur "Visibility"
6. Remplir Admin Sidebar Icon : `dashicons-products`
7. Cliquer sur "Créer le post type"

**Résultat attendu** :
- ✅ Le bouton affiche "Enregistrement..."
- ✅ Le bouton est désactivé pendant la soumission
- ✅ Message de succès s'affiche
- ✅ Redirection vers `/wp-admin/admin.php?page=simple-cpt` après 1 seconde
- ✅ Le nouveau post type apparaît dans la liste

**Vérifications dans la console** :
- Requête AJAX vers `admin-ajax.php`
- Action : `scpt_save_post_type`
- Réponse : `{success: true, data: {...}}`

---

### Test 6 : Responsive Design ✅

**Objectif** : Vérifier l'affichage sur différentes tailles d'écran

#### Desktop (> 1200px)
**Étapes** :
1. Ouvrir DevTools (F12)
2. Définir la largeur à 1400px

**Résultat attendu** :
- ✅ Grille à 3 colonnes
- ✅ Tous les onglets visibles
- ✅ Pas de scroll horizontal

#### Tablet (782px - 1200px)
**Étapes** :
1. Définir la largeur à 900px

**Résultat attendu** :
- ✅ Grille à 2 colonnes
- ✅ Onglets visibles ou scrollables
- ✅ Mise en page adaptée

#### Mobile (< 782px)
**Étapes** :
1. Définir la largeur à 375px (iPhone)

**Résultat attendu** :
- ✅ Grille à 1 colonne
- ✅ Onglets en scroll horizontal
- ✅ Champs en pleine largeur
- ✅ Boutons empilés verticalement

---

### Test 7 : Compatibilité Navigateurs ✅

**Objectif** : Vérifier sur différents navigateurs

**Navigateurs à tester** :
- [ ] Chrome (dernière version)
- [ ] Firefox (dernière version)
- [ ] Safari (dernière version)
- [ ] Edge (dernière version)

**Pour chaque navigateur** :
1. Ouvrir la page d'ajout
2. Naviguer entre les onglets
3. Remplir et soumettre le formulaire

**Résultat attendu** :
- ✅ Affichage identique
- ✅ Fonctionnalités identiques
- ✅ Pas d'erreur console

---

### Test 8 : Accessibilité ✅

**Objectif** : Vérifier l'accessibilité

#### Navigation au clavier
**Étapes** :
1. Utiliser uniquement le clavier (Tab, Enter, Espace)
2. Naviguer entre les onglets
3. Remplir les champs
4. Soumettre le formulaire

**Résultat attendu** :
- ✅ Tous les éléments sont accessibles au clavier
- ✅ L'ordre de tabulation est logique
- ✅ Le focus est visible
- ✅ Les onglets peuvent être activés avec Enter/Espace

#### Contraste des couleurs
**Étapes** :
1. Utiliser un outil de vérification de contraste
2. Vérifier les textes sur les fonds

**Résultat attendu** :
- ✅ Ratio de contraste ≥ 4.5:1 pour le texte normal
- ✅ Ratio de contraste ≥ 3:1 pour le texte large

---

### Test 9 : Performance ✅

**Objectif** : Vérifier les performances

**Étapes** :
1. Ouvrir DevTools > Network
2. Recharger la page
3. Noter les temps de chargement

**Résultat attendu** :
- ✅ Chargement de la page < 2 secondes
- ✅ Taille des assets raisonnable
  - admin.js < 50 KB
  - admin.css < 30 KB
- ✅ Pas de ressources bloquantes

---

### Test 10 : Gestion des Erreurs ✅

**Objectif** : Vérifier la gestion des erreurs

#### Erreur de validation serveur
**Étapes** :
1. Remplir le formulaire avec un slug réservé : `post`
2. Soumettre

**Résultat attendu** :
- ✅ Message d'erreur s'affiche
- ✅ Le bouton se réactive
- ✅ Les données du formulaire sont conservées

#### Erreur réseau
**Étapes** :
1. Ouvrir DevTools > Network
2. Activer "Offline"
3. Soumettre le formulaire

**Résultat attendu** :
- ✅ Message d'erreur générique
- ✅ Le bouton se réactive
- ✅ Pas de crash de la page

---

## 📊 Checklist Complète

### Affichage
- [ ] Page se charge correctement
- [ ] Onglets visibles
- [ ] Icônes affichées
- [ ] Pas d'erreur console

### Navigation
- [ ] Clic sur onglet fonctionne
- [ ] Onglet actif visuellement distinct
- [ ] Contenu change correctement
- [ ] Transition fluide

### Formulaire
- [ ] Tous les champs présents
- [ ] Labels corrects
- [ ] Placeholders visibles
- [ ] Textes d'aide affichés

### Validation
- [ ] Champs requis validés
- [ ] Pattern validé
- [ ] Messages d'erreur clairs

### Soumission
- [ ] Bouton désactivé pendant envoi
- [ ] Message de succès
- [ ] Redirection fonctionne
- [ ] Données sauvegardées

### Responsive
- [ ] Desktop (3 colonnes)
- [ ] Tablet (2 colonnes)
- [ ] Mobile (1 colonne)

### Navigateurs
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge

### Accessibilité
- [ ] Navigation clavier
- [ ] Focus visible
- [ ] Contraste suffisant

### Performance
- [ ] Chargement rapide
- [ ] Assets optimisés

### Erreurs
- [ ] Validation serveur
- [ ] Erreurs réseau
- [ ] Messages clairs

---

## 🐛 Bugs Potentiels à Surveiller

1. **Onglets ne changent pas** → Vérifier les événements jQuery
2. **Formulaire vide** → Vérifier `renderAddForm()` est appelé
3. **Erreur 500** → Vérifier le handler AJAX PHP
4. **Checkboxes non sauvegardées** → Vérifier le traitement des supports
5. **Pas de redirection** → Vérifier le code JavaScript après succès

---

## 📝 Rapport de Test

### Informations
- **Date** : _______________
- **Testeur** : _______________
- **Navigateur** : _______________
- **Version WordPress** : _______________

### Résultats
- Tests réussis : ___ / 10
- Tests échoués : ___ / 10
- Bugs trouvés : ___

### Bugs Identifiés
1. _______________________________________________
2. _______________________________________________
3. _______________________________________________

### Recommandations
_______________________________________________
_______________________________________________
_______________________________________________

---

**✅ Tests terminés avec succès !**

Made with ❤️ by InfinityWeb
