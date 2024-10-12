-- phpMyAdmin SQL Dump
-- version 4.0.10.10
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2015 at 03:41 PM
-- Server version: 5.1.73
-- PHP Version: 5.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gigadb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `bootername` varchar(255) NOT NULL,
  `booterurl` varchar(255) NOT NULL,
  `booterlogo` varchar(255) NOT NULL,
  `wmsg` varchar(1024) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`bootername`, `booterurl`, `booterlogo`, `wmsg`, `id`) VALUES
('GigaStress', 'http://gigastress.com', 'http://logo.png', 'Welcome to GigaStress were we never false advertise about our power. What we say you get is what you will receive. We have custom coded our own source to enhance your guys experience here at GigaStress. Here also at GigaStress we have staff ready around 24/7 ready to give you the best customer support. ', 1);

-- --------------------------------------------------------

--
-- Table structure for table `api`
--

CREATE TABLE IF NOT EXISTS `api` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `api` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `api`
--

INSERT INTO `api` (`ID`, `api`) VALUES
(1, 'http://api.gigastress.com/controlsend.php?key=GigaNigga&target=[host]&port=[port]&time=[time]&method=[method]'),
(2, 'http://api2.gigastress.com/controlsend.php?key=GigaNigga&target=[host]&port=[port]&time=[time]&method=[method]');

-- --------------------------------------------------------

--
-- Table structure for table `blacklist`
--

CREATE TABLE IF NOT EXISTS `blacklist` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `IP` varchar(269) NOT NULL,
  `note` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `blacklist`
--

INSERT INTO `blacklist` (`ID`, `IP`, `note`) VALUES
(1, 'http://gigastress.com/', 'Site'),
(2, 'http://gigastress.com', 'Site'),
(3, 'http://gigastress.com/login.php', 'Site'),
(4, 'http://gigastress.com/register.php', 'Site'),
(5, 'http://hackforums.com', 'HackForums');

-- --------------------------------------------------------

--
-- Table structure for table `fe`
--

CREATE TABLE IF NOT EXISTS `fe` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `type` varchar(1) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `note` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `gateway`
--

