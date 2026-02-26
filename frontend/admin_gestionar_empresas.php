<?php
// La conexión a la BD SÍ la puedes necesitar aquí si esta página hace consultas
require_once '../backend/config/db_connection.php';

// header_admin.php ya se encarga del ROL y auth_guard
require_once 'header_admin.php'; // Carga la cabecera

$empresas = $pdo->query("SELECT * FROM empresas ORDER BY nombre")->fetchAll();
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
        background: #dc2626;
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
        <h1 class="text-3xl font-bold mb-4 md:mb-0">Administración de Empresas</h1>

        <button @click="formVisible = true; setCreateMode();"
            class="flex items-center px-4 py-2 bg-red-600 text-white rounded-lg shadow-md hover:bg-red-700 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                </path>
            </svg>
            Crear Nueva Empresa
        </button>
    </div>

    <div x-show="formVisible" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-4"
        class="bg-white p-6 rounded-lg shadow-sm mb-8 max-w-lg mx-auto">
        <h2 id="form-title" class="text-2xl font-bold mb-4">Crear Nueva Empresa</h2>
        <form id="empresaForm" method="POST" action="../backend/admin_gestionar_empresas_process.php" class="space-y-4">
            <input type="hidden" id="empresa_id" name="empresa_id">
            <input type="hidden" id="action" name="action" value="create">

            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre de la Empresa</label>
                <input type="text" id="nombre" name="nombre" required
                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
            </div>

            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                <select id="estado" name="estado" required
                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                    <option value="Activa">Activa</option>
                    <option value="Inactiva">Inactiva</option>
                </select>
            </div>

            <!-- ═══════════ COORDENADAS CON SELECTOR DE MAPA ═══════════ -->
            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-700">📍 Ubicación Geográfica</h3>
                    <button type="button" onclick="openMapSelector()"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 transition">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Elegir en el Mapa
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500">Latitud</label>
                        <input type="text" id="lat" name="lat" placeholder="Ej: 20.9674"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500">Longitud</label>
                        <input type="text" id="lng" name="lng" placeholder="Ej: -89.6237"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-sm">
                    </div>
                </div>
            </div>
            <div class="flex justify-end space-x-4">
                <button type="button" @click="formVisible = false; setCreateMode();"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Cancelar</button>
                <button type="submit" id="submitButton"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Crear Empresa</button>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h2 class="text-2xl font-bold mb-4">Listado de Empresas</h2>

            <table id="empresasTable" class="w-full text-left dt-responsive" style="width:100%">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="p-4">ID</th>
                        <th class="p-4">Nombre</th>
                        <th class="p-4">Estado</th>
                        <th class="p-4">Coordenadas</th>
                        <th class="p-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($empresas as $empresa): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="p-4"><?= htmlspecialchars($empresa['empresa_id'] ?? '') ?></td>
                            <td class="p-4"><?= htmlspecialchars($empresa['nombre'] ?? '') ?></td>
                            <td class="p-4">
                                <?php if (($empresa['estado'] ?? '') === 'Activa'): ?>
                                    <span
                                        class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Activa</span>
                                <?php else: ?>
                                    <span
                                        class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Inactiva</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-xs font-mono text-gray-500">
                                <?= $empresa['lat'] ? htmlspecialchars($empresa['lat'] . ', ' . $empresa['lng']) : '<i>Sin coordenadas</i>' ?>
                            </td>
                            <td class="p-4 text-right space-x-2 whitespace-nowrap">
                                <button class="edit-btn text-red-600 hover:underline"
                                    data-empresa='<?= htmlspecialchars(json_encode($empresa), ENT_QUOTES, 'UTF-8') ?>'>Editar</button>
                                <form method="POST" action="../backend/admin_gestionar_empresas_process.php"
                                    class="inline-block">
                                    <input type="hidden" name="empresa_id"
                                        value="<?= htmlspecialchars($empresa['empresa_id'] ?? '') ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="text-red-600 hover:underline"
                                        onclick="return confirm('¿Seguro?');">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#empresasTable').DataTable({ // <-- ID de tabla actualizado
            "pageLength": 10,
            "responsive": true, // <-- AÑADIDO PARA HACERLA RESPONSIVA
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

