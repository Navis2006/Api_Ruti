<?php
    // La conexión a la BD SÍ la puedes necesitar aquí si esta página hace consultas
    require_once '../backend/config/db_connection.php'; 
    // header_admin.php ya se encarga del ROL y auth_guard
    require_once 'header_admin.php'; // Carga la cabecera

    // --- Consultas PHP (sin cambios) ---
    $empresas = $pdo->query("SELECT empresa_id, nombre FROM empresas ORDER BY nombre")->fetchAll();
    $roles = $pdo->query("SELECT rol_id, nombre_rol FROM roles ORDER BY nombre_rol")->fetchAll();
    $usuarios = $pdo->query("
        SELECT u.usuario_id, u.nombre, u.apellidos, u.email, u.empresa_id, u.rol_id, u.estatus, e.nombre as empresa_nombre, r.nombre_rol
        FROM usuarios u
        JOIN empresas e ON u.empresa_id = e.empresa_id
        JOIN roles r ON u.rol_id = r.rol_id
        ORDER BY u.usuario_id ASC
    ")->fetchAll();
?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css" />

<script src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.dataTables.min.css" />


<div x-data="{ formVisible: false }" @open-form.window="formVisible = true">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <h1 class="text-3xl font-bold mb-4 md:mb-0">Administración de Usuarios</h1>
        
        <button 
            @click="formVisible = true; setCreateMode();" 
            class="flex items-center px-4 py-2 bg-red-600 text-white rounded-lg shadow-md hover:bg-red-700 transition"
        >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Crear Nuevo Usuario
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
        <h2 id="form-title" class="text-2xl font-bold mb-4">Crear Nuevo Usuario</h2>
        <form id="usuarioForm" method="POST" action="../backend/admin_registro_process.php" class="space-y-4">
            <input type="hidden" id="usuario_id" name="usuario_id">
            <input type="hidden" id="action" name="action" value="create">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" id="nombre" name="nombre" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <label for="apellidos" class="block text-sm font-medium text-gray-700">Apellidos</label>
                    <input type="text" id="apellidos" name="apellidos" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                </div>
            </div>
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" required autocomplete="email" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                 <div>
                    <label for="empresa_id" class="block text-sm font-medium text-gray-700">Empresa</label>
                    <select id="empresa_id" name="empresa_id" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                        <option value="">-- Seleccione --</option>
                        <?php foreach ($empresas as $empresa): ?><option value="<?= htmlspecialchars($empresa['empresa_id']) ?>"><?= htmlspecialchars($empresa['nombre']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="rol_id" class="block text-sm font-medium text-gray-700">Rol</label>
                    <select id="rol_id" name="rol_id" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                        <option value="">-- Seleccione --</option>
                        <?php foreach ($roles as $rol): ?><option value="<?= htmlspecialchars($rol['rol_id']) ?>"><?= htmlspecialchars($rol['nombre_rol']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="estatus" class="block text-sm font-medium text-gray-700">Estatus</label>
                    <select id="estatus" name="estatus" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label for="contrasena" class="block text-sm font-medium text-gray-700">Contraseña</label>
                <input type="password" id="contrasena" name="contrasena" placeholder="Dejar en blanco para no cambiar" autocomplete="new-password" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
            </div>

            <div class="flex justify-end space-x-4 pt-4">
                <button 
                    type="button" 
                    @click="formVisible = false; setCreateMode();" 
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                >
                    Cancelar
                </button>
                <button type="submit" id="submitButton" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Crear Usuario</button>
            </div>
        </form>
    </div>
<div class="overflow-x-auto">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <table id="usuariosTable" class="w-full text-left dt-responsive" style="width:100%">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-4">ID</th>
                    <th class="p-4">Nombre Completo</th>
                    <th class="p-4">Email</th>
                    <th class="p-4">Empresa</th>
                    <th class="p-4">Rol</th>
                    <th class="p-4">Estatus</th>
                    <th class="p-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($usuarios as $usuario): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="p-4"><?= htmlspecialchars($usuario['usuario_id']) ?></td>
                        <td class="p-4"><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']) ?></td>
                        <td class="p-4"><?= htmlspecialchars($usuario['email']) ?></td>
                        <td class="p-4"><?= htmlspecialchars($usuario['empresa_nombre']) ?></td>
                        <td class="p-4"><?= htmlspecialchars($usuario['nombre_rol']) ?></td>
                        <td class="p-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                <?php echo ($usuario['estatus'] == 'activo') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?= ucfirst(htmlspecialchars($usuario['estatus'])) ?>
                            </span>
                        </td>
                        <td class="p-4 text-right space-x-2 whitespace-nowrap">
                            <button class="edit-btn text-red-600 hover:underline" data-usuario='<?= htmlspecialchars(json_encode($usuario), ENT_QUOTES, 'UTF-8') ?>'>Editar</button>
                            <?php if ($usuario['estatus'] === 'activo'): ?>
                                <button class="btn-status text-red-600 hover:underline" data-id="<?= $usuario['usuario_id'] ?>" data-estatus="inactivo">Dar de Baja</button>
                            <?php else: ?>
                                <button class="btn-status text-green-600 hover:underline" data-id="<?= $usuario['usuario_id'] ?>" data-estatus="activo">Activar</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div> 
<script>
$(document).ready(function() {
    $('#usuariosTable').DataTable({
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
    const form = document.getElementById('usuarioForm');
    const formTitle = document.getElementById('form-title');
    const actionInput = document.getElementById('action');
    const submitButton = document.getElementById('submitButton');
    
    const setEditMode = (usuario) => {
        // AVISAMOS A ALPINE.JS QUE ABRA EL FORMULARIO
        window.dispatchEvent(new CustomEvent('open-form'));
        
        formTitle.textContent = `Editando Usuario #${usuario.usuario_id}`;
        actionInput.value = 'update';
        document.getElementById('usuario_id').value = usuario.usuario_id;
        document.getElementById('nombre').value = usuario.nombre;
        document.getElementById('apellidos').value = usuario.apellidos;
        document.getElementById('email').value = usuario.email;
        document.getElementById('empresa_id').value = usuario.empresa_id;
        document.getElementById('rol_id').value = usuario.rol_id;
        document.getElementById('estatus').value = usuario.estatus; 
        document.getElementById('contrasena').value = '';
        document.getElementById('contrasena').removeAttribute('required');
        submitButton.textContent = 'Actualizar Usuario';
        
        // Retrasamos el scroll un poco para dar tiempo a la animación de Alpine
        setTimeout(() => {
            form.scrollIntoView({ behavior: 'smooth' });
        }, 100); 
    };

    // Esta función la llama el botón "Crear Nuevo Usuario" y "Cancelar"
    window.setCreateMode = () => {
        formTitle.textContent = 'Crear Nuevo Usuario';
        form.reset();
        actionInput.value = 'create';
        document.getElementById('contrasena').setAttribute('required', 'required');
        submitButton.textContent = 'Crear Usuario';
    };

    // Usamos 'delegación de eventos' en la tabla.
    // Esto es MÁS ROBUSTO para DataTables, ya que los botones
    // pueden moverse entre páginas.
    document.getElementById('usuariosTable').addEventListener('click', function(e) {
        // Click en botón Editar
        if (e.target.classList.contains('edit-btn')) {
            const usuarioData = JSON.parse(e.target.dataset.usuario);
            setEditMode(usuarioData);
        }
        
        // Click en botón de Estatus
        if (e.target.classList.contains('btn-status')) {
            const id = e.target.dataset.id;
            const estatus = e.target.dataset.estatus;
            const accion = estatus === 'activo' ? 'activar' : 'dar de baja';

            if (!confirm(`¿Estás seguro de que quieres ${accion} a este usuario?`)) {
                return;
            }
            const formData = new FormData();
            formData.append('action', 'update_status');
            formData.append('usuario_id', id);
            formData.append('estatus', estatus);

            fetch('../backend/admin_registro_process.php', {
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
