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
}
