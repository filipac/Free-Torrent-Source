<?php
#======================================
#User agent ban by beeman
#======================================

$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;
ADMIN::check();
stdhead( str3 ) ;
begin_main_frame() ;
unset( $add, $delete ) ;
$add = false ;
$delete = false ;
/*------------------
* |Submits for user agent ban hack
* -------------------*/
if ( $_POST['submit'] == 'Add Ban' )
{
    $query = "INSERT INTO banned_agent (agent) VALUES (" . sqlesc( $_POST["ban"] ) .
        ");" ;
    sql_query( $query ) or sqlerr( __file__, __line__ ) ;
    $add = true ;
}
if ( $_POST['action'] == 'Delete Ban' )
{
    $aquery = "DELETE FROM banned_agent WHERE agent = " . sqlesc( $_POST['dban'] ) .
        " LIMIT 1" ;
    sql_query( $aquery ) or sqlerr( __file__, __line__ ) ;
    $delete = true ;
}

begin_frame( str4 ) ;
/*------------------
* |HTML form for user agent ban hack
* ------------------*/
?>
                <div align="center">
                <table width='100%' cellspacing='3' cellpadding='3'>
               <?php
if ( $add )
    print ( str5 . " <b>(" . htmlspecialchars($_POST["ban"]) . ")" ) ;
elseif ( $delete )
    print ( str6 . " <b>(" . htmlspecialchars($_POST["dban"]) . ")" ) ;
?>
                <tr>
                <td bgcolor='#eeeeee' colspan="2"><b><font face="Verdana" size="1">
                <?= str7 ?><br /></font><font size="1" face="Times New Roman">&#9492;
                </font></b><font size="1" face="Verdana"><?= str8 ?><font></td>
                </tr>
                <form id="add ban" name="add ban" method="POST" action="agentban.php">
                <tr>
                <td bgcolor='#eeeeee'><font face="Verdana" size="1"><?= str9 ?>:  <input type="text" name="ban" id="banned" size="50" maxlength="255" value=""></font></td>
                <td bgcolor='#eeeeee' align='left'>
                <font size="1" face="Verdana"><input type="submit" name="submit" value="<?= str11 ?>"></font></td>
                </tr>
                </form>

                <form id="Add known Ban" name="Add known" method="POST" action="agentban.php">
                <tr>
                <td bgcolor='#eeeeee'><font face="Verdana" size="1"><?= str10 ?>: <select name="ban">
                <?php
/*-------------
* |Get the known agents to ban
* -------------*/
$se = "SELECT client, agentString FROM clients ORDER BY client" ;
$resa = sql_query( $se ) ;
while ( $asrow = mysql_fetch_array($resa) )
{
    echo "<option value=" . $asrow['agentString'] . ">" . $asrow['client'] .
        "</option>\n" ;
}
echo '</select></font></td><td bgcolor="#eeeeee" align="left">
                <font size="1" face="Verdana"><input type="submit" name="submit" value="' .
    str11 . '"></font></td>
                </tr></form><br>' ;
?>

                <form id="Deleate Ban" name="Deleate Ban" method="POST" action="agentban.php">
                <tr>
                <td bgcolor='#eeeeee'><font face="Verdana" size="1"><?= str13 ?>: <select name="dban">
                <?php
/*-------------
* |Get the agents currently banned
* -------------*/
$select = "SELECT id, agent FROM banned_agent ORDER BY agent" ;
$sres = sql_query( $select ) ;
while ( $srow = mysql_fetch_array($sres) )
{
    echo "<option>" . $srow['agent'] . "</option>\n" ;
}
echo '</select></font></td><td bgcolor="#eeeeee" align="left">
                <font size="1" face="Verdana"><input type="submit" name="action" value="' .
    str12 . '"></font></td>
                </tr></form><br>' ;
#=======================================
#End user agent ban hack
#=======================================
end_frame() ;

end_main_frame() ;
stdfoot() ;
?>
