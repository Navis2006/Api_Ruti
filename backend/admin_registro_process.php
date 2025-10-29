<?php
require_once 'auth_guard.php';
require_once 'config/db_connection.php';

// Definimos la URL de redirección para las acciones de formulario
$redirect_url = '../frontend/admin_registro.php';

// Para las acciones de fetch, preparamos una respuesta JSON
header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Acción no válida.'];

try {
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
    $usuario_id = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);

    switch ($action) {
        case 'create':
        case 'update':
            // Recoger todos los datos del formulario, incluyendo el nuevo campo 'estatus'
            $nombre = trim((string) filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS));
            $apellidos = trim((string) filter_input(INPUT_POST, 'apellidos', FILTER_SANITIZE_SPECIAL_CHARS));
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $empresa_id = filter_input(INPUT_POST, 'empresa_id', FILTER_VALIDATE_INT);
            $rol_id = filter_input(INPUT_POST, 'rol_id', FILTER_VALIDATE_INT);
            $estatus = filter_input(INPUT_POST, 'estatus', FILTER_SANITIZE_SPECIAL_CHARS);
            $contrasena = isset($_POST['contrasena']) ? trim((string) $_POST['contrasena']) : '';

            if ($nombre === '' || $apellidos === '' || !$email || !$empresa_id || !$rol_id) {
                throw new Exception("Todos los campos excepto la contraseña son obligatorios.");
            }
            
            if ($action === 'create') {
                if ($contrasena === '') throw new Exception("La contraseña es obligatoria para crear un usuario.");
                $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellidos, email, empresa_id, rol_id, estatus, contrasena_hash) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $apellidos, $email, $empresa_id, $rol_id, $estatus, $contrasena_hash]);
            } else { // update
                if (!$usuario_id) throw new Exception("ID de usuario no válido.");
                if ($contrasena !== '') {
                    $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, apellidos = ?, email = ?, empresa_id = ?, rol_id = ?, estatus = ?, contrasena_hash = ? WHERE usuario_id = ?");
                    $stmt->execute([$nombre, $apellidos, $email, $empresa_id, $rol_id, $estatus, $contrasena_hash, $usuario_id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, apellidos = ?, email = ?, empresa_id = ?, rol_id = ?, estatus = ? WHERE usuario_id = ?");
                    $stmt->execute([$nombre, $apellidos, $email, $empresa_id, $rol_id, $estatus, $usuario_id]);
                }
            }
            // Si la operación de formulario es exitosa, redirigir
            header('Location: ' . $redirect_url . '?status=success');
            exit();

        case 'update_status':
            $estatus = filter_input(INPUT_POST, 'estatus', FILTER_SANITIZE_SPECIAL_CHARS);
            if (!$usuario_id || !in_array($estatus, ['activo', 'inactivo'])) {
                throw new Exception("Datos no válidos para actualizar el estatus.");
            }
            
            $stmt = $pdo->prepare("UPDATE usuarios SET estatus = ? WHERE usuario_id = ?");
            $stmt->execute([$estatus, $usuario_id]);
            
            $response = ['status' => 'success', 'message' => 'Estatus actualizado correctamente.'];
            break;

        default:
            throw new Exception("Acción no reconocida.");
    }

} catch (Exception $e) {
    // Si la acción era de formulario, redirigir con error
    if (isset($_POST['action']) && ($_POST['action'] === 'create' || $_POST['action'] === 'update')) {
        header('Location: ' . $redirect_url . '?status=error&message=' . urlencode($e->getMessage()));
        exit();
    }
    // Si era una acción fetch (update_status), preparar respuesta JSON de error
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

// Para la acción 'update_status', imprimir la respuesta JSON
echo json_encode($response);
exit();