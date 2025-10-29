<?php
    define('ROL_REQUERIDO', 1);
require_once '../backend/auth_guard.php';
require_once '../backend/config/db_connection.php';
require_once 'header_admin.php'; // Carga la cabecera y el menú lateral

$empresas = $pdo->query("SELECT empresa_id, nombre FROM empresas ORDER BY nombre")->fetchAll();
$roles = $pdo->query("SELECT rol_id, nombre_rol FROM roles ORDER BY nombre_rol")->fetchAll();

$usuarios = $pdo->query("
    SELECT u.usuario_id, u.nombre, u.apellidos, u.email, u.empresa_id, u.rol_id, u.estatus, e.nombre as empresa_nombre, r.nombre_rol
    FROM usuarios u
    JOIN empresas e ON u.empresa_id = e.empresa_id
    JOIN roles r ON u.rol_id = r.rol_id
    ORDER BY u.apellidos
")->fetchAll();
?>

<header class="mb-8">
    <h1 class="text-3xl font-bold">Administración de Usuarios</h1>
</header>

<!-- Formulario de Creación/Edición -->
<div class="bg-white p-6 rounded-lg shadow-sm mb-8">
    <h2 id="form-title" class="text-2xl font-bold mb-4">Crear Nuevo Usuario</h2>
    <form id="usuarioForm" method="POST" action="../backend/admin_registro_process.php" class="space-y-4">
        <input type="hidden" id="usuario_id" name="usuario_id">
        <input type="hidden" id="action" name="action" value="create">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" id="nombre" name="nombre" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="apellidos" class="block text-sm font-medium text-gray-700">Apellidos</label>
                <input type="text" id="apellidos" name="apellidos" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
        
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" name="email" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
             <div>
                <label for="empresa_id" class="block text-sm font-medium text-gray-700">Empresa</label>
                <select id="empresa_id" name="empresa_id" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($empresas as $empresa): ?><option value="<?= htmlspecialchars($empresa['empresa_id']) ?>"><?= htmlspecialchars($empresa['nombre']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="rol_id" class="block text-sm font-medium text-gray-700">Rol</label>
                <select id="rol_id" name="rol_id" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($roles as $rol): ?><option value="<?= htmlspecialchars($rol['rol_id']) ?>"><?= htmlspecialchars($rol['nombre_rol']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="estatus" class="block text-sm font-medium text-gray-700">Estatus</label>
                <select id="estatus" name="estatus" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
        </div>
        
        <div>
            <label for="contrasena" class="block text-sm font-medium text-gray-700">Contraseña</label>
            <input type="password" id="contrasena" name="contrasena" placeholder="Dejar en blanco para no cambiar" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="flex justify-end space-x-4 pt-4">
            <button type="button" id="cancelButton" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300" style="display:none;">Cancelar</button>
            <button type="submit" id="submitButton" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Crear Usuario</button>
        </div>
    </form>
</div>

<!-- Listado de Usuarios -->
<div class="bg-white p-6 rounded-lg shadow-sm">
    <h2 class="text-2xl font-bold mb-4">Listado de Usuarios</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-full">
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
                            <button class="edit-btn text-blue-600 hover:underline" data-usuario='<?= htmlspecialchars(json_encode($usuario), ENT_QUOTES, 'UTF-8') ?>'>Editar</button>
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
document.addEventListener('DOMContentLoaded', function() {
    // Tu script de JS para Editar/Cancelar y Cambiar Estatus (sin cambios)
    const form = document.getElementById('usuarioForm');
    const formTitle = document.getElementById('form-title');
    const actionInput = document.getElementById('action');
    const submitButton = document.getElementById('submitButton');
    const cancelButton = document.getElementById('cancelButton');
    
    const setEditMode = (usuario) => {
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
        cancelButton.style.display = 'inline-block';
        form.scrollIntoView({ behavior: 'smooth' });
    };

    const setCreateMode = () => {
        formTitle.textContent = 'Crear Nuevo Usuario';
        form.reset();
        actionInput.value = 'create';
        document.getElementById('contrasena').setAttribute('required', 'required');
        submitButton.textContent = 'Crear Usuario';
        cancelButton.style.display = 'none';
    };

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const usuarioData = JSON.parse(this.dataset.usuario);
            setEditMode(usuarioData);
        });
    });

    cancelButton.addEventListener('click', setCreateMode);

    document.querySelectorAll('.btn-status').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const estatus = this.dataset.estatus;
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
        });
    });
});
</script>

<?php
require_once 'footer.php'; // Cierra la página
?>
