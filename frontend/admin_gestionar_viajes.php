<?php
// La conexión a la BD SÍ la puedes necesitar aquí si esta página hace consultas
require_once '../backend/config/db_connection.php';

// header_admin.php ya se encarga del ROL y auth_guard
require_once 'header_admin.php'; // Carga la cabecera

try {
    // Consultas para los dropdowns del formulario
    $rutas = $pdo->query("SELECT ruta_id, nombre FROM rutas ORDER BY nombre")->fetchAll();
    $empresas_activas = $pdo->query("SELECT empresa_id, nombre FROM empresas WHERE estado = 'Activa' ORDER BY nombre")->fetchAll();

    // ¡Ajusta 'rol_id = 2' si tu rol de operador es otro número!
    $operadores = $pdo->query("SELECT usuario_id, nombre, apellidos FROM usuarios WHERE rol_id = 2 ORDER BY nombre")->fetchAll();
    // ¡Ajusta 'en_servicio' si tu estatus de vehículo se llama diferente!
    $vehiculos = $pdo->query("SELECT vehiculo_id, nombre, placa FROM vehiculos WHERE estatus = 'en_servicio' ORDER BY nombre")->fetchAll();

    // Tu consulta principal para la tabla (ya incluye 'v.*', que trae 'estado')
    $viajes = $pdo->query("
        SELECT 
            v.viaje_id, v.origen_empresa_id, v.operador_usuario_id, v.vehiculo_id,
            v.asignado_por_usuario_id, v.estado, v.fecha_inicio,
            e_origen.nombre as origen_nombre,
            CONCAT(op.nombre, ' ', op.apellidos) as operador_nombre,
            ve.nombre as vehiculo_nombre,
            CONCAT(asig.nombre, ' ', asig.apellidos) as asignador_nombre,
            (
                SELECT JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'tipo', CASE WHEN vd.empresa_id IS NOT NULL THEN 'empresa' ELSE 'ruta' END,
                        'id', COALESCE(vd.empresa_id, vd.ruta_id)
                    )
                ) 
                FROM (SELECT * FROM viaje_destinos ORDER BY orden ASC) as vd 
                WHERE vd.viaje_id = v.viaje_id
            ) as destinos_json
        FROM viajes v
        LEFT JOIN empresas e_origen ON v.origen_empresa_id = e_origen.empresa_id
        JOIN usuarios op ON v.operador_usuario_id = op.usuario_id
        JOIN vehiculos ve ON v.vehiculo_id = ve.vehiculo_id
        JOIN usuarios asig ON v.asignado_por_usuario_id = asig.usuario_id
        ORDER BY v.viaje_id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener datos: " . $e->getMessage());
}

