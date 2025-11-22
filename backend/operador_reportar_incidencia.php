<?php
// require_once 'session_start();' // No es necesario, auth_guard ya lo tiene
require_once 'config/db_connection.php';
require_once 'auth_guard.php'; // Protege el script

// Verificamos que sea un operador
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 2) {
    die("Acceso no autorizado.");
}

// IDs que vienen del formulario del modal
$vehiculo_id = $_POST['vehiculo_id'] ?? 0;
$viaje_id = $_POST['viaje_id'] ?? 0;
$operador_id = $_POST['operador_id'] ?? 0;
$tipo_incidencia = $_POST['tipo_incidencia'] ?? 'Otro';
$descripcion = $_POST['descripcion'] ?? 'Sin descripción';

// Seguridad: El ID del operador DEBE ser el de la sesión
if ($operador_id != $_SESSION['usuario_id']) {
    die("Error de validación de usuario.");
}

// Buscamos la ruta_id asociada a este viaje
$ruta_id = $pdo->prepare("SELECT ruta_id FROM viajes WHERE viaje_id = ?");
$ruta_id->execute([$viaje_id]);
$ruta_id = $ruta_id->fetchColumn();

if (!$ruta_id) {
    // Si no hay ruta_id, no podemos crear la alerta
    $ruta_id = 1; // O un ID de ruta "general" por si acaso
}

// El URL para redirigir de vuelta
$redirect_url = "../frontend/trailer_asignado.php";

try {
    // Creamos una NUEVA ALERTA en la tabla 'alertas'
    // Asumimos un Nivel 4 (Alto) para incidencias mecánicas
    $sql = "INSERT INTO alertas (ruta_id, creado_por_usuario_id, descripcion, tipo_alerta, nivel, estatus_alerta, ubicacion_geom) 
            VALUES (?, ?, ?, ?, 4, 'Abierta', ST_GeomFromText(?))";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $ruta_id,
        $operador_id,
        $descripcion,
        $tipo_incidencia,
        'POINT(0 0)' // Ubicación por defecto
    ]);
    
    // Éxito: Redirigir con mensaje de éxito
    $message = urlencode("¡Incidencia reportada con éxito!");
    header("Location: $redirect_url?status=success&message=$message");
    exit;

} catch (PDOException $e) {
    // Error: Redirigir con mensaje de error
    $message = urlencode("Error al reportar incidencia: " . $e->getMessage());
    header("Location: $redirect_url?status=error&message=$message");
    exit;
}
?>