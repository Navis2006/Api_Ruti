<?php
define('ROL_REQUERIDO', 2);
require_once '../backend/auth_guard.php'; 
require_once '../backend/config/db_connection.php'; 
require_once 'header_operador.php'; // Carga la cabecera del operador

// --- 1. OBTENER LOS VIAJES DE ESTE OPERADOR ---
$operador_id = $_SESSION['usuario_id']; 

try {
    // Consulta para el VIAJE ACTUAL (CORREGIDO: usa 'v.estado')
    $stmt_actual = $pdo->prepare("
        SELECT v.viaje_id, v.estado, v.fecha_inicio, r.nombre as ruta_nombre, ve.nombre as vehiculo_nombre
        FROM viajes v
        LEFT JOIN rutas r ON v.ruta_id = r.ruta_id
        LEFT JOIN vehiculos ve ON v.vehiculo_id = ve.vehiculo_id
        WHERE v.operador_usuario_id = ? AND v.estado = 'En Curso'
        ORDER BY v.fecha_inicio ASC
        LIMIT 1
    ");
    $stmt_actual->execute([$operador_id]);
    $viaje_actual = $stmt_actual->fetch(PDO::FETCH_ASSOC);

    // Consulta para los PRÓXIMOS VIAJES (CORREGIDO: usa 'v.estado')
    $stmt_proximos = $pdo->prepare("
        SELECT v.viaje_id, v.estado, v.fecha_inicio, r.nombre as ruta_nombre, ve.nombre as vehiculo_nombre
        FROM viajes v
        LEFT JOIN rutas r ON v.ruta_id = r.ruta_id
        LEFT JOIN vehiculos ve ON v.vehiculo_id = ve.vehiculo_id
        WHERE v.operador_usuario_id = ? AND v.estado IN ('Planeado', 'Asignado')
        ORDER BY v.fecha_inicio ASC
    ");
    $stmt_proximos->execute([$operador_id]);
    $proximos_viajes = $stmt_proximos->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al obtener los viajes: " . $e->getMessage());
}
?>

<style>
    /* Clases para las tarjetas (reemplazando las de Tailwind por si fallan) */
    .card {
        background-color: #ffffff;
        padding: 1.5rem; /* p-6 */
        border-radius: 0.5rem; /* rounded-lg */
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); /* shadow-md */
        transition: box-shadow 0.3s ease-in-out;
    }
    .card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); /* shadow-xl */
    }
    .card-current {
        border-left: 4px solid #2563EB; /* border-l-4 border-blue-600 */
    }
    
    /* Clases para los "badges" de estatus */
    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem; /* px-3 py-1 */
        font-size: 0.875rem; /* text-sm */
        font-weight: 600; /* font-semibold */
        border-radius: 9999px; /* rounded-full */
        align-self: flex-start;
    }
    .badge-blue { /* 'En Curso' */
        background-color: #DBEAFE; /* bg-blue-100 */
        color: #1E40AF; /* text-blue-800 */
    }
    .badge-yellow { /* 'Asignado' */
        background-color: #FEF3C7; /* bg-yellow-100 */
        color: #92400E; /* text-yellow-800 */
    }
    .badge-gray { /* 'Planeado' */
        background-color: #F3F4F6; /* bg-gray-100 */
        color: #1F2937; /* text-gray-800 */
    }

    /* Clases para el link de "Ver Detalles" */
    .card-link {
        text-align: right;
        margin-top: 1rem;
    }
    .card-link span {
        font-size: 1.125rem; /* text-lg */
        font-weight: 700; /* font-bold */
        color: #2563EB; /* text-blue-600 */
    }
    
    /* Clases para el texto de "No hay viajes" */
    .card-empty {
        text-align: center;
        color: #6B7280; /* text-gray-500 */
    }
</style>
<header class="mb-8">
    <h1 class="text-3xl font-bold">Mis Viajes</h1>
    <p class="text-gray-500">Aquí puedes ver tus viajes actuales y próximos.</p>
</header>

<div class="mb-10">
    <h2 class="text-2xl font-bold mb-4 text-blue-600">Viaje Actual</h2>
    
    <?php if ($viaje_actual): ?>
        <a href="operador_viaje_detalle.php?id=<?= htmlspecialchars($viaje_actual['viaje_id']) ?>" 
           class="block card card-current">
            
            <div class="flex flex-col sm:flex-row justify-between sm:items-center">
                <h3 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($viaje_actual['ruta_nombre'] ?? 'Ruta no definida') ?></h3>
                <span class="mt-2 sm:mt-0 badge badge-blue">En Curso</span>
            </div>
            
            <p class="text-gray-600 mt-3"><strong>Vehículo:</strong> <?= htmlspecialchars($viaje_actual['vehiculo_nombre'] ?? 'N/A') ?></p>
            <p class="text-gray-600"><strong>Programado:</strong> <?= htmlspecialchars(date('d M, Y h:i A', strtotime($viaje_actual['fecha_inicio']))) ?></p>
            
            <div class="card-link">
                <span>
                    Ver Detalles y Mapa &rarr;
                </span>
            </div>
        </a>
    <?php else: ?>
        <div class="card card-empty">
            <p>No tienes ningún viaje "En Curso" en este momento.</p>
        </div>
    <?php endif; ?>
</div>

<div>
    <h2 class="text-2xl font-bold mb-4">Próximos Viajes</h2>

    <?php if (empty($proximos_viajes)): ?>
        <div class="card card-empty">
            <p>No tienes viajes programados. Contacta a tu administrador.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($proximos_viajes as $viaje): ?>
                <a href="operador_viaje_detalle.php?id=<?= htmlspecialchars($viaje['viaje_id']) ?>" 
                   class="block card">
                    
                    <?php
                    // Lógica para el color del estatus (CORREGIDO: usa 'v.estado')
                    $estatus = htmlspecialchars($viaje['estado']);
                    $color_class = 'badge-gray'; // Planeado
                    if ($estatus == 'Asignado') $color_class = 'badge-yellow';
                    ?>

                    <div class="flex flex-col sm:flex-row justify-between sm:items-center">
                        <h3 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($viaje['ruta_nombre'] ?? 'Ruta no definida') ?></h3>
                        <span class="mt-2 sm:mt-0 badge <?= $color_class ?>"><?= $estatus ?></span>
                    </div>
                    
                    <p class="text-gray-600 mt-3"><strong>Vehículo:</strong> <?= htmlspecialchars($viaje['vehiculo_nombre'] ?? 'N/A') ?></p>
                    <p class="text-gray-600"><strong>Programado:</strong> <?= htmlspecialchars(date('d M, Y h:i A', strtotime($viaje['fecha_inicio']))) ?></p>
                    
                    <div class="card-link">
                        <span>
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