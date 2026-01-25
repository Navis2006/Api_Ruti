<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol_id'])) {
    // Si no está logueado, redirigir al index (login)
    header("Location: ../index.php");
    exit();
}

// Opcional: Si quieres asegurar que solo los traileros (rol_id = 1) accedan a estas páginas
// Si el rol es diferente de trailero, redirigir
if ($_SESSION['rol_id'] != 1) { // Asumiendo que 1 es el rol para traileros
    $_SESSION['error_message'] = "No tienes permiso para acceder a esta sección.";
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit();
}

// Si la sesión es válida y el rol es correcto, el script continúa.
?>