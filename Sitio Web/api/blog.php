<?php
// ============================================================
// INTSOLCOM — Blog REST API
// ============================================================
// Endpoint: https://intsolcom.com/api/blog
// Auth: Bearer token in Authorization header
// Methods: POST (create), PUT (update by slug), GET (list)
// ============================================================

require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ── Auth ──
function apiAuth(): bool {
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (preg_match('/Bearer\s+(.+)/', $header, $m)) {
        $token = $m[1];
        return $token === (defined('API_BLOG_TOKEN') ? API_BLOG_TOKEN : 'intsolcom_blog_api_2026');
    }
    return false;
}

function apiError(int $code, string $msg): never {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $msg]);
    exit;
}

if (!apiAuth()) {
    apiError(401, 'Unauthorized. Use Authorization: Bearer <token>');
}

try {
    $db = db();
    $method = $_SERVER['REQUEST_METHOD'];

    // ── GET /api/blog — list posts ──
    if ($method === 'GET') {
        $limit = min((int)($_GET['limit'] ?? 20), 50);
        $page  = max((int)($_GET['page'] ?? 1), 1);
        $offset = ($page - 1) * $limit;
        $slug  = $_GET['slug'] ?? '';

        if ($slug) {
            $s = $db->prepare("SELECT * FROM resources WHERE slug = ? AND status = 1 LIMIT 1");
            $s->execute([$slug]);
            $post = $s->fetch();
            if (!$post) apiError(404, 'Post not found');
            echo json_encode(['ok' => true, 'post' => $post]);
            exit;
        }

        $total = $db->query("SELECT COUNT(*) FROM resources WHERE type='article'")->fetchColumn();
        $posts = $db->query("SELECT * FROM resources WHERE type='article' ORDER BY published_at DESC LIMIT $limit OFFSET $offset")->fetchAll();
        echo json_encode(['ok' => true, 'posts' => $posts, 'total' => (int)$total, 'page' => $page, 'limit' => $limit]);
        exit;
    }

    // ── POST /api/blog — create post ──
    if ($method === 'POST') {
        $body = json_decode(file_get_contents('php://input'), true);
        if (!$body) apiError(400, 'Invalid JSON body');

        $title      = trim($body['title'] ?? '');
        $slug       = trim($body['slug'] ?? '');
        $excerpt    = trim($body['excerpt'] ?? '');
        $content    = $body['content'] ?? '';
        $coverImage = trim($body['cover_image'] ?? '');
        $author     = trim($body['author'] ?? 'INTSOLCOM Team');
        $readTime   = (int)($body['read_time'] ?? 5);
        $featured   = !empty($body['featured']) ? 1 : 0;
        $metaTitle  = trim($body['meta_title'] ?? $title);
        $metaDesc   = trim($body['meta_desc'] ?? $excerpt);
        $publishedAt = $body['published_at'] ?? date('Y-m-d H:i:s');

        if (!$title || !$slug || !$content) {
            apiError(400, 'title, slug, and content are required');
        }

        // Auto-generate slug if missing
        if (!$slug) {
            $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $title), '-'));
        }

        // Auto-calculate read time
        if (!$readTime || $readTime <= 1) {
            $wordCount = str_word_count(strip_tags($content));
            $readTime = max(1, ceil($wordCount / 200));
        }

        // Check if slug exists
        $check = $db->prepare("SELECT id FROM resources WHERE slug = ? LIMIT 1");
        $check->execute([$slug]);
        if ($check->fetch()) {
            apiError(409, "Slug '$slug' already exists. Use PUT to update.");
        }

        $ins = $db->prepare("INSERT INTO resources (title, slug, excerpt, content, cover_image, type, author, read_time, featured, meta_title, meta_desc, status, published_at) VALUES (?,?,?,?,?,'article',?,?,?,?,?,1,?)");
        $ins->execute([$title, $slug, $excerpt, $content, $coverImage, $author, $readTime, $featured, $metaTitle, $metaDesc, $publishedAt]);

        $id = $db->lastInsertId();
        echo json_encode(['ok' => true, 'id' => (int)$id, 'slug' => $slug, 'url' => SITE_URL . '/blog/' . $slug]);
        exit;
    }

    // ── PUT /api/blog — update post by slug ──
    if ($method === 'PUT') {
        $body = json_decode(file_get_contents('php://input'), true);
        if (!$body) apiError(400, 'Invalid JSON body');

        $slug = trim($body['slug'] ?? '');
        if (!$slug) apiError(400, 'slug is required');

        $check = $db->prepare("SELECT id FROM resources WHERE slug = ? LIMIT 1");
        $check->execute([$slug]);
        $existing = $check->fetch();
        if (!$existing) apiError(404, 'Post not found');

        $sets = [];
        $vals = [];
        $fields = ['title', 'excerpt', 'content', 'cover_image', 'author', 'read_time', 'featured', 'meta_title', 'meta_desc', 'published_at'];
        foreach ($fields as $f) {
            if (isset($body[$f])) {
                $sets[] = "$f = ?";
                $vals[] = $body[$f];
            }
        }

        if (empty($sets)) apiError(400, 'No fields to update');

        $vals[] = $existing['id'];
        $db->prepare("UPDATE resources SET " . implode(', ', $sets) . " WHERE id = ?")->execute($vals);

        echo json_encode(['ok' => true, 'slug' => $slug, 'url' => SITE_URL . '/blog/' . $slug]);
        exit;
    }

    apiError(405, 'Method not allowed');
} catch (Exception $e) {
    apiError(500, 'Server error: ' . $e->getMessage());
}
