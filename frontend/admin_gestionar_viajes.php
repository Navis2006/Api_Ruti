<?php
    define('ROL_REQUERIDO', 1);
require_once '../backend/auth_guard.php';
require_once '../backend/config/db_connection.php';
require_once 'header_admin.php'; // Carga la cabecera y el menú lateral

try {
    $rutas = $pdo->query("SELECT ruta_id, nombre FROM rutas ORDER BY nombre")->fetchAll();
    $usuarios = $pdo->query("SELECT usuario_id, nombre, apellidos FROM usuarios ORDER BY nombre")->fetchAll();
    $vehiculos = $pdo->query("SELECT vehiculo_id, nombre, placa FROM vehiculos ORDER BY nombre")->fetchAll();

    $viajes = $pdo->query("
        SELECT 
            v.*, r.nombre as ruta_nombre,
            CONCAT(op.nombre, ' ', op.apellidos) as operador_nombre,
            ve.nombre as vehiculo_nombre,
            CONCAT(asig.nombre, ' ', asig.apellidos) as asignador_nombre
        FROM viajes v
        JOIN rutas r ON v.ruta_id = r.ruta_id
        JOIN usuarios op ON v.operador_usuario_id = op.usuario_id
        JOIN vehiculos ve ON v.vehiculo_id = ve.vehiculo_id
        JOIN usuarios asig ON v.asignado_por_usuario_id = asig.usuario_id
        ORDER BY v.viaje_id DESC
    ")->fetchAll();
} catch (PDOException $e) {
    die("Error al obtener datos: " . $e->getMessage());
}
?>

<header class="mb-8">
    <h1 class="text-3xl font-bold">Administración de Viajes</h1>
</header>

<!-- Formulario de Creación/Edición -->
<div class="bg-white p-6 rounded-lg shadow-sm mb-8">
    <h2 id="form-title" class="text-2xl font-bold mb-4">Crear Nuevo Viaje</h2>
    <form id="viajeForm" method="POST" action="../backend/admin_gestionar_viajes_process.php" class="space-y-4">
        <input type="hidden" id="viaje_id" name="viaje_id">
        <input type="hidden" id="action" name="action" value="create">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label for="ruta_id" class="block text-sm font-medium text-gray-700">Ruta</label>
                <select id="ruta_id" name="ruta_id" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($rutas as $ruta): ?><option value="<?= htmlspecialchars($ruta['ruta_id']) ?>"><?= htmlspecialchars($ruta['nombre']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="operador_usuario_id" class="block text-sm font-medium text-gray-700">Operador (Conductor)</label>
                <select id="operador_usuario_id" name="operador_usuario_id" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($usuarios as $usuario): ?><option value="<?= htmlspecialchars($usuario['usuario_id']) ?>"><?= htmlspecialchars($usuario['nombre'].' '.$usuario['apellidos']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="vehiculo_id" class="block text-sm font-medium text-gray-700">Vehículo</label>
                <select id="vehiculo_id" name="vehiculo_id" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($vehiculos as $vehiculo): ?><option value="<?= htmlspecialchars($vehiculo['vehiculo_id']) ?>"><?= htmlspecialchars($vehiculo['nombre'].' - '.$vehiculo['placa']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="asignado_por_usuario_id" class="block text-sm font-medium text-gray-700">Asignado por</label>
                <select id="asignado_por_usuario_id" name="asignado_por_usuario_id" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($usuarios as $usuario): ?><option value="<?= htmlspecialchars($usuario['usuario_id']) ?>"><?= htmlspecialchars($usuario['nombre'].' '.$usuario['apellidos']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                <input type="datetime-local" id="fecha_inicio" name="fecha_inicio" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="fecha_finalizacion" class="block text-sm font-medium text-gray-700">Fecha de Finalización</label>
                <input type="datetime-local" id="fecha_finalizacion" name="fecha_finalizacion" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
        
        <div>
            <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
            <select id="estado" name="estado" required class="mt-1 block w-full max-w-xs p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="Planeado">Planeado</option>
                <option value="En curso">En curso</option>
                <option value="Finalizado">Finalizado</option>
                <option value="Cancelado">Cancelado</option>
            </select>
        </div>

        <div class="flex justify-end space-x-4 pt-4">
            <button type="button" id="cancelButton" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300" style="display:none;">Cancelar</button>
            <button type="submit" id="submitButton" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Crear Viaje</button>
        </div>
    </form>
</div>

<!-- Listado de Viajes -->
<div class="bg-white p-6 rounded-lg shadow-sm">
    <h2 class="text-2xl font-bold mb-4">Listado de Viajes</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-4">ID</th>
                    <th class="p-4">Ruta</th>
                    <th class="p-4">Operador</th>
                    <th class="p-4">Vehículo</th>
                    <th class="p-4">Estado</th>
                    <th class="p-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($viajes as $viaje): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="p-4"><?= htmlspecialchars($viaje['viaje_id']) ?></td>
                        <td class="p-4"><?= htmlspecialchars($viaje['ruta_nombre']) ?></td>
                        <td class="p-4"><?= htmlspecialchars($viaje['operador_nombre']) ?></td>
                        <td class="p-4"><?= htmlspecialchars($viaje['vehiculo_nombre'] ?? 'N/A') ?></td>
                        <td class="p-4"><?= htmlspecialchars($viaje['estado']) ?></td>
                        <td class="p-4 text-right space-x-2 whitespace-nowrap">
                            <button class="edit-btn text-blue-600 hover:underline" data-viaje='<?= htmlspecialchars(json_encode($viaje), ENT_NOQUOTES, 'UTF-8') ?>'>Editar</button>
                            <form method="POST" action="../backend/admin_gestionar_viajes_process.php" class="inline-block">
                                <input type="hidden" name="viaje_id" value="<?= htmlspecialchars($viaje['viaje_id']) ?>">
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
    const form = document.getElementById('viajeForm');
    const formTitle = document.getElementById('form-title');
    const actionInput = document.getElementById('action');
    const submitButton = document.getElementById('submitButton');
    const cancelButton = document.getElementById('cancelButton');

    const setEditMode = (viaje) => {
        formTitle.textContent = `Editando Viaje #${viaje.viaje_id}`;
        actionInput.value = 'update';
        
        document.getElementById('viaje_id').value = viaje.viaje_id;
        document.getElementById('ruta_id').value = viaje.ruta_id;
        document.getElementById('operador_usuario_id').value = viaje.operador_usuario_id;
        document.getElementById('vehiculo_id').value = viaje.vehiculo_id;
        document.getElementById('asignado_por_usuario_id').value = viaje.asignado_por_usuario_id;
        document.getElementById('estado').value = viaje.estado;
        
        if (viaje.fecha_inicio) {
            document.getElementById('fecha_inicio').value = viaje.fecha_inicio.slice(0, 16).replace(' ', 'T');
        }
        if (viaje.fecha_finalizacion) {
            document.getElementById('fecha_finalizacion').value = viaje.fecha_finalizacion.slice(0, 16).replace(' ', 'T');
        }
        
        submitButton.textContent = 'Actualizar Viaje';
        cancelButton.style.display = 'inline-block';
        form.scrollIntoView({ behavior: 'smooth' });
    };

    const setCreateMode = () => {
        formTitle.textContent = 'Crear Nuevo Viaje';
        form.reset();
        actionInput.value = 'create';
        submitButton.textContent = 'Crear Viaje';
        cancelButton.style.display = 'none';
    };

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const viajeData = JSON.parse(this.dataset.viaje);
            setEditMode(viajeData);
        });
    });
    cancelButton.addEventListener('click', setCreateMode);
});
</script>

<?php
require_once 'footer.php'; // Cierra la página
?>
