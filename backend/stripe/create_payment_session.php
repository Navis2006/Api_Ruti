<?php
/**
 * Crear Sesión de Pago en Stripe
 * 
 * Este endpoint crea una sesión de Stripe Checkout para renovar la suscripción
 */

session_start();
header('Content-Type: application/json');

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['empresa_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'No autenticado'
    ]);
    exit();
}

// Verificar que el usuario sea administrador (rol_id = 1)
if ($_SESSION['rol_id'] != 1) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error' => 'Solo los administradores pueden renovar la suscripción'
    ]);
    exit();
}

require_once __DIR__ . '/../config/stripe_config.php';
require_once __DIR__ . '/../config/db_connection.php';
require_once __DIR__ . '/../check_subscription.php';

try {
    // Obtener información de la empresa
    $stmt = $pdo->prepare("SELECT nombre FROM empresas WHERE empresa_id = :empresa_id");
    $stmt->bindParam(':empresa_id', $_SESSION['empresa_id'], PDO::PARAM_INT);
    $stmt->execute();
    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$empresa) {
        throw new Exception('Empresa no encontrada');
    }
    
    // Crear sesión de Stripe Checkout
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => SUBSCRIPTION_CURRENCY,
                'product_data' => [
                    'name' => 'Renovación Mensual - Software CEDIS',
                    'description' => 'Suscripción mensual para ' . $empresa['nombre'],
                ],
                'unit_amount' => (int)(SUBSCRIPTION_PRICE * 100), // Stripe usa centavos
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => PAYMENT_SUCCESS_URL . '?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => PAYMENT_CANCEL_URL,
        'client_reference_id' => (string)$_SESSION['empresa_id'],
        'metadata' => [
            'empresa_id' => $_SESSION['empresa_id'],
            'usuario_id' => $_SESSION['usuario_id'],
            'tipo' => 'renovacion_suscripcion'
        ],
    ]);
    
    // Registrar en log el intento de pago
    registrarLogPago(
        $_SESSION['empresa_id'],
        $_SESSION['usuario_id'],
        $checkout_session->id,
        SUBSCRIPTION_PRICE,
        'iniciado',
        'Sesión de pago creada'
    );
    
    // Devolver URL de checkout
    echo json_encode([
        'success' => true,
        'checkout_url' => $checkout_session->url,
        'session_id' => $checkout_session->id
    ]);
    
} catch (\Stripe\Exception\ApiErrorException $e) {
    error_log('Error de Stripe: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al crear sesión de pago: ' . $e->getMessage()
    ]);
    
} catch (Exception $e) {
    error_log('Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
