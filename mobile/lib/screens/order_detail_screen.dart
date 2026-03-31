import 'package:flutter/material.dart';

import '../api/app_api.dart';
import '../ui/components/components.dart';

class OrderDetailScreen extends StatefulWidget {
  const OrderDetailScreen({super.key, required this.orderId, required this.api});
  final int orderId;
  final OrdersApi api;

  @override
  State<OrderDetailScreen> createState() => _OrderDetailScreenState();
}

class _OrderDetailScreenState extends State<OrderDetailScreen> {
  final amountCtrl = TextEditingController();
  final customAmountCtrl = TextEditingController();
  final messageCtrl = TextEditingController();
  bool busy = false;
  bool customPriceMode = false;

  Future<Map<String, dynamic>> _load() => widget.api.fetchOrderDetail(widget.orderId);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Заказ')),
      body: FutureBuilder<Map<String, dynamic>>(
        future: _load(),
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }
          if (snapshot.hasError) {
            return ErrorState(message: snapshot.error.toString(), onRetry: () => setState(() {}));
          }
          final data = snapshot.data ?? {};
          final order = data['order'] as Map<String, dynamic>? ?? {};
          final bids = data['bids'] as List<dynamic>? ?? [];
          final clientReviews = data['client_reviews'] as List<dynamic>? ?? [];
          final budget = (order['budget'] as num?)?.toDouble() ?? 0;

          if (amountCtrl.text.isEmpty && budget > 0) {
            amountCtrl.text = budget.toStringAsFixed(0);
          }

          final status = (order['status'] ?? 'open') as String;
          Color statusColor;
          String statusLabel;
          switch (status) {
            case 'in_progress':
              statusColor = Colors.blue;
              statusLabel = 'В работе';
              break;
            case 'completed':
              statusColor = Colors.grey;
              statusLabel = 'Завершён';
              break;
            case 'cancelled':
              statusColor = Colors.red;
              statusLabel = 'Отменён';
              break;
            default:
              statusColor = Colors.green;
              statusLabel = 'Открыт';
          }

