# Agent Instructions — INTSOLCOM Website

## Entorno de producción

- Producción: **Contabo VPS** — nginx + PHP-FPM + MySQL (páginas servidas por PHP). Hostinger está fuera.
- nginx IGNORA `.htaccess`: los headers de seguridad y bloqueos viven en `nginx-site.conf` (mantener sincronizado con `.htaccess`).
- Despliegue de cambios: `git pull` en `/var/www/intsolcom` + `sudo systemctl reload php8.3-fpm` (OPcache).
- El Node (`server.js`) es versión preview/dev (start.bat o Dokploy), no la producción.

## Git / GitHub

- Después de hacer commit, SIEMPRE ejecutar `git push` al repositorio remoto de GitHub (`origin`, rama `master`). El usuario lo pidió explícitamente.
- Nunca commitear secretos: `Sitio Web/includes/config.php` contiene credenciales de BD y API keys.

## Reglas de desarrollo (ver también ADMIN_GUIDELINES.md)

- Nunca inyectar JS crudo en PHP sin etiquetas `<script>`.
- Emojis en JS como escapes Unicode (`\u{1F310}`).
- El logo del sitio debe ser CSS-driven (clases `.nav__logo-text` / `.nav__logo-accent`), nunca colores inline desde settings.
- Páginas con hero oscuro usan `nav--transparent`; páginas con top claro usan `nav` simple.
- El tema del nav se auto-corrige en runtime vía `autoNavTheme()` en main.js (detecta si el primer bloque es oscuro). No quitar esa lógica.
- El menú móvil se togglea con clases `open` y `active` (JS) — mantener ambas en CSS.
- Wontia AIP = Applied Intelligence System (no CRM). INTSOLCOM solo da intro y linkea a wontia.com.
- IA Annotation Manager linkea a iaam.com.

## Seguridad (reglas duras)

- Protocolo de rotación de credenciales y checklist de servidor: ver `SECURITY.md`.
- Nunca commitear secretos: `config.php` (gitignoreado), API keys, tokens, `BYT_ENCRYPTION_KEY`. El ejemplo (`config.example.php`) solo con placeholders.
- Admin: todo POST requiere header `X-CSRF-Token` (token en meta `csrf-token`, wrapper global de fetch). Login con rate-limit (5 fallos = 15 min lock) y `session_regenerate_id`.
- Uploads: solo jpg/png/gif/webp/ico con MIME real (finfo); nunca SVG; fallback corrupto = error (no mover original). Tokens de redes (`distro_tokens`) SIEMPRE cifrados con `bytEncrypt`.
- Headers: CSP, HSTS, nosniff, X-Frame-Options, Permissions-Policy vía `.htaccess` (Apache) y `SEC_HEADERS` (server.js). Mantener ambos al agregar dominios/scripts.
- `assets/uploads/.htaccess` debe existir (PHP desactivado). `db-install.php` tiene guard anti-re-ejecución.
- Si se sube la web por otro medio (FTP/panel), verificar que `config.php` y `.htaccess` (incluido el de uploads) lleguen al servidor.

## Verificación

- `node --check "Sitio Web\server.js"` y `node --check "Sitio Web\assets\js\main.js"` después de cambiar JS.
- PHP CLI no está instalado en la máquina local; validar sintaxis PHP manualmente o en el servidor.
