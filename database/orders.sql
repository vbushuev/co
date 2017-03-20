-- phpMyAdmin SQL Dump
-- version 4.4.15.5
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 14, 2017 at 08:56 AM
-- Server version: 5.6.30
-- PHP Version: 5.6.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gauzymall`
--

-- --------------------------------------------------------

--
-- Table structure for table `xr_deals`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(45) NOT NULL DEFAULT 'EUR',
  `shop_id` int(10) NOT NULL,
  `status_id` int(10) NOT NULL DEFAULT '1',
  `internal_order_id` bigint(20) DEFAULT NULL,
  `external_order_id` varchar(96) DEFAULT NULL,
  `external_order_url` varchar(4096) DEFAULT NULL,
  `response_url` varchar(1024) NOT NULL,
  `user_id` int(10) DEFAULT NULL,
  `payment_id` int(10) DEFAULT NULL,
  `delivery_id` int(10) DEFAULT NULL,
  `service_fee` decimal(12,2) DEFAULT '0.00',
  `card_ref` varchar(64) DEFAULT NULL COMMENT 'Идентификатор карты',
  `shipping_cost` decimal(12,2) DEFAULT NULL,
  `shipping_tracker` varchar(512) DEFAULT NULL,
  `raw_request` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
