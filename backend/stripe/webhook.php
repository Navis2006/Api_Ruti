<?php
/**
 * Webhook de Stripe (GLOBAL)
 * 
 * Este archivo recibe notificaciones de Stripe cuando ocurren eventos importantes
 * Ahora maneja la suscripción GLOBAL del sistema, no por empresa
 */

require_once __DIR__ . '/../config/stripe_config.php';
require_once __DIR__ . '/../config/db_connection.php';
require_once __DIR__ . '/../check_subscription.php';

// Obtener el payload del webhook
$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

// Log para debugging
error_log("Webhook recibido de Stripe");

try {
    // Verificar la firma del webhook (seguridad)
    $event = \Stripe\Webhook::constructEvent(
        $payload,
        $sig_header,
        STRIPE_WEBHOOK_SECRET
    );
    
    error_log("Evento verificado: " . $event->type);
    
    // Manejar el evento según su tipo
    switch ($event->type) {
        case 'checkout.session.completed':
            // Pago completado exitosamente
            $session = $event->data->object;
            
            error_log("Pago completado - Session ID: " . $session->id);
            
            // Obtener metadata
            $usuario_id = (int)($session->metadata->usuario_id ?? 0);
            $monto = $session->amount_total / 100; // Convertir de centavos a pesos
            
            // Crear nueva suscripción GLOBAL
            $nueva_suscripcion_id = crearNuevaSuscripcionGlobal(
                $monto,
                $usuario_id,
                $session->payment_intent,
                $session->id
            );
            
            if ($nueva_suscripcion_id) {
                error_log("Nueva suscripción GLOBAL creada: ID " . $nueva_suscripcion_id);
                
                // Registrar en log
                registrarLogPagoGlobal(
                    $usuario_id,
                    $session->id,
                    $monto,
                    'completado',
                    'Pago procesado exitosamente - Suscripción global renovada'
                );
                
                http_response_code(200);
            } else {
                error_log("Error al crear nueva suscripción global");
                registrarLogPagoGlobal(
                    $usuario_id,
                    $session->id,
                    $monto,
                    'fallido',
                    'Error al crear suscripción global en base de datos'
                );
            }
            break;
            
        case 'checkout.session.expired':
            // Sesión de pago expiró sin completarse
            $session = $event->data->object;
            $usuario_id = (int)($session->metadata->usuario_id ?? 0);
            
            registrarLogPagoGlobal(
                $usuario_id,
                $session->id,
                0,
                'cancelado',
                'Sesión de pago expirada'
            );
            break;
            
        default:
            error_log("Evento no manejado: " . $event->type);
    }
    
    http_response_code(200);
    
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    // Firma inválida
    error_log('Webhook error: Firma inválida - ' . $e->getMessage());
    http_response_code(400);
    exit();
    
} catch (Exception $e) {
    error_log('Webhook error: ' . $e->getMessage());
    http_response_code(500);
    exit();
}
?>
