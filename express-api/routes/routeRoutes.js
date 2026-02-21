// routes/routeRoutes.js
const express = require('express');
const router = express.Router();
const authGuard = require('../middleware/authGuard');
const { generarRuta } = require('../controllers/routeController');

// POST /api/rutas/generar (requiere autenticación)
router.post('/generar', authGuard, generarRuta);

module.exports = router;
