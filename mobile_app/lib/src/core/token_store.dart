import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class TokenStore {
  const TokenStore();
  static const _storage = FlutterSecureStorage();
  static const _tokenKey = 'cnet_web_services_client_token';
  static const _emailKey = 'cnet_web_services_client_email';

  Future<String?> readToken() => _storage.read(key: _tokenKey);
  Future<String?> readEmail() => _storage.read(key: _emailKey);

  Future<void> save({required String token, required String email}) async {
    await _storage.write(key: _tokenKey, value: token);
    await _storage.write(key: _emailKey, value: email);
  }

  Future<void> clear() async {
    await _storage.delete(key: _tokenKey);
    await _storage.delete(key: _emailKey);
  }
}
