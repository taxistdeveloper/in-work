<?php

namespace App\Models;

use Core\Model;

class Transaction extends Model
{
    protected string $table = 'transactions';

    public function getUserTransactions(int $userId, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, 'user_id = ?', [$userId], 'created_at DESC');
    }

    public function log(int $userId, string $type, float $amount, float $balanceAfter, string $description = '', ?int $referenceId = null): int
    {
        return $this->create([
            'user_id'      => $userId,
            'type'         => $type,
            'amount'       => $amount,
            'balance_after'=> $balanceAfter,
            'description'  => $description,
            'reference_id' => $referenceId,
        ]);
    }
}
