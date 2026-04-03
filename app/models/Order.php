<?php

namespace App\Models;

use Core\Model;

class Order extends Model
{
    protected string $table = 'orders';

    public function getOpenOrders(int $page = 1, int $perPage = 20, string $category = '', string $search = ''): array
    {
        $where = "status = 'open'";
        $params = [];

        $catalogSlugs = catalog_category_slugs();
        if ($catalogSlugs !== []) {
            $placeholders = implode(', ', array_fill(0, count($catalogSlugs), '?'));
            $where .= " AND category NOT IN ({$placeholders})";
            foreach ($catalogSlugs as $slug) {
                $params[] = $slug;
            }
        }

        if ($category) {
            $where .= " AND category = ?";
            $params[] = $category;
        }
        if ($search) {
            $where .= " AND (title LIKE ? OR description LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        return $this->paginate($page, $perPage, $where, $params, 'created_at DESC');
    }

    public function getOrderWithClient(int $orderId): ?array
    {
        return $this->db->fetch(
            "SELECT o.*, u.name as client_name, u.rating as client_rating, u.completed_orders as client_completed
             FROM orders o
             JOIN users u ON o.client_id = u.id
             WHERE o.id = ?",
            [$orderId]
        );
    }

    public function getClientOrders(int $clientId, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, 'client_id = ?', [$clientId], 'created_at DESC');
    }

    public function getClientOpenOrders(int $clientId, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, "client_id = ? AND status = 'open'", [$clientId], 'created_at DESC');
    }

    public function getFreelancerOrders(int $freelancerId, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, 'freelancer_id = ?', [$freelancerId], 'created_at DESC');
    }

    public function getActiveOrders(int $userId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM orders WHERE (client_id = ? OR freelancer_id = ?) AND status = 'in_progress' ORDER BY updated_at DESC",
            [$userId, $userId]
        );
    }

    public function countByStatus(int $userId): array
    {
        $rows = $this->db->fetchAll(
            "SELECT status, COUNT(*) as cnt FROM orders WHERE client_id = ? OR freelancer_id = ? GROUP BY status",
            [$userId, $userId]
        );

        $counts = ['open' => 0, 'in_progress' => 0, 'completed' => 0, 'cancelled' => 0];
        foreach ($rows as $row) {
            $counts[$row['status']] = (int) $row['cnt'];
        }
        return $counts;
    }
}
