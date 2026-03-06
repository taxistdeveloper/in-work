<?php $rank = get_rank($profile['completed_orders']); ?>

<div class="max-w-2xl">
    <a href="<?= url('admin/users') ?>" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Назад к списку
    </a>

    <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-14 h-14 bg-gradient-to-br from-brand-400 to-brand-600 rounded-2xl flex items-center justify-center text-white text-xl font-bold">
                <?= strtoupper(mb_substr($profile['name'], 0, 2)) ?>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900"><?= e($profile['name']) ?></h2>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <span>ID: #<?= $profile['id'] ?></span>
                    <span>·</span>
                    <span><?= render_stars((float)$profile['rating']) ?> <?= number_format((float)$profile['rating'], 1) ?></span>
                    <span>·</span>
                    <span class="px-2 py-0.5 text-xs rounded-full bg-<?= $rank['color'] ?>-100 text-<?= $rank['color'] ?>-700"><?= $rank['name'] ?></span>
                </div>
            </div>
        </div>

        <form method="POST" action="<?= url("admin/users/{$profile['id']}") ?>" class="space-y-5">
            <?= csrf_field() ?>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Имя</label>
                    <input type="text" name="name" value="<?= e($profile['name']) ?>" required
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="<?= e($profile['email']) ?>" required
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Роль</label>
                    <select name="role" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none bg-white">
                        <option value="client" <?= $profile['role'] === 'client' ? 'selected' : '' ?>>Заказчик</option>
                        <option value="freelancer" <?= $profile['role'] === 'freelancer' ? 'selected' : '' ?>>Исполнитель</option>
                        <option value="admin" <?= $profile['role'] === 'admin' ? 'selected' : '' ?>>Администратор</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Баланс (₸)</label>
                    <input type="number" name="balance" step="0.01" value="<?= $profile['balance'] ?>"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">О себе</label>
                <textarea name="bio" rows="3" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none resize-none"><?= e($profile['bio'] ?? '') ?></textarea>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <button type="submit" class="px-6 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-700 transition">
                    Сохранить
                </button>

                <?php if ((int)$profile['id'] !== user_id()): ?>
                    <a href="<?= url("admin/users/{$profile['id']}/delete") ?>"
                       onclick="return confirm('Удалить пользователя? Это необратимо!')"
                       class="px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 rounded-xl transition font-medium">
                        Удалить пользователя
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Инфо -->
    <div class="bg-gray-50 rounded-2xl p-5 text-sm text-gray-500 space-y-1">
        <div>Зарегистрирован: <?= $profile['created_at'] ?></div>
        <div>Выполнено заказов: <?= $profile['completed_orders'] ?></div>
        <div>Рейтинг: <?= number_format((float)$profile['rating'], 2) ?></div>
        <div>Ранг: <?= $rank['name'] ?> (уровень <?= $rank['level'] ?>)</div>
    </div>
</div>
