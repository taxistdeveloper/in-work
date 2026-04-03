<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
    <div class="text-center max-w-2xl mx-auto mb-12">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">Каталог специалистов</h1>
        <p class="text-gray-500 text-lg">Выберите направление и сравните исполнителей по рейтингу, отзывам и опыту — затем сразу оформите заказ с резервированием оплаты.</p>
    </div>

    <?php if (empty($catalog_categories)): ?>
        <p class="text-center text-gray-500">Каталожные категории пока не настроены.</p>
    <?php else: ?>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($catalog_categories as $cat): ?>
                <a href="<?= url('catalog/' . $cat['slug']) ?>"
                   class="group bg-white rounded-2xl border border-gray-100 p-6 hover:border-brand-200 hover:shadow-lg hover:shadow-brand-500/5 transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white text-xl mb-4 group-hover:scale-105 transition-transform">
                        ✓
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-brand-700 transition"><?= e($cat['label']) ?></h2>
                    <p class="text-sm text-gray-500 mb-4">Специалисты с подтверждённой специализацией и рейтингом платформы.</p>
                    <span class="text-brand-600 font-medium text-sm inline-flex items-center gap-1">
                        Смотреть список
                        <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="mt-12 text-center">
        <p class="text-gray-500 text-sm mb-3">Нужен классический тендер по откликам?</p>
        <a href="<?= url('orders') ?>" class="text-brand-600 font-medium hover:underline">Лента заказов для исполнителей</a>
        <?php if (is_logged_in() && user_role() === 'client'): ?>
            <span class="text-gray-300 mx-2">·</span>
            <a href="<?= url('orders/create') ?>" class="text-brand-600 font-medium hover:underline">Создать заказ (рынок)</a>
        <?php endif; ?>
    </div>
</div>
