import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../core/api_client.dart';
import 'client_login_screen.dart';

class MainShell extends StatefulWidget {
  const MainShell({super.key});

  @override
  State<MainShell> createState() => _MainShellState();
}

class _MainShellState extends State<MainShell> {
  final ApiClient _api = ApiClient();
  int _index = 0;
  late Future<Map<String, dynamic>> _dashboard;

  @override
  void initState() {
    super.initState();
    _dashboard = _api.dashboard();
  }

  @override
  void dispose() {
    _api.close();
    super.dispose();
  }

  void _retry() => setState(() => _dashboard = _api.dashboard());

  Future<void> _open(String url) async {
    final uri = Uri.parse(url);
    if (!await launchUrl(uri, mode: LaunchMode.externalApplication) && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Unable to open this link.')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<Map<String, dynamic>>(
      future: _dashboard,
      builder: (context, snapshot) {
        if (snapshot.connectionState != ConnectionState.done) {
          return const _LoadingScreen();
        }
        if (snapshot.hasError || !snapshot.hasData) {
          return _ErrorScreen(message: snapshot.error.toString(), onRetry: _retry);
        }

        final data = snapshot.data!;
        final pages = [
          _HomePage(data: data, onOpen: _open),
          _ServicesPage(data: data),
          _ClientProjectsPage(api: _api, onOpen: _open),
          _ProfilePage(data: data, onOpen: _open),
        ];

        return Scaffold(
          body: IndexedStack(index: _index, children: pages),
          bottomNavigationBar: NavigationBar(
            selectedIndex: _index,
            onDestinationSelected: (value) => setState(() => _index = value),
            destinations: const [
              NavigationDestination(icon: Icon(Icons.home_outlined), selectedIcon: Icon(Icons.home), label: 'Home'),
              NavigationDestination(icon: Icon(Icons.design_services_outlined), selectedIcon: Icon(Icons.design_services), label: 'Services'),
              NavigationDestination(icon: Icon(Icons.work_outline), selectedIcon: Icon(Icons.work), label: 'Projects'),
              NavigationDestination(icon: Icon(Icons.person_outline), selectedIcon: Icon(Icons.person), label: 'Profile'),
            ],
          ),
        );
      },
    );
  }
}

class _LoadingScreen extends StatelessWidget {
  const _LoadingScreen();
  @override
  Widget build(BuildContext context) => const Scaffold(
        body: Center(child: Column(mainAxisSize: MainAxisSize.min, children: [
          CircularProgressIndicator(),
          SizedBox(height: 16),
          Text('Connecting to C-Net Web Services...'),
        ])),
      );
}

class _ErrorScreen extends StatelessWidget {
  const _ErrorScreen({required this.message, required this.onRetry});
  final String message;
  final VoidCallback onRetry;
  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: AppBar(title: const Text('C-Net Web Services')),
        body: Center(
          child: Padding(
            padding: const EdgeInsets.all(28),
            child: Column(mainAxisSize: MainAxisSize.min, children: [
              const Icon(Icons.cloud_off_rounded, size: 72, color: Color(0xFF0756A3)),
              const SizedBox(height: 18),
              const Text('Unable to connect', style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
              const SizedBox(height: 8),
              Text(message, textAlign: TextAlign.center),
              const SizedBox(height: 22),
              FilledButton.icon(onPressed: onRetry, icon: const Icon(Icons.refresh), label: const Text('Retry')),
            ]),
          ),
        ),
      );
}

class _HomePage extends StatelessWidget {
  const _HomePage({required this.data, required this.onOpen});
  final Map<String, dynamic> data;
  final Future<void> Function(String) onOpen;

