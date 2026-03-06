<?php
$txTypeLabels = [
    'deposit'        => 'Пополнение',
    'withdrawal'     => 'Вывод',
    'escrow_hold'    => 'Эскроу',
    'escrow_release' => 'Выплата',
    'escrow_refund'  => 'Возврат',
    'earning'        => 'Заработок',
    'fee'            => 'Комиссия',
];
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-8">Мой баланс</h1>

    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <!-- Карточка баланса -->
        <div class="bg-gradient-to-br from-brand-600 to-brand-700 rounded-2xl p-6 text-white">
            <div class="text-sm text-brand-200 mb-1">Доступный баланс</div>
            <div class="text-3xl font-bold mb-4"><?= format_money($balance) ?></div>
            <?php if ($held > 0): ?>
                <div class="text-sm text-brand-200">
                    <span class="inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                        <?= format_money($held) ?> на эскроу
                    </span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Пополнение -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Пополнить баланс</h3>
            <form method="POST" action="<?= url('balance/deposit') ?>">
                <?= csrf_field() ?>
                <div class="relative mb-3">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">₸</span>
                    <input type="number" name="amount" step="1" min="100" max="500000" required
                           class="w-full pl-8 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                           placeholder="Сумма">
                </div>
                <div class="flex gap-2 mb-3">
                    <button type="button" onclick="this.form.amount.value='2000'" class="flex-1 py-1.5 text-xs font-medium border border-gray-200 rounded-lg hover:bg-gray-50 transition">2 000 ₸</button>
                    <button type="button" onclick="this.form.amount.value='5000'" class="flex-1 py-1.5 text-xs font-medium border border-gray-200 rounded-lg hover:bg-gray-50 transition">5 000 ₸</button>
                    <button type="button" onclick="this.form.amount.value='25000'" class="flex-1 py-1.5 text-xs font-medium border border-gray-200 rounded-lg hover:bg-gray-50 transition">25 000 ₸</button>
                </div>
                <button type="submit" class="w-full py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-700 transition">
                    Пополнить
                </button>
            </form>
        </div>

        <!-- Вывод -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Вывести средства</h3>
            <form method="POST" action="<?= url('balance/withdraw') ?>">
                <?= csrf_field() ?>
                <div class="relative mb-3">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">₸</span>
                    <input type="number" name="amount" step="1" min="100" max="<?= $balance ?>" required
                           class="w-full pl-8 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                           placeholder="Сумма">
                </div>
                <button type="submit" class="w-full py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-gray-800 transition">
                    Вывести
                </button>
            </form>
        </div>
    </div>

    <!-- История транзакций -->
    <div class="bg-white rounded-2xl border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">История транзакций</h2>
        </div>
        <?php if (empty($transactions)): ?>
            <div class="px-6 py-12 text-center">
                <p class="text-gray-400">Пока нет транзакций.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3 font-medium">Тип</th>
                            <th class="px-6 py-3 font-medium">Описание</th>
                            <th class="px-6 py-3 font-medium text-right">Сумма</th>
                            <th class="px-6 py-3 font-medium text-right">Баланс</th>
                            <th class="px-6 py-3 font-medium text-right">Дата</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach ($transactions as $tx): ?>
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full <?php
                                        echo match($tx['type']) {
                                            'deposit'        => 'bg-emerald-50 text-emerald-700',
                                            'withdrawal'     => 'bg-red-50 text-red-700',
                                            'earning'        => 'bg-blue-50 text-blue-700',
                                            'escrow_hold'    => 'bg-amber-50 text-amber-700',
                                            'escrow_release' => 'bg-emerald-50 text-emerald-700',
                                            'escrow_refund'  => 'bg-purple-50 text-purple-700',
                                            default          => 'bg-gray-50 text-gray-700',
                                        };
                                    ?>">
                                        <?= $txTypeLabels[$tx['type']] ?? $tx['type'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600"><?= e($tx['description'] ?: '—') ?></td>
                                <td class="px-6 py-4 text-sm font-semibold text-right <?= (float)$tx['amount'] >= 0 ? 'text-emerald-600' : 'text-red-600' ?>">
                                    <?= (float)$tx['amount'] >= 0 ? '+' : '' ?><?= format_money((float)$tx['amount']) ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right font-medium"><?= format_money((float)$tx['balance_after']) ?></td>
                                <td class="px-6 py-4 text-xs text-gray-400 text-right"><?= time_ago($tx['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="px-6 pb-4">
                <?= paginate_links($pagination['page'], $pagination['total_pages'], url('balance')) ?>
            </div>
        <?php endif; ?>
    </div>
</div>
