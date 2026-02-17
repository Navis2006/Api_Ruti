## 1. Estado Actual

### Base de Datos - Tabla `alertas` (SIN CAMBIOS)

La tabla ya existe y se usará tal cual, sin modificaciones:

| Columna                   | Tipo                            | Descripción                                                      |
| ------------------------- | ------------------------------- | ----------------------------------------------------------------- |
| `alerta_id`             | int (PK, AUTO_INCREMENT)        | ID único de la alerta                                            |
| `ruta_id`               | int (FK → rutas)               | Ruta asociada a la alerta                                         |
| `descripcion`           | text                            | Descripción detallada                                            |
| `tipo_alerta`           | varchar(100)                    | Ej: Baches Peligrosos, Accidente, Tráfico, Peligro en Vía, Otro |
| `nivel`                 | int (default 3)                 | Prioridad: 1=Bajo, 2=Medio-Bajo, 3=Medio, 4=Alto, 5=Urgente       |
| `estatus_alerta`        | varchar(20) (default 'Abierta') | Estado: Abierta / Resuelta                                        |
| `ubicacion_geom`        | point                           | Coordenadas en formato POINT(longitud latitud)                    |
| `creado_por_usuario_id` | int (FK → usuarios)            | Quién creó la alerta                                            |

### Backend (Api_Ruti)

- **Archivos existentes para web (sesión PHP, NO API REST):**
  - `backend/admin_gestionar_alertas_process.php` → CRUD de alertas para admin web
  - `backend/operador_gestionar_alerta.php` → Marcar alerta como "Resuelta" (web)
  - `backend/operador_reportar_alerta.php` → Crear alerta desde viaje web
  - `backend/operador_reportar_incidencia.php` → Crear incidencia desde web
  - `backend/get_alerts_data.php` → Función PHP interna para obtener alertas de un operador
- **Endpoints API REST existentes (carpeta `backend/api/`):**
  - `login_mobile.php` → Login con JWT
  - `get_my_trips.php` → Viajes del operador (JWT)
  - `get_trip_details.php` → Detalle de viaje (JWT)
  - `update_trip_status.php` → Cambiar estado de viaje (JWT)
- **NO existen endpoints API REST para alertas** (hay que crearlos)

### App Móvil (rutitruck_movil)

- `lib/screens/alerts/alerts_center_screen.dart` → Pantalla de Centro de Alertas, **usa datos mock (hardcodeados)**
- `lib/screens/trip/report_issue_screen.dart` → Pantalla "Reportar Falla", **no está conectada al backend**
- `lib/data/mock_data.dart` → Datos falsos de alertas
- `lib/models/app_models.dart` → Modelo `Alert` con: id, title, description, type, timestamp, isRead
- `lib/services/auth_service.dart` → Base URL: `https://api-ruti.onrender.com`, guarda JWT en secure storage
- `lib/services/trip_service.dart` → Ejemplo de cómo consumir la API (patrón a seguir)

---

## 2. Los 7 Campos Esenciales de una Alerta

Cuando un operador reporta una alerta desde la app móvil, se necesitan llenar estos 7 campos en la base de datos. A continuación se explica de dónde sale cada uno:

### Campo 1: `creado_por_usuario_id` — Creado por

- **¿De dónde se obtiene?** Del operador que está logeado en ese momento en la app móvil.
- **¿Cómo?** Al hacer login, el `usuario_id` del operador se guarda en el almacenamiento seguro del teléfono (`FlutterSecureStorage` con la key `user_id`). Cuando el operador reporta una alerta, el backend extrae el `operador_id` del token JWT, y ese es el `creado_por_usuario_id`.
- **El operador NO lo ingresa manualmente**, es automático.

### Campo 2: `ruta_id` — Ruta Asociada