  @override
  Widget build(BuildContext context) {
    final brand = Map<String, dynamic>.from(data['brand'] as Map);
    final services = List<Map<String, dynamic>>.from(
      (data['services'] as List).map((e) => Map<String, dynamic>.from(e as Map)),
    );

    return CustomScrollView(slivers: [
      SliverAppBar.large(
        pinned: true,
        title: const Text('C-Net Web Services'),
        expandedHeight: 230,
        flexibleSpace: FlexibleSpaceBar(
          background: Container(
            decoration: const BoxDecoration(
              gradient: LinearGradient(colors: [Color(0xFF061D36), Color(0xFF0756A3), Color(0xFF09A9D1)]),
            ),
            padding: const EdgeInsets.fromLTRB(22, 100, 22, 20),
            child: const Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text('Build Your Digital Presence', style: TextStyle(color: Colors.white, fontSize: 28, fontWeight: FontWeight.w900)),
              SizedBox(height: 7),
              Text('Domain • Hosting • Website • SEO • Promotion', style: TextStyle(color: Color(0xFFDFF5FF), fontSize: 15)),
            ]),
          ),
        ),
      ),
      SliverPadding(
        padding: const EdgeInsets.all(16),
        sliver: SliverList.list(children: [
          FilledButton.icon(
            onPressed: () => onOpen(brand['trial_url'] as String),
            icon: const Icon(Icons.rocket_launch),
            label: const Padding(padding: EdgeInsets.all(14), child: Text('Create Free Trial Website')),
          ),
          const SizedBox(height: 24),
          const Text('Our Services', style: TextStyle(fontSize: 24, fontWeight: FontWeight.w900)),
          const SizedBox(height: 12),
          ...services.take(6).map((service) => Card(
                child: ListTile(
                  contentPadding: const EdgeInsets.all(16),
                  leading: CircleAvatar(child: Text((service['icon'] ?? '🌐').toString())),
                  title: Text(service['title'].toString(), style: const TextStyle(fontWeight: FontWeight.bold)),
                  subtitle: Text(service['short_description'].toString()),
                ),
              )),
          const SizedBox(height: 18),
        ]),
      ),
    ]);
  }
}

class _ServicesPage extends StatelessWidget {
  const _ServicesPage({required this.data});
  final Map<String, dynamic> data;
  @override
  Widget build(BuildContext context) {
    final services = List<Map<String, dynamic>>.from(
      (data['services'] as List).map((e) => Map<String, dynamic>.from(e as Map)),
    );
    return Scaffold(
      appBar: AppBar(title: const Text('Services')),
      body: ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: services.length,
        separatorBuilder: (_, __) => const SizedBox(height: 12),
        itemBuilder: (_, index) {
          final service = services[index];
          return Card(
            child: Padding(
              padding: const EdgeInsets.all(18),
              child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Text((service['icon'] ?? '🌐').toString(), style: const TextStyle(fontSize: 30)),
                const SizedBox(width: 14),
                Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  Text(service['title'].toString(), style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
                  const SizedBox(height: 5),
                  Text(service['short_description'].toString()),
                ])),
              ]),
            ),
          );
        },
      ),
    );
  }
}

class _ClientProjectsPage extends StatefulWidget {
  const _ClientProjectsPage({required this.api, required this.onOpen});
  final ApiClient api;
  final Future<void> Function(String) onOpen;

  @override
  State<_ClientProjectsPage> createState() => _ClientProjectsPageState();
}

class _ClientProjectsPageState extends State<_ClientProjectsPage> {
  Future<Map<String, dynamic>>? future;

  @override
  void initState() {
    super.initState();
    future = widget.api.clientDashboard();
  }

  void reload() => setState(() => future = widget.api.clientDashboard());

