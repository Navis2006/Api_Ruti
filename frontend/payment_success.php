<?php
/**
 * Página de Pago Exitoso
 * 
 * Mostrada después de que el usuario completa el pago en Stripe
 */

session_start();

// Obtener session_id de Stripe de la URL
$session_id = $_GET['session_id'] ?? '';

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['empresa_id']) || empty($session_id)) {
    header("Location: ../index.php");
    exit();
}

require_once __DIR__ . '/../backend/config/db_connection.php';

// Obtener información de la empresa
$stmt = $pdo->prepare("SELECT nombre FROM empresas WHERE empresa_id = :empresa_id");
$stmt->bindParam(':empresa_id', $_SESSION['empresa_id'], PDO::PARAM_INT);
$stmt->execute();
$empresa = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Exitoso</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Lato', sans-serif; }
        .checkmark {
            animation: checkmark 0.8s ease-in-out;
        }
        @keyframes checkmark {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    
    <div class="max-w-2xl w-full bg-white rounded-lg shadow-xl p-8 m-4">
        <!-- Ícono de éxito -->
        <div class="flex justify-center mb-6">
            <div class="bg-green-100 rounded-full p-6 checkmark">
                <svg class="w-16 h-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>

        <!-- Título -->
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-4">
            ¡Pago Procesado Exitosamente!
        </h1>

        <!-- Mensaje principal -->
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
            <p class="text-sm text-green-700 text-center">
                Su suscripción ha sido renovada correctamente.
            </p>
        </div>

        <!-- Estado del pago -->
        <div id="payment-status" class="mb-6">
            <div class="flex items-center justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
                <span class="ml-3 text-gray-600">Verificando pago...</span>
            </div>
        </div>

        <!-- Información (se mostrará cuando se verifique) -->
        <div id="payment-info" class="hidden">
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h3 class="font-semibold text-gray-800 mb-4">Detalles de la Renovación:</h3>
                
                <div class="space-y-2">
                    <p class="text-sm text-gray-600">
                        <span class="font-medium">Empresa:</span> 
                        <?= htmlspecialchars($empresa['nombre'] ?? 'Su empresa') ?>
                    </p>
                    
                    <p class="text-sm text-gray-600">
                        <span class="font-medium">Nueva fecha de vencimiento:</span> 
                        <span id="nueva-fecha" class="text-green-600 font-semibold">-</span>
                    </p>
                    
                    <p class="text-sm text-gray-600">
                        <span class="font-medium">Días restantes:</span> 
                        <span id="dias-restantes" class="text-blue-600 font-semibold">30 días</span>
                    </p>
                </div>
            </div>

            <!-- Botón para continuar -->
            <div class="flex justify-center">
                <a 
                    href="menu_admin.php" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-lg transition-colors shadow-lg hover:shadow-xl"
                >
                    Ir al Panel de Control
                </a>
            </div>
        </div>

        <!-- Mensaje de agradecimiento -->
        <p class="text-sm text-gray-600 text-center mt-6">
            Gracias por renovar su suscripción. Puede continuar usando el sistema sin interrupciones.
        </p>
    </div>

    <script>
        // Verificar el estado del pago
        const sessionId = '<?= htmlspecialchars($session_id) ?>';
        
        async function verificarPago() {
            try {
                const response = await fetch(`../backend/stripe/verify_payment_status.php?session_id=${sessionId}`);
                const data = await response.json();
                
                if (data.success && data.paid && data.subscription_active) {
                    // Pago verificado y suscripción actualizada
                    document.getElementById('payment-status').classList.add('hidden');
                    document.getElementById('payment-info').classList.remove('hidden');
                    
                    // Calcular nueva fecha (30 días desde hoy)
                    const fechaVencimiento = new Date();
                    fechaVencimiento.setDate(fechaVencimiento.getDate() + data.dias_restantes);
                    document.getElementById('nueva-fecha').textContent = 
                        fechaVencimiento.toLocaleDateString('es-MX');
                    
                    document.getElementById('dias-restantes').textContent = 
                        data.dias_restantes + ' días';
                    
                } else if (data.paid && !data.subscription_active) {
                    // El pago se completó pero aún no se actualizó la base de datos
                    // Reintentar en 2 segundos
                    setTimeout(verificarPago, 2000);
                } else {
                    throw new Error('Pago no completado');
                }
                
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('payment-status').innerHTML = `
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <p class="text-sm text-yellow-700">
                            Estamos procesando su pago. Por favor, espere unos momentos y recargue la página.
                        </p>
                    </div>
                `;
                // Reintentar en 3 segundos
                setTimeout(verificarPago, 3000);
            }
        }
        
        // Iniciar verificación
        verificarPago();
    </script>

</body>
</html>
