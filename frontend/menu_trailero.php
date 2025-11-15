<?php
define('ROL_REQUERIDO', 2);
require_once '../backend/auth_guard.php'; 
require_once '../backend/config/db_connection.php'; 
require_once 'header_operador.php'; // Carga la cabecera del operador

// --- 1. OBTENER LOS VIAJES DE ESTE OPERADOR ---
$operador_id = $_SESSION['usuario_id']; // Asumimos que auth_guard.php define esto

try {
    // Consulta para el VIAJE ACTUAL (solo debe haber uno "En Curso")
    // CORREGIDO: Se usa 'fecha_inicio'
    $stmt_actual = $pdo->prepare("
        SELECT v.viaje_id, v.estado, v.fecha_inicio, r.nombre as ruta_nombre, ve.nombre as vehiculo_nombre
        FROM viajes v
        JOIN rutas r ON v.ruta_id = r.ruta_id
        JOIN vehiculos ve ON v.vehiculo_id = ve.vehiculo_id
        WHERE v.operador_usuario_id = ? AND v.estado = 'En Curso'
        ORDER BY v.fecha_inicio ASC
        LIMIT 1
    ");
    $stmt_actual->execute([$operador_id]);
    $viaje_actual = $stmt_actual->fetch(PDO::FETCH_ASSOC);

    // Consulta para los PRÓXIMOS VIAJES (Planeados o Asignados)
    // CORREGIDO: Se usa 'fecha_inicio'
    $stmt_proximos = $pdo->prepare("
        SELECT v.viaje_id, v.estado, v.fecha_inicio, r.nombre as ruta_nombre, ve.nombre as vehiculo_nombre
        FROM viajes v
        JOIN rutas r ON v.ruta_id = r.ruta_id
        JOIN vehiculos ve ON v.vehiculo_id = ve.vehiculo_id
        WHERE v.operador_usuario_id = ? AND v.estado IN ('Planeado', 'Asignado')
        ORDER BY v.fecha_inicio ASC
    ");
    $stmt_proximos->execute([$operador_id]);
    $proximos_viajes = $stmt_proximos->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al obtener los viajes: " . $e->getMessage());
}
?>

<header class="mb-8">
    <h1 class="text-3xl font-bold">Mis Viajes</h1>
    <p class="text-gray-500">Aquí puedes ver tus viajes actuales y próximos.</p>
</header>

<div class="mb-10">
    <h2 class="text-2xl font-bold mb-4 text-blue-600">Viaje Actual</h2>
    
    <?php if ($viaje_actual): ?>
        <a href="operador_viaje_detalle.php?id=<?= htmlspecialchars($viaje_actual['viaje_id']) ?>" 
           class="block bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow border-l-4 border-blue-600">
            
            <div class="flex flex-col sm:flex-row justify-between sm:items-center">
                <h3 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($viaje_actual['ruta_nombre'] ?? 'Ruta no definida') ?></h3>
                <span class="mt-2 sm:mt-0 px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800 self-start">En Curso</span>
            </div>
            
            <p class="text-gray-600 mt-3"><strong>Vehículo:</strong> <?= htmlspecialchars($viaje_actual['vehiculo_nombre'] ?? 'N/A') ?></p>
            <p class="text-gray-600"><strong>Programado:</strong> <?= htmlspecialchars(date('d M, Y h:i A', strtotime($viaje_actual['fecha_inicio']))) ?></p>
            
            <div class="text-right mt-4">
                <span class="text-lg font-bold text-blue-600">
                    Ver Detalles y Mapa &rarr;
                </span>
            </div>
        </a>
    <?php else: ?>
        <div class="bg-white p-6 rounded-lg shadow-sm text-center text-gray-500">
            <p>No tienes ningún viaje "En Curso" en este momento.</p>
        </div>
    <?php endif; ?>
</div>

<div>
    <h2 class="text-2xl font-bold mb-4">Próximos Viajes</h2>

    <?php if (empty($proximos_viajes)): ?>
        <div class="bg-white p-6 rounded-lg shadow-sm text-center text-gray-500">
            <p>No tienes viajes programados. Contacta a tu administrador.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($proximos_viajes as $viaje): ?>
                <a href="operador_viaje_detalle.php?id=<?= htmlspecialchars($viaje['viaje_id']) ?>" 
                   class="block bg-white p-6 rounded-lg shadow-sm hover:shadow-lg transition-shadow">
                    
                    <?php
                    // Lógica para el color del estatus
                    $estatus = htmlspecialchars($viaje['estado']);
                    $color = 'bg-gray-100 text-gray-800'; // Planeado
                    if ($estatus == 'Asignado') $color = 'bg-yellow-100 text-yellow-800';
                    ?>

                    <div class="flex flex-col sm:flex-row justify-between sm:items-center">
                        <h3 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($viaje['ruta_nombre'] ?? 'Ruta no definida') ?></h3>
                        <span class="mt-2 sm:mt-0 px-3 py-1 text-sm font-semibold rounded-full <?= $color ?> self-start"><?= $estatus ?></span>
                    </div>
                    
                    <p class="text-gray-600 mt-3"><strong>Vehículo:</strong> <?= htmlspecialchars($viaje['vehiculo_nombre'] ?? 'N/A') ?></p>
                    <p class="text-gray-600"><strong>Programado:</strong> <?= htmlspecialchars(date('d M, Y h:i A', strtotime($viaje['fecha_inicio']))) ?></p>
                    
                    <div class="text-right mt-4">
                        <span class="font-bold text-blue-600">
                            Ver Detalles &rarr;
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
require_once 'footer_operador.php'; // Cierra la página
?>