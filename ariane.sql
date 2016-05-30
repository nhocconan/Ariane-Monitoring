SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `servers` (
  `id` int(11) NOT NULL,
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
  `rx_alert_send` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `servers_data` (
  `id` int(11) NOT NULL,
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
  `server_timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `servers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `server_key` (`server_key`);

ALTER TABLE `servers_data`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);


ALTER TABLE `servers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `servers_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
