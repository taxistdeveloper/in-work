<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Conversation;
use App\Models\Message;

class ChatController extends Controller
{
    private Conversation $convModel;
    private Message $msgModel;

    public function __construct()
    {
        $this->convModel = new Conversation();
        $this->msgModel = new Message();
    }

    public function index(): void
    {
        $this->requireAuth();

        $conversations = $this->convModel->getUserConversations(user_id());

        $this->view('chat.index', [
            'title'         => 'Messages',
            'conversations' => $conversations,
        ]);
    }

    public function show(string $id): void
    {
        $this->requireAuth();

        $conversation = $this->convModel->getWithPartner((int) $id, user_id());

        if (!$conversation) {
            flash('error', 'Conversation not found.');
            $this->redirect(url('chat'));
            return;
        }

        $this->msgModel->markAsRead((int) $id, user_id());

        $messages = $this->msgModel->getConversationMessages((int) $id);

        $this->view('chat.show', [
            'title'        => 'Chat with ' . $conversation['partner_name'],
            'conversation' => $conversation,
            'messages'     => $messages,
        ]);
    }

    public function send(string $id): void
    {
        $this->requireAuth();

        $conversation = $this->convModel->getWithPartner((int) $id, user_id());

        if (!$conversation) {
            $this->json(['error' => 'Not found'], 404);
            return;
        }

        $body = trim((string) $this->input('message', ''));
        if ($body === '') {
            $this->json(['error' => 'Message is empty'], 422);
            return;
        }

        $msgId = $this->msgModel->create([
            'conversation_id' => (int) $id,
            'sender_id'       => user_id(),
            'body'            => $body,
        ]);

        $this->convModel->update((int) $id, [
            'last_message_at' => date('Y-m-d H:i:s'),
        ]);

        if ($this->isAjax()) {
            $this->json([
                'success' => true,
                'message' => [
                    'id'          => $msgId,
                    'body'        => $body,
                    'sender_id'   => user_id(),
                    'sender_name' => $this->currentUser()['name'],
                    'created_at'  => date('Y-m-d H:i:s'),
                ],
            ]);
        } else {
            $this->redirect(url("chat/{$id}"));
        }
    }

    public function messages(string $id): void
    {
        $this->requireAuth();

        $conversation = $this->convModel->getWithPartner((int) $id, user_id());

        if (!$conversation) {
            $this->json(['error' => 'Not found'], 404);
            return;
        }

        $lastId = (int) $this->input('after', 0);
        $messages = [];

        if ($lastId > 0) {
            $messages = \Core\Database::getInstance()->fetchAll(
                "SELECT m.*, u.name as sender_name FROM messages m
                 JOIN users u ON m.sender_id = u.id
                 WHERE m.conversation_id = ? AND m.id > ?
                 ORDER BY m.created_at ASC",
                [(int) $id, $lastId]
            );
        } else {
            $messages = $this->msgModel->getConversationMessages((int) $id);
        }

        $this->msgModel->markAsRead((int) $id, user_id());
        $messages = array_map(static function (array $m): array {
            $m['current_user_id'] = user_id();
            return $m;
        }, $messages);

        $this->json(['messages' => $messages]);
    }

    public function apiConversations(): void
    {
        $this->requireAuth();
        $conversations = $this->convModel->getUserConversations(user_id());
        $this->jsonSuccess(['items' => $conversations]);
    }

    public function apiSend(string $id): void
    {
        $this->requireAuth();
        $conversation = $this->convModel->getWithPartner((int) $id, user_id());
        if (!$conversation) {
            $this->jsonError('Чат не найден', 404, [], 'NOT_FOUND');
            return;
        }
        $data = $this->allInput();
        $body = trim((string) ($data['message'] ?? ''));
        if ($body === '') {
            $this->jsonError('Сообщение пустое', 422, ['message' => 'Введите текст сообщения'], 'VALIDATION_ERROR');
            return;
        }
        $msgId = $this->msgModel->create([
            'conversation_id' => (int) $id,
            'sender_id' => user_id(),
            'body' => $body,
        ]);
        $this->convModel->update((int) $id, ['last_message_at' => date('Y-m-d H:i:s')]);
        $this->jsonSuccess([
            'message' => [
                'id' => $msgId,
                'body' => $body,
                'sender_id' => user_id(),
                'created_at' => date('Y-m-d H:i:s'),
            ]
        ], 'Сообщение отправлено', 201);
    }
}
