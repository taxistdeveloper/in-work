<?php
$statusLabels = ['open' => 'Открыт', 'in_progress' => 'В работе', 'completed' => 'Завершён', 'cancelled' => 'Отменён'];
$roleLabels = ['client' => 'Заказчик', 'freelancer' => 'Исполнитель', 'admin' => 'Админ'];
?>

<div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-gray-100 bg-white px-5 py-4">
    <div>
        <p class="text-sm font-medium text-gray-900">Категории платформы</p>
        <p class="text-xs text-gray-500 mt-0.5">Каталог (электрики, сантехника, ремонт) и рыночные направления из конфига</p>
    </div>
    <a href="<?= url('admin/categories') ?>" class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 transition">
        Смотреть категории
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
</div>

<!-- Статистика -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl p-5 border border-gray-100">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-gray-900"><?= $stats['users'] ?></div>
        <div class="text-sm text-gray-500">Пользователей</div>
        <div class="text-xs text-gray-400 mt-1"><?= $stats['clients'] ?> заказчиков / <?= $stats['freelancers'] ?> исполнителей</div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-gray-900"><?= $stats['orders_total'] ?></div>
        <div class="text-sm text-gray-500">Всего заказов</div>
        <div class="text-xs text-gray-400 mt-1"><?= $stats['orders_open'] ?> открытых / <?= $stats['orders_active'] ?> в работе</div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-gray-900"><?= format_money((float)$stats['total_volume']) ?></div>
        <div class="text-sm text-gray-500">Объём сделок</div>
        <div class="text-xs text-gray-400 mt-1"><?= $stats['orders_done'] ?> завершённых</div>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-gray-900"><?= format_money((float)$stats['fees_earned']) ?></div>
        <div class="text-sm text-gray-500">Комиссия платформы</div>
        <div class="text-xs text-gray-400 mt-1"><?= format_money((float)$stats['escrow_held']) ?> на эскроу</div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <!-- Последние пользователи -->
    <div class="bg-white rounded-2xl border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900">Новые пользователи</h2>
            <a href="<?= url('admin/users') ?>" class="text-sm text-brand-600 hover:text-brand-700 font-medium">Все</a>
        </div>
        <div class="divide-y divide-gray-50">
            <?php foreach ($recentUsers as $u): ?>
                <a href="<?= url("admin/users/{$u['id']}") ?>" class="flex items-center gap-3 px-6 py-3 hover:bg-gray-50 transition">
                    <div class="w-8 h-8 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center text-xs font-bold"><?= strtoupper(mb_substr($u['name'], 0, 2)) ?></div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-900 truncate"><?= e($u['name']) ?></div>
                        <div class="text-xs text-gray-400"><?= e($u['email']) ?></div>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full <?php
                        echo match($u['role']) { 'admin' => 'bg-red-100 text-red-700', 'client' => 'bg-blue-100 text-blue-700', default => 'bg-emerald-100 text-emerald-700' };
                    ?>"><?= $roleLabels[$u['role']] ?? $u['role'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Последние заказы -->
    <div class="bg-white rounded-2xl border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900">Последние заказы</h2>
            <a href="<?= url('admin/orders') ?>" class="text-sm text-brand-600 hover:text-brand-700 font-medium">Все</a>
        </div>
        <div class="divide-y divide-gray-50">
            <?php foreach ($recentOrders as $o): ?>
                <a href="<?= url("orders/{$o['id']}") ?>" class="flex items-center gap-3 px-6 py-3 hover:bg-gray-50 transition">
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-900 truncate"><?= e($o['title']) ?></div>
                        <div class="text-xs text-gray-400"><?= e($o['client_name']) ?> · <?= time_ago($o['created_at']) ?></div>
                    </div>
                    <span class="text-sm font-semibold text-brand-600"><?= format_money((float)$o['budget']) ?></span>
                    <span class="text-xs px-2 py-0.5 rounded-full <?php
                        echo match($o['status']) { 'open' => 'bg-emerald-50 text-emerald-700', 'in_progress' => 'bg-blue-50 text-blue-700', 'completed' => 'bg-gray-100 text-gray-600', default => 'bg-red-50 text-red-700' };
                    ?>"><?= $statusLabels[$o['status']] ?? $o['status'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
