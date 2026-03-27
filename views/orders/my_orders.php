<?php
$user = current_user();
$statusLabels = [
    'open'        => 'Открыт',
    'in_progress' => 'В работе',
    'completed'   => 'Завершён',
    'cancelled'   => 'Отменён',
];
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Мои заказы</h1>
            <p class="text-gray-500 mt-1"><?= $pagination['total'] ?> всего заказов</p>
        </div>
        <?php if ($user['role'] === 'client'): ?>
            <a href="<?= url('orders/create') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 text-white text-sm font-medium rounded-xl hover:bg-brand-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Новый заказ
            </a>
        <?php endif; ?>
    </div>

    <?php if (empty($orders)): ?>
        <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Пока нет заказов</h3>
            <p class="text-gray-500 mb-4"><?= $user['role'] === 'client' ? 'Опубликуйте первый проект, чтобы начать.' : 'Просматривайте заказы и начните откликаться.' ?></p>
            <a href="<?= $user['role'] === 'client' ? url('orders/create') : url('orders') ?>"
               class="inline-block px-6 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-700 transition">
                <?= $user['role'] === 'client' ? 'Создать заказ' : 'Найти заказы' ?>
            </a>
        </div>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($orders as $order): ?>
                <div class="flex items-stretch gap-2 bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md hover:border-brand-200 transition-all">
                    <a href="<?= url("orders/{$order['id']}") ?>" class="flex-1 min-w-0 flex items-center gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="text-base font-semibold text-gray-900 truncate"><?= e($order['title']) ?></h3>
                                <span class="flex-shrink-0 inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full <?php
                                    echo match($order['status']) {
                                        'open'        => 'bg-emerald-50 text-emerald-700',
                                        'in_progress' => 'bg-blue-50 text-blue-700',
                                        'completed'   => 'bg-gray-100 text-gray-700',
                                        'cancelled'   => 'bg-red-50 text-red-700',
                                        default       => 'bg-gray-100 text-gray-700',
                                    };
                                ?>">
                                    <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                                </span>
                            </div>
                            <div class="flex items-center gap-3 text-xs text-gray-400">
                                <span><?= e($order['category']) ?></span>
                                <span>Срок: <?= e($order['deadline']) ?></span>
                                <span><?= time_ago($order['created_at']) ?></span>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <div class="text-lg font-bold text-brand-600"><?= format_money((float)($order['final_price'] ?? $order['budget'])) ?></div>
                        </div>
                    </a>
                    <?php if ($user['role'] === 'client' && $order['status'] === 'open'): ?>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 flex-shrink-0 self-center">
                            <a href="<?= url("orders/{$order['id']}/edit") ?>" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-xl hover:bg-gray-800 transition whitespace-nowrap">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Редактировать
                            </a>
                            <form method="POST" action="<?= url("orders/{$order['id']}/delete") ?>"
                                  onsubmit="return confirm('Удалить этот заказ безвозвратно? Отклики также будут удалены.');">
                                <?= csrf_field() ?>
                                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-red-600 border border-red-200 rounded-xl hover:bg-red-50 transition whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Удалить
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?= paginate_links($pagination['page'], $pagination['total_pages'], url('my-orders')) ?>
    <?php endif; ?>
</div>
