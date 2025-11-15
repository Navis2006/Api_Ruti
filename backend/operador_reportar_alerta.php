<?php
// session_start(); // BORRADO - ya no es necesario
require_once 'config/db_connection.php';
require_once 'auth_guard.php'; // Protege el script

// Verificamos que sea un operador
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 2) {
    die("Acceso no autorizado.");
}

// IDs que vienen del formulario
$ruta_id = $_POST['ruta_id'] ?? 0;
$viaje_id = $_POST['viaje_id'] ?? null; // El ID del viaje desde el que se reporta
$creado_por_usuario_id = $_POST['creado_por_usuario_id'] ?? 0;
$tipo_alerta = $_POST['tipo_alerta'] ?? 'Otro';
$descripcion = $_POST['descripcion'] ?? 'Sin descripción';

// Seguridad: El ID del creador DEBE ser el de la sesión
if ($creado_por_usuario_id != $_SESSION['usuario_id']) {
    die("Error de validación de usuario.");
}

// ==========================================================
//       ↓ CORRECCIÓN 1: Redirigir de vuelta al viaje ↓
// ==========================================================
$redirect_url = "../frontend/operador_viaje_detalle.php?id=" . $viaje_id;

try {
    // ==========================================================
    //       ↓ CORRECCIÓN 2: Añadir un POINT(0 0) por defecto ↓
    // ==========================================================
    // (Asumimos que 'nivel' y 'estatus_alerta' aún no existen)
    $sql = "INSERT INTO alertas (ruta_id, creado_por_usuario_id, tipo_alerta, descripcion, ubicacion_geom) 
            VALUES (?, ?, ?, ?, ST_GeomFromText(?))"; // Usamos ST_GeomFromText
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $ruta_id,
        $creado_por_usuario_id,
        $tipo_alerta,
        $descripcion,
        'POINT(0 0)' // Valor por defecto para 'ubicacion_geom'
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