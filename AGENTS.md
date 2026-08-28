# Agent Instructions — INTSOLCOM Website

## Git / GitHub

- Después de hacer commit, SIEMPRE ejecutar `git push` al repositorio remoto de GitHub (`origin`, rama `master`). El usuario lo pidió explícitamente.
- Nunca commitear secretos: `Sitio Web/includes/config.php` contiene credenciales de BD y API keys.

## Reglas de desarrollo (ver también ADMIN_GUIDELINES.md)

- Nunca inyectar JS crudo en PHP sin etiquetas `<script>`.
- Emojis en JS como escapes Unicode (`\u{1F310}`).
- El logo del sitio debe ser CSS-driven (clases `.nav__logo-text` / `.nav__logo-accent`), nunca colores inline desde settings.
- Páginas con hero oscuro usan `nav--transparent`; páginas con top claro usan `nav` simple.
- El menú móvil se togglea con clases `open` y `active` (JS) — mantener ambas en CSS.
- Wontia AIP = Applied Intelligence System (no CRM). INTSOLCOM solo da intro y linkea a wontia.com.
- IA Annotation Manager linkea a iaam.com.

## Verificación

- `node --check "Sitio Web\server.js"` y `node --check "Sitio Web\assets\js\main.js"` después de cambiar JS.
- PHP CLI no está instalado en la máquina local; validar sintaxis PHP manualmente o en el servidor.
