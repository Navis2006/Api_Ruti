// server.js - Punto de entrada del microservicio Express.js (Rutitruck API)
require('dotenv').config();
const { startKeepAlive } = require('./keepAlive');

const express = require('express');
const cors = require('cors');

const app = express();
const PORT = process.env.PORT || 3000;

// ============================================================
// Middlewares Globales
// ============================================================

// CORS - Permitir peticiones desde la app móvil y cualquier origen
app.use(cors({
    origin: '*',
    methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With']
}));

// Parsear JSON del body
app.use(express.json());

// ============================================================
// Registro de Rutas
// ============================================================
const authRoutes = require('./routes/authRoutes');
const tripRoutes = require('./routes/tripRoutes');
const routeRoutes = require('./routes/routeRoutes');
const alertsRoutes = require('./routes/alertsRoutes');

app.use('/api/auth', authRoutes);       // Login
app.use('/api/trips', tripRoutes);      // Viajes (my-trips, details, update-status)
app.use('/api/rutas', routeRoutes);     // Ruteo TomTom (generar)
app.use('/api/alerts', alertsRoutes);   // Alertas (report, my-alerts)

// ============================================================
// Ruta de salud (health check)
// ============================================================
app.get('/', (req, res) => {
    res.json({
        success: true,
        message: 'Rutitruck API - Express.js Microservice',
        version: '1.0.0',
        endpoints: {
            login: 'POST /api/auth/login',
            myTrips: 'GET /api/trips/my-trips',
            tripDetails: 'GET /api/trips/details/:id',
            updateStatus: 'POST /api/trips/update-status',
            generarRuta: 'POST /api/rutas/generar',
            obtenerRuta: 'GET /api/rutas/:viaje_id',
            recalcularRuta: 'POST /api/rutas/recalcular',
            reportAlert: 'POST /api/alerts/report',
            myAlerts: 'GET /api/alerts/my-alerts'
        }
    });
});

// ============================================================
// Manejo de rutas no encontradas
// ============================================================
app.use((req, res) => {
    res.status(404).json({
        success: false,
        message: `Ruta no encontrada: ${req.method} ${req.originalUrl}`
    });
});

// ============================================================
// Manejo global de errores
// ============================================================
app.use((err, req, res, next) => {
    console.error('Error no manejado:', err);
    res.status(500).json({
        success: false,
        message: 'Error interno del servidor.'
    });
});

// ============================================================
// Iniciar servidor
// ============================================================
app.listen(PORT, () => {
    console.log(`🚛 Rutitruck API corriendo en puerto ${PORT}`);
    console.log(`📍 Health check: http://localhost:${PORT}/`);

    // Iniciar el keep-alive para mantener activos los servicios de Render
    startKeepAlive();
});
