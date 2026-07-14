# INTSOLCOM Website — Deployment Guide

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

## 3. Dokploy (recomendado)

1. En Dokploy: New Application → From GitHub
2. Selecciona `intsolcom/intsolcom-website`
3. Dokploy lee `dokploy.json` automaticamente
4. Dominios: `intsolcom.com`, `www.intsolcom.com`
5. SSL: Let's Encrypt automatico
6. Deploy

## 4. Docker Manual

```bash
# En el servidor VPS
git clone https://github.com/intsolcom/intsolcom-website.git
cd intsolcom-website
docker compose -f docker-compose.prod.yml up -d
```

## 5. PM2 + Nginx

```bash
# En el servidor
git clone https://github.com/intsolcom/intsolcom-website.git /var/www/intsolcom
cd /var/www/intsolcom
npm install -g pm2
pm2 start "Sitio Web/server.js" --name intsolcom-website
pm2 save && pm2 startup

# Nginx config:
# server {
#     listen 80;
#     server_name intsolcom.com www.intsolcom.com;
#     location / {
#         proxy_pass http://localhost:3000;
#         proxy_http_version 1.1;
#         proxy_set_header Upgrade $http_upgrade;
#         proxy_set_header Connection 'upgrade';
#         proxy_set_header Host $host;
#         proxy_cache_bypass $http_upgrade;
#     }
# }
```

## 6. Hostinger (PHP + MySQL)

```bash
# 1. FTP: Subir contenido de Sitio Web/ a public_html/
# 2. Renombrar includes/config.example.php → config.php
# 3. Editar credenciales MySQL en config.php
# 4. Navegador: https://intsolcom.com/includes/db-install.php
# 5. ELIMINAR db-install.php del servidor
# 6. Admin: https://intsolcom.com/admin
#    User: admin / Pass: IntsolcomAdmin2026!
```
