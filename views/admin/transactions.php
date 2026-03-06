<?php
$txTypeLabels = [
    'deposit' => 'Пополнение', 'withdrawal' => 'Вывод', 'escrow_hold' => 'Эскроу',
    'escrow_release' => 'Выплата', 'escrow_refund' => 'Возврат', 'earning' => 'Заработок', 'fee' => 'Комиссия',
];
?>

<div class="text-sm text-gray-500 mb-4">Всего: <?= $pagination['total'] ?></div>

<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs text-gray-500 uppercase tracking-wider bg-gray-50">
                    <th class="px-6 py-3 font-medium">ID</th>
                    <th class="px-6 py-3 font-medium">Пользователь</th>
                    <th class="px-6 py-3 font-medium">Тип</th>
                    <th class="px-6 py-3 font-medium">Описание</th>
                    <th class="px-6 py-3 font-medium text-right">Сумма</th>
                    <th class="px-6 py-3 font-medium text-right">Баланс после</th>
                    <th class="px-6 py-3 font-medium text-right">Дата</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($transactions as $tx): ?>
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-3 text-sm text-gray-400">#<?= $tx['id'] ?></td>
                        <td class="px-6 py-3">
                            <div class="text-sm font-medium text-gray-900"><?= e($tx['user_name']) ?></div>
                            <div class="text-xs text-gray-400"><?= e($tx['user_email']) ?></div>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full <?php
                                echo match($tx['type']) {
                                    'deposit' => 'bg-emerald-50 text-emerald-700', 'withdrawal' => 'bg-red-50 text-red-700',
                                    'earning' => 'bg-blue-50 text-blue-700', 'escrow_hold' => 'bg-amber-50 text-amber-700',
                                    'escrow_release' => 'bg-emerald-50 text-emerald-700', 'escrow_refund' => 'bg-purple-50 text-purple-700',
                                    default => 'bg-gray-50 text-gray-700',
                                };
                            ?>"><?= $txTypeLabels[$tx['type']] ?? $tx['type'] ?></span>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-600"><?= e($tx['description'] ?: '—') ?></td>
                        <td class="px-6 py-3 text-sm font-semibold text-right <?= (float)$tx['amount'] >= 0 ? 'text-emerald-600' : 'text-red-600' ?>">
                            <?= (float)$tx['amount'] >= 0 ? '+' : '' ?><?= format_money((float)$tx['amount']) ?>
                        </td>
                        <td class="px-6 py-3 text-sm text-right text-gray-900 font-medium"><?= format_money((float)$tx['balance_after']) ?></td>
                        <td class="px-6 py-3 text-xs text-right text-gray-400"><?= time_ago($tx['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= paginate_links($pagination['page'], $pagination['total_pages'], url('admin/transactions')) ?>
