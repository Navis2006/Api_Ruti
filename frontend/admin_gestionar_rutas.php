<?php
    // La conexión a la BD SÍ la puedes necesitar aquí si esta página hace consultas
    require_once '../backend/config/db_connection.php'; 
    
    // header_admin.php ya se encarga del ROL y auth_guard
    require_once 'header_admin.php'; // Carga la cabecera

try {
    $empresas = $pdo->query("SELECT empresa_id, nombre FROM empresas ORDER BY nombre")->fetchAll();
    $usuarios = $pdo->query("SELECT usuario_id, nombre, apellidos FROM usuarios ORDER BY nombre")->fetchAll();

    $rutas = $pdo->query("
        SELECT 
            r.ruta_id, r.empresa_id, r.nombre, r.descripcion, r.creado_por_usuario_id,
            ST_AsText(r.trazado_geom) as trazado_geom, 
            e.nombre as empresa_nombre, 
            CONCAT(u.nombre, ' ', u.apellidos) as creador_nombre
        FROM rutas r
        JOIN empresas e ON r.empresa_id = e.empresa_id
        JOIN usuarios u ON r.creado_por_usuario_id = u.usuario_id
        ORDER BY r.ruta_id DESC
    ")->fetchAll();
} catch (PDOException $e) {
    die("Error al obtener datos: ". $e->getMessage());
}
?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css" />

<script src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.dataTables.min.css" />

<div x-data="{ formVisible: false }" @open-form.window="formVisible = true">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <h1 class="text-3xl font-bold mb-4 md:mb-0">Administración de Rutas</h1>
        
        <button 
            @click="formVisible = true; setCreateMode();" 
            class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition"
        >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Crear Nueva Ruta
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
        <h2 id="form-title" class="text-2xl font-bold mb-4">Crear Nueva Ruta</h2>
        <form id="rutaForm" method="POST" action="../backend/admin_gestionar_rutas_process.php" class="space-y-4">
            <input type="hidden" id="ruta_id" name="ruta_id">
            <input type="hidden" id="action" name="action" value="create">
            
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre de la Ruta</label>
                <input type="text" id="nombre" name="nombre" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="empresa_id" class="block text-sm font-medium text-gray-700">Empresa</label>
                    <select id="empresa_id" name="empresa_id" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Seleccione --</option>
                        <?php foreach ($empresas as $empresa): ?>
                            <option value="<?= htmlspecialchars($empresa['empresa_id'] ?? '') ?>"><?= htmlspecialchars($empresa['nombre'] ?? '') ?></option>
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
            
            <div>
                <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="3" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            <div>
                <label for="trazado_geom" class="block text-sm font-medium text-gray-700">Trazado Geométrico (GEOMETRY)</label>
                <input type="text" id="trazado_geom" name="trazado_geom" placeholder="Ej: LINESTRING(lon lat, lon lat, ...)" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div class="flex justify-end space-x-4">
                <button type="button" @click="formVisible = false; setCreateMode();" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Cancelar</button>
                <button type="submit" id="submitButton" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Crear Ruta</button>
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
                                <form method="POST" action="../backend/admin_gestionar_rutas_process.php" class="inline-block">
                                    <input type="hidden" name="ruta_id" value="<?= htmlspecialchars($ruta['ruta_id'] ?? '') ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('¿Estás seguro de que quieres eliminar esta ruta? Esto podría afectar a los viajes que dependen de ella.');">Eliminar</button>
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
    $('#rutasTable').DataTable({ // <-- ID de tabla actualizado
        "pageLength": 10,
        "responsive": true, // <-- AÑADIDO PARA HACERLA RESPONSIVA
        "language": {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('rutaForm');
    const formTitle = document.getElementById('form-title');
    const actionInput = document.getElementById('action');
    const submitButton = document.getElementById('submitButton');
    
    // Función para llenar el formulario en modo edición
    const setEditMode = (ruta) => {
        // AVISAMOS A ALPINE.JS QUE ABRA EL FORMULARIO
        window.dispatchEvent(new CustomEvent('open-form'));
        
        formTitle.textContent = `Editando Ruta #${ruta.ruta_id}`;
        actionInput.value = 'update';
        document.getElementById('ruta_id').value = ruta.ruta_id;
        document.getElementById('nombre').value = ruta.nombre;
        document.getElementById('empresa_id').value = ruta.empresa_id;
        // Hacemos visible el 'creado_por_usuario_id' (aunque esté disabled)
        document.getElementById('creado_por_usuario_id').value = ruta.creado_por_usuario_id;
        document.getElementById('descripcion').value = ruta.descripcion;
        document.getElementById('trazado_geom').value = ruta.trazado_geom;
        submitButton.textContent = 'Actualizar Ruta';
        
        setTimeout(() => {
            form.scrollIntoView({ behavior: 'smooth' });
        }, 100); 
    };

    // Función para resetear el formulario (la llama el botón "Crear" y "Cancelar")
    window.setCreateMode = () => {
        formTitle.textContent = 'Crear Nueva Ruta';
        form.reset();
        actionInput.value = 'create';
        document.getElementById('creado_por_usuario_id').value = ""; // Limpia el campo disabled
        submitButton.textContent = 'Crear Ruta';
    };

    // Delegación de eventos en la tabla
    document.getElementById('rutasTable').addEventListener('click', function(e) {
        
        // Click en botón Editar
        if (e.target.classList.contains('edit-btn')) {
            const rutaData = JSON.parse(e.target.dataset.ruta);
            setEditMode(rutaData);
        }
    });
});
</script>

<?php
require_once 'footer.php'; // Cierra la página
?>