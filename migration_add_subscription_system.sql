-- =====================================================
-- MIGRACIÓN: Sistema de Suscripción y Pagos
-- Base de datos: b3ehoylez0wwlhvuad4s
-- Fecha: 2025-11-21
-- Descripción: Agrega tabla de suscripciones para gestión
--              de pagos mensuales por empresa
-- =====================================================

-- Crear tabla de suscripciones
CREATE TABLE IF NOT EXISTS `suscripciones` (
  `suscripcion_id` INT NOT NULL AUTO_INCREMENT,
  `empresa_id` INT NOT NULL,
  `fecha_inicio` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_vencimiento` TIMESTAMP NOT NULL,
  `monto_pagado` DECIMAL(10,2) NOT NULL DEFAULT 10.00,
  `estado` ENUM('activa', 'vencida', 'cancelada') NOT NULL DEFAULT 'activa',
  `stripe_payment_id` VARCHAR(255) NULL COMMENT 'ID de pago de Stripe',
  `stripe_session_id` VARCHAR(255) NULL COMMENT 'ID de sesión de Stripe Checkout',
  `fecha_pago` TIMESTAMP NULL,
  `notas` TEXT NULL,
  `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`suscripcion_id`),
  KEY `idx_empresa_id` (`empresa_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_fecha_vencimiento` (`fecha_vencimiento`),
  CONSTRAINT `fk_suscripcion_empresa` 
    FOREIGN KEY (`empresa_id`) 
    REFERENCES `empresas` (`empresa_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar suscripciones iniciales para todas las empresas existentes
-- Todas comienzan con 30 días de acceso gratuito
INSERT INTO `suscripciones` (`empresa_id`, `fecha_inicio`, `fecha_vencimiento`, `monto_pagado`, `estado`, `notas`)
SELECT 
    `empresa_id`,
    NOW() as fecha_inicio,
    DATE_ADD(NOW(), INTERVAL 30 DAY) as fecha_vencimiento,
    0.00 as monto_pagado,
    'activa' as estado,
    'Suscripción inicial gratuita - 30 días' as notas
FROM `empresas`
WHERE NOT EXISTS (
    SELECT 1 FROM `suscripciones` WHERE `suscripciones`.`empresa_id` = `empresas`.`empresa_id`
);

-- Crear tabla de logs de intentos de pago (opcional, para auditoría)
CREATE TABLE IF NOT EXISTS `pagos_log` (
  `log_id` INT NOT NULL AUTO_INCREMENT,
  `empresa_id` INT NOT NULL,
  `usuario_id` INT NULL,
  `stripe_session_id` VARCHAR(255) NULL,
  `monto` DECIMAL(10,2) NOT NULL,
  `estado` ENUM('iniciado', 'completado', 'cancelado', 'fallido') NOT NULL,
  `mensaje` TEXT NULL,
  `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_empresa_pago` (`empresa_id`),
  KEY `idx_stripe_session` (`stripe_session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- VERIFICACIÓN: Consultas para validar la migración
-- =====================================================

-- Verificar que la tabla se creó correctamente
-- SELECT COUNT(*) as total_suscripciones FROM suscripciones;

-- Ver todas las suscripciones activas
-- SELECT s.*, e.nombre as nombre_empresa 
-- FROM suscripciones s 
-- JOIN empresas e ON s.empresa_id = e.empresa_id 
-- WHERE s.estado = 'activa';

-- Ver empresas cuya suscripción vencerá pronto (próximos 7 días)
-- SELECT e.nombre, s.fecha_vencimiento, 
--        DATEDIFF(s.fecha_vencimiento, NOW()) as dias_restantes
-- FROM suscripciones s
-- JOIN empresas e ON s.empresa_id = e.empresa_id
-- WHERE s.estado = 'activa' 
-- AND s.fecha_vencimiento BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)
-- ORDER BY s.fecha_vencimiento ASC;
