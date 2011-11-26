<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
interface CrOnEx
{
    public function MainCron() ;
    public function stopint() ;
    public function torrentscron() ;
    public function deadtime() ;
    public function announce() ;
}
class cron implements CrOnEx
{
    public function stopint()
    {
        return ignore_user_abort( 1 ) ;
    }
    public function MainCron()
    {
        global $torrent_dir, $signup_timeout, $max_dead_torrent_time, $autoclean_interval,
            $SITENAME, $bonus, $invite_timeout ;
        do
        {
            $res = mysql_query( "SELECT id FROM torrents" ) ;
            $ar = array() ;
            while ( $row = mysql_fetch_array($res) )
            {
                $id = $row[0] ;
                $ar[$id] = 1 ;
            }

            if ( ! count($ar) )
                break ;

            $dp = @opendir( $torrent_dir ) ;
            if ( ! $dp )
                break ;

            $ar2 = array() ;
            while ( ($file = readdir($dp)) !== false )
            {
                if ( ! preg_match('/^(\d+)\.torrent$/', $file, $m) )
                    continue ;
                $id = $m[1] ;
                $ar2[$id] = 1 ;
                if ( isset($ar[$id]) && $ar[$id] )
                    continue ;
                $ff = $torrent_dir . "/$file" ;
                unlink( $ff ) ;
            }
            closedir( $dp ) ;

            if ( ! count($ar2) )
                break ;

            $delids = array() ;
            foreach ( array_keys($ar) as $k )
            {
                if ( isset($ar2[$k]) && $ar2[$k] )
                    continue ;
                $delids[] = $k ;
                unset( $ar[$k] ) ;
            }
            if ( count($delids) )
                mysql_query( "DELETE FROM torrents WHERE id IN (" . join(",", $delids) . ")" ) ;

            $res = mysql_query( "SELECT torrent FROM peers GROUP BY torrent" ) ;
            $delids = array() ;
            while ( $row = mysql_fetch_array($res) )
            {
                $id = $row[0] ;
                if ( isset($ar[$id]) && $ar[$id] )
                    continue ;
                $delids[] = $id ;
            }
            if ( count($delids) )
                mysql_query( "DELETE FROM peers WHERE torrent IN (" . join(",", $delids) . ")" ) ;

            $res = mysql_query( "SELECT torrent FROM files GROUP BY torrent" ) ;
            $delids = array() ;
            while ( $row = mysql_fetch_array($res) )
            {
                $id = $row[0] ;
                if ( $ar[$id] )
                    continue ;
                $delids[] = $id ;
            }
            if ( count($delids) )
                mysql_query( "DELETE FROM files WHERE torrent IN (" . join(",", $delids) . ")" ) ;
        } while ( 0 ) ;
    }
    public function deadtime()
    {
        global $torrent_dir, $signup_timeout, $max_dead_torrent_time, $autoclean_interval,
            $SITENAME, $bonus, $invite_timeout ;
        $deadtime = deadtime() ;
        mysql_query( "DELETE FROM peers WHERE last_action < FROM_UNIXTIME($deadtime)" ) ;

        $deadtime = deadtime() ;
        mysql_query( "UPDATE snatched SET seeder='no' WHERE seeder='yes' AND last_action < FROM_UNIXTIME($deadtime)" ) ;

        $deadtime -= $max_dead_torrent_time ;
        mysql_query( "UPDATE torrents SET visible='no' WHERE visible='yes' AND last_action < FROM_UNIXTIME($deadtime)" ) ;

        $deadtime = time() - $signup_timeout ;
        mysql_query( "DELETE FROM users WHERE status = 'pending' AND added < FROM_UNIXTIME($deadtime) AND last_login < FROM_UNIXTIME($deadtime) AND last_access < FROM_UNIXTIME($deadtime)" ) ;
    }
    public function torrentscron()
    {
        $torrents = array() ;
        $res = mysql_query( "SELECT torrent, seeder, COUNT(*) AS c FROM peers GROUP BY torrent, seeder" ) ;
        while ( $row = mysql_fetch_assoc($res) )
        {
            if ( $row["seeder"] == "yes" )
                $key = "seeders" ;
            else
                $key = "leechers" ;
            $torrents[$row["torrent"]][$key] = $row["c"] ;
        }

        $res = mysql_query( "SELECT torrent, COUNT(*) AS c FROM comments GROUP BY torrent" ) ;
        while ( $row = mysql_fetch_assoc($res) )
        {
            $torrents[$row["torrent"]]["comments"] = $row["c"] ;
        }

        $fields = explode( ":", "comments:leechers:seeders" ) ;
        $res = mysql_query( "SELECT id, seeders, leechers, comments FROM torrents" ) ;
        while ( $row = mysql_fetch_assoc($res) )
        {
            $id = $row["id"] ;
            $torr = $torrents[$id] ;
            foreach ( $fields as $field )
            {
                if ( ! isset($torr[$field]) )
                    $torr[$field] = 0 ;
            }
            $update = array() ;
            foreach ( $fields as $field )
            {
                if ( $torr[$field] != $row[$field] )
                    $update[] = "$field = " . $torr[$field] ;
            }
            if ( count($update) )
                mysql_query( "UPDATE torrents SET " . implode(",", $update) . " WHERE id = $id" ) ;
        }
            // delete old torrents
    $days = 128 ;
    $dt = sqlesc( get_date_time(gmtime() - ($days * 86400)) ) ;
    $res = mysql_query( "SELECT id, name FROM torrents WHERE added < $dt" ) ;
    while ( $arr = mysql_fetch_assoc($res) )
    {
        @unlink( "$torrent_dir/$arr[id].torrent" ) ;
        mysql_query( "DELETE FROM torrents WHERE id=$arr[id]" ) ;
        mysql_query( "DELETE FROM snatched WHERE torrentid =$arr[id]" ) ;
        mysql_query( "DELETE FROM peers WHERE torrent=$arr[id]" ) ;
        mysql_query( "DELETE FROM comments WHERE torrent=$arr[id]" ) ;
        mysql_query( "DELETE FROM files WHERE torrent=$arr[id]" ) ;
        write_log( "Torrent $arr[id] ($arr[name]) was deleted by system (older than $days days)" ) ;
    }
    }
    public function announce() {
		    global $autoclean_interval ;
    $nexttime = time() + $autoclean_interval ;
    $nextcl = date( "d.m.Y H:i:s", $nexttime ) ;
    $cleanupdone = date( "d.m.Y H:i:s" ) ;
    $message = "Clean-up executed at $cleanupdone. Next clean-up at: $nextcl" ;
    if(duty('cleanups'))
        add_shout( $message ) ;
    write_log("Cleanup done. Next cleanup date: $nextcl");    
	}
	
}
?>