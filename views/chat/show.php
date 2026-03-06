<?php $userId = user_id(); ?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <!-- Шапка чата -->
    <div class="bg-white rounded-t-2xl border border-gray-100 px-6 py-4 flex items-center gap-4">
        <a href="<?= url('chat') ?>" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div class="w-10 h-10 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center text-sm font-bold">
            <?= strtoupper(mb_substr($conversation['partner_name'], 0, 2)) ?>
        </div>
        <div class="flex-1 min-w-0">
            <a href="<?= url("profile/{$conversation['partner_id']}") ?>" class="text-sm font-semibold text-gray-900 hover:text-brand-600 transition"><?= e($conversation['partner_name']) ?></a>
            <?php if ($conversation['order_title']): ?>
                <p class="text-xs text-gray-500 truncate"><?= e($conversation['order_title']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Сообщения -->
    <div id="chatMessages" class="bg-gray-50 border-x border-gray-100 px-6 py-4 space-y-4 overflow-y-auto" style="height: calc(100vh - 16rem); min-height: 300px;">
        <?php if (empty($messages)): ?>
            <div class="flex items-center justify-center h-full">
                <p class="text-gray-400">Пока нет сообщений. Начните общение!</p>
            </div>
        <?php else: ?>
            <?php foreach ($messages as $msg): $isMine = (int)$msg['sender_id'] === $userId; ?>
                <div class="flex <?= $isMine ? 'justify-end' : 'justify-start' ?> fade-in" data-msg-id="<?= $msg['id'] ?>">
                    <div class="max-w-[75%] <?= $isMine ? 'bg-brand-600 text-white' : 'bg-white border border-gray-100 text-gray-900' ?> rounded-2xl <?= $isMine ? 'rounded-br-md' : 'rounded-bl-md' ?> px-4 py-2.5 shadow-sm">
                        <?php if (!$isMine): ?>
                            <div class="text-xs font-semibold mb-1 text-brand-600"><?= e($msg['sender_name']) ?></div>
                        <?php endif; ?>
                        <p class="text-sm leading-relaxed"><?= nl2br(e($msg['body'])) ?></p>
                        <div class="text-xs mt-1 <?= $isMine ? 'text-brand-200' : 'text-gray-400' ?> text-right"><?= date('H:i', strtotime($msg['created_at'])) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Поле ввода -->
    <div class="bg-white rounded-b-2xl border border-gray-100 px-4 py-3">
        <form id="chatForm" class="flex items-center gap-3">
            <input type="text" id="messageInput" placeholder="Введите сообщение..." autocomplete="off"
                   class="flex-1 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
            <button type="submit" class="px-5 py-2.5 bg-brand-600 text-white rounded-xl hover:bg-brand-700 transition flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            </button>
        </form>
    </div>
</div>

<script>
const conversationId = <?= (int)$conversation['id'] ?>;
const currentUserId = <?= $userId ?>;
const chatMessages = document.getElementById('chatMessages');
const chatForm = document.getElementById('chatForm');
const messageInput = document.getElementById('messageInput');

chatMessages.scrollTop = chatMessages.scrollHeight;

chatForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const message = messageInput.value.trim();
    if (!message) return;

    const myMsg = document.createElement('div');
    myMsg.className = 'flex justify-end fade-in';
    myMsg.innerHTML = `<div class="max-w-[75%] bg-brand-600 text-white rounded-2xl rounded-br-md px-4 py-2.5 shadow-sm">
        <p class="text-sm leading-relaxed">${message.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>')}</p>
        <div class="text-xs mt-1 text-brand-200 text-right">${new Date().toLocaleTimeString('ru-RU', {hour:'2-digit',minute:'2-digit'})}</div>
    </div>`;
    chatMessages.appendChild(myMsg);
    chatMessages.scrollTop = chatMessages.scrollHeight;
    messageInput.value = '';

    const formData = new FormData();
    formData.append('message', message);

    try {
        await fetch(`<?= url("chat/{$conversation['id']}/send") ?>`, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
    } catch (err) {
        console.error('Ошибка отправки', err);
    }
});

function getLastMessageId() {
    const msgs = chatMessages.querySelectorAll('[data-msg-id]');
    if (msgs.length === 0) return 0;
    return parseInt(msgs[msgs.length - 1].dataset.msgId) || 0;
}

async function pollMessages() {
    try {
        const lastId = getLastMessageId();
        const resp = await fetch(`<?= url("api/chat/{$conversation['id']}/messages") ?>?after=${lastId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await resp.json();

        if (data.messages) {
            data.messages.forEach(msg => {
                if (parseInt(msg.sender_id) === currentUserId) return;
                if (chatMessages.querySelector(`[data-msg-id="${msg.id}"]`)) return;

                const div = document.createElement('div');
                div.className = 'flex justify-start fade-in';
                div.dataset.msgId = msg.id;
                div.innerHTML = `<div class="max-w-[75%] bg-white border border-gray-100 text-gray-900 rounded-2xl rounded-bl-md px-4 py-2.5 shadow-sm">
                    <div class="text-xs font-semibold mb-1 text-brand-600">${msg.sender_name}</div>
                    <p class="text-sm leading-relaxed">${msg.body.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>')}</p>
                    <div class="text-xs mt-1 text-gray-400 text-right">${new Date(msg.created_at).toLocaleTimeString('ru-RU', {hour:'2-digit',minute:'2-digit'})}</div>
                </div>`;
                chatMessages.appendChild(div);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
        }
    } catch (err) {}
}

setInterval(pollMessages, 3000);
</script>
