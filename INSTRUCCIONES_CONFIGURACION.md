# 📋 Instrucciones de Configuración - Sistema de Suscripción y Pagos

Este documento contiene **todos los pasos manuales** que debes realizar para configurar el sistema de suscripción y pagos con Stripe.

---

## 📊 PARTE 1: Configuración de Base de Datos

### Paso 1: Ejecutar Migración SQL

1. **Accede a tu panel de base de datos en Render**:
   - Ve a https://dashboard.render.com
   - Inicia sesión
   - Busca tu servicio de base de datos MySQL (probablemente se llama algo relacionado con tu proyecto)

2. **Conéctate a la base de datos**:
   - En el panel de tu base de datos, busca la sección "Connect"
   - Copia la URL de conexión o usa las credenciales (host, usuario, contraseña, base de datos)

3. **Ejecutar el archivo de migración**:

   **Opción A: Desde phpMyAdmin o panel web**
   - Si tienes acceso a phpMyAdmin desde Render:
     - Ve a la pestaña "SQL"
     - Abre el archivo `migration_add_subscription_system.sql` en un editor de texto
     - Copia todo el contenido
     - Pégalo en el área de texto de phpMyAdmin
     - Click en "Ejecutar" o "Execute"

   **Opción B: Desde línea de comandos**
   ```bash
   # Cargar el archivo SQL directamente
   mysql -h [TU_HOST] -u [TU_USUARIO] -p [NOMBRE_BD] < migration_add_subscription_system.sql
   ```

4. **Verificar que se aplicó correctamente**:
   ```sql
   -- Ejecuta esta consulta para verificar
   SELECT COUNT(*) as total_suscripciones FROM suscripciones;
   -- Deberías ver un número igual a la cantidad de empresas
   ```

---

## 🔐 PARTE 2: Configuración de Stripe

### Paso 1: Crear Cuenta en Stripe

1. **Ir a Stripe**:
   - Ve a https://stripe.com
   - Click en "Sign up" (Registrarse)

2. **Completar registro**:
   - Ingresa tu email
   - Crea una contraseña
   - Completa la información básica
   - **IMPORTANTE**: Por ahora solo necesitas el email, NO necesitas verificar tu identidad aún (eso es para pagos reales)

3. **Verificar email**:
   - Revisa tu correo
   - Click en el enlace de verificación

### Paso 2: Obtener API Keys (Modo Test)

1. **Ir al Dashboard de Stripe**:
   - Una vez dentro, asegúrate de que estés en **modo "Test data"** (toggle en la parte superior derecha)
   - Debería verse un indicador que dice "TEST MODE" o similar

2. **Obtener las Keys**:
   - En el menú lateral, ve a **Developers** → **API keys**
   - Verás dos tipos de keys:
     - **Publishable key**: Empieza con `pk_test_...`
     - **Secret key**: Empieza con `sk_test_...` (click en "Reveal" para verla)

3. **Copiar las keys**:
   ```
   Publishable key: pk_test_XXXXXXXXXXXXXXXXX
   Secret key: sk_test_XXXXXXXXXXXXXXXXX
   ```
   **GUÁRDALAS EN UN LUGAR SEGURO** (las necesitarás en el siguiente paso)

### Paso 3: Configurar Webhook

1. **Ir a Webhooks**:
   - En el menú **Developers** → **Webhooks**
   - Click en "Add endpoint" o "Agregar endpoint"

2. **Configurar endpoint**:
   ```
   Endpoint URL: https://TU_DOMINIO_EN_RENDER.onrender.com/backend/stripe/webhook.php
   ```
   (Reemplaza `TU_DOMINIO_EN_RENDER` con tu URL real de Render)

3. **Seleccionar eventos**:
   - En "Events to send", click en "+ Select events"
   - Busca y selecciona:
     - ✅ `checkout.session.completed`
     - ✅ `checkout.session.expired`
   - Click en "Add events"

4. **Guardar y obtener Signing Secret**:
   - Click en "Add endpoint"
   - Una vez creado, click en el webhook que acabas de crear
   - Busca "Signing secret" y click en "Reveal"
   - Copia el secret (empieza con `whsec_...`)
   ```
   Webhook Signing Secret: whsec_XXXXXXXXXXXXXXXXX
   ```
   **GUÁRDALO también**

---

## 🌐 PARTE 3: Configuración en Render

### Paso 1: Actualizar Variables de Entorno

1. **Ir a tu servicio web en Render**:
   - Ve a https://dashboard.render.com
   - Selecciona tu servicio web (donde está tu aplicación PHP)

2. **Agregar Variables de Entorno**:
   - En el menú lateral, ve a **Environment**
   - Click en "Add Environment Variable"
   - Agrega las siguientes variables (una por una):

   ```
   Variable: STRIPE_PUBLISHABLE_KEY
   Value: pk_test_XXXXXXXXX (tu key de Stripe)

   Variable: STRIPE_SECRET_KEY
   Value: sk_test_XXXXXXXXX (tu secret key de Stripe)

   Variable: STRIPE_WEBHOOK_SECRET
   Value: whsec_XXXXXXXXX (tu webhook secret de Stripe)

   Variable: STRIPE_MODE
   Value: test

   Variable: APP_URL
   Value: https://tu-app.onrender.com (tu URL de Render)
   ```

