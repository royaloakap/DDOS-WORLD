-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 31, 2022 at 10:31 PM
-- Server version: 5.7.35-38
-- PHP Version: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ci28752_keker`
--

-- --------------------------------------------------------

--
-- Table structure for table `2authsettings`
--

CREATE TABLE IF NOT EXISTS `2authsettings` (
  `secret` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `2authsettings`
--

INSERT INTO `2authsettings` (`secret`) VALUES
('66PUM3ZT5GIRPD3S'),
('66PUM3ZT5GIRPD3S');

-- --------------------------------------------------------

--
-- Table structure for table `actions`
--

CREATE TABLE IF NOT EXISTS `actions` (
  `id` int(64) NOT NULL AUTO_INCREMENT,
  `admin` varchar(64) NOT NULL,
  `client` varchar(64) NOT NULL,
  `action` varchar(6444) NOT NULL,
  `date` int(21) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `actions`
--

INSERT INTO `actions` (`id`, `admin`, `client`, `action`, `date`) VALUES
(73, 'Crypton', 'Next', 'Users expire updated from -10800 to 28-06-2022', 1653819298);

-- --------------------------------------------------------

--
-- Table structure for table `affiliateWithdraws`
--

CREATE TABLE IF NOT EXISTS `affiliateWithdraws` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `withdrawAmount` varchar(255) NOT NULL,
  `paymentMethod` varchar(255) NOT NULL,
  `paymentAddress` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `date` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `api`
--

CREATE TABLE IF NOT EXISTS `api` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `api` varchar(1024) NOT NULL,
  `slots` int(3) NOT NULL,
  `methods` varchar(100) NOT NULL,
  `vip` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=175 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `api`
--

INSERT INTO `api` (`id`, `name`, `api`, `slots`, `methods`, `vip`) VALUES
(174, 'VDS2', 'http://lmao.lmao/api.php?key=97375564&host=[host]&port=[port]&time=[time]&method=[method]', 3, 'RAW RAND STRONG CRYPTO BYPASS', 0);

-- --------------------------------------------------------

--
-- Table structure for table `bans`
--

CREATE TABLE IF NOT EXISTS `bans` (
  `username` varchar(15) NOT NULL,
  `reason` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bans`
--

INSERT INTO `bans` (`username`, `reason`) VALUES
('The1NOlnyKing', ''),
('The1NOlnyKing', ''),
('The1NOlnyKing', ''),
('The1NOlnyKing', 'Testing'),
('test', ''),
('test', ''),
('test', ''),
('root', ''),
('Next', 'hm'),
('Next', '');

-- --------------------------------------------------------

--
-- Table structure for table `blacklist`
--

CREATE TABLE IF NOT EXISTS `blacklist` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `data` varchar(50) NOT NULL,
  `type` varchar(10) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `blacklist`
--