CREATE TABLE IF NOT EXISTS `gateway` (
  `email` varchar(1024) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `logins`
--

CREATE TABLE IF NOT EXISTS `logins` (
  `name` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `loc` varchar(2) NOT NULL,
  `time` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `logins`
--

INSERT INTO `logins` (`name`, `ip`, `loc`, `time`) VALUES
('Adversary', '', '', ''),
('Adversary', '', '', ''),
('ChromeProducts', '', '', ''),
('Adversary', '', '', ''),
('Fusion', '', '', ''),
('ChromeProducts', '', '', ''),
('Adversary', '', '', ''),
('ChromeProducts', '', '', ''),
('Adversary', '', '', ''),
('ChromeProducts', '', '', ''),
('ChromeProduct', '', '', ''),
('Gigastress', '', '', ''),
('ChromeProduct', '', '', ''),
('Wopy', '', '', ''),
('Wopy', '', '', ''),
('Adversary', '', '', ''),
('ChromeProducts', '', '', ''),
('Adversary', '', '', ''),
('Adversary', '', '', ''),
('Cavp', '', '', ''),
('unixc', '', '', ''),
('Adversary', '', '', ''),
('Adversary', '', '', ''),
('Adversary', '', '', ''),
('ChromeProducts', '', '', ''),
('sammet', '', '', ''),
('Chromeproducts', '', '', ''),
('conormansham', '', '', ''),
('Adversary', '', '', ''),
('unix', '', '', ''),
('cuntcunt', '', '', ''),
('Sception', '', '', ''),
('Verdict', '', '', ''),
('Xillios', '', '', ''),
('Xillios', '', '', ''),
('Xillios', '', '', ''),
('Xillios', '', '', ''),
('Xillios', '', '', ''),
('Xillios', '', '', ''),
('TheRipper', '', '', ''),
('lulu1992', '', '', ''),
('Xillios', '', '', ''),
('dylankerssies', '', '', ''),
('dylankerssies', '', '', ''),
('nicoofully', '', '', ''),
('nicoofully', '', '', ''),
('mafeofstorm', '', '', ''),
('nicoofully', '', '', ''),
('Adversary', '', '', ''),
('Adversary', '', '', ''),
('Adversary', '', '', ''),
('Adversary', '', '', ''),
('Adversary', '', '', ''),
('aidsaids', '', '', ''),
('Adversary', '', '', ''),
('nicoofully', '', '', ''),
('majorwunder', '', '', ''),
('morphast1998', '', '', ''),
('Adversary', '', '', '');

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
  `stopped` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `user`, `ip`, `port`, `time`, `method`, `date`, `stopped`) VALUES
(1, 'Adversary', '212.1.209.9', 80, 60, 'essyn', 1434079581, 0),
(2, 'Adversary', '212.1.209.9', 80, 20, 'cfbypass', 1434080091, 0),
(3, 'Adversary', '212.1.209.9', 80, 20, 'cfbypass', 1434080237, 0),
(4, 'Adversary', '212.1.209.9', 80, 60, 'cfbypass', 1434080296, 0),
(5, 'Adversary', '212.1.209.9', 80, 45, 'cfbypass', 1434080448, 1),
(6, 'Adversary', '212.1.209.9', 80, 45, 'audp', 1434080475, 1),
(7, 'Adversary', '212.1.209.9', 80, 20, 'audp', 1434080495, 0),
(8, 'Adversary', '212.1.209.9', 80, 20, 'audp', 1434080570, 1),
(9, 'Adversary', '212.1.209.9', 80, 20, 'audp', 1434080630, 1),
(10, 'Adversary', '212.1.209.9', 80, 20, 'audp', 1434080634, 1),
(11, 'Adversary', '212.1.209.9', 80, 20, 'cfbypass', 1434154170, 0),
(12, 'ChromeProducts', '1.1.1.1', 80, 120, 'audp', 1434154346, 0),
(13, 'ChromeProducts', '1.1.1.1', 80, 323, 'cfbypass', 1434154961, 1),
(14, 'Adversary', '212.1.209.9', 80, 10, 'audp', 1434243552, 1),
(15, 'Adversary', '212.1.209.9', 80, 45, 'ntp', 1434320821, 0),
(16, 'Adversary', 'http://sinister.ly', 80, 60, 'j', 1434322924, 1),
(17, 'Adversary', '212.1.209.9', 80, 45, 'ntp', 1434337713, 1),
(18, 'Adversary', '212.1.209.9', 80, 45, 'essyn', 1434337741, 0),
(19, 'Adversary', '212.1.209.9', 80, 30, 'ssyn', 1434350493, 0),
(20, 'Adversary', '52.17.60.200', 80, 200, 'ntp', 1434351453, 1),
(21, 'Adversary', '52.17.60.200', 80, 200, 'ntp', 1434351547, 1),
(22, 'ChromeProducts', '101.183.26.85', 80, 60, 'ssdp', 1434395323, 0),
(23, 'Adversary', '212.1.209.9', 80, 45, 'ntp', 1434395425, 0),
(24, 'Wopy', '192.99.201.183', 2302, 300, 'ntp', 1434405949, 0),
(25, 'Adversary', '212.1.209.9', 80, 45, 'audp', 1434409669, 1),
(26, 'Adversary', '212.1.209.9', 80, 45, 'ntp', 1434410478, 0),
(27, 'ChromeProducts', '195.154.97.226', 80, 120, 'audp', 1434430570, 0),
(28, 'ChromeProducts', '195.154.97.226', 80, 120, 'ntp', 1434431082, 0),
(29, 'Adversary', '212.1.209.9', 80, 60, 'essyn', 1434654718, 0),
(30, 'ChromeProducts', '37.187.29.143', 80, 600, 'ntp', 1435067541, 0),
(31, 'ChromeProducts', '94.23.18.102', 25565, 600, 'essyn', 1435068295, 1),
(32, 'ChromeProducts', 'http://prison.lordsworld.co.uk/', 80, 600, 'xmlrpc', 1435068563, 1),
(33, 'ChromeProducts', '155.4.6.143', 80, 600, 'ssdp', 1435069556, 0),
(34, 'ChromeProducts', '85.159.237.148', 80, 120, 'ssdp', 1435540846, 1),
(35, 'ChromeProducts', '85.159.237.148', 80, 120, 'ntp', 1435540911, 1),
(36, 'Adversary', '212.1.209.9', 80, 60, 'essyn', 1436240186, 1),
(37, 'Adversary', '212.1.209.9', 80, 60, 'essyn', 1439188143, 1),
(38, 'Adversary', '212.1.209.9', 80, 60, 'essyn', 1439487857, 1);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `messageid` int(11) NOT NULL AUTO_INCREMENT,
  `ticketid` int(11) NOT NULL,
  `content` text NOT NULL,
  `sender` varchar(30) NOT NULL,
  PRIMARY KEY (`messageid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`messageid`, `ticketid`, `content`, `sender`) VALUES
(1, 3, 'Hell yea can you help please', 'Client'),
(2, 3, 'How can I help you today', 'Adversary'),
(3, 4, 'nigga', 'Adversary'),
(4, 4, 'hi', 'ChromeProducts'),
(5, 4, 'hi', 'ChromeProducts'),
(6, 6, 'Can someone please help me? I''d like to keep using this, but I feel like this thing isn''t even being supported anymore and that I just lost my money.', 'Client'),
(7, 6, 'Sadly I am the owner I have not even released this product. It not fully completed once it is I will activate your accounts.', 'Adversary'),
(8, 6, 'Sadly I am the owner I have not even released this product. It not fully completed once it is I will activate your accounts.', 'Adversary'),
(9, 7, 'Once the site is fully completed it will be able to knock off OVH Servers that are highly protected with Ease', 'Adversary'),
(10, 7, 'Once the site is fully completed it will be able to knock off OVH Servers that are highly protected with Ease', 'Adversary'),
(11, 8, 'Project has yet to be completed and has yet to be released once we are finished I will activate your account.', 'Adversary'),
(12, 8, 'Project has yet to be completed and has yet to be released once we are finished I will activate your account.', 'Adversary');

-- --------------------------------------------------------

--
-- Table structure for table `methods`
--

CREATE TABLE IF NOT EXISTS `methods` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `friendly` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `methods`
--

INSERT INTO `methods` (`ID`, `friendly`, `name`) VALUES
(18, 'HTTP HEAD', 'head'),
(17, 'HTTP GET', 'get'),
(16, 'ESSYN', 'essyn'),
(15, 'SSYN', 'ssyn'),
(14, 'NTP', 'ntp'),
(13, 'SSDP', 'ssdp'),
(12, 'AUDP', 'audp'),
(19, 'HTTP POST', 'post'),
(20, 'XML-RPC', 'xmlrpc'),
(22, 'JOOMLA', 'joomla');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `detail` text NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`ID`, `author_id`, `detail`, `date`) VALUES
(1, 1, 'Welcome to GigaStress we are proud to be launching finally with our fully custom coded source.\r\n\r\n- Staff', 1434249225);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE IF NOT EXISTS `payments` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `paid` float NOT NULL,
  `plan` int(11) NOT NULL,
  `user` int(15) NOT NULL,
  `email` varchar(60) DEFAULT NULL,
  `btc_addr` varchar(69) DEFAULT NULL,
  `type` enum('btc','stripe','pp','') DEFAULT NULL,
  `tid` varchar(30) NOT NULL,
  `date` int(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE IF NOT EXISTS `plans` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `mbt` int(11) NOT NULL,
  `unit` varchar(10) NOT NULL,
  `length` int(11) NOT NULL,
  `price` float NOT NULL,
  `con` varchar(10) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`ID`, `name`, `description`, `mbt`, `unit`, `length`, `price`, `con`) VALUES
(1, 'Giga Bronze', 'lsadfksdfldsk', 300, 'Months', 1, 3, '1'),
(2, 'Giga Silver', 'sdalfkl', 900, 'Months', 1, 7, '1'),
(3, 'Giga Gold', 'sdlfsakl', 1500, 'Months', 1, 11, '1'),
(4, 'Giga Platinum', 'asldfkl', 3600, 'Months', 1, 15, '2'),
(5, 'Giga Deluxe', 'aslfdl', 5400, 'Months', 1, 20, '2'),
(6, 'Giga Diamond', 'aldflkas', 7200, 'Months', 1, 26, '2'),
(7, 'Giga Mega', 'aslfdskla', 10800, 'Months', 1, 32, '1'),
(8, 'Giga Bronze Life', 'aklfdklas', 300, 'Years', 10, 12, '1'),
(9, 'Giga Silver Life', 'asdlfkdsal', 900, 'Years', 10, 16, '1'),
(10, 'Giga Gold Life', 'ladfkaslk', 1500, 'Years', 10, 25, '2');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(30) NOT NULL,
  `content` text NOT NULL,
  `status` varchar(30) NOT NULL,
  `username` varchar(15) NOT NULL,
  `department` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `subject`, `content`, `status`, `username`, `department`) VALUES
(1, 'Big', 'Dick', 'Closed', 'ChromeProduct', 'Sales'),
(2, 'my penis is too small', 'dfmdgdfgsdfgser', 'Closed', 'ChromeProducts', '0'),
(3, 'I BOUGHT ADMIN', 'I Didnt recieve admin', 'Closed', 'Wopy', 'Sales'),
(4, 'Credit Card Number', '7862 4556 6825 4265\r\nExp date 05/18\r\nCVC 152\r\n', 'Waiting for User response.', 'Wopy', '0'),
(5, 'Can''t find option for booting.', 'Hello,\r\n\r\nI just purchased a $3 Bronze package for this service, and I can''t find the stresser hub anywhere. How do I access it? All I see is Support Center and Sales.\r\n\r\nI would appreciate a swift response to this issue. Thank you.', 'Closed', 'Xillios', 'Technical Support'),
(6, 'No option for booting.', 'Hello, I just purchased a $3 Bronze package for this service, and I can''t find the stresser hub anywhere. How do I access it? All I see is Support Center and Sales. Did I encounter a glitch of some sort? I would appreciate a swift response to this issue. Thank you.', 'Waiting for User response.', 'Xillios', 'Technical Support'),
(7, 'Is there a OVH  stress??', 'Is there a OVH  stress ??', 'Waiting for User response.', 'lulu1992', 'Technical Support'),
(8, 'Paid but no activation', 'My account didn''t activate after I bought a package.', 'Waiting for User response.', 'nicoofully', 'Sales');

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
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `username`, `password`, `email`, `rank`, `membership`, `expire`, `status`) VALUES
(1, 'Adversary', 'dcd26b24c3df1fec33dc41acfbe41caa399d824f', 'adversaryhackforums@outlook.com', 1, 7, 1441866521, 0),
(2, 'ChromeProducts', '1b09f00bd9dae89cd5395f654b2cd9575e4c81dc', 'jdog3366@gmail.com', 0, 0, 0, 0),
(21, 'aidsaids', '1a344723561e3b57f5f76061e4415f4719af5856', 'aids@aids.cappa', 0, 0, 0, 0),
(3, 'Fusion', '8583334d10ed2ad7dc725871c4adbe561d4f574e', 'fusioncstwitch@gmail.com', 0, 0, 0, 0),
(9, 'sammet', '367580d553b378e32951df6c96e25d2c03a9d98e', 'sammet@gmail.com', 0, 0, 0, 0),
(6, 'Wopy', '7c4a8d09ca3762af61e59520943dc26494f8941b', '123456@gmail.com', 0, 0, 0, 1),
(7, 'Cavp', 'a3ea593b6e5cde6a030274de85f48fc8b141dcf3', 'admin@cavp.host', 0, 0, 0, 0),
(8, 'unixc', '4f101de0da27f8a48126c72cc54aa5eb5bfe5a95', 'ext420@gmail.com', 0, 0, 0, 0),
(10, 'conormansham', '81baaab94ae9a6d93f6a6fe01112ac0f9af579a5', 'psnmodz1998@gmail.com', 0, 0, 0, 0),
(11, 'unix', '579f99934357648963deb17e94158d8c655de7af', 'ext420@gmail.com', 0, 0, 0, 0),
(12, 'cuntcunt', '98a20b5d1592d11c613c8f7e7eebb6a8767aa968', 'cuntcunt@cuntcunt.cunt', 0, 0, 0, 0),
(13, 'Sception', '5baf31adcfc9f1422ac32f77cc211089fe76fd6b', 'sception@gmail.com', 0, 0, 0, 0),
(14, 'Verdict', '50d767670ead67c3dab6f2fddf6e8e80940bf89a', 'aresene@nigge.rs', 0, 0, 0, 0),
(15, 'Xillios', '693f6b19f4bb776686eb6207757582b0d6205123', 'Xillios@protonmail.ch', 0, 0, 0, 0),
(16, 'TheRipper', '369378cdc17f6860aaa243be373a058f2f601170', 'makolouo@disposableinbox.com', 0, 0, 0, 0),
(17, 'lulu1992', '2f2fb79c0c59e56b1533d2498978f4af85f16443', 'lama_omar_alqadi@hotmail.com', 0, 0, 0, 0),
(18, 'dylankerssies', '1a2eb67dddd23f7cdf9599c686dc89e652743791', 'dylankerssies@hotmail.com', 0, 0, 0, 0),
(19, 'nicoofully', '393306c517736952c03e4553bacb549790b754e3', 'hottie@hotmail.com', 0, 0, 0, 0),
(20, 'mafeofstorm', '6972338c7dee23442ec61ed2658567f653ec3f1a', 'jsilvestre@gmail.com', 0, 0, 0, 0),
(22, 'majorwunder', 'eb5cf78b1570aa835f28bedbff99bd1518d28c99', 'themajorwunder@gmail.com', 0, 0, 0, 0),
(23, 'morphast1998', 'ffd514750eafcf73a0de7b3374286b6785939463', 'williamstewartwallace1998@hotmail.com', 0, 0, 0, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
