<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
// lock topics where last post was made more than x days ago
$secs = 67 * 86400 ;
$res = mysql_query( "SELECT topics.id FROM topics LEFT JOIN posts ON topics.lastpost = posts.id AND topics.sticky = 'no' WHERE " .
    gmtime() . " - UNIX_TIMESTAMP(posts.added) > $secs" ) or sqlerr( __file__,
    __line__ ) ;
while ( $arr = mysql_fetch_assoc($res) )
    mysql_query( "UPDATE topics SET locked='yes' WHERE id=$arr[id]" ) or sqlerr( __file__,
        __line__ ) ;
?>