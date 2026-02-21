-- sql/alter_viajes.sql
-- Agregar columna para almacenar las coordenadas generadas por TomTom
-- Ejecutar este comando en la base de datos de Clever Cloud

ALTER TABLE viajes 
ADD COLUMN coordenadas_tomtom JSON NULL 
AFTER fecha_finalizacion;
