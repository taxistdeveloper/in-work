<?php
$sortLabels = [
    'score'        => 'Рейтинг системы',
    'rating'       => 'Оценка',
    'reviews'      => 'Отзывы',
    'experience'   => 'Стаж',
    'completed'    => 'Заказы',
    'reliability'  => 'Надёжность',
];
$items = $result['items'] ?? [];
$page = (int) ($result['page'] ?? 1);
$totalPages = (int) ($result['total_pages'] ?? 1);
$total = (int) ($result['total'] ?? 0);
$baseQs = static function (string $s, int $p) use ($category): string {
    return 'sort=' . urlencode($s) . '&page=' . $p;
};
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <nav class="text-sm text-gray-500 mb-2">
                <a href="<?= url('catalog') ?>" class="hover:text-brand-600">Каталог</a>
                <span class="mx-1">/</span>
                <span class="text-gray-900"><?= e($category_label) ?></span>
            </nav>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900"><?= e($category_label) ?></h1>
            <p class="text-gray-500 mt-1"><?= $total ?> специалист<?= $total === 1 ? '' : ($total >= 2 && $total <= 4 ? 'а' : 'ов') ?></p>
        </div>
        <a href="<?= url('catalog') ?>" class="text-sm text-brand-600 font-medium hover:underline shrink-0">← Все категории</a>
    </div>

    <div class="flex flex-wrap gap-2 mb-8">
        <?php foreach ($sortLabels as $key => $lbl): ?>
            <?php
            $active = $sort === $key;
            $href = url('catalog/' . $category) . '?' . $baseQs($key, 1);
            ?>
            <a href="<?= $href ?>"
               class="px-3 py-1.5 rounded-full text-sm font-medium border transition <?= $active ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-gray-600 border-gray-200 hover:border-brand-300' ?>">
                <?= e($lbl) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($items)): ?>
        <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center text-gray-500">
            Пока нет исполнителей с этой специализацией. Загляните позже или пригласите мастера зарегистрироваться и указать категорию в профиле.
        </div>
    <?php else: ?>
        <div class="grid gap-4 md:gap-6">
            <?php foreach ($items as $s): ?>
                <article class="bg-white rounded-2xl border border-gray-100 p-5 sm:p-6 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 hover:border-brand-100 transition">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white text-lg font-bold shrink-0">
                                <?= strtoupper(mb_substr($s['name'] ?? '?', 0, 2)) ?>
                            </div>
                            <div class="min-w-0">
                                <h2 class="text-lg font-bold text-gray-900">
                                    <a href="<?= url('profile/' . (int) $s['id']) ?>" class="hover:text-brand-600"><?= e($s['name'] ?? '') ?></a>
                                </h2>
                                <div class="flex flex-wrap gap-x-3 gap-y-1 mt-1 text-sm text-gray-500">
                                    <span><?= render_stars((float) ($s['rating'] ?? 0)) ?> <?= number_format((float) ($s['rating'] ?? 0), 1) ?></span>
                                    <span>Отзывов: <?= (int) ($s['review_count'] ?? 0) ?></span>
                                    <span>Заказов: <?= (int) ($s['completed_orders'] ?? 0) ?></span>
                                    <span>Стаж: <?= (int) ($s['tenure_days'] ?? 0) ?> дн.</span>
                                </div>
                                <p class="text-xs text-brand-600 font-semibold mt-2">Рейтинг inWork: <?= number_format((float) ($s['platform_score'] ?? 0), 1) ?></p>
                                <?php if (!empty($s['bio'])): ?>
                                    <p class="text-sm text-gray-600 mt-3 line-clamp-3"><?= e($s['bio']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="flex sm:flex-col gap-2 shrink-0">
                        <?php if (is_logged_in() && user_role() === 'client'): ?>
                            <a href="<?= url('catalog/' . $category . '/hire/' . (int) $s['id']) ?>"
                               class="inline-flex justify-center items-center px-5 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-700 transition shadow-sm">
                                Нанять
                            </a>
                        <?php elseif (! is_logged_in()): ?>
                            <a href="<?= url('login') ?>"
                               class="inline-flex justify-center items-center px-5 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-gray-800 transition">
                                Войти для найма
                            </a>
                        <?php else: ?>
                            <span class="text-xs text-gray-400 text-center sm:text-right max-w-[140px]">Найм доступен заказчикам</span>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav class="flex justify-center items-center gap-2 mt-10" aria-label="Страницы">
                <?php if ($page > 1): ?>
                    <a href="<?= url('catalog/' . $category) . '?' . $baseQs($sort, $page - 1) ?>"
                       class="px-4 py-2 rounded-xl border border-gray-200 text-sm hover:bg-gray-50">Назад</a>
                <?php endif; ?>
                <span class="text-sm text-gray-500 px-2">Стр. <?= (int) $page ?> из <?= (int) $totalPages ?></span>
                <?php if ($page < $totalPages): ?>
                    <a href="<?= url('catalog/' . $category) . '?' . $baseQs($sort, $page + 1) ?>"
                       class="px-4 py-2 rounded-xl border border-gray-200 text-sm hover:bg-gray-50">Вперёд</a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>
