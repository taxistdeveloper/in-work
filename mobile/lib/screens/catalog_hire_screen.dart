import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

import '../api/app_api.dart';
import '../ui/components/components.dart';
import 'order_detail_screen.dart';

class CatalogHireScreen extends StatefulWidget {
  const CatalogHireScreen({
    super.key,
    required this.ordersApi,
    required this.categorySlug,
    required this.categoryLabel,
    required this.specialistId,
    required this.specialistName,
  });

  final OrdersApi ordersApi;
  final String categorySlug;
  final String categoryLabel;
  final int specialistId;
  final String specialistName;

  @override
  State<CatalogHireScreen> createState() => _CatalogHireScreenState();
}

class _CatalogHireScreenState extends State<CatalogHireScreen> {
  final titleCtrl = TextEditingController();
  final descCtrl = TextEditingController();
  final budgetCtrl = TextEditingController();
  final deadlineCtrl = TextEditingController();
  bool saving = false;

  @override
  void initState() {
    super.initState();
    final d = DateTime.now().add(const Duration(days: 7));
    deadlineCtrl.text = DateFormat('yyyy-MM-dd').format(d);
  }

  @override
  void dispose() {
    titleCtrl.dispose();
    descCtrl.dispose();
    budgetCtrl.dispose();
    deadlineCtrl.dispose();
    super.dispose();
  }

  Future<void> _pickDeadline() async {
    final initial = DateTime.tryParse(deadlineCtrl.text) ?? DateTime.now().add(const Duration(days: 1));
    final picked = await showDatePicker(
      context: context,
      initialDate: initial,
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 365 * 3)),
    );
    if (picked != null) {
      deadlineCtrl.text = DateFormat('yyyy-MM-dd').format(picked);
      setState(() {});
    }
  }

  Future<void> _submit() async {
    final title = titleCtrl.text.trim();
    final desc = descCtrl.text.trim();
    final budget = double.tryParse(budgetCtrl.text.replaceAll(',', '.')) ?? 0;
    if (title.length < 5) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Название минимум 5 символов')));
      return;
    }
    if (desc.length < 20) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Описание минимум 20 символов')));
      return;
    }
    if (budget < 100) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Бюджет для каталога от 100 ₸')));
      return;
    }
    setState(() => saving = true);
    try {
      final data = await widget.ordersApi.createOrder(
        title: title,
        description: desc,
        category: widget.categorySlug,
        budget: budget,
        deadline: deadlineCtrl.text.trim(),
        freelancerId: widget.specialistId,
      );
      if (!mounted) return;
      final order = data['order'] as Map<String, dynamic>? ?? {};
      final id = (order['id'] as num?)?.toInt();
      if (id != null) {
        await Navigator.of(context).pushReplacement(
          MaterialPageRoute<void>(
            builder: (_) => OrderDetailScreen(orderId: id, api: widget.ordersApi),
          ),
        );
      } else {
        Navigator.of(context).pop(true);
      }
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.toString())));
    } finally {
      if (mounted) setState(() => saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Найм из каталога')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          AppCard(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Исполнитель: ${widget.specialistName}', style: Theme.of(context).textTheme.titleSmall),
                const SizedBox(height: 4),
                Text('Категория: ${widget.categoryLabel}', style: Theme.of(context).textTheme.bodySmall),
              ],
            ),
          ),
          const SizedBox(height: 16),
          TextField(
            controller: titleCtrl,
            decoration: const InputDecoration(labelText: 'Название задачи', border: OutlineInputBorder()),
          ),
          const SizedBox(height: 12),
          TextField(
            controller: descCtrl,
            maxLines: 4,
            decoration: const InputDecoration(labelText: 'Описание', border: OutlineInputBorder()),
          ),
          const SizedBox(height: 12),
          TextField(
            controller: budgetCtrl,
            keyboardType: TextInputType.number,
            decoration: const InputDecoration(
              labelText: 'Бюджет (₸), мин. 100',
              border: OutlineInputBorder(),
            ),
          ),
          const SizedBox(height: 12),
          TextField(
            controller: deadlineCtrl,
            readOnly: true,
            onTap: _pickDeadline,
            decoration: InputDecoration(
              labelText: 'Дедлайн',
              border: const OutlineInputBorder(),
              suffixIcon: IconButton(icon: const Icon(Icons.calendar_today), onPressed: _pickDeadline),
            ),
          ),
          const SizedBox(height: 24),
          PrimaryButton(
            fullWidth: true,
            onPressed: saving ? null : _submit,
            child: saving ? const SizedBox(height: 22, width: 22, child: CircularProgressIndicator(strokeWidth: 2)) : const Text('Создать заказ и зарезервировать оплату'),
          ),
        ],
      ),
    );
  }
}
