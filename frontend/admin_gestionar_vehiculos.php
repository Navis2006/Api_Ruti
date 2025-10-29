<?php
    define('ROL_REQUERIDO', 1);
require_once '../backend/auth_guard.php';
require_once '../backend/config/db_connection.php';
require_once 'header_admin.php'; // Carga la cabecera y el menú lateral

$empresas = $pdo->query("SELECT empresa_id, nombre FROM empresas ORDER BY nombre")->fetchAll();
$vehiculos = $pdo->query("
    SELECT v.vehiculo_id, v.empresa_id, v.nombre, v.placa, v.tipo, v.estatus, v.altura_metros, v.ancho_metros, v.largo_metros, v.peso_toneladas, e.nombre as empresa_nombre
    FROM vehiculos v
    JOIN empresas e ON v.empresa_id = e.empresa_id
    ORDER BY v.nombre
")->fetchAll();
?>

<header class="mb-8">
    <h1 class="text-3xl font-bold">Administración de Vehículos</h1>
</header>

<!-- Formulario de Creación/Edición -->
<div class="bg-white p-6 rounded-lg shadow-sm mb-8">
    <h2 id="form-title" class="text-2xl font-bold mb-4">Crear Nuevo Vehículo</h2>
    <form id="vehiculoForm" method="POST" action="../backend/admin_gestionar_vehiculos_process.php" class="space-y-4">
        <input type="hidden" id="vehiculo_id" name="vehiculo_id">
        <input type="hidden" id="action" name="action" value="create">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre / Identificador</label>
                <input type="text" id="nombre" name="nombre" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="placa" class="block text-sm font-medium text-gray-700">Placa</label>
                <input type="text" id="placa" name="placa" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="empresa_id" class="block text-sm font-medium text-gray-700">Empresa</label>
                <select id="empresa_id" name="empresa_id" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($empresas as $empresa): ?><option value="<?= htmlspecialchars($empresa['empresa_id']) ?>"><?= htmlspecialchars($empresa['nombre']) ?></option><?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
             <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo</label>
                <input type="text" id="tipo" name="tipo" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
             <div>
                <label for="estatus" class="block text-sm font-medium text-gray-700">Estatus</label>
                <select id="estatus" name="estatus" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="en_servicio">En Servicio</option>
                    <option value="en_mantenimiento">En Mantenimiento</option>
                    <option value="de_baja">De Baja</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label for="altura_metros" class="block text-sm font-medium text-gray-700">Altura (m)</label>
                <input type="number" step="0.01" id="altura_metros" name="altura_metros" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="largo_metros" class="block text-sm font-medium text-gray-700">Largo (m)</label>
                <input type="number" step="0.01" id="largo_metros" name="largo_metros" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
             <div>
                <label for="ancho_metros" class="block text-sm font-medium text-gray-700">Ancho (m)</label>
                <input type="number" step="0.01" id="ancho_metros" name="ancho_metros" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="peso_toneladas" class="block text-sm font-medium text-gray-700">Peso (t)</label>
                <input type="number" step="0.01" id="peso_toneladas" name="peso_toneladas" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
        
        <div class="flex justify-end space-x-4 pt-4">
            <button type="button" id="cancelButton" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300" style="display:none;">Cancelar</button>
            <button type="submit" id="submitButton" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Crear Vehículo</button>
        </div>
    </form>
</div>

<!-- Listado de Vehículos -->
<div class="bg-white p-6 rounded-lg shadow-sm">
     <h2 class="text-2xl font-bold mb-4">Listado de Vehículos</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-4">ID</th>
                    <th class="p-4">Nombre</th>
                    <th class="p-4">Placa</th>
                    <th class="p-4">Empresa</th>
                    <th class="p-4">Tipo</th>
                    <th class="p-4">Estatus</th>
                    <th class="p-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($vehiculos as $vehiculo): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="p-4"><?= htmlspecialchars($vehiculo['vehiculo_id']) ?></td>
                        <td class="p-4"><?= htmlspecialchars($vehiculo['nombre']) ?></td>
                        <td class="p-4 font-mono"><?= htmlspecialchars($vehiculo['placa']) ?></td>
                        <td class="p-4"><?= htmlspecialchars($vehiculo['empresa_nombre']) ?></td>
                        <td class="p-4"><?= htmlspecialchars($vehiculo['tipo']) ?></td>
                        <td class="p-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                <?php echo ($vehiculo['estatus'] == 'en_servicio') ? 'bg-green-100 text-green-800' : (($vehiculo['estatus'] == 'en_mantenimiento') ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                                <?= ucfirst(str_replace('_', ' ', htmlspecialchars($vehiculo['estatus']))) ?>
                            </span>
                        </td>
                        <td class="p-4 text-right space-x-2 whitespace-nowrap">
                            <button class="edit-btn text-blue-600 hover:underline" data-vehiculo='<?= htmlspecialchars(json_encode($vehiculo), ENT_QUOTES, 'UTF-8') ?>'>Editar</button>
                            <?php if ($vehiculo['estatus'] !== 'de_baja'): ?>
                                <button class="btn-status text-red-600 hover:underline" data-id="<?= $vehiculo['vehiculo_id'] ?>" data-estatus="de_baja">Dar de Baja</button>
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
    const form = document.getElementById('vehiculoForm');
    const formTitle = document.getElementById('form-title');
    const actionInput = document.getElementById('action');
    const submitButton = document.getElementById('submitButton');
    const cancelButton = document.getElementById('cancelButton');

    const setEditMode = (vehiculo) => {
        formTitle.textContent = `Editando Vehículo #${vehiculo.vehiculo_id}`;
        actionInput.value = 'update';
        document.getElementById('vehiculo_id').value = vehiculo.vehiculo_id;
        document.getElementById('nombre').value = vehiculo.nombre;
        document.getElementById('placa').value = vehiculo.placa;
        document.getElementById('empresa_id').value = vehiculo.empresa_id;
        document.getElementById('tipo').value = vehiculo.tipo;
        document.getElementById('estatus').value = vehiculo.estatus;
        document.getElementById('altura_metros').value = vehiculo.altura_metros;
        document.getElementById('ancho_metros').value = vehiculo.ancho_metros;
        document.getElementById('largo_metros').value = vehiculo.largo_metros;
        document.getElementById('peso_toneladas').value = vehiculo.peso_toneladas;
        submitButton.textContent = 'Actualizar Vehículo';
        cancelButton.style.display = 'inline-block';
        form.scrollIntoView({ behavior: 'smooth' });
    };

    const setCreateMode = () => {
        formTitle.textContent = 'Crear Nuevo Vehículo';
        form.reset();
        actionInput.value = 'create';
        submitButton.textContent = 'Crear Vehículo';
        cancelButton.style.display = 'none';
    };

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const vehiculoData = JSON.parse(this.dataset.vehiculo);
            setEditMode(vehiculoData);
        });
    });

    cancelButton.addEventListener('click', setCreateMode);

    document.querySelectorAll('.btn-status').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const estatus = this.dataset.estatus;
            
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
        });
    });
});
</script>

<?php
require_once 'footer.php'; // Cierra la página
?>
