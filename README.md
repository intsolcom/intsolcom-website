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

### Via Dokploy

1. Connect Dokploy to this GitHub repo
2. Set domains: `intsolcom.com`, `www.intsolcom.com`
3. Deploy — Dokploy reads `dokploy.json` automatically

### Via Docker + Traefik

```bash
docker compose -f docker-compose.prod.yml up -d
```

### Via Hostinger (PHP + Apache)

1. Upload `Sitio Web/` contents to `public_html/`
2. Copy `includes/config.example.php` to `includes/config.php` and update DB credentials
3. Run `https://intsolcom.com/includes/db-install.php` once
4. Delete `db-install.php`
5. Login at `/admin` (admin / IntsolcomAdmin2026!)

## Pages

- `/` — Home (hero, ecosystem, products, capabilities, industries, comparison, testimonials, FAQ)
- `/holding` — Corporate structure, mission, vision, governance
- `/business-units` — Business unit cards
- `/technology` — Product portfolio (WONTIA, MACROPONDER, IA Annotation Manager)
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
