<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;

lang::load( "allagents" ) ;
ADMIN::check();
$res2 = sql_query( "SELECT agent,peer_id FROM peers  GROUP BY agent " ) or
    sqlerr() ;
stdhead( str3 ) ;
print ( "<table align=center border=3 cellspacing=0 cellpadding=5>\n" ) ;
print ( "<tr><td class=colhead>" . str4 . "</td><td class=colhead>" . str5 .
    "</td></tr>\n" ) ;
while ( $arr2 = mysql_fetch_assoc($res2) )
{
    print ( "</a></td><td align=left>$arr2[agent]</td><td align=left>$arr2[peer_id]</td></tr>\n" ) ;
}
print ( "</table>\n" ) ;
stdfoot() ;
?>