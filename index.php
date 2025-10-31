<?php 
session_start(); // Iniciar sesión para manejar mensajes y futuras redirecciones
// Asegúrate de que l rutas a las imágenes y al backend sean correctas
$logo_path = 'frontend/img/logo_empresa.png'; // Ajusta esta ruta si es necesario
$login_process_path = 'backend/login_process.php'; // Ajusta esta ruta si es necesario
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Dunosusa Logística</title>
    <!-- Carga de Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Carga de Fuente LATO -->
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Lato', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md">
        
        <?php if (file_exists($logo_path)): ?>
            <img src="<?= htmlspecialchars($logo_path) ?>" alt="Logo de la Empresa" class="w-32 h-auto mx-auto mb-4">
        <?php else: ?>
            <h1 class="text-3xl font-bold text-center text-blue-600">Dunosusa Logística</h1>
        <?php endif; ?>
        
        <p class="text-center text-gray-600">Ingresa tus credenciales para acceder.</p>

        <?php
        // Mostrar mensajes de error o éxito
        if (isset($_SESSION['error_message'])) {
            echo '<div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
            unset($_SESSION['error_message']); // Limpiar el mensaje
        }
        if (isset($_SESSION['success_message'])) {
            echo '<div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
            unset($_SESSION['success_message']);
        }
        ?>

        <form action="<?= htmlspecialchars($login_process_path) ?>" method="POST" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-bold text-gray-700">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="contrasena" class="block text-sm font-bold text-gray-700">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button type="submit" class="w-full py-3 px-4 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition-colors">Iniciar Sesión</button>
        </form>
        <p class="text-sm text-center text-gray-500">
            ¿Necesitas acceso? Contacta a tu administrador de empresa.
        </p>
        
    </div>

</body>
</html>

