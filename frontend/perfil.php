<?php
// define('ROL_REQUERIDO', 2); // Esto ya no es necesario, 'header_operador.php' lo tiene
require_once '../backend/auth_guard.php';
require_once '../backend/config/db_connection.php';
require_once 'header_operador.php'; // Carga el encabezado de operador

// 1. OBTENER EL ID DEL USUARIO DE LA SESIÓN
$usuario_id = $_SESSION['usuario_id'];
$usuario = null;
$error_message = '';

try {
    // 2. CONSULTAR LA BASE DE DATOS
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
?>

<div x-data="{ modalOpen: false }">

    <header class="mb-8">
        <h1 class="text-3xl font-bold">Mi Perfil</h1>
        <p class="text-gray-500">Tu información personal y de contacto.</p>
    </header>

    <?php if ($error_message): ?>
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php elseif ($usuario): ?>
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-2xl mx-auto">
            <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-6">
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
                    <p class="text-lg"><?= htmlspecialchars($usuario['email'] ?? '') ?></p>
                </div>
                <div>
                    <strong class="block text-sm font-medium text-gray-500">Teléfono</strong>
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
                <button 
                    @click="modalOpen = true"
                    class="py-2 px-4 bg-gray-200 text-gray-700 font-semibold rounded-md hover:bg-gray-300">
                    Cambiar Contraseña
                </button>
                <button class="py-2 px-4 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 opacity-50 cursor-not-allowed" disabled>Editar Perfil</button>
            </div>
        </div>
    <?php endif; ?>


    <div 
        x-show="modalOpen" 
        x-cloak 
        class="fixed inset-0 z-40 overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true"
    >
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div 
                x-show="modalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="modalOpen = false"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                aria-hidden="true">
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div 
                x-show="modalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
            >
                <form method="POST" action="../backend/operador_cambiar_password.php">
                    <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario_id) ?>">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Cambiar Contraseña
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="password_actual" class="block text-sm font-medium text-gray-700">Contraseña Actual</label>
                                        <input type="password" id="password_actual" name="password_actual" required autocomplete="current-password" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label for="password_nueva" class="block text-sm font-medium text-gray-700">Contraseña Nueva</label>
                                        <input type="password" id="password_nueva" name="password_nueva" required autocomplete="new-password" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label for="password_confirmar" class="block text-sm font-medium text-gray-700">Confirmar Contraseña Nueva</label>
                                        <input type="password" id="password_confirmar" name="password_confirmar" required autocomplete="new-password" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Actualizar Contraseña
                        </button>
                        <button 
                            type="button" 
                            @click="modalOpen = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div> 

<?php
require_once 'footer_operador.php'; // Carga el pie de página
?>