<?php
$staticHeroTitle = 'Политика конфиденциальности';
$staticHeroSubtitle = 'Как мы собираем, храним и защищаем ваши данные на платформе inWork.';
$staticHeroIcon = 'shield';
require __DIR__ . '/../partials/static_page_hero.php';
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 -mt-14 md:-mt-16 relative z-10 pb-16 md:pb-20 static-page-content">
    <div class="rounded-2xl bg-gradient-to-br from-brand-50 to-emerald-50/80 border border-brand-100 p-6 md:p-8 mb-8 shadow-sm">
        <p class="text-gray-700 leading-relaxed text-[15px] md:text-base">
            Настоящая Политика конфиденциальности регулирует порядок сбора, хранения и использования персональных данных
            пользователей платформы inWork (далее — «Платформа»).
        </p>
    </div>

    <div class="space-y-4 md:space-y-5">
        <section class="legal-card rounded-2xl bg-white border border-gray-100 p-6 md:p-7 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex gap-4 md:gap-5">
                <span class="flex-shrink-0 w-10 h-10 md:w-11 md:h-11 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white text-sm font-bold flex items-center justify-center shadow-md shadow-brand-500/25">1</span>
                <div class="min-w-0 flex-1 pt-0.5">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">Общие положения</h2>
                    <p class="text-gray-600 leading-relaxed mb-3">Используя Платформу, пользователь соглашается с условиями настоящей Политики.</p>
                    <p class="text-gray-600 leading-relaxed">Платформа обрабатывает персональные данные в соответствии с законодательством Республики Казахстан.</p>
                </div>
            </div>
        </section>

        <section class="legal-card rounded-2xl bg-white border border-gray-100 p-6 md:p-7 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex gap-4 md:gap-5">
                <span class="flex-shrink-0 w-10 h-10 md:w-11 md:h-11 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white text-sm font-bold flex items-center justify-center shadow-md shadow-brand-500/25">2</span>
                <div class="min-w-0 flex-1 pt-0.5">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">Какие данные мы собираем</h2>
                    <p class="text-gray-500 text-sm mb-3">Мы можем собирать следующие данные:</p>
                    <ul class="space-y-2">
                        <?php foreach (['Имя и контактная информация (email, телефон)', 'Данные профиля (навыки, фото, описание)', 'Платежные данные (в обезличенном виде через платежные системы)', 'История заказов и откликов', 'IP-адрес, cookies, данные устройства'] as $line): ?>
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
                    <h2 class="text-lg font-bold text-gray-900 mb-3">Цели обработки данных</h2>
                    <p class="text-gray-500 text-sm mb-3">Данные используются для:</p>
                    <ul class="space-y-2">
                        <?php foreach (['Регистрации и авторизации пользователей', 'Обеспечения работы платформы', 'Проведения сделок между пользователями', 'Улучшения качества сервиса', 'Предотвращения мошенничества', 'Рассылки уведомлений'] as $line): ?>
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
                <span class="flex-shrink-0 w-10 h-10 md:w-11 md:h-11 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white text-sm font-bold flex items-center justify-center shadow-md shadow-brand-500/25">4</span>
                <div class="min-w-0 flex-1 pt-0.5">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">Передача данных третьим лицам</h2>
                    <p class="text-gray-500 text-sm mb-3">Мы не передаем персональные данные третьим лицам, за исключением:</p>
                    <ul class="space-y-2">
                        <?php foreach (['Платежных систем (для обработки платежей)', 'Государственных органов (по законному требованию)', 'Технических подрядчиков (хостинг, аналитика)'] as $line): ?>
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
                <span class="flex-shrink-0 w-10 h-10 md:w-11 md:h-11 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white text-sm font-bold flex items-center justify-center shadow-md shadow-brand-500/25">5</span>
                <div class="min-w-0 flex-1 pt-0.5">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">Хранение данных</h2>
                    <p class="text-gray-600 leading-relaxed">Данные хранятся столько, сколько необходимо для выполнения целей обработки, либо в соответствии с законодательством.</p>
                </div>
            </div>
        </section>

        <section class="legal-card rounded-2xl bg-white border border-gray-100 p-6 md:p-7 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex gap-4 md:gap-5">
                <span class="flex-shrink-0 w-10 h-10 md:w-11 md:h-11 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white text-sm font-bold flex items-center justify-center shadow-md shadow-brand-500/25">6</span>
                <div class="min-w-0 flex-1 pt-0.5">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">Защита данных</h2>
                    <p class="text-gray-500 text-sm mb-3">Мы принимаем все разумные меры для защиты данных:</p>
                    <ul class="space-y-2">
                        <?php foreach (['Шифрование', 'Контроль доступа', 'Мониторинг активности'] as $line): ?>
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
                <span class="flex-shrink-0 w-10 h-10 md:w-11 md:h-11 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white text-sm font-bold flex items-center justify-center shadow-md shadow-brand-500/25">7</span>
                <div class="min-w-0 flex-1 pt-0.5">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">Права пользователя</h2>
                    <p class="text-gray-500 text-sm mb-3">Пользователь имеет право:</p>
                    <ul class="space-y-2">
                        <?php foreach (['Запросить доступ к своим данным', 'Изменить или удалить данные', 'Отозвать согласие на обработку'] as $line): ?>
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
                <span class="flex-shrink-0 w-10 h-10 md:w-11 md:h-11 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white text-sm font-bold flex items-center justify-center shadow-md shadow-brand-500/25">8</span>
                <div class="min-w-0 flex-1 pt-0.5">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">Cookies</h2>
                    <p class="text-gray-500 text-sm mb-3">Платформа использует cookies для:</p>
                    <ul class="space-y-2">
                        <?php foreach (['Авторизации', 'Аналитики', 'Персонализации'] as $line): ?>
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
                <span class="flex-shrink-0 w-10 h-10 md:w-11 md:h-11 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white text-sm font-bold flex items-center justify-center shadow-md shadow-brand-500/25">9</span>
                <div class="min-w-0 flex-1 pt-0.5">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">Изменения политики</h2>
                    <p class="text-gray-600 leading-relaxed">Мы можем обновлять Политику. Новая версия вступает в силу с момента публикации.</p>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-gradient-to-br from-gray-900 to-brand-900 text-white p-6 md:p-8 shadow-xl">
            <div class="flex gap-4 md:gap-5">
                <span class="flex-shrink-0 w-10 h-10 md:w-11 md:h-11 rounded-2xl bg-white/15 flex items-center justify-center text-lg font-bold">10</span>
                <div class="min-w-0 flex-1 pt-0.5">
                    <h2 class="text-lg font-bold mb-3">Контакты</h2>
                    <p class="text-brand-100 text-sm mb-2">По вопросам обработки данных:</p>
                    <a href="mailto:support@in-work.kz" class="inline-flex items-center gap-2 text-lg font-semibold text-white border-b border-white/40 hover:border-white transition">support@in-work.kz</a>
                </div>
            </div>
        </section>

        <div class="flex flex-wrap gap-3 pt-4 justify-center">
            <a href="<?= url('help') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 bg-white border border-gray-200 hover:border-brand-200 hover:text-brand-700 transition shadow-sm">← Центр помощи</a>
            <a href="<?= url('terms') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 transition shadow-md shadow-brand-600/20">Условия использования →</a>
        </div>
    </div>
</div>
