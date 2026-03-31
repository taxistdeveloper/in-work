<?php

namespace Core;

abstract class Controller
{
    protected function isApiRequest(): bool
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        return strpos($uri, '/api/') === 0;
    }

    protected function view(string $view, array $data = []): void
    {
        extract($data);

        $viewPath = __DIR__ . '/../views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View [{$view}] not found.");
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        if (strpos($view, 'admin.') === 0) {
            $layoutPath = __DIR__ . '/../views/admin/layout.php';
        } else {
            $layoutPath = __DIR__ . '/../views/layouts/app.php';
        }

        if (file_exists($layoutPath)) {
            require $layoutPath;
        } else {
            echo $content;
        }
    }

    protected function viewPartial(string $view, array $data = []): void
    {
        extract($data);

        $viewPath = __DIR__ . '/../views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View [{$view}] not found.");
        }

        require $viewPath;
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    protected function jsonSuccess(array $data = [], string $message = 'OK', int $statusCode = 200): void
    {
        $this->json([
            'status' => 'ok',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    protected function jsonError(string $message, int $statusCode = 400, array $errors = [], string $code = ''): void
    {
        $this->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
            'code' => $code,
        ], $statusCode);
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function allInput(): array
    {
        $json = json_decode(file_get_contents('php://input'), true);
        return $json ?? array_merge($_GET, $_POST);
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    protected function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function validateCsrf(): bool
    {
        $token = $this->input('csrf_token') ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    protected function currentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    protected function isAuthenticated(): bool
    {
        return isset($_SESSION['user']);
    }

    protected function requireAuth(): void
    {
        if (!$this->isAuthenticated()) {
            $this->redirect(url('login'));
        }
    }
}
