-- phpMyAdmin SQL Dump
-- version 4.7.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 07-Set-2017 às 14:14
-- Versão do servidor: 5.5.52-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `admin_zsyn`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `api`
--

CREATE TABLE `api` (
  `id` int(2) NOT NULL,
  `name` varchar(50) NOT NULL,
  `api` varchar(1024) NOT NULL,
  `slots` int(3) NOT NULL,
  `methods` varchar(9999) NOT NULL,
  `kind` varchar(50) NOT NULL DEFAULT 'Layer 4',
  `connection` varchar(30) NOT NULL DEFAULT '1 GBPS'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `api`
--

INSERT INTO `api` (`id`, `name`, `api`, `slots`, `methods`, `kind`, `connection`) VALUES
(127, 'Zorin', 'http://ip/path/api.php?host=[host]&method=[method]&time=[time]&port=[port]&key=sdfsdf', 3, 'HTTP ADVANCED XML', 'Layer 7', '1 GBPS');

-- --------------------------------------------------------

--
-- Estrutura da tabela `apipremium`
--

CREATE TABLE `apipremium` (
  `id` int(2) NOT NULL,
  `name` varchar(50) NOT NULL,
  `api` varchar(1024) NOT NULL,
  `slots` int(3) NOT NULL,
  `methods` varchar(9999) NOT NULL,
  `kind` varchar(50) NOT NULL DEFAULT 'Layer 4',
  `connection` varchar(30) NOT NULL DEFAULT '1 GBPS'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `bans`
--

CREATE TABLE `bans` (
  `username` varchar(15) NOT NULL,
  `reason` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `bans`
--

INSERT INTO `bans` (`username`, `reason`) VALUES
('bengraal', 'Spam in tickets'),
('kubag200225', 'Fake transaction.'),
('GuzMan9666', 'suck my dick'),
('gd3223yg23d', 'suck my dick'),
('qMarzio4699', '.');

-- --------------------------------------------------------

--
-- Estrutura da tabela `blacklist`
--

CREATE TABLE `blacklist` (
  `ID` int(11) NOT NULL,
  `data` varchar(50) NOT NULL,
  `type` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `faq`
--

CREATE TABLE `faq` (
  `id` int(3) NOT NULL,
  `question` varchar(1024) NOT NULL,
  `answer` varchar(5000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `faq`
--

INSERT INTO `faq` (`id`, `question`, `answer`) VALUES
(1, 'My first question!', 'Well it\'s simple sir, you just find the answer.'),
(2, 'Update!', 'New methods and new servers!');

-- --------------------------------------------------------

--
-- Estrutura da tabela `fe`
--

CREATE TABLE `fe` (
  `ID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `type` varchar(1) NOT NULL,
  `ip` varchar(15) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `iplogs`
--

CREATE TABLE `iplogs` (
  `ID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `logged` varchar(15) NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `loginlogs`
--

CREATE TABLE `loginlogs` (
  `username` varchar(15) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `date` int(11) NOT NULL,
  `country` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user` varchar(15) NOT NULL,
  `ip` varchar(1024) NOT NULL,
  `port` int(5) NOT NULL,
  `time` int(4) NOT NULL,
  `method` varchar(10) NOT NULL,
  `date` int(11) NOT NULL,
  `stopped` int(1) NOT NULL DEFAULT '0',
  `handler` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `lostp`
--

CREATE TABLE `lostp` (
  `id` int(11) NOT NULL,
  `code` text NOT NULL,
  `username` text NOT NULL,
  `mail` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `messages`
--

CREATE TABLE `messages` (
  `messageid` int(11) NOT NULL,
  `ticketid` int(11) NOT NULL,
  `content` text NOT NULL,
  `sender` varchar(30) NOT NULL,
  `date` int(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `methods`
--

CREATE TABLE `methods` (
  `id` int(2) NOT NULL,
  `name` varchar(30) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `type` varchar(6) NOT NULL,
  `command` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `methods`
--

INSERT INTO `methods` (`id`, `name`, `fullname`, `type`, `command`) VALUES
(135, 'HTTP', 'BoostHTTP [WEB]', 'layer7', ''),
(136, 'ADVANCED', 'BoostHTTP Advanced [WEB] [Custom] ', 'layer7', ''),
(137, 'XML', 'XML-RPC [WEB] [BETA]', 'layer7', '');

-- --------------------------------------------------------

--
-- Estrutura da tabela `news`
--

CREATE TABLE `news` (
  `ID` int(11) NOT NULL,
  `title` varchar(1024) NOT NULL,
  `content` varchar(1000) NOT NULL,
  `date` int(11) NOT NULL,
  `author` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `news`
--

INSERT INTO `news` (`ID`, `title`, `content`, `date`, `author`) VALUES
(1, 'zSyn Launching', 'Enjoy our services, any question open a ticket.', 1501890280, 'admin'),
(2, 'New Methods & Servers', 'Added new methods for Layer 7 and new servers, layer 4 servers are comming soon.', 1501918309, 'admin');

-- --------------------------------------------------------

--
-- Estrutura da tabela `payments`
--

CREATE TABLE `payments` (
  `ID` int(11) NOT NULL,
  `paid` float NOT NULL,
  `plan` int(11) NOT NULL,
  `user` int(15) NOT NULL,
  `email` varchar(60) NOT NULL,
  `tid` varchar(30) NOT NULL,
  `date` int(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `plans`
--

CREATE TABLE `plans` (
  `ID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `mbt` int(11) NOT NULL,
  `unit` varchar(10) NOT NULL,
  `length` int(11) NOT NULL,
  `price` float NOT NULL,
  `concurrents` int(11) NOT NULL,
  `private` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `plans`
--

INSERT INTO `plans` (`ID`, `name`, `mbt`, `unit`, `length`, `price`, `concurrents`, `private`) VALUES
(74, 'Gold', 400, 'Days', 30, 5, 1, 0),
(75, 'Super', 700, 'Days', 30, 10, 1, 0),
(76, 'Elite', 1300, 'Days', 30, 20, 1, 0),
(78, 'Owner', 2147483647, 'years', 2147483647, 3.40282e38, 2147483647, 1),
(92, 'Trial', 150, 'Days', 1, 2, 1, 1),
(5, 'Free', 100, 'Years', 1, 1, 1, 1),
(81, 'YouTuber', 850, 'Days', 30, 1337, 2, 1),
(93, 'Basic YouTuber', 600, 'Months', 1, 1337, 1, 1),
(101, 'God', 1500, 'Days', 30, 30, 1, 0),
(102, 'God Long', 1600, 'days', 30, 60, 2, 1),
(103, 'Xtreme', 2100, 'days', 30, 50, 1, 0),
(109, 'Extreme (Enterprise)', 7200, 'days', 30, 900, 8, 1),
(107, 'Gold Premium', 2300, 'Days', 30, 90, 2, 1),
(108, 'Bronze VPN', 1, 'days', 1, 1, 1, 1),
(110, 'Bronze Premium', 1200, 'Days', 30, 1, 2, 1),
(111, 'Diamond Premium', 4500, 'days', 30, 1, 4, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `rusers`
--

CREATE TABLE `rusers` (
  `id` int(11) NOT NULL,
  `user` text NOT NULL,
  `password` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `servers`
--

CREATE TABLE `servers` (
  `id` int(2) NOT NULL,
  `name` varchar(50) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `password` varchar(100) NOT NULL,
  `slots` int(3) NOT NULL,
  `methods` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `settings`
--

CREATE TABLE `settings` (
  `sitename` varchar(1024) NOT NULL,
  `description` text NOT NULL,
  `paypal` varchar(50) NOT NULL,
  `bitcoin` varchar(50) NOT NULL,
  `maintaince` int(1) NOT NULL,
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
  `ipnSecret` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `settings`
--

INSERT INTO `settings` (`sitename`, `description`, `paypal`, `bitcoin`, `maintaince`, `tos`, `url`, `rotation`, `system`, `maxattacks`, `key`, `testboots`, `cloudflare`, `cbp`, `skype`, `issuerId`, `secretKey`, `coinpayments`, `ipnSecret`) VALUES
('zSyn Stressing', 'zSyn Stressing', 'ratotesudo@sapo.pt', '1NCmxRYrG8ipMkdpThUZYFhX3icH6J69XT', 0, 'tos.php', 'https://zsyn.org', 1, 'api', 50, '', 0, 1, 0, '', '', 'x01AhBQ8Uc-Vivhtvp-j7w', '7a7e7e59c12bafe43351914dd41884e1', '');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `subject` varchar(1024) NOT NULL,
  `content` text NOT NULL,
  `status` varchar(30) NOT NULL,
  `username` varchar(15) NOT NULL,
  `date` int(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `username` varchar(15) NOT NULL,
  `password` varchar(40) NOT NULL,
  `email` varchar(50) NOT NULL,
  `scode` text NOT NULL,
  `rank` int(11) NOT NULL DEFAULT '5',
  `membership` int(11) NOT NULL,
  `expire` int(11) NOT NULL DEFAULT '2147483647',
  `status` int(11) NOT NULL,
  `referral` varchar(50) NOT NULL,
  `referralbalance` int(3) NOT NULL DEFAULT '0',
  `testattack` int(1) NOT NULL,
  `Premium` int(2) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `vipn`
--

CREATE TABLE `vipn` (
  `ID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `mbt` int(11) NOT NULL,
  `unit` varchar(10) NOT NULL,
  `length` int(11) NOT NULL,
  `price` float NOT NULL,
  `concurrents` int(11) NOT NULL,
  `private` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `vipn`
--

INSERT INTO `vipn` (`ID`, `name`, `mbt`, `unit`, `length`, `price`, `concurrents`, `private`) VALUES
(74, 'Bronze', 1200, 'Days', 30, 50, 2, 0),
(105, 'Gold', 2300, 'Days', 30, 90, 3, 0),
(106, 'Diamond', 4500, 'Days', 30, 180, 4, 0),
(107, 'Extreme (Enterprise)', 7200, 'Days', 30, 510, 8, 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `yt`
--

CREATE TABLE `yt` (
  `id1` text NOT NULL,
  `date1` text NOT NULL,
  `id2` text NOT NULL,
  `date2` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `yt`
--

INSERT INTO `yt` (`id1`, `date1`, `id2`, `date2`) VALUES
('S1FSmM6ANsk', '05/03/2017', 'KNBFcHArRsQ', '03/02/2017');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `api`
--
ALTER TABLE `api`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `apipremium`
--
ALTER TABLE `apipremium`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blacklist`
--
ALTER TABLE `blacklist`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lostp`
--
ALTER TABLE `lostp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`messageid`);

--
-- Indexes for table `methods`
--
ALTER TABLE `methods`
  ADD UNIQUE KEY `id_2` (`id`),
  ADD KEY `id` (`id`);

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
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `rusers`
--
ALTER TABLE `rusers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `servers`
--
ALTER TABLE `servers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD KEY `sitename` (`sitename`(767));

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
-- Indexes for table `vipn`
--
ALTER TABLE `vipn`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `api`
--
ALTER TABLE `api`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;
--
-- AUTO_INCREMENT for table `apipremium`
--
ALTER TABLE `apipremium`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;
--
-- AUTO_INCREMENT for table `blacklist`
--
ALTER TABLE `blacklist`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `fe`
--
ALTER TABLE `fe`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `iplogs`
--
ALTER TABLE `iplogs`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `lostp`
--
ALTER TABLE `lostp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `messageid` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `methods`
--
ALTER TABLE `methods`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;
--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;
--
-- AUTO_INCREMENT for table `rusers`
--
ALTER TABLE `rusers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `servers`
--
ALTER TABLE `servers`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `vipn`
--
ALTER TABLE `vipn`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
