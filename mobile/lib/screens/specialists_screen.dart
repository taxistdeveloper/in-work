import 'package:flutter/material.dart';

import '../api/app_api.dart';
import '../ui/components/components.dart';
import 'catalog_hire_screen.dart';

class SpecialistsScreen extends StatefulWidget {
  const SpecialistsScreen({
    super.key,
    required this.catalogApi,
    required this.ordersApi,
  });

  final CatalogApi catalogApi;
  final OrdersApi ordersApi;

  @override
  State<SpecialistsScreen> createState() => _SpecialistsScreenState();
}

class _SpecialistsScreenState extends State<SpecialistsScreen> {
  List<Map<String, dynamic>> _catalogCategories = [];
  String? _categorySlug;
  String _sort = 'score';
  Future<Map<String, dynamic>>? _future;

  static const _sortLabels = <String, String>{
    'score': 'Рейтинг системы',
    'rating': 'Оценка',
    'reviews': 'Отзывы',
    'experience': 'Стаж',
    'completed': 'Заказы',
    'reliability': 'Надёжность',
  };

  @override
  void initState() {
    super.initState();
    _loadCategories();
  }

  Future<void> _loadCategories() async {
    try {
      final all = await widget.catalogApi.fetchCategories();
      final catalog = all.where((c) => c['mode']?.toString() == 'catalog').toList();
      if (!mounted) return;
      setState(() {
        _catalogCategories = catalog;
        if (_categorySlug == null && catalog.isNotEmpty) {
          _categorySlug = catalog.first['slug']?.toString();
        }
        _refresh();
      });
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.toString())));
    }
  }

  void _refresh() {
    final slug = _categorySlug;
    if (slug == null || slug.isEmpty) {
      _future = Future.value({'specialists': <dynamic>[]});
      return;
    }
    _future = widget.catalogApi.fetchSpecialists(category: slug, sort: _sort);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Каталог специалистов')),
      body: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                if (_catalogCategories.isNotEmpty)
                  DropdownButtonFormField<String>(
                    value: _categorySlug,
                    decoration: const InputDecoration(
                      labelText: 'Категория',
                      border: OutlineInputBorder(),
                      isDense: true,
                    ),
                    items: _catalogCategories
                        .map(
                          (c) => DropdownMenuItem<String>(
                            value: c['slug']?.toString(),
                            child: Text(c['label']?.toString() ?? ''),
                          ),
                        )
                        .toList(),
                    onChanged: (v) {
                      setState(() {
                        _categorySlug = v;
                        _refresh();
                      });
                    },
                  ),
                const SizedBox(height: 12),
                SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    children: _sortLabels.entries.map((e) {
                      final selected = _sort == e.key;
                      return Padding(
                        padding: const EdgeInsets.only(right: 8),
                        child: FilterChip(
                          label: Text(e.value),
                          selected: selected,
                          onSelected: (_) {
                            setState(() {
                              _sort = e.key;
                              _refresh();
                            });
                          },
                        ),
                      );
                    }).toList(),
                  ),
                ),
              ],
            ),
          ),
          Expanded(
            child: FutureBuilder<Map<String, dynamic>>(
              future: _future,
              builder: (context, snapshot) {
                if (snapshot.connectionState == ConnectionState.waiting) {
                  return const Center(child: CircularProgressIndicator());
                }
                if (snapshot.hasError) {
                  return ErrorState(
                    message: snapshot.error.toString(),
                    onRetry: () => setState(_refresh),
                  );
                }
                final data = snapshot.data ?? {};
                final items = (data['specialists'] as List<dynamic>? ?? [])
                    .map((e) => Map<String, dynamic>.from(e as Map))
                    .toList();
                if (items.isEmpty) {
                  return const EmptyState(
                    title: 'Нет специалистов',
                    subtitle: 'Исполнители могут указать категории в профиле.',
                  );
                }
                return ListView.separated(
                  padding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
                  itemCount: items.length,
                  separatorBuilder: (_, __) => const SizedBox(height: 12),
                  itemBuilder: (context, i) {
                    final s = items[i];
                    final name = s['name']?.toString() ?? '';
                    final rating = (s['rating'] as num?)?.toDouble() ?? 0;
                    final reviews = (s['review_count'] as num?)?.toInt() ?? 0;
                    final done = (s['completed_orders'] as num?)?.toInt() ?? 0;
                    final tenure = (s['tenure_days'] as num?)?.toInt() ?? 0;
                    final score = (s['platform_score'] as num?)?.toDouble() ?? 0;
                    return AppCard(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(name, style: Theme.of(context).textTheme.titleMedium),
                                    const SizedBox(height: 4),
                                    Text(
                                      '★ ${rating.toStringAsFixed(1)} · Отзывов: $reviews · Заказов: $done · Стаж: ${tenure} дн.',
                                      style: Theme.of(context).textTheme.bodySmall?.copyWith(color: Colors.grey.shade700),
                                    ),
                                    const SizedBox(height: 4),
                                    Text(
                                      'Рейтинг inWork: ${score.toStringAsFixed(1)}',
                                      style: Theme.of(context).textTheme.labelSmall?.copyWith(
                                            color: Theme.of(context).colorScheme.primary,
                                            fontWeight: FontWeight.w600,
                                          ),
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                          if ((s['bio']?.toString() ?? '').isNotEmpty) ...[
                            const SizedBox(height: 8),
                            Text(
                              s['bio'].toString(),
                              maxLines: 3,
                              overflow: TextOverflow.ellipsis,
                              style: Theme.of(context).textTheme.bodySmall,
                            ),
                          ],
                          const SizedBox(height: 12),
                          Align(
                            alignment: Alignment.centerRight,
                            child: FilledButton(
                              onPressed: _categorySlug == null
                                  ? null
                                  : () {
                                      Navigator.of(context).push(
                                        MaterialPageRoute<void>(
                                          builder: (_) => CatalogHireScreen(
                                            ordersApi: widget.ordersApi,
                                            categorySlug: _categorySlug!,
                                            categoryLabel: _catalogCategories
                                                    .firstWhere(
                                                      (c) => c['slug'] == _categorySlug,
                                                      orElse: () => {},
                                                    )['label']
                                                    ?.toString() ??
                                                '',
                                            specialistId: (s['id'] as num).toInt(),
                                            specialistName: name,
                                          ),
                                        ),
                                      );
                                    },
                              child: const Text('Нанять'),
                            ),
                          ),
                        ],
                      ),
                    );
                  },
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}
