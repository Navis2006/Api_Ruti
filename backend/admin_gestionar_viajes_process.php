<?php
//session_start(); 
require_once 'auth_guard.php';
require_once 'config/db_connection.php';

// Verificamos que el admin esté logueado
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
    die(json_encode(['status' => 'error', 'message' => 'Acceso no autorizado']));
}

// El ID del admin que está creando/editando el viaje
$asignado_por_usuario_id = $_SESSION['usuario_id'];

$redirect_url = '../frontend/admin_gestionar_viajes.php';

try {
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
    $viaje_id = filter_input(INPUT_POST, 'viaje_id', FILTER_VALIDATE_INT);

    // --- ACCIÓN: CREAR O ACTUALIZAR (Desde el formulario) ---
    if ($action === 'create' || $action === 'update') {
        
        // CORREGIDO: Leer 'estado' y 'fecha_hora_programada' (del form)
        $ruta_id = filter_input(INPUT_POST, 'ruta_id', FILTER_VALIDATE_INT);
        $operador_usuario_id = filter_input(INPUT_POST, 'operador_usuario_id', FILTER_VALIDATE_INT);
        $vehiculo_id = filter_input(INPUT_POST, 'vehiculo_id', FILTER_VALIDATE_INT);
        $estado = trim((string) filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_SPECIAL_CHARS)); // Nombre del form
        $fecha_hora_programada = trim((string) filter_input(INPUT_POST, 'fecha_hora_programada', FILTER_SANITIZE_SPECIAL_CHARS)); // Nombre del form

        // Validación
        if (!$ruta_id || !$operador_usuario_id || !$vehiculo_id || $estado === '' || $fecha_hora_programada === '') {
            throw new Exception("Todos los campos son obligatorios.");
        }
    }
    
    // --- ACCIÓN: ACTUALIZAR ESTATUS (Desde el botón "Cancelar Viaje") ---
    else if ($action === 'update_status') {
        if (!$viaje_id) throw new Exception("ID de viaje no válido para actualizar estatus.");
        // CORREGIDO: Leer 'estado'
        $estado = trim((string) filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_SPECIAL_CHARS));
        if ($estado === '') throw new Exception("Estatus no válido.");
    }
    

    switch ($action) {
        case 'create':
            // CORREGIDO: Usar 'estado' y 'fecha_inicio' (nombres de tu BD)
            $stmt = $pdo->prepare(
                "INSERT INTO viajes (ruta_id, operador_usuario_id, vehiculo_id, asignado_por_usuario_id, estado, fecha_asignacion, fecha_inicio) 
                 VALUES (?, ?, ?, ?, ?, NOW(), ?)"
            );
            $stmt->execute([
                $ruta_id, 
                $operador_usuario_id, 
                $vehiculo_id, 
                $asignado_por_usuario_id, // ID del admin logueado
                $estado, 
                $fecha_hora_programada // El valor del form va a la columna 'fecha_inicio'
            ]);
            break;

        case 'update':
            if (!$viaje_id) throw new Exception("ID de viaje no válido.");
            
            // CORREGIDO: Usar 'estado' y 'fecha_inicio' (nombres de tu BD)
            $stmt = $pdo->prepare(
                "UPDATE viajes 
                 SET ruta_id = ?, operador_usuario_id = ?, vehiculo_id = ?, estado = ?, fecha_inicio = ?
                 WHERE viaje_id = ?"
            );
            $stmt->execute([
                $ruta_id, 
                $operador_usuario_id, 
                $vehiculo_id, 
                $estado, 
                $fecha_hora_programada, // El valor del form va a la columna 'fecha_inicio'
                $viaje_id
            ]);
            break;

        case 'update_status':
            // CORREGIDO: Usar 'estado'
            $stmt = $pdo->prepare("UPDATE viajes SET estado = ? WHERE viaje_id = ?");
            $stmt->execute([$estado, $viaje_id]);
            
            echo json_encode(['status' => 'success', 'message' => 'Viaje actualizado.']);
            exit; // Salimos para no redirigir

        default:
            throw new Exception("Acción no reconocida.");
    }
    
    // Si la acción fue 'create' o 'update', redirigimos
    header('Location: ' . $redirect_url . '?status=success');

} catch (Exception $e) {
    // Si la acción fue 'update_status' (AJAX), devolvemos JSON
    if ($action === 'update_status') {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    } else {
        // Si fue 'create' o 'update', redirigimos con error
        header('Location: ' . $redirect_url . '?status=error&message=' . urlencode($e->getMessage()));
    }
}
exit();
?>