INSERT INTO `blacklist` (`ID`, `data`, `type`) VALUES
(3, 'ilyxa05.ml', 'victim'),
(4, 'snetwork.xyz', 'victim'),
(7, 'https://s416108.ha003.t.justns.ru', 'victim'),
(8, 'http://s416108.ha003.t.justns.ru', 'victim'),
(9, 'https://mao-stress.xyz', 'victim'),
(10, 'http://mao-stress.xyz', 'victim'),
(11, 'mao-stress.xyz', 'victim'),
(12, 's416108.ha003.t.justns.ru', 'victim'),
(13, 'https://snetwork.xyz', 'victim'),
(14, 'http://snetwork.xyz', 'victim'),
(15, 'http://ilyxa05.ml', 'victim'),
(16, 'https://ilyxa05.ml', 'victim'),
(17, '91.229.90.154', 'victim'),
(18, 'https://mao-stress.xyz/', 'victim'),
(19, 'https://mao-stress.xyz/login.php', 'victim'),
(20, 'https://ilyxa05.ml/', 'victim');

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE IF NOT EXISTS `faq` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `question` varchar(1024) NOT NULL,
  `answer` varchar(5000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `faq`
--

INSERT INTO `faq` (`id`, `question`, `answer`) VALUES
(4, 'What powers the Layer 7 attack methods?', 'We don\'t use old methods like XML-RPC, Joomla or even proxies.\r\nOur Layer 7 is is powered by 2 botnets (1 for normal and 1 for VIP) which means that our power doesn\'t rely on exploits or proxies which will die quickly.'),
(5, 'Why are VIP packages double the price of normal packages?', 'VIP packages are double the price because of the drastically large amount of power per attack.\r\nVIP attacks have more than twice the amount of power (on average) than normal attacks, making VIP packages worth more than double the amount or normal packages.'),
(8, 'What\'s the difference between normal and VIP packages?', 'VIP packages will send an attack with VIP servers and normal servers.\r\nNormal packages will send an attack with normal servers only.\r\nThis keeps the power for VIP strong and much harder hitting, making it worthy for the price.');

-- --------------------------------------------------------

--
-- Table structure for table `fe`
--

CREATE TABLE IF NOT EXISTS `fe` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `type` varchar(1) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `giftcards`
--

CREATE TABLE IF NOT EXISTS `giftcards` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `planID` int(11) NOT NULL,
  `claimedby` int(11) NOT NULL,
  `dateClaimed` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `giftcards`
--

INSERT INTO `giftcards` (`ID`, `code`, `planID`, `claimedby`, `dateClaimed`, `date`) VALUES
(14, 'qv1z7c18k5', 40, 45, 1653202410, 1653202250),
(15, 'c9yiy5ws1b', 40, 79, 1653247830, 1653202490),
(16, 'jz2nl1mkua', 41, 81, 1653205291, 1653205248),
(17, 'f8iz8ba10c', 42, 80, 1653206686, 1653206556),
(18, '4h4ny3vksw', 37, 80, 1653209677, 1653209626),
(19, '8zapoa0p32', 42, 68, 1653216551, 1653216406),
(20, '6pl4pwzby6', 42, 47, 1653217248, 1653217092),
(21, '1ranaq1kmb', 42, 60, 1653228608, 1653220828),
(22, 'qgbhr5ztvq', 42, 94, 1653224524, 1653224478),
(23, 'owddtjzgrg', 36, 64, 1653226966, 1653226949),
(24, 'hdbgihrt2f', 36, 60, 1653236688, 1653236639),
(25, '8ngq49ao0t', 43, 80, 1653252207, 1653252174),
(26, 'nzg5yhgzpo', 37, 150, 1653585733, 1653585224),
(27, 'pzestqq0it', 36, 148, 1653585734, 1653585269),
(28, 'hk605ai4q2', 41, 48, 1653585615, 1653585289),
(29, 'cmefhc2dq0', 41, 71, 1653586522, 1653585564),
(30, 'oek01nj97a', 36, 0, 0, 1653585984),
(31, '50o1v9c4s7', 37, 103, 1653586858, 1653586016),
(32, '96kifb75p9', 41, 81, 1653586385, 1653586036),
(33, 'h3flbij6pd', 36, 64, 1653586107, 1653586057),
(34, 'i3qbczp7ml', 37, 0, 0, 1653586741),
(35, '9rwwpl0ben', 36, 151, 1653587011, 1653586960),
(36, 'onskp6q946', 44, 146, 1653588082, 1653587852),
(37, 'fq6w80rhov', 36, 165, 1653680665, 1653680563),
(38, '2yqzs4jzs0', 45, 103, 1653727046, 1653726999),
(39, 'k5ltfzoyz5', 45, 103, 1653732579, 1653732519),
(40, 'hk9i165hlz', 37, 80, 1653758067, 1653747172),
(41, 'pcqnxgk0kw', 36, 0, 0, 1653847726),
(42, 'cl6b5240at', 36, 0, 0, 1653847727),
(43, 'pkye12nu7o', 36, 0, 0, 1653847728),
(44, '77ecwewahb', 36, 0, 0, 1653847730),
(45, 'ln4azkxnwh', 36, 0, 0, 1653847731),
(46, 'r1n1fwpphj', 37, 0, 0, 1653847734),
(48, 'kwoz0keaof', 37, 0, 0, 1653847738),
(49, 'tml8ra54ha', 37, 0, 0, 1653847740),
(50, 'ndo9zj0czx', 37, 0, 0, 1653847742),
(51, '2ckh4ncrsi', 37, 0, 0, 1653847744),
(52, 'raf291urxt', 41, 0, 0, 1653847747),
(53, 'qc4scxm1kc', 41, 0, 0, 1653847750),
(54, 'hhqq5ouqvp', 41, 0, 0, 1653847752),
(55, 'o7wd9pfvrz', 41, 0, 0, 1653847754),
(56, 'p0wzqvj0b2', 41, 0, 0, 1653847757),
(57, '2g85vjyojk', 36, 141, 1653854827, 1653854644),
(58, 'k5065le2fr', 42, 185, 1653978724, 1653978699),
(59, '8dftdzynhh', 37, 188, 1654000044, 1654000030);

-- --------------------------------------------------------

--
-- Table structure for table `iplogs`
--

CREATE TABLE IF NOT EXISTS `iplogs` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `logged` varchar(15) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `loginlogs`
--

CREATE TABLE IF NOT EXISTS `loginlogs` (
  `username` varchar(15) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `date` int(11) NOT NULL,
  `country` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `loginlogs`
--

INSERT INTO `loginlogs` (`username`, `ip`, `date`, `country`) VALUES
('Crypton', '1.1.1.1', 1654022908, 'China');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(15) NOT NULL,
  `ip` varchar(1024) NOT NULL,
  `port` int(5) NOT NULL,
  `time` int(4) NOT NULL,
  `method` varchar(10) NOT NULL,
  `date` int(11) NOT NULL,
  `chart` varchar(255) NOT NULL,
  `stopped` int(1) NOT NULL DEFAULT '0',
  `handler` varchar(50) NOT NULL,
  `vip` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1924 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `user`, `ip`, `port`, `time`, `method`, `date`, `chart`, `stopped`, `handler`, `vip`) VALUES
(1923, 'f', 'https://beta.exitus.me', 443, 50, 'rapid', 1654022747, '31-05', 0, 'BROWSER', 0);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `messageid` int(11) NOT NULL AUTO_INCREMENT,
  `ticketid` int(11) NOT NULL,
  `content` text NOT NULL,
  `sender` varchar(30) NOT NULL,
  `date` int(20) NOT NULL,
  PRIMARY KEY (`messageid`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`messageid`, `ticketid`, `content`, `sender`, `date`) VALUES
(5, 9, 'bey', 'Admin', 1511394407),
(6, 9, 'bey', 'Admin', 1511394413),
(7, 11, '32112312', 'Client', 1653057036),
(8, 13, '13221', 'Client', 1653131643),
(9, 14, '312123', 'Client', 1653131763);

-- --------------------------------------------------------

--
-- Table structure for table `methods`
--

CREATE TABLE IF NOT EXISTS `methods` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `fullname` varchar(20) NOT NULL,
  `type` varchar(6) NOT NULL,
  `command` varchar(1000) NOT NULL,
  UNIQUE KEY `id_2` (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `methods`
--

INSERT INTO `methods` (`id`, `name`, `fullname`, `type`, `command`) VALUES
(80, 'RAW', 'RAW', 'layer7', ''),
(81, 'RAND', 'RAND', 'layer7', ''),
(84, 'STRONG', 'STRONG', 'layer7', ''),
(85, 'CRYPTO', 'CRYPTO', 'layer7', ''),
(101, 'BYPASS', 'BYPASS', 'layer7', ''),
(106, 'rapid', 'BROWSER', 'layer7', '');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(1024) NOT NULL,
  `content` varchar(1000) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`ID`, `title`, `content`, `date`) VALUES
(7, 'We are opened!', 'https://t.me/maostress', 1653133884),
(9, 'Telegram', 'Check out our telegram channel for all the latest news!', 1653156846),
(10, 'Insurrection', 'After blocking due to one motherfucker, we have recovered and are online again!', 1653583994);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ping_sessions`
--

CREATE TABLE IF NOT EXISTS `ping_sessions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ping_key` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ping_ip` varchar(25) NOT NULL,
  `ping_port` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1871 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ping_sessions`
--

INSERT INTO `ping_sessions` (`ID`, `ping_key`, `user_id`, `ping_ip`, `ping_port`) VALUES
(1870, '2eb965d62809727507e4c889afdac4d1', 188, 'https://beta.exitus.me', '443');

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE IF NOT EXISTS `plans` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `vip` int(11) NOT NULL,
  `mbt` int(11) NOT NULL,
  `unit` varchar(10) NOT NULL,
  `length` int(11) NOT NULL,
  `price` float NOT NULL,
  `concurrents` int(11) NOT NULL,
  `private` int(1) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`ID`, `name`, `vip`, `mbt`, `unit`, `length`, `price`, `concurrents`, `private`) VALUES
(36, 'Premuim', 0, 300, 'Days', 30, 300, 1, 0),
(37, 'Elite', 0, 500, 'Days', 30, 500, 2, 0),
(39, 'Admin', 999999, 999999, 'Years', 999, -1, 999, 1),
(41, 'Private', 0, 500, 'Days', 60, 750, 2, 0),
(42, 'Test Plan', 0, 120, 'Days', 1, -1, 1, 1),
(44, 'Premium 14D', 0, 300, 'Days', 14, -1, 1, 1),
(45, 'Elite+', 0, 500, 'Days', 29, -1, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `report` varchar(644) NOT NULL,
  `date` int(64) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `servers`
--

CREATE TABLE IF NOT EXISTS `servers` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `password` varchar(100) NOT NULL,
  `slots` int(3) NOT NULL,
  `methods` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `sitename` varchar(1024) NOT NULL,
  `stripePubKey` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `cooldown` int(11) NOT NULL,
  `cooldownTime` int(11) NOT NULL,
  `paypal` varchar(50) NOT NULL,
  `bitcoin` varchar(50) NOT NULL,
  `stripe` int(11) NOT NULL,
  `maintaince` varchar(100) NOT NULL,
  `rotation` int(1) NOT NULL DEFAULT '0',
  `system` varchar(7) NOT NULL,
  `maxattacks` int(5) NOT NULL,
  `testboots` int(1) NOT NULL,
  `cloudflare` int(1) NOT NULL,
  `skype` varchar(200) NOT NULL,
  `key` varchar(100) NOT NULL,
  `issuerId` varchar(50) NOT NULL,
  `coinpayments` varchar(50) NOT NULL,
  `ipnSecret` varchar(100) NOT NULL,
  `google_site` varchar(644) NOT NULL,
  `google_secret` varchar(644) NOT NULL,
  `btc_address` varchar(64) NOT NULL,
  `secretKey` varchar(50) NOT NULL,
  `cbp` int(1) NOT NULL,
  `paypal_email` varchar(64) NOT NULL,
  `theme` varchar(64) NOT NULL,
  `logo` varchar(64) NOT NULL,
  `stripeSecretKey` varchar(255) NOT NULL,
  UNIQUE KEY `key` (`key`),
  KEY `sitename` (`sitename`(767))
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `smtpsettings`
--

CREATE TABLE IF NOT EXISTS `smtpsettings` (
  `host` varchar(255) NOT NULL,
  `auth` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `port` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(1024) NOT NULL,
  `content` text NOT NULL,
  `status` varchar(30) NOT NULL,
  `username` varchar(15) NOT NULL,
  `date` int(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `subject`, `content`, `status`, `username`, `date`) VALUES
(8, 'dfgdf', 'gdfgdfg', 'Waiting for admin response', 'shadow', 1477157708),
(9, 'ticket', 'loli', 'Closed', 'livepreview', 1511381911),
(10, 'cxcxc', 'xcxcds', 'Closed', 'ilyxa05', 1653047866),
(11, 'admin', '123', 'Waiting for admin response', 'test', 1653056979),
(12, '?????????', '?????', 'Closed', 'Crypton', 1653064138),
(13, 'test-ticket', '3211322', 'Closed', 'root', 1653131619),
(14, '23312', '312123', 'Closed', 'root', 1653131747);

-- --------------------------------------------------------

--
-- Table structure for table `tos`
--

CREATE TABLE IF NOT EXISTS `tos` (
  `archive` longtext NOT NULL COMMENT '11'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(15) NOT NULL,
  `password` varchar(40) NOT NULL,
  `email` varchar(50) NOT NULL,
  `rank` int(11) NOT NULL DEFAULT '0',
  `membership` int(11) NOT NULL,
  `expire` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `referral` varchar(50) NOT NULL,
  `referralbalance` int(3) NOT NULL DEFAULT '0',
  `testattack` int(1) NOT NULL,
  `activity` int(64) NOT NULL DEFAULT '0',
  `2auth` int(11) NOT NULL,
  `referedBy` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=191 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `username`, `password`, `email`, `rank`, `membership`, `expire`, `status`, `referral`, `referralbalance`, `testattack`, `activity`, `2auth`, `referedBy`) VALUES
(44, 'ilyxa05', 'ddddd', 'root@maclo.ml', 1, 39, 2147483647, 0, '0', 0, 0, 0, 1, 0),
(45, 'Crypton', 'aiaiaiai', 'crypton@lols.ga', 1, 39, 2147461200, 0, '0', 0, 0, 0, 1, 0),

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
