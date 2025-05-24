-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le : ven. 23 mai 2025 à 11:55
-- Version du serveur : 8.0.40
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ecoride`
--

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE `avis` (
  `id` int NOT NULL,
  `contenu` text NOT NULL,
  `chauffeur_id` int NOT NULL,
  `auteur_id` int NOT NULL,
  `valide` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `note` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`id`, `contenu`, `chauffeur_id`, `auteur_id`, `valide`, `created_at`, `note`) VALUES
(3, 'agréable voyage , chauffeur ponctuel. ', 5, 5, 1, '2025-05-20 21:24:00', 0);

-- --------------------------------------------------------

--
-- Structure de la table `litiges`
--

CREATE TABLE `litiges` (
  `id` int NOT NULL,
  `ride_id` int NOT NULL,
  `passager_id` int NOT NULL,
  `chauffeur_id` int NOT NULL,
  `commentaire` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Structure de la table `participants`
--

CREATE TABLE `participants` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `ride_id` int NOT NULL,
  `statut` varchar(20) DEFAULT 'réservé',
  `date_reservation` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `participants`
--

INSERT INTO `participants` (`id`, `user_id`, `ride_id`, `statut`, `date_reservation`) VALUES
(8, 5, 7, 'réservé', '2025-05-20 13:52:50'),
(9, 5, 8, 'réservé', '2025-05-20 13:57:29');

-- --------------------------------------------------------

--
-- Structure de la table `rides`
--

CREATE TABLE `rides` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `vehicle_id` int NOT NULL,
  `depart` varchar(100) NOT NULL,
  `arrivee` varchar(100) NOT NULL,
  `date_depart` datetime NOT NULL,
  `date_arrivee` datetime NOT NULL,
  `prix` decimal(5,2) NOT NULL,
  `places` int NOT NULL,
  `ecologique` tinyint(1) DEFAULT '0',
  `statut` enum('en attente','en cours','terminé') DEFAULT 'en attente',
  `duree` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `rides`
--

INSERT INTO `rides` (`id`, `user_id`, `vehicle_id`, `depart`, `arrivee`, `date_depart`, `date_arrivee`, `prix`, `places`, `ecologique`, `statut`, `duree`) VALUES
(1, 5, 1, 'Paris', 'Lyon', '2025-05-10 08:00:00', '2025-05-10 12:00:00', 20.00, 3, 1, 'en attente', 0),
(2, 5, 5, 'Paris', 'Lyon', '2025-05-10 08:00:00', '2025-05-10 12:00:00', 20.00, 3, 1, 'en attente', 0),
(3, 5, 5, 'paris', 'LYON', '2025-05-05 12:51:00', '2025-05-05 15:51:00', 20.00, 2, 0, 'en attente', 0),
(4, 5, 5, 'paris', 'lyon', '2025-05-05 00:00:00', '2025-05-05 00:00:00', 20.00, 3, 0, 'en cours', 0),
(5, 5, 5, 'paris', 'marseille', '2025-05-05 00:00:00', '2025-05-05 00:00:00', 20.00, 3, 0, 'en cours', 0),
(7, 5, 5, 'paris', 'marseille', '2025-05-19 00:00:00', '2025-05-19 00:00:00', 22.00, 2, 0, 'terminé', 0),
(8, 5, 5, 'paris', 'lyon', '2025-05-21 00:00:00', '2025-05-21 00:00:00', 22.00, 1, 0, 'en cours', 0),
(9, 23, 7, 'paris', 'nantes', '2025-05-20 00:00:00', '2025-05-20 00:00:00', 20.00, 5, 0, 'en attente', 0),
(10, 23, 7, 'paris', 'montpellier', '2025-05-20 00:00:00', '2025-05-20 00:00:00', 22.00, 4, 0, 'en attente', 0);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `pseudo` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `credits` int DEFAULT '20',
  `role` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'utilisateur',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `actif` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `pseudo`, `email`, `password`, `credits`, `role`, `created_at`, `actif`) VALUES
(5, 'TestUser', 'test@ecoride.fr', '$2y$10$JWgRCA/6Q8paJEQEjKCXxeACVUjG0xROt1OZt4/mfZO7rVoW6kOtW', 21, 'chauffeur', '2025-04-28 10:12:05', 1),
(17, 'Admin', 'admin@ecoride.fr', '$2y$10$6wHFj3d5rIvcghpvw/Fw.OBEHnvIipFZBWWAGSBlP3GFodkabLg2G', 999, 'admin', '2025-05-03 18:36:02', 1),
(21, 'Employe', 'employe@ecoride.fr', '$2y$10$looA7LvVMzQtDS5Y//iBYOFzDFebzMgH46gy.OMXVGTyV9BuUu/e.', 0, 'employe', '2025-05-06 15:58:58', 1),
(23, 'testuser2', 'testuser2@ecoride.fr', '$2y$10$/.TYamQ69IrTl0Cm74kQMOKxvamLQC7qwXd7bRxxoq6U9usn9CVla', 0, 'les deux', '2025-05-20 15:26:50', 1),
(24, 'MANEL', 'employe2@ecoride.fr', '$2y$10$kJLaqUUTiQrgY4uV/ne5KuoFjJyMhBrInSbzY9YoMYefcRDlqyaj.', 0, 'employe', '2025-05-20 17:33:17', 0);

-- --------------------------------------------------------

--
-- Structure de la table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `plaque` varchar(20) NOT NULL,
  `date_immatriculation` date DEFAULT NULL,
  `modele` varchar(50) NOT NULL,
  `couleur` varchar(30) NOT NULL,
  `marque` varchar(50) NOT NULL,
  `energie` enum('électrique','essence','hybride') NOT NULL,
  `places` int DEFAULT NULL,
  `fumeur` tinyint(1) DEFAULT '0',
  `animal` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `preferences` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `vehicles`
--

INSERT INTO `vehicles` (`id`, `user_id`, `plaque`, `date_immatriculation`, `modele`, `couleur`, `marque`, `energie`, `places`, `fumeur`, `animal`, `created_at`, `preferences`) VALUES
(1, 5, 'AA-123-AA', NULL, 'tesla 3', 'rouge', 'tesla', 'hybride', NULL, 0, 0, '2025-04-28 13:22:05', NULL),
(2, 5, 'AB-123-AC', NULL, 'TOUAREG', 'NOIR', 'GOLF', 'essence', NULL, 0, 0, '2025-04-29 10:28:07', NULL),
(5, 5, 'AB-123-CD', NULL, 'Zoé', 'vert', 'Renault', 'électrique', NULL, 0, 0, '2025-05-03 18:43:05', NULL),
(7, 23, 'AB-123-AC', NULL, 'clio 3', 'BEIGE', 'RENAULT', 'électrique', NULL, 0, 0, '2025-05-20 15:28:32', NULL),
(8, 5, 'AB-920-EB', NULL, '3008', 'NOIR', 'Peugeot', 'électrique', NULL, 0, 0, '2025-05-22 10:44:48', NULL),
(9, 5, 'AA-123-AA', '2022-01-01', 'Tesla 3', 'Rouge', 'Tesla', 'électrique', 4, 0, 0, '2025-05-22 10:58:20', NULL),
(10, 5, 'AB-123-AC', '2022-06-10', 'Touareg', 'Noir', 'Golf', 'électrique', 4, 0, 0, '2025-05-22 10:58:20', NULL),
(11, 5, 'AB-123-CD', '2023-03-15', 'Zoé', 'Vert', 'Renault', 'électrique', 4, 0, 0, '2025-05-22 10:58:20', NULL),
(12, 23, 'AB-123-AC', '2021-09-20', 'Clio 3', 'Beige', 'Renault', 'électrique', 4, 0, 0, '2025-05-22 10:58:20', NULL),
(13, 5, 'AB-920-EB', '2024-04-01', '3008', 'Noir', 'Peugeot', 'électrique', 4, 0, 0, '2025-05-22 10:58:20', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chauffeur_id` (`chauffeur_id`),
  ADD KEY `auteur_id` (`auteur_id`);

