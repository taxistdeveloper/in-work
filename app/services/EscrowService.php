<?php

namespace App\Services;

use Core\Database;
use App\Models\User;
use App\Models\Order;
use App\Models\Escrow;
use App\Models\Transaction;
use App\Models\Notification;

class EscrowService
{
    private Database $db;
    private User $userModel;
    private Order $orderModel;
    private Escrow $escrowModel;
    private Transaction $transactionModel;
    private Notification $notificationModel;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->userModel = new User();
        $this->orderModel = new Order();
        $this->escrowModel = new Escrow();
        $this->transactionModel = new Transaction();
        $this->notificationModel = new Notification();
    }

    public function holdFunds(int $orderId, int $clientId, int $freelancerId, float $amount): bool
    {
        $this->db->beginTransaction();

        try {
            $client = $this->userModel->find($clientId);

            if ((float) $client['balance'] < $amount) {
                $this->db->rollBack();
                return false;
            }

            $fee = round($amount * PLATFORM_FEE, 2);

            $this->userModel->updateBalance($clientId, -$amount);

            $updatedClient = $this->userModel->find($clientId);
            $this->transactionModel->log(
                $clientId, 'escrow_hold', -$amount, (float) $updatedClient['balance'],
                "Эскроу для заказа #{$orderId}", $orderId
            );

            $this->escrowModel->create([
                'order_id'      => $orderId,
                'client_id'     => $clientId,
                'freelancer_id' => $freelancerId,
                'amount'        => $amount,
                'platform_fee'  => $fee,
                'status'        => 'held',
            ]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function releaseFunds(int $orderId): bool
    {
        $this->db->beginTransaction();

        try {
            $escrow = $this->escrowModel->findByOrder($orderId);
            if (!$escrow || $escrow['status'] !== 'held') {
                $this->db->rollBack();
                return false;
            }

            $payout = (float) $escrow['amount'] - (float) $escrow['platform_fee'];

            $this->userModel->updateBalance($escrow['freelancer_id'], $payout);

            $updatedFreelancer = $this->userModel->find($escrow['freelancer_id']);
            $this->transactionModel->log(
                $escrow['freelancer_id'], 'earning', $payout, (float) $updatedFreelancer['balance'],
                "Оплата за заказ #{$orderId}", $orderId
            );

            $this->escrowModel->update($escrow['id'], [
                'status'      => 'released',
                'released_at' => date('Y-m-d H:i:s'),
            ]);

            $this->userModel->incrementCompleted($escrow['freelancer_id']);

            $this->notificationModel->notify(
                $escrow['freelancer_id'], 'payment',
                "Оплата " . format_money($payout) . " за заказ #{$orderId} переведена",
                "/orders/{$orderId}"
            );

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function refundFunds(int $orderId): bool
    {
        $this->db->beginTransaction();

        try {
            $escrow = $this->escrowModel->findByOrder($orderId);
            if (!$escrow || $escrow['status'] !== 'held') {
                $this->db->rollBack();
                return false;
            }

            $this->userModel->updateBalance($escrow['client_id'], (float) $escrow['amount']);

            $updatedClient = $this->userModel->find($escrow['client_id']);
            $this->transactionModel->log(
                $escrow['client_id'], 'escrow_refund', (float) $escrow['amount'], (float) $updatedClient['balance'],
                "Возврат за отменённый заказ #{$orderId}", $orderId
            );

            $this->escrowModel->update($escrow['id'], ['status' => 'refunded']);

            $this->notificationModel->notify(
                $escrow['client_id'], 'refund',
                "Возврат " . format_money((float) $escrow['amount']) . " за заказ #{$orderId}",
                "/orders/{$orderId}"
            );

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
