<?php

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\OrderController;
use App\Controllers\BidController;
use App\Controllers\ProfileController;
use App\Controllers\DashboardController;
use App\Controllers\BalanceController;
use App\Controllers\ReviewController;
use App\Controllers\ChatController;
use App\Controllers\AdminController;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\GuestMiddleware;
use App\Middlewares\AdminMiddleware;
use App\Middlewares\PageAccessMiddleware;

/** @var Core\Router $router */

// Public
$router->get('/', [HomeController::class, 'index']);

// Auth (guest only)
$router->get('/register', [AuthController::class, 'showRegister'], [GuestMiddleware::class, [PageAccessMiddleware::class, 'register']]);
$router->post('/register', [AuthController::class, 'register'], [GuestMiddleware::class, [PageAccessMiddleware::class, 'register']]);
$router->get('/login', [AuthController::class, 'showLogin'], [GuestMiddleware::class]);
$router->post('/login', [AuthController::class, 'login'], [GuestMiddleware::class]);
$router->get('/logout', [AuthController::class, 'logout']);

// Dashboard (auth required)
$router->group([AuthMiddleware::class], function ($router) {
    // Dashboard
    $router->get('/dashboard', [DashboardController::class, 'index'], [[PageAccessMiddleware::class, 'dashboard']]);

    // Orders
    $router->get('/orders', [OrderController::class, 'index'], [[PageAccessMiddleware::class, 'orders']]);
    $router->get('/orders/create', [OrderController::class, 'create'], [[PageAccessMiddleware::class, 'orders_create']]);
    $router->post('/orders/create', [OrderController::class, 'store'], [[PageAccessMiddleware::class, 'orders_create']]);
    $router->get('/orders/{id}', [OrderController::class, 'show'], [[PageAccessMiddleware::class, 'orders']]);
    $router->post('/orders/{id}/complete', [OrderController::class, 'complete'], [[PageAccessMiddleware::class, 'orders']]);
    $router->post('/orders/{id}/cancel', [OrderController::class, 'cancel'], [[PageAccessMiddleware::class, 'orders']]);
    $router->get('/my-orders', [OrderController::class, 'myOrders'], [[PageAccessMiddleware::class, 'my_orders']]);

    // Bids
    $router->post('/orders/{id}/bid', [BidController::class, 'store'], [[PageAccessMiddleware::class, 'orders']]);
    $router->post('/bids/{id}/accept', [BidController::class, 'accept'], [[PageAccessMiddleware::class, 'orders']]);
    $router->post('/bids/{id}/reject', [BidController::class, 'reject'], [[PageAccessMiddleware::class, 'orders']]);

    // Profile
    $router->get('/profile/{id}', [ProfileController::class, 'show'], [[PageAccessMiddleware::class, 'profile']]);
    $router->get('/profile', [ProfileController::class, 'edit'], [[PageAccessMiddleware::class, 'profile']]);
    $router->post('/profile', [ProfileController::class, 'update'], [[PageAccessMiddleware::class, 'profile']]);

    // Balance
    $router->get('/balance', [BalanceController::class, 'index'], [[PageAccessMiddleware::class, 'balance']]);
    $router->post('/balance/deposit', [BalanceController::class, 'deposit'], [[PageAccessMiddleware::class, 'balance']]);
    $router->post('/balance/withdraw', [BalanceController::class, 'withdraw'], [[PageAccessMiddleware::class, 'balance']]);

    // Reviews
    $router->post('/reviews', [ReviewController::class, 'store'], [[PageAccessMiddleware::class, 'reviews']]);

    // Chat
    $router->get('/chat', [ChatController::class, 'index'], [[PageAccessMiddleware::class, 'chat']]);
    $router->get('/chat/{id}', [ChatController::class, 'show'], [[PageAccessMiddleware::class, 'chat']]);
    $router->post('/chat/{id}/send', [ChatController::class, 'send'], [[PageAccessMiddleware::class, 'chat']]);

    // API endpoints
    $router->get('/api/notifications', [DashboardController::class, 'notifications']);
    $router->get('/api/chat/{id}/messages', [ChatController::class, 'messages'], [[PageAccessMiddleware::class, 'chat']]);
});

// Admin panel (admin only)
$router->group([AuthMiddleware::class, AdminMiddleware::class], function ($router) {
    $router->get('/admin', [AdminController::class, 'dashboard']);
    $router->get('/admin/users', [AdminController::class, 'users']);
    $router->get('/admin/users/{id}', [AdminController::class, 'userEdit']);
    $router->post('/admin/users/{id}', [AdminController::class, 'userUpdate']);
    $router->get('/admin/users/{id}/delete', [AdminController::class, 'userDelete']);
    $router->get('/admin/orders', [AdminController::class, 'orders']);
    $router->get('/admin/orders/{id}/delete', [AdminController::class, 'orderDelete']);
    $router->get('/admin/transactions', [AdminController::class, 'transactions']);
    $router->get('/admin/pages', [AdminController::class, 'pages']);
    $router->get('/admin/pages/{id}/toggle', [AdminController::class, 'pageToggle']);
});
