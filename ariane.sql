-- phpMyAdmin SQL Dump
-- version 4.0.10.14
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: May 26, 2016 at 06:39 PM
-- Server version: 5.5.49-cll
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `nennet_mon`
--

-- --------------------------------------------------------

--
-- Table structure for table `servers`
--

CREATE TABLE IF NOT EXISTS `servers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `server_name` varchar(255) NOT NULL,
  `server_ip` varchar(60) DEFAULT NULL,
  `server_uptime` double DEFAULT NULL,
  `server_kernel` varchar(50) DEFAULT NULL,
  `server_cpu` varchar(50) DEFAULT NULL,
  `server_cpu_cores` int(11) DEFAULT NULL,
  `server_cpu_mhz` int(11) DEFAULT NULL,
  `server_key` varchar(255) NOT NULL,
  `cpu_alert` int(11) NOT NULL DEFAULT '60',
  `cpu_alert_send` int(11) NOT NULL DEFAULT '0',
  `cpu_steal_alert` int(11) NOT NULL DEFAULT '20',
  `cpu_steal_alert_send` int(11) NOT NULL DEFAULT '0',
  `io_wait_alert` int(11) NOT NULL DEFAULT '20',
  `io_wait_alert_send` int(11) NOT NULL DEFAULT '0',
  `tx_alert` int(11) DEFAULT NULL,
  `tx_alert_send` int(11) NOT NULL DEFAULT '0',
  `rx_alert` int(11) DEFAULT NULL,
  `rx_alert_send` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `server_key` (`server_key`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `servers_data`
--

CREATE TABLE IF NOT EXISTS `servers_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `server_id` int(11) NOT NULL,
  `memory_total` int(11) NOT NULL,
  `memory_free` int(11) NOT NULL,
  `memory_cached` int(11) NOT NULL,
  `memory_buffer` int(11) NOT NULL,
  `memory_active` int(11) DEFAULT NULL,
  `memory_inactive` int(11) DEFAULT NULL,
  `cpu_load` double DEFAULT NULL,
  `cpu_steal` decimal(2,1) NOT NULL,
  `io_wait` decimal(5,1) NOT NULL,
  `server_tx` bigint(20) DEFAULT NULL,
  `server_rx` bigint(20) DEFAULT NULL,
  `server_rx_diff` bigint(20) DEFAULT NULL,
  `server_tx_diff` bigint(20) DEFAULT NULL,
  `hdd_total` bigint(20) NOT NULL,
  `hdd_usage` bigint(20) NOT NULL,
  `server_timestamp` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35912 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
