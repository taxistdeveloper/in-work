<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Order;
use App\Models\Bid;
use App\Models\Notification;
use App\Models\Message;

class DashboardController extends Controller
{
    public function index(): void
    {
        $user = $this->currentUser();
        $orderModel = new Order();
        $bidModel = new Bid();
        $notifModel = new Notification();
        $msgModel = new Message();

        $stats = $orderModel->countByStatus($user['id']);
        $activeOrders = $orderModel->getActiveOrders($user['id']);
        $unreadMessages = $msgModel->getUnreadCount($user['id']);
        $unreadNotifs = $notifModel->getUnreadCount($user['id']);

        $data = [
            'title'          => 'Dashboard',
            'stats'          => $stats,
            'activeOrders'   => $activeOrders,
            'unreadMessages' => $unreadMessages,
            'unreadNotifs'   => $unreadNotifs,
        ];

        if ($user['role'] === 'freelancer') {
            $myBids = $bidModel->getUserBidsWithOrders($user['id'], 1, 5);
            $data['myBids'] = $myBids['items'];
        }

        $this->view('dashboard.index', $data);
    }

    public function notifications(): void
    {
        $user = $this->currentUser();
        $notifModel = new Notification();

        $notifications = $notifModel->getUserNotifications($user['id']);
        $notifModel->markAllRead($user['id']);

        $this->json(['notifications' => $notifications]);
    }

    /** Unread notifications for polling; does not mark as read. */
    public function notificationsUnread(): void
    {
        $user = $this->currentUser();
        $notifModel = new Notification();
        $notifications = $notifModel->getUnreadNotifications($user['id']);
        $unreadCount = $notifModel->getUnreadCount($user['id']);
        $this->json([
            'notifications'  => $notifications,
            'unread_count'   => $unreadCount,
        ]);
    }

    /** Счётчики шапки/нижней навигации + список непрочитанных уведомлений для системного пуша (один запрос). */
    public function navBadges(): void
    {
        $user = $this->currentUser();
        $notifModel = new Notification();
        $msgModel = new Message();
        $notifications = $notifModel->getUnreadNotifications($user['id']);
        $this->json([
            'unread_notifications' => $notifModel->getUnreadCount($user['id']),
            'unread_messages'      => $msgModel->getUnreadCount($user['id']),
            'notifications'        => $notifications,
        ]);
    }
}
