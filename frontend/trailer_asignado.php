<?php
// define('ROL_REQUERIDO', 2); // Esto ya no es necesario, 'header_operador.php' lo tiene
require_once '../backend/auth_guard.php'; 
require_once '../backend/config/db_connection.php'; 
require_once 'header_operador.php'; // Carga la cabecera del operador

// --- 1. OBTENER EL VEHÍCULO REAL DEL OPERADOR ---
// La mejor forma es buscar el vehículo asignado a su viaje "En Curso" o el próximo "Asignado"
$operador_id = $_SESSION['usuario_id'];
$vehiculo = null;
$viaje_id_asociado = null;

try {
    $stmt = $pdo->prepare("
        SELECT 
            v.vehiculo_id, v.nombre, v.placa, v.tipo, v.estatus, 
            v.altura_metros, v.ancho_metros, v.largo_metros, v.peso_toneladas, 
            e.nombre as empresa_nombre,
            j.viaje_id
        FROM vehiculos v
        JOIN viajes j ON v.vehiculo_id = j.vehiculo_id
        JOIN empresas e ON v.empresa_id = e.empresa_id
        WHERE j.operador_usuario_id = ? 
        AND j.estado IN ('En Curso', 'Asignado')
        ORDER BY j.estado ASC, j.fecha_inicio ASC -- 'En Curso' primero, luego el más próximo
        LIMIT 1
    ");
    $stmt->execute([$operador_id]);
    $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($vehiculo) {
        $viaje_id_asociado = $vehiculo['viaje_id'];
    }

} catch (PDOException $e) {
    // Manejar el error de consulta
    $error_db = "Error al consultar el vehículo: " . $e->getMessage();
}

// Lista de tipos de incidencia para el formulario
$tipos_incidencia = ['Mecánica (Motor)', 'Neumáticos', 'Eléctrica (Luces)', 'Carrocería', 'Documentos Faltantes', 'Otro'];
?>

<!-- ========================================================== -->
<!--       ↓ INICIO DEL CONTENIDO DE LA PÁGINA ↓          -->
<!-- ========================================================== -->

<!-- 
    Contenedor principal de Alpine.js
    - 'modalOpen': controla si el formulario de reporte se muestra
-->
<div x-data="{ modalOpen: false }">

    <header class="mb-8">
        <h1 class="text-3xl font-bold">Mi Vehículo Asignado</h1>
        <p class="text-gray-500">Información detallada del vehículo para tu viaje actual.</p>
    </header>

    <?php if (isset($error_db)): ?>
        <!-- Mensaje de Error si la consulta falla -->
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-md" role="alert">
            <p class="font-bold">Error de Base de Datos</p>
            <p><?= htmlspecialchars($error_db) ?></p>
        </div>

    <?php elseif ($vehiculo): ?>
    
        <!-- Tarjeta Principal de Información del Vehículo -->
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-2xl mx-auto">
            
            <div class="flex flex-col md:flex-row items-center md:items-start">
                <!-- Icono de Camión (Padrote) -->
                <div class="flex-shrink-0 mb-4 md:mb-0 md:mr-6">
                    <svg class="w-24 h-24 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8zM13 16h2m-2-7h2m0 0H9.692l-2-4H4.382l-2 4H13zM19 16V6a1 1 0 00-1-1h-1l-2 4h3v7z"></path></svg>
                    

[Image of un ícono de camión de carga]

                </div>

                <!-- Detalles Principales -->
                <div class="flex-1 text-center md:text-left">
                    <h2 class="text-2xl font-bold text-red-600">
                        <?= htmlspecialchars($vehiculo['nombre'] ?? 'N/A') ?>
                    </h2>
                    <p class="text-3xl font-bold font-mono text-gray-800 my-2"><?= htmlspecialchars($vehiculo['placa'] ?? 'N/A') ?></p>
                    <p class="text-lg font-medium text-gray-600"><?= htmlspecialchars($vehiculo['tipo'] ?? 'N/A') ?></p>
                </div>
            </div>

            <!-- Separador -->
            <hr class="my-6">

            <!-- Especificaciones (Dimensiones y Peso) -->
            <h3 class="text-lg font-semibold mb-3 text-gray-700">Especificaciones</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div>
                    <p class="text-sm text-gray-500">Altura</p>
                    <p class="text-lg font-medium"><?= htmlspecialchars($vehiculo['altura_metros'] ?? 'N/A') ?> m</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Ancho</p>
                    <p class="text-lg font-medium"><?= htmlspecialchars($vehiculo['ancho_metros'] ?? 'N/A') ?> m</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Largo</p>
                    <p class="text-lg font-medium"><?= htmlspecialchars($vehiculo['largo_metros'] ?? 'N/A') ?> m</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Peso</p>
                    <p class="text-lg font-medium"><?= htmlspecialchars($vehiculo['peso_toneladas'] ?? 'N/A') ?> t</p>
                </div>
            </div>
            
            <!-- Empresa -->
            <div class="mt-6 border-t pt-4">
                <p class="text-sm text-gray-500">Empresa Propietaria</p>
                <p class="text-lg font-medium"><?= htmlspecialchars($vehiculo['empresa_nombre'] ?? 'N/A') ?></p>
            </div>

            <!-- Botones de Acción -->
            <div class="mt-8 flex flex-col sm:flex-row gap-4">
                <!-- Botón para abrir el modal -->
                <button 
                    @click="modalOpen = true"
                    class="w-full px-6 py-3 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Reportar Incidencia
                </button>
                <a href="historial_mantenimiento.php?vehiculo_id=<?= htmlspecialchars($vehiculo['vehiculo_id']) ?>" class="w-full text-center px-6 py-3 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition-colors">
                    Ver Historial de Mantenimiento
                </a>
            </div>
        </div>

    <?php else: ?>
        <!-- Mensaje si no hay vehículo asignado -->
        <div class="bg-white p-10 rounded-lg shadow-sm max-w-2xl mx-auto text-center">
            <h2 class="text-2xl font-bold text-gray-700 mb-2">No hay vehículo asignado</h2>
            <p class="text-gray-500">No tienes un vehículo asociado a un viaje "En Curso" o "Asignado". Si crees que esto es un error, contacta a tu administrador.</p>
        </div>
    <?php endif; ?>

    
    <!-- ========================================================== -->
    <!--       ↓ MODAL PARA REPORTAR INCIDENCIA ↓               -->
    <!-- ========================================================== -->
    <div 
        x-show="modalOpen" 
        x-cloak 
        class="fixed inset-0 z-40 overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true"
    >
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <!-- Fondo (Overlay) -->
            <div 
                x-show="modalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="modalOpen = false"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                aria-hidden="true">
            </div>

            <!-- Contenedor del Modal -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div 
                x-show="modalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
            >
                <form method="POST" action="../backend/operador_reportar_incidencia.php">
                    <!-- Pasamos los IDs necesarios al backend -->
                    <input type="hidden" name="vehiculo_id" value="<?= htmlspecialchars($vehiculo['vehiculo_id'] ?? '') ?>">
                    <input type="hidden" name="viaje_id" value="<?= htmlspecialchars($viaje_id_asociado ?? '') ?>">
                    <input type="hidden" name="operador_id" value="<?= htmlspecialchars($operador_id) ?>">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Reportar Incidencia del Vehículo
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="tipo_incidencia" class="block text-sm font-medium text-gray-700">Tipo de Incidencia</label>
                                        <select id="tipo_incidencia" name="tipo_incidencia" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                                            <option value="">-- Seleccione un tipo --</option>
                                            <?php foreach ($tipos_incidencia as $tipo): ?>
                                                <option value="<?= $tipo ?>"><?= $tipo ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción (¿Qué pasó?)</label>
                                        <textarea id="descripcion" name="descripcion" rows="3" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500" placeholder="Ej: La luz trasera izquierda no enciende..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Enviar Reporte
                        </button>
                        <button 
                            type="button" 
                            @click="modalOpen = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ========================================================== -->
    <!--       ↑ FIN DEL MODAL ↑                                -->
    <!-- ========================================================== -->

</div> 

<?php
require_once 'footer_operador.php'; // Cierra la página
?>
