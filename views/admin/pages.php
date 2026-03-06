<div class="max-w-3xl">
    <div class="mb-6">
        <p class="text-sm text-gray-500">Включайте и отключайте разделы платформы. Отключённые страницы показывают пользователям экран «В разработке». Администраторы видят все страницы.</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="divide-y divide-gray-50">
            <?php foreach ($pages as $page): ?>
                <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50/50 transition">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center <?= $page['is_enabled'] ? 'bg-brand-50' : 'bg-gray-100' ?> transition">
                            <svg class="w-5 h-5 <?= $page['is_enabled'] ? 'text-brand-600' : 'text-gray-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= e($page['page_icon'] ?? 'M4 6h16M4 12h16M4 18h16') ?>"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900"><?= e($page['page_name']) ?></div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <code class="text-xs text-gray-400 bg-gray-50 px-1.5 py-0.5 rounded"><?= e($page['page_key']) ?></code>
                                <?php if ($page['is_enabled']): ?>
                                    <span class="inline-flex items-center gap-1 text-xs text-emerald-600">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                        Активна
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 text-xs text-red-500">
                                        <span class="w-1.5 h-1.5 bg-red-400 rounded-full"></span>
                                        Отключена
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <a href="<?= url("admin/pages/{$page['id']}/toggle") ?>"
                       class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors duration-200 focus:outline-none <?= $page['is_enabled'] ? 'bg-brand-500' : 'bg-gray-300' ?>"
                       title="<?= $page['is_enabled'] ? 'Отключить' : 'Включить' ?>">
                        <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow-sm transition-transform duration-200 <?= $page['is_enabled'] ? 'translate-x-6' : 'translate-x-1' ?>"></span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="mt-6 bg-amber-50 border border-amber-200 rounded-2xl p-4">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <div class="text-sm text-amber-800">
                <p class="font-semibold mb-1">Обратите внимание</p>
                <p class="text-amber-700">Администраторы имеют доступ ко всем страницам, даже отключённым. Отключение влияет только на обычных пользователей (заказчики и исполнители).</p>
            </div>
        </div>
    </div>
</div>
