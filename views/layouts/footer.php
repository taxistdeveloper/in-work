<footer class="bg-white border-t border-gray-100 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="col-span-2 md:col-span-1">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-gradient-to-br from-brand-500 to-brand-700 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-sm">in</span>
                    </div>
                    <span class="text-xl font-bold">in<span class="text-brand-600">Work</span></span>
                </div>
                <p class="text-sm text-gray-500 leading-relaxed">Современная фриланс-платформа с динамическим ценообразованием. Назови свою цену — найди лучшего специалиста.</p>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-900 mb-3">Платформа</h4>
                <ul class="space-y-2">
                    <li><a href="<?= url('catalog') ?>" class="text-sm text-gray-500 hover:text-brand-600 transition">Каталог специалистов</a></li>
                    <li><a href="<?= (is_logged_in() && user_role() === 'client') ? url('my-orders') : url('orders') ?>" class="text-sm text-gray-500 hover:text-brand-600 transition"><?= (is_logged_in() && user_role() === 'client') ? 'Мои заказы' : 'Лента заказов' ?></a></li>
                    <li><a href="<?= url('register') ?>" class="text-sm text-gray-500 hover:text-brand-600 transition">Стать фрилансером</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-900 mb-3">Каталог</h4>
                <ul class="space-y-2">
                    <li><a href="<?= url('catalog/electrician') ?>" class="text-sm text-gray-500 hover:text-brand-600 transition">Электрики</a></li>
                    <li><a href="<?= url('catalog/plumber') ?>" class="text-sm text-gray-500 hover:text-brand-600 transition">Сантехника</a></li>
                    <li><a href="<?= url('catalog/repair') ?>" class="text-sm text-gray-500 hover:text-brand-600 transition">Ремонт</a></li>
                    <li><a href="<?= url('orders') ?>?category=web-development" class="text-sm text-gray-500 hover:text-brand-600 transition">Веб-разработка (рынок)</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-900 mb-3">Поддержка</h4>
                <ul class="space-y-2">
                    <li><a href="<?= url('help') ?>" class="text-sm text-gray-500 hover:text-brand-600 transition">Центр помощи</a></li>
                    <li><a href="<?= url('terms') ?>" class="text-sm text-gray-500 hover:text-brand-600 transition">Условия использования</a></li>
                    <li><a href="<?= url('privacy') ?>" class="text-sm text-gray-500 hover:text-brand-600 transition">Политика конфиденциальности</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-100 mt-8 pt-8 text-center">
            <p class="text-sm text-gray-400">&copy; <?= date('Y') ?> inWork. Все права защищены.</p>
        </div>
    </div>
</footer>
