<?php
// backend/auth_guard.php
session_start();
require_once __DIR__ . '/config/jwt_utils.php';

// Definimos los roles aquí para que estén disponibles globalmente
define('ROL_GERENTE', 1);
define('ROL_TRAILERO', 2);

$token_valid = false;

if (isset($_COOKIE['jwt'])) {
    $user_data = validate_jwt($_COOKIE['jwt']);
    if ($user_data) {
        // El token es válido, reconstruimos la sesión para compatibilidad
        $_SESSION['usuario_id'] = $user_data['id'];
        $_SESSION['rol_id'] = $user_data['rol'];
        $token_valid = true;
    }
}

// 1. ¿El token no fue válido?
if (!$token_valid) {
    // Limpiamos cualquier sesión antigua y la cookie
    session_unset();
    session_destroy();
    setcookie("jwt", "", time() - 3600, "/"); // Expira la cookie

    $_SESSION['error_message'] = "Tu sesión ha expirado. Por favor, inicia sesión de nuevo.";
    header('Location: ../index.php'); 
    exit();
}

// 2. ¿La página que llama a este guardián definió un ROL_REQUERIDO?
if (defined('ROL_REQUERIDO')) {
    
    // 3. ¿El rol del usuario NO coincide con el rol requerido?
    if ($_SESSION['rol_id'] != ROL_REQUERIDO) {
        
        // No coincide.
        $_SESSION['error_message'] = "No tienes permiso para acceder a esa página.";
        
        // Lo mandamos a SU PROPIO menú para que no se pierda
        if ($_SESSION['rol_id'] == ROL_GERENTE) {
            header('Location: ../frontend/menu_admin.php');
        } elseif ($_SESSION['rol_id'] == ROL_TRAILERO) {
            header('Location: ../frontend/menu_trailero.php');
        } else {
            // Rol desconocido, mándalo al login
            header('Location: ../index.php');
        }
        exit();
    }
}
// Si no se define ROL_REQUERIDO, o si el rol coincide, se le deja pasar.
?>
