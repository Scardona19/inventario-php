-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-05-2026 a las 16:26:54
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `inventario_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `creado_en`) VALUES
(1, 'Electrónica', 'Dispositivos y componentes electrónicos', '2026-05-19 13:58:20'),
(2, 'Papelería', 'Artículos de oficina y escritura', '2026-05-19 13:58:20'),
(3, 'Herramientas', 'Herramientas manuales y eléctricas', '2026-05-19 13:58:20'),
(4, 'Limpieza', 'Productos de limpieza e higiene', '2026-05-19 13:58:20'),
(5, 'Electrónica', 'Dispositivos y componentes electrónicos', '2026-05-19 14:00:17'),
(6, 'Papelería', 'Artículos de oficina y escritura', '2026-05-19 14:00:17'),
(7, 'Herramientas', 'Herramientas manuales y eléctricas', '2026-05-19 14:00:17'),
(8, 'Limpieza', 'Productos de limpieza e higiene', '2026-05-19 14:00:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

CREATE TABLE `movimientos` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `tipo` enum('entrada','salida') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `observacion` varchar(255) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `movimientos`
--

INSERT INTO `movimientos` (`id`, `producto_id`, `tipo`, `cantidad`, `observacion`, `fecha`) VALUES
(1, 1, 'entrada', 10, 'Compra inicial', '2026-05-19 13:58:20'),
(2, 2, 'entrada', 25, 'Compra inicial', '2026-05-19 13:58:20'),
(3, 3, 'entrada', 30, 'Compra inicial', '2026-05-19 13:58:20'),
(4, 1, 'salida', 2, 'Venta a cliente', '2026-05-19 13:58:20'),
(5, 4, 'entrada', 50, 'Reposición de stock', '2026-05-19 13:58:20'),
(6, 5, 'salida', 5, 'Uso interno', '2026-05-19 13:58:20'),
(7, 1, 'entrada', 10, 'Compra inicial', '2026-05-19 14:00:17'),
(8, 2, 'entrada', 25, 'Compra inicial', '2026-05-19 14:00:17'),
(9, 3, 'entrada', 30, 'Compra inicial', '2026-05-19 14:00:17'),
(10, 1, 'salida', 2, 'Venta a cliente', '2026-05-19 14:00:17'),
(11, 4, 'entrada', 50, 'Reposición de stock', '2026-05-19 14:00:17'),
(12, 5, 'salida', 5, 'Uso interno', '2026-05-19 14:00:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `stock_minimo` int(11) NOT NULL DEFAULT 5,
  `categoria_id` int(11) NOT NULL,
  `proveedor_id` int(11) NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `stock`, `stock_minimo`, `categoria_id`, `proveedor_id`, `creado_en`) VALUES
(1, 'Laptop HP 15\"', 'Laptop Intel i5, 8GB RAM, 256GB SSD', 12500.00, 10, 3, 1, 1, '2026-05-19 13:58:20'),
(2, 'Teclado Inalámbrico', 'Teclado USB/BT compatible Windows/Mac', 350.00, 25, 5, 1, 1, '2026-05-19 13:58:20'),
(3, 'Mouse Óptico', 'Mouse USB 1200 DPI ergonómico', 150.00, 30, 5, 1, 1, '2026-05-19 13:58:20'),
(4, 'Resma Papel A4', '500 hojas, 75g/m²', 85.00, 50, 10, 2, 2, '2026-05-19 13:58:20'),
(5, 'Bolígrafos (caja x12)', 'Bolígrafos azules punta media', 45.00, 40, 10, 2, 2, '2026-05-19 13:58:20'),
(6, 'Destornillador Set', 'Set 12 piezas Phillips y plano', 220.00, 15, 3, 3, 3, '2026-05-19 13:58:20'),
(7, 'Cinta Métrica 5m', 'Cinta métrica acero inoxidable', 95.00, 8, 2, 3, 3, '2026-05-19 13:58:20'),
(8, 'Desinfectante 1L', 'Desinfectante multiusos aroma cítrico', 60.00, 20, 5, 4, 4, '2026-05-19 13:58:20'),
(9, 'Escoba con Mango', 'Escoba plástica mango aluminio', 75.00, 12, 3, 4, 4, '2026-05-19 13:58:20'),
(10, 'Laptop HP 15\"', 'Laptop Intel i5, 8GB RAM, 256GB SSD', 12500.00, 10, 3, 1, 1, '2026-05-19 14:00:17'),
(11, 'Teclado Inalámbrico', 'Teclado USB/BT compatible Windows/Mac', 350.00, 25, 5, 1, 1, '2026-05-19 14:00:17'),
(12, 'Mouse Óptico', 'Mouse USB 1200 DPI ergonómico', 150.00, 30, 5, 1, 1, '2026-05-19 14:00:17'),
(13, 'Resma Papel A4', '500 hojas, 75g/m²', 85.00, 50, 10, 2, 2, '2026-05-19 14:00:17'),
(14, 'Bolígrafos (caja x12)', 'Bolígrafos azules punta media', 45.00, 40, 10, 2, 2, '2026-05-19 14:00:17'),
(15, 'Destornillador Set', 'Set 12 piezas Phillips y plano', 220.00, 15, 3, 3, 3, '2026-05-19 14:00:17'),
(16, 'Cinta Métrica 5m', 'Cinta métrica acero inoxidable', 95.00, 8, 2, 3, 3, '2026-05-19 14:00:17'),
(17, 'Desinfectante 1L', 'Desinfectante multiusos aroma cítrico', 60.00, 20, 5, 4, 4, '2026-05-19 14:00:17'),
(18, 'Escoba con Mango', 'Escoba plástica mango aluminio', 75.00, 12, 3, 4, 4, '2026-05-19 14:00:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `contacto` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id`, `nombre`, `contacto`, `telefono`, `email`, `direccion`, `creado_en`) VALUES
(1, 'TechSupply S.A.', 'Carlos Méndez', '555-1001', 'carlos@techsupply.com', 'Av. Industrial 123', '2026-05-19 13:58:20'),
(2, 'OficMax', 'Laura Torres', '555-2002', 'laura@oficmax.com', 'Calle Comercio 456', '2026-05-19 13:58:20'),
(3, 'HerraFácil', 'Roberto Ruiz', '555-3003', 'roberto@herrafacil.com', 'Zona Industrial 789', '2026-05-19 13:58:20'),
(4, 'LimpiaTodo', 'Ana González', '555-4004', 'ana@limpiatodo.com', 'Blvd. Norte 321', '2026-05-19 13:58:20'),
(5, 'TechSupply S.A.', 'Carlos Méndez', '555-1001', 'carlos@techsupply.com', 'Av. Industrial 123', '2026-05-19 14:00:17'),
(6, 'OficMax', 'Laura Torres', '555-2002', 'laura@oficmax.com', 'Calle Comercio 456', '2026-05-19 14:00:17'),
(7, 'HerraFácil', 'Roberto Ruiz', '555-3003', 'roberto@herrafacil.com', 'Zona Industrial 789', '2026-05-19 14:00:17'),
(8, 'LimpiaTodo', 'Ana González', '555-4004', 'ana@limpiatodo.com', 'Blvd. Norte 321', '2026-05-19 14:00:17');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `proveedor_id` (`proveedor_id`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD CONSTRAINT `movimientos_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  ADD CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
