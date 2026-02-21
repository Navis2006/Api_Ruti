// controllers/routeController.js - Endpoint de Ruteo Inteligente (TomTom API)
const axios = require('axios');
const pool = require('../config/db');

/**
 * POST /api/rutas/generar
 * Body: { viaje_id, latOrigen, lngOrigen, latDestino, lngDestino }
 * 
 * Flujo:
 * 1. Recibir coordenadas origen/destino
 * 2. Llamar a TomTom Routing API (modo camión)
 * 3. Extraer arreglo de puntos de la ruta
 * 4. Guardar coordenadas en la tabla viajes
 * 5. Retornar puntos al cliente (Flutter)
 */
const generarRuta = async (req, res) => {
    try {
        const { viaje_id, latOrigen, lngOrigen, latDestino, lngDestino } = req.body;

        // 1. Validar payload
        if (!viaje_id || !latOrigen || !lngOrigen || !latDestino || !lngDestino) {
            return res.status(400).json({
                success: false,
                message: 'Datos incompletos. Se requiere: viaje_id, latOrigen, lngOrigen, latDestino, lngDestino.'
            });
        }

        // 2. Construir URL de TomTom Routing API
        const tomtomUrl = `https://api.tomtom.com/routing/1/calculateRoute/${latOrigen},${lngOrigen}:${latDestino},${lngDestino}/json`;

        // 3. Petición a TomTom con parámetros para vehículo pesado
        const tomtomResponse = await axios.get(tomtomUrl, {
            params: {
                key: process.env.TOMTOM_API_KEY,
                travelMode: 'truck',
                vehicleWeight: 30000,   // 30 toneladas en kg
                vehicleHeight: 4.15     // 4.15 metros
            }
        });

        // 4. Extraer arreglo de coordenadas
        const points = tomtomResponse.data.routes[0].legs[0].points;

        if (!points || points.length === 0) {
            return res.status(404).json({
                success: false,
                message: 'No se encontró una ruta válida para el trayecto solicitado.'
            });
        }

        // 5. Persistir en MySQL - guardar en columna coordenadas_tomtom
        const coordenadasJson = JSON.stringify(points);
        await pool.execute(
            'UPDATE viajes SET coordenadas_tomtom = ? WHERE viaje_id = ?',
            [coordenadasJson, viaje_id]
        );

        // 6. Responder al cliente (Flutter)
        return res.status(200).json({
            success: true,
            message: 'Ruta generada exitosamente.',
            data: points
        });

    } catch (error) {
        console.error('Error generarRuta:', error.message);

        // Manejar errores específicos de TomTom
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

module.exports = { generarRuta };
