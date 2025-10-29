<?php
    define('ROL_REQUERIDO', 1);
require_once '../backend/auth_guard.php'; // Protege la página para gerentes
require_once 'header_admin.php'; // Carga la cabecera y el menú lateral
?>

<header class="mb-8">
    <h1 class="text-3xl font-bold">Bienvenido al Panel de Administración</h1>
    <p class="text-gray-500">Utiliza el menú de la izquierda para gestionar la plataforma.</p>
</header>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <a href="admin_registro.php" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
        <h2 class="text-xl font-bold mb-2 text-blue-600">Gestionar Usuarios</h2>
        <p class="text-sm text-gray-600">Dar de alta, editar y administrar operadores y gerentes.</p>
    </a>
    <a href="admin_gestionar_vehiculos.php" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
        <h2 class="text-xl font-bold mb-2 text-blue-600">Gestionar Vehículos</h2>
        <p class="text-sm text-gray-600">Administrar el inventario de vehículos, sus detalles y estado.</p>
    </a>
    <a href="admin_gestionar_rutas.php" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
        <h2 class="text-xl font-bold mb-2 text-blue-600">Gestionar Rutas</h2>
        <p class="text-sm text-gray-600">Crear, editar o eliminar rutas y sus trazados geométricos.</p>
    </a>
    <a href="admin_gestionar_viajes.php" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
        <h2 class="text-xl font-bold mb-2 text-blue-600">Gestionar Viajes</h2>
        <p class="text-sm text-gray-600">Asignar, monitorear y actualizar el estado de los viajes.</p>
    </a>
    <a href="admin_gestionar_alertas.php" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
        <h2 class="text-xl font-bold mb-2 text-blue-600">Gestionar Alertas</h2>
        <p class="text-sm text-gray-600">Crear y administrar alertas para las rutas y operadores.</p>
    </a>
    <a href="admin_gestionar_empresas.php" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
        <h2 class="text-xl font-bold mb-2 text-blue-600">Gestionar Empresas</h2>
        <p class="text-sm text-gray-600">Administrar los datos de las empresas asociadas.</p>
    </a>
</div>

<?php
require_once 'footer.php'; // Cierra la página
?>