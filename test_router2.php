<?php
require '/var/www/intsolcom/includes/config.php';
require '/var/www/intsolcom/includes/api/Core/Router.php';
require '/var/www/intsolcom/includes/api/Core/Response.php';

use App\Core\{Router, Response};

$db = db();

$router = new Router();
$router->get('/api/v2/health', function() use ($db) {
    $c = $db->query("SELECT COUNT(*) FROM blog_categories")->fetchColumn();
    Response::ok(['status'=>'ok', 'categories'=>(int)$c]);
});

$_SERVER['REQUEST_URI'] = '/api/v2/health';
$_SERVER['REQUEST_METHOD'] = 'GET';
$router->dispatch();
