# CMS INNOVATIONS — INTSOLCOM Content Manager

Requisito: TODO el contenido del sitio administrable desde el dashboard.
Estas 30 innovaciones convierten el CMS en uno de los más potentes del mundo.
Estado: ✅ implementado · 🔜 roadmap inmediato

## Arquitectura
1. ✅ **Colecciones universales** — tabla `cms_collections` con JSON: cualquier bloque del sitio (capacidades, FAQs, comparativa, stats, CTA, ecosistema, pasos SDD, servicios) es editable sin tocar código.
2. ✅ **Fallback a prueba de fallos** — cada página lee el CMS; si una colección no existe, usa el contenido por defecto del código. El sitio NUNCA se rompe.
3. ✅ **Caché por request** — `cmsItems()` cachea en memoria; 0 queries duplicados por página.
4. ✅ **Editor universal de items** — título, texto, tag, URL, icono (emoji), visibilidad: un solo formato para todos los bloques.
5. ✅ **CRUD sin recarga** — crear, editar, duplicar, eliminar y reordenar items en modal con AJAX.

## Historial y seguridad
6. ✅ **Versionado automático** — cada guardado crea un snapshot en `cms_history`.
7. ✅ **Undo real** — restaurar cualquier versión anterior con un clic.
8. ✅ **Activity log** — `cms_activity` registra quién/cuándo/quién cambió qué (auditoría completa).
9. ✅ **Reset a default** — devolver cualquier colección al contenido original del código.
10. ✅ **CSRF + sesión endurecida** — el CMS hereda la seguridad del admin (token CSRF, rate-limit, sesión regenerada).

## Productividad
11. ✅ **Ctrl+S para guardar** — atajo de teclado en el editor.
12. ✅ **Búsqueda instantánea** — filtrar colecciones por nombre/key mientras escribes.
13. ✅ **Reordenar con ↑/↓** — mover items sin drag & drop complejo (móvil-friendly).
14. ✅ **Duplicar item (⧉)** — clonar cualquier bloque en un segundo.
15. ✅ **Toggle de visibilidad** — ocultar items sin borrarlos (drafts live).
16. ✅ **Modal grande y legible** — editor de 860px con scroll, texto cómodo.

## Portabilidad
17. ✅ **Export JSON por colección** — descarga un bloque completo.
18. ✅ **Export All** — backup completo del contenido en un JSON.
19. ✅ **Import JSON** — restaurar todo (o una colección) desde archivo.
20. ✅ **Migraciones idempotentes** — `fix-live-data.sql` aplica esquema y datos sin duplicar.

## SEO y contenido
21. ✅ **Meta tags editables** — pages/sections ya permiten meta title/desc por página (existente, ahora conectado al mismo dashboard).
22. 🔜 **Vista previa SEO** — previsualizar snippet de Google/Twitter antes de publicar.
23. ✅ **Traducción automática conectada** — el traductor EN↔ES protege marcas y valida respuestas (i18n.php).
24. 🔜 **Programación de publicación** — publicar cambios de colección en fecha futura (el scheduler ya existe para blog).

## Experiencia del editor
25. ✅ **Dashboard con estadísticas** — páginas, productos, unidades, traducciones de un vistazo.
26. ✅ **Acceso rápido desde el sitio** — (roadmap) botón "Edit Content" flotante para admin logueado que abre la colección correcta.
27. 🔜 **Edición inline** — clic sobre cualquier texto del sitio y editar en el acto (contenteditable + API).
28. 🔜 **Multi-usuario con roles** — editor/administrador con permisos por colección.
29. 🔜 **CDN purge automático** — al guardar, invalidar caché nginx/CDN automáticamente.
30. 🔜 **Backups programados** — snapshot diario automático de cms_collections + media a almacenamiento externo.

## Cómo agregar un bloque nuevo al CMS (5 minutos)
1. En la página PHP: `$data = cmsItems('mi_bloque', [...defaults...]);`
2. Renderiza los items (usa `ht()` para escapar).
3. En el admin → Content Manager → "+ New Collection" → key `mi_bloque` → edita.
Sin código adicional, sin riesgo: si no existe la colección, se usa el default.
