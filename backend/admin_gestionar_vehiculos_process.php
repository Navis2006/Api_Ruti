<?php
require_once 'auth_guard.php';
require_once 'config/db_connection.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Ocurrió un error inesperado.'];
$redirect_url = '../frontend/admin_gestionar_vehiculos.php';

try {
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);

    switch ($action) {
        case 'create':
        case 'update':
            $vehiculo_id = filter_input(INPUT_POST, 'vehiculo_id', FILTER_VALIDATE_INT);
            // Ya no recibimos empresa_id
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
            $placa = filter_input(INPUT_POST, 'placa', FILTER_SANITIZE_SPECIAL_CHARS);
            $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS);
            $estatus = filter_input(INPUT_POST, 'estatus', FILTER_SANITIZE_SPECIAL_CHARS);
            $ancho_metros = filter_input(INPUT_POST, 'ancho_metros', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $largo_metros = filter_input(INPUT_POST, 'largo_metros', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $peso_toneladas = filter_input(INPUT_POST, 'peso_toneladas', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $peso_eje_kg = filter_input(INPUT_POST, 'peso_eje_kg', FILTER_VALIDATE_INT);
            $velocidad_max_kmh = filter_input(INPUT_POST, 'velocidad_max_kmh', FILTER_VALIDATE_INT);

            // Convertir campos vacíos a null para evitar errores de SQL
            $altura_metros = $altura_metros !== false && $altura_metros !== null ? $altura_metros : null;
            $ancho_metros = $ancho_metros !== false && $ancho_metros !== null ? $ancho_metros : null;
            $largo_metros = $largo_metros !== false && $largo_metros !== null ? $largo_metros : null;
            $peso_toneladas = $peso_toneladas !== false && $peso_toneladas !== null ? $peso_toneladas : null;
            $peso_eje_kg = $peso_eje_kg !== false && $peso_eje_kg !== null ? $peso_eje_kg : null;
            $velocidad_max_kmh = $velocidad_max_kmh !== false && $velocidad_max_kmh !== null ? $velocidad_max_kmh : null;

            if (empty($nombre) || empty($placa)) {
                throw new Exception("Nombre y placa son obligatorios.");
            }

            if ($action === 'create') {
                $stmt = $pdo->prepare("INSERT INTO vehiculos (nombre, placa, tipo, estatus, altura_metros, ancho_metros, largo_metros, peso_toneladas, peso_eje_kg, velocidad_max_kmh) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $placa, $tipo, $estatus, $altura_metros, $ancho_metros, $largo_metros, $peso_toneladas, $peso_eje_kg, $velocidad_max_kmh]);

            } else { // update
                if (!$vehiculo_id)
                    throw new Exception("ID de vehículo no válido.");
                $stmt = $pdo->prepare("UPDATE vehiculos SET nombre=?, placa=?, tipo=?, estatus=?, altura_metros=?, ancho_metros=?, largo_metros=?, peso_toneladas=?, peso_eje_kg=?, velocidad_max_kmh=? WHERE vehiculo_id = ?");
                $stmt->execute([$nombre, $placa, $tipo, $estatus, $altura_metros, $ancho_metros, $largo_metros, $peso_toneladas, $peso_eje_kg, $velocidad_max_kmh, $vehiculo_id]);
            }
            header('Location: ' . $redirect_url . '?success=true');
            exit();

        case 'update_status':
            $vehiculo_id = filter_input(INPUT_POST, 'vehiculo_id', FILTER_VALIDATE_INT);
            $estatus = filter_input(INPUT_POST, 'estatus', FILTER_SANITIZE_SPECIAL_CHARS);

            if (!$vehiculo_id || !in_array($estatus, ['en_servicio', 'en_mantenimiento', 'de_baja'])) {
                throw new Exception("Datos no válidos para actualizar el estatus.");
            }

            $stmt = $pdo->prepare("UPDATE vehiculos SET estatus = ? WHERE vehiculo_id = ?");
            $stmt->execute([$estatus, $vehiculo_id]);

            if ($stmt->rowCount() > 0) {
                $response = ['status' => 'success', 'message' => 'Estatus del vehículo actualizado.'];
            } else {
                throw new Exception("No se encontró el vehículo para actualizar.");
            }
            break;

        default:
            throw new Exception("Acción no reconocida.");
    }

} catch (PDOException $e) {
    $userMessage = 'Error de base de datos. Verifica que los campos numéricos tengan valores válidos.';
    if (isset($_POST['action']) && ($_POST['action'] === 'create' || $_POST['action'] === 'update')) {
        header('Location: ' . $redirect_url . '?status=error&message=' . urlencode($userMessage));
        exit();
    }
    $response = ['status' => 'error', 'message' => $userMessage];
} catch (Exception $e) {
    if (isset($_POST['action']) && ($_POST['action'] === 'create' || $_POST['action'] === 'update')) {
        header('Location: ' . $redirect_url . '?status=error&message=' . urlencode($e->getMessage()));
        exit();
    }
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
exit();