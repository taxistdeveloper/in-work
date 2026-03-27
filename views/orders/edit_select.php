<?php
$statusLabels = [
    'open'        => 'Открыт',
    'in_progress' => 'В работе',
    'completed'   => 'Завершён',
    'cancelled'   => 'Отменён',
];
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Редактировать заказ</h1>
            <p class="text-gray-500 mt-1">Изменить можно только открытые заказы (до выбора исполнителя).</p>
        </div>
        <a href="<?= url('orders/create') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 text-white text-sm font-medium rounded-xl hover:bg-brand-700 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Новый заказ
        </a>
    </div>

    <?php if (empty($orders)): ?>
        <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Нет открытых заказов</h3>
            <p class="text-gray-500 mb-4">Создайте заказ или откройте «Мои заказы», если заказ уже в работе.</p>
            <a href="<?= url('my-orders') ?>" class="inline-block px-6 py-2.5 border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition mr-2">
                Мои заказы
            </a>
            <a href="<?= url('orders/create') ?>" class="inline-block px-6 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-700 transition">
                Создать заказ
            </a>
        </div>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($orders as $order): ?>
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md hover:border-brand-200 transition-all">
                    <a href="<?= url("orders/{$order['id']}") ?>" class="flex-1 min-w-0 block">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="text-base font-semibold text-gray-900 truncate"><?= e($order['title']) ?></h3>
                            <span class="flex-shrink-0 inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-emerald-50 text-emerald-700">
                                <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                            </span>
                        </div>
                        <div class="flex items-center gap-3 text-xs text-gray-400">
                            <span><?= e($order['category']) ?></span>
                            <span>Срок: <?= e($order['deadline']) ?></span>
                            <span><?= format_money((float)($order['final_price'] ?? $order['budget'])) ?></span>
                        </div>
                    </a>
                    <a href="<?= url("orders/{$order['id']}/edit") ?>" class="inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Редактировать
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <?= paginate_links($pagination['page'], $pagination['total_pages'], url('orders/edit')) ?>
    <?php endif; ?>
</div>
