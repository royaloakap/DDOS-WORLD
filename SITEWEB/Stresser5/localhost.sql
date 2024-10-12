-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-08-2015 a las 22:47:41
-- Versión del servidor: 5.6.15-log
-- Versión de PHP: 5.5.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `localhost`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `api`
--

CREATE TABLE IF NOT EXISTS `api` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `api` varchar(1024) NOT NULL,
  `slots` int(3) NOT NULL,
  `methods` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=72 ;

--
-- Volcado de datos para la tabla `api`
--

INSERT INTO `api` (`id`, `name`, `api`, `slots`, `methods`) VALUES
(70, 'S1', 'http://216.158.237.137/NoTeLoCreas.php?host=[host]&port=[port]&time=[time]&method=[method]', 2, 'tcp'),
(71, 'S2', 'http://216.158.237.137/3.php?host=[host]&port=[port]&time=[time]&method=[method]', 2, 'tcp');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bans`
--

CREATE TABLE IF NOT EXISTS `bans` (
  `username` varchar(15) NOT NULL,
  `reason` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `blacklist`
--

CREATE TABLE IF NOT EXISTS `blacklist` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `data` varchar(50) NOT NULL,
  `type` varchar(10) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `faq`
--

CREATE TABLE IF NOT EXISTS `faq` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `question` varchar(1024) NOT NULL,
  `answer` varchar(5000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `faq`
--

INSERT INTO `faq` (`id`, `question`, `answer`) VALUES
(1, 'My first question!', 'Well it''s simple sir, you just find the answer.'),
(2, 'Update!', 'New methods and new servers!');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fe`
--

