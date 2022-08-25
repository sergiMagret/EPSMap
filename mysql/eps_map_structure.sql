-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-08-2022 a las 19:48:37
-- Versión del servidor: 10.4.14-MariaDB
-- Versión de PHP: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `eps_map`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `buildings`
--

CREATE TABLE `buildings` (
  `id` int(2) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `capture_points`
--

CREATE TABLE `capture_points` (
  `id` int(3) NOT NULL,
  `node_id` int(20) NOT NULL,
  `face_direction` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departments`
--

CREATE TABLE `departments` (
  `id` int(3) NOT NULL,
  `name` varchar(100) NOT NULL,
  `alias` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `destination_zones`
--

CREATE TABLE `destination_zones` (
  `id` int(6) NOT NULL,
  `name` varchar(50) NOT NULL,
  `main_node_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `doors`
--

CREATE TABLE `doors` (
  `id` int(8) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `edges`
--

CREATE TABLE `edges` (
  `id` int(20) NOT NULL,
  `from_node_id` int(10) NOT NULL,
  `to_node_id` int(10) NOT NULL,
  `bidirectional` tinyint(1) NOT NULL DEFAULT 1,
  `weight` int(10) NOT NULL,
  `direction_2d` varchar(2) NOT NULL,
  `direction_3d` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `edge_instructions`
--

CREATE TABLE `edge_instructions` (
  `from_edge_id` int(20) NOT NULL,
  `to_edge_id` int(20) NOT NULL,
  `instruction_id` int(5) NOT NULL,
  `image_name` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `edge_instruction_translation_view`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `edge_instruction_translation_view` (
`from_edge_id` int(20)
,`to_edge_id` int(20)
,`instruction_id` int(5)
,`instruction_name` varchar(50)
,`lang_id` int(2)
,`lang_short_name` varchar(2)
,`lang_name` varchar(50)
,`text` varchar(500)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instructions`
--

CREATE TABLE `instructions` (
  `id` int(5) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instructions_lang`
--

CREATE TABLE `instructions_lang` (
  `instruction_id` int(5) NOT NULL,
  `lang_id` int(2) NOT NULL,
  `text` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `languages`
--

CREATE TABLE `languages` (
  `id` int(2) NOT NULL,
  `short_name` varchar(2) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nodes`
--

CREATE TABLE `nodes` (
  `id` int(10) NOT NULL,
  `nodes_type_id` int(3) NOT NULL,
  `level` int(2) NOT NULL DEFAULT 0,
  `dest_zone_id` int(6) DEFAULT NULL,
  `latitude` decimal(7,5) NOT NULL,
  `longitude` decimal(8,5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `people`
--

CREATE TABLE `people` (
  `id` int(6) NOT NULL,
  `name` varchar(100) NOT NULL,
  `space_id` int(6) DEFAULT NULL,
  `department_id` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `spaces`
--

CREATE TABLE `spaces` (
  `id` int(6) NOT NULL,
  `name` varchar(100) NOT NULL,
  `alias` varchar(20) NOT NULL,
  `spaces_type_id` int(3) NOT NULL,
  `building_id` int(2) NOT NULL,
  `door_id` int(8) DEFAULT NULL,
  `node_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura para la vista `edge_instruction_translation_view`
--
DROP TABLE IF EXISTS `edge_instruction_translation_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `edge_instruction_translation_view`  AS  select `edge_ins`.`from_edge_id` AS `from_edge_id`,`edge_ins`.`to_edge_id` AS `to_edge_id`,`ins_lang`.`instruction_id` AS `instruction_id`,`ins`.`name` AS `instruction_name`,`ins_lang`.`lang_id` AS `lang_id`,`lang`.`short_name` AS `lang_short_name`,`lang`.`name` AS `lang_name`,`ins_lang`.`text` AS `text` from (((`edge_instructions` `edge_ins` join `instructions_lang` `ins_lang` on(`edge_ins`.`instruction_id` = `ins_lang`.`instruction_id`)) join `languages` `lang` on(`ins_lang`.`lang_id` = `lang`.`id`)) join `instructions` `ins` on(`ins_lang`.`instruction_id` = `ins`.`id`)) order by `edge_ins`.`from_edge_id`,`edge_ins`.`to_edge_id` ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `buildings`
--
ALTER TABLE `buildings`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `capture_points`
--
ALTER TABLE `capture_points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `caputre_point_node_id` (`node_id`);

--
-- Indices de la tabla `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `destination_zones`
--
ALTER TABLE `destination_zones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `destination_zones_main_node_key` (`main_node_id`);

--
-- Indices de la tabla `doors`
--
ALTER TABLE `doors`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `edges`
--
ALTER TABLE `edges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `edge_between_nodes_unique` (`from_node_id`,`to_node_id`) USING BTREE,
  ADD KEY `segments_to_node_key` (`to_node_id`);

--
-- Indices de la tabla `edge_instructions`
--
ALTER TABLE `edge_instructions`
  ADD PRIMARY KEY (`from_edge_id`,`to_edge_id`),
  ADD KEY `edge_instructions_to_edge_id_key` (`to_edge_id`),
  ADD KEY `edge_instructions_instruction_id_key` (`instruction_id`);

--
-- Indices de la tabla `instructions`
--
ALTER TABLE `instructions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_instruction_name` (`name`);

--
-- Indices de la tabla `instructions_lang`
--
ALTER TABLE `instructions_lang`
  ADD PRIMARY KEY (`instruction_id`,`lang_id`) USING BTREE,
  ADD KEY `instructions_lang_id_key` (`lang_id`);

--
-- Indices de la tabla `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `language_short_name_unique` (`short_name`);

--
-- Indices de la tabla `nodes`
--
ALTER TABLE `nodes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nodes_dest_zone_id_key` (`dest_zone_id`),
  ADD KEY `nodes_type_id_key` (`nodes_type_id`);

--
-- Indices de la tabla `people`
--
ALTER TABLE `people`
  ADD PRIMARY KEY (`id`),
  ADD KEY `people_space_id_key` (`space_id`),
  ADD KEY `people_department_id_key` (`department_id`);

--
-- Indices de la tabla `spaces`
--
ALTER TABLE `spaces`
  ADD PRIMARY KEY (`id`),
  ADD KEY `spaces_door_id_key` (`door_id`),
  ADD KEY `spaces_building_id_key` (`building_id`),
  ADD KEY `spaces_node_id_key` (`node_id`),
  ADD KEY `spaces_type_id_key` (`spaces_type_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `buildings`
--
ALTER TABLE `buildings`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `capture_points`
--
ALTER TABLE `capture_points`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `destination_zones`
--
ALTER TABLE `destination_zones`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `doors`
--
ALTER TABLE `doors`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `edges`
--
ALTER TABLE `edges`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `instructions`
--
ALTER TABLE `instructions`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `nodes`
--
ALTER TABLE `nodes`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `people`
--
ALTER TABLE `people`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `spaces`
--
ALTER TABLE `spaces`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `capture_points`
--
ALTER TABLE `capture_points`
  ADD CONSTRAINT `caputre_point_node_id` FOREIGN KEY (`node_id`) REFERENCES `nodes` (`id`);

--
-- Filtros para la tabla `destination_zones`
--
ALTER TABLE `destination_zones`
  ADD CONSTRAINT `destination_zones_main_node_key` FOREIGN KEY (`main_node_id`) REFERENCES `nodes` (`id`);

--
-- Filtros para la tabla `edges`
--
ALTER TABLE `edges`
  ADD CONSTRAINT `segments_from_node_key` FOREIGN KEY (`from_node_id`) REFERENCES `nodes` (`id`),
  ADD CONSTRAINT `segments_to_node_key` FOREIGN KEY (`to_node_id`) REFERENCES `nodes` (`id`);

--
-- Filtros para la tabla `edge_instructions`
--
ALTER TABLE `edge_instructions`
  ADD CONSTRAINT `edge_instructions_from_edge_id_key` FOREIGN KEY (`from_edge_id`) REFERENCES `edges` (`id`),
  ADD CONSTRAINT `edge_instructions_instruction_id_key` FOREIGN KEY (`instruction_id`) REFERENCES `instructions` (`id`),
  ADD CONSTRAINT `edge_instructions_to_edge_id_key` FOREIGN KEY (`to_edge_id`) REFERENCES `edges` (`id`);

--
-- Filtros para la tabla `instructions_lang`
--
ALTER TABLE `instructions_lang`
  ADD CONSTRAINT `instructions_id_key` FOREIGN KEY (`instruction_id`) REFERENCES `instructions` (`id`),
  ADD CONSTRAINT `instructions_lang_id_key` FOREIGN KEY (`lang_id`) REFERENCES `languages` (`id`);

--
-- Filtros para la tabla `nodes`
--
ALTER TABLE `nodes`
  ADD CONSTRAINT `nodes_dest_zone_id_key` FOREIGN KEY (`dest_zone_id`) REFERENCES `destination_zones` (`id`);

--
-- Filtros para la tabla `people`
--
ALTER TABLE `people`
  ADD CONSTRAINT `people_department_id_key` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `people_space_id_key` FOREIGN KEY (`space_id`) REFERENCES `spaces` (`id`);

--
-- Filtros para la tabla `spaces`
--
ALTER TABLE `spaces`
  ADD CONSTRAINT `spaces_building_id_key` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`),
  ADD CONSTRAINT `spaces_door_id_key` FOREIGN KEY (`door_id`) REFERENCES `doors` (`id`),
  ADD CONSTRAINT `spaces_node_id_key` FOREIGN KEY (`node_id`) REFERENCES `nodes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
