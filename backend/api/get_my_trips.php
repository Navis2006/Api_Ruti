<?php
// backend/api/get_my_trips.php
// 1. Headers CORS y JSON (Igual que en login_mobile.php)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// Manejar OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
require_once '../config/db_connection.php';
require_once '../config/jwt_utils.php'; // Necesario para validar el token
try {
    // 2. Validar Token JWT
    // (Asumimos que tienes una función validate_jwt() o similar en jwt_utils.php)
    // $payload = validate_jwt_from_headers(); // Implementar según tu jwt_utils
    // $operador_id = $payload['usuario_id'];
    
    // OJO: Si auth_mobile_guard.php ya hace esto, inclúyelo:
    // require_once '../auth_mobile_guard.php'; 
    // $operador_id = $user_id_from_token;
    
    // HARDCODE TEMPORAL para probar si aún no tienes el validador:
    // $operador_id = 12; // Descomentar para probar sin token
    
    if (!isset($operador_id)) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Token no válido o expirado."]);
        exit();
    }
    // 3. Consultas SQL (Lógica original de menu_trailero.php)
    
    // A. Viaje Actual
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
    // B. Próximos Viajes
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