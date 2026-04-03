<?php

namespace App\Models;

use Core\Model;

class FreelancerCategory extends Model
{
    protected string $table = 'freelancer_categories';

    /** @param list<string> $slugs */
    public function syncForUser(int $userId, array $slugs): void
    {
        $this->db->query('DELETE FROM ' . $this->table . ' WHERE user_id = ?', [$userId]);
        foreach ($slugs as $slug) {
            $slug = trim((string) $slug);
            if ($slug === '' || ! is_valid_category_slug($slug)) {
                continue;
            }
            $this->db->query(
                'INSERT INTO ' . $this->table . ' (user_id, category) VALUES (?, ?)',
                [$userId, $slug]
            );
        }
    }

    /** @return list<string> */
    public function getSlugsForUser(int $userId): array
    {
        $rows = $this->db->fetchAll(
            'SELECT category FROM ' . $this->table . ' WHERE user_id = ? ORDER BY category',
            [$userId]
        );

        return array_column($rows, 'category');
    }

    public function userHasCategory(int $userId, string $category): bool
    {
        $r = $this->db->fetch(
            'SELECT 1 FROM ' . $this->table . ' WHERE user_id = ? AND category = ? LIMIT 1',
            [$userId, $category]
        );

        return $r !== null;
    }
}
