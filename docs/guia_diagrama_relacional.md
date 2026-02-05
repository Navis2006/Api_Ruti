# Guía para el Diagrama Relacional - Proyecto Ruti

Este documento contiene la información necesaria para construir el diagrama relacional. Incluye la estructura de cada tabla (Claves Primarias, Foráneas y atributos) y las reglas de cardinalidad entre ellas.

## 1. Estructura de Tablas

### **Empresas**
- **PK**: `empresa_id`
- `nombre`
- `estado_suscripcion`
- `fecha_creacion`

### **Roles**
- **PK**: `rol_id`
- `nombre_rol`

### **Usuarios**
- **PK**: `usuario_id`
- **FK**: `empresa_id` (Relación con Empresas)
- **FK**: `rol_id` (Relación con Roles)
- `estatus`
- `nombre`
- `apellidos`
- `email`
- `contrasena_hash`
- `fecha_creacion`

### **Vehiculos**
- **PK**: `vehiculo_id`
- **FK**: `empresa_id` (Relación con Empresas)
- `nombre`
- `placa`
- `tipo`
- `estatus`
- `altura_metros`
- `ancho_metros`
- `largo_metros`
- `peso_toneladas`

### **Rutas**
- **PK**: `ruta_id`
- **FK**: `empresa_id` (Relación con Empresas)
- **FK**: `creado_por_usuario_id` (Relación con Usuarios)
- `nombre`
- `descripcion`
- `trazado_geom` (Geometría)

### **Alertas**
- **PK**: `alerta_id`
- **FK**: `ruta_id` (Relación con Rutas)
- **FK**: `creado_por_usuario_id` (Relación con Usuarios)
- `descripcion`
- `tipo_alerta`
- `nivel`
- `estatus_alerta`
- `ubicacion_geom` (Geometría)

### **Viajes**
- **PK**: `viaje_id`
- **FK**: `ruta_id` (Relación con Rutas)
- **FK**: `operador_usuario_id` (Relación con Usuarios - Conductor)
- **FK**: `vehiculo_id` (Relación con Vehiculos)
- **FK**: `asignado_por_usuario_id` (Relación con Usuarios - Monitor)
- `estado`
- `fecha_asignacion`
- `fecha_inicio`
- `fecha_finalizacion`

### **Suscripcion_sistema**
- **PK**: `suscripcion_id`
- **FK**: `pagado_por_usuario_id` (Relación con Usuarios, opcional)
- `fecha_inicio`
- `fecha_vencimiento`
- `monto_pagado`
- `estado`
- `stripe_payment_id`
- `stripe_session_id`
- `fecha_pago`
- `notas`
- `creado_en`

---

## 2. Relaciones y Cardinalidades

Aquí se define cómo se unen las tablas. El formato es **(Min, Max)**.

1.  **Empresas (1, 1) <---> (1, N) Usuarios**
    *   Una empresa tiene muchos usuarios. Un usuario pertenece a una única empresa.

2.  **Roles (1, 1) <---> (0, N) Usuarios**
    *   Un rol define a muchos usuarios. Un usuario tiene un único rol.

3.  **Empresas (1, 1) <---> (0, N) Vehiculos**
    *   Una empresa es dueña de muchos vehículos. Un vehículo pertenece a una única empresa.

4.  **Empresas (1, 1) <---> (0, N) Rutas**
    *   Una empresa define muchas rutas. Una ruta pertenece a una única empresa.

5.  **Usuarios (1, 1) <---> (0, N) Rutas** *(Creador)*
    *   Un usuario crea muchas rutas. Una ruta es creada por un único usuario.

6.  **Rutas (1, 1) <---> (0, N) Alertas**
    *   Una ruta tiene muchas alertas. Una alerta pertenece a una única ruta.

7.  **Usuarios (1, 1) <---> (0, N) Alertas** *(Reporte)*
    *   Un usuario reporta muchas alertas. Una alerta es reportada por un único usuario.

8.  **Rutas (1, 1) <---> (0, N) Viajes**
    *   Una ruta se usa en muchos viajes. Un viaje pertenece a una única ruta.

9.  **Vehiculos (1, 1) <---> (0, N) Viajes**
    *   Un vehículo realiza muchos viajes. Un viaje es realizado por un único vehículo.

10. **Usuarios (1, 1) <---> (0, N) Viajes** *(Operador/Conductor)*
    *   Un conductor realiza muchos viajes. Un viaje tiene un único conductor asignado.

11. **Usuarios (1, 1) <---> (0, N) Viajes** *(Asignador/Monitor)*
    *   Un monitor asigna muchos viajes. Un viaje es asignado por un único monitor.

12. **Usuarios (0, 1) <---> (0, N) Suscripcion_sistema**
    *   Un usuario puede pagar muchas suscripciones. Una suscripción es pagada por un (o ningún) usuario.
