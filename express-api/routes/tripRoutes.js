// routes/tripRoutes.js
const express = require('express');
const router = express.Router();
const authGuard = require('../middleware/authGuard');
const { getMyTrips, getTripDetails, updateTripStatus, getTripVehicleDetails } = require('../controllers/tripController');

// Todas las rutas de viajes requieren autenticación
router.use(authGuard);

// GET /api/trips/my-trips
router.get('/my-trips', getMyTrips);

// GET /api/trips/details/:id
router.get('/details/:id', getTripDetails);

// POST /api/trips/update-status
router.post('/update-status', updateTripStatus);

// GET /api/trips/vehicle-details/:id
router.get('/vehicle-details/:id', getTripVehicleDetails);

module.exports = router;
