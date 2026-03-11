<nav class="bg-white border-b border-gray-100 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Лого -->
            <a href="<?= url('/') ?>" class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-br from-brand-500 to-brand-700 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-sm">in</span>
                </div>
                <span class="text-xl font-bold text-gray-900">in<span class="text-brand-600">Work</span></span>
            </a>

            <!-- Десктоп навигация -->
            <div class="hidden md:flex items-center gap-1">
                <a href="<?= url('orders') ?>" class="px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition">Лента заказов</a>
                <?php if (is_logged_in()): ?>
                    <?php if (user_role() === 'client'): ?>
                        <a href="<?= url('orders/create') ?>" class="px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition">Создать заказ</a>
                    <?php endif; ?>
                    <a href="<?= url('my-orders') ?>" class="px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition">Мои заказы</a>
                    <a href="<?= url('chat') ?>" class="px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition relative">
                        Сообщения
                        <span id="nav-unread-badge" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center"></span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Правая часть -->
            <div class="flex items-center gap-3">
                <?php if (is_logged_in()): ?>
                    <a href="<?= url('balance') ?>" class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <?= format_money(current_user()['balance'] ?? 0) ?>
                    </a>

                    <!-- Меню пользователя (работает без JS: details/summary) -->
                    <div class="relative" id="userMenu">
                        <details class="group/details">
                            <summary class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-gray-50 transition cursor-pointer list-none [&::-webkit-details-marker]:hidden">
                                <div class="w-8 h-8 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center text-sm font-bold">
                                    <?= strtoupper(substr(current_user()['name'] ?? 'U', 0, 2)) ?>
                                </div>
                                <span class="hidden sm:block text-sm font-medium text-gray-700"><?= e(current_user()['name'] ?? '') ?></span>
                                <svg class="w-4 h-4 text-gray-400 group-open/details:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </summary>
                            <div class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-[100]" role="menu">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-semibold text-gray-900"><?= e(current_user()['name'] ?? '') ?></p>
                                <p class="text-xs text-gray-500 mt-0.5"><?= e(current_user()['email'] ?? '') ?></p>
                                <span class="inline-block mt-1.5 px-2 py-0.5 text-xs font-medium rounded-full <?php
                                    echo match(user_role()) { 'admin' => 'bg-red-100 text-red-700', 'client' => 'bg-blue-100 text-blue-700', default => 'bg-emerald-100 text-emerald-700' };
                                ?>">
                                    <?php echo match(user_role()) { 'admin' => 'Администратор', 'client' => 'Заказчик', default => 'Исполнитель' }; ?>
                                </span>
                            </div>
                            <a href="<?= url('dashboard') ?>" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                Панель управления
                            </a>
                            <a href="<?= url('profile') ?>" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Профиль
                            </a>
                            <a href="<?= url('balance') ?>" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Баланс
                            </a>
                            <?php if (user_role() === 'admin'): ?>
                            <a href="<?= url('admin') ?>" class="flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Админ-панель
                            </a>
                            <?php endif; ?>
                            <div class="border-t border-gray-100 my-1"></div>
                            <a href="<?= url('logout') ?>" class="flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Выйти
                            </a>
                            </div>
                        </details>
                    </div>
                <?php else: ?>
                    <a href="<?= url('login') ?>" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition">Войти</a>
                    <a href="<?= url('register') ?>" class="px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition shadow-sm">Регистрация</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Нижняя навигация как в мобильном приложении -->
<?php if (is_logged_in()): ?>
<nav class="fixed inset-x-0 bottom-0 z-50 bg-white border-t border-gray-200 shadow-[0_-1px_8px_rgba(15,23,42,0.08)] md:hidden">
    <div class="max-w-md mx-auto px-2 flex justify-between items-center h-14 text-xs font-medium text-gray-500">
        <a href="<?= url('orders') ?>" class="flex flex-col items-center justify-center flex-1 h-full">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4-9 4-9-4z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10l9 4 9-4V7"/>
            </svg>
            <span>Лента</span>
        </a>
        <a href="<?= url('my-orders') ?>" class="flex flex-col items-center justify-center flex-1 h-full">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8M8 11h4m-7 8h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <span>Мои</span>
        </a>
        <a href="<?= url('chat') ?>" class="flex flex-col items-center justify-center flex-1 h-full relative">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h8M8 14h5m-9 1a9 9 0 1116 0l-2 3-2-1-2 1-2-1-2 1-2-3z"/>
            </svg>
            <span>Чат</span>
            <span id="nav-unread-badge-bottom" class="hidden absolute top-1.5 right-6 bg-red-500 text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center"></span>
        </a>
        <a href="<?= url('dashboard') ?>" class="flex flex-col items-center justify-center flex-1 h-full">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h4v12H4zM10 10h4v8h-4zM16 4h4v14h-4z"/>
            </svg>
            <span>Панель</span>
        </a>
        <a href="<?= url('profile') ?>" class="flex flex-col items-center justify-center flex-1 h-full">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM5 20a7 7 0 0114 0"/>
            </svg>
            <span>Профиль</span>
        </a>
    </div>
</nav>
<?php endif; ?>

