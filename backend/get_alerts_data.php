<?php
require_once 'config/db_connection.php';

function getAlertsForTrailero($pdo, $usuario_id) {
    try {
        // Obtener alertas relacionadas con rutas en las que el trailero es el operador
        $stmt = $pdo->prepare("
            SELECT
                a.alerta_id,
                a.descripcion AS descripcion_alerta,
                a.tipo_alerta,
                a.ubicacion_geom AS ubicacion_alerta_geojson,
                r.nombre AS nombre_ruta_afectada,
                r.descripcion AS descripcion_ruta_afectada
            FROM
                alertas a
            JOIN
                rutas r ON a.ruta_id = r.ruta_id
            JOIN
                viajes v ON r.ruta_id = v.ruta_id
            WHERE
                v.operador_usuario_id = :usuario_id
            ORDER BY a.alerta_id DESC
        ");
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        error_log("Error al obtener alertas del trailero: " . $e->getMessage());
        return [];
    }
}
?>