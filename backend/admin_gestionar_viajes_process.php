<?php
require_once 'auth_guard.php';
require_once 'config/db_connection.php';

$redirect_url = '../frontend/admin_gestionar_viajes.php';

try {
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
    $viaje_id = filter_input(INPUT_POST, 'viaje_id', FILTER_VALIDATE_INT);

    if ($action === 'create' || $action === 'update') {
        $ruta_id = filter_input(INPUT_POST, 'ruta_id', FILTER_VALIDATE_INT);
        $operador_usuario_id = filter_input(INPUT_POST, 'operador_usuario_id', FILTER_VALIDATE_INT);
        $vehiculo_id = filter_input(INPUT_POST, 'vehiculo_id', FILTER_VALIDATE_INT);
        $asignado_por_usuario_id = filter_input(INPUT_POST, 'asignado_por_usuario_id', FILTER_VALIDATE_INT);
        $estado = trim((string) filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_SPECIAL_CHARS));
        $fecha_inicio = trim((string) filter_input(INPUT_POST, 'fecha_inicio', FILTER_SANITIZE_SPECIAL_CHARS));
        $fecha_finalizacion = trim((string) filter_input(INPUT_POST, 'fecha_finalizacion', FILTER_SANITIZE_SPECIAL_CHARS));

        if ($fecha_finalizacion === '') {
            $fecha_finalizacion = null;
        }

        if (!$ruta_id || !$operador_usuario_id || !$vehiculo_id || !$asignado_por_usuario_id || $estado === '' || $fecha_inicio === '') {
            throw new Exception("Todos los campos, excepto fecha de finalización, son obligatorios.");
        }
    }

    switch ($action) {
        case 'create':
            $stmt = $pdo->prepare(
                "INSERT INTO viajes (ruta_id, operador_usuario_id, vehiculo_id, asignado_por_usuario_id, estado, fecha_asignacion, fecha_inicio, fecha_finalizacion) 
                 VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)"
            );
            $stmt->execute([$ruta_id, $operador_usuario_id, $vehiculo_id, $asignado_por_usuario_id, $estado, $fecha_inicio, $fecha_finalizacion]);
            break;

        case 'update':
            if (!$viaje_id) throw new Exception("ID de viaje no válido.");
            $stmt = $pdo->prepare(
                "UPDATE viajes 
                 SET ruta_id = ?, operador_usuario_id = ?, vehiculo_id = ?, asignado_por_usuario_id = ?, estado = ?, fecha_inicio = ?, fecha_finalizacion = ?
                 WHERE viaje_id = ?"
            );
            $stmt->execute([$ruta_id, $operador_usuario_id, $vehiculo_id, $asignado_por_usuario_id, $estado, $fecha_inicio, $fecha_finalizacion, $viaje_id]);
            break;

        case 'delete':
            if (!$viaje_id) throw new Exception("ID de viaje no válido.");
            $stmt = $pdo->prepare("DELETE FROM viajes WHERE viaje_id = ?");
            $stmt->execute([$viaje_id]);
            break;

        default:
            throw new Exception("Acción no reconocida.");
    }
    header('Location: ' . $redirect_url . '?status=success');

} catch (Exception $e) {
    header('Location: ' . $redirect_url . '?status=error&message=' . urlencode($e->getMessage()));
}
exit();
