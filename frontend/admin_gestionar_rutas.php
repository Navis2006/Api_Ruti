<?php
require_once '../backend/config/db_connection.php';
require_once 'header_admin.php';

try {
    $empresas = $pdo->query("SELECT empresa_id, nombre FROM empresas ORDER BY nombre")->fetchAll();
    $usuarios = $pdo->query("SELECT usuario_id, nombre, apellidos FROM usuarios ORDER BY nombre")->fetchAll();

    $rutas = $pdo->query("
        SELECT 
            r.ruta_id, r.empresa_id, r.nombre, r.descripcion, r.creado_por_usuario_id,
            ST_AsText(r.trazado_geom) as trazado_geom,
            r.lat_origen, r.lng_origen, r.lat_destino, r.lng_destino,
            e.nombre as empresa_nombre, 
            CONCAT(u.nombre, ' ', u.apellidos) as creador_nombre
        FROM rutas r
        JOIN empresas e ON r.empresa_id = e.empresa_id
        JOIN usuarios u ON r.creado_por_usuario_id = u.usuario_id
        ORDER BY r.ruta_id DESC
    ")->fetchAll();
} catch (PDOException $e) {
    die("Error al obtener datos: " . $e->getMessage());
}
?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css" />
<script src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.dataTables.min.css" />

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    #mapModal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 9999;
        background: rgba(0, 0, 0, 0.85);
    }

    #mapModal.active {
        display: flex;
        flex-direction: column;
    }

    #mapModal .map-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 20px;
        background: #1e293b;
        color: white;
    }

    #mapModal .map-header h3 {
        margin: 0;
        font-size: 1.1rem;
    }

    #mapContainer {
        flex: 1;
        width: 100%;
    }

    /* Diálogo de confirmación sobre el mapa */
    #confirmDialog {
        display: none;
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10000;
        background: white;
        border-radius: 12px;
        padding: 16px 24px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        text-align: center;
        min-width: 280px;
    }

    #confirmDialog.active {
        display: block;
    }

    #confirmDialog p {
        margin: 0 0 4px;
        font-weight: 600;
        font-size: 0.95rem;
    }

    #confirmDialog .coords {
        color: #6b7280;
        font-size: 0.8rem;
        margin-bottom: 12px;
    }

    #confirmDialog .btns {
        display: flex;
        gap: 10px;
        justify-content: center;
    }

    #confirmDialog .btns button {
        padding: 8px 20px;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        font-size: 0.9rem;
    }

    #confirmDialog .btn-yes {
        background: #2563eb;
        color: white;
    }

    #confirmDialog .btn-yes:hover {
        background: #1d4ed8;
    }

    #confirmDialog .btn-no {
        background: #e5e7eb;
        color: #374151;
    }

    #confirmDialog .btn-no:hover {
        background: #d1d5db;
    }
</style>

