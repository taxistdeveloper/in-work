<?php

namespace App\Controllers;

use Core\Controller;
use Core\Validator;
use App\Models\Order;
use App\Models\Bid;
use App\Models\Escrow;
use App\Models\Review;
use App\Models\Notification;
use App\Services\EscrowService;

class OrderController extends Controller
{
    private Order $orderModel;

    public function __construct()
    {
        $this->orderModel = new Order();
    }

    public function index(): void
    {
        $this->requireAuth();
        if (user_role() === 'client') {
            $this->redirect(url('orders/edit'));
            return;
        }

        $page = max(1, (int) ($this->input('page', 1)));
        $category = $this->input('category', '');
        $search = $this->input('search', '');

        $orders = $this->orderModel->getOpenOrders($page, 20, $category, $search);
        $appConfig = require ROOT_PATH . '/config/app.php';

        $this->view('orders.index', [
            'title'      => 'Лента заказов',
            'orders'     => $orders['items'],
            'pagination' => $orders,
            'categories' => $appConfig['categories'],
            'category'   => $category,
            'search'     => $search,
        ]);
    }

    public function apiIndex(): void
    {
        $page = max(1, (int) ($this->input('page', 1)));
        $category = $this->input('category', '');
        $search = $this->input('search', '');

        $orders = $this->orderModel->getOpenOrders($page, 20, $category, $search);

        $this->json([
            'items' => array_map(static function (array $order): array {
                return [
                    'id'          => (int) $order['id'],
                    'title'       => $order['title'],
                    'description' => $order['description'],
                    'category'    => $order['category'],
                    'budget'      => (float) $order['budget'],
                    'deadline'    => $order['deadline'],
                    'status'      => $order['status'],
                    'created_at'  => $order['created_at'],
                ];
            }, $orders['items']),
            'pagination' => [
                'page'       => (int) $orders['page'],
                'per_page'   => (int) $orders['per_page'],
                'total'      => (int) $orders['total'],
                'totalPages' => (int) $orders['total_pages'],
            ],
        ]);
    }

    public function editSelect(): void
    {
        $this->requireAuth();

        if (user_role() !== 'client') {
            $this->redirect(url('orders'));
            return;
        }

        $page = max(1, (int) ($this->input('page', 1)));
        $orders = $this->orderModel->getClientOpenOrders(user_id(), $page);

        $this->view('orders.edit_select', [
            'title'      => 'Редактировать заказ',
            'orders'     => $orders['items'],
            'pagination' => $orders,
        ]);
    }

    public function edit(string $id): void
    {
        $this->requireAuth();

        if (user_role() !== 'client') {
            flash('error', 'Редактирование доступно только заказчикам.');
            $this->redirect(url('orders'));
            return;
        }

        $order = $this->orderModel->find((int) $id);
        if (!$order || (int) $order['client_id'] !== user_id() || $order['status'] !== 'open') {
            flash('error', 'Этот заказ нельзя изменить.');
            $this->redirect(url('orders/edit'));
            return;
        }

        $appConfig = require ROOT_PATH . '/config/app.php';

        $this->view('orders.edit', [
            'title'      => 'Редактировать заказ',
            'order'      => $order,
            'categories' => $appConfig['categories'],
        ]);
    }

    public function update(string $id): void
    {
        $this->requireAuth();

        if (user_role() !== 'client') {
            flash('error', 'Редактирование доступно только заказчикам.');
            $this->redirect(url('orders'));
            return;
        }

        $order = $this->orderModel->find((int) $id);
        if (!$order || (int) $order['client_id'] !== user_id() || $order['status'] !== 'open') {
            flash('error', 'Этот заказ нельзя изменить.');
            $this->redirect(url('orders/edit'));
            return;
        }

        $data = $this->allInput();

        if (!$this->validateCsrf()) {
            flash('error', 'Неверный токен безопасности.');
            $this->redirect(url("orders/{$id}/edit"));
            return;
        }

        $validator = new Validator($data);
        $validator
            ->required('title', 'Название')
            ->minLength('title', 5, 'Название')
            ->maxLength('title', 255, 'Название')
            ->required('description', 'Описание')
            ->minLength('description', 20, 'Описание')
            ->required('category', 'Категория')
            ->required('budget', 'Бюджет')
            ->numeric('budget', 'Бюджет')
            ->min('budget', 5, 'Бюджет')
            ->required('deadline', 'Дедлайн');

        if ($validator->fails()) {
            $_SESSION['old_input'] = $data;
            $_SESSION['errors'] = $validator->firstErrors();
            $this->redirect(url("orders/{$id}/edit"));
            return;
        }

        $deadlineTs = strtotime((string) $data['deadline']);
        $minTs = strtotime('tomorrow midnight');
        if ($deadlineTs === false || $deadlineTs < $minTs) {
            $_SESSION['old_input'] = $data;
            $_SESSION['errors'] = ['deadline' => 'Дедлайн должен быть не раньше завтрашнего дня.'];
            $this->redirect(url("orders/{$id}/edit"));
            return;
        }

        $this->orderModel->update((int) $id, [
            'title'       => $data['title'],
            'description' => $data['description'],
            'category'    => $data['category'],
            'budget'      => (float) $data['budget'],
            'deadline'    => $data['deadline'],
        ]);

        flash('success', 'Заказ обновлён.');
        $this->redirect(url("orders/{$id}"));
    }

