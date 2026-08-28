#!/bin/bash
# ============================================================
# Admin integrity check — run before any deploy
# Ensures no raw JS leaks as visible text in dashboards
# ============================================================
set -e

echo "🔍 Checking admin panels for raw JS leaks..."

check_file() {
    local file=$1
    local label=$2
    
    # Check for JS code NOT inside script tags
    # A proper admin file should have matching <script> and </script> around all JS
    
    local script_opens=$(grep -c '<script' "$file" 2>/dev/null || echo 0)
    local script_closes=$(grep -c '</script>' "$file" 2>/dev/null || echo 0)
    
    if [ "$script_opens" != "$script_closes" ]; then
        echo "❌ $label: Mismatched script tags (opens: $script_opens, closes: $script_closes)"
        return 1
    fi
    
    # Check for common patterns that indicate raw JS in HTML
    if grep -Pn '(?<!<script>)let sched|const sched|function sched' "$file" 2>/dev/null; then
        echo "❌ $label: Raw JS found outside script context"
        return 1
    fi
    
    # Check for broken emoji patterns
    if grep -Pn '\?\\uFFFD|�' "$file" 2>/dev/null; then
        echo "⚠️  $label: Corrupted Unicode characters found"
        return 1
    fi
    
    echo "✅ $label: Clean"
    return 0
}

# Check local files
if [ -f "Sitio Web/admin/index.php" ]; then
    check_file "Sitio Web/admin/index.php" "INTSOLCOM (local)"
fi

if [ -f "../MARCASBPO/Sitio Web/admin/index.php" ]; then
    check_file "../MARCASBPO/Sitio Web/admin/index.php" "Marcas BPO (local)"
fi

echo ""
echo "✅ All checks passed — safe to deploy"
