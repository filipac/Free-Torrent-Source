<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
    // delete report items older than a week
    $secs = 7 * 86400 ;
    $dt = sqlesc( get_date_time(gmtime() - $secs) ) ;
    mysql_query( "DELETE FROM reports WHERE dealtwith=1 AND added < $dt" ) ;
    ?>