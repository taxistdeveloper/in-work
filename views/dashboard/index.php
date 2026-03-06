<?php $user = current_user(); $rank = get_rank($user['completed_orders']); ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Заголовок -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">С возвращением, <?= e($user['name']) ?>!</h1>
        <p class="text-gray-500 mt-1">Вот что происходит с вашим аккаунтом.</p>
    </div>

    <!-- Карточки статистики -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl p-5 border border-gray-100">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900"><?= $stats['in_progress'] ?? 0 ?></div>
            <div class="text-sm text-gray-500">В работе</div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900"><?= $stats['completed'] ?? 0 ?></div>
            <div class="text-sm text-gray-500">Выполнено</div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900"><?= number_format($user['rating'], 1) ?></div>
            <div class="text-sm text-gray-500">Рейтинг</div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900"><?= e($rank['name']) ?></div>
            <div class="text-sm text-gray-500">Ранг</div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Активные заказы -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Активные заказы</h2>
                    <a href="<?= url('my-orders') ?>" class="text-sm text-brand-600 hover:text-brand-700 font-medium">Все заказы</a>
                </div>
                <div class="divide-y divide-gray-50">
                    <?php if (empty($activeOrders)): ?>
                        <div class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <p class="text-gray-400">Нет активных заказов</p>
                            <?php if ($user['role'] === 'client'): ?>
                                <a href="<?= url('orders/create') ?>" class="inline-block mt-3 text-sm text-brand-600 font-medium">Создать первый заказ</a>
                            <?php else: ?>
                                <a href="<?= url('orders') ?>" class="inline-block mt-3 text-sm text-brand-600 font-medium">Найти заказы</a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <?php foreach ($activeOrders as $order): ?>
                            <a href="<?= url("orders/{$order['id']}") ?>" class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 transition">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-gray-900 truncate"><?= e($order['title']) ?></h3>
                                    <p class="text-xs text-gray-500 mt-0.5">Дедлайн: <?= e($order['deadline']) ?></p>
                                </div>
                                <span class="text-sm font-semibold text-brand-600"><?= format_money((float)($order['final_price'] ?? $order['budget'])) ?></span>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($user['role'] === 'freelancer' && !empty($myBids)): ?>
            <div class="bg-white rounded-2xl border border-gray-100 mt-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Последние отклики</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    <?php foreach ($myBids as $bid): ?>
                        <a href="<?= url("orders/{$bid['order_id']}") ?>" class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 transition">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-medium text-gray-900 truncate"><?= e($bid['title']) ?></h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs px-2 py-0.5 rounded-full <?php
                                        echo match($bid['status']) {
                                            'accepted' => 'bg-emerald-100 text-emerald-700',
                                            'rejected' => 'bg-red-100 text-red-700',
                                            default    => 'bg-amber-100 text-amber-700',
                                        };
                                    ?>"><?php
                                        echo match($bid['status']) {
                                            'accepted' => 'Принят',
                                            'rejected' => 'Отклонён',
                                            default    => 'Ожидание',
                                        };
                                    ?></span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-900"><?= format_money((float)$bid['amount']) ?></div>
                                <div class="text-xs text-gray-400">Бюджет: <?= format_money((float)$bid['budget']) ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Боковая панель -->
        <div class="space-y-6">
            <!-- Быстрые действия -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Быстрые действия</h2>
                <div class="space-y-2">
                    <?php if ($user['role'] === 'client'): ?>
                        <a href="<?= url('orders/create') ?>" class="flex items-center gap-3 w-full px-4 py-3 bg-brand-50 text-brand-700 rounded-xl hover:bg-brand-100 transition text-sm font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Создать заказ
                        </a>
                    <?php else: ?>
                        <a href="<?= url('orders') ?>" class="flex items-center gap-3 w-full px-4 py-3 bg-brand-50 text-brand-700 rounded-xl hover:bg-brand-100 transition text-sm font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Найти заказы
                        </a>
                    <?php endif; ?>
                    <a href="<?= url('balance') ?>" class="flex items-center gap-3 w-full px-4 py-3 bg-gray-50 text-gray-700 rounded-xl hover:bg-gray-100 transition text-sm font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Управление балансом
                    </a>
                    <a href="<?= url('chat') ?>" class="flex items-center gap-3 w-full px-4 py-3 bg-gray-50 text-gray-700 rounded-xl hover:bg-gray-100 transition text-sm font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        Сообщения
                        <?php if ($unreadMessages > 0): ?>
                            <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full"><?= $unreadMessages ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>

            <!-- Карточка профиля -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center text-lg font-bold">
                        <?= strtoupper(mb_substr($user['name'], 0, 2)) ?>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900"><?= e($user['name']) ?></h3>
                        <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-<?= $rank['color'] ?>-100 text-<?= $rank['color'] ?>-700"><?= e($rank['name']) ?></span>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Рейтинг</span>
                        <span class="font-medium"><?= render_stars($user['rating']) ?> <?= number_format($user['rating'], 1) ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Выполнено</span>
                        <span class="font-medium"><?= $user['completed_orders'] ?> заказов</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Баланс</span>
                        <span class="font-semibold text-brand-600"><?= format_money($user['balance']) ?></span>
                    </div>
                </div>
                <a href="<?= url("profile/{$user['id']}") ?>" class="block w-full mt-4 text-center text-sm text-brand-600 font-medium py-2 border border-brand-200 rounded-xl hover:bg-brand-50 transition">Смотреть профиль</a>
            </div>
        </div>
    </div>
</div>
