# 🧹 Limpieza y Configuración Final - Base de Datos

## 📋 Situación Actual

Tu base de datos tiene **tablas obsoletas** del sistema anterior que necesitan eliminarse antes de aplicar el nuevo sistema global.

### Tablas Obsoletas a Eliminar

❌ `suscripciones` (con `empresa_id` - modelo antiguo)
❌ `pagos_log` (con `empresa_id` - modelo antiguo)

### Tablas Nuevas a Crear

✅ `suscripcion_sistema` (SIN `empresa_id` - modelo global)
✅ `pagos_sistema_log` (SIN `empresa_id` - modelo global)
✅ `sistema_config` (configuración global del sistema)

---

## 🚀 Pasos para Configurar (EN ORDEN)

### Paso 1: Limpiar Tablas Obsoletas

Ejecuta: **`cleanup_old_subscription_tables.sql`**

```sql
-- Este script:
-- 1. Elimina tabla `suscripciones` antigua
-- 2. Elimina tabla `pagos_log` antigua
-- 3. Prepara la base de datos para el nuevo sistema
```

**En Clever Cloud:**

1. Accede a tu base de datos
2. Abre/ejecuta `cleanup_old_subscription_tables.sql`
3. Verifica que se eliminaron:
```sql
SHOW TABLES LIKE '%suscri%';
-- No debería mostrar nada
```

---

### Paso 2: Crear Nuevas Tablas Globales

Ejecuta: **`migration_subscription_global.sql`**

```sql
-- Este script:
-- 1. Crea tabla `suscripcion_sistema` (global)
-- 2. Crea tabla `sistema_config` (configuración)
-- 3. Crea tabla `pagos_sistema_log` (logs)
-- 4. Inserta suscripción inicial (30 días gratis)
-- 5. Inserta configuración del sistema
```

**En Clever Cloud:**

1. Ejecuta `migration_subscription_global.sql`
2. Verifica que se crearon:
```sql
SELECT * FROM suscripcion_sistema;
-- Deberías ver 1 registro con 30 días

SELECT * FROM sistema_config;
-- Deberías ver configuración de Dunosusa
```

---

### Paso 3: Configurar Stripe y Render

Sigue los pasos en: **`INSTRUCCIONES_SUSCRIPCION_GLOBAL.md`**

1. Configurar API keys de Stripe
2. Configurar webhook
3. Agregar variables de entorno en Render
4. Ejecutar `composer install`

---

## ✅ Verificación Final

### Confirmar que el sistema funciona:

```sql
-- 1. Ver suscripción global actual
SELECT 
    suscripcion_id,
    fecha_vencimiento,
    estado,
    DATEDIFF(fecha_vencimiento, NOW()) as dias_restantes
FROM suscripcion_sistema
ORDER BY suscripcion_id DESC
LIMIT 1;

-- 2. Ver configuración
SELECT * FROM sistema_config;

-- 3. Forzar vencimiento para probar
UPDATE suscripcion_sistema 
SET fecha_vencimiento = '2025-01-01', estado = 'vencida'
ORDER BY suscripcion_id DESC LIMIT 1;
```

### Probar en la aplicación:

1. Login con cualquier usuario
2. Deberías ver pantalla de suscripción vencida
3. Si eres admin → ves botón "Renovar"
4. Si eres operador → solo ves mensaje

---

## 📁 Archivos en Orden de Ejecución

| Orden | Archivo | Descripción |
|-------|---------|-------------|
| 1 | `cleanup_old_subscription_tables.sql` | Elimina tablas obsoletas |
| 2 | `migration_subscription_global.sql` | Crea nuevas tablas globales |
| 3 | `INSTRUCCIONES_SUSCRIPCION_GLOBAL.md` | Configuración de Stripe y Render |

---

## ⚠️ IMPORTANTE

- **NO ejecutes** `migration_add_subscription_system.sql` (ese ya NO existe, fue el primer intento incorrecto)
- **SÍ ejecuta** en orden: limpieza → migración global → configuración
- Las tablas antiguas con `empresa_id` YA NO SE USAN
- El nuevo sistema es GLOBAL para todo Dunosusa

---

## 🆘 Si algo sale mal

### Si ejecutaste migration_subscription_global antes de cleanup:

```sql
-- Eliminar todo y empezar de nuevo
DROP TABLE IF EXISTS suscripcion_sistema;
DROP TABLE IF EXISTS sistema_config;
DROP TABLE IF EXISTS pagos_sistema_log;
DROP TABLE IF EXISTS suscripciones;
DROP TABLE IF EXISTS pagos_log;

-- Ahora sigue el orden correcto:
-- 1. cleanup (ya no hace nada porque eliminamos todo)
-- 2. migration_subscription_global
```

### Si la migración da error de foreign key:

```sql
SET FOREIGN_KEY_CHECKS = 0;
-- ejecutar los DROP TABLE
SET FOREIGN_KEY_CHECKS = 1;
```

---

¡Listo! Sigue los pasos en orden y todo funcionará perfect perfectamente. 🎉
