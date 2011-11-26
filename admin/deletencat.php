<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;

ADMIN::check();
mysql_query("DELETE FROM newscats WHERE id = '$_GET[id]'") or die(mysql_error());
doredir('../news.php');
?>