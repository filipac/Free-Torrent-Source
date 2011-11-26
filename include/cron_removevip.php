<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
    //=== remove VIP status if time up===//
    //=== change class to whatever is under your vip class number
    $res = mysql_query( "SELECT id, modcomment FROM users WHERE vip_added='yes' AND vip_until < NOW()" ) or
        sqlerr( __file__, __line__ ) ;
    if ( mysql_num_rows($res) > 0 )
    {
        $dt = sqlesc( get_date_time() ) ;
        $subject = sqlesc( "VIP status removed by system." ) ; //=== comment out this line if you DO NOT have subject in your PM system and change SITE NAME HERE to your site name duh :P
        $msg = sqlesc( "Your VIP status has timed out and has been auto-removed by the system. Become a VIP again by donating to $SITENAME, or exchanging some Karma Bonus Points. Cheers!\n" ) ;
        while ( $arr = mysql_fetch_assoc($res) )
        {
            ///---AUTOSYSTEM MODCOMMENT---//
            $modcomment = htmlspecialchars( $arr["modcomment"] ) ;
            $modcomment = gmdate( "Y-m-d" ) . " - VIP status removed by -AutoSystem.\n" . $modcomment ;
            $modcom = sqlesc( $modcomment ) ;
            ///---end
            mysql_query( "UPDATE users SET class = '1', vip_added = 'no', vip_until = '0000-00-00 00:00:00', modcomment = $modcom WHERE id = $arr[id]" ) or
                sqlerr( __file__, __line__ ) ;
            mysql_query( "INSERT INTO messages (sender, receiver, added, msg, subject, poster) VALUES(0, $arr[id], $dt, $msg, $subject, 0)" ) or
                sqlerr( __file__, __line__ ) ;
            //mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $arr[id], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__); //=== use this line (and comment out the above line) if you DO NOT have subject in your PM system

        }
    }
    //===end===//
    ?>