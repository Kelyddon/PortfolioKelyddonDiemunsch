-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : dim. 04 jan. 2026 à 19:59
-- Version du serveur : 5.7.24
-- Version de PHP : 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `portfolio_kelyddondiemunsch`
--

-- --------------------------------------------------------

--
-- Structure de la table `contact_message`
--

CREATE TABLE `contact_message` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `description` longtext NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `contact_message`
--

INSERT INTO `contact_message` (`id`, `email`, `phone`, `description`, `created_at`) VALUES
(2, 'dupont@dupont.fr', '1245030152', 'Bonjour', '2026-01-04 15:54:36');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20251215162734', '2025-12-15 16:27:48', 84),
('DoctrineMigrations\\Version20251215163213', '2025-12-15 16:32:22', 112),
('DoctrineMigrations\\Version20251219112823', '2025-12-19 12:08:54', 335),
('DoctrineMigrations\\Version20251219120845', NULL, NULL),
('DoctrineMigrations\\Version20251221104829', NULL, NULL),
('DoctrineMigrations\\Version20251221110121', NULL, NULL),
('DoctrineMigrations\\Version20251221110327', NULL, NULL),
('DoctrineMigrations\\Version20251221110409', NULL, NULL),
('DoctrineMigrations\\Version20251221115225', NULL, NULL),
('DoctrineMigrations\\Version20251223105903', NULL, NULL),
('DoctrineMigrations\\Version20251223110250', NULL, NULL),
('DoctrineMigrations\\Version20251223110504', NULL, NULL),
('DoctrineMigrations\\Version20251223110530', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `hard_skill`
--

CREATE TABLE `hard_skill` (
  `id` int(11) NOT NULL,
  `language` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `hard_skill`
--

INSERT INTO `hard_skill` (`id`, `language`) VALUES
(3, 'Symfony'),
(4, 'JavaScript'),
(6, 'Wordpress'),
(7, 'SQL'),
(8, 'Nodejs'),
(9, 'Github'),
(10, 'PHP'),
(11, 'Reactjs'),
(12, 'Typescript');

-- --------------------------------------------------------

--
-- Structure de la table `presentation_text`
--

CREATE TABLE `presentation_text` (
  `id` int(11) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `content` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `presentation_text`
--

INSERT INTO `presentation_text` (`id`, `slug`, `content`) VALUES
(1, 'about', 'Développeur full stack avec une forte sensibilité data, j’aborde chaque projet comme un système à analyser, optimiser et fiabiliser. Issu d’un parcours scientifique, j’applique une démarche rigoureuse au développement : exploration, tests comparatifs, documentation et choix techniques argumentés.\n\n\nJe m’appelle Kelyddon Diemunsch, développeur full stack en deuxième année de Bachelor à la 3W Academy.\n');

-- --------------------------------------------------------

--
-- Structure de la table `project`
--

CREATE TABLE `project` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `langages` json DEFAULT NULL,
  `description` longtext,
  `github_link` varchar(255) DEFAULT NULL,
  `create_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `project`
--

INSERT INTO `project` (`id`, `name`, `image`, `langages`, `description`, `github_link`, `create_at`) VALUES
(6, 'Snake en Javascript', 'upload/project_1767453793_78006e09.png', '[\"JavaScript\"]', 'Projet : réaliser en JavaScript l\'un des trois jeux suivants : akanoid, pacman, snake.\r\n\r\nAction : création d\'un snake en ajoutant un score board et différents types de difficultés.\r\n\r\nRésultat : les joueurs accèdent au jeu snake qui affiche un leader board avec les personnes qui ont réalisé les meilleurs scores et un choix de difficultés de facile à difficile.\r\nEn cliquant sur le lien github ci-dessous vous accéderez à la page git contenant 3 jeux : Snake, le jeu principal ainsi que 2 tests : un akanoïd et un pacman.', 'https://github.com/Kelyddon/SnakeJs.git', '2024-12-13 20:32:00'),
(7, 'Akinator en PHP', 'upload/project_1767453841_2eb6a994.png', '[\"SQL\", \"PHP\"]', 'Projet : réaliser un Akinator en php (jeu en ligne où un \"génie virtuel\" devine le personnage, l\'objet ou la chose, à laquelle le joueur pense en posant une série de questions).\r\n\r\nAction : grâce au développement d\'une base de données en SQL, les questions, réponses, chemin logique et comptes utilisateurs sont gérés. \r\n\r\nRésultats : les joueurs peuvent, chacun, enregistrer leurs parties. Le lien git contient les fichiers pour l\'Akinator ainsi que la base de données.', 'https://github.com/Kelyddon/Akinator_Kelyddon_Diemunsch.git', '2025-02-03 12:32:00'),
(8, 'Carnet d\'adresses en React', 'upload/project_1767453859_2861dc46.png', '[\"Reactjs\"]', 'Projet : réaliser un carnet d\'adresses en react permettant d\'enregistrer les prénom, nom, téléphone, email et date d\'anniversaire des contacts.\r\n\r\nAction : via la création d\'un formulaire, les données sont stockées dans un localstorage.\r\n\r\nRésultats : les données saisies sont enregistrées et visibles dans le tableau annuaire avec une option de mise en surbrillance des personnes à leur date anniversaire.', 'https://github.com/Kelyddon/TPReact_carnet_adresse_KMM.git', '2025-12-15 15:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `soft_skill`
--

CREATE TABLE `soft_skill` (
  `id` int(11) NOT NULL,
  `skill` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `soft_skill`
--

INSERT INTO `soft_skill` (`id`, `skill`) VALUES
(4, 'Curiosité'),
(5, 'Planification'),
(6, 'Rigueur'),
(7, 'Négociation'),
(8, 'Gestion des conflits');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`) VALUES
(1, 'admin@example.com', '[\"ROLE_ADMIN\"]', '$2y$13$7rztc68pzfivGHC5y64nheIoyheKwCdQp5LcZCkL5FUCsqGEcStD6');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `contact_message`
--
ALTER TABLE `contact_message`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `hard_skill`
--
ALTER TABLE `hard_skill`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `presentation_text`
--
ALTER TABLE `presentation_text`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `soft_skill`
--
ALTER TABLE `soft_skill`
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
-- AUTO_INCREMENT pour la table `contact_message`
--
ALTER TABLE `contact_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `hard_skill`
--
ALTER TABLE `hard_skill`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `presentation_text`
--
ALTER TABLE `presentation_text`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `project`
--
ALTER TABLE `project`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `soft_skill`
--
ALTER TABLE `soft_skill`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
