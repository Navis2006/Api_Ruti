<?php
define('ROL_REQUERIDO', 2);
require_once '../backend/auth_guard.php'; 
require_once '../backend/config/db_connection.php'; 
require_once 'header_operador.php'; // Carga la cabecera

$vehiculo_id = filter_input(INPUT_GET, 'vehiculo_id', FILTER_VALIDATE_INT);
$historial = [];
$vehiculo_nombre = '';

if ($vehiculo_id) {
    try {
        // Obtenemos el nombre del vehículo
        $stmt_nombre = $pdo->prepare("SELECT nombre, placa FROM vehiculos WHERE vehiculo_id = ?");
        $stmt_nombre->execute([$vehiculo_id]);
        $vehiculo = $stmt_nombre->fetch();
        $vehiculo_nombre = $vehiculo ? ($vehiculo['nombre'] . ' (' . $vehiculo['placa'] . ')') : 'Vehículo Desconocido';

        // CONSULTA (¡TU AMIGO DEBE CREAR ESTA TABLA!)
        // Esta consulta asume que tienes una tabla 'mantenimientos'
        $stmt_historial = $pdo->prepare("
            SELECT * FROM mantenimientos 
            WHERE vehiculo_id = ? 
            ORDER BY fecha_realizacion DESC
        ");
        $stmt_historial->execute([$vehiculo_id]);
        $historial = $stmt_historial->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        // Si la tabla 'mantenimientos' no existe, $historial quedará vacío
        $error_db = "Error al cargar el historial: " . $e->getMessage();
    }
}
?>

<style>
    .card {
        background-color: #ffffff; padding: 1.5rem; border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }
    .card-empty { text-align: center; color: #6B7280; }
</style>

<header class="mb-8">
    <h1 class="text-3xl font-bold">Historial de Mantenimiento</h1>
    <p class="text-gray-500">Revisión de todos los servicios realizados al vehículo: <?= htmlspecialchars($vehiculo_nombre) ?></p>
</header>

<div class="space-y-6">
    
    <?php if (isset($error_db)): ?>
        <div class="card card-empty bg-red-100 text-red-700">
            <p><strong>Error de Base de Datos:</strong> <?= htmlspecialchars($error_db) ?></p>
            <p class="mt-2">Es probable que la tabla `mantenimientos` no exista.</p>
        </div>
    <?php elseif (empty($historial)): ?>
        <div class="card card-empty">
            <p class="text-gray-600">No hay registros de mantenimiento para este vehículo.</p>
        </div>
    <?php else: ?>
        <?php foreach ($historial as $mantenimiento): ?>
            <div class="card">
                <div class="flex justify-between items-center mb-2">
                    <h2 class="text-xl font-bold text-blue-600">
                        Servicio ID: <?= htmlspecialchars($mantenimiento['mantenimiento_id']) ?>
                    </h2>
                    <span class="text-sm font-medium text-gray-600">
                        <?= htmlspecialchars(date('d M, Y', strtotime($mantenimiento['fecha_realizacion']))) ?>
                    </span>
                </div>
                
                <p class="font-semibold text-gray-800">Tipo de Servicio: <?= htmlspecialchars($mantenimiento['tipo_servicio']) ?></p>
                <p class="mt-2 text-gray-700"><strong>Descripción:</strong> <?= htmlspecialchars($mantenimiento['descripcion']) ?></p>
                <p class="text-sm text-gray-500 mt-2"><strong>Costo:</strong> $<?= htmlspecialchars(number_format($mantenimiento['costo'], 2)) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="text-center mt-8">
        <a href="trailer_asignado.php" class="text-blue-600 hover:underline">&larr; Volver a Mi Vehículo</a>
    </div>
</div>

<?php
require_once 'footer_operador.php'; // Cierra la página
?>