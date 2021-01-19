-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  mer. 15 jan. 2020 à 08:29
-- Version du serveur :  5.7.26
-- Version de PHP :  7.3.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `mywishlist`
--

-- --------------------------------------------------------

--
-- Structure de la table `compte`
--

DROP TABLE IF EXISTS `compte`;
CREATE TABLE IF NOT EXISTS `compte` (
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `pseudo` varchar(20) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  PRIMARY KEY (`username`),
  UNIQUE KEY `PSEUDO_UNIQUE` (`pseudo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `compte`
--

INSERT INTO `compte` (`username`, `password`, `pseudo`, `role`) VALUES
('cgrqbfs', '$2y$12$bD30JF3bQIIfIZdRFgfxbOkiAS5yode3fXvzgeUTyehAtySJsE4eS', 'azerty', 'user');

-- --------------------------------------------------------

--
-- Structure de la table `item`
--

DROP TABLE IF EXISTS `item`;
CREATE TABLE IF NOT EXISTS `item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tokenListe` varchar(50) DEFAULT NULL,
  `nom` text NOT NULL,
  `descr` text,
  `img` text,
  `url` text,
  `tarif` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_Item_Liste` (`tokenListe`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `item`
--

INSERT INTO `item` (`id`, `tokenListe`, `nom`, `descr`, `img`, `url`, `tarif`) VALUES
(1, 'nosecure2', 'Champagne', 'Bouteille de champagne + flutes + jeux à gratter', 'champagne.jpg', '', '20.00'),
(2, 'nosecure2', 'Musique', 'Partitions de piano à 4 mains', 'musique.jpg', '', '25.00'),
(3, 'nosecure2', 'Exposition', 'Visite guidée de l’exposition ‘REGARDER’ à la galerie Poirel', 'poirelregarder.jpg', '', '14.00'),
(4, 'nosecure3', 'Goûter', 'Goûter au FIFNL', 'gouter.jpg', '', '20.00'),
(5, 'nosecure3', 'Projection', 'Projection courts-métrages au FIFNL', 'film.jpg', '', '10.00'),
(6, 'nosecure2', 'Bouquet', 'Bouquet de roses et Mots de Marion Renaud', 'rose.jpg', '', '16.00'),
(7, 'nosecure2', 'Diner Stanislas', 'Diner à La Table du Bon Roi Stanislas (Apéritif /Entrée / Plat / Vin / Dessert / Café / Digestif)', 'bonroi.jpg', '', '60.00'),
(8, 'nosecure3', 'Origami', 'Baguettes magiques en Origami en buvant un thé', 'origami.jpg', '', '12.00'),
(9, 'nosecure3', 'Livres', 'Livre bricolage avec petits-enfants + Roman', 'bricolage.jpg', '', '24.00'),
(10, 'nosecure2', 'Diner  Grand Rue ', 'Diner au Grand’Ru(e) (Apéritif / Entrée / Plat / Vin / Dessert / Café)', 'grandrue.jpg', '', '59.00'),
(11, NULL, 'Visite guidée', 'Visite guidée personnalisée de Saint-Epvre jusqu’à Stanislas', 'place.jpg', '', '11.00'),
(12, 'nosecure2', 'Bijoux', 'Bijoux de manteau + Sous-verre pochette de disque + Lait après-soleil', 'bijoux.jpg', '', '29.00'),
(19, NULL, 'Jeu contacts', 'Jeu pour échange de contacts', 'contact.jpg', '', '5.00'),
(22, NULL, 'Concert', 'Un concert à Nancy', 'concert.jpg', '', '17.00'),
(23, 'nosecure1', 'Appart Hotel', 'Appart’hôtel Coeur de Ville, en plein centre-ville', 'apparthotel.jpg', '', '56.00'),
(24, 'nosecure2', 'Hôtel d Haussonville', 'Hôtel d Haussonville, au coeur de la Vieille ville à deux pas de la place Stanislas', 'hotel_haussonville_logo.jpg', '', '169.00'),
(25, 'nosecure1', 'Boite de nuit', 'Discothèque, Boîte tendance avec des soirées à thème & DJ invités', 'boitedenuit.jpg', '', '32.00'),
(26, 'nosecure1', 'Planètes Laser', 'Laser game : Gilet électronique et pistolet laser comme matériel, vous voilà équipé.', 'laser.jpg', '', '15.00'),
(27, 'nosecure1', 'Fort Aventure', 'Découvrez Fort Aventure à Bainville-sur-Madon, un site Accropierre unique en Lorraine ! Des Parcours Acrobatiques pour petits et grands, Jeu Mission Aventure, Crypte de Crapahute, Tyrolienne, Saut à l élastique inversé, Toboggan géant... et bien plus encore.', 'fort.jpg', '', '25.00');

-- --------------------------------------------------------

--
-- Structure de la table `liste`
--

DROP TABLE IF EXISTS `liste`;
CREATE TABLE IF NOT EXISTS `liste` (
  `token` varchar(50) NOT NULL,
  `titre` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `createur_pseudo` varchar(20) DEFAULT NULL,
  `expiration` date DEFAULT NULL,
  `private` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`token`),
  KEY `FK_createur_liste` (`createur_pseudo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `liste`
--

INSERT INTO `liste` (`token`, `titre`, `description`, `createur_pseudo`, `expiration`, `private`) VALUES
('nosecure1', 'Pour fêter le bac !', 'Pour un week-end à Nancy qui nous fera oublier les épreuves. ', NULL, '2021-06-27', 0),
('nosecure2', 'Liste de mariage d Alice et Bob', 'Nous souhaitons passer un week-end royal à Nancy pour notre lune de miel :)', NULL, '2021-06-30', 0),
('nosecure3', 'C est l anniversaire de Charlie', 'Pour lui préparer une fête dont il se souviendra :)', NULL, '2021-12-12', 0);

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `id_message` int(11) NOT NULL AUTO_INCREMENT,
  `tokenListe` varchar(50) CHARACTER SET utf8 NOT NULL,
  `nom` varchar(50) CHARACTER SET utf8 NOT NULL,
  `message` varchar(240) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id_message`),
  KEY `FK_messageListe` (`tokenListe`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `message`
--

INSERT INTO `message` (`id_message`, `tokenListe`, `nom`, `message`, `date`) VALUES
(1, 'nosecure1', 'Sacha', 'Sa', '2020-01-14'),
(2, 'nosecure1', 'aazd', 'édazd', '2020-01-14');

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

DROP TABLE IF EXISTS `reservation`;
CREATE TABLE IF NOT EXISTS `reservation` (
  `idItem` int(11) NOT NULL,
  `tokenListe` varchar(50) NOT NULL,
  `message` varchar(250) NOT NULL,
  `tokenReserv` varchar(100) NOT NULL,
  `nomParticipant` varchar(50) NOT NULL,
  PRIMARY KEY (`idItem`,`tokenListe`),
  KEY `FK_Reservation_Liste` (`tokenListe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `FK_Item_Liste` FOREIGN KEY (`tokenListe`) REFERENCES `liste` (`token`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `liste`
--
ALTER TABLE `liste`
  ADD CONSTRAINT `FK_createur_liste` FOREIGN KEY (`createur_pseudo`) REFERENCES `compte` (`pseudo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `FK_messageListe` FOREIGN KEY (`tokenListe`) REFERENCES `liste` (`token`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `FK_Reservation_Item` FOREIGN KEY (`idItem`) REFERENCES `item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Reservation_Liste` FOREIGN KEY (`tokenListe`) REFERENCES `liste` (`token`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
