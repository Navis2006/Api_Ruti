<?php
/**
 * Página de Pago Cancelado
 * 
 * Mostrada cuando el usuario cancela el pago en Stripe
 */

session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['empresa_id'])) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Cancelado</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Lato', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    
    <div class="max-w-2xl w-full bg-white rounded-lg shadow-xl p-8 m-4">
        <!-- Ícono de información -->
        <div class="flex justify-center mb-6">
            <div class="bg-blue-100 rounded-full p-6">
                <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <!-- Título -->
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-4">
            Pago Cancelado
        </h1>

        <!-- Mensaje principal -->
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
            <p class="text-sm text-blue-700 text-center">
                El proceso de pago fue cancelado. No se realizó ningún cargo a su tarjeta.
            </p>
        </div>

        <!-- Información -->
        <div class="space-y-4 mb-8">
            <p class="text-gray-700 text-center">
                Si canceló el pago por error o desea intentar nuevamente, puede renovar su suscripción en cualquier momento.
            </p>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-sm text-yellow-800 text-center">
                    ⚠️ Recuerde que mientras la suscripción esté vencida, <strong>no podrá acceder</strong> a las funcionalidades del sistema.
                </p>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="space-y-4">
            <!-- Botón para intentar nuevamente -->
            <a 
                href="subscription_expired_admin.php" 
                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-lg transition-colors shadow-lg hover:shadow-xl flex items-center justify-center"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Intentar Nuevamente
            </a>

            <!-- Botón de cerrar sesión -->
            <a 
                href="../backend/logout.php" 
                class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors flex items-center justify-center"
            >
                Cerrar Sesión
            </a>
        </div>

        <!-- Nota de ayuda -->
        <div class="mt-8 bg-gray-50 rounded-lg p-4">
            <h3 class="font-semibold text-gray-800 mb-2">¿Necesita ayuda?</h3>
            <p class="text-sm text-gray-600">
                Si tiene problemas con el proceso de pago o necesita asistencia, puede contactar a soporte técnico.
            </p>
        </div>
    </div>

</body>
</html>
