<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;
lang::load( 'bans' ) ;

ADMIN::check();

$remove = ( int )$_GET['remove'] ;
if ( is_valid_id($remove) )
{
    sql_query( "DELETE FROM bans WHERE id=" . mysql_real_escape_string($remove) ) or
        sqlerr() ;
    write_log( sprintf(str3, htmlspecialchars($remove), $CURUSER[id], $CURUSER[username]) ) ;
}

if ( $_SERVER["REQUEST_METHOD"] == "POST" && get_user_class() >=
    UC_ADMINISTRATOR )
{
    $first = trim( $_POST["first"] ) ;
    $last = trim( $_POST["last"] ) ;
    $comment = trim( $_POST["comment"] ) ;
    if ( ! $first || ! $last || ! $comment )
        stderr( str4, str5 ) ;
    $first = ip2long( $first ) ;
    $last = ip2long( $last ) ;
    if ( $first == -1 || $last == -1 )
        stderr( str4, str6 ) ;
    $comment = sqlesc( $comment ) ;
    $added = sqlesc( get_date_time() ) ;
    sql_query( "INSERT INTO bans (added, addedby, first, last, comment) VALUES($added, " .
        mysql_real_escape_string($CURUSER[id]) . ", $first, $last, $comment)" ) or
        sqlerr( __file__, __line__ ) ;
    header( "Location: $_SERVER[REQUEST_URI]" ) ;
    die ;
}

ob_start( "ob_gzhandler" ) ;

$res = sql_query( "SELECT * FROM bans ORDER BY added DESC" ) or sqlerr() ;

stdhead( str7 ) ;

print ( "<h1>" . str8 . "</h1>\n" ) ;

if ( mysql_num_rows($res) == 0 )
    print ( "<p align=center><b>" . str9 . "</b></p>\n" ) ;
else
{
    print ( "<table border=1 cellspacing=0 cellpadding=5>\n" ) ;
    print ( "<tr><td class=colhead>" . str10 . "</td><td class=colhead align=left>" .
        str11 . "</td><td class=colhead align=left>" . str12 . "</td>" .
        "<td class=colhead align=left>" . str13 . "</td><td class=colhead align=left>" .
        str14 . "</td><td class=colhead>" . str15 . "</td></tr>\n" ) ;

    while ( $arr = mysql_fetch_assoc($res) )
    {
        $r2 = sql_query( "SELECT username FROM users WHERE id=$arr[addedby]" ) or sqlerr() ;
        $a2 = mysql_fetch_assoc( $r2 ) ;
        $arr["first"] = long2ip( $arr["first"] ) ;
        $arr["last"] = long2ip( $arr["last"] ) ;
        print ( "<tr><td>$arr[added]</td><td align=left>$arr[first]</td><td align=left>$arr[last]</td><td align=left><a href=userdetails.php?id=$arr[addedby]>$a2[username]" .
            "</a></td><td align=left>$arr[comment]</td><td><a href=bans.php?remove=$arr[id]>" .
            str15 . "</a></td></tr>\n" ) ;
    }
    print ( "</table>\n" ) ;
}

if ( get_user_class() >= UC_ADMINISTRATOR )
{
    print ( "<h2>" . str16 . "</h2>\n" ) ;
    print ( "<table border=1 cellspacing=0 cellpadding=5>\n" ) ;
    print ( "<form method=post action=bans.php>\n" ) ;
    print ( "<tr><td class=rowhead>" . str17 .
        "</td><td><input type=text name=first size=40></td>\n" ) ;
    print ( "<tr><td class=rowhead>" . str18 .
        "</td><td><input type=text name=last size=40></td>\n" ) ;
    print ( "<tr><td class=rowhead>" . str19 .
        "</td><td><input type=text name=comment size=40></td>\n" ) ;
    print ( "<tr><td colspan=2><input type=submit value='" . str20 .
        "' class=btn></td></tr>\n" ) ;
    print ( "</form>\n</table>\n" ) ;
}
stdfoot() ;

?>