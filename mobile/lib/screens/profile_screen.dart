import 'package:flutter/material.dart';

import '../api/app_api.dart';
import '../ui/components/components.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key, required this.api});
  final ProfileApi api;

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final nameCtrl = TextEditingController();
  final bioCtrl = TextEditingController();

  Future<Map<String, dynamic>> _load() async {
    final p = await widget.api.me();
    nameCtrl.text = p['name']?.toString() ?? '';
    bioCtrl.text = p['bio']?.toString() ?? '';
    return p;
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
        final p = snapshot.data ?? {};
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
                      await widget.api.update(nameCtrl.text, bioCtrl.text);
                      if (!mounted) return;
                      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Профиль обновлён')));
                    },
                    child: const Text('Сохранить изменения'),
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

