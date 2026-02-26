<?php
    define('ROL_REQUERIDO', 2);
// require_once '../backend/auth_guard.php'; // Descomentar si es necesario
require_once 'header_operador.php'; // Carga la cabecera del operador
?>

<header class="mb-8">
    <h1 class="text-3xl font-bold">Contacto y Soporte</h1>
    <p class="text-gray-500">Si tienes alguna pregunta, problema o necesitas asistencia, no dudes en contactarnos.</p>
</header>

<div class="space-y-6">
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h2 class="text-xl font-bold mb-2 text-red-600">Contacto de Emergencia 24/7</h2>
        <p class="text-lg"><strong>Teléfono:</strong> <a href="tel:+523398765432" class="text-red-600 hover:underline">+52 33 9876 5432</a></p>
        <p class="text-lg"><strong>Correo:</strong> <a href="mailto:emergencias@empresa.com" class="text-red-600 hover:underline">emergencias@empresa.com</a></p>
        <p class="text-sm text-gray-600 mt-2">Para situaciones críticas en ruta o cualquier urgencia.</p>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h2 class="text-xl font-bold mb-2 text-red-600">Soporte General</h2>
        <p class="text-gray-700"><strong>Horario:</strong> Lunes a Viernes, 9:00 AM - 6:00 PM</p>
        <p class="text-lg"><strong>Teléfono:</strong> <a href="tel:+523355551122" class="text-red-600 hover:underline">+52 33 5555 1122</a></p>
        <p class="text-lg"><strong>Correo:</strong> <a href="mailto:soporte@empresa.com" class="text-red-600 hover:underline">soporte@empresa.com</a></p>
        <p class="text-sm text-gray-600 mt-2">Para preguntas sobre la plataforma, rutas, tráilers o perfil.</p>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h2 class="text-xl font-bold mb-2 text-red-600">Soporte Técnico</h2>
        <p class="text-gray-700"><strong>Horario:</strong> Lunes a Viernes, 9:00 AM - 6:00 PM</p>
        <p class="text-lg"><strong>Teléfono:</strong> <a href="tel:+523344443322" class="text-red-600 hover:underline">+52 33 4444 3322</a></p>
        <p class="text-lg"><strong>Correo:</strong> <a href="mailto:tecnico@empresa.com" class="text-red-600 hover:underline">tecnico@empresa.com</a></p>
        <p class="text-sm text-gray-600 mt-2">Para problemas con la aplicación, errores o fallas técnicas.</p>
    </div>

    <p class="text-center text-gray-500 pt-4">Tu seguridad y la eficiencia de tus viajes son nuestra prioridad.</p>
</div>

<?php
require_once 'footer_operador.php'; // Cierra la página
?>
