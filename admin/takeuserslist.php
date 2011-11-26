<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;

loggedinorreturn();

if ((!ur::cstaff()) AND get_user_class() < UC_MODERATOR)
	stderr("Error", "Permission denied.");

loggedinorreturn();


if (isset($_POST['delusr'])) {

	foreach ($_POST['delusr'] as $del){
		sql_query("DELETE FROM users WHERE id = ".sqlesc($del));
	}
	//$do="DELETE FROM users WHERE id IN (" . implode(", ", mysql_real_escape_string($_POST[delusr])) . ") AND class='0'";
	//$res=sql_query($do);
}
if (isset($_POST['banusr'])) {
	foreach ($_POST['banusr'] as $ban){
		sql_query("UPDATE users SET enabled = 'no' WHERE id = ".sqlesc($ban));
	}
}
if (isset($_POST['unbanusr'])) {
	foreach ($_POST['unbanusr'] as $unban){
		sql_query("UPDATE users SET enabled = 'yes' WHERE id = ".sqlesc($unban));
	}
}
header("Location: userslist.php");
?>