<?php
/** @var list<array{slug: string, label: string, mode: string, freelancers_in_catalog: int, orders_total: int}> $category_rows */
/** @var int $catalog_total */
?>

<div class="mb-6 rounded-2xl border border-amber-100 bg-amber-50/80 px-5 py-4 text-sm text-amber-950">
    <p class="font-medium text-amber-900">Каталожные категории (<?= (int) $catalog_total ?>)</p>
    <p class="mt-1 text-amber-800/90">Электрики, сантехника, ремонт — выбор исполнителя вручную и найм без ленты откликов. Редактирование списка и режимов — в файле <code class="rounded bg-white/80 px-1.5 py-0.5 text-xs">config/app.php</code> (массивы <code class="rounded bg-white/80 px-1">categories</code> и <code class="rounded bg-white/80 px-1">category_modes</code>).</p>
</div>

<div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100 text-left text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 font-semibold text-gray-700">Название</th>
                    <th class="px-5 py-3 font-semibold text-gray-700">Slug</th>
                    <th class="px-5 py-3 font-semibold text-gray-700">Режим</th>
                    <th class="px-5 py-3 font-semibold text-gray-700 text-right">В каталоге</th>
                    <th class="px-5 py-3 font-semibold text-gray-700 text-right">Заказов (всего)</th>
                    <th class="px-5 py-3 font-semibold text-gray-700"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($category_rows as $row): ?>
                    <tr class="hover:bg-gray-50/80">
                        <td class="px-5 py-3.5 font-medium text-gray-900"><?= e($row['label']) ?></td>
                        <td class="px-5 py-3.5 font-mono text-xs text-gray-600"><?= e($row['slug']) ?></td>
                        <td class="px-5 py-3.5">
                            <?php if ($row['mode'] === 'catalog'): ?>
                                <span class="inline-flex items-center rounded-full bg-violet-100 px-2.5 py-0.5 text-xs font-medium text-violet-800">Каталог</span>
                            <?php else: ?>
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">Рынок</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3.5 text-right tabular-nums text-gray-700"><?= (int) $row['freelancers_in_catalog'] ?></td>
                        <td class="px-5 py-3.5 text-right tabular-nums text-gray-700"><?= (int) $row['orders_total'] ?></td>
                        <td class="px-5 py-3.5 text-right">
                            <?php if ($row['mode'] === 'catalog'): ?>
                                <a href="<?= url('catalog/' . $row['slug']) ?>" target="_blank" rel="noopener" class="text-brand-600 hover:text-brand-700 font-medium">Открыть каталог ↗</a>
                            <?php else: ?>
                                <a href="<?= url('orders') ?>?category=<?= urlencode($row['slug']) ?>" target="_blank" rel="noopener" class="text-brand-600 hover:text-brand-700 font-medium">Лента ↗</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<p class="mt-6 text-center text-xs text-gray-400">Добавленные каталожные направления: электрики (<code class="rounded bg-gray-100 px-1">electrician</code>), сантехника (<code class="rounded bg-gray-100 px-1">plumber</code>), ремонт (<code class="rounded bg-gray-100 px-1">repair</code>).</p>
