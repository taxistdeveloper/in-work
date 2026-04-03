<?php

namespace App\Services;

use Core\Database;

class SpecialistListingService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * @return array{items: list<array<string,mixed>>, total: int, page: int, per_page: int, total_pages: int}
     */
    public function listByCategory(string $category, string $sort, int $page, int $perPage): array
    {
        $sort = in_array($sort, ['rating', 'reviews', 'experience', 'completed', 'reliability', 'score'], true)
            ? $sort
            : 'score';

        $offset = ($page - 1) * $perPage;

        $totalRow = $this->db->fetch(
            'SELECT COUNT(DISTINCT u.id) AS cnt
             FROM users u
             INNER JOIN freelancer_categories fc ON fc.user_id = u.id AND fc.category = ?
             WHERE u.role = ?',
            [$category, 'freelancer']
        );
        $total = (int) ($totalRow['cnt'] ?? 0);
        $totalPages = (int) max(1, ceil($total / $perPage));

        $orderSql = match ($sort) {
            'rating' => 's.rating DESC, s.id DESC',
            'reviews' => 's.review_count DESC, s.rating DESC, s.id DESC',
            'experience' => 's.tenure_days DESC, s.id DESC',
            'completed' => 's.completed_orders DESC, s.id DESC',
            'reliability' => 's.cancelled_count ASC, s.completed_orders DESC, s.id DESC',
            default => 'platform_score DESC, s.id DESC',
        };

        $sql = "SELECT s.*,
            (s.rating * 14.0
                + LEAST(s.review_count, 100) * 0.22
                + LEAST(s.completed_orders, 150) * 0.18
                + LEAST(s.tenure_days, 4000) / 4000.0 * 18.0
                + GREATEST(0.0, 25.0 - s.cancelled_count * 4.0)
            ) AS platform_score
            FROM (
                SELECT u.id, u.name, u.avatar, u.bio, u.rating, u.completed_orders, u.created_at,
                    (SELECT COUNT(*) FROM reviews r WHERE r.reviewee_id = u.id) AS review_count,
                    (SELECT COUNT(*) FROM orders o WHERE o.freelancer_id = u.id AND o.status = 'cancelled') AS cancelled_count,
                    DATEDIFF(NOW(), u.created_at) AS tenure_days
                FROM users u
                INNER JOIN freelancer_categories fc ON fc.user_id = u.id AND fc.category = ?
                WHERE u.role = 'freelancer'
            ) s
            ORDER BY {$orderSql}
            LIMIT ? OFFSET ?";

        $rows = $this->db->fetchAll($sql, [$category, $perPage, $offset]);

        $items = [];
        foreach ($rows as $row) {
            $score = (float) ($row['platform_score'] ?? 0);
            $items[] = [
                'id'                => (int) $row['id'],
                'name'              => $row['name'],
                'avatar'            => $row['avatar'],
                'bio'               => $row['bio'],
                'rating'            => round((float) $row['rating'], 2),
                'review_count'      => (int) $row['review_count'],
                'completed_orders'  => (int) $row['completed_orders'],
                'tenure_days'       => (int) $row['tenure_days'],
                'cancelled_count'   => (int) $row['cancelled_count'],
                'platform_score'    => round($score, 2),
                'member_since'      => $row['created_at'],
            ];
        }

        return [
            'items'       => $items,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => $totalPages,
        ];
    }
}
