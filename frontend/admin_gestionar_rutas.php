<?php
    define('ROL_REQUERIDO', 1);
require_once '../backend/auth_guard.php';
require_once '../backend/config/db_connection.php';
require_once 'header_admin.php'; // Carga la cabecera y el menú lateral

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
    die("Error al obtener datos: " . $e->getMessage());
}
?>

<header class="mb-8">
    <h1 class="text-3xl font-bold">Administración de Rutas</h1>
</header>

<!-- Formulario de Creación/Edición -->
<div class="bg-white p-6 rounded-lg shadow-sm mb-8">
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
                        <option value="<?= htmlspecialchars($empresa['empresa_id']) ?>"><?= htmlspecialchars($empresa['nombre']) ?></option>
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
            <textarea id="descripcion" name="descripcion" rows="3" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
        </div>
        <div>
            <label for="trazado_geom" class="block text-sm font-medium text-gray-700">Trazado Geométrico (GEOMETRY)</label>
            <input type="text" id="trazado_geom" name="trazado_geom" placeholder="Ej: LINESTRING(lon lat, lon lat, ...)" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <div class="flex justify-end space-x-4">
            <button type="button" id="cancelButton" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300" style="display:none;">Cancelar</button>
            <button type="submit" id="submitButton" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Crear Ruta</button>
        </div>
    </form>
</div>

<!-- Listado de Rutas -->
<div class="bg-white p-6 rounded-lg shadow-sm">
    <h2 class="text-2xl font-bold mb-4">Listado de Rutas</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-full">
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
                        <td class="p-4"><?= htmlspecialchars($ruta['ruta_id']) ?></td>
                        <td class="p-4"><?= htmlspecialchars($ruta['nombre']) ?></td>
                        <td class="p-4"><?= htmlspecialchars($ruta['empresa_nombre']) ?></td>
                        <td class="p-4"><?= htmlspecialchars($ruta['creador_nombre']) ?></td>
                        <td class="p-4 text-right space-x-2 whitespace-nowrap">
                            <button class="edit-btn text-blue-600 hover:underline" 
                                data-ruta='<?= htmlspecialchars(json_encode($ruta), ENT_QUOTES, 'UTF-8') ?>'>
                                Editar
                            </button>
                            <form method="POST" action="../backend/admin_gestionar_rutas_process.php" class="inline-block">
                                <input type="hidden" name="ruta_id" value="<?= htmlspecialchars($ruta['ruta_id']) ?>">
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
    const form = document.getElementById('rutaForm');
    const formTitle = document.getElementById('form-title');
    const actionInput = document.getElementById('action');
    const submitButton = document.getElementById('submitButton');
    const cancelButton = document.getElementById('cancelButton');
    
    const setEditMode = (ruta) => {
        formTitle.textContent = `Editando Ruta #${ruta.ruta_id}`;
        actionInput.value = 'update';
        document.getElementById('ruta_id').value = ruta.ruta_id;
        document.getElementById('nombre').value = ruta.nombre;
        document.getElementById('empresa_id').value = ruta.empresa_id;
        document.getElementById('creado_por_usuario_id').value = ruta.creado_por_usuario_id;
        document.getElementById('descripcion').value = ruta.descripcion;
        document.getElementById('trazado_geom').value = ruta.trazado_geom;
        submitButton.textContent = 'Actualizar Ruta';
        cancelButton.style.display = 'inline-block';
        form.scrollIntoView({ behavior: 'smooth' });
    };

    const setCreateMode = () => {
        formTitle.textContent = 'Crear Nueva Ruta';
        form.reset();
        actionInput.value = 'create';
        submitButton.textContent = 'Crear Ruta';
        cancelButton.style.display = 'none';
    };

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const rutaData = JSON.parse(this.dataset.ruta);
            setEditMode(rutaData);
        });
    });

    cancelButton.addEventListener('click', setCreateMode);
});
</script>

<?php
require_once 'footer.php'; // Cierra la página
?>
