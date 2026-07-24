<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

// ─── Auth ───
if (isset($_GET['logout'])) { session_destroy(); header('Location: /'); exit; }
$isLoggedIn = isset($_SESSION['intsolcom_admin']) && $_SESSION['intsolcom_admin'] === true;
if (!$isLoggedIn && !empty($_POST['username']) && !empty($_POST['password'])) {
    if ($_POST['username'] === ADMIN_USER && $_POST['password'] === ADMIN_PASS) {
        $_SESSION['intsolcom_admin'] = true;
        $isLoggedIn = true;
        header('Location: /admin'); exit;
    } else { $loginError = 'Invalid credentials.'; }
}

// ─── AJAX Handler ───
if ($isLoggedIn && isset($_GET['action'])) {
    header('Content-Type: application/json');
    $action = $_GET['action'];
    try {
        $db = db();
        switch ($action) {

            // ── SAVE SETTING ──
            case 'save_setting':
                $k = $_POST['key'] ?? '';
                $v = $_POST['value'] ?? '';
                if (!$k) { echo json_encode(['ok'=>false,'error'=>'Missing key']); exit; }
                $db->prepare("INSERT INTO settings (`key`, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = ?")->execute([$k, $v, $v]);
                echo json_encode(['ok'=>true]); exit;

            // ── CLEAR CACHE ──
            case 'clear_cache':
                $db->prepare("INSERT INTO settings (`key`, value) VALUES ('cache_version', '1') ON DUPLICATE KEY UPDATE value = value + 1")->execute();
                echo json_encode(['ok'=>true]); exit;

            // ── SAVE SECTION ──
            case 'save_section':
                $sectionId = (int)($_POST['section_id'] ?? 0);
                if ($sectionId <= 0) { echo json_encode(['ok'=>false,'error'=>'Invalid section_id']); exit; }
                $del = $db->prepare("DELETE FROM section_fields WHERE section_id = ?");
                $del->execute([$sectionId]);
                $ins = $db->prepare("INSERT INTO section_fields (section_id, field_key, field_value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE field_value = ?");
                foreach ($_POST as $k => $v) {
                    if (in_array($k, ['section_id','action','page_id','type'])) continue;
                    $ins->execute([$sectionId, $k, $v, $v]);
                }
                echo json_encode(['ok'=>true]); exit;

            // ── ADD SECTION ──
            case 'add_section':
                $pageId = (int)($_POST['page_id'] ?? 0);
                $type   = $_POST['type'] ?? '';
                $sort   = (int)($_POST['sort_order'] ?? 0);
                if ($pageId <= 0 || !$type) { echo json_encode(['ok'=>false,'error'=>'Missing page_id or type']); exit; }
                $db->prepare("INSERT INTO sections (page_id, type, sort_order, status) VALUES (?, ?, ?, 1)")->execute([$pageId, $type, $sort]);
                echo json_encode(['ok'=>true, 'id' => $db->lastInsertId()]); exit;

            // ── DELETE SECTION ──
            case 'delete_section':
                $id = (int)($_POST['id'] ?? 0);
                if ($id <= 0) { echo json_encode(['ok'=>false,'error'=>'Invalid id']); exit; }
                $db->prepare("DELETE FROM sections WHERE id = ?")->execute([$id]);
                echo json_encode(['ok'=>true]); exit;

            // ── REORDER SECTIONS ──
            case 'reorder_sections':
                $items = json_decode($_POST['items'] ?? '[]', true);
                $up = $db->prepare("UPDATE sections SET sort_order = ? WHERE id = ?");
                foreach ($items as $item) $up->execute([(int)$item['sort_order'], (int)$item['id']]);
                echo json_encode(['ok'=>true]); exit;

            // ── SAVE NAV ──
            case 'save_nav':
                $items = json_decode($_POST['items'] ?? '[]', true);
                if (!is_array($items)) { echo json_encode(['ok'=>false,'error'=>'Invalid JSON']); exit; }
                $db->exec("DELETE FROM nav_items");
                $ins = $db->prepare("INSERT INTO nav_items (text, url, is_cta, visible, sort_order) VALUES (?, ?, ?, ?, ?)");
                foreach ($items as $i) {
                    $ins->execute([$i['text'] ?? '', $i['url'] ?? '', (int)($i['is_cta'] ?? 0), (int)($i['visible'] ?? 1), (int)($i['sort_order'] ?? 0)]);
                }
                echo json_encode(['ok'=>true]); exit;

            // ── SAVE CLIENTS ──
            case 'save_clients':
                $items = json_decode($_POST['items'] ?? '[]', true);
                if (!is_array($items)) { echo json_encode(['ok'=>false,'error'=>'Invalid JSON']); exit; }
                $db->exec("DELETE FROM clients");
                $ins = $db->prepare("INSERT INTO clients (name, logo_url, visible, sort_order) VALUES (?, ?, ?, ?)");
                foreach ($items as $i) {
                    $ins->execute([$i['name'] ?? '', $i['logo_url'] ?? '', (int)($i['visible'] ?? 1), (int)($i['sort_order'] ?? 0)]);
                }
                echo json_encode(['ok'=>true]); exit;

            // ── SAVE TESTIMONIAL ──
            case 'save_testimonial':
                $id = (int)($_POST['id'] ?? 0);
                $fields = ['name'=>$_POST['name']??'', 'role'=>$_POST['role']??'', 'company'=>$_POST['company']??'', 'content'=>$_POST['content']??'', 'rating'=>(int)($_POST['rating']??5), 'visible'=>(int)($_POST['visible']??1), 'sort_order'=>(int)($_POST['sort_order']??0)];
                if ($id > 0) {
                    $sets = []; $vals = [];
                    foreach ($fields as $k => $v) { $sets[] = "`$k` = ?"; $vals[] = $v; }
                    $vals[] = $id;
                    $db->prepare("UPDATE testimonials SET " . implode(', ', $sets) . " WHERE id = ?")->execute($vals);
                } else {
                    $ks = array_keys($fields); $vs = array_values($fields);
                    $db->prepare("INSERT INTO testimonials (" . implode(', ', $ks) . ") VALUES (" . implode(', ', array_fill(0, count($ks), '?')) . ")")->execute($vs);
                }
                echo json_encode(['ok'=>true]); exit;

            // ── DELETE TESTIMONIAL ──
            case 'delete_testimonial':
                $id = (int)($_POST['id'] ?? 0);
                if ($id <= 0) { echo json_encode(['ok'=>false,'error'=>'Invalid id']); exit; }
                $db->prepare("DELETE FROM testimonials WHERE id = ?")->execute([$id]);
                echo json_encode(['ok'=>true]); exit;

            // ── UPLOAD ──
            case 'upload':
                if (empty($_FILES['file'])) { echo json_encode(['ok'=>false,'error'=>'No file']); exit; }
                $file = $_FILES['file'];
                if ($file['error'] !== UPLOAD_ERR_OK) { echo json_encode(['ok'=>false,'error'=>'Upload error: ' . $file['error']]); exit; }
                if ($file['size'] > 5 * 1024 * 1024) { echo json_encode(['ok'=>false,'error'=>'Max 5MB']); exit; }
                $allowed = ['image/jpeg','image/png','image/gif','image/webp','image/svg+xml','image/svg'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
                if (!in_array($mime, $allowed)) { echo json_encode(['ok'=>false,'error'=>'Images only']); exit; }
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg';
                $newName = uniqid('img_') . '.' . strtolower($ext);
                $dest = UPLOAD_DIR . $newName;
                if (!move_uploaded_file($file['tmp_name'], $dest)) { echo json_encode(['ok'=>false,'error'=>'Move failed']); exit; }
                $db->prepare("INSERT INTO media (filename, original_name, mime_type, file_size) VALUES (?, ?, ?, ?)")->execute([$newName, $file['name'], $mime, $file['size']]);
                echo json_encode(['ok'=>true, 'url' => UPLOAD_URL . $newName, 'filename'=>$newName]); exit;

            // ── SAVE UNIT ──
            case 'save_unit':
                $id = (int)($_POST['id'] ?? 0);
                $fields = [];
                $cols = ['name','slug','description','hero_title','hero_subtitle','hero_video_id','icon','order_num','status',
                    'capabilities','benefits','process','technologies','industries'];
                foreach ($cols as $c) {
                    $fields[$c] = $_POST[$c] ?? null;
                    if ($fields[$c] !== null) $fields[$c] = is_string($fields[$c]) ? trim($fields[$c]) : $fields[$c];
                }
                if ($id > 0) {
                    $sets = []; $vals = [];
                    foreach ($fields as $k => $v) { $sets[] = "`$k` = ?"; $vals[] = $v; }
                    $vals[] = $id;
                    $db->prepare("UPDATE business_units SET " . implode(', ', $sets) . " WHERE id = ?")->execute($vals);
                } else {
                    $ks = array_keys($fields); $vs = array_values($fields);
                    $db->prepare("INSERT INTO business_units (" . implode(', ', $ks) . ") VALUES (" . implode(', ', array_fill(0, count($ks), '?')) . ")")->execute($vs);
                }
                echo json_encode(['ok'=>true]); exit;

            // ── DELETE UNIT ──
            case 'delete_unit':
                $id = (int)($_POST['id'] ?? 0);
                if ($id <= 0) { echo json_encode(['ok'=>false,'error'=>'Invalid id']); exit; }
                $db->prepare("DELETE FROM business_units WHERE id = ?")->execute([$id]);
                echo json_encode(['ok'=>true]); exit;

            // ── SAVE PRODUCT ──
            case 'save_product':
                $id = (int)($_POST['id'] ?? 0);
                $fields = [];
                $cols = ['name','slug','description','short_desc','hero_title','hero_subtitle','icon','category','order_num','status',
                    'overview','problem','solution','features','screenshots','benefits','use_cases','architecture','roadmap','demo_cta_url','demo_cta_text'];
                foreach ($cols as $c) { $fields[$c] = trim($_POST[$c] ?? ''); }
                if ($id > 0) {
                    $sets = []; $vals = [];
                    foreach ($fields as $k => $v) { $sets[] = "`$k` = ?"; $vals[] = $v; }
                    $vals[] = $id;
                    $db->prepare("UPDATE products SET " . implode(', ', $sets) . " WHERE id = ?")->execute($vals);
                } else {
                    $ks = array_keys($fields); $vs = array_values($fields);
                    $db->prepare("INSERT INTO products (" . implode(', ', $ks) . ") VALUES (" . implode(', ', array_fill(0, count($ks), '?')) . ")")->execute($vs);
                }
                echo json_encode(['ok'=>true]); exit;

            // ── DELETE PRODUCT ──
            case 'delete_product':
                $id = (int)($_POST['id'] ?? 0);
                if ($id <= 0) { echo json_encode(['ok'=>false,'error'=>'Invalid id']); exit; }
                $db->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
                echo json_encode(['ok'=>true]); exit;

            // ── SAVE INDUSTRY ──
            case 'save_industry':
                $id = (int)($_POST['id'] ?? 0);
                $fields = [];
                $cols = ['name','slug','description','body','icon','hero_title','hero_subtitle','short_desc','benefits','use_cases','order_num','status'];
                foreach ($cols as $c) { $fields[$c] = trim($_POST[$c] ?? ''); }
                if ($id > 0) {
                    $sets = []; $vals = [];
                    foreach ($fields as $k => $v) { $sets[] = "`$k` = ?"; $vals[] = $v; }
                    $vals[] = $id;
                    $db->prepare("UPDATE industries SET " . implode(', ', $sets) . " WHERE id = ?")->execute($vals);
                } else {
                    $ks = array_keys($fields); $vs = array_values($fields);
                    $db->prepare("INSERT INTO industries (" . implode(', ', $ks) . ") VALUES (" . implode(', ', array_fill(0, count($ks), '?')) . ")")->execute($vs);
                }
                echo json_encode(['ok'=>true]); exit;

            // ── DELETE INDUSTRY ──
            case 'delete_industry':
                $id = (int)($_POST['id'] ?? 0);
                if ($id <= 0) { echo json_encode(['ok'=>false,'error'=>'Invalid id']); exit; }
                $db->prepare("DELETE FROM industries WHERE id = ?")->execute([$id]);
                echo json_encode(['ok'=>true]); exit;

            // ── SAVE RESOURCE ──
            case 'save_resource':
                $id = (int)($_POST['id'] ?? 0);
                $fields = [];
                $cols = ['title','slug','excerpt','content','cover_image','type','author','read_time','featured','meta_title','meta_desc','status','published_at'];
                foreach ($cols as $c) { $fields[$c] = trim($_POST[$c] ?? ''); }
                if ($id > 0) {
                    $sets = []; $vals = [];
                    foreach ($fields as $k => $v) { $sets[] = "`$k` = ?"; $vals[] = $v; }
                    $vals[] = $id;
                    $db->prepare("UPDATE resources SET " . implode(', ', $sets) . " WHERE id = ?")->execute($vals);
                } else {
                    $ks = array_keys($fields); $vs = array_values($fields);
                    $db->prepare("INSERT INTO resources (" . implode(', ', $ks) . ") VALUES (" . implode(', ', array_fill(0, count($ks), '?')) . ")")->execute($vs);
                }
                echo json_encode(['ok'=>true]); exit;

            // ── DELETE RESOURCE ──
            case 'delete_resource':
                $id = (int)($_POST['id'] ?? 0);
                if ($id <= 0) { echo json_encode(['ok'=>false,'error'=>'Invalid id']); exit; }
                $db->prepare("DELETE FROM resources WHERE id = ?")->execute([$id]);
                echo json_encode(['ok'=>true]); exit;

            // ── TOGGLE STATUS ──
            case 'toggle_status':
                $table = $_POST['table'] ?? '';
                $id = (int)($_POST['id'] ?? 0);
                $allowedTables = ['pages','sections','business_units','products','industries','resources','testimonials','clients'];
                if (!in_array($table, $allowedTables) || $id <= 0) { echo json_encode(['ok'=>false,'error'=>'Invalid table or id']); exit; }
                $db->prepare("UPDATE `$table` SET status = 1 - status WHERE id = ?")->execute([$id]);
                $row = $db->prepare("SELECT status FROM `$table` WHERE id = ?");
                $row->execute([$id]);
                echo json_encode(['ok'=>true, 'status'=>$row->fetchColumn()]); exit;

            // ── PRETRANSLATE ──
            case 'pretranslate':
                set_time_limit(0);
                $texts = [];
                $res = $db->query("SELECT value FROM settings WHERE value != '' UNION SELECT field_value FROM section_fields WHERE field_value != ''");
                while ($r = $res->fetch()) $texts[] = $r['value'];
                $count = mbpoPretranslate($texts, 'es');
                echo json_encode(['ok'=>true, 'count'=>$count]); exit;

            // ── BLOG: GET POSTS (for scheduler) ──
            case 'blog_get_posts':
                $posts = $db->query("SELECT id, title, slug, status, published_at FROM resources WHERE type='article' AND status != 0 ORDER BY published_at DESC")->fetchAll();
                echo json_encode(['ok'=>true, 'posts'=>$posts]); exit;

            // ── DISTRO: SCHEDULE POST ──
            case 'distro_schedule':
                $body = json_decode(file_get_contents('php://input'), true);
                $postId = (int)($body['post_id'] ?? 0);
                $platforms = $body['platforms'] ?? [];
                $scheduledAt = $body['scheduled_at'] ?? '';
                if (!$postId || empty($platforms) || !$scheduledAt) {
                    echo json_encode(['ok'=>false, 'error'=>'post_id, platforms, and scheduled_at required']); exit;
                }
                $post = $db->prepare("SELECT title FROM resources WHERE id = ?")->execute([$postId])->fetch();
                $title = $post ? $post['title'] : 'Unknown';
                $ins = $db->prepare("INSERT INTO distro_schedule (post_id, title, platforms, scheduled_at, status) VALUES (?,?,?,?,'pending')");
                $ins->execute([$postId, $title, json_encode($platforms), $scheduledAt]);
                echo json_encode(['ok'=>true, 'id'=>(int)$db->lastInsertId()]); exit;

            // ── DISTRO: GET SCHEDULE ──
            case 'distro_get_schedule':
                $schedule = $db->query("SELECT * FROM distro_schedule ORDER BY scheduled_at DESC LIMIT 50")->fetchAll();
                foreach ($schedule as &$s) { $s['platforms'] = json_decode($s['platforms'] ?? '[]', true); }
                echo json_encode(['ok'=>true, 'schedule'=>$schedule]); exit;

            // ── DISTRO: PUBLISH NOW ──
            case 'distro_publish':
                $body = json_decode(file_get_contents('php://input'), true);
                $postId = (int)($body['post_id'] ?? 0);
                $platforms = $body['platforms'] ?? [];
                if (!$postId || empty($platforms)) {
                    echo json_encode(['ok'=>false, 'error'=>'post_id and platforms required']); exit;
                }
                $post = $db->prepare("SELECT * FROM resources WHERE id = ?")->execute([$postId])->fetch();
                if (!$post) { echo json_encode(['ok'=>false, 'error'=>'Post not found']); exit; }
                $results = [];
                foreach ($platforms as $pf) {
                    $status = 'pending'; $err = '';
                    try {
                        if ($pf === 'intsolcom') {
                            // Already published — update status
                            $db->prepare("UPDATE resources SET status=1, published_at=NOW() WHERE id=?")->execute([$postId]);
                            $status = 'success';
                        } else {
                            // LinkedIn — log attempt, actual API call in cron
                            $tok = $db->prepare("SELECT access_token FROM distro_tokens WHERE platform=? AND status='active' LIMIT 1");
                            $tok->execute([$pf]);
                            if ($tok->fetch()) $status = 'success';
                            else { $status = 'failed'; $err = 'No active token for '.$pf; }
                        }
                    } catch (Exception $e) { $status = 'failed'; $err = $e->getMessage(); }
                    $db->prepare("INSERT INTO distro_logs (post_id, platform, action, status, error_message, executed_at) VALUES (?,?,?,'publish',?,?,NOW())")->execute([$postId, $pf, $status, $err]);
                    $results[] = ['platform'=>$pf, 'status'=>$status, 'error'=>$err];
                }
                echo json_encode(['ok'=>true, 'results'=>$results]); exit;

            // ── DISTRO: DIAGNOSE ──
            case 'distro_diagnose':
                $results = [];
                // Check intsolcom
                $start = microtime(true);
                $ch = curl_init(SITE_URL . '/api/blog');
                curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_TIMEOUT=>5, CURLOPT_HTTPHEADER=>['Authorization: Bearer '.(defined('API_BLOG_TOKEN')?API_BLOG_TOKEN:'intsolcom_blog_api_2026')]]);
                $resp = curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $ms = round((microtime(true)-$start)*1000);
                curl_close($ch);
                $results['intsolcom'] = ['ok'=>$code===200, 'code'=>$code, 'ms'=>$ms, 'endpoint'=>'GET /api/blog'];

                // Check LinkedIn tokens
                foreach (['linkedin_me','linkedin_co'] as $pf) {
                    $tok = $db->prepare("SELECT access_token, expires_at FROM distro_tokens WHERE platform=? AND status='active' LIMIT 1");
                    $tok->execute([$pf]); $t = $tok->fetch();
                    if ($t && $t['access_token']) {
                        $start = microtime(true);
                        $ch = curl_init('https://api.linkedin.com/v2/userinfo');
                        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_TIMEOUT=>8, CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$t['access_token']]]);
                        curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        $ms = round((microtime(true)-$start)*1000);
                        curl_close($ch);
                        $results[$pf] = ['ok'=>$code===200, 'code'=>$code, 'ms'=>$ms, 'endpoint'=>'GET /v2/userinfo'];
                    } else {
                        $results[$pf] = ['ok'=>false, 'code'=>0, 'ms'=>0, 'error'=>'No token configured'];
                    }
                }

                // Check DeepSeek
                $start = microtime(true);
                $ch = curl_init('https://api.deepseek.com/v1/models');
                curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_TIMEOUT=>5, CURLOPT_HTTPHEADER=>['Authorization: Bearer '.(defined('DEEPSEEK_API_KEY')?DEEPSEEK_API_KEY:'')]]);
                curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $ms = round((microtime(true)-$start)*1000);
                curl_close($ch);
                $results['deepseek'] = ['ok'=>$code===200, 'code'=>$code, 'ms'=>$ms, 'endpoint'=>'GET /v1/models'];

                $allOk = !in_array(false, array_column($results, 'ok'));
                echo json_encode(['ok'=>true, 'all_ok'=>$allOk, 'results'=>$results]); exit;

            // ── DISTRO: SAVE TOKEN ──
            case 'distro_save_token':
                $platform = $_POST['platform'] ?? '';
                $accessToken = $_POST['access_token'] ?? '';
                $refreshToken = $_POST['refresh_token'] ?? '';
                $expiresAt = $_POST['expires_at'] ?? null;
                if (!$platform || !$accessToken) { echo json_encode(['ok'=>false,'error'=>'platform and access_token required']); exit; }
                $db->prepare("INSERT INTO distro_tokens (platform, access_token, refresh_token, expires_at, status) VALUES (?,?,?,?,'active') ON DUPLICATE KEY UPDATE access_token=?, refresh_token=?, expires_at=?, status='active'")
                   ->execute([$platform, $accessToken, $refreshToken, $expiresAt, $accessToken, $refreshToken, $expiresAt]);
                echo json_encode(['ok'=>true]); exit;

            // ── GET TABLE DATA ──
            case 'get_table_data':
                $table = $_GET['table'] ?? '';
                $id = (int)($_GET['id'] ?? 0);
                $allowedGetTables = ['sections','business_units','products','industries','resources','testimonials','nav_items','clients'];
                if (!in_array($table, $allowedGetTables) || $id <= 0) { echo json_encode(['ok'=>false,'error'=>'Invalid']); exit; }
                if ($table === 'sections') {
                    $st = $db->prepare("SELECT s.* FROM sections s WHERE s.id = ?");
                    $st->execute([$id]);
                    $row = $st->fetch();
                    if (!$row) { echo json_encode(['ok'=>false,'error'=>'Not found']); exit; }
                    $ff = $db->prepare("SELECT field_key, field_value FROM section_fields WHERE section_id = ?");
                    $ff->execute([$id]);
                    $row['fields'] = $ff->fetchAll(PDO::FETCH_KEY_PAIR);
                } else {
                    $st = $db->prepare("SELECT * FROM `$table` WHERE id = ?");
                    $st->execute([$id]);
                    $row = $st->fetch();
                }
                echo json_encode(['ok'=>true, 'data'=>$row ?: null]); exit;

            // ── DELETE MEDIA ──
            case 'delete_media':
                $id = (int)($_POST['id'] ?? 0);
                if ($id <= 0) { echo json_encode(['ok'=>false,'error'=>'Invalid id']); exit; }
                $row = $db->prepare("SELECT filename FROM media WHERE id = ?")->execute([$id])->fetch();
                if ($row) @unlink(UPLOAD_DIR . $row['filename']);
                $db->prepare("DELETE FROM media WHERE id = ?")->execute([$id]);
                echo json_encode(['ok'=>true]); exit;

            // ── GET SECTIONS (list for a page) ──
            case 'get_sections':
                $pageId = (int)($_GET['page_id'] ?? 0);
                if ($pageId <= 0) { echo json_encode(['ok'=>false,'error'=>'Missing page_id']); exit; }
                $secs = $db->prepare("SELECT * FROM sections WHERE page_id = ? ORDER BY sort_order ASC");
                $secs->execute([$pageId]);
                $sections = $secs->fetchAll();
                foreach ($sections as &$sec) {
                    $ff = $db->prepare("SELECT field_key, field_value FROM section_fields WHERE section_id = ?");
                    $ff->execute([$sec['id']]);
                    $sec['fields'] = $ff->fetchAll(PDO::FETCH_KEY_PAIR);
                }
                echo json_encode(['ok'=>true, 'sections'=>$sections]); exit;

            default:
                echo json_encode(['ok'=>false,'error'=>'Unknown action']);
        }
    } catch (Exception $e) {
        echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
    }
    exit;
}

