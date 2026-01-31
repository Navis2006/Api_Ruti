<?php
// backend/api/update_trip_status.php
// 1. Headers CORS y JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
require_once '../config/db_connection.php';
// USAR LA GUARDIA MÓVIL (El archivo que ya creaste en el paso anterior)
require_once '../auth_mobile_guard.php';
// $operador_id viene validado del Token en auth_mobile_guard.php
// 2. Obtener Datos (Soporta JSON o FormData)
$input_json = file_get_contents("php://input");
$data = json_decode($input_json, true);
$viaje_id = $_POST['viaje_id'] ?? $data['viaje_id'] ?? 0;
$action = $_POST['action'] ?? $data['action'] ?? '';
if (!$viaje_id) {
    echo json_encode(['status' => 'error', 'message' => 'ID de viaje no proporcionado.']);
    exit;
}
try {
    // 3. Verificar Propiedad (Igual que el original)
    $stmt_check = $pdo->prepare("SELECT operador_usuario_id FROM viajes WHERE viaje_id = ?");
    $stmt_check->execute([$viaje_id]);
    $viaje_owner = $stmt_check->fetchColumn();
    if ($viaje_owner != $operador_id) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'No tienes permiso para modificar este viaje.']);
        exit;
    }
    // 4. Procesar Acción (Igual que el original)
    if ($action === 'iniciar_viaje') {
        $stmt = $pdo->prepare("UPDATE viajes SET estado = 'En Curso', fecha_inicio = NOW() WHERE viaje_id = ?");
        $stmt->execute([$viaje_id]);
        echo json_encode(['status' => 'success', 'message' => 'Viaje iniciado']);
    
    } elseif ($action === 'finalizar_viaje') {
        $stmt = $pdo->prepare("UPDATE viajes SET estado = 'Finalizado', fecha_finalizacion = NOW() WHERE viaje_id = ?");
        $stmt->execute([$viaje_id]);
        echo json_encode(['status' => 'success', 'message' => 'Viaje finalizado']);
    
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Acción desconocida.']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error DB: ' . $e->getMessage()]);
}
?>