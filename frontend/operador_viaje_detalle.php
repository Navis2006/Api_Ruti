<?php
define('ROL_REQUERIDO', 2);
require_once '../backend/auth_guard.php'; 
require_once '../backend/config/db_connection.php'; 
require_once 'header_operador.php'; // Carga la cabecera del operador

// --- 1. OBTENER LOS DATOS DEL VIAJE ---
$operador_id = $_SESSION['usuario_id'];
$viaje_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$viaje = null;
$alertas = [];

if (!$viaje_id) {
    die("ID de viaje no válido.");
}

try {
    // Consulta principal: Obtiene el viaje, la ruta, el vehículo y el TRAZADO DEL MAPA
    // Es vital que el operador_usuario_id coincida con la sesión por seguridad
    $stmt = $pdo->prepare("
        SELECT 
            v.viaje_id, v.estado, v.fecha_inicio, 
            r.ruta_id, r.nombre as ruta_nombre, r.descripcion as ruta_descripcion,
            ST_AsText(r.trazado_geom) as trazado_wkt, -- El trazado para el mapa
            ve.nombre as vehiculo_nombre, ve.placa as vehiculo_placa
        FROM viajes v
        JOIN rutas r ON v.ruta_id = r.ruta_id
        JOIN vehiculos ve ON v.vehiculo_id = ve.vehiculo_id
        WHERE v.operador_usuario_id = ? AND v.viaje_id = ?
        LIMIT 1
    ");
    $stmt->execute([$operador_id, $viaje_id]);
    $viaje = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$viaje) {
        die("Viaje no encontrado o no asignado a este operador.");
    }

    // Consulta secundaria: Obtener alertas ABIERTAS para esta RUTA
    // (Asume que tu amigo ya añadió 'estatus_alerta' y 'nivel')
    $stmt_alertas = $pdo->prepare("
        SELECT * FROM alertas 
        WHERE ruta_id = ? AND estatus_alerta = 'Abierta'
        ORDER BY nivel DESC
    ");
    $stmt_alertas->execute([$viaje['ruta_id']]);
    $alertas = $stmt_alertas->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al obtener los datos del viaje: " . $e->getMessage());
}

// Lista de tipos de alerta para el modal de reporte
$tipos_de_alerta = ['Tráfico', 'Accidente', 'Peligro en Vía', 'Mecánica', 'Desvío', 'Otro'];
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<style>
    /* Estilos para las tarjetas de alerta (copiadas de menu_trailero.php) */
    .card { background-color: #ffffff; padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); }
    .card-nivel-5 { border-left: 4px solid #EF4444; } /* Rojo */
    .card-nivel-4 { border-left: 4px solid #F59E0B; } /* Amarillo */
    .card-nivel-3, .card-nivel-2, .card-nivel-1 { border-left: 4px solid #3B82F6; } /* Azul */
    .badge { display: inline-block; padding: 0.25rem 0.75rem; font-size: 0.875rem; font-weight: 600; border-radius: 9999px; }
    .badge-red { background-color: #FEE2E2; color: #991B1B; }
    .badge-yellow { background-color: #FEF3C7; color: #92400E; }
    .badge-blue { background-color: #DBEAFE; color: #1E40AF; }
</style>
<div x-data="{ 
    modalOpen: false, 
    estatusViaje: '<?= htmlspecialchars($viaje['estado']) ?>' 
}">

    <header class="mb-6">
        <h1 class="text-3xl font-bold"><?= htmlspecialchars($viaje['ruta_nombre']) ?></h1>
        <p class="text-gray-500">Vehículo: <?= htmlspecialchars($viaje['vehiculo_nombre']) ?> (<?= htmlspecialchars($viaje['vehiculo_placa']) ?>)</p>
    </header>

    <div class="mb-6">
        <button 
            x-show="estatusViaje === 'Planeado' || estatusViaje === 'Asignado'"
            @click="handleViajeAction('iniciar_viaje', 'En Curso')"
            class="w-full text-center px-6 py-4 bg-green-600 text-white text-xl font-bold rounded-lg shadow-lg hover:bg-green-700 transition-colors">
            INICIAR VIAJE
        </button>

        <button 
            x-show="estatusViaje === 'En Curso'"
            @click="handleViajeAction('finalizar_viaje', 'Finalizado')"
            class="w-full text-center px-6 py-4 bg-red-600 text-white text-xl font-bold rounded-lg shadow-lg hover:bg-red-700 transition-colors">
            FINALIZAR VIAJE
        </button>

        <div x-show="estatusViaje === 'Finalizado' || estatusViaje === 'Cancelado'">
            <p class="text-center text-lg font-medium text-gray-600 bg-gray-200 p-4 rounded-lg">Este viaje ya ha concluido.</p>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div id="map" class="w-full h-80 rounded-lg z-10"></div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div>
            <h2 class="text-2xl font-bold mb-4">Alertas Activas en esta Ruta</h2>
            <div class="space-y-4">
                <?php if (empty($alertas)): ?>
                    <div class="card card-empty">
                        <p>No hay alertas abiertas para esta ruta. ¡Buen viaje!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($alertas as $alerta): ?>
                        <?php
                        $nivel = $alerta['nivel'] ?? 3;
                        $card_class = 'card-nivel-3'; $badge_class = 'badge-blue';
                        if ($nivel == 5) { $card_class = 'card-nivel-5'; $badge_class = 'badge-red'; }
                        if ($nivel == 4) { $card_class = 'card-nivel-4'; $badge_class = 'badge-yellow'; }
                        ?>
                        <div class="card <?= $card_class ?>">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="text-lg font-bold"><?= htmlspecialchars($alerta['tipo_alerta'] ?? 'General') ?></h3>
                                <span class="badge <?= $badge_class ?>">Nivel <?= htmlspecialchars($nivel) ?></span>
                            </div>
                            <p class="text-gray-700"><?= htmlspecialchars($alerta['descripcion'] ?? 'N/A') ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <h2 class="text-2xl font-bold mb-4">¿Ves algo nuevo?</h2>
            <div class="card text-center">
                <p class="text-gray-600 mb-4">Reporta tráfico, accidentes o problemas mecánicos para ayudar a los demás.</p>
                <button 
                    @click="modalOpen = true"
                    class="w-full px-6 py-3 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition-colors">
                    Reportar Incidencia
                </button>
            </div>
        </div>
    </div>


    <div 
        x-show="modalOpen" 
        x-cloak 
        class="fixed inset-0 z-40 overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true"
    >
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div @click="modalOpen = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div 
                x-show="modalOpen"
                x-transition
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
            >
                <form method="POST" action="../backend/operador_reportar_alerta.php">
                    <input type="hidden" name="ruta_id" value="<?= htmlspecialchars($viaje['ruta_id']) ?>">
                    <input type="hidden" name="viaje_id" value="<?= htmlspecialchars($viaje['viaje_id']) ?>">
                    <input type="hidden" name="creado_por_usuario_id" value="<?= htmlspecialchars($operador_id) ?>">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Reportar Nueva Alerta en Ruta
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="tipo_alerta" class="block text-sm font-medium text-gray-700">Tipo de Alerta</label>
                                        <select id="tipo_alerta" name="tipo_alerta" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                                            <option value="">-- Seleccione un tipo --</option>
                                            <?php foreach ($tipos_de_alerta as $tipo): ?>
                                                <option value="<?= $tipo ?>"><?= $tipo ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción (¿Qué pasó?)</label>
                                        <textarea id="descripcion" name="descripcion" rows="3" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500" placeholder="Ej: Accidente en el carril derecho..."></textarea>
                                    </div>
                                    <div>
                                        <label for="nivel" class="block text-sm font-medium text-gray-700">Nivel de Prioridad</label>
                                        <select id="nivel" name="nivel" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                                            <option value="5">5 (Urgente)</option>
                                            <option value="4">4 (Alto)</option>
                                            <option value="3" selected>3 (Medio)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Enviar Reporte
                        </button>
                        <button type="button" @click="modalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div> 

<script>
    // --- 1. FUNCIÓN PARA LOS BOTONES DE INICIAR/FINALIZAR ---
    function handleViajeAction(action, newStatus) {
        if (!confirm(`¿Estás seguro de que quieres ${action.replace('_', ' ')}?`)) {
            return;
        }

        const formData = new FormData();
        formData.append('action', action);
        formData.append('viaje_id', <?= $viaje_id ?>);

        fetch('../backend/operador_gestionar_viaje.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Éxito: recarga la página para mostrar el nuevo estado
                window.location.reload();
            } else {
                alert('Error: ' (data.message || 'No se pudo actualizar el viaje.'));
            }
        })
        .catch(error => {
            console.error('Error de Fetch:', error);
            alert('Error de conexión. Inténtalo de nuevo.');
        });
    }

    // --- 2. INICIALIZACIÓN DEL MAPA LEAFLET ---
    document.addEventListener('DOMContentLoaded', () => {
        // Coordenadas de la ruta (trazado_wkt)
        const wkt = <?= json_encode($viaje['trazado_wkt'] ?? null) ?>;
        
        // Coordenadas de Mérida (por si la ruta no tiene trazado)
        let mapCenter = [20.9674, -89.6243];
        let mapZoom = 10;
        let polylineCoords = [];

        if (wkt) {
            try {
                // Función "padrote" para convertir "LINESTRING(-89 20, -88 21)"
                // en [[20, -89], [21, -88]] que Leaflet entiende.
                polylineCoords = wkt
                    .replace('LINESTRING(', '')
                    .replace(')', '')
                    .split(',')
                    .map(pair => {
                        const coords = pair.trim().split(' ').map(Number.parseFloat);
                        return [coords[1], coords[0]]; // ¡Leaflet usa [lat, lon]!
                    });
                
                if (polylineCoords.length > 0) {
                    mapCenter = polylineCoords[0]; // Centra el mapa en el inicio de la ruta
                    mapZoom = 12;
                }
            } catch (e) {
                console.error("Error al parsear el WKT de la ruta:", e);
            }
        }

        // Inicializa el mapa
        const map = L.map('map').setView(mapCenter, mapZoom);

        // Añade la capa de OpenStreetMap (gratis)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Si tenemos una ruta, la dibujamos
        if (polylineCoords.length > 0) {
            const polyline = L.polyline(polylineCoords, { color: 'blue' }).addTo(map);
            // Ajusta el zoom del mapa para que se vea la ruta completa
            map.fitBounds(polyline.getBounds());
        }

        // Obtener la ubicación actual del operador
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(function(position) {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                const userLocation = [lat, lon];
                
                // Pone un marcador azul en la ubicación del operador
                L.marker(userLocation, {
                    icon: L.icon({
                        iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    })
                }).addTo(map)
                  .bindPopup('Tu ubicación actual.');

            }, function() {
                // Error al obtener ubicación (ej. permiso denegado)
                console.warn('Error: No se pudo obtener la geolocalización.');
            });
        }
    });
</script>


<?php
require_once 'footer_operador.php'; // Cierra la página
?>
