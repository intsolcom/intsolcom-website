<?php
// ============================================================
// INTSOLCOM API v2 — Procedural (zero deps, no fancy patterns)
// ============================================================
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$db   = db();
$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri  = rtrim($uri, '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

function json_ok($data, $code = 200) {
    http_response_code($code);
    echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
function json_err($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}
function json_paginated($items, $total, $page, $per) {
    json_ok([
        'items' => $items,
        'pagination' => [
            'page' => $page, 'per_page' => $per, 'total' => $total,
            'total_pages' => max(1, (int)ceil($total / $per)),
            'has_next' => ($page * $per) < $total, 'has_prev' => $page > 1,
        ]
    ]);
}

// ── ROUTER SIMPLE ──
// /api/v2/health
if ($uri === '/api/v2/health' && $method === 'GET') {
    $total = (int)$db->query("SELECT COUNT(*) FROM resources WHERE type='article' AND status=1")->fetchColumn();
    json_ok(['status' => 'healthy', 'db' => 'connected', 'articles' => $total, 'time' => date('c')]);
}

// /api/v2/posts
if ($uri === '/api/v2/posts' && $method === 'GET') {
    $page    = max(1, (int)($_GET['page'] ?? 1));
    $perPage = min(50, max(1, (int)($_GET['per_page'] ?? 12)));
    $offset  = ($page - 1) * $perPage;

    $where = ["type = 'article'", "status = 1"];
    $params = [];

    if ($q = ($_GET['search'] ?? $_GET['q'] ?? '')) {
        $where[] = "(title LIKE ? OR excerpt LIKE ?)";
        $params[] = "%$q%"; $params[] = "%$q%";
    }
    if (($_GET['featured'] ?? '') !== '') $where[] = "featured = 1";

    $sort = ($_GET['sort'] ?? 'latest') === 'oldest' ? 'published_at ASC' : 'featured DESC, published_at DESC';

    $whereStr = implode(' AND ', $where);
    $total = $db->prepare("SELECT COUNT(*) FROM resources WHERE $whereStr");
    $total->execute($params);
    $totalPosts = (int)$total->fetchColumn();

    $excludeBody = ($_GET['exclude_body'] ?? '') === 'true';
    $fields = $excludeBody
        ? 'id,title,slug,excerpt,cover_image,type,author,read_time,featured,views,status,published_at,created_at'
        : '*';

    $stmt = $db->prepare("SELECT $fields FROM resources WHERE $whereStr ORDER BY $sort LIMIT $perPage OFFSET $offset");
    $stmt->execute($params);
    $posts = $stmt->fetchAll();

    json_paginated($posts, $totalPosts, $page, $perPage);
}

// /api/v2/posts/{slug}
if (preg_match('#^/api/v2/posts/([^/]+)$#', $uri, $m) && $method === 'GET') {
    $stmt = $db->prepare("SELECT * FROM resources WHERE slug = ? AND type = 'article' AND status = 1 LIMIT 1");
    $stmt->execute([$m[1]]);
    $post = $stmt->fetch();
    if (!$post) json_err('Post not found', 404);

    $related = $db->prepare("SELECT id,title,slug,excerpt,cover_image,published_at FROM resources WHERE type='article' AND status=1 AND id!=? ORDER BY published_at DESC LIMIT 3");
    $related->execute([$post['id']]);
    $post['related'] = $related->fetchAll();

    $db->prepare("UPDATE resources SET views = views + 1 WHERE id = ?")->execute([$post['id']]);
    json_ok($post);
}

// /api/v2/categories
if ($uri === '/api/v2/categories' && $method === 'GET') {
    $cats = [
        ['id'=>1,'name'=>'Technology','slug'=>'technology','color'=>'#00C896','post_count'=>(int)$db->query("SELECT COUNT(*) FROM resources WHERE type='article' AND status=1 AND (title LIKE '%tech%' OR title LIKE '%AI%' OR title LIKE '%CRM%' OR title LIKE '%software%')")->fetchColumn()],
        ['id'=>2,'name'=>'Business','slug'=>'business','color'=>'#2563EB','post_count'=>(int)$db->query("SELECT COUNT(*) FROM resources WHERE type='article' AND status=1 AND (title LIKE '%business%' OR title LIKE '%operation%' OR title LIKE '%BPO%')")->fetchColumn()],
        ['id'=>3,'name'=>'Nearshore','slug'=>'nearshore','color'=>'#8B5CF6','post_count'=>(int)$db->query("SELECT COUNT(*) FROM resources WHERE type='article' AND status=1 AND (title LIKE '%nearshore%' OR title LIKE '%colombia%' OR title LIKE '%offshore%')")->fetchColumn()],
        ['id'=>4,'name'=>'AI & Data','slug'=>'ai-data','color'=>'#F59E0B','post_count'=>(int)$db->query("SELECT COUNT(*) FROM resources WHERE type='article' AND status=1 AND (title LIKE '%AI%' OR title LIKE '%artificial%' OR title LIKE '%data%' OR title LIKE '%annotation%')")->fetchColumn()],
        ['id'=>5,'name'=>'Development','slug'=>'development','color'=>'#64748B','post_count'=>(int)$db->query("SELECT COUNT(*) FROM resources WHERE type='article' AND status=1 AND (title LIKE '%develop%' OR title LIKE '%SDD%' OR title LIKE '%code%')")->fetchColumn()],
    ];
    json_ok($cats);
}

// Sitemap
if ($uri === '/sitemap.xml' && $method === 'GET') {
    header('Content-Type: application/xml; charset=utf-8');
    $posts = $db->query("SELECT slug, published_at FROM resources WHERE type='article' AND status=1 ORDER BY published_at DESC LIMIT 1000")->fetchAll();
    echo '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
    foreach ($posts as $p) {
        $d = date('c', strtotime($p['published_at'] ?? 'now'));
        echo "  <url><loc>".SITE_URL."/blog/".h($p['slug'])."</loc><lastmod>$d</lastmod></url>\n";
    }
    echo '</urlset>'; exit;
}

// Health (catch-all alias)
if (($uri === '/health' || $uri === '/api/v2') && $method === 'GET') {
    $total = (int)$db->query("SELECT COUNT(*) FROM resources WHERE type='article' AND status=1")->fetchColumn();
    json_ok(['status' => 'healthy', 'db' => 'connected', 'articles' => $total, 'time' => date('c')]);
}

// 404
json_err('Not Found', 404);
