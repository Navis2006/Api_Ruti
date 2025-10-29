<?php
require_once 'auth_guard.php';
require_once 'config/db_connection.php';
$redirect_url = '../frontend/admin_gestionar_empresas.php';

try {
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
    $empresa_id = filter_input(INPUT_POST, 'empresa_id', FILTER_VALIDATE_INT);

    if ($action === 'create' || $action === 'update') {
        $nombre = trim((string) filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS));
        $estado_suscripcion = trim((string) filter_input(INPUT_POST, 'estado_suscripcion', FILTER_SANITIZE_SPECIAL_CHARS));

        if ($nombre === '' || $estado_suscripcion === '') {
            throw new Exception("Nombre y estado de suscripción son obligatorios.");
        }
    }

    switch ($action) {
        case 'create':
            $stmt = $pdo->prepare("INSERT INTO empresas (nombre, estado_suscripcion) VALUES (?, ?)");
            $stmt->execute([$nombre, $estado_suscripcion]);
            break;
        case 'update':
            if (!$empresa_id) throw new Exception("ID de empresa no válido.");
            $stmt = $pdo->prepare("UPDATE empresas SET nombre = ?, estado_suscripcion = ? WHERE empresa_id = ?");
            $stmt->execute([$nombre, $estado_suscripcion, $empresa_id]);
            break;
        case 'delete':
            if (!$empresa_id) throw new Exception("ID de empresa no válido.");
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
