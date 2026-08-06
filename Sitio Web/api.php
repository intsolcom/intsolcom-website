<?php
// ============================================================
// INTSOLCOM API v2 — Unified REST endpoint
// ============================================================
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/api/Core/Router.php';
require_once __DIR__ . '/includes/api/Core/Response.php';
require_once __DIR__ . '/includes/api/Core/Request.php';

use App\Core\{Router, Response, Request};

// CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key');
    http_response_code(200); exit;
}

$db = db(); // from config.php

$router = new Router();

// ── Health ──
$router->get('/api/v2/health', function() use ($db) {
    Response::ok(['status' => 'healthy', 'db' => 'connected', 'time' => date('c')]);
});

// ── Posts (public) ──
$router->get('/api/v2/posts', function() use ($db) {
    $page    = max(1, (int)($_GET['page'] ?? 1));
    $perPage = min(50, max(1, (int)($_GET['per_page'] ?? 12)));
    $offset  = ($page - 1) * $perPage;
    $siteId  = 1;

    $where = ["p.status = 'published'", "p.site_id = ?"];
    $params = [$siteId];

    if ($cat = ($_GET['category'] ?? '')) {
        $cs = $db->prepare("SELECT id FROM blog_categories WHERE slug = ? AND site_id = ?");
        $cs->execute([$cat, $siteId]);
        if ($catId = $cs->fetchColumn()) { $where[] = "p.category_id = ?"; $params[] = $catId; }
    }
    if ($tag = ($_GET['tag'] ?? '')) {
        $where[] = "p.id IN (SELECT post_id FROM blog_post_tags WHERE tag_id = (SELECT id FROM blog_tags WHERE slug = ? AND site_id = ?))";
        $params[] = $tag; $params[] = $siteId;
    }
    if ($_GET['featured'] ?? '') $where[] = "p.featured = 1";
    if ($q = ($_GET['search'] ?? $_GET['q'] ?? '')) {
        $where[] = "(p.title LIKE ? OR p.excerpt LIKE ?)";
        $params[] = "%$q%"; $params[] = "%$q%";
    }

    $sort = match($_GET['sort'] ?? 'latest') {
        'oldest' => 'p.published_at ASC', 'popular' => 'p.views DESC',
        default => 'p.featured DESC, p.published_at DESC',
    };

    $whereStr = implode(' AND ', $where);
    $total = $db->prepare("SELECT COUNT(*) FROM blog_posts p WHERE $whereStr");
    $total->execute($params);
    $totalPosts = (int)$total->fetchColumn();

    $exclude = ($_GET['exclude_body'] ?? '') === 'true';
    $fields = $exclude
        ? 'p.id,p.title,p.slug,p.excerpt,p.cover_image,p.author_name,p.author_role,p.read_time,p.status,p.featured,p.views,p.published_at,p.created_at,c.name as cat_name,c.slug as cat_slug,c.color as cat_color'
        : 'p.*,c.name as cat_name,c.slug as cat_slug,c.color as cat_color';

    $stmt = $db->prepare("SELECT $fields FROM blog_posts p LEFT JOIN blog_categories c ON p.category_id = c.id WHERE $whereStr ORDER BY $sort LIMIT $perPage OFFSET $offset");
    $stmt->execute($params);
    $posts = $stmt->fetchAll();

    // Tags
    $ts = $db->prepare("SELECT t.name,t.slug FROM blog_tags t JOIN blog_post_tags pt ON t.id=pt.tag_id WHERE pt.post_id=?");
    foreach ($posts as &$p) { $ts->execute([$p['id']]); $p['tags'] = $ts->fetchAll(); }

    Response::paginated($posts, $totalPosts, $page, $perPage);
});

// ── Single post ──
$router->get('/api/v2/posts/{slug}', function($params) use ($db) {
    $slug = $params['slug'] ?? '';
    $stmt = $db->prepare("SELECT p.*,c.name as cat_name,c.slug as cat_slug,c.color as cat_color FROM blog_posts p LEFT JOIN blog_categories c ON p.category_id=c.id WHERE p.slug=? AND p.status='published' LIMIT 1");
    $stmt->execute([$slug]);
    $post = $stmt->fetch();
    if (!$post) { Response::error('Post not found', 404); }

    $ts = $db->prepare("SELECT t.name,t.slug FROM blog_tags t JOIN blog_post_tags pt ON t.id=pt.tag_id WHERE pt.post_id=?");
    $ts->execute([$post['id']]); $post['tags'] = $ts->fetchAll();

    $related = $db->prepare("SELECT id,title,slug,excerpt,cover_image,published_at FROM blog_posts WHERE category_id=? AND id!=? AND status='published' ORDER BY published_at DESC LIMIT 3");
    $related->execute([$post['category_id'], $post['id']]); $post['related'] = $related->fetchAll();

    $db->prepare("UPDATE blog_posts SET views=views+1 WHERE id=?")->execute([$post['id']]);
    Response::ok($post);
});

// ── Categories ──
$router->get('/api/v2/categories', function() use ($db) {
    $cats = $db->query("SELECT c.*, (SELECT COUNT(*) FROM blog_posts p WHERE p.category_id=c.id AND p.status='published') as post_count FROM blog_categories c ORDER BY c.sort_order")->fetchAll();
    Response::ok($cats);
});

// ── Sitemap ──
$router->get('/sitemap.xml', function() use ($db) {
    header('Content-Type: application/xml; charset=utf-8');
    $posts = $db->query("SELECT slug, updated_at FROM blog_posts WHERE status='published' ORDER BY published_at DESC LIMIT 1000")->fetchAll();
    echo '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
    foreach ($posts as $p) echo "  <url><loc>".SITE_URL."/blog/".htmlspecialchars($p['slug'])."</loc><lastmod>".date('c',strtotime($p['updated_at']??'now'))."</lastmod></url>\n";
    echo '</urlset>'; exit;
});

$router->dispatch();
