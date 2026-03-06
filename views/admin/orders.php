<?php $statusLabels = ['open' => 'Открыт', 'in_progress' => 'В работе', 'completed' => 'Завершён', 'cancelled' => 'Отменён']; ?>

<!-- Фильтры -->
<div class="bg-white rounded-2xl border border-gray-100 p-4 mb-6">
    <form method="GET" action="<?= url('admin/orders') ?>" class="flex flex-col sm:flex-row gap-3">
        <select name="status" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none bg-white flex-1">
            <option value="">Все статусы</option>
            <option value="open" <?= $status === 'open' ? 'selected' : '' ?>>Открыт</option>
            <option value="in_progress" <?= $status === 'in_progress' ? 'selected' : '' ?>>В работе</option>
            <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Завершён</option>
            <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Отменён</option>
        </select>
        <button type="submit" class="px-6 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition">Фильтр</button>
    </form>
</div>

<div class="text-sm text-gray-500 mb-4">Всего: <?= $pagination['total'] ?></div>

<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs text-gray-500 uppercase tracking-wider bg-gray-50">
                    <th class="px-6 py-3 font-medium">ID</th>
                    <th class="px-6 py-3 font-medium">Заказ</th>
                    <th class="px-6 py-3 font-medium">Заказчик</th>
                    <th class="px-6 py-3 font-medium">Категория</th>
                    <th class="px-6 py-3 font-medium text-right">Бюджет</th>
                    <th class="px-6 py-3 font-medium">Статус</th>
                    <th class="px-6 py-3 font-medium text-right">Дата</th>
                    <th class="px-6 py-3 font-medium text-right">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($orders as $o): ?>
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-3 text-sm text-gray-400">#<?= $o['id'] ?></td>
                        <td class="px-6 py-3">
                            <a href="<?= url("orders/{$o['id']}") ?>" class="text-sm font-medium text-gray-900 hover:text-brand-600 truncate block max-w-[200px]"><?= e($o['title']) ?></a>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-600"><?= e($o['client_name']) ?></td>
                        <td class="px-6 py-3 text-xs text-gray-500"><?= e($o['category']) ?></td>
                        <td class="px-6 py-3 text-sm text-right font-semibold text-brand-600"><?= format_money((float)$o['budget']) ?></td>
                        <td class="px-6 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full <?php
                                echo match($o['status']) { 'open' => 'bg-emerald-50 text-emerald-700', 'in_progress' => 'bg-blue-50 text-blue-700', 'completed' => 'bg-gray-100 text-gray-600', default => 'bg-red-50 text-red-700' };
                            ?>"><?= $statusLabels[$o['status']] ?? $o['status'] ?></span>
                        </td>
                        <td class="px-6 py-3 text-xs text-right text-gray-400"><?= time_ago($o['created_at']) ?></td>
                        <td class="px-6 py-3 text-right">
                            <a href="<?= url("orders/{$o['id']}") ?>" class="text-xs text-brand-600 hover:text-brand-700 font-medium mr-2">Открыть</a>
                            <a href="<?= url("admin/orders/{$o['id']}/delete") ?>" onclick="return confirm('Удалить заказ #<?= $o['id'] ?>?')"
                               class="text-xs text-red-500 hover:text-red-700 font-medium">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= paginate_links($pagination['page'], $pagination['total_pages'], url('admin/orders')) ?>
