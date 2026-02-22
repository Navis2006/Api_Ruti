// keepAlive.js - Cron job para mantener los servicios de Render activos
// Hace peticiones cada 10 minutos a la API Express y a la web PHP

const cron = require('node-cron');
const axios = require('axios');

// ============================================================
// URLs de los servicios en Render
// ============================================================
const SERVICES = [
    {
        name: 'Web PHP (api-ruti)',
        url: 'https://api-ruti.onrender.com'
    },
    {
        name: 'Express API (rutitruck)',
        url: 'https://rutitruck-express-api.onrender.com'
    }
];

// ============================================================
// Función que hace el ping a un servicio
// ============================================================
async function pingService(service) {
    const timestamp = new Date().toLocaleTimeString('es-MX', { hour12: false });
    try {
        const response = await axios.get(service.url, { timeout: 10000 });
        console.log(`[${timestamp}] ✅ Keep-Alive OK → ${service.name} (HTTP ${response.status})`);
    } catch (error) {
        // Si responde aunque sea con error HTTP (404, 500), el servidor sigue despierto
        if (error.response) {
            console.log(`[${timestamp}] ⚠️  Keep-Alive OK (${error.response.status}) → ${service.name}`);
        } else {
            console.error(`[${timestamp}] ❌ Keep-Alive FALLÓ → ${service.name}: ${error.message}`);
        }
    }
}

// ============================================================
// Función principal: ping a todos los servicios
// ============================================================
async function pingAllServices() {
    console.log(`\n🔔 [Keep-Alive] Ejecutando ping a ${SERVICES.length} servicios en Render...`);
    await Promise.allSettled(SERVICES.map(pingService));
}

// ============================================================
// Iniciar el cron job - Cada 10 minutos
// Formato: '*/10 * * * *'
// ============================================================
function startKeepAlive() {
    console.log('⏰ [Keep-Alive] Cron iniciado → ping cada 10 minutos a servicios de Render');
    console.log('   📌 Servicios monitoreados:');
    SERVICES.forEach(s => console.log(`      - ${s.name}: ${s.url}`));
    console.log('');

    // Ejecutar inmediatamente al iniciar (para despertar servicios si es necesario)
    pingAllServices();

    // Programar cada 10 minutos
    cron.schedule('*/10 * * * *', () => {
        pingAllServices();
    });
}

module.exports = { startKeepAlive };