<!-- ═══════════ MODAL DEL MAPA (FULLSCREEN) ═══════════ -->
<div id="mapModal">
    <div class="map-header">
        <h3 id="mapTitle">Seleccionar ubicación</h3>
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
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('empresaForm');
        const formTitle = document.getElementById('form-title');
        const actionInput = document.getElementById('action');
        const submitButton = document.getElementById('submitButton');

        // Función para llenar el formulario en modo edición
        const setEditMode = (empresa) => {
            // AVISAMOS A ALPINE.JS QUE ABRA EL FORMULARIO
            window.dispatchEvent(new CustomEvent('open-form'));

            formTitle.textContent = `Editando Empresa #${empresa.empresa_id}`;
            actionInput.value = 'update';
            document.getElementById('empresa_id').value = empresa.empresa_id;
            document.getElementById('nombre').value = empresa.nombre;
            document.getElementById('estado').value = empresa.estado;
            document.getElementById('lat').value = empresa.lat || '';
            document.getElementById('lng').value = empresa.lng || '';
            submitButton.textContent = 'Actualizar Empresa';

            // Retrasamos el scroll un poco para dar tiempo a la animación de Alpine
            setTimeout(() => {
                form.scrollIntoView({ behavior: 'smooth' });
            }, 100);
        };

        // Función para resetear el formulario (la llama el botón "Crear" y "Cancelar")
        window.setCreateMode = () => {
            formTitle.textContent = 'Crear Nueva Empresa';
            form.reset();
            actionInput.value = 'create';
            submitButton.textContent = 'Crear Empresa';
        };

        // Delegación de eventos en la tabla (más robusto para DataTables)
        document.getElementById('empresasTable').addEventListener('click', function (e) {

            // Click en botón Editar
            if (e.target.classList.contains('edit-btn')) {
                const empresaData = JSON.parse(e.target.dataset.empresa);
                setEditMode(empresaData);
            }
        });

        // ═══════════ LÓGICA DEL MAPA INTERACTIVO ═══════════
        let map = null;
        let marker = null;
        let selectedLat = null;
        let selectedLng = null;

        window.openMapSelector = () => {
            const modal = document.getElementById('mapModal');
            const title = document.getElementById('mapTitle');
            title.textContent = '📍 Seleccionar ubicación de la Empresa — haz click en el mapa';

            modal.classList.add('active');

            // Inicializar mapa (solo la primera vez)
            setTimeout(() => {
                if (!map) {
                    // Leer coordenadas actuales de los inputs
                    let initialLat = 20.9674; // Defecto: Mérida
                    let initialLng = -89.6237;

                    const inputLat = parseFloat(document.getElementById('lat').value);
                    const inputLng = parseFloat(document.getElementById('lng').value);

                    if (!isNaN(inputLat) && !isNaN(inputLng)) {
                        initialLat = inputLat;
                        initialLng = inputLng;
                    }

                    map = L.map('mapContainer').setView([initialLat, initialLng], 12);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors',
                        maxZoom: 19
                    }).addTo(map);

                    // Si ya había coordenadas, poner el marcador
                    if (!isNaN(inputLat) && !isNaN(inputLng)) {
                        marker = L.marker([inputLat, inputLng]).addTo(map);
                    }

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

                // Limpiar confirmDialog al abrir
                document.getElementById('confirmDialog').classList.remove('active');
            }, 100);
        };

        window.closeMapSelector = () => {
            document.getElementById('mapModal').classList.remove('active');
            document.getElementById('confirmDialog').classList.remove('active');
        };

        window.cancelSelection = () => {
            document.getElementById('confirmDialog').classList.remove('active');
            if (marker) {
                map.removeLayer(marker);
                marker = null;
            }
        };

        window.confirmSelection = () => {
            if (selectedLat && selectedLng) {
                document.getElementById('lat').value = selectedLat;
                document.getElementById('lng').value = selectedLng;
            }
            closeMapSelector();
        };
    });
</script>

<?php
require_once 'footer.php'; // Cierra la página
?>
