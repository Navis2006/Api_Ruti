<?php
    define('ROL_REQUERIDO', 2);
require_once '../backend/auth_guard.php'; // ¡PROTEGER ESTA PÁGINA!
// require_once '../backend/get_travels_data.php'; // Incluye la función para obtener viajes
require_once '../backend/config/db_connection.php'; // Necesario para la función
require_once 'header_operador.php'; // Carga la cabecera del operador

// Simulación de datos si la función no existe
if (function_exists('getTravelsForTrailero')) {
    $viajes = getTravelsForTrailero($pdo, $_SESSION['usuario_id']);
} else {
    // Datos de ejemplo si la función no está
    $viajes = [
        [
            'viaje_id' => 101,
            'nombre_ruta' => 'Ruta Centro',
            'estado_viaje' => 'En curso',
            'fecha_asignacion' => '2025-10-22 10:00:00',
            'fecha_inicio' => '2025-10-22 12:05:00',
            'fecha_finalizacion' => '2025-10-23 18:00:00',
            'descripcion_ruta' => 'Entrega de mercancía en tiendas del centro.'
        ],
        [
            'viaje_id' => 102,
            'nombre_ruta' => 'Ruta Periférico',
            'estado_viaje' => 'Planeado',
            'fecha_asignacion' => '2025-10-22 15:00:00',
            'fecha_inicio' => null,
            'fecha_finalizacion' => null,
            'descripcion_ruta' => 'Recolección en bodega y entrega en CEDIS.'
        ]
    ];
}
?>

<header class="mb-8">
    <h1 class="text-3xl font-bold">Mis Viajes Asignados</h1>
    <p class="text-gray-500">Aquí puedes consultar todos los viajes que te han sido asignados y su estado.</p>
</header>

<div class="space-y-6">
    <?php if (!empty($viajes)): ?>
        <?php foreach ($viajes as $viaje): ?>
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-2">
                    <h2 class="text-xl font-bold text-red-600">Viaje ID: <?= htmlspecialchars($viaje['viaje_id']); ?></h2>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full mt-2 sm:mt-0
                        <?php 
                            $estado = $viaje['estado_viaje'];
                            if ($estado == 'En curso') echo 'bg-green-100 text-green-800';
                            elseif ($estado == 'Planeado') echo 'bg-yellow-100 text-yellow-800';
                            else echo 'bg-gray-100 text-gray-800';
                        ?>">
                        <?= htmlspecialchars($estado); ?>
                    </span>
                </div>
                
                <h3 class="text-lg font-semibold mb-2">Ruta: <?= htmlspecialchars($viaje['nombre_ruta']); ?></h3>
                <p class="text-gray-700 mb-4"><?= htmlspecialchars($viaje['descripcion_ruta']); ?></p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Fecha Asignación:</p>
                        <p class="font-medium"><?= htmlspecialchars($viaje['fecha_asignacion']); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-500">Fecha Inicio:</p>
                        <p class="font-medium"><?= htmlspecialchars($viaje['fecha_inicio'] ?? 'N/A'); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-500">Fecha Finalización:</p>
                        <p class="font-medium"><?= htmlspecialchars($viaje['fecha_finalizacion'] ?? 'N/A'); ?></p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button class="px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">Ver Mapa de Ruta</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="bg-white p-6 rounded-lg shadow-sm text-center">
            <p class="text-gray-600">No tienes viajes asignados en este momento.</p>
        </div>
    <?php endif; ?>

    <p class="text-center text-gray-500 pt-4">Reporta cualquier actualización o incidencia relacionada con tus viajes.</p>
</div>

<?php
require_once 'footer_operador.php'; // Cierra la página
?>

<!-- Se borrara -->
