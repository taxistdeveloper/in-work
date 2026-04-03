<?php

namespace App\Controllers;

use Core\Controller;
use Core\Validator;
use App\Models\Order;
use App\Models\Bid;
use App\Models\Escrow;
use App\Models\Review;
use App\Models\Notification;
use App\Models\User;
use App\Models\Conversation;
use App\Models\FreelancerCategory;
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
        $categories = array_filter(
            $appConfig['categories'],
            static fn (string $label, string $slug): bool => category_mode($slug) === 'market',
            ARRAY_FILTER_USE_BOTH
        );

        $this->view('orders.edit', [
            'title'      => 'Редактировать заказ',
            'order'      => $order,
            'categories' => $categories,
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
        $categories = array_filter(
            $appConfig['categories'],
            static fn (string $label, string $slug): bool => category_mode($slug) === 'market',
            ARRAY_FILTER_USE_BOTH
        );

        $this->view('orders.create', [
            'title'      => 'Создать заказ',
            'categories' => $categories,
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

        $category = (string) $data['category'];
        if (! is_valid_category_slug($category)) {
            flash('error', 'Неизвестная категория.');
            $this->redirect(url('orders/create'));
            return;
        }

        if (category_mode($category) === 'catalog') {
            $freelancerId = (int) ($data['freelancer_id'] ?? 0);
            if ($freelancerId <= 0) {
                flash('error', 'Для этой категории выберите исполнителя в каталоге (создание через приложение).');
                $this->redirect(url('orders/create'));
                return;
            }
            if ((float) $data['budget'] < 100) {
                flash('error', 'Минимальный бюджет для найма из каталога — 100 ₸.');
                $this->redirect(url('orders/create'));
                return;
            }
            $this->createCatalogOrderWeb($data, $freelancerId);
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

    private function mapOrderForApi(array $order): array
    {
        return [
            'id' => (int) $order['id'],
            'client_id' => (int) $order['client_id'],
            'freelancer_id' => isset($order['freelancer_id']) ? (int) $order['freelancer_id'] : null,
            'title' => $order['title'],
            'description' => $order['description'],
            'category' => $order['category'],
            'budget' => isset($order['budget']) ? (float) $order['budget'] : 0.0,
            'final_price' => isset($order['final_price']) ? (float) $order['final_price'] : null,
            'deadline' => $order['deadline'] ?? null,
            'status' => $order['status'],
            'created_at' => $order['created_at'] ?? null,
            'updated_at' => $order['updated_at'] ?? null,
            'client_name' => $order['client_name'] ?? null,
            'client_rating' => isset($order['client_rating']) ? (float) $order['client_rating'] : null,
            'client_completed' => isset($order['client_completed']) ? (int) $order['client_completed'] : null,
        ];
    }

    public function apiShow(string $id): void
    {
        $order = $this->orderModel->getOrderWithClient((int) $id);
        if (!$order) {
            $this->jsonError('Заказ не найден', 404, [], 'NOT_FOUND');
            return;
        }

        $bidModel = new Bid();
        $bids = $bidModel->getOrderBids((int) $id);
        $userBid = is_logged_in() ? $bidModel->getBidByUserForOrder((int) $id, user_id()) : null;
        $reviews = (new Review())->getUserReviews((int) $order['client_id'], 1, 5);

        $this->jsonSuccess([
            'order' => $this->mapOrderForApi($order),
            'bids' => $bids,
            'user_bid' => $userBid,
            'client_reviews' => $reviews['items'],
        ]);
    }

    public function apiMyOrders(): void
    {
        $this->requireAuth();
        $page = max(1, (int) ($this->input('page', 1)));
        $user = $this->currentUser();
        $orders = $user['role'] === 'client'
            ? $this->orderModel->getClientOrders((int) $user['id'], $page)
            : $this->orderModel->getFreelancerOrders((int) $user['id'], $page);

        $this->jsonSuccess([
            'items' => array_map(fn(array $order): array => $this->mapOrderForApi($order), $orders['items']),
            'pagination' => [
                'page' => (int) $orders['page'],
                'per_page' => (int) $orders['per_page'],
                'total' => (int) $orders['total'],
                'totalPages' => (int) $orders['total_pages'],
            ],
        ]);
    }

    public function apiStore(): void
    {
        $this->requireAuth();
        if (user_role() !== 'client') {
            $this->jsonError('Только заказчик может создавать заказ', 403, [], 'FORBIDDEN');
            return;
        }

        $data = $this->allInput();
        $required = ['title', 'description', 'category', 'budget', 'deadline'];
        $errors = [];
        foreach ($required as $field) {
            if (trim((string) ($data[$field] ?? '')) === '') {
                $errors[$field] = 'Поле обязательно';
            }
        }
        $category = trim((string) ($data['category'] ?? ''));
        if ($category !== '' && ! is_valid_category_slug($category)) {
            $errors['category'] = 'Неизвестная категория';
        }

        $mode = $category !== '' ? category_mode($category) : 'market';
        $minBudget = $mode === 'catalog' ? 100 : 5;
        if ((float) ($data['budget'] ?? 0) < $minBudget) {
            $errors['budget'] = $mode === 'catalog'
                ? 'Бюджет для найма из каталога не менее 100'
                : 'Бюджет должен быть не менее 5';
        }
        if (mb_strlen((string) ($data['title'] ?? '')) < 5) {
            $errors['title'] = 'Название минимум 5 символов';
        }
        if (mb_strlen((string) ($data['description'] ?? '')) < 20) {
            $errors['description'] = 'Описание минимум 20 символов';
        }

        $freelancerId = (int) ($data['freelancer_id'] ?? 0);
        if ($mode === 'catalog' && $freelancerId <= 0) {
            $errors['freelancer_id'] = 'Выберите исполнителя из каталога';
        }

        if ($errors !== []) {
            $this->jsonError('Ошибка валидации', 422, $errors, 'VALIDATION_ERROR');
            return;
        }

        if ($mode === 'catalog') {
            $result = $this->hireFromCatalog(user_id(), $data, $freelancerId);
            if (!$result['ok']) {
                $this->jsonError($result['message'], $result['http'], $result['errors'] ?? [], $result['code'] ?? 'ERROR');
                return;
            }
            $order = $this->orderModel->getOrderWithClient((int) $result['order_id']);
            $this->jsonSuccess(['order' => $this->mapOrderForApi($order ?: [])], 'Исполнитель назначен, заказ в работе', 201);
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

        $order = $this->orderModel->find((int) $orderId);
        $this->jsonSuccess(['order' => $this->mapOrderForApi($order ?: [])], 'Заказ создан', 201);
    }

    /**
     * @param array<string, mixed> $data
     * @return array{ok: true, order_id: int}|array{ok: false, message: string, http: int, code: string, errors?: array<string, string>}
     */
    private function hireFromCatalog(int $clientId, array $data, int $freelancerId): array
    {
        $category = (string) $data['category'];
        $userModel = new User();
        $fl = $userModel->find($freelancerId);
        if (!$fl || ($fl['role'] ?? '') !== 'freelancer') {
            return ['ok' => false, 'message' => 'Исполнитель не найден', 'http' => 404, 'code' => 'NOT_FOUND'];
        }
        if ((int) $fl['id'] === $clientId) {
            return ['ok' => false, 'message' => 'Нельзя нанять самого себя', 'http' => 422, 'code' => 'INVALID'];
        }
        if (!(new FreelancerCategory())->userHasCategory($freelancerId, $category)) {
            return [
                'ok'       => false,
                'message'  => 'Исполнитель не указал эту специализацию',
                'http'     => 422,
                'code'     => 'FREELANCER_CATEGORY_MISMATCH',
                'errors'   => ['freelancer_id' => 'Не подходит для категории'],
            ];
        }

        $amount = (float) $data['budget'];
        $client = $userModel->find($clientId);
        if ((float) $client['balance'] < $amount) {
            return ['ok' => false, 'message' => 'Недостаточно средств. Пополните баланс', 'http' => 422, 'code' => 'INSUFFICIENT_FUNDS'];
        }

        $orderId = $this->orderModel->create([
            'client_id'     => $clientId,
            'freelancer_id' => $freelancerId,
            'title'         => $data['title'],
            'description'   => $data['description'],
            'category'      => $category,
            'budget'        => $amount,
            'final_price'   => $amount,
            'deadline'      => $data['deadline'],
            'status'        => 'in_progress',
        ]);

        $escrow = new EscrowService();
        if (!$escrow->holdFunds((int) $orderId, $clientId, $freelancerId, $amount)) {
            $this->orderModel->destroy((int) $orderId);

            return ['ok' => false, 'message' => 'Ошибка резервирования средств', 'http' => 500, 'code' => 'ESCROW_ERROR'];
        }

        $convModel = new Conversation();
        $convModel->findOrCreate($clientId, $freelancerId, (int) $orderId);

        $notifModel = new Notification();
        $notifModel->notify(
            $freelancerId,
            'catalog_hire',
            'Вас наняли по заказу «' . $data['title'] . '»',
            '/orders/' . $orderId
        );

        $_SESSION['user'] = $userModel->getSessionData($clientId);

        return ['ok' => true, 'order_id' => (int) $orderId];
    }

    /** @param array<string, mixed> $data */
    private function createCatalogOrderWeb(array $data, int $freelancerId): void
    {
        $result = $this->hireFromCatalog((int) user_id(), $data, $freelancerId);
        if (!$result['ok']) {
            flash('error', $result['message']);
            $this->redirect(url('orders/create'));
            return;
        }
        flash('success', 'Исполнитель назначен, средства зарезервированы на эскроу.');
        $this->redirect(url('orders/' . $result['order_id']));
    }

    /** Веб: отправка формы найма из каталога (POST /catalog/hire). */
    public function catalogHireStore(): void
    {
        $this->requireAuth();
        if (user_role() !== 'client') {
            flash('error', 'Только заказчик может нанять исполнителя.');
            $this->redirect(url('catalog'));
            return;
        }
        if (! $this->validateCsrf()) {
            flash('error', 'Неверный токен безопасности.');
            $this->redirect(url('catalog'));
            return;
        }

        $data = $this->allInput();
        $category = trim((string) ($data['category'] ?? ''));
        $freelancerId = (int) ($data['freelancer_id'] ?? 0);

        $errors = [];
        foreach (['title', 'description', 'category', 'budget', 'deadline'] as $f) {
            if (trim((string) ($data[$f] ?? '')) === '') {
                $errors[$f] = 'Поле обязательно';
            }
        }
        if ($category === '' || ! is_valid_category_slug($category) || category_mode($category) !== 'catalog') {
            $errors['category'] = 'Некорректная категория';
        }
        if ((float) ($data['budget'] ?? 0) < 100) {
            $errors['budget'] = 'Минимум 100 ₸ для найма из каталога';
        }
        if (mb_strlen((string) ($data['title'] ?? '')) < 5) {
            $errors['title'] = 'Минимум 5 символов';
        }
        if (mb_strlen((string) ($data['description'] ?? '')) < 20) {
            $errors['description'] = 'Минимум 20 символов';
        }
        if ($freelancerId <= 0) {
            $errors['freelancer_id'] = 'Не указан исполнитель';
        }

        $deadlineTs = strtotime((string) ($data['deadline'] ?? ''));
        $minTs = strtotime('tomorrow midnight');
        if ($deadlineTs === false || $deadlineTs < $minTs) {
            $errors['deadline'] = 'Дедлайн не раньше завтрашнего дня';
        }

        if ($errors !== []) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $data;
            $this->redirect(url('catalog/' . $category . '/hire/' . $freelancerId));
            return;
        }

        $result = $this->hireFromCatalog((int) user_id(), $data, $freelancerId);
        if (! $result['ok']) {
            flash('error', $result['message']);
            $_SESSION['old_input'] = $data;
            $this->redirect(url('catalog/' . $category . '/hire/' . $freelancerId));
            return;
        }

        flash('success', 'Исполнитель назначен, средства зарезервированы на эскроу.');
        $this->redirect(url('orders/' . $result['order_id']));
    }

    public function apiUpdate(string $id): void
    {
        $this->requireAuth();
        $order = $this->orderModel->find((int) $id);
        if (!$order || (int) $order['client_id'] !== user_id() || $order['status'] !== 'open') {
            $this->jsonError('Этот заказ нельзя изменить', 403, [], 'FORBIDDEN');
            return;
        }
        $data = $this->allInput();
        $this->orderModel->update((int) $id, [
            'title'       => $data['title'] ?? $order['title'],
            'description' => $data['description'] ?? $order['description'],
            'category'    => $data['category'] ?? $order['category'],
            'budget'      => isset($data['budget']) ? (float) $data['budget'] : (float) $order['budget'],
            'deadline'    => $data['deadline'] ?? $order['deadline'],
        ]);
        $updated = $this->orderModel->find((int) $id);
        $this->jsonSuccess(['order' => $this->mapOrderForApi($updated ?: $order)], 'Заказ обновлён');
    }

    public function apiComplete(string $id): void
    {
        $this->requireAuth();
        $order = $this->orderModel->find((int) $id);
        if (!$order || (int) $order['client_id'] !== user_id() || $order['status'] !== 'in_progress') {
            $this->jsonError('Невозможно завершить этот заказ', 403, [], 'FORBIDDEN');
            return;
        }
        (new EscrowService())->releaseFunds((int) $id);
        $this->orderModel->update((int) $id, ['status' => 'completed']);
        $_SESSION['user'] = (new \App\Models\User())->getSessionData(user_id());
        $this->jsonSuccess([], 'Заказ завершён');
    }

    public function apiDeliver(string $id): void
    {
        $this->requireAuth();
        $order = $this->orderModel->find((int) $id);
        if (!$order || (int) $order['freelancer_id'] !== user_id() || $order['status'] !== 'in_progress') {
            $this->jsonError('Невозможно сдать работу по этому заказу', 403, [], 'FORBIDDEN');
            return;
        }
        $message = trim((string) $this->input('delivery_message', ''));
        $this->orderModel->update((int) $id, [
            'delivered_at' => date('Y-m-d H:i:s'),
            'delivery_message' => $message !== '' ? $message : null,
        ]);
        $this->jsonSuccess([], 'Работа сдана');
    }

    public function apiCancel(string $id): void
    {
        $this->requireAuth();
        $order = $this->orderModel->find((int) $id);
        if (!$order || (int) $order['client_id'] !== user_id()) {
            $this->jsonError('Невозможно отменить этот заказ', 403, [], 'FORBIDDEN');
            return;
        }
        if ($order['status'] === 'in_progress') {
            (new EscrowService())->refundFunds((int) $id);
            $_SESSION['user'] = (new \App\Models\User())->getSessionData(user_id());
        }
        $this->orderModel->update((int) $id, ['status' => 'cancelled']);
        $this->jsonSuccess([], 'Заказ отменён');
    }

    public function apiDestroy(string $id): void
    {
        $this->requireAuth();
        if (user_role() !== 'client') {
            $this->jsonError('Удалять заказы могут только заказчики', 403, [], 'FORBIDDEN');
            return;
        }
        $order = $this->orderModel->find((int) $id);
        if (!$order || (int) $order['client_id'] !== user_id() || $order['status'] !== 'open') {
            $this->jsonError('Этот заказ нельзя удалить', 403, [], 'FORBIDDEN');
            return;
        }
        $this->orderModel->destroy((int) $id);
        $this->jsonSuccess([], 'Заказ удалён');
    }
}
