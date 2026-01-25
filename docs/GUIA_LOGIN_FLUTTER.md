# 📱 Guía Completa para Implementar Login en Flutter - RutiTruck

## 🎯 Objetivo
Esta guía contiene toda la información necesaria para crear un login funcional en Flutter para operadores, conectando con el backend existente de RutiTruck.

---

## 🔗 Información del Servidor

### URL Base de la API
```
https://api-ruti.onrender.com
```

### Endpoint de Login
```
POST /backend/api/login_mobile.php
```

> [!NOTE]
> Si estás desarrollando localmente, puedes usar:
> - `http://localhost/Api_Ruti-main/backend/api/login_mobile.php`
> - Asegúrate de que XAMPP/WAMP esté corriendo con Apache y MySQL

---

## 📡 Especificación del Endpoint de Login

### Request

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `email` | string | ✅ Sí | Correo electrónico del operador |
| `contrasena` | string | ✅ Sí | Contraseña del usuario |

#### Headers Requeridos
```json
{
  "Content-Type": "application/json"
}
```

#### Body (JSON)
```json
{
  "email": "operador@gmail.com",
  "contrasena": "password123"
}
```

---

### Responses

#### ✅ Login Exitoso (HTTP 200)
```json
{
  "success": true,
  "message": "Autenticación exitosa.",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "usuario": {
    "id": 57,
    "nombre": "operador operador",
    "rol_id": 2,
    "empresa_id": 33
  }
}
```

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `success` | boolean | `true` si la autenticación fue exitosa |
| `message` | string | Mensaje descriptivo |
| `token` | string | Token JWT para autenticación en futuras peticiones |
| `usuario.id` | int | ID único del usuario en la base de datos |
| `usuario.nombre` | string | Nombre completo del usuario |
| `usuario.rol_id` | int | `1` = Gerente, `2` = Operador |
| `usuario.empresa_id` | int | ID de la empresa a la que pertenece |

#### ❌ Datos Incompletos (HTTP 400)
```json
{
  "success": false,
  "message": "Datos incompletos. Se requiere email y contrasena."
}
```

#### ❌ Credenciales Incorrectas (HTTP 401)
```json
{
  "success": false,
  "message": "Correo o contraseña incorrectos."
}
```

#### ❌ Usuario Inactivo/Suspendido (HTTP 403)
```json
{
  "success": false,
  "message": "Usuario inactivo o suspendido."
}
```

#### ❌ Error del Servidor (HTTP 500)
```json
{
  "success": false,
  "message": "Error interno del servidor.",
  "debug_error": "..."
}
```

---

## 🗄️ Estructura de la Base de Datos

### Tabla `usuarios`
```sql
CREATE TABLE usuarios (
  usuario_id int NOT NULL AUTO_INCREMENT,
  empresa_id int NOT NULL,
  rol_id int NOT NULL,
  estatus enum('activo','inactivo') DEFAULT 'activo',
  nombre varchar(100) NOT NULL,
  apellidos varchar(100) NOT NULL,
  email varchar(255) NOT NULL UNIQUE,
  contrasena_hash varchar(255) NOT NULL,
  fecha_creacion timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (usuario_id)
);
```

### Tabla `roles`
```sql
INSERT INTO roles (rol_id, nombre_rol) VALUES
(1, 'Gerente'),
(2, 'Operador');
```

> [!IMPORTANT]
> Para la app de operadores, solo deben poder iniciar sesión usuarios con `rol_id = 2`

---

## 🛡️ Información del Token JWT

### Configuración del Token
- **Algoritmo**: HS256
- **Duración**: 1 hora (3600 segundos)
- **Issuer/Audience**: `http://localhost/Api_Ruti`

### Estructura del Payload Decodificado
```json
{
  "iss": "http://localhost/Api_Ruti",
  "aud": "http://localhost/Api_Ruti",
  "iat": 1706234567,
  "nbf": 1706234567,
  "exp": 1706238167,
  "data": {
    "id": 57,
    "rol": 2
  }
}
```

