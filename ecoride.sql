-- Suppression des tables existantes si elles existent déjà
DROP TABLE IF EXISTS participants, avis, litiges, rides, vehicles, users;

-- Table users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    credits INT DEFAULT 0,
    role ENUM('utilisateur', 'chauffeur', 'passager', 'les deux', 'admin', 'employe') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    actif TINYINT(1) DEFAULT 1
);

-- Table vehicles
CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plaque VARCHAR(20) NOT NULL,
    modele VARCHAR(100) NOT NULL,
    couleur VARCHAR(50),
    marque VARCHAR(100),
    energie VARCHAR(20),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table rides
CREATE TABLE rides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    depart VARCHAR(100) NOT NULL,
    arrivee VARCHAR(100) NOT NULL,
    date_depart DATETIME NOT NULL,
    date_arrivee DATETIME NOT NULL,
    prix DECIMAL(5,2) NOT NULL,
    places INT NOT NULL,
    ecologique TINYINT(1) DEFAULT 0,
    statut VARCHAR(20) DEFAULT 'en attente',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
);

-- Table participants
CREATE TABLE participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    ride_id INT NOT NULL,
    statut VARCHAR(20) DEFAULT 'réservé',
    date_reservation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (ride_id) REFERENCES rides(id) ON DELETE CASCADE
);

-- Table litiges
CREATE TABLE litiges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ride_id INT NOT NULL,
    passager_id INT NOT NULL,
    chauffeur_id INT NOT NULL,
    commentaire TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ride_id) REFERENCES rides(id) ON DELETE CASCADE,
    FOREIGN KEY (passager_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (chauffeur_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table avis
CREATE TABLE avis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contenu TEXT NOT NULL,
    chauffeur_id INT NOT NULL,
    auteur_id INT NOT NULL,
    valide TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chauffeur_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (auteur_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Données de la table users
INSERT INTO `users` (`id`, `pseudo`, `email`, `password`, `credits`, `role`, `created_at`, `actif`) VALUES
(5, 'TestUser', 'test@ecoride.fr', '$2y$10$JWgRCA/6Q8paJEQEjKCXxeACVUjG0xROt1OZt4/mfZO7rVoW6kOtW', 20, 'les deux', '2025-04-28 10:12:05', 1),
(12, 'manu', 'manu@ecoride.fr', '$2y$10$xf4OHESvGtepa84kvk/Nc.QfjFKjcmwGwggx2FTkOO1PM55.ImqZ.', 0, 'employe', '2025-04-29 14:25:39', 0),
(16, 'TestUser', 'test2@ecoride.fr', '$2y$10$g5vSm29rb7YlS7YlYXL.Wuk3GZCPN6a6QzPknTFjq6x2jKZHAXFQa', 20, 'utilisateur', '2025-05-03 18:36:02', 1),
(17, 'Admin', 'admin2@ecoride.fr', '$2y$10$6wHFj3d5rIvcghpvw/Fw.OBEHnvIipFZBWWAGSBlP3GFodkabLg2G', 999, 'admin', '2025-05-03 18:36:02', 1),
(19, 'testeur3', 'test3@ecoride.fr', '$2y$10$Ey/X7mewu1bvAyPkuYLUsekSb8wV2YrRxhl8rCsbrxeHn30zdLI.u', 20, 'chauffeur', '2025-05-05 17:46:41', 1),
(21, 'Employe', 'employe@ecoride.fr', '$2y$10$looA7LvVMzQtDS5Y//iBYOFzDFebzMgH46gy.OMXVGTyV9BuUu/e.', 0, 'employe', '2025-05-06 15:58:58', 1);

-- Données de la table vehicles
INSERT INTO `vehicles` (`id`, `user_id`, `plaque`, `modele`, `couleur`, `marque`, `energie`, `created_at`) VALUES
(1, 5, 'AA-123-AA', 'tesla 3', 'rouge', 'tesla', 'hybride', '2025-04-28 13:22:05'),
(2, 5, 'AB-123-AC', 'TOUAREG', 'NOIR', 'GOLF', 'essence', '2025-04-29 10:28:07'),
(5, 5, 'AB-123-CD', 'Zoé', 'vert', 'Renault', 'électrique', '2025-05-03 18:43:05'),
(6, 19, 'EB-123-AF ', 'CLIO5', ' NOIR', 'RENAULT', 'essence', '2025-05-05 18:02:11');

-- Données de la table rides
INSERT INTO `rides` (`id`, `user_id`, `vehicle_id`, `depart`, `arrivee`, `date_depart`, `date_arrivee`, `prix`, `places`, `ecologique`, `statut`) VALUES
(1, 5, 1, 'Paris', 'Lyon', '2025-05-10 08:00:00', '2025-05-10 12:00:00', 20.00, 3, 1, 'en attente'),
(2, 5, 5, 'Paris', 'Lyon', '2025-05-10 08:00:00', '2025-05-10 12:00:00', 20.00, 3, 1, 'en attente'),
(3, 5, 5, 'paris', 'LYON', '2025-05-05 12:51:00', '2025-05-05 15:51:00', 20.00, 2, 0, 'en attente'),
(4, 5, 5, 'paris', 'lyon', '2025-05-05 00:00:00', '2025-05-05 00:00:00', 20.00, 3, 0, 'en attente'),
(5, 5, 5, 'paris', 'marseille', '2025-05-05 00:00:00', '2025-05-05 00:00:00', 20.00, 3, 0, 'en attente'),
(6, 19, 6, 'paris', 'marseille', '2025-05-06 00:00:00', '2025-05-06 00:00:00', 22.00, 0, 0, 'en attente');

-- Données de la table participants
INSERT INTO `participants` (`id`, `user_id`, `ride_id`, `statut`, `date_reservation`) VALUES
(2, 16, 1, 'réservé', '2025-05-03 19:21:21'),
(6, 5, 6, 'réservé', '2025-05-06 15:06:13'),
(7, 19, 3, 'réservé', '2025-05-06 20:50:38');

-- Données de la table litiges
INSERT INTO `litiges` (`id`, `ride_id`, `passager_id`, `chauffeur_id`, `commentaire`, `created_at`) VALUES
(1, 3, 19, 5, 'le chauffeur ne s\'est pas présenté', '2025-05-06 21:11:24');

-- Données de la table avis
INSERT INTO `avis` (`id`, `contenu`, `chauffeur_id`, `auteur_id`, `valide`, `created_at`) VALUES
(1, 'chauffeur aimable et courtois', 19, 5, 0, '2025-05-06 20:35:49');