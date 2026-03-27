<?php
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
$oldInput = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);

$titleVal = $oldInput['title'] ?? $order['title'];
$descVal = $oldInput['description'] ?? $order['description'];
$catVal = $oldInput['category'] ?? $order['category'];
$budgetVal = $oldInput['budget'] ?? $order['budget'];
$deadlineVal = $oldInput['deadline'] ?? $order['deadline'];
$minDeadline = date('Y-m-d', strtotime('+1 day'));
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Редактировать заказ</h1>
        <p class="text-gray-500 mt-1">Пока заказ открыт, вы можете изменить описание, бюджет и срок.</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8">
        <form method="POST" action="<?= url("orders/{$order['id']}/edit") ?>" class="space-y-6">
            <?= csrf_field() ?>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Название проекта</label>
                <input type="text" name="title" value="<?= e((string) $titleVal) ?>" required
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                       placeholder="Например: Создать адаптивный лендинг">
                <?php if (!empty($errors['title'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= e($errors['title']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Описание</label>
                <textarea name="description" rows="5" required
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition resize-none"
                          placeholder="Подробно опишите проект — требования, ожидания, результат..."><?= e((string) $descVal) ?></textarea>
                <?php if (!empty($errors['description'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= e($errors['description']) ?></p>
                <?php endif; ?>
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Категория</label>
                    <select name="category" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none bg-white">
                        <option value="">Выберите категорию</option>
                        <?php foreach ($categories as $key => $label): ?>
                            <option value="<?= e($key) ?>" <?= (string) $catVal === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['category'])): ?>
                        <p class="text-red-500 text-xs mt-1"><?= e($errors['category']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Бюджет (₸)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">₸</span>
                        <input type="number" name="budget" step="100" min="500" value="<?= e((string) $budgetVal) ?>" required
                               class="w-full pl-8 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                               placeholder="1000.00">
                    </div>
                    <?php if (!empty($errors['budget'])): ?>
                        <p class="text-red-500 text-xs mt-1"><?= e($errors['budget']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Дедлайн</label>
                <input type="date" name="deadline" value="<?= e((string) $deadlineVal) ?>" required min="<?= e($minDeadline) ?>"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
                <?php if (!empty($errors['deadline'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= e($errors['deadline']) ?></p>
                <?php endif; ?>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit" class="flex-1 py-3 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition shadow-sm">
                    Сохранить изменения
                </button>
                <a href="<?= url("orders/{$order['id']}") ?>" class="flex-1 py-3 text-center border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition">
                    Отмена
                </a>
            </div>
        </form>
    </div>
</div>
