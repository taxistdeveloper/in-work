import 'package:flutter/material.dart';

import '../api/app_api.dart';
import '../ui/components/components.dart';
import 'help_and_legal_screens.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key, required this.api, this.catalogApi});
  final ProfileApi api;
  final CatalogApi? catalogApi;

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final nameCtrl = TextEditingController();
  final bioCtrl = TextEditingController();
  late Future<Map<String, dynamic>> _profileFuture;
  List<Map<String, dynamic>> _catalogCats = [];
  final Set<String> _selectedSpecs = {};

  @override
  void initState() {
    super.initState();
    _profileFuture = _load();
  }

  Future<Map<String, dynamic>> _load() async {
    final p = await widget.api.me();
    nameCtrl.text = p['name']?.toString() ?? '';
    bioCtrl.text = p['bio']?.toString() ?? '';
    _catalogCats = [];
    _selectedSpecs.clear();
    if (p['role']?.toString() == 'freelancer' && widget.catalogApi != null) {
      try {
        final all = await widget.catalogApi!.fetchCategories();
        _catalogCats = all.where((c) => c['mode']?.toString() == 'catalog').toList();
        final spec = p['specializations'];
        if (spec is List) {
          _selectedSpecs.addAll(spec.map((e) => e.toString()));
        }
      } catch (_) {}
    }
    return p;
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<Map<String, dynamic>>(
      future: _profileFuture,
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return const Center(child: CircularProgressIndicator());
        }
        if (snapshot.hasError) {
          return ErrorState(
            message: snapshot.error.toString(),
            onRetry: () => setState(() => _profileFuture = _load()),
          );
        }
        final p = snapshot.data ?? {};
        final isFreelancer = p['role']?.toString() == 'freelancer';

        return ListView(
          padding: const EdgeInsets.all(16),
          children: [
            AppCard(
              child: Row(
                children: [
                  Container(
                    width: 56,
                    height: 56,
                    decoration: BoxDecoration(
                      gradient: const LinearGradient(
                        colors: [Color(0xFF34D399), Color(0xFF059669)],
                      ),
                      borderRadius: BorderRadius.circular(16),
                    ),
                    alignment: Alignment.center,
                    child: Text(
                      (p['name']?.toString() ?? '?').substring(0, 1).toUpperCase(),
                      style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.bold),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(p['name']?.toString() ?? '-', style: Theme.of(context).textTheme.titleMedium),
                      Text(p['email']?.toString() ?? '', style: Theme.of(context).textTheme.bodySmall),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),
            if (isFreelancer && _catalogCats.isNotEmpty) ...[
              AppCard(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('Каталожные специализации', style: TextStyle(fontWeight: FontWeight.w600)),
                    const SizedBox(height: 8),
                    Text(
                      'Отметьте направления, в которых вас видят заказчики в каталоге.',
                      style: Theme.of(context).textTheme.bodySmall?.copyWith(color: Colors.grey.shade700),
                    ),
                    const SizedBox(height: 12),
                    Wrap(
                      spacing: 8,
                      runSpacing: 8,
                      children: _catalogCats.map((c) {
                        final slug = c['slug']?.toString() ?? '';
                        final label = c['label']?.toString() ?? slug;
                        final sel = _selectedSpecs.contains(slug);
                        return FilterChip(
                          label: Text(label),
                          selected: sel,
                          onSelected: (v) {
                            setState(() {
                              if (v) {
                                _selectedSpecs.add(slug);
                              } else {
                                _selectedSpecs.remove(slug);
                              }
                            });
                          },
                        );
                      }).toList(),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 16),
            ],
            AppCard(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('О себе', style: TextStyle(fontWeight: FontWeight.w600)),
                  const SizedBox(height: 12),
                  TextField(controller: nameCtrl, decoration: const InputDecoration(labelText: 'Имя')),
                  const SizedBox(height: 8),
                  TextField(
                    controller: bioCtrl,
                    decoration: const InputDecoration(labelText: 'О себе'),
                    maxLines: 3,
                  ),
                  const SizedBox(height: 12),
                  PrimaryButton(
                    fullWidth: true,
                    onPressed: () async {
                      await widget.api.update(
                        nameCtrl.text,
                        bioCtrl.text,
                        specializations: isFreelancer ? _selectedSpecs.toList() : null,
                      );
                      if (!mounted) return;
                      setState(() => _profileFuture = _load());
                      if (!mounted) return;
                      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Профиль обновлён')));
                    },
                    child: const Text('Сохранить изменения'),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),
            AppCard(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('Помощь и документы', style: TextStyle(fontWeight: FontWeight.w600)),
                  const SizedBox(height: 4),
                  ListTile(
                    contentPadding: EdgeInsets.zero,
                    leading: const Icon(Icons.help_outline),
                    title: const Text('Центр помощи'),
                    trailing: const Icon(Icons.chevron_right),
                    onTap: () => Navigator.of(context).push(
                      MaterialPageRoute<void>(builder: (_) => const HelpCenterScreen()),
                    ),
                  ),
                  const Divider(height: 1),
                  ListTile(
                    contentPadding: EdgeInsets.zero,
                    leading: const Icon(Icons.privacy_tip_outlined),
                    title: const Text('Политика конфиденциальности'),
                    trailing: const Icon(Icons.chevron_right),
                    onTap: () => Navigator.of(context).push(
                      MaterialPageRoute<void>(builder: (_) => const PrivacyPolicyScreen()),
                    ),
                  ),
                  const Divider(height: 1),
                  ListTile(
                    contentPadding: EdgeInsets.zero,
                    leading: const Icon(Icons.description_outlined),
                    title: const Text('Условия использования'),
                    trailing: const Icon(Icons.chevron_right),
                    onTap: () => Navigator.of(context).push(
                      MaterialPageRoute<void>(builder: (_) => const TermsOfUseScreen()),
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
