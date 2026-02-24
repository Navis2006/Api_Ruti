<?php
// backend/api/get_trip_details.php
// 1. Headers CORS y JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
require_once '../config/db_connection.php';
// INCLUIR LA NUEVA GUARDIA MÓVIL
require_once '../auth_mobile_guard.php';
// $operador_id ya está definido por auth_mobile_guard.php
$viaje_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
try {
    if ($viaje_id <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID de viaje inválido."]);
        exit;
    }
    // 3. Consulta Detalle
    $stmt = $pdo->prepare("
        SELECT 
            v.viaje_id, v.estado, v.fecha_inicio, v.ruta_id as legacy_ruta_id,
            CONCAT(e_origen.nombre, ' a ', COALESCE(e_dest.nombre, r_dest.nombre)) as ruta_nombre, /* Compatibilidad: titulo dinamico de ruta */
            e_origen.nombre as origen_nombre,
            COALESCE(e_dest.nombre, r_dest.nombre) as destino_nombre,
            '' as ruta_descripcion,
            e_origen.lat as lat_origen, e_origen.lng as lng_origen,
            COALESCE(e_dest.lat, r_dest.lat_origen) as lat_destino,
            COALESCE(e_dest.lng, r_dest.lng_origen) as lng_destino,
            r_dest.trazado_geom as trazado_wkt, 
            ve.nombre as vehiculo_nombre, ve.placa as vehiculo_placa
        FROM viajes v
        LEFT JOIN empresas e_origen ON v.origen_empresa_id = e_origen.empresa_id
        LEFT JOIN vehiculos ve ON v.vehiculo_id = ve.vehiculo_id
        LEFT JOIN (
            SELECT viaje_id, MAX(orden) as max_orden
            FROM viaje_destinos
            GROUP BY viaje_id
        ) max_vd ON v.viaje_id = max_vd.viaje_id
        LEFT JOIN viaje_destinos vd_last ON v.viaje_id = vd_last.viaje_id AND vd_last.orden = max_vd.max_orden
        LEFT JOIN empresas e_dest ON vd_last.empresa_id = e_dest.empresa_id
        LEFT JOIN rutas r_dest ON vd_last.ruta_id = r_dest.ruta_id
        WHERE v.operador_usuario_id = ? AND v.viaje_id = ?
        LIMIT 1
    ");
    $stmt->execute([$operador_id, $viaje_id]);
    $viaje = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$viaje) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Viaje no encontrado o no asignado."]);
        exit;
    }

    // 4. Obtener Alertas (usando el legacy_ruta_id por ahora si existe, o array vacío)
    $alertas = [];
    if (!empty($viaje['legacy_ruta_id'])) {
        $stmt_alertas = $pdo->prepare("
            SELECT * FROM alertas 
            WHERE ruta_id = ? AND estatus_alerta = 'Abierta'
            ORDER BY nivel DESC
        ");
        $stmt_alertas->execute([$viaje['legacy_ruta_id']]);
        $alertas = $stmt_alertas->fetchAll(PDO::FETCH_ASSOC);
    }

    $viaje['alertas_activas'] = $alertas;

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $viaje
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error DB: " . $e->getMessage()]);
}
?>