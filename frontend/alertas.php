<?php
    define('ROL_REQUERIDO', 2);
// require_once '../backend/auth_guard.php'; // Descomentar si es necesario
require_once 'header_operador.php'; // Carga la cabecera del operador

// Aquí deberías tener lógica PHP para cargar las alertas
// $alertas = getAlertasForTrailero($pdo, $_SESSION['usuario_id']);
?>

<header class="mb-8">
    <h1 class="text-3xl font-bold">Mis Alertas</h1>
    <p class="text-gray-500">Revisa las alertas importantes relacionadas con tus viajes o vehículos.</p>
</header>

<div class="space-y-6">
    <!-- Este contenido debe venir de la base de datos -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h2 class="text-xl font-bold mb-2 text-yellow-600">Alerta ID: 201 - ¡Precaución en Ruta!</h2>
        <p class="font-semibold"><strong>Tipo de Alerta:</strong> CLIMA_ADVERSO</p>
        <p class="mt-2 text-gray-700"><strong>Descripción:</strong> Se esperan lluvias intensas y niebla en la carretera 57 a la altura de San Juan del Río. Conduzca con extrema precaución.</p>
        <p class="text-sm text-gray-500 mt-2"><strong>Ubicación:</strong> San Juan del Río, Querétaro</p>
        <p class="text-sm text-gray-500"><strong>Creado por:</strong> Despacho Central</p>
        <button class="mt-4 px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">Marcar como Leída</button>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h2 class="text-xl font-bold mb-2 text-blue-600">Alerta ID: 202 - Revisión de Vehículo</h2>
        <p class="font-semibold"><strong>Tipo de Alerta:</strong> MANTENIMIENTO_PENDIENTE</p>
        <p class="mt-2 text-gray-700"><strong>Descripción:</strong> Próximo mantenimiento preventivo para el vehículo TRK-501 agendado para el 2023-11-05.</p>
        <p class="text-sm text-gray-500 mt-2"><strong>Ubicación:</strong> Taller Central Guadalajara</p>
        <p class="text-sm text-gray-500"><strong>Creado por:</strong> Departamento de Mantenimiento</p>
        <button class="mt-4 px-4 py-2 bg-gray-200 text-gray-800 text-sm rounded-md hover:bg-gray-300">Ver Detalles</button>
    </div>

    <p class="text-center text-gray-500 pt-4">Si tienes información adicional sobre una alerta, comunícala a la brevedad.</p>
</div>

<?php
require_once 'footer_operador.php'; // Cierra la página
?>
