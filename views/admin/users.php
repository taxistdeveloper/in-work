<?php $roleLabels = ['client' => 'Заказчик', 'freelancer' => 'Исполнитель', 'admin' => 'Админ']; ?>

<!-- Фильтры -->
<div class="bg-white rounded-2xl border border-gray-100 p-4 mb-6">
    <form method="GET" action="<?= url('admin/users') ?>" class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
            <input type="text" name="search" value="<?= e($search) ?>" placeholder="Поиск по имени или email..."
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
        </div>
        <select name="role" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none bg-white">
            <option value="">Все роли</option>
            <option value="client" <?= $role === 'client' ? 'selected' : '' ?>>Заказчик</option>
            <option value="freelancer" <?= $role === 'freelancer' ? 'selected' : '' ?>>Исполнитель</option>
            <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Админ</option>
        </select>
        <button type="submit" class="px-6 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition">Найти</button>
    </form>
</div>

<div class="text-sm text-gray-500 mb-4">Всего: <?= $pagination['total'] ?></div>

<!-- Таблица -->
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs text-gray-500 uppercase tracking-wider bg-gray-50">
                    <th class="px-6 py-3 font-medium">ID</th>
                    <th class="px-6 py-3 font-medium">Пользователь</th>
                    <th class="px-6 py-3 font-medium">Роль</th>
                    <th class="px-6 py-3 font-medium text-right">Баланс</th>
                    <th class="px-6 py-3 font-medium text-right">Рейтинг</th>
                    <th class="px-6 py-3 font-medium text-right">Заказов</th>
                    <th class="px-6 py-3 font-medium text-right">Дата</th>
                    <th class="px-6 py-3 font-medium text-right">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($users as $u): ?>
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-3 text-sm text-gray-400">#<?= $u['id'] ?></td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center text-xs font-bold"><?= strtoupper(mb_substr($u['name'], 0, 2)) ?></div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900"><?= e($u['name']) ?></div>
                                    <div class="text-xs text-gray-400"><?= e($u['email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full <?php
                                echo match($u['role']) { 'admin' => 'bg-red-100 text-red-700', 'client' => 'bg-blue-100 text-blue-700', default => 'bg-emerald-100 text-emerald-700' };
                            ?>"><?= $roleLabels[$u['role']] ?? $u['role'] ?></span>
                        </td>
                        <td class="px-6 py-3 text-sm text-right font-medium"><?= format_money((float)$u['balance']) ?></td>
                        <td class="px-6 py-3 text-sm text-right"><?= number_format((float)$u['rating'], 1) ?></td>
                        <td class="px-6 py-3 text-sm text-right"><?= $u['completed_orders'] ?></td>
                        <td class="px-6 py-3 text-xs text-right text-gray-400"><?= time_ago($u['created_at']) ?></td>
                        <td class="px-6 py-3 text-right">
                            <a href="<?= url("admin/users/{$u['id']}") ?>" class="text-xs text-brand-600 hover:text-brand-700 font-medium">Редактировать</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= paginate_links($pagination['page'], $pagination['total_pages'], url('admin/users')) ?>
