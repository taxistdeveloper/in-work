import 'package:flutter/foundation.dart';

import 'api/app_api.dart';

class AppSession extends ChangeNotifier {
  AppSession(this.authApi);
  final AuthApi authApi;

  Map<String, dynamic>? user;
  bool loading = true;

  bool get isLoggedIn => user != null;

  Future<void> restore() async {
    loading = true;
    notifyListeners();
    try {
      user = await authApi.me();
    } finally {
      loading = false;
      notifyListeners();
    }
  }

  Future<void> login(String email, String password) async {
    user = await authApi.login(email, password);
    notifyListeners();
  }

  Future<void> register(String name, String email, String password, String role) async {
    user = await authApi.register(name: name, email: email, password: password, role: role);
    notifyListeners();
  }

  Future<void> logout() async {
    await authApi.logout();
    user = null;
    notifyListeners();
  }
}

