<?php $errors = $_SESSION['errors'] ?? []; unset($_SESSION['errors']); ?>
<?php $oldInput = $_SESSION['old_input'] ?? []; unset($_SESSION['old_input']); ?>

<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-gradient-to-br from-brand-500 to-brand-700 rounded-xl flex items-center justify-center mx-auto mb-4">
                <span class="text-white font-bold text-lg">in</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Создать аккаунт</h1>
            <p class="text-gray-500 mt-1">Присоединяйтесь к динамичной фриланс-платформе</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <form method="POST" action="<?= url('register') ?>" class="space-y-5">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Имя</label>
                    <input type="text" name="name" value="<?= e($oldInput['name'] ?? '') ?>" required
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                           placeholder="Арман Сериков">
                    <?php if (!empty($errors['name'])): ?>
                        <p class="text-red-500 text-xs mt-1"><?= e($errors['name']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="<?= e($oldInput['email'] ?? '') ?>" required
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                           placeholder="you@example.kz">
                    <?php if (!empty($errors['email'])): ?>
                        <p class="text-red-500 text-xs mt-1"><?= e($errors['email']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Я хочу</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="role" value="client" class="peer sr-only" <?= ($oldInput['role'] ?? '') === 'client' ? 'checked' : '' ?>>
                            <div class="border-2 border-gray-200 rounded-xl p-4 text-center peer-checked:border-brand-500 peer-checked:bg-brand-50 transition hover:border-gray-300">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-400 peer-checked:text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span class="text-sm font-medium text-gray-700">Нанять специалиста</span>
                                <span class="block text-xs text-gray-400 mt-0.5">Заказчик</span>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="role" value="freelancer" class="peer sr-only" <?= ($oldInput['role'] ?? 'freelancer') === 'freelancer' ? 'checked' : '' ?>>
                            <div class="border-2 border-gray-200 rounded-xl p-4 text-center peer-checked:border-brand-500 peer-checked:bg-brand-50 transition hover:border-gray-300">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-400 peer-checked:text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                <span class="text-sm font-medium text-gray-700">Найти работу</span>
                                <span class="block text-xs text-gray-400 mt-0.5">Исполнитель</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Пароль</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                           placeholder="Минимум 6 символов">
                    <?php if (!empty($errors['password'])): ?>
                        <p class="text-red-500 text-xs mt-1"><?= e($errors['password']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Подтвердите пароль</label>
                    <input type="password" name="password_confirm" required
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                           placeholder="Повторите пароль">
                </div>

                <button type="submit" class="w-full py-3 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition shadow-sm">
                    Создать аккаунт
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-500 mt-6">
            Уже есть аккаунт?
            <a href="<?= url('login') ?>" class="text-brand-600 font-medium hover:text-brand-700">Войти</a>
        </p>
    </div>
</div>
