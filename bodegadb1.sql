-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-09-2026 a las 17:59:56
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bodegadb1`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `id_carrito` int(11) NOT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `estado` varchar(50) DEFAULT 'ACTIVO',
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `carrito`
--

INSERT INTO `carrito` (`id_carrito`, `fecha_creacion`, `estado`, `id_usuario`) VALUES
(1, '2026-07-04 19:22:31', 'COMPLETADO', 17),
(2, '2026-07-05 08:15:53', 'COMPLETADO', 17),
(3, '2026-07-06 16:25:49', 'COMPLETADO', 5),
(4, '2026-07-07 06:11:52', 'COMPLETADO', 5),
(5, '2026-07-07 15:56:10', 'COMPLETADO', 15),
(6, '2026-07-07 16:17:25', 'COMPLETADO', 15),
(7, '2026-07-07 22:35:15', 'COMPLETADO', 15),
(8, '2026-07-07 22:51:08', 'COMPLETADO', 15),
(9, '2026-07-07 22:51:37', 'COMPLETADO', 15),
(10, '2026-07-08 01:19:25', 'COMPLETADO', 5),
(11, '2026-07-08 01:41:13', 'COMPLETADO', 15),
(12, '2026-07-08 01:46:04', 'COMPLETADO', 15),
(13, '2026-07-08 14:40:14', 'COMPLETADO', 15),
(14, '2026-07-08 14:46:36', 'COMPLETADO', 15),
(15, '2026-07-08 14:50:22', 'COMPLETADO', 5),
(16, '2026-07-16 23:11:04', 'COMPLETADO', 15),
(17, '2026-07-22 16:13:07', 'COMPLETADO', 20),
(18, '2026-07-22 17:53:44', 'COMPLETADO', 20),
(19, '2026-07-22 17:55:56', 'COMPLETADO', 20),
(20, '2026-07-22 19:30:37', 'COMPLETADO', 5),
(21, '2026-07-22 19:50:31', 'COMPLETADO', 15),
(22, '2026-07-22 19:54:44', 'COMPLETADO', 15),
(23, '2026-07-22 19:55:13', 'COMPLETADO', 15),
(24, '2026-07-22 19:58:36', 'COMPLETADO', 15),
(25, '2026-07-22 20:01:37', 'COMPLETADO', 15),
(26, '2026-07-22 20:02:01', 'COMPLETADO', 15),
(27, '2026-07-22 20:10:32', 'COMPLETADO', 15),
(28, '2026-07-22 20:14:16', 'COMPLETADO', 15),
(29, '2026-07-22 20:15:47', 'COMPLETADO', 15),
(30, '2026-07-22 20:32:16', 'COMPLETADO', 15),
(31, '2026-07-22 20:34:35', 'COMPLETADO', 15),
(32, '2026-07-22 20:49:50', 'COMPLETADO', 15),
(33, '2026-07-22 20:53:04', 'COMPLETADO', 15),
(34, '2026-07-22 20:57:25', 'COMPLETADO', 15),
(35, '2026-07-22 21:00:27', 'COMPLETADO', 15),
(36, '2026-07-22 21:03:50', 'COMPLETADO', 15),
(37, '2026-07-22 21:04:20', 'COMPLETADO', 15),
(38, '2026-07-22 21:08:13', 'COMPLETADO', 15),
(39, '2026-07-22 21:08:39', 'COMPLETADO', 15),
(40, '2026-07-22 21:10:59', 'COMPLETADO', 15),
(41, '2026-07-22 21:13:01', 'COMPLETADO', 15),
(42, '2026-07-22 21:14:54', 'COMPLETADO', 15),
(43, '2026-07-22 21:18:06', 'COMPLETADO', 15),
(44, '2026-07-22 21:21:20', 'COMPLETADO', 15),
(45, '2026-07-22 21:22:40', 'COMPLETADO', 15),
(46, '2026-07-23 08:10:24', 'COMPLETADO', 15),
(47, '2026-07-23 10:32:01', 'COMPLETADO', 15),
(48, '2026-07-23 10:32:09', 'COMPLETADO', 15),
(49, '2026-07-23 10:33:02', 'COMPLETADO', 15),
(50, '2026-07-23 10:34:10', 'COMPLETADO', 15),
(51, '2026-07-23 10:35:06', 'COMPLETADO', 15),
(52, '2026-07-23 10:35:17', 'COMPLETADO', 15),
(53, '2026-07-23 10:35:37', 'COMPLETADO', 15),
(54, '2026-07-23 10:38:21', 'COMPLETADO', 15),
(55, '2026-07-23 10:38:34', 'COMPLETADO', 15),
(56, '2026-07-23 10:41:30', 'COMPLETADO', 15),
(57, '2026-07-23 10:41:43', 'COMPLETADO', 15),
(58, '2026-07-23 10:42:18', 'COMPLETADO', 15),
(59, '2026-07-23 10:54:00', 'COMPLETADO', 15),
(60, '2026-07-23 10:57:08', 'COMPLETADO', 15),
(61, '2026-07-23 11:00:07', 'COMPLETADO', 15),
(62, '2026-07-23 11:00:20', 'COMPLETADO', 15),
(63, '2026-07-23 11:04:08', 'COMPLETADO', 15),
(64, '2026-07-23 11:16:08', 'COMPLETADO', 15),
(65, '2026-07-23 11:17:04', 'COMPLETADO', 15),
(66, '2026-07-23 11:40:07', 'COMPLETADO', 15),
(67, '2026-07-23 11:41:25', 'COMPLETADO', 15),
(68, '2026-07-23 11:44:45', 'COMPLETADO', 15),
(69, '2026-07-23 11:47:35', 'COMPLETADO', 15),
(70, '2026-07-23 12:00:04', 'COMPLETADO', 15),
(71, '2026-07-23 12:05:43', 'COMPLETADO', 15),
(72, '2026-07-23 12:13:27', 'COMPLETADO', 15),
(73, '2026-07-23 12:22:38', 'COMPLETADO', 5),
(74, '2026-07-23 12:24:31', 'ACTIVO', 1),
(75, '2026-07-23 12:43:18', 'COMPLETADO', 15),
(83, '2026-07-23 15:35:22', 'ACTIVO', 23),
(84, '2026-07-23 17:04:40', 'ACTIVO', 17),
(85, '2026-07-24 00:24:30', 'ACTIVO', 5),
(86, '2026-07-24 08:33:58', 'COMPLETADO', 15),
(87, '2026-07-25 14:37:05', 'COMPLETADO', 15),
(88, '2026-07-25 15:08:11', 'COMPLETADO', 15),
(89, '2026-07-25 15:43:17', 'ACTIVO', 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `imagen` varchar(500) DEFAULT NULL,
  `grupo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nombre`, `descripcion`, `imagen`, `grupo`) VALUES
(1, 'Vegetales', 'Verduras y hortalizas frescas', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQs6jwEi4PdFrD-HV4NgyIuH3QCcZpt5sh7ypOoAhhBVB7rxTTC5QBGioX0&s=10', 'Comida'),
(2, 'Frutas', 'Frutas nacionales e importadas', 'https://media.istockphoto.com/id/182810893/es/foto/mezcla-de-frutas.jpg?s=612x612&w=0&k=20&c=ntg-8kQNeO75_hz-XEkq--L7SeBUCJk3XUml6-oTJrU=', 'Comida'),
(3, 'Carne y aves', 'Carnes rojas, pollo y derivados', 'https://edualimentaria.com/images/carnes/carnes-derivados-cecinas.jpg', 'Comida'),
(4, 'Pescados y mariscos', 'Productos marinos frescos y congelados', 'https://media.istockphoto.com/id/514674889/es/foto/surtido-de-pescado-crudo.jpg?s=612x612&w=0&k=20&c=SRSZDXi7eMl1t9A4mHUQYHs-oovHeF9WZZtFXJ3twJE=', 'Comida'),
(5, 'Lácteos y huevos', 'Leche, quesos, yogurt y huevos', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR7X6JSDqSfO377P3htAeTolXUQA24QLJL8j3LNIur6PF3U-TXYYiVyvVbX&s=10', 'Comida'),
(6, 'Panadería', 'Panes, pasteles y productos horneados', 'https://media.istockphoto.com/id/880444440/es/foto/varios-de-los-productos-de-panaderia-aislados-sobre-fondo-blanco.jpg?s=612x612&w=0&k=20&c=ZQte0O5E7fORk7LrcZHTjdCgG9YI3-Q8rgV19Y4ll1M=', 'Comida'),
(7, 'Pastas y granos', 'Arroz, fideos, menestras y cereales', 'https://thumbs.dreamstime.com/b/manojo-baguette-macarrones-y-pastas-del-trigo-en-tarro-en-el-fondo-blanco-ramo-y-pan-del-grano-espiguillas-de-oro-alimento-88343321.jpg', 'Comida'),
(8, 'Cereales y botanas', 'Snacks, galletas y cereales', 'https://thumbs.dreamstime.com/b/variedad-de-comida-chatarra-con-fondo-blanco-que-muestra-una-tentempi%C3%A9s-r%C3%A1pida-y-golosinas-para-la-fotograf%C3%ADa-e-inspiraci%C3%B3n-404381175.jpg', 'Comida'),
(9, 'Bebidas con alcohol', 'Cervezas, vinos y licores', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ_mhtF8hqLmHmXJqIM5r20ZXDS8_QNMggGWyAEhdeRYcS0SxBgjXQPZ5DI&s=10', 'Bebidas'),
(10, 'Bebidas sin alcohol', 'Gaseosas, jugos y agua', 'https://i.pinimg.com/736x/c4/33/86/c433864e6c61c6cd4611d2cf80328e3d.jpg', 'Bebidas'),
(11, 'Casa y cocina', 'Artículos para el hogar y cocina', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQeyRxah7vHKDvNW3OgkDKgOpA9iJVWdcBJe2KrEcDfTaEEFOrG2qc9UuM&s=10', 'Limpieza del hogar'),
(12, 'Productos de limpieza', 'Detergentes y productos de aseo', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSKuosTuhy4V-U829GxbzwEk_a1txSpbnD7me_T0_EjqhIA1Jcqd3y9ic5y&s=10', 'Limpieza del hogar'),
(13, 'Higiene personal', 'Shampoo, jabón y cuidado personal', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSJh6BkDOxe4jN1CXBAe5MPMaoXhgLi9P1Ae52AIMOmyU6EtdXXh2N2MYUm&s=10', 'Cuidado personal'),
(14, 'Bebés', 'Pañales, leche y accesorios para bebé', 'https://images.ctfassets.net/zmk7e03n1uhr/hxtHdAXKjRSzbCOwlyTfp/4b126bd8bdcd9d46d3c9de81d7043609/img-box-classic-produts-new-es-pe-?fm=webp&w=3840', 'Cuidado personal');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chatbot_faq`
--

