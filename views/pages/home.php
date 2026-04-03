<!-- Герой -->
<section class="relative overflow-hidden bg-gradient-to-br from-gray-900 via-gray-800 to-brand-900">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-20 left-10 w-72 h-72 bg-brand-400 rounded-full filter blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-emerald-400 rounded-full filter blur-3xl"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
        <div class="text-center max-w-3xl mx-auto">
            
            <h1 class="text-4xl md:text-6xl font-extrabold text-white leading-tight mb-6">
                Назови свою цену,<br>
                <span class="bg-gradient-to-r from-brand-400 to-emerald-300 bg-clip-text text-transparent">получи результат</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-300 mb-10 max-w-2xl mx-auto leading-relaxed">
                Фриланс-платформа, где вы устанавливаете условия. Опубликуйте проект, получите предложения с гибкой ценой и выберите лучшего исполнителя.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?= url('register') ?>" class="px-8 py-3.5 bg-brand-500 text-white font-semibold rounded-xl hover:bg-brand-600 transition shadow-lg shadow-brand-500/25 text-center">
                    Начать нанимать
                </a>
                <a href="<?= url('catalog') ?>" class="px-8 py-3.5 bg-white/15 backdrop-blur text-white font-semibold rounded-xl hover:bg-white/25 transition border border-white/20 text-center">
                    Каталог специалистов
                </a>
                <a href="<?= url('orders') ?>" class="px-8 py-3.5 bg-white/10 backdrop-blur text-white font-semibold rounded-xl hover:bg-white/20 transition border border-white/10 text-center">
                    Найти работу
                </a>
            </div>
        </div>

        <!-- Статистика -->
        <div class="grid grid-cols-3 gap-8 mt-20 max-w-2xl mx-auto">
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-bold text-white">10K+</div>
                <div class="text-sm text-gray-400 mt-1">Фрилансеров</div>
            </div>
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-bold text-white">50K+</div>
                <div class="text-sm text-gray-400 mt-1">Проектов выполнено</div>
            </div>
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-bold text-white">₸250M+</div>
                <div class="text-sm text-gray-400 mt-1">Выплачено</div>
            </div>
        </div>
    </div>
</section>

<!-- Как это работает -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Как это работает</h2>
            <p class="text-lg text-gray-500 max-w-2xl mx-auto">Как вызов такси, только для фриланса. Назови цену, договорись и начинай работать.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="relative group">
                <div class="bg-gray-50 rounded-2xl p-8 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="w-14 h-14 bg-brand-100 text-brand-600 rounded-2xl flex items-center justify-center mb-6 text-2xl font-bold group-hover:bg-brand-500 group-hover:text-white transition">1</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Опубликуйте задание</h3>
                    <p class="text-gray-500 leading-relaxed">Опишите задачу, укажите бюджет — и фрилансеры сами придут к вам с лучшими предложениями.</p>
                </div>
            </div>
            <div class="relative group">
                <div class="bg-gray-50 rounded-2xl p-8 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="w-14 h-14 bg-brand-100 text-brand-600 rounded-2xl flex items-center justify-center mb-6 text-2xl font-bold group-hover:bg-brand-500 group-hover:text-white transition">2</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Получите предложения</h3>
                    <p class="text-gray-500 leading-relaxed">Исполнители предлагают свою цену — ваш бюджет, на 10% меньше, на 10% больше или свою сумму.</p>
                </div>
            </div>
            <div class="relative group">
                <div class="bg-gray-50 rounded-2xl p-8 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="w-14 h-14 bg-brand-100 text-brand-600 rounded-2xl flex items-center justify-center mb-6 text-2xl font-bold group-hover:bg-brand-500 group-hover:text-white transition">3</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Выберите и начинайте</h3>
                    <p class="text-gray-500 leading-relaxed">Сравните отклики по цене, рейтингу и рангу. Выберите лучшего — и деньги уходят на эскроу.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Последние заказы -->
<?php if (!empty($orders)): ?>
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Свежие проекты</h2>
                <p class="text-gray-500 mt-1">Новые возможности ждут вас</p>
            </div>
            <a href="<?= url('orders') ?>" class="hidden sm:flex items-center gap-2 px-5 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-white hover:shadow-sm transition">
                Все заказы
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($orders as $order): ?>
            <a href="<?= url("orders/{$order['id']}") ?>" class="bg-white rounded-2xl p-6 border border-gray-100 hover:shadow-lg hover:border-brand-200 transition-all duration-300 group">
                <div class="flex items-start justify-between mb-4">
                    <span class="px-3 py-1 bg-brand-50 text-brand-700 text-xs font-medium rounded-full"><?= e($order['category']) ?></span>
                    <span class="text-lg font-bold text-brand-600"><?= format_money((float)$order['budget']) ?></span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-brand-600 transition line-clamp-1"><?= e($order['title']) ?></h3>
                <p class="text-sm text-gray-500 mb-4 line-clamp-2"><?= e(mb_substr($order['description'], 0, 120)) ?>...</p>
                <div class="flex items-center justify-between text-xs text-gray-400">
                    <span>Дедлайн: <?= e($order['deadline']) ?></span>
                    <span><?= time_ago($order['created_at']) ?></span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Призыв к действию -->
<section class="py-20 bg-gradient-to-r from-brand-600 to-brand-700">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Готовы начать?</h2>
        <p class="text-lg text-brand-100 mb-10 max-w-2xl mx-auto">Присоединяйтесь к тысячам заказчиков и фрилансеров, которые доверяют inWork честное и динамичное ценообразование.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?= url('register') ?>" class="px-8 py-3.5 bg-white text-brand-700 font-semibold rounded-xl hover:bg-gray-50 transition shadow-lg text-center">Создать аккаунт бесплатно</a>
            <a href="<?= url('catalog') ?>" class="px-8 py-3.5 bg-brand-500/30 text-white font-semibold rounded-xl hover:bg-brand-500/40 transition border border-white/30 text-center">Каталог мастеров</a>
            <a href="<?= url('orders') ?>" class="px-8 py-3.5 bg-brand-500 text-white font-semibold rounded-xl hover:bg-brand-400 transition border border-brand-400 text-center">Смотреть проекты</a>
        </div>
    </div>
</section>
