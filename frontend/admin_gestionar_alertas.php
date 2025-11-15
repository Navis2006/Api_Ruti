<?php
    // La conexión a la BD SÍ la puedes necesitar aquí si esta página hace consultas
    require_once '../backend/config/db_connection.php'; 
    
    // header_admin.php ya se encarga del ROL y auth_guard
    require_once 'header_admin.php'; // Carga la cabecera

try {
    $rutas = $pdo->query("SELECT ruta_id, nombre FROM rutas ORDER BY nombre")->fetchAll();
    $usuarios = $pdo->query("SELECT usuario_id, nombre, apellidos FROM usuarios ORDER BY nombre")->fetchAll();

    // MODIFICADO: Añadido 'a.nivel' y 'a.estatus_alerta'
    $alertas = $pdo->query("
        SELECT 
            a.alerta_id, a.ruta_id, a.creado_por_usuario_id, a.descripcion, a.tipo_alerta, a.nivel, a.estatus_alerta,
            ST_AsText(a.ubicacion_geom) as ubicacion_geom,
            r.nombre as ruta_nombre,
            CONCAT(u.nombre, ' ', u.apellidos) as creador_nombre
        FROM alertas a
        JOIN rutas r ON a.ruta_id = r.ruta_id
        JOIN usuarios u ON a.creado_por_usuario_id = u.usuario_id
        ORDER BY a.alerta_id DESC
    ")->fetchAll();
} catch (PDOException $e) {
    die("Error al obtener datos: ". $e->getMessage());
}

// Definir los tipos y estatus en un solo lugar
$tipos_de_alerta = ['Tráfico', 'Accidente', 'Peligro en Vía', 'Mecánica', 'Desvío', 'Otro'];
$estatus_de_alerta = ['Abierta', 'Resuelta'];
?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css" />
<script src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.dataTables.min.css" />
<div x-data="{ formVisible: false }" @open-form.window="formVisible = true">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <h1 class="text-3xl font-bold mb-4 md:mb-0">Administración de Alertas</h1>
        
        <button 
            @click="formVisible = true; setCreateMode();" 
            class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition"
        >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Crear Nueva Alerta
        </button>
    </div>

    <div 
        x-show="formVisible" 
        x-cloak 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-4"
        class="bg-white p-6 rounded-lg shadow-md mb-8"
    >
        <h2 id="form-title" class="text-2xl font-bold mb-4">Crear Nueva Alerta</h2>
        <form id="alertaForm" method="POST" action="../backend/admin_gestionar_alertas_process.php" class="space-y-4">
            <input type="hidden" id="alerta_id" name="alerta_id">
            <input type="hidden" id="action" name="action" value="create">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="ruta_id" class="block text-sm font-medium text-gray-700">Ruta Asociada</label>
                    <select id="ruta_id" name="ruta_id" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Seleccione --</option>
                        <?php foreach ($rutas as $ruta): ?>
                            <option value="<?= htmlspecialchars($ruta['ruta_id'] ?? '') ?>"><?= htmlspecialchars($ruta['nombre'] ?? '') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="creado_por_usuario_id" class="block text-sm font-medium text-gray-700">Creado por (Automático)</label>
                    <select id="creado_por_usuario_id" name="creado_por_usuario_id" disabled class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Asignado por Admin Logueado --</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="tipo_alerta" class="block text-sm font-medium text-gray-700">Tipo de Alerta</label>
                    <select id="tipo_alerta" name="tipo_alerta" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Seleccione Tipo --</option>
                        <?php foreach ($tipos_de_alerta as $tipo): ?>
                            <option value="<?= $tipo ?>"><?= $tipo ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="nivel" class="block text-sm font-medium text-gray-700">Nivel (Prioridad)</label>
                    <select id="nivel" name="nivel" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="1">1 (Bajo)</option>
                        <option value="2">2 (Medio-Bajo)</option>
                        <option value="3" selected>3 (Medio)</option>
                        <option value="4">4 (Alto)</option>
                        <option value="5">5 (Urgente)</option>
                    </select>
                </div>
                <div>
                    <label for="estatus_alerta" class="block text-sm font-medium text-gray-700">Estatus</label>
                    <select id="estatus_alerta" name="estatus_alerta" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <?php foreach ($estatus_de_alerta as $estatus): ?>
                            <option value="<?= $estatus ?>"><?= $estatus ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div>
                <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="3" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            
            <div>
                <label for="ubicacion_geom" class="block text-sm font-medium text-gray-700">Ubicación (POINT)</label>
                <input type="text" id="ubicacion_geom" name="ubicacion_geom" placeholder="Ej: POINT(lon lat)" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex justify-end space-x-4">
                <button type="button" @click="formVisible = false; setCreateMode();" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Cancelar</button>
                <button type="submit" id="submitButton" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Crear Alerta</button>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-4">Listado de Alertas</h2>
            
            <table id="alertasTable" class="w-full text-left dt-responsive" style="width:100%">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="p-4">ID</th>
                        <th class="p-4">Estatus</th>
                        <th class="p-4">Nivel</th>
                        <th class="p-4">Ruta</th>
                        <th class="p-4">Descripción</th>
                        <th class="p-4">Tipo</th>
                        <th class="p-4">Creado por</th>
                        <th class="p-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($alertas as $alerta): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="p-4"><?= htmlspecialchars($alerta['alerta_id'] ?? '') ?></td>
                            <td class="p-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    <?php echo (($alerta['estatus_alerta'] ?? 'Abierta') == 'Abierta') ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?>">
                                    <?= htmlspecialchars($alerta['estatus_alerta'] ?? 'Abierta') ?>
                                </span>
                            </td>
                            <td class="p-4 font-bold"><?= htmlspecialchars($alerta['nivel'] ?? '3') ?></td>
                            <td class="p-4"><?= htmlspecialchars($alerta['ruta_nombre'] ?? '') ?></td>
                            <td class="p-4"><?= htmlspecialchars($alerta['descripcion'] ?? '') ?></td>
                            <td class="p-4"><?= htmlspecialchars($alerta['tipo_alerta'] ?? '') ?></td>
                            <td class="p-4"><?= htmlspecialchars($alerta['creador_nombre'] ?? '') ?></td>
                            <td class="p-4 text-right space-x-2 whitespace-nowrap">
                                <button class="edit-btn text-blue-600 hover:underline"
                                    data-alerta='<?= htmlspecialchars(json_encode($alerta), ENT_QUOTES, 'UTF-8') ?>'>
                                    Editar
                                </button>
                                <form method="POST" action="../backend/admin_gestionar_alertas_process.php" class="inline-block">
                                    <input type="hidden" name="alerta_id" value="<?= htmlspecialchars($alerta['alerta_id'] ?? '') ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('¿Seguro?');">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div> <script>
$(document).ready(function() {
    $('#alertasTable').DataTable({ // <-- ID de tabla actualizado
        "pageLength": 15,
        "responsive": true, 
        "language": {
            // (Tu traducción al español)
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sSearch":         "Buscar:",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            }
        },
        // Ordenar por Estatus (col 1) y Nivel (col 2)
        "order": [[ 1, "asc" ], [ 2, "desc" ]] 
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('alertaForm');
    const formTitle = document.getElementById('form-title');
    const actionInput = document.getElementById('action');
    const submitButton = document.getElementById('submitButton');
    
    // Función para llenar el formulario en modo edición
    const setEditMode = (alerta) => {
        // AVISAMOS A ALPINE.JS QUE ABRA EL FORMULARIO
        window.dispatchEvent(new CustomEvent('open-form'));
        
        formTitle.textContent = `Editando Alerta #${alerta.alerta_id}`;
        actionInput.value = 'update';
        document.getElementById('alerta_id').value = alerta.alerta_id;
        document.getElementById('ruta_id').value = alerta.ruta_id;
        // El campo 'creado_por_usuario_id' es solo visual
        document.getElementById('creado_por_usuario_id').value = alerta.creado_por_usuario_id; 
        document.getElementById('descripcion').value = alerta.descripcion;
        document.getElementById('tipo_alerta').value = alerta.tipo_alerta;
        document.getElementById('nivel').value = alerta.nivel; 
        document.getElementById('estatus_alerta').value = alerta.estatus_alerta;
        document.getElementById('ubicacion_geom').value = alerta.ubicacion_geom;
        submitButton.textContent = 'Actualizar Alerta';
        
        setTimeout(() => {
            form.scrollIntoView({ behavior: 'smooth' });
        }, 100); 
    };

    // Función para resetear el formulario
    window.setCreateMode = () => {
        formTitle.textContent = 'Crear Nueva Alerta';
        form.reset();
        document.getElementById('nivel').value = '3'; // Resetea el nivel a 'Medio'
        document.getElementById('estatus_alerta').value = 'Abierta';
        document.getElementById('creado_por_usuario_id').value = ""; // Limpia el campo disabled
        actionInput.value = 'create';
        submitButton.textContent = 'Crear Alerta';
    };

    // Delegación de eventos en la tabla
    document.getElementById('alertasTable').addEventListener('click', function(e) {
        
        // Click en botón Editar
        if (e.target.classList.contains('edit-btn')) {
            const alertaData = JSON.parse(e.target.dataset.alerta);
            setEditMode(alertaData);
        }
    });
});
</script>

<?php
require_once 'footer.php'; // Cierra la página
?>