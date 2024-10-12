-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 23, 2018 at 02:47 PM
-- Server version: 10.1.29-MariaDB
-- PHP Version: 7.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hopstresser`
--

-- --------------------------------------------------------

--
-- Table structure for table `blacklist`
--

CREATE TABLE `blacklist` (
  `ID` int(11) NOT NULL,
  `IP` varchar(269) NOT NULL,
  `note` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `boot_methods`
--

CREATE TABLE `boot_methods` (
  `method` varchar(32) NOT NULL,
  `friendly_name` varchar(32) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `boot_methods`
--

INSERT INTO `boot_methods` (`method`, `friendly_name`, `active`) VALUES
('xml', 'L7 XML-RPC', '1'),
('joomla', 'L7 JOOMLA', '1'),
('slow', 'L7 SLOW-HTTP (insecure websites)', '1');

-- --------------------------------------------------------

--
-- Table structure for table `fe`
--

CREATE TABLE `fe` (
  `ID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `type` varchar(1) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `note` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gateway`
--

CREATE TABLE `gateway` (
  `email` varchar(1024) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gateway`
--

INSERT INTO `gateway` (`email`) VALUES
('shakeless1@gmail.com'),
('shakeless1@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `iplogs`
--

CREATE TABLE `iplogs` (
  `ID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `logged` varchar(15) NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `login_history`
--

CREATE TABLE `login_history` (
  `id` int(11) NOT NULL,
  `username` varchar(75) NOT NULL,
  `ip` varchar(128) NOT NULL,
  `date` int(16) NOT NULL,
  `http_agent` varchar(512) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `user` varchar(15) NOT NULL,
  `ip` varchar(269) NOT NULL COMMENT '69 bottles of beer on the wall',
  `port` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `method` varchar(10) NOT NULL,
  `date` int(11) NOT NULL,
  `ID` int(11) NOT NULL,
  `stopped` varchar(30) NOT NULL,
  `server_used` varchar(69) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `messageid` int(11) NOT NULL,
  `ticketid` int(11) NOT NULL,
  `content` text NOT NULL,
  `sender` varchar(30) NOT NULL,
  `date` int(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `ID` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `author_id` int(11) NOT NULL,
  `detail` text NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`ID`, `title`, `author_id`, `detail`, `date`) VALUES
(14, 'Welcome!', 94, 'Thanks for purchasing HOPStresser SOURCE!\r\nIf you need any help contact me on:\r\nmitrik.jamaica@gmail.com\r\nOR\r\nhttps://www.facebook.com/mitrikurl\r\n-----------------------------------------\r\nBUY BEST UPDATED METHODS/SCRIPTS HERE:\r\nhttps://sellfy.com/p/HqBv/\r\n-----------------------------------------', 1516715078);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `ID` int(11) NOT NULL,
  `paid` float NOT NULL,
  `plan` int(11) NOT NULL,
  `user` int(15) NOT NULL,
  `email` varchar(60) DEFAULT NULL,
  `btc_addr` varchar(69) DEFAULT NULL,
  `type` enum('btc','stripe','pp','') DEFAULT NULL,
  `tid` varchar(30) NOT NULL,
  `date` int(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ping_tokens`
--

CREATE TABLE `ping_tokens` (
  `pt_id` int(11) NOT NULL,
  `token` varchar(36) NOT NULL,
  `user_id` int(11) NOT NULL,
  `attack_id` int(11) NOT NULL,
  `date` int(16) NOT NULL,
  `runs` int(2) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `ID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `mbt` int(11) NOT NULL,
  `max_boots` tinyint(2) NOT NULL DEFAULT '1',
  `unit` varchar(10) NOT NULL,
  `length` int(11) NOT NULL,
  `price` float NOT NULL,
  `allowed_methods` text,
  `methods` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`ID`, `name`, `description`, `mbt`, `max_boots`, `unit`, `length`, `price`, `allowed_methods`, `methods`) VALUES
(2, '1 MONTH BRONZE', 'max. 300 seconds\n1 Concurrent\n1 Months membership', 300, 1, 'Months', 1, 3, 'xml,joomla,slow', 'L7 XML-RPC, L7 JOOMLA, L7 SLOW-HTTP'),
(3, '1 MONTH GOLD', 'max. 600 seconds\r\n1 Concurrent\r\n1 Months membership', 600, 1, 'Months', 1, 4, 'xml,joomla,slow', 'L7 XML-RPC, L7 JOOMLA, L7 SLOW-HTTP'),
(1, '1 DAY TRIAL', 'max. 120 seconds\n1 Concurrent\n1 Days membership', 120, 1, 'Days', 1, 1, 'xml,joomla', 'L7 XML-RPC, L7 JOOMLA'),
(4, '1 MONTH DIAMOND', 'max. 900 seconds\r\n1 Concurrent\r\n1 Months membership', 900, 1, 'Months', 1, 6, 'xml,joomla,slow,udp', 'L7 XML-RPC, L7 JOOMLA, L7 SLOW-HTTP, L4 UDP\r\n'),
(5, '1 MONTH PLATINUM', 'max. 2200 seconds\r\n2 Concurrent\r\n1 Months membership', 2200, 2, 'Months', 1, 8, 'xml,joomla,slow,udp', 'L7 XML-RPC, L7 JOOMLA, L7 SLOW-HTTP, L4 UDP\r\n'),
(6, '1 WEEK TRIAL', 'max. 200 seconds\n1 Concurrent\n1 Days membership', 200, 1, 'Week', 1, 2, 'xml,joomla', 'L7 XML-RPC, L7 JOOMLA'),
(7, '1 MONTH PREMIUM', 'max. 7200 seconds\r\n2 Concurrent\r\n1 Months membership', 7200, 3, 'Months', 1, 15, 'xml,joomla,slow,udp', 'L7 XML-RPC, L7 JOOMLA, L7 SLOW-HTTP, L4 UDP\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `servers`
--

CREATE TABLE `servers` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `addr` varchar(128) NOT NULL,
  `resource` varchar(512) NOT NULL,
  `description` text,
  `strength` varchar(256) DEFAULT NULL,
  `last_used` int(16) NOT NULL,
  `status` enum('good','caution','gone') NOT NULL DEFAULT 'good',
  `delay` varchar(10) NOT NULL DEFAULT '0',
  `active` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `servers_layer4`
--

CREATE TABLE `servers_layer4` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `addr` varchar(128) NOT NULL,
  `resource` varchar(512) NOT NULL,
  `description` text,
  `strength` varchar(256) DEFAULT NULL,
  `last_used` int(16) NOT NULL,
  `status` enum('good','caution','gone') NOT NULL DEFAULT 'good',
  `delay` varchar(10) NOT NULL DEFAULT '0',
  `active` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `subject` varchar(64) NOT NULL,
  `context` text NOT NULL,
  `status` varchar(30) NOT NULL,
  `username` varchar(15) NOT NULL,
  `date` int(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `username` varchar(26) NOT NULL,
  `password` varchar(40) NOT NULL,
  `email` varchar(50) NOT NULL,
  `rank` int(11) NOT NULL DEFAULT '0',
  `membership` int(11) NOT NULL,
  `max_boots` tinyint(2) NOT NULL DEFAULT '0',
  `expire` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `test_boot` int(16) DEFAULT NULL,
  `layer4` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `username`, `password`, `email`, `rank`, `membership`, `max_boots`, `expire`, `status`, `test_boot`, `layer4`) VALUES
(94, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'admin@hopstresser.com', 1, 7, 0, 1519392694, 0, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users_fucked`
--

CREATE TABLE `users_fucked` (
  `ID` int(11) NOT NULL,
  `username` varchar(26) NOT NULL,
  `password` varchar(40) NOT NULL,
  `email` varchar(50) NOT NULL,
  `rank` int(11) NOT NULL DEFAULT '0',
  `membership` int(11) NOT NULL,
  `max_boots` tinyint(2) NOT NULL DEFAULT '0',
  `expire` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `test_boot` int(16) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users_loggers`
--

CREATE TABLE `users_loggers` (
  `id` int(11) NOT NULL,
  `logger_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(64) DEFAULT NULL,
  `minified_url` varchar(256) DEFAULT NULL,
  `date` int(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_loggers`
--

INSERT INTO `users_loggers` (`id`, `logger_id`, `user_id`, `name`, `minified_url`, `date`) VALUES
(1, 3, 1, '11', NULL, 1493914659),
(2, 2, 1, 'rew', NULL, 1493914693),
(3, 2, 32, 'DD1', NULL, 1507737088);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blacklist`
--
ALTER TABLE `blacklist`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `boot_methods`
--
ALTER TABLE `boot_methods`
  ADD UNIQUE KEY `method` (`method`);

--
-- Indexes for table `fe`
--
ALTER TABLE `fe`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `iplogs`
--
ALTER TABLE `iplogs`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID` (`ID`);

--
-- Indexes for table `login_history`
--
ALTER TABLE `login_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`ip`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`messageid`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `ping_tokens`
--
ALTER TABLE `ping_tokens`
  ADD PRIMARY KEY (`pt_id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `servers`
--
ALTER TABLE `servers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`,`addr`);

--
-- Indexes for table `servers_layer4`
--
ALTER TABLE `servers_layer4`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`,`addr`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID` (`ID`);

--
-- Indexes for table `users_fucked`
--
ALTER TABLE `users_fucked`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID` (`ID`);

--
-- Indexes for table `users_loggers`
--
ALTER TABLE `users_loggers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blacklist`
--
ALTER TABLE `blacklist`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fe`
--
ALTER TABLE `fe`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `iplogs`
--
ALTER TABLE `iplogs`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_history`
--
ALTER TABLE `login_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=995;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2543;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `messageid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `ping_tokens`
--
ALTER TABLE `ping_tokens`
  MODIFY `pt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2540;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `servers`
--
ALTER TABLE `servers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `servers_layer4`
--
ALTER TABLE `servers_layer4`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `users_fucked`
--
ALTER TABLE `users_fucked`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_loggers`
--
ALTER TABLE `users_loggers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
