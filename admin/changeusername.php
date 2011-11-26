<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;
lang::load( "changeusername" ) ;
ADMIN::check();
if ( $HTTP_SERVER_VARS["REQUEST_METHOD"] == "POST" )
{
    if ( $HTTP_POST_VARS["username"] == "" || $HTTP_POST_VARS["id"] == "" || $HTTP_POST_VARS["id"] ==
        "" )
        stderr( str1, str3 ) ;
    $id = sqlesc( $HTTP_POST_VARS["id"] ) ;
    $username = sqlesc( $HTTP_POST_VARS["username"] ) ;

    sql_query( "UPDATE users SET username=$username WHERE id=$id" ) or sqlerr( __file__,
        __line__ ) ;
    $res = sql_query( "SELECT username FROM users WHERE id=$id" ) ;
    $arr = mysql_fetch_row( $res ) ;
    if ( ! $arr )
        stderr( str1, str4 ) ;
    header( "Location: $BASEURL/userdetails.php?id=$id" ) ;
    die ;
}
stdhead( str5 ) ;
?>
<h1><?= str5 ?></h1>
<form method=post action=changeusername.php>
<table border=1 cellspacing=0 cellpadding=5>
<tr><td class=rowhead><?= str6 ?></td><td><input type=text name=id size=6></td></tr>
<tr><td class=rowhead><?= str7 ?></td><td><input type=uploaded name=username size=25></td></tr>
<tr><td colspan=2 align=center><input type=submit value="<?= str8 ?>" class=btn></td></tr>
</table>
</form>
<?php stdfoot() ; ?>