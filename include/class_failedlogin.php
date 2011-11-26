<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
/**
 * FLogin
 *
 * @package Free Torrent Source
 * @author Filip
 * @copyright 2008
 * @version $Id$
 * @access public
 */
class FLogin
{
  /**
   * FLogin::failedloginscheck()
   *
   * @param string $type
   * @return
   */
    function failedloginscheck( $type = 'Login' )
    {
        global $maxloginattempts ;
        $total = 0 ;
        $ip = sqlesc( IP::getip() ) ;
        $Query = mysql_query( "SELECT SUM(attempts) FROM loginattempts WHERE ip=$ip" ) or
            sqlerr( __file__, __line__ ) ;
        list( $total ) = mysql_fetch_array( $Query ) ;
        if ( $total >= $maxloginattempts )
        {
            mysql_query( "UPDATE loginattempts SET banned = 'yes' WHERE ip=$ip" ) or sqlerr( __file__,
                __line__ ) ;
            stderr( "$type Locked! (the maximum number of failed $type attempts is reached during reauthentication)",
                "We come to believe you are trying to cheat our system, therefore we've banned your ip!" ) ;
        }
    }
  /**
   * FLogin::failedlogins()
   *
   * @param string $type
   * @param bool $recover
   * @param bool $head
   * @return
   */
    function failedlogins( $type = 'login', $recover = false, $head = true )
    {
        $ip = sqlesc( IP::getip() ) ;
        $added = sqlesc( get_date_time() ) ;
        $a = ( @mysql_fetch_row(@mysql_query("select count(*) from loginattempts where ip=$ip")) ) or
            sqlerr( __file__, __line__ ) ;
        if ( $a[0] == 0 )
            mysql_query( "INSERT INTO loginattempts (ip, added, attempts) VALUES ($ip, $added, 1)" ) or
                sqlerr( __file__, __line__ ) ;
        else
            mysql_query( "UPDATE loginattempts SET attempts = attempts + 1 where ip=$ip" ) or
                sqlerr( __file__, __line__ ) ;
        if ( $recover )
            mysql_query( "UPDATE loginattempts SET type = 'recover' WHERE ip = $ip" ) or
                sqlerr( __file__, __line__ ) ;
        if ( $type == 'silent' )
            return ;
        elseif ( $type == 'login' )
            stderr( "Login failed!",
                "<b>Error</b>: Username or password incorrect!<br><br>Don't remember your password? <b><a href=recover.php>Recover</a></b> your password!", false ) ;
        else
            stderr( "Recover Failed", $type, false, $head ) ;

    }

  /**
   * FLogin::remaining()
   *
   * @param string $type
   * @return
   */
    function remaining( $type = 'login' )
    {
        global $maxloginattempts ;
        $total = 0 ;
        $ip = sqlesc( IP::getip() ) ;
        $Query = mysql_query( "SELECT SUM(attempts) FROM loginattempts WHERE ip=$ip" ) or
            sqlerr( __file__, __line__ ) ;
        list( $total ) = mysql_fetch_array( $Query ) ;
        $remaining = $maxloginattempts - $total ;
        if ( $remaining <= 2 )
            $remaining = "<font color=red size=2>[" . $remaining . "]</font>" ;
        else
            $remaining = "<font color=green size=2>[" . $remaining . "]</font>" ;

        return $remaining ;
    }
}
?>