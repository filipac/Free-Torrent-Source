<?php
$rootpath = '../' ;
include $rootpath . 'include/bittorrent.php' ;
loggedinorreturn() ;
if ( ! ur::isadmin() )
{
    write_log( "User $CURUSER[username] tried to view the administration panel, but it was stopped because his usergroup doesn't have access there." ) ;
    die( 'You\'re to small, baby!<BR>Hacking attempt logged.' ) ;
}
$page = isset( $_POST['page'] ) ? $_POST['page'] : ( isset($_GET['page']) ? $_GET['page'] : '' ) ;
call_user_func($page);
?>