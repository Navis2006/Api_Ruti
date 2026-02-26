const express = require('express');
const router = express.Router();

const { reportAlert, getMyAlerts } = require('../controllers/alertsController');
const authGuard = require('../middleware/authGuard');

// Middleware para proteger todas las rutas de este router
router.use(authGuard);

// POST /api/alerts/report
router.post('/report', reportAlert);

// GET /api/alerts/my-alerts
router.get('/my-alerts', getMyAlerts);

module.exports = router;
