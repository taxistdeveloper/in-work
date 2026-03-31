<?php

namespace App\Middlewares;

class AuthMiddleware
{
    public function handle(): bool
    {
        if (!isset($_SESSION['user'])) {
            if ($this->isApiRequest()) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Необходима авторизация',
                    'errors' => [],
                    'code' => 'UNAUTHORIZED',
                ], JSON_UNESCAPED_UNICODE);
            } else {
                header('Location: ' . APP_URL . '/login');
            }
            return false;
        }
        return true;
    }

    private function isApiRequest(): bool
    {
        return strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') === 0;
    }
}
