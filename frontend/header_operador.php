<?php
    /* * La lógica de autenticación y sesión debe ir ANTES 
     * de cualquier salida HTML.
     */
    
    // Usamos 'defined' para evitar el error si se incluye accidentalmente dos veces
    if (!defined('ROL_REQUERIDO')) {
        define('ROL_REQUERIDO', 2); // ROL_REQUERIDO para operador
    }
    require_once '../backend/auth_guard.php'; // Protege la página
    
    // Obtenemos el nombre del operador para mostrarlo en el header
    $nombre_operador = $_SESSION['usuario_nombre'] ?? 'Operador';
    $apellido_operador = $_SESSION['usuario_apellidos'] ?? '';
    $nombre_completo = trim($nombre_operador . ' ' . $apellido_operador);

    // --- Obtenemos el nombre del archivo actual ---
    $currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Operador | Rutitruck</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        darkMode: 'class' // Solo si quieres dark mode
      }
    </script>
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> 

    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Lato', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-gray-100 text-gray-800">
    <div 
        x-data="{ 
            mobileMenuOpen: false,
            showNotification: false,
            notificationMessage: '',
            notificationType: 'success'
        }" 
        x-init="() => {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const message = urlParams.get('message');

            if (status === 'success') {
                notificationType = 'success';
                notificationMessage = message ? decodeURIComponent(message) : '¡Operación completada con éxito!';
                showNotification = true;
                setTimeout(() => { showNotification = false }, 5000); // Ocultar después de 5 seg
            } else if (status === 'error') {
                notificationType = 'error';
                notificationMessage = message ? decodeURIComponent(message) : 'Ocurrió un error inesperado.';
                showNotification = true;
                setTimeout(() => { showNotification = false }, 7000); // Más tiempo para errores
            }
        }"
    >
        
        <div 
            x-show="showNotification"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-300 transform"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed top-0 right-0 z-50 p-4 m-6 max-w-sm w-full rounded-lg shadow-lg"
            :class="notificationType === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'"
            x-cloak
        >
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg x-show="notificationType === 'success'" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <svg x-show="notificationType === 'error'" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="font-bold" x-text="notificationType === 'success' ? 'Éxito' : 'Error'"></p>
                    <p class="text-sm" x-text="notificationMessage"></p>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button @click="showNotification = false" class="text-white/70 hover:text-white">
                        <span class="sr-only">Cerrar</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
        </div>
        <header class="bg-white shadow-md sticky top-0 z-20">
            <nav class="container mx-auto px-6 py-4" x-data="{ mobileMenuOpen: false }">
                <div class="flex justify-between items-center">
                    
                    <div>
                        <a href="menu_trailero.php">
                            <img src="assets/Logotipo.png" alt="Rutitruck" class="h-10 w-auto">
                        </a>
                    </div>

                    <div class="hidden md:flex items-center space-x-4">
                        <span class="text-sm font-medium text-gray-700">Bienvenido, <?= htmlspecialchars($nombre_operador) ?></span>
                        <a href="menu_trailero.php" class="px-2 py-1 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'menu_trailero.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Inicio (Mis Viajes)</a>
                        
                        <a href="operador_historial.php" class="px-2 py-1 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'operador_historial.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Historial</a>
                        
                        <a href="trailer_asignado.php" class="px-2 py-1 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'trailer_asignado.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Mi Vehículo</a>
                        <a href="alertas.php" class="px-2 py-1 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'alertas.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Alertas</a>
                        <a href="perfil.php" class="px-2 py-1 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'perfil.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Mi Perfil</a>
                        <a href="contacto_soporte.php" class="px-2 py-1 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'contacto_soporte.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Soporte</a>
                        <a href="../backend/logout.php" class="block md:inline-block bg-red-500 text-white text-center px-4 py-2 rounded-md hover:bg-red-600">Cerrar Sesión</a>
                    </div>
                    
                    <div class="md:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-600 hover:text-blue-600 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>

                </div>

                <div x-show="mobileMenuOpen" class="md:hidden mt-4 space-y-2" x-cloak>
                    <a href="menu_trailero.php" class="block px-3 py-2 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'menu_trailero.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Inicio (Mis Viajes)</a>
                    
                    <a href="operador_historial.php" class="block px-3 py-2 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'operador_historial.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Historial</a>

                    <a href="trailer_asignado.php" class="block px-3 py-2 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'trailer_asignado.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Mi Vehículo</a>
                    <a href="alertas.php" class="block px-3 py-2 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'alertas.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Alertas</a>
                    <a href="perfil.php" class="block px-3 py-2 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'perfil.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Mi Perfil</a>
                    <a href="contacto_soporte.php" class="block px-3 py-2 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'contacto_soporte.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Soporte</a>
                    <a href="../backend/logout.php" class="block mt-2 md:mt-0 bg-red-500 text-white text-center px-4 py-2 rounded-md hover:bg-red-600">Cerrar Sesión</a>
                </div>
            </nav>
        </header>

        <main class="container mx-auto p-6 md:p-10">