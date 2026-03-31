import 'package:flutter/material.dart';

import '../api/app_api.dart';
import '../models/order.dart';

class MyOrdersScreen extends StatelessWidget {
  const MyOrdersScreen({super.key, required this.api});
  final OrdersApi api;

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<List<OrderModel>>(
      future: api.fetchMyOrders(),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return const Center(child: CircularProgressIndicator());
        }
        if (snapshot.hasError) return Center(child: Text(snapshot.error.toString()));
        final items = snapshot.data ?? [];
        if (items.isEmpty) return const Center(child: Text('Заказов пока нет'));
        return ListView.builder(
          itemCount: items.length,
          itemBuilder: (_, i) => ListTile(
            title: Text(items[i].title),
            subtitle: Text('Статус: ${items[i].status}'),
          ),
        );
      },
    );
  }
}

class ChatListScreen extends StatelessWidget {
  const ChatListScreen({super.key, required this.api});
  final ChatApi api;

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<List<Map<String, dynamic>>>(
      future: api.conversations(),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) return const Center(child: CircularProgressIndicator());
        if (snapshot.hasError) return Center(child: Text(snapshot.error.toString()));
        final chats = snapshot.data ?? [];
        if (chats.isEmpty) return const Center(child: Text('Нет чатов'));
        return ListView(
          children: chats
              .map(
                (c) => ListTile(
                  title: Text(c['partner_name']?.toString() ?? 'Чат'),
                  subtitle: Text(c['last_message']?.toString() ?? ''),
                ),
              )
              .toList(),
        );
      },
    );
  }
}

class BalanceScreen extends StatefulWidget {
  const BalanceScreen({super.key, required this.api});
  final BalanceApi api;

  @override
  State<BalanceScreen> createState() => _BalanceScreenState();
}

class _BalanceScreenState extends State<BalanceScreen> {
  final amountCtrl = TextEditingController();

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<Map<String, dynamic>>(
      future: widget.api.fetch(),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) return const Center(child: CircularProgressIndicator());
        if (snapshot.hasError) return Center(child: Text(snapshot.error.toString()));
        final data = snapshot.data ?? {};
        final tx = data['transactions'] as List<dynamic>? ?? [];
        return ListView(
          padding: const EdgeInsets.all(16),
          children: [
            Text('Баланс: ${data['balance'] ?? 0} ₸', style: Theme.of(context).textTheme.titleLarge),
            Text('В эскроу: ${data['held'] ?? 0} ₸'),
            TextField(controller: amountCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Сумма')),
            Row(
              children: [
                Expanded(
                  child: FilledButton(
                    onPressed: () async {
                      await widget.api.deposit(double.tryParse(amountCtrl.text) ?? 0);
                      if (mounted) setState(() {});
                    },
                    child: const Text('Пополнить'),
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: FilledButton.tonal(
                    onPressed: () async {
                      await widget.api.withdraw(double.tryParse(amountCtrl.text) ?? 0);
                      if (mounted) setState(() {});
                    },
                    child: const Text('Вывести'),
                  ),
                ),
              ],
            ),
            const Divider(height: 24),
            ...tx.map((e) => ListTile(
                  title: Text(e['type'].toString()),
                  subtitle: Text(e['description'].toString()),
                  trailing: Text('${e['amount']}'),
                )),
          ],
        );
      },
    );
  }
}

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key, required this.api});
  final ProfileApi api;

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final nameCtrl = TextEditingController();
  final bioCtrl = TextEditingController();

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<Map<String, dynamic>>(
      future: widget.api.me(),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) return const Center(child: CircularProgressIndicator());
        if (snapshot.hasError) return Center(child: Text(snapshot.error.toString()));
        final p = snapshot.data ?? {};
        nameCtrl.text = p['name']?.toString() ?? '';
        bioCtrl.text = p['bio']?.toString() ?? '';
        return ListView(
          padding: const EdgeInsets.all(16),
          children: [
            TextField(controller: nameCtrl, decoration: const InputDecoration(labelText: 'Имя')),
            TextField(controller: bioCtrl, decoration: const InputDecoration(labelText: 'О себе')),
            const SizedBox(height: 12),
            FilledButton(
              onPressed: () async {
                await widget.api.update(nameCtrl.text, bioCtrl.text);
                if (!mounted) return;
                ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Профиль обновлён')));
              },
              child: const Text('Сохранить'),
            ),
          ],
        );
      },
    );
  }
}

