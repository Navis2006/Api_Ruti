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

<div x-data="{ formVisible: false }" @open-form.window="formVisible = true">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <h1 class="text-3xl font-bold mb-4 md:mb-0">Administración de Empresas</h1>
        
        <button 
            @click="formVisible = true; setCreateMode();" 
            class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition"
        >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Crear Nueva Empresa
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
        class="bg-white p-6 rounded-lg shadow-sm mb-8 max-w-lg mx-auto"
    >
        <h2 id="form-title" class="text-2xl font-bold mb-4">Crear Nueva Empresa</h2>
        <form id="empresaForm" method="POST" action="../backend/admin_gestionar_empresas_process.php" class="space-y-4">
            <input type="hidden" id="empresa_id" name="empresa_id">
            <input type="hidden" id="action" name="action" value="create">
            
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre de la Empresa</label>
                <input type="text" id="nombre" name="nombre" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="estado_suscripcion" class="block text-sm font-medium text-gray-700">Estado de Suscripción</label>
                <select id="estado_suscripcion" name="estado_suscripcion" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="Activa">Activa</option>
                    <option value="Inactiva">Inactiva</option>
                    <option value="Prueba">Prueba</option>
                </select>
            </div>
            <div class="flex justify-end space-x-4">
                <button type="button" @click="formVisible = false; setCreateMode();" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Cancelar</button>
                <button type="submit" id="submitButton" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Crear Empresa</button>
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
                        <th class="p-4">Suscripción</th>
                        <th class="p-4">Fecha Creación</th>
                        <th class="p-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($empresas as $empresa): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="p-4"><?= htmlspecialchars($empresa['empresa_id'] ?? '') ?></td>
                            <td class="p-4"><?= htmlspecialchars($empresa['nombre'] ?? '') ?></td>
                            <td class="p-4"><?= htmlspecialchars($empresa['estado_suscripcion'] ?? '') ?></td>
                            <td class="p-4"><?= htmlspecialchars($empresa['fecha_creacion'] ?? '') ?></td>
                            <td class="p-4 text-right space-x-2 whitespace-nowrap">
                                <button class="edit-btn text-blue-600 hover:underline" data-empresa='<?= htmlspecialchars(json_encode($empresa), ENT_QUOTES, 'UTF-8') ?>'>Editar</button>
                                <form method="POST" action="../backend/admin_gestionar_empresas_process.php" class="inline-block">
                                    <input type="hidden" name="empresa_id" value="<?= htmlspecialchars($empresa['empresa_id'] ?? '') ?>">
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
    $('#empresasTable').DataTable({ // <-- ID de tabla actualizado
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
        document.getElementById('estado_suscripcion').value = empresa.estado_suscripcion;
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
    document.getElementById('empresasTable').addEventListener('click', function(e) {
        
        // Click en botón Editar
        if (e.target.classList.contains('edit-btn')) {
            // Corregido: 'data-empresa'
            const empresaData = JSON.parse(e.target.dataset.empresa); 
            setEditMode(empresaData);
        }
        
        // No necesitamos un listener para "Eliminar" porque es un <form>
    });
});
</script>

<?php
require_once 'footer.php'; // Cierra la página
?>