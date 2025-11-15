<?php
session_start();
require_once 'config/db_connection.php';
require_once 'auth_guard.php'; // Protege el script

// Verificamos que sea un operador
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 2) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Acceso no autorizado']);
    exit;
}

header('Content-Type: application/json');
$operador_id = $_SESSION['usuario_id'];
$viaje_id = $_POST['viaje_id'] ?? 0;
$action = $_POST['action'] ?? '';

if (!$viaje_id) {
    echo json_encode(['status' => 'error', 'message' => 'ID de viaje no proporcionado.']);
    exit;
}

try {
    // Verificación de seguridad: ¿Este viaje le pertenece a este operador?
    $stmt_check = $pdo->prepare("SELECT operador_usuario_id FROM viajes WHERE viaje_id = ?");
    $stmt_check->execute([$viaje_id]);
    $viaje_owner = $stmt_check->fetchColumn();

    if ($viaje_owner != $operador_id) {
        echo json_encode(['status' => 'error', 'message' => 'No tienes permiso para modificar este viaje.']);
        exit;
    }

    // Procesar la acción
    if ($action === 'iniciar_viaje') {
        // Usamos 'estado' y 'fecha_inicio' (de tu BD)
        $stmt = $pdo->prepare("UPDATE viajes SET estado = 'En Curso', fecha_inicio = NOW() WHERE viaje_id = ?");
        $stmt->execute([$viaje_id]);
        echo json_encode(['status' => 'success', 'message' => 'Viaje iniciado']);
    
    } elseif ($action === 'finalizar_viaje') {
        // Usamos 'estado' y 'fecha_finalizacion' (de tu BD)
        $stmt = $pdo->prepare("UPDATE viajes SET estado = 'Finalizado', fecha_finalizacion = NOW() WHERE viaje_id = ?");
        $stmt->execute([$viaje_id]);
        echo json_encode(['status' => 'success', 'message' => 'Viaje finalizado']);
    
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Acción desconocida.']);
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error de base de datos: ' . $e->getMessage()]);
}
exit;
?>