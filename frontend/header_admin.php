<?php
    /* * La lógica de autenticación y sesión debe ir ANTES 
     * de cualquier salida HTML.
     */
    
    // Usamos 'defined' para evitar el error si se incluye accidentalmente dos veces
    if (!defined('ROL_REQUERIDO')) {
        define('ROL_REQUERIDO', 1);
    }
    require_once '../backend/auth_guard.php'; // Protege la página
    
    // --- NUEVO: Obtenemos el nombre del archivo actual ---
    $currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración | Dunosusa</title>

    <script src="https://cdn.tailwindcss.com"></script> 

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon">
</head>

<body class="bg-gray-100">

    <div x-data="{ desktopSidebarOpen: true, mobileMenuOpen: false }" class="flex h-screen">

        <div 
            x-show="mobileMenuOpen" 
            class="fixed inset-0 z-40 flex lg:hidden" 
            x-cloak
            @keydown.escape.window="mobileMenuOpen = false"
        >
            <div 
                @click="mobileMenuOpen = false" 
                class="fixed inset-0 bg-black bg-opacity-30" 
                aria-hidden="true"
                x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            ></div>

            <div 
                class="relative flex-1 flex flex-col max-w-xs w-full bg-white shadow-lg"
                x-transition:enter="transition ease-in-out duration-300 transform"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in-out duration-300 transform"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
            >
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button @click="mobileMenuOpen = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                    <div class="flex-shrink-0 flex items-center px-4">
                        <h2 class="text-2xl font-bold text-blue-600">Dunosusa</h2>
                    </div>
                    <nav class="mt-5 px-2 space-y-1">
                        <a href="menu_admin.php" class="flex items-center px-3 py-2 text-base font-medium rounded-md <?php echo ($currentPage == 'menu_admin.php') ? 'text-blue-700 bg-blue-100' : 'text-gray-600 hover:bg-gray-50'; ?>">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            Dashboard
                        </a>
                        <a href="admin_gestionar_vehiculos.php" class="flex items-center px-3 py-2 text-base font-medium rounded-md <?php echo ($currentPage == 'admin_gestionar_vehiculos.php') ? 'text-blue-700 bg-blue-100' : 'text-gray-600 hover:bg-gray-50'; ?>">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2-2h8zM13 16h2m-2-7h2m0 0H9.692l-2-4H4.382l-2 4H13z"></path></svg>
                            Vehículos
                        </a>
                        <a href="admin_gestionar_rutas.php" class="flex items-center px-3 py-2 text-base font-medium rounded-md <?php echo ($currentPage == 'admin_gestionar_rutas.php') ? 'text-blue-700 bg-blue-100' : 'text-gray-600 hover:bg-gray-50'; ?>">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Rutas
                        </a>
                        <a href="admin_registro.php" class="flex items-center px-3 py-2 text-base font-medium rounded-md <?php echo ($currentPage == 'admin_registro.php') ? 'text-blue-700 bg-blue-100' : 'text-gray-600 hover:bg-gray-50'; ?>">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Usuarios
                        </a>
                        <a href="admin_gestionar_viajes.php" class="flex items-center px-3 py-2 text-base font-medium rounded-md <?php echo ($currentPage == 'admin_gestionar_viajes.php') ? 'text-blue-700 bg-blue-100' : 'text-gray-600 hover:bg-gray-50'; ?>">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7l4-4m0 0l4 4m-4-4v18"></path></svg>
                            Viajes
                        </a>
                        <a href="admin_gestionar_alertas.php" class="flex items-center px-3 py-2 text-base font-medium rounded-md <?php echo ($currentPage == 'admin_gestionar_alertas.php') ? 'text-blue-700 bg-blue-100' : 'text-gray-600 hover:bg-gray-50'; ?>">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341A6.002 6.002 0 006 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            Alertas
                        </a>
                        <a href="admin_gestionar_empresas.php" class="flex items-center px-3 py-2 text-base font-medium rounded-md <?php echo ($currentPage == 'admin_gestionar_empresas.php') ? 'text-blue-700 bg-blue-100' : 'text-gray-600 hover:bg-gray-50'; ?>">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                            Empresas
                        </a>
                    </nav>
                    </div>
                <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
                    <a href="logout.php" class="flex-shrink-0 group block">
                        <div class="flex items-center">
                            <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-red-100">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </span>
                            <div class="ml-3">
                                <p class="text-base font-medium text-red-600 group-hover:text-red-800">Cerrar Sesión</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="flex-shrink-0 w-14" aria-hidden="true"></div>
        </div>

        <aside 
            :class="desktopSidebarOpen ? 'w-64' : 'w-20'"
            class="hidden lg:flex flex-col flex-shrink-0 bg-white border-r border-gray-200 shadow-md transition-all duration-300 ease-in-out"
        >
            <div class="flex flex-col h-full">
                <div class="h-16 flex items-center justify-center border-b">
                    <h2 class="text-2xl font-bold text-blue-600" x-show="desktopSidebarOpen">Dunosusa</h2>
                    <span class="text-2xl font-bold text-blue-600" x-show="!desktopSidebarOpen" x-cloak>D</span>
                </div>

                <nav class="flex-1 overflow-y-auto pt-4">
                    <a href="menu_admin.php" class="flex items-center px-6 py-3 <?php echo ($currentPage == 'menu_admin.php') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'; ?>" :class="{ 'justify-center': !desktopSidebarOpen }">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        <span class="ml-4 font-medium" x-show="desktopSidebarOpen" x-cloak>Dashboard</span>
                    </a>
                    <a href="admin_gestionar_vehiculos.php" class="flex items-center px-6 py-3 <?php echo ($currentPage == 'admin_gestionar_vehiculos.php') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'; ?>" :class="{ 'justify-center': !desktopSidebarOpen }">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2-2h8zM13 16h2m-2-7h2m0 0H9.692l-2-4H4.382l-2 4H13z"></path></svg>
                        <span class="ml-4 font-medium" x-show="desktopSidebarOpen" x-cloak>Vehículos</span>
                    </a>
                    <a href="admin_gestionar_rutas.php" class="flex items-center px-6 py-3 <?php echo ($currentPage == 'admin_gestionar_rutas.php') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'; ?>" :class="{ 'justify-center': !desktopSidebarOpen }">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="ml-4 font-medium" x-show="desktopSidebarOpen" x-cloak>Rutas</span>
                    </a>
                    <a href="admin_registro.php" class="flex items-center px-6 py-3 <?php echo ($currentPage == 'admin_registro.php') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'; ?>" :class="{ 'justify-center': !desktopSidebarOpen }">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span class="ml-4 font-medium" x-show="desktopSidebarOpen" x-cloak>Usuarios</span>
                    </a>
                    <a href="admin_gestionar_viajes.php" class="flex items-center px-6 py-3 <?php echo ($currentPage == 'admin_gestionar_viajes.php') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'; ?>" :class="{ 'justify-center': !desktopSidebarOpen }">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7l4-4m0 0l4 4m-4-4v18"></path></svg>
                        <span class="ml-4 font-medium" x-show="desktopSidebarOpen" x-cloak>Viajes</span>
                    </a>
                    <a href="admin_gestionar_alertas.php" class="flex items-center px-6 py-3 <?php echo ($currentPage == 'admin_gestionar_alertas.php') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'; ?>" :class="{ 'justify-center': !desktopSidebarOpen }">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341A6.002 6.002 0 006 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span class="ml-4 font-medium" x-show="desktopSidebarOpen" x-cloak>Alertas</span>
                    </a>
                    <a href="admin_gestionar_empresas.php" class="flex items-center px-6 py-3 <?php echo ($currentPage == 'admin_gestionar_empresas.php') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'; ?>" :class="{ 'justify-center': !desktopSidebarOpen }">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                        <span class="ml-4 font-medium" x-show="desktopSidebarOpen" x-cloak>Empresas</span>
                    </a>
                </nav>
                <div class="border-t p-4">
                    <a href="logout.php" class="flex items-center px-4 py-2 text-red-600 bg-red-100 rounded-lg hover:bg-red-200" :class="{ 'justify-center': !desktopSidebarOpen }">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        <span class="ml-3 font-medium" x-show="desktopSidebarOpen" x-cloak>Cerrar Sesión</span>
                    </a>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-y-auto">
            
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 lg:px-8">
                
                <button 
                    @click="mobileMenuOpen = true" 
                    class="p-2 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 lg:hidden"
                >
                    <span class="sr-only">Abrir menú</span>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>

                <button 
                    @click="desktopSidebarOpen = !desktopSidebarOpen" 
                    class="hidden lg:inline-flex p-2 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700"
                >
                    <span class="sr-only">Colapsar menú</span>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                
                <div class="flex-1"></div>

                <div class="ml-4 flex items-center">
                    <span class="text-sm font-medium text-gray-700">Bienvenido, Admin</span>
                    <img class="ml-3 h-8 w-8 rounded-full" src="https://via.placeholder.com/150/0000FF/808080?text=A" alt="Avatar">
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
<?php
// Fin de header_admin.php
?>