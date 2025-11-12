<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Carga de Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Carga de Fuente LATO -->
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="css/style.css">
    <title>Panel de Administración</title>
    <style>
        body { font-family: 'Lato', sans-serif; }
        .sidebar-icon { width: 24px; height: 24px; }
    </style>
</head>

<body class="bg-gray-100 text-gray-800">
    <div class="flex h-screen">

        <!-- Sidebar -->
        <aside class="w-20 md:w-64 bg-white shadow-md flex flex-col">
            <div class="flex items-center justify-center h-20 border-b">
                <h1 class="text-xl md:text-2xl font-bold text-blue-600">Dunosusa</h1>
            </div>
            <nav class="flex-1 px-4 py-8 space-y-4">
                <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
                <a href="menu_admin.php" class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-200 
                   <?= ($currentPage == 'menu_admin.php') ? 'bg-blue-100 text-blue-600' : '' ?>">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="hidden md:inline">Dashboard</span>
                </a>
                <a href="admin_gestionar_vehiculos.php" class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-200
                   <?= ($currentPage == 'admin_gestionar_vehiculos.php') ? 'bg-blue-100 text-blue-600' : '' ?>">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="hidden md:inline">Vehículos</span>
                </a>
                <a href="admin_gestionar_rutas.php" class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-200
                   <?= ($currentPage == 'admin_gestionar_rutas.php') ? 'bg-blue-100 text-blue-600' : '' ?>">
                     <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                    <span class="hidden md:inline">Rutas</span>
                </a>
                <a href="admin_registro.php" class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-200
                   <?= ($currentPage == 'admin_registro.php') ? 'bg-blue-100 text-blue-600' : '' ?>">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="hidden md:inline">Usuarios</span>
                </a>
                <a href="admin_gestionar_viajes.php" class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-200
                   <?= ($currentPage == 'admin_gestionar_viajes.php') ? 'bg-blue-100 text-blue-600' : '' ?>">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7l4-4m0 0l4 4m-4-4v18"></path></svg>
                    <span class="hidden md:inline">Viajes</span>
                </a>
                <a href="admin_gestionar_alertas.php" class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-200
                   <?= ($currentPage == 'admin_gestionar_alertas.php') ? 'bg-blue-100 text-blue-600' : '' ?>">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span class="hidden md:inline">Alertas</span>
                </a>
                <a href="admin_gestionar_empresas.php" class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-200
                   <?= ($currentPage == 'admin_gestionar_empresas.php') ? 'bg-blue-100 text-blue-600' : '' ?>">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m-1 4h1m6-4h1m-1 4h1m-1-8h1m-1 4h1m-1 4h1"></path></svg>
                    <span class="hidden md:inline">Empresas</span>
                </a>
            </nav>
            <div class="px-4 py-4 border-t">
                <a href="../backend/logout.php" class="w-full flex items-center justify-center md:justify-start space-x-4 p-3 bg-red-100 text-red-600 hover:bg-red-200 rounded-lg">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span class="hidden md:inline">Cerrar Sesión</span>
                </a>
            </div>
        </aside>
        <!-- Contenido Principal -->
        <main class="flex-1 p-6 md:p-10 overflow-y-auto">
    <!-- El contenido específico de cada página PHP irá aquí -->