3. **Guardar y redesplegar**:
   - Click en "Save Changes"
   - Render automáticamente redesplegar tu aplicación
   - Espera a que termine el despliegue (status debe ser "Live")

### Paso 2: Instalar Composer (si no lo tienes)

Tu aplicación necesita la librería de Stripe para PHP. Debes agregar esto:

1. **Verifica si tienes archivo `composer.json`** en tu proyecto
   - Si NO lo tienes, créalo en la raíz del proyecto con este contenido:
   ```json
   {
       "require": {
           "stripe/stripe-php": "^10.0"
       }
   }
   ```

2. **En Render, agregar comando de build**:
   - Ve a tu servicio en Render
   - Busca "Build Command"
   - Actualízalo a:
   ```bash
   composer install && <tu comando actual si hay>
   ```
   - Si no tenías comando de build, simplemente pon:
   ```bash
   composer install
   ```

3. **Guardar y redesplegar**

---

## ✅ PARTE 4: Verificación del Sistema

### Paso 1: Probar Login y Redirección

1. **Forzar una suscripción vencida** (para probar):
   - En tu base de datos, ejecuta:
   ```sql
   -- Cambiar la fecha de vencimiento de una empresa específica
   UPDATE suscripciones 
   SET fecha_vencimiento = DATE_SUB(NOW(), INTERVAL 1 DAY),
       estado = 'vencida'
   WHERE empresa_id = 1;  -- Cambia el ID por el de una empresa de prueba
   ```

2. **Intentar login**:
   - Como **Operador** de esa empresa → Deberías ver pantalla sin opción de pago
   - Como **Administrador** de esa empresa → Deberías ver pantalla con botón de pago

### Paso 2: Probar Pago con Tarjeta de Prueba

1. **Click en "Renovar Ahora"** (como administrador)
2. **Usar tarjeta de prueba de Stripe**:
   ```
   Número de tarjeta: 4242 4242 4242 4242
   Fecha: Cualquier fecha futura (ej: 12/25)
   CVC: Cualquier 3 dígitos (ej: 123)
   Código postal: Cualquier 5 dígitos (ej: 12345)
   ```

3. **Completar pago**
4. **Verificar**:
   - Deberías ser redirigido a página de éxito
   - En la base de datos:
   ```sql
   SELECT * FROM suscripciones ORDER BY suscripcion_id DESC LIMIT 1;
   -- Deberías ver una nueva suscripción activa con fecha_vencimiento = +30 días
   ```
   - Intentar login nuevamente → Ahora deberías poder acceder al sistema normal

### Paso 3: Verificar en Stripe Dashboard

1. **Ir a Stripe Dashboard**:
   - Ve a https://dashboard.stripe.com (asegúrate de estar en TEST mode)
   - Ve a **Payments** → deberías ver el pago de prueba
   - Ve a **Developers** → **Webhooks** → deberías ver eventos recibidos exitosamente

---

## 🚀 PARTE 5: Pasar a Modo Producción (DESPUÉS DE PROBAR)

**⚠️ NO HAGAS ESTO HASTA HABER PROBADO TODO EN MODO TEST**

### Cuando estés listo para pagos reales:

1. **Verificar identidad en Stripe**:
   - Stripe te pedirá información personal/fiscal
   - Proporcionar identificación oficial
   - Configurar cuenta bancaria para recibir pagos

2. **Obtener Keys de Producción**:
   - En Stripe Dashboard, desactiva "Test mode" (toggle arriba)
   - Ve a **Developers** → **API keys**
   - Obtén las keys de producción (empiezan con `pk_live_` y `sk_live_`)

3. **Actualizar Variables en Render**:
   ```
   STRIPE_PUBLISHABLE_KEY = pk_live_XXXXX
   STRIPE_SECRET_KEY = sk_live_XXXXX
   STRIPE_MODE = live
   ```

4. **Recrear Webhook para producción**:
   - En Stripe (modo live), crear nuevo webhook con la misma URL
   - Obtener nuevo Signing Secret
   - Actualizar `STRIPE_WEBHOOK_SECRET` en Render

---

## 🆘 Solución de Problemas

### Problema: Webhook no recibe notificaciones

**Solución**:
- Verifica que la URL del webhook sea accesible públicamente
- En Stripe Dashboard → Webhooks → click en tu webhook → "Send test webhook"
- Revisa los logs en Render para ver si llegó la petición

### Problema: Error "Stripe library not found"

**Solución**:
- Ejecutar `composer install` en el servidor
- Verificar que existe carpeta `vendor/` con subfolder `stripe/`

### Problema: Pago se completa pero suscripción no se actualiza

**Solución**:
- Revisar tabla `pagos_log` para ver si hay errores
- Verificar que el webhook esté recibiendo eventos (Stripe Dashboard)
- Revisar logs de Render para ver errores de PHP

---

## 📞 Contacto y Notas

- **Comisiones de Stripe**: ~3.6% + $3 MXN por transacción
- **Soporte de Stripe**: https://support.stripe.com
- **Documentación**: https://stripe.com/docs

---

**¿Listo para comenzar?**

1. ✅ Ejecuta la migración SQL
2. ✅ Configura Stripe
3. ✅ Actualiza variables en Render
4. ✅ Prueba el sistema

¡Cualquier duda, avísame!
