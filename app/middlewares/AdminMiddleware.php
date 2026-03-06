<?php

namespace App\Middlewares;

class AdminMiddleware
{
    public function handle(): bool
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            if ($this->isApiRequest()) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Доступ запрещён']);
            } else {
                header('Location: ' . APP_URL . '/dashboard');
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
