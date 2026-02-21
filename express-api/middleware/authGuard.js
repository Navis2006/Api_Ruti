// middleware/authGuard.js - Equivalente a auth_mobile_guard.php
const jwt = require('jsonwebtoken');

const authGuard = (req, res, next) => {
    try {
        // 1. Obtener el header Authorization
        const authHeader = req.headers['authorization'] || '';

        // 2. Extraer Token "Bearer eyJ..."
        const match = authHeader.match(/Bearer\s(\S+)/);
        if (!match) {
            return res.status(401).json({
                success: false,
                message: 'Acceso denegado: Token no proporcionado.'
            });
        }

        const token = match[1];

        // 3. Verificar y decodificar el token
        const decoded = jwt.verify(token, process.env.JWT_SECRET);

        // 4. Inyectar datos del usuario en el request
        // Compatible con el payload del PHP: { data: { id, rol } }
        req.userId = decoded.data?.id || decoded.id;
        req.userRol = decoded.data?.rol || decoded.rol;

        next();
    } catch (error) {
        return res.status(401).json({
            success: false,
            message: 'Acceso denegado: Token inválido o expirado.'
        });
    }
};

module.exports = authGuard;
