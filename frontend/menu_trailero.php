<?php
define('ROL_REQUERIDO', 2);
require_once '../backend/auth_guard.php'; 
require_once '../backend/config/db_connection.php'; 
require_once 'header_operador.php'; // Carga la cabecera del operador
?>

<header class="mb-8">
    <h1 class="text-3xl font-bold">Panel del Operador</h1>
    <p class="text-gray-500">Selecciona una opción para gestionar tus asignaciones.</p>
</header>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <a href="rutas_asignadas.php" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
        <h2 class="text-xl font-bold mb-2 text-blue-600">Mis Viajes</h2>
        <p class="text-sm text-gray-600">Consulta el detalle de tus viajes asignados.</p>
    </a>
    <a href="trailer_asignado.php" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
        <h2 class="text-xl font-bold mb-2 text-blue-600">Mi Vehículo</h2>
        <p class="text-sm text-gray-600">Verifica la información de tu vehículo asignado.</p>
    </a>
    <a href="alertas.php" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
        <h2 class="text-xl font-bold mb-2 text-blue-600">Alertas</h2>
        <p class="text-sm text-gray-600">Revisa las alertas importantes.</p>
    </a>
</div>

<?php
require_once 'footer_operador.php'; // Cierra la página
?>
