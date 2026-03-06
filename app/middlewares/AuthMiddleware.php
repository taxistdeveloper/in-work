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
                echo json_encode(['error' => 'Необходима авторизация']);
            } else {
                header('Location: ' . APP_URL . '/login');
            }
            return false;
        }
        return true;
    }

    private function isApiRequest(): bool
    {
        return str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/');
    }
}
