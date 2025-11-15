<?php
//session_start();
require_once 'auth_guard.php'; // Protege el script
require_once 'config/db_connection.php';

// Verificamos que sea un operador
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 2) {
    die("Acceso no autorizado.");
}

// El ID del usuario que está cambiando su propia contraseña
$usuario_id = $_SESSION['usuario_id'];
$redirect_url = '../frontend/perfil.php';

try {
    // 1. Obtener datos del formulario
    $password_actual = $_POST['password_actual'] ?? '';
    $password_nueva = $_POST['password_nueva'] ?? '';
    $password_confirmar = $_POST['password_confirmar'] ?? '';

    // 2. Validaciones
    if (empty($password_actual) || empty($password_nueva) || empty($password_confirmar)) {
        throw new Exception("Todos los campos son obligatorios.");
    }
    if (strlen($password_nueva) < 8) {
        throw new Exception("La contraseña nueva debe tener al menos 8 caracteres.");
    }
    if ($password_nueva !== $password_confirmar) {
        throw new Exception("Las contraseñas nuevas no coinciden.");
    }
    if ($password_actual === $password_nueva) {
        throw new Exception("La contraseña nueva no puede ser igual a la actual.");
    }

    // 3. Verificar la contraseña actual
    $stmt = $pdo->prepare("SELECT contrasena FROM usuarios WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch();

    if (!$usuario || !password_verify($password_actual, $usuario['contrasena'])) {
        throw new Exception("La contraseña actual es incorrecta.");
    }

    // 4. Si todo es correcto, actualizar la contraseña
    $hash_nueva_password = password_hash($password_nueva, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE usuarios SET contrasena = ? WHERE usuario_id = ?");
    $stmt->execute([$hash_nueva_password, $usuario_id]);

    // 5. Redirigir con éxito
    $message = urlencode("¡Contraseña actualizada con éxito!");
    header("Location: $redirect_url?status=success&message=$message");
    exit;

} catch (Exception $e) {
    // Redirigir con error
    $message = urlencode($e->getMessage());
    header("Location: $redirect_url?status=error&message=$message");
    exit;
}
?>