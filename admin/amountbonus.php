<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;
lang::load( "amountbonus" ) ;
ADMIN::check();
if ( $HTTP_SERVER_VARS["REQUEST_METHOD"] == "POST" )
{
    if ( $_POST['doit'] == 'yes' )
    {
        sql_query( "UPDATE users SET seedbonus = seedbonus + 25.0 WHERE status='confirmed'" ) ;
        redirect('admin/amountbonus.php',str4,str3);
        die ;
    }

    if ( $HTTP_POST_VARS["username"] == "" || $HTTP_POST_VARS["seedbonus"] == "" ||
        $HTTP_POST_VARS["seedbonus"] == "" )
        stderr( str1, str5 ) ;
    $username = sqlesc( $HTTP_POST_VARS["username"] ) ;
    $seedbonus = sqlesc( $HTTP_POST_VARS["seedbonus"] ) ;

    sql_query( "UPDATE users SET seedbonus=seedbonus + $seedbonus WHERE username=$username" ) or
        sqlerr( __file__, __line__ ) ;
    $res = sql_query( "SELECT id FROM users WHERE username=$username" ) ;
    $arr = mysql_fetch_row( $res ) ;
    if ( ! $arr )
        stderr( str1, str6 ) ;
    header( "Location: $BASEURL/userdetails.php?id=" . htmlspecialchars($arr[0]) ) ;
    die ;
}
stdhead( str7 ) ;
?>
<h1><?= str7 ?></h1>

<?php begin_frame( str8, false, 10, false ) ;
echo "<form method=\"post\" action=\"amountbonus.php\">" ;
begin_table( true ) ;
?>
<tr><td class="rowhead"><?= str9 ?></td><td class="row1"><input type="text" name="username" size="40"/></td></tr>
<tr><td class="rowhead"><?= str10 ?></td><td class="row1"><input type="text" name="seedbonus" size="5"/></td></tr>
<tr><td colspan="2" class="row1" align="center"><input type="submit" value="<?= str11 ?>" class="btn"/></td></tr>
<?php end_table() ; ?>
</form>
<?php end_frame() ; ?>
<?php begin_frame( str12, false, 10, false ) ; ?>
<form action="amountbonus.php" method="post">
<?php begin_table( true ) ; ?>
<tr><td class="row1" align="center">
<?= str13 ?><br /><br />
<input type = "hidden" name = "doit" value = "yes" />
<input type="submit" value="<?= str14 ?>" />
</td></tr>
<?php end_table() ; ?>
</form>
<?php end_frame() ;

stdfoot() ; ?>