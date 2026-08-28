# INTSOLCOM LLC — Corporate Website

Technology Holding website built with vanilla PHP CMS + Node.js server.

## Quick Start

```bash
# Option 1: Node.js (instant, no DB)
node Sitio\ Web/server.js
# → http://localhost:3000

# Option 2: Docker
docker compose up -d
# → http://localhost:3000

# Option 3: Windows one-click
start.bat
```

## Architecture

```
intsolcom-website/
├── Sitio Web/           # Web root
│   ├── server.js        # Node.js server (zero deps)
│   ├── index.php        # Homepage (10 sections)
│   ├── _other PHP pages # Holding, Technology, Contact, etc.
│   ├── admin/           # CMS admin panel
│   ├── assets/          # CSS (2,354 lines) + JS (540 lines)
│   ├── includes/        # Config, DB installer, i18n
│   └── .htaccess        # Apache routing
├── Dockerfile           # Node 24 Alpine
├── docker-compose.yml   # Local dev
├── docker-compose.prod.yml  # Production (Traefik + SSL)
├── dokploy.json         # Dokploy config
└── start.bat            # Windows launcher
```

## Design

| Token | Value |
|-------|-------|
| Primary | `#00C896` (mint green) |
| Dark | `#0F172A` (navy) |
| Secondary | `#2563EB` (blue) |
| Accent | `#8B5CF6` (purple) |
| Font | Inter 300-800 |
| Inspired by | Stripe, Vercel, OpenAI, Linear |

## Deploy to intsolcom.com

Production: **Contabo VPS** (nginx + PHP-FPM + MySQL + certbot). Hostinger is out of the equation.

### Initial VPS setup

```bash
# 1. Clone the repo
cd /var/www && git clone https://github.com/intsolcom/intsolcom-website.git intsolcom
cd intsolcom && git pull origin master

# 2. PHP + MySQL
sudo apt install nginx php8.3-fpm php8.3-mysql php8.3-curl php8.3-gd php8.3-mbstring php8.3-xml mysql-server

# 3. Database (match includes/config.php)
sudo mysql <<'SQL'
CREATE DATABASE IF NOT EXISTS intsolcom CHARACTER SET utf8mb4;
CREATE USER IF NOT EXISTS 'intsolcom'@'localhost' IDENTIFIED BY 'AHCgbDRqohTZJ=@+4-W2cLPi';
GRANT ALL PRIVILEGES ON intsolcom.* TO 'intsolcom'@'localhost';
FLUSH PRIVILEGES;
SQL

# 4. Import schema + data (from Hostinger dump or run installer once)
#    Run https://intsolcom.com/includes/db-install.php ONCE, then DELETE db-install.php

# 5. nginx site config
sudo cp nginx-site.conf /etc/nginx/sites-available/intsolcom
sudo ln -s /etc/nginx/sites-available/intsolcom /etc/nginx/sites-enabled/intsolcom
sudo nginx -t && sudo systemctl reload nginx

# 6. SSL
sudo certbot --nginx -d intsolcom.com -d www.intsolcom.com
```

### Every deploy (VPS)

```bash
cd /var/www/intsolcom
git pull origin master
sudo systemctl reload php8.3-fpm   # clears OPcache (or: php -r 'opcache_reset();')
```

### Via Dokploy (Node preview)

1. Connect Dokploy to this GitHub repo
2. Set domains: `intsolcom.com`, `www.intsolcom.com`
3. Deploy — Dokploy reads `dokploy.json` automatically

## Pages

- `/` — Home (hero, ecosystem, products, capabilities, industries, comparison, testimonials, FAQ)
- `/holding` — Corporate structure, mission, vision, governance
- `/business-units` — Business unit cards
- `/technology` — Product portfolio (Wontia AIP, MACROPONDER, IA Annotation Manager)
- `/industries` — 10 industry sectors
- `/resources` — Articles, whitepapers, guides
- `/contact` — Minimalist form + office locations

## Tech Stack

- **PHP**: Vanilla CMS with MySQL (same architecture as MARCAS BPO)
- **Node.js**: Standalone server with in-memory asset cache, gzip, ETags
- **Docker**: Multi-stage, non-root user, healthcheck
- **Zero npm dependencies** — Node.js built-in modules only

## License

Proprietary — INTSOLCOM LLC. All rights reserved.
