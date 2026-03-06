<?php

namespace App\Models;

use Core\Model;

class User extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }

    public function createUser(array $data): int
    {
        return $this->create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role'     => $data['role'],
        ]);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function updateBalance(int $userId, float $amount): void
    {
        $this->db->query(
            "UPDATE users SET balance = balance + ? WHERE id = ?",
            [$amount, $userId]
        );
    }

    public function updateRating(int $userId): void
    {
        $result = $this->db->fetch(
            "SELECT AVG(rating) as avg_rating FROM reviews WHERE reviewee_id = ?",
            [$userId]
        );

        $avg = round((float) ($result['avg_rating'] ?? 0), 2);
        $this->update($userId, ['rating' => $avg]);
    }

    public function incrementCompleted(int $userId): void
    {
        $this->db->query(
            "UPDATE users SET completed_orders = completed_orders + 1 WHERE id = ?",
            [$userId]
        );
    }

    public function getProfile(int $userId): ?array
    {
        $user = $this->find($userId);
        if (!$user) return null;

        $user['rank'] = get_rank($user['completed_orders']);
        return $user;
    }

    public function getSessionData(int $userId): ?array
    {
        $user = $this->find($userId);
        if (!$user) return null;

        return [
            'id'               => $user['id'],
            'name'             => $user['name'],
            'email'            => $user['email'],
            'role'             => $user['role'],
            'avatar'           => $user['avatar'],
            'balance'          => (float) $user['balance'],
            'rating'           => (float) $user['rating'],
            'completed_orders' => (int) $user['completed_orders'],
        ];
    }
}
