<?php
// ============================================================
// MARCAS BPO — On-the-fly Translation System (ES <-> EN)
// ============================================================
// Usage: echo t('Texto en español'); — outputs translated text
// if current language is 'en', or the original if 'es'.
//
// Translations are cached in `translations` table (one Claude API
// call per unique string, ever). Subsequent requests are pure DB reads.

/**
 * Detect browser language from Accept-Language header.
 * Returns 'en', 'es', or null if not detectable/supported.
 */
function detectBrowserLang(): ?string {
    static $detected = null;
    if ($detected !== null) return $detected;

    $detected = null;
    $header = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? $_SERVER['Accept-Language'] ?? '';
    if ($header === '') return null;

    $supported = ['es' => true, 'en' => true];
    $parts = explode(',', $header);
    foreach ($parts as $part) {
        $lang = strtolower(trim(explode(';', $part)[0]));
        if (strlen($lang) >= 2) $lang = substr($lang, 0, 2);
        if (isset($supported[$lang])) {
            $detected = $lang;
            break;
        }
    }
    return $detected;
}

/**
 * Ensure translations table exists (idempotent, cheap check).
 */
function ensureTranslationsTable(): void {
    static $checked = false;
    if ($checked) return;
    $checked = true;
    db()->exec("CREATE TABLE IF NOT EXISTS `translations` (
        id INT AUTO_INCREMENT PRIMARY KEY,
        src_hash CHAR(32) NOT NULL,
        src_lang VARCHAR(5) NOT NULL DEFAULT 'es',
        dst_lang VARCHAR(5) NOT NULL,
        src_text TEXT NOT NULL,
        dst_text TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_translation (src_hash, dst_lang)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

/**
 * Detect current language: ?lang= override > cookie > default 'en'.
 * English is the site's source language and the default for all visitors.
 * Spanish is only shown if the visitor explicitly selects it.
 */
function currentLang(): string {
    static $lang = null;
    if ($lang !== null) return $lang;

    $supported = ['en','es'];

    if (isset($_GET['lang']) && in_array($_GET['lang'], $supported, true)) {
        $lang = $_GET['lang'];
        setcookie('intsolcom_lang', $lang, time() + 86400 * 365, '/');
        return $lang;
    }
    if (isset($_COOKIE['intsolcom_lang']) && in_array($_COOKIE['intsolcom_lang'], $supported, true)) {
        $lang = $_COOKIE['intsolcom_lang'];
        return $lang;
    }
    // Browser language auto-detection on first visit (no cookie, no URL param)
    $browser = detectBrowserLang();
    if ($browser !== null && in_array($browser, $supported, true)) {
        $lang = $browser;
        setcookie('intsolcom_lang', $lang, time() + 86400 * 365, '/');
        return $lang;
    }

    $lang = 'en'; // default site language — always English unless visitor chooses Spanish
    return $lang;
}

/**
 * Translate text to the current language (if needed), with DB caching.
 * Site content is authored in English; if currentLang()==='es', translate to Spanish.
 */
function t(string $text): string {
    $text = trim($text);
    if ($text === '') return $text;

    $lang = currentLang();
    if ($lang === 'en') return $text; // source language, no translation needed

    ensureTranslationsTable();

    $hash = md5($text);
    $stmt = db()->prepare("SELECT dst_text FROM translations WHERE src_hash = ? AND dst_lang = ? LIMIT 1");
    $stmt->execute([$hash, $lang]);
    $row = $stmt->fetch();
    if ($row) return $row['dst_text'];

    // Not cached — translate via Claude API (synchronous, only happens once per string)
    $translated = mbpoTranslateViaClaude($text, $lang);
    if ($translated === null) return $text; // fallback to original on API error

    $ins = db()->prepare("INSERT INTO translations (src_hash, src_lang, dst_lang, src_text, dst_text) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE dst_text = ?");
    $ins->execute([$hash, 'en', $lang, $text, $translated, $translated]);

    return $translated;
}

/**
 * Calls Claude API to translate a single string. Returns null on failure.
 * Rate-limited to MAX_API_CALLS per page load to prevent runaway API usage.
 */
function mbpoTranslateViaClaude(string $text, string $targetLang): ?string {
    static $apiCallCount = 0;
    $maxCalls = defined('I18N_MAX_API_CALLS') ? I18N_MAX_API_CALLS : 5;

    if ($apiCallCount >= $maxCalls) return null;
    $apiCallCount++;

    try {
        if (!defined('ANTHROPIC_API_KEY') || ANTHROPIC_API_KEY === '' || strpos(ANTHROPIC_API_KEY, 'YOUR_ANTHROPIC') !== false) {
            return null;
        }
        if (!function_exists('curl_init')) {
            return null;
        }

        $langNames = ['en' => 'English', 'es' => 'Spanish'];
        $targetName = $langNames[$targetLang] ?? 'Spanish';

        $payload = [
            'model'      => ANTHROPIC_MODEL,
            'max_tokens' => 500,
            'system'     => "You are a professional translator for a BPO company's corporate website. Translate the given English text to {$targetName}. Preserve tone, formatting, capitalization style, any HTML tags, and any placeholder tokens like {name} or %ENDING% EXACTLY as written (do not translate or alter their contents). Keep it professional and corporate in register. Output ONLY the translation, nothing else — no quotes, no explanations.",
            'messages'   => [
                ['role' => 'user', 'content' => $text],
            ],
        ];

        $ch = curl_init('https://api.anthropic.com/v1/messages');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-api-key: ' . ANTHROPIC_API_KEY,
                'anthropic-version: 2023-06-01',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 20,
        ]);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return null;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode >= 400) return null;

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) return null;

        $translated = $data['content'][0]['text'] ?? null;
        return $translated !== null ? trim($translated) : null;
    } catch (\Throwable $e) {
        return null;
    }
}

/**
 * Pre-warm the translation cache for a batch of English strings (call from admin "Pre-traducir" button).
 * Translates English source text to Spanish. Returns number of new translations created.
 */
function mbpoPretranslate(array $texts, string $targetLang): int {
    ensureTranslationsTable();
    $count = 0;
    foreach ($texts as $text) {
        $text = trim($text);
        if ($text === '') continue;
        $hash = md5($text);
        $stmt = db()->prepare("SELECT id FROM translations WHERE src_hash = ? AND dst_lang = ? LIMIT 1");
        $stmt->execute([$hash, $targetLang]);
        if ($stmt->fetch()) continue; // already cached

        $translated = mbpoTranslateViaClaude($text, $targetLang);
        if ($translated === null) continue;

        $ins = db()->prepare("INSERT INTO translations (src_hash, src_lang, dst_lang, src_text, dst_text) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE dst_text = ?");
        $ins->execute([$hash, 'en', $targetLang, $text, $translated, $translated]);
        $count++;
    }
    return $count;
}
