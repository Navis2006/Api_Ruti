<?php
/**
 * Verificación de Estado de Suscripción GLOBAL
 * 
 * Funciones para verificar si el SISTEMA tiene suscripción activa
 * (No por empresa, sino global para todo Dunosusa)
 */

require_once __DIR__ . '/config/db_connection.php';

/**
 * Verificar si el SISTEMA tiene suscripción activa
 * 
 * @return array ['activa' => bool, 'suscripcion' => array|null, 'dias_restantes' => int]
 */
function checkSubscriptionGlobal() {
    global $pdo;
    
    try {
        // Buscar la suscripción más reciente del SISTEMA (no por empresa)
        $stmt = $pdo->prepare("
            SELECT 
                suscripcion_id,
                fecha_inicio,
                fecha_vencimiento,
                monto_pagado,
                estado,
                stripe_payment_id,
                pagado_por_usuario_id,
                DATEDIFF(fecha_vencimiento, NOW()) as dias_restantes
            FROM suscripcion_sistema
            ORDER BY suscripcion_id DESC
            LIMIT 1
        ");
        
        $stmt->execute();
        $suscripcion = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Si no existe suscripción, retornar inactiva
        if (!$suscripcion) {
            return [
                'activa' => false,
                'suscripcion' => null,
                'dias_restantes' => 0,
                'mensaje' => 'No se encontró suscripción del sistema'
            ];
        }
        
        // Verificar si la fecha de vencimiento ya pasó
        $fecha_vencimiento = new DateTime($suscripcion['fecha_vencimiento']);
        $fecha_actual = new DateTime();
        
        $esta_activa = ($fecha_actual < $fecha_vencimiento) && ($suscripcion['estado'] === 'activa');
        
        // Si está vencida pero el estado aún dice 'activa', actualizarla
        if (!$esta_activa && $suscripcion['estado'] === 'activa') {
            actualizarEstadoSuscripcionGlobal($suscripcion['suscripcion_id'], 'vencida');
            $suscripcion['estado'] = 'vencida';
        }
        
        return [
            'activa' => $esta_activa,
            'suscripcion' => $suscripcion,
            'dias_restantes' => max(0, (int)$suscripcion['dias_restantes']),
            'mensaje' => $esta_activa ? 'Suscripción activa' : 'Suscripción vencida'
        ];
        
    } catch (PDOException $e) {
        error_log("Error al verificar suscripción global: " . $e->getMessage());
        return [
            'activa' => false,
            'suscripcion' => null,
            'dias_restantes' => 0,
            'mensaje' => 'Error al verificar suscripción'
        ];
    }
}

/**
 * Actualizar estado de la suscripción global
 * 
 * @param int $suscripcion_id ID de la suscripción
 * @param string $nuevo_estado Estado nuevo ('activa', 'vencida', 'cancelada')
 * @return bool true si se actualizó correctamente
 */
function actualizarEstadoSuscripcionGlobal($suscripcion_id, $nuevo_estado) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            UPDATE suscripcion_sistema 
            SET estado = :estado 
            WHERE suscripcion_id = :suscripcion_id
        ");
        
        $stmt->bindParam(':estado', $nuevo_estado, PDO::PARAM_STR);
        $stmt->bindParam(':suscripcion_id', $suscripcion_id, PDO::PARAM_INT);
        
        return $stmt->execute();
        
    } catch (PDOException $e) {
        error_log("Error al actualizar estado de suscripción global: " . $e->getMessage());
        return false;
    }
}

/**
 * Crear nueva suscripción GLOBAL después de un pago exitoso
 * 
 * @param float $monto Monto pagado
 * @param int $usuario_id ID del usuario que realizó el pago
 * @param string $stripe_payment_id ID del pago en Stripe
 * @param string $stripe_session_id ID de la sesión de Stripe
 * @return int|false ID de la suscripción creada o false si falló
 */
function crearNuevaSuscripcionGlobal($monto, $usuario_id, $stripe_payment_id = null, $stripe_session_id = null) {
    global $pdo;
    
    try {
        // Primero, cancelar suscripciones anteriores
        $stmt = $pdo->prepare("
            UPDATE suscripcion_sistema 
            SET estado = 'cancelada' 
            WHERE estado = 'activa'
        ");
        $stmt->execute();
        
        // Crear nueva suscripción
        $fecha_inicio = date('Y-m-d H:i:s');
        $fecha_vencimiento = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        $stmt = $pdo->prepare("
            INSERT INTO suscripcion_sistema 
            (fecha_inicio, fecha_vencimiento, monto_pagado, estado, stripe_payment_id, stripe_session_id, fecha_pago, pagado_por_usuario_id)
            VALUES 
            (:fecha_inicio, :fecha_vencimiento, :monto_pagado, 'activa', :stripe_payment_id, :stripe_session_id, NOW(), :usuario_id)
        ");
        
        $stmt->bindParam(':fecha_inicio', $fecha_inicio, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_vencimiento', $fecha_vencimiento, PDO::PARAM_STR);
        $stmt->bindParam(':monto_pagado', $monto, PDO::PARAM_STR);
        $stmt->bindParam(':stripe_payment_id', $stripe_payment_id, PDO::PARAM_STR);
        $stmt->bindParam(':stripe_session_id', $stripe_session_id, PDO::PARAM_STR);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $pdo->lastInsertId();
        }
        
        return false;
        
    } catch (PDOException $e) {
        error_log("Error al crear nueva suscripción global: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtener cualquier administrador del sistema (para contacto)
 * 
 * @return array|null Información de un administrador o null
 */
function getAdministradorSistema() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT usuario_id, nombre, apellidos, email
            FROM usuarios
            WHERE rol_id = 1
            AND estatus = 'activo'
            LIMIT 1
        ");
        
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error al obtener administrador: " . $e->getMessage());
        return null;
    }
}

/**
 * Registrar intento de pago en log (global)
 * 
 * @param int $usuario_id ID del usuario
 * @param string $stripe_session_id ID de sesión de Stripe
 * @param float $monto Monto
 * @param string $estado Estado del pago
 * @param string $mensaje Mensaje adicional
 */
function registrarLogPagoGlobal($usuario_id, $stripe_session_id, $monto, $estado, $mensaje = '') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO pagos_sistema_log 
            (usuario_id, stripe_session_id, monto, estado, mensaje)
            VALUES 
            (:usuario_id, :stripe_session_id, :monto, :estado, :mensaje)
        ");
        
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(':stripe_session_id', $stripe_session_id, PDO::PARAM_STR);
        $stmt->bindParam(':monto', $monto, PDO::PARAM_STR);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':mensaje', $mensaje, PDO::PARAM_STR);
        
        $stmt->execute();
        
    } catch (PDOException $e) {
        error_log("Error al registrar log de pago global: " . $e->getMessage());
    }
}

// ===========================
// COMPATIBILIDAD CON CÓDIGO ANTIGUO
// Función que mantiene la misma firma pero usa versión global
// ===========================
function checkSubscription($empresa_id) {
    // Ignorar empresa_id, verificar suscripción global
    return checkSubscriptionGlobal();
}
?>
