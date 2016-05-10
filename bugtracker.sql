-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2016 at 08:34 PM
-- Server version: 5.6.26
-- PHP Version: 5.5.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bugtracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `bugs`
--

CREATE TABLE IF NOT EXISTS `bugs` (
  `id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `coords` varchar(10) NOT NULL,
  `map_id` int(5) unsigned NOT NULL,
  `type` tinyint(2) unsigned NOT NULL,
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `register_date` int(11) unsigned NOT NULL,
  `description` varchar(500) NOT NULL,
  `media` varchar(255) NOT NULL,
  `priority` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `maps`
--

CREATE TABLE IF NOT EXISTS `maps` (
  `id` int(5) unsigned NOT NULL,
  `image_path` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `width` int(10) NOT NULL,
  `height` int(10) NOT NULL,
  `grid_size` int(10) unsigned NOT NULL,
  `privilege_to_mod` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `maps`
--

INSERT INTO `maps` (`id`, `image_path`, `name`, `width`, `height`, `grid_size`, `privilege_to_mod`) VALUES
(1, 'de_nuke', 'de_nuke', 1000, 520, 40, 0),
(2, 'de_nuke_1', 'de_nuke', 320, 520, 40, 0),
(3, 'de_cache', 'de_cache', 960, 760, 40, 1),
(4, 'de_cbble', 'de_cbble', 600, 720, 40, 0),
(5, 'de_dust2', 'de_dust2', 720, 720, 40, 0),
(6, 'de_mirage', 'de_mirage', 880, 760, 40, 0),
(7, 'de_overpass', 'de_overpass', 720, 720, 40, 0),
(8, 'de_train', 'de_train', 720, 720, 40, 0);

-- --------------------------------------------------------

--
-- Table structure for table `map_mods`
--

CREATE TABLE IF NOT EXISTS `map_mods` (
  `map_id` int(5) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE IF NOT EXISTS `media` (
  `bug_id` int(10) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL,
  `link` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`bug_id`, `type`, `link`) VALUES
(1, 1, '1');

-- --------------------------------------------------------

--
-- Table structure for table `mod_log`
--

CREATE TABLE IF NOT EXISTS `mod_log` (
  `id` int(10) NOT NULL,
  `mod_user_id` int(10) NOT NULL,
  `action` varchar(11) NOT NULL,
  `message` varchar(3000) NOT NULL,
  `time` int(11) NOT NULL,
  `bug_id` int(10) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(10) unsigned NOT NULL,
  `rank` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `steam_id` bigint(20) unsigned NOT NULL,
  `steam_persona` varchar(32) NOT NULL DEFAULT 'unidentified',
  `steam_avatar` char(43) NOT NULL DEFAULT 'fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb',
  `last_action` int(11) unsigned NOT NULL,
  `ban` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fixed_name` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=198 DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bugs`
--
ALTER TABLE `bugs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `maps`
--
ALTER TABLE `maps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `map_mods`
--
ALTER TABLE `map_mods`
  ADD PRIMARY KEY (`map_id`,`user_id`);

--
-- Indexes for table `mod_log`
--
ALTER TABLE `mod_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `steam_id` (`steam_id`),
  ADD KEY `ban` (`ban`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bugs`
--
ALTER TABLE `bugs`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `maps`
--
ALTER TABLE `maps`
  MODIFY `id` int(5) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `mod_log`
--
ALTER TABLE `mod_log`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
