<?php
$rootpath = '../';
$passChecK = true;
include $rootpath.'include/bittorrent.php';
global $mysql_db,$mysql_host,$mysql_pass,$mysql_user;
$db_server   = $mysql_host;
$db_name     = $mysql_db;
$db_username = $mysql_user;
$db_password = $mysql_pass;
$dbconnection = @mysql_connect($db_server,$db_username,$db_password);
  if ($dbconnection) 
    $db = mysql_select_db($db_name);
  if (!$dbconnection || !$db) 
  { echo ("<p class=\"error\">Database connection failed due to ".mysql_error()."</p>\n");
    echo ("<p>Edit the database settings in ".$_SERVER["SCRIPT_FILENAME"]." or contact your database provider</p>\n");
    $error=true;
  }
include ("../include/upgrade.php");
switch($_GET['type']) {
default:
_sqldo("ALTER TABLE `topics` ADD `iconid` VARCHAR( 10 ) NOT NULL ;");
_sqldo("DROP TABLE IF EXISTS `peerguardian`;");
_sqldo("CREATE TABLE `peerguardian` (
  `first` text NOT NULL,
  `last` text NOT NULL,
  `comment` text NOT NULL
) TYPE=MyISAM AUTO_INCREMENT=1;");
_sqldo("ALTER TABLE torrents ADD `doubleupload` enum('yes','no')  default 'no';");
_sqldo("DROP TABLE IF EXISTS `snatched`;");
_sqldo("CREATE TABLE `snatched` (
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
  `finished` enum('yes','no')  NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  KEY `finished` (`torrentid`),
  KEY `torrentid` (`userid`)
) TYPE=MyISAM AUTO_INCREMENT=1;");
_sqldo("ALTER TABLE usergroups ADD `args` text;");
_sqldo("ALTER TABLE posts ADD `subject` text;");  
_sqldo("DROP TABLE IF EXISTS `usergroups`;");
_sqldo("CREATE TABLE IF NOT EXISTS `usergroups` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `isbanned` enum('yes','no') NOT NULL DEFAULT 'no',
  `canpm` enum('yes','no') NOT NULL DEFAULT 'yes',
  `candwd` enum('yes','no') NOT NULL DEFAULT 'yes',
  `canup` enum('yes','no') NOT NULL DEFAULT 'no',
  `canreq` enum('yes','no') NOT NULL DEFAULT 'yes',
  `canof` enum('yes','no') NOT NULL DEFAULT 'yes',
  `canpc` enum('yes','no') NOT NULL DEFAULT 'yes',
  `canvo` enum('yes','no') NOT NULL DEFAULT 'yes',
  `canth` enum('yes','no') NOT NULL DEFAULT 'yes',
  `canka` enum('yes','no') NOT NULL DEFAULT 'yes',
  `canrp` enum('yes','no') NOT NULL DEFAULT 'no',
  `canusercp` enum('yes','no') NOT NULL DEFAULT 'yes',
  `canviewotherprofile` enum('yes','no') NOT NULL DEFAULT 'yes',
  `canchat` enum('yes','no') NOT NULL DEFAULT 'yes',
  `canmemberlist` enum('yes','no') NOT NULL DEFAULT 'yes',
  `canfriendslist` enum('yes','no') NOT NULL DEFAULT 'yes',
  `cantopten` enum('yes','no') NOT NULL DEFAULT 'yes',
  `cansettingspanel` enum('yes','no') NOT NULL DEFAULT 'no',
  `canstaffpanel` enum('yes','no') NOT NULL DEFAULT 'no',
  `showonstaff` enum('yes','no') NOT NULL DEFAULT 'no',
  `usernamestyle` varchar(255) NOT NULL DEFAULT '{u}',
  `pmquote` int(11) NOT NULL,
  `iscustom` enum('yes','no') NOT NULL DEFAULT 'yes',
  `minclasstopr` varchar(200) NOT NULL,
  `minclasstoedit` varchar(200) NOT NULL,
  `maxclasstopr` varchar(200) NOT NULL,
  `maxclasstoedit` varchar(200) NOT NULL,
  `candeletetorrent` enum('yes','no') NOT NULL DEFAULT 'no',
  `hasfreeleech` enum('yes','no') NOT NULL DEFAULT 'no',
  `antifloodcheck` enum('yes','no') NOT NULL DEFAULT 'yes',
  `antifloodtime` text NOT NULL,
  `args` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;");
$_longq1 = <<<lq
INSERT INTO `usergroups` (`id`, `title`, `description`, `isbanned`, `canpm`, `candwd`, `canup`, `canreq`, `canof`, `canpc`, `canvo`, `canth`, `canka`, `canrp`, `canusercp`, `canviewotherprofile`, `canchat`, `canmemberlist`, `canfriendslist`, `cantopten`, `cansettingspanel`, `canstaffpanel`, `showonstaff`, `usernamestyle`, `pmquote`, `iscustom`, `minclasstopr`, `minclasstoedit`, `maxclasstopr`, `maxclasstoedit`, `candeletetorrent`, `hasfreeleech`, `antifloodcheck`, `antifloodtime`, `args`) VALUES
(6, 'SysOp', 'Has the full power.', 'no', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'yes', 'yes', '<span style="color: #2587A7;"><strong>{u} </strong></span>', 1000, 'no', '7', '7', '7', '7', 'yes', 'yes', 'no', '0', 'a:1:{s:15:"canpostintopics";s:2:"no";}'),
(0, 'User', 'Simple User', 'no', 'yes', 'yes', 'no', 'no', 'no', 'yes', 'yes', 'yes', 'yes', 'no', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'no', 'no', '{u}', 23, 'no', '4', '4', '7', '7', 'no', 'no', 'yes', '60', 'a:1:{s:15:"canpostintopics";s:3:"yes";}'),
(1, 'Power User', 'The Class above user', 'no', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'no', 'no', '<span style="color: #f9a200;"><strong>{u}</strong></span>', 50, 'no', '4', '4', '7', '7', 'no', 'no', 'yes', '30', NULL),
(2, 'Vip', 'If a user donated to the tracker, he is vip', 'no', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'no', 'no', '<span style="color: #009F00;"><strong>{u} </strong></span>', 900, 'no', '4', '4', '7', '7', 'no', 'no', 'yes', '25', NULL),
(3, 'Uploader', 'User with upload privileges.', 'no', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'no', 'no', '<span style="color:#6464FF;"><strong>{u} </strong></span>', 200, 'no', '4', '4', '7', '7', 'no', 'no', 'yes', '25', NULL),
(4, 'Moderator', 'Can delete torrents,forum posts, etc...', 'no', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'yes', 'yes', '<span style="color: #ff5151;"><strong>{u}</strong></span>', 250, 'no', '5', '5', '7', '7', 'yes', 'yes', 'no', '0', 'a:1:{s:15:"canpostintopics";s:2:"no";}'),
(5, 'Administrator', 'Almost full power :)', 'no', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'yes', 'yes', '<span style="color: #CC00FF;"><strong><em>{u} </em></strong></span>', 500, 'no', '6', '6', '7', '7', 'yes', 'yes', 'no', '0', 'a:1:{s:15:"canpostintopics";s:2:"no";}'),
(7, 'Staff Leader', 'The user with the supreme power :D :P', 'no', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', '<span style="color: darkred;"><strong><em>{u} </em></strong></span>', 1000000000, 'no', '7', '7', '7', '7', 'yes', 'yes', 'no', '0', 'a:1:{s:15:"canpostintopics";s:3:"yes";}');
lq;
_sqldo($_longq1);
_sqldo("UPDATE usergroups SET id=0 WHERE title='User' LIMIT 1;");
_sqldo("DROP TABLE IF EXISTS `stafftools`;");
_sqldo("CREATE TABLE `stafftools` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `file` text NOT NULL,
  `desc` text NOT NULL,
  `usergroups` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;");
_sqldo("INSERT INTO `stafftools` (`id`, `name`, `file`, `desc`, `usergroups`) VALUES
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
");
_sqldo("ALTER TABLE `users` ADD `dst` enum('yes','no') NOT NULL default 'no';");
_sqldo("ALTER TABLE `shoutbox` CHANGE `text` `text` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
global $VERSION;
$version = $VERSION->getShortVersion();
update("software_database_version",$version);
global $_nr_q;
_message("Database was upgraded. Now you are up-to-date and you can use FTS $version. Enjoy!");
break;
}
?>
