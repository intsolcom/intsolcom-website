<?php
// ============================================================
// INTSOLCOM — Database Configuration (EXAMPLE)
// Copy to config.php and fill in real values.
// NEVER commit config.php with real credentials.
// ============================================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'intsolcom');
define('DB_USER', 'intsolcom');
define('DB_PASS', 'CHANGE_ME_STRONG_PASSWORD');
define('DB_CHARSET', 'utf8mb4');

define('SITE_URL',   'https://intsolcom.com');
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads/');
define('UPLOAD_URL', SITE_URL . '/assets/uploads/');
define('ADMIN_USER', 'admin');

// ── Admin password ──
// Option A (legacy): plaintext password in this file.
define('ADMIN_PASS', 'CHANGE_ME_STRONG_PASSWORD');
// Option B (recommended): hashed password — generate with:
//   php -r "echo password_hash('YOUR_PASSWORD', PASSWORD_BCRYPT);"
// If ADMIN_PASS_HASH is defined and non-empty, it takes priority over ADMIN_PASS.
// define('ADMIN_PASS_HASH', '$2y$10$...');

// ---- AI TRANSLATION ----
// DeepSeek API (OpenAI-compatible) — used for on-the-fly EN→ES translation
define('DEEPSEEK_API_KEY', 'YOUR_DEEPSEEK_API_KEY');
// Legacy Anthropic key (kept for backward compatibility)
define('ANTHROPIC_API_KEY', 'YOUR_ANTHROPIC_API_KEY');
define('ANTHROPIC_MODEL',   'claude-sonnet-4-6');

// ---- BLOG REST API ----
// Token for external systems to publish blog posts via POST/PUT /api/blog
define('API_BLOG_TOKEN', 'YOUR_API_BLOG_TOKEN');

// ---- BYT — credential encryption ----
// Used to encrypt third-party API keys before storing them in the database.
// Generate with: php -r "echo bin2hex(random_bytes(32));"
define('BYT_ENCRYPTION_KEY', 'CHANGE_ME_64_HEX_CHARS');

function bytEncrypt(string $plaintext): string {
    $key = hex2bin(BYT_ENCRYPTION_KEY);
    $iv = openssl_random_pseudo_bytes(16);
    $cipher = openssl_encrypt($plaintext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $cipher);
}

function bytDecrypt(?string $encoded): string {
    if (!$encoded) return '';
    $key = hex2bin(BYT_ENCRYPTION_KEY);
    $raw = base64_decode($encoded);
    if ($raw === false || strlen($raw) < 17) return '';
    $iv = substr($raw, 0, 16);
    $cipher = substr($raw, 16);
    $plain = openssl_decrypt($cipher, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    return $plain === false ? '' : $plain;
}

function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    try {
        $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        error_log('[INTSOLCOM] DB connection failed: ' . $e->getMessage());
        http_response_code(500);
        die(json_encode(['error' => 'Database connection failed. Please try again later.']));
    }
    return $pdo;
}

// ---- Helpers ----
function setting(string $key, string $default = ''): string {
    static $cache = [];
    if (isset($cache[$key])) return $cache[$key];
    $s = db()->prepare("SELECT value FROM settings WHERE `key` = ? LIMIT 1");
    $s->execute([$key]);
    $row = $s->fetch();
    $cache[$key] = $row ? $row['value'] : $default;
    return $cache[$key];
}

function setSetting(string $key, string $value): void {
    $s = db()->prepare("INSERT INTO settings (`key`, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = ?");
    $s->execute([$key, $value, $value]);
}

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

/**
 * Translate + escape. Use for any user-facing text content (not URLs/colors/config).
 */
function ht(string $s): string {
    return htmlspecialchars(t($s), ENT_QUOTES, 'UTF-8');
}

function getSections(int $pageId): array {
    $s = db()->prepare("SELECT * FROM sections WHERE page_id = ? AND status = 1 ORDER BY sort_order ASC");
    $s->execute([$pageId]);
    $sections = $s->fetchAll();
    foreach ($sections as &$sec) {
        $f = db()->prepare("SELECT field_key, field_value FROM section_fields WHERE section_id = ?");
        $f->execute([$sec['id']]);
        $sec['fields'] = [];
        foreach ($f->fetchAll() as $row) {
            $sec['fields'][$row['field_key']] = $row['field_value'];
        }
    }
    return $sections;
}

function getPage(string $slug): ?array {
    $s = db()->prepare("SELECT * FROM pages WHERE slug = ? AND status = 1 LIMIT 1");
    $s->execute([$slug]);
    return $s->fetch() ?: null;
}

require_once __DIR__ . '/i18n.php';
