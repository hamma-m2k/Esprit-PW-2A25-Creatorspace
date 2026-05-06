<?php
class Router {
    private array $routes = [];

    public function add(string $method, string $path, string $handler): void {
        $this->routes[] = compact('method', 'path', 'handler');
    }

    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $base = str_replace('/index.php', '', dirname($_SERVER['SCRIPT_NAME']));
        $uri = substr($uri, strlen($base)) ?: '/';
        if ($uri === '' || $uri[0] !== '/') $uri = '/' . $uri;

        foreach ($this->routes as $route) {
            $pattern = '#^' . $route['path'] . '$#';
            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                [$controllerName, $action] = explode('@', $route['handler']);
                $file = ROOT . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . $controllerName . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    $controller = new $controllerName();
                    call_user_func_array([$controller, $action], $matches);
                    return;
                }
            }
        }

        http_response_code(404);
        echo '<h1>404 - Page non trouvée</h1>';
    }
}
