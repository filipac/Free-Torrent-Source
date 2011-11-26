<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
//delete old regimage codes
$secs = 1 * 86400 ; //delete daily
$dt = sqlesc( get_date_time(gmtime() - $secs) ) ; // calculate date.
mysql_query( "DELETE FROM regimages WHERE added < $dt" ) ; // do job.

?>