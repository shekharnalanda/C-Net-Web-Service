import 'package:flutter/material.dart';
import 'screens/main_shell.dart';

class CNetWebServicesApp extends StatelessWidget {
  const CNetWebServicesApp({super.key});

  @override
  Widget build(BuildContext context) {
    const blue = Color(0xFF0756A3);
    const navy = Color(0xFF061D36);
    const orange = Color(0xFFF58220);

    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'C-Net Web Services',
      theme: ThemeData(
        useMaterial3: true,
        colorScheme: ColorScheme.fromSeed(
          seedColor: blue,
          primary: blue,
          secondary: orange,
          surface: const Color(0xFFF5F8FC),
        ),
        scaffoldBackgroundColor: const Color(0xFFF5F8FC),
        appBarTheme: const AppBarTheme(
          backgroundColor: navy,
          foregroundColor: Colors.white,
          centerTitle: false,
        ),
        cardTheme: const CardThemeData(
          elevation: 0,
          margin: EdgeInsets.zero,
        ),
      ),
      home: const MainShell(),
    );
  }
}
