import 'package:flutter/material.dart';

import '../api/app_api.dart';
import '../models/order.dart';
import '../ui/components/components.dart';
import 'order_form_screen.dart';

class MyOrdersScreen extends StatefulWidget {
  const MyOrdersScreen({super.key, required this.api});
  final OrdersApi api;

  @override
  State<MyOrdersScreen> createState() => _MyOrdersScreenState();
}

class _MyOrdersScreenState extends State<MyOrdersScreen> {
  late Future<List<OrderModel>> _future;

  @override
  void initState() {
    super.initState();
    _future = widget.api.fetchMyOrders();
  }

  void _reload() {
    setState(() {
      _future = widget.api.fetchMyOrders();
    });
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<List<OrderModel>>(
      future: _future,
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return const Center(child: CircularProgressIndicator());
        }
        if (snapshot.hasError) {
          return ErrorState(message: snapshot.error.toString(), onRetry: _reload);
        }
        final items = snapshot.data ?? [];
        return ListView.builder(
          padding: const EdgeInsets.all(16),
          itemCount: items.isEmpty ? 2 : items.length + 1,
          itemBuilder: (_, i) {
            if (i == 0) {
              return Padding(
                padding: const EdgeInsets.only(bottom: 12),
                child: PrimaryButton(
                  fullWidth: true,
                  onPressed: () async {
                    final created = await Navigator.of(context).push<bool>(
                      MaterialPageRoute(
                        builder: (_) => OrderFormScreen(api: widget.api),
                      ),
                    );
                    if (created == true) _reload();
                  },
                  child: const Text('Создать заказ'),
                ),
              );
            }
            if (items.isEmpty) {
              return const EmptyState(title: 'Пока нет заказов');
            }
            final o = items[i - 1];
            return Padding(
              padding: const EdgeInsets.only(bottom: 12),
              child: AppCard(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Expanded(
                          child: Text(o.title, style: Theme.of(context).textTheme.titleMedium),
                        ),
                        const SizedBox(width: 8),
                        StatusChip(
                          label: o.status,
                          color: o.status == 'open'
                              ? Colors.green
                              : o.status == 'in_progress'
                                  ? Colors.blue
                                  : o.status == 'completed'
                                      ? Colors.grey
                                      : Colors.red,
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                    MoneyText(o.budget),
                    const SizedBox(height: 4),
                    Text('Дедлайн: ${o.deadline}', style: Theme.of(context).textTheme.bodySmall),
                    const SizedBox(height: 12),
                    Row(
                      children: [
                        Expanded(
                          child: SecondaryButton(
                            onPressed: o.status == 'open'
                                ? () async {
                                    final updated = await Navigator.of(context).push<bool>(
                                      MaterialPageRoute(
                                        builder: (_) => OrderFormScreen(api: widget.api, orderId: o.id),
                                      ),
                                    );
                                    if (updated == true) _reload();
                                  }
                                : null,
                            child: const Text('Редактировать'),
                          ),
                        ),
                        const SizedBox(width: 8),
                        Expanded(
                          child: SecondaryButton(
                            onPressed: o.status == 'open'
                                ? () async {
                                    try {
                                      await widget.api.deleteOrder(o.id);
                                      _reload();
                                    } catch (e) {
                                      if (!context.mounted) return;
                                      ScaffoldMessenger.of(context).showSnackBar(
                                        SnackBar(content: Text(e.toString())),
                                      );
                                    }
                                  }
                                : null,
                            child: const Text('Удалить'),
                          ),
                        ),
                      ],
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

