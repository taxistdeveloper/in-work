<?php $errors = $_SESSION['errors'] ?? []; unset($_SESSION['errors']); ?>
<?php $oldInput = $_SESSION['old_input'] ?? []; unset($_SESSION['old_input']); ?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Создать новый заказ</h1>
        <p class="text-gray-500 mt-1">Опишите проект и укажите бюджет. Фрилансеры предложат свою лучшую цену.</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8">
        <form method="POST" action="<?= url('orders/create') ?>" class="space-y-6">
            <?= csrf_field() ?>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Название проекта</label>
                <input type="text" name="title" value="<?= e($oldInput['title'] ?? '') ?>" required
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
                          placeholder="Подробно опишите проект — требования, ожидания, результат..."><?= e($oldInput['description'] ?? '') ?></textarea>
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
                            <option value="<?= e($key) ?>" <?= ($oldInput['category'] ?? '') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
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
                        <input type="number" name="budget" step="100" min="500" value="<?= e($oldInput['budget'] ?? '') ?>" required
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
                <input type="date" name="deadline" value="<?= e($oldInput['deadline'] ?? '') ?>" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
                <?php if (!empty($errors['deadline'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= e($errors['deadline']) ?></p>
                <?php endif; ?>
            </div>

            <div class="bg-amber-50 border border-amber-100 rounded-xl p-4">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <div>
                        <h4 class="text-sm font-medium text-amber-800">Динамическое ценообразование</h4>
                        <p class="text-sm text-amber-600 mt-0.5">Фрилансеры могут предложить свою цену — ваш бюджет, -10%, +10% или свою сумму. Вы выбираете лучшее предложение.</p>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full py-3 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition shadow-sm">
                Опубликовать заказ
            </button>
        </form>
    </div>
</div>
