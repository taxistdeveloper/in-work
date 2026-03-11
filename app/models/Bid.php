<?php

namespace App\Models;

use Core\Model;

class Bid extends Model
{
    protected string $table = 'bids';

    public function getOrderBids(int $orderId): array
    {
        return $this->db->fetchAll(
            "SELECT b.*, u.name, u.rating, u.completed_orders, u.avatar
             FROM bids b
             JOIN users u ON b.freelancer_id = u.id
             WHERE b.order_id = ?
             ORDER BY
                FIELD(
                    CASE
                        WHEN u.completed_orders >= 200 THEN 4
                        WHEN u.completed_orders >= 50 THEN 3
                        WHEN u.completed_orders >= 10 THEN 2
                        ELSE 1
                    END, 4, 3, 2, 1
                ),
                u.rating DESC,
                b.amount ASC",
            [$orderId]
        );
    }

    public function hasUserBid(int $orderId, int $freelancerId): bool
    {
        $result = $this->db->fetch(
            "SELECT COUNT(*) as cnt FROM bids WHERE order_id = ? AND freelancer_id = ?",
            [$orderId, $freelancerId]
        );
        return ($result['cnt'] ?? 0) > 0;
    }

    /** @return array|null Bid row with status (pending/accepted/rejected) or null */
    public function getBidByUserForOrder(int $orderId, int $freelancerId): ?array
    {
        $row = $this->db->fetch(
            "SELECT * FROM bids WHERE order_id = ? AND freelancer_id = ?",
            [$orderId, $freelancerId]
        );
        return $row ?: null;
    }

    public function getUserBidsWithOrders(int $userId, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;

        $total = $this->count('freelancer_id = ?', [$userId]);
        $totalPages = (int) ceil($total / $perPage);

        $items = $this->db->fetchAll(
            "SELECT b.*, o.title, o.budget, o.status as order_status
             FROM bids b
             JOIN orders o ON b.order_id = o.id
             WHERE b.freelancer_id = ?
             ORDER BY b.created_at DESC
             LIMIT ? OFFSET ?",
            [$userId, $perPage, $offset]
        );

        return [
            'items'       => $items,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => $totalPages,
        ];
    }
}
