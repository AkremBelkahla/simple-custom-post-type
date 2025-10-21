# Script PowerShell pour compiler les fichiers de traduction .po en .mo
# Usage: .\compile-translations.ps1

Write-Host "🌍 Compilation des fichiers de traduction..." -ForegroundColor Cyan
Write-Host ""

# Vérifier si msgfmt est disponible
$msgfmtPath = Get-Command msgfmt -ErrorAction SilentlyContinue

if (-not $msgfmtPath) {
    Write-Host "❌ Erreur: msgfmt n'est pas installé" -ForegroundColor Red
    Write-Host ""
    Write-Host "Pour installer msgfmt:" -ForegroundColor Yellow
    Write-Host "1. Télécharger gettext pour Windows: https://mlocati.github.io/articles/gettext-iconv-windows.html" -ForegroundColor Yellow
    Write-Host "2. Ou utiliser Poedit: https://poedit.net/" -ForegroundColor Yellow
    Write-Host "3. Ou utiliser WP-CLI: wp i18n make-mo ." -ForegroundColor Yellow
    exit 1
}

# Dossier des langues
$languagesDir = Join-Path $PSScriptRoot "languages"

# Vérifier si le dossier existe
if (-not (Test-Path $languagesDir)) {
    Write-Host "❌ Erreur: Le dossier languages/ n'existe pas" -ForegroundColor Red
    exit 1
}

# Trouver tous les fichiers .po
$poFiles = Get-ChildItem -Path $languagesDir -Filter "*.po"

if ($poFiles.Count -eq 0) {
    Write-Host "⚠️  Aucun fichier .po trouvé dans languages/" -ForegroundColor Yellow
    exit 0
}

$successCount = 0
$errorCount = 0

# Compiler chaque fichier .po
foreach ($poFile in $poFiles) {
    $moFile = $poFile.FullName -replace '\.po$', '.mo'
    $fileName = $poFile.Name
    
    Write-Host "📝 Compilation de $fileName..." -ForegroundColor White
    
    try {
        & msgfmt $poFile.FullName -o $moFile
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host "   ✅ $fileName → $(Split-Path $moFile -Leaf)" -ForegroundColor Green
            $successCount++
        } else {
            Write-Host "   ❌ Erreur lors de la compilation de $fileName" -ForegroundColor Red
            $errorCount++
        }
    } catch {
        Write-Host "   ❌ Exception: $_" -ForegroundColor Red
        $errorCount++
    }
}

Write-Host ""
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Cyan
Write-Host "📊 Résumé:" -ForegroundColor Cyan
Write-Host "   ✅ Succès: $successCount" -ForegroundColor Green
Write-Host "   ❌ Erreurs: $errorCount" -ForegroundColor $(if ($errorCount -gt 0) { "Red" } else { "Green" })
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Cyan
Write-Host ""

if ($successCount -gt 0) {
    Write-Host "🎉 Traductions compilées avec succès!" -ForegroundColor Green
} else {
    Write-Host "⚠️  Aucune traduction compilée" -ForegroundColor Yellow
}
