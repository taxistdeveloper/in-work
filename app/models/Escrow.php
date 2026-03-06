<?php

namespace App\Models;

use Core\Model;

class Escrow extends Model
{
    protected string $table = 'escrow';

    public function findByOrder(int $orderId): ?array
    {
        return $this->findBy('order_id', $orderId);
    }

    public function getActiveEscrows(int $userId): array
    {
        return $this->db->fetchAll(
            "SELECT e.*, o.title FROM escrow e
             JOIN orders o ON e.order_id = o.id
             WHERE (e.client_id = ? OR e.freelancer_id = ?) AND e.status = 'held'",
            [$userId, $userId]
        );
    }
}
