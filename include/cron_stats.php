<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
    // Update stats
    $seeders = get_row_count( "peers", "WHERE seeder='yes'" ) ;
    $leechers = get_row_count( "peers", "WHERE seeder='no'" ) ;
    mysql_query( "UPDATE avps SET value_u=$seeders WHERE arg='seeders'" ) or sqlerr( __file__,
        __line__ ) ;
    mysql_query( "UPDATE avps SET value_u=$leechers WHERE arg='leechers'" ) or
        sqlerr( __file__, __line__ ) ;
        ?>