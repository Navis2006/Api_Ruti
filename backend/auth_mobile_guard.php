<?php
// backend/auth_mobile_guard.php
require_once __DIR__ . '/config/jwt_utils.php';
// 1. Obtener Headers
$headers = getallheaders();
$auth_header = isset($headers['Authorization']) ? $headers['Authorization'] : '';
// 2. Extraer Token "Bearer eyJ..."
$token = '';
if (preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
    $token = $matches[1];
}
// 3. Validar
$payload = null;
if ($token) {
    // Usamos la función existente en jwt_utils.php
    $payload_obj = validate_jwt($token); // Retorna objeto o array
    if ($payload_obj) {
        // Convertir a array si es objeto
        $payload = (array)$payload_obj; 
    }
}
// 4. Bloquear si falla
if (!$payload) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Acceso denegado: Token inválido o expirado."]);
    exit();
}
// 5. Dejar disponible el ID para el script principal
$operador_id = $payload['id'] ?? 0;
// $rol_id = $payload['rol'] ?? 0;
?>