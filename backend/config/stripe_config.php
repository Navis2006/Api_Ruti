<?php
/**
 * Configuración de Stripe para Sistema de Suscripción
 * 
 * Este archivo contiene todas las configuraciones necesarias para Stripe.
 * Las keys deben ser configuradas en las variables de entorno de Render.
 */

// Modo de Stripe (test o live)
define('STRIPE_MODE', getenv('STRIPE_MODE') ?: 'test');

// API Keys de Stripe
// En modo test, usar keys que empiezan con pk_test_ y sk_test_
// En modo live, usar keys que empiezan con pk_live_ y sk_live_
define('STRIPE_PUBLISHABLE_KEY', getenv('STRIPE_PUBLISHABLE_KEY'));
define('STRIPE_SECRET_KEY', getenv('STRIPE_SECRET_KEY'));
define('STRIPE_WEBHOOK_SECRET', getenv('STRIPE_WEBHOOK_SECRET'));

// URL base de la aplicación
define('APP_URL', getenv('APP_URL') ?: 'http://localhost');

// Configuración de suscripción
define('SUBSCRIPTION_PRICE', 10.00); // Precio en MXN
define('SUBSCRIPTION_CURRENCY', 'mxn'); // Moneda
define('SUBSCRIPTION_DURATION_DAYS', 30); // Duración de la suscripción en días

// URLs de retorno después del pago
define('PAYMENT_SUCCESS_URL', APP_URL . '/frontend/payment_success.php');
define('PAYMENT_CANCEL_URL', APP_URL . '/frontend/payment_cancelled.php');

// Verificar que las keys estén configuradas
if (!STRIPE_SECRET_KEY || !STRIPE_PUBLISHABLE_KEY) {
    error_log("ERROR: Stripe API keys no están configuradas. Verifica las variables de entorno.");
}

// Cargar librería de Stripe
// Requiere: composer require stripe/stripe-php
$stripe_autoload = dirname(__DIR__, 2) . '/vendor/autoload.php';
if (file_exists($stripe_autoload)) {
    require_once $stripe_autoload;
    
    // Configurar la API key
    if (STRIPE_SECRET_KEY) {
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
    }
} else {
    error_log("ERROR: Librería de Stripe no encontrada. Ejecuta 'composer install'");
}

/**
 * Obtener configuración para el cliente JavaScript
 * Solo devuelve la publishable key (segura para frontend)
 */
function getStripePublicConfig() {
    return [
        'publishableKey' => STRIPE_PUBLISHABLE_KEY,
        'currency' => SUBSCRIPTION_CURRENCY,
        'price' => SUBSCRIPTION_PRICE,
        'mode' => STRIPE_MODE
    ];
}

/**
 * Verificar si estamos en modo de prueba
 */
function isStripeTestMode() {
    return STRIPE_MODE === 'test';
}

/**
 * Formatear precio para mostrar
 */
function formatSubscriptionPrice() {
    return '$' . number_format(SUBSCRIPTION_PRICE, 2) . ' MXN';
}
?>
