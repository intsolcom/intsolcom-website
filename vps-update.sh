#!/bin/bash
# ============================================================
# INTSOLCOM — VPS update (Contabo: nginx + PHP-FPM + MySQL)
# Run on the server:  bash vps-update.sh
# ============================================================
set -e

APP_DIR="/var/www/intsolcom"

echo "========================================"
echo " INTSOLCOM - VPS Update"
echo "========================================"

if [ ! -d "$APP_DIR/.git" ]; then
    echo "[!] $APP_DIR is not a git repo."
    echo "    Initial clone first:"
    echo "    sudo git clone https://github.com/intsolcom/intsolcom-website.git /var/www/intsolcom"
    exit 1
fi

echo "[1/4] Pulling latest code..."
cd "$APP_DIR"
git pull origin master

echo "[2/4] Applying live data fixes (idempotent)..."
sudo mysql intsolcom < fix-live-data.sql || {
    echo "[!] MySQL step failed — verify DB name and credentials."
    exit 1
}

echo "[3/4] Reloading PHP-FPM (clears OPcache)..."
sudo systemctl reload php8.3-fpm || sudo systemctl reload php-fpm

echo "[4/4] Done!"
echo "       Hard-refresh the site: Ctrl+F5"
echo "       Check menu + products at https://intsolcom.com"
echo "========================================"
