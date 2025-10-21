# Guide de contribution

Merci de votre intérêt pour contribuer à Simple Custom Post Type !

## 🚀 Comment contribuer

### Signaler un bug

1. Vérifier que le bug n'a pas déjà été signalé
2. Créer une issue avec :
   - Description claire du problème
   - Étapes pour reproduire
   - Comportement attendu vs observé
   - Version de WordPress et PHP
   - Captures d'écran si pertinent

### Proposer une fonctionnalité

1. Créer une issue pour discuter de la fonctionnalité
2. Attendre l'approbation avant de commencer le développement
3. Suivre les standards de code du projet

### Soumettre une Pull Request

1. Fork le projet
2. Créer une branche depuis `develop` :
   ```bash
   git checkout -b feature/ma-fonctionnalite
   ```
3. Faire vos modifications
4. Ajouter des tests si nécessaire
5. Vérifier le code :
   ```bash
   composer phpcs
   composer test
   ```
6. Commit avec un message descriptif :
   ```bash
   git commit -m "feat: ajouter la fonctionnalité X"
   ```
7. Push vers votre fork :
   ```bash
   git push origin feature/ma-fonctionnalite
   ```
8. Ouvrir une Pull Request vers `develop`

## 📋 Standards de code

### PHP

- Suivre les [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)
- Utiliser PSR-4 pour l'autoloading
- Documenter avec PHPDoc
- Typage strict quand possible
- Nommer les variables en `snake_case`
- Nommer les méthodes en `camelCase`
- Nommer les classes en `PascalCase`

### JavaScript

- Suivre les [WordPress JavaScript Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/)
- Utiliser ES6+ quand possible
- Documenter avec JSDoc

### CSS

- Suivre les [WordPress CSS Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/css/)
- Utiliser des variables CSS
- Mobile-first approach
- Préfixer les classes avec `scpt-`

## 🧪 Tests

### Tests unitaires

```bash
# Installer les dépendances
composer install

# Lancer les tests
composer test

# Avec couverture
composer test -- --coverage-html coverage/
```

### Tests de code

```bash
# Vérifier le code
composer phpcs

# Corriger automatiquement
composer phpcbf
```

## 📝 Conventions de commit

Utiliser le format [Conventional Commits](https://www.conventionalcommits.org/) :

- `feat:` Nouvelle fonctionnalité
- `fix:` Correction de bug
- `docs:` Documentation
- `style:` Formatage (sans changement de code)
- `refactor:` Refactoring
- `test:` Ajout/modification de tests
- `chore:` Maintenance

Exemples :
```
feat: ajouter l'export de configurations
fix: corriger la validation du slug
docs: mettre à jour le README
```

## 🔒 Sécurité

Si vous découvrez une vulnérabilité de sécurité :

1. **NE PAS** créer d'issue publique
2. Envoyer un email à : contact@infinityweb.tn
3. Inclure une description détaillée
4. Attendre une réponse avant de divulguer

## 📖 Documentation

- Documenter toutes les fonctions publiques
- Ajouter des exemples d'utilisation
- Mettre à jour le README si nécessaire
- Documenter les breaking changes dans CHANGELOG.md

## ✅ Checklist PR

Avant de soumettre une PR, vérifier :

- [ ] Le code suit les standards du projet
- [ ] Les tests passent
- [ ] La documentation est à jour
- [ ] Les commits suivent les conventions
- [ ] Pas de conflits avec `develop`
- [ ] La PR a une description claire
- [ ] Les changements sont testés manuellement

## 🤝 Code de conduite

- Être respectueux et professionnel
- Accepter les critiques constructives
- Se concentrer sur ce qui est mieux pour le projet
- Faire preuve d'empathie envers les autres contributeurs

## 📞 Questions

Pour toute question :
- Créer une issue avec le label `question`
- Contacter : contact@infinityweb.tn

Merci de contribuer à Simple Custom Post Type ! 🎉
