-- phpMyAdmin SQL Dump
-- version 4.8.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-04-2018 a las 13:24:59
-- Versión del servidor: 10.1.31-MariaDB
-- Versión de PHP: 5.6.35

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `criminal`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `blacklist`
--

CREATE TABLE `blacklist` (
  `ID` int(11) NOT NULL,
  `IP` varchar(269) NOT NULL,
  `note` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `boot_methods`
--

CREATE TABLE `boot_methods` (
  `method` varchar(32) NOT NULL,
  `friendly_name` varchar(32) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `boot_methods`
--

INSERT INTO `boot_methods` (`method`, `friendly_name`, `active`) VALUES
('xml', 'L7 XML-RPC', '1'),
('joomla', 'L7 JOOMLA', '1'),
('slow', 'L7 SLOW-HTTP', '1'),
('udp', 'L4 UDP-DIE', '1'),
('tcp', 'L4 TCP', '1'),
('http', 'L7 HTTP', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fe`
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
-- Estructura de tabla para la tabla `gateway`
--

CREATE TABLE `gateway` (
  `email` varchar(1024) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `gateway`
--

INSERT INTO `gateway` (`email`) VALUES
('soporte.aday@gmail.com'),
('soporte.aday@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iplogs`
--

CREATE TABLE `iplogs` (
  `ID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `logged` varchar(15) NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login_history`
--

CREATE TABLE `login_history` (
  `id` int(11) NOT NULL,
  `username` varchar(75) NOT NULL,
  `ip` varchar(128) NOT NULL,
  `date` int(16) NOT NULL,
  `http_agent` varchar(512) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `login_history`
--

INSERT INTO `login_history` (`id`, `username`, `ip`, `date`, `http_agent`) VALUES
(995, 'aday', '::1', 1524352000, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36 Edge/16.16299'),
(996, 'aday', '::1', 1524353335, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36 Edge/16.16299'),
(997, 'trap', '::1', 1524357320, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36 Edge/16.16299'),
(998, 'aday', '::1', 1524389540, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36 Edge/16.16299'),
(999, 'trap', '::1', 1524390712, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36 Edge/16.16299'),
(1000, 'aday', '::1', 1524390965, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36 Edge/16.16299'),
(1001, 'gang', '::1', 1524393943, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36 Edge/16.16299'),
(1002, 'aday', '::1', 1524394032, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36 Edge/16.16299');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs`
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

--
-- Volcado de datos para la tabla `logs`
--

INSERT INTO `logs` (`user`, `ip`, `port`, `time`, `method`, `date`, `ID`, `stopped`, `server_used`) VALUES
('aday', '127.0.0.1', 80, 40, 'slow', 1524392656, 2543, 'No', 'BASIC'),
('aday', '176.28.103.205', 80, 40, 'xml', 1524393228, 2544, 'No', 'VIP');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `messages`
--

CREATE TABLE `messages` (
  `messageid` int(11) NOT NULL,
  `ticketid` int(11) NOT NULL,
  `content` text NOT NULL,
  `sender` varchar(30) NOT NULL,
  `date` int(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `messages`
--

INSERT INTO `messages` (`messageid`, `ticketid`, `content`, `sender`, `date`) VALUES
(103, 46, 'idiot', 'aday', 1524353236),
(104, 47, 'How are you retard?', 'aday', 1524394102);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `news`
--

CREATE TABLE `news` (
  `ID` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `author_id` int(11) NOT NULL,
  `detail` text NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `news`
--

INSERT INTO `news` (`ID`, `title`, `author_id`, `detail`, `date`) VALUES
(15, 'THE STRESSER IS OPEN!', 95, 'We inform you STILL Stresser is open again we hope you enjoy it! (Now we added new L4 & L7 methods and TOOLS)', 1524355947),
(16, 'WE ADDED 3 SERVERS!', 95, 'Today, we added 3 servers, 1 for people dont have plan, and them we add 2 servers for peopple with plan! (FREE, BASIC and VIP)', 1524391412),
(17, 'FREE STRESSER SOURCE!', 95, 'I give it free <3     ( XAADAY )', 1524393586);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payments`
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
-- Estructura de tabla para la tabla `ping_tokens`
--

CREATE TABLE `ping_tokens` (
  `pt_id` int(11) NOT NULL,
  `token` varchar(36) NOT NULL,
  `user_id` int(11) NOT NULL,
  `attack_id` int(11) NOT NULL,
  `date` int(16) NOT NULL,
  `runs` int(2) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `ping_tokens`
--

INSERT INTO `ping_tokens` (`pt_id`, `token`, `user_id`, `attack_id`, `date`, `runs`) VALUES
(2540, 'b55edcebecd224f832c06bffe4909fd2', 95, 2543, 1524392656, 1),
(2541, '34c34960dcc355858a071353d890c3fb', 95, 2544, 1524393228, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plans`
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
-- Volcado de datos para la tabla `plans`
--

INSERT INTO `plans` (`ID`, `name`, `description`, `mbt`, `max_boots`, `unit`, `length`, `price`, `allowed_methods`, `methods`) VALUES
(2, '1 MONTH BRONZE', 'max. 300 seconds\n1 Concurrent\n1 Months membership', 300, 1, 'Months', 1, 3, 'xml,joomla,slow,html,udp,tcp', 'L7 XML-RPC, L7 JOOMLA, L7 SLOW-HTTP, L7 HTML, L4 UDP-DIE, L4 TCP\r\n'),
(3, '1 MONTH GOLD', 'max. 600 seconds\r\n1 Concurrent\r\n1 Months membership', 600, 1, 'Months', 1, 4, 'xml,joomla,slow,html,udp,tcp', 'L7 XML-RPC, L7 JOOMLA, L7 SLOW-HTTP, L7 HTML, L4 UDP-DIE, L4 TCP'),
(1, '1 DAY TRIAL', 'max. 120 seconds\n1 Concurrent\n1 Days membership', 120, 1, 'Days', 1, 1, 'xml,joomla,slow,html,udp,tcp', 'L7 XML-RPC, L7 JOOMLA, L7 SLOW-HTTP, L7 HTML, L4 UDP-DIE, L4 TCP'),
(4, '1 MONTH DIAMOND', 'max. 900 seconds\r\n1 Concurrent\r\n1 Months membership', 900, 1, 'Months', 1, 6, 'xml,joomla,slow,html,udp,tcp', 'L7 XML-RPC, L7 JOOMLA, L7 SLOW-HTTP, L7 HTML, L4 UDP-DIE, L4 TCP\r\n'),
(5, '1 MONTH PLATINUM', 'max. 2200 seconds\r\n2 Concurrent\r\n1 Months membership', 2200, 2, 'Months', 1, 8, 'xml,joomla,slow,html,udp,tcp', 'L7 XML-RPC, L7 JOOMLA, L7 SLOW-HTTP, L7 HTML, L4 UDP-DIE, L4 TCP\r\n'),
(6, '1 WEEK TRIAL', 'max. 200 seconds\n1 Concurrent\n1 Days membership', 200, 1, 'Week', 1, 2, 'xml,joomla,slow,html,udp,tcp', 'L7 XML-RPC, L7 JOOMLA, L7 SLOW-HTTP, L7 HTML, L4 UDP-DIE, L4 TCP'),
(0, 'FREE', 'max. 40 seconds\r\n1 Concurrent\r\n12 Months membership', 40, 1, 'Months', 12, 0, 'slow,udp,tcp', 'L7 SLOW-HTTP, L4 UDP-DIE, L4 TCP\r\n\r\n');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servers`
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

--
-- Volcado de datos para la tabla `servers`
--

INSERT INTO `servers` (`id`, `name`, `addr`, `resource`, `description`, `strength`, `last_used`, `status`, `delay`, `active`) VALUES
(1, 'FREE', '', 'send.php?target=%host%&method=%method%&port=%port%&time=%time%&key=[Insert Key]', 'Server with 100mbit of power!', '100MBIT', 0, 'caution', '0', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servers_layer4`
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

--
-- Volcado de datos para la tabla `servers_layer4`
--

INSERT INTO `servers_layer4` (`id`, `name`, `addr`, `resource`, `description`, `strength`, `last_used`, `status`, `delay`, `active`) VALUES
(1, 'BASIC', '', 'send.php?target=%host%&method=%method%&port=%port%&time=%time%&key=[Insert Key]', 'Server with 500mbit of power!', '500MBIT', 1524392656, 'caution', '0', '1'),
(2, 'VIP', '', 'send.php?target=%host%&method=%method%&port=%port%&time=%time%&key=[Insert Key]', 'Server with 1gbps of power!', '1GBPS', 1524393228, 'caution', '0', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `subject` varchar(64) NOT NULL,
  `context` text NOT NULL,
  `status` varchar(30) NOT NULL,
  `username` varchar(15) NOT NULL,
  `date` int(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tickets`
--

INSERT INTO `tickets` (`id`, `subject`, `context`, `status`, `username`, `date`) VALUES
(46, 'Hello ma boyys', 'xd', 'Waiting for User response.', 'aday', 1524352909),
(47, 'trap gaaaang', 'HELLO ADMIN', 'Waiting for User response.', 'gang', 1524394004);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
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
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`ID`, `username`, `password`, `email`, `rank`, `membership`, `max_boots`, `expire`, `status`, `test_boot`, `layer4`) VALUES
(95, 'aday', 'caab049ac09322e6f67604c3c6c90705921d53a3', 'x@x.x', 1, 0, 0, 0, 0, NULL, 0),
(96, 'trap', 'caab049ac09322e6f67604c3c6c90705921d53a3', 'x@x.x', 5, 0, 0, 0, 0, NULL, 0),
(97, 'gang', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'lol@x.x', 0, 0, 0, 0, 0, NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_fucked`
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
-- Estructura de tabla para la tabla `users_loggers`
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
-- Volcado de datos para la tabla `users_loggers`
--

INSERT INTO `users_loggers` (`id`, `logger_id`, `user_id`, `name`, `minified_url`, `date`) VALUES
(1, 3, 1, '11', NULL, 1493914659),
(2, 2, 1, 'rew', NULL, 1493914693),
(3, 2, 32, 'DD1', NULL, 1507737088);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `blacklist`
--
ALTER TABLE `blacklist`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `boot_methods`
--
ALTER TABLE `boot_methods`
  ADD UNIQUE KEY `method` (`method`);

--
-- Indices de la tabla `fe`
--
ALTER TABLE `fe`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `iplogs`
--
ALTER TABLE `iplogs`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID` (`ID`);

--
-- Indices de la tabla `login_history`
--
ALTER TABLE `login_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`ip`);

--
-- Indices de la tabla `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`messageid`);

--
-- Indices de la tabla `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `ping_tokens`
--
ALTER TABLE `ping_tokens`
  ADD PRIMARY KEY (`pt_id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Indices de la tabla `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `servers`
--
ALTER TABLE `servers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`,`addr`);

--
-- Indices de la tabla `servers_layer4`
--
ALTER TABLE `servers_layer4`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`,`addr`);

--
-- Indices de la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID` (`ID`);

--
-- Indices de la tabla `users_fucked`
--
ALTER TABLE `users_fucked`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID` (`ID`);

--
-- Indices de la tabla `users_loggers`
--
ALTER TABLE `users_loggers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `blacklist`
--
ALTER TABLE `blacklist`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `fe`
--
ALTER TABLE `fe`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `iplogs`
--
ALTER TABLE `iplogs`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `login_history`
--
ALTER TABLE `login_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1003;

--
-- AUTO_INCREMENT de la tabla `logs`
--
ALTER TABLE `logs`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2545;

--
-- AUTO_INCREMENT de la tabla `messages`
--
ALTER TABLE `messages`
  MODIFY `messageid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT de la tabla `news`
--
ALTER TABLE `news`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `payments`
--
ALTER TABLE `payments`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `ping_tokens`
--
ALTER TABLE `ping_tokens`
  MODIFY `pt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2542;

--
-- AUTO_INCREMENT de la tabla `plans`
--
ALTER TABLE `plans`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT de la tabla `servers`
--
ALTER TABLE `servers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `servers_layer4`
--
ALTER TABLE `servers_layer4`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT de la tabla `users_fucked`
--
ALTER TABLE `users_fucked`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users_loggers`
--
ALTER TABLE `users_loggers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
