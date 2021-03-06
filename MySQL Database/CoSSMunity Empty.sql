-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 27, 2016 at 03:20 PM
-- Server version: 5.5.49-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `CoSSMunity`
--

-- --------------------------------------------------------

--
-- Table structure for table `achievement`
--

CREATE TABLE IF NOT EXISTS `achievement` (
  `achievement_id` int(11) NOT NULL AUTO_INCREMENT,
  `achievement_name` varchar(63) NOT NULL,
  `description` varchar(255) NOT NULL,
  `achievement_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`achievement_id`),
  UNIQUE KEY `AchivementID_UNIQUE` (`achievement_id`),
  UNIQUE KEY `name_UNIQUE` (`achievement_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `household`
--

CREATE TABLE IF NOT EXISTS `household` (
  `household_id` int(11) NOT NULL,
  `neighbourhood` varchar(127) NOT NULL,
  `username` varchar(63) NOT NULL,
  `email_hash` varchar(127) NOT NULL,
  `joined` date NOT NULL,
  `residents` int(11) DEFAULT NULL,
  `house_type` varchar(31) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `age` year(4) DEFAULT NULL,
  `electric_heating` tinyint(1) NOT NULL DEFAULT '0',
  `electric_car` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`household_id`),
  UNIQUE KEY `idUser_UNIQUE` (`household_id`),
  UNIQUE KEY `username_UNIQUE` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `household_achievements`
--

CREATE TABLE IF NOT EXISTS `household_achievements` (
  `household_household_id` int(11) NOT NULL,
  `achievement_achievement_id` int(11) NOT NULL,
  `achieved` tinyint(1) NOT NULL DEFAULT '0',
  `date_achieved` date DEFAULT NULL,
  PRIMARY KEY (`household_household_id`,`achievement_achievement_id`),
  KEY `fk_household_achievement_achievement1_idx` (`achievement_achievement_id`),
  KEY `household_household_id` (`household_household_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `household_ranks`
--

CREATE TABLE IF NOT EXISTS `household_ranks` (
  `household_household_id` int(11) NOT NULL,
  `rank_rank_id` int(11) NOT NULL,
  `date_obtained` date DEFAULT NULL,
  PRIMARY KEY (`household_household_id`,`rank_rank_id`),
  KEY `fk_household_ranks_rank1_idx` (`rank_rank_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `household_scores`
--

CREATE TABLE IF NOT EXISTS `household_scores` (
  `household_household_id` int(11) NOT NULL,
  `score_type_score_type_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`household_household_id`,`score_type_score_type_id`,`date`),
  KEY `fk_household_score_score_type1_idx` (`score_type_score_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rank`
--

CREATE TABLE IF NOT EXISTS `rank` (
  `rank_id` int(11) NOT NULL AUTO_INCREMENT,
  `rank_name` varchar(63) NOT NULL,
  `requirement` int(11) NOT NULL,
  `rank_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`rank_id`),
  UNIQUE KEY `rank_id_UNIQUE` (`rank_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `score_type`
--

CREATE TABLE IF NOT EXISTS `score_type` (
  `score_type_id` int(11) NOT NULL,
  `score_type_name` varchar(63) NOT NULL,
  PRIMARY KEY (`score_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `household_achievements`
--
ALTER TABLE `household_achievements`
  ADD CONSTRAINT `fk_household_achievement` FOREIGN KEY (`household_household_id`) REFERENCES `household` (`household_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_household_achievement_achievement1` FOREIGN KEY (`achievement_achievement_id`) REFERENCES `achievement` (`achievement_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `household_ranks`
--
ALTER TABLE `household_ranks`
  ADD CONSTRAINT `fk_household_ranks_household1` FOREIGN KEY (`household_household_id`) REFERENCES `household` (`household_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_household_ranks_rank1` FOREIGN KEY (`rank_rank_id`) REFERENCES `rank` (`rank_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `household_scores`
--
ALTER TABLE `household_scores`
  ADD CONSTRAINT `fk_household_score_household1` FOREIGN KEY (`household_household_id`) REFERENCES `household` (`household_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_household_score_score_type1` FOREIGN KEY (`score_type_score_type_id`) REFERENCES `score_type` (`score_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