### Uso del Token en Futuras Peticiones
Para endpoints protegidos, enviar el token en el header:
```
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

---

## 📱 Implementación en Flutter

### 1. Dependencias Requeridas (pubspec.yaml)
```yaml
dependencies:
  flutter:
    sdk: flutter
  http: ^1.2.0                    # Para peticiones HTTP
  shared_preferences: ^2.2.2      # Para almacenar el token localmente
  flutter_secure_storage: ^9.0.0  # Para almacenamiento seguro del token
```

### 2. Modelo de Usuario (lib/models/usuario.dart)
```dart
class Usuario {
  final int id;
  final String nombre;
  final int rolId;
  final int empresaId;

  Usuario({
    required this.id,
    required this.nombre,
    required this.rolId,
    required this.empresaId,
  });

  factory Usuario.fromJson(Map<String, dynamic> json) {
    return Usuario(
      id: json['id'],
      nombre: json['nombre'],
      rolId: json['rol_id'],
      empresaId: json['empresa_id'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nombre': nombre,
      'rol_id': rolId,
      'empresa_id': empresaId,
    };
  }
}
```

### 3. Modelo de Respuesta de Login (lib/models/login_response.dart)
```dart
import 'usuario.dart';

class LoginResponse {
  final bool success;
  final String message;
  final String? token;
  final Usuario? usuario;

  LoginResponse({
    required this.success,
    required this.message,
    this.token,
    this.usuario,
  });

  factory LoginResponse.fromJson(Map<String, dynamic> json) {
    return LoginResponse(
      success: json['success'],
      message: json['message'],
      token: json['token'],
      usuario: json['usuario'] != null ? Usuario.fromJson(json['usuario']) : null,
    );
  }
}
```

### 4. Servicio de Autenticación (lib/services/auth_service.dart)
```dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../models/login_response.dart';
import '../models/usuario.dart';

class AuthService {
  // URL de producción en Render
  static const String baseUrl = 'https://api-ruti.onrender.com';
  static const String loginEndpoint = '/backend/api/login_mobile.php';
  
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  // Realizar login
  Future<LoginResponse> login(String email, String password) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl$loginEndpoint'),
        headers: {
          'Content-Type': 'application/json',
        },
        body: jsonEncode({
          'email': email,
          'contrasena': password,
        }),
      );

      final data = jsonDecode(response.body);
      final loginResponse = LoginResponse.fromJson(data);

      // Si el login es exitoso, guardar el token y datos del usuario
      if (loginResponse.success && loginResponse.token != null) {
        await _saveToken(loginResponse.token!);
        await _saveUserData(loginResponse.usuario!);
      }

