import 'package:flutter/material.dart';

import '../api/app_api.dart';
import '../ui/components/components.dart';
import 'chat_conversation_screen.dart';

class ChatListScreen extends StatelessWidget {
  const ChatListScreen({super.key, required this.api});
  final ChatApi api;

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<List<Map<String, dynamic>>>(
      future: api.conversations(),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return const Center(child: CircularProgressIndicator());
        }
        if (snapshot.hasError) {
          return ErrorState(message: snapshot.error.toString(), onRetry: () => (context as Element).markNeedsBuild());
        }
        final chats = snapshot.data ?? [];
        if (chats.isEmpty) {
          return const EmptyState(title: 'Нет сообщений', subtitle: 'Начните общение, откликаясь на заказы');
        }
        return ListView.separated(
          padding: const EdgeInsets.all(16),
          itemCount: chats.length,
          separatorBuilder: (_, __) => const SizedBox(height: 8),
          itemBuilder: (_, i) {
            final c = chats[i];
            return InkWell(
              borderRadius: BorderRadius.circular(20),
              onTap: () {
                final id = int.tryParse(c['id'].toString()) ?? 0;
                if (id <= 0) return;
                Navigator.of(context).push(
                  MaterialPageRoute(
                    builder: (_) => ChatConversationScreen(
                      api: api,
                      conversationId: id,
                      partnerName: c['partner_name']?.toString() ?? 'Чат',
                    ),
                  ),
                );
              },
              child: AppCard(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                child: Row(
                  children: [
                    CircleAvatar(
                      backgroundColor: Colors.green.shade100,
                      child: Text(
                        (c['partner_name']?.toString() ?? '?').substring(0, 1).toUpperCase(),
                        style: const TextStyle(color: Colors.green),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(c['partner_name']?.toString() ?? '-', style: Theme.of(context).textTheme.titleSmall),
                          if (c['order_title'] != null)
                            Text(
                              c['order_title'].toString(),
                              style: Theme.of(context).textTheme.bodySmall?.copyWith(color: Colors.grey.shade600),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                          const SizedBox(height: 4),
                          Text(
                            c['last_message']?.toString() ?? '',
                            style: Theme.of(context).textTheme.bodySmall?.copyWith(color: Colors.grey.shade600),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            );
          },
        );
      },
    );
  }
}

