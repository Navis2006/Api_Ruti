# 🧪 GUÍA COMPLETA DE TESTING - Sistema de Suscripción

## 📋 Pre-requisitos

- Acceso a Clever Cloud (base de datos)
- Cuenta de Stripe en modo TEST
- Usuario administrador en el sistema
- Usuario operador en el sistema

---

## ✅ TEST 1: Suscripción Activa (Flujo Normal)

### Objetivo
Verificar que usuarios pueden acceder al sistema cuando la suscripción está activa.

### Pasos

1. **Verificar suscripción activa en BD**
   ```sql
   SELECT 
       suscripcion_id,
       fecha_vencimiento,
       estado,
       DATEDIFF(fecha_vencimiento, NOW()) as dias_restantes
   FROM suscripcion_sistema
   ORDER BY suscripcion_id DESC
   LIMIT 1;
   ```
   
   **Resultado esperado**: `estado = 'activa'` y `dias_restantes > 0`

2. **Login como Administrador**
   - Email: `adminn@gmail.com` (o tu admin)
   - Password: tu contraseña
   
   **Resultado esperado**: Redirige a `menu_admin.php` directamente ✅

3. **Login como Operador**
   - Email: `operador@gmail.com` (o tu operador)
   - Password: su contraseña
   
   **Resultado esperado**: Redirige a `menu_trailero.php` directamente ✅

---

## ❌ TEST 2: Suscripción Vencida (Bloqueo de Acceso)

### Objetivo
Verificar que el sistema bloquea acceso cuando la suscripción está vencida.

### Pasos

1. **Forzar vencimiento de suscripción**
   ```sql
   -- Hacer que la suscripción actual esté vencida
   UPDATE suscripcion_sistema 
   SET 
       fecha_vencimiento = '2025-01-01 00:00:00',  -- Fecha pasada
       estado = 'vencida'
   ORDER BY suscripcion_id DESC
   LIMIT 1;
   ```

2. **Verificar que se aplicó**
   ```sql
   SELECT * FROM suscripcion_sistema 
   ORDER BY suscripcion_id DESC LIMIT 1;
   ```
   
   **Resultado esperado**: `estado = 'vencida'` y `fecha_vencimiento` es pasada

3. **Intentar login como OPERADOR**
   - Email: operador
   - Password: contraseña
   
   **Resultado esperado**: 
   - ✅ Redirige a `subscription_expired_operator.php`
   - ✅ Ve mensaje: "La licencia mensual del sistema ha vencido"
   - ✅ NO ve botón de "Renovar"
   - ✅ Ve información de contacto del administrador
   - ❌ NO puede acceder al sistema

4. **Intentar login como ADMINISTRADOR**
   - Email: admin
   - Password: contraseña
   
   **Resultado esperado**:
   - ✅ Redirige a `subscription_expired_admin.php`
   - ✅ Ve mensaje: "La licencia mensual del sistema (Dunosusa Logística) ha vencido"
   - ✅ Ve botón verde "Renovar Ahora - $10.00 MXN"
   - ✅ Ve información de suscripción (fecha vencimiento, costo)

---

## 💳 TEST 3: Flujo Completo de Pago (Modo TEST de Stripe)

### Objetivo
Verificar el flujo completo: pago → webhook → renovación → acceso restaurado

### Pasos

1. **Asegurar que suscripción está vencida** (TEST 2, paso 1)

2. **Login como Administrador** → Verás pantalla de renovación

3. **Click en "Renovar Ahora"**
   
   **Resultado esperado**:
   - ✅ Botón se deshabilita
   - ✅ Aparece "Procesando..."
   - ✅ Redirige a Stripe Checkout (pantalla azul de Stripe)

4. **En Stripe Checkout, usar tarjeta de prueba:**
   ```
   Número de tarjeta: 4242 4242 4242 4242
   Fecha de vencimiento: 12/28 (cualquier fecha futura)
   CVC: 123 (cualquier 3 dígitos)
   Nombre: Test User
   Código postal: 12345
   ```

5. **Complete el pago**
   
   **Resultado esperado**:
   - ✅ Stripe muestra "Payment successful"
   - ✅ Redirige a `payment_success.php`
   - ✅ Ve checkmark verde ✓
   - ✅ Ve "¡Pago Procesado Exitosamente!"
   - ⏳ Ve "Verificando pago..." (por 2-5 segundos)

6. **Esperar verificación automática**
   
   **Resultado esperado**:
   - ✅ Después de 2-5 segundos, aparece información:
     - Sistema: Dunosusa Logística
     - Nueva fecha de vencimiento: (30 días desde hoy)
     - Días restantes: 30 días
   - ✅ Aparece botón "Ir al Panel de Control"

7. **Verificar en Base de Datos que se creó nueva suscripción**
   ```sql
   -- Ver todas las suscripciones (debe haber una nueva)
   SELECT * FROM suscripcion_sistema 
   ORDER BY suscripcion_id DESC;
   
   -- Ver logs de pago
   SELECT * FROM pagos_sistema_log 
   ORDER BY fecha_creacion DESC LIMIT 5;
   ```
   
   **Resultado esperado**:
   - ✅ Nueva fila en `suscripcion_sistema` con:
     - estado = 'activa'
     - fecha_vencimiento = (30 días desde ahora)
     - monto_pagado = 10.00
     - stripe_payment_id = (ID del pago)
     - stripe_session_id = (ID de sesión)
   - ✅ Suscripción anterior tiene estado = 'cancelada'
   - ✅ En `pagos_sistema_log` hay registro con estado = 'completado'

