<?php
require_once 'auth_guard.php';
require_once 'config/db_connection.php';
$redirect_url = '../frontend/admin_gestionar_vehiculos.php';

try {
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
    $vehiculo_id = filter_input(INPUT_POST, 'vehiculo_id', FILTER_VALIDATE_INT);

    if ($action === 'create' || $action === 'update') {
        $empresa_id = filter_input(INPUT_POST, 'empresa_id', FILTER_VALIDATE_INT);
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
        $placa = filter_input(INPUT_POST, 'placa', FILTER_SANITIZE_SPECIAL_CHARS);
        $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS);
        $altura_metros = filter_input(INPUT_POST, 'altura_metros', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $ancho_metros = filter_input(INPUT_POST, 'ancho_metros', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $largo_metros = filter_input(INPUT_POST, 'largo_metros', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $peso_toneladas = filter_input(INPUT_POST, 'peso_toneladas', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        
        if (!$empresa_id || empty($nombre) || empty($placa)) {
            throw new Exception("Empresa, nombre y placa son obligatorios.");
        }
    }

    switch ($action) {
        case 'create':
            $stmt = $pdo->prepare("INSERT INTO vehiculos (empresa_id, nombre, placa, tipo, altura_metros, ancho_metros, largo_metros, peso_toneladas) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$empresa_id, $nombre, $placa, $tipo, $altura_metros, $ancho_metros, $largo_metros, $peso_toneladas]);
            break;
        case 'update':
            if (!$vehiculo_id) throw new Exception("ID de vehículo no válido.");
            $stmt = $pdo->prepare("UPDATE vehiculos SET empresa_id=?, nombre=?, placa=?, tipo=?, altura_metros=?, ancho_metros=?, largo_metros=?, peso_toneladas=? WHERE vehiculo_id = ?");
            $stmt->execute([$empresa_id, $nombre, $placa, $tipo, $altura_metros, $ancho_metros, $largo_metros, $peso_toneladas, $vehiculo_id]);
            break;
        case 'delete':
            if (!$vehiculo_id) throw new Exception("ID de vehículo no válido.");
            $stmt = $pdo->prepare("DELETE FROM vehiculos WHERE vehiculo_id = ?");
            $stmt->execute([$vehiculo_id]);
            break;
        default:
            throw new Exception("Acción no reconocida.");
    }
    header('Location: ' . $redirect_url . '?status=success');
} catch (Exception $e) {
    header('Location: ' . $redirect_url . '?status=error&message=' . urlencode($e->getMessage()));
}
exit();