-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-12-2025 a las 05:55:08
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
(1, 1, 'OswaldoGV', '1234'),
(2, 2, 'Vec1', '1234'),
(3, 3, 'Admin1', '1234'),
(4, 4, 'Vig1', '1234'),
(6, 6, 'MarianaAR', '1234'),
(8, 8, 'Vec2', '1234');

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
  `Aprobado` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `incidentes`
--

INSERT INTO `incidentes` (`IdIncidente`, `IdUsuario`, `IdVigilante`, `EdificioAfectado`, `DepartamentoAfectado`, `Sangre`, `TipoEmergencia`, `PublicoOPrivado`, `Armas`, `Descripcion`, `Fecha`, `HoraInicio`, `HoraFin`, `AccionesGuardia`, `Aprobado`) VALUES
(1, 3, NULL, 'B1', '2', 0, 'medica', 'publico', 0, 'Descripción del usuario: fdsfs. \n[DETALLES MÉDICOS] Paciente: Vec2. Edad: mayor. Sexo: masculino. Consciente: no. Respirando: si_dificultad. ', '2025-12-03', '18:36:14', '21:15:29', NULL, 0),
(2, 3, NULL, 'B1', '2', 0, 'seguridad', 'publico', 1, 'Descripción del usuario: Asalto. \n[DETALLES SEGURIDAD] Sucede ahora: no. Heridos: no. Personas: 2. Desc. Sospechosos: Morenas. Fuga: vehiculo. Vehículo: Moto Italika Azul verde. Anónimo: si.', '2025-12-03', '18:38:22', '21:07:20', NULL, 0),
(3, 3, NULL, 'B1', '2', 0, 'medica', 'privado', 0, 'Descripción del usuario: Infarto. \n[DETALLES MÉDICOS] Paciente: Vec3. Edad: mayor. Sexo: femenino. Consciente: no. Respirando: si_dificultad. ', '2025-12-03', '19:40:46', '21:15:29', NULL, 1),
(4, 3, NULL, 'B1', '2', 0, 'seguridad', 'publico', 0, 'Descripción del usuario: Robo. \n[DETALLES SEGURIDAD] Sucede ahora: no. Heridos: no. Personas: 2. Desc. Sospechosos: Morenas. Fuga: vehiculo. Vehículo: Camioneta Chevrolet Negra. Anónimo: si.', '2025-12-03', '20:33:31', '21:07:20', NULL, 2),
(5, 3, NULL, 'B1', '2', 0, 'medica', 'Indefinido', 0, 'Descripción del usuario: . \n[DETALLES MÉDICOS] Paciente: . Edad: ?. Sexo: ?. Consciente: ?. Respirando: ?. ', '2025-12-03', '21:46:23', NULL, NULL, 0),
(6, 3, NULL, 'B1', '2', 0, 'seguridad', 'publico', 0, 'Detalle: Explosión. \n[SEGURIDAD] Sucede ahora: si. Heridos: si. Personas: mas-de-5. Sospechosos: Los Vecinos del piso mas alto. Fuga: no_huyeron. Vehículo: . Anónimo: no.', '2025-12-03', '21:51:57', NULL, NULL, 0),
(7, 6, NULL, 'A1', '4', 1, 'medica', 'publico', 0, 'Detalle: asdaxa. \n[MÉDICA] Paciente: Vec2. Edad: bebe. Sexo: masculino. Consciente: si. Respirando: si. Sangrado en: wda.', '2025-12-03', '22:14:39', '22:18:53', NULL, 0),
(8, 6, NULL, 'A1', '4', 0, 'seguridad', 'publico', 1, 'Detalle: dwa. \n[SEGURIDAD] Sucede ahora: no. Heridos: no. Personas: 2. Sospechosos: Morenas. Fuga: vehiculo. Vehículo: dawdasdwq. Anónimo: si.', '2025-12-03', '22:16:13', '22:19:50', NULL, 1);

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
(1, 3, 'Oswaldo', 'Gil', 'Valentín', 'osw@unialert.com', 'A1', '14', 0, 1),
(2, 3, 'Vecino1', 'A', 'A', 'vec1@unialert.com', 'A2', '1', 0, 1),
(3, 1, 'Admin1', 'A', 'A', 'admin@unialert.com', 'B1', '2', 0, 1),
(4, 2, 'Vigilante1', 'A', 'A', 'vig1@unialert.com', 'B2', '3', 0, 1),
(6, 3, 'Mariana', 'Aguilar', 'Ramos', 'mar@unialert.com', 'A1', '4', 0, 1),
(8, 3, 'Vecino2', 'A', 'A', 'vec2@unialert.com', 'A2', '5', 0, 1);

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
  MODIFY `IdCredencial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `incidentes`
--
ALTER TABLE `incidentes`
  MODIFY `IdIncidente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  MODIFY `IdUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
