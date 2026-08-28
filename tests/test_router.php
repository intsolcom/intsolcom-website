<?php
require '/var/www/intsolcom/includes/config.php';
require '/var/www/intsolcom/includes/api/Core/Router.php';
require '/var/www/intsolcom/includes/api/Core/Response.php';

use App\Core\{Router, Response};

$router = new Router();
$router->get('/api/v2/health', function() { Response::ok(['status'=>'ok']); });

$_SERVER['REQUEST_URI'] = '/api/v2/health';
$_SERVER['REQUEST_METHOD'] = 'GET';

$router->dispatch();
