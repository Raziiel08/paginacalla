-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-07-2026 a las 21:52:53
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
(1, 1, '247173', 'Stray', 14.00, 0, '2026-05-22 13:16:08'),
(2, 1, '144209', 'ARK: Survival Evolved', 15.00, 0, '2026-06-03 11:37:48'),
(3, 1, '282776', 'ELDEN RING Shadow of the Erdtree', 15.00, 0, '2026-07-04 15:03:18');

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
(105, 1, 'Project Zomboid', '2026-06-07 13:55:17'),
(106, 1, 'ELDEN RING', '2026-07-04 12:12:59'),
(107, 1, 'ARK: Survival Ascended', '2026-07-04 12:17:04'),
(108, 1, 'ELDEN RING', '2026-07-04 12:17:40');

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
(4, 'aaa', 'aa@gmail.com', '$2y$10$9COkY5dcdEqS5YUF1AHDU.IaRkOYS7ONeL1xqUaylVK/evx6mRPIm', '2026-05-25 21:11:37', 'usuario', 'default.jpg'),
(5, 'qweqwe', 'qweqwe@pepe', '$2y$10$wkhwjXc8Cy1xvVk42di0He7deDjq9H8n88XOZROP1erYjhLetcWZq', '2026-07-04 16:36:52', 'usuario', 'default.jpg'),
(6, 'qweqwe', 'qweqwe@gmail.com', '$2y$10$StHP1FYDJfS9NCQZXjAp/.pQqe3LObmlE8O6hTT1N1lUlStjqahBq', '2026-07-04 16:43:10', 'usuario', 'default.jpg');

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
(14, 1, '197924', 'Subnautica Below Zero', NULL, '2026-05-25 22:45:50'),
(15, 1, '282776', 'ELDEN RING Shadow of the Erdtree', NULL, '2026-07-04 12:03:15');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `historial_busquedas`
--
ALTER TABLE `historial_busquedas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
