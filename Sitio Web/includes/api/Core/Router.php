<?php
namespace App\Core;

class Router {
    private array $routes = [];
    
    public function get(string $path, callable $handler): self {
        $this->routes[] = ['GET', $path, $handler];
        return $this;
    }
    
    public function post(string $path, callable $handler): self {
        $this->routes[] = ['POST', $path, $handler];
        return $this;
    }
    
    public function put(string $path, callable $handler): self {
        $this->routes[] = ['PUT', $path, $handler];
        return $this;
    }
    
    public function patch(string $path, callable $handler): self {
        $this->routes[] = ['PATCH', $path, $handler];
        return $this;
    }
    
    public function delete(string $path, callable $handler): self {
        $this->routes[] = ['DELETE', $path, $handler];
        return $this;
    }
    
    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';
        
        // Handle CORS preflight
        if ($method === 'OPTIONS') {
            Response::json(['ok' => true]);
        }
        
        foreach ($this->routes as [$routeMethod, $routePath, $handler]) {
            if ($method !== $routeMethod) continue;
            
            // Convert route pattern to regex: /posts/{id} -> /posts/(?P<id>[^/]+)
            $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $routePath);
            $pattern = '#^' . $pattern . '$#';
            
            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $handler($params);
                return;
            }
        }
        
        Response::error('Not Found', 404);
    }
}
