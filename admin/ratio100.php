<?php

ob_start("ob_gzhandler");
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;

ADMIN::check();



stdhead("Ratio is 100 of above");

$mainquery = "SELECT id as userid, username, added, uploaded, downloaded, uploaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS upspeed, downloaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS downspeed FROM users WHERE enabled = 'yes'";

$limit = 250;
$order = "added ASC";
//$order = "uploaded / downloaded ASC, downloaded DESC";
$extrawhere = " AND uploaded / downloaded > 100 and class <= 2";
$r = sql_query($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
usertable_r100($r, "Ratio Above 100");

stdfoot();

?>