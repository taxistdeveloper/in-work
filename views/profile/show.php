<?php $rank = get_rank($profile['completed_orders']); ?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Шапка профиля -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8 mb-6">
        <div class="flex flex-col sm:flex-row items-start gap-6">
            <div class="w-20 h-20 bg-gradient-to-br from-brand-400 to-brand-600 rounded-2xl flex items-center justify-center text-white text-3xl font-bold flex-shrink-0">
                <?= strtoupper(mb_substr($profile['name'], 0, 2)) ?>
            </div>
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-3 mb-2">
                    <h1 class="text-2xl font-bold text-gray-900"><?= e($profile['name']) ?></h1>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-<?= $rank['color'] ?>-100 text-<?= $rank['color'] ?>-700"><?= e($rank['name']) ?></span>
                    <span class="px-3 py-1 text-xs font-medium rounded-full <?= $profile['role'] === 'client' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' ?>">
                        <?= $profile['role'] === 'client' ? 'Заказчик' : 'Исполнитель' ?>
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-3">
                    <span class="flex items-center gap-1"><?= render_stars((float)$profile['rating']) ?> <strong class="text-gray-900"><?= number_format((float)$profile['rating'], 1) ?></strong></span>
                    <span><?= $profile['completed_orders'] ?> заказов выполнено</span>
                    <span>На платформе с <?= date('m.Y', strtotime($profile['created_at'])) ?></span>
                </div>
                <?php if ($profile['bio']): ?>
                    <p class="text-sm text-gray-600 leading-relaxed"><?= nl2br(e($profile['bio'])) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Статистика -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 p-5 text-center">
            <div class="text-2xl font-bold text-gray-900"><?= number_format((float)$profile['rating'], 1) ?></div>
            <div class="text-sm text-gray-500 mt-1">Рейтинг</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5 text-center">
            <div class="text-2xl font-bold text-gray-900"><?= $profile['completed_orders'] ?></div>
            <div class="text-sm text-gray-500 mt-1">Выполнено</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5 text-center">
            <div class="text-2xl font-bold text-<?= $rank['color'] ?>-600"><?= e($rank['name']) ?></div>
            <div class="text-sm text-gray-500 mt-1">Ранг</div>
        </div>
    </div>

    <!-- Отзывы -->
    <div class="bg-white rounded-2xl border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Отзывы (<?= $pagination['total'] ?>)</h2>
        </div>
        <?php if (empty($reviews)): ?>
            <div class="px-6 py-12 text-center">
                <p class="text-gray-400">Пока нет отзывов.</p>
            </div>
        <?php else: ?>
            <div class="divide-y divide-gray-50">
                <?php foreach ($reviews as $review): ?>
                    <div class="p-6">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-gray-100 text-gray-600 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">
                                <?= strtoupper(mb_substr($review['reviewer_name'], 0, 2)) ?>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-semibold text-gray-900"><?= e($review['reviewer_name']) ?></span>
                                    <span class="text-xs text-gray-400"><?= time_ago($review['created_at']) ?></span>
                                </div>
                                <div class="mb-2"><?= render_stars((int)$review['rating']) ?></div>
                                <?php if ($review['comment']): ?>
                                    <p class="text-sm text-gray-600"><?= e($review['comment']) ?></p>
                                <?php endif; ?>
                                <p class="text-xs text-gray-400 mt-1">Заказ: <?= e($review['order_title']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?= paginate_links($pagination['page'], $pagination['total_pages'], url("profile/{$profile['id']}")) ?>
        <?php endif; ?>
    </div>
</div>
