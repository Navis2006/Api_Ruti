-- ALTER TABLE vehiculos: agregar columnas para TomTom Truck Routing
ALTER TABLE vehiculos 
  ADD COLUMN peso_eje_kg INT NULL AFTER peso_toneladas,
  ADD COLUMN velocidad_max_kmh INT NULL AFTER peso_eje_kg;

-- ALTER TABLE rutas: agregar coordenadas origen/destino
ALTER TABLE rutas
  ADD COLUMN lat_origen DECIMAL(10,7) NULL AFTER trazado_geom,
  ADD COLUMN lng_origen DECIMAL(10,7) NULL AFTER lat_origen,
  ADD COLUMN lat_destino DECIMAL(10,7) NULL AFTER lng_origen,
  ADD COLUMN lng_destino DECIMAL(10,7) NULL AFTER lat_destino;
