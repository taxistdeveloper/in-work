import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:package_info_plus/package_info_plus.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'screens/feed_screen.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  SystemChrome.setEnabledSystemUIMode(SystemUiMode.edgeToEdge);
  runApp(const InWorkApp());
}

class InWorkApp extends StatelessWidget {
  const InWorkApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'inWork',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: const Color(0xFF1565C0)),
        useMaterial3: true,
      ),
      home: const _RootShell(),
    );
  }
}

class _RootShell extends StatefulWidget {
  const _RootShell();

  @override
  State<_RootShell> createState() => _RootShellState();
}

class _RootShellState extends State<_RootShell> {
  int _selectedIndex = 0;
  bool _didCheckVersion = false;

  @override
  void initState() {
    super.initState();
    _checkForAppUpdate();
  }

  Future<void> _checkForAppUpdate() async {
    final prefs = await SharedPreferences.getInstance();
    final info = await PackageInfo.fromPlatform();
    final currentVersion = '${info.version}+${info.buildNumber}';
    final previousVersion = prefs.getString('installed_app_version');

    if (!mounted) return;

    if (previousVersion != null && previousVersion != currentVersion) {
      await showDialog<void>(
        context: context,
        builder: (context) => AlertDialog(
          title: const Text('Приложение обновлено'),
          content: Text(
            'Успешно обновлено до версии ${info.version}.\n\n'
            'Что нового:\n'
            '- Улучшена стабильность\n'
            '- Исправлены ошибки',
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(context).pop(),
              child: const Text('ОК'),
            ),
          ],
        ),
      );
    }

    await prefs.setString('installed_app_version', currentVersion);
    if (mounted) {
      setState(() {
        _didCheckVersion = true;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final screens = <Widget>[
      const FeedScreen(),
      const _PlaceholderScreen(title: 'Мои заказы'),
      const _PlaceholderScreen(title: 'Чат'),
      const _PlaceholderScreen(title: 'Панель'),
      const _PlaceholderScreen(title: 'Профиль'),
    ];

    return Scaffold(
      body: _didCheckVersion
          ? SafeArea(
              child: IndexedStack(
                index: _selectedIndex,
                children: screens,
              ),
            )
          : const Center(
              child: CircularProgressIndicator(),
            ),
      bottomNavigationBar: _didCheckVersion
          ? NavigationBar(
              selectedIndex: _selectedIndex,
              onDestinationSelected: (index) {
                setState(() => _selectedIndex = index);
              },
              destinations: const [
                NavigationDestination(
                  icon: Icon(Icons.view_list),
                  label: 'Лента',
                ),
                NavigationDestination(
                  icon: Icon(Icons.assignment),
                  label: 'Мои',
                ),
                NavigationDestination(
                  icon: Icon(Icons.chat_bubble_outline),
                  label: 'Чат',
                ),
                NavigationDestination(
                  icon: Icon(Icons.dashboard_outlined),
                  label: 'Панель',
                ),
                NavigationDestination(
                  icon: Icon(Icons.person_outline),
                  label: 'Профиль',
                ),
              ],
            )
          : null,
    );
  }
}

class _PlaceholderScreen extends StatelessWidget {
  const _PlaceholderScreen({required this.title});

  final String title;

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Text(
            title,
            style: Theme.of(context).textTheme.headlineMedium,
          ),
          const SizedBox(height: 12),
          Text(
            'Здесь скоро будет нативный экран $title',
            textAlign: TextAlign.center,
            style: Theme.of(context).textTheme.bodyMedium,
          ),
        ],
      ),
    );
  }
}