          return ListView(
            padding: const EdgeInsets.all(16),
            children: [
              AppCard(
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
                          child: Text(order['category']?.toString() ?? '', style: Theme.of(context).textTheme.labelSmall),
                        ),
                        StatusChip(label: statusLabel, color: statusColor),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Text(
                      order['title']?.toString() ?? '-',
                      style: Theme.of(context).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      order['description']?.toString() ?? '-',
                      style: Theme.of(context).textTheme.bodyMedium?.copyWith(color: Colors.grey.shade700),
                    ),
                    const SizedBox(height: 16),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        MoneyText((order['budget'] as num?)?.toDouble() ?? 0, large: true),
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.end,
                          children: [
                            Text('Дедлайн: ${order['deadline'] ?? '-'}', style: Theme.of(context).textTheme.bodySmall),
                            Text('Создан: ${order['created_at'] ?? '-'}', style: Theme.of(context).textTheme.bodySmall),
                          ],
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 16),
              AppCard(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Заказчик', style: Theme.of(context).textTheme.titleMedium),
                    const SizedBox(height: 10),
                    Row(
                      children: [
                        CircleAvatar(
                          backgroundColor: Colors.blue.shade100,
                          child: Text(
                            (order['client_name']?.toString().isNotEmpty ?? false)
                                ? order['client_name'].toString().substring(0, 1).toUpperCase()
                                : '?',
                            style: const TextStyle(color: Colors.blue),
                          ),
                        ),
                        const SizedBox(width: 10),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(order['client_name']?.toString() ?? 'Неизвестно'),
                              Text(
                                'Рейтинг: ${(order['client_rating'] ?? 0).toString()} • Заказов: ${(order['client_completed'] ?? 0).toString()}',
                                style: Theme.of(context).textTheme.bodySmall?.copyWith(color: Colors.grey.shade600),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 16),
              AppCard(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Предложить свою цену', style: Theme.of(context).textTheme.titleMedium),
                    const SizedBox(height: 8),
                    Text(
                      'Выберите цену или введите свою',
                      style: Theme.of(context).textTheme.bodySmall?.copyWith(color: Colors.grey.shade600),
                    ),
                    const SizedBox(height: 8),
                    GridView.count(
                      crossAxisCount: 2,
                      mainAxisSpacing: 8,
                      crossAxisSpacing: 8,
                      childAspectRatio: 2.2,
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      children: [
                        _priceTile(
                          amount: budget,
                          subtitle: 'Цена заказчика',
                          color: Colors.black87,
                          active: !customPriceMode &&
                              ((double.tryParse(amountCtrl.text) ?? 0).round() == budget.round()),
                          onTap: () {
                            setState(() {
                              customPriceMode = false;
                              amountCtrl.text = budget.toStringAsFixed(0);
                            });
                          },
                        ),
                        _priceTile(
                          amount: budget * 0.9,
                          subtitle: '-10%',
                          color: Colors.green,
                          active: !customPriceMode &&
                              ((double.tryParse(amountCtrl.text) ?? 0).round() == (budget * 0.9).round()),
                          onTap: () {
                            setState(() {
                              customPriceMode = false;
                              amountCtrl.text = (budget * 0.9).toStringAsFixed(0);
                            });
                          },
                        ),
                        _priceTile(
                          amount: budget * 1.1,
                          subtitle: '+10%',
                          color: Colors.orange,
                          active: !customPriceMode &&
                              ((double.tryParse(amountCtrl.text) ?? 0).round() == (budget * 1.1).round()),
                          onTap: () {
                            setState(() {
                              customPriceMode = false;
                              amountCtrl.text = (budget * 1.1).toStringAsFixed(0);
                            });
                          },
                        ),
                        InkWell(
                          onTap: () => setState(() => customPriceMode = !customPriceMode),
                          borderRadius: BorderRadius.circular(14),
                          child: Container(
                            decoration: BoxDecoration(
                              border: Border.all(
                                color: customPriceMode ? Colors.purple : Colors.grey.shade300,
                                width: customPriceMode ? 2 : 1.2,
                              ),
                              borderRadius: BorderRadius.circular(14),
                            ),
                            alignment: Alignment.center,
                            child: const Column(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Text('₸?', style: TextStyle(color: Colors.purple, fontWeight: FontWeight.bold, fontSize: 26)),
                                Text('Своя цена', style: TextStyle(color: Colors.purple)),
                              ],
                            ),
                          ),
                        ),
                      ],
                    ),
                    if (customPriceMode) ...[
                      const SizedBox(height: 8),
                      TextField(
                        controller: customAmountCtrl,
                        keyboardType: TextInputType.number,
                        decoration: const InputDecoration(labelText: 'Введите свою цену'),
                        onChanged: (v) {
                          final parsed = double.tryParse(v) ?? 0;
                          amountCtrl.text = parsed.toStringAsFixed(0);
                          setState(() {});
                        },
                      ),
                    ],
                    const SizedBox(height: 8),
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 14),
                      decoration: BoxDecoration(
                        color: Colors.grey.shade100,
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text(
                            'Ваше предложение: ',
                            style: Theme.of(context).textTheme.bodyMedium?.copyWith(color: Colors.grey.shade700),
                          ),
                          MoneyText(double.tryParse(amountCtrl.text) ?? 0),
                        ],
                      ),
                    ),
                    const SizedBox(height: 12),
                    TextField(
                      controller: messageCtrl,
                      decoration: const InputDecoration(hintText: 'Добавьте сообщение (необязательно)'),
                      maxLines: 3,
                    ),
                    const SizedBox(height: 12),
                    PrimaryButton(
                      fullWidth: true,
                      onPressed: busy
                          ? null
                          : () async {
                              setState(() => busy = true);
                              try {
                                await widget.api.sendBid(
                                  widget.orderId,
                                  double.tryParse(amountCtrl.text) ?? 0,
                                  messageCtrl.text,
                                );
                                if (!mounted) return;
                                ScaffoldMessenger.of(context)
                                    .showSnackBar(const SnackBar(content: Text('Отклик отправлен')));
                              } catch (e) {
                                if (!mounted) return;
                                ScaffoldMessenger.of(context)
                                    .showSnackBar(SnackBar(content: Text(e.toString())));
                              } finally {
                                if (mounted) setState(() => busy = false);
                              }
                            },
                      child: const Text('Отправить отклик'),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 16),
              AppCard(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Отклики (${bids.length})', style: Theme.of(context).textTheme.titleMedium),
                    const SizedBox(height: 8),
                    if (bids.isEmpty)
                      const EmptyState(title: 'Пока нет откликов')
                    else
                      ...bids.map(
                        (b) => ListTile(
                          contentPadding: EdgeInsets.zero,
                          title: Text('${b['name'] ?? 'Исполнитель'} — ${(b['amount'] as num).toDouble().toStringAsFixed(0)} ₸'),
                          subtitle: Text((b['message'] ?? '').toString()),
                        ),
                      ),
                  ],
                ),
              ),
              const SizedBox(height: 16),
              AppCard(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Отзывы о заказчике (${clientReviews.length})', style: Theme.of(context).textTheme.titleMedium),
                    const SizedBox(height: 8),
                    if (clientReviews.isEmpty)
                      const EmptyState(title: 'Пока нет отзывов')
                    else
                      ...clientReviews.map(
                        (r) => ListTile(
                          contentPadding: EdgeInsets.zero,
                          title: Text('${r['reviewer_name'] ?? 'Пользователь'} • ${r['rating'] ?? '-'} / 5'),
                          subtitle: Text((r['comment'] ?? 'Без комментария').toString()),
                        ),
                      ),
                  ],
                ),
              ),
            ],
          );
        },
      ),
    );
  }

  Widget _priceTile({
    required double amount,
    required String subtitle,
    required Color color,
    required bool active,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(14),
      child: Container(
        decoration: BoxDecoration(
          border: Border.all(
            color: active ? color : Colors.grey.shade300,
            width: active ? 2 : 1.2,
          ),
          borderRadius: BorderRadius.circular(14),
        ),
        alignment: Alignment.center,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              '${amount.toStringAsFixed(0)} ₸',
              style: TextStyle(color: color, fontWeight: FontWeight.bold, fontSize: 30 / 2),
            ),
            Text(subtitle, style: TextStyle(color: color)),
          ],
        ),
      ),
    );
  }
}


