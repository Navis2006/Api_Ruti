<?php
define('ROL_REQUERIDO', 2);
require_once '../backend/auth_guard.php'; 
require_once '../backend/config/db_connection.php'; 
require_once 'header_operador.php'; // Carga la cabecera del operador

// --- 1. OBTENER EL CONTEXTO DEL OPERADOR ---
$operador_id = $_SESSION['usuario_id'];
$ruta_id_actual = null;
$alertas_activas = [];

try {
    // Primero, buscamos el viaje "En Curso" o el próximo "Asignado" para saber en qué ruta está
    $stmt_context = $pdo->prepare("
        SELECT ruta_id 
        FROM viajes 
        WHERE operador_usuario_id = ? AND estado IN ('En Curso', 'Asignado')
        ORDER BY estado ASC, fecha_inicio ASC 
        LIMIT 1
    ");
    $stmt_context->execute([$operador_id]);
    $contexto_viaje = $stmt_context->fetch();
    
    if ($contexto_viaje) {
        $ruta_id_actual = $contexto_viaje['ruta_id'];
    }

    // --- 2. OBTENER LAS ALERTAS PARA ESA RUTA ---
    if ($ruta_id_actual) {
        // Buscamos alertas "Abiertas" para la ruta actual
        // (Esto asume que tu amigo ya añadió 'nivel' y 'estatus_alerta')
        $stmt_alertas = $pdo->prepare("
            SELECT a.*, CONCAT(u.nombre, ' ', u.apellidos) as creador_nombre
            FROM alertas a
            JOIN usuarios u ON a.creado_por_usuario_id = u.usuario_id
            WHERE a.ruta_id = ? AND a.estatus_alerta = 'Abierta'
            ORDER BY a.nivel DESC, a.alerta_id DESC
        ");
        $stmt_alertas->execute([$ruta_id_actual]);
        $alertas_activas = $stmt_alertas->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    // Si falla (ej: la columna 'estatus_alerta' no existe), mostramos un error amigable
    $error_db = "Error al cargar alertas. (Posiblemente la BD no está actualizada).";
    // $error_db = $e->getMessage(); // Descomenta para depurar
}
?>

<style>
    /* Clases para las tarjetas (copiadas de menu_trailero.php) */
    .card {
        background-color: #ffffff; padding: 1.5rem; border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }
    .card-empty { text-align: center; color: #6B7280; }
    
    /* Colores de borde por Nivel de Alerta */
    .card-nivel-5 { border-left: 4px solid #EF4444; } /* Rojo */
    .card-nivel-4 { border-left: 4px solid #F59E0B; } /* Amarillo */
    .card-nivel-3, .card-nivel-2, .card-nivel-1 { border-left: 4px solid #3B82F6; } /* Azul */
    
    .badge {
        display: inline-block; padding: 0.25rem 0.75rem; font-size: 0.875rem;
        font-weight: 600; border-radius: 9999px;
    }
    .badge-red { background-color: #FEE2E2; color: #991B1B; }
    .badge-yellow { background-color: #FEF3C7; color: #92400E; }
    .badge-blue { background-color: #DBEAFE; color: #1E40AF; }
</style>

<header class="mb-8">
    <h1 class="text-3xl font-bold">Mis Alertas</h1>
    <p class="text-gray-500">Alertas "Abiertas" para tu ruta actual. Márcalas como leídas cuando las hayas visto.</p>
</header>

<div class="space-y-6">
    <?php if (isset($error_db)): ?>
        <div class="card card-empty bg-red-100 text-red-700">
            <p><?= $error_db ?></p>
        </div>
    <?php elseif (!$ruta_id_actual): ?>
        <div class="card card-empty">
            <p class="text-gray-600">No tienes un viaje activo o asignado. No hay alertas para mostrar.</p>
        </div>
    <?php elseif (empty($alertas_activas)): ?>
                <div class="card card-empty">
            <p class="text-gray-600">¡Todo despejado! No tienes alertas abiertas para tu ruta actual.</p>
        </div>
    <?php else: ?>
                <?php foreach ($alertas_activas as $alerta): ?>
            <?php
                // Lógica de estilo por nivel
                $nivel = $alerta['nivel'] ?? 3; // Nivel 3 (Medio) por defecto
                $card_class = 'card-nivel-3';
                $badge_class = 'badge-blue';
                if ($nivel == 5) { $card_class = 'card-nivel-5'; $badge_class = 'badge-red'; }
                if ($nivel == 4) { $card_class = 'card-nivel-4'; $badge_class = 'badge-yellow'; }
            ?>
            <div class="card <?= $card_class ?>" data-alerta-card-id="<?= $alerta['alerta_id'] ?>">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-2">
                    <h2 class="text-xl font-bold text-gray-800">
                        Alerta #<?= htmlspecialchars($alerta['alerta_id']) ?>: <?= htmlspecialchars($alerta['tipo_alerta'] ?? 'General') ?>
                    </h2>
                    <span class="badge <?= $badge_class ?> mt-2 sm:mt-0">
                        Nivel <?= htmlspecialchars($nivel) ?>
                    </span>
                </div>
                
                <p class="mt-2 text-gray-700 text-lg"><strong>Descripción:</strong> <?= htmlspecialchars($alerta['descripcion'] ?? 'N/A') ?></p>
                <p class="text-sm text-gray-500 mt-2"><strong>Ruta:</strong> <?= htmlspecialchars($alerta['ruta_nombre'] ?? 'N/A') ?></p>
                <p class="text-sm text-gray-500"><strong>Creado por:</strong> <?= htmlspecialchars($alerta['creador_nombre'] ?? 'Sistema') ?></p>
                
                <div class="text-right mt-4">
                    <button 
                        class="btn-leida px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700"
                        data-id="<?= htmlspecialchars($alerta['alerta_id']) ?>"
                    >
                        Marcar como Leída
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <p class="text-center text-gray-500 pt-4">Si tienes una nueva incidencia, repórtala desde la página "Mi Vehículo".</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Usamos delegación de eventos en el 'main'
    const mainContainer = document.querySelector('main');

    mainContainer.addEventListener('click', function(e) {
        // Solo nos interesan los clics en el botón 'btn-leida'
        if (!e.target.classList.contains('btn-leida')) {
            return;
        }

        const button = e.target;
        const alertaId = button.dataset.id;
        const card = button.closest('.card'); // La tarjeta <div> padre

        if (!confirm('¿Estás seguro de que has leído y entendido esta alerta?')) {
            return;
        }

        // Deshabilitar el botón para evitar doble clic
        button.disabled = true;
        button.textContent = 'Procesando...';

        // Preparamos los datos para enviar
        const formData = new FormData();
        formData.append('action', 'marcar_leida'); // <-- Esta es la acción
        formData.append('alerta_id', alertaId);

        // Enviamos la petición al backend
        fetch('../backend/operador_gestionar_alerta.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // ¡Éxito! Ocultamos la tarjeta "padrote"
                card.style.transition = 'opacity 0.5s';
                card.style.opacity = '0';
                setTimeout(() => {
                    card.remove();
                }, 500); // Espera a que termine la animación
            } else {
                // Si falla, mostramos el error y reactivamos el botón
                alert('Error: ' + data.message);
                button.disabled = false;
                button.textContent = 'Marcar como Leída';
            }
        })
        .catch(error => {
            console.error('Error de Fetch:', error);
            alert('Error de conexión. Inténtalo de nuevo.');
            button.disabled = false;
            button.textContent = 'Marcar como Leída';
        });
    });
});
</script>


<?php
require_once 'footer_operador.php'; // Cierra la página
?>
