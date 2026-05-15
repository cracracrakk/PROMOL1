<?php
class Router
{
    private array $routes = [];

    public function get(string $path, string $handler): void   { $this->add('GET', $path, $handler); }
    public function post(string $path, string $handler): void  { $this->add('POST', $path, $handler); }
    public function put(string $path, string $handler): void   { $this->add('PUT', $path, $handler); }
    public function patch(string $path, string $handler): void { $this->add('PATCH', $path, $handler); }
    public function delete(string $path, string $handler): void{ $this->add('DELETE', $path, $handler); }

    private function add(string $method, string $path, string $handler): void
    {
        $pattern = '#^' . preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $path) . '$#';
        $this->routes[] = compact('method', 'pattern', 'handler');
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = '/' . trim($path, '/');
        if ($path === '/') $path = '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== strtoupper($method)) continue;
            if (preg_match($route['pattern'], $path, $m)) {
                $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
                [$class, $action] = explode('@', $route['handler']);
                $file = dirname(__DIR__) . '/controllers/' . $class . '.php';
                if (!is_file($file)) {
                    throw new RuntimeException("Controller no encontrado: $class");
                }
                require_once $file;
                $controller = new $class();
                if (!method_exists($controller, $action)) {
                    throw new RuntimeException("Acción no encontrada: $class@$action");
                }
                $controller->$action(...array_values($params));
                return;
            }
        }
        http_response_code(404);
        require dirname(__DIR__) . '/views/errors/404.php';
    }
}
