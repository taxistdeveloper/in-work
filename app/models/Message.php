<?php

namespace App\Models;

use Core\Model;

class Message extends Model
{
    protected string $table = 'messages';

    public function getConversationMessages(int $conversationId, int $limit = 50, int $offset = 0): array
    {
        return $this->db->fetchAll(
            "SELECT m.*, u.name as sender_name, u.avatar as sender_avatar
             FROM messages m
             JOIN users u ON m.sender_id = u.id
             WHERE m.conversation_id = ?
             ORDER BY m.created_at ASC
             LIMIT ? OFFSET ?",
            [$conversationId, $limit, $offset]
        );
    }

    public function markAsRead(int $conversationId, int $userId): void
    {
        $this->db->query(
            "UPDATE messages SET is_read = 1 WHERE conversation_id = ? AND sender_id != ? AND is_read = 0",
            [$conversationId, $userId]
        );
    }

    public function getUnreadCount(int $userId): int
    {
        $result = $this->db->fetch(
            "SELECT COUNT(*) as cnt FROM messages m
             JOIN conversations c ON m.conversation_id = c.id
             WHERE (c.user1_id = ? OR c.user2_id = ?) AND m.sender_id != ? AND m.is_read = 0",
            [$userId, $userId, $userId]
        );
        return (int) ($result['cnt'] ?? 0);
    }
}
