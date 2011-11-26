<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;

ADMIN::check();
$action = isset($_POST['action']) ? htmlspecialchars($_POST['action']) : (isset($_GET['action']) ? htmlspecialchars($_GET['action']) : 'main');
if ($action == 'reset') {
	mysql_query("UPDATE users SET skin = ''");
	stderr('Success','All skins reset to default..');
}elseif ($action == 'main'){
	stderr('Are you sure?','Click <a href=resetheme.php?action=reset>here</a> to set all user defined template to default..', false);
}
?>