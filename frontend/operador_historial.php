<?php
define('ROL_REQUERIDO', 2);
require_once '../backend/auth_guard.php'; 
require_once '../backend/config/db_connection.php'; 
require_once 'header_operador.php'; // Carga la cabecera del operador

// --- 1. OBTENER EL ID DEL OPERADOR ---
$operador_id = $_SESSION['usuario_id']; 
$historial_viajes = [];
$historial_alertas = [];

try {
    // --- CONSULTA 1: HISTORIAL DE VIAJES ---
    $stmt_historial = $pdo->prepare("
        SELECT 
            v.viaje_id, v.estado, v.fecha_inicio, v.fecha_finalizacion,
            r.nombre as ruta_nombre, 
            ve.nombre as vehiculo_nombre
        FROM viajes v
        LEFT JOIN rutas r ON v.ruta_id = r.ruta_id
        LEFT JOIN vehiculos ve ON v.vehiculo_id = ve.vehiculo_id
        WHERE v.operador_usuario_id = ? 
        AND v.estado IN ('Finalizado', 'Cancelado')
        ORDER BY v.fecha_finalizacion DESC
    ");
    $stmt_historial->execute([$operador_id]);
    $historial_viajes = $stmt_historial->fetchAll(PDO::FETCH_ASSOC);

    // --- CONSULTA 2: HISTORIAL DE ALERTAS CREADAS POR ÉL ---
    // (Esta consulta SÍ asume que tu amigo ya añadió 'nivel' y 'estatus_alerta')
    $stmt_alertas_historial = $pdo->prepare("
        SELECT 
            a.alerta_id, a.descripcion, a.tipo_alerta, a.nivel, a.estatus_alerta,
            r.nombre as ruta_nombre
        FROM alertas a
        LEFT JOIN rutas r ON a.ruta_id = r.ruta_id
        WHERE a.creado_por_usuario_id = ?
        ORDER BY a.alerta_id DESC
    ");
    $stmt_alertas_historial->execute([$operador_id]);
    $historial_alertas = $stmt_alertas_historial->fetchAll(PDO::FETCH_ASSOC);


} catch (PDOException $e) {
    // Si falla (ej: 'estatus_alerta' no existe), mostramos un error
    $error_db = "Error al obtener el historial: " . $e->getMessage();
}
?>

<style>
    .card {
        background-color: #ffffff; padding: 1.5rem; border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        transition: box-shadow 0.3s ease-in-out;
    }
    .card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .badge {
        display: inline-block; padding: 0.25rem 0.75rem; font-size: 0.875rem;
        font-weight: 600; border-radius: 9999px; align-self: flex-start;
    }
    .badge-green { background-color: #D1FAE5; color: #065F46; } /* Finalizado, Resuelta */
    .badge-red { background-color: #FEE2E2; color: #991B1B; } /* Cancelado, Abierta */
    .badge-yellow { background-color: #FEF3C7; color: #92400E; } /* Nivel 4 */
    .badge-blue { background-color: #DBEAFE; color: #1E40AF; } /* Nivel 1-3 */

    .card-link { text-align: right; margin-top: 1rem; }
    .card-link span { font-size: 1.125rem; font-weight: 700; color: #2563EB; }
    .card-empty { text-align: center; color: #6B7280; }
</style>
<header class="mb-8">
    <h1 class="text-3xl font-bold">Historial</h1>
    <p class="text-gray-500">Aquí puedes ver tus viajes pasados y las alertas que has reportado.</p>
</header>

<?php if (isset($error_db)): ?>
    <div class="card card-empty bg-red-100 text-red-700">
        <p><strong>Error de Base de Datos:</strong> <?= htmlspecialchars($error_db) ?></p>
        <p class="mt-2">Es posible que las columnas 'nivel' o 'estatus_alerta' aún no existan. Por favor, avisa a tu administrador.</p>
    </div>
<?php else: ?>

    <div class="mb-10">
        <h2 class="text-2xl font-bold mb-4">Historial de Viajes</h2>
        <?php if (empty($historial_viajes)): ?>
            <div class="card card-empty">
                <p>Aún no tienes viajes en tu historial.</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($historial_viajes as $viaje): ?>
                    <a href="operador_viaje_detalle.php?id=<?= htmlspecialchars($viaje['viaje_id']) ?>" class="block card">
                        <?php
                        $estatus = htmlspecialchars($viaje['estado']);
                        $color_class = ($estatus == 'Finalizado') ? 'badge-green' : 'badge-red';
                        $fecha_fin = $viaje['fecha_finalizacion'] 
                            ? date('d M, Y h:i A', strtotime($viaje['fecha_finalizacion'])) 
                            : ($viaje['fecha_inicio'] ? date('d M, Y h:i A', strtotime($viaje['fecha_inicio'])) : 'N/A');
                        ?>
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center">
                            <h3 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($viaje['ruta_nombre'] ?? 'Ruta no definida') ?></h3>
                            <span class="mt-2 sm:mt-0 badge <?= $color_class ?>"><?= $estatus ?></span>
                        </div>
                        <p class="text-gray-600 mt-3"><strong>Vehículo:</strong> <?= htmlspecialchars($viaje['vehiculo_nombre'] ?? 'N/A') ?></p>
                        <p class="text-gray-600"><strong>Fecha de Fin/Cancelación:</strong> <?= $fecha_fin ?></p>
                        <div class="card-link">
                            <span>Ver Detalles &rarr;</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div>
        <h2 class="text-2xl font-bold mb-4">Mis Alertas Reportadas</h2>
        <?php if (empty($historial_alertas)): ?>
            <div class="card card-empty">
                <p>No has reportado ninguna alerta.</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($historial_alertas as $alerta): ?>
                    <div class="card">
                        <?php
                        // Lógica para el estatus de la alerta
                        $estatus_alerta = $alerta['estatus_alerta'] ?? 'Abierta';
                        $estatus_color = ($estatus_alerta == 'Abierta') ? 'badge-red' : 'badge-green';

                        // Lógica para el nivel de la alerta
                        $nivel = $alerta['nivel'] ?? 3;
                        $nivel_color = 'badge-blue';
                        if ($nivel == 4) $nivel_color = 'badge-yellow';
                        if ($nivel == 5) $nivel_color = 'badge-red';
                        ?>
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center">
                            <h3 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($alerta['tipo_alerta'] ?? 'General') ?></h3>
                            <div class="flex space-x-2 mt-2 sm:mt-0">
                                <span class="badge <?= $nivel_color ?>">Nivel <?= htmlspecialchars($nivel) ?></span>
                                <span class="badge <?= $estatus_color ?>"><?= $estatus_alerta ?></span>
                            </div>
                        </div>
                        <p class="text-gray-600 mt-3"><strong>Ruta:</strong> <?= htmlspecialchars($alerta['ruta_nombre'] ?? 'N/A') ?></p>
                        <p class="text-gray-600 mt-1"><strong>Reporte:</strong> <?= htmlspecialchars($alerta['descripcion'] ?? '') ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
<?php endif; ?>

<?php
require_once 'footer_operador.php'; // Cierra la página
?>