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

        $origen_empresa_id = filter_input(INPUT_POST, 'origen_empresa_id', FILTER_VALIDATE_INT);
        $operador_usuario_id = filter_input(INPUT_POST, 'operador_usuario_id', FILTER_VALIDATE_INT);
        $vehiculo_id = filter_input(INPUT_POST, 'vehiculo_id', FILTER_VALIDATE_INT);
        $estado = trim((string) filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_SPECIAL_CHARS));
        $fecha_hora_programada = trim((string) filter_input(INPUT_POST, 'fecha_hora_programada', FILTER_SANITIZE_SPECIAL_CHARS));

        $destinos = $_POST['destinos'] ?? [];

        // Validación
        if (!$origen_empresa_id || !$operador_usuario_id || !$vehiculo_id || $estado === '' || $fecha_hora_programada === '') {
            throw new Exception("Todos los campos base son obligatorios.");
        }

        // Filtramos destinos vacíos por si acaso
        $destinos_validos = array_filter($destinos, function ($v) {
            return !empty($v); });

        if (empty($destinos_validos)) {
            throw new Exception("Debes especificar al menos una parada de destino.");
        }
    }

    // --- ACCIÓN: ACTUALIZAR ESTATUS (Desde el botón "Cancelar Viaje") ---
    else if ($action === 'update_status') {
        if (!$viaje_id)
            throw new Exception("ID de viaje no válido para actualizar estatus.");
        $estado = trim((string) filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_SPECIAL_CHARS));
        if ($estado === '')
            throw new Exception("Estatus no válido.");
    }

    switch ($action) {
        case 'create':
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                "INSERT INTO viajes (origen_empresa_id, operador_usuario_id, vehiculo_id, asignado_por_usuario_id, estado, fecha_asignacion, fecha_inicio) 
                 VALUES (?, ?, ?, ?, ?, NOW(), ?)"
            );
            $stmt->execute([
                $origen_empresa_id,
                $operador_usuario_id,
                $vehiculo_id,
                $asignado_por_usuario_id,
                $estado,
                $fecha_hora_programada
            ]);

            $new_viaje_id = $pdo->lastInsertId();

            // Insertamos cada destino ordenadamente
            $orden = 1;
            $stmtDest = $pdo->prepare("INSERT INTO viaje_destinos (viaje_id, empresa_id, ruta_id, orden) VALUES (?, ?, ?, ?)");
            foreach ($destinos_validos as $dest) {
                list($type, $id) = explode('_', $dest);
                $empresa_id_val = ($type === 'empresa') ? $id : null;
                $ruta_id_val = ($type === 'ruta') ? $id : null;

                $stmtDest->execute([$new_viaje_id, $empresa_id_val, $ruta_id_val, $orden]);
                $orden++;
            }

            $pdo->commit();
            break;

        case 'update':
            if (!$viaje_id)
                throw new Exception("ID de viaje no válido.");
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                "UPDATE viajes 
                 SET origen_empresa_id = ?, operador_usuario_id = ?, vehiculo_id = ?, estado = ?, fecha_inicio = ?
                 WHERE viaje_id = ?"
            );
            $stmt->execute([
                $origen_empresa_id,
                $operador_usuario_id,
                $vehiculo_id,
                $estado,
                $fecha_hora_programada,
                $viaje_id
            ]);

            // Limpiamos sub-tabla y reinsertamos
            $pdo->prepare("DELETE FROM viaje_destinos WHERE viaje_id = ?")->execute([$viaje_id]);

            $orden = 1;
            $stmtDest = $pdo->prepare("INSERT INTO viaje_destinos (viaje_id, empresa_id, ruta_id, orden) VALUES (?, ?, ?, ?)");
            foreach ($destinos_validos as $dest) {
                list($type, $id) = explode('_', $dest);
                $empresa_id_val = ($type === 'empresa') ? $id : null;
                $ruta_id_val = ($type === 'ruta') ? $id : null;

                $stmtDest->execute([$viaje_id, $empresa_id_val, $ruta_id_val, $orden]);
                $orden++;
            }

            $pdo->commit();
            break;

        case 'update_status':
            $stmt = $pdo->prepare("UPDATE viajes SET estado = ? WHERE viaje_id = ?");
            $stmt->execute([$estado, $viaje_id]);

            echo json_encode(['status' => 'success', 'message' => 'Viaje actualizado.']);
            exit;

        default:
            throw new Exception("Acción no reconocida.");
    }

    header('Location: ' . $redirect_url . '?status=success');

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    if (isset($action) && $action === 'update_status') {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    } else {
        header('Location: ' . $redirect_url . '?status=error&message=' . urlencode($e->getMessage()));
    }
}
exit();