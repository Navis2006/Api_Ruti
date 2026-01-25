<?php
/**
 * Pantalla de Suscripción Vencida - Administrador
 * 
 * Mostrada a administradores con opción de renovar mediante pago
 */

session_start();

// Redirigir si no está autenticado
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['empresa_id'])) {
    header("Location: ../index.php");
    exit();
}

// Verificar que sea administrador
if ($_SESSION['rol_id'] != 1) {
    header("Location: subscription_expired_operator.php");
    exit();
}

// Obtener información de suscripción GLOBAL
require_once __DIR__ . '/../backend/check_subscription.php';
require_once __DIR__ . '/../backend/config/db_connection.php';
require_once __DIR__ . '/../backend/config/stripe_config.php';

$estado_suscripcion = $_SESSION['suscripcion_info'] ?? checkSubscriptionGlobal();

// Obtener nombre del sistema desde configuración
$stmt = $pdo->prepare("SELECT valor FROM sistema_config WHERE clave = 'empresa_nombre'");
$stmt->execute();
$config = $stmt->fetch(PDO::FETCH_ASSOC);
$nombre_sistema = $config['valor'] ?? 'Dunosusa Logística';

$stripe_config = getStripePublicConfig();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renovar Suscripción - Administrador</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Lato', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    
    <div class="max-w-2xl w-full bg-white rounded-lg shadow-xl p-8 m-4">
        <!-- Ícono de advertencia -->
        <div class="flex justify-center mb-6">
            <div class="bg-red-100 rounded-full p-6">
                <svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <!-- Título -->
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-4">
            Licencia Vencida
        </h1>

        <!-- Mensaje principal -->
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
            <p class="text-sm text-red-700">
                La <strong>licencia mensual del sistema</strong> (<?= htmlspecialchars($nombre_sistema) ?>) ha vencido. 
                Renueve ahora para continuar usando el sistema.
            </p>
        </div>

        <!-- Información de la suscripción -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h3 class="font-semibold text-gray-800 mb-4">Información de la Suscripción:</h3>
            
            <div class="space-y-2">
                <?php if ($estado_suscripcion && $estado_suscripcion['suscripcion']): ?>
                <p class="text-sm text-gray-600">
                    <span class="font-medium">Fecha de vencimiento:</span> 
                    <span class="text-red-600 font-semibold">
                        <?= date('d/m/Y', strtotime($estado_suscripcion['suscripcion']['fecha_vencimiento'])) ?>
                    </span>
                </p>
                <?php endif; ?>
                
                <p class="text-sm text-gray-600">
                    <span class="font-medium">Costo de renovación:</span> 
                    <span class="text-2xl font-bold text-green-600"><?= formatSubscriptionPrice() ?></span>
                </p>
                
                <p class="text-sm text-gray-600">
                    <span class="font-medium">Vigencia:</span> 30 días
                </p>

                <?php if (isStripeTestMode()): ?>
                <div class="mt-4 bg-blue-100 border border-blue-300 rounded p-3">
                    <p class="text-xs text-blue-800">
                        ⚠️ <strong>MODO DE PRUEBA</strong> - Use tarjeta de prueba: 4242 4242 4242 4242
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="space-y-4">
            <!-- Botón de renovar -->
            <button 
                id="btn-renovar"
                onclick="iniciarPago()"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-lg transition-colors shadow-lg hover:shadow-xl flex items-center justify-center"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Renovar Ahora - <?= formatSubscriptionPrice() ?>
            </button>

            <!-- Botón de cerrar sesión -->
            <a 
                href="../backend/logout.php" 
                class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors flex items-center justify-center"
            >
                Cerrar Sesión
            </a>
        </div>

        <!-- Loading indicator -->
        <div id="loading" class="hidden mt-6">
            <div class="flex items-center justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
                <span class="ml-3 text-gray-600">Procesando...</span>
            </div>
        </div>

        <!-- Nota adicional -->
        <p class="text-xs text-gray-500 text-center mt-6">
            El pago es procesado de forma segura mediante Stripe. Sus datos bancarios están protegidos.
        </p>
    </div>

    <script>
        async function iniciarPago() {
            const btnRenovar = document.getElementById('btn-renovar');
            const loading = document.getElementById('loading');
            
            // Deshabilitar botón y mostrar loading
            btnRenovar.disabled = true;
            btnRenovar.classList.add('opacity-50', 'cursor-not-allowed');
            loading.classList.remove('hidden');
            
            try {
                // Llamar al backend para crear sesión de pago
                const response = await fetch('../backend/stripe/create_payment_session.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success && data.checkout_url) {
                    // Redirigir a Stripe Checkout
                    window.location.href = data.checkout_url;
                } else {
                    throw new Error(data.error || 'Error al crear sesión de pago');
                }
                
            } catch (error) {
                console.error('Error:', error);
                alert('Ocurrió un error al procesar el pago. Por favor, intente nuevamente.\n\nError: ' + error.message);
                
                // Rehabilitar botón
                btnRenovar.disabled = false;
                btnRenovar.classList.remove('opacity-50', 'cursor-not-allowed');
                loading.classList.add('hidden');
            }
        }
    </script>

</body>
</html>
