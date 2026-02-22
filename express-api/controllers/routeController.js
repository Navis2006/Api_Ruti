// controllers/routeController.js - Ruteo Inteligente (TomTom API)
const axios = require('axios');
const pool = require('../config/db');

// ─────────────────────────────────────────────────────────
// Función auxiliar: llama a TomTom y extrae puntos + resumen
// ─────────────────────────────────────────────────────────
const callTomTom = async (latOrigen, lngOrigen, latDestino, lngDestino) => {
    const url = `https://api.tomtom.com/routing/1/calculateRoute/${latOrigen},${lngOrigen}:${latDestino},${lngDestino}/json`;

    const response = await axios.get(url, {
        params: {
            key: process.env.TOMTOM_API_KEY,
            travelMode: 'truck',
            vehicleWeight: 30000,   // 30 toneladas en kg
            vehicleHeight: 4.15     // 4.15 metros
        }
    });

    const route = response.data.routes[0];
    const summary = route.summary;
    const points = route.legs[0].points;

    return { points, summary };
};

// ─────────────────────────────────────────────────────────
// POST /api/rutas/generar
// Body: { viaje_id, latOrigen, lngOrigen, latDestino, lngDestino }
//
// Flujo: Llama a TomTom → guarda en DB → retorna puntos + resumen
// ─────────────────────────────────────────────────────────
const generarRuta = async (req, res) => {
    try {
        const { viaje_id, latOrigen, lngOrigen, latDestino, lngDestino } = req.body;

        if (!viaje_id || !latOrigen || !lngOrigen || !latDestino || !lngDestino) {
            return res.status(400).json({
                success: false,
                message: 'Datos incompletos. Se requiere: viaje_id, latOrigen, lngOrigen, latDestino, lngDestino.'
            });
        }

        const { points, summary } = await callTomTom(latOrigen, lngOrigen, latDestino, lngDestino);

        if (!points || points.length === 0) {
            return res.status(404).json({
                success: false,
                message: 'No se encontró una ruta válida para el trayecto solicitado.'
            });
        }

        // Guardar puntos + resumen en la DB
        const payload = JSON.stringify({ points, summary });
        await pool.execute(
            'UPDATE viajes SET coordenadas_tomtom = ? WHERE viaje_id = ?',
            [payload, viaje_id]
        );

        return res.status(200).json({
            success: true,
            message: 'Ruta generada exitosamente.',
            data: points,
            resumen: {
                distanciaMetros: summary.lengthInMeters,
                tiempoSegundos: summary.travelTimeInSeconds,
                traficoSegundos: summary.trafficDelayInSeconds,
                horaSalida: summary.departureTime,
                horaLlegada: summary.arrivalTime
            }
        });

    } catch (error) {
        console.error('Error generarRuta:', error.message);
        if (error.response) {
            return res.status(502).json({
                success: false,
                message: 'Error al consultar TomTom API.',
                debug_error: error.response.data
            });
        }
        return res.status(500).json({
            success: false,
            message: 'Error interno del servidor.',
            debug_error: error.message
        });
    }
};

// ─────────────────────────────────────────────────────────
// GET /api/rutas/:viaje_id
// Devuelve la ruta ya guardada en DB sin llamar a TomTom.
// Si no hay ruta guardada, responde 404 para que Flutter
// muestre el botón "Calcular Ruta Segura".
// ─────────────────────────────────────────────────────────
const obtenerRutaGuardada = async (req, res) => {
    try {
        const viajeId = parseInt(req.params.viaje_id);

        if (!viajeId) {
            return res.status(400).json({ success: false, message: 'viaje_id inválido.' });
        }

        const [rows] = await pool.execute(
            'SELECT coordenadas_tomtom FROM viajes WHERE viaje_id = ? AND operador_usuario_id = ?',
            [viajeId, req.userId]
        );

        if (!rows[0] || !rows[0].coordenadas_tomtom) {
            return res.status(404).json({
                success: false,
                message: 'No hay ruta guardada para este viaje.'
            });
        }

        // coordenadas_tomtom es JSON: { points, summary }
        const saved = rows[0].coordenadas_tomtom;
        // mysql2 ya parsea columnas JSON automáticamente
        const { points, summary } = saved;

        return res.status(200).json({
            success: true,
            message: 'Ruta obtenida de la base de datos.',
            data: points,
            resumen: summary ? {
                distanciaMetros: summary.lengthInMeters,
                tiempoSegundos: summary.travelTimeInSeconds,
                traficoSegundos: summary.trafficDelayInSeconds,
                horaSalida: summary.departureTime,
                horaLlegada: summary.arrivalTime
            } : null
        });

    } catch (error) {
        console.error('Error obtenerRutaGuardada:', error.message);
        return res.status(500).json({
            success: false,
            message: 'Error interno del servidor.',
            debug_error: error.message
        });
    }
};

// ─────────────────────────────────────────────────────────
// POST /api/rutas/recalcular
// Body: { viaje_id, latActual, lngActual, latDestino, lngDestino }
//
// Se llama cuando Flutter detecta que el camión se desvió.
// Usa la posición actual como nuevo origen y recalcula.
// Sobreescribe la ruta guardada en DB.
// ─────────────────────────────────────────────────────────
const recalcularRuta = async (req, res) => {
    try {
        const { viaje_id, latActual, lngActual, latDestino, lngDestino } = req.body;

        if (!viaje_id || !latActual || !lngActual || !latDestino || !lngDestino) {
            return res.status(400).json({
                success: false,
                message: 'Datos incompletos. Se requiere: viaje_id, latActual, lngActual, latDestino, lngDestino.'
            });
        }

        // Recalcular desde la posición actual
        const { points, summary } = await callTomTom(latActual, lngActual, latDestino, lngDestino);

        if (!points || points.length === 0) {
            return res.status(404).json({
                success: false,
                message: 'No se encontró una ruta alternativa.'
            });
        }

        // Sobreescribir la ruta guardada
        const payload = JSON.stringify({ points, summary });
        await pool.execute(
            'UPDATE viajes SET coordenadas_tomtom = ? WHERE viaje_id = ?',
            [payload, viaje_id]
        );

        return res.status(200).json({
            success: true,
            message: 'Ruta recalculada exitosamente.',
            data: points,
            resumen: {
                distanciaMetros: summary.lengthInMeters,
                tiempoSegundos: summary.travelTimeInSeconds,
                traficoSegundos: summary.trafficDelayInSeconds,
                horaSalida: summary.departureTime,
                horaLlegada: summary.arrivalTime
            }
        });

    } catch (error) {
        console.error('Error recalcularRuta:', error.message);
        if (error.response) {
            return res.status(502).json({
                success: false,
                message: 'Error al consultar TomTom API.',
                debug_error: error.response.data
            });
        }
        return res.status(500).json({
            success: false,
            message: 'Error interno del servidor.',
            debug_error: error.message
        });
    }
};

module.exports = { generarRuta, obtenerRutaGuardada, recalcularRuta };
