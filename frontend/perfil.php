<?php
    define('ROL_REQUERIDO', 2);
// require_once '../backend/auth_guard.php'; // Descomentar si es necesario
require_once 'header_operador.php'; // Carga la cabecera del operador

// Aquí deberías tener lógica PHP para obtener los datos del perfil
// $usuario = getUserProfile($pdo, $_SESSION['usuario_id']);
?>

<header class="mb-8">
    <h1 class="text-3xl font-bold">Mi Perfil</h1>
    <p class="text-gray-500">Información personal y de contacto. Puedes actualizar algunos datos aquí.</p>
</header>

<div class="bg-white p-6 rounded-lg shadow-sm max-w-2xl mx-auto">
    <div class="flex flex-col md:flex-row items-center md:items-start space-y-4 md:space-y-0 md:space-x-6">
        <!-- Reemplaza 'img/avatar_default.png' con una ruta de avatar válida o una variable de PHP -->
        <img src="https://placehold.co/150x150/E2E8F0/334155?text=Perfil" alt="Avatar del Tráilero" class="w-32 h-32 rounded-full border-4 border-gray-200 object-cover">
        
        <div class="flex-1 text-center md:text-left">
            <h2 class="text-2xl font-bold text-blue-600">Juan Pérez García</h2>
            <p class="text-gray-600">ID Tráilero: TR00123</p>
            <p class="text-gray-600">Empresa: Transportes del Bajío S.A. de C.V.</p>
            <p class="text-sm text-gray-500">Antigüedad: 5 años</p>
        </div>
    </div>

    <div class="mt-6 border-t pt-6 space-y-4">
        <div>
            <p class="text-sm font-medium text-gray-500">Correo Electrónico</p>
            <p class="text-lg text-gray-800">juan.perez@empresa.com</p>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Teléfono</p>
            <p class="text-lg text-gray-800">+52 33 1234 5678</p>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Licencia de Conducir</p>
            <p class="text-lg text-gray-800">Tipo E - #123456789 (Vence: 2025-12-31)</p>
        </div>
    </div>

    <div class="mt-6 border-t pt-6 flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
        <button class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Editar Perfil</button>
        <button class="px-5 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">Cambiar Contraseña</button>
    </div>
</div>

<p class="text-center text-gray-500 pt-8">Para cambios en información crítica como licencia o empresa, contacta a tu administrador.</p>


<?php
require_once 'footer_operador.php'; // Cierra la página
?>
