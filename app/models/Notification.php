<?php

namespace App\Models;

use Core\Model;

class Notification extends Model
{
    protected string $table = 'notifications';

    public function getUserNotifications(int $userId, int $limit = 20): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?",
            [$userId, $limit]
        );
    }

    public function getUnreadCount(int $userId): int
    {
        $result = $this->db->fetch(
            "SELECT COUNT(*) as cnt FROM notifications WHERE user_id = ? AND is_read = 0",
            [$userId]
        );
        return (int) ($result['cnt'] ?? 0);
    }

    /** Unread rows only; does not mark as read (for polling / push). */
    public function getUnreadNotifications(int $userId, int $limit = 50): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY id DESC LIMIT ?",
            [$userId, $limit]
        );
    }

    public function markAllRead(int $userId): void
    {
        $this->db->query(
            "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0",
            [$userId]
        );
    }

    public function notify(int $userId, string $type, string $message, string $link = ''): int
    {
        return $this->create([
            'user_id' => $userId,
            'type'    => $type,
            'message' => $message,
            'link'    => $link,
        ]);
    }
}
