// controllers/routeController.js - Ruteo Inteligente (TomTom API)
const axios = require('axios');
const pool = require('../config/db');

// ─────────────────────────────────────────────────────────
// Función auxiliar: obtener dimensiones del vehículo asignado al viaje
// ─────────────────────────────────────────────────────────
const getVehicleDimensions = async (viajeId) => {
    const [rows] = await pool.execute(
        `SELECT v.altura_metros, v.ancho_metros, v.largo_metros, 
                v.peso_toneladas, v.peso_eje_kg, v.velocidad_max_kmh
         FROM viajes vi
         JOIN vehiculos v ON vi.vehiculo_id = v.vehiculo_id
         WHERE vi.viaje_id = ?`,
        [viajeId]
    );

    if (rows.length === 0) return null;

    const v = rows[0];
    return {
        vehicleWeight: v.peso_toneladas ? Math.round(v.peso_toneladas * 1000) : 30000,
        vehicleHeight: v.altura_metros || 4.15,
        vehicleWidth: v.ancho_metros || 2.55,
        vehicleLength: v.largo_metros || 16.5,
        vehicleAxleWeight: v.peso_eje_kg || undefined,
        vehicleMaxSpeed: v.velocidad_max_kmh || undefined
    };
};

// ─────────────────────────────────────────────────────────
// Función auxiliar: llama a TomTom con dimensiones del vehículo
// waypointsString = "lat1,lng1:lat2,lng2:lat3,lng3"
// ─────────────────────────────────────────────────────────
const callTomTom = async (waypointsString, vehicleDims = {}) => {
    const url = `https://api.tomtom.com/routing/1/calculateRoute/${waypointsString}/json`;

    // Construir parámetros dinámicamente (solo envía los que tienen valor)
    const params = {
        key: process.env.TOMTOM_API_KEY,
        travelMode: 'truck',
        departAt: 'now',
        vehicleWeight: vehicleDims.vehicleWeight || 30000,
        vehicleHeight: vehicleDims.vehicleHeight || 4.15,
        vehicleWidth: vehicleDims.vehicleWidth || 2.55,
        vehicleLength: vehicleDims.vehicleLength || 16.5,
        vehicleCommercial: true
    };

    // Solo agregar opcionales si tienen valor
    if (vehicleDims.vehicleAxleWeight) params.vehicleAxleWeight = vehicleDims.vehicleAxleWeight;
    if (vehicleDims.vehicleMaxSpeed) params.vehicleMaxSpeed = vehicleDims.vehicleMaxSpeed;

    const response = await axios.get(url, { params });

    const route = response.data.routes[0];
    const summary = route.summary;
    const points = route.legs[0].points;

    return { points, summary };
};

// ─────────────────────────────────────────────────────────
// POST /api/rutas/generar
// Body: { viaje_id, latActual, lngActual, latDestino, lngDestino, latRegreso, lngRegreso }
//
// Flujo: 
// 1. Ruta de 3 puntos: GPS Actual -> Destino -> Regreso (Origen)
// 2. Busca dimensiones del vehículo → Llama a TomTom → guarda en DB → retorna
// ─────────────────────────────────────────────────────────
const generarRuta = async (req, res) => {
    try {
        const { viaje_id, latActual, lngActual, latDestino, lngDestino, latRegreso, lngRegreso } = req.body;

        if (!viaje_id || !latActual || !lngActual || !latDestino || !lngDestino) {
            return res.status(400).json({
                success: false,
                message: 'Datos incompletos. Se requiere: viaje_id, latActual, lngActual, latDestino, lngDestino.'
            });
        }

        // Obtener dimensiones reales del vehículo asignado al viaje
        const vehicleDims = await getVehicleDimensions(viaje_id);

        // Construir string de puntos: GPS -> Destino -> Regreso (Opcional)
        let waypoints = `${latActual},${lngActual}:${latDestino},${lngDestino}`;
        if (latRegreso && lngRegreso) {
            waypoints += `:${latRegreso},${lngRegreso}`;
        }

        const { points, summary } = await callTomTom(waypoints, vehicleDims || {});

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
// genere la ruta automáticamente.
// ─────────────────────────────────────────────────────────
const obtenerRutaGuardada = async (req, res) => {
    try {
        const viajeId = parseInt(req.params.viaje_id);

        if (!viajeId) {
            return res.status(400).json({
                success: false,
                message: 'viaje_id no válido.'
            });
        }

        const operadorId = req.user.id;
        const [rows] = await pool.execute(
            'SELECT coordenadas_tomtom FROM viajes WHERE viaje_id = ? AND operador_usuario_id = ?',
            [viajeId, operadorId]
        );

        if (rows.length === 0) {
            return res.status(404).json({
                success: false,
                message: 'Viaje no encontrado o no te pertenece.'
            });
        }

        const raw = rows[0].coordenadas_tomtom;
        if (!raw) {
            return res.status(404).json({
                success: false,
                message: 'Este viaje aún no tiene ruta calculada.'
            });
        }

        const saved = JSON.parse(raw);
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
// Body: { viaje_id, latActual, lngActual, latDestino, lngDestino, latRegreso, lngRegreso }
//
// Se llama cuando Flutter detecta que el camión se desvió.
// Usa la posición actual como nuevo origen -> Destino -> Regreso.
// Sobreescribe la ruta guardada en DB.
// ─────────────────────────────────────────────────────────
const recalcularRuta = async (req, res) => {
    try {
        const { viaje_id, latActual, lngActual, latDestino, lngDestino, latRegreso, lngRegreso } = req.body;

        if (!viaje_id || !latActual || !lngActual || !latDestino || !lngDestino) {
            return res.status(400).json({
                success: false,
                message: 'Datos incompletos. Se requiere: viaje_id, latActual, lngActual, latDestino, lngDestino.'
            });
        }

        // Obtener dimensiones reales del vehículo
        const vehicleDims = await getVehicleDimensions(viaje_id);

        let waypoints = `${latActual},${lngActual}:${latDestino},${lngDestino}`;
        if (latRegreso && lngRegreso) {
            waypoints += `:${latRegreso},${lngRegreso}`;
        }

        const { points, summary } = await callTomTom(waypoints, vehicleDims || {});

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
