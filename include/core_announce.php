<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
if ( ! defined('IN_TRACKER') )
    die( 'Hacking attempt!' ) ;
define( "UC_USER", 0 ) ;
define( "UC_POWER_USER", 1 ) ;
define( "UC_VIP", 2 ) ;
define( "UC_UPLOADER", 3 ) ;
define( "UC_MODERATOR", 4 ) ;
define( "UC_ADMINISTRATOR", 5 ) ;
define( "UC_SYSOP", 6 ) ;
define( "UC_STAFFLEADER", 7 ) ;
set_magic_quotes_runtime( 0 ) ;
ignore_user_abort( 1 ) ;
@set_time_limit( 0 ) ;
#include_once ( $rootpath . 'include/factory.php' ) ;
include_once ( $rootpath . 'include/functions_announce.php' ) ;
if ( ! isset($HTTP_POST_VARS) && isset($_POST) )
{
    $HTTP_POST_VARS = $_POST ;
    $HTTP_GET_VARS = $_GET ;
    $HTTP_SERVER_VARS = $_SERVER ;
    $HTTP_COOKIE_VARS = $_COOKIE ;
    $HTTP_ENV_VARS = $_ENV ;
    $HTTP_POST_FILES = $_FILES ;
}
if ( get_magic_quotes_gpc() )
{
    if ( ! empty($_GET) )
    {
        $_GET = strip_magic_quotes( $_GET ) ;
    }
    if ( ! empty($_POST) )
    {
        $_POST = strip_magic_quotes( $_POST ) ;
    }
    if ( ! empty($_COOKIE) )
    {
        $_COOKIE = strip_magic_quotes( $_COOKIE ) ;
    }
}
if ( ! get_magic_quotes_gpc() )
{
    if ( is_array($HTTP_GET_VARS) )
    {
        while ( list($k, $v) = each($HTTP_GET_VARS) )
        {
            if ( is_array($HTTP_GET_VARS[$k]) )
            {
                while ( list($k2, $v2) = each($HTTP_GET_VARS[$k]) )
                {
                    $HTTP_GET_VARS[$k][$k2] = addslashes( $v2 ) ;
                }
                @reset( $HTTP_GET_VARS[$k] ) ;
            }
            else
            {
                $HTTP_GET_VARS[$k] = addslashes( $v ) ;
            }
        }
        @reset( $HTTP_GET_VARS ) ;
    }

    if ( is_array($HTTP_POST_VARS) )
    {
        while ( list($k, $v) = each($HTTP_POST_VARS) )
        {
            if ( is_array($HTTP_POST_VARS[$k]) )
            {
                while ( list($k2, $v2) = each($HTTP_POST_VARS[$k]) )
                {
                    $HTTP_POST_VARS[$k][$k2] = addslashes( $v2 ) ;
                }
                @reset( $HTTP_POST_VARS[$k] ) ;
            }
            else
            {
                $HTTP_POST_VARS[$k] = addslashes( $v ) ;
            }
        }
        @reset( $HTTP_POST_VARS ) ;
    }

    if ( is_array($HTTP_COOKIE_VARS) )
    {
        while ( list($k, $v) = each($HTTP_COOKIE_VARS) )
        {
            if ( is_array($HTTP_COOKIE_VARS[$k]) )
            {
                while ( list($k2, $v2) = each($HTTP_COOKIE_VARS[$k]) )
                {
                    $HTTP_COOKIE_VARS[$k][$k2] = addslashes( $v2 ) ;
                }
                @reset( $HTTP_COOKIE_VARS[$k] ) ;
            }
            else
            {
                $HTTP_COOKIE_VARS[$k] = addslashes( $v ) ;
            }
        }
        @reset( $HTTP_COOKIE_VARS ) ;
    }
}
?>