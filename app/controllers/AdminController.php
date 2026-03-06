<?php

namespace App\Controllers;

use Core\Controller;
use Core\Database;
use App\Models\User;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\PageSetting;

class AdminController extends Controller
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function dashboard(): void
    {
        $stats = [
            'users'        => $this->db->fetch("SELECT COUNT(*) as cnt FROM users")['cnt'],
            'clients'      => $this->db->fetch("SELECT COUNT(*) as cnt FROM users WHERE role = 'client'")['cnt'],
            'freelancers'  => $this->db->fetch("SELECT COUNT(*) as cnt FROM users WHERE role = 'freelancer'")['cnt'],
            'orders_total' => $this->db->fetch("SELECT COUNT(*) as cnt FROM orders")['cnt'],
            'orders_open'  => $this->db->fetch("SELECT COUNT(*) as cnt FROM orders WHERE status = 'open'")['cnt'],
            'orders_active'=> $this->db->fetch("SELECT COUNT(*) as cnt FROM orders WHERE status = 'in_progress'")['cnt'],
            'orders_done'  => $this->db->fetch("SELECT COUNT(*) as cnt FROM orders WHERE status = 'completed'")['cnt'],
            'total_volume' => $this->db->fetch("SELECT COALESCE(SUM(final_price), 0) as total FROM orders WHERE status = 'completed'")['total'],
            'escrow_held'  => $this->db->fetch("SELECT COALESCE(SUM(amount), 0) as total FROM escrow WHERE status = 'held'")['total'],
            'fees_earned'  => $this->db->fetch("SELECT COALESCE(SUM(platform_fee), 0) as total FROM escrow WHERE status = 'released'")['total'],
        ];

        $recentUsers = $this->db->fetchAll(
            "SELECT * FROM users ORDER BY created_at DESC LIMIT 5"
        );
        $recentOrders = $this->db->fetchAll(
            "SELECT o.*, u.name as client_name FROM orders o JOIN users u ON o.client_id = u.id ORDER BY o.created_at DESC LIMIT 5"
        );

        $this->view('admin.dashboard', [
            'title'        => 'Админ-панель',
            'stats'        => $stats,
            'recentUsers'  => $recentUsers,
            'recentOrders' => $recentOrders,
        ]);
    }

    public function users(): void
    {
        $page = max(1, (int) ($this->input('page', 1)));
        $search = $this->input('search', '');
        $role = $this->input('role', '');
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $where = '1=1';
        $params = [];

        if ($search) {
            $where .= " AND (name LIKE ? OR email LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        if ($role) {
            $where .= " AND role = ?";
            $params[] = $role;
        }

        $total = (int) $this->db->fetch("SELECT COUNT(*) as cnt FROM users WHERE {$where}", $params)['cnt'];
        $totalPages = (int) ceil($total / $perPage);

        $users = $this->db->fetchAll(
            "SELECT * FROM users WHERE {$where} ORDER BY created_at DESC LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        $this->view('admin.users', [
            'title'      => 'Пользователи',
            'users'      => $users,
            'search'     => $search,
            'role'       => $role,
            'pagination' => ['page' => $page, 'total_pages' => $totalPages, 'total' => $total],
        ]);
    }

    public function userEdit(string $id): void
    {
        $userModel = new User();
        $user = $userModel->find((int) $id);

        if (!$user) {
            flash('error', 'Пользователь не найден.');
            $this->redirect(url('admin/users'));
            return;
        }

        $this->view('admin.user_edit', [
            'title'   => 'Редактировать: ' . $user['name'],
            'profile' => $user,
        ]);
    }

    public function userUpdate(string $id): void
    {
        if (!$this->validateCsrf()) {
            flash('error', 'Неверный токен безопасности.');
            $this->redirect(url("admin/users/{$id}"));
            return;
        }

        $userModel = new User();
        $user = $userModel->find((int) $id);

        if (!$user) {
            flash('error', 'Пользователь не найден.');
            $this->redirect(url('admin/users'));
            return;
        }

        $data = $this->allInput();
        $updateData = [
            'name'    => $data['name'],
            'email'   => $data['email'],
            'role'    => $data['role'],
            'balance' => (float) ($data['balance'] ?? $user['balance']),
            'bio'     => $data['bio'] ?? '',
        ];

        $userModel->update((int) $id, $updateData);

        flash('success', 'Пользователь обновлён.');
        $this->redirect(url("admin/users/{$id}"));
    }

    public function userDelete(string $id): void
    {
        if ((int) $id === user_id()) {
            flash('error', 'Нельзя удалить себя.');
            $this->redirect(url('admin/users'));
            return;
        }

        $userModel = new User();
        $userModel->destroy((int) $id);

        flash('success', 'Пользователь удалён.');
        $this->redirect(url('admin/users'));
    }

    public function orders(): void
    {
        $page = max(1, (int) ($this->input('page', 1)));
        $status = $this->input('status', '');
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $where = '1=1';
        $params = [];

        if ($status) {
            $where .= " AND o.status = ?";
            $params[] = $status;
        }

        $total = (int) $this->db->fetch("SELECT COUNT(*) as cnt FROM orders o WHERE {$where}", $params)['cnt'];
        $totalPages = (int) ceil($total / $perPage);

        $orders = $this->db->fetchAll(
            "SELECT o.*, u.name as client_name FROM orders o JOIN users u ON o.client_id = u.id WHERE {$where} ORDER BY o.created_at DESC LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        $this->view('admin.orders', [
            'title'      => 'Все заказы',
            'orders'     => $orders,
            'status'     => $status,
            'pagination' => ['page' => $page, 'total_pages' => $totalPages, 'total' => $total],
        ]);
    }

    public function orderDelete(string $id): void
    {
        $orderModel = new Order();
        $orderModel->destroy((int) $id);

        flash('success', 'Заказ удалён.');
        $this->redirect(url('admin/orders'));
    }

    public function transactions(): void
    {
        $page = max(1, (int) ($this->input('page', 1)));
        $perPage = 30;
        $offset = ($page - 1) * $perPage;

        $total = (int) $this->db->fetch("SELECT COUNT(*) as cnt FROM transactions")['cnt'];
        $totalPages = (int) ceil($total / $perPage);

        $transactions = $this->db->fetchAll(
            "SELECT t.*, u.name as user_name, u.email as user_email
             FROM transactions t JOIN users u ON t.user_id = u.id
             ORDER BY t.created_at DESC LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );

        $this->view('admin.transactions', [
            'title'        => 'Транзакции',
            'transactions' => $transactions,
            'pagination'   => ['page' => $page, 'total_pages' => $totalPages, 'total' => $total],
        ]);
    }

    public function pages(): void
    {
        $model = new PageSetting();
        $pages = $model->all();

        $this->view('admin.pages', [
            'title' => 'Управление страницами',
            'pages' => $pages,
        ]);
    }

    public function pageToggle(string $id): void
    {
        $page = $this->db->fetch("SELECT * FROM page_settings WHERE id = ?", [(int) $id]);

        if (!$page) {
            flash('error', 'Страница не найдена.');
            $this->redirect(url('admin/pages'));
            return;
        }

        $newStatus = $page['is_enabled'] ? 0 : 1;
        $model = new PageSetting();
        $model->toggle($page['page_key'], (bool) $newStatus);

        $statusText = $newStatus ? 'включена' : 'отключена';
        flash('success', "Страница «{$page['page_name']}» {$statusText}.");
        $this->redirect(url('admin/pages'));
    }
}