- **¿De dónde se obtiene?** De la ruta en la que se encuentra el operador en ese momento.
- **¿Cómo?** El operador tiene un viaje activo (por ejemplo, viaje #56 en la ruta "CEDIS Umán a Valladolid"). Desde la app se envía el `viaje_id` del viaje activo, y el backend hace un `SELECT ruta_id FROM viajes WHERE viaje_id = X` para obtener la ruta correspondiente.
- **Ejemplo:** Si el operador está en la ruta "San Benito" y reporta, esa ruta es la que se ingresa.
- **El operador NO selecciona la ruta manualmente**, es automática basada en su viaje activo.

### Campo 3: `tipo_alerta` — Tipo de Alerta

- **¿De dónde se obtiene?** Del botón que el operador presiona en la pantalla "Reportar Falla".
- **Opciones disponibles en móvil:**

| Botón en la App  | Valor que se guarda como `tipo_alerta` |
| ----------------- | ---------------------------------------- |
| Baches Peligrosos | `Baches Peligrosos`                    |
| Choque            | `Accidente`                            |
| Tráfico Pesado   | `Tráfico`                             |
| Cables Bajos      | `Peligro en Vía`                      |
| Otro Incidente    | `Otro`                                 |

### Campo 4: `nivel` — Nivel de Prioridad

- **¿De dónde se obtiene?** Se asigna automáticamente según el tipo de alerta seleccionado.
- **El operador NO lo elige manualmente.**
- **Asignación de niveles:**

| Tipo de Alerta     | `nivel` asignado | Significado |
| ------------------ | ------------------ | ----------- |
| Baches Peligrosos  | `3`              | Medio       |
| Tráfico Pesado    | `3`              | Medio       |
| Cables Bajos       | `4`              | Alto        |
| Choque / Accidente | `5`              | Urgente     |
| Otro Incidente     | `3`              | Medio       |

### Campo 5: `estatus_alerta` — Estatus de la Alerta

- **¿De dónde se obtiene?** Siempre es `'Abierta'` cuando el operador reporta.
- **Valor fijo:** `Abierta`
- Solo un Administrador desde la web puede cambiar el estatus a `Resuelta`.

### Campo 6: `ubicacion_geom` — Ubicación en el Mapa

- **¿De dónde se obtiene?** De la ubicación GPS actual del dispositivo del operador en el momento de reportar.
- **Formato:** `POINT(longitud latitud)`
- **Ejemplo real:** `POINT(-89.62199301338234 20.957303788896212)`
- **¿Cómo?** La app usa el paquete `geolocator` de Flutter para obtener las coordenadas actuales del operador, y las envía al backend como `latitud` y `longitud`. El backend arma el `POINT(lng lat)` con `ST_GeomFromText()`.

### Campo 7: `descripcion` — Descripción de la Alerta

- **¿De dónde se obtiene?** De lo que el operador escriba opcionalmente, o de un texto por defecto.
- **Flujo detallado (ver sección 5 para flujo visual completo):**

  1. El operador presiona un tipo de alerta (ej. "Baches Peligrosos")
  2. Aparece un botón para **confirmar** la selección
  3. Al confirmar, aparece una ventana intermedia (que NO abarca toda la pantalla) con dos botones:
     - **"Puedo describir"** → Se muestra un campo de texto para que el operador escriba una breve descripción, luego se envía
     - **"No puedo describir"** → Se envía la alerta con una descripción automática por defecto
- **Descripciones por defecto (cuando el operador elige "No puedo describir"):**

| Tipo de Alerta     | Descripción por defecto                    |
| ------------------ | ------------------------------------------- |
| Baches Peligrosos  | `Baches peligrosos reportados en la ruta` |
| Choque / Accidente | `Accidente reportado en la ruta`          |
| Tráfico Pesado    | `Tráfico pesado reportado en la ruta`    |
| Cables Bajos       | `Cables bajos reportados en la ruta`      |
| Otro Incidente     | `Incidente reportado en la ruta`          |

---

## 3. Nuevos Endpoints Backend (API)

Todos los endpoints van en la carpeta `backend/api/` y usan autenticación JWT mediante `auth_mobile_guard.php`.

### 3.1 `GET /backend/api/get_my_alerts.php` — Obtener alertas del operador

**Propósito:** Obtener todas las alertas **abiertas** relacionadas con las rutas de los viajes del operador autenticado.

**Autenticación:** Bearer Token JWT (Authorization header)

**Archivo a crear:** `Api_Ruti/backend/api/get_my_alerts.php`

**Lógica:**

1. Validar JWT con `auth_mobile_guard.php` → obtener `$operador_id`
2. Consultar alertas abiertas de las rutas asignadas al operador (a través de la tabla `viajes`)
3. Ordenar por prioridad (`nivel` DESC) y luego por `alerta_id` DESC
4. Devolver la lista completa de alertas; **el filtrado de alertas "ya leídas" se hace en la app móvil con caché local**

**Query SQL sugerido:**

```sql
SELECT
    a.alerta_id,
    a.ruta_id,
    a.descripcion,
    a.tipo_alerta,
    a.nivel,
    a.estatus_alerta,
    ST_X(a.ubicacion_geom) AS longitud,
    ST_Y(a.ubicacion_geom) AS latitud,
    r.nombre AS nombre_ruta,
    CONCAT(u.nombre, ' ', u.apellidos) AS creador_nombre
FROM alertas a
INNER JOIN rutas r ON a.ruta_id = r.ruta_id
INNER JOIN viajes v ON r.ruta_id = v.ruta_id
LEFT JOIN usuarios u ON a.creado_por_usuario_id = u.usuario_id
WHERE v.operador_usuario_id = :operador_id
  AND a.estatus_alerta = 'Abierta'
GROUP BY a.alerta_id
ORDER BY a.nivel DESC, a.alerta_id DESC
```

**Respuesta JSON esperada:**

```json
{
  "success": true,
  "data": [
    {
      "alerta_id": 52,
      "ruta_id": 2,
      "descripcion": "Accidente reportado en la ruta",
      "tipo_alerta": "Accidente",
      "nivel": 5,
      "estatus_alerta": "Abierta",
      "longitud": -89.6243,
      "latitud": 20.9674,
      "nombre_ruta": "CEDIS Umán a Valladolid",
      "creador_nombre": "Super Administrador"
    },
    {
      "alerta_id": 2,
      "ruta_id": 2,
      "descripcion": "Tramo con muchos baches peligrosos después de pasar el pueblo de Kantunil.",
      "tipo_alerta": "Baches Peligrosos",
      "nivel": 3,
      "estatus_alerta": "Abierta",
      "longitud": -89.4567,
      "latitud": 20.8234,
      "nombre_ruta": "CEDIS Umán a Valladolid",
      "creador_nombre": "Pedro García Pérez"
    }
  ]
}
```

**Clasificación visual en la app (no en el backend):**

| Condición     | Representación en móvil                                    |
| -------------- | ------------------------------------------------------------ |
| `nivel >= 4` | Banner rojo PRIORIDAD CRÍTICA + badge PRECAUCIÓN (naranja) |
| `nivel == 3` | Tarjeta con badge PRECAUCIÓN (naranja)                      |
| `nivel < 3`  | Tarjeta con badge INFORMATIVO (azul)                         |

---

### 3.2 `POST /backend/api/report_alert.php` — Reportar una nueva alerta desde móvil

**Propósito:** Cuando el operador usa "Reportar Falla", se crea una alerta que aparecerá en el panel web del Administrador y para otros operadores de la misma ruta.

**Autenticación:** Bearer Token JWT

**Archivo a crear:** `Api_Ruti/backend/api/report_alert.php`

**Body JSON esperado:**

```json
{
  "viaje_id": 56,
  "tipo_alerta": "Baches Peligrosos",
  "descripcion": "Baches peligrosos reportados en la ruta",
  "latitud": 20.957303788896212,
  "longitud": -89.62199301338234
}
```

**Lógica detallada:**

1. Validar JWT con `auth_mobile_guard.php` → obtener `$operador_id`
2. Leer el body JSON y extraer: `viaje_id`, `tipo_alerta`, `descripcion`, `latitud`, `longitud`
3. Obtener `ruta_id` de la tabla `viajes`:
   ```sql
   SELECT ruta_id FROM viajes WHERE viaje_id = :viaje_id AND operador_usuario_id = :operador_id
   ```
4. Determinar el `nivel` según el `tipo_alerta`:
   ```php
   $niveles = [
       'Baches Peligrosos' => 3,
       'Tráfico'           => 3,
       'Peligro en Vía'    => 4,  // Cables Bajos
       'Accidente'         => 5,  // Choque
       'Otro'              => 3
   ];
   $nivel = $niveles[$tipo_alerta] ?? 3;
   ```
5. Armar el POINT WKT: `"POINT($longitud $latitud)"` → ej: `POINT(-89.62199301338234 20.957303788896212)`
6. Insertar en la tabla `alertas`:
   ```sql
   INSERT INTO alertas (ruta_id, creado_por_usuario_id, descripcion, tipo_alerta, nivel, estatus_alerta, ubicacion_geom)
   VALUES (:ruta_id, :operador_id, :descripcion, :tipo_alerta, :nivel, 'Abierta', ST_GeomFromText(:point_wkt))
   ```
7. Responder con éxito y el ID de la nueva alerta

**Respuesta JSON:**

```json
{
  "success": true,
  "message": "Alerta reportada exitosamente.",
  "alerta_id": 55
}
```

**Respuesta de error (ejemplo):**

```json
{
  "success": false,
  "message": "No se encontró el viaje o no pertenece al operador."
}
```

---

### 3.3 Resumen de Endpoints

| Método  | Endpoint                           | Acción                               | Auth       |
| -------- | ---------------------------------- | ------------------------------------- | ---------- |
| `GET`  | `/backend/api/get_my_alerts.php` | Obtener alertas abiertas del operador | JWT Bearer |
| `POST` | `/backend/api/report_alert.php`  | Reportar nueva alerta (falla)         | JWT Bearer |

> **Nota:** No se necesita un endpoint `dismiss_alert`. Las alertas descartadas se manejan con caché local en la app (ver sección 4.3).
>
