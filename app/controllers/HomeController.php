<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Order;

class HomeController extends Controller
{
    public function index(): void
    {
        $orderModel = new Order();
        $recentOrders = $orderModel->getOpenOrders(1, 6);

        $this->view('pages.home', [
            'title'  => 'Найди работу, найми специалиста',
            'orders' => $recentOrders['items'],
        ]);
    }

    public function help(): void
    {
        $this->view('pages.help', [
            'title' => 'Центр помощи',
        ]);
    }

    public function privacy(): void
    {
        $this->view('pages.privacy', [
            'title' => 'Политика конфиденциальности',
        ]);
    }

    public function terms(): void
    {
        $this->view('pages.terms', [
            'title' => 'Условия использования',
        ]);
    }
}
