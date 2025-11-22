-- =====================================================
-- SCRIPT DE LIMPIEZA: Eliminar Tablas Obsoletas
-- Base de datos: b3ehoylez0wwlhvuad4s
-- Fecha: 2025-11-22
-- Descripción: Elimina tablas del sistema ANTERIOR de suscripción
--              (por empresa) para dejar espacio al sistema GLOBAL
-- =====================================================

-- IMPORTANTE: Ejecuta este script ANTES de migration_subscription_global.sql

-- Eliminar constraint de foreign key primero (si existe)
SET FOREIGN_KEY_CHECKS = 0;

-- Eliminar tabla de suscripciones antiguas (con empresa_id)
DROP TABLE IF EXISTS `suscripciones`;

-- Eliminar tabla de logs de pagos antiguos (con empresa_id)
DROP TABLE IF EXISTS `pagos_log`;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- Ejecuta esto para verificar que las tablas fueron eliminadas:
-- SHOW TABLES LIKE '%suscri%';
-- SHOW TABLES LIKE '%pagos%';

-- =====================================================
-- SIGUIENTE PASO
-- =====================================================
-- Ahora ejecuta migration_subscription_global.sql
-- para crear las nuevas tablas del sistema GLOBAL