--
-- Index pour la table `litiges`
--
ALTER TABLE `litiges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ride_id` (`ride_id`),
  ADD KEY `passager_id` (`passager_id`),
  ADD KEY `chauffeur_id` (`chauffeur_id`);

--
-- Index pour la table `participants`
--
ALTER TABLE `participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `ride_id` (`ride_id`);

--
-- Index pour la table `rides`
--
ALTER TABLE `rides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `litiges`
--
ALTER TABLE `litiges`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `participants`
--
ALTER TABLE `participants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `rides`
--
ALTER TABLE `rides`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `avis_ibfk_1` FOREIGN KEY (`chauffeur_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `avis_ibfk_2` FOREIGN KEY (`auteur_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `litiges`
--
ALTER TABLE `litiges`
  ADD CONSTRAINT `litiges_ibfk_1` FOREIGN KEY (`ride_id`) REFERENCES `rides` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `litiges_ibfk_2` FOREIGN KEY (`passager_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `litiges_ibfk_3` FOREIGN KEY (`chauffeur_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `participants`
--
ALTER TABLE `participants`
  ADD CONSTRAINT `participants_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `participants_ibfk_2` FOREIGN KEY (`ride_id`) REFERENCES `rides` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `rides`
--
ALTER TABLE `rides`
  ADD CONSTRAINT `rides_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rides_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
