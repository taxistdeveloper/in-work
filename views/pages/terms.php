<?php
$staticHeroTitle = 'Условия использования';
$staticHeroSubtitle = 'Правила работы с платформой inWork для заказчиков и исполнителей.';
$staticHeroIcon = 'doc';
require __DIR__ . '/../partials/static_page_hero.php';
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 -mt-14 md:-mt-16 relative z-10 pb-16 md:pb-20 static-page-content">
    <div class="rounded-2xl bg-gradient-to-br from-brand-50 to-emerald-50/80 border border-brand-100 p-6 md:p-8 mb-8 shadow-sm">
        <p class="text-gray-700 leading-relaxed text-[15px] md:text-base">
            Настоящие Условия регулируют использование платформы inWork.
        </p>
    </div>

    <div class="space-y-4 md:space-y-5">
        <section class="legal-card rounded-2xl bg-white border border-gray-100 p-6 md:p-7 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex gap-4 md:gap-5">
                <span class="flex-shrink-0 w-10 h-10 md:w-11 md:h-11 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white text-sm font-bold flex items-center justify-center shadow-md shadow-brand-500/25">1</span>
                <div class="min-w-0 flex-1 pt-0.5">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">Общие положения</h2>
                    <p class="text-gray-600 leading-relaxed mb-3">Платформа предоставляет сервис для взаимодействия заказчиков и исполнителей.</p>
                    <p class="text-gray-600 leading-relaxed">Платформа не является стороной сделки между пользователями.</p>
                </div>
            </div>
        </section>

        <section class="legal-card rounded-2xl bg-white border border-gray-100 p-6 md:p-7 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex gap-4 md:gap-5">
                <span class="flex-shrink-0 w-10 h-10 md:w-11 md:h-11 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white text-sm font-bold flex items-center justify-center shadow-md shadow-brand-500/25">2</span>
                <div class="min-w-0 flex-1 pt-0.5">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">Регистрация</h2>
                    <p class="text-gray-500 text-sm mb-3">Пользователь обязуется:</p>
                    <ul class="space-y-2">
                        <?php foreach (['Указывать достоверные данные', 'Не передавать аккаунт третьим лицам', 'Обеспечивать безопасность доступа'] as $line): ?>
                        <li class="flex gap-3 text-gray-600 leading-relaxed">
                            <svg class="w-5 h-5 flex-shrink-0 text-brand-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span><?= e($line) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </section>

        <section class="legal-card rounded-2xl bg-white border border-gray-100 p-6 md:p-7 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex gap-4 md:gap-5">
                <span class="flex-shrink-0 w-10 h-10 md:w-11 md:h-11 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white text-sm font-bold flex items-center justify-center shadow-md shadow-brand-500/25">3</span>
                <div class="min-w-0 flex-1 pt-0.5">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Роли пользователей</h2>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="rounded-xl bg-blue-50/80 border border-blue-100 p-4">
                            <p class="font-bold text-blue-900 mb-2 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span> Заказчик
                            </p>
                            <ul class="space-y-1.5 text-sm text-blue-900/80">
                                <li>• Создает задания</li>
                                <li>• Выбирает исполнителей</li>
                                <li>• Оплачивает услуги</li>
                            </ul>
                        </div>
                        <div class="rounded-xl bg-emerald-50/80 border border-emerald-100 p-4">
                            <p class="font-bold text-emerald-900 mb-2 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Исполнитель
                            </p>
                            <ul class="space-y-1.5 text-sm text-emerald-900/80">
                                <li>• Откликается на задания</li>
                                <li>• Выполняет работу</li>
                                <li>• Получает оплату</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="legal-card rounded-2xl bg-white border border-gray-100 p-6 md:p-7 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex gap-4 md:gap-5">
                <span class="flex-shrink-0 w-10 h-10 md:w-11 md:h-11 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white text-sm font-bold flex items-center justify-center shadow-md shadow-brand-500/25">4</span>
                <div class="min-w-0 flex-1 pt-0.5">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">Платежи</h2>
                    <p class="text-gray-500 text-sm mb-3">Платформа может использовать систему безопасных сделок (escrow):</p>
                    <ul class="space-y-2 mb-3">
                        <?php foreach (['Средства резервируются', 'Переводятся исполнителю после подтверждения'] as $line): ?>
                        <li class="flex gap-3 text-gray-600 leading-relaxed">
                            <svg class="w-5 h-5 flex-shrink-0 text-brand-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span><?= e($line) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="text-gray-600 leading-relaxed text-sm pl-8 border-l-2 border-brand-200">Платформа может удерживать комиссию.</p>
                </div>
            </div>
        </section>

        <section class="legal-card rounded-2xl bg-white border border-gray-100 p-6 md:p-7 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex gap-4 md:gap-5">
                <span class="flex-shrink-0 w-10 h-10 md:w-11 md:h-11 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white text-sm font-bold flex items-center justify-center shadow-md shadow-brand-500/25">5</span>
                <div class="min-w-0 flex-1 pt-0.5">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">Ответственность</h2>
                    <p class="text-gray-500 text-sm mb-3">Платформа:</p>
                    <ul class="space-y-2 mb-3">
                        <?php foreach (['Не гарантирует качество услуг', 'Не несет ответственности за действия пользователей'] as $line): ?>
                        <li class="flex gap-3 text-gray-600 leading-relaxed">
                            <svg class="w-5 h-5 flex-shrink-0 text-brand-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span><?= e($line) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="text-gray-600 leading-relaxed text-sm pl-8 border-l-2 border-brand-200">Пользователи несут ответственность за свои действия.</p>
                </div>
            </div>
        </section>

        <section class="legal-card rounded-2xl bg-white border border-gray-100 p-6 md:p-7 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex gap-4 md:gap-5">
                <span class="flex-shrink-0 w-10 h-10 md:w-11 md:h-11 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white text-sm font-bold flex items-center justify-center shadow-md shadow-brand-500/25">6</span>
                <div class="min-w-0 flex-1 pt-0.5">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">Запрещенные действия</h2>
                    <p class="text-gray-500 text-sm mb-3">Запрещено:</p>
                    <ul class="space-y-2">
                        <?php foreach (['Мошенничество', 'Обман пользователей', 'Использование платформы в незаконных целях', 'Обход комиссии платформы'] as $line): ?>
                        <li class="flex gap-3 text-gray-600 leading-relaxed">
                            <svg class="w-5 h-5 flex-shrink-0 text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span><?= e($line) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </section>

        <section class="legal-card rounded-2xl bg-white border border-gray-100 p-6 md:p-7 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex gap-4 md:gap-5">
                <span class="flex-shrink-0 w-10 h-10 md:w-11 md:h-11 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white text-sm font-bold flex items-center justify-center shadow-md shadow-brand-500/25">7</span>
                <div class="min-w-0 flex-1 pt-0.5">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">Блокировка аккаунта</h2>
                    <p class="text-gray-500 text-sm mb-3">Платформа вправе:</p>
                    <ul class="space-y-2 mb-3">
                        <?php foreach (['Ограничить доступ', 'Заблокировать аккаунт'] as $line): ?>
                        <li class="flex gap-3 text-gray-600 leading-relaxed">
                            <svg class="w-5 h-5 flex-shrink-0 text-brand-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span><?= e($line) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="text-gray-600 leading-relaxed text-sm">При нарушении условий.</p>
                </div>
            </div>
        </section>

        <section class="legal-card rounded-2xl bg-white border border-gray-100 p-6 md:p-7 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex gap-4 md:gap-5">
                <span class="flex-shrink-0 w-10 h-10 md:w-11 md:h-11 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white text-sm font-bold flex items-center justify-center shadow-md shadow-brand-500/25">8</span>
                <div class="min-w-0 flex-1 pt-0.5">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">Отзывы и рейтинг</h2>
                    <p class="text-gray-600 leading-relaxed mb-3">Пользователи могут оставлять отзывы.</p>
                    <p class="text-gray-600 leading-relaxed">Платформа вправе модерировать их.</p>
                </div>
            </div>
        </section>

        <section class="legal-card rounded-2xl bg-white border border-gray-100 p-6 md:p-7 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex gap-4 md:gap-5">
                <span class="flex-shrink-0 w-10 h-10 md:w-11 md:h-11 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white text-sm font-bold flex items-center justify-center shadow-md shadow-brand-500/25">9</span>
                <div class="min-w-0 flex-1 pt-0.5">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">Изменения условий</h2>
                    <p class="text-gray-600 leading-relaxed">Платформа может изменять условия без предварительного уведомления.</p>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-gradient-to-br from-gray-900 to-brand-900 text-white p-6 md:p-8 shadow-xl">
            <div class="flex gap-4 md:gap-5">
                <span class="flex-shrink-0 w-10 h-10 md:w-11 md:h-11 rounded-2xl bg-white/15 flex items-center justify-center text-lg font-bold">10</span>
                <div class="min-w-0 flex-1 pt-0.5">
                    <h2 class="text-lg font-bold mb-3">Контакты</h2>
                    <a href="mailto:support@in-work.kz" class="inline-flex items-center gap-2 text-lg font-semibold text-white border-b border-white/40 hover:border-white transition">support@in-work.kz</a>
                </div>
            </div>
        </section>

        <div class="flex flex-wrap gap-3 pt-4 justify-center">
            <a href="<?= url('help') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 bg-white border border-gray-200 hover:border-brand-200 hover:text-brand-700 transition shadow-sm">← Центр помощи</a>
            <a href="<?= url('privacy') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 transition shadow-md shadow-brand-600/20">Политика конфиденциальности →</a>
        </div>
    </div>
</div>
