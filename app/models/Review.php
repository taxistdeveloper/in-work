<?php

namespace App\Models;

use Core\Model;

class Review extends Model
{
    protected string $table = 'reviews';

    public function getUserReviews(int $userId, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $total = $this->count('reviewee_id = ?', [$userId]);
        $totalPages = (int) ceil($total / $perPage);

        $items = $this->db->fetchAll(
            "SELECT r.*, u.name as reviewer_name, u.avatar as reviewer_avatar, o.title as order_title
             FROM reviews r
             JOIN users u ON r.reviewer_id = u.id
             JOIN orders o ON r.order_id = o.id
             WHERE r.reviewee_id = ?
             ORDER BY r.created_at DESC
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

    public function hasReviewed(int $orderId, int $reviewerId): bool
    {
        return $this->count('order_id = ? AND reviewer_id = ?', [$orderId, $reviewerId]) > 0;
    }
}
