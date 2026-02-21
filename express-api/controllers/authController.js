// controllers/authController.js - Migración de login_mobile.php
const jwt = require('jsonwebtoken');
const bcrypt = require('bcryptjs');
const pool = require('../config/db');

/**
 * POST /api/auth/login
 * Body: { email, contrasena }
 */
const login = async (req, res) => {
    try {
        const { email, contrasena } = req.body;

        // 1. Validar datos
        if (!email || !contrasena) {
            return res.status(400).json({
                success: false,
                message: 'Datos incompletos. Se requiere email y contrasena.'
            });
        }

        // 2. Buscar usuario por email
        const [rows] = await pool.execute(
            `SELECT usuario_id, nombre, apellidos, contrasena_hash, rol_id, empresa_id, estatus 
       FROM usuarios 
       WHERE email = ? LIMIT 1`,
            [email.trim()]
        );

        const user = rows[0];

        if (!user) {
            return res.status(401).json({
                success: false,
                message: 'Correo o contraseña incorrectos.'
            });
        }

        // 3. Verificar contraseña con bcrypt
        // PHP usa prefijo $2y$ que bcryptjs no reconoce, convertir a $2a$ (son equivalentes)
        let hash = user.contrasena_hash;
        if (hash.startsWith('$2y$')) {
            hash = '$2a$' + hash.substring(4);
        }
        const passwordValid = await bcrypt.compare(contrasena, hash);

        if (!passwordValid) {
            return res.status(401).json({
                success: false,
                message: 'Correo o contraseña incorrectos.'
            });
        }

        // 4. Verificar estatus
        if (user.estatus && user.estatus !== 'activo') {
            return res.status(403).json({
                success: false,
                message: 'Usuario inactivo o suspendido.'
            });
        }

        // 5. Generar Token JWT (compatible con el payload PHP)
        const token = jwt.sign(
            {
                iss: 'rutitruck-api',
                aud: 'rutitruck-api',
                data: {
                    id: user.usuario_id,
                    rol: user.rol_id
                }
            },
            process.env.JWT_SECRET,
            { expiresIn: '1h' }
        );

        // 6. Responder éxito
        return res.status(200).json({
            success: true,
            message: 'Autenticación exitosa.',
            token,
            usuario: {
                id: user.usuario_id,
                nombre: `${user.nombre} ${user.apellidos}`,
                rol_id: parseInt(user.rol_id),
                empresa_id: parseInt(user.empresa_id)
            }
        });

    } catch (error) {
        console.error('Error API Login:', error.message);
        return res.status(500).json({
            success: false,
            message: 'Error interno del servidor.',
            debug_error: error.message
        });
    }
};

module.exports = { login };