CREATE TABLE `chatbot_faq` (
  `id_faq` int(11) NOT NULL,
  `pregunta` varchar(255) NOT NULL,
  `respuesta` text NOT NULL,
  `palabras_clave` varchar(255) DEFAULT NULL,
  `categoria` varchar(50) DEFAULT 'General',
  `orden` int(11) DEFAULT 0,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `chatbot_faq`
--

INSERT INTO `chatbot_faq` (`id_faq`, `pregunta`, `respuesta`, `palabras_clave`, `categoria`, `orden`, `estado`) VALUES
(1, '¿Cuáles son los métodos de pago?', 'Aceptamos varios métodos de pago: Efectivo al momento de entrega o recojo, Tarjeta de Crédito/Débito (Visa, Mastercard) y transferencias directas por Yape / Plin.', 'metodos de pago, efectivo, tarjeta, yape, plin, como pagar', 'Pagos', 1, 1),
(2, '¿Cuánto cuesta el delivery y cuáles son las zonas?', 'El costo de envío estándar es de S/ 5.00 a todo Chiclayo y zonas aledañas. Realizamos entregas directas a tu domicilio.', 'costo delivery, precio envio, zonas cobertura, cuanto cuesta envio, delivery', 'Delivery', 2, 1),
(3, '¿Cuáles son los horarios de atención?', 'Nuestro horario de atención en tienda y para pedidos por delivery es de Lunes a Domingo de 7:00 am a 10:00 pm.', 'horarios, hora de atencion, cuando abren, horario tienda, atencion', 'General', 3, 1),
(4, '¿Cómo realizar un pedido en la tienda?', 'Navega por nuestro catálogo de productos, selecciona lo que necesites, agrégalos al carrito y haz clic en \"Finalizar Compra\". Podrás elegir delivery o recojo en tienda.', 'como comprar, hacer pedido, realizar pedido, comprar productos', 'Pedidos', 4, 1),
(5, '¿Cómo rastrear mi pedido?', 'Si iniciaste sesión, ve a \"Mis Pedidos\" en tu perfil para ver el estado en tiempo real (\"En preparación\", \"En camino\", \"Entregado\"). Si compraste como invitado, ingresa a la sección \"Rastrear Pedido\" con tu DNI y número de teléfono.', 'rastrear pedido, estado del pedido, donde esta mi pedido, mi pedido, seguimiento', 'Pedidos', 5, 1),
(6, '¿Dónde se encuentra ubicada la tienda?', 'Nos encontramos ubicados en Urb. Los Sauces 6448 – Chiclayo, Lambayeque, Perú. ¡Estaremos encantados de atenderte!', 'ubicacion, direccion, donde estan, donde queda la tienda', 'General', 6, 1),
(7, '¿Qué hago si tengo un problema con mi compra?', 'Puedes escribirnos directamente a nuestra sección de \"Atención al Cliente\" desde el menú principal o llamarnos/escribirnos a nuestro WhatsApp de soporte.', 'reclamo, ayuda, problema, soporte, atencion al cliente, devolucion', 'Soporte', 7, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comprobante`
--

CREATE TABLE `comprobante` (
  `id_comprobante` int(11) NOT NULL,
  `tipo_comprobante` varchar(50) NOT NULL,
  `serie` varchar(10) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `fecha_emision` datetime DEFAULT current_timestamp(),
  `subtotal` decimal(10,2) NOT NULL,
  `igv` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` varchar(50) DEFAULT 'Emitido',
  `id_pedido` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `comprobante`
--

INSERT INTO `comprobante` (`id_comprobante`, `tipo_comprobante`, `serie`, `numero`, `fecha_emision`, `subtotal`, `igv`, `total`, `estado`, `id_pedido`) VALUES
(1, 'Boleta', 'B001', '00000001', '2026-07-04 19:22:31', 10.00, 1.80, 11.80, 'Emitido', 1),
(2, 'Boleta', 'B001', '00000002', '2026-07-05 08:15:53', 24.68, 4.44, 34.12, 'Emitido', 2),
(3, 'Boleta', 'B001', '00000003', '2026-07-06 16:25:49', 22.80, 4.10, 31.90, 'Emitido', 3),
(4, 'Boleta', 'B001', '00000004', '2026-07-07 06:11:52', 10.68, 1.92, 17.60, 'Emitido', 4),
(5, 'Boleta', 'B001', '00000005', '2026-07-07 15:56:10', 10.50, 1.89, 17.39, 'Emitido', 5),
(6, 'Boleta', 'B001', '00000006', '2026-07-07 16:17:25', 9.40, 1.69, 16.09, 'Emitido', 6),
(7, 'Boleta', 'B001', '00000007', '2026-07-07 22:35:15', 18.90, 3.40, 27.30, 'Emitido', 7),
(8, 'Boleta', 'B001', '00000008', '2026-07-07 22:51:08', 16.43, 2.96, 24.39, 'Emitido', 8),
(9, 'Boleta', 'B001', '00000009', '2026-07-07 22:51:37', 5.33, 0.96, 11.29, 'Emitido', 9),
(10, 'Boleta', 'B001', '00000010', '2026-07-08 01:19:25', 40.40, 7.27, 52.67, 'Emitido', 10),
(11, 'Boleta', 'B001', '00000011', '2026-07-08 01:41:14', 1.50, 0.27, 6.77, 'Emitido', 11),
(12, 'Boleta', 'B001', '00000012', '2026-07-08 01:46:04', 29.90, 5.38, 40.28, 'Emitido', 12),
(13, 'Boleta', 'B001', '00000013', '2026-07-08 14:40:14', 9.90, 1.78, 16.68, 'Emitido', 13),
(14, 'Boleta', 'B001', '00000014', '2026-07-08 14:46:37', 9.84, 1.77, 16.61, 'Emitido', 14),
(15, 'Boleta', 'B001', '00000015', '2026-07-08 14:50:22', 1.88, 0.34, 7.21, 'Emitido', 15),
(16, 'Boleta', 'B001', '00000016', '2026-07-16 23:11:04', 14.50, 2.61, 22.11, 'Emitido', 16),
(17, 'Boleta', 'B001', '00000017', '2026-07-22 16:13:07', 11.35, 2.04, 18.39, 'Emitido', 17),
(18, 'Boleta', 'B001', '00000018', '2026-07-22 17:53:44', 15.99, 2.88, 23.87, 'Emitido', 18),
(19, 'Boleta', 'B001', '00000019', '2026-07-22 17:55:56', 9.84, 1.77, 16.61, 'Emitido', 19),
(20, 'Boleta', 'B001', '00000020', '2026-07-22 18:25:49', 41.40, 7.45, 48.85, 'Emitido', 20),
(21, 'Boleta', 'B001', '00000021', '2026-07-22 19:07:54', 120.99, 21.78, 147.77, 'Emitido', 21),
(22, 'Boleta', 'B001', '00000022', '2026-07-22 19:18:24', 16.43, 2.96, 24.38, 'Emitido', 22),
(23, 'Boleta', 'B001', '00000023', '2026-07-22 19:18:30', 16.43, 2.96, 24.38, 'Emitido', 23),
(24, 'Boleta', 'B001', '00000024', '2026-07-22 19:18:38', 16.43, 2.96, 24.38, 'Emitido', 24),
(25, 'Boleta', 'B001', '00000025', '2026-07-22 19:19:03', 16.43, 2.96, 24.38, 'Emitido', 25),
(26, 'Boleta', 'B001', '00000026', '2026-07-22 19:24:29', 16.43, 2.96, 24.38, 'Emitido', 26),
(27, 'Boleta', 'B001', '00000027', '2026-07-22 19:30:37', 26.31, 4.74, 31.05, 'Emitido', 27),
(28, 'Boleta', 'B001', '00000028', '2026-07-22 19:37:27', 24.68, 4.44, 34.12, 'Emitido', 28),
(29, 'Boleta', 'B001', '00000029', '2026-07-22 19:50:31', 25.85, 4.65, 30.50, 'Emitido', 29),
(30, 'Boleta', 'B001', '00000030', '2026-07-22 19:54:44', 65.00, 11.70, 76.70, 'Emitido', 30),
(31, 'Boleta', 'B001', '00000031', '2026-07-22 19:55:13', 5.33, 0.96, 6.29, 'Emitido', 31),
(32, 'Boleta', 'B001', '00000032', '2026-07-22 19:58:36', 24.68, 4.44, 29.12, 'Emitido', 32),
(33, 'Boleta', 'B001', '00000033', '2026-07-22 20:01:37', 6.00, 1.08, 7.08, 'Emitido', 33),
(34, 'Boleta', 'B001', '00000034', '2026-07-22 20:02:01', 3.40, 0.61, 4.01, 'Emitido', 34),
(35, 'Boleta', 'B001', '00000035', '2026-07-22 20:10:32', 16.43, 2.96, 19.39, 'Emitido', 35),
(36, 'Boleta', 'B001', '00000036', '2026-07-22 20:14:16', 1.50, 0.27, 1.77, 'Emitido', 36),
(37, 'Boleta', 'B001', '00000037', '2026-07-22 20:15:47', 13.88, 2.50, 16.38, 'Emitido', 37),
(38, 'Boleta', 'B001', '00000038', '2026-07-22 20:17:01', 15.99, 2.88, 23.87, 'Emitido', 38),
(39, 'Boleta', 'B001', '00000039', '2026-07-22 20:18:39', 12.23, 2.20, 19.43, 'Emitido', 39),
(40, 'Boleta', 'B001', '00000040', '2026-07-22 20:32:16', 35.00, 6.30, 41.30, 'Emitido', 40),
(41, 'Boleta', 'B001', '00000041', '2026-07-22 20:34:35', 11.90, 2.14, 14.04, 'Emitido', 41),
(42, 'Boleta', 'B001', '00000042', '2026-07-22 20:49:50', 12.23, 2.20, 14.43, 'Emitido', 42),
(43, 'Boleta', 'B001', '00000043', '2026-07-22 20:53:04', 54.62, 9.83, 69.45, 'Emitido', 43),
(44, 'Boleta', 'B001', '00000044', '2026-07-22 20:54:50', 12.50, 2.25, 14.75, 'Emitido', 44),
(45, 'Boleta', 'B001', '00000045', '2026-07-22 20:57:25', 65.34, 11.76, 77.10, 'Emitido', 45),
(46, 'Boleta', 'B001', '00000046', '2026-07-22 21:00:27', 69.38, 12.49, 86.86, 'Emitido', 46),
(47, 'Boleta', 'B001', '00000047', '2026-07-22 21:03:50', 15.99, 2.88, 18.87, 'Emitido', 47),
(48, 'Boleta', 'B001', '00000048', '2026-07-22 21:04:20', 54.90, 9.88, 64.78, 'Emitido', 48),
(49, 'Boleta', 'B001', '00000049', '2026-07-22 21:08:13', 22.50, 4.05, 26.55, 'Emitido', 49),
(50, 'Boleta', 'B001', '00000050', '2026-07-22 21:08:39', 13.88, 2.50, 16.38, 'Emitido', 50),
(51, 'Boleta', 'B001', '00000051', '2026-07-22 21:10:59', 19.60, 3.53, 23.13, 'Emitido', 51),
(52, 'Boleta', 'B001', '00000052', '2026-07-22 21:13:01', 5.33, 0.96, 11.29, 'Emitido', 52),
(53, 'Boleta', 'B001', '00000053', '2026-07-22 21:14:54', 11.35, 2.04, 18.39, 'Emitido', 53),
(54, 'Boleta', 'B001', '00000054', '2026-07-22 21:18:06', 21.90, 3.94, 25.84, 'Emitido', 54),
(55, 'Boleta', 'B001', '00000055', '2026-07-22 21:21:20', 69.15, 12.45, 81.60, 'Emitido', 55),
(56, 'Boleta', 'B001', '00000056', '2026-07-22 21:22:40', 8.50, 1.53, 10.03, 'Emitido', 56),
(57, 'Boleta', 'B001', '00000057', '2026-07-22 21:58:26', 18.50, 3.33, 21.83, 'Emitido', 57),
(58, 'Boleta', 'B001', '00000058', '2026-07-22 22:01:53', 37.75, 6.80, 49.55, 'Emitido', 58),
(59, 'Boleta', 'B001', '00000059', '2026-07-23 10:32:01', 97.30, 17.51, 114.81, 'Emitido', 59),
(60, 'Boleta', 'B001', '00000060', '2026-07-23 10:32:09', 97.30, 17.51, 114.81, 'Emitido', 60),
(61, 'Boleta', 'B001', '00000061', '2026-07-23 10:33:02', 97.30, 17.51, 114.81, 'Emitido', 61),
(62, 'Boleta', 'B001', '00000062', '2026-07-23 10:34:10', 97.30, 17.51, 114.81, 'Emitido', 62),
(63, 'Boleta', 'B001', '00000063', '2026-07-23 10:35:06', 21.90, 3.94, 25.84, 'Emitido', 63),
(64, 'Boleta', 'B001', '00000064', '2026-07-23 10:35:17', 21.90, 3.94, 25.84, 'Emitido', 64),
(65, 'Boleta', 'B001', '00000065', '2026-07-23 10:35:37', 21.90, 3.94, 25.84, 'Emitido', 65),
(66, 'Boleta', 'B001', '00000066', '2026-07-23 10:38:21', 21.90, 3.94, 25.84, 'Emitido', 66),
(67, 'Boleta', 'B001', '00000067', '2026-07-23 10:38:34', 21.90, 3.94, 25.84, 'Emitido', 67),
(68, 'Boleta', 'B001', '00000068', '2026-07-23 10:41:30', 32.90, 5.92, 38.82, 'Emitido', 68),
(69, 'Boleta', 'B001', '00000069', '2026-07-23 10:41:43', 32.90, 5.92, 38.82, 'Emitido', 69),
(70, 'Boleta', 'B001', '00000070', '2026-07-23 10:42:18', 21.90, 3.94, 30.84, 'Emitido', 70),
(71, 'Boleta', 'B001', '00000071', '2026-07-23 10:54:00', 18.50, 3.33, 26.83, 'Emitido', 71),
(72, 'Boleta', 'B001', '00000072', '2026-07-23 10:57:08', 21.90, 3.94, 30.84, 'Emitido', 72),
(73, 'Boleta', 'B001', '00000073', '2026-07-23 10:59:04', 10.00, 1.80, 16.80, 'Emitido', 73),
(74, 'Boleta', 'B001', '00000074', '2026-07-23 11:00:07', 18.50, 3.33, 21.83, 'Emitido', 74),
(75, 'Boleta', 'B001', '00000075', '2026-07-23 11:00:20', 18.50, 3.33, 21.83, 'Emitido', 75),
(76, 'Boleta', 'B001', '00000076', '2026-07-23 11:03:26', 10.00, 1.80, 16.80, 'Emitido', 76),
(77, 'Boleta', 'B001', '00000077', '2026-07-23 11:03:33', 10.00, 1.80, 16.80, 'Emitido', 77),
(78, 'Boleta', 'B001', '00000078', '2026-07-23 11:04:08', 3.00, 0.54, 3.54, 'Emitido', 78),
(79, 'Boleta', 'B001', '00000079', '2026-07-23 11:16:08', 9.90, 1.78, 16.68, 'Emitido', 79),
(80, 'Boleta', 'B001', '00000080', '2026-07-23 11:17:04', 22.90, 4.12, 32.02, 'Emitido', 80),
(81, 'Boleta', 'B001', '00000081', '2026-07-23 11:40:07', 6.50, 1.17, 12.67, 'Emitido', 81),
(82, 'Boleta', 'B001', '00000082', '2026-07-23 11:41:25', 32.90, 5.92, 43.82, 'Emitido', 82),
(83, 'Boleta', 'B001', '00000083', '2026-07-23 11:44:45', 5.00, 0.90, 10.90, 'Emitido', 83),
(84, 'Boleta', 'B001', '00000084', '2026-07-23 11:47:35', 32.90, 5.92, 43.82, 'Emitido', 84),
(85, 'Boleta', 'B001', '00000085', '2026-07-23 12:00:04', 37.10, 6.68, 48.78, 'Emitido', 85),
(86, 'Boleta', 'B001', '00000086', '2026-07-23 12:05:43', 25.50, 4.59, 35.09, 'Emitido', 86),
(87, 'Boleta', 'B001', '00000087', '2026-07-23 12:13:27', 12.00, 2.16, 19.16, 'Emitido', 87),
(88, 'Boleta', 'B001', '00000088', '2026-07-23 12:14:38', 4.25, 0.77, 10.02, 'Emitido', 88),
(89, 'Boleta', 'B001', '00000089', '2026-07-23 12:43:19', 21.90, 3.94, 30.84, 'Emitido', 89),
(90, 'Boleta', 'B001', '00000090', '2026-07-23 16:08:20', 1.88, 0.34, 7.22, 'Emitido', 90),
(91, 'Boleta', 'B001', '00000091', '2026-07-23 16:14:02', 22.50, 4.05, 31.55, 'Emitido', 91),
(92, 'Boleta', 'B001', '00000092', '2026-07-24 00:24:30', 53.22, 9.58, 67.80, 'Emitido', 93),
(93, 'Boleta', 'B001', '00000093', '2026-07-24 08:33:58', 44.83, 8.07, 52.90, 'Emitido', 94),
(94, 'Boleta', 'B001', '00000094', '2026-07-25 14:37:05', 18.56, 3.34, 21.90, 'Emitido', 95),
(95, 'Boleta', 'B001', '00000095', '2026-07-25 15:08:11', 29.66, 5.34, 40.00, 'Emitido', 96),
(96, 'Boleta', 'B001', '00000096', '2026-07-25 15:43:17', 8.05, 1.45, 14.50, 'Emitido', 97);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `delivery`
--

CREATE TABLE `delivery` (
  `id_delivery` int(11) NOT NULL,
  `direccion_entrega` varchar(255) NOT NULL,
  `referencia` varchar(255) DEFAULT NULL,
  `costo_delivery` decimal(10,2) DEFAULT NULL,
  `estado_delivery` varchar(50) DEFAULT NULL,
  `motivo_rechazo` varchar(255) DEFAULT NULL,
  `hora_salida` datetime DEFAULT NULL,
  `hora_entrega` datetime DEFAULT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_repartidor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `delivery`
--

INSERT INTO `delivery` (`id_delivery`, `direccion_entrega`, `referencia`, `costo_delivery`, `estado_delivery`, `motivo_rechazo`, `hora_salida`, `hora_entrega`, `id_pedido`, `id_repartidor`) VALUES
(1, 'av grau123, Chiclayo', 'puerta grande', 5.00, 'Entregado', NULL, '2026-07-07 06:13:43', '2026-07-07 22:51:55', 3, NULL),
(2, 'av grau123, Chiclayo', 'puerta grande', 5.00, 'Entregado', NULL, '2026-07-07 06:23:48', '2026-07-07 21:45:37', 4, NULL),
(3, 'av grau, Chiclayo, Chiclayo', NULL, 5.00, 'Entregado', NULL, '2026-07-07 15:58:06', '2026-07-07 21:42:23', 5, 3),
(4, 'av grau, Chiclayo', NULL, 5.00, 'Entregado', NULL, '2026-07-07 22:43:25', '2026-07-07 22:43:30', 5, 3),
(5, 'av grau, Chiclayo, Chiclayo', NULL, 5.00, 'Entregado', NULL, '2026-07-07 22:43:34', '2026-07-07 22:43:37', 6, 3),
(6, 'av grau, Chiclayo, Chiclayo', NULL, 5.00, 'Entregado', NULL, '2026-07-07 22:43:04', '2026-07-07 22:43:23', 7, 3),
(7, 'av grau, Chiclayo, Chiclayo', NULL, 5.00, 'Entregado', NULL, '2026-07-08 14:38:44', '2026-07-08 14:38:48', 8, 3),
(8, 'av grau, Chiclayo, Chiclayo', NULL, 5.00, 'Entregado', NULL, '2026-07-08 14:41:53', '2026-07-08 14:41:55', 9, NULL),
(9, 'av grau123, Chiclayo, Chiclayo', 'puerta grande', 5.00, 'Entregado', NULL, '2026-07-08 01:42:40', '2026-07-08 01:42:53', 10, 3),
(10, 'av grau, Chiclayo, Chiclayo', NULL, 5.00, 'Entregado', NULL, '2026-07-08 01:41:48', '2026-07-08 01:42:30', 11, 3),
(11, 'av grau, Chiclayo, Chiclayo', NULL, 5.00, 'Entregado', NULL, '2026-07-08 01:47:49', '2026-07-08 14:38:41', 12, 3),
(12, 'av grau, Chiclayo, Chiclayo', NULL, 5.00, 'Entregado', NULL, '2026-07-08 14:41:46', '2026-07-08 14:47:07', 13, 3),
(13, 'av grau, Chiclayo, Chiclayo', NULL, 5.00, 'Entregado', NULL, '2026-07-08 14:47:50', '2026-07-08 14:47:54', 14, 3),
(14, 'av grau123, Chiclayo, Chiclayo', 'puerta grande', 5.00, 'Entregado', NULL, '2026-07-08 15:13:08', '2026-07-08 15:13:10', 15, 3),
(15, 'av grau, Chiclayo, Chiclayo', NULL, 5.00, 'Entregado', NULL, '2026-07-22 16:09:55', '2026-07-22 16:13:35', 16, 3),
(16, 'ferreñafe, Chiclayo, Chiclayo', NULL, 5.00, 'Entregado', NULL, '2026-07-22 16:13:37', '2026-07-22 18:04:47', 17, 3),
(17, 'salaverry, Chiclayo', NULL, 5.00, 'Entregado', NULL, '2026-07-22 21:55:59', '2026-07-22 21:59:04', 1, 3),
(18, 'ferreñafe, Chiclayo, Chiclayo', NULL, 5.00, 'Entregado', NULL, '2026-07-23 12:23:46', '2026-07-23 12:42:32', 18, 3),
(19, 'ferreñafe, Chiclayo, Chiclayo', NULL, 5.00, 'Pendiente', NULL, NULL, NULL, 19, NULL),
(20, 'Sin dirección', 'Pedido de invitado', 5.00, 'Pendiente', NULL, NULL, NULL, 21, NULL),
(21, 'Sin dirección', 'Pedido de invitado', 5.00, 'Pendiente', NULL, NULL, NULL, 22, NULL),
(22, 'Sin dirección', 'Pedido de invitado', 5.00, 'Pendiente', NULL, NULL, NULL, 23, NULL),
(23, 'Sin dirección', 'Pedido de invitado', 5.00, 'Pendiente', NULL, NULL, NULL, 24, NULL),
(24, 'Sin dirección', 'Pedido de invitado', 5.00, 'Pendiente', NULL, NULL, NULL, 25, NULL),
(25, 'Chiclayo', 'Pedido de invitado', 5.00, 'Pendiente', NULL, NULL, NULL, 26, NULL),
(26, 'Pj amplic victor haya de la torre, Mz. J, Lt. 09, José Leonardo Ortiz', 'Nombre: PAOLA DEL CARMEN LUCIA SERNAQUE ABAD | Tel: 955728700', 5.00, 'Entregado', NULL, '2026-07-22 19:39:03', '2026-07-22 21:29:32', 28, 3),
(27, 'Pj amplic victor haya de la torre, Mz. J, Lt. 09, José Leonardo Ortiz', 'Nombre: LUCY LINA FONSECA ESPINOZA | Tel: 955728700', 5.00, 'Pendiente', NULL, NULL, NULL, 38, NULL),
(28, 'Pj amplic victor haya de la torre, Mz. J, Lt. 09, La Victoria', 'Nombre: JHANEYRA ALIAGA CANAYO | Tel: 955728700', 5.00, 'Pendiente', NULL, NULL, NULL, 39, NULL),
(29, 'av grau, Chiclayo, Chiclayo', NULL, 5.00, 'Cancelado', NULL, NULL, NULL, 43, NULL),
(30, 'av grau, Chiclayo, Chiclayo', NULL, 5.00, 'Cancelado', NULL, NULL, NULL, 46, NULL),
(31, 'av grau, Chiclayo, Chiclayo', NULL, 5.00, 'Cancelado', NULL, NULL, NULL, 52, NULL),
(32, 'av grau, Chiclayo, Chiclayo', NULL, 5.00, 'Cancelado', 'Pepe Perez: Ocupado con otro pedido', NULL, NULL, 53, NULL),
(33, 'Pj amplic victor haya de la torre, Mz. J, Lt. 09, José Leonardo Ortiz', 'Nombre: PAOLA DEL CARMEN LUCIA SERNAQUE ABAD | Tel: 955728700', 5.00, 'Pendiente', NULL, NULL, NULL, 58, NULL),
(34, 'av grau, Chiclayo, Chiclayo', 'frente al mar', 5.00, 'Cancelado', NULL, NULL, NULL, 70, NULL),
(35, 'av grau, Chiclayo, Chiclayo', 'frente al mar', 5.00, 'Pendiente', NULL, NULL, NULL, 71, NULL),
(36, 'av grau, Chiclayo, Chiclayo', 'frente al mar', 5.00, 'Pendiente', NULL, NULL, NULL, 72, NULL),
(37, 'Calle 1, Lambayeque', 'Nombre: Juan Perez | Tel: 999999999 | Email: juan@example.com | Ref: Casa', 5.00, 'Pendiente', NULL, NULL, NULL, 73, NULL),
(38, 'Calle 1, Lambayeque', 'Nombre: Juan Perez | Tel: 999999999 | Email: juan@example.com | Ref: Casa', 5.00, 'Pendiente', NULL, NULL, NULL, 76, NULL),
(39, 'Calle 1, Lambayeque', 'Nombre: Juan Perez | Tel: 999999999 | Email: juan@example.com | Ref: Casa', 5.00, 'Pendiente', NULL, NULL, NULL, 77, NULL),
(40, 'av grau, Chiclayo, Chiclayo', 'frente al mar', 5.00, 'Pendiente', NULL, NULL, NULL, 79, NULL),
(41, 'av grau, Chiclayo, Chiclayo', NULL, 5.00, 'Pendiente', NULL, NULL, NULL, 80, NULL),
(42, 'av grau, Chiclayo, Chiclayo', 'frente al mar', 5.00, 'Pendiente', NULL, NULL, NULL, 81, NULL),
(43, 'av grau, Chiclayo, Chiclayo', 'frente al mar', 5.00, 'Pendiente', NULL, NULL, NULL, 82, NULL),
(44, 'av grau, Chiclayo, Chiclayo', 'frente al mar', 5.00, 'Pendiente', NULL, NULL, NULL, 83, NULL),
(45, 'av grau, Chiclayo, Chiclayo', 'frente al mar', 5.00, 'Pendiente', NULL, NULL, NULL, 84, NULL),
(46, 'av grau, Chiclayo, Chiclayo', 'frente al mar', 5.00, 'Pendiente', NULL, NULL, NULL, 85, NULL),
(47, 'av grau, Chiclayo, Chiclayo', 'frente al mar', 5.00, 'Pendiente', NULL, NULL, NULL, 86, NULL),
(48, 'av grau, Chiclayo, Chiclayo', 'frente al mar', 5.00, 'Pendiente', NULL, NULL, NULL, 87, NULL),
(49, 'Pj amplic victor haya de la torre, Mz. J, Lt. 09, Chiclayo', 'Nombre: PAOLA DEL CARMEN LUCIA SERNAQUE ABAD | Tel: 955728700', 5.00, 'Pendiente', NULL, NULL, NULL, 88, NULL),
(50, 'av grau, Chiclayo, Chiclayo', 'frente al mar', 5.00, 'Pendiente', NULL, NULL, NULL, 89, NULL),
(51, 'av no se, José Leonardo Ortiz', 'Nombre: GLORIA LILI QUISPE FARFAN | Tel: 967777777 | Ref: afuera', 5.00, 'Pendiente', NULL, NULL, NULL, 90, NULL),
(52, 'jlo, José Leonardo Ortiz', 'Nombre: yo no se | Tel: 999999999', 5.00, 'Pendiente', NULL, NULL, NULL, 91, NULL),
(53, 'av grau123, Chiclayo, Chiclayo', 'puerta grande', 5.00, 'Pendiente', NULL, NULL, NULL, 93, NULL),
(54, 'av grau, Chiclayo, Chiclayo', 'frente al mar', 5.00, 'Cancelado', NULL, NULL, NULL, 96, NULL),
(55, 'av grau, Chiclayo, Chiclayo', 'frente al mar', 5.00, 'Cancelado', NULL, NULL, NULL, 97, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_carrito`
--

CREATE TABLE `detalle_carrito` (
  `id_detalle_carrito` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `id_carrito` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_carrito`
--

INSERT INTO `detalle_carrito` (`id_detalle_carrito`, `cantidad`, `subtotal`, `id_carrito`, `id_producto`) VALUES
(1, 1, 10.00, 1, 162),
(2, 1, 24.68, 2, 211),
(3, 1, 8.90, 3, 248),
(4, 1, 13.90, 3, 244),
(5, 1, 4.68, 4, 136),
(6, 1, 6.00, 4, 141),
(7, 1, 10.50, 5, 254),
(8, 1, 6.00, 6, 148),
(9, 1, 3.40, 6, 143),
(10, 1, 18.90, 7, 256),
(11, 1, 16.43, 8, 205),
(12, 1, 5.33, 9, 225),
(13, 1, 10.50, 10, 254),
(14, 1, 29.90, 10, 255),
(15, 1, 1.50, 11, 218),
(16, 1, 29.90, 12, 255),
(17, 1, 9.90, 13, 229),
(18, 1, 9.84, 14, 234),
(19, 1, 1.88, 15, 214),
(20, 1, 14.50, 16, 196),
(21, 1, 11.35, 17, 253),
(22, 1, 15.99, 18, 223),
(23, 1, 9.84, 19, 234),
(24, 1, 26.31, 20, 255),
(25, 1, 11.35, 21, 253),
(26, 1, 14.50, 21, 196),
(27, 1, 65.00, 22, 157),
(28, 1, 5.33, 23, 225),
(29, 1, 24.68, 24, 211),
(30, 1, 6.00, 25, 148),
(31, 1, 3.40, 26, 143),
(32, 1, 16.43, 27, 205),
(33, 1, 1.50, 28, 218),
(34, 1, 13.88, 29, 206),
(35, 1, 12.50, 30, 258),
(36, 1, 22.50, 30, 257),
(37, 1, 11.90, 31, 239),
(38, 1, 12.23, 32, 244),
(39, 1, 2.00, 33, 222),
(40, 2, 52.62, 33, 255),
(41, 1, 15.99, 34, 223),
(42, 2, 49.35, 34, 211),
(43, 5, 69.38, 35, 206),
(44, 1, 15.99, 36, 223),
(45, 1, 54.90, 37, 210),
(46, 1, 22.50, 38, 257),
(47, 1, 13.88, 39, 206),
(48, 1, 19.60, 40, 228),
(49, 1, 5.33, 41, 225),
(50, 1, 11.35, 42, 245),
(51, 1, 21.90, 43, 205),
(52, 1, 12.50, 44, 258),
(53, 1, 18.90, 44, 256),
(54, 1, 37.75, 44, 250),
(55, 1, 8.50, 45, 249),
(62, 1, 13.00, 46, 155),
(63, 1, 18.50, 46, 206),
(64, 2, 65.80, 46, 211),
(65, 1, 16.43, 50, 205),
(66, 1, 16.43, 53, 205),
(67, 1, 24.68, 55, 211),
(68, 1, 16.43, 57, 205),
(69, 1, 13.88, 58, 206),
(70, 1, 16.43, 59, 205),
(71, 1, 13.88, 60, 206),
(73, 1, 3.00, 62, 215),
(75, 1, 9.90, 63, 229),
(77, 1, 22.90, 64, 213),
(79, 1, 6.50, 65, 225),
(81, 1, 32.90, 66, 211),
(83, 1, 5.00, 67, 144),
(85, 1, 32.90, 68, 211),
(89, 1, 4.20, 69, 192),
(90, 1, 32.90, 69, 211),
(94, 1, 6.00, 70, 148),
(95, 1, 19.50, 70, 223),
(97, 2, 12.00, 71, 148),
(99, 1, 21.90, 72, 205),
(153, 1, 32.90, 73, 211),
(154, 1, 29.90, 73, 255),
(182, 1, 18.90, 75, 212),
(183, 1, 19.50, 75, 223),
(184, 1, 14.50, 75, 235),
(187, 1, 21.90, 86, 205),
(196, 1, 22.50, 87, 257),
(197, 1, 12.50, 87, 258),
(201, 1, 9.50, 88, 251);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id_detalle_pedido` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_pedido`
--

INSERT INTO `detalle_pedido` (`id_detalle_pedido`, `cantidad`, `precio_unitario`, `subtotal`, `id_pedido`, `id_producto`) VALUES
(1, 1, 10.00, 10.00, 1, 162),
(2, 1, 24.68, 24.68, 2, 211),
(3, 1, 8.90, 8.90, 3, 248),
(4, 1, 13.90, 13.90, 3, 244),
(5, 1, 4.68, 4.68, 4, 136),
(6, 1, 6.00, 6.00, 4, 141),
(7, 1, 10.50, 10.50, 5, 254),
(8, 1, 6.00, 6.00, 6, 148),
(9, 1, 3.40, 3.40, 6, 143),
(10, 1, 18.90, 18.90, 7, 256),
(11, 1, 16.43, 16.43, 8, 205),
(12, 1, 5.33, 5.33, 9, 225),
(13, 1, 10.50, 10.50, 10, 254),
(14, 1, 29.90, 29.90, 10, 255),
(15, 1, 1.50, 1.50, 11, 218),
(16, 1, 29.90, 29.90, 12, 255),
(17, 1, 9.90, 9.90, 13, 229),
(18, 1, 9.84, 9.84, 14, 234),
(19, 1, 1.88, 1.88, 15, 214),
(20, 1, 14.50, 14.50, 16, 196),
(21, 1, 11.35, 11.35, 17, 253),
(22, 1, 15.99, 15.99, 18, 223),
(23, 1, 9.84, 9.84, 19, 234),
(24, 1, 18.90, 18.90, 20, 256),
(25, 1, 22.50, 22.50, 20, 257),
(26, 4, 22.50, 90.00, 21, 257),
(27, 1, 4.68, 4.68, 21, 136),
(28, 1, 26.31, 26.31, 21, 255),
(29, 1, 16.43, 16.43, 22, 205),
(30, 1, 16.43, 16.43, 23, 205),
(31, 1, 16.43, 16.43, 24, 205),
(32, 1, 16.43, 16.43, 25, 205),
(33, 1, 16.43, 16.43, 26, 205),
(34, 1, 26.31, 26.31, 27, 255),
(35, 1, 24.68, 24.68, 28, 211),
(36, 1, 11.35, 11.35, 29, 253),
(37, 1, 14.50, 14.50, 29, 196),
(38, 1, 65.00, 65.00, 30, 157),
(39, 1, 5.33, 5.33, 31, 225),
(40, 1, 24.68, 24.68, 32, 211),
(41, 1, 6.00, 6.00, 33, 148),
(42, 1, 3.40, 3.40, 34, 143),
(43, 1, 16.43, 16.43, 35, 205),
(44, 1, 1.50, 1.50, 36, 218),
(45, 1, 13.88, 13.88, 37, 206),
(46, 1, 15.99, 15.99, 38, 223),
(47, 1, 12.23, 12.23, 39, 244),
(48, 1, 12.50, 12.50, 40, 258),
(49, 1, 22.50, 22.50, 40, 257),
(50, 1, 11.90, 11.90, 41, 239),
(51, 1, 12.23, 12.23, 42, 244),
(52, 1, 2.00, 2.00, 43, 222),
(53, 2, 26.31, 52.62, 43, 255),
(54, 1, 12.50, 12.50, 44, 258),
(55, 1, 15.99, 15.99, 45, 223),
(56, 2, 24.68, 49.36, 45, 211),
(57, 5, 13.88, 69.40, 46, 206),
(58, 1, 15.99, 15.99, 47, 223),
(59, 1, 54.90, 54.90, 48, 210),
(60, 1, 22.50, 22.50, 49, 257),
(61, 1, 13.88, 13.88, 50, 206),
(62, 1, 19.60, 19.60, 51, 228),
(63, 1, 5.33, 5.33, 52, 225),
(64, 1, 11.35, 11.35, 53, 245),
(65, 1, 21.90, 21.90, 54, 205),
(66, 1, 12.50, 12.50, 55, 258),
(67, 1, 18.90, 18.90, 55, 256),
(68, 1, 37.75, 37.75, 55, 250),
(69, 1, 8.50, 8.50, 56, 249),
(70, 1, 18.50, 18.50, 57, 238),
(71, 1, 37.75, 37.75, 58, 250),
(72, 1, 13.00, 13.00, 59, 155),
(73, 1, 18.50, 18.50, 59, 206),
(74, 2, 32.90, 65.80, 59, 211),
(75, 1, 13.00, 13.00, 60, 155),
(76, 1, 18.50, 18.50, 60, 206),
(77, 2, 32.90, 65.80, 60, 211),
(78, 1, 13.00, 13.00, 61, 155),
(79, 1, 18.50, 18.50, 61, 206),
(80, 2, 32.90, 65.80, 61, 211),
(81, 1, 13.00, 13.00, 62, 155),
(82, 1, 18.50, 18.50, 62, 206),
(83, 2, 32.90, 65.80, 62, 211),
(84, 1, 21.90, 21.90, 63, 205),
(85, 1, 21.90, 21.90, 64, 205),
(86, 1, 21.90, 21.90, 65, 205),
(87, 1, 21.90, 21.90, 66, 205),
(88, 1, 21.90, 21.90, 67, 205),
(89, 1, 32.90, 32.90, 68, 211),
(90, 1, 32.90, 32.90, 69, 211),
(91, 1, 21.90, 21.90, 70, 205),
(92, 1, 18.50, 18.50, 71, 206),
(93, 1, 21.90, 21.90, 72, 205),
(94, 1, 10.00, 10.00, 73, 130),
(95, 1, 18.50, 18.50, 74, 206),
(96, 1, 18.50, 18.50, 75, 206),
(97, 1, 10.00, 10.00, 76, 130),
(98, 1, 10.00, 10.00, 77, 130),
(99, 1, 3.00, 3.00, 78, 215),
(100, 1, 9.90, 9.90, 79, 229),
(101, 1, 22.90, 22.90, 80, 213),
(102, 1, 6.50, 6.50, 81, 225),
(103, 1, 32.90, 32.90, 82, 211),
(104, 1, 5.00, 5.00, 83, 144),
(105, 1, 32.90, 32.90, 84, 211),
(106, 1, 4.20, 4.20, 85, 192),
(107, 1, 32.90, 32.90, 85, 211),
(108, 1, 6.00, 6.00, 86, 148),
(109, 1, 19.50, 19.50, 86, 223),
(110, 2, 6.00, 12.00, 87, 148),
(111, 1, 4.25, 4.25, 88, 144),
(112, 1, 21.90, 21.90, 89, 205),
(113, 1, 1.88, 1.88, 90, 214),
(114, 1, 22.50, 22.50, 91, 257),
(115, 1, 32.90, 32.90, 93, 211),
(116, 1, 29.90, 29.90, 93, 255),
(117, 1, 18.90, 18.90, 94, 212),
(118, 1, 19.50, 19.50, 94, 223),
(119, 1, 14.50, 14.50, 94, 235),
(120, 1, 21.90, 21.90, 95, 205),
(121, 1, 22.50, 22.50, 96, 257),
(122, 1, 12.50, 12.50, 96, 258),
(123, 1, 9.50, 9.50, 97, 251);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direccion`
--

CREATE TABLE `direccion` (
  `id_direccion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `etiqueta` varchar(50) NOT NULL,
  `departamento` varchar(100) NOT NULL,
  `provincia` varchar(100) NOT NULL,
  `distrito` varchar(100) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `referencia` varchar(255) DEFAULT NULL,
  `predeterminada` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `direccion`
--

INSERT INTO `direccion` (`id_direccion`, `id_usuario`, `etiqueta`, `departamento`, `provincia`, `distrito`, `direccion`, `referencia`, `predeterminada`) VALUES
(1, 5, 'Principal', 'Lambayeque', 'Chiclayo', 'Chiclayo', 'av grau123', 'puerta grande', 1),
(3, 6, 'Principal', 'Lambayeque', 'Chiclayo', 'Chiclayo', 'av grau 4600', NULL, 1),
(5, 15, 'Principal', 'Lambayeque', 'Chiclayo', 'Chiclayo', 'av grau', NULL, 0),
(6, 17, 'Principal', 'Lambayeque', 'Chiclayo', 'Chiclayo', 'salaverry', NULL, 1),
(7, 17, 'trabajo', 'Lambayeque', 'Chiclayo', 'Chiclayo', 'salaverry', 'Casa verde', 0),
(8, 19, 'Principal', 'Lambayeque', 'Chiclayo', 'Chiclayo', 'ferreñafe', NULL, 1),
(9, 20, 'Principal', 'Lambayeque', 'Chiclayo', 'Chiclayo', 'ferreñafe', NULL, 1),
(10, 15, 'casa', 'Lambayeque', 'Chiclayo', 'Chiclayo', 'av grau', 'frente al mar', 1),
(11, 23, 'Principal', 'Lambayeque', 'Chiclayo', 'Chiclayo', 'ferreñafe', NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodo_pago`
--

CREATE TABLE `metodo_pago` (
  `id_metodo_pago` int(11) NOT NULL,
  `nombre_metodo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `metodo_pago`
--

INSERT INTO `metodo_pago` (`id_metodo_pago`, `nombre_metodo`) VALUES
(1, 'Efectivo'),
(2, 'Tarjeta de Crédito/Débito'),
(3, 'Yape / Plin');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimiento_stock`
--

CREATE TABLE `movimiento_stock` (
  `id_movimiento` int(11) NOT NULL,
  `tipo_movimiento` varchar(50) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha_movimiento` datetime DEFAULT current_timestamp(),
  `motivo` varchar(255) DEFAULT NULL,
  `id_producto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `movimiento_stock`
--

INSERT INTO `movimiento_stock` (`id_movimiento`, `tipo_movimiento`, `cantidad`, `fecha_movimiento`, `motivo`, `id_producto`) VALUES
(1, 'SALIDA', 1, '2026-07-04 19:22:31', 'Pedido #1', 162),
(2, 'SALIDA', 1, '2026-07-05 08:15:53', 'Pedido #2', 211),
(3, 'SALIDA', 1, '2026-07-06 16:25:49', 'Pedido #3', 248),
(4, 'SALIDA', 1, '2026-07-06 16:25:49', 'Pedido #3', 244),
(5, 'SALIDA', 1, '2026-07-07 06:11:52', 'Pedido #4', 136),
(6, 'SALIDA', 1, '2026-07-07 06:11:52', 'Pedido #4', 141),
(7, 'SALIDA', 1, '2026-07-07 15:56:10', 'Pedido #5', 254),
(8, 'SALIDA', 1, '2026-07-07 16:17:25', 'Pedido #6', 148),
(9, 'SALIDA', 1, '2026-07-07 16:17:25', 'Pedido #6', 143),
(10, 'SALIDA', 1, '2026-07-07 22:35:15', 'Pedido #7', 256),
(11, 'SALIDA', 1, '2026-07-07 22:51:08', 'Pedido #8', 205),
(12, 'SALIDA', 1, '2026-07-07 22:51:37', 'Pedido #9', 225),
(13, 'SALIDA', 1, '2026-07-08 01:19:25', 'Pedido #10', 254),
(14, 'SALIDA', 1, '2026-07-08 01:19:25', 'Pedido #10', 255),
(15, 'SALIDA', 1, '2026-07-08 01:41:13', 'Pedido #11', 218),
(16, 'SALIDA', 1, '2026-07-08 01:46:04', 'Pedido #12', 255),
(17, 'SALIDA', 1, '2026-07-08 14:40:14', 'Pedido #13', 229),
(18, 'SALIDA', 1, '2026-07-08 14:46:37', 'Pedido #14', 234),
(19, 'SALIDA', 1, '2026-07-08 14:50:22', 'Pedido #15', 214),
(20, 'SALIDA', 1, '2026-07-16 23:11:04', 'Pedido #16', 196),
(21, 'SALIDA', 1, '2026-07-22 16:13:07', 'Pedido #17', 253),
(22, 'SALIDA', 1, '2026-07-22 17:53:44', 'Pedido #18', 223),
(23, 'SALIDA', 1, '2026-07-22 17:55:56', 'Pedido #19', 234),
(24, 'SALIDA', 1, '2026-07-22 18:25:49', 'Pedido #20', 256),
(25, 'SALIDA', 1, '2026-07-22 18:25:49', 'Pedido #20', 257),
(26, 'SALIDA', 4, '2026-07-22 19:07:54', 'Pedido #21', 257),
(27, 'SALIDA', 1, '2026-07-22 19:07:54', 'Pedido #21', 136),
(28, 'SALIDA', 1, '2026-07-22 19:07:54', 'Pedido #21', 255),
(29, 'SALIDA', 1, '2026-07-22 19:18:24', 'Pedido #22', 205),
(30, 'SALIDA', 1, '2026-07-22 19:18:30', 'Pedido #23', 205),
(31, 'SALIDA', 1, '2026-07-22 19:18:38', 'Pedido #24', 205),
(32, 'SALIDA', 1, '2026-07-22 19:19:03', 'Pedido #25', 205),
(33, 'SALIDA', 1, '2026-07-22 19:24:29', 'Pedido #26', 205),
(34, 'SALIDA', 1, '2026-07-22 19:30:37', 'Pedido #27', 255),
(35, 'SALIDA', 1, '2026-07-22 19:37:27', 'Pedido #28', 211),
(36, 'SALIDA', 1, '2026-07-22 19:50:31', 'Pedido #29', 253),
(37, 'SALIDA', 1, '2026-07-22 19:50:31', 'Pedido #29', 196),
(38, 'SALIDA', 1, '2026-07-22 19:54:44', 'Pedido #30', 157),
(39, 'SALIDA', 1, '2026-07-22 19:55:13', 'Pedido #31', 225),
(40, 'SALIDA', 1, '2026-07-22 19:58:36', 'Pedido #32', 211),
(41, 'SALIDA', 1, '2026-07-22 20:01:37', 'Pedido #33', 148),
(42, 'SALIDA', 1, '2026-07-22 20:02:01', 'Pedido #34', 143),
(43, 'SALIDA', 1, '2026-07-22 20:10:32', 'Pedido #35', 205),
(44, 'SALIDA', 1, '2026-07-22 20:14:16', 'Pedido #36', 218),
(45, 'SALIDA', 1, '2026-07-22 20:15:47', 'Pedido #37', 206),
(46, 'SALIDA', 1, '2026-07-22 20:17:01', 'Pedido #38', 223),
(47, 'SALIDA', 1, '2026-07-22 20:18:39', 'Pedido #39', 244),
(48, 'SALIDA', 1, '2026-07-22 20:32:16', 'Pedido #40', 258),
(49, 'SALIDA', 1, '2026-07-22 20:32:16', 'Pedido #40', 257),
(50, 'SALIDA', 1, '2026-07-22 20:34:35', 'Pedido #41', 239),
(51, 'SALIDA', 1, '2026-07-22 20:49:50', 'Pedido #42', 244),
(52, 'SALIDA', 1, '2026-07-22 20:53:04', 'Pedido #43', 222),
(53, 'SALIDA', 2, '2026-07-22 20:53:04', 'Pedido #43', 255),
(54, 'SALIDA', 1, '2026-07-22 20:54:50', 'Pedido #44', 258),
(55, 'SALIDA', 1, '2026-07-22 20:57:25', 'Pedido #45', 223),
(56, 'SALIDA', 2, '2026-07-22 20:57:25', 'Pedido #45', 211),
(57, 'SALIDA', 5, '2026-07-22 21:00:27', 'Pedido #46', 206),
(58, 'SALIDA', 1, '2026-07-22 21:03:50', 'Pedido #47', 223),
(59, 'SALIDA', 1, '2026-07-22 21:04:20', 'Pedido #48', 210),
(60, 'SALIDA', 1, '2026-07-22 21:08:13', 'Pedido #49', 257),
(61, 'SALIDA', 1, '2026-07-22 21:08:39', 'Pedido #50', 206),
(62, 'SALIDA', 1, '2026-07-22 21:10:59', 'Pedido #51', 228),
(63, 'SALIDA', 1, '2026-07-22 21:13:01', 'Pedido #52', 225),
(64, 'SALIDA', 1, '2026-07-22 21:14:54', 'Pedido #53', 245),
(65, 'SALIDA', 1, '2026-07-22 21:18:06', 'Pedido #54', 205),
(66, 'SALIDA', 1, '2026-07-22 21:21:20', 'Pedido #55', 258),
(67, 'SALIDA', 1, '2026-07-22 21:21:20', 'Pedido #55', 256),
(68, 'SALIDA', 1, '2026-07-22 21:21:20', 'Pedido #55', 250),
(69, 'SALIDA', 1, '2026-07-22 21:22:40', 'Pedido #56', 249),
(70, 'SALIDA', 1, '2026-07-22 21:58:26', 'Pedido #57', 238),
(71, 'SALIDA', 1, '2026-07-22 22:01:53', 'Pedido #58', 250),
(72, 'SALIDA', 1, '2026-07-23 10:32:01', 'Pedido #59', 155),
(73, 'SALIDA', 1, '2026-07-23 10:32:01', 'Pedido #59', 206),
(74, 'SALIDA', 2, '2026-07-23 10:32:01', 'Pedido #59', 211),
(75, 'SALIDA', 1, '2026-07-23 10:32:09', 'Pedido #60', 155),
(76, 'SALIDA', 1, '2026-07-23 10:32:09', 'Pedido #60', 206),
(77, 'SALIDA', 2, '2026-07-23 10:32:09', 'Pedido #60', 211),
(78, 'SALIDA', 1, '2026-07-23 10:33:02', 'Pedido #61', 155),
(79, 'SALIDA', 1, '2026-07-23 10:33:02', 'Pedido #61', 206),
(80, 'SALIDA', 2, '2026-07-23 10:33:02', 'Pedido #61', 211),
(81, 'SALIDA', 1, '2026-07-23 10:34:10', 'Pedido #62', 155),
(82, 'SALIDA', 1, '2026-07-23 10:34:10', 'Pedido #62', 206),
(83, 'SALIDA', 2, '2026-07-23 10:34:10', 'Pedido #62', 211),
(84, 'SALIDA', 1, '2026-07-23 10:35:06', 'Pedido #63', 205),
(85, 'SALIDA', 1, '2026-07-23 10:35:17', 'Pedido #64', 205),
(86, 'SALIDA', 1, '2026-07-23 10:35:37', 'Pedido #65', 205),
(87, 'SALIDA', 1, '2026-07-23 10:38:21', 'Pedido #66', 205),
(88, 'SALIDA', 1, '2026-07-23 10:38:34', 'Pedido #67', 205),
(89, 'SALIDA', 1, '2026-07-23 10:41:30', 'Pedido #68', 211),
(90, 'SALIDA', 1, '2026-07-23 10:41:43', 'Pedido #69', 211),
(91, 'SALIDA', 1, '2026-07-23 10:42:18', 'Pedido #70', 205),
(92, 'SALIDA', 1, '2026-07-23 10:54:00', 'Pedido #71', 206),
(93, 'SALIDA', 1, '2026-07-23 10:57:08', 'Pedido #72', 205),
(94, 'SALIDA', 1, '2026-07-23 10:59:04', 'Pedido #73', 130),
(95, 'SALIDA', 1, '2026-07-23 11:00:07', 'Pedido #74', 206),
(96, 'SALIDA', 1, '2026-07-23 11:00:20', 'Pedido #75', 206),
(97, 'SALIDA', 1, '2026-07-23 11:03:26', 'Pedido #76', 130),
(98, 'SALIDA', 1, '2026-07-23 11:03:33', 'Pedido #77', 130),
(99, 'SALIDA', 1, '2026-07-23 11:04:08', 'Pedido #78', 215),
(100, 'SALIDA', 1, '2026-07-23 11:16:08', 'Pedido #79', 229),
(101, 'SALIDA', 1, '2026-07-23 11:17:04', 'Pedido #80', 213),
(102, 'SALIDA', 1, '2026-07-23 11:40:07', 'Pedido #81', 225),
(103, 'SALIDA', 1, '2026-07-23 11:41:25', 'Pedido #82', 211),
(104, 'SALIDA', 1, '2026-07-23 11:44:45', 'Pedido #83', 144),
(105, 'SALIDA', 1, '2026-07-23 11:47:35', 'Pedido #84', 211),
(106, 'SALIDA', 1, '2026-07-23 12:00:04', 'Pedido #85', 192),
(107, 'SALIDA', 1, '2026-07-23 12:00:04', 'Pedido #85', 211),
(108, 'SALIDA', 1, '2026-07-23 12:05:43', 'Pedido #86', 148),
(109, 'SALIDA', 1, '2026-07-23 12:05:43', 'Pedido #86', 223),
(110, 'ENTRADA', 1, '2026-07-23 12:11:05', 'Cancelación Pedido #86', 148),
(111, 'ENTRADA', 1, '2026-07-23 12:11:05', 'Cancelación Pedido #86', 223),
(112, 'ENTRADA', 1, '2026-07-23 12:12:50', 'Cancelación Pedido #85', 192),
(113, 'ENTRADA', 1, '2026-07-23 12:12:50', 'Cancelación Pedido #85', 211),
(114, 'SALIDA', 2, '2026-07-23 12:13:27', 'Pedido #87', 148),
(115, 'SALIDA', 1, '2026-07-23 12:14:38', 'Pedido #88', 144),
(116, 'SALIDA', 1, '2026-07-23 12:43:19', 'Pedido #89', 205),
(117, 'SALIDA', 1, '2026-07-23 16:08:20', 'Pedido #90', 214),
(118, 'SALIDA', 1, '2026-07-23 16:14:02', 'Pedido #91', 257),
(119, 'ENTRADA', 2, '2026-07-23 16:14:57', 'Cancelación Pedido #87', 148),
(120, 'ENTRADA', 1, '2026-07-23 16:15:01', 'Cancelación Pedido #89', 205),
(121, 'ENTRADA', 1, '2026-07-23 16:15:29', 'Cancelación Pedido #78', 215),
(122, 'ENTRADA', 1, '2026-07-23 16:21:56', 'Cancelación Pedido #84', 211),
(123, 'ENTRADA', 1, '2026-07-23 16:25:47', 'Cancelación Pedido #83', 144),
(124, 'ENTRADA', 1, '2026-07-23 16:26:28', 'Cancelación Pedido #82', 211),
(125, 'ENTRADA', 1, '2026-07-23 16:26:51', 'Cancelación Pedido #81', 225),
(126, 'ENTRADA', 1, '2026-07-23 16:27:06', 'Cancelación Pedido #80', 213),
(127, 'ENTRADA', 1, '2026-07-23 16:35:12', 'Cancelación Pedido #72', 205),
(128, 'ENTRADA', 1, '2026-07-23 16:35:18', 'Cancelación Pedido #79', 229),
(129, 'ENTRADA', 1, '2026-07-23 18:00:05', 'Cancelación Pedido #71', 206),
(130, 'SALIDA', 1, '2026-07-24 00:24:30', 'Pedido #93', 211),
(131, 'SALIDA', 1, '2026-07-24 00:24:30', 'Pedido #93', 255),
(132, 'ENTRADA', 1, '2026-07-24 08:24:16', 'Cancelación Pedido #74', 206),
(133, 'ENTRADA', 1, '2026-07-24 08:24:20', 'Cancelación Pedido #70', 205),
(134, 'ENTRADA', 1, '2026-07-24 08:24:27', 'Cancelación Pedido #69', 211),
(135, 'ENTRADA', 1, '2026-07-24 08:24:36', 'Cancelación Pedido #68', 211),
(136, 'ENTRADA', 1, '2026-07-24 08:24:46', 'Cancelación Pedido #55', 258),
(137, 'ENTRADA', 1, '2026-07-24 08:24:46', 'Cancelación Pedido #55', 256),
(138, 'ENTRADA', 1, '2026-07-24 08:24:46', 'Cancelación Pedido #55', 250),
(139, 'ENTRADA', 1, '2026-07-24 08:24:49', 'Cancelación Pedido #67', 205),
(140, 'ENTRADA', 1, '2026-07-24 08:24:54', 'Cancelación Pedido #63', 205),
(141, 'ENTRADA', 1, '2026-07-24 08:24:59', 'Cancelación Pedido #66', 205),
(142, 'ENTRADA', 1, '2026-07-24 08:25:05', 'Cancelación Pedido #65', 205),
(143, 'ENTRADA', 1, '2026-07-24 08:25:11', 'Cancelación Pedido #64', 205),
(144, 'ENTRADA', 1, '2026-07-24 08:25:21', 'Cancelación Pedido #31', 225),
(145, 'ENTRADA', 1, '2026-07-24 08:25:25', 'Cancelación Pedido #49', 257),
(146, 'ENTRADA', 1, '2026-07-24 08:25:30', 'Cancelación Pedido #52', 225),
(147, 'ENTRADA', 1, '2026-07-24 08:25:34', 'Cancelación Pedido #53', 245),
(148, 'ENTRADA', 1, '2026-07-24 08:25:39', 'Cancelación Pedido #51', 228),
(149, 'ENTRADA', 1, '2026-07-24 08:26:02', 'Cancelación Pedido #48', 210),
(150, 'ENTRADA', 1, '2026-07-24 08:26:07', 'Cancelación Pedido #54', 205),
(151, 'ENTRADA', 1, '2026-07-24 08:26:14', 'Cancelación Pedido #30', 157),
(152, 'ENTRADA', 1, '2026-07-24 08:31:47', 'Cancelación Pedido #40', 258),
(153, 'ENTRADA', 1, '2026-07-24 08:31:47', 'Cancelación Pedido #40', 257),
(154, 'ENTRADA', 1, '2026-07-24 08:31:52', 'Cancelación Pedido #45', 223),
(155, 'ENTRADA', 2, '2026-07-24 08:31:52', 'Cancelación Pedido #45', 211),
(156, 'ENTRADA', 1, '2026-07-24 08:31:57', 'Cancelación Pedido #42', 244),
(157, 'ENTRADA', 1, '2026-07-24 08:32:02', 'Cancelación Pedido #41', 239),
(158, 'ENTRADA', 1, '2026-07-24 08:32:07', 'Cancelación Pedido #43', 222),
(159, 'ENTRADA', 2, '2026-07-24 08:32:07', 'Cancelación Pedido #43', 255),
(160, 'ENTRADA', 1, '2026-07-24 08:32:14', 'Cancelación Pedido #35', 205),
(161, 'ENTRADA', 1, '2026-07-24 08:32:19', 'Cancelación Pedido #29', 253),
(162, 'ENTRADA', 1, '2026-07-24 08:32:19', 'Cancelación Pedido #29', 196),
(163, 'ENTRADA', 1, '2026-07-24 08:32:25', 'Cancelación Pedido #47', 223),
(164, 'ENTRADA', 5, '2026-07-24 08:32:30', 'Cancelación Pedido #46', 206),
(165, 'ENTRADA', 1, '2026-07-24 08:32:35', 'Cancelación Pedido #36', 218),
(166, 'ENTRADA', 1, '2026-07-24 08:32:43', 'Cancelación Pedido #50', 206),
(167, 'ENTRADA', 1, '2026-07-24 08:32:59', 'Cancelación Pedido #32', 211),
(168, 'ENTRADA', 1, '2026-07-24 08:33:05', 'Cancelación Pedido #33', 148),
(169, 'ENTRADA', 1, '2026-07-24 08:33:10', 'Cancelación Pedido #34', 143),
(170, 'ENTRADA', 1, '2026-07-24 08:33:17', 'Cancelación Pedido #37', 206),
(171, 'SALIDA', 1, '2026-07-24 08:33:58', 'Pedido #94', 212),
(172, 'SALIDA', 1, '2026-07-24 08:33:58', 'Pedido #94', 223),
(173, 'SALIDA', 1, '2026-07-24 08:33:58', 'Pedido #94', 235),
(174, 'ENTRADA', 1, '2026-07-25 14:35:50', 'Cancelación Pedido #94', 212),
(175, 'ENTRADA', 1, '2026-07-25 14:35:50', 'Cancelación Pedido #94', 223),
(176, 'ENTRADA', 1, '2026-07-25 14:35:50', 'Cancelación Pedido #94', 235),
(177, 'SALIDA', 1, '2026-07-25 14:37:05', 'Pedido #95', 205),
(178, 'ENTRADA', 1, '2026-07-25 15:04:51', 'Cancelación Pedido #95', 205),
(179, 'SALIDA', 1, '2026-07-25 15:08:11', 'Pedido #96', 257),
(180, 'SALIDA', 1, '2026-07-25 15:08:11', 'Pedido #96', 258),
(181, 'ENTRADA', 1, '2026-07-25 15:40:43', 'Cancelación Pedido #96', 257),
(182, 'ENTRADA', 1, '2026-07-25 15:40:43', 'Cancelación Pedido #96', 258),
(183, 'ENTRADA', 1, '2026-07-25 15:41:37', 'Cancelación Pedido #60', 155),
(184, 'ENTRADA', 1, '2026-07-25 15:41:37', 'Cancelación Pedido #60', 206),
(185, 'ENTRADA', 2, '2026-07-25 15:41:37', 'Cancelación Pedido #60', 211),
(186, 'ENTRADA', 1, '2026-07-25 15:41:45', 'Cancelación Pedido #62', 155),
(187, 'ENTRADA', 1, '2026-07-25 15:41:45', 'Cancelación Pedido #62', 206),
(188, 'ENTRADA', 2, '2026-07-25 15:41:45', 'Cancelación Pedido #62', 211),
(189, 'ENTRADA', 1, '2026-07-25 15:41:51', 'Cancelación Pedido #59', 155),
(190, 'ENTRADA', 1, '2026-07-25 15:41:51', 'Cancelación Pedido #59', 206),
(191, 'ENTRADA', 2, '2026-07-25 15:41:51', 'Cancelación Pedido #59', 211),
(192, 'ENTRADA', 1, '2026-07-25 15:42:39', 'Cancelación Pedido #61', 155),
(193, 'ENTRADA', 1, '2026-07-25 15:42:39', 'Cancelación Pedido #61', 206),
(194, 'ENTRADA', 2, '2026-07-25 15:42:39', 'Cancelación Pedido #61', 211),
(195, 'SALIDA', 1, '2026-07-25 15:43:17', 'Pedido #97', 251),
(196, 'ENTRADA', 1, '2026-07-25 15:46:03', 'Cancelación Pedido #97', 251);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago`
--

CREATE TABLE `pago` (
  `id_pago` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `monto_recibido` decimal(10,2) DEFAULT NULL,
  `vuelto` decimal(10,2) DEFAULT NULL,
  `fecha_pago` datetime DEFAULT current_timestamp(),
  `estado_pago` varchar(50) DEFAULT NULL,
  `id_metodo_pago` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pago`
--

INSERT INTO `pago` (`id_pago`, `monto`, `monto_recibido`, `vuelto`, `fecha_pago`, `estado_pago`, `id_metodo_pago`) VALUES
(1, 11.80, NULL, NULL, '2026-07-04 19:22:31', 'Completado', 1),
(2, 34.12, NULL, NULL, '2026-07-05 08:15:53', 'Completado', 3),
(3, 31.90, NULL, NULL, '2026-07-06 16:25:49', 'Completado', 1),
(4, 17.60, NULL, NULL, '2026-07-07 06:11:52', 'Completado', 1),
(5, 17.39, NULL, NULL, '2026-07-07 15:56:10', 'Completado', 2),
(6, 16.09, NULL, NULL, '2026-07-07 16:17:25', 'Completado', 3),
(7, 27.30, NULL, NULL, '2026-07-07 22:35:15', 'Completado', 3),
(8, 24.39, NULL, NULL, '2026-07-07 22:51:08', 'Completado', 3),
(9, 11.29, NULL, NULL, '2026-07-07 22:51:37', 'Completado', 3),
(10, 52.67, NULL, NULL, '2026-07-08 01:19:25', 'Completado', 3),
(11, 6.77, NULL, NULL, '2026-07-08 01:41:13', 'Completado', 1),
(12, 40.28, NULL, NULL, '2026-07-08 01:46:04', 'Completado', 1),
(13, 16.68, NULL, NULL, '2026-07-08 14:40:14', 'Completado', 2),
(14, 16.61, 50.00, 33.39, '2026-07-08 14:46:36', 'Completado', 1),
(15, 7.21, NULL, NULL, '2026-07-08 14:50:22', 'Completado', 3),
(16, 22.11, 25.00, 2.89, '2026-07-16 23:11:04', 'Completado', 1),
(17, 18.39, NULL, NULL, '2026-07-22 16:13:07', 'Completado', 3),
(18, 23.87, NULL, NULL, '2026-07-22 17:53:44', 'Completado', 2),
(19, 16.61, 20.00, 3.39, '2026-07-22 17:55:56', 'Completado', 1),
(20, 48.85, NULL, NULL, '2026-07-22 18:25:49', 'Completado', 3),
(21, 147.77, NULL, NULL, '2026-07-22 19:07:54', 'Completado', 3),
(22, 24.38, NULL, NULL, '2026-07-22 19:18:24', 'Completado', 3),
(23, 24.38, NULL, NULL, '2026-07-22 19:18:30', 'Completado', 3),
(24, 24.38, 56.00, 31.62, '2026-07-22 19:18:38', 'Completado', 1),
(25, 24.38, NULL, NULL, '2026-07-22 19:19:03', 'Completado', 3),
(26, 24.38, NULL, NULL, '2026-07-22 19:24:29', 'Completado', 3),
(27, 31.05, NULL, NULL, '2026-07-22 19:30:37', 'Completado', 3),
(28, 34.12, 50.00, 15.88, '2026-07-22 19:37:27', 'Completado', 1),
(29, 30.50, 50.00, 19.50, '2026-07-22 19:50:31', 'Completado', 1),
(30, 76.70, 100.00, 23.30, '2026-07-22 19:54:44', 'Completado', 1),
(31, 6.29, 7.00, 0.71, '2026-07-22 19:55:13', 'Completado', 1),
(32, 29.12, 50.00, 20.88, '2026-07-22 19:58:36', 'Completado', 1),
(33, 7.08, 10.00, 2.92, '2026-07-22 20:01:37', 'Completado', 1),
(34, 4.01, 5.00, 0.99, '2026-07-22 20:02:01', 'Completado', 1),
(35, 19.39, 20.00, 0.61, '2026-07-22 20:10:32', 'Completado', 1),
(36, 1.77, 5.00, 3.23, '2026-07-22 20:14:16', 'Completado', 1),
(37, 16.38, 50.00, 33.62, '2026-07-22 20:15:47', 'Completado', 1),
(38, 23.87, 50.00, 26.13, '2026-07-22 20:17:01', 'Completado', 1),
(39, 19.43, NULL, NULL, '2026-07-22 20:18:39', 'Completado', 2),
(40, 41.30, NULL, NULL, '2026-07-22 20:32:16', 'Completado', 2),
(41, 14.04, NULL, NULL, '2026-07-22 20:34:35', 'Completado', 2),
(42, 14.43, NULL, NULL, '2026-07-22 20:49:50', 'Completado', 2),
(43, 69.45, NULL, NULL, '2026-07-22 20:53:04', 'Completado', 2),
(44, 14.75, NULL, NULL, '2026-07-22 20:54:50', 'Completado', 2),
(45, 77.10, NULL, NULL, '2026-07-22 20:57:25', 'Completado', 2),
(46, 86.86, NULL, NULL, '2026-07-22 21:00:27', 'Completado', 2),
(47, 18.87, NULL, NULL, '2026-07-22 21:03:50', 'Completado', 2),
(48, 64.78, NULL, NULL, '2026-07-22 21:04:20', 'Completado', 2),
(49, 26.55, NULL, NULL, '2026-07-22 21:08:13', 'Completado', 2),
(50, 16.38, NULL, NULL, '2026-07-22 21:08:39', 'Completado', 2),
(51, 23.13, NULL, NULL, '2026-07-22 21:10:59', 'Completado', 2),
(52, 11.29, NULL, NULL, '2026-07-22 21:13:01', 'Completado', 2),
(53, 18.39, NULL, NULL, '2026-07-22 21:14:54', 'Completado', 2),
(54, 25.84, NULL, NULL, '2026-07-22 21:18:06', 'Completado', 2),
(55, 81.60, NULL, NULL, '2026-07-22 21:21:20', 'Completado', 2),
(56, 10.03, NULL, NULL, '2026-07-22 21:22:40', 'Completado', 2),
(57, 21.83, NULL, NULL, '2026-07-22 21:58:26', 'Completado', 2),
(58, 49.55, NULL, NULL, '2026-07-22 22:01:53', 'Completado', 3),
(59, 114.81, NULL, NULL, '2026-07-23 10:32:01', 'Completado', 2),
(60, 114.81, NULL, NULL, '2026-07-23 10:32:09', 'Completado', 2),
(61, 114.81, NULL, NULL, '2026-07-23 10:33:02', 'Completado', 3),
(62, 114.81, NULL, NULL, '2026-07-23 10:34:10', 'Completado', 3),
(63, 25.84, NULL, NULL, '2026-07-23 10:35:06', 'Completado', 2),
(64, 25.84, NULL, NULL, '2026-07-23 10:35:17', 'Completado', 2),
(65, 25.84, 50.00, 24.16, '2026-07-23 10:35:37', 'Completado', 1),
(66, 25.84, NULL, NULL, '2026-07-23 10:38:21', 'Completado', 2),
(67, 25.84, NULL, NULL, '2026-07-23 10:38:34', 'Completado', 2),
(68, 38.82, NULL, NULL, '2026-07-23 10:41:30', 'Completado', 2),
(69, 38.82, NULL, NULL, '2026-07-23 10:41:43', 'Completado', 2),
(70, 30.84, NULL, NULL, '2026-07-23 10:42:18', 'Completado', 2),
(71, 26.83, NULL, NULL, '2026-07-23 10:54:00', 'Completado', 2),
(72, 30.84, NULL, NULL, '2026-07-23 10:57:08', 'Completado', 2),
(73, 16.80, NULL, NULL, '2026-07-23 10:59:04', 'Completado', 1),
(74, 21.83, NULL, NULL, '2026-07-23 11:00:07', 'Completado', 2),
(75, 21.83, NULL, NULL, '2026-07-23 11:00:20', 'Completado', 3),
(76, 16.80, NULL, NULL, '2026-07-23 11:03:26', 'Completado', 1),
(77, 16.80, NULL, NULL, '2026-07-23 11:03:33', 'Completado', 1),
(78, 3.54, NULL, NULL, '2026-07-23 11:04:08', 'Completado', 2),
(79, 16.68, NULL, NULL, '2026-07-23 11:16:08', 'Completado', 2),
(80, 32.02, NULL, NULL, '2026-07-23 11:17:04', 'Completado', 2),
(81, 12.67, NULL, NULL, '2026-07-23 11:40:07', 'Completado', 2),
(82, 43.82, NULL, NULL, '2026-07-23 11:41:25', 'Completado', 2),
(83, 10.90, NULL, NULL, '2026-07-23 11:44:45', 'Completado', 2),
(84, 43.82, NULL, NULL, '2026-07-23 11:47:35', 'Completado', 2),
(85, 48.78, NULL, NULL, '2026-07-23 12:00:04', 'Completado', 2),
(86, 35.09, NULL, NULL, '2026-07-23 12:05:43', 'Completado', 2),
(87, 19.16, NULL, NULL, '2026-07-23 12:13:27', 'Completado', 2),
(88, 10.02, 50.00, 39.98, '2026-07-23 12:14:38', 'Completado', 1),
(89, 30.84, NULL, NULL, '2026-07-23 12:43:18', 'Completado', 2),
(90, 7.22, 25.00, 17.78, '2026-07-23 16:08:20', 'Completado', 1),
(91, 31.55, 50.00, 18.45, '2026-07-23 16:14:02', 'Completado', 1),
(92, 67.80, 100.00, 32.20, '2026-07-24 00:24:30', 'Completado', 1),
(93, 52.90, 60.00, 7.10, '2026-07-24 08:33:58', 'Completado', 1),
(94, 21.90, NULL, NULL, '2026-07-25 14:37:05', 'Completado', 2),
(95, 40.00, 50.00, 10.00, '2026-07-25 15:08:11', 'Completado', 1),
(96, 14.50, NULL, NULL, '2026-07-25 15:43:17', 'Completado', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `id_pedido` int(11) NOT NULL,
  `fecha_pedido` datetime DEFAULT current_timestamp(),
  `subtotal` decimal(10,2) DEFAULT NULL,
  `descuento` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `estado_pedido` varchar(50) DEFAULT NULL,
  `tipo_entrega` varchar(50) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_pago` int(11) DEFAULT NULL,
  `dni_cliente` varchar(20) DEFAULT NULL,
  `nombre_cliente` varchar(100) DEFAULT NULL,
  `telefono_cliente` varchar(20) DEFAULT NULL,
  `correo_cliente` varchar(100) DEFAULT NULL,
  `motivo_cancelacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedido`
--

INSERT INTO `pedido` (`id_pedido`, `fecha_pedido`, `subtotal`, `descuento`, `total`, `estado_pedido`, `tipo_entrega`, `id_usuario`, `id_pago`, `dni_cliente`, `nombre_cliente`, `telefono_cliente`, `correo_cliente`, `motivo_cancelacion`) VALUES
(1, '2026-07-04 19:22:31', 10.00, 2.50, 11.80, 'Entregado', 'Recojo en tienda', 17, 1, NULL, NULL, NULL, NULL, NULL),
(2, '2026-07-05 08:15:53', 24.68, 8.23, 34.12, 'Entregado', 'Delivery', 17, 2, NULL, NULL, NULL, NULL, NULL),
(3, '2026-07-06 16:25:49', 22.80, 0.00, 31.90, 'Entregado', 'Delivery', 5, 3, NULL, NULL, NULL, NULL, NULL),
(4, '2026-07-07 06:11:52', 10.68, 0.82, 17.60, 'Entregado', 'Delivery', 5, 4, NULL, NULL, NULL, NULL, NULL),
(5, '2026-07-07 15:56:10', 10.50, 0.00, 17.39, 'Entregado', 'Delivery', 15, 5, NULL, NULL, NULL, NULL, NULL),
(6, '2026-07-07 16:17:25', 9.40, 0.60, 16.09, 'Entregado', 'Delivery', 15, 6, NULL, NULL, NULL, NULL, NULL),
(7, '2026-07-07 22:35:15', 18.90, 0.00, 27.30, 'Entregado', 'Delivery', 15, 7, NULL, NULL, NULL, NULL, NULL),
(8, '2026-07-07 22:51:08', 16.43, 5.47, 24.39, 'Entregado', 'Delivery', 15, 8, NULL, NULL, NULL, NULL, NULL),
(9, '2026-07-07 22:51:37', 5.33, 1.17, 11.29, 'Entregado', 'Delivery', 15, 9, NULL, NULL, NULL, NULL, NULL),
(10, '2026-07-08 01:19:25', 40.40, 0.00, 52.67, 'Entregado', 'Delivery', 5, 10, NULL, NULL, NULL, NULL, NULL),
(11, '2026-07-08 01:41:13', 1.50, 0.50, 6.77, 'Entregado', 'Delivery', 15, 11, NULL, NULL, NULL, NULL, NULL),
(12, '2026-07-08 01:46:04', 29.90, 0.00, 40.28, 'Entregado', 'Delivery', 15, 12, NULL, NULL, NULL, NULL, NULL),
(13, '2026-07-08 14:40:14', 9.90, 0.00, 16.68, 'Entregado', 'Delivery', 15, 13, NULL, NULL, NULL, NULL, NULL),
(14, '2026-07-08 14:46:36', 9.84, 2.16, 16.61, 'Entregado', 'Delivery', 15, 14, NULL, NULL, NULL, NULL, NULL),
(15, '2026-07-08 14:50:22', 1.88, 0.63, 7.21, 'Entregado', 'Delivery', 5, 15, NULL, NULL, NULL, NULL, NULL),
(16, '2026-07-16 23:11:04', 14.50, 0.00, 22.11, 'Entregado', 'Delivery', 15, 16, NULL, NULL, NULL, NULL, NULL),
(17, '2026-07-22 16:13:07', 11.35, 1.55, 18.39, 'Entregado', 'Delivery', 20, 17, NULL, NULL, NULL, NULL, NULL),
(18, '2026-07-22 17:53:44', 15.99, 3.51, 23.87, 'Entregado', 'Delivery', 20, 18, NULL, NULL, NULL, NULL, NULL),
(19, '2026-07-22 17:55:56', 9.84, 2.16, 16.61, 'En preparación', 'Delivery', 20, 19, NULL, NULL, NULL, NULL, NULL),
(20, '2026-07-22 18:25:49', 41.40, 0.00, 48.85, 'En preparación', 'Recojo en tienda', NULL, 20, NULL, NULL, NULL, NULL, NULL),
(21, '2026-07-22 19:07:54', 120.99, 4.41, 147.77, 'En preparación', 'Delivery', NULL, 21, NULL, NULL, NULL, NULL, NULL),
(22, '2026-07-22 19:18:24', 16.43, 5.48, 24.38, 'Pendiente', 'Delivery', NULL, 22, NULL, '', '', NULL, NULL),
(23, '2026-07-22 19:18:30', 16.43, 5.48, 24.38, 'Pendiente', 'Delivery', NULL, 23, NULL, '', '', NULL, NULL),
(24, '2026-07-22 19:18:38', 16.43, 5.48, 24.38, 'Pendiente', 'Delivery', NULL, 24, NULL, '', '', NULL, NULL),
(25, '2026-07-22 19:19:03', 16.43, 5.48, 24.38, 'Pendiente', 'Delivery', NULL, 25, NULL, '', '', NULL, NULL),
(26, '2026-07-22 19:24:29', 16.43, 5.48, 24.38, 'Pendiente', 'Delivery', NULL, 26, '', '', '', NULL, NULL),
(27, '2026-07-22 19:30:37', 26.31, 3.59, 31.05, 'Cancelado', 'Recojo en tienda', 5, 27, NULL, NULL, NULL, NULL, NULL),
(28, '2026-07-22 19:37:27', 24.68, 8.23, 34.12, 'Entregado', 'Delivery', NULL, 28, '74535526', 'PAOLA DEL CARMEN LUCIA SERNAQUE ABAD', '955728700', NULL, NULL),
(29, '2026-07-22 19:50:31', 25.85, 1.55, 30.50, 'Cancelado', 'Recojo en tienda', 15, 29, NULL, NULL, NULL, NULL, NULL),
(30, '2026-07-22 19:54:44', 65.00, 0.00, 76.70, 'Cancelado', 'Recojo en tienda', 15, 30, NULL, NULL, NULL, NULL, NULL),
(31, '2026-07-22 19:55:13', 5.33, 1.17, 6.29, 'Cancelado', 'Recojo en tienda', 15, 31, NULL, NULL, NULL, NULL, NULL),
(32, '2026-07-22 19:58:36', 24.68, 8.23, 29.12, 'Cancelado', 'Recojo en tienda', 15, 32, NULL, NULL, NULL, NULL, NULL),
(33, '2026-07-22 20:01:37', 6.00, 0.00, 7.08, 'Cancelado', 'Recojo en tienda', 15, 33, NULL, NULL, NULL, NULL, NULL),
(34, '2026-07-22 20:02:01', 3.40, 0.60, 4.01, 'Cancelado', 'Recojo en tienda', 15, 34, NULL, NULL, NULL, NULL, NULL),
(35, '2026-07-22 20:10:32', 16.43, 5.47, 19.39, 'Cancelado', 'Recojo en tienda', 15, 35, NULL, NULL, NULL, NULL, NULL),
(36, '2026-07-22 20:14:16', 1.50, 0.50, 1.77, 'Cancelado', 'Recojo en tienda', 15, 36, NULL, NULL, NULL, NULL, NULL),
(37, '2026-07-22 20:15:47', 13.88, 4.62, 16.38, 'Cancelado', 'Recojo en tienda', 15, 37, NULL, NULL, NULL, NULL, NULL),
(38, '2026-07-22 20:17:01', 15.99, 3.51, 23.87, 'Pendiente', 'Delivery', NULL, 38, '74764664', 'LUCY LINA FONSECA ESPINOZA', '955728700', NULL, NULL),
(39, '2026-07-22 20:18:39', 12.23, 1.67, 19.43, 'Pendiente', 'Delivery', NULL, 39, '74535345', 'JHANEYRA ALIAGA CANAYO', '955728700', NULL, NULL),
(40, '2026-07-22 20:32:16', 35.00, 0.00, 41.30, 'Cancelado', 'Recojo en tienda', 15, 40, NULL, NULL, NULL, NULL, NULL),
(41, '2026-07-22 20:34:35', 11.90, 0.00, 14.04, 'Cancelado', 'Recojo en tienda', 15, 41, NULL, NULL, NULL, NULL, NULL),
(42, '2026-07-22 20:49:50', 12.23, 1.67, 14.43, 'Cancelado', 'Recojo en tienda', 15, 42, NULL, NULL, NULL, NULL, NULL),
(43, '2026-07-22 20:53:04', 54.62, 7.18, 69.45, 'Cancelado', 'Delivery', 15, 43, NULL, NULL, NULL, NULL, NULL),
(44, '2026-07-22 20:54:50', 12.50, 0.00, 14.75, 'Pendiente', 'Recojo en tienda', NULL, 44, '74464747', 'DAVID JHONATAN TAFUR CABRAL', '958585585', NULL, NULL),
(45, '2026-07-22 20:57:25', 65.34, 19.96, 77.10, 'Cancelado', 'Recojo en tienda', 15, 45, NULL, NULL, NULL, NULL, NULL),
(46, '2026-07-22 21:00:27', 69.38, 23.13, 86.86, 'Cancelado', 'Delivery', 15, 46, NULL, NULL, NULL, NULL, NULL),
(47, '2026-07-22 21:03:50', 15.99, 3.51, 18.87, 'Cancelado', 'Recojo en tienda', 15, 47, NULL, NULL, NULL, NULL, NULL),
(48, '2026-07-22 21:04:20', 54.90, 0.00, 64.78, 'Cancelado', 'Recojo en tienda', 15, 48, NULL, NULL, NULL, NULL, NULL),
(49, '2026-07-22 21:08:13', 22.50, 0.00, 26.55, 'Cancelado', 'Recojo en tienda', 15, 49, NULL, NULL, NULL, NULL, NULL),
(50, '2026-07-22 21:08:39', 13.88, 4.62, 16.38, 'Cancelado', 'Recojo en tienda', 15, 50, NULL, NULL, NULL, NULL, NULL),
(51, '2026-07-22 21:10:59', 19.60, 4.30, 23.13, 'Cancelado', 'Recojo en tienda', 15, 51, NULL, NULL, NULL, NULL, NULL),
(52, '2026-07-22 21:13:01', 5.33, 1.17, 11.29, 'Cancelado', 'Delivery', 15, 52, NULL, NULL, NULL, NULL, NULL),
(53, '2026-07-22 21:14:54', 11.35, 1.55, 18.39, 'Cancelado', 'Delivery', 15, 53, NULL, NULL, NULL, NULL, NULL),
(54, '2026-07-22 21:18:06', 21.90, 0.00, 25.84, 'Cancelado', 'Recojo en tienda', 15, 54, NULL, NULL, NULL, NULL, NULL),
(55, '2026-07-22 21:21:20', 69.15, 5.15, 81.60, 'Cancelado', 'Recojo en tienda', 15, 55, NULL, NULL, NULL, NULL, NULL),
(56, '2026-07-22 21:22:40', 8.50, 0.00, 10.03, 'Cancelado', 'Recojo en tienda', 15, 56, NULL, NULL, NULL, NULL, 'porque quiero'),
(57, '2026-07-22 21:58:26', 18.50, 0.00, 21.83, 'Pendiente', 'Recojo en tienda', NULL, 57, '46205246', 'CARLOS OMAR OLANO PAZ', '984747474', NULL, NULL),
(58, '2026-07-22 22:01:53', 37.75, 5.15, 49.55, 'Pendiente', 'Delivery', NULL, 58, '74535526', 'PAOLA DEL CARMEN LUCIA SERNAQUE ABAD', '955728700', NULL, NULL),
(59, '2026-07-23 10:32:01', 97.30, 0.00, 114.81, 'Cancelado', 'Recojo en tienda', 15, 59, NULL, NULL, NULL, NULL, NULL),
(60, '2026-07-23 10:32:09', 97.30, 0.00, 114.81, 'Cancelado', 'Recojo en tienda', 15, 60, NULL, NULL, NULL, NULL, NULL),
(61, '2026-07-23 10:33:02', 97.30, 0.00, 114.81, 'Cancelado', 'Recojo en tienda', 15, 61, NULL, NULL, NULL, NULL, NULL),
(62, '2026-07-23 10:34:10', 97.30, 0.00, 114.81, 'Cancelado', 'Recojo en tienda', 15, 62, NULL, NULL, NULL, NULL, NULL),
(63, '2026-07-23 10:35:06', 21.90, 0.00, 25.84, 'Cancelado', 'Recojo en tienda', 15, 63, NULL, NULL, NULL, NULL, NULL),
(64, '2026-07-23 10:35:17', 21.90, 0.00, 25.84, 'Cancelado', 'Recojo en tienda', 15, 64, NULL, NULL, NULL, NULL, NULL),
(65, '2026-07-23 10:35:37', 21.90, 0.00, 25.84, 'Cancelado', 'Recojo en tienda', 15, 65, NULL, NULL, NULL, NULL, NULL),
(66, '2026-07-23 10:38:21', 21.90, 0.00, 25.84, 'Cancelado', 'Recojo en tienda', 15, 66, NULL, NULL, NULL, NULL, NULL),
(67, '2026-07-23 10:38:34', 21.90, 0.00, 25.84, 'Cancelado', 'Recojo en tienda', 15, 67, NULL, NULL, NULL, NULL, NULL),
(68, '2026-07-23 10:41:30', 32.90, 0.00, 38.82, 'Cancelado', 'Recojo en tienda', 15, 68, NULL, NULL, NULL, NULL, NULL),
(69, '2026-07-23 10:41:43', 32.90, 0.00, 38.82, 'Cancelado', 'Recojo en tienda', 15, 69, NULL, NULL, NULL, NULL, NULL),
(70, '2026-07-23 10:42:18', 21.90, 0.00, 30.84, 'Cancelado', 'Delivery', 15, 70, NULL, NULL, NULL, NULL, NULL),
(71, '2026-07-23 10:54:00', 18.50, 0.00, 26.83, 'Cancelado', 'Delivery', 15, 71, NULL, NULL, NULL, NULL, NULL),
(72, '2026-07-23 10:57:08', 21.90, 0.00, 30.84, 'Cancelado', 'Delivery', 15, 72, NULL, NULL, NULL, NULL, NULL),
(73, '2026-07-23 10:59:04', 10.00, 0.00, 16.80, 'Pendiente', 'Delivery', NULL, 73, '12345678', 'Juan Perez', '999999999', NULL, NULL),
(74, '2026-07-23 11:00:07', 18.50, 0.00, 21.83, 'Cancelado', 'Recojo en tienda', 15, 74, NULL, NULL, NULL, NULL, NULL),
(75, '2026-07-23 11:00:20', 18.50, 0.00, 21.83, 'Cancelado', 'Recojo en tienda', 15, 75, NULL, NULL, NULL, NULL, NULL),
(76, '2026-07-23 11:03:26', 10.00, 0.00, 16.80, 'Pendiente', 'Delivery', NULL, 76, '12345678', 'Juan Perez', '999999999', NULL, NULL),
(77, '2026-07-23 11:03:33', 10.00, 0.00, 16.80, 'Pendiente', 'Delivery', NULL, 77, '12345678', 'Juan Perez', '999999999', NULL, NULL),
(78, '2026-07-23 11:04:08', 3.00, 0.00, 3.54, 'Cancelado', 'Recojo en tienda', 15, 78, NULL, NULL, NULL, NULL, NULL),
(79, '2026-07-23 11:16:08', 9.90, 0.00, 16.68, 'Cancelado', 'Delivery', 15, 79, NULL, NULL, NULL, NULL, NULL),
(80, '2026-07-23 11:17:04', 22.90, 0.00, 32.02, 'Cancelado', 'Delivery', 15, 80, NULL, NULL, NULL, NULL, NULL),
(81, '2026-07-23 11:40:07', 6.50, 0.00, 12.67, 'Cancelado', 'Delivery', 15, 81, NULL, NULL, NULL, NULL, NULL),
(82, '2026-07-23 11:41:25', 32.90, 0.00, 43.82, 'Cancelado', 'Delivery', 15, 82, NULL, NULL, NULL, NULL, NULL),
(83, '2026-07-23 11:44:45', 5.00, 0.00, 10.90, 'Cancelado', 'Delivery', 15, 83, NULL, NULL, NULL, NULL, NULL),
(84, '2026-07-23 11:47:35', 32.90, 0.00, 43.82, 'Cancelado', 'Delivery', 15, 84, NULL, NULL, NULL, NULL, NULL),
(85, '2026-07-23 12:00:04', 37.10, 0.00, 48.78, 'Cancelado', 'Delivery', 15, 85, NULL, NULL, NULL, NULL, '....'),
(86, '2026-07-23 12:05:43', 25.50, 0.00, 35.09, 'Cancelado', 'Delivery', 15, 86, NULL, NULL, NULL, NULL, 'no quiero'),
(87, '2026-07-23 12:13:27', 12.00, 0.00, 19.16, 'Cancelado', 'Delivery', 15, 87, NULL, NULL, NULL, NULL, NULL),
(88, '2026-07-23 12:14:38', 4.25, 0.75, 10.02, 'Pendiente', 'Delivery', NULL, 88, '74535526', 'PAOLA DEL CARMEN LUCIA SERNAQUE ABAD', '955728700', '', NULL),
(89, '2026-07-23 12:43:18', 21.90, 0.00, 30.84, 'Cancelado', 'Delivery', 15, 89, NULL, NULL, NULL, NULL, NULL),
(90, '2026-07-23 16:08:20', 1.88, 0.62, 7.22, 'Pendiente', 'Delivery', NULL, 90, '74646464', 'GLORIA LILI QUISPE FARFAN', '967777777', '', NULL),
(91, '2026-07-23 16:14:02', 22.50, 0.00, 31.55, 'Pendiente', 'Delivery', NULL, 91, '11111111', 'yo no se', '999999999', '', NULL),
(92, '2026-07-23 16:31:05', NULL, NULL, 10.00, 'Cancelado', 'tienda', 1, NULL, NULL, NULL, NULL, NULL, 'prueba'),
(93, '2026-07-24 00:24:30', 62.80, 0.00, 67.80, 'Pendiente', 'Delivery', 5, 92, NULL, NULL, NULL, NULL, NULL),
(94, '2026-07-24 08:33:58', 52.90, 0.00, 52.90, 'Cancelado', 'Recojo en tienda', 15, 93, NULL, NULL, NULL, NULL, NULL),
(95, '2026-07-25 14:37:05', 21.90, 0.00, 21.90, 'Cancelado', 'Recojo en tienda', 15, 94, NULL, NULL, NULL, NULL, NULL),
(96, '2026-07-25 15:08:11', 35.00, 0.00, 40.00, 'Cancelado', 'Delivery', 15, 95, NULL, NULL, NULL, NULL, NULL),
(97, '2026-07-25 15:43:17', 9.50, 0.00, 14.50, 'Cancelado', 'Delivery', 15, 96, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock_actual` int(11) NOT NULL,
  `stock_minimo` int(11) DEFAULT 5,
  `codigo_barra` varchar(100) DEFAULT NULL,
  `imagen` varchar(500) DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `id_categoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id_producto`, `nombre`, `descripcion`, `precio`, `stock_actual`, `stock_minimo`, `codigo_barra`, `imagen`, `fecha_vencimiento`, `estado`, `id_categoria`) VALUES
(130, 'Cebolla blanca 1 kg', 'Cebolla blanca fresca de la mejor calidad. Perfecta para tus ensaladas y guisos.', 3.50, 97, 10, NULL, 'https://media.falabella.com/tottusPE/43492639_1/w=800,h=800,fit=pad', NULL, 1, 1),
(131, 'Tomate fresco 1 kg', 'Tomates rojos maduros, jugosos y llenos de sabor. Ideales para salsas y ensaladas.', 5.20, 100, 10, NULL, 'https://images.unsplash.com/photo-1546094096-0df4bcaaa337?w=400&h=300&fit=crop', NULL, 1, 1),
(132, 'Zanahoria 1 kg', 'Zanahorias crujientes y dulces. Excelente fuente de vitamina A y betacarotenos.', 3.00, 100, 10, NULL, 'https://images.unsplash.com/photo-1598170845058-32b9d6a5da37?w=400&h=300&fit=crop', NULL, 1, 1),
(133, 'Lechuga unidad', 'Lechuga fresca y crujiente. Perfecta para ensaladas saludables y nutritivas.', 2.50, 100, 10, NULL, 'https://images.unsplash.com/photo-1622206151226-18ca2c9ab4a1?w=400&h=300&fit=crop', NULL, 1, 1),
(134, 'Pimiento rojo 500 g', 'Pimientos rojos dulces y crujientes. Perfectos para saltados y ensaladas.', 4.80, 100, 10, NULL, 'https://images.unsplash.com/photo-1563565375-f3fdfdbefa83?w=400&h=300&fit=crop', NULL, 1, 1),
(135, 'Ajo fresco 250 g', 'Ajo fresco de excelente calidad. Indispensable en toda cocina peruana.', 6.50, 100, 10, NULL, 'https://imgs.search.brave.com/7Xx1jiUZOAribEBHr5qHFGxpTpzZs56NtXD_uHLhtdo/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9pbWcu/ZnJlZXBpay5jb20v/Zm90b3MtcHJlbWl1/bS9ham8tZW50ZXJv/LWNydWRvLWNhc2Nh/cmEtc29icmUtZm9u/ZG8tYmxhbmNvLXRy/YXphZG8tcmVjb3J0/ZV8xMDU0MjgtMTky/Ny5qcGc_c2VtdD1h/aXNfaHlicmlkJnc9/NzQwJnE9ODA', NULL, 1, 1),
(136, 'Brócoli 500 g', 'Brócoli fresco rico en nutrientes. Perfecto al vapor o salteado.', 5.50, 98, 10, NULL, 'https://images.unsplash.com/photo-1628773822503-930a7eaecf80?w=400&h=300&fit=crop', NULL, 1, 1),
(137, 'Coliflor unidad', 'Coliflor blanca y fresca. Versátil para múltiples preparaciones culinarias.', 4.20, 100, 10, NULL, 'https://images.unsplash.com/photo-1566842600175-97dca489844f?q=80&w=1964&auto=format&fit=crop&ixlib=rb-4.1.0', NULL, 1, 1),
(138, 'Espinaca 500 g', 'Espinaca fresca rica en hierro. Ideal para ensaladas y batidos verdes.', 3.80, 100, 10, NULL, 'https://images.unsplash.com/photo-1576045057995-568f588f82fb?w=400&h=300&fit=crop', NULL, 1, 1),
(139, 'Papa blanca 2 kg', 'Papa blanca peruana de primera calidad. Perfecta para cualquier preparación.', 4.50, 100, 10, NULL, 'https://images.unsplash.com/photo-1518977676601-b53f82aba655?w=400&h=300&fit=crop', NULL, 1, 1),
(140, 'Pepino 1 kg', 'Pepinos frescos y crujientes. Perfectos para ensaladas refrescantes.', 2.80, 100, 10, NULL, 'https://images.unsplash.com/photo-1604977042946-1eecc30f269e?w=400&h=300&fit=crop', NULL, 1, 1),
(141, 'Calabaza 1 unidad', 'Calabaza fresca y dulce. Excelente para sopas y guisos nutritivos.', 6.00, 99, 10, NULL, 'https://images.unsplash.com/photo-1570586437263-ab629fccc818?w=400&h=300&fit=crop', NULL, 1, 1),
(142, 'Manzana roja 1 kg', 'Manzanas rojas, crujientes y jugosas. Perfectas para comer frescas o para ensaladas.', 6.50, 100, 10, NULL, 'https://images.unsplash.com/photo-1567306226416-28f0efdc88ce?w=800&q=80&auto=format&fit=crop', NULL, 1, 2),
(143, 'Banana 1 kg', 'Bananas maduras, dulces y listas para consumir. Fuente natural de potasio.', 4.00, 99, 10, NULL, 'https://www.recetasnestle.com.pe/sites/default/files/2022-07/propiedades-del-platano.jpg', NULL, 1, 2),
(144, 'Naranja 1 kg', 'Naranjas jugosas, ideales para zumos y ensaladas. Buen aporte de vitamina C.', 5.00, 99, 10, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRQhsRlDCPm5BKzCYLk5NoSSoMlhECgGBqRqg&s', NULL, 1, 2),
(145, 'Uva sin semilla 500 g', 'Uvas dulces sin semilla, perfectas para mesa y repostería.', 8.00, 100, 10, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQU1yu0xp6hCAGbeveVq5OJs0u8lJfid32pig&s', NULL, 1, 2),
(146, 'Pera 1 kg', 'Peras suaves y jugosas, buen complemento en postres y ensaladas.', 7.50, 100, 10, NULL, 'https://plazavea.vteximg.com.br/arquivos/ids/175198-450-450/20083819.jpg?v=635808869866470000', NULL, 1, 2),
(147, 'Mango (unidad, 400-600g aprox.)', 'Mangos maduros, dulces y perfumados. Perfectos para batidos y postres.', 3.50, 100, 10, NULL, 'https://media.istockphoto.com/id/1318935291/es/foto/fruta-de-mango.jpg?s=612x612&w=0&k=20&c=pSsuDloovUY2M4VK81CuQwGzk_jPniuicixW5RL8nOg=', NULL, 1, 2),
(148, 'Fresas 250 g', 'Fresas frescas y aromáticas, ideales para postres y mermeladas.', 6.00, 99, 10, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ7vNwB0YloW293Jb5mhPSNWDQZkJ3Aj-fmxw&s', NULL, 1, 2),
(149, 'Kiwi 1 kg', 'Kiwis verdes, ricos en vitamina C y fibra. Buen complemento para batidos.', 9.00, 100, 10, NULL, 'https://static.vecteezy.com/system/resources/previews/002/286/604/large_2x/kiwi-fruit-isolated-on-white-background-free-photo.jpg', NULL, 1, 2),
(150, 'Piña (unidad mediana)', 'Piña dulce y jugosa, perfecta para jugos y postres tropicales.', 7.00, 100, 10, NULL, 'https://previews.123rf.com/images/bookybuggy/bookybuggy1701/bookybuggy170100150/70753992-isolated-of-pineapple-fruit-sliced-on-white-background.jpg', NULL, 1, 2),
(151, 'Pollo entero (fresco) 1.8–2.2 kg', 'Pollo entero fresco, apto para asados, al horno o guisos. Carne jugosa y sabor natural.', 12.00, 50, 5, NULL, 'https://metroio.vtexassets.com/arquivos/ids/290311/Pollo-Entero-Fresco-Metro-x-kg-2-183284.jpg?v=638179316343400000', NULL, 1, 3),
(152, 'Pechuga de pollo (sin piel, sin hueso) 1 kg', 'Pechuga de pollo fresca, deshuesada y sin piel. Alta en proteína y de fácil preparación.', 9.50, 50, 5, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQSd5Ut8vXRNibx0WAGdhmc67dzn59huHwZUw&s', NULL, 1, 3),
(153, 'Bistec de res selecto (fresco) 1 kg', 'Bistec de res selecto, corte magro con buen marmoleo. Ideal para sartén, plancha o parrilla.', 19.00, 40, 5, NULL, 'https://bitworks-multimedia.superselectos.com/api/selectos/multimedia/0284027a-009e-447f-8c92-d5601e7f2f6d/content', NULL, 1, 3),
(154, 'Carne molida 1 kg (≈20% grasa) - fresca', 'Carne molida fresca con aproximadamente 20% de grasa. Ideal para hamburguesas, albóndigas y salsas.', 12.50, 40, 5, NULL, 'https://metroio.vtexassets.com/arquivos/ids/239354-800-auto?v=638173822458030000&width=800&height=auto&aspect=true', NULL, 1, 3),
(155, 'Costillas de cerdo 1 kg (frescas)', 'Costillas de cerdo frescas, jugosas y con buena grasa para asados, al horno o ahumados.', 13.00, 35, 5, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQjLVXKU6zLGjNZZgQTnWpRoYWsvWnMqAD-Cg&s', NULL, 1, 3),
(156, 'Filete de cerdo 1 kg (magro)', 'Filete de cerdo magro y tierno, ideal para plancha, salteados o al horno.', 14.50, 30, 5, NULL, 'https://media.istockphoto.com/id/178148993/es/foto/filete-de-cerdo.jpg?s=612x612&w=0&k=20&c=9whSxme4eR-OHmNu7cVBYyuYXMHnxKn2lPwINuh-Eak=', NULL, 1, 3),
(157, 'Pavo entero (fresco) 6–8 kg', 'Pavo entero fresco, ideal para horno, rellenos y celebraciones. Carne suave y jugosa.', 65.00, 15, 2, NULL, 'https://media.falabella.com/tottusPE/10182815_1/w=800,h=800,fit=pad', NULL, 1, 3),
(158, 'Pato entero (fresco) 2.5–3.5 kg', 'Pato entero fresco, carne firme y sabor intenso. Ideal para estofados o al horno.', 42.00, 15, 2, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQrN4-zOBkOAGvQsGZlJxP04DxBk4PPIjbmJQ&s', NULL, 1, 3),
(159, 'Pierna de pavo (fresca) 1.5–2 kg', 'Pierna de pavo fresca, carne jugosa y perfecta para horno, estofados o parrilla.', 24.00, 20, 3, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSgIaaS4CPIpm_jvKmDpNwqD5yy-HvDppE0I_h3GnzOufVA6plIlmxvZXw&s=10', NULL, 1, 3),
(160, 'Trucha eviscerada (fresca) 900 g – 1.2 kg', 'Trucha fresca, eviscerada y lista para cocinar. Carne rosada y suave, ideal para asar, al horno o a la sartén.', 22.00, 25, 3, NULL, 'https://metroio.vtexassets.com/arquivos/ids/275473-800-auto?v=638179302188200000&width=800&height=auto&aspect=true', NULL, 1, 4),
(161, 'Bonito entero (fresco) 1.5 kg – 2 kg', 'Bonito entero fresco, adecuado para guisos, fileteado o parrilla. Carne firme y sabor tradicional.', 14.00, 25, 3, NULL, 'https://media.falabella.com/tottusPE/10162949_1/w=800,h=800,fit=pad', NULL, 1, 4),
(162, 'Caballa entera (fresca) 800 g – 1.1 kg', 'Caballa entera fresca, rica en omega-3. Ideal para escabeche, a la plancha o frita.', 12.50, 24, 3, NULL, 'https://media.falabella.com/tottusPE/40096717_1/w=800,h=800,fit=pad', NULL, 1, 4),
(163, 'Pejerrey Bells (congelado) 500 g', 'Pejerrey Bells, presentación en bolsa congelada. Ideal para freír o empanizar.', 28.00, 20, 3, NULL, 'https://plazavea.vteximg.com.br/arquivos/ids/19995851-450-450/20078783.jpg?v=638017641574200000', NULL, 1, 4),
(164, 'Tubo de pota (congelado) 1 kg', 'Tubo de pota limpio y congelado. Ideal para aros, salteados o guisos.', 19.00, 20, 3, NULL, 'https://puntofrioencasa.com/cdn/shop/files/pota-tubo-congelada-cefalopodo.jpg?v=1712320604', NULL, 1, 4),
(165, 'Pulpo entero (congelado) 1.2 kg – 1.6 kg', 'Pulpo entero congelado, listo para cocer. Ideal para parrilla, ceviche o platos al olivo.', 38.00, 15, 2, NULL, 'https://media.falabella.com/tottusPE/43366770_1/w=800,h=800,fit=pad', NULL, 1, 4),
(166, 'Filete de salmón premium (congelado) 1 kg', 'Filete de salmón premium congelado, de textura suave y alto contenido de omega-3.', 32.00, 15, 2, NULL, 'https://wongfood.vtexassets.com/arquivos/ids/530612/Filete-de-Salm-n-Premium-Congelado-x-kg-1-56429.jpg?v=637829202410200000', NULL, 1, 4),
(167, 'Lenguado entero (fresco) 700 g – 1 kg', 'Lenguado fresco entero, carne blanca y delicada. Ideal para sudados y plancha.', 26.00, 15, 2, NULL, 'https://s3.eu-west-2.amazonaws.com/mentta/producto/lenguado-de-costa-fresco-pieza-entera-4-lista.jpg', NULL, 1, 4),
(168, 'Camarones pelados y cocidos Mar Verde 227 g', 'Camarones Mar Verde pelados, cocidos y listos para usar. Perfectos para pastas y ensaladas.', 18.00, 20, 3, NULL, 'https://media.falabella.com/tottusCL/21082370_1/w=800,h=800,fit=pad', NULL, 1, 4),
(169, 'Leche Gloria entera 1 L', 'Leche entera UHT Gloria, ideal para consumo diario.', 5.90, 100, 10, NULL, 'https://tofuu.getjusto.com/orioneat-local/resized2/J8Jz39b4wovSoAyFt-300-x.webp', NULL, 1, 5),
(170, 'Leche Laive light 1 L', 'Leche light reducida en grasa, perfecta para dietas balanceadas.', 4.50, 100, 10, NULL, 'https://4msurtidos.com/cdn/shop/products/489584.jpg?v=1592785911', NULL, 1, 5),
(171, 'Huevos pardos 15 unidades', 'Huevos frescos tamaño A, pack familiar de 15 unidades.', 11.90, 80, 10, NULL, 'https://metroio.vtexassets.com/arquivos/ids/240765/Huevos-Pardos-Metro-15un-1-317505114.jpg?v=638173829753730000', NULL, 1, 5),
(172, 'Queso crema Laive 300 g', 'Queso crema suave y fácil de untar, perfecto para panes y recetas.', 10.90, 60, 5, NULL, 'https://storage.googleapis.com/web-laive-storage/Media/pages/17.%20Laive%20Queso%20crema%20227g.jpg', NULL, 1, 5),
(173, 'Queso Edam Gloria 185 g', 'Queso Edam semiduro, ideal para sándwiches y bocaditos.', 12.50, 50, 5, NULL, 'https://metroio.vtexassets.com/arquivos/ids/240985-800-auto?v=638173830987500000&width=800&height=auto&aspect=true', NULL, 1, 5),
(174, 'Yogurt Gloria batido fresa 1 kg', 'Yogurt batido sabor fresa, ideal para desayuno o loncheras.', 9.50, 50, 5, NULL, 'https://static.wixstatic.com/media/95ef20_00d12f42c68d436698fade516a82948c~mv2.jpg/v1/fill/w_480,h_480,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/95ef20_00d12f42c68d436698fade516a82948c~mv2.jpg', NULL, 1, 5),
(175, 'Mantequilla Gloria sin sal 200 g', 'Mantequilla sin sal, ideal para cocina y repostería.', 9.90, 40, 5, NULL, 'https://static.wixstatic.com/media/95ef20_a1d1b6fa1ef14e449d893f914bfcce01~mv2.jpg/v1/fit/w_500,h_500,q_90/file.jpg', NULL, 1, 5),
(176, 'Yogurt griego natural 500 g', 'Yogurt griego espeso y natural, alto en proteína.', 13.90, 40, 5, NULL, 'https://d20f60vzbd93dl.cloudfront.net/uploads/tienda_008371/tienda_008371_5634c54436a861797e3e0eadca997568cdbf6a02_producto_large_90.png?not-from-cache-please', NULL, 1, 5),
(177, 'Huevos de codorniz 24 unidades', 'Huevos de codorniz frescos, ideales para ensaladas, sopas, aperitivos y comidas rápidas.', 8.50, 50, 5, NULL, 'https://huevossantarita.com/wp-content/uploads/2023/07/huevos-codorniz.jpg', NULL, 1, 5),
(178, 'Pan francés (unidad)', 'Pan francés crujiente por fuera y tierno por dentro. Ideal para acompañar las comidas.', 0.40, 200, 20, NULL, 'https://florayfauna.vtexassets.com/arquivos/ids/166627-800-auto?v=638768675971700000&width=800&height=auto&aspect=true', NULL, 1, 6),
(179, 'Pan de molde (paquete)', 'Pan de molde suave, perfecto para tostadas y sándwiches diarios.', 8.50, 80, 10, NULL, 'https://mercury.vtexassets.com/arquivos/ids/9205468/image-154a99b2163b424ab44fadb46284ede3.jpg?v=637981025376200000', NULL, 1, 6),
(180, 'Pan integral (paquete)', 'Pan integral con alto contenido de fibra, opción más saludable para el día a día.', 10.00, 60, 10, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQpn1pju1NFYG9pWkfJsTFFKXzBM86SJnUW9W2pBPXoqDIIIHQBAM4h3o0&s=10', NULL, 1, 6),
(181, 'Keke de vainilla rectangular', 'Keke de vainilla suave y esponjoso, ideal para desayunos o lonches.', 13.50, 30, 5, NULL, 'https://wongfood.vtexassets.com/arquivos/ids/655719-800-auto?v=638296293955670000&width=800&height=auto&aspect=true', NULL, 1, 6),
(182, 'Croissant (unidad)', 'Croissant hojaldrado con mantequilla, perfecto para desayuno o merienda.', 2.50, 50, 5, NULL, 'https://culinaria.group/site/media/Croissant-70g-mantequilla-410339.jpg', NULL, 1, 6),
(183, 'Empanada (unidad)', 'Empanada horneada, relleno clásico. Lista para calentar y servir.', 3.50, 50, 5, NULL, 'https://media.falabella.com/tottusPE/41757817_1/w=800,h=800,fit=pad', NULL, 1, 6),
(184, 'Galletas (paquete)', 'Paquete de galletas surtidas, crujientes y aptas para toda la familia.', 6.00, 40, 5, NULL, 'https://wongfood.vtexassets.com/arquivos/ids/726325-800-auto?v=638629151491400000&width=800&height=auto&aspect=true', NULL, 1, 6),
(185, 'Pan ciabatta (unidad)', 'Pan ciabatta con miga abierta, ideal para sándwiches rústicos y bruschettas.', 3.00, 40, 5, NULL, 'https://unimarc.vtexassets.com/arquivos/ids/245385/000000000000672335-KG-01.jpg?v=638672810561630000', NULL, 1, 6),
(186, 'Pan de mantequilla (unidad)', 'Pan suave con sabor a mantequilla, muy popular para desayunos.', 2.80, 60, 5, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR0CTd4k7HUqhjkKO5J6zhc-ihtQL3yvUS9h-3tlt-ZJ0VLJ0u4L0fwhL5W&s=10', NULL, 1, 6),
(187, 'Fideos Spaghetti Nicolini 450 g', 'Fideos spaghetti elaborados con trigo de alta calidad, ideales para platos tradicionales.', 4.20, 100, 10, NULL, 'https://miamarket.pe/assets/uploads/07d5a45a2601c8689dd035eb10398983.png', NULL, 1, 7),
(188, 'Arroz Pacasmayo 5 kg', 'Arroz extra de grano largo, ideal para comidas familiares.', 22.50, 80, 10, NULL, 'https://metroio.vtexassets.com/arquivos/ids/576236-800-auto?v=638772465935770000&width=800&height=auto&aspect=true', NULL, 1, 7),
(189, 'Lentejas Supremo 500 g', 'Lentejas seleccionadas, ideales para guisos y sopas.', 4.50, 100, 10, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQcrDPYYsJEM3V3biq_tp5WnLUvH6A2dLuwnAeaguGypQ&s=10', NULL, 1, 7),
(190, 'Avena Grano de Oro 1 kg', 'Avena tradicional Grano de Oro, ideal para desayunos y bebidas nutritivas.', 9.50, 60, 5, NULL, 'https://media.falabella.com/tottusPE/42261048_1/w=800,h=800,fit=pad', NULL, 1, 7),
(191, 'Spaghetti Don Vittorio 450 g', 'Spaghetti Don Vittorio elaborado con sémola de trigo duro para una cocción perfecta.', 4.80, 80, 10, NULL, 'https://miamarket.pe/assets/uploads/ac240ebaa06104f08f6112ad4b49f186.jpg', NULL, 1, 7),
(192, 'Arveja partida Costeño 500 g', 'Arveja verde partida Costeño, ideal para sopas, purés y guisos caseros.', 4.20, 80, 10, NULL, 'https://wongfood.vtexassets.com/arquivos/ids/155986/Arveja-Verde-Costeno-Bolsa-500-g-30039.jpg?v=636052204548530000', NULL, 1, 7),
(193, 'Maíz para palomitas 1 kg', 'Maíz para palomitas de alta expansión, perfecto para preparar canchita casera.', 7.50, 50, 5, NULL, 'https://carnicaszurita.es/188721-large_default/maiz-rosetero-1kg.jpg', NULL, 1, 7),
(194, 'Frejol Panamito Costeño 1 kg', 'Frejol panamito Costeño, de grano grande y textura cremosa. Ideal para menestras peruanas.', 9.20, 50, 5, NULL, 'https://plazavea.vteximg.com.br/arquivos/ids/27552450-450-450/1605.jpg?v=638313121215030000', NULL, 1, 7),
(195, 'Maíz morado seco 1 kg', 'Maíz morado seco, ideal para preparar chicha morada y mazamorra.', 6.50, 50, 5, NULL, 'https://plazavea.vteximg.com.br/arquivos/ids/169290-512-512/maiz-morado-bolsa-1kg.jpg', NULL, 1, 7),
(196, 'Cereal Zucaritas 420 g', 'Cereal de hojuelas de maíz azucaradas, perfecto para un desayuno energético y delicioso.', 14.50, 49, 5, NULL, 'https://plazavea.vteximg.com.br/arquivos/ids/29227177-450-450/20426139.jpg?v=638566909311870000', NULL, 1, 8),
(197, 'Hojas de maíz Zuck 140 g', 'Hojas de maíz secas listas para preparar tamales, humitas o recetas tradicionales.', 3.00, 40, 5, NULL, 'https://tofuu.getjusto.com/orioneat-local/resized2/LDR4qtrb3bL4SJDuk-2400-x.webp', NULL, 1, 8),
(198, 'Papas Lays clásicas 170 g', 'Papas fritas clásicas Lays, crocantes y con el sabor tradicional que todos conocen.', 7.00, 60, 5, NULL, 'https://aceleralastatic.nyc3.cdn.digitaloceanspaces.com/files/uploads/1499/1603484942-100-lays-papas-jpg.jpg', NULL, 1, 8),
(199, 'Chizitos 190 g', 'Snacks de maíz sabor queso, ligeros y perfectos para picar en cualquier momento.', 4.50, 60, 5, NULL, 'https://tofuu.getjusto.com/orioneat-local/resized2/h6k7NYChQvMqFFbKs-300-x.webp', NULL, 1, 8),
(200, 'Doritos Nacho 146 g', 'Tortillas crujientes sabor nacho, ideales para compartir en reuniones o disfrutar como snack.', 3.50, 60, 5, NULL, 'https://unimarket.ca/cdn/shop/files/Doritos-Nacho.jpg?v=1725985062&width=1080', NULL, 1, 8),
(201, 'Maní salado 200 g', 'Maní salado crocante, fuente natural de energía. Perfecto como snack o complemento.', 5.50, 50, 5, NULL, 'https://metroio.vtexassets.com/arquivos/ids/418851/773615-1.jpg?v=638279379670330000', NULL, 1, 8),
(202, 'Cereal Ángel Flakes 500 g', 'Cereal Ángel Flakes de maíz tostado, ideal para desayunos rápidos y nutritivos.', 9.90, 40, 5, NULL, 'https://plazachevere.com/6953-large_default/cereal-angel-flakes-bolsa-500g.jpg', NULL, 1, 8),
(203, 'Botana surtida Barcel 27 unidades', 'Caja surtida con 27 snacks individuales Barcel, incluyendo Takis, Chips y otras botanas variadas.', 24.90, 30, 5, NULL, 'https://i5.walmartimages.com.mx/samsmx/images/product-images/img_large/981040866l.jpg?odnHeight=612&odnWidth=612&odnBg=FFFFFF', NULL, 1, 8),
(204, 'Pringles Original 104 g', 'Papas Pringles sabor original, con textura crujiente y empaque práctico para llevar.', 8.50, 40, 5, NULL, 'https://mercadomadrid.com.co/13430-superlarge_default_2x/papas-pringles-naturalx124-gramosxund.jpg', NULL, 1, 8),
(205, 'Cerveza Cusqueña Dorada Six Pack 310 ml', 'Cerveza premium tipo lager con un sabor equilibrado y refrescante.', 21.90, 44, 5, NULL, 'https://www.happydrinkdelivery.com/wp-content/uploads/2022/04/CUSQUENA-DORADA-SIX-PACK-BOTELLAS-DE-310-ML.jpg', NULL, 1, 9),
(206, 'Cerveza Pilsen Six Pack 355 ml', 'Cerveza clásica peruana con cuerpo ligero y sabor suave.', 18.50, 49, 5, NULL, 'https://liqueurbeef.com/wp-content/uploads/2021/08/pils.jpg', NULL, 1, 9),
(207, 'Vino Tacama Selección Especial Tinto 750 ml', 'Vino tinto peruano con notas frutales y taninos suaves, ideal para carnes.', 32.90, 30, 3, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSBuNUF6_TtGw3O9LUrgo3ShyeRTjCJMI_cDcqX_6DXNGhwsIbSe_ut87AA&s=10', NULL, 1, 9),
(208, 'Ron Cartavio Black 750 ml', 'Ron oscuro peruano con aroma intenso, perfecto para cocteles.', 26.90, 30, 3, NULL, 'https://corporacionliderperu.com/51988-large_default/ron-cartavio-x-750-ml-black.jpg', NULL, 1, 9),
(209, 'Whisky Johnnie Walker Red Label 750 ml', 'Whisky escocés blended con sabor especiado y ahumado.', 69.90, 20, 2, NULL, 'https://licoresmedellin.com/cdn/shop/files/whisky-johnnie-walker-red-label-botella-700mlwhisky-johnnie-walker-red-label-botella-700mljohnnie-walkerlicores-medellin-9011566_810x.jpg?v=1759122553', NULL, 1, 9),
(210, 'Tequila José Cuervo Especial 750 ml', 'Tequila reposado suave, ideal para shots y cocteles como margaritas.', 54.90, 20, 2, NULL, 'https://www.stock.com.py/images/thumbs/0203374.jpeg', NULL, 1, 9),
(211, 'Cerveza Corona Extra Six Pack 355 ml', 'Pack de cerveza Corona ligera y refrescante. Ideal para reuniones.', 32.90, 512, 5, NULL, 'https://licoreriadisenzo.pe/wp-content/uploads/2024/10/Cerveza-Corona-Extra-Six-Pack-en-botella-355-ml.jpg', NULL, 1, 9),
(212, 'Vino Santiago Queirolo Tinto Borgoña', 'Vino tinto Borgoña de sabor afrutado y suave. Ideal para carnes y pastas.', 18.90, 30, 3, NULL, 'https://licoreriadisenzo.pe/wp-content/uploads/2025/02/Santiago-Queirolo-Borgona-375-ml-1024x1024.jpg', NULL, 1, 9),
(213, 'Espumante Ricadonna Asti', 'Espumante dulce y afrutado, perfecto para celebraciones.', 22.90, 25, 3, NULL, 'https://licoreriadisenzo.pe/wp-content/uploads/2024/10/Espumoso-Riccadonna-Asti-200-ml.jpg', NULL, 1, 9),
(214, 'Agua Mineral San Mateo 500 ml', 'Agua mineral San Mateo en presentación personal, ideal para mantener la hidratación diaria.', 2.50, 98, 10, NULL, 'https://kyotorolls.com/cdn/shop/products/AguaSanMateo.png?v=1674195723', NULL, 1, 10),
(215, 'Coca Cola Original 500 ml', 'Coca-Cola clásica en presentación personal, refrescante y con su sabor original.', 3.00, 120, 10, NULL, 'https://www.bembos.com.pe/media/catalog/product/2/1/2146469602_1.png?optimize=medium&bg-color=255,255,255&fit=bounds&height=700&width=700&canvas=700:700&format=jpeg', NULL, 1, 10),
(216, 'Inca Kola Original 500 ml', 'La bebida de sabor nacional en formato personal.', 3.00, 120, 10, NULL, 'https://vegaperu.vtexassets.com/arquivos/ids/166544-800-450?v=638521644764100000&width=800&height=450&aspect=true', NULL, 1, 10),
(217, 'Guaraná Backus 450 ml', 'Bebida gasificada sabor guaraná, muy popular y refrescante.', 2.50, 80, 10, NULL, 'https://chang.pe/wp-content/uploads/2023/12/GUARANA-450-ML.jpg', NULL, 1, 10),
(218, 'Agua San Luis 625 ml', 'Agua purificada San Luis en presentación personal.', 2.00, 119, 10, NULL, 'https://www.bembos.com.pe/media/catalog/product/2/1/2146466379.png', NULL, 1, 10),
(219, 'Sporade Blue 500 ml', 'Bebida rehidratante sabor Blue Energy.', 3.50, 60, 5, NULL, 'https://media.falabella.com/tottusPE/40884697_1/w=1500,h=1500,fit=pad', NULL, 1, 10),
(220, 'Volt Energy Drink 473 ml', 'Bebida energética de alto rendimiento para recuperar energía.', 3.00, 60, 5, NULL, 'https://tofuu.getjusto.com/orioneat-local/resized2/rqBA3buRXbtGXSvxp-2400-x.webp', NULL, 1, 10),
(221, 'Frugos Néctar Naranja 1 L', 'Néctar Frugos sabor naranja, presentación ideal para un consumo prolongado.', 6.50, 50, 5, NULL, 'https://oechsle.vteximg.com.br/arquivos/ids/1350475-1000-1000/image-c418f8ff0cbc4ed5a0194c947dde3beb.jpg?v=637494467536630000', NULL, 1, 10),
(222, 'Cifrut Sabor Piña 500 ml', 'Bebida Cifrut sabor piña, refrescante y económica en presentación personal.', 2.00, 80, 10, NULL, 'https://a3f5a93f5f.cbaul-cdnwnd.com/eb784279c426bd80f366ada98f54e61d/200000105-f1a1ef298f/700/cifrut%20granadilla.PNG?ph=a3f5a93f5f', NULL, 1, 10),
(223, 'Escoba de cerdas mixtas', 'Escoba resistente para uso doméstico en piso y exteriores.', 19.50, 28, 3, NULL, 'https://www.ofimarket.pe/cdn/shop/products/PR09910_600x600_crop_center.jpg?v=1627315342', NULL, 1, 11),
(224, 'Trapeador microfibra + balde', 'Set trapeador con mopa microfibra y balde escurridor.', 89.90, 15, 2, NULL, 'https://productosdelimpiezalima.com/image/catalog/1lazy/balde.jpg', NULL, 1, 11),
(225, 'Esponja multiuso x3', 'Pack de 3 esponjas para cocina y limpieza general.', 6.50, 49, 5, NULL, 'https://metroio.vtexassets.com/arquivos/ids/533833/ESPONJA-SALVAU-AS-X3-HOME-CARE-2-262811.jpg?v=638556465668100000', NULL, 1, 11),
(226, 'Guantes de limpieza (par)', 'Guantes reutilizables para limpieza hogareña.', 12.00, 40, 5, NULL, 'https://www.ofimarket.pe/cdn/shop/files/guante-conveniente-talla-s-7-virutex-pro-sku-z310027.jpg?v=1697228494&width=1000', NULL, 1, 11),
(227, 'Bolsas de basura 35 L', 'Bolsas resistentes para residuos domésticos.', 29.90, 60, 5, NULL, 'https://grupocasalima.com/wp-content/uploads/venta-de-bolsa-negra-de-35-lt-100-unidades-en-lima.jpg', NULL, 1, 11),
(228, 'Organizador multiusos apilable', 'Caja apilable para almacenamiento en cocina o despensa.', 23.90, 25, 3, NULL, 'https://www.megaplastic.cl/wp-content/uploads/2022/04/apilable-ventilado.png', NULL, 1, 11),
(229, 'Jarra medidora 1 L', 'Jarra medidora transparente con graduación precisa para líquidos y alimentos.', 9.90, 39, 5, NULL, 'https://promart.vteximg.com.br/arquivos/ids/577076-1000-1000/image-b1292295c33647a3bb5ea3de1c54a929.jpg?v=637406411194830000', NULL, 1, 11),
(230, 'Tabla de picar de plástico', 'Tabla de picar resistente, ideal para carnes, verduras y uso diario en cocina.', 14.50, 35, 5, NULL, 'https://media.falabella.com/tottusPE/10494869_1/public', NULL, 1, 11),
(231, 'Sartén antiadherente 26 cm', 'Sartén antiadherente de 26 cm, ideal para cocinar con menos aceite. Distribuye el calor de manera uniforme.', 24.90, 20, 2, NULL, 'https://www.facusa.com.pe/1597-large_default/set-de-ollas-x-06-tapa-de-vidrio-.jpg', NULL, 1, 11),
(232, 'Kleinhe Detergente líquido para ropa 5 L', 'Detergente líquido Kleinhe para ropa, fórmula concentrada y rendimiento para múltiples lavadas.', 34.90, 30, 3, NULL, 'https://media.falabella.com/sodimacPE/4253108_01/w=800,h=800,fit=pad', NULL, 1, 12),
(233, 'Clorox Lejía / Cloro 639 ml', 'Lejía Clorox para desinfección profunda en superficies y blanqueo de ropa blanca.', 2.50, 80, 10, NULL, 'https://mundoabarrotes.com/wp-content/uploads/2021/08/Lejia-Clorox-15-unidades-639ml.webp', NULL, 1, 12),
(234, 'Ayudín Lavavajillas líquido 900 ml', 'Lavavajillas líquido Ayudín, elimina grasa difícil y deja un acabado brillante.', 12.00, 48, 5, NULL, 'https://vegaperu.vtexassets.com/arquivos/ids/161011-800-450?v=637975846425930000&width=800&height=450&aspect=true', NULL, 1, 12),
(235, 'Pledge Limpiador multisuperficie 650 ml', 'Limpiador multisuperficie Pledge, limpia, desengrasa y da brillo sin dañar.', 14.50, 40, 5, NULL, 'https://promart.vteximg.com.br/arquivos/ids/10298081-1000-1000/153475.jpg?v=639080826888770000', NULL, 1, 12),
(236, 'Suavizante Ensueño Primaveral 1 L', 'Suavizante Ensueño aroma primaveral, suaviza las telas y deja un perfume prolongado.', 16.90, 40, 5, NULL, 'https://oechsle.vteximg.com.br/arquivos/ids/1718205-1000-1000/image-62803e23b262487e91f96a62b38b4e5a.jpg?v=637494942182170000', NULL, 1, 12),
(237, 'Sanytol Desinfectante en spray para cocinas 500 ml', 'Desinfectante Sanytol en spray, elimina bacterias y grasa en superficies de cocina.', 18.00, 30, 5, NULL, 'https://dankmarket.cl/cdn/shop/products/SANYTOL-Cocinas.jpg?v=1749099558', NULL, 1, 12),
(238, 'Ariel Detergente en polvo 1 kg', 'Detergente en polvo Ariel, fórmula concentrada para eliminar manchas difíciles y mantener el color de las prendas.', 18.50, 39, 5, NULL, 'https://superxtrapanama.vtexassets.com/arquivos/ids/172022/7500435112413.png?v=638134228135470000', NULL, 1, 12),
(239, 'Cif Gel Limpiador para Baño 500 ml', 'Gel limpiador Cif para baño, elimina sarro y manchas de superficies cerámicas, lavatorios y bañeras.', 11.90, 35, 5, NULL, 'https://www.lagranbodega.com.pe/public/images/products/794245.jpg', NULL, 1, 12),
(240, 'Finish Quantum pastillas para lavavajillas (46 unidades)', 'Finish Quantum en cápsulas para lavavajillas con poder desengrasante y brillo superior.', 29.90, 20, 2, NULL, 'https://kitchenstudio.pe/wp-content/uploads/2025/10/Proyecto-nuevo-32.png', NULL, 1, 12),
(241, 'Shampoo Head & Shoulders Limpieza Renovadora 375 ml', 'Shampoo anticaspa con fórmula refrescante que limpia profundamente el cuero cabelludo.', 17.90, 40, 5, NULL, 'https://cdnx.jumpseller.com/variedades-adonays/image/48882441/resize/640/640?1715979122', NULL, 1, 13),
(242, 'Jabón de Tocador Dove Original Pack 3 unidades', 'Jabón Dove con crema humectante para una piel suave y nutrida.', 9.50, 50, 5, NULL, 'https://plazavea.vteximg.com.br/arquivos/ids/29522461-512-512/20281722.jpg', NULL, 1, 13),
(243, 'Pasta Dental Colgate', 'Protección completa con frescura, limpieza y prevención de caries.', 3.50, 80, 10, NULL, 'https://pxmshare.colgatepalmolive.com/JPEG_1500/SxhXNHpW28B-ie1Py-dTh.jpg', NULL, 1, 13),
(244, 'Desodorante Nivea Men Fresh Active 150 ml', 'Desodorante masculino con 48 horas de protección y fragancia fresca.', 13.90, 48, 5, NULL, 'https://media.falabella.com/tottusPE/41672421_2/w=800,h=800,fit=pad', NULL, 1, 13),
(245, 'Enjuague Bucal Listerine Cool Mint 250 ml', 'Enjuague bucal Listerine Cool Mint que elimina gérmenes y refresca el aliento por más tiempo.', 12.90, 40, 5, NULL, 'https://wongfood.vtexassets.com/arquivos/ids/723024-800-auto?v=638611761726500000&width=800&height=auto&aspect=true', NULL, 1, 13),
(246, 'Papel Higiénico Elite Pack 6', 'Papel higiénico de doble hoja, extra suave y de alta duración.', 9.90, 60, 5, NULL, 'https://4msurtidos.com/cdn/shop/products/p-h-elite-celeste-doble-hoja-x-6-un.jpg?v=1593015113', NULL, 1, 13),
(247, 'Crema corporal Nivea Soft', 'Crema hidratante Nivea Soft con textura ligera, ideal para rostro, manos y cuerpo.', 11.90, 40, 5, NULL, 'https://farmaciasdelpueblo.vtexassets.com/arquivos/ids/188425/Nivea-Crema-Corporal-NIVEA-Soft-Milk-5en1-para-piel-seca-x-250-ml-4006000040417_img1.png?v=638513790119530000', NULL, 1, 13),
(248, 'Crema para barba Gillette 175 g', 'Crema para barba Gillette, ideal para un afeitado suave y fácil.', 8.90, 29, 5, NULL, 'https://static.beautytocare.com/media/catalog/product/k/i/king-c-gillette-shaving-cream-175ml.png', NULL, 1, 13),
(249, 'Gel fijador Ego Extrafuerte', 'Gel fijador Ego de máxima fijación, ideal para peinados duraderos sin dejar residuos blancos.', 8.50, 34, 5, NULL, 'https://www.hogarysalud.com.pe/wp-content/uploads/2024/10/00210829.jpg', NULL, 1, 13),
(250, 'Pañales Pampers Confort Sec Talla M (44 unidades)', 'Pañales Pampers con ajuste cómodo y mayor absorción para mantener seco al bebé.', 42.90, 29, 5, NULL, 'https://farmacorp.com/cdn/shop/files/7500435106627_700x700.jpg?v=1717011166', NULL, 1, 14),
(251, 'Toallitas Húmedas Huggies Manzanilla (80 unidades)', 'Toallitas húmedas suaves con extracto de manzanilla, ideales para piel sensible.', 9.50, 40, 5, NULL, 'https://walmartni.vtexassets.com/arquivos/ids/421184-800-450?v=638569365359270000&width=800&height=450&aspect=true', NULL, 1, 14),
(252, 'Shampoo para Bebé Johnsons 400 ml', 'Shampoo hipoalergénico No Más Lágrimas, ideal para baño diario.', 16.90, 25, 5, NULL, 'https://farmaciasdelpueblo.vtexassets.com/arquivos/ids/205294-800-800?v=638730623093630000&width=800&height=800&aspect=true', NULL, 1, 14),
(253, 'Crema Protectora para Bebé Huggies 100 g', 'Crema protectora para prevenir irritaciones y mantener la piel suave.', 12.90, 24, 5, NULL, 'https://cofasur.vteximg.com.br/arquivos/ids/285451-1000-1000/D_NQ_NP_680246-MLA89723838183_082025-O.webp?v=638953613957530000', NULL, 1, 14),
(254, 'Talco Johnsons Baby 200 g', 'Talco suave para mantener la piel del bebé fresca y seca.', 10.50, 28, 5, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR4ZOhtxAJYfqDohyGDQJQvecFZIKOWNiSBgw&s', NULL, 1, 14),
(255, 'Biberón Philips Avent Clásico 260 ml', 'Biberón anticólicos de flujo controlado, ideal para recién nacidos.', 29.90, 15, 5, NULL, 'https://http2.mlstatic.com/D_NQ_NP_2X_727148-MPE111195892016_052026-F.webp', NULL, 1, 14),
(256, 'Loción Humectante Johnsons Baby 500 ml', 'Loción humectante hipoalergénica para mantener la piel del bebé suave e hidratada todo el día.', 18.90, 23, 5, NULL, 'https://images-eu.ssl-images-amazon.com/images/I/61Gd8CPVzvL._AC_UL495_SR435,495_.jpg', NULL, 1, 14),
(257, 'Chupón ortodóntico Tommee Tippee Fun Friends Pack x2', 'Chupón ortodóntico diseñado para apoyar el desarrollo natural de encías y dientes.', 22.50, 14, 5, NULL, 'https://promart.vteximg.com.br/arquivos/ids/9663558-380-380/image-5951f948664b4738b6e7ffb5f10f61b8.jpg?v=638986654545000000', NULL, 1, 14),
(258, 'Aceite para Bebé Johnsons 200 ml', 'Aceite suave indicado para masajes y cuidado diario de la piel del bebé.', 12.50, 24, 5, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQIIFJGVXkDE03v1XEs4SYAavpuw_jed9S3jQ1uMY3fGeo97uC2aN_s4W9N54Q9VdL9mFI&usqp=CAU', NULL, 1, 14);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_promocion`
--

CREATE TABLE `producto_promocion` (
  `id_producto` int(11) NOT NULL,
  `id_promocion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `producto_promocion`
--

INSERT INTO `producto_promocion` (`id_producto`, `id_promocion`) VALUES
(130, 1),
(131, 1),
(132, 1),
(133, 1),
(134, 1),
(135, 1),
(136, 1),
(138, 1),
(142, 1),
(143, 1),
(144, 1),
(145, 1),
(146, 1),
(151, 2),
(152, 2),
(153, 2),
(154, 2),
(155, 2),
(156, 2),
(160, 2),
(161, 2),
(162, 2),
(163, 2),
(164, 2),
(165, 2),
(169, 3),
(170, 3),
(171, 3),
(172, 3),
(173, 3),
(178, 3),
(179, 3),
(180, 3),
(181, 3),
(187, 3),
(188, 3),
(189, 3),
(191, 3),
(205, 4),
(206, 4),
(207, 4),
(208, 4),
(211, 4),
(212, 4),
(214, 4),
(215, 4),
(216, 4),
(217, 4),
(218, 4),
(219, 4),
(220, 4),
(223, 5),
(224, 5),
(225, 5),
(226, 5),
(227, 5),
(228, 5),
(232, 5),
(233, 5),
(234, 5),
(235, 5),
(236, 5),
(237, 5),
(241, 6),
(242, 6),
(243, 6),
(244, 6),
(245, 6),
(246, 6),
(250, 6),
(251, 6),
(252, 6),
(253, 6),
(254, 6),
(255, 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promocion`
--

CREATE TABLE `promocion` (
  `id_promocion` int(11) NOT NULL,
  `nombre_promocion` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `porcentaje_descuento` decimal(5,2) NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `promocion`
--

INSERT INTO `promocion` (`id_promocion`, `nombre_promocion`, `descripcion`, `porcentaje_descuento`, `fecha_inicio`, `fecha_fin`, `estado`) VALUES
(1, 'Oferta Verano', 'Descuentos frescos en verduras y frutas de temporada', 15.00, '2026-07-01', '2030-12-31', 1),
(2, 'Semana de Proteínas', 'Las mejores carnes y pescados con descuento especial', 20.00, '2026-07-01', '2030-12-31', 1),
(3, 'Desayuno Feliz', 'Todo para tu desayuno ideal a precios increíbles', 10.00, '2026-07-01', '2030-12-31', 1),
(4, 'Happy Hour Bebidas', 'Refrescate con nuestras bebidas al mejor precio', 25.00, '2026-07-05', '2030-12-31', 1),
(5, 'Hogar Limpio', 'Productos de limpieza y cuidado personal con gran descuento', 18.00, '2026-07-01', '2030-12-31', 1),
(6, 'Snack Time', 'Snacks y bebidas para disfrutar en casa', 12.00, '2026-07-10', '2030-12-31', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `repartidor`
--

CREATE TABLE `repartidor` (
  `id_repartidor` int(11) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `placa_vehiculo` varchar(20) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `repartidor`
--

INSERT INTO `repartidor` (`id_repartidor`, `nombres`, `telefono`, `placa_vehiculo`, `estado`, `id_usuario`) VALUES
(3, 'Pepe Perez', '949844744', '1233', 0, 5),
(7, 'RICHARD ALEXANDER QUIROZ QUISPE', '935349698', '122', 1, 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `nombre_rol`) VALUES
(1, 'Administrador'),
(4, 'Almacenero'),
(6, 'Cajero'),
(2, 'Cliente'),
(5, 'Despachador'),
(3, 'Repartidor'),
(7, 'Soporte');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `soporte_cliente`
--

CREATE TABLE `soporte_cliente` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `asunto` varchar(100) NOT NULL,
  `mensaje` text NOT NULL,
  `respuesta_admin` text DEFAULT NULL,
  `fecha_respuesta` datetime DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','respondido') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `soporte_cliente`
--

INSERT INTO `soporte_cliente` (`id`, `usuario_id`, `asunto`, `mensaje`, `respuesta_admin`, `fecha_respuesta`, `fecha`, `estado`) VALUES
(1, 2, 'pedido', 'pedido no entregado', NULL, NULL, '2026-06-30 04:31:00', 'pendiente'),
(2, 2, 'pedido', 'NO ME LLEO EL PEDIDO', NULL, NULL, '2026-06-30 23:44:02', 'pendiente'),
(3, 15, 'pedido', 'no llegó el pedido', 'ahora lo reviso', '2026-07-23 09:18:44', '2026-07-23 13:23:24', 'respondido'),
(4, 15, 'devolucion', 'pedio malogrado', NULL, '2026-07-23 09:22:30', '2026-07-23 14:17:08', 'pendiente'),
(5, 15, 'facturacion', 'me cobraron 2 soles demas', NULL, NULL, '2026-07-23 17:31:08', 'pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `soporte_mensajes`
--

CREATE TABLE `soporte_mensajes` (
  `id` int(11) NOT NULL,
  `soporte_id` int(11) NOT NULL,
  `remitente` enum('cliente','admin') NOT NULL,
  `mensaje` text NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `soporte_mensajes`
--

INSERT INTO `soporte_mensajes` (`id`, `soporte_id`, `remitente`, `mensaje`, `fecha`) VALUES
(1, 1, 'cliente', 'pedido no entregado', '2026-06-30 04:31:00'),
(2, 2, 'cliente', 'NO ME LLEO EL PEDIDO', '2026-06-30 23:44:02'),
(3, 3, 'cliente', 'no llegó el pedido', '2026-07-23 13:23:24'),
(4, 3, 'admin', 'ahora lo reviso', '2026-07-23 13:54:33'),
(5, 3, 'cliente', 'ya', '2026-07-23 14:16:32'),
(6, 4, 'cliente', 'pedio malogrado', '2026-07-23 14:17:09'),
(7, 4, 'admin', 'otra vez tu', '2026-07-23 14:17:30'),
(8, 3, 'admin', 'espera', '2026-07-23 14:18:32'),
(9, 3, 'admin', '...', '2026-07-23 14:18:43'),
(10, 4, 'admin', 'xd', '2026-07-23 14:21:26'),
(11, 4, 'admin', 'ya', '2026-07-23 14:22:30'),
(12, 4, 'cliente', 'ok', '2026-07-23 14:23:21'),
(13, 5, 'cliente', 'me cobraron 2 soles demas', '2026-07-23 17:31:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarjeta_guardada`
--

CREATE TABLE `tarjeta_guardada` (
  `id_tarjeta` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `titular` varchar(150) NOT NULL,
  `numero_encriptado` varchar(255) NOT NULL,
  `vencimiento_encriptado` varchar(255) NOT NULL,
  `marca` varchar(20) NOT NULL,
  `ultimos_cuatro` varchar(4) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tarjeta_guardada`
--

INSERT INTO `tarjeta_guardada` (`id_tarjeta`, `id_usuario`, `titular`, `numero_encriptado`, `vencimiento_encriptado`, `marca`, `ultimos_cuatro`, `fecha_creacion`) VALUES
(1, 15, 'RICHARD QUIROZ', '/bKaQF1sOnkj6FoeNUXRUkhxWnl5ak5GVGZOYm1HVG4wVGxlY1F4aHRHTHp6MUR5WldkdHY2cDdyU009', 'k/0sOilzTNOaAVgudiPevHdMZFZ4ZjVGVEl5TDhUUXhXb3djZFE9PQ==', 'visa', '7575', '2026-07-23 01:32:16'),
(3, 15, 'RICHARD QUIROZ', 'y34uk3sUCF7KArf8JFgRf3lkTGo3eWRvMG1jU1hRQ2xwNytrVkpCdzlTVWg1YS9KM3VVRUZFY1BkbWc9', 'TEchYjU98U7k6uGMHLXklTB4ZTlwL1pMTkdvYTBjakZZNjZrcFE9PQ==', 'mastercard', '5555', '2026-07-23 02:00:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajador`
--

CREATE TABLE `trabajador` (
  `id_trabajador` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `cargo` varchar(50) NOT NULL,
  `turno` varchar(30) DEFAULT 'Mañana',
  `sueldo` decimal(10,2) DEFAULT NULL,
  `fecha_ingreso` date DEFAULT curdate(),
  `estado` tinyint(1) DEFAULT 1,
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `trabajador`
--

INSERT INTO `trabajador` (`id_trabajador`, `id_usuario`, `cargo`, `turno`, `sueldo`, `fecha_ingreso`, `estado`, `notas`) VALUES
(1, 5, 'Cajero', 'Completo', NULL, '2026-09-15', 1, ''),
(2, 15, 'Repartidor', 'Completo', NULL, '2026-09-15', 1, NULL),
(3, 17, 'Soporte', 'Completo', NULL, '2026-09-15', 1, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `google_uid` varchar(150) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `estado` tinyint(1) DEFAULT 1,
  `dni` varchar(8) DEFAULT NULL,
  `codigo_recuperacion` varchar(6) DEFAULT NULL,
  `codigo_expira` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombres`, `apellidos`, `correo`, `password_hash`, `google_uid`, `telefono`, `direccion`, `fecha_registro`, `estado`, `dni`, `codigo_recuperacion`, `codigo_expira`) VALUES
(1, 'Admin', 'Market', 'admin@market.com', '$2y$10$yZZ5/9/GBkXTVnYDo2cyS.QvQKNZb9QtRBzwxSvOdkiX1hu1HbJyO', NULL, '999888777', 'Av. Primavera 123', '2026-06-20 10:29:39', 1, NULL, NULL, NULL),
(5, 'Pepe', 'Perez', 'pepe@market.com', '$2y$10$SKcwxD0dQaB9X32JEmbuUuEr4WZjEejaX2.hbcJKrUlVYZyx4Z.R2', NULL, '949844744', 'av grau123', '2026-06-30 23:09:49', 1, '75646464', NULL, NULL),
(6, 'A', 'Premium', 'alexqroz1@gmail.com', NULL, '1aQVHP2sFvUttk9lgaS3WYxB2oq2', '949844744', 'av grau 4600', '2026-07-01 00:57:50', 1, NULL, NULL, NULL),
(15, 'RICHARD ALEXANDER', 'QUIROZ QUISPE', 'alexndrpe@gmail.com', '$2y$10$b3En9Ru2dj4W.SJ3hxA/KOUHr1YauPepIqIdcQnTSWzFFTWRaHUGW', 'vvebm3Y3GUT0CKEPlWNzprwKAD92', '935349698', 'av grau', '2026-07-01 19:50:06', 1, '76345180', NULL, NULL),
(16, 'JUNIOR', 'JOSUE TICLIAHUANCA PARRA', 'tparrajuniorjos@uss.edu.pe', NULL, 'dg5sp7X9zodSw3AWGXZDN1nxnUV2', NULL, NULL, '2026-07-01 20:10:07', 1, NULL, NULL, NULL),
(17, 'Junior', 'TICLIAHUANCA PARRA', 'tpjune9@gmail.com', '$2y$10$OzIxohO7s9UA6FbMlgRCvOh4j8YtAAgq.02R1oYCgkJPolbtw2xkm', 'UcdYWFvCOTMEEss88I6EnX3vNFO2', '971075741', 'salaverry', '2026-07-01 20:10:22', 1, '76755831', NULL, NULL),
(18, 'Josue', 'TP', 'jtp853860@gmail.com', NULL, 'DPQ3Qh2KjYc4ZRfFFxoY9VLzXng2', NULL, NULL, '2026-07-02 06:26:10', 1, NULL, NULL, NULL),
(19, 'EDUAR ALFREDO', 'YAMPUFE PURISACA', 'eduar@gmail.com', '$2y$10$qOBcp6xDoK9i4Pt8YbZrf.HvdO1PTFh4qlYzyq3lbGXanPvYbmA6G', NULL, '949948484', 'ferreñafe', '2026-07-22 12:38:30', 1, '78900000', NULL, NULL),
(20, 'EDUAR ALFREDO', 'YAMPUFE PURISACA', 'eduar1@gmail.com', '$2y$10$AD9eFPkB/Srdpm7tYqnRcOTALDf99/sGgbWkovOLnegm5LtAHLl5u', NULL, '999999999', 'ferreñafe', '2026-07-22 12:39:42', 1, '55555555', NULL, NULL),
(21, 'Alex', '', 'spot2026pe@gmail.com', NULL, 'jwYUVxoGCyfMyOFOkOXpZTKxIjF3', NULL, NULL, '2026-07-22 21:42:27', 1, NULL, NULL, NULL),
(23, 'JAVIER SMITH', 'MOLINA TAQUIRE', 'quirozalexander897@gmail.com', NULL, 'aP8333DfADS3NOBfa6SvD5T9Mab2', '984848488', 'ferreñafe', '2026-07-23 15:35:21', 1, '74646636', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_rol`
--

CREATE TABLE `usuario_rol` (
  `id_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario_rol`
--

INSERT INTO `usuario_rol` (`id_usuario`, `id_rol`) VALUES
(1, 1),
(5, 2),
(5, 6),
(6, 2),
(15, 2),
(15, 3),
(16, 2),
(17, 7),
(18, 2),
(19, 2),
(20, 2),
(21, 2),
(23, 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id_carrito`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `chatbot_faq`
--
ALTER TABLE `chatbot_faq`
  ADD PRIMARY KEY (`id_faq`);

--
-- Indices de la tabla `comprobante`
--
ALTER TABLE `comprobante`
  ADD PRIMARY KEY (`id_comprobante`),
  ADD KEY `id_pedido` (`id_pedido`);

--
-- Indices de la tabla `delivery`
--
ALTER TABLE `delivery`
  ADD PRIMARY KEY (`id_delivery`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_repartidor` (`id_repartidor`);

--
-- Indices de la tabla `detalle_carrito`
--
ALTER TABLE `detalle_carrito`
  ADD PRIMARY KEY (`id_detalle_carrito`),
  ADD UNIQUE KEY `uk_carrito_producto` (`id_carrito`,`id_producto`),
  ADD KEY `id_carrito` (`id_carrito`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`id_detalle_pedido`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `direccion`
--
ALTER TABLE `direccion`
  ADD PRIMARY KEY (`id_direccion`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  ADD PRIMARY KEY (`id_metodo_pago`);

--
-- Indices de la tabla `movimiento_stock`
--
ALTER TABLE `movimiento_stock`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `pago`
--
ALTER TABLE `pago`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_metodo_pago` (`id_metodo_pago`);

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_pago` (`id_pago`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `producto_promocion`
--
ALTER TABLE `producto_promocion`
  ADD PRIMARY KEY (`id_producto`,`id_promocion`),
  ADD KEY `id_promocion` (`id_promocion`);

--
-- Indices de la tabla `promocion`
--
ALTER TABLE `promocion`
  ADD PRIMARY KEY (`id_promocion`);

--
-- Indices de la tabla `repartidor`
--
ALTER TABLE `repartidor`
  ADD PRIMARY KEY (`id_repartidor`),
  ADD KEY `fk_repartidor_usuario` (`id_usuario`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `nombre_rol` (`nombre_rol`);

--
-- Indices de la tabla `soporte_cliente`
--
ALTER TABLE `soporte_cliente`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `soporte_mensajes`
--
ALTER TABLE `soporte_mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `soporte_id` (`soporte_id`);

--
-- Indices de la tabla `tarjeta_guardada`
--
ALTER TABLE `tarjeta_guardada`
  ADD PRIMARY KEY (`id_tarjeta`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `trabajador`
--
ALTER TABLE `trabajador`
  ADD PRIMARY KEY (`id_trabajador`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `dni` (`dni`);

--
-- Indices de la tabla `usuario_rol`
--
ALTER TABLE `usuario_rol`
  ADD PRIMARY KEY (`id_usuario`,`id_rol`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `id_carrito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `chatbot_faq`
--
ALTER TABLE `chatbot_faq`
  MODIFY `id_faq` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `comprobante`
--
ALTER TABLE `comprobante`
  MODIFY `id_comprobante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT de la tabla `delivery`
--
ALTER TABLE `delivery`
  MODIFY `id_delivery` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT de la tabla `detalle_carrito`
--
ALTER TABLE `detalle_carrito`
  MODIFY `id_detalle_carrito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=202;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id_detalle_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT de la tabla `direccion`
--
ALTER TABLE `direccion`
  MODIFY `id_direccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  MODIFY `id_metodo_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `movimiento_stock`
--
ALTER TABLE `movimiento_stock`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=197;

--
-- AUTO_INCREMENT de la tabla `pago`
--
ALTER TABLE `pago`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=259;

--
-- AUTO_INCREMENT de la tabla `promocion`
--
ALTER TABLE `promocion`
  MODIFY `id_promocion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `repartidor`
--
ALTER TABLE `repartidor`
  MODIFY `id_repartidor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `soporte_cliente`
--
ALTER TABLE `soporte_cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `soporte_mensajes`
--
ALTER TABLE `soporte_mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `tarjeta_guardada`
--
ALTER TABLE `tarjeta_guardada`
  MODIFY `id_tarjeta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `trabajador`
--
ALTER TABLE `trabajador`
  MODIFY `id_trabajador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `comprobante`
--
ALTER TABLE `comprobante`
  ADD CONSTRAINT `comprobante_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id_pedido`) ON DELETE CASCADE;

--
-- Filtros para la tabla `delivery`
--
ALTER TABLE `delivery`
  ADD CONSTRAINT `delivery_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id_pedido`) ON DELETE CASCADE,
  ADD CONSTRAINT `delivery_ibfk_2` FOREIGN KEY (`id_repartidor`) REFERENCES `repartidor` (`id_repartidor`) ON DELETE SET NULL;

--
-- Filtros para la tabla `detalle_carrito`
--
ALTER TABLE `detalle_carrito`
  ADD CONSTRAINT `detalle_carrito_ibfk_1` FOREIGN KEY (`id_carrito`) REFERENCES `carrito` (`id_carrito`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_carrito_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id_pedido`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_pedido_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`);

--
-- Filtros para la tabla `direccion`
--
ALTER TABLE `direccion`
  ADD CONSTRAINT `direccion_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `movimiento_stock`
--
ALTER TABLE `movimiento_stock`
  ADD CONSTRAINT `movimiento_stock_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pago`
--
ALTER TABLE `pago`
  ADD CONSTRAINT `pago_ibfk_1` FOREIGN KEY (`id_metodo_pago`) REFERENCES `metodo_pago` (`id_metodo_pago`) ON DELETE SET NULL;

--
-- Filtros para la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL,
  ADD CONSTRAINT `pedido_ibfk_2` FOREIGN KEY (`id_pago`) REFERENCES `pago` (`id_pago`) ON DELETE SET NULL;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`);

--
-- Filtros para la tabla `producto_promocion`
--
ALTER TABLE `producto_promocion`
  ADD CONSTRAINT `producto_promocion_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`) ON DELETE CASCADE,
  ADD CONSTRAINT `producto_promocion_ibfk_2` FOREIGN KEY (`id_promocion`) REFERENCES `promocion` (`id_promocion`) ON DELETE CASCADE;

--
-- Filtros para la tabla `repartidor`
--
ALTER TABLE `repartidor`
  ADD CONSTRAINT `fk_repartidor_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `soporte_mensajes`
--
ALTER TABLE `soporte_mensajes`
  ADD CONSTRAINT `soporte_mensajes_ibfk_1` FOREIGN KEY (`soporte_id`) REFERENCES `soporte_cliente` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tarjeta_guardada`
--
ALTER TABLE `tarjeta_guardada`
  ADD CONSTRAINT `tarjeta_guardada_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `trabajador`
--
ALTER TABLE `trabajador`
  ADD CONSTRAINT `fk_trabajador_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario_rol`
--
ALTER TABLE `usuario_rol`
  ADD CONSTRAINT `usuario_rol_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuario_rol_ibfk_2` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
