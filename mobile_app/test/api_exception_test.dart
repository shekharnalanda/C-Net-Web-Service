import 'package:cnet_web_services/src/core/api_client.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('ApiException exposes its user-facing message', () {
    final exception = ApiException('Connection failed');

    expect(exception.toString(), 'Connection failed');
  });
}
