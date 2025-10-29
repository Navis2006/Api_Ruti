<?php
// backend/auth_guard.php
session_start();

// Definimos los roles aquí para que estén disponibles globalmente
define('ROL_GERENTE', 1);
define('ROL_TRAILERO', 2);

// 1. ¿Está logueado? (Revisamos ID y Rol)
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol_id'])) {
    $_SESSION['error_message'] = "Por favor, inicia sesión para continuar.";
    // Corregido: Redirige a index.php, no a login.php
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
