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
            $empresa_id = filter_input(INPUT_POST, 'empresa_id', FILTER_VALIDATE_INT);
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
            $placa = filter_input(INPUT_POST, 'placa', FILTER_SANITIZE_SPECIAL_CHARS);
            $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS);
            $estatus = filter_input(INPUT_POST, 'estatus', FILTER_SANITIZE_SPECIAL_CHARS); // Campo nuevo
            $altura_metros = filter_input(INPUT_POST, 'altura_metros', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $ancho_metros = filter_input(INPUT_POST, 'ancho_metros', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $largo_metros = filter_input(INPUT_POST, 'largo_metros', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $peso_toneladas = filter_input(INPUT_POST, 'peso_toneladas', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

            if (!$empresa_id || empty($nombre) || empty($placa)) {
                throw new Exception("Empresa, nombre y placa son obligatorios.");
            }
            
            if ($action === 'create') {
                $stmt = $pdo->prepare("INSERT INTO vehiculos (empresa_id, nombre, placa, tipo, estatus, altura_metros, ancho_metros, largo_metros, peso_toneladas) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$empresa_id, $nombre, $placa, $tipo, $estatus, $altura_metros, $ancho_metros, $largo_metros, $peso_toneladas]);

            } else { // update
                if (!$vehiculo_id) throw new Exception("ID de vehículo no válido.");
                $stmt = $pdo->prepare("UPDATE vehiculos SET empresa_id=?, nombre=?, placa=?, tipo=?, estatus=?, altura_metros=?, ancho_metros=?, largo_metros=?, peso_toneladas=? WHERE vehiculo_id = ?");
                $stmt->execute([$empresa_id, $nombre, $placa, $tipo, $estatus, $altura_metros, $ancho_metros, $largo_metros, $peso_toneladas, $vehiculo_id]);
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

} catch (Exception $e) {
    if (isset($_POST['action']) && ($_POST['action'] === 'create' || $_POST['action'] === 'update')) {
        header('Location: ' . $redirect_url . '?status=error&message=' . urlencode($e->getMessage()));
        exit();
    }
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
exit();