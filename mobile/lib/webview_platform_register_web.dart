import 'package:flutter_web_plugins/flutter_web_plugins.dart';
import 'package:webview_flutter_web/webview_flutter_web.dart';

void registerWebViewPlatformIfNeeded() {
  WebWebViewPlatform.registerWith(webPluginRegistrar);
}
