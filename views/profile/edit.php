<?php $errors = $_SESSION['errors'] ?? []; unset($_SESSION['errors']); ?>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Редактировать профиль</h1>

    <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8">
        <form method="POST" action="<?= url('profile') ?>" class="space-y-6">
            <?= csrf_field() ?>

            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 bg-gradient-to-br from-brand-400 to-brand-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold">
                    <?= strtoupper(mb_substr($profile['name'], 0, 2)) ?>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-900"><?= e($profile['name']) ?></h2>
                    <p class="text-sm text-gray-500"><?= e($profile['email']) ?></p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Имя</label>
                <input type="text" name="name" value="<?= e($profile['name']) ?>" required
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
                <?php if (!empty($errors['name'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= e($errors['name']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">О себе</label>
                <textarea name="bio" rows="4"
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition resize-none"
                          placeholder="Расскажите о себе..."><?= e($profile['bio'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="w-full py-3 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition">
                Сохранить изменения
            </button>
        </form>
    </div>
</div>
