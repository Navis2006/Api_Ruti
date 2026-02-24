-- Script de Migración: Rediseño de Arquitectura SIN pérdida de datos
-- Este script adapta la BD para soportar Rutas y Empresas como destinos, 
-- preservando el historial de viajes actual.

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Modificar la tabla empresas (Agregar coordenadas y estado)
ALTER TABLE empresas 
  DROP COLUMN estado_suscripcion,
  ADD COLUMN estado ENUM('Activa', 'Inactiva') NOT NULL DEFAULT 'Activa' AFTER nombre,
  ADD COLUMN lat DECIMAL(10,7) NULL AFTER estado,
  ADD COLUMN lng DECIMAL(10,7) NULL AFTER lat;

-- 2. Modificar la tabla viajes (Agregar origen, mantener ruta_id preexistente por compatibilidad)
ALTER TABLE viajes
  ADD COLUMN origen_empresa_id INT NULL AFTER viaje_id,
  ADD CONSTRAINT fk_viajes_origen FOREIGN KEY (origen_empresa_id) REFERENCES empresas(empresa_id);

-- OJO: Asignamos por defecto la empresa 1 como Origen de todos los viajes antiguos
UPDATE viajes SET origen_empresa_id = 1 WHERE origen_empresa_id IS NULL;

-- Ahora hacemos la columna NOT NULL
ALTER TABLE viajes MODIFY COLUMN origen_empresa_id INT NOT NULL;

-- 2.5. Hacer que ruta_id sea opcional (NULL) ya que los viajes nuevos no lo usarán en la tabla principal
ALTER TABLE viajes MODIFY COLUMN ruta_id INT NULL;

-- 3. Crear tabla para los destinos multi-parada
CREATE TABLE viaje_destinos (
  destino_id INT AUTO_INCREMENT PRIMARY KEY,
  viaje_id INT NOT NULL,
  empresa_id INT NULL COMMENT 'Aplica si el destino es una sede oficial',
  ruta_id INT NULL COMMENT 'Aplica si el destino es una ubicación personalizada',
  orden INT NOT NULL DEFAULT 1 COMMENT '1 = primer destino, 2 = segundo, etc.',
  FOREIGN KEY (viaje_id) REFERENCES viajes(viaje_id) ON DELETE CASCADE,
  FOREIGN KEY (empresa_id) REFERENCES empresas(empresa_id),
  FOREIGN KEY (ruta_id) REFERENCES rutas(ruta_id),
  CHECK (
    (empresa_id IS NOT NULL AND ruta_id IS NULL) OR 
    (empresa_id IS NULL AND ruta_id IS NOT NULL)
  ) -- Asegura que un destino es O una empresa O una ruta, no ambos.
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 4. MIGRAMOS LOS DATOS: Copiar la ruta asignada actualmente a cada viaje como su "Primer Destino"
INSERT INTO viaje_destinos (viaje_id, ruta_id, orden)
SELECT viaje_id, ruta_id, 1 FROM viajes WHERE ruta_id IS NOT NULL;

-- 5. Restaurar FK checks
SET FOREIGN_KEY_CHECKS = 1;

-- Opcional: Actualizar algunas empresas con coordenadas iniciales de prueba (Mérida)
UPDATE empresas SET lat = 20.9673, lng = -89.5926 WHERE empresa_id = 1;
UPDATE empresas SET lat = 21.0500, lng = -89.6000 WHERE empresa_id = 2;
