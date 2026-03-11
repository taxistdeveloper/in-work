<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Order;
use App\Models\Bid;
use App\Models\User;
use App\Models\Notification;
use App\Models\Conversation;
use App\Services\EscrowService;

class BidController extends Controller
{
    private Bid $bidModel;
    private Order $orderModel;

    public function __construct()
    {
        $this->bidModel = new Bid();
        $this->orderModel = new Order();
    }

    public function store(string $orderId): void
    {
        $this->requireAuth();
        $user = $this->currentUser();

        if ($user['role'] !== 'freelancer') {
            flash('error', 'Только исполнители могут откликаться.');
            $this->redirect(url("orders/{$orderId}"));
            return;
        }

        $order = $this->orderModel->find((int) $orderId);
        if (!$order || $order['status'] !== 'open') {
            flash('error', 'Заказ недоступен для откликов.');
            $this->redirect(url("orders/{$orderId}"));
            return;
        }

        if ((int) $order['client_id'] === user_id()) {
            flash('error', 'Вы не можете откликнуться на свой заказ.');
            $this->redirect(url("orders/{$orderId}"));
            return;
        }

        if ($this->bidModel->hasUserBid((int) $orderId, user_id())) {
            flash('error', 'Вы уже отправили отклик.');
            $this->redirect(url("orders/{$orderId}"));
            return;
        }

        $amount = (float) $this->input('amount', 0);
        $message = trim((string) $this->input('message', ''));

        if ($amount < 100) {
            flash('error', 'Сумма отклика должна быть не менее 100 ₸.');
            $this->redirect(url("orders/{$orderId}"));
            return;
        }

        $this->bidModel->create([
            'order_id'      => (int) $orderId,
            'freelancer_id' => user_id(),
            'amount'        => $amount,
            'message'       => $message,
            'status'        => 'pending',
        ]);

        $notifModel = new Notification();
        $notifModel->notify(
            (int) $order['client_id'], 'new_bid',
            "Пришёл отклик от {$user['name']}: " . format_money($amount) . " за «{$order['title']}»",
            "/orders/{$orderId}"
        );

        flash('success', 'Отклик отправлен!');
        $this->redirect(url("orders/{$orderId}"));
    }

    public function accept(string $bidId): void
    {
        $this->requireAuth();

        $bid = $this->bidModel->find((int) $bidId);
        if (!$bid) {
            flash('error', 'Отклик не найден.');
            $this->redirect(url('orders'));
            return;
        }

        $order = $this->orderModel->find((int) $bid['order_id']);
        if (!$order || (int) $order['client_id'] !== user_id() || $order['status'] !== 'open') {
            flash('error', 'Невозможно принять этот отклик.');
            $this->redirect(url("orders/{$bid['order_id']}"));
            return;
        }

        $userModel = new User();
        $client = $userModel->find(user_id());

        if ((float) $client['balance'] < (float) $bid['amount']) {
            flash('error', 'Недостаточно средств. Пополните баланс.');
            $this->redirect(url("orders/{$bid['order_id']}"));
            return;
        }

        $escrowService = new EscrowService();
        $success = $escrowService->holdFunds(
            (int) $bid['order_id'],
            user_id(),
            (int) $bid['freelancer_id'],
            (float) $bid['amount']
        );

        if (!$success) {
            flash('error', 'Ошибка обработки эскроу. Проверьте баланс.');
            $this->redirect(url("orders/{$bid['order_id']}"));
            return;
        }

        $this->orderModel->update((int) $bid['order_id'], [
            'freelancer_id' => $bid['freelancer_id'],
            'final_price'   => $bid['amount'],
            'status'        => 'in_progress',
        ]);

        $this->bidModel->update((int) $bidId, ['status' => 'accepted']);

        $db = \Core\Database::getInstance();
        $db->query(
            "UPDATE bids SET status = 'rejected' WHERE order_id = ? AND id != ? AND status = 'pending'",
            [(int) $bid['order_id'], (int) $bidId]
        );

        $notifModel = new Notification();
        $notifModel->notify(
            (int) $bid['freelancer_id'], 'bid_accepted',
            "Ваш отклик на \"{$order['title']}\" принят!",
            "/orders/{$bid['order_id']}"
        );

        $convModel = new Conversation();
        $convModel->findOrCreate(user_id(), (int) $bid['freelancer_id'], (int) $bid['order_id']);

        $_SESSION['user'] = $userModel->getSessionData(user_id());

        flash('success', 'Отклик принят! Средства на эскроу.');
        $this->redirect(url("orders/{$bid['order_id']}"));
    }

    public function reject(string $bidId): void
    {
        $this->requireAuth();

        $bid = $this->bidModel->find((int) $bidId);
        if (!$bid) {
            flash('error', 'Отклик не найден.');
            $this->redirect(url('orders'));
            return;
        }

        $order = $this->orderModel->find((int) $bid['order_id']);
        if (!$order || (int) $order['client_id'] !== user_id()) {
            flash('error', 'Невозможно отклонить этот отклик.');
            $this->redirect(url("orders/{$bid['order_id']}"));
            return;
        }

        $this->bidModel->update((int) $bidId, ['status' => 'rejected']);

        $notifModel = new Notification();
        $notifModel->notify(
            (int) $bid['freelancer_id'], 'bid_rejected',
            "Ваш отклик на \"{$order['title']}\" отклонён.",
            "/orders/{$bid['order_id']}"
        );

        flash('success', 'Отклик отклонён.');
        $this->redirect(url("orders/{$bid['order_id']}"));
    }
}
