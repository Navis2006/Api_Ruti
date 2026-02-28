const pool = require('./config/db');

async function testQuery() {
    const query = `
        SELECT 
            a.alerta_id,
            a.ruta_id,
            ST_X(a.ubicacion_geom) AS longitud,
            ST_Y(a.ubicacion_geom) AS latitud
        FROM alertas a
        INNER JOIN (
            SELECT vd.ruta_id 
            FROM viajes v
            INNER JOIN viaje_destinos vd ON v.viaje_id = vd.viaje_id
            WHERE v.operador_usuario_id = 999 
              AND v.estado IN ('En Curso', 'Asignado', 'Planeado')
              AND vd.ruta_id IS NOT NULL
            ORDER BY v.fecha_inicio ASC
            LIMIT 10
        ) AS allowed_rutas ON a.ruta_id = allowed_rutas.ruta_id
        WHERE a.estatus_alerta = 'Abierta'
    `;
    try {
        await pool.execute(query);
        console.log("Success");
    } catch (e) {
        console.error("Error:", e.message);
    }
    process.exit(0);
}
testQuery();
