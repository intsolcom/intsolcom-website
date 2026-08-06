<?php
namespace App\Core;

class Response {
    public static function json(array $data, int $code = 200): never {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (function_exists('fastcgi_finish_request')) fastcgi_finish_request();
        exit;
    }
    
    public static function ok(mixed $data, array $meta = []): never {
        self::json(['ok' => true, 'data' => $data, 'meta' => $meta]);
    }
    
    public static function error(string $msg, int $code = 400): never {
        self::json(['ok' => false, 'error' => $msg], $code);
    }
    
    public static function paginated(array $items, int $total, int $page, int $perPage): never {
        self::ok([
            'items' => $items,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => max(1, (int)ceil($total / $perPage)),
                'has_next' => ($page * $perPage) < $total,
                'has_prev' => $page > 1,
            ]
        ]);
    }
}
