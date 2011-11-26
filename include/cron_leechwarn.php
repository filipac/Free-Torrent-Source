<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
//LEECHWARN USERS WITH LOW RATIO
    $minratio = 0.4 ; // ratio < 0.4
    $downloaded = 4 * 1024 * 1024 * 1024 ; // + 4 GB
    $length = 2 * 7 ; // warn users until 2 weeks

    $res = mysql_query( "SELECT id FROM users WHERE class = 0 AND leechwarn = 'no' AND uploaded / downloaded < $minratio AND downloaded >= $downloaded" ) or
        sqlerr( __file__, __line__ ) ;

    if ( mysql_num_rows($res) > 0 )
    {
        $dt = sqlesc( get_date_time() ) ;
        $msg = sqlesc( "You have been warned because of having low ratio. You need to get a ratio 0.6 before next 2 weeks or your account will be banned." ) ;

        $until = sqlesc( get_date_time(gmtime() + ($length * 86400)) ) ;

        while ( $arr = mysql_fetch_assoc($res) )
        {
            writecomment( $arr[id], "LeechWarned by System - Low Ratio." ) ;

            mysql_query( "UPDATE users SET leechwarn = 'yes', leechwarnuntil = $until WHERE id=$arr[id]" ) or
                sqlerr( __file__, __line__ ) ;
            mysql_query( "INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $arr[id], $dt, $msg, 0)" ) or
                sqlerr( __file__, __line__ ) ;
        }
    }
    //END//

    //REMOVE LEECHWARNING
    $minratio = 0.6 ; // ratio > 0.6

    $res = mysql_query( "SELECT id FROM users WHERE leechwarn = 'yes' AND uploaded / downloaded >= $minratio" ) or
        sqlerr( __file__, __line__ ) ;

    if ( mysql_num_rows($res) > 0 )
    {
        $dt = sqlesc( get_date_time() ) ;
        $msg = sqlesc( "Your warning of low ratio ave been removed. We highly recommend you to keep a your ratio up to not be warned again.\n" ) ;

        while ( $arr = mysql_fetch_assoc($res) )
        {
            writecomment( $arr[id], "LeechWarning removed by System." ) ;

            mysql_query( "UPDATE users SET leechwarn = 'no', leechwarnuntil = '0000-00-00 00:00:00' WHERE id = $arr[id]" ) or
                sqlerr( __file__, __line__ ) ;
            mysql_query( "INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $arr[id], $dt, $msg, 0)" ) or
                sqlerr( __file__, __line__ ) ;
        }
    }
    //END//

    //BAN USERS WITH LEECHWARNING EXPIRED
    $dt = sqlesc( get_date_time() ) ; // take date time
    $res = mysql_query( "SELECT id FROM users WHERE enabled = 'yes' AND leechwarn = 'yes' AND leechwarnuntil < $dt" ) or
        sqlerr( __file__, __line__ ) ;

    if ( mysql_num_rows($res) > 0 )
    {
        while ( $arr = mysql_fetch_assoc($res) )
        {
            writecomment( $arr[id], "Banned by System because of LeechWarning expired." ) ;

            mysql_query( "UPDATE users SET enabled = 'no', leechwarnuntil = '0000-00-00 00:00:00' WHERE id = $arr[id]" ) or
                sqlerr( __file__, __line__ ) ;
        }
    }
    //END//
    ?>