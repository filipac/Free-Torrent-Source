<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;

ADMIN::check();
$action = isset($_POST['action']) ? htmlspecialchars($_POST['action']) : (isset($_GET['action']) ? htmlspecialchars($_GET['action']) : 'main');
if ($action == 'setallfree') {
	mysql_query("UPDATE torrents SET free = 'yes' WHERE free = 'no'");
	stderr('Success','All torrents have been set free..');
}elseif ($action == 'setallnormal') {
	mysql_query("UPDATE torrents SET free = 'no' WHERE free = 'yes'");
	stderr('Success','All torrents have been set normal..');
}elseif ($action == 'main'){
	stderr('Select action','Click <a href=freeleech.php?action=setallfree>here</a> to set all torrents free.. <br><br> Click <a href=freeleech.php?action=setallnormal>here</a> to set all torrents normal..', false);
}
?>