import 'dart:convert';
import 'package:http/http.dart' as http;
import 'token_store.dart';

class ApiException implements Exception {
  ApiException(this.message);
  final String message;
  @override
  String toString() => message;
}

class ApiClient {
  ApiClient({http.Client? client, TokenStore? tokenStore})
      : _client = client ?? http.Client(),
        tokenStore = tokenStore ?? const TokenStore();

  static const String baseUrl = 'https://web.mciedu.com/api/mobile/v1';
  final http.Client _client;
  final TokenStore tokenStore;

  Future<Map<String, dynamic>> dashboard() => _get('/dashboard');

  Future<void> requestLoginOtp(String email) async {
    await _post('/client/request-otp', {'email': email});
  }

  Future<Map<String, dynamic>> verifyLoginOtp(String email, String otp) async {
    final payload = await _post('/client/verify-otp', {
      'email': email,
      'otp': otp,
      'device_name': 'C-Net Web Services Mobile App',
    });
    final token = payload['token']?.toString();
    if (token == null || token.isEmpty) {
      throw ApiException('Login token was not received.');
    }
    await tokenStore.save(token: token, email: email);
    return payload;
  }

  Future<Map<String, dynamic>> clientDashboard() async {
    final token = await tokenStore.readToken();
    if (token == null) throw ApiException('LOGIN_REQUIRED');
    return _get('/client/me', token: token);
  }

  Future<void> logout() async {
    final token = await tokenStore.readToken();
    if (token != null) {
      try {
        await _post('/client/logout', const {}, token: token);
      } catch (_) {}
    }
    await tokenStore.clear();
  }

  Future<Map<String, dynamic>> _get(String path, {String? token}) async {
    final response = await _client
        .get(Uri.parse('$baseUrl$path'), headers: _headers(token))
        .timeout(const Duration(seconds: 20));
    return _decode(response);
  }

  Future<Map<String, dynamic>> _post(
    String path,
    Map<String, dynamic> body, {
    String? token,
  }) async {
    final response = await _client
        .post(
          Uri.parse('$baseUrl$path'),
          headers: _headers(token, json: true),
          body: jsonEncode(body),
        )
        .timeout(const Duration(seconds: 20));
    return _decode(response);
  }

  Map<String, dynamic> _decode(http.Response response) {
    Map<String, dynamic> payload;
    try {
      payload = jsonDecode(response.body) as Map<String, dynamic>;
    } catch (_) {
      throw ApiException('The server response was not valid.');
    }
    if (response.statusCode < 200 || response.statusCode >= 300) {
      final errors = payload['errors'];
      if (errors is Map && errors.isNotEmpty) {
        final first = errors.values.first;
        if (first is List && first.isNotEmpty) throw ApiException(first.first.toString());
      }
      throw ApiException(payload['message']?.toString() ?? 'Request failed.');
    }
    if (payload['success'] != true) {
      throw ApiException(payload['message']?.toString() ?? 'Request failed.');
    }
    final data = payload['data'];
    return data is Map<String, dynamic> ? data : payload;
  }

  Map<String, String> _headers(String? token, {bool json = false}) => {
        'Accept': 'application/json',
        'X-Mobile-App': 'C-Net-Web-Services',
        if (json) 'Content-Type': 'application/json',
        if (token != null) 'Authorization': 'Bearer $token',
      };

  void close() => _client.close();
}
