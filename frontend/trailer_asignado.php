<?php
    define('ROL_REQUERIDO', 2);
// require_once '../backend/auth_guard.php'; // Descomentar si es necesario
require_once 'header_operador.php'; // Carga la cabecera del operador

// Aquí deberías tener lógica PHP para obtener los datos del vehículo
// asignado al $_SESSION['usuario_id']
// $vehiculo = getVehicleForTrailero($pdo, $_SESSION['usuario_id']);

// Datos de ejemplo si no hay lógica de backend
$vehiculo = [
    'vehiculo_id' => 501,
    'nombre' => 'Kenworth T680-AXN',
    'placa' => 'TRK-501',
    'tipo' => 'Tractocamión',
    'altura_metros' => 4.00,
    'ancho_metros' => 2.50,
    'largo_metros' => 16.50,
    'peso_toneladas' => 20.00,
    'empresa_nombre' => 'Transportes Rápidos S.A. de C.V.'
];
?>

<header class="mb-8">
    <h1 class="text-3xl font-bold">Mi Vehículo Asignado</h1>
    <p class="text-gray-500">Información detallada sobre el vehículo que tienes asignado para tus viajes.</p>
</header>

<!-- Este contenido debe venir de la base de datos -->
<div class="bg-white p-6 rounded-lg shadow-sm max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold mb-4 text-blue-600">
        Vehículo ID: <?= htmlspecialchars($vehiculo['vehiculo_id']) ?> (<?= htmlspecialchars($vehiculo['nombre']) ?>)
    </h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <p class="text-sm text-gray-500">Placa</p>
            <p class="text-lg font-medium font-mono"><?= htmlspecialchars($vehiculo['placa']) ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Tipo</p>
            <p class="text-lg font-medium"><?= htmlspecialchars($vehiculo['tipo']) ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Altura</p>
            <p class="text-lg font-medium"><?= htmlspecialchars($vehiculo['altura_metros']) ?> metros</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Ancho</p>
            <p class="text-lg font-medium"><?= htmlspecialchars($vehiculo['ancho_metros']) ?> metros</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Largo</p>
            <p class="text-lg font-medium"><?= htmlspecialchars($vehiculo['largo_metros']) ?> metros</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Peso</p>
            <p class="text-lg font-medium"><?= htmlspecialchars($vehiculo['peso_toneladas']) ?> toneladas</p>
        </div>
    </div>
    
    <div class="mt-6 border-t pt-4">
        <p class="text-sm text-gray-500">Empresa Propietaria</p>
        <p class="text-lg font-medium"><?= htmlspecialchars($vehiculo['empresa_nombre']) ?></p>
    </div>

    <button class="mt-6 px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">Ver Historial de Mantenimiento</button>
</div>

<p class="text-center text-gray-500 pt-8">Reporta cualquier anomalía o necesidad de mantenimiento urgente.</p>

<?php
require_once 'footer_operador.php'; // Cierra la página
?>
