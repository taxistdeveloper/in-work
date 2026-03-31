import 'dart:convert';

import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

import '../app_url.dart';

class ApiException implements Exception {
  ApiException(this.message, {this.statusCode});
  final String message;
  final int? statusCode;
  @override
  String toString() => message;
}

class ApiClient {
  static const _cookieKey = 'api_cookie';
  final String _baseUrl = resolveStartUrl();

  Future<Map<String, dynamic>> getJson(String path) async {
    final response = await _send('GET', path);
    return _decode(response);
  }

  Future<Map<String, dynamic>> postJson(String path, Map<String, dynamic> body) async {
    final response = await _send('POST', path, body: body);
    return _decode(response);
  }

  Future<http.Response> _send(String method, String path, {Map<String, dynamic>? body}) async {
    final prefs = await SharedPreferences.getInstance();
    final cookie = prefs.getString(_cookieKey);
    final headers = <String, String>{
      'Content-Type': 'application/json',
      if (cookie != null && cookie.isNotEmpty) 'Cookie': cookie,
    };
    final uri = Uri.parse(_baseUrl).resolve(path);
    final response = method == 'POST'
        ? await http.post(uri, headers: headers, body: json.encode(body ?? {})).timeout(const Duration(seconds: 15))
        : await http.get(uri, headers: headers).timeout(const Duration(seconds: 15));

    final setCookie = response.headers['set-cookie'];
    if (setCookie != null && setCookie.contains('PHPSESSID=')) {
      final sessionCookie = setCookie.split(';').first;
      await prefs.setString(_cookieKey, sessionCookie);
    }
    return response;
  }

  Map<String, dynamic> _decode(http.Response response) {
    final payload = response.body.isEmpty
        ? <String, dynamic>{}
        : (json.decode(response.body) as Map<String, dynamic>);
    if (response.statusCode >= 400) {
      throw ApiException(
        payload['message']?.toString() ??
            payload['error']?.toString() ??
            'HTTP error ${response.statusCode}',
        statusCode: response.statusCode,
      );
    }
    return payload;
  }

  Future<void> clearSession() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_cookieKey);
  }
}

