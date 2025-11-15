<?php
session_start();
require_once 'config/db_connection.php';
require_once 'auth_guard.php'; // Protege el script

// Verificamos que sea un operador (rol_id = 2)
// ¡Asegúrate de que 'rol_id' sea 2 para tus operadores!
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 2) {
    // Si no es un operador, devolvemos un error JSON
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Acceso no autorizado']);
    exit;
}

// Preparamos la respuesta JSON
header('Content-Type: application/json');

try {
    $action = $_POST['action'] ?? '';
    $alerta_id = $_POST['alerta_id'] ?? 0;
    
    if ($action === 'marcar_leida' && $alerta_id > 0) {
        
        // ¡IMPORTANTE! 
        // Aquí asumimos que tu amigo ya añadió la columna 'estatus_alerta'
        // Cambiamos el estatus a 'Resuelta'
        $stmt = $pdo->prepare("UPDATE alertas SET estatus_alerta = 'Resuelta' WHERE alerta_id = ?");
        $stmt->execute([$alerta_id]);
        
        echo json_encode(['status' => 'success', 'message' => 'Alerta marcada como leída']);
        
    } else {
        throw new Exception('Acción no válida o ID de alerta faltante.');
    }

} catch (PDOException $e) {
    // Si falla (ej. la columna 'estatus_alerta' no existe),
    // devolvemos el error de SQL
    echo json_encode(['status' => 'error', 'message' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
exit;
?>