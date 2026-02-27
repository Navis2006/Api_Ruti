// controllers/tripController.js - Migración de get_my_trips.php, get_trip_details.php, update_trip_status.php
const pool = require('../config/db');

/**
 * GET /api/trips/my-trips
 * Requiere auth → req.userId
 */
const getMyTrips = async (req, res) => {
    const operadorId = req.userId;

    try {
        // A. Viaje Actual (En Curso)
        const [currentRows] = await pool.execute(
            `SELECT v.viaje_id, v.estado, v.fecha_inicio, r.nombre as ruta_nombre, ve.nombre as vehiculo_nombre,
              ve.placa as vehiculo_placa, ve.tipo as vehiculo_tipo, ve.altura as vehiculo_altura,
              ve.ancho as vehiculo_ancho, ve.largo as vehiculo_largo, ve.peso_total as vehiculo_peso,
              r.lat_origen, r.lng_origen, r.lat_destino, r.lng_destino,
              eo.nombre as origen_nombre, 
              COALESCE(r.nombre, (
                  SELECT ed.nombre 
                  FROM viaje_destinos vd 
                  JOIN empresas ed ON vd.empresa_id = ed.empresa_id 
                  WHERE vd.viaje_id = v.viaje_id 
                  ORDER BY vd.orden DESC LIMIT 1
              )) as destino_nombre
       FROM viajes v
       LEFT JOIN rutas r ON v.ruta_id = r.ruta_id
       LEFT JOIN vehiculos ve ON v.vehiculo_id = ve.vehiculo_id
       LEFT JOIN empresas eo ON v.origen_empresa_id = eo.empresa_id
       WHERE v.operador_usuario_id = ? AND v.estado = 'En Curso'
       ORDER BY v.fecha_inicio ASC
       LIMIT 1`,
            [operadorId]
        );

        // B. Próximos Viajes
        const [upcomingRows] = await pool.execute(
            `SELECT v.viaje_id, v.estado, v.fecha_inicio, r.nombre as ruta_nombre, ve.nombre as vehiculo_nombre,
              r.lat_origen, r.lng_origen, r.lat_destino, r.lng_destino,
              eo.nombre as origen_nombre, 
              COALESCE(r.nombre, (
                  SELECT ed.nombre 
                  FROM viaje_destinos vd 
                  JOIN empresas ed ON vd.empresa_id = ed.empresa_id 
                  WHERE vd.viaje_id = v.viaje_id 
                  ORDER BY vd.orden DESC LIMIT 1
              )) as destino_nombre
       FROM viajes v
       LEFT JOIN rutas r ON v.ruta_id = r.ruta_id
       LEFT JOIN vehiculos ve ON v.vehiculo_id = ve.vehiculo_id
       LEFT JOIN empresas eo ON v.origen_empresa_id = eo.empresa_id
       WHERE v.operador_usuario_id = ? AND v.estado IN ('Planeado', 'Asignado')
       ORDER BY v.fecha_inicio ASC`,
            [operadorId]
        );

        return res.status(200).json({
            success: true,
            data: {
                current_trip: currentRows[0] || null,
                upcoming_trips: upcomingRows
            }
        });

    } catch (error) {
        console.error('Error getMyTrips:', error.message);
        return res.status(500).json({
            success: false,
            message: 'Error DB: ' + error.message
        });
    }
};

/**
 * GET /api/trips/details/:id
 * Requiere auth → req.userId
 */
const getTripDetails = async (req, res) => {
    const operadorId = req.userId;
    const viajeId = parseInt(req.params.id);

    if (!viajeId || viajeId <= 0) {
        return res.status(400).json({
            success: false,
            message: 'ID de viaje inválido.'
        });
    }

    try {
        // 1. Consulta detalle del viaje (incluye WKT del trazado)
        const [rows] = await pool.execute(
            `SELECT 
          v.viaje_id, v.estado, v.fecha_inicio, 
          r.ruta_id, r.nombre as ruta_nombre, r.descripcion as ruta_descripcion,
          r.lat_origen, r.lng_origen, r.lat_destino, r.lng_destino,
          ST_AsText(r.trazado_geom) as trazado_wkt, 
          ve.nombre as vehiculo_nombre, ve.placa as vehiculo_placa
       FROM viajes v
       JOIN rutas r ON v.ruta_id = r.ruta_id
       JOIN vehiculos ve ON v.vehiculo_id = ve.vehiculo_id
       WHERE v.operador_usuario_id = ? AND v.viaje_id = ?
       LIMIT 1`,
            [operadorId, viajeId]
        );

        const viaje = rows[0];

        if (!viaje) {
            return res.status(404).json({
                success: false,
                message: 'Viaje no encontrado o no asignado.'
            });
        }

        // 2. Obtener alertas activas de la ruta
        const [alertas] = await pool.execute(
            `SELECT * FROM alertas 
       WHERE ruta_id = ? AND estatus_alerta = 'Abierta'
       ORDER BY nivel DESC`,
            [viaje.ruta_id]
        );

        viaje.alertas_activas = alertas;

        return res.status(200).json({
            success: true,
            data: viaje
        });

    } catch (error) {
        console.error('Error getTripDetails:', error.message);
        return res.status(500).json({
            success: false,
            message: 'Error DB: ' + error.message
        });
    }
};

/**
 * POST /api/trips/update-status
 * Requiere auth → req.userId
 * Body: { viaje_id, action } → action: "iniciar_viaje" | "finalizar_viaje"
 */
const updateTripStatus = async (req, res) => {
    const operadorId = req.userId;
    const { viaje_id, action } = req.body;

    if (!viaje_id) {
        return res.json({
            status: 'error',
            message: 'ID de viaje no proporcionado.'
        });
    }

    try {
        // 1. Verificar propiedad del viaje
        const [checkRows] = await pool.execute(
            'SELECT operador_usuario_id FROM viajes WHERE viaje_id = ?',
            [viaje_id]
        );

        const viajeOwner = checkRows[0]?.operador_usuario_id;

        if (viajeOwner != operadorId) {
            return res.status(403).json({
                status: 'error',
                message: 'No tienes permiso para modificar este viaje.'
            });
        }

        // 2. Procesar acción
        if (action === 'iniciar_viaje') {
            await pool.execute(
                "UPDATE viajes SET estado = 'En Curso', fecha_inicio = NOW() WHERE viaje_id = ?",
                [viaje_id]
            );
            return res.json({ status: 'success', message: 'Viaje iniciado' });

        } else if (action === 'finalizar_viaje') {
            await pool.execute(
                "UPDATE viajes SET estado = 'Finalizado', fecha_finalizacion = NOW() WHERE viaje_id = ?",
                [viaje_id]
            );
            return res.json({ status: 'success', message: 'Viaje finalizado' });

        } else {
            return res.json({ status: 'error', message: 'Acción desconocida.' });
        }

    } catch (error) {
        console.error('Error updateTripStatus:', error.message);
        return res.status(500).json({
            status: 'error',
            message: 'Error DB: ' + error.message
        });
    }
};

module.exports = { getMyTrips, getTripDetails, updateTripStatus };
