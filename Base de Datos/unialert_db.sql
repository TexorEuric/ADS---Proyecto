-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-12-2025 a las 01:47:30
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
-- Base de datos: `unialert_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `credenciales`
--

CREATE TABLE `credenciales` (
  `IdCredencial` int(11) NOT NULL,
  `IdUsuario` int(11) NOT NULL,
  `NickName` varchar(100) DEFAULT NULL,
  `Contrasena` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `credenciales`
--

INSERT INTO `credenciales` (`IdCredencial`, `IdUsuario`, `NickName`, `Contrasena`) VALUES
(10, 10, 'Admin 1', '$2y$10$DClWQMSzcU5wP6BR7SFu8uTu9fhaejXwIfe90yC0ir.OoIycKebGS'),
(11, 11, 'OswaldoGV', '$2y$10$hoAkZC9.NbzR3rnO3u.jTOhE.BtgJrh1zOIJg72VNCq74VikjBLF2'),
(12, 12, 'MarianaAR', '$2y$10$NKII7z70TY1wRKT9yt8lju.HUo4uTDZE9GVwW.ON9f8HMnlXJHyce'),
(13, 13, 'Vigilante 1', '$2y$10$jDo6miCxKryaLtS5l/uA/elvqueYGzMbwpScJMxcAaS9OjT0Ov0Vq'),
(14, 14, 'Vecino 1', '$2y$10$SNvgCAfyQ6o4T.WimXwWM.DnIU91jzE8mz11vuChRXExOueBn3H3S'),
(15, 15, 'Vecino 2', '$2y$10$QvQCYw7b5YiY9r7uRnFIQeZ5CdZHfLKrfm7hV7K2LW01MmHNp5ozq'),
(16, 16, 'Vigilante 2', '$2y$10$iyyM6/1wnGexZnR6Yt5e7ehrDontyM8/nduh9Q6qUi1pjC8NAqHM.'),
(17, 17, 'Vigilante 3', '$2y$10$5l76bSu9HBuuO6tF1MCJWewBgHJP6x3G.YKpp9XF396jV4V25qv8W'),
(18, 18, 'Vigilante 4', '$2y$10$4nmwXiXI2/9sVoHadHteCucVvqOlpWjAWRHP.zSdE80g/Y1rJPlqC'),
(19, 19, 'Vecino 3', '$2y$10$2O7lQX2d42YIG0Izn3CwgeMxVKB5uRKL7Uvn9AEWYpVSzyDc9nCKa'),
(20, 20, 'Vecino 4', '$2y$10$wSSvCyzAidNWBFUzwusGmO0VsYV0HOg6mbeMdD7OUco8xkSGNaxNy'),
(21, 21, 'Vecino 5', '$2y$10$Bi038sSShFLPPwwIPuk9yeh97Erp2BIL8cC0nn1CBOrVpjDQofvJa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidentes`
--

