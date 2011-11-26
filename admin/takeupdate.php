<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;

 
loggedinorreturn(); 

if ((!ur::cstaff()) AND get_user_class() < 4)
       die();

$res = sql_query ("SELECT id FROM reports WHERE dealtwith=0 AND id IN (" . implode(", ", $_POST[delreport]) . ")");

while ($arr = mysql_fetch_assoc($res))
       sql_query ("UPDATE reports SET dealtwith=1, dealtby = $CURUSER[id] WHERE id = $arr[id]") or sqlerr();

header("Refresh: 0; url=reports.php"); 
?>