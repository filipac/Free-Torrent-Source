<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;
function bark($msg) { 
 stdhead(); 
   stdmsg("Failed", $msg); 
 stdfoot(); 
 exit; 
} 
 
ADMIN::check();

$res = sql_query ("SELECT id FROM reports WHERE id IN (" . implode(", ", $_POST[delreports]) . ")");

while ($arr = mysql_fetch_assoc($res))
       sql_query ("DELETE from reports WHERE id = $arr[id]") or sqlerr();
header("Refresh: 0; url=reports.php"); 
?>