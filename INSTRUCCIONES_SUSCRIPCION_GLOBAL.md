# 📋 INSTRUCCIONES SIMPLIFICADAS - Suscripción Global Dunosusa

## ✅ Modelo CORRECTO Implementado

**El sistema ahora gestiona UNA suscripción global para TODO el sistema de Dunosusa**, no por empresa individual.

- ✅ **Empresas en BD** = Destinatarios de carga (NO clientes)
- ✅ **Suscripción** = Global para todo el sistema
- ✅ **Dunosusa** = Único cliente que paga la licencia mensual

---

## 🚀 Paso 1: Aplicar Nueva Migración SQL

Ejecuta este archivo SQL (reemplaza la migración anterior):

**Archivo:** `migration_subscription_global.sql`

```sql
-- Este archivo crea:
-- 1. tabla `suscripcion_sistema` (una suscripción global)
-- 2. tabla `sistema_config` (configuración del sistema)
-- 3. tabla `pagos_sistema_log` (log de pagos globales)
```

### Desde Clever Cloud:

1. Accede a tu base de datos
2. Ejecuta el contenido completo de `migration_subscription_global.sql`
3. Verifica con:
```sql
SELECT * FROM suscripcion_sistema;
-- Deberías ver 1 registro con 30 días de suscripción gratuita
```

---

## ⚙️ Paso 2: Configurar Stripe (Igual que antes)

1. **Crear cuenta en Stripe**: https://stripe.com
2. **Obtener API keys** (modo test):
   - `pk_test_XXXXX` (Publishable key)
   - `sk_test_XXXXX` (Secret key)

3. **Configurar Webhook**:
   - URL: `https://tu-app.onrender.com/backend/stripe/webhook.php`
   - Eventos: `checkout.session.completed`, `checkout.session.expired`
   - Copiar Webhook Secret: `whsec_XXXXX`

---

## 🌐 Paso 3: Configurar Variables en Render

Agregar en Dashboard → Environment:

```
STRIPE_PUBLISHABLE_KEY = pk_test_XXXXX
STRIPE_SECRET_KEY = sk_test_XXXXX
STRIPE_WEBHOOK_SECRET = whsec_XXXXX
STRIPE_MODE = test
APP_URL = https://tu-app.onrender.com
```

**Build Command:**
```bash
composer install
```

Save y redesplegar.

---

## 🧪 Paso 4: Probar el Sistema

### Forzar Suscripción Vencida

```sql
-- Hacer que la suscripción GLOBAL esté vencida
UPDATE suscripcion_sistema 
SET fecha_vencimiento = '2025-01-01 00:00:00',
    estado = 'vencida'
ORDER BY suscripcion_id DESC
LIMIT 1;
```

### Probar con CUALQUIER Usuario

Ahora **todos los usuarios** (sin importar su empresa_id) verán la pantalla de suscripción vencida.

1. **Login como Operador** → Ve pantalla sin opción de pago
2. **Login como Administrador** → Ve pantalla con botón "Renovar"

### Probar Pago

1. Click "Renovar Ahora" (como admin)
2. Tarjeta de prueba: `4242 4242 4242 4242`
3. Webhook actualiza suscripción GLOBAL
4. **TODOS los usuarios** ahora pueden acceder

---

## ✅ Diferencias con Implementación Anterior

| Antes (Incorrecto) | Ahora (Correcto) |
|-------------------|------------------|
| Suscripción por `empresa_id` | Suscripción GLOBAL |
| Tabla `suscripciones` con empresa_id | Tabla `suscripcion_sistema` SIN empresa_id |
| Verificar `checkSubscription($empresa_id)` | Verificar `checkSubscriptionGlobal()` |
| Cada empresa paga su suscripción | TODO el sistema comparte 1 suscripción |

---

## 📊 Queries Útiles

### Ver suscripción global actual

```sql
SELECT 
    suscripcion_id,
    fecha_inicio,
    fecha_vencimiento,
    estado,
    DATEDIFF(fecha_vencimiento, NOW()) as dias_restantes,
    monto_pagado,
    stripe_payment_id
FROM suscripcion_sistema
ORDER BY suscripcion_id DESC
LIMIT 1;
```

### Ver historial de pagos

```sql
SELECT * FROM suscripcion_sistema 
ORDER BY suscripcion_id DESC;
```

### Ver logs de intentos de pago

```sql
SELECT * FROM pagos_sistema_log 
ORDER BY fecha_creacion DESC 
LIMIT 10;
```

### Restaurar suscripción activa (para probar)

```sql
UPDATE suscripcion_sistema 
SET fecha_vencimiento = DATE_ADD(NOW(), INTERVAL 30 DAY),
    estado = 'activa'
ORDER BY suscripcion_id DESC
LIMIT 1;
```

---

## 🎯 Lo Que Cambió en el Código

### Archivos Nuevos/Modificados:

✅ `migration_subscription_global.sql` - Nueva migración
✅ `backend/check_subscription.php` - Verificación global
✅ `backend/stripe/create_payment_session.php` - Sin empresa_id
✅ `backend/stripe/webhook.php` - Crea suscripción global
✅ `backend/stripe/verify_payment_status.php` - Verifica global
✅ `frontend/subscription_expired_operator.php` - Mensaje global
✅ `frontend/subscription_expired_admin.php` - Mensaje global
✅ `frontend/payment_success.php` - Sin referencia a empresa

### Archivos que YA NO USAS:

❌ `migration_add_subscription_system.sql` (la primera versión)
❌ No necesitas tablas antiguas `suscripciones` ni `pagos_log`

---

## 🚨 IMPORTANTE

- **UNA SOLA SUSCRIPCIÓN** para todo el sistema
- Si la suscripción vence → **NADIE puede acceder** (ni admins ni operadores)
- **Solo administradores** pueden renovar
- El pago beneficia a **TODO el sistema**, no a una empresa específica

---

## ✅ Checklist Final

- [ ] Ejecutar `migration_subscription_global.sql`
- [ ] Configurar credenciales de Stripe
- [ ] Agregar variables de entorno en Render
- [ ] Ejecutar `composer install`
- [ ] Forzar suscripción vencida para probar
- [ ] Verificar pantalla para operadores
- [ ] Verificar pantalla para admins con opción de pago
- [ ] Probar flujo completo de pago con tarjeta de prueba
- [ ] Verificar que todos pueden acceder después del pago

¡Listo! 🎉
