-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-06-2026 a las 16:22:32
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
-- Base de datos: `gamervault`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alertas`
--

CREATE TABLE `alertas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `game_id` varchar(50) NOT NULL,
  `game_nombre` varchar(100) NOT NULL,
  `precio_objetivo` decimal(10,2) NOT NULL,
  `email_notificado` tinyint(1) DEFAULT 0,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alertas`
--

INSERT INTO `alertas` (`id`, `usuario_id`, `game_id`, `game_nombre`, `precio_objetivo`, `email_notificado`, `fecha_creacion`) VALUES
(1, 1, '247173', 'Stray', 15.00, 0, '2026-05-22 13:16:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_busquedas`
--

CREATE TABLE `historial_busquedas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `game_nombre` varchar(200) NOT NULL,
  `fecha_busqueda` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_busquedas`
--

INSERT INTO `historial_busquedas` (`id`, `usuario_id`, `game_nombre`, `fecha_busqueda`) VALUES
(7, 3, 'DELTARUNE', '2026-05-12 09:11:09'),
(36, 1, 'ARK: Survival Evolved', '2026-05-18 11:25:20'),
(37, 1, 'AR-K', '2026-05-18 11:34:43'),
(38, 1, 'Undertale', '2026-05-20 20:03:15'),
(39, 1, 'Project Zomboid', '2026-05-20 20:25:34'),
(40, 1, 'Stardew Valley', '2026-05-20 21:26:15'),
(41, 1, 'Subnautica', '2026-05-22 09:10:16'),
(42, 1, 'Lethal Company', '2026-05-22 09:10:33'),
(43, 1, 'Stray', '2026-05-22 09:11:04'),
(44, 1, 'Rhythm Zone', '2026-05-22 09:11:38'),
(45, 1, 'Neon White', '2026-05-22 09:12:03'),
(46, 1, 'Stray', '2026-05-22 09:14:16'),
(47, 1, 'Neon White', '2026-05-22 09:17:21'),
(48, 1, 'Neon White', '2026-05-22 09:17:23'),
(49, 1, 'Neon White', '2026-05-22 09:21:49'),
(50, 1, 'Neon White', '2026-05-22 09:21:49'),
(51, 1, 'Neon White', '2026-05-22 09:21:50'),
(52, 1, 'Neon White', '2026-05-22 09:21:50'),
(53, 1, 'Neon White', '2026-05-22 09:21:50'),
(54, 1, 'Neon White', '2026-05-22 09:21:51'),
(55, 1, 'Stray', '2026-05-22 09:21:53'),
(56, 1, 'Neon', '2026-05-22 09:21:57'),
(57, 1, 'Neon White', '2026-05-22 09:22:01'),
(58, 1, 'Devil May Cry 5', '2026-05-22 09:22:24'),
(59, 1, 'Neon White', '2026-05-22 09:22:28'),
(60, 1, 'Neon', '2026-05-22 09:22:29'),
(61, 1, 'Neon White', '2026-05-22 09:22:29'),
(62, 1, 'Neon White', '2026-05-22 09:24:21'),
(63, 1, 'Neon White', '2026-05-22 09:24:29'),
(64, 1, 'Neon White', '2026-05-22 09:24:30'),
(65, 1, 'Neon White', '2026-05-22 09:24:30'),
(66, 1, 'Neon White', '2026-05-22 09:24:31'),
(67, 1, 'Neon White', '2026-05-22 09:24:31'),
(68, 1, 'Neon White', '2026-05-22 09:24:32'),
(69, 1, 'Neon White', '2026-05-22 09:24:32'),
(70, 1, 'Neon White', '2026-05-22 09:24:32'),
(71, 1, 'Neon White', '2026-05-22 09:24:32'),
(72, 1, 'Devil May Cry 5', '2026-05-22 09:24:50'),
(73, 1, 'ARK: Survival Evolved', '2026-05-22 09:24:55'),
(74, 1, 'Project Zomboid', '2026-05-22 09:45:27'),
(75, 1, 'Stray', '2026-05-22 10:15:52'),
(76, 1, 'Project Zomboid', '2026-05-22 10:28:16'),
(77, 1, 'Move or Die', '2026-05-22 10:28:39'),
(78, 1, 'Move or Die', '2026-05-22 10:28:51'),
(79, 1, 'Move or Die', '2026-05-22 10:28:53'),
(80, 1, 'Stray', '2026-05-22 10:28:56'),
(81, 1, 'Project Zomboid', '2026-05-22 11:16:06'),
(82, 1, 'Project Zomboid', '2026-05-25 20:45:32'),
(83, 1, 'Project Zomboid', '2026-05-25 20:45:39'),
(84, 1, 'Wallpaper Engine', '2026-05-25 22:02:35'),
(85, 1, 'Project Zomboid', '2026-05-25 22:03:11'),
(86, 1, 'ARK: Survival Evolved', '2026-05-25 22:07:19'),
(87, 1, 'Stray', '2026-05-25 22:07:54'),
(88, 1, 'Stray', '2026-05-25 22:08:01'),
(89, 1, 'ARK: Survival Evolved', '2026-05-25 22:27:53'),
(90, 1, 'ARK: Survival Evolved', '2026-05-25 22:28:01'),
(91, 1, 'ARK: Survival Evolved', '2026-05-25 22:28:06'),
(92, 1, 'ARK: Survival Evolved', '2026-05-25 22:28:20'),
(93, 1, 'ARK: Survival Evolved', '2026-05-25 22:33:11'),
(94, 1, 'ARK: Survival Evolved', '2026-05-25 22:33:12'),
(95, 1, 'AR-K', '2026-05-25 22:33:25'),
(96, 1, 'AR-K', '2026-05-25 22:33:37'),
(97, 1, 'AR-K', '2026-05-25 22:33:38'),
(98, 1, 'AR-K', '2026-05-25 22:33:38'),
(99, 1, 'AR-K', '2026-05-25 22:33:38'),
(100, 1, 'AR-K', '2026-05-25 22:33:40'),
(101, 1, 'AR-K', '2026-05-25 22:33:40'),
(102, 1, 'AR-K', '2026-05-25 22:33:41'),
(103, 1, 'Dark PGT', '2026-05-25 22:33:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `rol` enum('usuario','admin') DEFAULT 'usuario',
  `foto` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `contrasena`, `fecha_registro`, `rol`, `foto`) VALUES
