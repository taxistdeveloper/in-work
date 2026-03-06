<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Сообщения</h1>

    <?php if (empty($conversations)): ?>
        <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Пока нет диалогов</h3>
            <p class="text-gray-500">Диалоги появятся здесь, когда отклик будет принят.</p>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-2xl border border-gray-100 divide-y divide-gray-50">
            <?php foreach ($conversations as $conv): ?>
                <a href="<?= url("chat/{$conv['id']}") ?>" class="flex items-center gap-4 p-4 sm:p-5 hover:bg-gray-50 transition">
                    <div class="relative flex-shrink-0">
                        <div class="w-12 h-12 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center text-sm font-bold">
                            <?= strtoupper(mb_substr($conv['partner_name'], 0, 2)) ?>
                        </div>
                        <?php if ($conv['unread_count'] > 0): ?>
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center"><?= $conv['unread_count'] ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900"><?= e($conv['partner_name']) ?></h3>
                            <span class="text-xs text-gray-400"><?= $conv['last_message_at'] ? time_ago($conv['last_message_at']) : '' ?></span>
                        </div>
                        <?php if ($conv['order_title']): ?>
                            <p class="text-xs text-brand-600 mt-0.5"><?= e($conv['order_title']) ?></p>
                        <?php endif; ?>
                        <?php if ($conv['last_message']): ?>
                            <p class="text-sm text-gray-500 truncate mt-0.5"><?= e(mb_substr($conv['last_message'], 0, 60)) ?></p>
                        <?php endif; ?>
                    </div>
                    <svg class="w-5 h-5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
