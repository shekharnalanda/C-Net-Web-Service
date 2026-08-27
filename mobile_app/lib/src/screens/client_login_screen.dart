import 'package:flutter/material.dart';
import '../core/api_client.dart';

class ClientLoginScreen extends StatefulWidget {
  const ClientLoginScreen({super.key, required this.api, required this.onAuthenticated});
  final ApiClient api;
  final VoidCallback onAuthenticated;

  @override
  State<ClientLoginScreen> createState() => _ClientLoginScreenState();
}

class _ClientLoginScreenState extends State<ClientLoginScreen> {
  final email = TextEditingController();
  final otp = TextEditingController();
  bool otpSent = false;
  bool busy = false;
  String? error;

  Future<void> submit() async {
    setState(() { busy = true; error = null; });
    try {
      if (!otpSent) {
        await widget.api.requestLoginOtp(email.text.trim());
        setState(() => otpSent = true);
      } else {
        await widget.api.verifyLoginOtp(email.text.trim(), otp.text.trim());
        widget.onAuthenticated();
      }
    } catch (e) {
      setState(() => error = e.toString());
    } finally {
      if (mounted) setState(() => busy = false);
    }
  }

  @override
  void dispose() {
    email.dispose();
    otp.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: AppBar(title: const Text('Client Login')),
    body: ListView(padding: const EdgeInsets.all(24), children: [
      const Icon(Icons.verified_user_rounded, size: 78, color: Color(0xFF0756A3)),
      const SizedBox(height: 16),
      const Text('Access My Websites', textAlign: TextAlign.center, style: TextStyle(fontSize: 25, fontWeight: FontWeight.w900)),
      const SizedBox(height: 8),
      const Text('Use the same email that you used to create your Trial Website.', textAlign: TextAlign.center),
      const SizedBox(height: 26),
      TextField(controller: email, enabled: !otpSent, keyboardType: TextInputType.emailAddress, decoration: const InputDecoration(labelText: 'Registered Email', border: OutlineInputBorder(), prefixIcon: Icon(Icons.email_outlined))),
      if (otpSent) ...[
        const SizedBox(height: 16),
        TextField(controller: otp, keyboardType: TextInputType.number, maxLength: 6, decoration: const InputDecoration(labelText: '6-digit Email OTP', border: OutlineInputBorder(), prefixIcon: Icon(Icons.password))),
      ],
      if (error != null) Padding(padding: const EdgeInsets.only(top: 14), child: Text(error!, style: const TextStyle(color: Colors.red))),
      const SizedBox(height: 18),
      FilledButton.icon(onPressed: busy ? null : submit, icon: busy ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(strokeWidth: 2)) : Icon(otpSent ? Icons.login : Icons.send), label: Padding(padding: const EdgeInsets.all(14), child: Text(otpSent ? 'Verify OTP & Login' : 'Send Login OTP'))),
      if (otpSent) TextButton(onPressed: busy ? null : () => setState(() { otpSent = false; otp.clear(); }), child: const Text('Use another email')),
    ]),
  );
}
