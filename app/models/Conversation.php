<?php

namespace App\Models;

use Core\Model;

class Conversation extends Model
{
    protected string $table = 'conversations';

    public function getUserConversations(int $userId): array
    {
        return $this->db->fetchAll(
            "SELECT c.*,
                    CASE WHEN c.user1_id = ? THEN u2.name ELSE u1.name END as partner_name,
                    CASE WHEN c.user1_id = ? THEN u2.id ELSE u1.id END as partner_id,
                    CASE WHEN c.user1_id = ? THEN u2.avatar ELSE u1.avatar END as partner_avatar,
                    o.title as order_title,
                    (SELECT body FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message,
                    (SELECT COUNT(*) FROM messages WHERE conversation_id = c.id AND sender_id != ? AND is_read = 0) as unread_count
             FROM conversations c
             JOIN users u1 ON c.user1_id = u1.id
             JOIN users u2 ON c.user2_id = u2.id
             LEFT JOIN orders o ON c.order_id = o.id
             WHERE c.user1_id = ? OR c.user2_id = ?
             ORDER BY c.last_message_at DESC",
            [$userId, $userId, $userId, $userId, $userId, $userId]
        );
    }

    public function findOrCreate(int $user1Id, int $user2Id, ?int $orderId = null): int
    {
        $minId = min($user1Id, $user2Id);
        $maxId = max($user1Id, $user2Id);

        $existing = $this->db->fetch(
            "SELECT id FROM conversations WHERE user1_id = ? AND user2_id = ? AND (order_id = ? OR (order_id IS NULL AND ? IS NULL))",
            [$minId, $maxId, $orderId, $orderId]
        );

        if ($existing) {
            return (int) $existing['id'];
        }

        return $this->create([
            'user1_id' => $minId,
            'user2_id' => $maxId,
            'order_id' => $orderId,
        ]);
    }

    public function getWithPartner(int $conversationId, int $userId): ?array
    {
        return $this->db->fetch(
            "SELECT c.*,
                    CASE WHEN c.user1_id = ? THEN u2.name ELSE u1.name END as partner_name,
                    CASE WHEN c.user1_id = ? THEN u2.id ELSE u1.id END as partner_id,
                    o.title as order_title
             FROM conversations c
             JOIN users u1 ON c.user1_id = u1.id
             JOIN users u2 ON c.user2_id = u2.id
             LEFT JOIN orders o ON c.order_id = o.id
             WHERE c.id = ? AND (c.user1_id = ? OR c.user2_id = ?)",
            [$userId, $userId, $conversationId, $userId, $userId]
        );
    }
}
