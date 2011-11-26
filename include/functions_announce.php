<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
# IMPORTANT: Do not edit below unless you know what you are doing!
if ( ! defined('IN_TRACKER') )
    die( 'Hacking attempt!' ) ;

include_once ( $rootpath . 'include/config.php' ) ;
/**
 * get_date_time()
 *
 * @param integer $timestamp
 * @return
 */
function get_date_time( $timestamp = 0 )
{
    if ( $timestamp )
        return date( "Y-m-d H:i:s", $timestamp ) ;
    else
        return date( "Y-m-d H:i:s" ) ;
}
/**
 * gmtime()
 *
 * @return
 */
function gmtime()
{
    return strtotime( get_date_time() ) ;
}
/**
 * emu_getallheaders()
 *
 * @return
 */
function emu_getallheaders()
{
    foreach ( $_SERVER as $name => $value )
        if ( substr($name, 0, 5) == 'HTTP_' )
            $headers[str_replace( ' ', '-', ucwords(strtolower(str_replace('_', ' ', substr
                ($name, 5)))) )] = $value ;
    return $headers ;
}
/**
 * strip_magic_quotes()
 *
 * @param mixed $arr
 * @return
 */
function strip_magic_quotes( $arr )
{
    foreach ( $arr as $k => $v )
    {
        if ( is_array($v) )
        {
            $arr[$k] = strip_magic_quotes( $v ) ;
        }
        else
        {
            $arr[$k] = stripslashes( $v ) ;
        }
    }

    return $arr ;
}
/**
 * validip()
 *
 * @param mixed $ip
 * @return
 */
function validip( $ip )
{
    if ( ! empty($ip) && $ip == long2ip(ip2long($ip)) )
    {
        $reserved_ips = array( array('0.0.0.0', '2.255.255.255'), array('10.0.0.0',
            '10.255.255.255'), array('127.0.0.0', '127.255.255.255'), array('169.254.0.0',
            '169.254.255.255'), array('172.16.0.0', '172.31.255.255'), array('192.0.2.0',
            '192.0.2.255'), array('192.168.0.0', '192.168.255.255'), array('255.255.255.0',
            '255.255.255.255') ) ;

        foreach ( $reserved_ips as $r )
        {
            $min = ip2long( $r[0] ) ;
            $max = ip2long( $r[1] ) ;
            if ( (ip2long($ip) >= $min) && (ip2long($ip) <= $max) )
                return false ;
        }
        return true ;
    }
    else
        return false ;
}
/**
 * getip()
 *
 * @return
 */
function getip()
{
    if ( isset($_SERVER) )
    {
        if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && validip($_SERVER['HTTP_X_FORWARDED_FOR']) )
        {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ;
        } elseif ( isset($_SERVER['HTTP_CLIENT_IP']) && validip($_SERVER['HTTP_CLIENT_IP']) )
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'] ;
        }
        else
        {
            $ip = $_SERVER['REMOTE_ADDR'] ;
        }
    }
    else
    {
        if ( getenv('HTTP_X_FORWARDED_FOR') && validip(getenv('HTTP_X_FORWARDED_FOR')) )
        {
            $ip = getenv( 'HTTP_X_FORWARDED_FOR' ) ;
        } elseif ( getenv('HTTP_CLIENT_IP') && validip(getenv('HTTP_CLIENT_IP')) )
        {
            $ip = getenv( 'HTTP_CLIENT_IP' ) ;
        }
        else
        {
            $ip = getenv( 'REMOTE_ADDR' ) ;
        }
    }

    return $ip ;
}
/**
 * dbconn()
 *
 * @return
 */
function dbconn()
{
    global $mysql_host, $mysql_user, $mysql_pass, $mysql_db ;
    if ( ! @mysql_pconnect($mysql_host, $mysql_user, $mysql_pass) )
    {
        die( 'dbconn: mysql_connect: ' . mysql_error() ) ;
    }
    mysql_select_db( $mysql_db ) or die( 'dbconn: mysql_select_db: ' + mysql_error() ) ;
    userlogin() ;
}
/**
 * userlogin()
 *
 * @return
 */
function userlogin()
{
    global $ip ;
    if ( ! isset($ip) )
        $ip = getip() ;
    $nip = ip2long( $ip ) ;

    $res = mysql_query( "SELECT * FROM bans WHERE $nip >= first AND $nip <= last" ) ;
    if ( mysql_num_rows($res) > 0 )
    {
        header( "HTTP/1.0 403 Forbidden" ) ;
        echo '<html><body><h1>403 Forbidden</h1>Unauthorized IP address.</body></html>' ;
        die ;
    }
}
/**
 * sqlesc()
 *
 * @param mixed $value
 * @return
 */
function sqlesc( $value )
{
    // Stripslashes
    if ( get_magic_quotes_gpc() )
    {
        $value = stripslashes( $value ) ;
    }
    // Quote if not a number or a numeric string
    if ( ! is_numeric($value) )
    {
        $value = "'" . mysql_real_escape_string( $value ) . "'" ;
    }
    return $value ;
}
/**
 * hash_pad()
 *
 * @param mixed $hash
 * @return
 */
function hash_pad( $hash )
{
    return str_pad( $hash, 20 ) ;
}
/**
 * hash_where()
 *
 * @param mixed $name
 * @param mixed $hash
 * @return
 */
function hash_where( $name, $hash )
{
    $shhash = preg_replace( '/ *$/s', "", $hash ) ;
    return "($name = " . sqlesc( $hash ) . " OR $name = " . sqlesc( $shhash ) . ")" ;
}
/**
 * ReadConfig()
 *
 * @param mixed $configname
 * @return
 */
function ReadConfig( $configname )
{
    if ( strstr($configname, ',') )
    {
        $configlist = explode( ',', $configname ) ;
        foreach ( $configlist as $key => $configname )
        {
            ReadConfig( trim($configname) ) ;
        }
    }
    else
    {
        $configname = basename( $configname ) ;
        $path = './config/' . $configname ;
        if ( ! file_exists($path) )
        {
            return;
        }
        $fp = fopen( $path, 'r' ) ;
        $content = '' ;
        while ( ! feof($fp) )
        {
            $content .= fread( $fp, 102400 ) ;
        }
        fclose( $fp ) ;
        if ( empty($content) )
        {
            return array() ;
        }
        $tmp = @unserialize( $content ) ;
        if ( empty($tmp) )
        {
            die ;
        }
        $GLOBALS[$configname] = $tmp ;
        return true ;
    }
}
?>