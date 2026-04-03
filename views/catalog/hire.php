<?php
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
$oldInput = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);
$minDate = date('Y-m-d', strtotime('+1 day'));
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <nav class="text-sm text-gray-500 mb-6">
        <a href="<?= url('catalog') ?>" class="hover:text-brand-600">Каталог</a>
        <span class="mx-1">/</span>
        <a href="<?= url('catalog/' . $category) ?>" class="hover:text-brand-600"><?= e($category_label) ?></a>
        <span class="mx-1">/</span>
        <span class="text-gray-900">Найм</span>
    </nav>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Оформить заказ</h1>
        <p class="text-gray-500 mt-1">Исполнитель: <strong class="text-gray-800"><?= e($freelancer['name'] ?? '') ?></strong> · <?= e($category_label) ?></p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8 mb-6">
        <div class="flex items-start gap-4 pb-6 mb-6 border-b border-gray-100">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white text-xl font-bold">
                <?= strtoupper(mb_substr($freelancer['name'] ?? '?', 0, 2)) ?>
            </div>
            <div>
                <p class="font-semibold text-gray-900"><?= e($freelancer['name'] ?? '') ?></p>
                <p class="text-sm text-gray-500 mt-1">
                    <?= render_stars((float) ($freelancer['rating'] ?? 0)) ?>
                    <?= number_format((float) ($freelancer['rating'] ?? 0), 1) ?>
                    · Завершено заказов: <?= (int) ($freelancer['completed_orders'] ?? 0) ?>
                </p>
                <?php if (!empty($freelancer['bio'])): ?>
                    <p class="text-sm text-gray-600 mt-2"><?= e($freelancer['bio']) ?></p>
                <?php endif; ?>
                <a href="<?= url('profile/' . (int) $freelancer_id) ?>" class="text-sm text-brand-600 font-medium mt-2 inline-block hover:underline">Полный профиль</a>
            </div>
        </div>

        <form method="POST" action="<?= url('catalog/hire') ?>" class="space-y-6">
            <?= csrf_field() ?>
            <input type="hidden" name="category" value="<?= e($category) ?>">
            <input type="hidden" name="freelancer_id" value="<?= (int) $freelancer_id ?>">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Название задачи</label>
                <input type="text" name="title" required minlength="5"
                       value="<?= e($oldInput['title'] ?? '') ?>"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none"
                       placeholder="Например: Замена проводки на кухне">
                <?php if (!empty($errors['title'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= e($errors['title']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Описание</label>
                <textarea name="description" rows="5" required minlength="20"
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none resize-none"
                          placeholder="Подробно опишите объём работ, адрес/доступ, материалы..."><?= e($oldInput['description'] ?? '') ?></textarea>
                <?php if (!empty($errors['description'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= e($errors['description']) ?></p>
                <?php endif; ?>
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Бюджет (₸)</label>
                    <p class="text-xs text-gray-500 mb-1">Минимум 100 ₸ — сумма резервируется на эскроу при оформлении.</p>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">₸</span>
                        <input type="number" name="budget" step="100" min="100" required
                               value="<?= e($oldInput['budget'] ?? '') ?>"
                               class="w-full pl-8 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    </div>
                    <?php if (!empty($errors['budget'])): ?>
                        <p class="text-red-500 text-xs mt-1"><?= e($errors['budget']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Дедлайн</label>
                    <input type="date" name="deadline" required min="<?= e($minDate) ?>"
                           value="<?= e($oldInput['deadline'] ?? '') ?>"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    <?php if (!empty($errors['deadline'])): ?>
                        <p class="text-red-500 text-xs mt-1"><?= e($errors['deadline']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-100 rounded-xl p-4">
                <p class="text-sm text-amber-900">Средства с баланса будут зарезервированы на эскроу до завершения заказа. Убедитесь, что на балансе достаточно средств.</p>
            </div>

            <?php if (!empty($errors['freelancer_id'])): ?>
                <p class="text-red-500 text-sm"><?= e($errors['freelancer_id']) ?></p>
            <?php endif; ?>
            <?php if (!empty($errors['category'])): ?>
                <p class="text-red-500 text-sm"><?= e($errors['category']) ?></p>
            <?php endif; ?>

            <button type="submit" class="w-full py-3 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition shadow-sm">
                Нанять и зарезервировать оплату
            </button>
        </form>
    </div>
</div>
