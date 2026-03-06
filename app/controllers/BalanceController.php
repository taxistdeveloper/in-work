<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Escrow;

class BalanceController extends Controller
{
    private User $userModel;
    private Transaction $transactionModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->transactionModel = new Transaction();
    }

    public function index(): void
    {
        $this->requireAuth();

        $page = max(1, (int) ($this->input('page', 1)));
        $transactions = $this->transactionModel->getUserTransactions(user_id(), $page, 20);

        $user = $this->userModel->find(user_id());
        $escrowModel = new Escrow();
        $activeEscrows = $escrowModel->getActiveEscrows(user_id());

        $held = 0;
        foreach ($activeEscrows as $e) {
            if ((int) $e['client_id'] === user_id()) {
                $held += (float) $e['amount'];
            }
        }

        $this->view('balance.index', [
            'title'        => 'Мой баланс',
            'balance'      => (float) $user['balance'],
            'held'         => $held,
            'transactions' => $transactions['items'],
            'pagination'   => $transactions,
        ]);
    }

    public function deposit(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            flash('error', 'Неверный токен безопасности.');
            $this->redirect(url('balance'));
            return;
        }

        $amount = (float) $this->input('amount', 0);

        if ($amount < 100) {
            flash('error', 'Минимальная сумма пополнения — 100 ₸');
            $this->redirect(url('balance'));
            return;
        }

        if ($amount > 500000) {
            flash('error', 'Максимальная сумма пополнения — 500 000 ₸');
            $this->redirect(url('balance'));
            return;
        }

        $this->userModel->updateBalance(user_id(), $amount);

        $updatedUser = $this->userModel->find(user_id());
        $this->transactionModel->log(
            user_id(), 'deposit', $amount, (float) $updatedUser['balance'],
            'Пополнение баланса'
        );

        $_SESSION['user'] = $this->userModel->getSessionData(user_id());

        flash('success', 'Баланс пополнен на ' . format_money($amount));
        $this->redirect(url('balance'));
    }

    public function withdraw(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            flash('error', 'Неверный токен безопасности.');
            $this->redirect(url('balance'));
            return;
        }

        $amount = (float) $this->input('amount', 0);
        $user = $this->userModel->find(user_id());

        if ($amount < 100) {
            flash('error', 'Минимальная сумма вывода — 100 ₸');
            $this->redirect(url('balance'));
            return;
        }

        if ($amount > (float) $user['balance']) {
            flash('error', 'Недостаточно средств.');
            $this->redirect(url('balance'));
            return;
        }

        $this->userModel->updateBalance(user_id(), -$amount);

        $updatedUser = $this->userModel->find(user_id());
        $this->transactionModel->log(
            user_id(), 'withdrawal', -$amount, (float) $updatedUser['balance'],
            'Вывод средств'
        );

        $_SESSION['user'] = $this->userModel->getSessionData(user_id());

        flash('success', 'Средства выведены: ' . format_money($amount));
        $this->redirect(url('balance'));
    }
}
