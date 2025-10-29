<?php
    define('ROL_REQUERIDO', 1);
require_once '../backend/auth_guard.php';
require_once '../backend/config/db_connection.php';
require_once 'header_admin.php'; // Carga la cabecera y el menú lateral

try {
    $rutas = $pdo->query("SELECT ruta_id, nombre FROM rutas ORDER BY nombre")->fetchAll();
    $usuarios = $pdo->query("SELECT usuario_id, nombre, apellidos FROM usuarios ORDER BY nombre")->fetchAll();

    $alertas = $pdo->query("
        SELECT 
            a.alerta_id, a.ruta_id, a.creado_por_usuario_id, a.descripcion, a.tipo_alerta,
            ST_AsText(a.ubicacion_geom) as ubicacion_geom,
            r.nombre as ruta_nombre,
            CONCAT(u.nombre, ' ', u.apellidos) as creador_nombre
        FROM alertas a
        JOIN rutas r ON a.ruta_id = r.ruta_id
        JOIN usuarios u ON a.creado_por_usuario_id = u.usuario_id
        ORDER BY a.alerta_id DESC
    ")->fetchAll();
} catch (PDOException $e) {
    die("Error al obtener datos: " . $e->getMessage());
}
?>

<header class="mb-8">
    <h1 class="text-3xl font-bold">Administración de Alertas</h1>
</header>

<!-- Formulario de Creación/Edición -->
<div class="bg-white p-6 rounded-lg shadow-sm mb-8">
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
                        <option value="<?= htmlspecialchars($ruta['ruta_id']) ?>"><?= htmlspecialchars($ruta['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="creado_por_usuario_id" class="block text-sm font-medium text-gray-700">Creado por</label>
                <select id="creado_por_usuario_id" name="creado_por_usuario_id" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= htmlspecialchars($usuario['usuario_id']) ?>"><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="3" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="tipo_alerta" class="block text-sm font-medium text-gray-700">Tipo de Alerta</label>
                <input type="text" id="tipo_alerta" name="tipo_alerta" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="ubicacion_geom" class="block text-sm font-medium text-gray-700">Ubicación (POINT)</label>
                <input type="text" id="ubicacion_geom" name="ubicacion_geom" placeholder="Ej: POINT(lon lat)" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <button type="button" id="cancelButton" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300" style="display:none;">Cancelar</button>
            <button type="submit" id="submitButton" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Crear Alerta</button>
        </div>
    </form>
</div>

<!-- Listado de Alertas -->
<div class="bg-white p-6 rounded-lg shadow-sm">
    <h2 class="text-2xl font-bold mb-4">Listado de Alertas</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-4">ID</th>
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
                        <td class="p-4"><?= htmlspecialchars($alerta['alerta_id']) ?></td>
                        <td class="p-4"><?= htmlspecialchars($alerta['ruta_nombre']) ?></td>
                        <td class="p-4"><?= htmlspecialchars($alerta['descripcion']) ?></td>
                        <td class="p-4"><?= htmlspecialchars($alerta['tipo_alerta']) ?></td>
                        <td class="p-4"><?= htmlspecialchars($alerta['creador_nombre']) ?></td>
                        <td class="p-4 text-right space-x-2 whitespace-nowrap">
                            <button class="edit-btn text-blue-600 hover:underline"
                                data-alerta='<?= htmlspecialchars(json_encode($alerta), ENT_QUOTES, 'UTF-8') ?>'>
                                Editar
                            </button>
                            <form method="POST" action="../backend/admin_gestionar_alertas_process.php" class="inline-block">
                                <input type="hidden" name="alerta_id" value="<?= htmlspecialchars($alerta['alerta_id']) ?>">
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
    const form = document.getElementById('alertaForm');
    const formTitle = document.getElementById('form-title');
    const actionInput = document.getElementById('action');
    const submitButton = document.getElementById('submitButton');
    const cancelButton = document.getElementById('cancelButton');

    const setEditMode = (alerta) => {
        formTitle.textContent = `Editando Alerta #${alerta.alerta_id}`;
        actionInput.value = 'update';
        document.getElementById('alerta_id').value = alerta.alerta_id;
        document.getElementById('ruta_id').value = alerta.ruta_id;
        document.getElementById('creado_por_usuario_id').value = alerta.creado_por_usuario_id;
        document.getElementById('descripcion').value = alerta.descripcion;
        document.getElementById('tipo_alerta').value = alerta.tipo_alerta;
        document.getElementById('ubicacion_geom').value = alerta.ubicacion_geom;
        submitButton.textContent = 'Actualizar Alerta';
        cancelButton.style.display = 'inline-block';
        form.scrollIntoView({ behavior: 'smooth' });
    };

    const setCreateMode = () => {
        formTitle.textContent = 'Crear Nueva Alerta';
        form.reset();
        actionInput.value = 'create';
        submitButton.textContent = 'Crear Alerta';
        cancelButton.style.display = 'none';
    };

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const alertaData = JSON.parse(this.dataset.alerta);
            setEditMode(alertaData);
        });
    });

    cancelButton.addEventListener('click', setCreateMode);
});
</script>

<?php
require_once 'footer.php'; // Cierra la página
?>
