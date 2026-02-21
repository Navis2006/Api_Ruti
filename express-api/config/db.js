// config/db.js - Pool de conexiones MySQL (Clever Cloud)
const mysql = require('mysql2/promise');

const pool = mysql.createPool({
    host: process.env.DB_HOST,
    user: process.env.DB_USER,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_NAME,
    port: parseInt(process.env.DB_PORT) || 3306,
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0,
    // Configuración para mantener conexiones estables con Clever Cloud
    connectTimeout: 10000,
    enableKeepAlive: true,
    keepAliveInitialDelay: 10000
});

// Verificar conexión al iniciar
pool.getConnection()
    .then(conn => {
        console.log('✅ Conexión a MySQL exitosa (Clever Cloud)');
        conn.release();
    })
    .catch(err => {
        console.error('❌ Error conectando a MySQL:', err.message);
    });

module.exports = pool;
