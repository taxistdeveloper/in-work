<?php

namespace Core;

class Router
{
    private array $routes = [];
    private array $middlewares = [];

    public function get(string $path, array $action, array $middleware = []): self
    {
        return $this->addRoute('GET', $path, $action, $middleware);
    }

    public function post(string $path, array $action, array $middleware = []): self
    {
        return $this->addRoute('POST', $path, $action, $middleware);
    }

    public function put(string $path, array $action, array $middleware = []): self
    {
        return $this->addRoute('PUT', $path, $action, $middleware);
    }

    public function delete(string $path, array $action, array $middleware = []): self
    {
        return $this->addRoute('DELETE', $path, $action, $middleware);
    }

    public function group(array $middleware, callable $callback): void
    {
        $previousMiddlewares = $this->middlewares;
        $this->middlewares = array_merge($this->middlewares, $middleware);
        $callback($this);
        $this->middlewares = $previousMiddlewares;
    }

    private function addRoute(string $method, string $path, array $action, array $middleware = []): self
    {
        $pattern = $this->convertToRegex($path);
        $this->routes[] = [
            'method'     => $method,
            'path'       => $path,
            'pattern'    => $pattern,
            'controller' => $action[0],
            'action'     => $action[1],
            'middleware'  => array_merge($this->middlewares, $middleware),
        ];
        return $this;
    }

    private function convertToRegex(string $path): string
    {
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    public function resolve(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                foreach ($route['middleware'] as $mw) {
                    if (is_array($mw)) {
                        $middlewareInstance = new $mw[0]($mw[1]);
                    } else {
                        $middlewareInstance = new $mw();
                    }
                    if (!$middlewareInstance->handle()) {
                        return;
                    }
                }

                $controller = new $route['controller']();
                $actionMethod = $route['action'];

                if (!method_exists($controller, $actionMethod)) {
                    $this->sendError(500, 'Действие не найдено');
                    return;
                }

                call_user_func_array([$controller, $actionMethod], $params);
                return;
            }
        }

        $this->sendError(404, 'Страница не найдена');
    }

    private function sendError(int $code, string $message): void
    {
        http_response_code($code);
        if ($this->isApiRequest()) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $message]);
        } else {
            $viewPath = __DIR__ . '/../views/pages/error.php';
            if (file_exists($viewPath)) {
                extract(['code' => $code, 'message' => $message]);
                require $viewPath;
            } else {
                echo "<h1>{$code}</h1><p>{$message}</p>";
            }
        }
    }

    private function isApiRequest(): bool
    {
        return str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/');
    }
}