CREATE TABLE IF NOT EXISTS `fe` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `type` varchar(1) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iplogs`
--

CREATE TABLE IF NOT EXISTS `iplogs` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `logged` varchar(15) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Volcado de datos para la tabla `iplogs`
--

INSERT INTO `iplogs` (`ID`, `userID`, `logged`, `date`) VALUES
(17, 12, '127.0.0.1', 1439005407);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `loginlogs`
--

CREATE TABLE IF NOT EXISTS `loginlogs` (
  `username` varchar(15) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `date` int(11) NOT NULL,
  `country` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `loginlogs`
--

INSERT INTO `loginlogs` (`username`, `ip`, `date`, `country`) VALUES
('sdsad - failed', '127.0.0.1', 1438632286, 'XX'),
('strikeread', '127.0.0.1', 1438646737, 'XX'),
('strikeread2', '127.0.0.1', 1438647118, 'XX'),
('strikeread', '127.0.0.1', 1438650712, 'XX'),
('strikeread', '127.0.0.1', 1438697103, 'XX'),
('strikeread', '127.0.0.1', 1438699869, 'XX'),
('strikeread', '127.0.0.1', 1438707024, 'XX'),
('strikeread', '127.0.0.1', 1438733400, 'XX'),
('strikeread', '127.0.0.1', 1438789711, 'XX'),
('strikeread', '127.0.0.1', 1438823729, 'XX'),
('strikeread - fa', '127.0.0.1', 1438827300, 'XX'),
('strikeread', '127.0.0.1', 1438827302, 'XX'),
('strikeread', '127.0.0.1', 1438827422, 'XX'),
('strikeread', '127.0.0.1', 1438827962, 'XX'),
('strikeread', '127.0.0.1', 1438901663, 'XX'),
('strikeread', '127.0.0.1', 1438978310, 'XX'),
('strikeread', '127.0.0.1', 1438978645, 'XX'),
('strikeread', '127.0.0.1', 1438994028, 'XX'),
('strikeread', '127.0.0.1', 1439003536, 'XX'),
('strikeread', '127.0.0.1', 1439130018, 'XX'),
('strikeread', '127.0.0.1', 1439156050, 'XX'),
('strikeread', '127.0.0.1', 1439170131, 'XX'),
('strikeread', '127.0.0.1', 1439215394, 'XX'),
('strikeread', '127.0.0.1', 1439307307, 'XX'),
('strikeread', '127.0.0.1', 1439319460, 'XX');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(15) NOT NULL,
  `ip` varchar(1024) NOT NULL,
  `port` int(5) NOT NULL,
  `time` int(4) NOT NULL,
  `method` varchar(10) NOT NULL,
  `date` int(11) NOT NULL,
  `stopped` int(1) NOT NULL DEFAULT '0',
  `handler` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=151 ;

--
-- Volcado de datos para la tabla `logs`
--

INSERT INTO `logs` (`id`, `user`, `ip`, `port`, `time`, `method`, `date`, `stopped`, `handler`) VALUES
(113, 'StrikeREAD', '127.0.0.1', 80, 60, 'tcp', 1438741095, 1, 'S1,S2'),
(114, 'StrikeREAD', '127.0.0.1', 80, 60, 'tcp', 1438741141, 1, 'S2,S1'),
(115, 'StrikeREAD', '127.0.0.1', 80, 60, 'tcp', 1438741218, 1, 'S2,S1'),
(116, 'StrikeREAD', '127.0.0.1', 80, 60, 'tcp', 1438741282, 1, 'S2,S1'),
(117, 'StrikeREAD', '127.0.0.1', 80, 60, 'tcp', 1438741718, 0, 'S2,S1'),
(118, 'StrikeREAD', '127.0.0.1', 80, 60, 'tcp', 1438742920, 0, 'S2,S1'),
(119, 'StrikeREAD', '127.0.0.1', 80, 60, 'tcp', 1438789776, 0, 'S1,S2'),
(120, 'StrikeREAD', '127.0.0.1', 80, 60, 'tcp', 1438790159, 0, 'S2'),
(121, 'StrikeREAD', '127.0.0.2', 80, 60, 'tcp', 1438790271, 0, 'S1'),
(122, 'StrikeREAD', '127.0.0.2', 80, 60, 'tcp', 1438790315, 0, 'S2'),
(123, 'StrikeREAD', '127.0.0.2', 80, 60, 'tcp', 1438790731, 1, 'S1'),
(124, 'StrikeREAD', '127.0.0.2', 80, 60, 'tcp', 1438790739, 1, 'S1'),
(125, 'StrikeREAD', '127.0.0.2', 80, 60, 'tcp', 1438790751, 0, 'S1'),
(126, 'StrikeREAD', '127.0.0.2', 80, 60, 'tcp', 1438790761, 0, 'S2'),
(127, 'StrikeREAD', '127.0.0.1', 80, 60, 'tcp', 1438791434, 0, 'S2'),
(128, 'StrikeREAD', '127.0.0.1', 80, 60, 'tcp', 1438823740, 1, 'S2'),
(129, 'StrikeREAD', '127.0.0.1', 80, 60, 'tcp', 1438823745, 1, 'S1'),
(130, 'StrikeREAD', '127.0.0.1', 80, 60, 'tcp', 1438823878, 1, 'S1'),
(131, 'StrikeREAD', '127.0.0.1', 80, 60, 'tcp', 1438823881, 1, 'S2'),
(132, 'StrikeREAD', '127.0.0.1', 80, 60, 'tcp', 1438823923, 1, 'S2'),
(133, 'StrikeREAD', '127.0.0.1', 80, 60, 'tcp', 1438824063, 1, 'S2'),
(134, 'StrikeREAD', '127.0.0.1', 80, 60, 'tcp', 1438824066, 1, 'S1'),
(135, 'StrikeREAD', '127.0.0.1', 80, 60, 'tcp', 1438825312, 0, 'S1'),
(136, 'StrikeREAD', '127.0.0.1', 80, 60, 'tcp', 1438827704, 1, 'S2'),
(137, 'StrikeREAD', '127.0.0.1', 80, 60, 'tcp', 1438828003, 0, 'S2'),
(138, 'StrikeREAD', '127.0.0.1', 80, 60, 'tcp', 1438829778, 0, 'S2'),
(139, 'StrikeREAD', '127.0.0.1', 80, 60, 'tcp', 1438829780, 0, 'S1'),
(140, 'strikeread', '127.0.0.1', 80, 60, 'tcp', 1438978808, 1, 'S2'),
(141, 'strikeread', '127.0.0.1', 80, 60, 'tcp', 1438978817, 1, 'S1'),
(142, 'strikeread', '127.0.0.1', 80, 60, 'tcp', 1438978834, 1, 'S2'),
(143, 'strikeread', '127.0.0.1', 80, 60, 'tcp', 1438978836, 1, 'S1'),
(144, 'strikeread', '127.0.0.1', 80, 60, 'tcp', 1438978851, 1, 'S2'),
(145, 'strikeread', '127.0.0.1', 80, 60, 'tcp', 1438978851, 0, 'S2'),
(146, 'strikeread', '127.0.0.1', 80, 60, 'tcp', 1438978852, 0, 'S1'),
(147, 'strikeread', '127.0.0.1', 80, 60, 'tcp', 1438978858, 0, 'S2'),
(148, 'strikeread', '127.0.0.1', 80, 60, 'tcp', 1438978859, 0, 'S1'),
(149, 'strikeread', '127.0.0.1', 80, 60, 'tcp', 1439130038, 1, 'S1'),
(150, 'strikeread', '127.0.0.1', 80, 60, 'tcp', 1439314784, 0, 'S2');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lostp`
--

