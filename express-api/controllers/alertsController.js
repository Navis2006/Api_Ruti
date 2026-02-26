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
        // 1. Obtener ruta_id del viaje y verificar que pertenezca al operador
        const [viajeRows] = await pool.execute(
            'SELECT ruta_id FROM viajes WHERE viaje_id = ? AND operador_usuario_id = ?',
            [viaje_id, operadorId]
        );

        if (viajeRows.length === 0) {
            return res.status(404).json({
                success: false,
                message: "Viaje no encontrado o no autorizado."
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
            INNER JOIN viajes v ON r.ruta_id = v.ruta_id
            LEFT JOIN usuarios u ON a.creado_por_usuario_id = u.usuario_id
            WHERE v.operador_usuario_id = ?
              AND a.estatus_alerta = 'Abierta'
            GROUP BY a.alerta_id
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