(1, 'Raziiel', 'hermidaagustin08@gmail.com', '$2y$10$zCgEQxGDvpEWNEAatQZi4OrTLcs7Hy/Cf8YKaAtfnHEeutFuVtptu', '2026-05-11 20:53:36', 'usuario', 'perfil_1.jpg'),
(2, 'noha', 'nohara1@gmail.com', '$2y$10$csIpm2OnEqz/IsKtB2sBh.yJZj1egSLiXUBNFC40Jf5vlxi4maSSu', '2026-05-11 21:19:25', 'usuario', 'default.jpg'),
(3, 'elias', 'elias@gmail.com', '$2y$10$IA61hzL6EOecVCL.QtyV2uR2zA5iVyJFYrvZuJqBld7pyBVQnE4Fu', '2026-05-12 09:10:46', 'usuario', 'perfil_3.jpg'),
(4, 'aaa', 'aa@gmail.com', '$2y$10$9COkY5dcdEqS5YUF1AHDU.IaRkOYS7ONeL1xqUaylVK/evx6mRPIm', '2026-05-25 21:11:37', 'usuario', 'default.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `game_id` varchar(50) NOT NULL,
  `game_nombre` varchar(200) NOT NULL,
  `precio_objetivo` decimal(10,2) DEFAULT NULL,
  `fecha_agregado` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `wishlist`
--

INSERT INTO `wishlist` (`id`, `usuario_id`, `game_id`, `game_nombre`, `precio_objetivo`, `fecha_agregado`) VALUES
(6, 1, '321926', 'Subnautica 2', NULL, '2026-05-18 11:23:24'),
(10, 1, '102722', 'Project Zomboid', NULL, '2026-05-25 22:03:12'),
(12, 1, '247173', 'Stray', NULL, '2026-05-25 22:07:56'),
(13, 1, '144209', 'ARK: Survival Evolved', NULL, '2026-05-25 22:28:21'),
(14, 1, '197924', 'Subnautica Below Zero', NULL, '2026-05-25 22:45:50');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alertas`
--
ALTER TABLE `alertas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `historial_busquedas`
--
ALTER TABLE `historial_busquedas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alertas`
--
ALTER TABLE `alertas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `historial_busquedas`
--
ALTER TABLE `historial_busquedas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alertas`
--
ALTER TABLE `alertas`
  ADD CONSTRAINT `alertas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `historial_busquedas`
--
ALTER TABLE `historial_busquedas`
  ADD CONSTRAINT `historial_busquedas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
