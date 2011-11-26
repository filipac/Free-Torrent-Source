<?php
// mod_cheat for torrentbits based tracker
// Copy this file to the same dir as the rest of the tracker stuff...

$top = 100 ; // Only look at the top xxx most likely...
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;
lang::load( cheaters ) ;



ADMIN::check();

stdhead( str3 ) ;
begin_frame( str3 ) ;

$page = @$_GET['page'] ;
//$perpage = 100; // currently ignored

$class = @$_GET['c'] ;
if ( ! is_valid_user_class($class - 2) )
$class = '' ;

$ratio = @$_GET['r'] ;
if ( ! is_valid_id($ratio) && $ratio >= 1 && $ratio <= 7 )
$ratio = '' ;

echo '<center><form method="get" action="' . $_SERVER["PHP_SELF"] . '">' ;
begin_table() ;

echo '<tr><th colspan="4">'.str4.'</th></tr><tr><td colspan="4" class="left">' ;
echo str5;
echo '</td></tr>' ;
echo '<tr><th>'.str6.':</th>' ;
echo '<td><select name="c"><option value="1">'.str7.'</option>' ;
for ( $i = 2;; ++$i )
{
if ( $c = get_user_class_name($i - 2) )
echo '<option value="' . $i . '"' . ( $class == $i ? ' selected' : '' ) .
">&lt;= $c</option>\n" ;
else
break ;
}
echo '</select></td>' ;

echo '<th>'.str8.'</th>' ;
echo '<td><select name="r"><option value="1"' . ( $ratio == 1 ? ' selected' : '' ) .
'>'.str7.'</option>' ;
echo '<option value="2"' . ( $ratio == 2 ? ' selected' : '' ) .
'>&gt;= 1.000</option>' ;
echo '<option value="3"' . ( $ratio == 3 ? ' selected' : '' ) .
'>&gt;= 2.000</option>' ;
echo '<option value="4"' . ( $ratio == 4 ? ' selected' : '' ) .
'>&gt;= 3.000</option>' ;
echo '<option value="5"' . ( $ratio == 5 ? ' selected' : '' ) .
'>&gt;= 4.000</option>' ;
echo '<option value="6"' . ( $ratio == 6 ? ' selected' : '' ) .
'>&gt;= 5.000</option>' ;
echo '</select></td>' ;

echo '</tr><tr><td colspan="4"><input name="submit" type="submit"></td></tr>' ;
end_table() ;
echo '</form>' ;

$query = 'WHERE enabled = 1 AND downloaded > 0 AND uploaded > 0' ;
//' AND cheat >= '.$min
if ( $class > 2 )
$query .= ' AND class < ' . ( $class - 1 ) ;
if ( $ratio > 1 )
$query .= ' AND (uploaded / downloaded) > ' . ( $ratio - 1 ) ;

$res = sql_query( "SELECT COUNT(*),MIN(cheat),MAX(cheat) FROM users $query" ) or
sqlerr() ;
$arr = mysql_fetch_row( $res ) ;
$top = MIN( $top, $arr[0] ) ;
$min = $arr[1] ;
$max = $arr[2] ;

$pages = ceil( $top / 20 ) ;
if ( $page < 1 )
$page = 1 ;
elseif ( $page > $pages )
$page = $pages ;

list( $pagertop, $pagerbottom, $limit ) = pager( 20, $top, "cheaters.php?" ) ;

echo $pagertop ;
begin_table() ;
print ( "<tr><th class=\"left\">".str9."</th><th>".str10."</th><th>".str11."</th><th>".str12."</th><th>".str13."</th><th>".str14."</th><th>".str15."</th></tr>\n" ) ;

$res = sql_query( "SELECT * FROM users $query ORDER BY cheat DESC $limit" ) or
sqlerr() ;
while ( $arr = mysql_fetch_assoc($res) )
{
if ( $arr['added'] == "0000-00-00 00:00:00" )
$joindate = 'N/A' ;
else
$joindate = get_elapsed_time( sql_timestamp_to_unix_timestamp($arr['added']) ) .
' ago' ;
$age = date( 'U' ) - date( 'U', strtotime($arr['added']) ) ;
if ( $arr["downloaded"] > 0 )
{
$ratio = number_format( $arr["uploaded"] / $arr["downloaded"], 3 ) ;
$ratio = "<font color=" . get_ratio_color( $ratio ) . ">$ratio</font>" ;
}
else
{
if ( $arr["uploaded"] > 0 )
$ratio = "Inf." ;
else
$ratio = "---" ;
}
if ( $arr['added'] == '0000-00-00 00:00:00' )
$arr['added'] = '-' ;
echo '<tr><th class="left"><a href="'.$BASEURL.'/userdetails.php?id=' . $arr['id'] . '"><b>' .
$arr['username'] . '</b></a></th>' ;
echo '<td>' . $joindate . '</td>' ;
echo '<td class="right">' . mksize( $arr['uploaded'] ) . ' @ ' . mksize( $arr['uploaded'] /
$age ) . 'ps</td>' ;
echo '<td class="right">' . mksize( $arr['downloaded'] ) . ' @ ' . mksize( $arr['downloaded'] /
$age ) . 'ps</td>' ;
echo '<td>' . $ratio . '</td>' ;
echo '<td>' . $arr['cheat'] . '</td>' ;
echo '<td class="right">' . ceil( ($arr['cheat'] - $min) / max(1, ($max - $min)) *
100 ) . '%</td></tr>' . "\n" ;
}
end_table() ;
echo $pagerbottom ;
end_frame() ;

stdfoot() ;
?>
