<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use App\Models\Notification;

class ReviewController extends Controller
{
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            flash('error', 'Неверный токен безопасности.');
            $this->redirect(url('orders'));
            return;
        }

        $orderId = (int) $this->input('order_id', 0);
        $rating = (int) $this->input('rating', 0);
        $comment = trim((string) $this->input('comment', ''));

        if ($rating < 1 || $rating > 5) {
            flash('error', 'Оценка должна быть от 1 до 5.');
            $this->redirect(url("orders/{$orderId}"));
            return;
        }

        $orderModel = new Order();
        $order = $orderModel->find($orderId);

        if (!$order || $order['status'] !== 'completed') {
            flash('error', 'Невозможно оставить отзыв на этот заказ.');
            $this->redirect(url("orders/{$orderId}"));
            return;
        }

        $isClient = user_id() === (int) $order['client_id'];
        $isFreelancer = user_id() === (int) ($order['freelancer_id'] ?? 0);

        if (!$isClient && !$isFreelancer) {
            flash('error', 'Вы не участник этого заказа.');
            $this->redirect(url("orders/{$orderId}"));
            return;
        }

        $revieweeId = $isClient ? (int) $order['freelancer_id'] : (int) $order['client_id'];

        $reviewModel = new Review();
        if ($reviewModel->hasReviewed($orderId, user_id())) {
            flash('error', 'Вы уже оставили отзыв на этот заказ.');
            $this->redirect(url("orders/{$orderId}"));
            return;
        }

        $reviewModel->create([
            'order_id'    => $orderId,
            'reviewer_id' => user_id(),
            'reviewee_id' => $revieweeId,
            'rating'      => $rating,
            'comment'     => $comment,
        ]);

        $userModel = new User();
        $userModel->updateRating($revieweeId);

        $notifModel = new Notification();
        $reviewer = $this->currentUser();
        $notifModel->notify(
            $revieweeId, 'new_review',
            "{$reviewer['name']} оставил отзыв с оценкой {$rating}.",
            "/profile/{$revieweeId}"
        );

        flash('success', 'Отзыв отправлен!');
        $this->redirect(url("orders/{$orderId}"));
    }
}
