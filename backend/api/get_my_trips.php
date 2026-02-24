<?php
// backend/api/get_my_trips.php
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
try {
    // ... resto del código igual ...
    // 3. Consultas SQL (Lógica original de menu_trailero.php)

    // A. Viaje Actual
    $sql_viajes = "
        SELECT v.viaje_id, v.estado, v.fecha_inicio, ve.nombre as vehiculo_nombre,
               CONCAT(e_origen.nombre, ' a ', COALESCE(e_dest.nombre, r_dest.nombre)) as ruta_nombre, /* Título dinámico del viaje */
               e_origen.nombre as origen_nombre,
               COALESCE(e_dest.nombre, r_dest.nombre) as destino_nombre,
               e_origen.lat as lat_origen, e_origen.lng as lng_origen,
               /* Obtenemos el último destino de la ruta multi-parada */
               COALESCE(e_dest.lat, r_dest.lat_origen) as lat_destino,
               COALESCE(e_dest.lng, r_dest.lng_origen) as lng_destino
        FROM viajes v
        LEFT JOIN empresas e_origen ON v.origen_empresa_id = e_origen.empresa_id
        LEFT JOIN vehiculos ve ON v.vehiculo_id = ve.vehiculo_id
        /* Subconsulta para sacar la última parada del viaje */
        LEFT JOIN (
            SELECT viaje_id, MAX(orden) as max_orden
            FROM viaje_destinos
            GROUP BY viaje_id
        ) max_vd ON v.viaje_id = max_vd.viaje_id
        LEFT JOIN viaje_destinos vd_last ON v.viaje_id = vd_last.viaje_id AND vd_last.orden = max_vd.max_orden
        LEFT JOIN empresas e_dest ON vd_last.empresa_id = e_dest.empresa_id
        LEFT JOIN rutas r_dest ON vd_last.ruta_id = r_dest.ruta_id
    ";

    $stmt_actual = $pdo->prepare($sql_viajes . " 
        WHERE v.operador_usuario_id = ? AND v.estado = 'En Curso' 
        ORDER BY v.fecha_inicio ASC LIMIT 1
    ");
    $stmt_actual->execute([$operador_id]);
    $viaje_actual = $stmt_actual->fetch(PDO::FETCH_ASSOC);

    // B. Próximos Viajes
    $stmt_proximos = $pdo->prepare($sql_viajes . "
        WHERE v.operador_usuario_id = ? AND v.estado IN ('Planeado', 'Asignado')
        ORDER BY v.fecha_inicio ASC
    ");
    $stmt_proximos->execute([$operador_id]);
    $proximos_viajes = $stmt_proximos->fetchAll(PDO::FETCH_ASSOC);
    // 4. Responder JSON
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => [
            'current_trip' => $viaje_actual ?: null,
            'upcoming_trips' => $proximos_viajes
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error DB: " . $e->getMessage()]);
}
?>