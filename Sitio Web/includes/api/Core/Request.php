<?php
namespace App\Core;

class Request {
    public static function method(): string {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }
    
    public static function get(string $key, mixed $default = null): mixed {
        return $_GET[$key] ?? $default;
    }
    
    public static function post(string $key, mixed $default = null): mixed {
        return $_POST[$key] ?? $default;
    }
    
    public static function json(): array {
        $body = json_decode(file_get_contents('php://input'), true);
        return is_array($body) ? $body : [];
    }
    
    public static function header(string $key, string $default = ''): string {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $_SERVER[$key] ?? $default;
    }
    
    public static function bearerToken(): ?string {
        $header = self::header('Authorization');
        if (preg_match('/Bearer\s+(.+)/i', $header, $m)) return $m[1];
        return null;
    }
    
    public static function apiKey(): ?string {
        return self::header('X-API-Key') ?: self::get('api_key');
    }
    
    public static function queryParam(string $key, mixed $default = null): mixed {
        return $_GET[$key] ?? $default;
    }
}