      return loginResponse;
    } catch (e) {
      return LoginResponse(
        success: false,
        message: 'Error de conexión: ${e.toString()}',
      );
    }
  }

  // Guardar token de forma segura
  Future<void> _saveToken(String token) async {
    await _storage.write(key: 'jwt_token', value: token);
  }

  // Guardar datos del usuario
  Future<void> _saveUserData(Usuario usuario) async {
    await _storage.write(key: 'user_id', value: usuario.id.toString());
    await _storage.write(key: 'user_nombre', value: usuario.nombre);
    await _storage.write(key: 'user_rol_id', value: usuario.rolId.toString());
    await _storage.write(key: 'user_empresa_id', value: usuario.empresaId.toString());
  }

  // Obtener token guardado
  Future<String?> getToken() async {
    return await _storage.read(key: 'jwt_token');
  }

  // Verificar si hay sesión activa
  Future<bool> isLoggedIn() async {
    final token = await getToken();
    return token != null && token.isNotEmpty;
  }

  // Cerrar sesión
  Future<void> logout() async {
    await _storage.deleteAll();
  }

  // Obtener datos del usuario guardado
  Future<Usuario?> getCurrentUser() async {
    final id = await _storage.read(key: 'user_id');
    final nombre = await _storage.read(key: 'user_nombre');
    final rolId = await _storage.read(key: 'user_rol_id');
    final empresaId = await _storage.read(key: 'user_empresa_id');

    if (id == null || nombre == null || rolId == null || empresaId == null) {
      return null;
    }

    return Usuario(
      id: int.parse(id),
      nombre: nombre,
      rolId: int.parse(rolId),
      empresaId: int.parse(empresaId),
    );
  }
}
```

### 5. Pantalla de Login (lib/screens/login_screen.dart)
```dart
import 'package:flutter/material.dart';
import '../services/auth_service.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  final _authService = AuthService();
  
  bool _isLoading = false;
  bool _obscurePassword = true;
  String? _errorMessage;

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  Future<void> _handleLogin() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final response = await _authService.login(
      _emailController.text.trim(),
      _passwordController.text,
    );

    setState(() {
      _isLoading = false;
    });

    if (response.success) {
      // Verificar que sea un operador (rol_id = 2)
      if (response.usuario?.rolId != 2) {
        setState(() {
          _errorMessage = 'Esta app es solo para operadores.';
        });
        await _authService.logout(); // Limpiar sesión
        return;
      }

      // Navegar al dashboard
      if (mounted) {
        Navigator.pushReplacementNamed(context, '/dashboard');
      }
    } else {
      setState(() {
        _errorMessage = response.message;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF1A1A2E),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24.0),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                const SizedBox(height: 60),
                
                // Logo
                Container(
                  height: 120,
                  width: 120,
                  decoration: BoxDecoration(
                    color: const Color(0xFF16213E),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: const Icon(
                    Icons.local_shipping,
                    size: 60,
                    color: Color(0xFF0F4C75),
                  ),
                ),
                
                const SizedBox(height: 32),
                
                // Título
                const Text(
                  'RutiTruck',
                  style: TextStyle(
                    fontSize: 36,
                    fontWeight: FontWeight.bold,
                    color: Colors.white,
                  ),
                  textAlign: TextAlign.center,
                ),
                
                const SizedBox(height: 8),
                
                const Text(
                  'Portal de Operadores',
                  style: TextStyle(
                    fontSize: 16,
                    color: Color(0xFF888888),
                  ),
                  textAlign: TextAlign.center,
                ),
                
                const SizedBox(height: 48),
                
                // Error message
                if (_errorMessage != null)
                  Container(
                    padding: const EdgeInsets.all(12),
                    margin: const EdgeInsets.only(bottom: 16),
                    decoration: BoxDecoration(
                      color: Colors.red.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(color: Colors.red.withOpacity(0.3)),
                    ),
                    child: Row(
                      children: [
                        const Icon(Icons.error_outline, color: Colors.red, size: 20),
                        const SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            _errorMessage!,
                            style: const TextStyle(color: Colors.red),
                          ),
                        ),
                      ],
                    ),
                  ),
                
                // Email field
                TextFormField(
                  controller: _emailController,
                  keyboardType: TextInputType.emailAddress,
                  style: const TextStyle(color: Colors.white),
                  decoration: InputDecoration(
                    labelText: 'Correo electrónico',
                    labelStyle: const TextStyle(color: Color(0xFF888888)),
                    prefixIcon: const Icon(Icons.email, color: Color(0xFF0F4C75)),
                    filled: true,
                    fillColor: const Color(0xFF16213E),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                      borderSide: BorderSide.none,
                    ),
                    focusedBorder: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                      borderSide: const BorderSide(color: Color(0xFF0F4C75), width: 2),
                    ),
                  ),
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Por favor ingresa tu correo';
                    }
                    if (!value.contains('@')) {
                      return 'Por favor ingresa un correo válido';
                    }
                    return null;
                  },
                ),
                
                const SizedBox(height: 16),
                
                // Password field
                TextFormField(
                  controller: _passwordController,
                  obscureText: _obscurePassword,
                  style: const TextStyle(color: Colors.white),
                  decoration: InputDecoration(
                    labelText: 'Contraseña',
                    labelStyle: const TextStyle(color: Color(0xFF888888)),
                    prefixIcon: const Icon(Icons.lock, color: Color(0xFF0F4C75)),
                    suffixIcon: IconButton(
                      icon: Icon(
                        _obscurePassword ? Icons.visibility : Icons.visibility_off,
                        color: const Color(0xFF888888),
                      ),
                      onPressed: () {
                        setState(() {
                          _obscurePassword = !_obscurePassword;
                        });
                      },
                    ),
                    filled: true,
                    fillColor: const Color(0xFF16213E),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                      borderSide: BorderSide.none,
                    ),
                    focusedBorder: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                      borderSide: const BorderSide(color: Color(0xFF0F4C75), width: 2),
                    ),
                  ),
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Por favor ingresa tu contraseña';
                    }
                    return null;
                  },
                ),
                
                const SizedBox(height: 32),
                
                // Login button
                SizedBox(
                  height: 56,
                  child: ElevatedButton(
                    onPressed: _isLoading ? null : _handleLogin,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF0F4C75),
                      foregroundColor: Colors.white,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                      elevation: 0,
                    ),
                    child: _isLoading
                        ? const SizedBox(
                            height: 24,
                            width: 24,
                            child: CircularProgressIndicator(
                              color: Colors.white,
                              strokeWidth: 2,
                            ),
                          )
                        : const Text(
                            'Iniciar Sesión',
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                  ),
                ),
                
                const SizedBox(height: 24),
                
                // Footer
                const Text(
                  'v1.0.0',
                  style: TextStyle(color: Color(0xFF666666)),
                  textAlign: TextAlign.center,
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
```

---

## 🧪 Usuario de Prueba

Para probar el login, puedes usar el siguiente usuario operador:

| Campo | Valor |
|-------|-------|
| Email | `operador@gmail.com` |
| Contraseña | `password123` |
| Rol | Operador (rol_id: 2) |

> [!CAUTION]
> ¡Recuerda cambiar estas credenciales en producción!

---

## 📂 Estructura de Proyecto Recomendada

```
lib/
├── main.dart
├── models/
│   ├── usuario.dart
│   └── login_response.dart
├── services/
│   └── auth_service.dart
├── screens/
│   ├── login_screen.dart
│   ├── splash_screen.dart
│   └── dashboard_screen.dart
└── utils/
    └── constants.dart
```

---

## ✅ Checklist de Implementación

- [ ] Crear proyecto Flutter: `flutter create rutitruck_operador`
- [ ] Agregar dependencias en `pubspec.yaml`
- [ ] Ejecutar `flutter pub get`
- [ ] Crear carpetas de estructura del proyecto
- [ ] Implementar modelo `Usuario`
- [ ] Implementar modelo `LoginResponse`
- [ ] Implementar `AuthService`
- [ ] Implementar `LoginScreen`
- [ ] Crear `SplashScreen` para verificar sesión al inicio
- [ ] Crear `DashboardScreen` básico
- [ ] Configurar rutas en `main.dart`
- [ ] Probar en emulador/dispositivo

---

## 🔧 Configuración de main.dart

```dart
import 'package:flutter/material.dart';
import 'screens/login_screen.dart';
import 'screens/dashboard_screen.dart';
import 'services/auth_service.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'RutiTruck Operador',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(0xFF0F4C75),
          brightness: Brightness.dark,
        ),
        useMaterial3: true,
      ),
      initialRoute: '/',
      routes: {
        '/': (context) => const SplashScreen(),
        '/login': (context) => const LoginScreen(),
        '/dashboard': (context) => const DashboardScreen(),
      },
    );
  }
}

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    _checkAuth();
  }

  Future<void> _checkAuth() async {
    final authService = AuthService();
    final isLoggedIn = await authService.isLoggedIn();

    if (mounted) {
      Navigator.pushReplacementNamed(
        context,
        isLoggedIn ? '/dashboard' : '/login',
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      backgroundColor: Color(0xFF1A1A2E),
      body: Center(
        child: CircularProgressIndicator(
          color: Color(0xFF0F4C75),
        ),
      ),
    );
  }
}
```

---

## 📞 Soporte

Si tienes problemas con la conexión al API:

1. Verifica que la URL base sea correcta
2. Verifica que el servidor esté corriendo
3. Verifica conectividad a internet
4. Revisa los logs de la consola de Flutter
5. Usa herramientas como Postman para probar el endpoint directamente
