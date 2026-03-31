import 'package:flutter/material.dart';

import '../app_session.dart';
import '../ui/components/components.dart';

class AuthScreen extends StatefulWidget {
  const AuthScreen({super.key, required this.session});
  final AppSession session;

  @override
  State<AuthScreen> createState() => _AuthScreenState();
}

class _AuthScreenState extends State<AuthScreen> {
  bool registerMode = false;
  final email = TextEditingController();
  final password = TextEditingController();
  final name = TextEditingController();
  String role = 'freelancer';
  bool busy = false;

  Future<void> _submit() async {
    setState(() => busy = true);
    try {
      if (registerMode) {
        await widget.session.register(name.text, email.text, password.text, role);
      } else {
        await widget.session.login(email.text, password.text);
      }
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.toString())));
    } finally {
      if (mounted) setState(() => busy = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Scaffold(
      body: Center(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24),
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 420),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Container(
                  width: 56,
                  height: 56,
                  margin: const EdgeInsets.only(bottom: 16),
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(
                      colors: [Color(0xFF34D399), Color(0xFF059669)],
                    ),
                    borderRadius: BorderRadius.circular(16),
                  ),
                  alignment: Alignment.center,
                  child: const Text('in', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 18)),
                ),
                Text(
                  registerMode ? 'Создать аккаунт' : 'Добро пожаловать',
                  style: theme.textTheme.headlineSmall?.copyWith(fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 4),
                Text(
                  registerMode ? 'Зарегистрируйтесь в inWork' : 'Войдите в свой аккаунт inWork',
                  style: theme.textTheme.bodyMedium?.copyWith(color: Colors.grey.shade600),
                ),
                const SizedBox(height: 24),
                AppCard(
                  padding: const EdgeInsets.all(20),
                  child: Column(
                    children: [
                      if (registerMode) ...[
                        TextField(controller: name, decoration: const InputDecoration(labelText: 'Имя')),
                        const SizedBox(height: 12),
                      ],
                      TextField(controller: email, decoration: const InputDecoration(labelText: 'Email')),
                      const SizedBox(height: 12),
                      TextField(
                        controller: password,
                        decoration: const InputDecoration(labelText: 'Пароль'),
                        obscureText: true,
                      ),
                      if (registerMode) ...[
                        const SizedBox(height: 12),
                        DropdownButtonFormField<String>(
                          value: role,
                          items: const [
                            DropdownMenuItem(value: 'freelancer', child: Text('Исполнитель')),
                            DropdownMenuItem(value: 'client', child: Text('Заказчик')),
                          ],
                          onChanged: (v) => setState(() => role = v ?? 'freelancer'),
                          decoration: const InputDecoration(labelText: 'Роль'),
                        ),
                      ],
                      const SizedBox(height: 16),
                      PrimaryButton(
                        fullWidth: true,
                        onPressed: busy ? null : _submit,
                        child: Text(registerMode ? 'Создать аккаунт' : 'Войти'),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 16),
                TextButton(
                  onPressed: busy ? null : () => setState(() => registerMode = !registerMode),
                  child: Text(
                    registerMode ? 'Уже есть аккаунт? Войти' : 'Нет аккаунта? Зарегистрироваться',
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}


