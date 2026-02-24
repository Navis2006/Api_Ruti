<?php
require_once 'auth_guard.php';
require_once 'config/db_connection.php';
$redirect_url = '../frontend/admin_gestionar_empresas.php';

try {
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
    $empresa_id = filter_input(INPUT_POST, 'empresa_id', FILTER_VALIDATE_INT);

    if ($action === 'create' || $action === 'update') {
        $nombre = trim((string) filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS));
        $estado = trim((string) filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_SPECIAL_CHARS));

        $lat = filter_input(INPUT_POST, 'lat', FILTER_VALIDATE_FLOAT);
        $lng = filter_input(INPUT_POST, 'lng', FILTER_VALIDATE_FLOAT);

        if ($nombre === '' || $estado === '') {
            throw new Exception("Nombre y estado son obligatorios.");
        }

        // Convert strict false to null for DB insertion if empty
        $lat_val = ($lat !== false && $lat !== null) ? $lat : null;
        $lng_val = ($lng !== false && $lng !== null) ? $lng : null;
    }

    switch ($action) {
        case 'create':
            $stmt = $pdo->prepare("INSERT INTO empresas (nombre, estado, lat, lng) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nombre, $estado, $lat_val, $lng_val]);
            break;
        case 'update':
            if (!$empresa_id)
                throw new Exception("ID de empresa no válido.");
            $stmt = $pdo->prepare("UPDATE empresas SET nombre = ?, estado = ?, lat = ?, lng = ? WHERE empresa_id = ?");
            $stmt->execute([$nombre, $estado, $lat_val, $lng_val, $empresa_id]);
            break;
        case 'delete':
            if (!$empresa_id)
                throw new Exception("ID de empresa no válido.");
            $stmt = $pdo->prepare("DELETE FROM empresas WHERE empresa_id = ?");
            $stmt->execute([$empresa_id]);
            break;
        default:
            throw new Exception("Acción no reconocida.");
    }
    header('Location: ' . $redirect_url . '?status=success');
} catch (Exception $e) {
    header('Location: ' . $redirect_url . '?status=error&message=' . urlencode($e->getMessage()));
}
exit();
