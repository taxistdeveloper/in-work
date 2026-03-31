import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

import '../api/app_api.dart';
import '../ui/components/components.dart';

class OrderFormScreen extends StatefulWidget {
  const OrderFormScreen({
    super.key,
    required this.api,
    this.orderId,
  });

  final OrdersApi api;
  final int? orderId;

  @override
  State<OrderFormScreen> createState() => _OrderFormScreenState();
}

class _OrderFormScreenState extends State<OrderFormScreen> {
  final titleCtrl = TextEditingController();
  final descCtrl = TextEditingController();
  final budgetCtrl = TextEditingController();
  final deadlineCtrl = TextEditingController();
  String category = 'other';
  bool categoryTouched = false;
  bool saving = false;
  bool loading = false;
  String? titleError;
  String? descError;
  String? budgetError;
  String? deadlineError;

  bool get isEdit => widget.orderId != null;
  static const categoryItems = <DropdownMenuItem<String>>[
    DropdownMenuItem(value: 'web-development', child: Text('Веб-разработка')),
    DropdownMenuItem(value: 'mobile-development', child: Text('Мобильная разработка')),
    DropdownMenuItem(value: 'design', child: Text('Дизайн и креатив')),
    DropdownMenuItem(value: 'writing', child: Text('Тексты и переводы')),
    DropdownMenuItem(value: 'marketing', child: Text('Маркетинг')),
    DropdownMenuItem(value: 'video', child: Text('Видео и анимация')),
    DropdownMenuItem(value: 'music', child: Text('Музыка и аудио')),
    DropdownMenuItem(value: 'data', child: Text('Данные и аналитика')),
    DropdownMenuItem(value: 'admin', child: Text('Администрирование')),
    DropdownMenuItem(value: 'other', child: Text('Другое')),
  ];

  @override
  void initState() {
    super.initState();
    if (isEdit) {
      _loadOrder();
    }
  }

  Future<void> _loadOrder() async {
    setState(() => loading = true);
    try {
      final data = await widget.api.fetchOrderDetail(widget.orderId!);
      final order = data['order'] as Map<String, dynamic>? ?? {};
      titleCtrl.text = order['title']?.toString() ?? '';
      descCtrl.text = order['description']?.toString() ?? '';
      budgetCtrl.text = (order['budget'] ?? '').toString();
      deadlineCtrl.text = order['deadline']?.toString() ?? '';
      category = order['category']?.toString() ?? 'other';
    } finally {
      if (mounted) setState(() => loading = false);
    }
  }

