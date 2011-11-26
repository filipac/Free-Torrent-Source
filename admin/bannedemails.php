<?php
//Ban Email Address, Disable registration v.01
define( "VERSION", "BAN EMAIL's v.01" ) ;
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;
lang::load( 'bannedemails' ) ;
ADMIN::check();

$action = isset( $_POST['action'] ) ? htmlspecialchars( $_POST['action'] ) : ( isset
    ($_GET['action']) ? htmlspecialchars($_GET['action']) : 'showlist' ) ;

if ( $action == 'showlist' )
{
    stdhead( VERSION . str3 ) ;
    print ( "<table border=1 cellspacing=0 cellpadding=5 width=100%>\n" ) ;
    $sql = mysql_query( "SELECT * FROM bannedemails" ) or sqlerr( __file__, __line__ ) ;
    $list = mysql_fetch_array( $sql ) ;
?>
<form method=post action=bannedemails.php>
<input type=hidden name=action value=savelist>
<tr><td><?= str4 ?></td>
<td><textarea name="value" rows="5" cols="40"><?= $list[value] ?></textarea>
<input type=submit value="<?= str5 ?>"></form></td>
</tr></table>
<?php
    stdfoot() ;
} elseif ( $action == 'savelist' )
{
    stdhead( VERSION . str6 ) ;
    $value = trim( htmlspecialchars($_POST[value]) ) ;
    mysql_query( "UPDATE bannedemails SET value = " . sqlesc($value) ) or sqlerr( __file__,
        __line__ ) ;
    print ( str7 ) ;
    stdfoot() ;
}
?>