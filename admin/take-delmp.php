<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;

loggedinorreturn();

if ((!ur::cstaff()) AND get_user_class() >= UC_SYSOP) {
if(isset($_POST["delmp"])) {
	$do="DELETE FROM messages WHERE id IN (" . implode(", ", $_POST[delmp]) . ")";
	$res=sql_query($do);
	}
}
header("Refresh: 0; url=spam.php");
?>