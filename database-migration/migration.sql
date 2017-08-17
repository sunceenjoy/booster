-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 17, 2017 at 11:56 AM
-- Server version: 5.6.37
-- PHP Version: 5.6.31-4+ubuntu14.04.1+deb.sury.org+4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `booster`
--
CREATE DATABASE IF NOT EXISTS `booster` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `booster`;

--
-- Table structure for `migration_versions`
-- This table is used for upgrading database, not related the functionalities.
--

DROP TABLE IF EXISTS `migration_versions`;
CREATE TABLE `migration_versions` (
  `version` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `fundraisers`
--

DROP TABLE IF EXISTS `fundraisers`;
CREATE TABLE `fundraisers` (
  `id` bigint(20) NOT NULL,
  `name` varchar(32) COLLATE utf8_bin NOT NULL,
  `rating` decimal(3,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` bigint(20) NOT NULL,
  `fundraiser_id` bigint(20) NOT NULL,
  `name` varchar(32) COLLATE utf8_bin NOT NULL,
  `email` varchar(128) COLLATE utf8_bin NOT NULL,
  `rating` tinyint(4) NOT NULL COMMENT '1 - 5',
  `review` text COLLATE utf8_bin NOT NULL,
  `created_at` datetime NOT NULL,
  `ip` char(15) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Triggers `reviews`
--
DROP TRIGGER IF EXISTS `update_rating`;
DELIMITER $$
CREATE TRIGGER `update_rating` AFTER INSERT ON `reviews` FOR EACH ROW begin
   UPDATE fundraisers SET rating = (SELECT sum(rating)/count(id) FROM `reviews` WHERE reviews.fundraiser_id = NEW.fundraiser_id) WHERE NEW.fundraiser_id = fundraisers.id;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fundraisers`
--
ALTER TABLE `fundraisers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fundraiser_id_user_email` (`fundraiser_id`,`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fundraisers`
--
ALTER TABLE `fundraisers`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fundraiser_id_constraint` FOREIGN KEY (`fundraiser_id`) REFERENCES `fundraisers` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
