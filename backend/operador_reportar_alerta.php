<?php
session_start();
require_once 'config/db_connection.php';
require_once 'auth_guard.php'; // Protege el script

// Verificamos que sea un operador
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 2) {
    die("Acceso no autorizado.");
}

// IDs que vienen del formulario
$ruta_id = $_POST['ruta_id'] ?? 0;
$viaje_id = $_POST['viaje_id'] ?? null; // Puede ser nulo
$creado_por_usuario_id = $_POST['creado_por_usuario_id'] ?? 0;
$tipo_alerta = $_POST['tipo_alerta'] ?? 'Otro';
$descripcion = $_POST['descripcion'] ?? 'Sin descripción';
$nivel = $_POST['nivel'] ?? 4; // Por defecto 'Alto'

// Seguridad: El ID del creador DEBE ser el de la sesión
if ($creado_por_usuario_id != $_SESSION['usuario_id']) {
    die("Error de validación de usuario.");
}

// El URL para redirigir de vuelta
$redirect_url = "../frontend/operador_viaje_detalle.php?id=" . $viaje_id;

try {
    // Asume que tu amigo ya añadió 'nivel' y 'estatus_alerta'
    $sql = "INSERT INTO alertas (ruta_id, viaje_id, creado_por_usuario_id, tipo_alerta, descripcion, nivel, estatus_alerta) 
            VALUES (?, ?, ?, ?, ?, ?, 'Abierta')";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $ruta_id,
        $viaje_id,
        $creado_por_usuario_id,
        $tipo_alerta,
        $descripcion,
        $nivel
    ]);
    
    // Éxito: Redirigir con mensaje de éxito
    $message = urlencode("¡Alerta reportada con éxito!");
    header("Location: $redirect_url&status=success&message=$message");
    exit;

} catch (PDOException $e) {
    // Error: Redirigir con mensaje de error
    $message = urlencode("Error al reportar alerta: " . $e->getMessage());
    header("Location: $redirect_url&status=error&message=$message");
    exit;
}
?>