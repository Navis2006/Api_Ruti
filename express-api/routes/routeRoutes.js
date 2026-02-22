// routes/routeRoutes.js
const express = require('express');
const router = express.Router();
const authGuard = require('../middleware/authGuard');
const { generarRuta, obtenerRutaGuardada, recalcularRuta } = require('../controllers/routeController');

// Todas las rutas requieren autenticación
router.use(authGuard);

// POST /api/rutas/generar        → Calcular ruta nueva con TomTom y guardarla
router.post('/generar', generarRuta);

// GET  /api/rutas/:viaje_id      → Obtener ruta ya guardada (sin llamar a TomTom)
router.get('/:viaje_id', obtenerRutaGuardada);

// POST /api/rutas/recalcular     → Recalcular desde posición actual (desvío detectado)
router.post('/recalcular', recalcularRuta);

module.exports = router;