<div x-data="{ formVisible: false }" @open-form.window="formVisible = true">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <h1 class="text-3xl font-bold mb-4 md:mb-0">Administración de Rutas</h1>

        <button @click="formVisible = true; setCreateMode();"
            class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                </path>
            </svg>
            Crear Nueva Ruta
        </button>
    </div>

    <div x-show="formVisible" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-4" class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 id="form-title" class="text-2xl font-bold mb-4">Crear Nueva Ruta</h2>
        <form id="rutaForm" method="POST" action="../backend/admin_gestionar_rutas_process.php" class="space-y-4">
            <input type="hidden" id="ruta_id" name="ruta_id">
            <input type="hidden" id="action" name="action" value="create">

            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre de la Ruta</label>
                <input type="text" id="nombre" name="nombre" required
                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="empresa_id" class="block text-sm font-medium text-gray-700">Empresa</label>
                    <select id="empresa_id" name="empresa_id" required
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Seleccione --</option>
                        <?php foreach ($empresas as $empresa): ?>
                            <option value="<?= htmlspecialchars($empresa['empresa_id'] ?? '') ?>">
                                <?= htmlspecialchars($empresa['nombre'] ?? '') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="creado_por_usuario_id" class="block text-sm font-medium text-gray-700">Creado por
                        (Automático)</label>
                    <select id="creado_por_usuario_id" name="creado_por_usuario_id" disabled
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Asignado por Admin Logueado --</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="3"
                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>

            <!-- ═══════════ COORDENADAS CON SELECTOR DE MAPA ═══════════ -->
            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">📍 Coordenadas de la Ruta</h3>

                <!-- ORIGEN -->
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-green-700">🟢 Origen</span>
                        <button type="button" onclick="openMapSelector('origen')"
                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Buscar en el Mapa
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-500">Latitud</label>
                            <input type="text" id="lat_origen" name="lat_origen" placeholder="Ej: 20.9674"
                                class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500">Longitud</label>
                            <input type="text" id="lng_origen" name="lng_origen" placeholder="Ej: -89.6237"
                                class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                    </div>
                </div>

                <!-- DESTINO -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-blue-700">🔵 Destino</span>
                        <button type="button" onclick="openMapSelector('destino')"
                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Buscar en el Mapa
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-500">Latitud</label>
                            <input type="text" id="lat_destino" name="lat_destino" placeholder="Ej: 21.2833"
                                class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500">Longitud</label>
                            <input type="text" id="lng_destino" name="lng_destino" placeholder="Ej: -89.6667"
                                class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <button type="button" @click="formVisible = false; setCreateMode();"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Cancelar</button>
                <button type="submit" id="submitButton"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Crear Ruta</button>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-4">Listado de Rutas</h2>

            <table id="rutasTable" class="w-full text-left dt-responsive" style="width:100%">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="p-4">ID</th>
                        <th class="p-4">Nombre</th>
                        <th class="p-4">Empresa</th>
                        <th class="p-4">Creado por</th>
                        <th class="p-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($rutas as $ruta): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="p-4"><?= htmlspecialchars($ruta['ruta_id'] ?? '') ?></td>
                            <td class="p-4"><?= htmlspecialchars($ruta['nombre'] ?? '') ?></td>
                            <td class="p-4"><?= htmlspecialchars($ruta['empresa_nombre'] ?? '') ?></td>
                            <td class="p-4"><?= htmlspecialchars($ruta['creador_nombre'] ?? '') ?></td>
                            <td class="p-4 text-right space-x-2 whitespace-nowrap">
                                <button class="edit-btn text-blue-600 hover:underline"
                                    data-ruta='<?= htmlspecialchars(json_encode($ruta), ENT_QUOTES, 'UTF-8') ?>'>
                                    Editar
                                </button>
                                <form method="POST" action="../backend/admin_gestionar_rutas_process.php"
                                    class="inline-block">
                                    <input type="hidden" name="ruta_id"
                                        value="<?= htmlspecialchars($ruta['ruta_id'] ?? '') ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="text-red-600 hover:underline"
                                        onclick="return confirm('¿Estás seguro de que quieres eliminar esta ruta? Esto podría afectar a los viajes que dependen de ella.');">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ═══════════ MODAL DEL MAPA (FULLSCREEN) ═══════════ -->
<div id="mapModal">
    <div class="map-header">
        <h3 id="mapTitle">Seleccionar ubicación de Origen</h3>
        <button onclick="closeMapSelector()"
            style="background:none;border:none;color:white;font-size:1.5rem;cursor:pointer;">✕</button>
    </div>
    <div id="mapContainer"></div>
    <div id="confirmDialog">
        <p>¿Estás seguro de esta ubicación?</p>
        <div class="coords" id="selectedCoords"></div>
        <div class="btns">
            <button class="btn-no" onclick="cancelSelection()">No, cambiar</button>
            <button class="btn-yes" onclick="confirmSelection()">Sí, confirmar</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#rutasTable').DataTable({
            "pageLength": 10,
            "responsive": true,
            "language": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            }
        });
    });
</script>

