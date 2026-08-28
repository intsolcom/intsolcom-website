# INTSOLCOM — Security Checklist (Rotación de Credenciales)

Este documento es el protocolo a seguir tras cualquier exposición de secretos
y para mantener el sitio endurecido. Los secretos generados deben tratarse
como información crítica: nunca commitearlos, nunca compartirlos por chat.

## 1. Rotación inmediata (pendiente tras la auditoría)

| # | Secreto | Dónde se rota | Cómo |
|---|---------|---------------|------|
| 1 | **GitHub PAT** (`gho_...` expuesto en remote URL y logs) | github.com → Settings → Developer settings → Personal access tokens | Revocar el token viejo. Crear uno nuevo con scope `repo` únicamente. Actualizar el remote local: `git remote set-url origin https://github.com/intsolcom/intsolcom-website.git` y autenticar con Git Credential Manager. |
| 2 | **Contraseña MySQL** (estuvo en `config.example.php` del historial git) | VPS Contabo → MySQL | Generar con `node scripts/generate-secrets.js`. Aplicar en el VPS: `ALTER USER 'intsolcom'@'localhost' IDENTIFIED BY 'NUEVA';` y actualizar `includes/config.php`. |
| 3 | **BYT_ENCRYPTION_KEY** (estuvo en el historial git) | `includes/config.php` | Generar nueva (64 hex) con el script. OJO: los tokens ya cifrados en `distro_tokens` quedarán ilegibles → re-guardarlos desde el admin. |
| 4 | **ANTHROPIC_API_KEY / DEEPSEEK_API_KEY** | Consolas de Anthropic y DeepSeek | Revocar y crear nuevas. Actualizar `config.php`. |
| 5 | **Contraseña admin** (por defecto fue publicada) | `includes/config.php` | Generar hash en el servidor: `php -r "echo password_hash('...', PASSWORD_BCRYPT);"` y definir `ADMIN_PASS_HASH`. Borrar `ADMIN_PASS` si se prefiere. |
| 6 | **Tokens de LinkedIn/redes** (guardados en texto plano antes del fix) | Admin → Distro → guardar token de nuevo | Ahora se cifran con `bytEncrypt`. Los viejos en texto plano se sobrescriben. |

## 2. Servidor (Contabo VPS — nginx + PHP-FPM + MySQL)

- [ ] Borrar `includes/db-install.php` del servidor (tiene guard, pero debe eliminarse igual).
- [ ] Desplegar `nginx-site.conf` en `/etc/nginx/sites-available/intsolcom` (headers, bloqueos de archivos sensibles, PHP-FPM). nginx IGNORA `.htaccess` — los headers deben vivir aquí.
- [ ] `sudo nginx -t && sudo systemctl reload nginx` tras cada cambio.
- [ ] Limpiar OPcache tras cambios PHP: `sudo systemctl reload php8.3-fpm` (o `php -r "opcache_reset();"`).
- [ ] Comprobar headers: `curl -I https://intsolcom.com` → deben aparecer CSP, HSTS, nosniff, X-Frame-Options, Referrer-Policy.
- [ ] MySQL: usuario `intsolcom` solo con permisos sobre la BD `intsolcom` (nada de GRANT ALL global).

## 3. Repositorio

- [ ] Decidir visibilidad: si `intsolcom/intsolcom-website` es público, el historial git contiene credenciales viejas → hacerlo **privado** o reescribir historial (requiere force-push, coordinar antes).
- [ ] No commitear nunca `config.php` (gitignoreado), `.env`, tokens ni claves.
- [ ] `config.example.php` debe contener SOLO placeholders.

## 4. Verificaciones periódicas

- `node --check "Sitio Web/server.js"` y `node --check "Sitio Web/assets/js/main.js"` tras cambios JS.
- Revisar `git status` antes de cada commit para no incluir secretos.
- `node scripts/generate-secrets.js` para generar valores seguros.
