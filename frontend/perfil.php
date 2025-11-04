<?php
define('ROL_REQUERIDO', 2); // 2 = Operador
require_once '../backend/auth_guard.php';
require_once '../backend/config/db_connection.php';

// 1. OBTENER EL ID DEL USUARIO DE LA SESIÓN
$usuario_id = $_SESSION['usuario_id'];
$usuario = null;
$error_message = '';

try {
    // 2. CONSULTAR LA BASE DE DATOS
    // Hacemos un JOIN con 'empresas' y 'roles' para obtener los nombres
    $sql = "SELECT u.*, e.nombre AS nombre_empresa, r.nombre_rol
            FROM usuarios u
            LEFT JOIN empresas e ON u.empresa_id = e.empresa_id
            LEFT JOIN roles r ON u.rol_id = r.rol_id
            WHERE u.usuario_id = :usuario_id";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['usuario_id' => $usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si no se encuentra el usuario (algo salió mal)
    if (!$usuario) {
        $error_message = "No se pudo cargar la información del perfil.";
    }

} catch (PDOException $e) {
    error_log("Error al cargar perfil: " . $e->getMessage());
    $error_message = "Error al conectar con la base de datos.";
}

require_once 'header_operador.php'; // Carga el encabezado de operador
?>

<header class="mb-8">
    <h1 class="text-3xl font-bold">Mi Perfil</h1>
    <p class="text-gray-500">Tu información personal y de contacto.</p>
</header>

<?php if ($error_message): ?>
    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
        <?= htmlspecialchars($error_message) ?>
    </div>
<?php elseif ($usuario): ?>
    <!-- 3. MOSTRAR LOS DATOS DINÁMICOS -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-6">
            <!-- Avatar con las iniciales del usuario -->
            <div class="w-24 h-24 rounded-full bg-blue-600 text-white flex items-center justify-center text-4xl font-bold">
                <?= htmlspecialchars(strtoupper(substr($usuario['nombre'], 0, 1) . substr($usuario['apellidos'], 0, 1))) ?>
            </div>
            
            <div class="flex-1 text-center md:text-left">
                <h2 class="text-2xl font-bold"><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']) ?></h2>
                <p class="text-lg text-gray-600"><?= htmlspecialchars($usuario['nombre_rol']) ?></p>
                <p class="text-sm text-gray-500">ID de Usuario: <?= htmlspecialchars($usuario['usuario_id']) ?></p>
            </div>
        </div>

        <hr class="my-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <strong class="block text-sm font-medium text-gray-500">Correo Electrónico</strong>
                <p class="text-lg"><?= htmlspecialchars($usuario['email']) ?></p>
            </div>
            <div>
                <strong class="block text-sm font-medium text-gray-500">Teléfono</strong>
                <!-- Usamos '??' para mostrar 'No registrado' si el campo es nulo -->
                <p class="text-lg"><?= htmlspecialchars($usuario['telefono'] ?? 'No registrado') ?></p>
            </div>
            <div>
                <strong class="block text-sm font-medium text-gray-500">Licencia de Conducir</strong>
                <p class="text-lg"><?= htmlspecialchars($usuario['licencia_conducir'] ?? 'No registrada') ?></p>
            </div>
            <div>
                <strong class="block text-sm font-medium text-gray-500">Empresa Asignada</strong>
                <p class="text-lg"><?= htmlspecialchars($usuario['nombre_empresa'] ?? 'N/A') ?></p>
            </div>
        </div>

        <div class="mt-8 border-t pt-6 text-center md:text-right space-x-2">
            <!-- Próximamente: Funcionalidad para editar -->
            <button class="py-2 px-4 bg-gray-200 text-gray-700 font-semibold rounded-md hover:bg-gray-300 opacity-50 cursor-not-allowed" disabled>Cambiar Contraseña</button>
            <button class="py-2 px-4 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 opacity-50 cursor-not-allowed" disabled>Editar Perfil</button>
        </div>
    </div>
<?php endif; ?>

<?php
require_once 'footer_operador.php'; // Carga el pie de página
?>

