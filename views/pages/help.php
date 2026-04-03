<?php
$staticHeroTitle = 'Центр помощи';
$staticHeroSubtitle = 'Ответы на частые вопросы о заказах, оплате и безопасности на inWork.';
$staticHeroIcon = 'help';
require __DIR__ . '/../partials/static_page_hero.php';
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 -mt-14 md:-mt-16 relative z-10 pb-16 md:pb-20 static-page-content">
    <div class="space-y-4 md:space-y-5">

        <article class="help-card bg-white rounded-2xl border border-gray-100 p-6 md:p-7 shadow-sm">
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-gradient-to-br from-brand-100 to-emerald-50 flex items-center justify-center text-brand-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Как создать заказ?</h2>
                    <ol class="space-y-3">
                        <?php foreach (['Перейдите в личный кабинет', 'Нажмите «Создать заказ»', 'Заполните описание, цену и срок', 'Опубликуйте заказ'] as $i => $step): ?>
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-brand-500 text-white text-sm font-bold flex items-center justify-center"><?= $i + 1 ?></span>
                            <span class="text-gray-600 leading-relaxed pt-0.5"><?= e($step) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>
        </article>

        <article class="help-card bg-white rounded-2xl border border-gray-100 p-6 md:p-7 shadow-sm">
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-gradient-to-br from-brand-100 to-emerald-50 flex items-center justify-center text-brand-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h2 class="text-lg font-bold text-gray-900 mb-2">Как выбрать исполнителя?</h2>
                    <p class="text-gray-500 text-sm mb-3">Вы можете:</p>
                    <ul class="space-y-2.5">
                        <?php foreach (['Выбрать исполнителя из списка специалистов', 'Или дождаться откликов на заказ'] as $line): ?>
                        <li class="flex gap-3 text-gray-600 leading-relaxed">
                            <span class="mt-2 flex-shrink-0 w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                            <span><?= e($line) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </article>

        <article class="help-card bg-white rounded-2xl border border-gray-100 p-6 md:p-7 shadow-sm">
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-gradient-to-br from-brand-100 to-emerald-50 flex items-center justify-center text-brand-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Как работает оплата?</h2>
                    <ul class="space-y-2.5">
                        <?php foreach (['Средства резервируются на платформе', 'После выполнения работы переводятся исполнителю'] as $line): ?>
                        <li class="flex gap-3 text-gray-600 leading-relaxed">
                            <span class="mt-2 flex-shrink-0 w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                            <span><?= e($line) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </article>

        <article class="help-card bg-white rounded-2xl border border-gray-100 p-6 md:p-7 shadow-sm">
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-gradient-to-br from-brand-100 to-emerald-50 flex items-center justify-center text-brand-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h2 class="text-lg font-bold text-gray-900 mb-2">Как оставить отзыв?</h2>
                    <p class="text-gray-500 text-sm mb-3">После завершения заказа:</p>
                    <ul class="space-y-2.5">
                        <?php foreach (['Перейдите в раздел «Мои сделки»', 'Выберите заказ', 'Нажмите «Оставить отзыв»'] as $line): ?>
                        <li class="flex gap-3 text-gray-600 leading-relaxed">
                            <span class="mt-2 flex-shrink-0 w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                            <span><?= e($line) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </article>

        <article class="help-card bg-white rounded-2xl border border-gray-100 p-6 md:p-7 shadow-sm">
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-gradient-to-br from-brand-100 to-emerald-50 flex items-center justify-center text-brand-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Что делать при споре?</h2>
                    <ul class="space-y-2.5">
                        <?php foreach (['Свяжитесь с поддержкой', 'Опишите проблему', 'Предоставьте доказательства'] as $line): ?>
                        <li class="flex gap-3 text-gray-600 leading-relaxed">
                            <span class="mt-2 flex-shrink-0 w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                            <span><?= e($line) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </article>

        <article class="help-card bg-white rounded-2xl border border-gray-100 p-6 md:p-7 shadow-sm">
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-gradient-to-br from-brand-100 to-emerald-50 flex items-center justify-center text-brand-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Как пополнить баланс?</h2>
                    <ul class="space-y-2.5">
                        <?php foreach (['Перейдите в раздел «Баланс»', 'Выберите сумму', 'Оплатите удобным способом'] as $line): ?>
                        <li class="flex gap-3 text-gray-600 leading-relaxed">
                            <span class="mt-2 flex-shrink-0 w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                            <span><?= e($line) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </article>

        <article class="help-card bg-white rounded-2xl border border-gray-100 p-6 md:p-7 shadow-sm">
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-gradient-to-br from-brand-100 to-emerald-50 flex items-center justify-center text-brand-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Как стать исполнителем?</h2>
                    <ul class="space-y-2.5">
                        <?php foreach (['Зарегистрируйтесь', 'Заполните профиль', 'Укажите навыки', 'Начните откликаться на заказы'] as $line): ?>
                        <li class="flex gap-3 text-gray-600 leading-relaxed">
                            <span class="mt-2 flex-shrink-0 w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                            <span><?= e($line) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </article>

        <article class="help-card bg-white rounded-2xl border border-gray-100 p-6 md:p-7 shadow-sm border-amber-100/80 bg-gradient-to-br from-white to-amber-50/40">
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center text-amber-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h2 class="text-lg font-bold text-gray-900 mb-2">Безопасность</h2>
                    <p class="text-amber-900/80 text-sm font-medium mb-3">Никогда:</p>
                    <ul class="space-y-2.5">
                        <?php foreach (['Не переводите деньги вне платформы', 'Не передавайте личные данные'] as $line): ?>
                        <li class="flex gap-3 text-gray-700 leading-relaxed">
                            <span class="mt-2 flex-shrink-0 w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            <span><?= e($line) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </article>

        <div class="rounded-2xl bg-gradient-to-br from-brand-600 to-brand-800 p-6 md:p-8 text-white shadow-xl shadow-brand-900/20">
            <div class="flex flex-col sm:flex-row sm:items-center gap-6">
                <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-white/15 flex items-center justify-center">
                    <svg class="w-8 h-8 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold mb-2">Поддержка</h2>
                    <p class="text-brand-100 text-sm mb-1">Напишите нам — ответим в рабочее время</p>
                    <a href="mailto:support@in-work.kz" class="inline-flex items-center gap-2 text-lg font-semibold text-white border-b-2 border-white/30 hover:border-white transition pb-0.5">
                        support@in-work.kz
                    </a>
                </div>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-4 pt-2">
            <a href="<?= url('privacy') ?>" class="group flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm hover:border-brand-200 hover:shadow-md transition">
                <span class="flex-shrink-0 w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center group-hover:bg-brand-100 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </span>
                <span class="min-w-0 flex-1">
                    <span class="block font-semibold text-gray-900 group-hover:text-brand-700 transition">Политика конфиденциальности</span>
                    <span class="block text-sm text-gray-500 mt-0.5">Как мы обрабатываем данные</span>
                </span>
                <svg class="w-5 h-5 text-gray-300 group-hover:text-brand-500 flex-shrink-0 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <a href="<?= url('terms') ?>" class="group flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm hover:border-brand-200 hover:shadow-md transition">
                <span class="flex-shrink-0 w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center group-hover:bg-brand-100 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
                <span class="min-w-0 flex-1">
                    <span class="block font-semibold text-gray-900 group-hover:text-brand-700 transition">Условия использования</span>
                    <span class="block text-sm text-gray-500 mt-0.5">Правила платформы</span>
                </span>
                <svg class="w-5 h-5 text-gray-300 group-hover:text-brand-500 flex-shrink-0 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</div>
