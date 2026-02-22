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
    // 3. Consulta Detalle (Incluye WKT)
    $stmt = $pdo->prepare("
        SELECT 
            v.viaje_id, v.estado, v.fecha_inicio, 
            r.ruta_id, r.nombre as ruta_nombre, r.descripcion as ruta_descripcion,
            r.lat_origen, r.lng_origen, r.lat_destino, r.lng_destino,
            ST_AsText(r.trazado_geom) as trazado_wkt, 
            ve.nombre as vehiculo_nombre, ve.placa as vehiculo_placa
        FROM viajes v
        JOIN rutas r ON v.ruta_id = r.ruta_id
        JOIN vehiculos ve ON v.vehiculo_id = ve.vehiculo_id
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
    // 4. Obtener Alertas
    $stmt_alertas = $pdo->prepare("
        SELECT * FROM alertas 
        WHERE ruta_id = ? AND estatus_alerta = 'Abierta'
        ORDER BY nivel DESC
    ");
    $stmt_alertas->execute([$viaje['ruta_id']]);
    $alertas = $stmt_alertas->fetchAll(PDO::FETCH_ASSOC);
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