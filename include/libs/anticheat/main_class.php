<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */
 global $mysql_host, $mysql_user, $mysql_pass, $mysql_db ; #
$_db = new SQL( $mysql_host, $mysql_user, $mysql_pass, $mysql_db ) ; # 
// Change this to change the interval between good and bad...
    $weight = 5 ;

    // Delete date that hasn't changed since the last update...
    mysql_query( "DELETE FROM anti_cheat WHERE uploaded = 0 AND downloaded = 0" ) ;
    // Now half the cheat value of everyone who's still got an active torrent
    mysql_query( "UPDATE users SET cheat = cheat / 2 WHERE id = (SELECT DISTINCT user_id FROM anti_cheat)" ) ;
    // Now get the list of each active torrent, and find the average values for it
    $res = mysql_query( "SELECT torrent_id, AVG(uploaded) AS average, ( (SUM(uploaded) - SUM(downloaded)) / COUNT(*) ) AS cutoff FROM anti_cheat GROUP BY torrent_id" ) ;
    while ( $row = mysql_fetch_assoc($res) )
    {
        // Update the cheat value for everyone *if* $cutoff > 0
        $torrent_id = $row['torrent_id'] ;
        $average = $row['average'] ;
        $cutoff = $row['cutoff'] ;
        mysql_query( "UPDATE users, anti_cheat SET users.cheat = users.cheat + ( ( ( anti_cheat.uploaded / $average ) - 1 ) * $weight ), anti_cheat.uploaded = 0, anti_cheat.downloaded = 0 WHERE users.id = anti_cheat.user_id AND anti_cheat.torrent_id = $torrent_id AND anti_cheat.uploaded > $cutoff AND (anti_cheat.uploaded / $average) > 0" ) ;
    }
    ?>