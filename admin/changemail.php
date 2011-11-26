<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;
lang::load( "changemail" ) ;
ADMIN::check();
if ( $HTTP_SERVER_VARS["REQUEST_METHOD"] == "POST" )
{
    if ( $HTTP_POST_VARS["username"] == "" || $HTTP_POST_VARS["email"] == "" )
        stderr( str1, str3 ) ;
    $username = sqlesc( $HTTP_POST_VARS["username"] ) ;
    $email = sqlesc( $HTTP_POST_VARS["email"] ) ;


    sql_query( "UPDATE users SET email=$email WHERE username=$username" ) or sqlerr( __file__,
        __line__ ) ;
    $res = sql_query( "SELECT id FROM users WHERE username=$username" ) ;
    $arr = mysql_fetch_row( $res ) ;
    if ( ! $arr )
        stderr( str1, str4 ) ;
    header( "Location: $BASEURL/userdetails.php?id=$arr[0]" ) ;
    die ;
}
stdhead( str5 ) ;
?>
<h1><?= str5 ?></h1>
<form method=post action=changemail.php>
<table border=1 cellspacing=0 cellpadding=5>
<tr><td class=rowhead><?= str6 ?></td><td><input type=text name=username size=40></td></tr>
<tr><td class=rowhead><?= str7 ?></td><td><input type=email name=email size=80></td></tr>
<tr><td colspan=2 align=center><input type=submit value="<?= str8 ?>" class=btn></td></tr>
</table>
</form>
<?php stdfoot() ; ?>