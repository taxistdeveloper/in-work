import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:webview_flutter/webview_flutter.dart';

import 'app_url.dart';
import 'webview_platform_register_stub.dart'
    if (dart.library.html) 'webview_platform_register_web.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  registerWebViewPlatformIfNeeded();
  if (!kIsWeb) {
    SystemChrome.setEnabledSystemUIMode(SystemUiMode.edgeToEdge);
  }
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
      home: const WebShell(),
    );
  }
}

class WebShell extends StatefulWidget {
  const WebShell({super.key});

  @override
  State<WebShell> createState() => _WebShellState();
}

class _WebShellState extends State<WebShell> {
  late final WebViewController _controller;
  final String _startUrl = resolveStartUrl();

  int _loadingProgress = 0;
  String? _lastError;
  int _selectedAppTab = 0;

  @override
  void initState() {
    super.initState();
    if (kIsWeb) {
      // webview_flutter_web (iframe) не реализует setJavaScriptMode,
      // setBackgroundColor, setNavigationDelegate, canGoBack, reload и т.д.
      _controller = WebViewController()
        ..loadRequest(Uri.parse(_startUrl));
      WidgetsBinding.instance.addPostFrameCallback((_) {
        if (mounted) setState(() => _loadingProgress = 100);
      });
      return;
    }

    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setBackgroundColor(const Color(0xFFF5F5F5))
      ..setNavigationDelegate(
        NavigationDelegate(
          onProgress: (progress) {
            setState(() {
              _loadingProgress = progress;
            });
          },
          onPageStarted: (_) {
            setState(() {
              _lastError = null;
            });
          },
          onPageFinished: (_) {},
          onWebResourceError: (error) {
            if (!mounted) return;
            setState(() {
              _lastError =
                  '${error.description} (${error.errorType?.name ?? error.errorCode})';
            });
          },
        ),
      )
      ..loadRequest(Uri.parse(_startUrl));
  }

  Future<void> _reload() async {
    if (kIsWeb) {
      await _controller.loadRequest(Uri.parse(_startUrl));
    } else {
      await _controller.reload();
    }
  }

  Future<bool> _onWillPop() async {
    if (await _controller.canGoBack()) {
      await _controller.goBack();
      return false;
    }
    return true;
  }

  Future<void> _handleAppTabChange(int index) async {
    setState(() {
      _selectedAppTab = index;
    });

    String path;
    switch (index) {
      case 0:
        path = ''; // главная / лента
        break;
      case 1:
        path = 'my-orders';
        break;
      case 2:
        path = 'chat';
        break;
      case 3:
        path = 'dashboard';
        break;
      case 4:
        path = 'profile';
        break;
      default:
        path = '';
    }

    final uri = Uri.parse('$_startUrl$path');
    await _controller.loadRequest(uri);
  }

  @override
  Widget build(BuildContext context) {
    final scaffold = Scaffold(
      body: SafeArea(
        child: Stack(
          children: [
            WebViewWidget(controller: _controller),
            if (_loadingProgress < 100)
              LinearProgressIndicator(
                value: _loadingProgress / 100,
                minHeight: 2,
              ),
            if (_lastError != null)
              Material(
                color: Colors.white,
                child: Center(
                  child: Padding(
                    padding: const EdgeInsets.all(24),
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        const Icon(Icons.wifi_off, size: 48, color: Colors.grey),
                        const SizedBox(height: 16),
                        Text(
                          'Не удалось загрузить страницу',
                          style: Theme.of(context).textTheme.titleMedium,
                          textAlign: TextAlign.center,
                        ),
                        const SizedBox(height: 8),
                        Text(
                          _lastError!,
                          style: Theme.of(context).textTheme.bodySmall,
                          textAlign: TextAlign.center,
                        ),
                        const SizedBox(height: 8),
                        Text(
                          _startUrl,
                          style: Theme.of(context).textTheme.bodySmall?.copyWith(
                                color: Theme.of(context).colorScheme.primary,
                              ),
                          textAlign: TextAlign.center,
                        ),
                        const SizedBox(height: 20),
                        FilledButton(
                          onPressed: _reload,
                          child: const Text('Повторить'),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
          ],
        ),
      ),
      bottomNavigationBar: kIsWeb
          ? NavigationBar(
              height: 64,
              selectedIndex: _selectedAppTab,
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
              onDestinationSelected: _handleAppTabChange,
            )
          : null,
    );

    if (kIsWeb) {
      return scaffold;
    }

    return PopScope(
      canPop: false,
      onPopInvokedWithResult: (didPop, result) async {
        if (didPop) return;
        if (await _onWillPop() && context.mounted) {
          SystemNavigator.pop();
        }
      },
      child: scaffold,
    );
  }
}
