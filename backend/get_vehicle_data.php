<?php
require_once 'config/db_connection.php';

function getVehicleForTrailero($pdo, $usuario_id) {
    try {
        // Obtener el vehículo asignado al trailero para su viaje actual o último
        // Esto puede ser más complejo si un trailero puede tener múltiples vehículos o historial.
        // Por simplicidad, asumimos que obtenemos el vehículo de un viaje activo o el último asignado.
        // Idealmente, la relación usuario-vehículo debería ser más directa si es un vehículo "asignado".
        // Para este modelo, lo buscaremos a través del vehículo_id del último viaje activo.

        // Primero, obtener el vehiculo_id del viaje más reciente o activo del trailero
        $stmt_vehicle_id = $pdo->prepare("
            SELECT vh.vehiculo_id, vh.nombre, vh.placa, vh.tipo, vh.altura_metros, vh.ancho_metros, vh.largo_metros, vh.peso_toneladas, e.nombre AS nombre_empresa
            FROM vehiculos vh
            JOIN viajes vj ON vh.vehiculo_id = vj.vehiculo_id
            JOIN usuarios u ON vj.operador_usuario_id = u.usuario_id
            JOIN empresas e ON vh.empresa_id = e.empresa_id
            WHERE u.usuario_id = :usuario_id
            ORDER BY vj.fecha_inicio DESC, vj.fecha_asignacion DESC
            LIMIT 1
        ");
        $stmt_vehicle_id->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt_vehicle_id->execute();
        return $stmt_vehicle_id->fetch(PDO::FETCH_ASSOC);

    } catch (\PDOException $e) {
        error_log("Error al obtener vehículo del trailero: " . $e->getMessage());
        return false;
    }
}
?>