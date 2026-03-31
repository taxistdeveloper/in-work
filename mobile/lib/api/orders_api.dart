import 'dart:convert';

import 'package:http/http.dart' as http;

import '../app_url.dart';
import '../models/order.dart';

class OrdersApi {
  OrdersApi() : _baseUrl = resolveStartUrl();

  final String _baseUrl;

  Future<List<OrderModel>> fetchOpenOrders() async {
    final uri = Uri.parse('$_baseUrl/api/orders');
    final response = await http.get(uri);

    if (response.statusCode != 200) {
      throw Exception('Ошибка загрузки заказов (${response.statusCode})');
    }

    final decoded = json.decode(response.body) as Map<String, dynamic>;
    final items = decoded['items'] as List<dynamic>? ?? <dynamic>[];

    return items
        .map((e) => OrderModel.fromJson(e as Map<String, dynamic>))
        .toList();
  }
}

