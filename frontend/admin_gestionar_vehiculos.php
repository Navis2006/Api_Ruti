<?php
// La conexión a la BD SÍ la puedes necesitar aquí si esta página hace consultas
require_once '../backend/config/db_connection.php';
// header_admin.php ya se encarga del ROL y auth_guard
require_once 'header_admin.php'; // Carga la cabecera

// --- Consultas PHP ---
$vehiculos = $pdo->query("
        SELECT vehiculo_id, nombre, placa, tipo, estatus, altura_metros, ancho_metros, largo_metros, peso_toneladas, peso_eje_kg, velocidad_max_kmh
        FROM vehiculos
        ORDER BY nombre
    ")->fetchAll();
?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css" />
<script src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.dataTables.min.css" />

<div x-data="{ formVisible: false }" @open-form.window="formVisible = true">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <h1 class="text-3xl font-bold mb-4 md:mb-0">Administración de Vehículos</h1>

        <button @click="formVisible = true; setCreateMode();"
            class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                </path>
            </svg>
            Nuevo Vehículo
        </button>
    </div>

    <div x-show="formVisible" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-4" class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 id="form-title" class="text-2xl font-bold mb-4">Añadir Nuevo Vehículo</h2>
        <form id="vehiculoForm" method="POST" action="../backend/admin_gestionar_vehiculos_process.php"
            class="space-y-4">
            <input type="hidden" id="vehiculo_id" name="vehiculo_id">
            <input type="hidden" id="action" name="action" value="create">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre / Identificador</label>
                    <input type="text" id="nombre" name="nombre" required
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="placa" class="block text-sm font-medium text-gray-700">Placa</label>
                    <input type="text" id="placa" name="placa" required
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo</label>
                    <input type="text" id="tipo" name="tipo"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="estatus" class="block text-sm font-medium text-gray-700">Estatus</label>
                    <select id="estatus" name="estatus" required
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="en_servicio">En Servicio</option>
                        <option value="en_mantenimiento">En Mantenimiento</option>
                        <option value="de_baja">De Baja</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label for="altura_metros" class="block text-sm font-medium text-gray-700">Altura (m)</label>
                    <input type="number" step="0.01" id="altura_metros" name="altura_metros" placeholder="Ej: 4.15"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="ancho_metros" class="block text-sm font-medium text-gray-700">Ancho (m)</label>
                    <input type="number" step="0.01" id="ancho_metros" name="ancho_metros" placeholder="Ej: 2.55"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="largo_metros" class="block text-sm font-medium text-gray-700">Largo (m)</label>
                    <input type="number" step="0.01" id="largo_metros" name="largo_metros" placeholder="Ej: 16.5"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="peso_toneladas" class="block text-sm font-medium text-gray-700">Peso Total (t)</label>
                    <input type="number" step="0.01" id="peso_toneladas" name="peso_toneladas" placeholder="Ej: 30"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="peso_eje_kg" class="block text-sm font-medium text-gray-700">Peso por Eje (kg)</label>
                    <input type="number" id="peso_eje_kg" name="peso_eje_kg" placeholder="Ej: 11500"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="velocidad_max_kmh" class="block text-sm font-medium text-gray-700">Vel. Máxima
                        (km/h)</label>
                    <input type="number" id="velocidad_max_kmh" name="velocidad_max_kmh" placeholder="Ej: 90"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="flex justify-end space-x-4 pt-4">
                <button type="button" @click="formVisible = false; setCreateMode();"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    Cancelar
                </button>
                <button type="submit" id="submitButton"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Crear Vehículo</button>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-4">Listado de Vehículos</h2>

            <table id="vehiculosTable" class="w-full text-left dt-responsive" style="width:100%">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="p-4">ID</th>
                        <th class="p-4">Nombre</th>
                        <th class="p-4">Placa</th>
                        <th class="p-4">Tipo</th>
                        <th class="p-4">Estatus</th>
                        <th class="p-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($vehiculos as $vehiculo): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="p-4"><?= htmlspecialchars($vehiculo['vehiculo_id'] ?? '') ?></td>
                            <td class="p-4 min-w-64"><?= htmlspecialchars($vehiculo['nombre'] ?? '') ?></td>
                            <td class="p-4 font-mono"><?= htmlspecialchars($vehiculo['placa'] ?? '') ?></td>
                            <td class="p-4"><?= htmlspecialchars($vehiculo['tipo'] ?? '') ?></td>
                            <td class="p-4">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full
                                    <?php echo ($vehiculo['estatus'] == 'en_servicio') ? 'bg-green-100 text-green-800' : (($vehiculo['estatus'] == 'en_mantenimiento') ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                                    <?= ucfirst(str_replace('_', ' ', htmlspecialchars($vehiculo['estatus']))) ?>
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-2 whitespace-nowrap">
                                <button class="edit-btn text-blue-600 hover:underline"
                                    data-vehiculo='<?= htmlspecialchars(json_encode($vehiculo), ENT_QUOTES, 'UTF-8') ?>'>Editar</button>
                                <?php if ($vehiculo['estatus'] !== 'de_baja'): ?>
                                    <button class="btn-status text-red-600 hover:underline"
                                        data-id="<?= $vehiculo['vehiculo_id'] ?>" data-estatus="de_baja">Dar de Baja</button>
                                <?php endif; ?>
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
        $('#vehiculosTable').DataTable({ // <-- ID de tabla actualizado
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('vehiculoForm');
        const formTitle = document.getElementById('form-title');
        const actionInput = document.getElementById('action');
        const submitButton = document.getElementById('submitButton');

        // Función para llenar el formulario en modo edición
        const setEditMode = (vehiculo) => {
            // AVISAMOS A ALPINE.JS QUE ABRA EL FORMULARIO
            window.dispatchEvent(new CustomEvent('open-form'));

            formTitle.textContent = `Editando Vehículo #${vehiculo.vehiculo_id}`;
            actionInput.value = 'update';
            document.getElementById('vehiculo_id').value = vehiculo.vehiculo_id;
            document.getElementById('nombre').value = vehiculo.nombre;
            document.getElementById('placa').value = vehiculo.placa;
            document.getElementById('tipo').value = vehiculo.tipo;
            document.getElementById('estatus').value = vehiculo.estatus;
            document.getElementById('altura_metros').value = vehiculo.altura_metros;
            document.getElementById('ancho_metros').value = vehiculo.ancho_metros;
            document.getElementById('largo_metros').value = vehiculo.largo_metros;
            document.getElementById('peso_toneladas').value = vehiculo.peso_toneladas;
            document.getElementById('peso_eje_kg').value = vehiculo.peso_eje_kg;
            document.getElementById('velocidad_max_kmh').value = vehiculo.velocidad_max_kmh;
            submitButton.textContent = 'Actualizar Vehículo';

            // Retrasamos el scroll un poco para dar tiempo a la animación de Alpine
            setTimeout(() => {
                form.scrollIntoView({ behavior: 'smooth' });
            }, 100);
        };

        // Función para resetear el formulario (la llama el botón "Crear" y "Cancelar")
        window.setCreateMode = () => {
            formTitle.textContent = 'Añadir Nuevo Vehículo';
            form.reset();
            actionInput.value = 'create';
            submitButton.textContent = 'Crear Vehículo';
        };

        // Delegación de eventos en la tabla (más robusto para DataTables)
        document.getElementById('vehiculosTable').addEventListener('click', function (e) {

            // Click en botón Editar
            if (e.target.classList.contains('edit-btn')) {
                const vehiculoData = JSON.parse(e.target.dataset.vehiculo);
                setEditMode(vehiculoData);
            }

            // Click en botón de Estatus (Dar de Baja)
            if (e.target.classList.contains('btn-status')) {
                const id = e.target.dataset.id;
                const estatus = e.target.dataset.estatus;

                if (!confirm('¿Estás seguro de que quieres dar de baja este vehículo?')) {
                    return;
                }
                const formData = new FormData();
                formData.append('action', 'update_status');
                formData.append('vehiculo_id', id);
                formData.append('estatus', estatus);

                fetch('../backend/admin_gestionar_vehiculos_process.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            window.location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    });
</script>

<?php
require_once 'footer.php'; // Cierra la página
?>