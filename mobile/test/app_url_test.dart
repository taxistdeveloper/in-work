import 'package:flutter_test/flutter_test.dart';
import 'package:in_work/app_url.dart';

void main() {
  test('resolveStartUrl указывает на in-work', () {
    final url = resolveStartUrl();
    expect(url.startsWith('http'), isTrue);
    expect(url.contains('in-work'), isTrue);
    expect(url.endsWith('/'), isTrue);
  });
}
