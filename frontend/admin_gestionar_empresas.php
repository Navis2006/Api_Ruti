<?php
    define('ROL_REQUERIDO', 1);
require_once '../backend/auth_guard.php';
require_once '../backend/config/db_connection.php';
require_once 'header_admin.php'; // Carga la cabecera y el menú lateral

$empresas = $pdo->query("SELECT * FROM empresas ORDER BY nombre")->fetchAll();
?>

<header class="mb-8">
    <h1 class="text-3xl font-bold">Administración de Empresas</h1>
</header>

<!-- Formulario de Creación/Edición -->
<div class="bg-white p-6 rounded-lg shadow-sm mb-8 max-w-lg mx-auto">
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
            <button type="button" id="cancelButton" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300" style="display:none;">Cancelar</button>
            <button type="submit" id="submitButton" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Crear Empresa</button>
        </div>
    </form>
</div>

<!-- Listado de Empresas -->
<div class="bg-white p-6 rounded-lg shadow-sm">
    <h2 class="text-2xl font-bold mb-4">Listado de Empresas</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-full">
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
                        <td class="p-4"><?= htmlspecialchars($empresa['empresa_id']) ?></td>
                        <td class="p-4"><?= htmlspecialchars($empresa['nombre']) ?></td>
                        <td class="p-4"><?= htmlspecialchars($empresa['estado_suscripcion']) ?></td>
                        <td class="p-4"><?= htmlspecialchars($empresa['fecha_creacion']) ?></td>
                        <td class="p-4 text-right space-x-2 whitespace-nowrap">
                            <button class="edit-btn text-blue-600 hover:underline" data-empresa='<?= htmlspecialchars(json_encode($empresa), ENT_NOQUOTES, 'UTF-8') ?>'>Editar</button>
                            <form method="POST" action="../backend/admin_gestionar_empresas_process.php" class="inline-block">
                                <input type="hidden" name="empresa_id" value="<?= htmlspecialchars($empresa['empresa_id']) ?>">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tu script de JS para Editar/Cancelar (sin cambios)
    const form = document.getElementById('empresaForm');
    const formTitle = document.getElementById('form-title');
    const actionInput = document.getElementById('action');
    const submitButton = document.getElementById('submitButton');
    const cancelButton = document.getElementById('cancelButton');
    
    const setEditMode = (empresa) => {
        formTitle.textContent = `Editando Empresa #${empresa.empresa_id}`;
        actionInput.value = 'update';
        document.getElementById('empresa_id').value = empresa.empresa_id;
        document.getElementById('nombre').value = empresa.nombre;
        document.getElementById('estado_suscripcion').value = empresa.estado_suscripcion;
        submitButton.textContent = 'Actualizar Empresa';
        cancelButton.style.display = 'inline-block';
        form.scrollIntoView({ behavior: 'smooth' });
    };

    const setCreateMode = () => {
        formTitle.textContent = 'Crear Nueva Empresa';
        form.reset();
        actionInput.value = 'create';
        submitButton.textContent = 'Crear Empresa';
        cancelButton.style.display = 'none';
    };

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const empresaData = JSON.parse(this.dataset.empresa);
            setEditMode(empresaData);
        });
    });
    cancelButton.addEventListener('click', setCreateMode);
});
</script>

<?php
require_once 'footer.php'; // Cierra la página
?>
