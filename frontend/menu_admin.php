<?php
    // La conexión a la BD SÍ la puedes necesitar aquí si esta página hace consultas
    require_once '../backend/config/db_connection.php'; 
    
    // header_admin.php ya se encarga del ROL y auth_guard
    require_once 'header_admin.php'; // Carga la cabecera

try {
    // --- 1. CONSULTAS PARA KPIS (CON DETALLE) ---
    
    // KPI de Viajes (Estado es tu columna)
    $kpi_viajes = $pdo->query("
        SELECT 
            COUNT(CASE WHEN estado = 'En Curso' THEN 1 END) as en_curso,
            COUNT(CASE WHEN estado IN ('Planeado', 'Asignado') THEN 1 END) as programados
        FROM viajes
    ")->fetch(PDO::FETCH_ASSOC);

    // KPI de Operadores (rol_id = 2 es tu rol de operador, estatus es tu columna)
    $kpi_operadores = $pdo->query("
        SELECT 
            COUNT(CASE WHEN estatus = 'activo' THEN 1 END) as activos,
            COUNT(CASE WHEN estatus = 'inactivo' THEN 1 END) as inactivos
        FROM usuarios 
        WHERE rol_id = 2
    ")->fetch(PDO::FETCH_ASSOC);

    // KPI de Vehículos (estatus es tu columna)
    $kpi_vehiculos = $pdo->query("
        SELECT 
            COUNT(CASE WHEN estatus = 'en_servicio' THEN 1 END) as en_servicio,
            COUNT(CASE WHEN estatus != 'en_servicio' THEN 1 END) as otros
        FROM vehiculos
    ")->fetch(PDO::FETCH_ASSOC);
    
    // KPI de Alertas (esto depende de la nueva columna 'estatus_alerta')
    try {
        $kpi_alertas = $pdo->query("
            SELECT 
                COUNT(CASE WHEN estatus_alerta = 'Abierta' THEN 1 END) as abiertas,
                COUNT(CASE WHEN estatus_alerta = 'Resuelta' THEN 1 END) as resueltas
            FROM alertas
        ")->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Plan B: Si la columna 'estatus_alerta' aún no existe, contamos todas
        $kpi_alertas = [
            'abiertas' => $pdo->query("SELECT COUNT(*) FROM alertas")->fetchColumn(),
            'resueltas' => 0
        ];
    }


    // --- 2. CONSULTAS PARA LAS GRÁFICAS (SIN CAMBIOS) ---
    $viajes_data = $pdo->query("SELECT estado, COUNT(*) as total FROM viajes GROUP BY estado")->fetchAll(PDO::FETCH_ASSOC);
    $rutas_data = $pdo->query("
        SELECT r.nombre, COUNT(v.viaje_id) as total
        FROM rutas r
        LEFT JOIN viajes v ON r.ruta_id = v.ruta_id
        GROUP BY r.nombre
        ORDER BY total DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
    

    // --- 3. CONSULTAS PARA LAS TARJETAS DE ACTIVIDAD (SIN CAMBIOS) ---
    $alertas_recientes = $pdo->query("
        SELECT a.descripcion, a.tipo_alerta, r.nombre as ruta_nombre, a.nivel
        FROM alertas a
        JOIN rutas r ON a.ruta_id = r.ruta_id
        ORDER BY a.alerta_id DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    $operadores_en_ruta = $pdo->query("
        SELECT CONCAT(op.nombre, ' ', op.apellidos) as operador_nombre, r.nombre as ruta_nombre, ve.nombre as vehiculo_nombre
        FROM viajes v
        JOIN usuarios op ON v.operador_usuario_id = op.usuario_id
        JOIN rutas r ON v.ruta_id = r.ruta_id
        JOIN vehiculos ve ON v.vehiculo_id = ve.vehiculo_id
        WHERE v.estado = 'En Curso'
    ")->fetchAll(PDO::FETCH_ASSOC);


} catch (PDOException $e) {
    die("Error al obtener datos del dashboard: ". $e->getMessage());
}

// --- PREPARAR DATOS PARA PASAR A JAVASCRIPT ---
$viajes_labels = json_encode(array_column($viajes_data, 'estado'));
$viajes_counts = json_encode(array_column($viajes_data, 'total'));
$rutas_labels = json_encode(array_column($rutas_data, 'nombre'));
$rutas_counts = json_encode(array_column($rutas_data, 'total'));
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<header class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
    <p class="text-gray-500 mt-1">Resumen de la actividad de la plataforma.</p>
</header>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
        <div class="p-3 bg-blue-100 text-blue-600 rounded-full">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7l4-4m0 0l4 4m-4-4v18"></path></svg>
        </div>
        <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Viajes</p>
            <p class="text-3xl font-bold text-gray-800"><?= $kpi_viajes['en_curso'] ?? 0 ?></p>
            <p class="text-sm text-gray-500"><?= $kpi_viajes['programados'] ?? 0 ?> Programados/Asignados</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
        <div class="p-3 bg-green-100 text-green-600 rounded-full">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </div>
        <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Operadores</p>
            <p class="text-3xl font-bold text-gray-800"><?= $kpi_operadores['activos'] ?? 0 ?></p>
            <p class="text-sm text-gray-500"><?= $kpi_operadores['inactivos'] ?? 0 ?> Inactivos</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
        <div class="p-3 bg-indigo-100 text-indigo-600 rounded-full">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2-2h8zM13 16h2m-2-7h2m0 0H9.692l-2-4H4.382l-2 4H13z"></path></svg>
        </div>
        <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Vehículos</p>
            <p class="text-3xl font-bold text-gray-800"><?= $kpi_vehiculos['en_servicio'] ?? 0 ?></p>
            <p class="text-sm text-gray-500"><?= $kpi_vehiculos['otros'] ?? 0 ?> en Mantenimiento/Baja</p>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
        <div class="p-3 bg-red-100 text-red-600 rounded-full">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341A6.002 6.002 0 006 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
        </div>
        <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Alertas</p>
            <p class="text-3xl font-bold text-gray-800"><?= $kpi_alertas['abiertas'] ?? 0 ?></p>
            <p class="text-sm text-gray-500"><?= $kpi_alertas['resueltas'] ?? 0 ?> Resueltas (Hist.)</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    
    <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Top 5 - Viajes por Ruta</h3>
        <div>
            <canvas id="rutasChart"></canvas>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Viajes por Estatus</h3>
        <div>
            <canvas id="viajesChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Alertas Recientes</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="p-4">Ruta</th>
                        <th class="p-4">Descripción</th>
                        <th class="p-4">Tipo</th>
                        <th class="p-4">Nivel</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (empty($alertas_recientes)): ?>
                        <tr>
                            <td colspan="4" class="p-4 text-gray-500 text-center">No hay alertas recientes.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($alertas_recientes as $alerta): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="p-4"><?= htmlspecialchars($alerta['ruta_nombre'] ?? '') ?></td>
                                <td class="p-4"><?= htmlspecialchars($alerta['descripcion'] ?? '') ?></td>
                                <td class="p-4"><?= htmlspecialchars($alerta['tipo_alerta'] ?? '') ?></td>
                                <td class="p-4">
                                    <?php 
                                    // Usamos '?? 3' como default por si la columna 'nivel' aún no existe
                                    $nivel = $alerta['nivel'] ?? 3; 
                                    $color = 'text-gray-600'; // Nivel 1, 2, 3
                                    if ($nivel == 4) $color = 'text-yellow-600 font-bold';
                                    if ($nivel == 5) $color = 'text-red-600 font-bold';
                                    ?>
                                    <span class="<?= $color ?>">Nivel <?= htmlspecialchars($nivel) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Operadores en Ruta</h3>
        <div class="overflow-y-auto max-h-64">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b sticky top-0">
                    <tr>
                        <th class="p-4">Operador</th>
                        <th class="p-4">Ruta</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                     <?php if (empty($operadores_en_ruta)): ?>
                        <tr>
                            <td colspan="2" class="p-4 text-gray-500 text-center">No hay operadores "En Curso".</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($operadores_en_ruta as $op): ?>
                            <tr class="hover:bg-gray-50">
                                <td class_name("p-4")><?= htmlspecialchars($op['operador_nombre'] ?? '') ?></td>
                                <td class_name("p-4")><?= htmlspecialchars($op['ruta_nombre'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', () => {

    // --- Gráfico de Pastel (Viajes por Estatus) ---
    const ctxViajes = document.getElementById('viajesChart').getContext('2d');
    new Chart(ctxViajes, {
        type: 'doughnut',
        data: {
            labels: <?php echo $viajes_labels; ?>,
            datasets: [{
                label: 'Viajes',
                data: <?php echo $viajes_counts; ?>,
                backgroundColor: [
                    '#E5E7EB', // Planeado (Gris)
                    '#F59E0B', // Asignado (Amarillo)
                    '#3B82F6', // En Curso (Azul)
                    '#10B981', // Finalizado (Verde)
                    '#EF4444', // Cancelado (Rojo)
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    // --- Gráfico de Barras (Top Rutas) ---
    const ctxRutas = document.getElementById('rutasChart').getContext('2d');
    new Chart(ctxRutas, {
        type: 'bar',
        data: {
            labels: <?php echo $rutas_labels; ?>,
            datasets: [{
                label: 'Número de Viajes',
                data: <?php echo $rutas_counts; ?>,
                backgroundColor: '#3B82F6', // Azul
                borderColor: '#2563EB',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false // No necesitamos leyenda para una sola barra
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

});
</script>

<?php
require_once 'footer.php'; // Cierra la página
?>