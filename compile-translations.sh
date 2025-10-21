#!/bin/bash
# Script Bash pour compiler les fichiers de traduction .po en .mo
# Usage: ./compile-translations.sh

echo "🌍 Compilation des fichiers de traduction..."
echo ""

# Vérifier si msgfmt est disponible
if ! command -v msgfmt &> /dev/null; then
    echo "❌ Erreur: msgfmt n'est pas installé"
    echo ""
    echo "Pour installer msgfmt:"
    echo "  Ubuntu/Debian: sudo apt-get install gettext"
    echo "  macOS: brew install gettext"
    echo "  Ou utiliser WP-CLI: wp i18n make-mo ."
    exit 1
fi

# Dossier des langues
LANGUAGES_DIR="$(dirname "$0")/languages"

# Vérifier si le dossier existe
if [ ! -d "$LANGUAGES_DIR" ]; then
    echo "❌ Erreur: Le dossier languages/ n'existe pas"
    exit 1
fi

# Trouver tous les fichiers .po
PO_FILES=("$LANGUAGES_DIR"/*.po)

if [ ! -e "${PO_FILES[0]}" ]; then
    echo "⚠️  Aucun fichier .po trouvé dans languages/"
    exit 0
fi

SUCCESS_COUNT=0
ERROR_COUNT=0

# Compiler chaque fichier .po
for PO_FILE in "${PO_FILES[@]}"; do
    if [ -f "$PO_FILE" ]; then
        MO_FILE="${PO_FILE%.po}.mo"
        FILE_NAME=$(basename "$PO_FILE")
        
        echo "📝 Compilation de $FILE_NAME..."
        
        if msgfmt "$PO_FILE" -o "$MO_FILE"; then
            echo "   ✅ $FILE_NAME → $(basename "$MO_FILE")"
            ((SUCCESS_COUNT++))
        else
            echo "   ❌ Erreur lors de la compilation de $FILE_NAME"
            ((ERROR_COUNT++))
        fi
    fi
done

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📊 Résumé:"
echo "   ✅ Succès: $SUCCESS_COUNT"
echo "   ❌ Erreurs: $ERROR_COUNT"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

if [ $SUCCESS_COUNT -gt 0 ]; then
    echo "🎉 Traductions compilées avec succès!"
else
    echo "⚠️  Aucune traduction compilée"
fi
