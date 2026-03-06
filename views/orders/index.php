<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Лента заказов</h1>
            <p class="text-gray-500 mt-1"><?= $pagination['total'] ?? 0 ?> проектов доступно</p>
        </div>
        <?php if (is_logged_in() && user_role() === 'client'): ?>
            <a href="<?= url('orders/create') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 text-white text-sm font-medium rounded-xl hover:bg-brand-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Создать заказ
            </a>
        <?php endif; ?>
    </div>

    <!-- Фильтры -->
    <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-6">
        <form method="GET" action="<?= url('orders') ?>" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="<?= e($search) ?>" placeholder="Поиск заказов..."
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
            </div>
            <select name="category" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none bg-white">
                <option value="">Все категории</option>
                <?php foreach ($categories as $key => $label): ?>
                    <option value="<?= e($key) ?>" <?= $category === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="px-6 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition">
                Найти
            </button>
        </form>
    </div>

    <!-- Сетка заказов -->
    <?php if (empty($orders)): ?>
        <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Заказы не найдены</h3>
            <p class="text-gray-500">Попробуйте изменить поиск или фильтры.</p>
        </div>
    <?php else: ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php foreach ($orders as $order): ?>
                <a href="<?= url("orders/{$order['id']}") ?>" class="bg-white rounded-2xl p-6 border border-gray-100 hover:shadow-lg hover:border-brand-200 transition-all duration-300 group fade-in">
                    <div class="flex items-start justify-between mb-3">
                        <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full"><?= e($order['category']) ?></span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 text-emerald-700 text-xs font-medium rounded-full">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                            Открыт
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-brand-600 transition line-clamp-2"><?= e($order['title']) ?></h3>
                    <p class="text-sm text-gray-500 mb-4 line-clamp-2"><?= e(mb_substr($order['description'], 0, 100)) ?></p>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                        <span class="text-xl font-bold text-brand-600"><?= format_money((float)$order['budget']) ?></span>
                        <div class="text-right">
                            <div class="text-xs text-gray-400"><?= time_ago($order['created_at']) ?></div>
                            <div class="text-xs text-gray-500 mt-0.5">Срок: <?= e($order['deadline']) ?></div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <?= paginate_links($pagination['page'], $pagination['total_pages'], url('orders')) ?>
    <?php endif; ?>
</div>
