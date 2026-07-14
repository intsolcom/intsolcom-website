<?php
// ============================================================
// INTSOLCOM — Database Configuration
// ============================================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'u521293802_intsolcom');
define('DB_USER', 'u521293802_intsolcom');
define('DB_PASS', 'INTSOLcom2026$!');
define('DB_CHARSET', 'utf8mb4');

define('SITE_URL',   'https://intsolcom.com');
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads/');
define('UPLOAD_URL', SITE_URL . '/assets/uploads/');
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'IntsolcomAdmin2026!');

// ---- AI CHAT AGENTS ----
// Get your key at console.anthropic.com → Settings → API Keys
define('ANTHROPIC_API_KEY', 'YOUR_ANTHROPIC_API_KEY');
define('ANTHROPIC_MODEL',   'claude-sonnet-4-6');

// ---- BYT — credential encryption ----
// Used to encrypt third-party API keys before storing them in the database.
// This key itself must NEVER be committed to a public repo.
define('BYT_ENCRYPTION_KEY', 'fcfca098166116ce6dba01691e219f3da7f083c05e54af398b645d3cb932c5fe');

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
        http_response_code(500);
        die(json_encode(['error' => 'DB connection failed: ' . $e->getMessage()]));
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
