import 'package:flutter/material.dart';

import '../api/app_api.dart';
import '../ui/components/components.dart';

class BalanceScreen extends StatefulWidget {
  const BalanceScreen({super.key, required this.api});
  final BalanceApi api;

  @override
  State<BalanceScreen> createState() => _BalanceScreenState();
}

class _BalanceScreenState extends State<BalanceScreen> {
  final amountCtrl = TextEditingController();

  Future<Map<String, dynamic>> _load() => widget.api.fetch();

  double _toDouble(dynamic v) {
    if (v is num) return v.toDouble();
    if (v is String) return double.tryParse(v.replaceAll(' ', '')) ?? 0;
    return 0;
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<Map<String, dynamic>>(
      future: _load(),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return const Center(child: CircularProgressIndicator());
        }
        if (snapshot.hasError) {
          return ErrorState(message: snapshot.error.toString(), onRetry: () => setState(() {}));
        }
        final data = snapshot.data ?? {};
        final tx = data['transactions'] as List<dynamic>? ?? [];
        final balance = _toDouble(data['balance']);
        final held = _toDouble(data['held']);
        return ListView(
          padding: const EdgeInsets.all(16),
          children: [
            Row(
              children: [
                Expanded(
                  child: AppCard(
                    padding: const EdgeInsets.all(20),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text('Доступный баланс', style: TextStyle(fontSize: 12, color: Colors.grey)),
                        const SizedBox(height: 4),
                        MoneyText(balance, large: true),
                        if (held > 0) ...[
                          const SizedBox(height: 8),
                          Text(
                            'В эскроу: ${held.toStringAsFixed(0)} ₸',
                            style: const TextStyle(fontSize: 12, color: Colors.white),
                          ),
                        ],
                      ],
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            AppCard(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('Операции с балансом', style: TextStyle(fontWeight: FontWeight.w600)),
                  const SizedBox(height: 12),
                  TextField(
                    controller: amountCtrl,
                    keyboardType: TextInputType.number,
                    decoration: const InputDecoration(labelText: 'Сумма, ₸'),
                  ),
                  const SizedBox(height: 12),
                  Row(
                    children: [
                      Expanded(
                        child: PrimaryButton(
                          onPressed: () async {
                            await widget.api.deposit(double.tryParse(amountCtrl.text) ?? 0);
                            if (mounted) setState(() {});
                          },
                          child: const Text('Пополнить'),
                        ),
                      ),
                      const SizedBox(width: 8),
                      Expanded(
                        child: SecondaryButton(
                          onPressed: () async {
                            await widget.api.withdraw(double.tryParse(amountCtrl.text) ?? 0);
                            if (mounted) setState(() {});
                          },
                          child: const Text('Вывести'),
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
                  const Text('История транзакций', style: TextStyle(fontWeight: FontWeight.w600)),
                  const SizedBox(height: 8),
                  if (tx.isEmpty)
                    const EmptyState(title: 'Пока нет транзакций')
                  else
                    ...tx.map(
                      (e) => ListTile(
                        dense: true,
                        contentPadding: EdgeInsets.zero,
                        title: Text(e['description']?.toString() ?? '—'),
                        subtitle: Text(e['type']?.toString() ?? ''),
                        trailing: Text(
                          _toDouble(e['amount']).toStringAsFixed(0),
                          style: TextStyle(
                            color: _toDouble(e['amount']) >= 0 ? Colors.green : Colors.red,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ),
                    ),
                ],
              ),
            ),
          ],
        );
      },
    );
  }
}