  @override
  Widget build(BuildContext context) => FutureBuilder<Map<String, dynamic>>(
        future: future,
        builder: (context, snapshot) {
          if (snapshot.hasError && snapshot.error.toString().contains('LOGIN_REQUIRED')) {
            return ClientLoginScreen(api: widget.api, onAuthenticated: reload);
          }
          if (snapshot.connectionState != ConnectionState.done) {
            return const Scaffold(
              appBar: AppBar(title: Text('My Websites')),
              body: Center(child: CircularProgressIndicator()),
            );
          }
          if (snapshot.hasError || !snapshot.hasData) {
            return Scaffold(
              appBar: AppBar(title: const Text('My Websites')),
              body: Center(child: Padding(
                padding: const EdgeInsets.all(24),
                child: Column(mainAxisSize: MainAxisSize.min, children: [
                  Text(snapshot.error.toString(), textAlign: TextAlign.center),
                  const SizedBox(height: 16),
                  FilledButton.icon(onPressed: reload, icon: const Icon(Icons.refresh), label: const Text('Retry')),
                  TextButton(onPressed: () async { await widget.api.logout(); reload(); }, child: const Text('Sign out')),
                ]),
              )),
            );
          }

          final data = snapshot.data!;
          final client = Map<String, dynamic>.from(data['client'] as Map);
          final trials = List<Map<String, dynamic>>.from(
            (data['trials'] as List).map((e) => Map<String, dynamic>.from(e as Map)),
          );
          final projects = List<Map<String, dynamic>>.from(
            (data['projects'] as List).map((e) => Map<String, dynamic>.from(e as Map)),
          );

          return Scaffold(
            appBar: AppBar(
              title: const Text('My Websites'),
              actions: [
                IconButton(
                  tooltip: 'Sign out',
                  onPressed: () async { await widget.api.logout(); reload(); },
                  icon: const Icon(Icons.logout),
                ),
              ],
            ),
            body: RefreshIndicator(
              onRefresh: () async => reload(),
              child: ListView(padding: const EdgeInsets.all(16), children: [
                Text('Welcome, ${client['name'] ?? 'Client'}', style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w900)),
                Text(client['email'].toString()),
                const SizedBox(height: 24),
                const Text('Trial Websites', style: TextStyle(fontSize: 20, fontWeight: FontWeight.w800)),
                const SizedBox(height: 10),
                if (trials.isEmpty) const Card(child: Padding(padding: EdgeInsets.all(18), child: Text('No Trial Website found.'))),
                ...trials.map((trial) => Card(
                  child: ListTile(
                    contentPadding: const EdgeInsets.all(16),
                    leading: const CircleAvatar(child: Icon(Icons.language)),
                    title: Text((trial['website_name'] ?? trial['business_name']).toString(), style: const TextStyle(fontWeight: FontWeight.bold)),
                    subtitle: Text('Status: ${trial['status']}\n${trial['trial_url'] ?? ''}'),
                    isThreeLine: true,
                    trailing: trial['trial_url'] == null ? null : const Icon(Icons.open_in_new),
                    onTap: trial['trial_url'] == null ? null : () => widget.onOpen(trial['trial_url'].toString()),
                  ),
                )),
                const SizedBox(height: 22),
                const Text('Final Website Projects', style: TextStyle(fontSize: 20, fontWeight: FontWeight.w800)),
                const SizedBox(height: 10),
                if (projects.isEmpty) const Card(child: Padding(padding: EdgeInsets.all(18), child: Text('No final project has been created yet.'))),
                ...projects.map((project) {
                  final done = int.tryParse(project['progress_completed'].toString()) ?? 0;
                  final total = int.tryParse(project['progress_total'].toString()) ?? 0;
                  final progress = total == 0 ? 0.0 : done / total;
                  return Card(
                    child: Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                        Text(project['project_name'].toString(), style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
                        const SizedBox(height: 5),
                        Text('Project: ${project['project_status']} • Payment: ${project['payment_status']}'),
                        const SizedBox(height: 12),
                        LinearProgressIndicator(value: progress),
                        const SizedBox(height: 6),
                        Text('$done of $total deployment steps completed'),
                        if (project['custom_domain'] != null) TextButton.icon(
                          onPressed: () => widget.onOpen('https://${project['custom_domain']}'),
                          icon: const Icon(Icons.open_in_new),
                          label: Text(project['custom_domain'].toString()),
                        ),
                      ]),
                    ),
                  );
                }),
                const SizedBox(height: 18),
                OutlinedButton.icon(
                  onPressed: () => widget.onOpen('https://web.mciedu.com/trial/apply'),
                  icon: const Icon(Icons.add),
                  label: const Text('Create Another Trial Website'),
                ),
              ]),
            ),
          );
        },
      );
}

class _ProfilePage extends StatelessWidget {
  const _ProfilePage({required this.data, required this.onOpen});
  final Map<String, dynamic> data;
  final Future<void> Function(String) onOpen;
  @override
  Widget build(BuildContext context) {
    final brand = Map<String, dynamic>.from(data['brand'] as Map);
    return Scaffold(
      appBar: AppBar(title: const Text('Contact & Support')),
      body: ListView(padding: const EdgeInsets.all(16), children: [
        const CircleAvatar(radius: 42, backgroundColor: Color(0xFF0756A3), child: Icon(Icons.language, color: Colors.white, size: 42)),
        const SizedBox(height: 14),
        const Text('C-Net Web Services', textAlign: TextAlign.center, style: TextStyle(fontSize: 24, fontWeight: FontWeight.w900)),
        const Text('Complete Website Solutions', textAlign: TextAlign.center),
        const SizedBox(height: 24),
        Card(child: Column(children: [
          ListTile(leading: const Icon(Icons.public), title: const Text('Website'), subtitle: Text(brand['website'].toString()), onTap: () => onOpen(brand['website'].toString())),
          ListTile(leading: const Icon(Icons.email_outlined), title: const Text('Email'), subtitle: Text(brand['email'].toString()), onTap: () => onOpen('mailto:${brand['email']}')),
          ListTile(leading: const Icon(Icons.phone_outlined), title: const Text('Call'), subtitle: Text(brand['phone'].toString()), onTap: () => onOpen('tel:${brand['phone']}')),
          ListTile(leading: const Icon(Icons.chat_outlined), title: const Text('WhatsApp Support'), onTap: () => onOpen('https://wa.me/91${brand['phone']}')),
        ])),
      ]),
    );
  }
}
