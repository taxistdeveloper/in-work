<?php $errors = $_SESSION['errors'] ?? []; unset($_SESSION['errors']); ?>
<?php $oldInput = $_SESSION['old_input'] ?? []; unset($_SESSION['old_input']); ?>

<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-gradient-to-br from-brand-500 to-brand-700 rounded-xl flex items-center justify-center mx-auto mb-4">
                <span class="text-white font-bold text-lg">in</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Добро пожаловать</h1>
            <p class="text-gray-500 mt-1">Войдите в свой аккаунт inWork</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <form method="POST" action="<?= url('login') ?>" class="space-y-5">
                <?= csrf_field() ?>

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
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Пароль</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                           placeholder="Введите пароль">
                    <?php if (!empty($errors['password'])): ?>
                        <p class="text-red-500 text-xs mt-1"><?= e($errors['password']) ?></p>
                    <?php endif; ?>
                </div>

                <button type="submit" class="w-full py-3 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition shadow-sm">
                    Войти
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-500 mt-6">
            Нет аккаунта?
            <a href="<?= url('register') ?>" class="text-brand-600 font-medium hover:text-brand-700">Зарегистрироваться</a>
        </p>
    </div>
</div>
