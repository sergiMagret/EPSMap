-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-08-2022 a las 19:47:12
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

--
-- Volcado de datos para la tabla `buildings`
--

INSERT INTO `buildings` (`id`, `name`) VALUES
(1, 'P1'),
(2, 'P2'),
(3, 'P3'),
(4, 'P4');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `capture_points`
--

CREATE TABLE `capture_points` (
  `id` int(3) NOT NULL,
  `node_id` int(20) NOT NULL,
  `face_direction` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `capture_points`
--

INSERT INTO `capture_points` (`id`, `node_id`, `face_direction`) VALUES
(1, 1, 'SE'),
(2, 30, 'NE'),
(3, 20, 'SE'),
(4, 7, 'SO'),
(5, 40, 'NE'),
(6, 73, 'NE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departments`
--

CREATE TABLE `departments` (
  `id` int(3) NOT NULL,
  `name` varchar(100) NOT NULL,
  `alias` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `departments`
--

INSERT INTO `departments` (`id`, `name`, `alias`) VALUES
(1, 'Arquitectura i Enginyeria de La Construcció', 'AEC'),
(2, 'Arquitectura i Tecnologia de Computadors', 'ATC'),
(3, 'Enginyeria Elèctrica, Electrònica i Automàtica', 'EEEA'),
(4, 'Enginyeria Mecànica i De la Construcció Industrial', 'EMCI'),
(5, 'Enginyeria Química, Agrària i Tecnologia Agroalimentària', 'EQATA'),
(6, 'Física', NULL),
(7, 'Informàtica, Matemàtica Aplicada i Estadística', 'IMAE'),
(8, 'Organització, Gestió Empresarial i Disseny del Producte', 'OGEDP');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `destination_zones`
--

CREATE TABLE `destination_zones` (
  `id` int(6) NOT NULL,
  `name` varchar(50) NOT NULL,
  `main_node_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `destination_zones`
--

INSERT INTO `destination_zones` (`id`, `name`, `main_node_id`) VALUES
(1, 'A', 2),
(2, 'B', 7),
(3, 'C', 6),
(4, 'D', 17),
(5, 'E', 20),
(6, 'F', 24),
(7, 'G', 36),
(8, 'H', 35),
(9, 'I', 34),
(10, 'J', 38),
(11, 'K', 39),
(12, 'L', 40),
(13, 'M', 41),
(14, 'N', 43),
(15, 'O', 71),
(16, 'P', 72),
(17, 'Q', 74),
(18, 'R', 75);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `doors`
--

CREATE TABLE `doors` (
  `id` int(8) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `doors`
--

INSERT INTO `doors` (`id`, `name`) VALUES
(1, 'P2-001'),
(2, 'P2-002'),
(3, 'P2-003'),
(4, 'P2-004'),
(5, 'P2-005'),
(6, 'P2-006'),
(7, 'P2-007'),
(8, 'P2-008'),
(9, 'P2-009'),
(10, 'P2-010'),
(11, 'P2-011'),
(12, 'P2-012'),
(13, 'P2-013'),
(14, 'P2-014'),
(15, 'P2-015'),
(16, 'P2-016'),
(17, 'P2-017'),
(18, 'P2-018'),
(19, 'P2-019'),
(20, 'P2-020'),
(21, 'P2-021'),
(22, 'P2-022'),
(23, 'P2-023'),
(24, 'P2-024'),
(25, 'P2-025'),
(26, 'P2-026'),
(27, 'P2-027'),
(28, 'P2-028'),
(29, 'P2-029'),
(30, 'P2-030'),
(31, 'P2-031'),
(32, 'P2-032'),
(33, 'P2-033'),
(34, 'P2-034'),
(35, 'P2-035'),
(36, 'P2-036'),
(37, 'P2-037'),
(38, 'P2-038'),
(39, 'P2-039'),
(40, 'P2-040'),
(41, 'P2-041'),
(42, 'P2-042'),
(43, 'P2-043'),
(44, 'P2-044'),
(45, 'P2-045'),
(46, 'P2-046'),
(47, 'P2-047'),
(48, 'P2-048'),
(49, 'P2-049'),
(50, 'P2-050'),
(51, 'P2-051'),
(52, 'P2-052'),
(53, 'P2-053'),
(54, 'P2-054'),
(55, 'P2-055'),
(56, 'P2-056'),
(57, 'P2-057'),
(58, 'P2-058'),
(59, 'P2-059'),
(60, 'P2-060'),
(61, 'P2-061'),
(62, 'P2-062'),
(63, 'P2-063'),
(64, 'P2-064'),
(65, 'P2-065'),
(66, 'P2-066'),
(67, 'P2-067'),
(68, 'P2-068'),
(69, 'P2-069'),
(70, 'P2-070'),
(71, 'P2-071'),
(72, 'P2-072'),
(73, 'P2-073'),
(74, 'P2-074'),
(75, 'P2-075'),
(76, 'P2-076'),
(77, 'P2-077'),
(78, 'P2-078'),
(79, 'P2-079'),
(80, 'P2-080'),
(81, 'P2-081'),
(82, 'P2-082'),
(83, 'P2-083'),
(84, 'P2-084'),
(85, 'P2-085'),
(86, 'P2-086'),
(87, 'P2-087'),
(88, 'P2-088'),
(89, 'P2-089'),
(90, 'P2-090'),
(91, 'P2-091'),
(92, 'P2-092'),
(93, 'P2-093'),
(94, 'P2-094'),
(95, 'P2-095'),
(96, 'P2-096'),
(97, 'P2-097'),
(98, 'P2-098'),
(99, 'P2-099'),
(100, 'P2-100'),
(101, 'P2-101'),
(102, 'P2-102'),
(103, 'P2-103'),
(104, 'P2-104'),
(105, 'P2-105'),
(106, 'P2-106'),
(107, 'P2-107'),
(108, 'P2-108'),
(109, 'P2-109'),
(110, 'P2-110'),
(111, 'P2-111'),
(112, 'P2-112'),
(113, 'P2-113'),
(114, 'P2-114'),
(115, 'P2-115'),
(116, 'P2-116'),
(117, 'P2-117'),
(118, 'P2-118'),
(119, 'P2-119'),
(120, 'P2-120'),
(121, 'P2-121'),
(122, 'P2-122'),
(123, 'P2-123'),
(124, 'P2-124'),
(125, 'P2-125'),
(126, 'P2-126'),
(127, 'P2-127'),
(128, 'P2-128'),
(129, 'P2-129'),
(130, 'P2-130'),
(131, 'P2-131'),
(132, 'P2-132'),
(133, 'P2-133'),
(134, 'P2-134'),
(135, 'P2-135'),
(136, 'P2-136'),
(137, 'P2-137'),
(138, 'P2-138'),
(139, 'P2-139'),
(140, 'P2-140'),
(141, 'P2-141'),
(142, 'P2-142'),
(143, 'P2-143'),
(144, 'P2-144'),
(145, 'P2-145'),
(146, 'P2-146'),
(147, 'P2-147'),
(148, 'P2-148'),
(149, 'P2-149'),
(150, 'P2-150'),
(151, 'P2-151'),
(152, 'P2-152'),
(153, 'P2-153'),
(154, 'P2-154'),
(155, 'P2-155'),
(156, 'P2-156'),
(157, 'P2-157'),
(158, 'P2-158'),
(159, 'P2-159'),
(160, 'P2-160'),
(161, 'P2-161'),
(162, 'P2-162'),
(163, 'P2-163'),
(164, 'P2-164'),
(165, 'P2-165'),
(166, 'P2-166'),
(167, 'P2-167'),
(168, 'P2-168'),
(169, 'P2-169'),
(170, 'P2-170'),
(171, 'P2-171'),
(172, 'P2-172'),
(173, 'P2-173'),
(174, 'P2-174'),
(175, 'P2-175'),
(176, 'P2-176'),
(177, 'P2-177'),
(178, 'P2-178'),
(179, 'P2-179'),
(180, 'P2-180'),
(181, 'P2-181'),
(182, 'P2-182'),
(183, 'P2-183'),
(184, 'P2-184'),
(185, 'P2-185'),
(186, 'P2-186'),
(187, 'P2-187'),
(188, 'P2-188'),
(189, 'P2-189'),
(190, 'P2-190'),
(191, 'P2-191'),
(192, 'P2-192'),
(193, 'P2-193'),
(194, 'P2-194'),
(195, 'P2-195'),
(196, 'P2-196'),
(197, 'P2-197'),
(198, 'P2-198'),
(199, 'P2-199'),
(200, 'P2-200'),
(201, 'P2-201'),
(202, 'P2-202'),
(203, 'P2-203'),
(204, 'P2-204'),
(205, 'P2-205'),
(206, 'P2-206'),
(207, 'P2-207'),
(208, 'P2-208'),
(209, 'P2-209'),
(210, 'P2-210'),
(211, 'P2-211'),
(212, 'P2-212'),
(213, 'P2-213'),
(214, 'P2-214'),
(215, 'P2-215'),
(216, 'P2-216'),
(217, 'P2-217'),
(218, 'P2-218'),
(219, 'P2-219');

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

--
-- Volcado de datos para la tabla `edges`
--

INSERT INTO `edges` (`id`, `from_node_id`, `to_node_id`, `bidirectional`, `weight`, `direction_2d`, `direction_3d`) VALUES
(1, 1, 2, 1, 10, 'SE', 'P'),
(2, 2, 4, 1, 10, 'S', 'P'),
(3, 4, 6, 1, 8, 'O', 'P'),
(5, 6, 7, 1, 22, 'NO', 'P'),
(6, 6, 17, 1, 14, 'SE', 'P'),
(7, 17, 16, 1, 11, 'SE', 'P'),
(8, 16, 20, 1, 11, 'SE', 'P'),
(9, 20, 24, 1, 11, 'S', 'P'),
(10, 20, 29, 1, 20, 'NE', 'P'),
(11, 17, 30, 1, 20, 'NE', 'P'),
(12, 29, 30, 1, 22, 'NO', 'P'),
(13, 30, 32, 1, 22, 'NO', 'P'),
(14, 32, 34, 1, 12, 'NE', 'P'),
(15, 30, 35, 1, 12, 'NE', 'P'),
(16, 29, 36, 1, 12, 'NE', 'P'),
(17, 4, 17, 1, 8, 'S', 'P'),
(18, 44, 37, 1, 10, '', 'U'),
(19, 37, 38, 1, 9, 'N', 'P'),
(20, 38, 39, 1, 18, 'SE', 'P'),
(21, 39, 40, 1, 13, 'SE', 'P'),
(22, 40, 41, 1, 14, 'SE', 'P'),
(23, 41, 42, 1, 9, 'S', 'P'),
(24, 41, 43, 1, 14, 'SE', 'P'),
(25, 42, 43, 1, 5, 'E', 'P'),
(26, 37, 39, 1, 9, 'E', 'P'),
(27, 21, 42, 1, 10, '', 'U'),
(28, 7, 44, 1, 11, 'S', 'P'),
(29, 6, 44, 1, 11, 'O', 'P'),
(30, 37, 69, 1, 10, '', 'U'),
(31, 42, 70, 1, 10, '', 'U'),
(32, 69, 71, 1, 11, 'NO', 'P'),
(33, 71, 72, 1, 20, 'SE', 'P'),
(34, 72, 73, 1, 11, 'SE', 'P'),
(35, 73, 74, 1, 12, 'SE', 'P'),
(36, 74, 75, 1, 22, 'SE', 'P'),
(37, 70, 74, 1, 11, 'NO', 'P'),
(38, 70, 75, 1, 11, 'SE', 'P'),
(39, 69, 72, 1, 10, 'SE', 'P'),
(40, 20, 21, 1, 3, 'SO', 'P'),
(41, 21, 24, 1, 8, 'SE', 'P');

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

--
-- Volcado de datos para la tabla `edge_instructions`
--

INSERT INTO `edge_instructions` (`from_edge_id`, `to_edge_id`, `instruction_id`, `image_name`) VALUES
(1, 2, 1, NULL),
(2, 1, 1, NULL),
(2, 3, 14, NULL),
(2, 17, 13, NULL),
(3, 2, 2, NULL),
(3, 5, 1, NULL),
(3, 29, 2, 'd86e4a23986b55b61744986df98ec267.jpg'),
(5, 6, 1, NULL),
(6, 5, 6, NULL),
(6, 7, 1, NULL),
(6, 11, 2, NULL),
(6, 29, 2, NULL),
(7, 6, 1, NULL),
(7, 8, 1, NULL),
(7, 11, 3, NULL),
(7, 17, 1, NULL),
(8, 7, 1, NULL),
(8, 9, 7, '0b8b62f37003e530522395d5dc4c77ed.jpg'),
(8, 40, 3, NULL),
(9, 8, 1, NULL),
(9, 10, 3, NULL),
(10, 9, 2, NULL),
(10, 12, 2, NULL),
(10, 16, 1, NULL),
(10, 40, 1, NULL),
(11, 6, 3, NULL),
(11, 12, 3, NULL),
(11, 13, 2, NULL),
(11, 15, 1, NULL),
(12, 10, 3, NULL),
(12, 11, 2, NULL),
(12, 13, 1, NULL),
(12, 15, 3, NULL),
(12, 16, 2, NULL),
(13, 11, 3, NULL),
(13, 12, 1, NULL),
(13, 14, 3, NULL),
(14, 13, 2, NULL),
(15, 11, 1, NULL),
(15, 12, 2, NULL),
(16, 9, 2, NULL),
(16, 10, 1, NULL),
(16, 12, 3, NULL),
(17, 2, 5, NULL),
(17, 7, 1, NULL),
(17, 11, 15, 'ae64de8951d75c8778bf763704e050b2.jpg'),
(18, 19, 2, NULL),
(18, 26, 3, NULL),
(18, 28, 2, NULL),
(18, 29, 3, '4d54036a6d82bf968d84a3102040cbab.jpg'),
(18, 30, 8, NULL),
(19, 18, 9, NULL),
(21, 22, 1, NULL),
(21, 26, 1, NULL),
(22, 21, 1, NULL),
(22, 23, 1, NULL),
(22, 24, 1, NULL),
(23, 22, 1, NULL),
(23, 27, 9, NULL),
(23, 31, 8, NULL),
(25, 27, 9, NULL),
(26, 18, 9, NULL),
(26, 21, 1, NULL),
(26, 30, 8, NULL),
(27, 23, 2, NULL),
(27, 25, 3, NULL),
(27, 31, 8, NULL),
(27, 40, 1, NULL),
(27, 41, 3, NULL),
(29, 3, 4, NULL),
(29, 6, 12, 'ae64de8951d75c8778bf763704e050b2.jpg'),
(29, 18, 8, NULL),
(30, 19, 2, NULL),
(30, 26, 3, NULL),
(30, 32, 2, NULL),
(30, 39, 3, NULL),
(31, 23, 2, NULL),
(31, 25, 3, NULL),
(31, 37, 2, NULL),
(31, 38, 3, NULL),
(32, 30, 9, NULL),
(33, 34, 1, NULL),
(34, 33, 1, NULL),
(34, 35, 1, NULL),
(34, 39, 1, NULL),
(35, 34, 1, NULL),
(35, 36, 1, NULL),
(35, 37, 1, NULL),
(36, 35, 1, NULL),
(37, 31, 9, NULL),
(38, 31, 9, NULL),
(39, 30, 9, NULL),
(39, 34, 1, NULL),
(40, 8, 2, NULL),
(40, 10, 1, NULL),
(40, 27, 8, NULL);

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

--
-- Volcado de datos para la tabla `instructions`
--

INSERT INTO `instructions` (`id`, `name`) VALUES
(15, 'from_17_to_11'),
(14, 'from_2_to_3'),
(9, 'go_1_floor_downstairs'),
(8, 'go_1_floor_upstairs'),
(1, 'go_forward'),
(6, 'go_forward_hallway_left'),
(7, 'go_forward_hallway_right'),
(12, 'go_forward_until_reception'),
(13, 'go_through_hall_reception_on_left'),
(2, 'turn_left'),
(3, 'turn_right'),
(4, 'turn_slight_left'),
(5, 'turn_slight_right');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instructions_lang`
--

CREATE TABLE `instructions_lang` (
  `instruction_id` int(5) NOT NULL,
  `lang_id` int(2) NOT NULL,
  `text` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `instructions_lang`
--

INSERT INTO `instructions_lang` (`instruction_id`, `lang_id`, `text`) VALUES
(1, 1, 'Segueix recte'),
(1, 2, 'Sigue recto'),
(1, 3, 'Keep forward'),
(2, 1, 'Gira a l\'esquerra'),
(2, 2, 'Gira a la izquierda'),
(2, 3, 'Turn left'),
(3, 1, 'Gira a la dreta'),
(3, 2, 'Gira a la derecha'),
(3, 3, 'Turn right'),
(4, 1, 'Gira lleugerament a l\'esquerra'),
(4, 2, 'Gira ligeramente a la izquierda'),
(4, 3, 'Turn slightly left'),
(5, 1, 'Gira lleugerament a la dreta'),
(5, 2, 'Gira ligeramente a la derecha'),
(5, 3, 'Turn slightly right'),
(6, 1, 'Segueix recte i agafa el passadís de l\'esquerra'),
(6, 2, 'Sigue recto y coge el pasillo de la izquierda'),
(6, 3, 'Go forward and take the left hallway'),
(7, 1, 'Segueix recte i agafa el passadís de la dreta'),
(7, 2, 'Sigue recto y coge el pasillo de la derecha'),
(7, 3, 'Go forward and take the right hallway'),
(8, 1, 'Puja 1 pis'),
(8, 2, 'Sube 1 piso'),
(8, 3, 'Go up 1 floor'),
(9, 1, 'Baixa 1 pis'),
(9, 2, 'Baja 1 piso'),
(9, 3, 'Go down 1 floor'),
(12, 1, 'Avança fins la recepció'),
(12, 2, 'Avanza hasta la recepción'),
(12, 3, 'Go forward until the reception'),
(13, 1, 'Segueix avançant recte passant pel costat dret de consergeria'),
(13, 2, 'Sigue recto pasando por el lado derecho de consergeria'),
(13, 3, 'Keep going straight past the right side of reception'),
(14, 1, 'Gira a la dreta abans d\'arribar a consergeria i dirigeix-te cap a les màquines expenedores'),
(14, 2, 'Gira a la derecha antes de consergeria y dirígete hacia las máquinas expendedoras'),
(14, 3, 'Turn right before getting to the reception and head towards the vending machines'),
(15, 1, 'Gira a l\'esquerra just després de consergeria'),
(15, 2, 'Gira a la izquierda justo después de consergeria'),
(15, 3, 'Turn left right after reception');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `languages`
--

CREATE TABLE `languages` (
  `id` int(2) NOT NULL,
  `short_name` varchar(2) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `languages`
--

INSERT INTO `languages` (`id`, `short_name`, `name`) VALUES
(1, 'ca', 'Català'),
(2, 'es', 'Español'),
(3, 'en', 'English');

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

--
-- Volcado de datos para la tabla `nodes`
--

INSERT INTO `nodes` (`id`, `nodes_type_id`, `level`, `dest_zone_id`, `latitude`, `longitude`) VALUES
(1, 4, 0, NULL, '41.96370', '2.83086'),
(2, 3, 0, 1, '41.96365', '2.83097'),
(3, 2, 0, 1, '41.96363', '2.83085'),
(4, 3, 0, NULL, '41.96354', '2.83103'),
(5, 2, 0, 1, '41.96358', '2.83106'),
(6, 3, 0, 3, '41.96355', '2.83092'),
(7, 3, 0, 2, '41.96360', '2.83069'),
(8, 2, 0, 2, '41.96359', '2.83061'),
(9, 2, 0, 2, '41.96354', '2.83068'),
(10, 2, 0, 3, '41.96348', '2.83083'),
(11, 2, 0, 3, '41.96345', '2.83092'),
(12, 2, 0, 3, '41.96355', '2.83084'),
(13, 2, 0, 3, '41.96349', '2.83101'),
(14, 2, 0, 4, '41.96345', '2.83108'),
(15, 2, 0, 5, '41.96339', '2.83124'),
(16, 3, 0, NULL, '41.96342', '2.83116'),
(17, 3, 0, 4, '41.96350', '2.83108'),
(18, 2, 0, 4, '41.96338', '2.83107'),
(19, 2, 0, 5, '41.96335', '2.83117'),
(20, 3, 0, 5, '41.96340', '2.83131'),
(21, 1, 0, NULL, '41.96336', '2.83127'),
(22, 2, 0, 6, '41.96328', '2.83132'),
(23, 2, 0, 6, '41.96325', '2.83141'),
(24, 3, 0, 6, '41.96332', '2.83140'),
(25, 2, 0, 5, '41.96340', '2.83143'),
(26, 2, 0, 7, '41.96350', '2.83151'),
(27, 2, 0, 4, '41.96349', '2.83127'),
(28, 2, 0, 4, '41.96358', '2.83124'),
(29, 3, 0, NULL, '41.96358', '2.83143'),
(30, 3, 0, NULL, '41.96367', '2.83120'),
(31, 2, 0, 4, '41.96366', '2.83110'),
(32, 3, 0, NULL, '41.96377', '2.83096'),
(33, 2, 0, 1, '41.96374', '2.83093'),
(34, 3, 0, 9, '41.96387', '2.83103'),
(35, 3, 0, 8, '41.96377', '2.83126'),
(36, 3, 0, 7, '41.96367', '2.83150'),
(37, 1, 1, NULL, '41.96355', '2.83080'),
(38, 3, 1, 10, '41.96361', '2.83072'),
(39, 3, 1, 11, '41.96354', '2.83091'),
(40, 3, 1, 12, '41.96348', '2.83105'),
(41, 3, 1, 13, '41.96342', '2.83120'),
(42, 1, 1, NULL, '41.96336', '2.83127'),
(43, 3, 1, 14, '41.96335', '2.83133'),
(44, 1, 0, NULL, '41.96355', '2.83080'),
(45, 2, 0, 9, '41.96391', '2.83114'),
(46, 2, 0, 9, '41.96386', '2.83109'),
(47, 2, 0, 9, '41.96380', '2.83106'),
(48, 2, 0, 8, '41.96376', '2.83116'),
(49, 2, 0, 8, '41.96383', '2.83122'),
(50, 2, 0, 8, '41.96380', '2.83138'),
(51, 2, 0, 8, '41.96376', '2.83135'),
(52, 2, 0, 8, '41.96372', '2.83129'),
(53, 2, 0, 7, '41.96365', '2.83138'),
(54, 2, 0, 7, '41.96370', '2.83143'),
(55, 2, 0, 7, '41.96375', '2.83150'),
(56, 2, 0, 7, '41.96370', '2.83161'),
(57, 2, 0, 7, '41.96366', '2.83158'),
(58, 2, 0, 7, '41.96360', '2.83153'),
(59, 2, 1, 13, '41.96334', '2.83123'),
(60, 2, 1, 13, '41.96336', '2.83119'),
(61, 2, 1, 13, '41.96337', '2.83116'),
(62, 2, 1, 13, '41.96339', '2.83111'),
(63, 2, 1, 12, '41.96342', '2.83104'),
(64, 2, 1, 12, '41.96343', '2.83100'),
(65, 2, 1, 11, '41.96345', '2.83097'),
(66, 2, 1, 11, '41.96374', '2.83092'),
(67, 2, 1, 11, '41.96349', '2.83088'),
(68, 2, 1, 11, '41.96352', '2.83082'),
(69, 1, 2, NULL, '41.96355', '2.83079'),
(70, 1, 2, NULL, '41.96336', '2.83127'),
(71, 3, 2, 15, '41.96361', '2.83071'),
(72, 3, 2, 16, '41.96353', '2.83093'),
(73, 3, 2, NULL, '41.96348', '2.83105'),
(74, 3, 2, 17, '41.96342', '2.83118'),
(75, 3, 2, 18, '41.96334', '2.83140'),
(76, 2, 2, 17, '41.96338', '2.83112'),
(77, 2, 2, 17, '41.96340', '2.83118'),
(78, 2, 2, 17, '41.96342', '2.83114'),
(79, 2, 2, 17, '41.96344', '2.83109'),
(80, 2, 2, 16, '41.96348', '2.83098'),
(81, 2, 2, 16, '41.96350', '2.83094'),
(82, 2, 2, 16, '41.96351', '2.83089'),
(83, 2, 2, 16, '41.96353', '2.83084');

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

--
-- Volcado de datos para la tabla `people`
--

INSERT INTO `people` (`id`, `name`, `space_id`, `department_id`) VALUES
(1, 'Elena Roget', 26, 6),
(2, 'Josep Calbó', 27, 6),
(3, 'Lluïsa Escoda Acero', 28, 6),
(4, 'Jordi Farjas Silvia', 28, 6),
(5, 'Josep Costa BalazantPere Roura Grabulosa\r\n', 29, 6),
(6, 'Pere Roura Grabulosa', 29, 6),
(7, 'Miquel Llorens Solivera', 33, 4),
(8, 'Antoni Blánquez Boya', 32, 4),
(9, 'Jordi Renart Canalias', 32, 4),
(10, 'Albert Turon Travesa', 32, 4),
(11, 'Jordi Comas Baron', 31, 4),
(12, 'David Grabalosa Martin', 31, 4),
(13, 'Manuel Martin Vertedor', 31, 4),
(14, 'Teo Pulido Sureda', 31, 4),
(15, 'Joaquim Reda Llambrich', 31, 4),
(16, 'Enric Simon Madrenas', 31, 4),
(17, 'Joaquim Velayos Solé', 30, 4),
(18, 'Josep Ramon González Castro', 30, 4),
(19, 'Martí Comamala Laguna', 30, 4),
(20, 'Critina Barris Peña', 34, 4),
(21, 'Lluís Torres Llinàs', 35, 4),
(22, 'Xavier Cachís Carola', 36, 4),
(23, 'Laura Carreras Blasco', 37, 4),
(24, 'Alexandre Deltell Carbonell', 38, 4),
(25, 'Narcís Gascons Clarió', 40, 4),
(26, 'Dani Trias Mansilla', 41, 4);

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

--
-- Volcado de datos para la tabla `spaces`
--

INSERT INTO `spaces` (`id`, `name`, `alias`, `spaces_type_id`, `building_id`, `door_id`, `node_id`) VALUES
(1, 'II-01A', '', 1, 2, 8, 8),
(2, 'II-01B', '', 1, 2, 9, 9),
(3, 'II-04A', '', 1, 2, 33, 22),
(4, 'II-04B', '', 1, 2, 35, 23),
(5, 'Automàtica Industrial', 'A Ind', 4, 2, 84, 45),
(6, 'PLC\'s', 'PLC', 4, 2, 80, 46),
(7, 'Ciència i Tecnologia de Materials', 'CTM', 4, 2, 79, 47),
(8, 'Electrotècnica, Màquines Elèctriques i Electrònica de Potència (M.E.)', 'ME', 4, 2, 73, 49),
(9, 'Robòtica', 'Rob', 4, 2, 75, 48),
(10, 'Regulació Automàtica', 'RA', 4, 2, 69, 51),
(11, 'Mecànica de fluids: Pneumàtica Fluídica', '', 4, 2, 68, 52),
(12, 'Mecànica de fluids: Lubricants i Combustibles', '', 4, 2, 57, 55),
(13, 'Mecànica de Fluids Computacional', 'MFC', 4, 2, 70, 50),
(14, 'Mecànica de Fluids', 'MF', 4, 2, 59, 54),
(15, 'Visió per Computador', 'Vis Comp', 4, 2, 60, 53),
(16, 'Mecànica', 'Mec', 4, 2, 51, 56),
(17, 'II-02A', '', 1, 2, 16, 10),
(18, 'II-02B', '', 1, 2, 17, 11),
(19, 'II-03A', '', 1, 2, 27, 18),
(20, 'II-03B', '', 1, 2, 28, 19),
(21, 'II-05', '', 1, 2, 38, 25),
(22, 'II-06', '', 1, 2, 41, 26),
(23, 'II-07', '', 1, 2, 64, 27),
(24, 'II-08', '', 1, 2, 65, 28),
(25, 'II-09', '', 1, 2, 100, 31),
(26, 'Despatx 130', '', 3, 2, 116, 59),
(27, 'Despatx 131', '', 3, 2, 116, 60),
(28, 'Despatx 132', '', 3, 2, 115, 61),
(29, 'Despatx 133', '', 3, 2, 113, 62),
(30, 'Despatx 105', '', 3, 2, 105, 68),
(31, 'Despatx 106', '', 3, 2, 106, 67),
(32, 'Despatx 107', '', 3, 2, 107, 66),
(33, 'Despatx 108', '', 3, 2, 108, 65),
(34, 'Despatx 204', '', 3, 2, 204, 83),
(35, 'Despatx 205', '', 3, 2, 205, 82),
(36, 'Despatx 206', '', 3, 2, 206, 81),
(37, 'Despatx 207', '', 3, 2, 207, 80),
(38, 'Despatx 212', '', 3, 2, 212, 79),
(39, 'Despatx 213', '', 3, 2, 213, 78),
(40, 'Despatx 214', '', 3, 2, 214, 77),
(41, 'Despatx 215', '', 3, 2, 215, 76);

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
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `capture_points`
--
ALTER TABLE `capture_points`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `destination_zones`
--
ALTER TABLE `destination_zones`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `doors`
--
ALTER TABLE `doors`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=220;

--
-- AUTO_INCREMENT de la tabla `edges`
--
ALTER TABLE `edges`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `instructions`
--
ALTER TABLE `instructions`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `nodes`
--
ALTER TABLE `nodes`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=688;

--
-- AUTO_INCREMENT de la tabla `people`
--
ALTER TABLE `people`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `spaces`
--
ALTER TABLE `spaces`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

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
