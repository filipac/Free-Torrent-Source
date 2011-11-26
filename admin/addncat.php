<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;

ADMIN::check();
mysql_query("INSERT INTO `newscats` (name,img) VALUES ('$_POST[name]', '$_POST[image]');") or die(mysql_error());
?>