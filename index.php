<?php 
session_start(); // Iniciar sesión para manejar mensajes y futuras redirecciones
// Asegúrate de que las rutas a las imágenes y al backend sean correctas
$logo_path = 'frontend/img/logo_empresa.png'; // Ruta a tu logo
$login_process_path = 'backend/login_process.php'; // Ruta a tu backend
$video_path = 'frontend/videos/background.mp4'; // ¡IMPORTANTE! Ruta a tu video de fondo
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
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&display=swap" rel="stylesheet">
    
    <!-- Estilos personalizados y Animaciones -->
    <style>
        /* Aplicamos la fuente base de tu código */
        body { 
            font-family: 'Lato', sans-serif; 
        }

        /* Definición de la animación "Fade In y Arriba" */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Clase de utilidad para aplicar la animación */
        .animate-fadeInUp {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0; 
        }

        /* Arreglo para el video en bucle */
        video {
            object-fit: cover;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: -1;
        }
    </style>
</head>
<body class="text-gray-800">

    <!-- Contenedor Principal Relativo -->
    <div class="relative min-h-screen overflow-hidden">
        
        <!-- 1. Video de Fondo Animado -->
        <video autoplay loop muted playsinline class="absolute z-0 w-full h-full object-cover">
            <!-- 
                ¡IMPORTANTE! 
                Este código usa la variable $video_path que definimos arriba.
                Asegúrate de que el video exista en 'frontend/videos/background.mp4'
            -->
            <source src="<?= htmlspecialchars($video_path) ?>" type="video/mp4">
            Tu navegador no soporta videos.
        </video>
        
        <!-- 2. Capa Oscura (Overlay) -->
        <div class="absolute inset-0 bg-black/60 z-10"></div>

        <!-- 3. Contenido del Login (Centrado y sobre el video) -->
        <div class="relative z-20 flex flex-col items-center justify-center min-h-screen p-4">

            <!-- 
                4. Tarjeta de Login con Animación 
                - 'animate-fadeInUp' para el efecto de carga.
                - 'backdrop-blur-sm' crea el efecto de "vidrio esmerilado".
            -->
            <div class="w-full max-w-md p-8 space-y-6 bg-white/10 backdrop-blur-sm rounded-xl shadow-2xl animate-fadeInUp">
                
                <!-- Logo de la Empresa (usando tu lógica PHP) -->
                <div class="flex justify-center">
                    <?php if (file_exists($logo_path)): ?>
                        <img src="<?= htmlspecialchars($logo_path) ?>" alt="Logo de la Empresa" class="w-48 h-auto mx-auto mb-4">
                    <?php else: ?>
                        <h1 class="text-3xl font-bold text-center text-white">Dunosusa Logística</h1>
                    <?php endif; ?>
                </div>

                <p class="text-center text-gray-200">Ingresa tus credenciales para acceder al panel.</p>

                <!-- Mostrar mensajes de error o éxito (usando tu lógica PHP y nuevos estilos) -->
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="p-4 text-sm text-red-100 bg-red-600/50 rounded-lg text-center font-medium">
                        <?= htmlspecialchars($_SESSION['error_message']); ?>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="p-4 text-sm text-green-100 bg-green-600/50 rounded-lg text-center font-medium">
                        <?= htmlspecialchars($_SESSION['success_message']); ?>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>

                <!-- Formulario (usando tu variable $login_process_path) -->
                <form class="space-y-6" action="<?= htmlspecialchars($login_process_path) ?>" method="POST">
                    
                    <!-- Campo de Correo Electrónico con icono -->
                    <div class="relative">
                        <label for="email" class="block text-sm font-medium text-gray-200">Correo Electrónico:</label>
                        <div class="absolute inset-y-0 left-0 pl-3 pt-5 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <input type="email" id="email" name="email" required
                               class="w-full pl-10 pr-3 py-3 mt-1 text-white bg-white/20 border border-gray-400/50 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all">
                    </div>

                    <!-- Campo de Contraseña con icono -->
                    <div class="relative">
                        <label for="contrasena" class="block text-sm font-medium text-gray-200">Contraseña:</label>
                        <div class="absolute inset-y-0 left-0 pl-3 pt-5 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input type="password" id="contrasena" name="contrasena" required
                               class="w-full pl-10 pr-3 py-3 mt-1 text-white bg-white/20 border border-gray-400/50 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all">
                    </div>

                    <!-- Botón de Iniciar Sesión -->
                    <button type="submit" 
                            class="w-full py-3 px-4 font-bold text-white bg-blue-600 rounded-md shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-75 transition-all transform hover:scale-105">
                        Iniciar Sesión
                    </button>
                </form>
            </div>
            
            <p class="mt-8 text-center text-sm text-gray-300">
                ¿Necesitas acceso? Contacta a tu administrador de empresa.
            </p>

        </div>
    </div>

</body>
</html>