8. **Click en "Ir al Panel de Control"**
   
   **Resultado esperado**:
   - ✅ Redirige a `menu_admin.php`
   - ✅ Admin puede usar el sistema normalmente

9. **Cerrar sesión y login como Operador**
   
   **Resultado esperado**:
   - ✅ Operador TAMBIÉN puede acceder ahora
   - ✅ Redirige a `menu_trailero.php`
   - ❌ NO ve pantallas de suscripción vencida

---

## 🚫 TEST 4: Pago Cancelado

### Objetivo
Verificar qué pasa si el usuario cancela el pago en Stripe.

### Pasos

1. **Suscripción vencida** → Login como Admin → Click "Renovar"

2. **En Stripe Checkout, click en "← Back"** (flecha atrás)
   
   **Resultado esperado**:
   - ✅ Redirige a `payment_cancelled.php`
   - ✅ Ve mensaje: "Pago Cancelado"
   - ✅ Ve: "No se realizó ningún cargo a su tarjeta"
   - ✅ Ve botón "Intentar Nuevamente"
   - ✅ Ve botón "Cerrar Sesión"

3. **Verificar en BD que NO se creó suscripción**
   ```sql
   SELECT * FROM suscripcion_sistema 
   ORDER BY suscripcion_id DESC LIMIT 2;
   ```
   
   **Resultado esperado**: NO debe haber nueva suscripción activa

4. **Click "Intentar Nuevamente"**
   
   **Resultado esperado**: Vuelve a `subscription_expired_admin.php`

---

## 🔄 TEST 5: Verificación de Webhook de Stripe

### Objetivo
Confirmar que el webhook de Stripe está funcionando correctamente.

### Pasos

1. **Ir a Stripe Dashboard** → Developers → Webhooks

2. **Buscar tu webhook**: `https://api-ruti.onrender.com/backend/stripe/webhook.php`

3. **Hacer un pago de prueba** (TEST 3)

4. **En Stripe Dashboard → Webhooks → tu endpoint → Events**
   
   **Resultado esperado**:
   - ✅ Ves evento `checkout.session.completed`
   - ✅ Status: "Succeeded" (✓ verde)
   - ✅ Response code: 200

5. **Click en el evento → Ver detalles**
   
   **Resultado esperado**:
   - ✅ Request body contiene `session_id`
   - ✅ Response: 200 OK
   - ✅ No hay errores en logs

---

## 🎯 TEST 6: Verificación del Código de Validación

### ¿El sistema realmente valida el pago o solo redirige?

**SÍ, el sistema VALIDA el pago.** Aquí está el flujo:

1. **Usuario completa pago en Stripe** → Stripe redirige a `payment_success.php?session_id=XXXXX`

2. **`payment_success.php` ejecuta JavaScript** que:
   ```javascript
   // Llama a backend cada 2 segundos
   fetch(`/backend/stripe/verify_payment_status.php?session_id=${sessionId}`)
   ```

3. **`verify_payment_status.php` verifica REALMENTE con Stripe:**
   ```php
   // Consulta a Stripe API
   $session = \Stripe\Checkout\Session::retrieve($session_id);
   $payment_status = $session->payment_status;  // 'paid', 'unpaid'
   
   if ($payment_status === 'paid') {
       // Verifica que TAMBIÉN se actualizó en BD
       $estado_suscripcion = checkSubscriptionGlobal();
       return $estado_suscripcion['activa'];  // ✅ Doble verificación
   }
   ```

4. **Webhook de Stripe** (proceso paralelo):
   ```php
   // webhook.php se ejecuta cuando Stripe confirma pago
   // Crea NUEVA suscripción en BD
   crearNuevaSuscripcionGlobal(...);
   ```

**✅ Validación en 2 niveles:**
- Nivel 1: Stripe confirma que el pago fue exitoso
- Nivel 2: Sistema verifica que la suscripción se creó en BD

**❌ NO es solo redirección**, el sistema verifica activamente.

---

## 📊 Checklist Final de Testing

- [ ] TEST 1: Suscripción activa permite acceso
- [ ] TEST 2: Suscripción vencida bloquea operadores
- [ ] TEST 2: Suscripción vencida muestra renovación a admins
- [ ] TEST 3: Flujo completo de pago funciona
- [ ] TEST 3: Webhook actualiza BD correctamente
- [ ] TEST 3: Nueva suscripción créa acceso para todos
- [ ] TEST 4: Cancelar pago no crea suscripción
- [ ] TEST 5: Webhook de Stripe recibe eventos
- [ ] Verificar logs en Render sin errores

---

## 🔧 Comandos SQL Útiles para Testing

### Restaurar suscripción activa
```sql
UPDATE suscripcion_sistema 
SET 
    fecha_vencimiento = DATE_ADD(NOW(), INTERVAL 30 DAY),
    estado = 'activa'
ORDER BY suscripcion_id DESC
LIMIT 1;
```

### Forzar vencimiento
```sql
UPDATE suscripcion_sistema 
SET 
    fecha_vencimiento = '2025-01-01',
    estado = 'vencida'
ORDER BY suscripcion_id DESC
LIMIT 1;
```

### Limpiar logs de pago (para testing limpio)
```sql
DELETE FROM pagos_sistema_log;
```

### Ver historial completo
```sql
SELECT 
    s.suscripcion_id,
    s.fecha_inicio,
    s.fecha_vencimiento,
    s.estado,
    s.monto_pagado,
    s.stripe_session_id,
    DATEDIFF(s.fecha_vencimiento, NOW()) as dias_restantes
FROM suscripcion_sistema s
ORDER BY s.suscripcion_id DESC;
```

---

¡Sigue estos tests en orden y verás que todo funciona correctamente! 🎉
