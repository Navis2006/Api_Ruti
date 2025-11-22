-- =====================================================
-- MIGRACIÓN: Sistema de Suscripción GLOBAL (Simplificado)
-- Base de datos: b3ehoylez0wwlhvuad4s
-- Fecha: 2025-11-22
-- Descripción: Sistema de suscripción GLOBAL para Dunosusa
--              (una sola suscripción para todo el sistema)
-- =====================================================

-- Crear tabla de configuración global del sistema
CREATE TABLE IF NOT EXISTS `sistema_config` (
  `config_id` INT NOT NULL AUTO_INCREMENT,
  `clave` VARCHAR(100) NOT NULL UNIQUE,
  `valor` TEXT NULL,
  `descripcion` TEXT NULL,
  `actualizado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`config_id`),
  KEY `idx_clave` (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla de suscripción GLOBAL (una sola fila activa a la vez)
CREATE TABLE IF NOT EXISTS `suscripcion_sistema` (
  `suscripcion_id` INT NOT NULL AUTO_INCREMENT,
  `fecha_inicio` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_vencimiento` TIMESTAMP NOT NULL,
  `monto_pagado` DECIMAL(10,2) NOT NULL DEFAULT 10.00,
  `estado` ENUM('activa', 'vencida', 'cancelada') NOT NULL DEFAULT 'activa',
  `stripe_payment_id` VARCHAR(255) NULL COMMENT 'ID de pago de Stripe',
  `stripe_session_id` VARCHAR(255) NULL COMMENT 'ID de sesión de Stripe Checkout',
  `fecha_pago` TIMESTAMP NULL,
  `pagado_por_usuario_id` INT NULL COMMENT 'Administrador que realizó el pago',
  `notas` TEXT NULL,
  `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`suscripcion_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_fecha_vencimiento` (`fecha_vencimiento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de log de pagos (sin empresa_id, es global)
CREATE TABLE IF NOT EXISTS `pagos_sistema_log` (
  `log_id` INT NOT NULL AUTO_INCREMENT,
  `usuario_id` INT NULL COMMENT 'Usuario que inició el pago',
  `stripe_session_id` VARCHAR(255) NULL,
  `monto` DECIMAL(10,2) NOT NULL,
  `estado` ENUM('iniciado', 'completado', 'cancelado', 'fallido') NOT NULL,
  `mensaje` TEXT NULL,
  `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_stripe_session` (`stripe_session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar suscripción inicial GLOBAL (30 días gratis)
INSERT INTO `suscripcion_sistema` (`fecha_inicio`, `fecha_vencimiento`, `monto_pagado`, `estado`, `notas`)
VALUES (
    NOW(),
    DATE_ADD(NOW(), INTERVAL 30 DAY),
    0.00,
    'activa',
    'Suscripción inicial gratuita - 30 días para Dunosusa'
);

-- Insertar configuración del sistema
INSERT INTO `sistema_config` (`clave`, `valor`, `descripcion`) VALUES
('empresa_nombre', 'Dunosusa Logística', 'Nombre de la empresa dueña del sistema'),
('suscripcion_precio', '10.00', 'Precio mensual de la suscripción en MXN'),
('suscripcion_duracion_dias', '30', 'Duración de cada suscripción en días');

-- =====================================================
-- IMPORTANTE: Si ya ejecutaste la migración anterior
-- (migration_add_subscription_system.sql), puedes
-- eliminar la tabla antigua:
-- DROP TABLE IF EXISTS suscripciones;
-- DROP TABLE IF EXISTS pagos_log;
-- =====================================================

-- =====================================================
-- VERIFICACIÓN: Consultas para validar la migración
-- =====================================================

-- Ver la suscripción activa del sistema
-- SELECT 
--     suscripcion_id,
--     fecha_inicio,
--     fecha_vencimiento,
--     estado,
--     DATEDIFF(fecha_vencimiento, NOW()) as dias_restantes,
--     monto_pagado,
--     stripe_payment_id
-- FROM suscripcion_sistema
-- ORDER BY suscripcion_id DESC
-- LIMIT 1;

-- Ver configuración del sistema
-- SELECT * FROM sistema_config;
