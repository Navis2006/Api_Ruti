<?php
require_once 'config/db_connection.php'; // Asegúrate que la ruta sea correcta

function getTraileroData($pdo, $usuario_id) {
    try {
        $stmt = $pdo->prepare("SELECT nombre, apellidos, email, empresa_id FROM usuarios WHERE usuario_id = :usuario_id AND rol_id = 1"); // rol_id = 1 para trailero
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        error_log("Error al obtener datos del trailero: " . $e->getMessage());
        return false;
    }
}

?>