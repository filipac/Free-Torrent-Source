<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
//delete old invite codes
global $invite_timeout;
$secs = $invite_timeout * 86400 ; // when ?
$dt = sqlesc( get_date_time(gmtime() - $secs) ) ; // calculate date.
mysql_query( "DELETE FROM invites WHERE time_invited < $dt" ) ; // do job.

?>