<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
//delete parked user accounts
$secs = 120 * 86400 ; // change the time to fit your needs
$dt = sqlesc( get_date_time(gmtime() - $secs) ) ;
$maxclass = UC_POWER_USER ;
mysql_query( "DELETE FROM users WHERE parked='yes' AND status='confirmed' AND class <= $maxclass AND last_access < $dt" ) ;
?>