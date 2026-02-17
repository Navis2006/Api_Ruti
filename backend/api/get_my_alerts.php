<?php
// backend/api/get_my_alerts.php

// 1. Headers (CORS y JSON)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Manejo de pre-flight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 2. Conexión a BD y Auth Guard
require_once '../config/db_connection.php';
require_once '../auth_mobile_guard.php'; 
// El guard valida el token y deja disponible $operador_id

try {
    // 3. Consulta SQL
    // Obtener alertas de las rutas donde el operador tiene viajes asignados
    // Se filtran por estatus 'Abierta'
    // Se ordenan por nivel (prioridad) descendente
    $query = "
        SELECT 
            a.alerta_id,
            a.ruta_id,
            a.descripcion,
            a.tipo_alerta,
            a.nivel,
            a.estatus_alerta,
            ST_X(a.ubicacion_geom) AS longitud,
            ST_Y(a.ubicacion_geom) AS latitud,
            r.nombre AS nombre_ruta,
            CONCAT(u.nombre, ' ', u.apellidos) AS creador_nombre
        FROM alertas a
        INNER JOIN rutas r ON a.ruta_id = r.ruta_id
        INNER JOIN viajes v ON r.ruta_id = v.ruta_id
        LEFT JOIN usuarios u ON a.creado_por_usuario_id = u.usuario_id
        WHERE v.operador_usuario_id = :operador_id
          AND a.estatus_alerta = 'Abierta'
        GROUP BY a.alerta_id
        ORDER BY a.nivel DESC, a.alerta_id DESC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':operador_id', $operador_id);
    $stmt->execute();

    $alertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ajustar tipos de datos si es necesario (lat/long como float, nivel como int)
    foreach ($alertas as &$alerta) {
        $alerta['alerta_id'] = (int)$alerta['alerta_id'];
        $alerta['ruta_id'] = (int)$alerta['ruta_id'];
        $alerta['nivel'] = (int)$alerta['nivel'];
        $alerta['longitud'] = (float)$alerta['longitud'];
        $alerta['latitud'] = (float)$alerta['latitud'];
    }

    // 4. Respuesta Exitosa
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $alertas
    ]);

} catch (Exception $e) {
    // 5. Manejo de Errores
    error_log("Error en get_my_alerts.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "Error al obtener las alertas."
    ]);
}
?>
