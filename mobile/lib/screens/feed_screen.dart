import 'package:flutter/material.dart';

import '../api/app_api.dart';
import '../app_session.dart';
import '../models/order.dart';
import '../ui/components/components.dart';
import 'order_detail_screen.dart';
import 'specialists_screen.dart';

class FeedScreen extends StatefulWidget {
  const FeedScreen({
    super.key,
    required this.api,
    required this.session,
    required this.catalogApi,
  });
  final OrdersApi api;
  final AppSession session;
  final CatalogApi catalogApi;

  @override
  State<FeedScreen> createState() => _FeedScreenState();
}

class _FeedScreenState extends State<FeedScreen> {
  late Future<List<OrderModel>> _future;

  @override
  void initState() {
    super.initState();
    _future = widget.api.fetchOpenOrders();
  }

  @override
  Widget build(BuildContext context) {
    final role = widget.session.user?['role']?.toString();
    final isClient = role == 'client';

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 16, 16, 4),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('Лента заказов', style: Theme.of(context).textTheme.headlineSmall?.copyWith(fontWeight: FontWeight.bold)),
              const SizedBox(height: 4),
              Text(
                isClient ? 'Рыночные заказы (отклики) или найм из каталога' : 'Открытые проекты для исполнителей',
                style: Theme.of(context).textTheme.bodySmall?.copyWith(color: Colors.grey.shade600),
              ),
              if (isClient) ...[
                const SizedBox(height: 12),
                FilledButton.tonalIcon(
                  onPressed: () {
                    Navigator.of(context).push(
                      MaterialPageRoute<void>(
                        builder: (_) => SpecialistsScreen(
                          catalogApi: widget.catalogApi,
                          ordersApi: widget.api,
                        ),
                      ),
                    );
                  },
                  icon: const Icon(Icons.people_outline),
                  label: const Text('Каталог специалистов'),
                ),
              ],
            ],
          ),
        ),
        Expanded(
          child: FutureBuilder<List<OrderModel>>(
            future: _future,
            builder: (context, snapshot) {
              if (snapshot.connectionState == ConnectionState.waiting) {
                return const Center(child: CircularProgressIndicator());
              }

              if (snapshot.hasError) {
                return ErrorState(
                  message: snapshot.error.toString(),
                  onRetry: () {
                    setState(() {
                      _future = widget.api.fetchOpenOrders();
                    });
                  },
                );
              }

              final items = snapshot.data ?? const <OrderModel>[];

              if (items.isEmpty) {
                return const EmptyState(title: 'Пока нет открытых заказов', subtitle: 'Загляните позже или создайте свой заказ на сайте.');
              }

              return ListView.separated(
                padding: const EdgeInsets.fromLTRB(16, 8, 16, 16),
                itemCount: items.length,
                separatorBuilder: (_, __) => const SizedBox(height: 12),
                itemBuilder: (context, index) {
                  final order = items[index];
                  return GestureDetector(
                    onTap: () {
                      Navigator.of(context).push(
                        MaterialPageRoute(
                          builder: (_) => OrderDetailScreen(
                            orderId: order.id,
                            api: widget.api,
                          ),
                        ),
                      );
                    },
                    child: AppCard(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                decoration: BoxDecoration(
                                  color: Colors.grey.shade100,
                                  borderRadius: BorderRadius.circular(999),
                                ),
                                child: Text(order.category, style: Theme.of(context).textTheme.labelSmall),
                              ),
                              StatusChip(label: 'Открыт', color: Colors.green),
                            ],
                          ),
                          const SizedBox(height: 8),
                          Text(order.title, style: Theme.of(context).textTheme.titleMedium),
                          const SizedBox(height: 6),
                          Text(
                            order.description,
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                            style: Theme.of(context).textTheme.bodyMedium?.copyWith(color: Colors.grey.shade700),
                          ),
                          const SizedBox(height: 12),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              MoneyText(order.budget),
                              const Icon(Icons.chevron_right, color: Colors.grey),
                            ],
                          ),
                        ],
                      ),
                    ),
                  );
                },
              );
            },
          ),
        ),
      ],
    );
  }
}

