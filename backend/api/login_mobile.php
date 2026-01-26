<?php
// backend/api/login_mobile.php

// 1. Headers para permitir acceso desde la App (CORS) y definir respuesta JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Manejar pre-flight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 2. Incluir conexión a BD y utilidades
// La ruta asume que este archivo está en backend/api/
require_once '../config/db_connection.php';
require_once '../config/jwt_utils.php';

// 3. Obtener los datos enviados por la App (JSON)
$input_json = file_get_contents("php://input");
$data = json_decode($input_json);

// Validar que lleguen los datos
if (empty($data->email) || empty($data->contrasena)) {
    http_response_code(400); // Bad Request
    echo json_encode(["success" => false, "message" => "Datos incompletos. Se requiere email y contrasena."]);
    exit();
}

$email = trim($data->email);
$password = $data->contrasena;

try {
    // 4. Consulta a la Base de Datos
    $query = "SELECT usuario_id, nombre, apellidos, contrasena_hash, rol_id, empresa_id, estatus 
              FROM usuarios 
              WHERE email = :email LIMIT 1";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 5. Verificar contraseña
    if ($user && password_verify($password, $user['contrasena_hash'])) {

        // Verificar estatus
        if (isset($user['estatus']) && $user['estatus'] !== 'activo') {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Usuario inactivo o suspendido."]);
            exit();
        }

        // 6. Generar Token JWT
        $token = generate_jwt($user['usuario_id'], $user['rol_id']);

        // 7. Responder Éxito
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Autenticación exitosa.",
            "token" => $token,
            "usuario" => [
                "id" => $user['usuario_id'],
                "nombre" => $user['nombre'] . ' ' . $user['apellidos'],
                "rol_id" => (int) $user['rol_id'],
                "empresa_id" => (int) $user['empresa_id']
            ]
        ]);
    } else {
        // 8. Responder Fallo
        http_response_code(401); // Unauthorized
        echo json_encode(["success" => false, "message" => "Correo o contraseña incorrectos."]);
    }

} catch (Exception $e) {
    // 9. Manejo de Errores del Servidor
    error_log("Error API Login: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error interno del servidor.", "debug_error" => $e->getMessage()]);
}
?>