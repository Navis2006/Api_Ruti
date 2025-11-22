<?php
/**
 * Verificar Estado de Pago
 * 
 * Endpoint para verificar si un pago (session) fue completado exitosamente
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['empresa_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autenticado']);
    exit();
}

require_once __DIR__ . '/../config/stripe_config.php';
require_once __DIR__ . '/../check_subscription.php';

$session_id = $_GET['session_id'] ?? '';

if (empty($session_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'session_id requerido']);
    exit();
}

try {
    // Recuperar la sesión de Stripe
    $session = \Stripe\Checkout\Session::retrieve($session_id);
    
    // Verificar estado del pago
    $payment_status = $session->payment_status; // 'paid', 'unpaid', 'no_payment_required'
    
    if ($payment_status === 'paid') {
        // Verificar que la suscripción se haya actualizado en la base de datos
        $estado_suscripcion = checkSubscription($_SESSION['empresa_id']);
        
        echo json_encode([
            'success' => true,
            'paid' => true,
            'subscription_active' => $estado_suscripcion['activa'],
            'dias_restantes' => $estado_suscripcion['dias_restantes']
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'paid' => false,
            'payment_status' => $payment_status
        ]);
    }
    
} catch (\Stripe\Exception\ApiErrorException $e) {
    error_log('Error al verificar pago: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al verificar estado del pago'
    ]);
}
?>
