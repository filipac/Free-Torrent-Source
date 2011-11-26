<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
// promote power users
    $limit = 25 * 1024 * 1024 * 1024 ;
    $minratio = 1.05 ;
    $maxdt = sqlesc( get_date_time(gmtime() - 86400 * 28) ) ;
    $res = mysql_query( "SELECT id FROM users WHERE class = 0 AND uploaded >= $limit AND uploaded / downloaded >= $minratio AND added < $maxdt" ) or
        sqlerr( __file__, __line__ ) ;
    if ( mysql_num_rows($res) > 0 )
    {
        $dt = sqlesc( get_date_time() ) ;
        $msg = sqlesc( "Congratulations, you have been auto-promoted to [b]Power User[/b]. :)\nYou can now download dox over 1 meg and view torrent NFOs.\n" ) ;
        while ( $arr = mysql_fetch_assoc($res) )
        {
            mysql_query( "UPDATE users SET class = 1 WHERE id = $arr[id]" ) or sqlerr( __file__,
                __line__ ) ;
            mysql_query( "INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $arr[id], $dt, $msg, 0)" ) or
                sqlerr( __file__, __line__ ) ;
        }
    }

    // demote power users
    $minratio = 0.95 ;
    $res = mysql_query( "SELECT id FROM users WHERE class = 1 AND uploaded / downloaded < $minratio" ) or
        sqlerr( __file__, __line__ ) ;
    if ( mysql_num_rows($res) > 0 )
    {
        $dt = sqlesc( get_date_time() ) ;
        $msg = sqlesc( "You have been auto-demoted from [b]Power User[/b] to [b]User[/b] because your share ratio has dropped below $minratio.\n" ) ;
        while ( $arr = mysql_fetch_assoc($res) )
        {
            mysql_query( "UPDATE users SET class = 0 WHERE id = $arr[id]" ) or sqlerr( __file__,
                __line__ ) ;
            mysql_query( "INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $arr[id], $dt, $msg, 0)" ) or
                sqlerr( __file__, __line__ ) ;
        }
    }
    ?>