  Future<void> _submit() async {
    if (!_validateForm()) return;
    setState(() => saving = true);
    try {
      final budget = double.tryParse(budgetCtrl.text) ?? 0;
      if (isEdit) {
        await widget.api.updateOrder(
          widget.orderId!,
          title: titleCtrl.text,
          description: descCtrl.text,
          category: category,
          budget: budget,
          deadline: deadlineCtrl.text,
        );
      } else {
        await widget.api.createOrder(
          title: titleCtrl.text,
          description: descCtrl.text,
          category: category,
          budget: budget,
          deadline: deadlineCtrl.text,
        );
      }
      if (!mounted) return;
      Navigator.of(context).pop(true);
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.toString())));
    } finally {
      if (mounted) setState(() => saving = false);
    }
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
      deadlineError = null;
      setState(() {});
    }
  }

  bool _validateForm() {
    final budget = double.tryParse(budgetCtrl.text.replaceAll(' ', '')) ?? 0;
    titleError = null;
    descError = null;
    budgetError = null;
    deadlineError = null;

    if (titleCtrl.text.trim().length < 5) {
      titleError = 'Минимум 5 символов';
    }
    if (descCtrl.text.trim().length < 20) {
      descError = 'Минимум 20 символов';
    }
    if (budget < 5) {
      budgetError = 'Минимум 5 ₸';
    }
    final picked = DateTime.tryParse(deadlineCtrl.text.trim());
    if (picked == null) {
      deadlineError = 'Выберите дедлайн';
    } else {
      final minDate = DateTime.now().add(const Duration(days: 1));
      if (picked.isBefore(DateTime(minDate.year, minDate.month, minDate.day))) {
        deadlineError = 'Дедлайн не раньше завтрашнего дня';
      }
    }

    setState(() {});
    return titleError == null &&
        descError == null &&
        budgetError == null &&
        deadlineError == null;
  }

  void _autoSuggestCategory(String title) {
    if (categoryTouched) return;
    final t = title.toLowerCase();
    if (t.contains('сайт') || t.contains('web') || t.contains('лендинг')) {
      category = 'web-development';
    } else if (t.contains('приложен') || t.contains('android') || t.contains('ios')) {
      category = 'mobile-development';
    } else if (t.contains('дизайн') || t.contains('логотип') || t.contains('баннер')) {
      category = 'design';
    } else if (t.contains('текст') || t.contains('перевод') || t.contains('статья')) {
      category = 'writing';
    } else if (t.contains('реклам') || t.contains('маркетинг') || t.contains('seo')) {
      category = 'marketing';
    } else if (t.contains('видео') || t.contains('монтаж') || t.contains('анимац')) {
      category = 'video';
    } else if (t.contains('музык') || t.contains('аудио') || t.contains('озвуч')) {
      category = 'music';
    } else if (t.contains('данн') || t.contains('аналит')) {
      category = 'data';
    } else if (t.contains('админ') || t.contains('настройк') || t.contains('сервер')) {
      category = 'admin';
    } else {
      category = 'other';
    }
    setState(() {});
  }

  String _localizedDeadlineText() {
    final date = DateTime.tryParse(deadlineCtrl.text);
    if (date == null) return 'Дата не выбрана';
    return DateFormat('d MMMM y', 'ru').format(date);
  }

  @override
  Widget build(BuildContext context) {
    final titleStyle = Theme.of(context).textTheme.titleSmall?.copyWith(
          fontWeight: FontWeight.w700,
          color: Colors.grey.shade800,
        );

    return Scaffold(
      appBar: AppBar(title: Text(isEdit ? 'Редактировать заказ' : 'Создать заказ')),
      body: loading
          ? const Center(child: CircularProgressIndicator())
          : SafeArea(
              child: ListView(
                padding: const EdgeInsets.all(16),
                children: [
                  Padding(
                    padding: const EdgeInsets.only(bottom: 14),
                    child: Text(
                      isEdit ? 'Обновите параметры заказа' : 'Опишите задачу, чтобы исполнители могли сделать предложение',
                      style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                            color: Colors.grey.shade700,
                            height: 1.35,
                          ),
                    ),
                  ),
                  Container(
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(22),
                      gradient: LinearGradient(
                        colors: [Colors.white, Colors.grey.shade50],
                        begin: Alignment.topCenter,
                        end: Alignment.bottomCenter,
                      ),
                    ),
                    child: AppCard(
                      padding: const EdgeInsets.all(18),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('Основная информация', style: titleStyle),
                          const SizedBox(height: 10),
                          TextField(
                            controller: titleCtrl,
                            onChanged: (v) {
                              if (titleError != null) {
                                titleError = null;
                              }
                              _autoSuggestCategory(v);
                            },
                            decoration: InputDecoration(
                              hintText: 'Название заказа',
                              prefixIcon: const Icon(Icons.title_outlined),
                              errorText: titleError,
                            ),
                          ),
                          const SizedBox(height: 10),
                          TextField(
                            controller: descCtrl,
                            maxLines: 4,
                            onChanged: (_) {
                              if (descError != null) setState(() => descError = null);
                            },
                            decoration: InputDecoration(
                              hintText: 'Подробно опишите задачу',
                              prefixIcon: const Padding(
                                padding: EdgeInsets.only(bottom: 64),
                                child: Icon(Icons.description_outlined),
                              ),
                              errorText: descError,
                            ),
                          ),
                          const SizedBox(height: 14),
                          Text('Категория', style: titleStyle),
                          const SizedBox(height: 8),
                          DropdownButtonFormField<String>(
                            value: category,
                            isExpanded: true,
                            items: categoryItems,
                            decoration: const InputDecoration(
                              hintText: 'Выберите категорию',
                              prefixIcon: Icon(Icons.grid_view_rounded),
                            ),
                            onChanged: (v) => setState(() {
                              category = v ?? 'other';
                              categoryTouched = true;
                            }),
                          ),
                          const SizedBox(height: 14),
                          Text('Бюджет', style: titleStyle),
                          const SizedBox(height: 8),
                          TextField(
                            controller: budgetCtrl,
                            keyboardType: TextInputType.number,
                            onChanged: (_) {
                              if (budgetError != null) setState(() => budgetError = null);
                            },
                            decoration: InputDecoration(
                              hintText: 'Введите сумму',
                              prefixIcon: const Icon(Icons.payments_outlined),
                              suffixText: '₸',
                              errorText: budgetError,
                            ),
                          ),
                          const SizedBox(height: 8),
                          Wrap(
                            spacing: 8,
                            runSpacing: 8,
                            children: [
                              _quickBudgetChip('20 000', '20000'),
                              _quickBudgetChip('50 000', '50000'),
                              _quickBudgetChip('100 000', '100000'),
                            ],
                          ),
                          const SizedBox(height: 14),
                          Text('Дедлайн', style: titleStyle),
                          const SizedBox(height: 8),
                          TextField(
                            controller: deadlineCtrl,
                            readOnly: true,
                            onTap: _pickDeadline,
                            decoration: InputDecoration(
                              hintText: 'Выберите дату',
                              helperText: _localizedDeadlineText(),
                              errorText: deadlineError,
                              prefixIcon: const Icon(Icons.event_outlined),
                              suffixIcon: IconButton(
                                onPressed: _pickDeadline,
                                icon: const Icon(Icons.calendar_today_outlined),
                              ),
                            ),
                          ),
                          const SizedBox(height: 16),
                          PrimaryButton(
                            fullWidth: true,
                            onPressed: saving ? null : _submit,
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Icon(isEdit ? Icons.save_outlined : Icons.add_circle_outline, size: 18),
                                const SizedBox(width: 8),
                                Text(isEdit ? 'Сохранить изменения' : 'Создать заказ'),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
    );
  }

  Widget _quickBudgetChip(String label, String value) {
    final active = budgetCtrl.text == value;
    return GestureDetector(
      onTap: () => setState(() => budgetCtrl.text = value),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
        decoration: BoxDecoration(
          color: active ? Colors.green.shade50 : Colors.grey.shade100,
          borderRadius: BorderRadius.circular(999),
          border: Border.all(color: active ? Colors.green : Colors.grey.shade300),
        ),
        child: Text(
          '$label ₸',
          style: TextStyle(
            color: active ? Colors.green.shade700 : Colors.grey.shade700,
            fontWeight: FontWeight.w600,
          ),
        ),
      ),
    );
  }
}

