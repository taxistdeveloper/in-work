import '../models/order.dart';
import 'api_client.dart';

class AuthApi {
  AuthApi(this._client);
  final ApiClient _client;

  Future<Map<String, dynamic>?> me() async {
    try {
      final json = await _client.getJson('api/auth/me');
      return json['data']?['user'] as Map<String, dynamic>?;
    } on ApiException catch (e) {
      if (e.statusCode == 401) return null;
      rethrow;
    }
  }

  Future<Map<String, dynamic>> login(String email, String password) async {
    final json = await _client.postJson('api/auth/login', {
      'email': email,
      'password': password,
    });
    return json['data']['user'] as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> register({
    required String name,
    required String email,
    required String password,
    required String role,
  }) async {
    final json = await _client.postJson('api/auth/register', {
      'name': name,
      'email': email,
      'password': password,
      'password_confirm': password,
      'role': role,
    });
    return json['data']['user'] as Map<String, dynamic>;
  }

  Future<void> logout() async {
    await _client.postJson('api/auth/logout', {});
    await _client.clearSession();
  }
}

class OrdersApi {
  OrdersApi(this._client);
  final ApiClient _client;

  Future<List<OrderModel>> fetchOpenOrders() async {
    final json = await _client.getJson('api/orders');
    final items = (json['items'] as List<dynamic>? ?? []);
    return items.map((e) => OrderModel.fromJson(e as Map<String, dynamic>)).toList();
  }

  Future<List<OrderModel>> fetchMyOrders() async {
    final json = await _client.getJson('api/my-orders');
    final items = (json['data']?['items'] as List<dynamic>? ?? []);
    return items.map((e) => OrderModel.fromJson(e as Map<String, dynamic>)).toList();
  }

  Future<Map<String, dynamic>> fetchOrderDetail(int id) async {
    final json = await _client.getJson('api/orders/$id');
    return json['data'] as Map<String, dynamic>;
  }

  Future<void> sendBid(int orderId, double amount, String message) async {
    await _client.postJson('api/orders/$orderId/bid', {
      'amount': amount,
      'message': message,
    });
  }

  Future<void> createOrder({
    required String title,
    required String description,
    required String category,
    required double budget,
    required String deadline,
  }) async {
    await _client.postJson('api/orders', {
      'title': title,
      'description': description,
      'category': category,
      'budget': budget,
      'deadline': deadline,
    });
  }

  Future<void> updateOrder(
    int id, {
    required String title,
    required String description,
    required String category,
    required double budget,
    required String deadline,
  }) async {
    await _client.postJson('api/orders/$id/update', {
      'title': title,
      'description': description,
      'category': category,
      'budget': budget,
      'deadline': deadline,
    });
  }

  Future<void> deleteOrder(int id) async {
    await _client.postJson('api/orders/$id/delete', {});
  }
}

class ChatApi {
  ChatApi(this._client);
  final ApiClient _client;

  Future<List<Map<String, dynamic>>> conversations() async {
    final json = await _client.getJson('api/chats');
    return (json['data']?['items'] as List<dynamic>? ?? []).cast<Map<String, dynamic>>();
  }

  Future<List<Map<String, dynamic>>> messages(int conversationId) async {
    final json = await _client.getJson('api/chat/$conversationId/messages');
    return (json['messages'] as List<dynamic>? ?? []).cast<Map<String, dynamic>>();
  }

  Future<void> send(int conversationId, String message) async {
    await _client.postJson('api/chat/$conversationId/send', {'message': message});
  }
}

class ProfileApi {
  ProfileApi(this._client);
  final ApiClient _client;

  Future<Map<String, dynamic>> me() async {
    final json = await _client.getJson('api/profile');
    return json['data']['profile'] as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> update(String name, String bio) async {
    final json = await _client.postJson('api/profile', {'name': name, 'bio': bio});
    return json['data']['profile'] as Map<String, dynamic>;
  }
}

class BalanceApi {
  BalanceApi(this._client);
  final ApiClient _client;

  Future<Map<String, dynamic>> fetch() async {
    final json = await _client.getJson('api/balance');
    return json['data'] as Map<String, dynamic>;
  }

  Future<void> deposit(double amount) async {
    await _client.postJson('api/balance/deposit', {'amount': amount});
  }

  Future<void> withdraw(double amount) async {
    await _client.postJson('api/balance/withdraw', {'amount': amount});
  }
}

class NotificationsApi {
  NotificationsApi(this._client);
  final ApiClient _client;

  Future<List<Map<String, dynamic>>> all() async {
    final json = await _client.getJson('api/notifications');
    return (json['notifications'] as List<dynamic>? ?? []).cast<Map<String, dynamic>>();
  }

  Future<Map<String, dynamic>> unread() async {
    final json = await _client.getJson('api/notifications/unread');
    return {
      'count': (json['unread_count'] as num?)?.toInt() ?? 0,
      'items': (json['notifications'] as List<dynamic>? ?? []).cast<Map<String, dynamic>>(),
    };
  }
}

