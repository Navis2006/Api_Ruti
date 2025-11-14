<?php
    // La autenticación ya se manejó en el header
    require_once 'header_admin.php'; // Carga la cabecera y el menú lateral
?>

<header class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Bienvenido al Panel de Administración</h1>
    <p class="text-gray-500 mt-1">Utiliza el menú para gestionar la plataforma.</p>
</header>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    
    <a href="admin_registro.php" class="group bg-white p-6 rounded-lg border border-gray-200 shadow-md hover:shadow-lg hover:border-blue-500 transition-all duration-300 transform hover:-translate-y-1">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-blue-100 text-blue-600 rounded-full group-hover:bg-blue-600 group-hover:text-white transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 016-6h6a6 6 0 016 6v1h-3M15 21h6m-3-3v6"></path></svg>
            </div>
            <span class="text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity transform group-hover:translate-x-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </span>
        </div>
        <h2 class="text-xl font-bold mb-2 text-gray-800">Gestionar Usuarios</h2>
        <p class="text-sm text-gray-600">Dar de alta, editar y administrar operadores y gerentes.</p>
    </a>

    <a href="admin_gestionar_vehiculos.php" class="group bg-white p-6 rounded-lg border border-gray-200 shadow-md hover:shadow-lg hover:border-blue-500 transition-all duration-300 transform hover:-translate-y-1">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-blue-100 text-blue-600 rounded-full group-hover:bg-blue-600 group-hover:text-white transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2-2h8zM13 16h2m-2-7h2m0 0H9.692l-2-4H4.382l-2 4H13z"></path></svg>
            </div>
            <span class="text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity transform group-hover:translate-x-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </span>
        </div>
        <h2 class="text-xl font-bold mb-2 text-gray-800">Gestionar Vehículos</h2>
        <p class="text-sm text-gray-600">Administrar el inventario de vehículos, sus detalles y estado.</p>
    </a>

    <a href="admin_gestionar_rutas.php" class="group bg-white p-6 rounded-lg border border-gray-200 shadow-md hover:shadow-lg hover:border-blue-500 transition-all duration-300 transform hover:-translate-y-1">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-blue-100 text-blue-600 rounded-full group-hover:bg-blue-600 group-hover:text-white transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <span class="text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity transform group-hover:translate-x-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </span>
        </div>
        <h2 class="text-xl font-bold mb-2 text-gray-800">Gestionar Rutas</h2>
        <p class="text-sm text-gray-600">Crear, editar o eliminar rutas y sus trazados geométricos.</p>
    </a>

    <a href="admin_gestionar_viajes.php" class="group bg-white p-6 rounded-lg border border-gray-200 shadow-md hover:shadow-lg hover:border-blue-500 transition-all duration-300 transform hover:-translate-y-1">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-blue-100 text-blue-600 rounded-full group-hover:bg-blue-600 group-hover:text-white transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7l4-4m0 0l4 4m-4-4v18"></path></svg>
            </div>
            <span class="text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity transform group-hover:translate-x-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </span>
        </div>
        <h2 class="text-xl font-bold mb-2 text-gray-800">Gestionar Viajes</h2>
        <p class="text-sm text-gray-600">Asignar, monitorear y actualizar el estado de los viajes.</p>
    </a>

    <a href="admin_gestionar_alertas.php" class="group bg-white p-6 rounded-lg border border-gray-200 shadow-md hover:shadow-lg hover:border-blue-500 transition-all duration-300 transform hover:-translate-y-1">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-blue-100 text-blue-600 rounded-full group-hover:bg-blue-600 group-hover:text-white transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341A6.002 6.002 0 006 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            </div>
            <span class="text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity transform group-hover:translate-x-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </span>
        </div>
        <h2 class="text-xl font-bold mb-2 text-gray-800">Gestionar Alertas</h2>
        <p class="text-sm text-gray-600">Crear y administrar alertas para las rutas y operadores.</p>
    </a>

    <a href="admin_gestionar_empresas.php" class="group bg-white p-6 rounded-lg border border-gray-200 shadow-md hover:shadow-lg hover:border-blue-500 transition-all duration-300 transform hover:-translate-y-1">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-blue-100 text-blue-600 rounded-full group-hover:bg-blue-600 group-hover:text-white transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
            </div>
            <span class="text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity transform group-hover:translate-x-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </span>
        </div>
        <h2 class="text-xl font-bold mb-2 text-gray-800">Gestionar Empresas</h2>
        <p class="text-sm text-gray-600">Administrar los datos de las empresas asociadas.</p>
    </a>

</div>

<?php
    require_once 'footer.php'; // Cierra la página
?>