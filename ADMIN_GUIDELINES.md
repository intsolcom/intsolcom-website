# INTSOLCOM — Admin Development Rules

## CRITICAL: Never inject raw JS into PHP without <script> tags

### ❌ WRONG — Causes visible code on dashboard

```php
// In a PHP include file without <script> wrapper
let schedAllPosts = [];
function schedulerOpen() { ... }
```

### ✅ CORRECT — Always wrap in <script> or use data attributes

```php
<script>
// All JS goes here
let schedAllPosts = [];
function schedulerOpen() { ... }
</script>
```

## CRITICAL: Never use sed/perl to inject complex JS into PHP files

The escaping of quotes (`"`, `'`, `` ` ``) and special chars gets mangled.

### ✅ CORRECT — Use Write tool or Edit tool
- The Edit tool handles escaping properly
- The Write tool creates clean files from scratch

## CRITICAL: Avoid raw emoji in JS strings — use Unicode escapes

### ❌ WRONG
```js
const icons = { intsolcom: '🌐' };
```

### ✅ CORRECT
```js
const icons = { intsolcom: '\u{1F310}' };
```

## Architecture: Separate JS into standalone files

For complex JS modules that need to be in PHP admin panels:

1. Create a standalone `.js` file (e.g., `assets/js/scheduler.js`)
2. Include it via `<script src="/assets/js/scheduler.js"></script>`
3. NEVER `include` a PHP file that contains raw JS without script tags

## Docker Containers: Check inside the container, not just host

- Marcas BPO runs in `marcasbpo-php` Docker container
- INTSOLCOM runs directly on host filesystem
- When debugging, always check: `docker exec <container> grep "text" <file>`

## Pre-deploy Checklist

- [ ] JS is inside `<script>` tags OR in .js files
- [ ] No raw JS visible in page source (View → Page Source)
- [ ] Emojis use Unicode escapes, not raw characters
- [ ] Docker containers restarted if modified from host
- [ ] OPcache cleared after PHP file changes
