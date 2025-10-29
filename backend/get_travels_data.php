<?php
// backend/get_travels_data.php

/**
 * Obtiene todos los viajes asignados a un trailero específico.
 *
 * @param PDO $pdo Objeto de conexión a la base de datos.
 * @param int $trailero_id El ID del usuario (trailero) que ha iniciado sesión.
 * @return array Un arreglo con los viajes encontrados.
 */
function getTravelsForTrailero(PDO $pdo, int $trailero_id): array
{
    // Preparamos la consulta SQL.
    // Usamos un JOIN para obtener también el nombre y la descripción de la ruta.
    $sql = "
        SELECT 
            v.viaje_id,
            v.estado_viaje,
            v.fecha_asignacion,
            v.fecha_inicio,
            v.fecha_finalizacion,
            r.nombre AS nombre_ruta,
            r.descripcion AS descripcion_ruta
        FROM 
            viajes v
        JOIN 
            rutas r ON v.ruta_id = r.ruta_id
        WHERE 
            v.usuario_id = :trailero_id
        ORDER BY 
            v.fecha_asignacion DESC
    ";

    try {
        $stmt = $pdo->prepare($sql);
        
        // Usamos un parámetro nombrado (:trailero_id) para evitar inyección SQL.
        $stmt->bindParam(':trailero_id', $trailero_id, PDO::PARAM_INT);
        
        $stmt->execute();
        
        // Devolvemos todos los viajes encontrados como un array asociativo.
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        // En caso de un error con la base de datos, se podría registrar el error.
        // Por ahora, devolvemos un array vacío para no romper la página.
        // error_log("Error al obtener viajes: " . $e->getMessage());
        return [];
    }
}