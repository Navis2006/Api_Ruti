-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: b3ehoylez0wwlhvuad4s-mysql.services.clever-cloud.com:3306
-- Tiempo de generación: 22-11-2025 a las 15:59:32
-- Versión del servidor: 8.0.22-13
-- Versión de PHP: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `b3ehoylez0wwlhvuad4s`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alertas`
--

CREATE TABLE `alertas` (
  `alerta_id` int NOT NULL,
  `ruta_id` int NOT NULL,
  `descripcion` text NOT NULL,
  `tipo_alerta` varchar(100) NOT NULL COMMENT 'Ej: Cable bajo, Calle angosta',
  `nivel` int NOT NULL DEFAULT '3',
  `estatus_alerta` varchar(20) NOT NULL DEFAULT 'Abierta',
  `ubicacion_geom` point NOT NULL,
  `creado_por_usuario_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `alertas`
--

INSERT INTO `alertas` (`alerta_id`, `ruta_id`, `descripcion`, `tipo_alerta`, `nivel`, `estatus_alerta`, `ubicacion_geom`, `creado_por_usuario_id`) VALUES
(2, 2, 'Tramo con muchos baches peligrosos después de pasar el pueblo de Kantunil. Reducir velocidad.', 'Baches Peligrosos', 3, 'Abierta', 0x000000000101000000a4703d0ad73b56c0d7a3703d0ad73440, 15),
(3, 3, 'La entrada a la zona hotelera es muy angosta y con tráfico de autobuses. Maniobrar con cuidado.', 'Calle angosta', 3, 'Abierta', 0x00000000010100000021b0726891b155c06f1283c0ca213540, 2),
(4, 4, 'Calle cerrada para maniobras de carga de 8 a 10 AM. Planificar ruta alterna.', 'Restricción de Horario', 3, 'Abierta', 0x000000000101000000a4703d0ad76756c0295c8fc2f5f83440, 18),
(5, 5, 'Al llegar a la planta, reportarse en la caseta 2. La entrada de proveedores es por la parte trasera.', 'Punto de Descarga Específico', 3, 'Abierta', 0x000000000101000000a245b6f3fd6c56c0448b6ce7fbe93440, 5),
(6, 6, 'Cable de luz muy bajo al dar vuelta en esta esquina. Pasar pegado a la derecha.', 'Cable bajo', 3, 'Abierta', 0x00000000010100000014ae47e17ac455c00000000000a03440, 22),
(7, 7, 'Báscula de la SCT a la salida del periférico. Verificar que el peso sea correcto.', 'Báscula de Inspección', 3, 'Abierta', 0x0000000001010000008b6ce7fba96956c048e17a14aee73440, 7),
(8, 8, 'Topes muy altos y sin pintar en el pueblo de Pomuch. Cruzar despacio.', 'Topes Peligrosos', 3, 'Abierta', 0x000000000101000000b29defa7c68b56c008ac1c5a64fb3340, 25),
(9, 9, 'Zona de neblina densa por las mañanas, especialmente en invierno. Encender luces.', 'Condición Climática Frecuente', 3, 'Abierta', 0x00000000010100000052b81e85eb1156c0cdcccccccc8c3240, 9),
(10, 10, 'El patio de maniobras es de terracería y se encharca mucho con la lluvia.', 'Terreno Difícil', 3, 'Abierta', 0x000000000101000000b29defa7c66356c0931804560eed3440, 31),
(11, 11, 'Curva muy cerrada y peligrosa al entrar a Sucilá.', 'Curva Peligrosa', 3, 'Abierta', 0x000000000101000000910f7a36ab1256c0dcd7817346243540, 1),
(12, 12, 'Obras del Tren Maya, un solo carril habilitado por 5 km. Posibles retrasos.', 'Zona de Maniobras', 3, 'Abierta', 0x0000000001010000009a99999999f955c0fca9f1d24de23440, 14),
(13, 13, 'Acceso a la zona residencial con pluma. Anunciarse como proveedor.', 'Punto de Acceso Controlado', 3, 'Abierta', 0x0000000001010000006abc7493185456c0d7a3703d0a573540, 19),
(14, 14, 'No hay espacio para estacionarse frente al hospital. Usar la bahía de descarga rápida.', 'Zona de No Estacionarse', 3, 'Abierta', 0x000000000101000000c976be9f1a6756c04260e5d022fb3440, 4),
(15, 15, 'Entrada de proveedores en la parte trasera del hotel. Hay que rodear la manzana.', 'Punto de Descarga Específico', 3, 'Abierta', 0x000000000101000000cdccccccccc455c054e3a59bc4a03440, 28),
(16, 16, 'La rampa de acceso a la terminal es muy pronunciada.', 'Pendiente Pronunciada', 3, 'Abierta', 0x0000000001010000003333333333c755c017d9cef753933440, 20),
(17, 17, 'Retén del ejército en el límite estatal. Revisión puede tomar tiempo.', 'Retén Policial Frecuente', 3, 'Abierta', 0x0000000001010000003f575bb1bfc056c08a8ee4f21f823240, 33),
(18, 18, 'Calle de un solo sentido, el GPS a veces marca mal la entrada.', 'Sentido de la Vía', 3, 'Abierta', 0x0000000001010000007b14ae47e10256c08716d9cef7933340, 8),
(19, 19, 'Corrales de cuarentena. Seguir protocolo de la SAGARPA.', 'Inspección Sanitaria', 3, 'Abierta', 0x000000000101000000ac1c5a643baf56c05c8fc2f5289c3240, 36),
(20, 20, 'La entrada a la obra es un camino de terracería. Acceso complicado después de lluvia.', 'Terreno Difícil', 3, 'Abierta', 0x000000000101000000d34d621058a156c01904560e2dd23340, 40),
(21, 21, 'El espacio en la aduana del puerto es muy reducido para dar la vuelta.', 'Espacio de Maniobra Reducido', 3, 'Abierta', 0x000000000101000000d122dbf97e6a56c0f0a7c64b37493540, 1),
(22, 22, 'Mercado sobre ruedas los domingos. Calle cerrada.', 'Cierre de Vía Programado', 3, 'Abierta', 0x000000000101000000a245b6f3fd5c56c0aaf1d24d62d03440, 42),
(23, 23, 'Puente \"El Zacatal\" a veces cierra por vientos fuertes.', 'Posible Cierre de Vía', 3, 'Abierta', 0x00000000010100000014ae47e17af456c01f85eb51b89e3240, 13),
(24, 24, 'Báscula de la recicladora a la entrada. Vehículo debe pesarse antes de descargar.', 'Báscula de Inspección', 3, 'Abierta', 0x000000000101000000dd240681956b56c0fa7e6abc74f33440, 17),
(25, 25, 'Zona escolar. Reducir velocidad de 7 a 9 AM y de 1 a 3 PM.', 'Zona Escolar', 3, 'Abierta', 0x000000000101000000022b8716d96656c00e2db29def073540, 21),
(26, 26, 'Carretera angosta y con muchas curvas. No rebasar.', 'Carretera Angosta', 3, 'Abierta', 0x00000000010100000033333333337356c06666666666e63340, 30),
(27, 27, 'Descarga solo por la mañana (antes de las 12 PM).', 'Restricción de Horario', 3, 'Abierta', 0x000000000101000000068195438b0c56c0560e2db29daf3440, 34),
(28, 28, 'Caseta de cobro de la autopista a Cancún.', 'Punto de Peaje', 3, 'Abierta', 0x000000000101000000713d0ad7a31056c07b14ae47e1ba3440, 8),
(29, 29, 'La calle principal de Izamal es muy angosta. Cuidado con los coches de caballos.', 'Calle angosta', 3, 'Abierta', 0x000000000101000000fed478e9264156c0022b8716d9ee3440, 45),
(30, 30, 'Punto de revisión sanitaria para productos del mar.', 'Inspección Sanitaria', 3, 'Abierta', 0x000000000101000000295c8fc2f59856c05c8fc2f528dc3440, 50),
(31, 31, 'Prohibido el paso a vehículos de carga pesada de 8 PM a 6 AM.', 'Restricción de Horario', 3, 'Abierta', 0x0000000001010000001904560e2d5256c046b6f3fdd4183540, 1),
(32, 32, 'Entregar la mercancía en el andén 3 de la bodega de frío.', 'Punto de Descarga Específico', 3, 'Abierta', 0x000000000101000000fca9f1d24d6256c0a01a2fdd24663440, 12),
(33, 33, 'El camino para llegar a la subestación no está pavimentado.', 'Terreno Difícil', 3, 'Abierta', 0x000000000101000000d34d6210585956c0c74b378941003540, 16),
(34, 34, 'El ferry a Holbox no transporta vehículos pesados. La descarga es en el muelle.', 'Punto de Entrega Especial', 3, 'Abierta', 0x0000000001010000003f355eba49e455c02db29defa7863540, 23),
(35, 35, 'Acceso restringido. Llamar al cliente 30 minutos antes de llegar.', 'Punto de Acceso Controlado', 3, 'Abierta', 0x000000000101000000e7fba9f1d26956c0fed478e926113540, 29),
(36, 36, 'Presencia de ganado suelto en la carretera por la noche.', 'Peligro en la Vía', 3, 'Abierta', 0x0000000001010000001904560e2d5256c0fa7e6abc74333440, 32),
(37, 37, 'La entrada a la cervecería es por la puerta de proveedores, no la principal.', 'Punto de Descarga Específico', 3, 'Abierta', 0x000000000101000000f2d24d62107856c03108ac1c5a043540, 38),
(38, 38, 'Al llegar al supermercado, usar la rampa subterránea para descarga.', 'Entrada Subterránea', 3, 'Abierta', 0x000000000101000000dbf97e6abcc455c08d976e1283a03440, 41),
(39, 39, 'La imprenta no tiene montacargas. El operador debe ayudar en la descarga.', 'Requiere Maniobra Manual', 3, 'Abierta', 0x000000000101000000fca9f1d24da256c0dbf97e6abcd43340, 9),
(40, 40, 'Zona de topes y boyas. Velocidad máxima 30 km/h.', 'Topes Peligrosos', 3, 'Abierta', 0x000000000101000000068195438bb455c0b29defa7c62b3540, 44),
(41, 41, 'Uso obligatorio de casco y chaleco para ingresar a la planta.', 'Equipo de Seguridad Obligatorio', 3, 'Abierta', 0x0000000001010000005839b4c8766656c060e5d022dbd93440, 1),
(42, 42, 'Aduana en la terminal de carga aérea. Proceso puede ser lento.', 'Trámite Aduanal', 3, 'Abierta', 0x000000000101000000273108ac1c6a56c01d5a643bdfef3440, 15),
(43, 43, 'En Motul, cuidado con las bicicletas y triciclos en el centro.', 'Tráfico Local Intenso', 3, 'Abierta', 0x000000000101000000a01a2fdd245256c0295c8fc2f5183540, 24),
(44, 44, 'La rampa de la maquiladora tiene una inclinación del 15%.', 'Pendiente Pronunciada', 3, 'Abierta', 0x000000000101000000f2d24d62107056c0a8c64b3789e13440, 27),
(52, 2, 'asd', 'Peligro en Vía', 4, 'Abierta', 0x000000000101000000010000a0da5f56c06e4f39663bfa3440, 56),
(53, 45, 'Ya no hay muertos', 'Tráfico', 3, 'Resuelta', 0x00000000010100000001000000ff6256c01640923888f13440, 62),
(54, 45, 'a', 'Neumáticos', 4, 'Resuelta', 0x00000000010100000000000000000000000000000000000000, 62);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

