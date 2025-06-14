-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 14 juin 2025 à 12:42
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestionpharm`
--

-- --------------------------------------------------------

--
-- Structure de la table `approval`
--

CREATE TABLE `approval` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `approval`
--

INSERT INTO `approval` (`id`, `cart_id`, `admin_id`, `status`, `created_at`) VALUES
(5, 19, NULL, 'pending', '2025-06-11 21:22:06');

-- --------------------------------------------------------

--
-- Structure de la table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `medicament_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `requires_approval` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `medicament_id`, `quantity`, `requires_approval`) VALUES
(19, 20, 6, 4, 1);

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `category`
--

INSERT INTO `category` (`id`, `nom`) VALUES
(2, 'Antibiotiques   '),
(4, 'Antihypertenseurs'),
(5, 'Antidiabétiques'),
(6, 'Suppléments Vitaminiques'),
(7, 'Antiviraux   ');

-- --------------------------------------------------------

--
-- Structure de la table `detail`
--

CREATE TABLE `detail` (
  `id` int(11) NOT NULL,
  `medicament_id` int(11) NOT NULL,
  `vente_id` int(11) NOT NULL,
  `nombre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `detail`
--

INSERT INTO `detail` (`id`, `medicament_id`, `vente_id`, `nombre`) VALUES
(28, 1, 13, 5),
(29, 6, 13, 2),
(30, 2, 13, 2),
(31, 8, 14, 12),
(32, 9, 15, 20),
(33, 1, 15, 10000),
(34, 6, 16, 4),
(35, 2, 16, 2),
(36, 1, 19, 1),
(37, 1, 21, 3),
(38, 2, 22, 1),
(39, 4, 22, 1),
(40, 2, 23, 1),
(41, 1, 23, 6),
(42, 1, 25, 1),
(43, 2, 26, 5),
(44, 1, 27, 1);

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20240718021820', '2024-07-18 02:28:07', 559),
('DoctrineMigrations\\Version20240718054714', '2024-07-18 05:47:35', 320),
('DoctrineMigrations\\Version20240718054913', '2024-07-18 05:49:19', 1115),
('DoctrineMigrations\\Version20240718085939', '2024-07-18 08:59:54', 252),
('DoctrineMigrations\\Version20240718170853', '2024-07-18 17:09:04', 335),
('DoctrineMigrations\\Version20240718184322', '2024-07-18 18:43:38', 884),
('DoctrineMigrations\\Version20240718184639', '2024-07-18 18:46:47', 1398),
('DoctrineMigrations\\Version20250610152401', '2025-06-10 17:24:31', 366);

-- --------------------------------------------------------

--
-- Structure de la table `medicament`
--

CREATE TABLE `medicament` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prix` double NOT NULL,
  `nombre` int(11) NOT NULL,
  `ordonnance` tinyint(1) NOT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `medicament`
--

INSERT INTO `medicament` (`id`, `nom`, `prix`, `nombre`, `ordonnance`, `category_id`) VALUES
(1, 'Paracétamol', 3000, 10102, 0, 4),
(2, 'Vitamine D', 2500, 11989, 0, 6),
(3, 'Vitamine C', 2600, 1200, 0, 6),
(4, 'Amoxilline', 1500, 1199, 0, 2),
(6, 'Lisinopril', 8000, 2790, 1, 4),
(8, 'Cetirizine', 2700, 1193, 0, 5),
(9, 'Ciprofloxacine', 7000, 0, 1, 2),
(10, 'Metformine', 4000, 1000, 1, 5),
(11, 'Paracétamol Rouge', 100, 25, 0, 5),
(13, 'inflammatoire', 2000, 2, 1, 5);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `adresse` varchar(255) NOT NULL,
  `telephone` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `nom`, `prenom`, `image`, `adresse`, `telephone`) VALUES