<script>
    // ═══════════ LÓGICA DEL MAPA INTERACTIVO ═══════════
    let map = null;
    let marker = null;
    let currentTarget = null; // 'origen' o 'destino'
    let selectedLat = null;
    let selectedLng = null;

    function openMapSelector(target) {
        currentTarget = target;
        const modal = document.getElementById('mapModal');
        const title = document.getElementById('mapTitle');
        title.textContent = target === 'origen'
            ? '📍 Seleccionar ubicación de Origen — haz click en el mapa'
            : '📍 Seleccionar ubicación de Destino — haz click en el mapa';

        modal.classList.add('active');

        // Inicializar mapa (solo la primera vez)
        setTimeout(() => {
            if (!map) {
                map = L.map('mapContainer').setView([20.9674, -89.6237], 10); // Centro en Mérida
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(map);

                // Click en el mapa
                map.on('click', function (e) {
                    selectedLat = e.latlng.lat.toFixed(7);
                    selectedLng = e.latlng.lng.toFixed(7);

                    // Mover/crear marcador
                    if (marker) {
                        marker.setLatLng(e.latlng);
                    } else {
                        marker = L.marker(e.latlng).addTo(map);
                    }

                    // Mostrar diálogo de confirmación
                    document.getElementById('selectedCoords').textContent =
                        `Lat: ${selectedLat}, Lng: ${selectedLng}`;
                    document.getElementById('confirmDialog').classList.add('active');
                });
            } else {
                map.invalidateSize();
            }

            // Limpiar marcador anterior
            if (marker) {
                map.removeLayer(marker);
                marker = null;
            }
            document.getElementById('confirmDialog').classList.remove('active');
        }, 100);
    }

    function closeMapSelector() {
        document.getElementById('mapModal').classList.remove('active');
        document.getElementById('confirmDialog').classList.remove('active');
    }

    function cancelSelection() {
        document.getElementById('confirmDialog').classList.remove('active');
        if (marker) {
            map.removeLayer(marker);
            marker = null;
        }
    }

    function confirmSelection() {
        if (currentTarget === 'origen') {
            document.getElementById('lat_origen').value = selectedLat;
            document.getElementById('lng_origen').value = selectedLng;
        } else {
            document.getElementById('lat_destino').value = selectedLat;
            document.getElementById('lng_destino').value = selectedLng;
        }
        closeMapSelector();
    }

    // ═══════════ FORM EDIT/CREATE ═══════════
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('rutaForm');
        const formTitle = document.getElementById('form-title');
        const actionInput = document.getElementById('action');
        const submitButton = document.getElementById('submitButton');

        const setEditMode = (ruta) => {
            window.dispatchEvent(new CustomEvent('open-form'));

            formTitle.textContent = `Editando Ruta #${ruta.ruta_id}`;
            actionInput.value = 'update';
            document.getElementById('ruta_id').value = ruta.ruta_id;
            document.getElementById('nombre').value = ruta.nombre;
            document.getElementById('empresa_id').value = ruta.empresa_id;
            document.getElementById('creado_por_usuario_id').value = ruta.creado_por_usuario_id;
            document.getElementById('descripcion').value = ruta.descripcion;
            document.getElementById('lat_origen').value = ruta.lat_origen || '';
            document.getElementById('lng_origen').value = ruta.lng_origen || '';
            document.getElementById('lat_destino').value = ruta.lat_destino || '';
            document.getElementById('lng_destino').value = ruta.lng_destino || '';
            submitButton.textContent = 'Actualizar Ruta';

            setTimeout(() => {
                form.scrollIntoView({ behavior: 'smooth' });
            }, 100);
        };

        window.setCreateMode = () => {
            formTitle.textContent = 'Crear Nueva Ruta';
            form.reset();
            actionInput.value = 'create';
            document.getElementById('creado_por_usuario_id').value = "";
            submitButton.textContent = 'Crear Ruta';
        };

        document.getElementById('rutasTable').addEventListener('click', function (e) {
            if (e.target.classList.contains('edit-btn')) {
                const rutaData = JSON.parse(e.target.dataset.ruta);
                setEditMode(rutaData);
            }
        });
    });
</script>

<?php
require_once 'footer.php';
?>