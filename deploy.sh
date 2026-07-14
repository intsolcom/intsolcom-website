#!/bin/bash
set -e

echo "========================================"
echo " INTSOLCOM LLC - Website Deployment"
echo "========================================"

APP_DIR="/var/www/intsolcom"
REPO="https://github.com/intsolcom/intsolcom-website.git"
BRANCH="master"

# Clone or pull
if [ -d "$APP_DIR/.git" ]; then
    echo "[1/4] Pulling latest code..."
    cd "$APP_DIR"
    git pull origin "$BRANCH"
else
    echo "[1/4] Cloning repository..."
    git clone --depth 1 --branch "$BRANCH" "$REPO" "$APP_DIR"
    cd "$APP_DIR"
fi

# Install deps (none needed for Node.js server)
echo "[2/4] No dependencies to install (pure Node.js)"

# Restart server with PM2
echo "[3/4] Restarting server..."
if command -v pm2 &> /dev/null; then
    pm2 restart intsolcom-website || pm2 start "Sitio Web/server.js" --name intsolcom-website
    pm2 save
else
    echo "PM2 not found. Installing..."
    npm install -g pm2
    pm2 start "Sitio Web/server.js" --name intsolcom-website
    pm2 save
    pm2 startup
fi

echo "[4/4] Deployment complete!"
echo "       https://intsolcom.com"
echo "========================================"