CREATE TABLE `empresas` (
  `empresa_id` int NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `estado_suscripcion` varchar(50) NOT NULL DEFAULT 'Activa',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `empresas`
--

INSERT INTO `empresas` (`empresa_id`, `nombre`, `estado_suscripcion`, `fecha_creacion`) VALUES
(1, 'Transportes Rápidos del Sureste', 'Inactiva', '2025-10-10 00:14:26'),
(2, 'Logística Peninsular S.A. de C.V.', 'Activa', '2025-10-10 00:14:26'),
(3, 'Fletes y Mudanzas El Mayab', 'Activa', '2025-10-10 00:14:26'),
(4, 'Distribuidora de Abarrotes del Golfo', 'Activa', '2025-10-10 00:14:26'),
(5, 'Transportes KUKULKÁN Carga Pesada', 'Activa', '2025-10-10 00:14:26'),
(6, 'Materiales para Construcción del Caribe', 'Prueba', '2025-10-10 00:14:26'),
(7, 'Logística Integral de Yucatán (LIYSA)', 'Activa', '2025-10-10 00:14:26'),
(8, 'Autotransportes de Carga Itzá', 'Activa', '2025-10-10 00:14:26'),
(9, 'KEKÉN Logística y Transporte Refrigerado', 'Activa', '2025-10-10 00:14:26'),
(10, 'Frío Express de la Península', 'Inactiva', '2025-10-10 00:14:26'),
(11, 'Grupo Logístico del Sureste (GLS)', 'Activa', '2025-10-10 00:14:26'),
(12, 'Transportes Hermanos Pérez', 'Activa', '2025-10-10 00:14:26'),
(13, 'Acarreos y Maniobras de Mérida', 'Inactiva', '2025-10-10 00:14:26'),
(14, 'Distribuciones Farmacéuticas del Mayab', 'Activa', '2025-10-10 00:14:26'),
(15, 'Logística de Alimentos del Caribe Mexicano', 'Activa', '2025-10-10 00:14:26'),
(16, 'Transportadora Turquesa', 'Activa', '2025-10-10 00:14:26'),
(17, 'Fletes de Campeche a Cancún', 'Prueba', '2025-10-10 00:14:26'),
(18, 'Servicios de Carga Especializada del Golfo', 'Activa', '2025-10-10 00:14:26'),
(19, 'Transportes de Ganado Bovino de Tizimín', 'Activa', '2025-10-10 00:14:26'),
(20, 'Distribuidora de Acero de la Península', 'Activa', '2025-10-10 00:14:26'),
(21, 'Logística Portuaria de Progreso', 'Activa', '2025-10-10 00:14:26'),
(22, 'Transportes Cárdenas e Hijos', 'Activa', '2025-10-10 00:14:26'),
(23, 'Muebles y Mudanzas de la Riviera', 'Inactiva', '2025-10-10 00:14:26'),
(24, 'Transporte y Logística para la Construcción', 'Activa', '2025-10-10 00:14:26'),
(25, 'Servicio de Paquetería Peninsular Express', 'Activa', '2025-10-10 00:14:26'),
(26, 'Transportes de Material Pétreo \"La Cantera\"', 'Activa', '2025-10-10 00:14:26'),
(27, 'Logística de Bebidas y Embotellados del Sureste', 'Activa', '2025-10-10 00:14:26'),
(28, 'Autotransporte Federal del Mayab (ATF)', 'Activa', '2025-10-10 00:14:26'),
(29, 'Fletes Consolidados de Yucatán', 'Activa', '2025-10-10 00:14:26'),
(30, 'Servicios Logísticos de la Costa', 'Activa', '2025-10-10 00:14:26'),
(31, 'Transportes \"El Faisán y El Venado\"', 'Activa', '2025-10-10 00:14:26'),
(32, 'Distribución de Perecederos del Sureste', 'Activa', '2025-10-10 00:14:26'),
(33, 'Maniobras y Grúas de la Península', 'Activa', '2025-10-10 00:14:26'),
(34, 'Transporte de Maquinaria Pesada del Golfo', 'Activa', '2025-10-10 00:14:26'),
(35, 'Logística para Eventos y Exposiciones', 'Activa', '2025-10-10 00:14:26'),
(36, 'Paquetería y Mensajería del Mayab', 'Inactiva', '2025-10-10 00:14:26'),
(37, 'Fletes Económicos de Mérida', 'Activa', '2025-10-10 00:14:26'),
(38, 'Transportes de Carga Seca y Refrigerada', 'Activa', '2025-10-10 00:14:26'),
(39, 'Distribuidora de Papel y Cartón del Sureste', 'Activa', '2025-10-10 00:14:26'),
(40, 'Logística Inversa y Reciclaje Peninsular', 'Activa', '2025-10-10 00:14:26'),
(41, 'Transportes de Productos Químicos del Golfo', 'Activa', '2025-10-10 00:14:26'),
(42, 'Servicio de Carga Urgente 24/7', 'Activa', '2025-10-10 00:14:26'),
(43, 'Autotransportes de la Zona Henequenera', 'Activa', '2025-10-10 00:14:26'),
(44, 'Logística Textil de Yucatán', 'Activa', '2025-10-10 00:14:26'),
(45, 'Transportes de Aves y Porcinos del Sureste', 'Activa', '2025-10-10 00:14:26'),
(46, 'Fletes y Acarreos \"El Ceibo\"', 'Activa', '2025-10-10 00:14:26'),
(47, 'Distribuciones Comerciales de la Península', 'Activa', '2025-10-10 00:14:26'),
(48, 'Transporte Especializado de Vidrio y Cristal', 'Activa', '2025-10-10 00:14:26'),
(49, 'Logística para la Industria Maquiladora', 'Activa', '2025-10-10 00:14:26'),
(50, 'Transportes de Cemento y Concreto \"Holbox\"', 'Activa', '2025-10-10 00:14:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_log`
--

CREATE TABLE `pagos_log` (
  `log_id` int NOT NULL,
  `empresa_id` int NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `stripe_session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL,
  `estado` enum('iniciado','completado','cancelado','fallido') COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` text COLLATE utf8mb4_unicode_ci,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `rol_id` int NOT NULL,
  `nombre_rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`rol_id`, `nombre_rol`) VALUES
(1, 'Gerente'),
(2, 'Operador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rutas`
--

CREATE TABLE `rutas` (
  `ruta_id` int NOT NULL,
  `empresa_id` int NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text,
  `trazado_geom` geometry NOT NULL,
  `creado_por_usuario_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `rutas`
--

INSERT INTO `rutas` (`ruta_id`, `empresa_id`, `nombre`, `descripcion`, `trazado_geom`, `creado_por_usuario_id`) VALUES
(1, 1, 'Mérida Centro a Puerto Progreso', 'Ruta estándar para recolección de mercancía en el puerto. y mas', 0x00000000010200000002000000e4141dc9e56756c08126c286a7f73440917efb3a706a56c0d3bce3141d493540, 1),
(2, 2, 'CEDIS Umán a Valladolid', 'Ruta de abastecimiento para la sucursal de Valladolid. Pasa por la caseta de Chichén Itzá.', 0x0000000001020000000200000000000000007056c0e17a14ae47e13440865ad3bce30c56c0c66d3480b7b03440, 2),
(3, 3, 'Bodega Poniente a Cancún Zona Hotelera', 'Entrega de materiales a hoteles. Evitar horas pico en Av. Kukulcán.', 0x000000000102000000020000002f6ea301bc6956c0b6847cd0b3f9344005c58f3177b155c0c3d32b6519223540, 3),
(4, 4, 'Ruta de reparto Abarrotes Centro Mérida', 'Recorrido para tiendas en el primer cuadro de la ciudad. Vehículo tipo Rabón.', 0x0000000001020000000300000048e17a14ae6756c0b81e85eb51f8344000000000006856c09a99999999f93440643bdf4f8d6756c0d7a3703d0af73440, 4),
(5, 5, 'Ruta KEKÉN a Puerto Morelos', 'Transporte refrigerado de planta a punto de exportación.', 0x00000000010200000002000000295c8fc2f56c56c027a089b0e1e934400ebe30992ab855c041f163cc5ddb3440, 5),
(6, 6, 'Planta CEMEX (Umán) a Obra en Playa del Carmen', 'Entrega de material de construcción. Cuidado con el tráfico en la 307.', 0x0000000001020000000200000048e17a14ae6f56c0c3f5285c8fe23440789ca223b9c455c0aa8251499da03440, 6),
(7, 7, 'Circuito Periférico de Mérida', 'Ruta de circunvalación para conectar bodegas sin entrar a la ciudad.', 0x000000000102000000050000009a999999996956c06666666666e6344066666666666656c07b14ae47e1fa34409a999999996956c0cdcccccccc0c3540cdcccccccc6c56c07b14ae47e1fa34409a999999996956c06666666666e63440, 7),
(8, 8, 'Mérida a Campeche Centro', 'Ruta de transporte regular entre capitales.', 0x00000000010200000002000000e4141dc9e56756c08126c286a7f734406e3480b740a256c0dc68006f81d43340, 8),
(9, 9, 'Transporte Frío de Progreso a Chetumal', 'Ruta larga de costa a costa para producto congelado.', 0x00000000010200000002000000917efb3a706a56c0d3bce3141d4935400f0bb5a6791356c057ec2fbb277f3240, 9),
(10, 10, 'Ruta local Umán - Kanasín', 'Conexión entre parques industriales de la zona metropolitana.', 0x0000000001020000000200000000000000007056c0e17a14ae47e13440d656ec2fbb6356c0cdccccccccec3440, 10),
(11, 11, 'Recolección Tizimín a Bodega Mérida', 'Ruta para recolección de ganado.', 0x00000000010200000002000000a167b3ea73e955c0d5e76a2bf62735402f6ea301bc6956c0b6847cd0b3f93440, 1),
(12, 12, 'Ruta del Tren Maya - Tramo 4', 'Transporte de material para construcción a lo largo de la autopista a Cancún.', 0x00000000010200000003000000865ad3bce30c56c0c66d3480b7b03440e86a2bf697f955c0a779c7293ae23440984c158c4abe55c0aed85f764f0e3540, 2),
(13, 13, 'Entrega de Materiales a Telchac Puerto', 'Ruta de Mérida a la costa para desarrollos inmobiliarios.', 0x000000000102000000020000002f6ea301bc6956c0b6847cd0b3f9344040a4dfbe0e5456c065aa605452573540, 3),
(14, 14, 'Ruta Farmacéutica a Hospitales de Mérida', 'Distribución local a clínicas y hospitales.', 0x00000000010200000003000000e17a14ae476956c01f85eb51b8fe3440d7a3703d0a6756c07b14ae47e1fa3440b81e85eb516856c03333333333f33440, 4),
(15, 15, 'Entrega de Alimentos a Riviera Maya', 'Abastecimiento a hoteles desde el CEDIS de Cancún.', 0x00000000010200000003000000984c158c4abe55c0aed85f764f0e3540789ca223b9c455c0aa8251499da03440d1915cfe43ce55c0dd24068195833440, 5),
(16, 16, 'Transporte de personal a Calica', 'Ruta para llevar trabajadores a la terminal marítima.', 0x00000000010200000002000000789ca223b9c455c0aa8251499da034401ea7e8482ec755c03333333333933440, 6),
(17, 17, 'Doble Remolque Mérida - Villahermosa', 'Ruta interestatal para carga pesada.', 0x00000000010200000002000000e4141dc9e56756c08126c286a7f73440ec51b81e853b57c0226c787aa5fc3140, 7),
(18, 18, 'Plataforma a Parque Industrial de Felipe Carrillo Puerto', 'Entrega de maquinaria.', 0x000000000102000000020000002f6ea301bc6956c0b6847cd0b3f934403b70ce88d20256c04f401361c3933340, 8),
(19, 19, 'Ruta Ganadera Tizimín - Escárcega', 'Movimiento de ganado entre zonas productoras.', 0x00000000010200000002000000a167b3ea73e955c0d5e76a2bf62735407a36ab3e57af56c07958a835cd9b3240, 9),
(20, 20, 'Entrega de Acero a Obra en Campeche', 'Transporte de varilla y perfiles desde Mérida.', 0x000000000102000000020000002f6ea301bc6956c0b6847cd0b3f93440e17a14ae47a156c052b81e85ebd13340, 10),
(21, 21, 'Ruta de Contenedores Progreso - Umán', 'Movimiento de carga desde el puerto al parque industrial.', 0x00000000010200000002000000917efb3a706a56c0d3bce3141d49354000000000007056c0e17a14ae47e13440, 1),
(22, 22, 'Ruta corta Kanasín - Acanceh', 'Reparto en la zona sur de la ciudad.', 0x00000000010200000002000000d656ec2fbb6356c0cdccccccccec34403ee8d9acfa5c56c039d6c56d34d03440, 2),
(23, 23, 'Mudanza Mérida - Ciudad del Carmen', 'Ruta de mudanza residencial o de oficina.', 0x00000000010200000002000000e4141dc9e56756c08126c286a7f734403333333333f356c08a8ee4f21fa23240, 3),
(24, 24, 'Ruta de la Chatarra - Umán a Recicladora', 'Transporte de material para reciclaje.', 0x0000000001020000000200000000000000007056c0e17a14ae47e13440ec51b81e856b56c03333333333f33440, 4),
(25, 25, 'Paquetería Zona Norte Mérida', 'Recorrido por colonias como Altabrisa, Montebello, etc.', 0x00000000010200000003000000105839b4c86656c048e17a14ae07354085eb51b81e6556c0a4703d0ad70335408fc2f5285c6756c00000000000003540, 5),
(26, 26, 'Mérida a Hopelchén', 'Ruta de abastecimiento a zona agrícola.', 0x00000000010200000002000000e4141dc9e56756c08126c286a7f7344066666666667656c0dcd7817346c43340, 6),
(27, 27, 'Distribución de bebidas en Valladolid', 'Reparto a tiendas y restaurantes locales.', 0x00000000010200000003000000cdcccccccc0c56c0713d0ad7a3b0344014ae47e17a0c56c08fc2f5285caf344085eb51b81e0d56c052b81e85ebb13440, 7),
(28, 28, 'Full: CEDIS Mérida a CEDIS Cancún', 'Viaje nocturno para reabastecimiento entre centros de distribución.', 0x00000000010200000002000000ae47e17a146e56c085eb51b81e053540984c158c4abe55c0aed85f764f0e3540, 8),
(29, 29, 'Ruta de Consolidado a Izamal', 'Entrega de paquetería diversa al pueblo mágico.', 0x00000000010200000002000000e4141dc9e56756c08126c286a7f7344085eb51b81e4156c0ca54c1a8a4ee3440, 9),
(30, 30, 'Recolección de mariscos en Celestún', 'Ruta de producto fresco desde la costa a Mérida.', 0x00000000010200000002000000e10b93a9829956c024287e8cb9db34402f6ea301bc6956c0b6847cd0b3f93440, 10),
(31, 31, 'Entrega en el Parque Industrial de Motul', 'Abastecimiento a las fábricas de la zona.', 0x000000000102000000020000002f6ea301bc6956c0b6847cd0b3f934403cbd5296215256c0f085c954c1183540, 1),
(32, 32, 'Ruta de Perecederos a Ticul', 'Distribución de alimentos al sur del estado.', 0x000000000102000000020000002f6ea301bc6956c0b6847cd0b3f934400ad7a3703d6256c0d95f764f1e663440, 2),
(33, 33, 'Transporte de Postes de Concreto (Umán - Tixkokob)', 'Entrega de material eléctrico.', 0x0000000001020000000200000000000000007056c0e17a14ae47e13440e17a14ae475956c00000000000003540, 3),
(34, 34, 'Transporte de maquinaria a Holbox (Chiquilá)', 'Llevar maquinaria pesada hasta el puerto de Chiquilá.', 0x00000000010200000002000000e4141dc9e56756c08126c286a7f734402aa913d044e455c06666666666863540, 4),
(35, 35, 'Logística para Concierto en Hacienda Xcanatun', 'Entrega de equipo de sonido e iluminación.', 0x000000000102000000020000002f6ea301bc6956c0b6847cd0b3f934406f1283c0ca6956c0a9a44e4013113540, 5),
(36, 36, 'Ruta de paquetería Mérida - Tekax', 'Reparto al cono sur del estado.', 0x00000000010200000002000000e4141dc9e56756c08126c286a7f734403cbd5296215256c03333333333333440, 6),
(37, 37, 'Entrega económica a Hunucmá', 'Ruta corta a la zona industrial de Hunucmá.', 0x000000000102000000020000002f6ea301bc6956c0b6847cd0b3f9344000000000007856c0dcd7817346043540, 7),
(38, 38, 'Doble Temperatura a Playa del Carmen', 'Carga mixta (congelado y seco) para supermercados.', 0x00000000010200000002000000ae47e17a146e56c085eb51b81e053540789ca223b9c455c0aa8251499da03440, 8),
(39, 39, 'Entrega de Papel a imprenta en Campeche', 'Transporte de rollos de papel.', 0x000000000102000000020000002f6ea301bc6956c0b6847cd0b3f934406e3480b740a256c0dc68006f81d43340, 9),
(40, 40, 'Recolección de PET en Cancún', 'Ruta para centro de acopio de reciclaje.', 0x000000000102000000030000006666666666b655c0295c8fc2f528354014ae47e17ab455c0ec51b81e852b3540f6285c8fc2b555c06666666666263540, 10),
(41, 41, 'Ruta de Químicos a Planta de Tratamiento', 'Transporte de cloro y otros insumos.', 0x0000000001020000000200000000000000007056c0e17a14ae47e1344066666666666656c09a99999999d93440, 1),
(42, 42, 'Servicio Urgente al Aeropuerto de Mérida', 'Entrega de paquetería prioritaria a la terminal de carga.', 0x000000000102000000020000002f6ea301bc6956c0b6847cd0b3f93440ae47e17a146a56c0c7293a92cbef3440, 2),
(43, 43, 'Ruta Henequenera: Mérida - Motul - Tizimín', 'Ruta histórica de distribución en la zona henequenera.', 0x00000000010200000003000000e4141dc9e56756c08126c286a7f734403cbd5296215256c0f085c954c1183540a167b3ea73e955c0d5e76a2bf6273540, 3),
(44, 44, 'Entrega de Textiles a Maquiladora en Umán', 'Transporte de rollos de tela.', 0x000000000102000000020000002f6ea301bc6956c0b6847cd0b3f9344000000000007056c0e17a14ae47e13440, 4),
(45, 45, 'Ruta Avícola a Campeche', 'Transporte de aves desde Mérida.', 0x00000000010200000002000000e4141dc9e56756c08126c286a7f734406e3480b740a256c0dc68006f81d43340, 5),
(46, 46, 'Acarreo de materiales a obra en Chicxulub', 'Viajes cortos de material de construcción.', 0x00000000010200000002000000ec51b81e856b56c06666666666263540f775e09c116156c0a9a44e4013513540, 6),
(47, 47, 'Distribución Comercial en Playa del Carmen', 'Reparto a diversas tiendas de la 5ta Avenida.', 0x0000000001020000000300000014ae47e17ac455c01f85eb51b89e3440cdccccccccc455c054e3a59bc4a0344085eb51b81ec555c0c3f5285c8fa23440, 7),
(48, 48, 'Entrega de Vidrio a Hotel en Tulum', 'Transporte delicado de ventanales.', 0x000000000102000000020000002f6ea301bc6956c0b6847cd0b3f93440598638d6c5dd55c02e90a0f831363440, 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suscripciones`
--

CREATE TABLE `suscripciones` (
  `suscripcion_id` int NOT NULL,
  `empresa_id` int NOT NULL,
  `fecha_inicio` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_vencimiento` timestamp NOT NULL,
  `monto_pagado` decimal(10,2) NOT NULL DEFAULT '10.00',
  `estado` enum('activa','vencida','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activa',
  `stripe_payment_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID de pago de Stripe',
  `stripe_session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID de sesión de Stripe Checkout',
  `fecha_pago` timestamp NULL DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `suscripciones`
--

INSERT INTO `suscripciones` (`suscripcion_id`, `empresa_id`, `fecha_inicio`, `fecha_vencimiento`, `monto_pagado`, `estado`, `stripe_payment_id`, `stripe_session_id`, `fecha_pago`, `notas`, `creado_en`) VALUES
(1, 1, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(2, 2, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(3, 3, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(4, 4, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(5, 5, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(6, 6, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(7, 7, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(8, 8, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(9, 9, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(10, 10, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(11, 11, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(12, 12, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(13, 13, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(14, 14, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(15, 15, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(16, 16, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(17, 17, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(18, 18, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(19, 19, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(20, 20, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(21, 21, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(22, 22, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(23, 23, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(24, 24, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(25, 25, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(26, 26, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(27, 27, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(28, 28, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(29, 29, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(30, 30, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(31, 31, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(32, 32, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(33, 33, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(34, 34, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(35, 35, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(36, 36, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(37, 37, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(38, 38, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(39, 39, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(40, 40, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(41, 41, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(42, 42, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(43, 43, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(44, 44, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(45, 45, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(46, 46, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(47, 47, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(48, 48, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(49, 49, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17'),
(50, 50, '2025-11-22 06:05:17', '2025-12-22 06:05:17', 0.00, 'activa', NULL, NULL, NULL, 'Suscripción inicial gratuita - 30 días', '2025-11-22 06:05:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `usuario_id` int NOT NULL,
  `empresa_id` int NOT NULL,
  `rol_id` int NOT NULL,
  `estatus` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contrasena_hash` varchar(255) NOT NULL,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`usuario_id`, `empresa_id`, `rol_id`, `estatus`, `nombre`, `apellidos`, `email`, `contrasena_hash`, `fecha_creacion`) VALUES
(1, 1, 1, 'inactivo', 'Carlos1', 'Gutiérrez Pérez', 'carlos.gutierrez@transportesrapidos.com', '1', '2025-10-10 00:14:26'),
(2, 2, 1, 'inactivo', 'Ana', 'Martínez López', 'ana.martinez@logisticapeninsular.net', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(3, 3, 1, 'activo', 'Javier', 'Sánchez Castillo', 'javier.sanchez@fletesmayab.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(4, 4, 1, 'activo', 'Sofía', 'Ramírez Herrera', 'sofia.ramirez@abarrotesgolfo.com.mx', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(5, 5, 1, 'activo', 'Ricardo', 'Flores Morales', 'ricardo.flores@kukulcancarga.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(6, 6, 1, 'activo', 'Mariana', 'Gómez Vázquez', 'mariana.gomez@materialescaribe.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(7, 7, 1, 'activo', 'Fernando', 'Jiménez Domínguez', 'fernando.jimenez@liysa.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(8, 8, 1, 'activo', 'Lucía', 'Hernández Cruz', 'lucia.hernandez@autotransportesitza.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(9, 9, 1, 'activo', 'Diego', 'Moreno Salazar', 'diego.moreno@kekenlogistica.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(10, 10, 1, 'activo', 'Valeria', 'Rojas Mendoza', 'valeria.rojas@frioexpresspeninsula.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(11, 11, 2, 'activo', 'Juan', 'Pérez García', 'juan.perez@gls.com.mx', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(12, 12, 2, 'activo', 'José', 'González Rodríguez', 'jose.gonzalez@transporteshermanos.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(13, 13, 2, 'activo', 'Luis', 'Hernández López', 'luis.hernandez@acarreosmerida.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(14, 14, 2, 'activo', 'Miguel', 'Martínez González', 'miguel.martinez@difarmayab.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(15, 15, 2, 'activo', 'Pedro', 'García Pérez', 'pedro.garcia@alimentoscaribe.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(16, 16, 2, 'activo', 'Manuel', 'Rodríguez Sánchez', 'manuel.rodriguez@transportadoraturquesa.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(17, 17, 2, 'activo', 'Francisco', 'López Hernández', 'francisco.lopez@fletescampeche.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(18, 18, 2, 'activo', 'Jorge', 'González Martínez', 'jorge.gonzalez@cargagolfo.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(19, 19, 2, 'activo', 'Roberto', 'Pérez Rodríguez', 'roberto.perez@transportesganado.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(20, 20, 2, 'activo', 'David', 'Sánchez García', 'david.sanchez@aceropeninsula.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(21, 21, 2, 'activo', 'Daniel', 'Ramírez López', 'daniel.ramirez@logisticaprogreso.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(22, 22, 2, 'activo', 'Alejandro', 'Flores Hernández', 'alejandro.flores@cardenasehijos.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(23, 23, 2, 'activo', 'Eduardo', 'Gómez González', 'eduardo.gomez@mueblesriviera.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(24, 24, 2, 'activo', 'Héctor', 'Vázquez Pérez', 'hector.vazquez@logisticaconstruccion.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(25, 25, 2, 'activo', 'Raúl', 'Jiménez Rodríguez', 'raul.jimenez@paqueteriapeninsular.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(26, 26, 2, 'activo', 'Sergio', 'Domínguez Sánchez', 'sergio.dominguez@lacantera.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(27, 27, 2, 'activo', 'Mario', 'Cruz García', 'mario.cruz@bebidasdelsureste.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(28, 28, 2, 'activo', 'Alberto', 'Moreno Hernández', 'alberto.moreno@atfmayab.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(29, 29, 2, 'activo', 'Arturo', 'Salazar López', 'arturo.salazar@fletesconsolidados.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(30, 30, 2, 'activo', 'Gustavo', 'Mendoza González', 'gustavo.mendoza@servicioscosta.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(31, 31, 2, 'activo', 'Óscar', 'Rojas Pérez', 'oscar.rojas@elfaisanyelvenado.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(32, 32, 2, 'activo', 'Andrés', 'Castillo García', 'andres.castillo@perecederosdelsureste.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(33, 33, 2, 'activo', 'Felipe', 'Herrera Rodríguez', 'felipe.herrera@gruaspeninsula.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(34, 34, 2, 'activo', 'Víctor', 'Morales Sánchez', 'victor.morales@maquinariapesada.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(35, 35, 2, 'activo', 'Ramón', 'Vázquez López', 'ramon.vazquez@logisticaeventos.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(36, 36, 2, 'activo', 'Alfredo', 'Domínguez Hernández', 'alfredo.dominguez@paqueteriamayab.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(37, 37, 2, 'activo', 'César', 'Cruz González', 'cesar.cruz@fleteseconomicos.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(38, 38, 2, 'activo', 'Iván', 'Moreno Pérez', 'ivan.moreno@cargasecayrefrigerada.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(39, 39, 2, 'activo', 'Enrique', 'Salazar Rodríguez', 'enrique.salazar@papelycarton.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(40, 40, 2, 'activo', 'Adrián', 'Mendoza Sánchez', 'adrian.mendoza@reciclajepeninsular.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(41, 41, 2, 'activo', 'Julián', 'Rojas García', 'julian.rojas@quimicosdelgolfo.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(42, 42, 2, 'activo', 'Ismael', 'Castillo López', 'ismael.castillo@cargaurgente247.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(43, 43, 2, 'activo', 'Emilio', 'Herrera González', 'emilio.herrera@zonahenequenera.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(44, 44, 2, 'activo', 'Rubén', 'Morales Pérez', 'ruben.morales@logisticatextil.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(45, 45, 2, 'activo', 'Samuel', 'Vázquez Rodríguez', 'samuel.vazquez@avesyporcinos.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(46, 46, 2, 'activo', 'Abel', 'Domínguez García', 'abel.dominguez@elceibo.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(47, 47, 2, 'activo', 'Joaquín', 'Cruz López', 'joaquin.cruz@distribucionescomerciales.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(48, 48, 2, 'activo', 'Israel', 'Moreno González', 'israel.moreno@vidrioycristal.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(49, 49, 2, 'activo', 'Gerardo', 'Salazar Pérez', 'gerardo.salazar@maquiladoralogistica.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(50, 50, 2, 'activo', 'Martín', 'Mendoza Rodríguez', 'martin.mendoza@cementosholbox.com', '$2a$12$4rY.gPS9jPz3.L0V2ZfaPuzf1e9.E7mJd.2y3i.gK3.O4h.2L9.p6', '2025-10-10 00:14:26'),
(51, 1, 1, 'activo', 'Super', 'Administrador', 'admin@empresa.com', '$2y$10$fcs.BD/42OgTEuGdqTwIxOcQ/6l39X3nZFN9gnB/XgpEjH/DxbMrS', '2025-10-10 00:21:13'),
(52, 37, 1, 'activo', 'martin', 'cabrera3', 'martin@gmail.com', '$2y$10$.F1pfsf5YkLOKWeMGEAxR.X4GRdzz9.YNrzIpC05WwLYMsqIDCOya', '2025-10-10 00:23:17'),
(54, 28, 2, 'activo', 'borrar1', 'mar', 'borrar1@gmail.com', '$2y$10$fI.qr4YnLFnsu.5aKe9AZOcJ8QPy05.yXF4zmXo.64KQenYs8G4PW', '2025-10-10 07:05:47'),
(55, 46, 1, 'activo', 'borraradmin1', 'a', 'borraradmin1@gmail.com', '$2y$10$Q.j/lmosku1HWpCgRaBDmeyr86o/gF0YwuyB0eyTRYo.xgAovE4WK', '2025-10-10 07:26:24'),
(56, 1, 1, 'activo', 'adminn', 'adminn', 'adminn@gmail.com', '$2y$10$luxFLnsQleWB1l3E4vXup.TUqByquuTOg4UungA/Va4N6T1FIFjlK', '2025-11-02 18:41:20'),
(57, 33, 2, 'activo', 'operador', 'operador', 'operador@gmail.com\r\n', '$2y$10$2.qb.tk5bZXF59acMmVge.lgcIBEw6nAMFpwVnhXCET51G4Jd9EmO', '2025-11-02 18:46:41'),
(58, 43, 2, 'activo', 'Martin', 'Cachondo', 'nose@gmail.com', '$2y$10$.27kvTt1D1K5TzUxmkb6geH6eojE23VSYu2PvHXgJouxqYuhIHyiS', '2025-11-02 21:57:10'),
(59, 14, 2, 'activo', 'Angel Ernesto', 'Nava Sánchez', 'angel.er.nava.sa@gmail.com', '$2y$12$0K0N54wXtSom9uf/qcUEq.6zs2aiBkOKPJ8W1/L0oCS/K36iY8V5S', '2025-11-02 22:03:17'),
(60, 13, 1, 'activo', 'operadoreliminar', 'adas', 'a@gmail.com', '$2y$12$eaJ4TEIyDGKPvwwC2556GOaeI.MxjoYkDmyMwbH9IM50VVPpazF/6', '2025-11-04 01:26:53'),
(61, 28, 2, 'activo', 'asd', 'asd', 'ab@gmail.com', '$2y$12$1XiKBCMXuaRGJRVT5wJ6Bu4XF9QF1W5kZeEqPZFOqaRJ6MB6z.ceW', '2025-11-04 01:28:11'),
(62, 28, 2, 'activo', 'borrar1', 'ads', 'borrar2@gmail.com', '$2y$10$y6e8rIG81zfQIF8o0hZ52eituiqEKTt/CfDhM2Pc37hVWK847z8oG', '2025-11-04 01:33:57'),
(63, 13, 2, 'inactivo', 'borrar3', 'Alonzo', 'borrar3@gmail.com', '$2y$12$SpEmRzxYwpnAjjWJTxU/P.6dBZnweJXsrjFp8hDrDzjnkGRxf5ZJy', '2025-11-14 21:19:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

CREATE TABLE `vehiculos` (
  `vehiculo_id` int NOT NULL,
  `empresa_id` int NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `placa` varchar(20) NOT NULL,
  `tipo` varchar(50) NOT NULL COMMENT 'Ej: Torton, Full',
  `estatus` enum('en_servicio','en_mantenimiento','de_baja') NOT NULL DEFAULT 'en_servicio',
  `altura_metros` decimal(4,2) DEFAULT NULL,
  `ancho_metros` decimal(4,2) DEFAULT NULL,
  `largo_metros` decimal(4,2) DEFAULT NULL,
  `peso_toneladas` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`vehiculo_id`, `empresa_id`, `nombre`, `placa`, `tipo`, `estatus`, `altura_metros`, `ancho_metros`, `largo_metros`, `peso_toneladas`) VALUES
(1, 1, 'El Conquistador', '45-AB-1C', 'Torton', 'de_baja', 4.15, 2.60, 12.50, 18.50),
(2, 2, 'Unidad 101', '82-CD-2E', 'Caja Seca 53ft', 'en_servicio', 4.10, 2.60, 16.15, 28.00),
(3, 3, 'El Maya', '123-XN-8', 'Plataforma', 'en_servicio', 3.90, 2.60, 12.20, 20.00),
(4, 4, 'Abarrotero 01', '54-GH-3J', 'Rabón', 'en_servicio', 3.80, 2.55, 8.50, 10.00),
(5, 5, 'Kukulkán 1', '789-YF-5', 'Full (Doble Remolque)', 'en_servicio', 4.20, 2.60, 31.00, 52.50),
(6, 6, 'Cementero 04', '98-KL-4M', 'Góndola', 'en_servicio', 3.60, 2.60, 10.50, 30.00),
(7, 29, 'kakas', '321-WE-2', 'Torton', 'en_servicio', 4.15, 2.60, 12.50, 18.00),
(8, 8, 'Itzá 07', '65-MN-5P', 'Caja Seca 48ft', 'en_servicio', 4.10, 2.60, 14.60, 25.00),
(9, 9, 'Refrigerado 02', '34-QR-6S', 'Caja Refrigerada', 'en_servicio', 4.25, 2.60, 16.15, 26.50),
(10, 10, 'El Veloz', '76-TU-7V', 'Rabón', 'en_servicio', 3.85, 2.55, 8.70, 10.50),
(11, 11, 'Unidad 205', '90-WX-8Y', 'Torton', 'en_servicio', 4.15, 2.60, 12.50, 18.50),
(12, 12, 'n123', '23-ZA-9B', 'Plataforma', 'en_mantenimiento', 3.90, 2.60, 12.20, 20.00),
(13, 13, 'El Fuerte', '67-BC-1D', 'Góndola', 'en_servicio', 3.65, 2.60, 10.80, 30.50),
(14, 14, 'Farmacéutica 03', '89-DE-2F', 'Caja Refrigerada', 'en_servicio', 4.25, 2.60, 16.15, 26.00),
(15, 15, 'Alimenticio 08', '11-FG-3H', 'Torton Refrigerado', 'en_servicio', 4.20, 2.60, 12.80, 17.50),
(16, 16, 'Turquesa 05', '44-HI-4K', 'Caja Seca 53ft', 'en_servicio', 4.10, 2.60, 16.15, 28.00),
(17, 17, NULL, '77-JK-5N', 'Full (Doble Remolque)', 'en_servicio', 4.20, 2.60, 31.00, 53.00),
(18, 18, 'Carguero 11', '22-LM-6Q', 'Plataforma', 'en_servicio', 3.95, 2.60, 14.00, 22.00),
(19, 19, 'Ganadero 01', '55-NO-7T', 'Jaula Ganadera', 'en_servicio', 4.00, 2.60, 16.15, 24.00),
(20, 20, 'Acerero 09', '88-PQ-8W', 'Plataforma', 'en_servicio', 3.90, 2.60, 16.15, 29.00),
(21, 21, 'Portuario 06', '33-RS-9Z', 'Torton', 'en_servicio', 4.15, 2.60, 12.50, 18.00),
(22, 22, '456', '66-ST-1A', 'Rabón', 'en_servicio', 3.80, 2.55, 8.50, 10.00),
(23, 23, 'Mueblero 02', '99-UV-2C', 'Caja Seca 53ft', 'en_servicio', 4.10, 2.60, 16.15, 27.50),
(24, 24, 'Constructor 15', '14-VW-3E', 'Góndola', 'en_servicio', 3.70, 2.60, 11.00, 31.00),
(25, 25, 'Express 01', '47-XY-4G', 'Rabón', 'en_servicio', 3.85, 2.55, 8.70, 10.50),
(26, 26, 'El Cantero', '80-YZ-5J', 'Góndola', 'en_servicio', 3.60, 2.60, 10.50, 30.00),
(27, 27, 'Refresquero 04', '25-AB-6L', 'Torton', 'en_servicio', 4.15, 2.60, 12.50, 18.50),
(28, 28, NULL, '58-CD-7N', 'Full (Doble Remolque)', 'en_servicio', 4.20, 2.60, 31.00, 52.00),
(29, 29, 'Consolidado 07', '91-EF-8Q', 'Caja Seca 48ft', 'en_servicio', 4.10, 2.60, 14.60, 25.50),
(30, 30, 'Costeño 03', '36-GH-9T', 'Plataforma', 'en_servicio', 3.90, 2.60, 12.20, 20.00),
(31, 31, 'El Venado', '69-IJ-1V', 'Torton', 'en_servicio', 4.15, 2.60, 12.50, 18.00),
(32, 32, 'Perecedero 09', '12-KL-2Y', 'Caja Refrigerada', 'en_servicio', 4.25, 2.60, 16.15, 26.50),
(33, 33, 'La Grúa Titán', '45-MN-3B', 'Plataforma con Grúa', 'en_servicio', 4.00, 2.60, 13.00, 21.00),
(34, 34, 'a', '78-NO-4D', 'Lowboy (Cama Baja)', 'en_servicio', 3.50, 3.00, 18.00, 40.00),
(35, 35, 'Expositor 01', '23-PQ-5F', 'Caja Seca 53ft', 'en_servicio', 4.10, 2.60, 16.15, 28.00),
(36, 36, 'Nuevo Carro borrar', '56-RS-6H', 'Rabón', 'en_servicio', 3.83, 2.55, 8.50, 10.00),
(37, 37, 'El Ahorrador', '89-ST-7K', 'Torton', 'en_servicio', 4.15, 2.60, 12.50, 18.50),
(38, 38, 'Doble Temp 01', '14-UV-8M', 'Caja Refrigerada', 'en_servicio', 4.25, 2.60, 16.15, 26.00),
(39, 39, 'Papelero 06', '47-VW-9P', 'Caja Seca 48ft', 'en_servicio', 4.10, 2.60, 14.60, 25.00),
(40, 40, 'Reciclador 02', '80-XY-1R', 'Góndola', 'en_servicio', 3.65, 2.60, 10.80, 30.50),
(41, 41, 'El Químico', '25-YZ-2U', 'Pipa (Acero Inoxidable)', 'en_servicio', 3.90, 2.60, 14.50, 30.00),
(42, 42, 'Urgente 01', '58-AB-3X', 'Rabón', 'en_servicio', 3.85, 2.55, 8.70, 10.50),
(43, 43, 'Henequenero 08', '91-CD-4A', 'Plataforma', 'en_servicio', 3.90, 2.60, 12.20, 20.00),
(44, 44, 'Textilero 03', '36-EF-5C', 'Caja Seca 53ft', 'en_servicio', 4.10, 2.60, 16.15, 27.50),
(45, 45, 'El Avícola', '69-GH-6E', 'Jaula Ganadera', 'en_servicio', 4.00, 2.60, 16.15, 24.50),
(46, 46, 'El Ceibo 04', '12-IJ-7G', 'Torton', 'en_servicio', 4.15, 2.60, 12.50, 18.00),
(47, 47, 'Comercial 12', '45-KL-8J', 'Caja Seca 48ft', 'en_servicio', 4.10, 2.60, 14.60, 25.50),
(48, 48, 'El Cristalero', '78-MN-9L', 'Plataforma con Racks', 'en_servicio', 3.95, 2.60, 14.00, 22.00),
(49, 49, NULL, '23-NO-1N', 'Full (Doble Remolque)', 'en_servicio', 4.20, 2.60, 31.00, 52.50),
(50, 50, 'Holbox 07', '56-PQ-2Q', 'Góndola', 'en_servicio', 3.70, 2.60, 11.00, 31.00),
(52, 13, 'naaaavis', 'sdsdsf', 'df', 'en_servicio', 0.03, 3.00, 4.00, 34.00),
(53, 37, 'Angel Nava', '66-ST-1B', 'Full (Doble Remolque)', 'en_mantenimiento', 3.00, 3.00, 6.00, 54.00),
(54, 8, 'Angel Navaaa', 'ewdscd', 'dfdf', 'de_baja', 2.00, 5.00, 4.00, 43.00),
(55, 43, 'cliente 123', '66-ST-1R', 'Full (Doble Remolque)', 'en_mantenimiento', 4.00, 4.00, 3.00, 5.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `viajes`
--

CREATE TABLE `viajes` (
  `viaje_id` int NOT NULL,
  `ruta_id` int NOT NULL,
  `operador_usuario_id` int NOT NULL,
  `vehiculo_id` int NOT NULL,
  `asignado_por_usuario_id` int NOT NULL,
  `estado` varchar(50) NOT NULL DEFAULT 'Asignado',
  `fecha_asignacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_inicio` timestamp NULL DEFAULT NULL,
  `fecha_finalizacion` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `viajes`
--

INSERT INTO `viajes` (`viaje_id`, `ruta_id`, `operador_usuario_id`, `vehiculo_id`, `asignado_por_usuario_id`, `estado`, `fecha_asignacion`, `fecha_inicio`, `fecha_finalizacion`) VALUES
(1, 1, 11, 1, 1, 'Finalizado', '2025-10-05 00:14:26', '2025-10-05 02:14:26', '2025-10-06 00:14:26'),
(2, 2, 12, 2, 2, 'Finalizado', '2025-10-06 00:14:26', '2025-10-06 01:14:26', '2025-10-07 00:14:26'),
(3, 3, 13, 3, 3, 'Finalizado', '2025-10-07 00:14:26', '2025-10-07 03:14:26', '2025-10-08 00:14:26'),
(4, 4, 14, 4, 4, 'Finalizado', '2025-10-08 00:14:26', '2025-10-08 01:14:26', '2025-10-09 00:14:26'),
(5, 5, 15, 5, 5, 'Finalizado', '2025-10-04 00:14:26', '2025-10-04 04:14:26', '2025-10-05 00:14:26'),
(6, 6, 16, 6, 6, 'Finalizado', '2025-10-05 00:14:26', '2025-10-05 02:14:26', '2025-10-06 00:14:26'),
(7, 7, 17, 7, 7, 'Finalizado', '2025-10-06 00:14:26', '2025-10-06 01:14:26', '2025-10-07 00:14:26'),
(8, 8, 18, 8, 8, 'Finalizado', '2025-10-07 00:14:26', '2025-10-07 03:14:26', '2025-10-08 00:14:26'),
(9, 9, 19, 9, 9, 'Finalizado', '2025-10-08 00:14:26', '2025-10-08 01:14:26', '2025-10-09 00:14:26'),
(10, 10, 20, 10, 10, 'Finalizado', '2025-10-09 00:14:26', '2025-10-09 02:14:26', '2025-10-09 16:14:26'),
(11, 11, 21, 11, 1, 'En tránsito', '2025-10-09 12:14:26', '2025-10-09 16:14:26', NULL),
(12, 12, 22, 12, 2, 'En tránsito', '2025-10-09 14:14:26', '2025-10-09 18:14:26', NULL),
(13, 13, 23, 13, 3, 'En tránsito', '2025-10-09 16:14:26', '2025-10-09 19:14:26', NULL),
(14, 14, 24, 14, 4, 'En tránsito', '2025-10-09 18:14:26', '2025-10-09 20:14:26', NULL),
(15, 15, 25, 15, 5, 'En tránsito', '2025-10-09 20:14:26', '2025-10-09 22:14:26', NULL),
(16, 16, 26, 16, 6, 'En tránsito', '2025-10-09 13:14:26', '2025-10-09 17:14:26', NULL),
(17, 17, 27, 17, 7, 'En tránsito', '2025-10-09 15:14:26', '2025-10-09 19:14:26', NULL),
(18, 18, 28, 18, 8, 'En tránsito', '2025-10-09 17:14:26', '2025-10-09 21:14:26', NULL),
(19, 19, 29, 19, 9, 'En tránsito', '2025-10-09 19:14:26', '2025-10-09 23:14:26', NULL),
(20, 20, 30, 20, 10, 'En tránsito', '2025-10-09 21:14:26', '2025-10-09 23:14:26', NULL),
(21, 21, 31, 21, 1, 'En tránsito', '2025-10-09 12:14:26', '2025-10-09 16:14:26', NULL),
(22, 22, 32, 22, 2, 'En tránsito', '2025-10-09 14:14:26', '2025-10-09 17:14:26', NULL),
(23, 23, 33, 23, 3, 'En tránsito', '2025-10-09 16:14:26', '2025-10-09 18:14:26', NULL),
(24, 24, 34, 24, 4, 'En tránsito', '2025-10-09 18:14:26', '2025-10-09 20:14:26', NULL),
(25, 25, 35, 25, 5, 'En tránsito', '2025-10-09 20:14:26', '2025-10-09 22:14:26', NULL),
(26, 26, 36, 26, 6, 'Asignado', '2025-10-10 00:14:26', NULL, NULL),
(27, 27, 37, 27, 7, 'Asignado', '2025-10-10 00:14:26', NULL, NULL),
(28, 28, 38, 28, 8, 'Asignado', '2025-10-10 00:14:26', NULL, NULL),
(29, 29, 39, 29, 9, 'Asignado', '2025-10-10 00:14:26', NULL, NULL),
(30, 30, 40, 30, 10, 'Asignado', '2025-10-10 00:14:26', NULL, NULL),
(31, 31, 41, 31, 1, 'Asignado', '2025-10-10 00:14:26', NULL, NULL),
(32, 32, 42, 32, 2, 'Asignado', '2025-10-10 00:14:26', NULL, NULL),
(33, 33, 43, 33, 3, 'Asignado', '2025-10-10 00:14:26', NULL, NULL),
(34, 34, 44, 34, 4, 'Asignado', '2025-10-10 00:14:26', NULL, NULL),
(35, 35, 45, 35, 5, 'Asignado', '2025-10-10 00:14:26', NULL, NULL),
(36, 36, 46, 36, 6, 'Asignado', '2025-10-10 00:14:26', NULL, NULL),
(37, 37, 47, 37, 7, 'Asignado', '2025-10-10 00:14:26', NULL, NULL),
(38, 38, 48, 38, 8, 'Asignado', '2025-10-10 00:14:26', NULL, NULL),
(39, 39, 49, 39, 9, 'Asignado', '2025-10-10 00:14:26', NULL, NULL),
(40, 40, 50, 40, 10, 'Asignado', '2025-10-10 00:14:26', NULL, NULL),
(41, 41, 11, 41, 1, 'Asignado', '2025-10-10 00:14:26', NULL, NULL),
(42, 42, 12, 42, 2, 'Asignado', '2025-10-10 00:14:26', NULL, NULL),
(43, 43, 13, 43, 3, 'Asignado', '2025-10-10 00:14:26', NULL, NULL),
(44, 44, 14, 44, 4, 'Asignado', '2025-10-10 00:14:26', NULL, NULL),
(45, 45, 62, 45, 5, 'Finalizado', '2025-10-10 00:14:26', '2025-11-17 01:53:00', '2025-11-15 07:53:27'),
(46, 46, 16, 46, 6, 'Cancelado', '2025-10-09 00:14:26', NULL, NULL),
(47, 47, 17, 47, 7, 'Cancelado', '2025-10-08 00:14:26', NULL, NULL),
(48, 48, 18, 48, 8, 'Cancelado', '2025-10-07 00:14:26', NULL, NULL),
(49, 20, 52, 49, 28, 'En curso', '2025-10-10 00:14:26', '2025-10-10 03:11:00', '2025-10-12 03:11:00'),
(52, 2, 62, 28, 22, 'Planeado', '2025-10-10 16:24:19', '2025-11-20 21:52:00', '2025-10-23 17:24:00'),
(53, 17, 62, 34, 56, 'Cancelado', '2025-11-15 08:03:10', '2025-11-03 02:03:00', NULL),
(54, 17, 62, 34, 56, 'En Curso', '2025-11-15 08:04:41', '2025-11-16 02:07:00', NULL),
(55, 3, 40, 34, 56, 'En Curso', '2025-11-16 02:22:22', '2025-11-19 20:22:00', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alertas`
--
ALTER TABLE `alertas`
  ADD PRIMARY KEY (`alerta_id`),
  ADD KEY `ruta_id` (`ruta_id`),
  ADD KEY `creado_por_usuario_id` (`creado_por_usuario_id`);

--
-- Indices de la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`empresa_id`);

--
-- Indices de la tabla `pagos_log`
--
ALTER TABLE `pagos_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_empresa_pago` (`empresa_id`),
  ADD KEY `idx_stripe_session` (`stripe_session_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`rol_id`),
  ADD UNIQUE KEY `nombre_rol` (`nombre_rol`);

--
-- Indices de la tabla `rutas`
--
ALTER TABLE `rutas`
  ADD PRIMARY KEY (`ruta_id`),
  ADD KEY `empresa_id` (`empresa_id`),
  ADD KEY `creado_por_usuario_id` (`creado_por_usuario_id`);

--
-- Indices de la tabla `suscripciones`
--
ALTER TABLE `suscripciones`
  ADD PRIMARY KEY (`suscripcion_id`),
  ADD KEY `idx_empresa_id` (`empresa_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha_vencimiento` (`fecha_vencimiento`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`usuario_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `empresa_id` (`empresa_id`),
  ADD KEY `rol_id` (`rol_id`);

--
-- Indices de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD PRIMARY KEY (`vehiculo_id`),
  ADD UNIQUE KEY `placa` (`placa`),
  ADD KEY `empresa_id` (`empresa_id`);

--
-- Indices de la tabla `viajes`
--
ALTER TABLE `viajes`
  ADD PRIMARY KEY (`viaje_id`),
  ADD KEY `ruta_id` (`ruta_id`),
  ADD KEY `operador_usuario_id` (`operador_usuario_id`),
  ADD KEY `vehiculo_id` (`vehiculo_id`),
  ADD KEY `asignado_por_usuario_id` (`asignado_por_usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alertas`
--
ALTER TABLE `alertas`
  MODIFY `alerta_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de la tabla `empresas`
--
ALTER TABLE `empresas`
  MODIFY `empresa_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT de la tabla `pagos_log`
--
ALTER TABLE `pagos_log`
  MODIFY `log_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `rol_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `rutas`
--
ALTER TABLE `rutas`
  MODIFY `ruta_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `suscripciones`
--
ALTER TABLE `suscripciones`
  MODIFY `suscripcion_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `usuario_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  MODIFY `vehiculo_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT de la tabla `viajes`
--
ALTER TABLE `viajes`
  MODIFY `viaje_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alertas`
--
ALTER TABLE `alertas`
  ADD CONSTRAINT `alertas_ibfk_1` FOREIGN KEY (`ruta_id`) REFERENCES `rutas` (`ruta_id`),
  ADD CONSTRAINT `alertas_ibfk_2` FOREIGN KEY (`creado_por_usuario_id`) REFERENCES `usuarios` (`usuario_id`);

--
-- Filtros para la tabla `rutas`
--
ALTER TABLE `rutas`
  ADD CONSTRAINT `rutas_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`empresa_id`),
  ADD CONSTRAINT `rutas_ibfk_2` FOREIGN KEY (`creado_por_usuario_id`) REFERENCES `usuarios` (`usuario_id`);

--
-- Filtros para la tabla `suscripciones`
--
ALTER TABLE `suscripciones`
  ADD CONSTRAINT `fk_suscripcion_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`empresa_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`empresa_id`),
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`rol_id`);

--
-- Filtros para la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD CONSTRAINT `vehiculos_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`empresa_id`);

--
-- Filtros para la tabla `viajes`
--
ALTER TABLE `viajes`
  ADD CONSTRAINT `viajes_ibfk_1` FOREIGN KEY (`ruta_id`) REFERENCES `rutas` (`ruta_id`),
  ADD CONSTRAINT `viajes_ibfk_2` FOREIGN KEY (`operador_usuario_id`) REFERENCES `usuarios` (`usuario_id`),
  ADD CONSTRAINT `viajes_ibfk_3` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculos` (`vehiculo_id`),
  ADD CONSTRAINT `viajes_ibfk_4` FOREIGN KEY (`asignado_por_usuario_id`) REFERENCES `usuarios` (`usuario_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