CREATE TABLE IF NOT EXISTS `lostp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` text NOT NULL,
  `username` text NOT NULL,
  `mail` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=32 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `messageid` int(11) NOT NULL AUTO_INCREMENT,
  `ticketid` int(11) NOT NULL,
  `content` text NOT NULL,
  `sender` varchar(30) NOT NULL,
  `date` int(20) NOT NULL,
  PRIMARY KEY (`messageid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Volcado de datos para la tabla `messages`
--

INSERT INTO `messages` (`messageid`, `ticketid`, `content`, `sender`, `date`) VALUES
(11, 8, 'HOLA K ASE', 'Client', 1439313037),
(12, 8, 'NADA Y TU?', 'Admin', 1439313077),
(13, 8, 'Na c:', 'Client', 1439314810),
(14, 9, 'Hola, enseguida le activaremos su package pero no te pongas tan agresivo :c', 'Admin', 1439324043),
(15, 9, 'VENGA HA FREGAR PARGELAS', 'Client', 1439324064);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `methods`
--

CREATE TABLE IF NOT EXISTS `methods` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `fullname` varchar(20) NOT NULL,
  `type` varchar(6) NOT NULL,
  `command` varchar(1000) NOT NULL,
  UNIQUE KEY `id_2` (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `methods`
--

INSERT INTO `methods` (`id`, `name`, `fullname`, `type`, `command`) VALUES
(1, 'tcp', 'tcp', 'layer4', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(1024) NOT NULL,
  `content` varchar(1000) NOT NULL,
  `date` int(11) NOT NULL,
  `author` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=80 ;

--
-- Volcado de datos para la tabla `news`
--

INSERT INTO `news` (`ID`, `title`, `content`, `date`, `author`) VALUES
(76, 'Welcome', 'New source!', 1438704162, 'strikeread'),
(77, 'Welcome', 'Welcome to Time-Stresser.pw!', 1438704587, 'strikeread'),
(78, 'Update!', 'New methods are addeds!', 1438704672, 'strikeread'),
(79, 'Hello', 'How are u?', 1438704690, 'strikeread');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payments`
--

CREATE TABLE IF NOT EXISTS `payments` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `paid` float NOT NULL,
  `plan` int(11) NOT NULL,
  `user` int(15) NOT NULL,
  `email` varchar(60) NOT NULL,
  `tid` varchar(30) NOT NULL,
  `date` int(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plans`
--

CREATE TABLE IF NOT EXISTS `plans` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `mbt` int(11) NOT NULL,
  `unit` varchar(10) NOT NULL,
  `length` int(11) NOT NULL,
  `price` float NOT NULL,
  `concurrents` int(11) NOT NULL,
  `private` int(1) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=73 ;

--
-- Volcado de datos para la tabla `plans`
--

INSERT INTO `plans` (`ID`, `name`, `mbt`, `unit`, `length`, `price`, `concurrents`, `private`) VALUES
(1, 'Trial2', 300, 'Days', 1, 1.99, 1, 0),
(68, 'Lifetime Ultimate ', 5000, 'Years', 1, 150, 3, 0),
(72, 'Prueba', 120, 'Years', 1, 16, 10, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rusers`
--

CREATE TABLE IF NOT EXISTS `rusers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` text NOT NULL,
  `password` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `rusers`
--

INSERT INTO `rusers` (`id`, `user`, `password`) VALUES
(3, 'strikeread', 'strikeread');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servers`
--

CREATE TABLE IF NOT EXISTS `servers` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `password` varchar(100) NOT NULL,
  `slots` int(3) NOT NULL,
  `methods` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `sitename` varchar(1024) NOT NULL,
  `description` text NOT NULL,
  `paypal` varchar(50) NOT NULL,
  `bitcoin` varchar(50) NOT NULL,
  `maintaince` varchar(100) NOT NULL,
  `tos` varchar(50) NOT NULL,
  `url` varchar(50) NOT NULL,
  `rotation` int(1) NOT NULL DEFAULT '0',
  `system` varchar(7) NOT NULL,
  `maxattacks` int(5) NOT NULL,
  `key` varchar(100) NOT NULL,
  `testboots` int(1) NOT NULL,
  `cloudflare` int(1) NOT NULL,
  `cbp` int(1) NOT NULL,
  `skype` varchar(200) NOT NULL,
  `issuerId` varchar(50) NOT NULL,
  `secretKey` varchar(50) NOT NULL,
  `coinpayments` varchar(50) NOT NULL,
  `ipnSecret` varchar(100) NOT NULL,
  KEY `sitename` (`sitename`(767))
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `settings`
--

INSERT INTO `settings` (`sitename`, `description`, `paypal`, `bitcoin`, `maintaince`, `tos`, `url`, `rotation`, `system`, `maxattacks`, `key`, `testboots`, `cloudflare`, `cbp`, `skype`, `issuerId`, `secretKey`, `coinpayments`, `ipnSecret`) VALUES
('Time-Stresser', 'Welcome to Time-Stresser.pw', 'aa2a@gmail.com', '1JXWeMMJQUoG5GuTMFQD7pATKswq26oz5G', '', 'tos.php', 'http://127.0.0.1:123/Source/', 1, 'api', 0, '', 0, 0, 0, '', '', 'x01AhBQ8Uc-Vivhtvp-j7w', '7a7e7e59c12bafe43351914dd41884e1', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tickets`
--

CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(1024) NOT NULL,
  `content` text NOT NULL,
  `status` varchar(30) NOT NULL,
  `username` varchar(15) NOT NULL,
  `date` int(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Volcado de datos para la tabla `tickets`
--

INSERT INTO `tickets` (`id`, `subject`, `content`, `status`, `username`, `date`) VALUES
(8, 'StrikeREAD', 'strkieread', 'Closed', 'strikeread', 1439311903),
(9, 'QUE PASA', 'ME CAGO EN DIOS QUE HE PAGADO Y NO TENGO MI PACKAGE HIJOS DE PUTA\r\n\r\nMIRAR LAS FOTOS\r\n\r\nProofs:\r\nhttp://pepitogrillo.com/593SOYUNAFOTO\r\nTRANSACCION ID: pUTABIDA\r\n\r\nLo pillah?', 'Closed', 'strikeread', 1439324020);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(15) NOT NULL,
  `password` varchar(40) NOT NULL,
  `email` varchar(50) NOT NULL,
  `scode` text NOT NULL,
  `rank` int(11) NOT NULL DEFAULT '0',
  `membership` int(11) NOT NULL,
  `expire` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `referral` varchar(50) NOT NULL,
  `referralbalance` int(3) NOT NULL DEFAULT '0',
  `testattack` int(1) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`ID`, `username`, `password`, `email`, `scode`, `rank`, `membership`, `expire`, `status`, `referral`, `referralbalance`, `testattack`) VALUES
(12, 'strikeread', '9925c4b652a4aec5e707a5c3cf6af9eb2025769a', 'strikeread@gmail.com', '1234', 1, 72, 1470601199, 0, '0', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `yt`
--

CREATE TABLE IF NOT EXISTS `yt` (
  `id1` text NOT NULL,
  `date1` text NOT NULL,
  `id2` text NOT NULL,
  `date2` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `yt`
--

INSERT INTO `yt` (`id1`, `date1`, `id2`, `date2`) VALUES
('t94o4CCYN9U', '30 jul. 2015', '-evu2XQsiqg', '24 jul. 2015');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