// ─── LOGIN PAGE ───
if (!$isLoggedIn) {
    ?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>INTSOLCOM — Admin</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',system-ui,-apple-system,sans-serif;background:#0B1120;color:#F8FAFC;display:flex;align-items:center;justify-content:center;min-height:100vh}
        .login-box{background:#1E293B;border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:40px;width:100%;max-width:400px}
        .login-box h1{font-size:1.5rem;font-weight:800;text-align:center;margin-bottom:4px}
        .login-box h1 span{color:#00C896}
        .login-box .sub{color:rgba(255,255,255,.35);font-size:.8rem;text-align:center;margin-bottom:32px}
        .field{margin-bottom:16px}
        .field label{display:block;font-size:.8rem;color:rgba(255,255,255,.5);margin-bottom:6px;font-weight:500}
        .field input{width:100%;padding:12px 14px;background:#0F172A;border:1px solid rgba(255,255,255,.1);border-radius:8px;color:#F8FAFC;font-size:.9rem;outline:none;transition:border .2s}
        .field input:focus{border-color:#00C896}
        .btn{display:block;width:100%;padding:12px;background:#00C896;color:#0F172A;border:none;border-radius:8px;font-weight:700;font-size:.9rem;cursor:pointer;transition:opacity .2s}
        .btn:hover{opacity:.9}
        .error{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.25);border-radius:8px;padding:12px;color:#ef4444;font-size:.82rem;margin-bottom:20px;text-align:center}
    </style>
    </head><body>
    <form method="post" class="login-box"><h1>INTSOL<span>COM</span></h1><div class="sub">Administration Panel</div>
    <?php if (!empty($loginError)): ?><div class="error"><?=h($loginError)?></div><?php endif; ?>
    <div class="field"><label>Username</label><input type="text" name="username" required autofocus></div>
    <div class="field"><label>Password</label><input type="password" name="password" required></div>
    <button type="submit" class="btn">Sign In</button></form>
    </body></html><?php
    exit;
}

// ─── FETCH DASHBOARD DATA ───
$db = db();
$pageCount = $db->query("SELECT COUNT(*) FROM pages")->fetchColumn();
$productCount = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
$unitCount = $db->query("SELECT COUNT(*) FROM business_units")->fetchColumn();
$pages = $db->query("SELECT * FROM pages ORDER BY title")->fetchAll();
$translationCount = $db->query("SELECT COUNT(*) FROM translations")->fetchColumn();
$mediaItems = $db->query("SELECT * FROM media ORDER BY uploaded_at DESC")->fetchAll();
$navItems = $db->query("SELECT * FROM nav_items ORDER BY sort_order")->fetchAll();
$clients = $db->query("SELECT * FROM clients ORDER BY sort_order")->fetchAll();
$testimonials = $db->query("SELECT * FROM testimonials ORDER BY sort_order")->fetchAll();
$units = $db->query("SELECT * FROM business_units ORDER BY order_num")->fetchAll();
$products = $db->query("SELECT * FROM products ORDER BY order_num")->fetchAll();
$industries = $db->query("SELECT * FROM industries ORDER BY order_num")->fetchAll();
$resources = $db->query("SELECT * FROM resources ORDER BY published_at DESC")->fetchAll();

// Settings by category
$allSettings = $db->query("SELECT * FROM settings ORDER BY `key`")->fetchAll(PDO::FETCH_KEY_PAIR);
$settingCats = [
    'General' => ['site_name','site_tagline','site_desc'],
    'Colors' => ['color_bg','color_surface','color_surface2','color_dark','color_mid','color_light','color_accent','color_accent_dk','color_secondary','color_purple','color_white'],
    'Typography' => ['font_display','font_body'],
    'Navigation' => ['nav_h','nav_h_scrolled','nav_bg','nav_bg_scrolled','nav_blur'],
    'Logo' => ['logo_text','logo_accent','logo_text_color','logo_accent_color'],
    'Hero' => [],
    'Video' => [],
    'Effects' => [],
    'Contact' => ['contact_usa_phone','contact_usa_address','contact_col_email','contact_col_address','contact_whatsapp'],
    'Footer' => ['footer_desc','footer_copyright'],
    'Social' => ['social_linkedin'],
    'Blog' => [],
];
// Fill categories with actual settings
$categorizedSettings = [];
$seen = [];
foreach ($settingCats as $cat => $defined) {
    $categorizedSettings[$cat] = [];
    foreach ($defined as $k) { $categorizedSettings[$cat][$k] = $allSettings[$k] ?? ''; $seen[$k] = true; }
}
foreach ($allSettings as $k => $v) {
    if (!isset($seen[$k])) { $categorizedSettings['Other'][$k] = $v; }
}
if (empty($categorizedSettings['Other'])) unset($categorizedSettings['Other']);
$allSettings = []; // Free memory

function sectionFieldsForType($type) {
    switch ($type) {
        case 'hero': return ['eyebrow','h1_line1','h1_line2','h1_line3','description','btn1_text','btn1_url','btn2_text','btn2_url','trust_text','video_id','overlay'];
        case 'ecosystem': return ['label','title','description'];
        case 'stats': return ['label','title','items'];
        case 'products_grid': return ['label','title','subtitle','button_text','button_url'];
        case 'capabilities': return ['label','title','items'];
        case 'industries_grid': return ['label','title','items'];
        case 'comparison': return ['label','title','left_title','right_title','items'];
        case 'cta': return ['h1','h2','desc','btn1_text','btn1_url','btn2_text','btn2_url','note'];
        case 'testimonials': return ['title','subtitle'];
        case 'text_image': return ['label','title','description','btn_text','btn_url','image_url','bg_dark'];
        case 'faq': return ['title','items'];
        default: return [];
    }
}
function sectionTypeLabel($t) {
    $labels = ['hero'=>'Hero','ecosystem'=>'Ecosystem','stats'=>'Stats','products_grid'=>'Products Grid','capabilities'=>'Capabilities','industries_grid'=>'Industries Grid',
        'comparison'=>'Comparison','cta'=>'CTA','testimonials'=>'Testimonials','text_image'=>'Text + Image','faq'=>'FAQ'];
    return $labels[$t] ?? ucfirst($t);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>INTSOLCOM — Admin Panel</title>
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Inter',system-ui,-apple-system,sans-serif;background:#0F172A;color:#E2E8F0;display:flex;min-height:100vh}
a{color:#00C896;text-decoration:none}
/* Sidebar */
.sidebar{width:240px;background:#0B1120;border-right:1px solid rgba(255,255,255,.06);padding:24px 0;display:flex;flex-direction:column;flex-shrink:0;position:sticky;top:0;height:100vh;overflow-y:auto}
.sidebar-logo{padding:0 20px 24px;font-size:1.15rem;font-weight:800;color:#F8FAFC}
.sidebar-logo span{color:#00C896}
.sidebar-nav{flex:1;display:flex;flex-direction:column}
.sidebar-nav a{display:block;padding:10px 20px;color:rgba(255,255,255,.55);font-size:.84rem;transition:all .15s;border-left:3px solid transparent;cursor:pointer}
.sidebar-nav a:hover,.sidebar-nav a.active{color:#F8FAFC;background:rgba(0,200,150,.06);border-left-color:#00C896}
.sidebar-nav a.logout{color:rgba(255,255,255,.35);margin-top:auto;border-top:1px solid rgba(255,255,255,.06);padding-top:16px}
.sidebar-stats{padding:16px 20px;font-size:.72rem;color:rgba(255,255,255,.25);border-top:1px solid rgba(255,255,255,.06)}
/* Main */
.main{flex:1;overflow-y:auto;padding:32px}
.header{display:flex;align-items:center;gap:16px;margin-bottom:32px}
.header h2{font-size:1.35rem;font-weight:700;color:#F8FAFC}
/* Stats row */
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:32px}
.stat-card{background:#1E293B;border:1px solid rgba(255,255,255,.06);border-radius:12px;padding:20px}
.stat-card .val{font-size:2rem;font-weight:800;color:#00C896;line-height:1}
.stat-card .lbl{font-size:.76rem;color:rgba(255,255,255,.4);margin-top:6px}
/* Panels */
.panel{background:#1E293B;border:1px solid rgba(255,255,255,.06);border-radius:12px;padding:24px;margin-bottom:24px}
.panel h3{font-size:1rem;font-weight:700;color:#F8FAFC;margin-bottom:16px}
.h3-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}
.h3-row h3{margin-bottom:0}
/* Tables */
table{width:100%;border-collapse:collapse;font-size:.84rem}
th,td{padding:10px 12px;text-align:left;border-bottom:1px solid rgba(255,255,255,.05)}
th{color:rgba(255,255,255,.4);font-weight:600;font-size:.76rem;text-transform:uppercase;letter-spacing:.5px}
tr:hover td{background:rgba(255,255,255,.015)}
td{color:#CBD5E1}
/* Badges */
.badge{padding:3px 8px;border-radius:100px;font-size:.7rem;font-weight:600}
.badge-active{background:rgba(0,200,150,.15);color:#00C896}
.badge-inactive{background:rgba(239,68,68,.12);color:#ef4444}
.status-toggle{cursor:pointer;font-size:.76rem;background:none;border:1px solid rgba(255,255,255,.15);border-radius:6px;padding:4px 10px;color:rgba(255,255,255,.5);transition:all .15s}
.status-toggle:hover{border-color:#00C896;color:#00C896}
/* Forms */
.form-group{margin-bottom:14px}
.form-group label{display:block;font-size:.76rem;color:rgba(255,255,255,.45);margin-bottom:4px;font-weight:500}
.form-group input,.form-group select,.form-group textarea{width:100%;padding:9px 12px;background:#0F172A;border:1px solid rgba(255,255,255,.1);border-radius:8px;color:#E2E8F0;font-size:.84rem;font-family:inherit;outline:none;transition:border .15s}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:#00C896}
.form-group textarea{resize:vertical;min-height:80px}
.form-group .help{font-size:.72rem;color:rgba(255,255,255,.3);margin-top:3px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
/* Buttons */
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:8px;font-size:.82rem;font-weight:600;cursor:pointer;transition:all .15s;border:none;font-family:inherit}
.btn-sm{padding:4px 10px;font-size:.74rem}
.btn-primary{background:#00C896;color:#0F172A}
.btn-primary:hover{opacity:.9}
.btn-outline{background:transparent;border:1px solid rgba(255,255,255,.15);color:rgba(255,255,255,.6)}
.btn-outline:hover{border-color:#00C896;color:#00C896}
.btn-danger{border:1px solid rgba(239,68,68,.3);color:#ef4444;background:transparent}
.btn-danger:hover{background:rgba(239,68,68,.1)}
.btn-row{display:flex;gap:8px;flex-wrap:wrap;margin-top:12px}
/* Modal */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:1000;align-items:flex-start;justify-content:center;padding-top:60px}
.modal-overlay.show{display:flex}
.modal{background:#1E293B;border:1px solid rgba(255,255,255,.1);border-radius:16px;width:100%;max-width:660px;max-height:80vh;overflow-y:auto;padding:32px;position:relative}
.modal h3{margin-bottom:20px;font-size:1.1rem;color:#F8FAFC}
.modal-close{position:absolute;top:16px;right:16px;background:none;border:none;color:rgba(255,255,255,.4);cursor:pointer;font-size:1.2rem;padding:4px 8px;border-radius:6px}
.modal-close:hover{color:#F8FAFC;background:rgba(255,255,255,.05)}
/* Tabs */
.tab-content{display:none}
.tab-content.active{display:block}
/* Nav list */
.nav-item-row{display:flex;align-items:center;gap:10px;padding:8px 12px;background:#0F172A;border:1px solid rgba(255,255,255,.06);border-radius:8px;margin-bottom:6px}
.nav-item-row .info{flex:1;font-size:.84rem}
.nav-item-row .info .meta{font-size:.72rem;color:rgba(255,255,255,.3)}
.nav-item-row .btn-sm{padding:3px 8px;font-size:.7rem}
/* Media grid */
.media-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px}
.media-card{background:#0F172A;border-radius:8px;overflow:hidden;border:1px solid rgba(255,255,255,.06)}
.media-card img{width:100%;height:100px;object-fit:cover;display:block}
.media-card .info{padding:8px;font-size:.7rem;display:flex;align-items:center;justify-content:space-between;gap:4px}
.media-card .info span{color:rgba(255,255,255,.35);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
/* Collapsible */
.collapsible-header{cursor:pointer;display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:#0F172A;border:1px solid rgba(255,255,255,.06);border-radius:8px;margin-bottom:4px;font-size:.9rem;font-weight:600;color:#F8FAFC;transition:all .15s}
.collapsible-header:hover{border-color:rgba(255,255,255,.15)}
.collapsible-header .arrow{transition:transform .2s}
.collapsible-header.open .arrow{transform:rotate(90deg)}
.collapsible-body{display:none;padding:16px;background:rgba(0,0,0,.15);border-radius:0 0 8px 8px;margin-bottom:8px}
.collapsible-body.show{display:block}
/* Toast */
.toast{position:fixed;bottom:24px;right:24px;background:#00C896;color:#0F172A;padding:12px 20px;border-radius:100px;font-size:.82rem;font-weight:600;z-index:2000;box-shadow:0 8px 32px rgba(0,200,150,.2);transform:translateY(100px);opacity:0;transition:all .3s}
.toast.show{transform:translateY(0);opacity:1}
/* Empty state */
.empty{text-align:center;padding:32px;color:rgba(255,255,255,.25);font-size:.84rem}
/* Sort buttons */
.sort-btns{display:flex;flex-direction:column;gap:2px;margin-left:6px}
.sort-btns button{background:none;border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.4);padding:1px 6px;cursor:pointer;border-radius:3px;font-size:.65rem;line-height:1}
.sort-btns button:hover{color:#00C896;border-color:#00C896}
/* Inline check */
.inline-check{display:flex;align-items:center;gap:6px;font-size:.78rem;color:rgba(255,255,255,.5)}
.inline-check input[type=checkbox]{accent-color:#00C896}
/* Scrollbar */
::-webkit-scrollbar{width:6px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:3px}
</style>
</head>
<body>

<aside class="sidebar">
  <div class="sidebar-logo">INTSOL<span>COM</span></div>
  <nav class="sidebar-nav">
    <a data-tab="dashboard" class="active">Dashboard</a>
    <a data-tab="pages">Pages &amp; Sections</a>
    <a data-tab="nav">Navigation</a>
    <a data-tab="units">Business Units</a>
    <a data-tab="products">Products</a>
    <a data-tab="industries">Industries</a>
    <a data-tab="resources">Resources</a>
    <a data-tab="testimonials">Testimonials</a>
    <a data-tab="clients">Clients</a>
    <a data-tab="media">Media</a>
    <a data-tab="settings">Settings</a>
    <a data-tab="translations">Translations</a>
    <a data-tab="scheduler">📅 Scheduler</a>
    <a href="?logout" class="logout">Logout</a>
  </nav>
  <div class="sidebar-stats">v2.0 &middot; <?=h(ADMIN_USER)?></div>
</aside>

<main class="main">

<!-- ============ DASHBOARD TAB ============ -->
<div class="tab-content active" id="tab-dashboard">
  <div class="header"><h2>Dashboard</h2></div>
  <div class="stats">
    <div class="stat-card"><div class="val"><?=$pageCount?></div><div class="lbl">Pages</div></div>
    <div class="stat-card"><div class="val"><?=$productCount?></div><div class="lbl">Products</div></div>
    <div class="stat-card"><div class="val"><?=$unitCount?></div><div class="lbl">Business Units</div></div>
    <div class="stat-card"><div class="val"><?=$translationCount?></div><div class="lbl">Translations</div></div>
  </div>
  <div class="panel"><h3>Quick Actions</h3>
    <div class="btn-row">
      <button class="btn btn-outline" onclick="switchTab('pages')">Manage Pages</button>
      <button class="btn btn-outline" onclick="switchTab('nav')">Edit Navigation</button>
      <button class="btn btn-outline" onclick="switchTab('settings')">Site Settings</button>
      <button class="btn btn-outline" onclick="clearCache()">Clear Cache</button>
    </div>
  </div>
</div>

<!-- ============ PAGES & SECTIONS TAB ============ -->
<div class="tab-content" id="tab-pages">
  <div class="header"><h2>Pages &amp; Sections</h2></div>
  <div class="panel">
    <div class="h3-row"><h3>Sections</h3>
      <div style="display:flex;gap:10px;align-items:center">
        <select id="pageSelect" onchange="loadSections()" style="padding:6px 10px;background:#0F172A;border:1px solid rgba(255,255,255,.1);border-radius:6px;color:#E2E8F0;font-size:.82rem">
          <?php foreach ($pages as $p): ?><option value="<?=$p['id']?>"><?=h($p['slug'])?> — <?=h($p['title'])?></option><?php endforeach; ?>
        </select>
        <select id="newSectionType" style="padding:6px 10px;background:#0F172A;border:1px solid rgba(255,255,255,.1);border-radius:6px;color:#E2E8F0;font-size:.82rem">
          <option value="hero">Hero</option><option value="ecosystem">Ecosystem</option><option value="stats">Stats</option>
          <option value="products_grid">Products Grid</option><option value="capabilities">Capabilities</option>
          <option value="industries_grid">Industries Grid</option><option value="comparison">Comparison</option>
          <option value="cta">CTA</option><option value="testimonials">Testimonials</option>
          <option value="text_image">Text + Image</option><option value="faq">FAQ</option>
        </select>
        <button class="btn btn-primary btn-sm" onclick="addSection()">+ Add Section</button>
      </div>
    </div>
    <div id="sectionsList"><div class="empty">Select a page to view sections</div></div>
  </div>
</div>

<!-- ============ NAVIGATION TAB ============ -->
<div class="tab-content" id="tab-nav">
  <div class="header"><h2>Navigation</h2></div>
  <div class="panel">
    <div class="h3-row"><h3>Menu Items</h3><button class="btn btn-primary btn-sm" onclick="openNavModal()">+ Add Item</button></div>
    <div id="navList">
      <?php foreach ($navItems as $ni): ?>
      <div class="nav-item-row" data-id="<?=$ni['id']?>">
        <div class="info"><strong><?=h($ni['text'])?></strong> <span class="meta"><?=h($ni['url'])?> <?=$ni['is_cta']?'CTA':''?> <?=$ni['visible']?'':'[Hidden]'?></span></div>
        <button class="btn btn-outline btn-sm" onclick="openNavModal(<?=$ni['id']?>)">Edit</button>
        <button class="btn btn-outline btn-sm" onclick="moveNav(<?=$ni['id']?>,'up')">&#8593;</button>
        <button class="btn btn-outline btn-sm" onclick="moveNav(<?=$ni['id']?>,'down')">&#8595;</button>
      </div>
      <?php endforeach; ?>
      <?php if (empty($navItems)): ?><div class="empty">No navigation items</div><?php endif; ?>
    </div>
  </div>
</div>

<!-- ============ BUSINESS UNITS TAB ============ -->
<div class="tab-content" id="tab-units">
  <div class="header"><h2>Business Units</h2></div>
  <div class="panel"><div class="h3-row"><h3>All Units</h3><button class="btn btn-primary btn-sm" onclick="openUnitModal()">+ Add Unit</button></div>
    <table><thead><tr><th>Name</th><th>Slug</th><th>Status</th><th></th></tr></thead><tbody>
      <?php foreach ($units as $u): ?><tr><td><strong><?=h($u['name'])?></strong></td><td><?=h($u['slug'])?></td>
        <td><button class="status-toggle" onclick="toggleStatus('business_units',<?=$u['id']?>,this)"><?=$u['status']?'Active':'Inactive'?></button></td>
        <td><button class="btn btn-outline btn-sm" onclick="openUnitModal(<?=$u['id']?>)">Edit</button> <button class="btn btn-danger btn-sm" onclick="deleteUnit(<?=$u['id']?>)">Del</button></td></tr>
      <?php endforeach; ?></tbody></table>
  </div>
</div>

<!-- ============ PRODUCTS TAB ============ -->
<div class="tab-content" id="tab-products">
  <div class="header"><h2>Products</h2></div>
  <div class="panel"><div class="h3-row"><h3>All Products</h3><button class="btn btn-primary btn-sm" onclick="openProductModal()">+ Add Product</button></div>
    <table><thead><tr><th>Name</th><th>Category</th><th>Status</th><th></th></tr></thead><tbody>
      <?php foreach ($products as $p): ?><tr><td><strong><?=h($p['name'])?></strong></td><td><?=h($p['category'])?></td>
        <td><button class="status-toggle" onclick="toggleStatus('products',<?=$p['id']?>,this)"><?=$p['status']?'Active':'Inactive'?></button></td>
        <td><button class="btn btn-outline btn-sm" onclick="openProductModal(<?=$p['id']?>)">Edit</button> <button class="btn btn-danger btn-sm" onclick="deleteProduct(<?=$p['id']?>)">Del</button></td></tr>
      <?php endforeach; ?></tbody></table>
  </div>
</div>

<!-- ============ INDUSTRIES TAB ============ -->
<div class="tab-content" id="tab-industries">
  <div class="header"><h2>Industries</h2></div>
  <div class="panel"><div class="h3-row"><h3>All Industries</h3><button class="btn btn-primary btn-sm" onclick="openIndustryModal()">+ Add Industry</button></div>
    <table><thead><tr><th>Name</th><th>Status</th><th></th></tr></thead><tbody>
      <?php foreach ($industries as $ind): ?><tr><td><strong><?=h($ind['name'])?></strong></td>
        <td><button class="status-toggle" onclick="toggleStatus('industries',<?=$ind['id']?>,this)"><?=$ind['status']?'Active':'Inactive'?></button></td>
        <td><button class="btn btn-outline btn-sm" onclick="openIndustryModal(<?=$ind['id']?>)">Edit</button> <button class="btn btn-danger btn-sm" onclick="deleteIndustry(<?=$ind['id']?>)">Del</button></td></tr>
      <?php endforeach; ?></tbody></table>
  </div>
</div>

<!-- ============ RESOURCES TAB ============ -->
<div class="tab-content" id="tab-resources">
  <div class="header"><h2>Resources</h2></div>
  <div class="panel"><div class="h3-row"><h3>All Resources</h3><button class="btn btn-primary btn-sm" onclick="openResourceModal()">+ Add Resource</button></div>
    <table><thead><tr><th>Title</th><th>Type</th><th>Status</th><th>Published</th><th></th></tr></thead><tbody>
      <?php foreach ($resources as $r): ?><tr><td><strong><?=h($r['title'])?></strong></td><td><?=h($r['type'])?></td>
        <td><button class="status-toggle" onclick="toggleStatus('resources',<?=$r['id']?>,this)"><?=$r['status']?'Active':'Inactive'?></button></td>
        <td><?=h($r['published_at'])?></td>
        <td><button class="btn btn-outline btn-sm" onclick="openResourceModal(<?=$r['id']?>)">Edit</button> <button class="btn btn-danger btn-sm" onclick="deleteResource(<?=$r['id']?>)">Del</button></td></tr>
      <?php endforeach; ?></tbody></table>
  </div>
</div>

<!-- ============ TESTIMONIALS TAB ============ -->
<div class="tab-content" id="tab-testimonials">
  <div class="header"><h2>Testimonials</h2></div>
  <div class="panel"><div class="h3-row"><h3>All Testimonials</h3><button class="btn btn-primary btn-sm" onclick="openTestimonialModal()">+ Add</button></div>
    <table><thead><tr><th>Name</th><th>Role/Company</th><th>Rating</th><th>Status</th><th></th></tr></thead><tbody>
      <?php foreach ($testimonials as $t): ?><tr><td><strong><?=h($t['name'])?></strong></td><td><?=h($t['role'])?> / <?=h($t['company'])?></td>
        <td><?=str_repeat('★',$t['rating']).str_repeat('☆',5-$t['rating'])?></td>
        <td><button class="status-toggle" onclick="toggleStatus('testimonials',<?=$t['id']?>,this)"><?=$t['visible']?'Active':'Inactive'?></button></td>
        <td><button class="btn btn-outline btn-sm" onclick="openTestimonialModal(<?=$t['id']?>)">Edit</button> <button class="btn btn-danger btn-sm" onclick="deleteTestimonial(<?=$t['id']?>)">Del</button></td></tr>
      <?php endforeach; ?></tbody></table>
  </div>
</div>

<!-- ============ CLIENTS TAB ============ -->
<div class="tab-content" id="tab-clients">
  <div class="header"><h2>Clients</h2></div>
  <div class="panel">
    <div class="h3-row"><h3>Client Logos</h3><button class="btn btn-primary btn-sm" onclick="addClientRow()">+ Add</button></div>
    <div id="clientsList">
      <?php foreach ($clients as $i => $c): ?>
      <div class="nav-item-row" data-idx="<?=$i?>">
        <div class="info">
          <input type="text" value="<?=h($c['name'])?>" placeholder="Client name" style="background:#0F172A;border:1px solid rgba(255,255,255,.1);border-radius:6px;padding:4px 8px;color:#E2E8F0;width:200px;font-size:.82rem" onchange="updateClientField(this,'name')">
          <input type="text" value="<?=h($c['logo_url'])?>" placeholder="Logo URL" style="background:#0F172A;border:1px solid rgba(255,255,255,.1);border-radius:6px;padding:4px 8px;color:#E2E8F0;width:260px;font-size:.82rem;margin-left:8px" onchange="updateClientField(this,'logo_url')">
        </div>
        <label class="inline-check" style="margin-left:8px"><input type="checkbox" <?=$c['visible']?'checked':''?> onchange="updateClientField(this,'visible',this.checked?1:0)">Visible</label>
        <button class="btn btn-outline btn-sm" onclick="removeClientRow(this)">X</button>
      </div>
      <?php endforeach; ?>
      <?php if (empty($clients)): ?><div class="empty" id="clientsEmpty">No clients</div><?php endif; ?>
    </div>
    <div class="btn-row"><button class="btn btn-primary" onclick="saveClients()">Save Clients</button></div>
  </div>
</div>

<!-- ============ MEDIA TAB ============ -->
<div class="tab-content" id="tab-media">
  <div class="header"><h2>Media Library</h2></div>
  <div class="panel">
    <h3>Upload File</h3>
    <div class="form-row">
      <div class="form-group"><input type="file" id="uploadFile" accept="image/*" style="padding:7px"></div>
      <div><button class="btn btn-primary" onclick="uploadFile()">Upload</button></div>
    </div>
    <div style="margin-top:24px"><h3 style="margin-bottom:12px">Library</h3>
      <div class="media-grid">
        <?php foreach ($mediaItems as $m): ?>
        <div class="media-card" id="media-<?=$m['id']?>">
          <img src="<?=h(UPLOAD_URL.$m['filename'])?>" alt="">
          <div class="info">
            <span title="<?=h($m['original_name'])?>"><?=h($m['original_name'])?></span>
            <button class="btn btn-outline btn-sm" onclick="copyUrl('<?=h(UPLOAD_URL.$m['filename'])?>')" title="Copy URL">&#128279;</button>
            <button class="btn btn-danger btn-sm" onclick="deleteMedia(<?=$m['id']?>,this)" title="Delete">&#10005;</button>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<!-- ============ SETTINGS TAB ============ -->
<div class="tab-content" id="tab-settings">
  <div class="header"><h2>Settings</h2></div>
  <?php foreach ($categorizedSettings as $cat => $settings): ?>
  <div class="collapsible-outer" style="margin-bottom:8px">
    <div class="collapsible-header" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('show')">
      <?=h($cat)?> <span class="arrow">&#9654;</span>
    </div>
    <div class="collapsible-body">
      <?php foreach ($settings as $k => $v): ?>
      <div class="form-group">
        <label><?=h($k)?></label>
        <?php if (strlen($v) > 200): ?>
        <textarea data-key="<?=h($k)?>" rows="3"><?=h($v)?></textarea>
        <?php else: ?>
        <input type="text" data-key="<?=h($k)?>" value="<?=h($v)?>">
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
      <button class="btn btn-primary btn-sm" onclick="saveSettingsGroup(this)">Save <?=h($cat)?> Settings</button>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- ============ TRANSLATIONS TAB ============ -->
<div class="tab-content" id="tab-translations">
  <div class="header"><h2>Translations</h2></div>
  <div class="panel">
    <div class="stat-card" style="display:inline-block;margin-bottom:16px"><div class="val"><?=$translationCount?></div><div class="lbl">Cached Translations</div></div>
    <p style="margin-bottom:16px;color:rgba(255,255,255,.5);font-size:.84rem">Pre-translate all site content (settings + section fields) from English to Spanish using Claude AI. Each unique string is translated once and cached.</p>
    <button class="btn btn-primary" id="pretranslateBtn" onclick="pretranslate()">Pre-translate All Content</button>
    <span id="pretranslateResult" style="margin-left:12px;font-size:.82rem"></span>
  </div>
</div>

<!-- ============ SCHEDULER TAB ============ -->
<div class="tab-content" id="tab-scheduler">
  <div class="header"><h2>📡 Content Distribution</h2></div>
  <!-- SUB TABS -->
  <div style="display:flex;gap:0;margin-bottom:20px;border-bottom:1px solid rgba(255,255,255,.06)">
    <button class="btn btn-outline" onclick="distroSwitchTab('diagnose',this)" style="border-radius:8px 8px 0 0;border-bottom:none;margin-bottom:-1px" id="distro-tab-diagnose">🔄 Diagnostic</button>
    <button class="btn btn-outline" onclick="distroSwitchTab('parrilla',this)" style="border-radius:8px 8px 0 0;border-bottom:none;margin-bottom:-1px" id="distro-tab-parrilla">📅 Parrilla</button>
    <button class="btn btn-outline" onclick="distroSwitchTab('publish',this)" style="border-radius:8px 8px 0 0;border-bottom:none;margin-bottom:-1px" id="distro-tab-publish">📤 Publish Now</button>
  </div>

  <!-- DIAGNOSTIC PANEL -->
  <div class="distro-panel" id="distro-panel-diagnose" style="display:none">
    <div class="panel"><div id="distro-diag-result">Loading diagnostics...</div>
    <button class="btn btn-primary" onclick="distroRunDiagnose()" style="margin-top:12px">▶ Run Diagnostics</button></div>
  </div>

  <!-- PARRILLA PANEL -->
  <div class="distro-panel" id="distro-panel-parrilla" style="display:none">
    <div class="panel">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
        <h3>Programmed Publications</h3>
        <button class="btn btn-primary" onclick="schedulerOpen()">+ New Schedule</button>
      </div>
      <div id="sched-history-list"><div class="empty">Loading...</div></div>
    </div>
  </div>

  <!-- PUBLISH NOW PANEL -->
  <div class="distro-panel" id="distro-panel-publish" style="display:none">
    <div class="panel">
      <h3 style="margin-bottom:12px">Publish Article Now</h3>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
        <div>
          <label style="color:rgba(255,255,255,.4);font-size:.76rem">Article</label>
          <select id="distro-publish-article" style="width:100%;padding:8px 12px;background:#0F172A;border:1px solid rgba(255,255,255,.1);border-radius:8px;color:#E2E8F0;font-size:.82rem;margin:8px 0"><option value="">-- Select article --</option></select>
        </div>
        <div>
          <label style="color:rgba(255,255,255,.4);font-size:.76rem">Platforms</label>
          <div style="margin:8px 0">
            <label class="inline-check"><input type="checkbox" class="distro-publish-pf" value="intsolcom" checked> 🌐 Intsolcom.com</label>
            <label class="inline-check"><input type="checkbox" class="distro-publish-pf" value="linkedin_me"> 👤 LinkedIn Personal</label>
            <label class="inline-check"><input type="checkbox" class="distro-publish-pf" value="linkedin_co"> 🏢 LinkedIn Company</label>
          </div>
        </div>
      </div>
      <div id="distro-publish-result" style="margin-top:12px"></div>
      <button class="btn btn-accent" onclick="distroPublishNow()" style="margin-top:8px">📤 Publish Now</button>
    </div>
  </div>
</div>

</main>

<!-- MODAL OVERLAY -->
<div class="modal-overlay" id="modalOverlay" onclick="if(event.target===this)closeModal()">
  <div class="modal" id="modalContent"></div>
</div>

<div class="toast" id="toast"></div>

<script>
// ── Tab switching ──
function switchTab(name) {
    document.querySelectorAll('.sidebar-nav a[data-tab]').forEach(a => a.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('active'));
    var a = document.querySelector('.sidebar-nav a[data-tab="' + name + '"]');
    if (a) a.classList.add('active');
    var tab = document.getElementById('tab-' + name);
    if (tab) tab.classList.add('active');
    if (name === 'pages') loadSections();
}

document.querySelectorAll('.sidebar-nav a[data-tab]').forEach(a => {
    a.addEventListener('click', function(e) { e.preventDefault(); switchTab(this.dataset.tab); });
});

// ── Toast ──
var toastTimer;
function toast(msg) {
    var t = document.getElementById('toast');
    t.textContent = msg; t.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function() { t.classList.remove('show'); }, 2500);
}

// ── AJAX helper ──
function post(action, data, cb) {
    var fd = new FormData();
    for (var k in data) fd.append(k, data[k]);
    fetch('?action=' + action, { method: 'POST', body: fd })
        .then(r => r.json()).then(cb).catch(function(e) { toast('Error: ' + e.message); });
}

// ── Clear cache ──
function clearCache() {
    post('clear_cache', {}, function(r) { if (r.ok) toast('Cache cleared'); else toast(r.error); });
}

// ── Toggle status ──
function toggleStatus(table, id, btn) {
    post('toggle_status', { table: table, id: id }, function(r) {
        if (r.ok) { btn.textContent = r.status == 1 ? 'Active' : 'Inactive'; toast('Status updated'); }
        else toast(r.error);
    });
}

// ── MODAL ──
function openModal(html) { document.getElementById('modalContent').innerHTML = html; document.getElementById('modalOverlay').classList.add('show'); }
function closeModal() { document.getElementById('modalOverlay').classList.remove('show'); }

// ── PAGES / SECTIONS ──
function loadSections() {
    var pageId = document.getElementById('pageSelect').value;
    var container = document.getElementById('sectionsList');
    container.innerHTML = '<div class="empty">Loading...</div>';
    fetch('index.php?action=get_sections&page_id=' + pageId).then(r => r.json()).then(function(resp) {
        if (!resp.ok || !resp.sections || !resp.sections.length) { container.innerHTML = '<div class="empty">No sections found</div>'; return; }
        var html = '<table><thead><tr><th>Type</th><th>Sort</th><th>Status</th><th></th></tr></thead><tbody>';
        resp.sections.forEach(function(sec) {
            var typeLabel = sec.type.charAt(0).toUpperCase() + sec.type.slice(1).replace(/_/g,' ');
            html += '<tr><td><strong>' + typeLabel + '</strong></td><td>' + sec.sort_order + '</td>';
            html += '<td><button class="status-toggle" onclick="toggleStatus(\'sections\',' + sec.id + ',this)">' + (sec.status == 1 ? 'Active' : 'Inactive') + '</button></td>';
            html += '<td><button class="btn btn-outline btn-sm" onclick="openSectionModal(' + sec.id + ')">Edit</button> ';
            html += '<button class="btn btn-danger btn-sm" onclick="deleteSection(' + sec.id + ')">Del</button></td></tr>';
        });
        html += '</tbody></table>';
        container.innerHTML = html;
    }).catch(function() { container.innerHTML = '<div class="empty">Error loading sections</div>'; });
}

function addSection() {
    var pageId = document.getElementById('pageSelect').value;
    var type = document.getElementById('newSectionType').value;
    var sort = 99;
    post('add_section', { page_id: pageId, type: type, sort_order: sort }, function(r) {
        if (r.ok) { toast('Section added'); loadSections(); }
        else toast(r.error);
    });
}

function deleteSection(id) {
    if (!confirm('Delete this section and all its fields?')) return;
    post('delete_section', { id: id }, function(r) {
        if (r.ok) { toast('Section deleted'); loadSections(); }
        else toast(r.error);
    });
}

function openSectionModal(id) {
    fetch('index.php?action=get_table_data&table=sections&id=' + id).then(r => r.json()).then(function(resp) {
        if (!resp.ok) { toast(resp.error); return; }
        var sec = resp.data;
        var fields = sec.fields || {};
        var type = sec.type;
        var fieldDefs = ['hero','ecosystem','stats','products_grid','capabilities','industries_grid','comparison','cta','testimonials','text_image','faq'];
        var labelMap = {
            hero: ['eyebrow','h1_line1','h1_line2','h1_line3','description','btn1_text','btn1_url','btn2_text','btn2_url','trust_text','video_id','overlay'],
            ecosystem: ['label','title','description'],
            stats: ['label','title','items'],
            products_grid: ['label','title','subtitle','button_text','button_url'],
            capabilities: ['label','title','items'],
            industries_grid: ['label','title','items'],
            comparison: ['label','title','left_title','right_title','items'],
            cta: ['h1','h2','desc','btn1_text','btn1_url','btn2_text','btn2_url','note'],
            testimonials: ['title','subtitle'],
            text_image: ['label','title','description','btn_text','btn_url','image_url','bg_dark'],
            faq: ['title','items']
        };
        var fnames = labelMap[type] || [];
        var jsonFields = ['items','features','benefits','use_cases','process','technologies','industries','capabilities'];
        var html = '<h3>Edit Section: ' + type.replace(/_/g, ' ').replace(/\b\w/g, function(l){return l.toUpperCase()}) + '</h3>';
        html += '<form onsubmit="saveSection(event,' + id + ')">';
        for (var i = 0; i < fnames.length; i++) {
            var fn = fnames[i];
            var val = fields[fn] || '';
            html += '<div class="form-group"><label>' + fn.replace(/_/g, ' ') + '</label>';
            if (jsonFields.indexOf(fn) >= 0 || val.length > 300) {
                html += '<textarea name="' + fn + '" rows="5">' + escHtml(val) + '</textarea>';
                if (jsonFields.indexOf(fn) >= 0) html += '<div class="help">JSON array of objects, e.g. [{"title":"...","desc":"..."}]</div>';
            } else {
                html += '<input type="text" name="' + fn + '" value="' + escHtml(val) + '">';
            }
            html += '</div>';
        }
        html += '<div class="btn-row"><button type="submit" class="btn btn-primary">Save</button><button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button></div>';
        html += '</form>';
        openModal(html);
    }).catch(function(e) { toast('Error: ' + e.message); });
}

function escHtml(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

function saveSection(e, id) {
    e.preventDefault();
    var form = e.target;
    var data = { section_id: id };
    for (var i = 0; i < form.elements.length; i++) {
        var el = form.elements[i];
        if (el.name && el.name !== '') data[el.name] = el.value;
    }
    post('save_section', data, function(r) {
        if (r.ok) { toast('Section saved'); closeModal(); loadSections(); }
        else toast(r.error);
    });
}

// ── NAVIGATION ──
function openNavModal(id) {
    var item = { text: '', url: '', is_cta: 0, visible: 1, sort_order: 0 };
    if (id) {
        // Find nav item in PHP data passed as JSON
        var navData = <?=json_encode($navItems)?>;
        var found = navData.find(function(n) { return n.id == id; });
        if (found) item = found;
    }
    var html = '<h3>' + (id ? 'Edit' : 'Add') + ' Nav Item</h3>';
    html += '<form onsubmit="saveNavItem(event,' + (id||0) + ')">';
    html += '<div class="form-group"><label>Text</label><input type="text" name="text" value="' + escHtml(item.text) + '" required></div>';
    html += '<div class="form-group"><label>URL</label><input type="text" name="url" value="' + escHtml(item.url) + '" required></div>';
    html += '<div class="form-row">';
    html += '<div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" value="' + item.sort_order + '"></div>';
    html += '</div>';
    html += '<div style="display:flex;gap:16px;align-items:center;margin-bottom:14px">';
    html += '<label class="inline-check"><input type="checkbox" name="is_cta" value="1" ' + (item.is_cta == 1 ? 'checked' : '') + '> CTA Button</label>';
    html += '<label class="inline-check"><input type="checkbox" name="visible" value="1" ' + (item.visible == 1 ? 'checked' : '') + '> Visible</label>';
    html += '</div>';
    html += '<div class="btn-row"><button type="submit" class="btn btn-primary">Save</button><button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button></div>';
    html += '</form>';
    openModal(html);
}

function saveNavItem(e, id) {
    e.preventDefault();
    var form = e.target;
    var data = {
        text: form.text.value,
        url: form.url.value,
        sort_order: form.sort_order.value || 0,
        is_cta: form.is_cta.checked ? 1 : 0,
        visible: form.visible.checked ? 1 : 0
    };
    // Get current items, add/update, save all
    var navData = <?=json_encode($navItems)?>;
    if (id) {
        var idx = navData.findIndex(function(n) { return n.id == id; });
        if (idx >= 0) { data.id = id; navData[idx] = data; }
    } else {
        data.id = navData.length > 0 ? Math.max.apply(null, navData.map(function(n){return n.id})) + 1 : 1;
        data.sort_order = navData.length > 0 ? Math.max.apply(null, navData.map(function(n){return n.sort_order})) + 10 : 10;
        navData.push(data);
    }
    var items = navData.map(function(n) { return { text: n.text, url: n.url, is_cta: parseInt(n.is_cta), visible: parseInt(n.visible), sort_order: parseInt(n.sort_order) }; });
    post('save_nav', { items: JSON.stringify(items) }, function(r) {
        if (r.ok) { toast('Navigation saved'); closeModal(); location.reload(); }
        else toast(r.error);
    });
}

function moveNav(id, dir) {
    var navData = <?=json_encode($navItems)?>;
    var idx = navData.findIndex(function(n) { return n.id == id; });
    if (idx < 0) return;
    var swap = dir === 'up' ? idx - 1 : idx + 1;
    if (swap < 0 || swap >= navData.length) return;
    var tmp = navData[idx].sort_order;
    navData[idx].sort_order = navData[swap].sort_order;
    navData[swap].sort_order = tmp;
    var items = navData.map(function(n) { return { text: n.text, url: n.url, is_cta: parseInt(n.is_cta||0), visible: parseInt(n.visible||1), sort_order: parseInt(n.sort_order) }; });
    post('save_nav', { items: JSON.stringify(items) }, function(r) {
        if (r.ok) { toast('Reordered'); location.reload(); }
        else toast(r.error);
    });
}

// ── BUSINESS UNITS ──
function openUnitModal(id) {
    var html = '<h3>' + (id ? 'Edit' : 'Add') + ' Business Unit</h3><form onsubmit="saveUnit(event,' + (id||0) + ')">';
    if (id) {
        var unitsData = <?=json_encode($units)?>;
        var u = unitsData.find(function(x) { return x.id == id; });
        if (u) {
            var cols = ['name','slug','description','hero_title','hero_subtitle','hero_video_id','icon','order_num','status'];
            cols.forEach(function(c) {
                var val = u[c] !== null ? String(u[c]) : '';
                html += '<div class="form-group"><label>' + c + '</label>';
                if (c === 'status') html += '<select name="' + c + '"><option value="1"' + (val=='1'?' selected':'') + '>Active</option><option value="0"' + (val=='0'?' selected':'') + '>Inactive</option></select>';
                else if (val.length > 200) html += '<textarea name="' + c + '" rows="4">' + escHtml(val) + '</textarea>';
                else html += '<input type="text" name="' + c + '" value="' + escHtml(val) + '">';
                html += '</div>';
            });
            ['capabilities','benefits','process','technologies','industries'].forEach(function(c) {
                var val = u[c] !== null ? String(u[c]) : '';
                try { val = JSON.stringify(JSON.parse(val), null, 2); } catch(e) {}
                html += '<div class="form-group"><label>' + c + ' (JSON)</label><textarea name="' + c + '" rows="5">' + escHtml(val) + '</textarea></div>';
            });
        }
    } else {
        var cols = ['name','slug','description','hero_title','hero_subtitle','hero_video_id','icon','order_num','status'];
        cols.forEach(function(c) {
            html += '<div class="form-group"><label>' + c + '</label>';
            if (c === 'status') html += '<select name="' + c + '"><option value="1">Active</option><option value="0">Inactive</option></select>';
            else html += '<input type="text" name="' + c + '">';
            html += '</div>';
        });
        ['capabilities','benefits','process','technologies','industries'].forEach(function(c) {
            html += '<div class="form-group"><label>' + c + ' (JSON)</label><textarea name="' + c + '" rows="4"></textarea></div>';
        });
    }
    html += '<div class="btn-row"><button type="submit" class="btn btn-primary">Save</button><button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button></div></form>';
    openModal(html);
}

function saveUnit(e, id) {
    e.preventDefault();
    var form = e.target;
    var data = { id: id };
    for (var i = 0; i < form.elements.length; i++) {
        var el = form.elements[i];
        if (el.name && el.name !== '') data[el.name] = el.value;
    }
    post('save_unit', data, function(r) {
        if (r.ok) { toast('Unit saved'); closeModal(); location.reload(); }
        else toast(r.error);
    });
}

function deleteUnit(id) {
    if (!confirm('Delete this business unit?')) return;
    post('delete_unit', { id: id }, function(r) {
        if (r.ok) { toast('Unit deleted'); location.reload(); }
        else toast(r.error);
    });
}

// ── PRODUCTS ──
function openProductModal(id) {
    var html = '<h3>' + (id ? 'Edit' : 'Add') + ' Product</h3><form onsubmit="saveProduct(event,' + (id||0) + ')">';
    var cols = ['name','slug','description','short_desc','hero_title','hero_subtitle','icon','category','order_num','status',
        'overview','problem','solution','features','screenshots','benefits','use_cases','architecture','roadmap','demo_cta_url','demo_cta_text'];
    var jsonCols = ['features','screenshots','benefits','use_cases'];
    var longCols = ['overview','problem','solution','architecture','roadmap','description'];
    if (id) {
        var prodData = <?=json_encode($products)?>;
        var p = prodData.find(function(x) { return x.id == id; });
        if (p) {
            cols.forEach(function(c) {
                var val = p[c] !== null ? String(p[c]) : '';
                html += '<div class="form-group"><label>' + c + '</label>';
                if (c === 'status') html += '<select name="' + c + '"><option value="1"' + (val=='1'?' selected':'') + '>Active</option><option value="0"' + (val=='0'?' selected':'') + '>Inactive</option></select>';
                else if (jsonCols.indexOf(c) >= 0) {
                    try { val = JSON.stringify(JSON.parse(val), null, 2); } catch(e) {}
                    html += '<textarea name="' + c + '" rows="5">' + escHtml(val) + '</textarea>';
                } else if (longCols.indexOf(c) >= 0 || val.length > 200) html += '<textarea name="' + c + '" rows="4">' + escHtml(val) + '</textarea>';
                else html += '<input type="text" name="' + c + '" value="' + escHtml(val) + '">';
                html += '</div>';
            });
        }
    } else {
        cols.forEach(function(c) {
            html += '<div class="form-group"><label>' + c + '</label>';
            if (c === 'status') html += '<select name="' + c + '"><option value="1">Active</option><option value="0">Inactive</option></select>';
            else if (jsonCols.indexOf(c) >= 0 || longCols.indexOf(c) >= 0) html += '<textarea name="' + c + '" rows="4"></textarea>';
            else html += '<input type="text" name="' + c + '">';
            html += '</div>';
        });
    }
    html += '<div class="btn-row"><button type="submit" class="btn btn-primary">Save</button><button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button></div></form>';
    openModal(html);
}

function saveProduct(e, id) {
    e.preventDefault();
    var form = e.target;
    var data = { id: id };
    for (var i = 0; i < form.elements.length; i++) {
        var el = form.elements[i];
        if (el.name && el.name !== '') data[el.name] = el.value;
    }
    post('save_product', data, function(r) {
        if (r.ok) { toast('Product saved'); closeModal(); location.reload(); }
        else toast(r.error);
    });
}

function deleteProduct(id) {
    if (!confirm('Delete this product?')) return;
    post('delete_product', { id: id }, function(r) {
        if (r.ok) { toast('Product deleted'); location.reload(); }
        else toast(r.error);
    });
}

// ── INDUSTRIES ──
function openIndustryModal(id) {
    var html = '<h3>' + (id ? 'Edit' : 'Add') + ' Industry</h3><form onsubmit="saveIndustry(event,' + (id||0) + ')">';
    var cols = ['name','slug','description','body','icon','hero_title','hero_subtitle','short_desc','benefits','use_cases','order_num','status'];
    var jsonCols = ['benefits','use_cases'];
    var longCols = ['description','body'];
    if (id) {
        var indData = <?=json_encode($industries)?>;
        var p = indData.find(function(x) { return x.id == id; });
        if (p) {
            cols.forEach(function(c) {
                var val = p[c] !== null ? String(p[c]) : '';
                html += '<div class="form-group"><label>' + c + '</label>';
                if (c === 'status') html += '<select name="' + c + '"><option value="1"' + (val=='1'?' selected':'') + '>Active</option><option value="0"' + (val=='0'?' selected':'') + '>Inactive</option></select>';
                else if (jsonCols.indexOf(c) >= 0) {
                    try { val = JSON.stringify(JSON.parse(val), null, 2); } catch(e) {}
                    html += '<textarea name="' + c + '" rows="5">' + escHtml(val) + '</textarea>';
                } else if (longCols.indexOf(c) >= 0 || val.length > 200) html += '<textarea name="' + c + '" rows="4">' + escHtml(val) + '</textarea>';
                else html += '<input type="text" name="' + c + '" value="' + escHtml(val) + '">';
                html += '</div>';
            });
        }
    } else {
        cols.forEach(function(c) {
            html += '<div class="form-group"><label>' + c + '</label>';
            if (c === 'status') html += '<select name="' + c + '"><option value="1">Active</option><option value="0">Inactive</option></select>';
            else if (jsonCols.indexOf(c) >= 0 || longCols.indexOf(c) >= 0) html += '<textarea name="' + c + '" rows="4"></textarea>';
            else html += '<input type="text" name="' + c + '">';
            html += '</div>';
        });
    }
    html += '<div class="btn-row"><button type="submit" class="btn btn-primary">Save</button><button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button></div></form>';
    openModal(html);
}

function saveIndustry(e, id) {
    e.preventDefault();
    var form = e.target;
    var data = { id: id };
    for (var i = 0; i < form.elements.length; i++) {
        var el = form.elements[i];
        if (el.name && el.name !== '') data[el.name] = el.value;
    }
    post('save_industry', data, function(r) {
        if (r.ok) { toast('Industry saved'); closeModal(); location.reload(); }
        else toast(r.error);
    });
}

function deleteIndustry(id) {
    if (!confirm('Delete this industry?')) return;
    post('delete_industry', { id: id }, function(r) {
        if (r.ok) { toast('Industry deleted'); location.reload(); }
        else toast(r.error);
    });
}

// ── RESOURCES ──
function openResourceModal(id) {
    var html = '<h3>' + (id ? 'Edit' : 'Add') + ' Resource</h3><form onsubmit="saveResource(event,' + (id||0) + ')">';
    var cols = ['title','slug','excerpt','content','cover_image','type','author','read_time','featured','meta_title','meta_desc','status','published_at'];
    if (id) {
        var resData = <?=json_encode($resources)?>;
        var p = resData.find(function(x) { return x.id == id; });
        if (p) {
            cols.forEach(function(c) {
                var val = p[c] !== null ? String(p[c]) : '';
                html += '<div class="form-group"><label>' + c + '</label>';
                if (c === 'status') html += '<select name="' + c + '"><option value="1"' + (val=='1'?' selected':'') + '>Active</option><option value="0"' + (val=='0'?' selected':'') + '>Draft</option></select>';
                else if (c === 'featured') html += '<select name="' + c + '"><option value="1"' + (val=='1'?' selected':'') + '>Yes</option><option value="0"' + (val!='1'?' selected':'') + '>No</option></select>';
                else if (c === 'content' || c === 'excerpt' || val.length > 300) html += '<textarea name="' + c + '" rows="6">' + escHtml(val) + '</textarea>';
                else html += '<input type="text" name="' + c + '" value="' + escHtml(val) + '">';
                html += '</div>';
            });
        }
    } else {
        cols.forEach(function(c) {
            html += '<div class="form-group"><label>' + c + '</label>';
            if (c === 'status') html += '<select name="' + c + '"><option value="1">Active</option><option value="0">Draft</option></select>';
            else if (c === 'featured') html += '<select name="' + c + '"><option value="0">No</option><option value="1">Yes</option></select>';
            else if (c === 'content' || c === 'excerpt') html += '<textarea name="' + c + '" rows="6"></textarea>';
            else html += '<input type="text" name="' + c + '">';
            html += '</div>';
        });
    }
    html += '<div class="btn-row"><button type="submit" class="btn btn-primary">Save</button><button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button></div></form>';
    openModal(html);
}

function saveResource(e, id) {
    e.preventDefault();
    var form = e.target;
    var data = { id: id };
    for (var i = 0; i < form.elements.length; i++) {
        var el = form.elements[i];
        if (el.name && el.name !== '') data[el.name] = el.value;
    }
    post('save_resource', data, function(r) {
        if (r.ok) { toast('Resource saved'); closeModal(); location.reload(); }
        else toast(r.error);
    });
}

function deleteResource(id) {
    if (!confirm('Delete this resource?')) return;
    post('delete_resource', { id: id }, function(r) {
        if (r.ok) { toast('Resource deleted'); location.reload(); }
        else toast(r.error);
    });
}

// ── TESTIMONIALS ──
function openTestimonialModal(id) {
    var html = '<h3>' + (id ? 'Edit' : 'Add') + ' Testimonial</h3><form onsubmit="saveTestimonial(event,' + (id||0) + ')">';
    var defaults = { name: '', role: '', company: '', content: '', rating: 5, visible: 1, sort_order: 0 };
    if (id) {
        var tData = <?=json_encode($testimonials)?>;
        var t = tData.find(function(x) { return x.id == id; });
        if (t) defaults = t;
    }
    html += '<div class="form-row"><div class="form-group"><label>Name</label><input type="text" name="name" value="' + escHtml(defaults.name) + '" required></div>';
    html += '<div class="form-group"><label>Role</label><input type="text" name="role" value="' + escHtml(defaults.role||'') + '"></div></div>';
    html += '<div class="form-row"><div class="form-group"><label>Company</label><input type="text" name="company" value="' + escHtml(defaults.company||'') + '"></div>';
    html += '<div class="form-group"><label>Rating (1-5)</label><select name="rating">';
    for (var r = 1; r <= 5; r++) html += '<option value="' + r + '"' + (defaults.rating == r ? ' selected' : '') + '>' + r + '</option>';
    html += '</select></div></div>';
    html += '<div class="form-group"><label>Content</label><textarea name="content" rows="4">' + escHtml(defaults.content||'') + '</textarea></div>';
    html += '<div class="form-row"><div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" value="' + (defaults.sort_order||0) + '"></div>';
    html += '<div class="form-group"><label>Status</label><select name="visible"><option value="1"' + (defaults.visible == 1 ? ' selected' : '') + '>Active</option><option value="0"' + (defaults.visible == 0 ? ' selected' : '') + '>Inactive</option></select></div></div>';
    html += '<div class="btn-row"><button type="submit" class="btn btn-primary">Save</button><button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button></div></form>';
    openModal(html);
}

function saveTestimonial(e, id) {
    e.preventDefault();
    var form = e.target;
    var data = { id: id, name: form.name.value, role: form.role.value, company: form.company.value, content: form.content.value, rating: form.rating.value, visible: form.visible.value, sort_order: form.sort_order.value };
    post('save_testimonial', data, function(r) {
        if (r.ok) { toast('Testimonial saved'); closeModal(); location.reload(); }
        else toast(r.error);
    });
}

function deleteTestimonial(id) {
    if (!confirm('Delete this testimonial?')) return;
    post('delete_testimonial', { id: id }, function(r) {
        if (r.ok) { toast('Testimonial deleted'); location.reload(); }
        else toast(r.error);
    });
}

// ── CLIENTS ──
function addClientRow() {
    var cont = document.getElementById('clientsList');
    var empty = document.getElementById('clientsEmpty');
    if (empty) empty.remove();
    var idx = Date.now();
    var div = document.createElement('div');
    div.className = 'nav-item-row';
    div.innerHTML = '<div class="info">' +
        '<input type="text" placeholder="Client name" onchange="updateClientField(this,\'name\')" style="background:#0F172A;border:1px solid rgba(255,255,255,.1);border-radius:6px;padding:4px 8px;color:#E2E8F0;width:200px;font-size:.82rem"> ' +
        '<input type="text" placeholder="Logo URL" onchange="updateClientField(this,\'logo_url\')" style="background:#0F172A;border:1px solid rgba(255,255,255,.1);border-radius:6px;padding:4px 8px;color:#E2E8F0;width:260px;font-size:.82rem;margin-left:8px">' +
        '</div>' +
        '<label class="inline-check" style="margin-left:8px"><input type="checkbox" checked onchange="updateClientField(this,\'visible\',this.checked?1:0)">Visible</label>' +
        '<button class="btn btn-outline btn-sm" onclick="removeClientRow(this)">X</button>';
    cont.appendChild(div);
}

function removeClientRow(btn) {
    var row = btn.closest('.nav-item-row');
    if (row) row.remove();
}

function updateClientField(el, field, value) {
    // Handled at save time by reading all inputs
}

function saveClients() {
    var container = document.getElementById('clientsList');
    var rows = container.querySelectorAll('.nav-item-row');
    var items = [];
    rows.forEach(function(row, i) {
        var inputs = row.querySelectorAll('input[type=text]');
        var cb = row.querySelector('input[type=checkbox]');
        items.push({
            name: inputs[0] ? inputs[0].value : '',
            logo_url: inputs[1] ? inputs[1].value : '',
            visible: cb ? (cb.checked ? 1 : 0) : 1,
            sort_order: (i + 1) * 10
        });
    });
    post('save_clients', { items: JSON.stringify(items) }, function(r) {
        if (r.ok) { toast('Clients saved'); location.reload(); }
        else toast(r.error);
    });
}

// ── MEDIA ──
function uploadFile() {
    var fileInput = document.getElementById('uploadFile');
    if (!fileInput.files[0]) { toast('Select a file'); return; }
    var fd = new FormData();
    fd.append('file', fileInput.files[0]);
    fetch('index.php?action=upload', { method: 'POST', body: fd }).then(r => r.json()).then(function(resp) {
        if (resp.ok) { toast('Uploaded!'); location.reload(); }
        else toast(resp.error);
    });
}

function copyUrl(url) {
    navigator.clipboard.writeText(url).then(function() { toast('URL copied'); });
}

function deleteMedia(id, btn) {
    if (!confirm('Delete this file?')) return;
    post('delete_media', { id: id }, function(r) {
        if (r.ok) {
            var card = document.getElementById('media-' + id);
            if (card) card.remove();
            toast('Deleted');
        } else toast(r.error);
    });
}

// ── SETTINGS ──
function saveSettingsGroup(btn) {
    var body = btn.parentElement;
    var inputs = body.querySelectorAll('input[data-key], textarea[data-key]');
    var promises = [];
    inputs.forEach(function(el) {
        promises.push(new Promise(function(resolve) {
            var key = el.dataset.key;
            var val = el.value;
            post('save_setting', { key: key, value: val }, function(r) { resolve(r); });
        }));
    });
    Promise.all(promises).then(function() { toast('Settings saved'); clearCache(); });
}

// ── TRANSLATIONS ──
function pretranslate() {
    var btn = document.getElementById('pretranslateBtn');
    var res = document.getElementById('pretranslateResult');
    btn.disabled = true;
    btn.textContent = 'Translating... This may take a few minutes';
    res.textContent = '';
    post('pretranslate', {}, function(r) {
        btn.disabled = false;
        btn.textContent = 'Pre-translate All Content';
        if (r.ok) { res.textContent = 'Done! ' + r.count + ' new translations created.'; res.style.color = '#00C896'; }
        else { res.textContent = 'Error: ' + r.error; res.style.color = '#ef4444'; }
    });
}
// ── SCHEDULER API ──
function apiFetch(action, opts = {}) {
    if (!opts.headers) opts.headers = {};
    return fetch('?action=' + action, opts);
}
function h_js(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

let schedAllPosts = [];
let schedTemplates = JSON.parse(localStorage.getItem('sched_templates') || '[]');

function schedulerOpen() { document.getElementById('sched-modal').style.display='flex'; schedulerLoadArticles(); schedulerLoadHistory(); const t=new Date();t.setDate(t.getDate()+1);t.setHours(9,0,0,0);document.getElementById('sched-datetime').value=t.toISOString().slice(0,16); document.getElementById('sched-grid-type').value=''; schedulerUpdatePreview(); }
function schedulerClose() { document.getElementById('sched-modal').style.display='none'; document.querySelectorAll('.sched-article-cb').forEach(c=>c.checked=false); }
async function schedulerLoadArticles() { const r=await apiFetch('blog_get_posts'); const j=await r.json(); if(j.ok){schedAllPosts=j.posts||[]; schedulerRenderArticles('');} }
function schedulerRenderArticles(filter='') { const list=document.getElementById('sched-article-list'); if(!list)return; const f=filter.toLowerCase(); const filtered=schedAllPosts.filter(p=>!f||(p.title||'').toLowerCase().includes(f)||(p.slug||'').toLowerCase().includes(f)); if(!filtered.length){list.innerHTML='<div class=\"empty\">No articles found</div>';return;} list.innerHTML=filtered.map(p=>'<label class=\"nav-item-row\" style=\"cursor:pointer\"><input type=\"checkbox\" class=\"sched-article-cb\" value=\"'+p.id+'\" onchange=\"schedulerUpdatePreview()\"><span class=\"info\">'+h_js(p.title)+'<br><span class=\"meta\">'+(p.status||'draft')+'</span></span></label>').join(''); schedulerUpdatePreview(); }
function schedulerFilter(){schedulerRenderArticles(document.getElementById('sched-search')?.value||'');}
function schedulerUpdatePreview() { const checked=document.querySelectorAll('.sched-article-cb:checked'); const el=document.getElementById('sched-count'); if(el)el.textContent=checked.length+' selected'; const platforms=Array.from(document.querySelectorAll('.sched-platform-cb:checked')).map(c=>c.value); const dt=document.getElementById('sched-datetime')?.value; const grid=document.getElementById('sched-grid-type')?.value; const dates=grid?schedulerGenGrid(dt,grid):(dt?[dt]:[]); const pl=document.getElementById('sched-preview-list'); const pc=document.getElementById('sched-preview-count'); if(!checked.length||!dates.length||!platforms.length){if(pl)pl.innerHTML='<div class=\"empty\">Select articles, date and platforms.</div>';if(pc)pc.textContent='0 posts';return;} let slots=[],di=0; const arts=Array.from(checked).map(c=>schedAllPosts.find(p=>p.id==c.value)).filter(Boolean); for(const d of dates){const a=arts[di%arts.length];for(const pf of platforms){slots.push({date:d,article:a,platform:pf});}di++;} if(pc)pc.textContent=slots.length+' posts'; const icons={intsolcom:'\u{1F310}',linkedin_me:'\u{1F464}',linkedin_co:'\u{1F3E2}'}; if(pl)pl.innerHTML=slots.map(s=>'<div style=\"display:flex;align-items:center;gap:.5rem;padding:.3rem 0;border-bottom:1px solid rgba(255,255,255,.05);font-size:.78rem\"><span>'+ (icons[s.platform]||'📡') +'</span><span style=\"flex:1\">'+h_js(s.article?.title||'?')+'</span><span style=\"color:rgba(255,255,255,.3);font-size:.68rem\">'+ (s.date||'').replace('T',' ') +'</span></div>').join(''); }
function schedulerGenGrid(dt,type){if(!dt)return[];const d=new Date(dt);const res=[dt];if(type==='daily'){for(let i=1;i<7;i++){const nd=new Date(d);nd.setDate(d.getDate()+i);res.push(nd.toISOString().slice(0,16));}}else if(type==='weekly'){for(let i=1;i<4;i++){const nd=new Date(d);nd.setDate(d.getDate()+i*7);res.push(nd.toISOString().slice(0,16));}}else if(type==='biweekly'){const nd=new Date(d);nd.setDate(d.getDate()+14);res.push(nd.toISOString().slice(0,16));}else if(type==='monthly'){for(let i=1;i<3;i++){const nd=new Date(d);nd.setMonth(d.getMonth()+i);res.push(nd.toISOString().slice(0,16));}} return res; }
async function schedulerConfirm(){const checked=document.querySelectorAll('.sched-article-cb:checked');if(!checked.length){toast('Select at least one article');return;}const platforms=Array.from(document.querySelectorAll('.sched-platform-cb:checked')).map(c=>c.value);if(!platforms.length){toast('Select at least one platform');return;}const dt=document.getElementById('sched-datetime')?.value;if(!dt){toast('Select date and time');return;}const grid=document.getElementById('sched-grid-type')?.value;const dates=schedulerGenGrid(dt,grid);const finalDates=dates.length?dates:[dt];const btn=document.getElementById('sched-confirm-btn');btn.disabled=true;btn.textContent='Scheduling...';let ok=0,total=0;const ids=Array.from(checked).map(c=>parseInt(c.value));let di=0; for(const d of finalDates){const pid=ids[di%ids.length];const r=await apiFetch('distro_schedule',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({post_id:pid,platforms,scheduled_at:d})});const j=await r.json();if(j.ok)ok++;total++;di++;} btn.disabled=false;btn.textContent='Confirm Schedule';toast(ok+'/'+total+' posts scheduled');schedulerClose();}
function schedulerSaveTemplate(){const name=document.getElementById('sched-template-name')?.value.trim();if(!name){toast('Name the template');return;}const grid=document.getElementById('sched-grid-type')?.value;const platforms=Array.from(document.querySelectorAll('.sched-platform-cb:checked')).map(c=>c.value);schedTemplates.push({name,gridType:grid,platforms,created:new Date().toISOString()});localStorage.setItem('sched_templates',JSON.stringify(schedTemplates));toast('Template saved');}
function schedulerLoadTemplates(){const sel=document.getElementById('sched-template-load');if(!sel)return;sel.innerHTML='<option value=\"\">Load template...</option>'+schedTemplates.map((t,i)=>'<option value=\"'+i+'\">'+h_js(t.name)+'</option>').join('');}
function schedulerLoadTemplate(idx){if(!idx&&idx!==0)return;const t=schedTemplates[parseInt(idx)];if(!t)return;document.getElementById('sched-grid-type').value=t.gridType||'';document.querySelectorAll('.sched-platform-cb').forEach(c=>{c.checked=(t.platforms||[]).includes(c.value);});schedulerUpdatePreview();toast('Template loaded: '+t.name);}
function schedulerExportCSV(){const checked=document.querySelectorAll('.sched-article-cb:checked');if(!checked.length){toast('Select articles first');return;}const platforms=Array.from(document.querySelectorAll('.sched-platform-cb:checked')).map(c=>c.value);const dt=document.getElementById('sched-datetime')?.value||'';const grid=document.getElementById('sched-grid-type')?.value||'';const dates=schedulerGenGrid(dt,grid);const finalDates=dates.length?dates:[dt];let csv='Date,Article,Platform\n';const arts=Array.from(checked).map(c=>schedAllPosts.find(p=>p.id==c.value)).filter(Boolean);let di=0;for(const d of finalDates){const a=arts[di%arts.length];for(const pf of platforms){csv+=d+','+(a?.title||'?')+','+pf+'\n';}di++;}const blob=new Blob([csv],{type:'text/csv'});const url=URL.createObjectURL(blob);const a=document.createElement('a');a.href=url;a.download='schedule-'+new Date().toISOString().slice(0,10)+'.csv';a.click();URL.revokeObjectURL(url);toast('CSV downloaded');}
async function schedulerLoadHistory(){const el=document.getElementById('sched-history-list');if(!el)return;const r=await apiFetch('distro_get_schedule');const j=await r.json();if(!j.ok||!j.schedule.length){el.innerHTML='<div class=\"empty\">No scheduled publications.</div>';return;}const icons={completed:'✅',pending:'⏳',failed:'❌',partial:'⚠️',optimizing:'🤖',posting:'📤'};el.innerHTML=j.schedule.map(s=>'<div class=\"nav-item-row\"><span>'+ (icons[s.status]||'⏳') +'</span><span class=\"info\">'+h_js(s.title||'?')+'<br><span class=\"meta\">'+(s.status||'').toUpperCase()+' · '+(s.scheduled_at||'').replace('T',' ').substring(0,16)+'</span></span></div>').join('');}
if(document.getElementById('tab-scheduler')){setTimeout(()=>{schedulerLoadHistory();},2000);}
function distroSwitchTab(name, btn) {
    document.querySelectorAll('.distro-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('#tab-scheduler .btn-outline').forEach(b => {b.style.background='transparent';b.style.color='rgba(255,255,255,.6)';b.style.borderColor='rgba(255,255,255,.15)';});
    var panel = document.getElementById('distro-panel-' + name);
    if (panel) panel.style.display = 'block';
    if (btn) {btn.style.background='rgba(0,200,150,.1)';btn.style.color='#00C896';btn.style.borderColor='rgba(0,200,150,.3)';}
    if (name==='diagnose') distroRunDiagnose();
    if (name==='publish') distroLoadPublishArticles();
}
async function distroRunDiagnose() {
    var el = document.getElementById('distro-diag-result');
    el.innerHTML = '<div style=\"color:rgba(255,255,255,.4);font-size:.82rem\">Running diagnostics...</div>';
    var r = await apiFetch('distro_diagnose'); var j = await r.json();
    if (!j.ok) { el.innerHTML = '<div style=\"color:#e53935\">Diagnostic failed</div>'; return; }
    var icons = {intsolcom:'🌐',linkedin_me:'👤',linkedin_co:'🏢',deepseek:'🤖'};
    var html = j.all_ok ? '<div style=\"color:#00C896;font-weight:700;font-size:.9rem;margin-bottom:12px\">✅ All systems operational</div>' : '';
    for (var k in j.results) {
        var d = j.results[k];
        var icon = d.ok ? '✅' : '❌';
        html += '<div style=\"display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.05);font-size:.82rem\">';
        html += '<span style=\"font-size:1rem\">' + (icons[k]||'📡') + '</span>';
        html += '<span style=\"flex:1;font-weight:600;color:#F8FAFC\">' + k.replace(/_/g,' ') + '</span>';
        html += '<span style=\"color:rgba(255,255,255,.4);font-size:.72rem\">' + (d.endpoint||'') + ' (' + d.code + ', ' + d.ms + 'ms)</span>';
        html += '<span>' + icon + '</span></div>';
    }
    el.innerHTML = html;
}
async function distroLoadPublishArticles() {
    var sel = document.getElementById('distro-publish-article');
    if (!sel || sel.options.length > 1) return;
    var r = await apiFetch('blog_get_posts'); var j = await r.json();
    if (j.ok) { (j.posts||[]).forEach(function(p){ sel.innerHTML += '<option value=\"' + p.id + '\">' + (p.title||'') + '</option>'; }); }
}
async function distroPublishNow() {
    var articleId = document.getElementById('distro-publish-article')?.value;
    if (!articleId) { toast('Select an article'); return; }
    var platforms = Array.from(document.querySelectorAll('.distro-publish-pf:checked')).map(function(c){return c.value;});
    if (!platforms.length) { toast('Select at least one platform'); return; }
    var btn = event.target; btn.disabled = true; btn.textContent = 'Publishing...';
    var r = await apiFetch('distro_publish', {method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({post_id:parseInt(articleId),platforms:platforms})});
    var j = await r.json();
    btn.disabled = false; btn.textContent = '📤 Publish Now';
    if (j.ok) {
        var el = document.getElementById('distro-publish-result');
        el.innerHTML = '<div style=\"color:#00C896;font-weight:600\">✅ Published!</div>' + (j.results||[]).map(function(r){return '<div style=\"font-size:.78rem;color:rgba(255,255,255,.5);margin-top:4px\">' + r.platform + ': ' + r.status + '</div>';}).join('');
        toast('Published successfully');
    } else {
        toast('Error: ' + (j.error||'Unknown'));
    }
}
// Auto-load diagnostic on first tab switch
(function(){
    var obs = new MutationObserver(function(mutations){
        mutations.forEach(function(m){
            if (m.target.id==='tab-scheduler' && m.target.classList.contains('active')) {
                distroSwitchTab('diagnose', document.getElementById('distro-tab-diagnose'));
                obs.disconnect();
            }
        });
    });
    var tab = document.getElementById('tab-scheduler');
    if (tab) obs.observe(tab, {attributes:true, attributeFilter:['class']});
})();
</script>

<!-- SCHEDULER MODAL -->
<div class="modal-overlay" id="sched-modal" style="display:none" onclick="if(event.target===this)schedulerClose()">
  <div class="modal" style="max-width:800px;max-height:90vh;overflow-y:auto">
    <span class="modal-close" onclick="schedulerClose()">✕</span>
    <h3>📅 New Content Schedule</h3>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:16px">
      <!-- LEFT: Select articles -->
      <div>
        <label style="color:rgba(255,255,255,.4);font-size:.76rem">Select Articles</label>
        <input type="text" id="sched-search" placeholder="Search articles..." oninput="schedulerFilter()" style="width:100%;padding:8px 12px;background:#0F172A;border:1px solid rgba(255,255,255,.1);border-radius:8px;color:#E2E8F0;font-size:.82rem;margin:8px 0;outline:none">
        <label class="inline-check" style="margin-bottom:8px"><input type="checkbox" id="sched-select-all" onchange="schedulerToggleAll(this.checked)"> Select all</label>
        <div id="sched-article-list" style="max-height:300px;overflow-y:auto">Loading...</div>
      </div>
      <!-- RIGHT: Configure -->
      <div>
        <div class="form-group"><label>Date & Time</label><input type="datetime-local" id="sched-datetime"></div>
        <div class="form-group"><label>Grid (optional)</label><select id="sched-grid-type" onchange="schedulerUpdatePreview()" style="width:100%;padding:8px 12px;background:#0F172A;border:1px solid rgba(255,255,255,.1);border-radius:8px;color:#E2E8F0;font-size:.82rem"><option value="">Single post</option><option value="daily">Daily (7 days)</option><option value="weekly">Weekly (4 weeks)</option><option value="biweekly">Bi-weekly</option><option value="monthly">Monthly (3 months)</option></select></div>
        <div class="form-group"><label>Platforms</label>
          <label class="inline-check"><input type="checkbox" class="sched-platform-cb" value="intsolcom" checked onchange="schedulerUpdatePreview()"> 🌐 intsolcom.com</label>
          <label class="inline-check"><input type="checkbox" class="sched-platform-cb" value="linkedin_me" onchange="schedulerUpdatePreview()"> 👤 LinkedIn (personal)</label>
          <label class="inline-check"><input type="checkbox" class="sched-platform-cb" value="linkedin_co" onchange="schedulerUpdatePreview()"> 🏢 LinkedIn (company)</label>
        </div>
        <div class="form-group"><label>Template</label><div class="form-row"><input type="text" id="sched-template-name" placeholder="Template name" style="padding:8px;background:#0F172A;border:1px solid rgba(255,255,255,.1);border-radius:8px;color:#E2E8F0;font-size:.8rem"><button class="btn btn-sm btn-outline" onclick="schedulerSaveTemplate()">Save</button></div></div>
        <div class="form-group"><select id="sched-template-load" onchange="schedulerLoadTemplate(this.value)" style="width:100%;padding:8px 12px;background:#0F172A;border:1px solid rgba(255,255,255,.1);border-radius:8px;color:#E2E8F0;font-size:.82rem"><option value="">Load template...</option></select></div>
        <div style="background:rgba(0,200,150,.04);border:1px solid rgba(0,200,150,.15);border-radius:12px;padding:14px;margin-top:12px">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px"><strong style="font-size:.82rem;color:#F8FAFC">Preview</strong><span id="sched-count" style="font-size:.72rem;color:rgba(255,255,255,.4)">0 selected</span></div>
          <div id="sched-preview-list" style="font-size:.8rem"></div>
          <div style="display:flex;justify-content:space-between;align-items:center;margin-top:10px;padding-top:10px;border-top:1px solid rgba(255,255,255,.08)">
            <span id="sched-preview-count" style="font-size:.72rem;color:rgba(255,255,255,.4)">0 posts</span>
            <div class="btn-row">
              <button class="btn btn-sm btn-outline" onclick="schedulerExportCSV()">📥 CSV</button>
              <button class="btn btn-sm btn-primary" id="sched-confirm-btn" onclick="schedulerConfirm()">✅ Confirm Schedule</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</body>
</html>

