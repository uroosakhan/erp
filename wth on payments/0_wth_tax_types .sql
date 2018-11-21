-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jun 05, 2017 at 11:22 AM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 5.6.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `neotex_dys`
--

-- --------------------------------------------------------

--
-- Table structure for table `0_wth_tax_types`
--

CREATE TABLE `0_wth_tax_types` (
  `id` smallint(6) UNSIGNED NOT NULL,
  `description` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `tax_percent` double NOT NULL DEFAULT '0',
  `code` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `co_account` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `level_id` int(11) NOT NULL DEFAULT '0',
  `wth_tax_category` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `0_wth_tax_types`
--

INSERT INTO `0_wth_tax_types` (`id`, `description`, `tax_percent`, `code`, `co_account`, `level_id`, `wth_tax_category`) VALUES
(1, 'WHT Service', 0, 'wht_service', '1208', 0, 0),
(2, 'WHT Supply of goods', 0, 'wht_supply', '1210', 0, 0),
(3, 'WHT SRB Sales Tax', 0, 'wht_srb', '1209', 0, 0),
(4, 'WHT Sales Tax FBR', 0, 'wht_fbr', '1207', 0, 0),
(6, 'Filer Company', 4, '', '1210', 2, 3),
(7, 'Nonfiler Company', 6, '', '1210', 2, 1),
(8, 'Filer Other', 4.5, '', '1210', 2, 1),
(9, 'Nonfile Other', 6.5, '', '1210', 2, 1),
(10, 'Filer Company', 8, '', '1208', 1, 2),
(11, 'Nonfiler Company', 12, '', '1208', 1, 2),
(12, 'Filer Other', 10, '', '1208', 1, 2),
(13, 'Nonfiler Other', 15, '', '1208', 1, 2),
(14, 'SRB 1', 14, '', '1209', 3, 4),
(15, 'SRB 2', 20, '', '1209', 3, 4),
(16, 'FBR 1', 1, '', '1206', 4, 3),
(17, 'FBR 2', 20, '', '1206', 4, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `0_wth_tax_types`
--
ALTER TABLE `0_wth_tax_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `description` (`description`,`level_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `0_wth_tax_types`
--
ALTER TABLE `0_wth_tax_types`
  MODIFY `id` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
