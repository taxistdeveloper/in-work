import 'package:flutter/foundation.dart';

/// Соответствует `config/app.php`: прод — HTTPS, локально — `/in-work/public`.
const String _productionUrl = 'https://in-work.krg-ktsk.kz/';

/// Локальный путь под MAMP (Apache на порту по умолчанию).
const String _localPath = '/in-work/public/';

/// Явный URL: `flutter run --dart-define=WEB_URL=http://192.168.1.5/in-work/public/`
/// (нужен для физического Android‑телефона вместо эмулятора).
String resolveStartUrl() {
  const fromEnv = String.fromEnvironment('WEB_URL', defaultValue: '');
  if (fromEnv.isNotEmpty) {
    return fromEnv.endsWith('/') ? fromEnv : '$fromEnv/';
  }

  if (kReleaseMode) {
    return _productionUrl;
  }

  // `dart:io` Platform недоступен в Flutter Web — используем foundation.
  if (kIsWeb) {
    return 'http://localhost$_localPath';
  }

  if (defaultTargetPlatform == TargetPlatform.android) {
    // Эмулятор Android видит хост ПК как 10.0.2.2
    return 'http://10.0.2.2$_localPath';
  }

  // iOS Simulator, Windows/macOS desktop и т.д.
  return 'http://localhost$_localPath';
}
