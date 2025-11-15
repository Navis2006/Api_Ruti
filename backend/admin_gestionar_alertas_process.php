<?php
//session_start();
require_once 'config/db_connection.php';
require_once 'auth_guard.php'; // Protege el script

// Verificamos que el admin esté logueado
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
    die("Acceso no autorizado.");
}

// El ID del admin que está creando la alerta
$admin_usuario_id = $_SESSION['usuario_id'];

$redirect_url = '../frontend/admin_gestionar_alertas.php';

try {
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
    $alerta_id = filter_input(INPUT_POST, 'alerta_id', FILTER_VALIDATE_INT);

    // --- ACCIÓN: CREAR O ACTUALIZAR (Desde el formulario) ---
    if ($action === 'create' || $action === 'update') {
        
        // Obtenemos los datos del formulario
        $ruta_id = filter_input(INPUT_POST, 'ruta_id', FILTER_VALIDATE_INT);
        $tipo_alerta = trim((string) filter_input(INPUT_POST, 'tipo_alerta', FILTER_SANITIZE_SPECIAL_CHARS));
        $descripcion = trim((string) filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_SPECIAL_CHARS));
        $ubicacion_geom = trim((string) filter_input(INPUT_POST, 'ubicacion_geom', FILTER_SANITIZE_SPECIAL_CHARS));
        $nivel = filter_input(INPUT_POST, 'nivel', FILTER_VALIDATE_INT);
        $estatus_alerta = trim((string) filter_input(INPUT_POST, 'estatus_alerta', FILTER_SANITIZE_SPECIAL_CHARS));
        
        // Validación
        if (empty($descripcion) || !$ruta_id || empty($tipo_alerta)) {
            throw new Exception("Ruta, Tipo y Descripción son obligatorios.");
        }
        
        if (!empty($ubicacion_geom) && !preg_match('/^(POINT)\s*\(/i', $ubicacion_geom)) {
            throw new Exception("Formato de Ubicación no válido. Debe ser POINT(lon lat)");
        }
        if (empty($ubicacion_geom)) {
            $ubicacion_geom = null; // Permitir que sea nulo si está vacío
        }
    }
    
    switch ($action) {
        case 'create':
            // (Este SQL asume que 'nivel' y 'estatus_alerta' YA existen)
            $stmt = $pdo->prepare(
                "INSERT INTO alertas (ruta_id, creado_por_usuario_id, descripcion, tipo_alerta, nivel, estatus_alerta, ubicacion_geom) 
                 VALUES (?, ?, ?, ?, ?, ?, " . ($ubicacion_geom ? "ST_GeomFromText(?)" : "NULL") . ")"
            );
            
            $params = [$ruta_id, $admin_usuario_id, $descripcion, $tipo_alerta, $nivel, $estatus_alerta];
            if ($ubicacion_geom) {
                $params[] = $ubicacion_geom;
            }
            
            $stmt->execute($params);
            break;

        case 'update':
            if (!$alerta_id) throw new Exception("ID de alerta no válido.");
            
            $stmt = $pdo->prepare(
                "UPDATE alertas 
                 SET ruta_id = ?, descripcion = ?, tipo_alerta = ?, nivel = ?, estatus_alerta = ?, 
                     ubicacion_geom = " . ($ubicacion_geom ? "ST_GeomFromText(?)" : "NULL") . "
                 WHERE alerta_id = ?"
            );
            
            $params = [$ruta_id, $descripcion, $tipo_alerta, $nivel, $estatus_alerta];
            if ($ubicacion_geom) {
                $params[] = $ubicacion_geom;
            }
            $params[] = $alerta_id;

            $stmt->execute($params);
            break;

        case 'delete':
            if (!$alerta_id) throw new Exception("ID de alerta no válido.");
            
            $stmt = $pdo->prepare("DELETE FROM alertas WHERE alerta_id = ?");
            $stmt->execute([$alerta_id]);
            break;

        default:
            throw new Exception("Acción no reconocida.");
    }
    
    // Si la acción fue 'create', 'update' o 'delete', redirigimos
    header('Location: ' . $redirect_url . '?status=success');

} catch (Exception $e) {
    // Redirigimos con error
    header('Location: ' . $redirect_url . '?status=error&message=' . urlencode($e->getMessage()));
}
exit();
?>