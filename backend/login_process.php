<?php

session_start();

require_once 'config/db_connection.php'; // Asegúrate que la ruta sea correcta



// Definir los roles para una mejor legibilidad y mantenimiento, con los IDs correctos

define('ROL_GERENTE', 1);   // Administrador/Gerente

define('ROL_TRAILERO', 2); // Trailero



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);

    $contrasena = $_POST['contrasena'];



    if (empty($email) || empty($contrasena)) {

        $_SESSION['error_message'] = "Por favor, ingresa tu correo y contraseña.";

        header("Location: ../index.php");

        exit();

    }



    try {

        $stmt = $pdo->prepare("SELECT usuario_id, contrasena_hash, rol_id, empresa_id FROM usuarios WHERE email = :email");

        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);



        if ($user) {



                    if (password_verify($contrasena, $user['contrasena_hash'])) {



                        // Autenticación exitosa



                        require_once 'config/jwt_utils.php';



                        $token = generate_jwt($user['usuario_id'], $user['rol_id']);



        



                        // Guardar el token en una cookie HttpOnly



                        setcookie("jwt", $token, [



                            'expires' => time() + 3600, // 1 hora



                            'path' => '/',



                            //'domain' => 'localhost', // Ajusta tu dominio



                            'secure' => false, // Poner a true en producción con HTTPS



                            'httponly' => true,



                            'samesite' => 'Lax'



                        ]);



        



                        $_SESSION['usuario_id'] = $user['usuario_id'];



                        $_SESSION['rol_id'] = $user['rol_id'];



                        $_SESSION['empresa_id'] = $user['empresa_id'];



                        



                        // Redirigir según el rol detectado



                        if ($user['rol_id'] == ROL_GERENTE) {



                            header("Location: ../frontend/menu_admin.php"); // Redirige al menú del administrador (gerente)



                            exit();



                        } elseif ($user['rol_id'] == ROL_TRAILERO) {



                            header("Location: ../frontend/menu_trailero.php"); // Redirige al menú del trailero



                            exit();



                        } else {



                            // Si el rol no está definido o es desconocido



                            $_SESSION['error_message'] = "Tu cuenta tiene un rol no reconocido. Contacta al administrador.";



                            session_unset();



                            session_destroy();



                            header("Location: ../index.php");



                            exit();



                        }

            } else {

                $_SESSION['error_message'] = "Correo o contraseña incorrectos.";

                header("Location: ../index.php");

                exit();

            }

        } else {

            $_SESSION['error_message'] = "Correo o contraseña incorrectos.";

            header("Location: ../index.php");

            exit();

        }



    } catch (\PDOException $e) {

        error_log("Error de login: " . $e->getMessage());

        $_SESSION['error_message'] = "Ocurrió un error en el servidor. Intenta de nuevo más tarde.";

        header("Location: ../index.php");

        exit();

    }

} else {

    header("Location: ../index.php");

    exit();

}

?>