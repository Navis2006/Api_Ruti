<?php
require_once 'auth_guard.php';
require_once 'config/db_connection.php';

$redirect_url = '../frontend/admin_gestionar_rutas.php';

try {
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
    $ruta_id = filter_input(INPUT_POST, 'ruta_id', FILTER_VALIDATE_INT);

    if ($action === 'create' || $action === 'update') {
        $empresa_id = filter_input(INPUT_POST, 'empresa_id', FILTER_VALIDATE_INT);
        $nombre = trim((string) filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS));
        $descripcion = trim((string) filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_SPECIAL_CHARS));
        $creado_por_usuario_id = filter_input(INPUT_POST, 'creado_por_usuario_id', FILTER_VALIDATE_INT);

        // Coordenadas de origen y destino
        $lat_origen = filter_input(INPUT_POST, 'lat_origen', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $lng_origen = filter_input(INPUT_POST, 'lng_origen', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $lat_destino = filter_input(INPUT_POST, 'lat_destino', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $lng_destino = filter_input(INPUT_POST, 'lng_destino', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        // Generar trazado_geom automáticamente si tenemos las 4 coordenadas
        $trazado_geom = null;
        if ($lat_origen && $lng_origen && $lat_destino && $lng_destino) {
            $trazado_geom = "LINESTRING($lng_origen $lat_origen, $lng_destino $lat_destino)";
        }

        if ($nombre === '' || !$empresa_id || !$creado_por_usuario_id) {
            throw new Exception("Nombre, empresa y creador son obligatorios.");
        }
    }

    switch ($action) {
        case 'create':
            $stmt = $pdo->prepare(
                "INSERT INTO rutas (empresa_id, nombre, descripcion, trazado_geom, creado_por_usuario_id, lat_origen, lng_origen, lat_destino, lng_destino) 
                 VALUES (?, ?, ?, ST_GeomFromText(?), ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$empresa_id, $nombre, $descripcion, $trazado_geom, $creado_por_usuario_id, $lat_origen, $lng_origen, $lat_destino, $lng_destino]);
            break;

        case 'update':
            if (!$ruta_id)
                throw new Exception("ID de ruta no válido.");
            $stmt = $pdo->prepare(
                "UPDATE rutas SET 
                    empresa_id = ?, 
                    nombre = ?, 
                    descripcion = ?, 
                    trazado_geom = ST_GeomFromText(?), 
                    creado_por_usuario_id = ?,
                    lat_origen = ?,
                    lng_origen = ?,
                    lat_destino = ?,
                    lng_destino = ?
                 WHERE ruta_id = ?"
            );
            $stmt->execute([$empresa_id, $nombre, $descripcion, $trazado_geom, $creado_por_usuario_id, $lat_origen, $lng_origen, $lat_destino, $lng_destino, $ruta_id]);
            break;

        case 'delete':
            if (!$ruta_id)
                throw new Exception("ID de ruta no válido.");
            $stmt = $pdo->prepare("DELETE FROM rutas WHERE ruta_id = ?");
            $stmt->execute([$ruta_id]);
            break;

        default:
            throw new Exception("Acción no reconocida.");
    }
    header('Location: ' . $redirect_url . '?status=success');

} catch (Exception $e) {
    header('Location: ' . $redirect_url . '?status=error&message=' . urlencode($e->getMessage()));
}
exit();