# PROMPT MAESTRO — Portafolio de Productos Tecnológicos INTSOLCOM

> Prompt reutilizable. Objetivo: mantener TODAS las páginas y secciones del sitio
> (home, technology, product detail, footer, sitemap, versión Node y seeds/BD)
> alineadas con este portafolio.

## Portafolio oficial (3 productos, todos bajo la marca WONTIA)

### 1. WONTIA AIP — "Applied Intelligence Platform"
- **Qué es:** la plataforma de inteligencia aplicada que impulsa todo el ecosistema WONTIA, alimentada por TIA (Technology of Applied Intelligence).
- **Mensaje amigable:** "Tu capa de inteligencia. Entiende el contexto, toma decisiones y ejecuta acciones — en cualquier dominio."
- **Slug:** `wontia-aip` · **Categoría:** `AI Platform` · **Icono:** 🧠 · **Color:** mint `#00C896`
- **CTA:** `https://wontia.com` → "Visit wontia.com"

### 2. WONTIA FOOD SECURITY — "Food Security Intelligence"
- **Qué es:** inteligencia aplicada a la seguridad alimentaria: detectar riesgo, priorizar respuesta, coordinar acción y medir impacto.
- **Mensaje amigable:** "Detecta, prioriza, coordina y mide. La misma inteligencia WONTIA, aplicada a alimentar mejor al mundo."
- **Slug:** `wontia-food-security` · **Categoría:** `Food Security` · **Icono:** 🌾 · **Color:** amber `#F59E0B`
- **CTA:** `https://wontia.com/#food-security` → "Learn more"

### 3. WONTIA IA ANNOTATION SUITE — "AI Data Annotation"
- **Qué es:** plataforma integral para anotación de datos de IA: gestión de proyectos, control de calidad y análisis del equipo de anotación.
- **Mensaje amigable:** "Anotación de datos a escala. Proyectos claros, calidad verificada, equipos medidos."
- **Slug:** `wontia-ia-annotation-suite` · **Categoría:** `AI Data` · **Icono:** 🏷️ · **Color:** blue `#2563EB`
- **CTA:** `https://iaam.com` → "Visit iaam.com" (landing propia en iaam.com)

## Reglas de contenido
- INTSOLCOM solo da **introducción** y linkea a los sitios oficiales. Nunca describir
  el producto como CRM ni prometer demos propios.
- **MACROPONDER queda fuera** del portafolio (status = 0 en BD, fuera de grids y footers).
- "IA Annotation Manager" se renombra a **WONTIA IA ANNOTATION SUITE** en todo el sitio.
- Copys cortos (1 frase en cards, máximo 2 en detalle). Beneficio primero, luego función.

## Reglas visuales — Flat design, minimalista y amigable
- Cards de producto: superficie blanca sólida, borde 1px `#E2E8F0`, radio 16px,
  SIN gradientes (`.product-card__gradient*` ocultos), sin sombra grande,
  hover solo con borde accent + sombra sutil.
- SIN efectos 3D (desactivar tilt en main.js para product cards).
- Iconos planos dentro de contenedores suaves de color sólido (fondo al 8-10% del color de marca).
- Tipografía Inter, espaciado generoso, jerarquía clara: nombre → tag → descripción corta → CTA.

## Reglas técnicas
- Emojis en JS como escapes Unicode (`\u{1F33E}` para 🌾, `\u{1F3F7}` para 🏷️, `\u{1F9E0}` para 🧠).
- Mantener `nav--transparent` / auto-detección de tema y responsive móvil.
- En JS nunca inyectar HTML crudo sin `<script>`; en PHP siempre escapar con `h()`/`ht()`.
- Actualizar en TODOS los puntos: index.php, technology.php, product.php, server.js,
  db-install.php, fix-live-data.sql, footers, sitemap y seeds.