CREATE TABLE `incidentes` (
  `IdIncidente` int(11) NOT NULL,
  `IdUsuario` int(11) NOT NULL,
  `IdVigilante` int(11) DEFAULT NULL,
  `EdificioAfectado` varchar(50) DEFAULT NULL,
  `DepartamentoAfectado` varchar(50) DEFAULT NULL,
  `Sangre` tinyint(1) DEFAULT 0,
  `TipoEmergencia` varchar(50) DEFAULT NULL,
  `PublicoOPrivado` varchar(20) DEFAULT NULL,
  `Armas` tinyint(1) DEFAULT 0,
  `Descripcion` text DEFAULT NULL,
  `Fecha` date DEFAULT curdate(),
  `HoraInicio` time DEFAULT curtime(),
  `HoraFin` time DEFAULT NULL,
  `AccionesGuardia` text DEFAULT NULL,
  `Aprobado` tinyint(4) DEFAULT 0,
  `Finalizado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `incidentes`
--

INSERT INTO `incidentes` (`IdIncidente`, `IdUsuario`, `IdVigilante`, `EdificioAfectado`, `DepartamentoAfectado`, `Sangre`, `TipoEmergencia`, `PublicoOPrivado`, `Armas`, `Descripcion`, `Fecha`, `HoraInicio`, `HoraFin`, `AccionesGuardia`, `Aprobado`, `Finalizado`) VALUES
(9, 11, NULL, 'A2', '23', 1, 'medica', 'cualquiera', 0, 'Detalle: Se cayó del 7mo piso. \n[MÉDICA] Paciente: Vecino 5. Edad: adulto. Sexo: masculino. Consciente: no. Respirando: no. Sangrado en: Todo el Cuerpo.', '2025-12-05', '17:28:20', '17:32:12', 'Todo Salió Bien', 0, 1),
(10, 12, NULL, 'A2', '23', 0, 'seguridad', 'publico', 1, 'Detalle: Robo. \n[SEGURIDAD] Sucede ahora: si. Heridos: no. Personas: 3-5. Sospechosos: Personas Morenas con ropa desgastada. Fuga: no_huyeron. Vehículo: . Anónimo: si.', '2025-12-05', '17:29:18', '17:34:15', NULL, 2, 0),
(11, 14, NULL, 'B1', '18', 0, 'medica', 'publico', 0, 'Detalle: Infarto. \n[MÉDICA] Paciente: Vecino 2. Edad: mayor. Sexo: femenino. Consciente: no. Respirando: si_dificultad.', '2025-12-05', '17:30:02', '17:34:54', NULL, 1, 0),
(12, 19, NULL, 'B1', '20', 0, 'seguridad', 'publico', 0, 'Detalle: Explosión. \n[SEGURIDAD] Sucede ahora: no. Heridos: si. Personas: mas-de-5. Sospechosos: Vecinos de la unidad. Fuga: no_huyeron. Vehículo: . Anónimo: no.', '2025-12-05', '17:30:49', '17:34:31', NULL, 1, 0),
(13, 20, NULL, 'B2', '8', 0, 'seguridad', 'publico', 0, 'Detalle: Se cayó un arbol. \n[SEGURIDAD] Sucede ahora: si. Heridos: no. Personas: ?. Sospechosos: . Fuga: . Vehículo: . Anónimo: no.', '2025-12-05', '17:31:28', NULL, NULL, 0, 0),
(14, 11, 13, 'A2', '23', 1, 'medica', 'publico', 0, 'Detalle: Le salió sangre de la nariz. \n[MÉDICA] Paciente: Vecino 5. Edad: nino. Sexo: masculino. Consciente: si. Respirando: si. Sangrado en: Nariz.', '2025-12-08', '18:44:28', '01:45:00', 'Se llamó al 911', 0, 1),
(15, 11, NULL, 'A2', '23', 0, 'seguridad', 'publico', 1, 'Detalle: Asaltaron a una viejita en la calle. \n[SEGURIDAD] Sucede ahora: si. Heridos: no. Personas: 1. Sospechosos: Un tipo moreno, parece vagabundo. Fuga: pie. Vehículo: . Anónimo: si.', '2025-12-08', '18:45:17', NULL, NULL, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `IdRol` int(11) NOT NULL,
  `NombreRol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`IdRol`, `NombreRol`) VALUES
(1, 'Admin'),
(2, 'Vigilante'),
(3, 'Vecino');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turnos`
--

CREATE TABLE `turnos` (
  `IdTurno` int(11) NOT NULL,
  `IdUsuario` int(11) NOT NULL,
  `HoraInicio` time NOT NULL,
  `HoraFin` time NOT NULL,
  `DiasHabiles` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `IdUsuario` int(11) NOT NULL,
  `IdRol` int(11) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `ApPaterno` varchar(100) NOT NULL,
  `ApMaterno` varchar(100) DEFAULT NULL,
  `Correo` varchar(100) DEFAULT NULL,
  `Edificio` varchar(50) DEFAULT NULL,
  `Departamento` varchar(50) DEFAULT NULL,
  `Contador` int(11) DEFAULT 0,
  `Activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`IdUsuario`, `IdRol`, `Nombre`, `ApPaterno`, `ApMaterno`, `Correo`, `Edificio`, `Departamento`, `Contador`, `Activo`) VALUES
