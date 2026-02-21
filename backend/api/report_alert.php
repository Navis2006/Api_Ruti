<?php
// backend/api/report_alert.php

// 1. Headers (CORS y JSON)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
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

// 3. Obtener los datos enviados por la App (JSON)
$input_json = file_get_contents("php://input");
$data = json_decode($input_json);

// Validar datos mínimos
if (
    empty($data->viaje_id) || 
    empty($data->tipo_alerta) || 
    empty($data->latitud) || 
    empty($data->longitud)
) {
    http_response_code(400); // Bad Request
    echo json_encode(["success" => false, "message" => "Datos incompletos. Se requieren viaje_id, tipo_alerta, latitud y longitud."]);
    exit();
}

try {
    // 4. Obtener ruta_id del viaje
    // Verificar que el viaje pertenezca al operador (seguridad)
    $query_ruta = "SELECT ruta_id FROM viajes WHERE viaje_id = :viaje_id AND operador_usuario_id = :operador_id";
    $stmt_ruta = $pdo->prepare($query_ruta);
    $stmt_ruta->bindParam(':viaje_id', $data->viaje_id);
    $stmt_ruta->bindParam(':operador_id', $operador_id);
    $stmt_ruta->execute();

    if ($stmt_ruta->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Viaje no encontrado o no autorizado."]);
        exit();
    }

    $ruta_row = $stmt_ruta->fetch(PDO::FETCH_ASSOC);
    $ruta_id = $ruta_row['ruta_id'];

    // 5. Determinar Nivel de Prioridad
    $niveles = [
        'Baches Peligrosos' => 3,
        'Tráfico'           => 3,
        'Peligro en Vía'    => 4,
        'Accidente'         => 5,
        'Otro'              => 3
    ];
    $nivel = isset($niveles[$data->tipo_alerta]) ? $niveles[$data->tipo_alerta] : 3;

    // 6. Preparar Descripción (si viene vacía, usar default)
    $descripcion = !empty($data->descripcion) ? trim($data->descripcion) : '';
    if (empty($descripcion)) {
        $descripciones_default = [
            'Baches Peligrosos' => 'Baches peligrosos reportados en la ruta',
            'Accidente'         => 'Accidente reportado en la ruta',
            'Tráfico'           => 'Tráfico pesado reportado en la ruta',
            'Peligro en Vía'    => 'Cables bajos reportados en la ruta', // O peligro genérico
            'Otro'              => 'Incidente reportado en la ruta'
        ];
        // Mapeo específico para "Peligro en Vía" -> "Cables Bajos" si aplica, o genérico.
        // La indicación dice: Cables Bajos -> Peligro en Vía.
        // Si el tipo es "Peligro en Vía", la app mandará ese string.
        $descripcion = isset($descripciones_default[$data->tipo_alerta]) ? $descripciones_default[$data->tipo_alerta] : 'Alerta reportada en la ruta';
    }

    // 7. Insertar Alerta (POINT WKT)
    $lat = $data->latitud;
    $lon = $data->longitud;
    $point_wkt = "POINT($lon $lat)"; // WKT formato: LONG LAT

    $query_insert = "
        INSERT INTO alertas (ruta_id, creado_por_usuario_id, descripcion, tipo_alerta, nivel, estatus_alerta, ubicacion_geom)
        VALUES (:ruta_id, :operador_id, :descripcion, :tipo_alerta, :nivel, 'Abierta', ST_GeomFromText(:point_wkt))
    ";

    $stmt_insert = $pdo->prepare($query_insert);
    $stmt_insert->bindParam(':ruta_id', $ruta_id);
    $stmt_insert->bindParam(':operador_id', $operador_id);
    $stmt_insert->bindParam(':descripcion', $descripcion);
    $stmt_insert->bindParam(':tipo_alerta', $data->tipo_alerta);
    $stmt_insert->bindParam(':nivel', $nivel);
    $stmt_insert->bindParam(':point_wkt', $point_wkt); // MySQL 5.7+ o MariaDB soporta ST_GeomFromText

    if ($stmt_insert->execute()) {
        $alerta_id = $pdo->lastInsertId();
        http_response_code(201); // Created
        echo json_encode([
            "success" => true,
            "message" => "Alerta reportada exitosamente.",
            "alerta_id" => (int)$alerta_id
        ]);
    } else {
        throw new Exception("Error al ejecutar la inserción en BD.");
    }

} catch (Exception $e) {
    error_log("Error en report_alert.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error interno del servidor.", "debug" => $e->getMessage()]);
}
?>