(9, 'admin01@gmail.com', '[\"ROLE_ADMIN\"]', '$2y$13$kKUnIQZ/eHfdSTi2NxMkp..OK9FvCoKx5ASwLjR01AJaTECVpgVU2', 'Joseph', 'BAGA', '8080930f88246a29e72ca8e7446b298c.jpg', 'Ouaga secteur 15', '66555154'),
(10, 'user01@gmail.com', '[\"ROLE_ADMIN\"]', '$2y$13$0s8IKliTAuCLVkGcT6zeTOZRHESTbZ17K.0rzsErVtn1GWWiAOHbe', 'user', '01', '21f5fe4ac0143e554ca5e408c684d687.jpg', 'kilwin ', '0331260000'),
(11, 'user02@gmail.com', '[]', '$2y$13$TM5TiZUFpuDvWaD6otFmRuyJ/1ySAwpG.ePgbbPZwOFBqi0KhkOTm', 'user', '02', NULL, 'Dans le monde, Terre', '0331258545'),
(12, 'admin@joseph.bf', '[\"ROLE_ADMIN\"]', '$2y$13$Vbrn0djQIPCx23HatNJBbujlB1jQapjPgPaqzIgPo5dnGlK9mKdcu', 'baga', 'jooseph', '93871e567d82d0bd911ba27d3c67fd61.jpg', 'ouaga 15', '66555154'),
(14, 'admin@joseph.ci', '[\"ROLE_USER\"]', '$2y$13$5zQajMVh5ky1uC3QfGbf8e2ezOgx5rjdZhC2/epH3Fsl4teqBXL.O', 'baga', 'jooseph', '46c8c70e1b3353501cb6cae5ee136931.jpg', 'ouaga 15', '66555154'),
(16, 'admin@joseph.cm', '[\"ROLE_USER\"]', '$2y$13$SaSpUHiFpJVqCgtcO6/I.OVQFkdZvrZBVvPkpVboDdmCR/TFORrM2', 'baga', 'jooseph', 'edb5db0ceff82d5c984dbbc1d1cf7bcc.jpg', 'ouaga 15', '66555154'),
(20, 'abdoul@gmail.com', '[\"ROLE_USER\"]', '$2y$13$LsUJId38WlCuPCnM5FsisuUclRmW9cFNEWE.0f5TgAGiqyGU86jJ6', 'Tiendrebeogo', 'Abdoul', 'c1ace17cc88ba3e7c6facdf3eb2375e2.png', 'kossodo', '78963214'),
(21, 'final@gmail.com', '[\"ROLE_USER\"]', '$2y$13$xcY31OLGI3ErxHMrRCPb7ugs/WC1O/seknyClyhDebQ5WEgbiM.3q', 'final', 'final', NULL, 'ouga', '54785214'),
(22, 'moistest@gma.com', '[\"ROLE_USER\"]', '$2y$13$HN7xcn94mvfSkJlpW9tU9.7VSRNwESAVEYB7YpFJ6WmanCnEqX4bK', 'moi', 'mois', NULL, 'oua', '478214');

-- --------------------------------------------------------

--
-- Structure de la table `vente`
--

CREATE TABLE `vente` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total` double NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `vente`
--

INSERT INTO `vente` (`id`, `user_id`, `total`, `created_at`) VALUES
(13, 10, 27000, '2025-05-19 14:11:16'),
(14, 10, 32400, '2025-05-19 14:12:33'),
(15, 9, 12140, '2025-05-19 14:20:02'),
(16, 11, 37000, '2025-05-19 14:23:47'),
(19, 14, 3000, '2025-06-10 18:06:47'),
(20, 14, 47000, '2025-06-10 19:22:37'),
(21, 14, 9000, '2025-06-10 19:25:06'),
(22, 14, 4000, '2025-06-10 19:26:05'),
(23, 14, 20500, '2025-06-10 19:55:50'),
(24, 9, 8000, '2025-06-11 20:06:14'),
(25, 20, 3000, '2025-06-11 21:00:54'),
(26, 9, 12500, '2025-06-14 10:04:47'),
(27, 14, 3000, '2025-06-14 11:05:04');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `approval`
--
ALTER TABLE `approval`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_16E0952B1AD5CDBF` (`cart_id`),
  ADD KEY `IDX_16E0952B642B8210` (`admin_id`);

--
-- Index pour la table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_BA388B7A76ED395` (`user_id`),
  ADD KEY `IDX_BA388B7AB0D61F7` (`medicament_id`);

--
-- Index pour la table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `detail`
--
ALTER TABLE `detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_2E067F93AB0D61F7` (`medicament_id`),
  ADD KEY `IDX_2E067F937DC7170A` (`vente_id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `medicament`
--
ALTER TABLE `medicament`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_9A9C723A12469DE2` (`category_id`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

--
-- Index pour la table `vente`
--
ALTER TABLE `vente`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_888A2A4CA76ED395` (`user_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `approval`
--
ALTER TABLE `approval`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT pour la table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `detail`
--
ALTER TABLE `detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT pour la table `medicament`
--
ALTER TABLE `medicament`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `vente`
--
ALTER TABLE `vente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `approval`
--
ALTER TABLE `approval`
  ADD CONSTRAINT `FK_16E0952B1AD5CDBF` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`id`),
  ADD CONSTRAINT `FK_16E0952B642B8210` FOREIGN KEY (`admin_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `FK_BA388B7A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_BA388B7AB0D61F7` FOREIGN KEY (`medicament_id`) REFERENCES `medicament` (`id`);

--
-- Contraintes pour la table `detail`
--
ALTER TABLE `detail`
  ADD CONSTRAINT `FK_2E067F937DC7170A` FOREIGN KEY (`vente_id`) REFERENCES `vente` (`id`),
  ADD CONSTRAINT `FK_2E067F93AB0D61F7` FOREIGN KEY (`medicament_id`) REFERENCES `medicament` (`id`);

--
-- Contraintes pour la table `medicament`
--
ALTER TABLE `medicament`
  ADD CONSTRAINT `FK_9A9C723A12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Contraintes pour la table `vente`
--
ALTER TABLE `vente`
  ADD CONSTRAINT `FK_888A2A4CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