    public function create(): void
    {
        $this->requireAuth();

        $appConfig = require ROOT_PATH . '/config/app.php';

        $this->view('orders.create', [
            'title'      => 'Создать заказ',
            'categories' => $appConfig['categories'],
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $data = $this->allInput();

        if (!$this->validateCsrf()) {
            flash('error', 'Неверный токен безопасности.');
            $this->redirect(url('orders/create'));
            return;
        }

        $validator = new Validator($data);
        $validator
            ->required('title', 'Название')
            ->minLength('title', 5, 'Название')
            ->maxLength('title', 255, 'Название')
            ->required('description', 'Описание')
            ->minLength('description', 20, 'Описание')
            ->required('category', 'Категория')
            ->required('budget', 'Бюджет')
            ->numeric('budget', 'Бюджет')
            ->min('budget', 5, 'Бюджет')
            ->required('deadline', 'Дедлайн');

        if ($validator->fails()) {
            $_SESSION['old_input'] = $data;
            $_SESSION['errors'] = $validator->firstErrors();
            $this->redirect(url('orders/create'));
            return;
        }

        $orderId = $this->orderModel->create([
            'client_id'   => user_id(),
            'title'       => $data['title'],
            'description' => $data['description'],
            'category'    => $data['category'],
            'budget'      => (float) $data['budget'],
            'deadline'    => $data['deadline'],
            'status'      => 'open',
        ]);

        flash('success', 'Заказ успешно создан!');
        $this->redirect(url("orders/{$orderId}"));
    }

    public function show(string $id): void
    {
        $order = $this->orderModel->getOrderWithClient((int) $id);

        if (!$order) {
            $this->redirect(is_logged_in() && user_role() === 'client' ? url('orders/edit') : url('orders'));
            return;
        }

        $bidModel = new Bid();
        $bids = $bidModel->getOrderBids((int) $id);
        $userBid = is_logged_in() ? $bidModel->getBidByUserForOrder((int) $id, user_id()) : null;

        $reviewModel = new Review();
        $canReview = false;
        $hasReviewed = false;
        if (is_logged_in() && $order['status'] === 'completed') {
            $hasReviewed = $reviewModel->hasReviewed((int) $id, user_id());
            $canReview = !$hasReviewed && (
                user_id() === (int) $order['client_id'] ||
                user_id() === (int) ($order['freelancer_id'] ?? 0)
            );
        }

        $escrowModel = new Escrow();
        $escrow = $escrowModel->findByOrder((int) $id);

        $this->view('orders.show', [
            'title'       => $order['title'],
            'order'       => $order,
            'bids'        => $bids,
            'userBid'     => $userBid,
            'canReview'   => $canReview,
            'hasReviewed' => $hasReviewed,
            'escrow'      => $escrow,
        ]);
    }

    public function myOrders(): void
    {
        $this->requireAuth();

        (new Notification())->markAllRead(user_id());

        $page = max(1, (int) ($this->input('page', 1)));
        $user = $this->currentUser();

        if ($user['role'] === 'client') {
            $orders = $this->orderModel->getClientOrders($user['id'], $page);
        } else {
            $orders = $this->orderModel->getFreelancerOrders($user['id'], $page);
        }

        $this->view('orders.my_orders', [
            'title'      => 'Мои заказы',
            'orders'     => $orders['items'],
            'pagination' => $orders,
        ]);
    }

    public function complete(string $id): void
    {
        $this->requireAuth();
        $order = $this->orderModel->find((int) $id);

        if (!$order || (int) $order['client_id'] !== user_id() || $order['status'] !== 'in_progress') {
            flash('error', 'Невозможно завершить этот заказ.');
            $this->redirect(url("orders/{$id}"));
            return;
        }

        $escrowService = new EscrowService();
        $escrowService->releaseFunds((int) $id);

        $this->orderModel->update((int) $id, ['status' => 'completed']);

        $notifModel = new Notification();
        $notifModel->notify(
            (int) $order['freelancer_id'], 'order_completed',
            "Заказ \"{$order['title']}\" завершён!",
            "/orders/{$id}"
        );

        $_SESSION['user'] = (new \App\Models\User())->getSessionData(user_id());

        flash('success', 'Заказ завершён! Средства переведены исполнителю.');
        $this->redirect(url("orders/{$id}"));
    }

    public function deliver(string $id): void
    {
        $this->requireAuth();
        $order = $this->orderModel->find((int) $id);

        if (!$order || (int) $order['freelancer_id'] !== user_id() || $order['status'] !== 'in_progress') {
            flash('error', 'Невозможно сдать работу по этому заказу.');
            $this->redirect(url("orders/{$id}"));
            return;
        }

        if (!empty($order['delivered_at'])) {
            flash('info', 'Работа по этому заказу уже сдана.');
            $this->redirect(url("orders/{$id}"));
            return;
        }

        $message = trim((string) $this->input('delivery_message', ''));

        $this->orderModel->update((int) $id, [
            'delivered_at'      => date('Y-m-d H:i:s'),
            'delivery_message'  => $message !== '' ? $message : null,
        ]);

        $user = $this->currentUser();
        $notifModel = new Notification();
        $notifModel->notify(
            (int) $order['client_id'], 'work_delivered',
            "Исполнитель {$user['name']} сдал работу по заказу «{$order['title']}». Проверьте и завершите заказ.",
            "/orders/{$id}"
        );

        flash('success', 'Работа сдана! Ожидайте подтверждения заказчика.');
        $this->redirect(url("orders/{$id}"));
    }

    public function cancel(string $id): void
    {
        $this->requireAuth();
        $order = $this->orderModel->find((int) $id);

        if (!$order || (int) $order['client_id'] !== user_id()) {
            flash('error', 'Невозможно отменить этот заказ.');
            $this->redirect(url("orders/{$id}"));
            return;
        }

        if ($order['status'] === 'in_progress') {
            $escrowService = new EscrowService();
            $escrowService->refundFunds((int) $id);
            $_SESSION['user'] = (new \App\Models\User())->getSessionData(user_id());
        }

        $this->orderModel->update((int) $id, ['status' => 'cancelled']);

        if ($order['freelancer_id']) {
            $notifModel = new Notification();
            $notifModel->notify(
                (int) $order['freelancer_id'], 'order_cancelled',
                "Заказ \"{$order['title']}\" был отменён.",
                "/orders/{$id}"
            );
        }

        flash('success', 'Заказ отменён.');
        $this->redirect(url("orders/{$id}"));
    }

    public function destroy(string $id): void
    {
        $this->requireAuth();

        if (user_role() !== 'client') {
            flash('error', 'Удалять заказы могут только заказчики.');
            $this->redirect(url('my-orders'));
            return;
        }

        if (!$this->validateCsrf()) {
            flash('error', 'Неверный токен безопасности.');
            $this->redirect(url('my-orders'));
            return;
        }

        $order = $this->orderModel->find((int) $id);
        if (!$order || (int) $order['client_id'] !== user_id() || $order['status'] !== 'open') {
            flash('error', 'Этот заказ нельзя удалить.');
            $this->redirect(url('my-orders'));
            return;
        }

        $escrowModel = new Escrow();
        if ($escrowModel->findByOrder((int) $id)) {
            flash('error', 'Нельзя удалить заказ с зарезервированными средствами.');
            $this->redirect(url("orders/{$id}"));
            return;
        }

        $this->orderModel->destroy((int) $id);
        flash('success', 'Заказ удалён.');
        $this->redirect(url('my-orders'));
    }
}
