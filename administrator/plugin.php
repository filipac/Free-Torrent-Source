<?php
$rootpath = '../' ;
include $rootpath . 'include/bittorrent.php' ;
include "func.php";
loggedinorreturn() ;
if ( ! ur::isadmin() )
{
    write_log( "User $CURUSER[username] tried to view the administration panel, but it was stopped because his usergroup doesn't have access there." ) ;
    die( 'You\'re to small, baby!<BR>Hacking attempt logged.' ) ;
}
#print ( '<link rel="stylesheet" type="text/css" href="controlpanel.css" /><body>') ; 
$action = isset( $_POST['action'] ) ? $_POST['action'] : ( isset($_GET['action']) ? $_GET['action'] :
    '' ) ;
if($action == 'activate') {
	$file = $_GET['file'];
	activate_plugin($file);
	header("location: plugins.php?activate");
}
if($action == 'deactivate') {
	$file = $_GET['file'];
	deactivate_plugin($file);
	header("location: plugins.php?deactivate");
}
?>