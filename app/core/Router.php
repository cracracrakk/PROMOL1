<?php
class Router {
    private array $routes = ['GET' => [], 'POST' => []];

    public function get(string $path, $handler): void  { $this->routes['GET'][$this->norm($path)]  = $handler; }
    public function post(string $path, $handler): void { $this->routes['POST'][$this->norm($path)] = $handler; }

    private function norm(string $p): string { return '/' . trim($p, '/'); }

    public function dispatch(string $method, string $uri): void {
        $uri = parse_url($uri, PHP_URL_PATH) ?? '/';
        $uri = $this->norm($uri);

        $handlers = $this->routes[$method] ?? [];

        // Coincidencia exacta
        if (isset($handlers[$uri])) {
            $this->invoke($handlers[$uri], []);
            return;
        }

        // Coincidencia con parámetros {id}
        foreach ($handlers as $pattern => $handler) {
            $regex = '#^' . preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $pattern) . '$#';
            if (preg_match($regex, $uri, $m)) {
                $params = array_filter($m, fn($k) => !is_int($k), ARRAY_FILTER_USE_KEY);
                $this->invoke($handler, $params);
                return;
            }
        }

        http_response_code(404);
        require dirname(__DIR__) . '/views/errors/404.php';
    }

    private function invoke($handler, array $params): void {
        if (is_callable($handler)) {
            call_user_func_array($handler, array_values($params));
            return;
        }
        [$class, $method] = explode('@', $handler);
        require_once dirname(__DIR__) . "/controllers/{$class}.php";
        $instance = new $class();
        call_user_func_array([$instance, $method], array_values($params));
    }
}
