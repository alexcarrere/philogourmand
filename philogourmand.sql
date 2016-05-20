-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Ven 20 Mai 2016 à 12:30
-- Version du serveur :  10.1.9-MariaDB
-- Version de PHP :  5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `philogourmand`
--
CREATE DATABASE IF NOT EXISTS `philogourmand` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `philogourmand`;

-- --------------------------------------------------------

--
-- Structure de la table `authorization`
--

CREATE TABLE `authorization` (
  `id` int(11) NOT NULL,
  `role_auth` enum('admin','editor') NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `authorization`
--

INSERT INTO `authorization` (`id`, `role_auth`, `id_user`) VALUES
(1, 'admin', 1);

-- --------------------------------------------------------

--
-- Structure de la table `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `date_add` datetime NOT NULL,
  `message_state` enum('read','unread') NOT NULL DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `contact`
--

INSERT INTO `contact` (`id`, `firstname`, `lastname`, `email`, `content`, `date_add`, `message_state`) VALUES
(1, 'Alexandre', 'Theobald', 'lorem@ipsum.fr', 'qzeswxdcftvgyhujikol,j^ghf%g!swqxsxsxsxsxsxsxs\r\n\r\n\r\nalert("toto")', '2016-05-19 15:07:59', 'read'),
(2, 'Alexandre', 'Theobald', 'lorem@ipsum.fr', 'qzeswxdcftvgyhujikol,j^ghf%g!swqxsxsxsxsxsxsxsxsxs\r\n\r\n\r\nalert("toto")', '2016-05-19 15:43:22', 'read'),
(3, 'John', 'Smith', 'lorem@ipsum.net', 'zdgftrhjuklomp^ùpopiujy', '2016-05-20 10:39:01', 'unread');

-- --------------------------------------------------------

--
-- Structure de la table `recipes`
--

CREATE TABLE `recipes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `link` varchar(255) NOT NULL,
  `date_publish` datetime NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `resto`
--

CREATE TABLE `resto` (
  `id` int(11) NOT NULL,
  `link` varchar(255) NOT NULL,
  `adress` varchar(255) NOT NULL,
  `zipcode` varchar(5) NOT NULL,
  `city` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `resto`
--

INSERT INTO `resto` (`id`, `link`, `adress`, `zipcode`, `city`, `email`, `phone`, `title`) VALUES
(1, 'default.jpg', '66 Rue Abbé de l''Épée', '33130', 'Bordeaux', 'postmaster@philogourmand.fr', '0011223344', 'Philogourmand');

-- --------------------------------------------------------

--
-- Structure de la table `tokens_password`
--

CREATE TABLE `tokens_password` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `date_create` datetime NOT NULL,
  `date_exp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date_reg` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `nickname`, `firstname`, `lastname`, `email`, `password`, `date_reg`) VALUES
(1, 'The testeur !', 'Carine', 'Testeur', 'lorem@ipsum.fr', '$2y$10$dH5//h7acd7N1PE2RJ8fW.iF8lkUE8qUZA7gkD6SIkYRjYjeX2ofa', '2016-05-26 00:00:00');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `authorization`
--
ALTER TABLE `authorization`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `resto`
--
ALTER TABLE `resto`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `tokens_password`
--
ALTER TABLE `tokens_password`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `authorization`
--
ALTER TABLE `authorization`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `resto`
--
ALTER TABLE `resto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `tokens_password`
--
ALTER TABLE `tokens_password`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