// Usando tus estatus de la imagen
$estatus_de_viaje = ['Planeado', 'Asignado', 'En Curso', 'Finalizado', 'Cancelado'];
?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css" />
<script src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.dataTables.min.css" />
<div x-data="{ formVisible: false }" @open-form.window="formVisible = true">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <h1 class="text-3xl font-bold mb-4 md:mb-0">Administración de Viajes</h1>

        <button @click="formVisible = true; setCreateMode();"
            class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                </path>
            </svg>
            Programar Nuevo Viaje
        </button>
    </div>

    <div x-show="formVisible" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-4" class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 id="form-title" class="text-2xl font-bold mb-4">Programar Nuevo Viaje</h2>
        <form id="viajeForm" method="POST" action="../backend/admin_gestionar_viajes_process.php" class="space-y-4">
            <input type="hidden" id="viaje_id" name="viaje_id">
            <input type="hidden" id="action" name="action" value="create">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="origen_empresa_id" class="block text-sm font-medium text-gray-700">Sede Origen</label>
                    <select id="origen_empresa_id" name="origen_empresa_id" required
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Seleccione Origen --</option>
                        <?php foreach ($empresas_activas as $empresa): ?>
                            <option value="<?= htmlspecialchars($empresa['empresa_id']) ?>">
                                🏢 <?= htmlspecialchars($empresa['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="operador_usuario_id" class="block text-sm font-medium text-gray-700">Operador</label>
                    <select id="operador_usuario_id" name="operador_usuario_id" required
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Seleccione Operador --</option>
                        <?php foreach ($operadores as $operador): ?>
                            <option value="<?= htmlspecialchars($operador['usuario_id']) ?>">
                                <?= htmlspecialchars($operador['nombre'] . ' ' . $operador['apellidos']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="vehiculo_id" class="block text-sm font-medium text-gray-700">Vehículo</label>
                    <select id="vehiculo_id" name="vehiculo_id" required
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Seleccione Vehículo --</option>
                        <?php foreach ($vehiculos as $vehiculo): ?>
                            <option value="<?= htmlspecialchars($vehiculo['vehiculo_id']) ?>">
                                <?= htmlspecialchars($vehiculo['nombre'] . ' (' . ($vehiculo['placa'] ?? 'N/A') . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="fecha_hora_programada" class="block text-sm font-medium text-gray-700">Fecha y Hora
                        Programada</label>
                    <input type="datetime-local" id="fecha_hora_programada" name="fecha_hora_programada" required
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700">Estatus</label>
                    <select id="estado" name="estado" required
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <?php foreach ($estatus_de_viaje as $estatus): ?>
                            <option value="<?= $estatus ?>"><?= $estatus ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- ═══════════ MULTI-DESTINOS ═══════════ -->
            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 mb-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-700">📍 Destinos del Viaje (Paradas)</h3>
                    <button type="button" onclick="agregarDestino()"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition">
                        + Agregar Parada
                    </button>
                </div>

                <div id="destinos-container" class="space-y-3">
                    <!-- Los select de destinos se injectan aquí -->
                </div>
                <p id="destinos-empty-msg" class="text-xs text-gray-500 mt-2">Haz clic en "+ Agregar Parada" para
                    programar el destino del viaje.</p>
            </div>

            <div class="flex justify-end space-x-4">
                <button type="button" @click="formVisible = false; setCreateMode();"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Cancelar</button>
                <button type="submit" id="submitButton"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Programar Viaje</button>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-4">Listado de Viajes</h2>

            <table id="viajesTable" class="w-full text-left dt-responsive" style="width:100%">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="p-4">ID</th>
                        <th class="p-4">Estado</th>
                        <th class="p-4">Sede Origen</th>
                        <th class="p-4">Operador</th>
                        <th class="p-4">Vehículo</th>
                        <th class="p-4">Programado</th>
                        <th class="p-4">Asignado por</th>
                        <th class="p-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($viajes as $viaje): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="p-4"><?= htmlspecialchars($viaje['viaje_id'] ?? '') ?></td>
                            <td class="p-4">
                                <?php
                                // CORREGIDO: Leer de $viaje['estado']
                                $estatus = $viaje['estado'] ?? 'Planeado';
                                $color = 'bg-gray-100 text-gray-800'; // Planeado
                                if ($estatus == 'Asignado')
                                    $color = 'bg-yellow-100 text-yellow-800';
                                if ($estatus == 'En Curso' || $estatus == 'En curso')
                                    $color = 'bg-blue-100 text-blue-800';
                                if ($estatus == 'Finalizado')
                                    $color = 'bg-green-100 text-green-800';
                                if ($estatus == 'Cancelado')
                                    $color = 'bg-red-100 text-red-800';
                                ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $color ?>">
                                    <?= htmlspecialchars($estatus) ?>
                                </span>
                            </td>
                            <td class="p-4 font-medium text-blue-700">🏢
                                <?= htmlspecialchars($viaje['origen_nombre'] ?? '') ?>
                            </td>
                            <td class="p-4"><?= htmlspecialchars($viaje['operador_nombre'] ?? '') ?></td>
                            <td class="p-4"><?= htmlspecialchars($viaje['vehiculo_nombre'] ?? 'N/A') ?></td>
                            <td class="p-4"><?= htmlspecialchars($viaje['fecha_inicio'] ?? '') ?></td>
                            <td class="p-4"><?= htmlspecialchars($viaje['asignador_nombre'] ?? '') ?></td>
                            <td class="p-4 text-right space-x-2 whitespace-nowrap">
                                <button class="edit-btn text-blue-600 hover:underline"
                                    data-viaje='<?= htmlspecialchars(json_encode($viaje), ENT_QUOTES, 'UTF-8') ?>'>
                                    Editar
                                </button>
                                <?php if ($viaje['estado'] !== 'Finalizado' && $viaje['estado'] !== 'Cancelado'): ?>
                                    <button class="btn-status text-red-600 hover:underline" data-id="<?= $viaje['viaje_id'] ?>"
                                        data-estatus="Cancelado">Cancelar Viaje</button>
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
        $('#viajesTable').DataTable({
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
            },
            "order": [[0, "desc"]]
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('viajeForm');
        const formTitle = document.getElementById('form-title');
        const actionInput = document.getElementById('action');
        const submitButton = document.getElementById('submitButton');

        // Función para llenar el formulario en modo edición
        const setEditMode = (viaje) => {
            window.dispatchEvent(new CustomEvent('open-form'));

            formTitle.textContent = `Editando Viaje #${viaje.viaje_id}`;
            actionInput.value = 'update';
            document.getElementById('viaje_id').value = viaje.viaje_id;
            document.getElementById('origen_empresa_id').value = viaje.origen_empresa_id;
            document.getElementById('operador_usuario_id').value = viaje.operador_usuario_id;
            document.getElementById('vehiculo_id').value = viaje.vehiculo_id;

            // Vaciar destinos previos
            document.getElementById('destinos-container').innerHTML = '';
            document.getElementById('destinos-empty-msg').style.display = 'block';
            destinoCount = 0; // Se define más abajo, pero lo reseteamos aquí a 0 global si es accesible
            if (typeof window.resetDestinoCount === 'function') {
                window.resetDestinoCount();
            }

            // Cargar destinos
            if (viaje.destinos_json) {
                try {
                    const destinosArray = JSON.parse(viaje.destinos_json);
                    if (Array.isArray(destinosArray)) {
                        destinosArray.forEach(d => {
                            if (d && d.tipo && d.id) {
                                window.agregarDestino(`${d.tipo}_${d.id}`);
                            }
                        });
                    }
                } catch (e) {
                    console.error("Error al parsear destinos JSON", e);
                }
            }

            let fechaProgramada = '';
            if (viaje.fecha_inicio) { // CORREGIDO: ahora es 'fecha_inicio'
                fechaProgramada = viaje.fecha_inicio.replace(' ', 'T');
            }
            document.getElementById('fecha_hora_programada').value = fechaProgramada;

            // CORREGIDO: Leer 'viaje.estado' y llenar el select 'estado'
            document.getElementById('estado').value = viaje.estado;
            submitButton.textContent = 'Actualizar Viaje';

            setTimeout(() => {
                form.scrollIntoView({ behavior: 'smooth' });
            }, 100);
        };

        // Función para resetear el formulario
        window.setCreateMode = () => {
            formTitle.textContent = 'Programar Nuevo Viaje';
            form.reset();

            // Vaciar destinos
            document.getElementById('destinos-container').innerHTML = '';
            document.getElementById('destinos-empty-msg').style.display = 'block';

            // CORREGIDO: Poner 'Planeado' en el select 'estado'
            document.getElementById('estado').value = 'Planeado';
            actionInput.value = 'create';
            submitButton.textContent = 'Programar Viaje';
        };

        // Delegación de eventos en la tabla
        document.getElementById('viajesTable').addEventListener('click', function (e) {

            // Click en botón Editar
            if (e.target.classList.contains('edit-btn')) {
                const viajeData = JSON.parse(e.target.dataset.viaje);
                setEditMode(viajeData);
            }

            // Click en botón de Estatus (Cancelar Viaje)
            if (e.target.classList.contains('btn-status')) {
                const id = e.target.dataset.id;
                const estatus = e.target.dataset.estatus; // 'Cancelado'

                if (!confirm('¿Estás seguro de que quieres CANCELAR este viaje?')) {
                    return;
                }
                const formData = new FormData();
                formData.append('action', 'update_status');
                formData.append('viaje_id', id);
                // CORREGIDO: Enviar 'estado' al backend
                formData.append('estado', estatus);

                fetch('../backend/admin_gestionar_viajes_process.php', {
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

    // --- LÓGICA DE MULTI-DESTINOS ---
    let destinoCount = 0;

    // Convertir las opciones a strings para JS
    const empresasOptions = `
        <optgroup label="Empresas (Sedes Oficiales)">
            <?php foreach ($empresas_activas as $empresa): ?>
                <option value="empresa_<?= $empresa['empresa_id'] ?>">🏢 <?= htmlspecialchars(addslashes($empresa['nombre'])) ?></option>
            <?php endforeach; ?>
        </optgroup>
    `;
    const rutasOptions = `
        <optgroup label="Destinos Personalizados (Rutas)">
            <?php foreach ($rutas as $ruta): ?>
                <option value="ruta_<?= $ruta['ruta_id'] ?>">📍 <?= htmlspecialchars(addslashes($ruta['nombre'])) ?></option>
            <?php endforeach; ?>
        </optgroup>
    `;

    window.resetDestinoCount = () => {
        destinoCount = 0;
    };

    window.agregarDestino = (selectedValue = '') => {
        destinoCount++;
        document.getElementById('destinos-empty-msg').style.display = 'none';

        const wrapper = document.createElement('div');
        wrapper.className = 'flex items-center space-x-2 destino-item';

        // Creamos el select como nodo DOM para seleccionar fácilmente el valor predeterminado
        const selectHTML = `
            <select name="destinos[]" required class="flex-1 p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                <option value="">-- Seleccione parada --</option>
                ${empresasOptions}
                ${rutasOptions}
            </select>
        `;

        wrapper.innerHTML = `
            <span class="font-bold text-gray-500 w-6 text-center">${destinoCount}.</span>
            ${selectHTML}
            <button type="button" onclick="this.parentElement.remove(); actualizarNumeros();" class="p-2 text-red-600 hover:bg-red-50 rounded-md" title="Eliminar Parada">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        `;

        // Si hay un valor seleccionado, lo aplicamos
        if (selectedValue) {
            const selectElement = wrapper.querySelector('select');
            selectElement.value = selectedValue;
        }

        document.getElementById('destinos-container').appendChild(wrapper);
    };

    window.actualizarNumeros = () => {
        const items = document.querySelectorAll('.destino-item');
        destinoCount = 0;
        items.forEach(item => {
            destinoCount++;
            item.querySelector('span').textContent = `${destinoCount}.`;
        });

        if (destinoCount === 0) {
            document.getElementById('destinos-empty-msg').style.display = 'block';
        }
    };
</script>

<?php
require_once 'footer.php'; // Cierra la página
?>