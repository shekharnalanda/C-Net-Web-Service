import 'dart:convert';
import 'package:http/http.dart' as http;

class ApiException implements Exception {
  ApiException(this.message);
  final String message;
  @override
  String toString() => message;
}

class ApiClient {
  ApiClient({http.Client? client}) : _client = client ?? http.Client();

  static const String baseUrl = 'https://web.mciedu.com/api/mobile/v1';
  final http.Client _client;

  Future<Map<String, dynamic>> dashboard() async {
    final response = await _client
        .get(Uri.parse('$baseUrl/dashboard'), headers: _headers)
        .timeout(const Duration(seconds: 20));

    if (response.statusCode != 200) {
      throw ApiException('Server returned status ${response.statusCode}.');
    }

    final payload = jsonDecode(response.body) as Map<String, dynamic>;
    if (payload['success'] != true || payload['data'] is! Map) {
      throw ApiException('The server response was not valid.');
    }
    return Map<String, dynamic>.from(payload['data'] as Map);
  }

  Map<String, String> get _headers => const {
        'Accept': 'application/json',
        'X-Mobile-App': 'C-Net-Web-Services',
      };

  void close() => _client.close();
}
