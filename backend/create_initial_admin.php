<?php
// Script para crear un usuario administrador inicial
// ¡USAR SÓLO UNA VEZ PARA INICIALIZAR LA BASE DE DATOS EN DESARROLLO!
// No debe ser accesible públicamente en producción.

require_once 'config/db_connection.php'; // Incluye la conexión a la base de datos

$nombre = "Super";
$apellidos = "Administrador";
$email = "admin@empresa.com"; // Correo electrónico del administrador
$contrasena_plana = "admin123"; // Contraseña en texto plano para el hash
$empresa_id = 1; // ID de la empresa a la que pertenecerá el admin (debe existir en tu tabla 'empresas')
$rol_id = 1; // ID del rol de administrador (debe existir en tu tabla 'roles', asumimos 2 para admin)

echo "<h2>Intentando crear usuario administrador inicial...</h2>";

try {
    // Verificar si el correo ya existe para evitar duplicados
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
    $stmt_check->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt_check->execute();
    if ($stmt_check->fetchColumn() > 0) {
        echo "<p style='color: orange;'>El usuario con el correo '{$email}' ya existe. No se ha creado un nuevo administrador.</p>";
    } else {
        // Hashear la contraseña
        $contrasena_hash = password_hash($contrasena_plana, PASSWORD_DEFAULT);

        // Insertar el usuario
        $stmt_insert = $pdo->prepare("
            INSERT INTO usuarios (nombre, apellidos, email, contrasena_hash, empresa_id, rol_id, fecha_creacion)
            VALUES (:nombre, :apellidos, :email, :contrasena_hash, :empresa_id, :rol_id, NOW())
        ");

        $stmt_insert->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt_insert->bindParam(':apellidos', $apellidos, PDO::PARAM_STR);
        $stmt_insert->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt_insert->bindParam(':contrasena_hash', $contrasena_hash, PDO::PARAM_STR);
        $stmt_insert->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':rol_id', $rol_id, PDO::PARAM_INT);

        if ($stmt_insert->execute()) {
            echo "<p style='color: green;'>¡Usuario administrador inicial '{$email}' creado exitosamente!</p>";
            echo "<p style='color: green;'>Contraseña: '{$contrasena_plana}'</p>";
            echo "<p style='color: green;'>Ahora puedes iniciar sesión con estas credenciales.</p>";
        } else {
            echo "<p style='color: red;'>Error al crear el usuario administrador.</p>";
        }
    }
} catch (\PDOException $e) {
    echo "<p style='color: red;'>Error en la base de datos: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<p>Fin del script de creación de administrador inicial.</p>";
?>