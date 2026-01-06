-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : db
-- Généré le : ven. 12 déc. 2025 à 07:57
-- Version du serveur : 10.8.8-MariaDB-1:10.8.8+maria~ubu2204
-- Version de PHP : 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `sae301`
--

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `category`
--

INSERT INTO `category` (`id`, `name`, `slug`) VALUES
(9, 'Soins des Mains', 'soins-des-mains'),
(10, 'Beauté des Pieds', 'beauté-des-pieds'),
(11, 'Dépose & Réparations', 'dépose-&-réparations'),
(12, 'Nail Art', 'nail-art');

-- --------------------------------------------------------

--
-- Structure de la table `promotion`
--

CREATE TABLE `promotion` (
  `id` int(11) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `percentage` int(11) NOT NULL,
  `ative` tinyint(4) NOT NULL,
  `valid_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `promotion`
--

INSERT INTO `promotion` (`id`, `code`, `percentage`, `ative`, `valid_until`) VALUES
(1, 'BIENVENUE10', 10, 1, NULL),
(2, 'NOEL2023', 20, 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

CREATE TABLE `reservation` (
  `id` int(11) NOT NULL,
  `reference` varchar(20) NOT NULL,
  `date_rdv` datetime NOT NULL,
  `status` varchar(50) NOT NULL,
  `total_price` double NOT NULL,
  `total_duration` int(11) NOT NULL,
  `comment` longtext DEFAULT NULL,
  `guest_firstname` varchar(100) DEFAULT NULL,
  `guest_lastname` varchar(100) DEFAULT NULL,
  `guest_email` varchar(100) DEFAULT NULL,
  `guest_phone` varchar(20) DEFAULT NULL,
  `visit_address` varchar(255) NOT NULL,
  `client_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reservation`
--

INSERT INTO `reservation` (`id`, `reference`, `date_rdv`, `status`, `total_price`, `total_duration`, `comment`, `guest_firstname`, `guest_lastname`, `guest_email`, `guest_phone`, `visit_address`, `client_id`) VALUES
(1, 'RDV-6939547391CEA', '2025-12-07 12:15:00', 'COMPLETED', 125, 190, 'Sint ducimus animi velit qui facere voluptatem itaque.', NULL, NULL, NULL, NULL, '854, chemin de Seguin Lefort', 6),
(2, 'RDV-6939547391E13', '2025-12-01 09:00:00', 'COMPLETED', 10, 15, 'Fuga minus odit excepturi deleniti.', NULL, NULL, NULL, NULL, '51, rue de Valentin Reynaud', 3),
(3, 'RDV-6939547391E2A', '2025-12-15 10:00:00', 'CONFIRMED', 5, 10, 'Architecto sit quis id nemo adipisci fugit aliquam.', NULL, NULL, NULL, NULL, '86, impasse Jérôme Durand Philippedan', 9),
(4, 'RDV-6939547391E3D', '2025-11-19 16:00:00', 'COMPLETED', 25, 35, NULL, NULL, NULL, NULL, NULL, '86, impasse Jérôme Durand Philippedan', 9),
(5, 'RDV-6939547391E48', '2025-12-04 17:30:00', 'COMPLETED', 5, 10, NULL, NULL, NULL, NULL, NULL, '55, impasse Georges Traore Traore', 5),
(6, 'RDV-6939547391E52', '2025-12-10 13:30:00', 'CONFIRMED', 47, 95, 'Earum sit non est minima earum.', NULL, NULL, NULL, NULL, '31, rue Arnaud LebonBourg', 7),
(7, 'RDV-6939547391E62', '2026-01-06 17:15:00', 'CONFIRMED', 50, 70, NULL, NULL, NULL, NULL, NULL, '86, impasse Jérôme Durand Philippedan', 9),
(8, 'RDV-6939547391E6C', '2025-12-20 16:45:00', 'CONFIRMED', 115, 170, 'Voluptate quis quod aperiam ipsam tempore inventore consequatur non.', NULL, NULL, NULL, NULL, '31, rue Arnaud LebonBourg', 7),
(9, 'RDV-6939547391E7E', '2025-12-27 15:30:00', 'CONFIRMED', 15, 20, 'Deleniti itaque necessitatibus maiores est et qui.', NULL, NULL, NULL, NULL, '51, rue de Valentin Reynaud', 3),
(10, 'RDV-6939547391E8F', '2026-01-09 13:45:00', 'CONFIRMED', 45, 90, NULL, NULL, NULL, NULL, NULL, '98, rue de Barbe Chauvet', 4),
(11, 'RDV-6939547391E97', '2025-12-08 17:15:00', 'COMPLETED', 10, 15, 'Aut eligendi hic non inventore alias repellendus.', NULL, NULL, NULL, NULL, '98, rue de Barbe Chauvet', 4),
(12, 'RDV-6939547391EA6', '2026-01-08 17:15:00', 'CONFIRMED', 47, 65, NULL, NULL, NULL, NULL, NULL, '26, place de Masson Perez', 10),
(13, 'RDV-6939547391EB0', '2025-11-27 14:00:00', 'COMPLETED', 102, 170, NULL, NULL, NULL, NULL, NULL, '9, avenue Lebreton Buissondan', 8),
(14, 'RDV-6939547391EBA', '2025-11-26 14:45:00', 'COMPLETED', 105, 175, NULL, NULL, NULL, NULL, NULL, '31, rue Arnaud LebonBourg', 7),
(15, 'RDV-6939547391EC3', '2025-11-25 12:30:00', 'COMPLETED', 125, 195, 'Eos quisquam reprehenderit similique.', NULL, NULL, NULL, NULL, '436, rue Suzanne Moreno Roux', 11),
(16, 'RDV-6939547391ED0', '2025-12-03 16:00:00', 'COMPLETED', 135, 240, 'Ea possimus qui iste dicta.', NULL, NULL, NULL, NULL, '51, rue de Valentin Reynaud', 3),
(17, 'RDV-6939547391EDD', '2025-11-15 12:00:00', 'COMPLETED', 60, 75, 'Facilis et est voluptatem aut.', NULL, NULL, NULL, NULL, '854, chemin de Seguin Lefort', 6),
(18, 'RDV-6939547391EEB', '2025-11-10 09:45:00', 'COMPLETED', 15, 20, NULL, NULL, NULL, NULL, NULL, '26, place de Masson Perez', 10),
(19, 'RDV-6939547391EF4', '2025-11-20 10:45:00', 'COMPLETED', 40, 50, NULL, NULL, NULL, NULL, NULL, '98, rue de Barbe Chauvet', 4),
(20, 'RDV-6939547391EFD', '2025-12-20 12:45:00', 'CONFIRMED', 80, 90, 'Aperiam voluptatem et tenetur sed quis laborum.', NULL, NULL, NULL, NULL, '26, place de Masson Perez', 10),
(21, 'RDV-6939830d380ca', '2025-12-17 11:00:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(22, 'RDV-6939835cdd966', '2025-12-11 09:00:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(23, 'RDV-6939836211d2f', '2025-12-11 09:00:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(24, 'RDV-693983628ed7e', '2025-12-11 09:00:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(25, 'RDV-69398362bed97', '2025-12-11 09:00:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(26, 'RDV-69398362ec725', '2025-12-11 09:00:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(27, 'RDV-6939836322ad9', '2025-12-11 09:00:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(28, 'RDV-693983635a4ba', '2025-12-11 09:00:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(29, 'RDV-693983816ae06', '2025-12-11 09:00:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(30, 'RDV-6939839ae7e17', '2025-12-11 09:00:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(31, 'RDV-6939841ed1676', '2025-12-12 10:30:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(32, 'RDV-69398420d1ddf', '2025-12-12 10:30:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(33, 'RDV-693984215d20b', '2025-12-12 10:30:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(34, 'RDV-693984218b809', '2025-12-12 10:30:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(35, 'RDV-69398421bc049', '2025-12-12 10:30:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(36, 'RDV-69398421ece2d', '2025-12-12 10:30:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(37, 'RDV-693984222c51a', '2025-12-12 10:30:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(38, 'RDV-693984225ea64', '2025-12-12 10:30:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(39, 'RDV-6939842292871', '2025-12-12 10:30:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(40, 'RDV-693984235c73f', '2025-12-12 10:30:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(41, 'RDV-693984238ffaa', '2025-12-12 10:30:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(42, 'RDV-69398423b88b6', '2025-12-12 10:30:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(43, 'RDV-69398423e4e10', '2025-12-12 10:30:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(44, 'RDV-693984241f0bf', '2025-12-12 10:30:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(45, 'RDV-693984244cefd', '2025-12-12 10:30:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(46, 'RDV-6939842472b50', '2025-12-12 10:30:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(47, 'RDV-69398424a7dc5', '2025-12-12 10:30:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(48, 'RDV-69398459503dc', '2025-12-12 10:30:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(49, 'RDV-69398581e04e2', '2025-12-12 10:30:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(50, 'RDV-69398582ba33b', '2025-12-12 10:30:00', 'CONFIRMED', 45, 90, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(51, 'RDV-6939859b9f2cd', '2025-12-11 11:00:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(52, 'RDV-6939859d2cbe3', '2025-12-11 11:00:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(53, 'RDV-6939859d9c6fd', '2025-12-11 11:00:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(54, 'RDV-6939859dda05d', '2025-12-11 11:00:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(55, 'RDV-6939859e31e4e', '2025-12-11 11:00:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(56, 'RDV-69398610625a8', '2025-12-11 11:00:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(57, 'RDV-6939861137b92', '2025-12-11 11:00:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(58, 'RDV-693986307352d', '2025-12-23 10:00:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(59, 'RDV-6939863191b2a', '2025-12-23 10:00:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(60, 'RDV-69398631c390d', '2025-12-23 10:00:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(61, 'RDV-69398631ede7a', '2025-12-23 10:00:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(62, 'RDV-693986322d7ed', '2025-12-23 10:00:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(63, 'RDV-6939863334416', '2025-12-23 10:00:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(64, 'RDV-6939863366859', '2025-12-23 10:00:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(65, 'RDV-6939874eae76e', '2025-12-18 10:30:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(66, 'RDV-6939874fee673', '2025-12-18 10:30:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(67, 'RDV-693987508d3a9', '2025-12-18 10:30:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(68, 'RDV-69398750bfec6', '2025-12-18 10:30:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(69, 'RDV-6939886EB6ADE', '2025-12-19 10:00:00', 'CONFIRMED', 55, 110, 'appartement 19 | Invité: klara dupont', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(70, 'RDV-6939886FCD837', '2025-12-19 10:00:00', 'CONFIRMED', 55, 110, 'appartement 19 | Invité: klara dupont', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(71, 'RDV-6939889b23b2e', '2025-12-19 10:00:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(72, 'RDV-6939889c27947', '2025-12-19 10:00:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(73, 'RDV-69398b30c2031', '2025-12-23 09:30:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(74, 'RDV-69398b321f6c1', '2025-12-23 09:30:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(75, 'RDV-69398b32b57d9', '2025-12-23 09:30:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(76, 'RDV-69398b32ebe39', '2025-12-23 09:30:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(77, 'RDV-69398b3c48994', '2025-12-23 09:30:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(78, 'RDV-69398b3d4a584', '2025-12-23 09:30:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(79, 'RDV-69398b3e33aeb', '2025-12-23 09:30:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(80, 'RDV-69398b514e285', '2025-12-23 09:30:00', 'CONFIRMED', 55, 110, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(81, 'RDV-69398c0c77e2d', '2025-12-12 10:30:00', 'CONFIRMED', 65, 125, 'appartement 19 [INVITÉ: klara dupont]', 'klara', 'dupont', 'klara.dupont@gmail.com', '0526154851', '12 rue du jambon 10110 polisot', NULL),
(82, 'RDV-693990db9a270', '2025-12-26 11:30:00', 'CONFIRMED', 55, 60, ' [INVITÉ: kfvbvfq ]', 'kfvbvfq', NULL, NULL, NULL, '12 rue du jambon 10110 polisot', NULL),
(83, 'RDV-693990dc2b2a3', '2025-12-26 11:30:00', 'CONFIRMED', 55, 60, ' [INVITÉ: kfvbvfq ]', 'kfvbvfq', NULL, NULL, NULL, '12 rue du jambon 10110 polisot', NULL),
(84, 'RDV-693990dc53e7e', '2025-12-26 11:30:00', 'CONFIRMED', 55, 60, ' [INVITÉ: kfvbvfq ]', 'kfvbvfq', NULL, NULL, NULL, '12 rue du jambon 10110 polisot', NULL),
(85, 'RDV-693990dc8b3c5', '2025-12-26 11:30:00', 'CONFIRMED', 55, 60, ' [INVITÉ: kfvbvfq ]', 'kfvbvfq', NULL, NULL, NULL, '12 rue du jambon 10110 polisot', NULL),
(86, 'RDV-693990e21c237', '2025-12-26 11:30:00', 'CONFIRMED', 55, 60, ' [INVITÉ: kfvbvfq ]', 'kfvbvfq', NULL, NULL, NULL, '12 rue du jambon 10110 polisot', NULL),
(87, 'RDV-69399b8d2bc07', '2025-12-19 11:00:00', 'CONFIRMED', 70, 130, ' [INVITÉ: kjvlv ]', 'kjvlv', NULL, 'klara.dupont@gmail.com', NULL, '12 rue du jambon 10110 polisot', NULL),
(88, 'RDV-69399b8ebb70d', '2025-12-19 11:00:00', 'CONFIRMED', 70, 130, ' [INVITÉ: kjvlv ]', 'kjvlv', NULL, 'klara.dupont@gmail.com', NULL, '12 rue du jambon 10110 polisot', NULL),
(89, 'RDV-69399e67d2ddd', '2025-12-26 11:30:00', 'CONFIRMED', 50, 100, ' [INVITÉ: fkdrnqmoebfvmez ]', 'fkdrnqmoebfvmez', NULL, NULL, NULL, '12 rue du jambon', NULL),
(90, 'RDV-693a8d2e333d8', '2025-12-16 13:00:00', 'CONFIRMED', 55, 110, ' [INVITÉ: lola delacroix]', 'lola', 'delacroix', NULL, NULL, '12 rue du champignon', NULL),
(91, 'RDV-693A939E445D1', '2025-12-17 14:00:00', 'CONFIRMED', 75, 150, ' [INVITÉ: lola delacroix -  - ]', 'lola', 'delacroix', NULL, NULL, '12 rue du champignon', NULL),
(92, 'RDV-693A93CF9CF3F', '2025-12-17 14:00:00', 'CONFIRMED', 75, 150, ' [INVITÉ: lola delacroix -  - ]', 'lola', 'delacroix', NULL, NULL, '12 rue du champignon', NULL),
(93, 'RDV-693A93D18F538', '2025-12-17 14:00:00', 'CONFIRMED', 75, 150, ' [INVITÉ: lola delacroix -  - ]', 'lola', 'delacroix', NULL, NULL, '12 rue du champignon', NULL),
(94, 'RDV-693A93D1CFA12', '2025-12-17 14:00:00', 'CONFIRMED', 75, 150, ' [INVITÉ: lola delacroix -  - ]', 'lola', 'delacroix', NULL, NULL, '12 rue du champignon', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `reservation_service`
--

CREATE TABLE `reservation_service` (
  `reservation_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reservation_service`
--

INSERT INTO `reservation_service` (`reservation_id`, `service_id`) VALUES
(1, 20),
(1, 23),
(1, 25),
(2, 27),
(3, 25),
(4, 24),
(4, 27),
(5, 25),
(6, 21),
(6, 26),
(7, 19),
(7, 25),
(7, 27),
(8, 21),
(8, 23),
(8, 24),
(9, 24),
(10, 21),
(11, 27),
(12, 19),
(12, 26),
(12, 27),
(13, 19),
(13, 20),
(13, 26),
(14, 19),
(14, 20),
(14, 25),
(15, 19),
(15, 20),
(15, 22),
(16, 20),
(16, 21),
(16, 22),
(17, 19),
(17, 22),
(18, 24),
(19, 22),
(19, 24),
(20, 22),
(20, 23),
(21, 21),
(22, 21),
(23, 21),
(24, 21),
(25, 21),
(26, 21),
(27, 21),
(28, 21),
(29, 21),
(30, 21),
(31, 21),
(32, 21),
(33, 21),
(34, 21),
(35, 21),
(36, 21),
(37, 21),
(38, 21),
(39, 21),
(40, 21),
(41, 21),
(42, 21),
(43, 21),
(44, 21),
(45, 21),
(46, 21),
(47, 21),
(48, 21),
(49, 21),
(50, 21),
(51, 21),
(52, 21),
(53, 21),
(54, 21),
(55, 21),
(56, 21),
(57, 21),
(58, 21),
(59, 21),
(60, 21),
(61, 21),
(62, 21),
(63, 21),
(64, 21),
(65, 21),
(66, 21),
(67, 21),
(68, 21),
(69, 21),
(70, 21),
(71, 21),
(72, 21),
(73, 21),
(74, 21),
(75, 21),
(76, 21),
(77, 21),
(78, 21),
(79, 21),
(80, 21),
(81, 21),
(81, 27),
(82, 23),
(83, 23),
(84, 23),
(85, 23),
(86, 23),
(87, 21),
(87, 24),
(88, 21),
(88, 24),
(89, 21),
(89, 25),
(90, 21),
(91, 20),
(92, 20),
(93, 20),
(94, 20);

-- --------------------------------------------------------

--
-- Structure de la table `service`
--

CREATE TABLE `service` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `active` tinyint(4) NOT NULL,
  `relation` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `service`
--

INSERT INTO `service` (`id`, `title`, `description`, `price`, `duration`, `active`, `relation`, `category_id`) VALUES
(19, 'Pose Vernis Semi-permanent', 'Enim laudantium amet recusandae et quaerat magni mollitia harum adipisci consequatur earum excepturi incidunt.', 35, 45, 1, NULL, 9),
(20, 'Pose Complète Gel (Chablons)', 'Maxime sequi laborum corrupti delectus ullam sunt qui exercitationem.', 65, 120, 1, NULL, 9),
(21, 'Remplissage Gel', 'Qui quae iure qui alias fugit et nobis ea dignissimos fugiat quas dolorem.', 45, 90, 1, NULL, 9),
(22, 'Manucure Russe (Nettoyage)', 'Et dicta molestiae accusantium rerum animi maxime laboriosam.', 25, 30, 1, NULL, 9),
(23, 'Pédicure Complète + Vernis', 'At debitis vitae laborum voluptas minima et et asperiores voluptatem ad.', 55, 60, 1, NULL, 10),
(24, 'Dépose Semi-permanent', 'Et at explicabo consequuntur dolores sed eos.', 15, 20, 1, NULL, 11),
(25, 'Réparation ongle cassé', 'Sed quo a enim voluptas odit sed est magni soluta vitae similique in.', 5, 10, 1, NULL, 11),
(26, 'Nail Art simple (par doigt)', 'Aut aut fugit nihil est enim eum voluptatibus dolorem.', 2, 5, 1, NULL, 12),
(27, 'Babyboomer', 'Delectus beatae laborum hic est ratione deleniti placeat voluptatem possimus at omnis dolor deserunt.', 10, 15, 1, NULL, 12);

-- --------------------------------------------------------

--
-- Structure de la table `unavailability`
--

CREATE TABLE `unavailability` (
  `id` int(11) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `unavailability`
--

INSERT INTO `unavailability` (`id`, `start_date`, `end_date`, `reason`) VALUES
(1, '2025-12-12 12:00:00', '2025-12-12 14:00:00', 'Déjeuner pro');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `phono` varchar(20) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `zipcode` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `firstname`, `lastname`, `phono`, `address`, `city`, `zipcode`) VALUES
(1, 'admin@ongles.fr', '[\"ROLE_ADMIN\"]', '$2y$13$oFcUom14I1u.Y5KFD1W3jOVhBF2U30nzKtHTKOaATqDvF1JSDqf0S', 'Sophie', 'Lartiste', '0123456789', '10 rue de la Paix', 'Troyes', '10000'),
(2, 'client0@mail.com', '[]', '$2y$13$kHceGczLXa/AMMC7cpQ/a.O9edSNsmUn6Zxcg4ILahme5vOYR.HGm', 'Margot', 'Adam', '+33 1 28 21 47 15', '84, boulevard Isaac Perrier', 'Leconte', '47967'),
(3, 'client1@mail.com', '[]', '$2y$13$J/W5uleRUsXh8tqTvJ8aUOlMFgGBoTMmTgMLMfoe/Y29Y.CIHra6i', 'Margaux', 'De Oliveira', '07 85 76 43 76', '51, rue de Valentin', 'Reynaud', '42405'),
(4, 'client2@mail.com', '[]', '$2y$13$tWE77aHT1PvT3GnCEhX6X.LkBiDuQ3yAhYd5EN6kfmou5GmKCaPtO', 'Margaux', 'Mary', '+33 (0)4 58 47 13 80', '98, rue de Barbe', 'Chauvet', '55832'),
(5, 'client3@mail.com', '[]', '$2y$13$yulfSIdl3IDhLTvA3NJm1eosRXz6WqQwE8GsVnzzlTjykMYaZ.ETK', 'Dorothée', 'Rocher', '0930496018', '55, impasse Georges Traore', 'Traore', '15346'),
(6, 'client4@mail.com', '[]', '$2y$13$dcYShELJdaGHUZXQhiVOwOgdnzUd5DmjNSo3APQS6fJWDncG3g4YS', 'Marine', 'Torres', '0749683868', '854, chemin de Seguin', 'Lefort', '03831'),
(7, 'client5@mail.com', '[]', '$2y$13$hVhn0nGn7VvJf..XkNGsCeQC.5mrT/rdy2ZSoS.l9gMSLDF6ijbEu', 'Joséphine', 'Colin', '0907560402', '31, rue Arnaud', 'LebonBourg', '08772'),
(8, 'client6@mail.com', '[]', '$2y$13$DmBSMv8.gN6xEc7sV/StXeN37VffzCDsz74SUTGXZQgHTy4GvY.ZC', 'Julie', 'Sauvage', '+33 1 82 73 84 71', '9, avenue Lebreton', 'Buissondan', '21899'),
(9, 'client7@mail.com', '[]', '$2y$13$owJ1Nj8d3vCTRDiMZIIhCuUYqMS.R520UD06fTUkTf7N8.xtaOvO6', 'Danielle', 'Riviere', '01 18 55 89 35', '86, impasse Jérôme Durand', 'Philippedan', '44969'),
(10, 'client8@mail.com', '[]', '$2y$13$m.u6vweSvzvXdvkar26oFudVdmPUcAp59Qi0zwGZevx4Ze8Q1ZMEG', 'Nathalie', 'Vaillant', '+33 6 31 05 49 21', '26, place de Masson', 'Perez', '20463'),
(11, 'client9@mail.com', '[]', '$2y$13$dAnPwtlq2KHMtyE0zV2FuOpr765xVhLqTHPcbBAORwTqCHhtj5p/a', 'Audrey', 'Martins', '+33 5 28 08 56 64', '436, rue Suzanne Moreno', 'Roux', '49659');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `promotion`
--
ALTER TABLE `promotion`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_42C8495519EB6921` (`client_id`);

--
-- Index pour la table `reservation_service`
--
ALTER TABLE `reservation_service`
  ADD PRIMARY KEY (`reservation_id`,`service_id`),
  ADD KEY `IDX_86082157B83297E7` (`reservation_id`),
  ADD KEY `IDX_86082157ED5CA9E6` (`service_id`);

--
-- Index pour la table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_E19D9AD212469DE2` (`category_id`);

--
-- Index pour la table `unavailability`
--
ALTER TABLE `unavailability`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `promotion`
--
ALTER TABLE `promotion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT pour la table `service`
--
ALTER TABLE `service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pour la table `unavailability`
--
ALTER TABLE `unavailability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `FK_42C8495519EB6921` FOREIGN KEY (`client_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `reservation_service`
--
ALTER TABLE `reservation_service`
  ADD CONSTRAINT `FK_86082157B83297E7` FOREIGN KEY (`reservation_id`) REFERENCES `reservation` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_86082157ED5CA9E6` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `service`
--
ALTER TABLE `service`
  ADD CONSTRAINT `FK_E19D9AD212469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
