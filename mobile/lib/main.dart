import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:package_info_plus/package_info_plus.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'api/api_client.dart';
import 'api/app_api.dart';
import 'app_session.dart';
import 'release_notes.dart';
import 'screens/auth_screen.dart';
import 'screens/feed_screen.dart';
import 'screens/my_orders_screen.dart';
import 'screens/chat_list_screen.dart';
import 'screens/balance_screen.dart';
import 'screens/profile_screen.dart';
import 'screens/notifications_screen.dart';
import 'ui/theme/app_theme.dart';

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
      theme: buildAppTheme(),
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
  static const _lastSeenVersionKey = 'last_seen_app_version';

  int _selectedIndex = 0;
  late final ApiClient _client;
  late final AppSession _session;
  late final OrdersApi _ordersApi;
  late final ChatApi _chatApi;
  late final ProfileApi _profileApi;
  late final BalanceApi _balanceApi;
  late final NotificationsApi _notificationsApi;
  int _unreadNotifications = 0;

  @override
  void initState() {
    super.initState();
    _client = ApiClient();
    _ordersApi = OrdersApi(_client);
    _chatApi = ChatApi(_client);
    _profileApi = ProfileApi(_client);
    _balanceApi = BalanceApi(_client);
    _notificationsApi = NotificationsApi(_client);
    _session = AppSession(AuthApi(_client))..restore();
    _session.addListener(() {
      setState(() {});
      if (_session.isLoggedIn) {
        _loadUnreadNotifications();
      }
    });
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _checkForAppUpdateAndShowInfo();
    });
  }

  Future<void> _loadUnreadNotifications() async {
    try {
      final data = await _notificationsApi.unread();
      if (!mounted) return;
      setState(() => _unreadNotifications = data['count'] as int);
    } catch (_) {}
  }

  Future<void> _checkForAppUpdateAndShowInfo() async {
    final prefs = await SharedPreferences.getInstance();
    final packageInfo = await PackageInfo.fromPlatform();
    final currentVersion =
        '${packageInfo.version}+${packageInfo.buildNumber}';
    final previousVersion = prefs.getString(_lastSeenVersionKey);

    if (previousVersion == null) {
      await prefs.setString(_lastSeenVersionKey, currentVersion);
      return;
    }

    if (previousVersion == currentVersion || !mounted) {
      return;
    }

    await prefs.setString(_lastSeenVersionKey, currentVersion);
    final releaseNotes =
        releaseNotesByVersion[currentVersion] ?? defaultReleaseNotes;

    if (!mounted) return;
    await showDialog<void>(
      context: context,
      builder: (dialogContext) {
        return AlertDialog(
          title: const Text('Приложение обновлено'),
          content: SingleChildScrollView(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                Text('Текущая версия: $currentVersion'),
                const SizedBox(height: 12),
                const Text('Что изменилось:'),
                const SizedBox(height: 8),
                ...releaseNotes.map(
                  (item) => Padding(
                    padding: const EdgeInsets.only(bottom: 6),
                    child: Text('• $item'),
                  ),
                ),
              ],
            ),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(dialogContext).pop(),
              child: const Text('ОК'),
            ),
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final screens = <Widget>[
      FeedScreen(api: _ordersApi),
      MyOrdersScreen(api: _ordersApi),
      ChatListScreen(api: _chatApi),
      BalanceScreen(api: _balanceApi),
      ProfileScreen(api: _profileApi),
    ];

    if (_session.loading) {
      return const Scaffold(body: Center(child: CircularProgressIndicator()));
    }
    if (!_session.isLoggedIn) {
      return AuthScreen(session: _session);
    }

    return Scaffold(
      appBar: AppBar(
        title: const Text('inWork'),
        actions: [
          IconButton(
            onPressed: () async {
              await Navigator.of(context).push(
                MaterialPageRoute(
                  builder: (_) => NotificationsScreen(api: _notificationsApi),
                ),
              );
              _loadUnreadNotifications();
            },
            icon: Badge(
              isLabelVisible: _unreadNotifications > 0,
              label: Text(_unreadNotifications.toString()),
              child: const Icon(Icons.notifications_none),
            ),
          ),
          IconButton(
            onPressed: _session.logout,
            icon: const Icon(Icons.logout),
          ),
        ],
      ),
      body: SafeArea(
        child: IndexedStack(
          index: _selectedIndex,
          children: screens,
        ),
      ),
      bottomNavigationBar: NavigationBar(
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
            icon: Icon(Icons.account_balance_wallet_outlined),
            label: 'Баланс',
          ),
          NavigationDestination(
            icon: Icon(Icons.person_outline),
            label: 'Профиль',
          ),
        ],
      ),
    );
  }
}
