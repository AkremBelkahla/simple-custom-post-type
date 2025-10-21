# Test de la page d'ajout de post type

## Problème résolu
La page `/wp-admin/admin.php?page=simple-cpt-add` était vide car le JavaScript ne générait pas le formulaire dans le conteneur `#scpt-add-root`.

## Modifications apportées

### 1. JavaScript (`assets/js/admin.js`)
- ✅ Ajout de la méthode `renderAddForm()` pour générer le formulaire d'ajout
- ✅ Appel de `renderAddForm()` dans `init()`
- ✅ Amélioration de `savePostType()` pour gérer correctement les données du formulaire
- ✅ Traitement spécial des checkboxes `supports[]`
- ✅ Redirection automatique vers la liste après succès

### 2. CSS (`assets/css/admin.css`)
- ✅ Ajout de styles pour `.scpt-form` et `.scpt-form-section`
- ✅ Styles pour `.scpt-form-group` et `.scpt-input`
- ✅ Styles pour `.scpt-form-actions` et `.scpt-help-text`
- ✅ Amélioration de la mise en page du formulaire

### 3. PHP (`includes/Core/Plugin.php`)
- ✅ Correction de `ajax_save_post_type()` pour accepter les données POST au lieu de JSON
- ✅ Nettoyage des données `action` et `nonce` avant validation

## Comment tester

1. **Accéder à la page d'ajout**
   - Aller dans WordPress Admin
   - Menu : Simple CPT > Ajouter
   - URL : `/wp-admin/admin.php?page=simple-cpt-add`

2. **Vérifier l'affichage du formulaire**
   - Le formulaire doit s'afficher avec :
     - Section "Informations de base" (slug, nom pluriel, nom singulier)
     - Section "Options" (description, checkboxes, icône, position)
     - Boutons "Créer le post type" et "Annuler"

3. **Tester la création d'un post type**
   - Remplir les champs obligatoires :
     - Slug : `mon_article`
     - Nom (pluriel) : `Mes Articles`
     - Nom (singulier) : `Mon Article`
   - Cocher quelques options (titre, éditeur, etc.)
   - Cliquer sur "Créer le post type"
   - Vérifier :
     - Message de succès
     - Redirection vers la liste des post types
     - Le nouveau post type apparaît dans la liste

4. **Vérifier la console du navigateur**
   - Ouvrir les DevTools (F12)
   - Vérifier qu'il n'y a pas d'erreurs JavaScript
   - Vérifier la requête AJAX dans l'onglet Network

## Données envoyées au serveur

Le formulaire envoie les données suivantes :
```
action: scpt_save_post_type
nonce: [nonce]
slug: mon_article
labels[name]: Mes Articles
labels[singular_name]: Mon Article
description: Description optionnelle
public: 1
has_archive: 1
supports[0]: title
supports[1]: editor
supports[2]: thumbnail
menu_icon: dashicons-admin-post
menu_position: 5
```

## Validation côté serveur

Le validateur (`Utils\Validator`) vérifie :
- ✅ Slug requis et valide (max 20 caractères)
- ✅ Slug non réservé par WordPress
- ✅ Nom et nom singulier requis
- ✅ Sanitization de tous les champs
- ✅ Conversion des booléens
- ✅ Validation des supports

## Points à vérifier

- [ ] Le formulaire s'affiche correctement
- [ ] Les champs obligatoires sont marqués avec *
- [ ] La validation HTML fonctionne (pattern, required)
- [ ] Le bouton se désactive pendant la soumission
- [ ] Les messages d'erreur s'affichent correctement
- [ ] La redirection fonctionne après succès
- [ ] Le post type est bien enregistré dans la base de données
- [ ] Les logs sont créés (si activés)

## Dépannage

### Le formulaire ne s'affiche pas
- Vérifier que le fichier `admin.js` est bien chargé
- Vérifier la console pour des erreurs JavaScript
- Vérifier que le conteneur `#scpt-add-root` existe dans le HTML

### Erreur lors de la soumission
- Vérifier le nonce dans la console Network
- Vérifier les permissions utilisateur
- Vérifier les logs WordPress (si WP_DEBUG activé)
- Vérifier la réponse AJAX dans la console Network

### Les données ne sont pas sauvegardées
- Vérifier que le Manager existe et fonctionne
- Vérifier les logs du plugin
- Vérifier la base de données (table `wp_options` pour `scpt_post_types`)
