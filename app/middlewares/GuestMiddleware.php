<?php

namespace App\Middlewares;

class GuestMiddleware
{
    public function handle(): bool
    {
        if (isset($_SESSION['user'])) {
            header('Location: ' . APP_URL . '/dashboard');
            return false;
        }
        return true;
    }
}