(10, 1, 'Admin', '1', '.0', 'admin@unialert.com', 'A1', '1', 0, 1),
(11, 3, 'Oswaldo', 'Gil', 'Valentín', 'osw@unialert.com', 'A2', '23', 0, 1),
(12, 3, 'Mariana', 'Aguilar', 'Ramos', 'mar@unialert.com', 'A2', '23', 0, 1),
(13, 2, 'Vigilante', '1', '.0', 'vig1@unialert.com', NULL, NULL, 0, 1),
(14, 3, 'Vecino', '1', '.0', 'vec1@unialert.com', 'B1', '18', 0, 1),
(15, 3, 'Vecino', '2', '.0', 'vec2@unialert.com', 'B2', '11', 0, 1),
(16, 2, 'Vigilante', '2', '.0', 'vig2@unialert.com', NULL, NULL, 0, 1),
(17, 2, 'Vigilante', '3', '.0', 'vig3@unialert.com', NULL, NULL, 0, 1),
(18, 2, 'Vigilante', '4', '.0', 'vig4@unialert.com', NULL, NULL, 0, 1),
(19, 3, 'Vecino', '3', '.0', 'vec3@unialert.com', 'B1', '20', 0, 1),
(20, 3, 'Vecino', '4', '.0', 'vec4@unialert.com', 'B2', '8', 0, 1),
(21, 3, 'Vecino', '5', '.0', 'vec5@unialert.com', 'A1', '22', 0, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `credenciales`
--
ALTER TABLE `credenciales`
  ADD PRIMARY KEY (`IdCredencial`),
  ADD UNIQUE KEY `IdUsuario` (`IdUsuario`),
  ADD UNIQUE KEY `NickName` (`NickName`);

--
-- Indices de la tabla `incidentes`
--
ALTER TABLE `incidentes`
  ADD PRIMARY KEY (`IdIncidente`),
  ADD KEY `IdUsuario` (`IdUsuario`),
  ADD KEY `IdVigilante` (`IdVigilante`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`IdRol`);

--
-- Indices de la tabla `turnos`
--
ALTER TABLE `turnos`
  ADD PRIMARY KEY (`IdTurno`),
  ADD KEY `IdUsuario` (`IdUsuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`IdUsuario`),
  ADD KEY `IdRol` (`IdRol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `credenciales`
--
ALTER TABLE `credenciales`
  MODIFY `IdCredencial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `incidentes`
--
ALTER TABLE `incidentes`
  MODIFY `IdIncidente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `IdRol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `turnos`
--
ALTER TABLE `turnos`
  MODIFY `IdTurno` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `IdUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `credenciales`
--
ALTER TABLE `credenciales`
  ADD CONSTRAINT `credenciales_ibfk_1` FOREIGN KEY (`IdUsuario`) REFERENCES `usuarios` (`IdUsuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `incidentes`
--
ALTER TABLE `incidentes`
  ADD CONSTRAINT `incidentes_ibfk_1` FOREIGN KEY (`IdUsuario`) REFERENCES `usuarios` (`IdUsuario`),
  ADD CONSTRAINT `incidentes_ibfk_2` FOREIGN KEY (`IdVigilante`) REFERENCES `usuarios` (`IdUsuario`);

--
-- Filtros para la tabla `turnos`
--
ALTER TABLE `turnos`
  ADD CONSTRAINT `turnos_ibfk_1` FOREIGN KEY (`IdUsuario`) REFERENCES `usuarios` (`IdUsuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`IdRol`) REFERENCES `roles` (`IdRol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
