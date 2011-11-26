-- phpMyAdmin SQL Dump
-- version 2.9.1.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jan 15, 2010 at 06:46 PM
-- Server version: 5.0.27
-- PHP Version: 5.2.0
-- 
-- Database: `fts`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `addedrequests`
-- 

CREATE TABLE `addedrequests` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `requestid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pollid` (`id`),
  KEY `userid` (`userid`),
  KEY `requestid_userid` (`requestid`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `addedrequests`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `anti_cheat`
-- 

CREATE TABLE `anti_cheat` (
  `user_id` bigint(20) NOT NULL default '0',
  `torrent_id` bigint(20) NOT NULL default '0',
  `uploaded` bigint(20) NOT NULL default '0',
  `downloaded` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `usertorrent` (`user_id`,`torrent_id`),
  KEY `torrent_id` (`torrent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `anti_cheat`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `avps`
-- 

CREATE TABLE `avps` (
  `arg` varchar(20) NOT NULL default '',
  `value_s` text NOT NULL,
  `value_i` int(11) NOT NULL default '0',
  `value_u` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`arg`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `avps`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `banned_agent`
-- 

CREATE TABLE `banned_agent` (
  `id` int(3) NOT NULL auto_increment,
  `agent` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `banned_agent`
-- 

INSERT INTO `banned_agent` (`id`, `agent`) VALUES 
(1, 'Azureus 2.0'),
(2, 'Azureus 2.1'),
(3, 'RAZA'),
(4, 'Python-urllib');

-- --------------------------------------------------------

-- 
-- Table structure for table `bannedemails`
-- 

CREATE TABLE `bannedemails` (
  `id` int(10) NOT NULL auto_increment,
  `value` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `bannedemails`
-- 

INSERT INTO `bannedemails` (`id`, `value`) VALUES 
(1, '@test.com');

-- --------------------------------------------------------

-- 
-- Table structure for table `bans`
-- 

CREATE TABLE `bans` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `addedby` int(10) unsigned NOT NULL default '0',
  `comment` varchar(255) NOT NULL default '',
  `first` int(11) default NULL,
  `last` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `first_last` (`first`,`last`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `bans`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `bitbucket`
-- 

CREATE TABLE `bitbucket` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `owner` int(10) default NULL,
  `name` text,
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `public` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `bitbucket`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `blocks`
-- 

CREATE TABLE `blocks` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `blockid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `userfriend` (`userid`,`blockid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `blocks`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `bonus`
-- 

CREATE TABLE `bonus` (
  `id` int(5) NOT NULL auto_increment,
  `bonusname` varchar(50) NOT NULL default '',
  `points` decimal(5,1) NOT NULL default '0.0',
  `description` text NOT NULL,
  `art` varchar(10) NOT NULL default 'traffic',
  `menge` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- 
-- Dumping data for table `bonus`
-- 

INSERT INTO `bonus` (`id`, `bonusname`, `points`, `description`, `art`, `menge`) VALUES 
(1, '1.0GB Uploaded', 75.0, 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 1073741824),
(2, '2.5GB Uploaded', 150.0, 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 2684354560),
(3, '5GB Uploaded', 250.0, 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 5368709120),
(4, '3 Invites', 20.0, 'With enough bonus points acquired, you are able to exchange them for a few invites. The points are then removed from your Bonus Bank and the invitations are added to your invites amount.', 'invite', 3),
(5, 'Custom Title!', 50.0, 'For only 50.0 Karma Bonus Points you can buy yourself a custom title. the only restrictions are no foul or offensive language or userclass can be entered. The points are then removed from your Bonus Bank and your special title is changed to the title of your choice', 'title', 1),
(6, 'VIP Status', 500.0, 'With enough bonus points acquired, you can buy yourself VIP status for one month. The points are then removed from your Bonus Bank and your status is changed.', 'class', 1),
(7, 'Give A Karma Gift', 100.0, 'Well perhaps you don''t need the upload credit, but you know somebody that could use the Karma boost! You are now able to give your Karma credits as  a gift! The points are then removed from your Bonus Bank and  added to the account of a user of your choice!\r\n\r\nAnd they recieve a PM with all the info as well as who it came from...', 'gift_1', 1073741824);

-- --------------------------------------------------------

-- 
-- Table structure for table `categories`
-- 

CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(30) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `sort_index` int(10) unsigned NOT NULL default '0',
  `cat_desc` varchar(30) NOT NULL default '',
  `parent_id` mediumint(5) NOT NULL default '-1',
  `tabletype` tinyint(2) unsigned NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=40 ;

-- 
-- Dumping data for table `categories`
-- 

INSERT INTO `categories` (`id`, `name`, `image`, `sort_index`, `cat_desc`, `parent_id`, `tabletype`) VALUES 
(36, 'TV/DVDrip', 'tvdvdrip.png', 0, '', -1, 1),
(35, 'TV/DVDR', 'tvdvdr.png', 0, '', -1, 1),
(34, 'Movies/XviD', 'xvid.png', 0, '', -1, 1),
(33, 'Movies/X264', 'X264.png', 0, '', -1, 1),
(32, 'Movies/DVD-R', 'dvdr.png', 0, '', -1, 1),
(31, 'Games/Xbox360', 'xbox360.png', 0, '', -1, 1),
(28, 'Apps/PC', 'appspc.png', 0, '', -1, 1),
(30, 'Games/Wii', 'wii.png', 0, '', -1, 1),
(29, 'Games/PC', 'pcgame.png', 0, '', -1, 1),
(37, 'TV/HD', 'tvx264.png', 0, '', -1, 1),
(38, 'TV/XviD', 'tvxvid.png', 0, '', -1, 1),
(39, 'xxx', 'xxx.png', 0, '', -1, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `clients`
-- 

CREATE TABLE `clients` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `client` varchar(32) NOT NULL default '',
  `agentString` varchar(64) NOT NULL default '',
  `freq` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

-- 
-- Dumping data for table `clients`
-- 

INSERT INTO `clients` (`id`, `client`, `agentString`, `freq`) VALUES 
(1, 'ABC', 'BitTorrent/ABC-', 0),
(2, 'Azureus', 'Azureus', 0),
(3, 'BitAnarch', 'Anarch', 0),
(4, 'BitComet', 'Comet', 0),
(5, 'BitSpirit', 'BitTorrent/BitSpirit', 0),
(6, 'BitTorrent 3', 'BitTorrent/3.', 0),
(7, 'BitTorrent++', '++', 0),
(8, 'BitTornado', 'BitTorrent/T-', 0),
(9, 'BTQueue', 'Queue', 0),
(10, 'Burst', 'BitTorrent/brst', 0),
(11, 'CTorrent', 'CTorrent', 0),
(12, 'G3 Torrent', 'G3', 0),
(13, 'Nova Torrent', 'Nova', 0),
(14, 'PTC Bittorrent', 'PTC', 0),
(15, 'Experimental', 'BitTorrent/S-', 0),
(16, 'Shareaza', 'AZA', 0),
(17, 'Torrentstorm', 'storm', 0),
(18, 'TorrenTopia', 'Topia', 0),
(19, 'TurbotBT', 'Turbo', 0),
(20, 'UPnP BT Client', 'UPnP', 0),
(21, 'eMule', 'ed2k_plugin v', 0),
(22, 'BitTorrent Linux', 'Python-urllib/', 0),
(27, 'BitTorrent Plus!', 'Plus!', 0),
(24, 'eDonkey', 'MLdonkey', 0),
(25, 'BTManager', 'BitTorrent/BTManager', 0),
(26, 'Shareaza', 'Shareaza', 0),
(28, 'eXeem', 'eXeem', 0),
(29, 'XanTorrent', 'DansClient', 0),
(30, 'BitTorrent 4', 'BitTorrent/4.', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `clientselect`
-- 

CREATE TABLE `clientselect` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(80) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

-- 
-- Dumping data for table `clientselect`
-- 

INSERT INTO `clientselect` (`id`, `name`) VALUES 
(1, 'ABC'),
(2, 'Azureus'),
(3, 'BitAnarch'),
(4, 'BitComet'),
(5, 'BitSpirit'),
(6, 'BitTorrent'),
(7, 'BitTorrent++'),
(8, 'BitTornado'),
(9, 'BTQueue'),
(10, 'Burst'),
(11, 'CTorrent'),
(12, 'G3 Torrent'),
(13, 'Nova Torrent'),
(14, 'PTC Bittorrent'),
(15, 'Shadows Experimental'),
(16, 'Shareaza'),
(17, 'Torrentstorm'),
(18, 'TorrenTopia'),
(19, 'TurbotBT'),
(20, 'UPnP BT Client'),
(21, 'BitTorrent/3.4.2'),
(22, 'uTorrent/1300'),
(23, 'uTorrent/1600'),
(24, 'Azureus 2.5.0.0;Windows XP;Java 1.5.0_06'),
(25, 'uTorrent/1850(17414)');

-- --------------------------------------------------------

-- 
-- Table structure for table `comments`
-- 

CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user` int(10) unsigned NOT NULL default '0',
  `torrent` int(10) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `text` text NOT NULL,
  `ori_text` text NOT NULL,
  `editedby` int(10) unsigned NOT NULL default '0',
  `editedat` datetime NOT NULL default '0000-00-00 00:00:00',
  `request` int(11) NOT NULL default '0',
  `offer` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user` (`user`),
  KEY `torrent` (`torrent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `comments`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `countries`
-- 

CREATE TABLE `countries` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `flagpic` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=106 ;

-- 
-- Dumping data for table `countries`
-- 

INSERT INTO `countries` (`id`, `name`, `flagpic`) VALUES 
(1, 'Sweden', 'sweden.gif'),
(2, 'United States of America', 'usa.gif'),
(3, 'Russia', 'russia.gif'),
(4, 'Finland', 'finland.gif'),
(5, 'Canada', 'canada.gif'),
(6, 'France', 'france.gif'),
(7, 'Germany', 'germany.gif'),
(8, 'China', 'china.gif'),
(9, 'Italy', 'italy.gif'),
(10, 'Denmark', 'denmark.gif'),
(11, 'Norway', 'norway.gif'),
(12, 'United Kingdom', 'uk.gif'),
(13, 'Ireland', 'ireland.gif'),
(14, 'Poland', 'poland.gif'),
(15, 'Netherlands', 'netherlands.gif'),
(16, 'Belgium', 'belgium.gif'),
(17, 'Japan', 'japan.gif'),
(18, 'Brazil', 'brazil.gif'),
(19, 'Argentina', 'argentina.gif'),
(20, 'Australia', 'australia.gif'),
(21, 'New Zealand', 'newzealand.gif'),
(23, 'Spain', 'spain.gif'),
(24, 'Portugal', 'portugal.gif'),
(25, 'Mexico', 'mexico.gif'),
(26, 'Singapore', 'singapore.gif'),
(70, 'India', 'india.gif'),
(65, 'Albania', 'albania.gif'),
(29, 'South Africa', 'southafrica.gif'),
(30, 'South Korea', 'southkorea.gif'),
(31, 'Jamaica', 'jamaica.gif'),
(32, 'Luxembourg', 'luxembourg.gif'),
(33, 'Hong Kong', 'hongkong.gif'),
(34, 'Belize', 'belize.gif'),
(35, 'Algeria', 'algeria.gif'),
(36, 'Angola', 'angola.gif'),
(37, 'Austria', 'austria.gif'),
(38, 'Yugoslavia', 'yugoslavia.gif'),
(39, 'Western Samoa', 'westernsamoa.gif'),
(40, 'Malaysia', 'malaysia.gif'),
(41, 'Dominican Republic', 'dominicanrep.gif'),
(42, 'Greece', 'greece.gif'),
(43, 'Guatemala', 'guatemala.gif'),
(44, 'Israel', 'israel.gif'),
(45, 'Pakistan', 'pakistan.gif'),
(46, 'Czech Republic', 'czechrep.gif'),
(47, 'Serbia', 'serbia.gif'),
(48, 'Seychelles', 'seychelles.gif'),
(49, 'Taiwan', 'taiwan.gif'),
(50, 'Puerto Rico', 'puertorico.gif'),
(51, 'Chile', 'chile.gif'),
(52, 'Cuba', 'cuba.gif'),
(53, 'Congo', 'congo.gif'),
(54, 'Afghanistan', 'afghanistan.gif'),
(55, 'Turkey', 'turkey.gif'),
(56, 'Uzbekistan', 'uzbekistan.gif'),
(57, 'Switzerland', 'switzerland.gif'),
(58, 'Kiribati', 'kiribati.gif'),
(59, 'Philippines', 'philippines.gif'),
(60, 'Burkina Faso', 'burkinafaso.gif'),
(61, 'Nigeria', 'nigeria.gif'),
(62, 'Iceland', 'iceland.gif'),
(63, 'Nauru', 'nauru.gif'),
(64, 'Slovenia', 'slovenia.gif'),
(66, 'Turkmenistan', 'turkmenistan.gif'),
(67, 'Bosnia Herzegovina', 'bosniaherzegovina.gif'),
(68, 'Andorra', 'andorra.gif'),
(69, 'Lithuania', 'lithuania.gif'),
(71, 'Netherlands Antilles', 'nethantilles.gif'),
(72, 'Ukraine', 'ukraine.gif'),
(73, 'Venezuela', 'venezuela.gif'),
(74, 'Hungary', 'hungary.gif'),
(75, 'Romania', 'romania.gif'),
(76, 'Vanuatu', 'vanuatu.gif'),
(77, 'Vietnam', 'vietnam.gif'),
(78, 'Trinidad & Tobago', 'trinidadandtobago.gif'),
(79, 'Honduras', 'honduras.gif'),
(80, 'Kyrgyzstan', 'kyrgyzstan.gif'),
(81, 'Ecuador', 'ecuador.gif'),
(82, 'Bahamas', 'bahamas.gif'),
(83, 'Peru', 'peru.gif'),
(84, 'Cambodia', 'cambodia.gif'),
(85, 'Barbados', 'barbados.gif'),
(86, 'Bangladesh', 'bangladesh.gif'),
(87, 'Laos', 'laos.gif'),
(88, 'Uruguay', 'uruguay.gif'),
(89, 'Antigua Barbuda', 'antiguabarbuda.gif'),
(90, 'Paraguay', 'paraguay.gif'),
(93, 'Thailand', 'thailand.gif'),
(92, 'Union of Soviet Socialist Republics', 'ussr.gif'),
(94, 'Senegal', 'senegal.gif'),
(95, 'Togo', 'togo.gif'),
(96, 'North Korea', 'northkorea.gif'),
(97, 'Croatia', 'croatia.gif'),
(98, 'Estonia', 'estonia.gif'),
(99, 'Colombia', 'colombia.gif'),
(100, 'Lebanon', 'lebanon.gif'),
(101, 'Latvia', 'latvia.gif'),
(102, 'Costa Rica', 'costarica.gif'),
(103, 'Egypt', 'egypt.gif'),
(104, 'Bulgaria', 'bulgaria.gif'),
(105, 'Isla de Muerte', 'jollyroger.gif');

-- --------------------------------------------------------

-- 
-- Table structure for table `downloadspeed`
-- 

CREATE TABLE `downloadspeed` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=106 ;

-- 
-- Dumping data for table `downloadspeed`
-- 

INSERT INTO `downloadspeed` (`id`, `name`) VALUES 
(1, '64kbps'),
(2, '128kbps'),
(3, '256kbps'),
(4, '512kbps'),
(5, '768kbps'),
(6, '1Mbps'),
(7, '1.5Mbps'),
(8, '2Mbps'),
(9, '3Mbps'),
(10, '4Mbps'),
(11, '5Mbps'),
(12, '6Mbps'),
(13, '7Mbps'),
(14, '8Mbps'),
(15, '9Mbps'),
(16, '10Mbps'),
(17, '48Mbps'),
(18, '100Mbit');

-- --------------------------------------------------------

-- 
-- Table structure for table `faq`
-- 

CREATE TABLE `faq` (
  `id` int(10) NOT NULL auto_increment,
  `type` set('categ','item') NOT NULL default 'item',
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `flag` set('0','1','2','3') NOT NULL default '1',
  `categ` int(10) NOT NULL default '0',
  `order` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=71 ;

-- 
-- Dumping data for table `faq`
-- 

INSERT INTO `faq` (`id`, `type`, `question`, `answer`, `flag`, `categ`, `order`) VALUES 
(1, 'categ', 'Site information', '', '1', 0, 1),
(2, 'categ', 'User information', '', '1', 0, 2),
(3, 'categ', 'Stats', '', '1', 0, 3),
(4, 'categ', 'Uploading', '', '1', 0, 4),
(5, 'categ', 'Downloading', '', '1', 0, 5),
(6, 'categ', 'How can I improve my download speed?', '', '1', 0, 6),
(7, 'categ', 'My ISP uses a transparent proxy. What should I do?', '', '1', 0, 7),
(8, 'categ', 'Why can''t I connect? Is the site blocking me?', '', '1', 0, 8),
(9, 'categ', 'What if I can''t find the answer to my problem here?', '', '1', 0, 9),
(10, 'item', 'What is this bittorrent all about anyway? How do I get the files?', 'Check out <a class=altlink href="http://www.btfaq.com/">Brian''s BitTorrent FAQ and Guide</a>', '1', 1, 1),
(11, 'item', 'Where does the donated money go?', 'TS is situated on a dedicated server in the Netherlands. For the moment we have monthly running costs of approximately $ 213.\r\n', '1', 1, 2),
(12, 'item', 'Where can I get a copy of the source code?', 'Snapshots are available on the <a href=http://www.templateshares.net class=altlink>TEMPLATESHARES</a>. Please note: We do not give any kind of support on the source code so please don''t bug us about it. If it works, great, if not too bad. Use this software at your own risk!', '1', 1, 3),
(13, 'item', 'I registered an account but did not receive the confirmation e-mail!', 'You can use <a class=altlink href=delacct.php>this form</a> to delete the account so you can re-register.\r\nNote though that if you didn''t receive the email the first time it will probably not\r\nsucceed the second time either so you should really try another email address.', '1', 2, 1),
(14, 'item', 'I''ve lost my user name or password! Can you send it to me?', 'Please use <a class=altlink href=recover.php>this form</a> to have the login details mailed back to you.', '1', 2, 2),
(15, 'item', 'Can you rename my account?', 'We do not rename accounts. Please create a new one. (Use <a href=delacct.php class=altlink>this form</a> to\r\ndelete your present account.)', '1', 2, 3),
(16, 'item', 'Can you delete my (confirmed) account?', 'You can do it yourself by using <a href=delacct.php class=altlink>this form</a>.', '2', 2, 4),
(17, 'item', 'So, what''s MY ratio?', 'Click on your <a class=altlink href=usercp.php>profile</a>, then on your user name (at the top).<br>\r\n<br>\r\nIt''s important to distinguish between your overall ratio and the individual ratio on each torrent\r\nyou may be seeding or leeching. The overall ratio takes into account the total uploaded and downloaded\r\nfrom your account since you joined the site. The individual ratio takes into account those values for each torrent.<br>\r\n<br>\r\nYou may see two symbols instead of a number: "Inf.", which is just an abbreviation for Infinity, and\r\nmeans that you have downloaded 0 bytes while uploading a non-zero amount (ul/dl becomes infinity); "---",\r\nwhich should be read as "non-available", and shows up when you have both downloaded and uploaded 0 bytes\r\n(ul/dl = 0/0 which is an indeterminate amount).', '1', 2, 5),
(18, 'item', 'Why is my IP displayed on my details page?', 'Only you and the site moderators can view your IP address and email. Regular users do not see that information.', '1', 2, 6),
(19, 'item', 'Help! I cannot login!? (a.k.a. Login of Death)', 'This problem sometimes occurs with MSIE. Close all Internet Explorer windows and open Internet Options in the control panel. Click the Delete Cookies button. You should now be able to login.\r\n', '1', 2, 7),
(20, 'item', 'My IP address is dynamic. How do I stay logged in?', 'You do not have to anymore. All you have to do is make sure you are logged in with your actual\r\nIP when starting a torrent session. After that, even if the IP changes mid-session,\r\nthe seeding or leeching will continue and the statistics will update without any problem.', '2', 2, 8),
(21, 'item', 'Why is my port number reported as "---"? (And why should I care?)', 'The tracker has determined that you are firewalled or NATed and cannot accept incoming connections.\r\n<br>\r\n<br>\r\nThis means that other peers in the swarm will be unable to connect to you, only you to them. Even worse,\r\nif two peers are both in this state they will not be able to connect at all. This has obviously a\r\ndetrimental effect on the overall speed.\r\n<br>\r\n<br>\r\nThe way to solve the problem involves opening the ports used for incoming connections\r\n(the same range you defined in your client) on the firewall and/or configuring your\r\nNAT server to use a basic form of NAT\r\nfor that range instead of NAPT (the actual process differs widely between different router models.\r\nCheck your router documentation and/or support forum. You will also find lots of information on the\r\nsubject at <a class=altlink href="http://portforward.com/">PortForward</a>).', '1', 2, 9),
(22, 'item', 'What are the different user classes?', '<table cellspacing=3 cellpadding=0>\r\n<tr>\r\n<td class=embedded width=100 bgcolor="#F5F4EA">&nbsp; <b>User</b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded>The default class of new members.</td>\r\n</tr>\r\n<tr>\r\n<td class=embedded bgcolor="#F5F4EA">&nbsp; <b>Power User</b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded>Can download DOX over 1MB and view NFO files.</td>\r\n</tr>\r\n<tr>\r\n<td class=embedded bgcolor="#F5F4EA">&nbsp; <b><img src="pic/star.gif" alt="Star"></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded>Has donated money to Template Shares . </td>\r\n</tr>\r\n<tr>\r\n<td class=embedded valign=top bgcolor="#F5F4EA">&nbsp; <b>VIP</b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded valign=top>Same privileges as Power User and is considered an Elite Member of Template Shares. Immune to automatic demotion.</td>\r\n</tr>\r\n<tr>\r\n<td class=embedded bgcolor="#F5F4EA">&nbsp; <b>Other</b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded>Customised title.</td>\r\n</tr>\r\n<tr>\r\n<td class=embedded bgcolor="#F5F4EA">&nbsp; <b><font color="#4040c0">Uploader</font></b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded>Same as PU except with upload rights and immune to automatic demotion.</td>\r\n</tr>\r\n<tr>\r\n<td class=embedded valign=top bgcolor="#F5F4EA">&nbsp; <b><font color="#A83838">Moderator</font></b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded valign=top>Can edit and delete any uploaded torrents. Can also moderate usercomments and disable accounts.</td>\r\n</tr>\r\n<tr>\r\n<td class=embedded bgcolor="#F5F4EA">&nbsp; <b><font color="#A83838">Administrator</color></b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded>Can do just about anything.</td>\r\n</tr>\r\n<tr>\r\n<td class=embedded bgcolor="#F5F4EA">&nbsp; <b><font color="#A83838">SysOp</color></b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded>Redbeard (site owner).</td>\r\n</tr>\r\n</table>', '1', 2, 10),
(23, 'item', 'How does this promotion thing work anyway?', '<table cellspacing=3 cellpadding=0>\r\n<tr>\r\n<td class=embedded bgcolor="#F5F4EA" valign=top width=100>&nbsp; <b>Power User</b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded valign=top>Must have been be a member for at least 4 weeks, have uploaded at least 25GB and\r\nhave a ratio at or above 1.05.<br>\r\nThe promotion is automatic when these conditions are met. Note that you will be automatically demoted from<br>\r\nthis status if your ratio drops below 0.95 at any time.</td>\r\n</tr>\r\n<tr>\r\n<td class=embedded bgcolor="#F5F4EA">&nbsp; <b><img src="pic/star.gif" alt="Star"></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded>Just donate, and send a message to <a class=altlink href=sendmessage.php?receiver=1>Admin</a></td>\r\n</tr>\r\n<tr>\r\n<td class=embedded bgcolor="#F5F4EA" valign=top>&nbsp; <b>VIP</b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded valign=top>Assigned by mods at their discretion to users they feel contribute something special to the site.<br>\r\n(Anyone begging for VIP status will be automatically disqualified.)</td>\r\n</tr>\r\n<tr>\r\n<td class=embedded bgcolor="#F5F4EA">&nbsp; <b>Other</b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded>Conferred by mods at their discretion (not available to Users or Power Users).</td>\r\n</tr>\r\n<tr>\r\n<td class=embedded bgcolor="#F5F4EA">&nbsp; <b><font color="#4040c0">Uploader</font></b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded>Appointed by Admins/SysOp (see the ''Uploading'' section for conditions).</td>\r\n</tr>\r\n<tr>\r\n<td class=embedded bgcolor="#F5F4EA">&nbsp; <b><font color="#A83838">Moderator</font></b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded>You don''t ask us, we''ll ask you!</td>\r\n</tr>\r\n</table>', '1', 2, 11),
(24, 'item', 'Hey! I''ve seen Power Users with less than 25GB uploaded!', 'The PU limit used to be 10GB and we didn''t demote anyone when we raised it to 25GB.', '1', 2, 12),
(25, 'item', 'Why can''t my friend become a member?', 'There is a 75.000 users limit. When that number is reached we stop accepting new members. Accounts inactive for more than 42 days are automatically deleted, so keep trying. (There is no reservation or queuing system, don''t ask for that.)', '2', 2, 13),
(26, 'item', 'How do I add an avatar to my profile?', 'First, find an image that you like, and that is within the\r\n<a class=altlink href=rules.php>rules</a>. Then you will have\r\nto find a place to host it, such as our own <a class=altlink href=bitbucket-upload.php>BitBucket</a>.\r\n(Other popular choices are <a class="altlink" href="http://photobucket.com/">Photobucket</a>,\r\n<a class="altlink" href="http://uploadit.org/">Upload-It!</a> or\r\n<a class="altlink" href="http://www.imageshack.us/">ImageShack</a>).\r\nAll that is left to do is copy the URL you were given when\r\nuploading it to the avatar field in your <a class="altlink" href="usercp.php">profile</a>.<br>\r\n<br>\r\nPlease do not make a post just to test your avatar. If everything is allright you''ll see it\r\nin your details page.', '1', 2, 14),
(27, 'item', 'Most common reason for stats not updating', '<ul>\r\n<li>The user is cheating. (a.k.a. "Summary Ban")</li>\r\n<li>The server is overloaded and unresponsive. Just try to keep the session open until the server responds again. (Flooding the server with consecutive manual updates is not recommended.)</li>\r\n<li>You are using a faulty client. If you want to use an experimental or CVS version you do it at your own risk.</li>\r\n</ul>', '1', 3, 1),
(28, 'item', 'Best practices', '<ul>\r\n<li>If a torrent you are currently leeching/seeding is not listed on your profile, just wait or force a manual update.</li>\r\n<li>Make sure you exit your client properly, so that the tracker receives "event=completed".</li>\r\n<li>If the tracker is down, do not stop seeding. As long as the tracker is back up before you exit the client the stats should update properly.</li>\r\n</ul>', '1', 3, 2),
(29, 'item', 'May I use any bittorrent client?', 'Yes. The tracker now updates the stats correctly for all bittorrent clients. However, we still recommend\r\nthat you <b>avoid</b> the following clients:<br>\r\n<ul>\r\n<li>UTorrent</li>\r\n<li>Azerus</li>\r\n</ul>\r\nThese clients do not report correctly to the tracker when canceling/finishing a torrent session.\r\nIf you use them then a few MB may not be counted towards\r\nthe stats near the end, and torrents may still be listed in your profile for some time after you have closed the client.<br>\r\n<br>\r\nAlso, clients in alpha or beta version should be avoided.', '1', 3, 3),
(30, 'item', 'Why is a torrent I''m leeching/seeding listed several times in my profile?', 'If for some reason (e.g. pc crash, or frozen client) your client exits improperly and you restart it,\r\nit will have a new peer_id, so it will show as a new torrent. The old one will never receive a "event=completed"\r\nor "event=stopped" and will be listed until some tracker timeout. Just ignore it, it will eventually go away.', '1', 3, 4),
(31, 'item', 'I''ve finished or cancelled a torrent. Why is it still listed in my profile?', 'Some clients, notably TorrentStorm and Nova Torrent, do not report properly to the tracker when canceling or finishing a torrent.\r\nIn that case the tracker will keep waiting for some message - and thus listing the torrent as seeding or leeching - until some\r\ntimeout occurs. Just ignore it, it will eventually go away.', '1', 3, 5),
(32, 'item', 'Why do I sometimes see torrents I''m not leeching in my profile!?', 'When a torrent is first started, the tracker uses the IP to identify the user. Therefore the torrent will\r\nbecome associated with the user <i>who last accessed the site</i> from that IP. If you share your IP in some\r\nway (you are behind NAT/ICS, or using a proxy), and some of the persons you share it with are also users,\r\nyou may occasionally see their torrents listed in your profile. (If they start a torrent session from that\r\nIP and you were the last one to visit the site the torrent will be associated with you). Note that now\r\ntorrents listed in your profile will always count towards your total stats.', '2', 3, 6),
(33, 'item', 'Multiple IPs (Can I login from different computers?)', 'Yes, the tracker is now capable of following sessions from different IPs for the same user. A torrent is associated with the user when it starts, and only at that moment is the IP relevant. So if you want to seed/leech from computer A and computer B with the same account you should access the site from computer A, start the torrent there, and then repeat both steps from computer B (not limited to two computers or to a single torrent on each, this is just the simplest example). You do not need to login again when closing the torrent.\r\n', '2', 3, 7),
(34, 'item', 'How does NAT/ICS change the picture?', 'This is a very particular case in that all computers in the LAN will appear to the outside world as having the same IP. We must distinguish\r\nbetween two cases:<br>\r\n<br>\r\n<b>1.</b> <i>You are the single Template Shares users in the LAN</i><br>\r\n<br>\r\nYou should use the same Template Shares account in all the computers.<br>\r\n<br>\r\nNote also that in the ICS case it is preferable to run the BT client on the ICS gateway. Clients running on the other computers\r\nwill be unconnectable (their ports will be listed as "---", as explained elsewhere in the FAQ) unless you specify\r\nthe appropriate services in your ICS configuration (a good explanation of how to do this for Windows XP can be found\r\n<a class=altlink href="redirect.php?url=http://www.microsoft.com/downloads/details.aspx?FamilyID=1dcff3ce-f50f-4a34-ae67-cac31ccd7bc9&displaylang=en">here</a>).\r\nIn the NAT case you should configure different ranges for clients on different computers and create appropriate NAT rules in the router. (Details vary widely from router to router and are outside the scope of this FAQ. Check your router documentation and/or support forum.)<br>\r\n<br>\r\n<br>\r\n<b>2.</b> <i>There are multiple Template Shares users in the LAN</i><br>\r\n<br>\r\nAt present there is no way of making this setup always work properly with Template Shares.\r\nEach torrent will be associated with the user who last accessed the site from within\r\nthe LAN before the torrent was started.\r\nUnless there is cooperation between the users mixing of statistics is possible.\r\n(User A accesses the site, downloads a .torrent file, but does not start the torrent immediately.\r\nMeanwhile, user B accesses the site. User A then starts the torrent. The torrent will count\r\ntowards user B''s statistics, not user A''s.)\r\n<br>\r\n<br>\r\nIt is your LAN, the responsibility is yours. Do not ask us to ban other users\r\nwith the same IP, we will not do that. (Why should we ban <i>him</i> instead of <i>you</i>?)', '1', 3, 8),
(36, 'item', 'Why can''t I upload torrents?', 'Only specially authorized users (<font color="#4040c0"><b>Uploaders</b></font>) have permission to upload torrents.', '1', 4, 1),
(37, 'item', 'What criteria must I meet before I can join the <font color="#4040c0">Uploader</font> team?', 'You must be able to provide releases that:\r\n<li>include a proper NFO</li>\r\n<li>are genuine scene releases. If it''s not on <a class=altlink href="redirect.php?url=http://www.nforce.nl">NFOrce</a> then forget it! (except music)</li>\r\n<li>are not older than seven (7) days</li>\r\n<li>have all files in original format (usually 14.3 MB RARs)</li>\r\n<li>you''ll be able to seed, or make sure are well-seeded, for at least 24 hours.</li>\r\n<li>you should have atleast 2MBit upload bandwith.</li>\r\n</ul>\r\nIf you think you can match these criteria do not hesitate to <a class=altlink href=staff.php>contact</a> one of the administrators.<br>\r\n<b>Remember!</b> Write your application carefully! Be sure to include your UL speed and what kind of stuff you''re planning to upload.<br>\r\nOnly well written letters with serious intent will be considered.', '1', 4, 2),
(38, 'item', 'Can I upload your torrents to other trackers?', 'No. We are a closed, limited-membership community. Only registered users can use the TB tracker.\r\nPosting our torrents on other trackers is useless, since most people who attempt to download them will\r\nbe unable to connect with us. This generates a lot of frustration and bad-will against us at Template Shares,\r\nand will therefore not be tolerated.<br>\r\n<br>\r\nComplaints from other sites'' administrative staff about our torrents being posted on their sites will\r\nresult in the banning of the users responsible.<br>\r\n<br>\r\n(However, the files you download from us are yours to do as you please. You can always create another\r\ntorrent, pointing to some other tracker, and upload it to the site of your choice.)', '3', 4, 3),
(39, 'item', 'How do I use the files I''ve downloaded?', 'Check out <a class=altlink href=videoformats.php>this guide</a>.', '1', 5, 1),
(40, 'item', 'Downloaded a movie and don''t know what CAM/TS/TC/SCR means?', 'Check out <a class=altlink href=videoformats.php>this</a> guide.', '1', 5, 2),
(41, 'item', 'Why did an active torrent suddenly disappear?', 'There may be three reasons for this:<br>\r\n(<b>1</b>) The torrent may have been out-of-sync with the site\r\n<a class=altlink href=rules.php>rules</a>.<br>\r\n(<b>2</b>) The uploader may have deleted it because it was a bad release.\r\nA replacement will probably be uploaded to take its place.<br>\r\n(<b>3</b>) Torrents are automatically deleted after 28 days.', '2', 5, 3),
(42, 'item', 'How do I resume a broken download or reseed something?', 'Open the .torrent file. When your client asks you for a location, choose the location of the existing file(s) and it will resume/reseed the torrent.\r\n', '1', 5, 4),
(43, 'item', 'Why do my downloads sometimes stall at 99%?', 'The more pieces you have, the harder it becomes to find peers who have pieces you are missing. That is why downloads sometimes slow down or even stall when there are just a few percent remaining. Just be patient and you will, sooner or later, get the remaining pieces.\r\n', '1', 5, 5),
(44, 'item', 'What are these "a piece has failed an hash check" messages?', 'Bittorrent clients check the data they receive for integrity. When a piece fails this check it is\r\nautomatically re-downloaded. Occasional hash fails are a common occurrence, and you shouldn''t worry.<br>\r\n<br>\r\nSome clients have an (advanced) option/preference to ''kick/ban clients that send you bad data'' or\r\nsimilar. It should be turned on, since it makes sure that if a peer repeatedly sends you pieces that\r\nfail the hash check it will be ignored in the future.', '1', 5, 6),
(45, 'item', 'The torrent is supposed to be 100MB. How come I downloaded 120MB?', 'See the hash fails topic. If your client receives bad data it will have to redownload it, therefore\r\nthe total downloaded may be larger than the torrent size. Make sure the "kick/ban" option is turned on\r\nto minimize the extra downloads.', '1', 5, 7),
(46, 'item', 'Why do I get a "Not authorized (xx h) - READ THE FAQ" error?', 'From the time that each <b>new</b> torrent is uploaded to the tracker, there is a period of time that\r\nsome users must wait before they can download it.<br>\r\nThis delay in downloading will only affect users with a low ratio, and users with low upload amounts.<br>\r\n<br>\r\n<table cellspacing=3 cellpadding=0>\r\n <tr>\r\n	<td class=embedded width="70">Ratio below</td>\r\n	<td class=embedded width="40" bgcolor="#F5F4EA"><font color="#BB0000"><div align="center">0.50</div></font></td>\r\n	<td class=embedded width="10">&nbsp;</td>\r\n	<td class=embedded width="110">and/or upload below</td>\r\n	<td class=embedded width="40" bgcolor="#F5F4EA"><div align="center">5.0GB</div></td>\r\n	<td class=embedded width="10">&nbsp;</td>\r\n	<td class=embedded width="50">delay of</td>\r\n	<td class=embedded width="40" bgcolor="#F5F4EA"><div align="center">48h</div></td>\r\n </tr>\r\n <tr>\r\n	<td class=embedded>Ratio below</td>\r\n	<td class=embedded bgcolor="#F5F4EA"><font color="#A10000"><div align="center">0.65</div></font></td>\r\n	<td class=embedded width="10">&nbsp;</td>\r\n	<td class=embedded>and/or upload below</td>\r\n	<td class=embedded bgcolor="#F5F4EA"><div align="center">6.5GB</div></td>\r\n	<td class=embedded width="10">&nbsp;</td>\r\n	<td class=embedded>delay of</td>\r\n	<td class=embedded bgcolor="#F5F4EA"><div align="center">24h</div></td>\r\n </tr>\r\n <tr>\r\n	<td class=embedded>Ratio below</td>\r\n	<td class=embedded bgcolor="#F5F4EA"><font color="#880000"><div align="center">0.80</div></font></td>\r\n	<td class=embedded width="10">&nbsp;</td>\r\n	<td class=embedded>and/or upload below</td>\r\n	<td class=embedded bgcolor="#F5F4EA"><div align="center">8.0GB</div></td>\r\n	<td class=embedded width="10">&nbsp;</td>\r\n	<td class=embedded>delay of</td>\r\n	<td class=embedded bgcolor="#F5F4EA"><div align="center">12h</div></td>\r\n </tr>\r\n <tr>\r\n	<td class=embedded>Ratio below</td>\r\n	<td class=embedded bgcolor="#F5F4EA"><font color="#6E0000"><div align="center">0.95</div></font></td>\r\n	<td class=embedded width="10">&nbsp;</td>\r\n	<td class=embedded>and/or upload below</td>\r\n	<td class=embedded bgcolor="#F5F4EA"><div align="center">9.5GB</div></td>\r\n	<td class=embedded width="10">&nbsp;</td>\r\n	<td class=embedded>delay of</td>\r\n	<td class=embedded bgcolor="#F5F4EA"><div align="center">06h</div></td>\r\n </tr>\r\n</table>\r\n<br>\r\n"<b>And/or</b>" means any or both. Your delay will be the <b>largest</b> one for which you meet <b>at least</b> one condition.<br>\r\n<br>\r\nThis applies to new users as well, so opening a new account will not help. Note also that this\r\nworks at tracker level, you will be able to grab the .torrent file itself at any time.<br>\r\n<br>\r\n<!--The delay applies only to leeching, not to seeding. If you got the files from any other source and\r\nwish to seed them you may do so at any time irrespectively of your ratio or total uploaded.<br>-->\r\nN.B. Due to some users exploiting the ''no-delay-for-seeders'' policy we had to change it. The delay\r\nnow applies to both seeding and leeching. So if you are subject to a delay and get the files from\r\nsome other source you will not be able to seed them until the delay has elapsed.', '3', 5, 8),
(47, 'item', 'Why do I get a "rejected by tracker - Port xxxx is blacklisted" error?', 'Your client is reporting to the tracker that it uses one of the default bittorrent ports\r\n(6881-6889) or any other common p2p port for incoming connections.<br>\r\n<br>\r\nTemplate Shares does not allow clients to use ports commonly associated with p2p protocols.\r\nThe reason for this is that it is a common practice for ISPs to throttle those ports\r\n(that is, limit the bandwidth, hence the speed). <br>\r\n<br>\r\nThe blocked ports list include, but is not neccessarily limited to, the following:<br>\r\n<br>\r\n<table cellspacing=3 cellpadding=0>\r\n  <tr>\r\n	<td class=embedded width="80">Direct Connect</td>\r\n	<td class=embedded width="80" bgcolor="#F5F4EA"><div align="center">411 - 413</div></td>\r\n  </tr>\r\n  <tr>\r\n	<td class=embedded width="80">Kazaa</td>\r\n	<td class=embedded width="80" bgcolor="#F5F4EA"><div align="center">1214</div></td>\r\n  </tr>\r\n  <tr>\r\n	<td class=embedded width="80">eDonkey</td>\r\n	<td class=embedded width="80" bgcolor="#F5F4EA"><div align="center">4662</div></td>\r\n  </tr>\r\n  <tr>\r\n	<td class=embedded width="80">Gnutella</td>\r\n	<td class=embedded width="80" bgcolor="#F5F4EA"><div align="center">6346 - 6347</div></td>\r\n  </tr>\r\n  <tr>\r\n	<td class=embedded width="80">BitTorrent</td>\r\n	<td class=embedded width="80" bgcolor="#F5F4EA"><div align="center">6881 - 6889</div></td>\r\n </tr>\r\n</table>\r\n<br>\r\nIn order to use use our tracker you must  configure your client to use\r\nany port range that does not contain those ports (a range within the region 49152 through 65535 is preferable,\r\ncf. <a class=altlink href="http://www.iana.org/assignments/port-numbers">IANA</a>). Notice that some clients,\r\nlike Azureus 2.0.7.0 or higher, use a single port for all torrents, while most others use one port per open torrent. The size\r\nof the range you choose should take this into account (typically less than 10 ports wide. There\r\nis no benefit whatsoever in choosing a wide range, and there are possible security implications). <br>\r\n<br>\r\nThese ports are used for connections between peers, not client to tracker.\r\nTherefore this change will not interfere with your ability to use other trackers (in fact it\r\nshould <i>increase</i> your speed with torrents from any tracker, not just ours). Your client\r\nwill also still be able to connect to peers that are using the standard ports.\r\nIf your client does not allow custom ports to be used, you will have to switch to one that does.<br>\r\n<br>\r\nDo not ask us, or in the forums, which ports you should choose. The more random the choice is the harder\r\nit will be for ISPs to catch on to us and start limiting speeds on the ports we use.\r\nIf we simply define another range ISPs will start throttling that range also. <br>\r\n<br>\r\nFinally, remember to forward the chosen ports in your router and/or open them in your\r\nfirewall, should you have them.', '3', 5, 9),
(48, 'item', 'What''s this "IOError - [Errno13] Permission denied" error?', 'If you just want to fix it reboot your computer, it should solve the problem.\r\nOtherwise read on.<br>\r\n<br>\r\nIOError means Input-Output Error, and that is a file system error, not a tracker one.\r\nIt shows up when your client is for some reason unable to open the partially downloaded\r\ntorrent files. The most common cause is two instances of the client to be running\r\nsimultaneously:\r\nthe last time the client was closed it somehow didn''t really close but kept running in the\r\nbackground, and is therefore still\r\nlocking the files, making it impossible for the new instance to open them.<br>\r\n<br>\r\nA more uncommon occurrence is a corrupted FAT. A crash may result in corruption\r\nthat makes the partially downloaded files unreadable, and the error ensues. Running\r\nscandisk should solve the problem. (Note that this may happen only if you''re running\r\nWindows 9x - which only support FAT - or NT/2000/XP with FAT formatted hard drives.\r\nNTFS is much more robust and should never permit this problem.)', '3', 5, 10),
(49, 'item', 'What''s this "TTL" in the browse page?', 'The torrent''s Time To Live, in hours. It means the torrent will be deleted\r\nfrom the tracker after that many hours have elapsed (yes, even if it is still active).\r\nNote that this a maximum value, the torrent may be deleted at any time if it''s inactive.', '3', 5, 11),
(50, 'item', 'Do not immediately jump on new torrents', 'The download speed mostly depends on the seeder-to-leecher ratio (SLR). Poor download speed is\r\nmainly a problem with new and very popular torrents where the SLR is low.<br>\r\n<br>\r\n(Proselytising sidenote: make sure you remember that you did not enjoy the low speed.\r\n<b>Seed</b> so that others will not endure the same.)<br>\r\n<br>\r\nThere are a couple of things that you can try on your end to improve your speed:<br>\r\n<br>In particular, do not do it if you have a slow connection. The best speeds will be found around the\r\nhalf-life of a torrent, when the SLR will be at its highest. (The downside is that you will not be able to seed\r\nso much. It''s up to you to balance the pros and cons of this.)', '1', 6, 1),
(51, 'item', 'Limit your upload speed', 'The upload speed affects the download speed in essentially two ways:<br>\r\n<ul>\r\n    <li>Bittorrent peers tend to favour those other peers that upload to them. This means that if A and B\r\n	are leeching the same torrent and A is sending data to B at high speed then B will try to reciprocate.\r\n	So due to this effect high upload speeds lead to high download speeds.</li>\r\n\r\n    <li>Due to the way TCP works, when A is downloading something from B it has to keep telling B that\r\n        it received the data sent to him. (These are called acknowledgements - ACKs -, a sort of "got it!" messages).\r\n        If A fails to do this then B will stop sending data and wait. If A is uploading at full speed there may be no\r\n        bandwidth left for the ACKs and they will be delayed. So due to this effect excessively high upload speeds lead\r\n        to low download speeds.</li>\r\n</ul>\r\n\r\nThe full effect is a combination of the two. The upload should be kept as high as possible while allowing the\r\nACKs to get through without delay. <b>A good thumb rule is keeping the upload at about 80% of the theoretical\r\nupload speed.</b> You will have to fine tune yours to find out what works best for you. (Remember that keeping the\r\nupload high has the additional benefit of helping with your ratio.) <br>\r\n<br>\r\nIf you are running more than one instance of a client it is the overall upload speed that you must take into account.\r\nSome clients (e.g. Azureus) limit global upload speed, others (e.g. Shad0w''s) do it on a per torrent basis.\r\nKnow your client. The same applies if you are using your connection for anything else (e.g. browsing or ftp),\r\nalways think of the overall upload speed.', '1', 6, 2),
(52, 'item', 'Limit the number of simultaneous connections', 'Some operating systems (like Windows 9x) do not deal well with a large number of connections, and may even crash.\r\nAlso some home routers (particularly when running NAT and/or firewall with stateful inspection services) tend to become\r\nslow or crash when having to deal with too many connections. There are no fixed values for this, you may try 60 or 100\r\nand experiment with the value. Note that these numbers are additive, if you have two instances of\r\na client running the numbers add up.', '1', 6, 3),
(53, 'item', 'Limit the number of simultaneous uploads', 'Isn''t this the same as above? No. Connections limit the number of peers your client is talking to and/or\r\ndownloading from. Uploads limit the number of peers your client is actually uploading to. The ideal number is\r\ntypically much lower than the number of connections, and highly dependent on your (physical) connection.', '1', 6, 4),
(54, 'item', 'Just give it some time', 'As explained above peers favour other peers that upload to them. When you start leeching a new torrent you have\r\nnothing to offer to other peers and they will tend to ignore you. This makes the starts slow, in particular if,\r\nby change, the peers you are connected to include few or no seeders. The download speed should increase as soon\r\nas you have some pieces to share.', '1', 6, 5),
(55, 'item', 'Why is my browsing so slow while leeching?', 'Your download speed is always finite. If you are a peer in a fast torrent it will almost certainly saturate your\r\ndownload bandwidth, and your browsing will suffer. At the moment there is no client that allows you to limit the\r\ndownload speed, only the upload. You will have to use a third-party solution,\r\nsuch as <a class=altlink href="redirect.php?url=http://www.netlimiter.com/">NetLimiter</a>.<br>\r\n<br>\r\nBrowsing was used just as an example, the same would apply to gaming, IMing, etc...', '1', 6, 6),
(56, 'item', 'What is a proxy?', 'Basically a middleman. When you are browsing a site through a proxy your requests are sent to the proxy and the proxy\r\nforwards them to the site instead of you connecting directly to the site. There are several classifications\r\n(the terminology is far from standard):<br>\r\n<br>\r\n\r\n\r\n<table cellspacing=3 cellpadding=0>\r\n <tr>\r\n	<td class=embedded valign="top" bgcolor="#F5F4EA" width="100">&nbsp;Transparent</td>\r\n	<td class=embedded width="10">&nbsp;</td>\r\n	<td class=embedded valign="top">A transparent proxy is one that needs no configuration on the clients. It works by automatically redirecting all port 80 traffic to the proxy. (Sometimes used as synonymous for non-anonymous.)</td>\r\n </tr>\r\n <tr>\r\n	<td class=embedded valign="top" bgcolor="#F5F4EA">&nbsp;Explicit/Voluntary</td>\r\n	<td class=embedded width="10">&nbsp;</td>\r\n	<td class=embedded valign="top">Clients must configure their browsers to use them.</td>\r\n </tr>\r\n <tr>\r\n	<td class=embedded valign="top" bgcolor="#F5F4EA">&nbsp;Anonymous</td>\r\n	<td class=embedded width="10">&nbsp;</td>\r\n	<td class=embedded valign="top">The proxy sends no client identification to the server. (HTTP_X_FORWARDED_FOR header is not sent; the server does not see your IP.)</td>\r\n </tr>\r\n <tr>\r\n	<td class=embedded valign="top" bgcolor="#F5F4EA">&nbsp;Highly Anonymous</td>\r\n	<td class=embedded width="10">&nbsp;</td>\r\n	<td class=embedded valign="top">The proxy sends no client nor proxy identification to the server. (HTTP_X_FORWARDED_FOR, HTTP_VIA and HTTP_PROXY_CONNECTION headers are not sent; the server doesn''t see your IP and doesn''t even know you''re using a proxy.)</td>\r\n </tr>\r\n <tr>\r\n	<td class=embedded valign="top" bgcolor="#F5F4EA">&nbsp;Public</td>\r\n	<td class=embedded width="10">&nbsp;</td>\r\n	<td class=embedded valign="top">(Self explanatory)</td>\r\n </tr>\r\n</table>\r\n<br>\r\nA transparent proxy may or may not be anonymous, and there are several levels of anonymity.', '1', 7, 1),
(57, 'item', 'How do I find out if I''m behind a (transparent/anonymous) proxy?', 'Try <a href=http://proxyjudge.org class="altlink">ProxyJudge</a>. It lists the HTTP headers that the server where it is running\r\nreceived from you. The relevant ones are HTTP_CLIENT_IP, HTTP_X_FORWARDED_FOR and REMOTE_ADDR.<br>\r\n<br>\r\n<br>\r\n<b>Why is my port listed as "---" even though I''m not NAT/Firewalled?</b><a name="prox3"></a><br>\r\n<br>\r\nThe Template Shares tracker is quite smart at finding your real IP, but it does need the proxy to send the HTTP header\r\nHTTP_X_FORWARDED_FOR. If your ISP''s proxy does not then what happens is that the tracker will interpret the proxy''s IP\r\naddress as the client''s IP address. So when you login and the tracker tries to connect to your client to see if you are\r\nNAT/firewalled it will actually try to connect to the proxy on the port your client reports to be using for\r\nincoming connections. Naturally the proxy will not be listening on that port, the connection will fail and the\r\ntracker will think you are NAT/firewalled.', '1', 7, 2),
(58, 'item', 'Can I bypass my ISP''s proxy?', 'If your ISP only allows HTTP traffic through port 80 or blocks the usual proxy ports then you would need to use something\r\nlike <a href=http://www.socks.permeo.com>socks</a> and that is outside the scope of this FAQ.<br>\r\n<br>\r\nThe site accepts connections on port 81 besides the usual 80, and using them may be enough to fool some proxies. So the first\r\nthing to try should be connecting to www.templateshares.net:81. Note that even if this works your bt client will still try\r\nto connect to port 80 unless you edit the announce url in the .torrent file.<br>\r\n<br>\r\nOtherwise you may try the following:<br>\r\n<ul>\r\n    <li>Choose any public <b>non-anonymous</b> proxy that does <b>not</b> use port 80\r\n	(e.g. from <a href=http://tools.rosinstrument.com/proxy  class="altlink">this</a>,\r\n	<a href=http://www.proxy4free.com/index.html  class="altlink">this</a> or\r\n	<a href=http://www.samair.ru/proxy  class="altlink">this</a> list).</li>\r\n\r\n    <li>Configure your computer to use that proxy. For Windows XP, do <i>Start</i>, <i>Control Panel</i>, <i>Internet Options</i>,\r\n	<i>Connections</i>, <i>LAN Settings</i>, <i>Use a Proxy server</i>, <i>Advanced</i> and type in the IP and port of your chosen\r\n	proxy. Or from Internet Explorer use <i>Tools</i>, <i>Internet Options</i>, ...<br></li>\r\n\r\n    <li>(Facultative) Visit <a href=http://proxyjudge.org  class="altlink">ProxyJudge</a>. If you see an HTTP_X_FORWARDED_FOR in\r\n	the list followed by your IP then everything should be ok, otherwise choose another proxy and try again.<br></li>\r\n\r\n    <li>Visit Template Shares. Hopefully the tracker will now pickup your real IP (check your profile to make sure).</li>\r\n</ul>\r\n<br>\r\nNotice that now you will be doing all your browsing through a public proxy, which are typically quite slow.\r\nCommunications between peers do not use port 80 so their speed will not be affected by this, and should be better than when\r\nyou were "unconnectable".', '1', 7, 3),
(59, 'item', 'How do I make my bittorrent client use a proxy?', 'Just configure Windows XP as above. When you configure a proxy for Internet Explorer you''re actually configuring a proxy for\r\nall HTTP traffic (thank Microsoft and their "IE as part of the OS policy" ). On the other hand if you use another\r\nbrowser (Opera/Mozilla/Firefox) and configure a proxy there you''ll be configuring a proxy just for that browser. We don''t\r\nknow of any BT client that allows a proxy to be specified explicitly.', '1', 7, 4),
(60, 'item', 'Why can''t I signup from behind a proxy?', 'It is our policy not to allow new accounts to be opened from behind a proxy.', '1', 7, 5),
(61, 'item', 'Does this apply to other torrent sites?', 'This section was written for Template Shares, a closed, port 80-81 tracker. Other trackers may be open or closed, and many listen\r\non e.g. ports 6868 or 6969. The above does <b>not</b> necessarily apply to other trackers.', '1', 7, 6),
(62, 'item', 'Maybe my address is blacklisted?', 'The site blocks addresses listed in the (former) <a class=altlink href="http://methlabs.org/">PeerGuardian</a>\r\ndatabase, as well as addresses of banned users. This works at Apache/PHP level, it''s just a script that\r\nblocks <i>logins</i> from those addresses. It should not stop you from reaching the site. In particular\r\nit does not block lower level protocols, you should be able to ping/traceroute the server even if your\r\naddress is blacklisted. If you cannot then the reason for the problem lies elsewhere.<br>\r\n<br>\r\nIf somehow your address is indeed blocked in the PG database do not contact us about it, it is not our\r\npolicy to open <i>ad hoc</i> exceptions. You should clear your IP with the database maintainers instead.', '1', 8, 1),
(63, 'item', 'Your ISP blocks the site''s address', '(In first place, it''s unlikely your ISP is doing so. DNS name resolution and/or network problems are the usual culprits.)\r\n<br>\r\nThere''s nothing we can do.\r\nYou should contact your ISP (or get a new one). Note that you can still visit the site via a proxy, follow the instructions\r\nin the relevant section. In this case it doesn''t matter if the proxy is anonymous or not, or which port it listens to.<br>\r\n<br>\r\nNotice that you will always be listed as an "unconnectable" client because the tracker will be unable to\r\ncheck that you''re capable of accepting incoming connections.', '1', 8, 2),
(64, 'item', 'Alternate port (81)', 'Some of our torrents use ports other than the usual HTTP port 80. This may cause problems for some users,\r\nfor instance those behind some firewall or proxy configurations.\r\n\r\nYou can easily solve this by editing the .torrent file yourself with any torrent editor, e.g.\r\n<a href="http://sourceforge.net/projects/burst/" class="altlink">MakeTorrent</a>,\r\nand replacing the announce url templateshares.net:81 with templateshares.net:80 or just templateshares.net.<br>\r\n<br>\r\nEditing the .torrent with Notepad is not recommended. It may look like a text file, but it is in fact\r\na bencoded file. If for some reason you must use a plain text editor, change the announce url to\r\ntemplateshares.net:80, not templateshares.net. (If you''re thinking about changing the number before the\r\nannounce url instead, you know too much to be reading this.)', '2', 8, 3),
(65, 'item', 'You can try these:', 'Post in the <a class="altlink" href="forums">Forums</a>, by all means. You''ll find they\r\nare usually a friendly and helpful place,\r\nprovided you follow a few basic guidelines:\r\n<ul>\r\n<li>Make sure your problem is not really in this FAQ. There''s no point in posting just to be sent\r\nback here.\r\n<li>Before posting read the sticky topics (the ones at the top). Many times new information that\r\nstill hasn''t been incorporated in the FAQ can be found there.</li>\r\n<li>Help us in helping you. Do not just say "it doesn''t work!". Provide details so that we don''t\r\nhave to guess or waste time asking. What client do you use? What''s your OS? What''s your network setup? What''s the exact\r\nerror message you get, if any? What are the torrents you are having problems with? The more\r\nyou tell the easiest it will be for us, and the more probable your post will get a reply.</li>\r\n<li>And needless to say: be polite. Demanding help rarely works, asking for it usually does\r\nthe trick.', '1', 9, 1),
(66, 'item', 'Why do I get a "Not authorized (You are downloading your maximum number of allowed torrents - x)" error ?', 'This is part of the "Slot System". The slot system is being used to limit the concurrent downloads for users that have ratio below 0.95 and uploaded < 9.5 gb<br><br>\r\nIn detail: <br><br>\r\nUsers with ratio < 0.5 / Uploaded < 5 gb have only 1 download slot available <br>\r\nUsers with ratio < 0.65 / Uploaded < 6.5 gb have only 2 download slots available <br>\r\nUsers with ratio < 0.8 / Uploaded < 8 gb have only 3 download slots available <br>\r\nUsers with ratio < 0.95 / Uploaded < 9.5 gb have only 4 download slots available <br>\r\nUsers with ratio > 0.95 / Uploaded > 9.5 gb have unlimited download slots <br><br>\r\nIn all cases the seeding slots are unlimited. However if you have already filled all your available download slots and try to start seeding you will receive the same error. In this case you must free at least one download slot in order to start all your seeds and then start the download. If all your download slots are filled the system will deny any connection before validating if you want to download or seed. So first start your seeds and after that your downloads. <br>\r\n<br><br>\r\nIn any time, you can check your available slots in the member bar on top of the page <br><br>\r\nYou should also know that the slots for every user are calculated every 30min. Which means that if you cross a ratio barrier for more or less slots the tracker will need up to 30 min to be informed of this change, inspite of the maximum slots that you can see on your member bar.<br>', '3', 5, 12),
(67, 'item', 'What is the passkey System? How does it work? ', 'The passkey system has been implemented in order to substitute the ip checking system. This means that the tracker doesnt check anymore your logged ip in order to verify if you are logged in or registered with the tracker. Every user has a personal passkey, a random key generated by the system. When a user tries to download a torrent, its personal passkey is imprinted in the tracker url of the torrent, allowing to the tracker to identify any source connected on it. In this way, you can seed a torrent for example, at home and at your office simultaneously without any problem with the 2 different ips. Per torrent 3 simultaneous connections are permitted per user, and in case of leeching only 1 (That means you can leech a torrent from one location only at a time.', '3', 5, 13),
(68, 'item', 'Why do i get a "Unknown Passkey" error? ', 'You will get this error, firstly if you are not registered on our tracker, or if you havent downloaded the torrent to use from our webpage, when you were logged in. In this case, just register or log in and redownload the torrent.\r\n\r\nThere is a chance to get this error also, at the first time you download anything as a new user, or at the first download after you reset your passkey. The reason is simply that the tracker reviews the changes in the passkeys every few minutes and not instantly. For that reason just leave the torrent running for a few minutes, and you will get eventually an OK message from the tracker.', '3', 5, 14),
(69, 'item', 'When do i need to reset my passkey? ', '<ul><li> If your passkey has been leeched and other user(s) uses it to download torrents using your account. In this case, you will see torrents stated in your account that you are not leeching or seeding .</li>\r\n<li> When your clients hangs up or your connection is terminated without pressing the stop button of your client. In this case, in your account you will see that you are still leeching/seeding the torrents even that your client has been closed. Normally these "ghost peers" will be cleaned automatically within 30 minutes, but if you want to resume your downloads and the tracker denied that due to the fact that you "already are downloading the same torrents - Connection limit error" then you should reset your passkey and redownload the torrent, then resume it. </li></ul>', '3', 5, 15),
(70, 'item', 'What is DHT and Why must i turn it off?', 'DHT must be disabled in your client, DHT can cause your stats to be recorded incorrectly and could be seen as cheating also disable PEX (peer exchange) Anyone using this will be banned for cheating the system. Check your snatchlist regularly to ensure stats are being recorded correctly, allow 30mins for the tracker to update your stats. ', '3', 5, 16);

-- --------------------------------------------------------

-- 
-- Table structure for table `files`
-- 

CREATE TABLE `files` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `torrent` int(10) unsigned NOT NULL default '0',
  `filename` varchar(255) NOT NULL default '',
  `size` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `torrent` (`torrent`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=59 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `forums`
-- 

CREATE TABLE `forums` (
  `sort` tinyint(3) unsigned NOT NULL default '0',
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `description` varchar(200) default NULL,
  `minclassread` tinyint(3) unsigned NOT NULL default '0',
  `minclasswrite` tinyint(3) unsigned NOT NULL default '0',
  `postcount` int(10) unsigned NOT NULL default '0',
  `topiccount` int(10) unsigned NOT NULL default '0',
  `minclasscreate` tinyint(3) unsigned NOT NULL default '0',
  `forid` tinyint(4) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `forums`
-- 

INSERT INTO `forums` (`sort`, `id`, `name`, `description`, `minclassread`, `minclasswrite`, `postcount`, `topiccount`, `minclasscreate`, `forid`) VALUES 
(2, 1, 'General chat & discussion', 'Talk about anything here...', 0, 0, 1, 1, 0, 1),
(1, 2, 'Announcements & News ', 'News and upcoming events', 0, 0, 1, 1, 0, 2),
(3, 3, 'General Support & Help', 'Got any questions? Need help? Ask here!', 0, 0, 0, 0, 0, 1),
(4, 4, 'Request to be Uploader', 'Uploader Requests', 4, 4, 0, 0, 4, 3);

-- --------------------------------------------------------

-- 
-- Table structure for table `friends`
-- 

CREATE TABLE `friends` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `friendid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `userfriend` (`userid`,`friendid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `friends`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `funds`
-- 

CREATE TABLE `funds` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `cash` decimal(8,2) NOT NULL default '0.00',
  `user` int(10) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `funds`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `invites`
-- 

CREATE TABLE `invites` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `inviter` int(10) unsigned NOT NULL default '0',
  `invitee` varchar(80) NOT NULL default '',
  `hash` varchar(32) NOT NULL default '',
  `time_invited` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `inviter` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `invites`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `iplog`
-- 

CREATE TABLE `iplog` (
  `id` int(100) unsigned NOT NULL auto_increment,
  `ip` varchar(15) default NULL,
  `userid` int(10) default NULL,
  `access` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `iplog`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `ips`
-- 

CREATE TABLE `ips` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL,
  `ip` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `ips`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `leecherspmlog`
-- 

CREATE TABLE `leecherspmlog` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user` int(10) unsigned NOT NULL default '0',
  `date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `leecherspmlog`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `loginattempts`
-- 

CREATE TABLE `loginattempts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ip` varchar(15) NOT NULL default '',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `banned` enum('yes','no') NOT NULL default 'no',
  `attempts` int(10) NOT NULL default '0',
  `type` enum('login','recover') NOT NULL default 'login',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `loginattempts`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `messages`
-- 

CREATE TABLE `messages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sender` int(10) unsigned NOT NULL default '0',
  `receiver` int(10) unsigned NOT NULL default '0',
  `added` datetime default NULL,
  `subject` varchar(30) NOT NULL default 'No Subject',
  `msg` text,
  `unread` enum('yes','no') NOT NULL default 'yes',
  `poster` bigint(20) unsigned NOT NULL default '0',
  `location` smallint(6) NOT NULL default '1',
  `saved` enum('no','yes') NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  KEY `receiver` (`receiver`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `messages`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `news`
-- 

CREATE TABLE `news` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `body` text NOT NULL,
  `title` varchar(100) NOT NULL default 'Title',
  `cat` varchar(25) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `added` (`added`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `news`
-- 

INSERT INTO `news` (`id`, `userid`, `added`, `body`, `title`, `cat`) VALUES 
(1, 1, '2010-01-09 15:07:42', 'No news is good news....', 'Testing,', '1');

-- --------------------------------------------------------

-- 
-- Table structure for table `newscats`
-- 

CREATE TABLE `newscats` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `img` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `newscats`
-- 

INSERT INTO `newscats` (`id`, `name`, `img`) VALUES 
(1, 'General', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `notconnectablepmlog`
-- 

CREATE TABLE `notconnectablepmlog` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user` int(10) unsigned NOT NULL default '0',
  `date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `notconnectablepmlog`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `offers`
-- 

CREATE TABLE `offers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `name` varchar(225) default NULL,
  `descr` text NOT NULL,
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `yeah` int(10) unsigned NOT NULL default '0',
  `against` int(10) unsigned NOT NULL default '0',
  `category` int(11) NOT NULL default '0',
  `comments` int(11) NOT NULL default '0',
  `allowed` enum('allowed','pending','denied') NOT NULL default 'pending',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `offers`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `offervotes`
-- 

CREATE TABLE `offervotes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `offerid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `vote` enum('yeah','against') NOT NULL default 'yeah',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `offervotes`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `options`
-- 

CREATE TABLE `options` (
  `option_name` varchar(64) NOT NULL default '',
  `option_value` longtext NOT NULL,
  PRIMARY KEY  (`option_name`),
  KEY `option_name` (`option_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `options`
-- 

INSERT INTO `options` (`option_name`, `option_value`) VALUES 
('software_database_version', '1.1'),
('lastcron', '1263324183'),
('cache_index_news', '600'),
('cache_index_stats', '600'),
('cache_topten', '600'),
('cache_admin_stats', '900'),
('cache_admin_vcheck', '300'),
('pg_enable', 'yes'),
('pg_server', 'http://freetosu.sourceforge.net/pg.txt'),
('active_plugins', 'a:1:{i:0;s:25:"index-elements/legend.php";}');

-- --------------------------------------------------------

-- 
-- Table structure for table `overforums`
-- 

CREATE TABLE `overforums` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `description` varchar(200) default NULL,
  `minclassview` tinyint(3) unsigned NOT NULL default '0',
  `forid` tinyint(3) unsigned NOT NULL default '1',
  `sort` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `overforums`
-- 

INSERT INTO `overforums` (`id`, `name`, `description`, `minclassview`, `forid`, `sort`) VALUES 
(1, 'General', '', 0, 1, 1),
(2, 'Guidelines', '', 0, 1, 0),
(3, 'Staff Forums', '', 4, 0, 3);

-- --------------------------------------------------------

-- 
-- Table structure for table `peerguardian`
-- 

CREATE TABLE `peerguardian` (
  `first` text NOT NULL,
  `last` text NOT NULL,
  `comment` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `peerguardian`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `peers`
-- 

CREATE TABLE `peers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `torrent` int(10) unsigned NOT NULL default '0',
  `peer_id` varchar(20) NOT NULL default '',
  `ip` varchar(64) NOT NULL default '',
  `port` smallint(5) unsigned NOT NULL default '0',
  `uploaded` bigint(20) unsigned NOT NULL default '0',
  `downloaded` bigint(20) unsigned NOT NULL default '0',
  `to_go` bigint(20) unsigned NOT NULL default '0',
  `seeder` enum('yes','no') NOT NULL default 'no',
  `started` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_action` datetime NOT NULL default '0000-00-00 00:00:00',
  `prev_action` datetime NOT NULL default '0000-00-00 00:00:00',
  `connectable` enum('yes','no') NOT NULL default 'yes',
  `userid` int(10) unsigned NOT NULL default '0',
  `agent` varchar(60) NOT NULL default '',
  `finishedat` int(10) unsigned NOT NULL default '0',
  `downloadoffset` bigint(20) unsigned NOT NULL default '0',
  `uploadoffset` bigint(20) unsigned NOT NULL default '0',
  `passkey` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `torrent_peer_id` (`torrent`,`peer_id`),
  KEY `torrent` (`torrent`),
  KEY `torrent_seeder` (`torrent`,`seeder`),
  KEY `last_action` (`last_action`),
  KEY `connectable` (`connectable`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `peers`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pmboxes`
-- 

CREATE TABLE `pmboxes` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `boxnumber` tinyint(4) NOT NULL default '2',
  `name` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `pmboxes`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pollanswers`
-- 

CREATE TABLE `pollanswers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pollid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `selection` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pollid` (`pollid`),
  KEY `selection` (`selection`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `pollanswers`
-- 

INSERT INTO `pollanswers` (`id`, `pollid`, `userid`, `selection`) VALUES 
(1, 1, 1, 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `polls`
-- 

CREATE TABLE `polls` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `question` varchar(255) NOT NULL default '',
  `option0` varchar(40) NOT NULL default '',
  `option1` varchar(40) NOT NULL default '',
  `option2` varchar(40) NOT NULL default '',
  `option3` varchar(40) NOT NULL default '',
  `option4` varchar(40) NOT NULL default '',
  `option5` varchar(40) NOT NULL default '',
  `option6` varchar(40) NOT NULL default '',
  `option7` varchar(40) NOT NULL default '',
  `option8` varchar(40) NOT NULL default '',
  `option9` varchar(40) NOT NULL default '',
  `option10` varchar(40) NOT NULL default '',
  `option11` varchar(40) NOT NULL default '',
  `option12` varchar(40) NOT NULL default '',
  `option13` varchar(40) NOT NULL default '',
  `option14` varchar(40) NOT NULL default '',
  `option15` varchar(40) NOT NULL default '',
  `option16` varchar(40) NOT NULL default '',
  `option17` varchar(40) NOT NULL default '',
  `option18` varchar(40) NOT NULL default '',
  `option19` varchar(40) NOT NULL default '',
  `sort` enum('yes','no') NOT NULL default 'yes',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `polls`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `posts`
-- 

CREATE TABLE `posts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `topicid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `added` datetime default NULL,
  `body` text,
  `editedby` int(10) unsigned NOT NULL default '0',
  `editedat` datetime NOT NULL default '0000-00-00 00:00:00',
  `subject` text,
  PRIMARY KEY  (`id`),
  KEY `topicid` (`topicid`),
  KEY `userid` (`userid`),
  FULLTEXT KEY `body` (`body`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `posts`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `ratings`
-- 

CREATE TABLE `ratings` (
  `torrent` int(10) unsigned NOT NULL default '0',
  `topic` int(10) unsigned NOT NULL default '0',
  `user` int(10) unsigned NOT NULL default '0',
  `rating` tinyint(3) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `ratings`
-- 

INSERT INTO `ratings` (`torrent`, `topic`, `user`, `rating`, `added`) VALUES 
(0, 1, 1, 5, '2010-01-09 15:08:43');

-- --------------------------------------------------------

-- 
-- Table structure for table `readposts`
-- 

CREATE TABLE `readposts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `topicid` int(10) unsigned NOT NULL default '0',
  `lastpostread` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `userid` (`id`),
  KEY `topicid` (`topicid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `readposts`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `regimages`
-- 

CREATE TABLE `regimages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `imagehash` varchar(32) NOT NULL default '',
  `imagestring` varchar(8) NOT NULL default '',
  `dateline` bigint(30) NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=130 ;

-- 
-- Dumping data for table `regimages`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `reports`
-- 

CREATE TABLE `reports` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `addedby` int(10) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `votedfor` int(10) unsigned NOT NULL default '0',
  `votedfor_xtra` int(10) unsigned NOT NULL default '0',
  `type` enum('torrent','user','forum','comment') NOT NULL default 'torrent',
  `reason` varchar(255) NOT NULL default '',
  `dealtby` int(10) unsigned NOT NULL default '0',
  `dealtwith` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `reports`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `requests`
-- 

CREATE TABLE `requests` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `request` varchar(225) default NULL,
  `descr` text NOT NULL,
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `hits` int(10) unsigned NOT NULL default '0',
  `cat` int(10) unsigned NOT NULL default '0',
  `filledby` int(10) unsigned NOT NULL default '0',
  `filledurl` varchar(70) default NULL,
  `filled` enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`),
  KEY `id_added` (`id`,`added`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `requests`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `rules`
-- 

CREATE TABLE `rules` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `text` text NOT NULL,
  `public` enum('yes','no') NOT NULL default 'yes',
  `class` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- 
-- Dumping data for table `rules`
-- 

INSERT INTO `rules` (`id`, `title`, `text`, `public`, `class`) VALUES 
(1, '<h2>General rules - <font color=#004E98>Breaking these rules can and will get you banned!</font></h2>', '[*]Do not defy the moderators expressed wishes!\r\n[*]Do not upload our torrents to other trackers! (See the [url=faq.php#4][b]FAQ[/b][/url] for details.)\r\n[*]You will only get [b]one[/b] warning! After that it''s bye bye Kansas!!', 'yes', 5),
(2, '<h2>Downloading rules - <font color=#004E98>By not following these rules you will lose download privileges!</font></h2>', '[*]Access to the newest torrents is conditional on a good ratio! (See the [url=faq.php#5][b]FAQ[/b][/url] for details.)\r\n[*]Low ratios may result in severe consequences, including banning in extreme cases!', 'yes', 0),
(3, '<h2>General Forum Guidelines - <font color=#004E98>Please follow these guidelines or else you might end up with a warning!</font></h2>', '[*]No aggressive behaviour or flaming in the forums.\r\n[*]No trashing of other peoples topics (i.e. SPAM).\r\n[*]No language other than English in the forums.\r\n[*]No systematic foul language (and none at all on  titles).\r\n[*]No links to warez or crack sites in the forums.\r\n[*]No requesting or posting of serials, CD keys, passwords or cracks in the forums.\r\n[*]No requesting if there has been no "[url=http://www.nforce.nl]scene[/url]" release in the last 7 days.\r\n[*]No bumping... (All bumped threads will be deleted.)\r\n[*]No images larger than 800x600, and preferably web-optimised.\r\n[*]No double posting. If you wish to post again, and yours is the last post in the thread please use the EDIT function, instead of posting a double.\r\n[*]Please ensure all questions are posted in the correct section! (Game questions in the Games section, Apps questions in the Apps section)\r\n[*]Last, please read the [url=faq.php][b]FAQ[/b][/url] before asking any questions!\r\n', 'yes', 0),
(4, '<h2>Avatar Guidelines - <font color=#004E98>Please try to follow these guidelines</font></h2>', '[*]The allowed formats are .gif, .jpg and .png.\r\n[*]Be considerate. Resize your images to a width of 150 px and a size of no more than 150 KB. (Browsers will rescale them anyway: smaller images will be expanded and will not look good; larger images will just waste bandwidth and CPU cycles.) For now this is just a guideline but it will be automatically enforced in the near future.\r\n[*]Do not use potentially offensive material involving porn, religious material, animal / human cruelty or ideologically charged images. Mods have wide discretion on what is acceptable. If in doubt PM one.', 'yes', 0),
(5, '<h2>Uploading rules - <font color=#004E98>Torrents violating these rules may be deleted without notice</font></h2>', '[*]All uploads must include a proper NFO.\r\n[*]Only scene releases. If it''s not on [url=http://www.nforce.nl][b]NFOrce[/b][/url] or [url=http://www.grokmusiq.com][b]grokMusiQ[/b][/url] then forget it!\r\n[*]The stuff must not be older than seven (7) days.\r\n[*]All files must be in original format (usually 14.3 MB RARs).\r\n[*]Pre-release stuff should be labeled with an *ALPHA* or *BETA* tag.\r\n[*]Make sure not to include any serial numbers, CD keys or similar in the description (you do [b]not[/b] need to edit the NFO!).\r\n[*]Make sure your torrents are well-seeded for at least 24 hours.\r\n[*]Do not include the release date in the torrent name.\r\n[*]Stay active! You risk being demoted if you have no active torrents.\r\n\r\nIf you have something interesting that somehow violate these rules (e.g. not ISO format), ask a mod and we might make an exception.', 'yes', 0),
(6, '<h2>Moderating Rules - <font color=#004E98>Use your better judgement!</font></h2>', '[*]The most important rule: Use your better judgment!\r\n[*]Don''t be afraid to say [b]NO[/b]! (a.k.a. "Helshad''s rule".)\r\n[*]Don''t defy another mod in public, instead send a PM or through IM.\r\n[*]Be tolerant! Give the user(s) a chance to reform.\r\n[*]Don''t act prematurely, let the users make their mistakes and THEN correct them.\r\n[*]Try correcting any "off topics" rather then closing a thread.\r\n[*]Move topics rather than locking them.\r\n[*]Be tolerant when moderating the Chit-chat section (give them some slack).\r\n[*]If you lock a topic, give a brief explanation as to why you''re locking it.\r\n[*]Before you disable a user account, send him/her a PM and if they reply, put them on a 2 week trial.\r\n[*]Don''t disable a user account until he or she has been a member for at least 4 weeks.\r\n[*][b]Always[/b] state a reason (in the user comment box) as to why the user is being banned / warned.', 'no', 4),
(7, '<h2>Moderating rules - <font color=#004E98>Whom to promote and why</font></h2>', '[*][b] Power User [/b] - Automatically given to (and revoked from) users who have been members for at least 4 weeks, have uploaded at least 25 GB and have a share ratio above 1.05. Moderator changes of status last only until the next execution of the script.\r\n[*][img]/pic/star.gif[/img]- This status is given ONLY by General SysOp since he is the only one who can verify that they actually donated something.\r\n[*][b] VIP [/b] - Conferred to users you feel contribute something special to the site. (Anyone begging for VIP status will be automatically disqualified)\r\n[*][b] Other [/b] - Customised title given to special users only (Not available to Users or Power Users).\r\n[*][b][color=#4040c0] Uploader [/color][/b] - Appointed by Admins/SysOp. Send a PM to some Admin or SysOp if you think you''ve got a good candidate.\r\n[*][b][color=#A83838] Modetator [/color][/b] - Appointed by SysOp only. Send a PM to some SysOp if you think you''ve got a good candidate.', 'no', 4),
(8, '<h2>Moderating options - <font color=#004E98>What are my privileges as a mod?</font></h2>', '[*]You can delete and edit forum posts.\r\n[*]You can delete and edit torrents.\r\n[*]You can delete and change users avatars.\r\n[*]You can disable user accounts.\r\n[*]You can edit the title of VIP''s.\r\n[*]You can see the complete info of all users.\r\n[*]You can add comments to users (for other mods and admins to read).\r\n[*]You can stop reading now ''cuz you already knew about these options.\r\n[*]Lastly, check out the [url=staff.php][b]Staff[/b][/url] page (top right corner).', 'no', 4);

-- --------------------------------------------------------

-- 
-- Table structure for table `searchcloud`
-- 

CREATE TABLE `searchcloud` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `searchedfor` varchar(50) NOT NULL,
  `howmuch` int(10) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `searchcloud`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `shoutbox`
-- 

CREATE TABLE `shoutbox` (
  `id` smallint(6) NOT NULL auto_increment,
  `userid` smallint(6) NOT NULL default '0',
  `username` varchar(25) NOT NULL default '',
  `date` int(11) NOT NULL default '0',
  `text` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=50 ;

-- 
-- Dumping data for table `shoutbox`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `sitelog`
-- 

CREATE TABLE `sitelog` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` datetime default NULL,
  `txt` text,
  PRIMARY KEY  (`id`),
  KEY `added` (`added`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=73 ;

-- 
-- Dumping data for table `sitelog`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `snatched`
-- 

CREATE TABLE `snatched` (
  `id` int(11) NOT NULL auto_increment,
  `torrentid` int(11) default '0',
  `userid` int(11) default '0',
  `torrent` int(10) unsigned NOT NULL default '0',
  `torrent_name` varchar(255) NOT NULL default '',
  `torrent_category` int(10) unsigned NOT NULL default '0',
  `port` smallint(5) unsigned NOT NULL default '0',
  `uploaded` bigint(20) unsigned NOT NULL default '0',
  `downloaded` bigint(20) unsigned NOT NULL default '0',
  `to_go` bigint(20) unsigned NOT NULL default '0',
  `seeder` enum('yes','no') NOT NULL default 'no',
  `last_action` datetime NOT NULL default '0000-00-00 00:00:00',
  `startdat` datetime NOT NULL default '0000-00-00 00:00:00',
  `completedat` datetime NOT NULL default '0000-00-00 00:00:00',
  `connectable` enum('yes','no') NOT NULL default 'yes',
  `agent` varchar(60) NOT NULL default '',
  `finished` enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  KEY `finished` (`torrentid`),
  KEY `torrentid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `snatched`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `staffmessages`
-- 

CREATE TABLE `staffmessages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sender` int(10) unsigned NOT NULL default '0',
  `added` datetime default NULL,
  `msg` text,
  `subject` varchar(100) NOT NULL default '',
  `answeredby` int(10) unsigned NOT NULL default '0',
  `answered` tinyint(1) NOT NULL default '0',
  `answer` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `staffmessages`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `stafftools`
-- 

CREATE TABLE `stafftools` (
  `id` int(11) NOT NULL auto_increment,
  `name` text NOT NULL,
  `file` text NOT NULL,
  `desc` text NOT NULL,
  `usergroups` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=53 ;

-- 
-- Dumping data for table `stafftools`
-- 

INSERT INTO `stafftools` (`id`, `name`, `file`, `desc`, `usergroups`) VALUES 
(1, 'Manage tracker categories', 'category.php', 'Here you can manage all tracker categories', '[6],[7]'),
(2, 'Manage Tracker Countries', 'country.php', 'Here you can manage all tracker countries', '[6],[7]'),
(3, 'Manage Tracker Forums', 'forummanage.php', 'Edit/Delete forum', '[6],[7]'),
(4, 'Mysql Stats', 'mysql_stats.php', 'See MySql stats', '[6],[7]'),
(5, 'Mass Mailer', 'massmail.php', 'Send e-mail to all users on the tracker', '[6],[7]'),
(6, 'Do Cleanup', 'docleanup.php', 'Do cleanup functions', '[6],[7]'),
(7, 'FAQ Manage', 'faqmanage.php', 'Edit/Add/Delete FAQ Page', '[6],[7]'),
(8, 'RULES Manage', 'modrules.php', 'Edit/Add/Delete RULES Page', '[6],[7]'),
(9, 'View Log', 'log.php', 'Show Logs', '[6],[7]'),
(10, 'Add Bonus Points', 'amountbonus.php', 'Add Bonus Points to ALL Users or one user.', '[6],[7]'),
(11, 'Ban System', 'bans.php', 'Ban / Unban IP', '[6],[7]'),
(12, 'Change Email', 'changemail.php', 'Change User Email Address', '[6],[7]'),
(13, 'Change Username', 'changeusername.php', 'Change UserName', '[6],[7]'),
(14, 'View Reports', 'reports.php', 'View all reports', '[6],[7]'),
(15, 'Spam', 'spam.php', 'Check Spam Pms', '[6],[7]'),
(16, 'Failed Logins', 'maxlogin.php', 'Show Failed Login Attempts', '[6],[7]'),
(17, 'Ban Agent', 'agentban.php', 'User Agent Ban', '[6],[7]'),
(18, 'Delete account', 'delacctadmin.php', 'Delete User Account', '[6],[7]'),
(19, 'Bitbucket', 'bitbucketlog.php', 'Bitbucket Log', '[6],[7]'),
(20, 'Ban email address', 'bannedemails.php', 'Ban EMAILs stop registration.', '[6],[7]'),
(21, 'Optimize & Repair', 'optimize.php', 'Optimize & Repair tables.', '[6],[7]'),
(22, 'Traceroute', 'traceroute.php', 'Trace single IP', '[6],[7]'),
(23, 'Poll overview', 'polloverview.php', 'View poll votes', '[6],[7]'),
(24, 'Pending users confirmation', 'pusers.php', 'Here you can confirm unconfirmed accounts', '[5],[6],[7]'),
(25, 'Reset themes', 'resetheme.php', 'Set all user defined themes to default', '[6],[7]'),
(26, 'Add User', 'adduser.php', 'Create new user account', '[5],[6],[7]'),
(27, 'List Unconfirmed Users', 'unco.php', 'View unconfirmed accounts', '[5],[6],[7]'),
(28, 'Reset users password', 'reset.php', 'Rest lost Passwords', '[5],[6],[7]'),
(29, 'Mass PM', 'staffmess.php', 'Send PM to all users', '[5],[6],[7]'),
(30, 'Find not connectable users', 'notconnectable.php', 'View All Unconnectable Users', '[4],[5],[6],[7]'),
(31, 'Warned users', 'warned.php', 'See all warned users on tracker', '[5],[6],[7]'),
(32, 'Free Leech', 'freeleech.php', 'Set ALL Torrents FREE or NORMAL with one click', '[5],[6],[7]'),
(33, 'Reports', 'reports.php', 'Show Reports (forum,comment,torrent)', '[5],[6],[7]'),
(34, 'Tracker Statistics', 'statistics.php', 'View detailed and many statistics about your site', '[4],[5],[6],[7]'),
(35, 'Mass Re-seed', 'mass_reseed.php', 'Request an mass reseed.', '[4],[5],[6],[7]'),
(36, 'Leechers', 'leechers.php', 'Show users with ratio under 0.40', '[4],[5],[6],[7]'),
(37, 'Uploader Info Panel', 'uploaderinfopanel.php', 'Addon to monitor uploades activity', '[4],[5],[6],[7]'),
(38, 'Ratio 100', 'ratio100.php', 'Show users with ratio above 100', '[4],[5],[6],[7]'),
(39, 'Abnormal Upload Speed Detector', 'cheaters.php', 'See cheaters', '[4],[5],[6],[7]'),
(40, 'Duplicate IP Check', 'ipcheck.php', 'Check for Duplicate IP Users', '[4],[5],[6],[7]'),
(41, 'Show All Clients', 'allagents.php', 'Show All Clients (currently downloading/uploading/seeding)', '[4],[5],[6],[7]'),
(42, 'Show Users', 'userslist.php', 'List Registered Users.', '[4],[5],[6],[7]'),
(43, 'Staff BOX', 'staffbox.php', 'Staffbox (Staff Contacts)', '[4],[5],[6],[7]'),
(44, 'Make Poll', 'makepoll.php', 'Make a new poll', '[4],[5],[6],[7]'),
(45, 'Uploaders', 'uploaders.php', 'Uploaders', '[4],[5],[6],[7]'),
(46, 'List Polls', 'polloverview.php', 'List Polls', '[4],[5],[6],[7]'),
(47, 'Stats', 'stats.php', 'Tracker Stats', '[4],[5],[6],[7]'),
(48, 'Ip Test', 'testip.php', 'IP Test', '[4],[5],[6],[7]'),
(49, 'Ip to country', 'iptocountry.php', 'Determine from which country a user is using an ip', '[4],[5],[6],[7]'),
(50, 'Ip to country', 'iptocountry.php', 'Determine from which country a user is using an ip', '[4],[5],[6],[7]'),
(51, 'Ip to country', 'iptocountry.php', 'Determine from which country a user is using an ip', '[4],[5],[6],[7]'),
(52, 'Database Tools', 'database.php', 'Here you can repair, backup and do many things with your mysql database.', '[7]');

-- --------------------------------------------------------

-- 
-- Table structure for table `stats`
-- 

CREATE TABLE `stats` (
  `recordonline24` varchar(255) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `stats`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `subs`
-- 

CREATE TABLE `subs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `cd` int(10) unsigned NOT NULL default '0',
  `frame` varchar(255) NOT NULL default '',
  `comment` text NOT NULL,
  `added` datetime default NULL,
  `size` int(10) unsigned NOT NULL default '0',
  `uppedby` int(10) unsigned NOT NULL default '0',
  `url` varchar(255) NOT NULL default '',
  `hits` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `subs`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `subscriptions`
-- 

CREATE TABLE `subscriptions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `topicid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `subscriptions`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `thanks`
-- 

CREATE TABLE `thanks` (
  `torrentid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `thanks`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `topics`
-- 

CREATE TABLE `topics` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `subject` varchar(40) default NULL,
  `locked` enum('yes','no') NOT NULL default 'no',
  `forumid` int(10) unsigned NOT NULL default '0',
  `lastpost` int(10) unsigned NOT NULL default '0',
  `sticky` enum('yes','no') NOT NULL default 'no',
  `views` int(10) unsigned NOT NULL default '0',
  `iconid` varchar(10) NOT NULL default '0',
  `numratings` int(10) unsigned NOT NULL default '0',
  `ratingsum` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`),
  KEY `subject` (`subject`),
  KEY `lastpost` (`lastpost`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `topics`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `torrents`
-- 

CREATE TABLE `torrents` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `info_hash` varchar(20) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `save_as` varchar(255) NOT NULL default '',
  `search_text` text NOT NULL,
  `descr` text NOT NULL,
  `ori_descr` text NOT NULL,
  `category` int(10) unsigned NOT NULL default '0',
  `size` bigint(20) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `type` enum('single','multi') NOT NULL default 'single',
  `numfiles` int(10) unsigned NOT NULL default '0',
  `comments` int(10) unsigned NOT NULL default '0',
  `views` int(10) unsigned NOT NULL default '0',
  `hits` int(10) unsigned NOT NULL default '0',
  `times_completed` int(10) unsigned NOT NULL default '0',
  `leechers` int(10) unsigned NOT NULL default '0',
  `seeders` int(10) unsigned NOT NULL default '0',
  `last_action` datetime NOT NULL default '0000-00-00 00:00:00',
  `visible` enum('yes','no') NOT NULL default 'yes',
  `banned` enum('yes','no') NOT NULL default 'no',
  `owner` int(10) unsigned NOT NULL default '0',
  `numratings` int(10) unsigned NOT NULL default '0',
  `ratingsum` int(10) unsigned NOT NULL default '0',
  `nfo` text NOT NULL,
  `free` enum('yes','no') default 'no',
  `doubleupload` enum('yes','no') default 'no',
  `seen` blob NOT NULL,
  `anonymous` enum('yes','no') NOT NULL default 'no',
  `sticky` enum('yes','no') NOT NULL default 'no',
  `tube` varchar(80) NOT NULL default '',
  `imageurl` text,
  `Genre` varchar(120) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `info_hash` (`info_hash`),
  KEY `owner` (`owner`),
  KEY `visible` (`visible`),
  KEY `category_visible` (`category`,`visible`),
  FULLTEXT KEY `ft_search` (`search_text`,`ori_descr`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `torrents`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `transferlog`
-- 

CREATE TABLE `transferlog` (
  `added` datetime NOT NULL,
  `fromid` int(10) NOT NULL,
  `toid` int(10) NOT NULL,
  `amountmb` int(10) NOT NULL,
  `comment` varchar(185) default 'No Comment'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `transferlog`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `uploadspeed`
-- 

CREATE TABLE `uploadspeed` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

-- 
-- Dumping data for table `uploadspeed`
-- 

INSERT INTO `uploadspeed` (`id`, `name`) VALUES 
(1, '64kbps'),
(2, '128kbps'),
(3, '256kbps'),
(4, '512kbps'),
(5, '768kbps'),
(6, '1Mbps'),
(7, '1.5Mbps'),
(8, '2Mbps'),
(9, '3Mbps'),
(10, '4Mbps'),
(11, '5Mbps'),
(12, '6Mbps'),
(13, '7Mbps'),
(14, '8Mbps'),
(15, '9Mbps'),
(16, '10Mbps'),
(17, '48Mbps'),
(18, '100Mbit');

-- --------------------------------------------------------

-- 
-- Table structure for table `usergroups`
-- 

CREATE TABLE `usergroups` (
  `id` int(10) NOT NULL auto_increment,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `isbanned` enum('yes','no') NOT NULL default 'no',
  `canpm` enum('yes','no') NOT NULL default 'yes',
  `candwd` enum('yes','no') NOT NULL default 'yes',
  `canup` enum('yes','no') NOT NULL default 'no',
  `canreq` enum('yes','no') NOT NULL default 'yes',
  `canof` enum('yes','no') NOT NULL default 'yes',
  `canpc` enum('yes','no') NOT NULL default 'yes',
  `canvo` enum('yes','no') NOT NULL default 'yes',
  `canth` enum('yes','no') NOT NULL default 'yes',
  `canka` enum('yes','no') NOT NULL default 'yes',
  `canrp` enum('yes','no') NOT NULL default 'no',
  `canusercp` enum('yes','no') NOT NULL default 'yes',
  `canviewotherprofile` enum('yes','no') NOT NULL default 'yes',
  `canchat` enum('yes','no') NOT NULL default 'yes',
  `canmemberlist` enum('yes','no') NOT NULL default 'yes',
  `canfriendslist` enum('yes','no') NOT NULL default 'yes',
  `cantopten` enum('yes','no') NOT NULL default 'yes',
  `cansettingspanel` enum('yes','no') NOT NULL default 'no',
  `canstaffpanel` enum('yes','no') NOT NULL default 'no',
  `showonstaff` enum('yes','no') NOT NULL default 'no',
  `usernamestyle` varchar(255) NOT NULL default '{u}',
  `pmquote` int(11) NOT NULL,
  `iscustom` enum('yes','no') NOT NULL default 'yes',
  `minclasstopr` varchar(200) NOT NULL,
  `minclasstoedit` varchar(200) NOT NULL,
  `maxclasstopr` varchar(200) NOT NULL,
  `maxclasstoedit` varchar(200) NOT NULL,
  `candeletetorrent` enum('yes','no') NOT NULL default 'no',
  `hasfreeleech` enum('yes','no') NOT NULL default 'no',
  `antifloodcheck` enum('yes','no') NOT NULL default 'yes',
  `antifloodtime` text NOT NULL,
  `args` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

-- 
-- Dumping data for table `usergroups`
-- 

INSERT INTO `usergroups` (`id`, `title`, `description`, `isbanned`, `canpm`, `candwd`, `canup`, `canreq`, `canof`, `canpc`, `canvo`, `canth`, `canka`, `canrp`, `canusercp`, `canviewotherprofile`, `canchat`, `canmemberlist`, `canfriendslist`, `cantopten`, `cansettingspanel`, `canstaffpanel`, `showonstaff`, `usernamestyle`, `pmquote`, `iscustom`, `minclasstopr`, `minclasstoedit`, `maxclasstopr`, `maxclasstoedit`, `candeletetorrent`, `hasfreeleech`, `antifloodcheck`, `antifloodtime`, `args`) VALUES 
(6, 'SysOp', 'Has the full power.', 'no', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'yes', 'yes', '<span style="color: #2587A7;"><strong>{u} </strong></span>', 1000, 'no', '7', '7', '7', '7', 'yes', 'yes', 'no', '0', 'a:1:{s:15:"canpostintopics";s:2:"no";}'),
(0, 'User', 'Simple User', 'no', 'yes', 'yes', 'no', 'no', 'no', 'yes', 'yes', 'yes', 'yes', 'no', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'no', 'no', '{u}', 23, 'no', '4', '4', '7', '7', 'no', 'no', 'yes', '60', 'a:1:{s:15:"canpostintopics";s:3:"yes";}'),
(1, 'Power User', 'The Class above user', 'no', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'no', 'no', '<span style="color: #f9a200;"><strong>{u}</strong></span>', 50, 'no', '4', '4', '7', '7', 'no', 'no', 'yes', '30', NULL),
(2, 'Vip', 'If a user donated to the tracker, he is vip', 'no', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'no', 'no', '<span style="color: #009F00;"><strong>{u} </strong></span>', 900, 'no', '4', '4', '7', '7', 'no', 'no', 'yes', '25', NULL),
(3, 'Uploader', 'User with upload privileges.', 'no', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'no', 'no', '<span style="color:#6464FF;"><strong>{u} </strong></span>', 200, 'no', '4', '4', '7', '7', 'no', 'no', 'yes', '25', NULL),
(4, 'Moderator', 'Can delete torrents,forum posts, etc...', 'no', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'yes', 'yes', '<span style="color: #ff5151;"><strong>{u}</strong></span>', 250, 'no', '5', '5', '7', '7', 'yes', 'yes', 'no', '0', 'a:1:{s:15:"canpostintopics";s:2:"no";}'),
(5, 'Administrator', 'Almost full power :)', 'no', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'yes', 'yes', '<span style="color: #CC00FF;"><strong><em>{u} </em></strong></span>', 500, 'no', '6', '6', '7', '7', 'yes', 'yes', 'no', '0', 'a:1:{s:15:"canpostintopics";s:2:"no";}'),
(7, 'Staff Leader', 'The user with the supreme power :D :P', 'no', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', '<span style="color: darkred;"><strong><em>{u} </em></strong></span>', 1000000000, 'no', '7', '7', '7', '7', 'yes', 'yes', 'no', '0', 'a:1:{s:15:"canpostintopics";s:3:"yes";}');

-- --------------------------------------------------------

-- 
-- Table structure for table `userhits`
-- 

CREATE TABLE `userhits` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `hitid` int(10) unsigned NOT NULL default '0',
  `number` int(10) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `userhits`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(40) NOT NULL default '',
  `old_password` varchar(40) NOT NULL default '',
  `passhash` varchar(32) NOT NULL default '',
  `secret` varchar(20) NOT NULL default '',
  `email` varchar(80) NOT NULL default '',
  `status` enum('pending','confirmed') NOT NULL default 'pending',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_login` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_access` datetime NOT NULL default '0000-00-00 00:00:00',
  `forum_access` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_staffmsg` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_pm` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_comment` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_post` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_shout` datetime NOT NULL default '0000-00-00 00:00:00',
  `editsecret` varchar(20) NOT NULL default '',
  `privacy` enum('strong','normal','low') NOT NULL default 'normal',
  `stylesheet` int(10) default '1',
  `info` text,
  `acceptpms` enum('yes','friends','no') NOT NULL default 'yes',
  `commentpm` enum('yes','no') NOT NULL default 'yes',
  `ip` varchar(15) NOT NULL default '',
  `class` tinyint(3) unsigned NOT NULL default '0',
  `avatar` varchar(100) NOT NULL default '',
  `uploaded` bigint(20) unsigned NOT NULL default '0',
  `downloaded` bigint(20) unsigned NOT NULL default '0',
  `title` varchar(30) NOT NULL default '',
  `country` int(10) unsigned NOT NULL default '0',
  `notifs` varchar(100) NOT NULL default '',
  `modcomment` text NOT NULL,
  `enabled` enum('yes','no') NOT NULL default 'yes',
  `avatars` enum('yes','no') NOT NULL default 'yes',
  `donor` enum('yes','no') NOT NULL default 'no',
  `donated` decimal(8,2) NOT NULL default '0.00',
  `donoruntil` datetime NOT NULL default '0000-00-00 00:00:00',
  `total_donated` decimal(8,2) NOT NULL default '0.00',
  `warned` enum('yes','no') NOT NULL default 'no',
  `warneduntil` datetime NOT NULL default '0000-00-00 00:00:00',
  `torrentsperpage` int(3) unsigned NOT NULL default '0',
  `topicsperpage` int(3) unsigned NOT NULL default '0',
  `postsperpage` int(3) unsigned NOT NULL default '0',
  `deletepms` enum('yes','no') NOT NULL default 'yes',
  `savepms` enum('yes','no') NOT NULL default 'no',
  `support` enum('yes','no') NOT NULL default 'no',
  `supportfor` text NOT NULL,
  `supportlang` varchar(50) NOT NULL,
  `passkey` varchar(32) NOT NULL default '',
  `permban` enum('yes','no') NOT NULL default 'no',
  `last_browse` int(11) NOT NULL default '0',
  `uploadpos` enum('yes','no') NOT NULL default 'yes',
  `forumpost` enum('yes','no') NOT NULL default 'yes',
  `downloadpos` enum('yes','no') NOT NULL default 'yes',
  `clientselect` int(10) unsigned default '0',
  `signatures` enum('yes','no') NOT NULL default 'yes',
  `signature` varchar(225) NOT NULL default '',
  `tzoffset` int(10) NOT NULL default '0',
  `dst` enum('yes','no') NOT NULL default 'no',
  `cheat` smallint(6) NOT NULL default '0',
  `download` int(10) unsigned NOT NULL default '0',
  `upload` int(10) unsigned NOT NULL default '0',
  `invites` int(10) NOT NULL default '4',
  `invited_by` int(10) NOT NULL default '0',
  `gender` enum('Male','Female','N/A') NOT NULL default 'N/A',
  `vip_added` enum('yes','no') NOT NULL default 'no',
  `vip_until` datetime NOT NULL default '0000-00-00 00:00:00',
  `seedbonus` decimal(5,1) NOT NULL default '0.0',
  `bonuscomment` text NOT NULL,
  `parked` enum('yes','no') NOT NULL default 'no',
  `leechwarn` enum('yes','no') NOT NULL default 'no',
  `leechwarnuntil` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastwarned` datetime NOT NULL default '0000-00-00 00:00:00',
  `timeswarned` int(10) NOT NULL default '0',
  `warnedby` varchar(40) NOT NULL default '',
  `page` text NOT NULL,
  `passhint` text,
  `hintanswer` text,
  `birthday` text,
  `skin` text,
  `subscription_pm` enum('yes','no') NOT NULL default 'no',
  `icq` varchar(255) NOT NULL,
  `msn` varchar(255) NOT NULL,
  `aim` varchar(255) NOT NULL,
  `yahoo` varchar(255) NOT NULL,
  `skype` varchar(255) NOT NULL,
  `pmbox` enum('yes','no') NOT NULL default 'yes',
  `hits` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `status_added` (`status`,`added`),
  KEY `ip` (`ip`),
  KEY `uploaded` (`uploaded`),
  KEY `downloaded` (`downloaded`),
  KEY `country` (`country`),
  KEY `last_access` (`last_access`),
  KEY `enabled` (`enabled`),
  KEY `warned` (`warned`),
  KEY `cheat` (`cheat`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `users`
-- 