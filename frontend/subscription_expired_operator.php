<?php
/**
 * Pantalla de Suscripción Vencida - Operador
 * 
 * Mostrada a operadores cuando la empresa no ha renovado la suscripción
 */

session_start();

// Redirigir si no está autenticado
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['empresa_id'])) {
    header("Location: ../index.php");
    exit();
}

// Obtener información de suscripción GLOBAL
require_once __DIR__ . '/../backend/check_subscription.php';
require_once __DIR__ . '/../backend/config/db_connection.php';

$estado_suscripcion = $_SESSION['suscripcion_info'] ?? checkSubscriptionGlobal();

// Obtener un administrador para contacto
$admin = getAdministradorSistema();

// Obtener nombre de la empresa desde configuración
$stmt = $pdo->prepare("SELECT valor FROM sistema_config WHERE clave = 'empresa_nombre'");
$stmt->execute();
$config = $stmt->fetch(PDO::FETCH_ASSOC);
$nombre_sistema = $config['valor'] ?? 'Dunosusa Logística';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suscripción Vencida - Operador</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Lato', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    
    <div class="max-w-2xl w-full bg-white rounded-lg shadow-xl p-8 m-4">
        <!-- Ícono de advertencia -->
        <div class="flex justify-center mb-6">
            <div class="bg-yellow-100 rounded-full p-6">
                <svg class="w-16 h-16 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
        </div>

        <!-- Título -->
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-4">
            Acceso Suspendido
        </h1>

        <!-- Mensaje principal -->
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        La <strong>licencia mensual del sistema</strong> ha vencido. El acceso estará suspendido hasta que se renueve la suscripción.
                    </p>
                </div>
            </div>
        </div>

        <!-- Información -->
        <div class="space-y-4 mb-6">
            <p class="text-gray-700 text-center">
                El acceso al sistema ha sido suspendido temporalmente debido a que la suscripción mensual ha vencido.
            </p>
            
            <?php if ($estado_suscripcion && $estado_suscripcion['suscripcion']): ?>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600">
                    <strong>Fecha de vencimiento:</strong> 
                    <?= date('d/m/Y', strtotime($estado_suscripcion['suscripcion']['fecha_vencimiento'])) ?>
                </p>
            </div>
            <?php endif; ?>
            
            <p class="text-gray-700 text-center font-semibold">
                Por favor, contacte al administrador de su empresa para renovar el servicio.
            </p>
        </div>

        <!-- Información del administrador -->
        <?php if ($admin): ?>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-blue-900 mb-2">Contacto del Administrador:</h3>
            <p class="text-sm text-blue-800">
                <strong>Nombre:</strong> <?= htmlspecialchars($admin['nombre'] . ' ' . $admin['apellidos']) ?><br>
                <strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($admin['email']) ?>" class="underline hover:text-blue-600"><?= htmlspecialchars($admin['email']) ?></a>
            </p>
        </div>
        <?php endif; ?>

        <!-- Botón para cerrar sesión -->
        <div class="flex justify-center">
            <a href="../backend/logout.php" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-8 rounded-lg transition-colors">
                Cerrar Sesión
            </a>
        </div>

        <!-- Nota adicional -->
        <p class="text-xs text-gray-500 text-center mt-6">
            Si considera que esto es un error, contacte a soporte técnico.
        </p>
    </div>

</body>
</html>
