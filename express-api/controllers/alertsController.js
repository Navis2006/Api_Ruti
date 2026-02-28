// controllers/alertsController.js - Adaptación de report_alert.php y get_my_alerts.php
const pool = require('../config/db');

/**
 * POST /api/alerts/report
 * Equivalente a report_alert.php
 * Requiere auth → req.userId
 */
const reportAlert = async (req, res) => {
    const operadorId = req.userId;
    const { viaje_id, tipo_alerta, latitud, longitud, descripcion } = req.body;

    // Validar datos mínimos
    if (!viaje_id || !tipo_alerta || !latitud || !longitud) {
        return res.status(400).json({
            success: false,
            message: "Datos incompletos. Se requieren viaje_id, tipo_alerta, latitud y longitud."
        });
    }

    try {
        // 1. Obtener el viaje más actual del operador (En Curso, Asignado o Planeado)
        // y buscar el primer ruta_id válido en sus destinos.
        const [viajeRows] = await pool.execute(
            `SELECT vd.ruta_id 
             FROM viajes v
             INNER JOIN viaje_destinos vd ON v.viaje_id = vd.viaje_id
             WHERE v.operador_usuario_id = ? 
               AND v.estado IN ('En Curso', 'Asignado', 'Planeado')
               AND vd.ruta_id IS NOT NULL
             ORDER BY 
               CASE v.estado 
                 WHEN 'En Curso' THEN 1 
                 WHEN 'Asignado' THEN 2 
                 WHEN 'Planeado' THEN 3 
                 ELSE 4 
               END, 
               v.fecha_inicio ASC,
               vd.orden ASC
             LIMIT 1`,
            [operadorId]
        );

        if (viajeRows.length === 0) {
            return res.status(404).json({
                success: false,
                message: "No se encontró un viaje activo con una ruta asignada para este operador."
            });
        }

        const rutaId = viajeRows[0].ruta_id;

        // 2. Determinar Nivel de Prioridad
        const niveles = {
            'Baches Peligrosos': 3,
            'Tráfico': 3,
            'Peligro en Vía': 4,
            'Accidente': 5,
            'Otro': 3
        };
        const nivel = niveles[tipo_alerta] || 3;

        // 3. Preparar Descripción (si viene vacía, usar default)
        let descripcionAlerta = descripcion ? descripcion.trim() : '';
        if (!descripcionAlerta) {
            const descripcionesDefault = {
                'Baches Peligrosos': 'Baches peligrosos reportados en la ruta',
                'Accidente': 'Accidente reportado en la ruta',
                'Tráfico': 'Tráfico pesado reportado en la ruta',
                'Peligro en Vía': 'Cables bajos reportados en la ruta',
                'Otro': 'Incidente reportado en la ruta'
            };
            descripcionAlerta = descripcionesDefault[tipo_alerta] || 'Alerta reportada en la ruta';
        }

        // 4. Insertar Alerta (POINT WKT)
        // WKT formato: LONG LAT
        const pointWkt = `POINT(${longitud} ${latitud})`;

        const [result] = await pool.execute(
            `INSERT INTO alertas 
             (ruta_id, creado_por_usuario_id, descripcion, tipo_alerta, nivel, estatus_alerta, ubicacion_geom)
             VALUES (?, ?, ?, ?, ?, 'Abierta', ST_GeomFromText(?))`,
            [rutaId, operadorId, descripcionAlerta, tipo_alerta, nivel, pointWkt]
        );

        return res.status(201).json({
            success: true,
            message: "Alerta reportada exitosamente.",
            alerta_id: result.insertId
        });

    } catch (error) {
        console.error('Error reportAlert:', error.message);
        return res.status(500).json({
            success: false,
            message: "Error interno del servidor.",
            debug: error.message
        });
    }
};

/**
 * GET /api/alerts/my-alerts
 * Equivalente a get_my_alerts.php
 * Requiere auth → req.userId
 */
const getMyAlerts = async (req, res) => {
    const operadorId = req.userId;

    try {
        const query = `
            SELECT 
                a.alerta_id,
                a.ruta_id,
                a.descripcion,
                a.tipo_alerta,
                a.nivel,
                a.estatus_alerta,
                ST_X(a.ubicacion_geom) AS longitud,
                ST_Y(a.ubicacion_geom) AS latitud,
                r.nombre AS nombre_ruta,
                CONCAT(u.nombre, ' ', u.apellidos) AS creador_nombre
            FROM alertas a
            INNER JOIN rutas r ON a.ruta_id = r.ruta_id
            LEFT JOIN usuarios u ON a.creado_por_usuario_id = u.usuario_id
            WHERE a.estatus_alerta = 'Abierta'
              AND a.ruta_id IN (
                  -- Subconsulta: Obtener todas las rutas del viaje más actual del operador
                  SELECT vd.ruta_id 
                  FROM viajes v
                  INNER JOIN viaje_destinos vd ON v.viaje_id = vd.viaje_id
                  WHERE v.operador_usuario_id = ? 
                    AND v.estado IN ('En Curso', 'Asignado', 'Planeado')
                    AND vd.ruta_id IS NOT NULL
                  ORDER BY 
                    CASE v.estado 
                      WHEN 'En Curso' THEN 1 
                      WHEN 'Asignado' THEN 2 
                      WHEN 'Planeado' THEN 3 
                      ELSE 4 
                    END, 
                    v.fecha_inicio ASC
                  LIMIT 10 -- Límite razonable por si el viaje tiene muchas paradas
              )
            ORDER BY a.nivel DESC, a.alerta_id DESC
        `;

        const [alertas] = await pool.execute(query, [operadorId]);

        // Ajustar tipos
        const formattedAlerts = alertas.map(alerta => ({
            ...alerta,
            alerta_id: parseInt(alerta.alerta_id, 10),
            ruta_id: parseInt(alerta.ruta_id, 10),
            nivel: parseInt(alerta.nivel, 10),
            longitud: parseFloat(alerta.longitud),
            latitud: parseFloat(alerta.latitud)
        }));

        return res.status(200).json({
            success: true,
            data: formattedAlerts
        });

    } catch (error) {
        console.error('Error getMyAlerts:', error.message);
        return res.status(500).json({
            success: false,
            message: "Error al obtener las alertas."
        });
    }
};

module.exports = {
    reportAlert,
    getMyAlerts
};
