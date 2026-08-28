# INTSOLCOM Website — Deployment Guide

Producción: **Contabo VPS** (nginx + PHP-FPM + MySQL + certbot).
Hostinger está fuera de la ecuación.

## 1. Local (ya funciona)

```bash
cd "D:\INTSOLCOM\IA DEVELOPMENT\WEB INTSOLCOM"
start.bat
# → http://localhost:3000
```

## 2. GitHub

```bash
cd "D:\INTSOLCOM\IA DEVELOPMENT\WEB INTSOLCOM"
gh auth login
git push -u origin master
```

## 3. Contabo VPS — nginx + PHP-FPM (producción)

```bash
# En el servidor
cd /var/www
git clone https://github.com/intsolcom/intsolcom-website.git intsolcom
cd intsolcom

# PHP + MySQL + nginx
sudo apt install nginx php8.3-fpm php8.3-mysql php8.3-curl php8.3-gd php8.3-mbstring php8.3-xml mysql-server certbot python3-certbot-nginx

# Base de datos (coincide con includes/config.php)
sudo mysql <<'SQL'
CREATE DATABASE IF NOT EXISTS intsolcom CHARACTER SET utf8mb4;
CREATE USER IF NOT EXISTS 'intsolcom'@'localhost' IDENTIFIED BY 'AHCgbDRqohTZJ=@+4-W2cLPi';
GRANT ALL PRIVILEGES ON intsolcom.* TO 'intsolcom'@'localhost';
FLUSH PRIVILEGES;
SQL

# Schema + seed: ejecutar UNA vez https://intsolcom.com/includes/db-install.php
# (o importar dump de la BD anterior), luego ELIMINAR db-install.php

# nginx (los headers de seguridad viven aquí — nginx ignora .htaccess)
sudo cp nginx-site.conf /etc/nginx/sites-available/intsolcom
sudo ln -s /etc/nginx/sites-available/intsolcom /etc/nginx/sites-enabled/intsolcom
sudo nginx -t && sudo systemctl reload nginx

# SSL
sudo certbot --nginx -d intsolcom.com -d www.intsolcom.com
```

### Despliegue de cambios (cada vez)

```bash
cd /var/www/intsolcom
git pull origin master
sudo systemctl reload php8.3-fpm   # limpia OPcache
```

## 4. Dokploy (opcional — preview Node)

1. En Dokploy: New Application → From GitHub
2. Selecciona `intsolcom/intsolcom-website`
3. Dokploy lee `dokploy.json` automáticamente
4. Dominios: `intsolcom.com`, `www.intsolcom.com`
5. SSL: Let's Encrypt automático
6. Deploy

## 5. Docker Manual (opcional)

```bash
git clone https://github.com/intsolcom/intsolcom-website.git
cd intsolcom-website
docker compose -f docker-compose.prod.yml up -d
```
