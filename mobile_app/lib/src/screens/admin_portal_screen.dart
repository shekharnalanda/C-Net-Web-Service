import 'package:flutter/material.dart';
import '../core/api_client.dart';

class AdminPortalScreen extends StatefulWidget {
  const AdminPortalScreen({super.key, required this.api});
  final ApiClient api;

  @override
  State<AdminPortalScreen> createState() => _AdminPortalScreenState();
}

class _AdminPortalScreenState extends State<AdminPortalScreen> {
  final email = TextEditingController();
  final password = TextEditingController();
  Future<Map<String, dynamic>>? dashboard;
  bool busy = false;
  String? error;

  @override
  void initState() {
    super.initState();
    dashboard = widget.api.adminDashboard();
  }

  Future<void> login() async {
    setState(() { busy = true; error = null; });
    try {
      await widget.api.adminLogin(email.text.trim(), password.text);
      setState(() => dashboard = widget.api.adminDashboard());
    } catch (e) {
      setState(() => error = e.toString());
    } finally {
      if (mounted) setState(() => busy = false);
    }
  }

  @override
  void dispose() {
    email.dispose();
    password.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) => FutureBuilder<Map<String, dynamic>>(
    future: dashboard,
    builder: (context, snapshot) {
      if (snapshot.hasError && snapshot.error.toString().contains('ADMIN_LOGIN_REQUIRED')) {
        return _loginView();
      }
      if (snapshot.connectionState != ConnectionState.done) {
        return Scaffold(appBar: AppBar(title: const Text('Admin / Staff')), body: const Center(child: CircularProgressIndicator()));
      }
      if (snapshot.hasError || !snapshot.hasData) {
        return Scaffold(
          appBar: AppBar(title: const Text('Admin / Staff')),
          body: Center(child: Column(mainAxisSize: MainAxisSize.min, children: [
            Text(snapshot.error.toString()),
            FilledButton(onPressed: () => setState(() => dashboard = widget.api.adminDashboard()), child: const Text('Retry')),
            TextButton(onPressed: () async { await widget.api.adminLogout(); setState(() => dashboard = widget.api.adminDashboard()); }, child: const Text('Sign out')),
          ])),
        );
      }
      return _dashboardView(snapshot.data!);
    },
  );

  Widget _loginView() => Scaffold(
    appBar: AppBar(title: const Text('Admin / Staff Login')),
    body: ListView(padding: const EdgeInsets.all(24), children: [
      const Icon(Icons.admin_panel_settings_rounded, size: 78, color: Color(0xFF0756A3)),
      const SizedBox(height: 14),
      const Text('C-Net Web Services', textAlign: TextAlign.center, style: TextStyle(fontSize: 24, fontWeight: FontWeight.w900)),
      const Text('Authorized Admin / Staff Access', textAlign: TextAlign.center),
      const SizedBox(height: 26),
      TextField(controller: email, keyboardType: TextInputType.emailAddress, decoration: const InputDecoration(labelText: 'Admin Email', border: OutlineInputBorder(), prefixIcon: Icon(Icons.email))),
      const SizedBox(height: 15),
      TextField(controller: password, obscureText: true, decoration: const InputDecoration(labelText: 'Password', border: OutlineInputBorder(), prefixIcon: Icon(Icons.lock))),
      if (error != null) Padding(padding: const EdgeInsets.only(top: 12), child: Text(error!, style: const TextStyle(color: Colors.red))),
      const SizedBox(height: 18),
      FilledButton.icon(onPressed: busy ? null : login, icon: const Icon(Icons.login), label: const Padding(padding: EdgeInsets.all(14), child: Text('Secure Login'))),
    ]),
  );

  Widget _dashboardView(Map<String, dynamic> data) {
    final admin = Map<String, dynamic>.from(data['admin'] as Map);
    final counts = Map<String, dynamic>.from(data['counts'] as Map);
    final enquiries = List<Map<String, dynamic>>.from((data['enquiries'] as List).map((e) => Map<String, dynamic>.from(e as Map)));
    final trials = List<Map<String, dynamic>>.from((data['trials'] as List).map((e) => Map<String, dynamic>.from(e as Map)));
    final projects = List<Map<String, dynamic>>.from((data['projects'] as List).map((e) => Map<String, dynamic>.from(e as Map)));

    return Scaffold(
      appBar: AppBar(title: const Text('Operations Dashboard'), actions: [
        IconButton(onPressed: () async { await widget.api.adminLogout(); setState(() => dashboard = widget.api.adminDashboard()); }, icon: const Icon(Icons.logout)),
      ]),
      body: RefreshIndicator(
        onRefresh: () async => setState(() => dashboard = widget.api.adminDashboard()),
        child: ListView(padding: const EdgeInsets.all(16), children: [
          Text('Welcome, ${admin['name']}', style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w900)),
          Text(admin['email'].toString()),
          const SizedBox(height: 18),
          GridView.count(
            crossAxisCount: 2, shrinkWrap: true, physics: const NeverScrollableScrollPhysics(),
            mainAxisSpacing: 10, crossAxisSpacing: 10, childAspectRatio: 1.55,
            children: [
              _count('Enquiries', counts['enquiries'], Icons.message),
              _count('New Leads', counts['new_enquiries'], Icons.mark_email_unread),
              _count('Trials', counts['trials'], Icons.language),
              _count('Projects', counts['projects'], Icons.work),
            ],
          ),
          const SizedBox(height: 22),
          const Text('Recent Enquiries', style: TextStyle(fontSize: 20, fontWeight: FontWeight.w800)),
          ...enquiries.take(10).map((item) => Card(child: ListTile(
            title: Text((item['name'] ?? item['email'] ?? 'Enquiry').toString()),
            subtitle: Text('${item['email'] ?? ''}\nStatus: ${item['status'] ?? 'new'}'),
            isThreeLine: true,
            trailing: PopupMenuButton<String>(
              onSelected: (status) async {
                await widget.api.updateEnquiryStatus(int.parse(item['id'].toString()), status);
                setState(() => dashboard = widget.api.adminDashboard());
              },
              itemBuilder: (_) => ['new','contacted','in_progress','completed','closed']
                  .map((s) => PopupMenuItem(value: s, child: Text(s))).toList(),
            ),
          ))),
          const SizedBox(height: 18),
          Text('Recent Trials (${trials.length})', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w800)),
          ...trials.take(8).map((item) => Card(child: ListTile(
            leading: const Icon(Icons.web),
            title: Text((item['website_name'] ?? item['business_name']).toString()),
            subtitle: Text('${item['owner_name']} • ${item['status']}'),
          ))),
          const SizedBox(height: 18),
          Text('Website Projects (${projects.length})', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w800)),
          ...projects.take(8).map((item) => Card(child: ListTile(
            leading: const Icon(Icons.work_outline),
            title: Text(item['project_name'].toString()),
            subtitle: Text('${item['project_status']} • ${item['payment_status']}'),
          ))),
        ]),
      ),
    );
  }

  Widget _count(String label, dynamic value, IconData icon) => Card(
    child: Padding(padding: const EdgeInsets.all(14), child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
      Icon(icon, color: const Color(0xFF0756A3)),
      Text(value.toString(), style: const TextStyle(fontSize: 25, fontWeight: FontWeight.w900)),
      Text(label),
    ])),
  );
}
