<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
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
        $_GET = HANDLE::strip_magic_quotes( $_GET ) ;
    }
    if ( ! empty($_POST) )
    {
        $_POST = HANDLE::strip_magic_quotes( $_POST ) ;
    }
    if ( ! empty($_COOKIE) )
    {
        $_COOKIE = HANDLE::strip_magic_quotes( $_COOKIE ) ;
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
if (!function_exists("htmlspecialchars_uni")) {
    function htmlspecialchars_uni($message) {
        $message = preg_replace("#&(?!\#[0-9]+;)#si", "&amp;", $message); // Fix & but allow unicode
        $message = str_replace("<","&lt;",$message);
        $message = str_replace(">","&gt;",$message);
        $message = str_replace("\"","&quot;",$message);
        $message = str_replace("  ", "&nbsp;&nbsp;", $message);
        return $message;
    }
}
/**
 * my_error_handler()
 *
 * @param mixed $errno
 * @param mixed $errstr
 * @param mixed $errfile
 * @param mixed $errline
 * @return
 */
function my_error_handler( $errno, $errstr, $errfile, $errline )
{
    $errno = $errno & error_reporting() ;
    if ( $errno == 0 )
        return ;
    if ( ! defined('E_STRICT') )
        define( 'E_STRICT', 2048 ) ;
    if ( ! defined('E_RECOVERABLE_ERROR') )
        define( 'E_RECOVERABLE_ERROR', 4096 ) ;
    print "<pre>\n<b>" ;
    switch ( $errno )
    {
        case E_ERROR:
            print "Error" ;
            break ;
        case E_WARNING:
            print "Warning" ;
            break ;
        case E_PARSE:
            print "Parse Error" ;
            break ;
        case E_NOTICE:
            print "Notice" ;
            break ;
        case E_CORE_ERROR:
            print "Core Error" ;
            break ;
        case E_CORE_WARNING:
            print "Core Warning" ;
            break ;
        case E_COMPILE_ERROR:
            print "Compile Error" ;
            break ;
        case E_COMPILE_WARNING:
            print "Compile Warning" ;
            break ;
        case E_USER_ERROR:
            print "User Error" ;
            break ;
        case E_USER_WARNING:
            print "User Warning" ;
            break ;
        case E_USER_NOTICE:
            print "User Notice" ;
            break ;
        case E_STRICT:
            print "Strict Notice" ;
            break ;
        case E_RECOVERABLE_ERROR:
            print "Recoverable Error" ;
            break ;
        default:
            print "Unknown error ($errno)" ;
            break ;
    }
    print ":</b> <i>$errstr</i> in <b>$errfile</b> on line <b>$errline</b>\n" ;
    if ( function_exists('debug_backtrace') )
    {
        //print "backtrace:\n";
        $backtrace = debug_backtrace() ;
        array_shift( $backtrace ) ;
        foreach ( $backtrace as $i => $l )
        {
            print "[$i] in function <b>{$l['class']}{$l['type']}{$l['function']}</b>" ;
            if ( $l['file'] )
                print " in <b>{$l['file']}</b>" ;
            if ( $l['line'] )
                print " on line <b>{$l['line']}</b>" ;
            print "\n" ;
        }
    }
    print "\n</pre>" ;
    if ( isset($GLOBALS['error_fatal']) )
    {
        if ( $GLOBALS['error_fatal'] & $errno )
            die( 'fatal' ) ;
    }
}

/**
 * error_fatal()
 *
 * @param mixed $mask
 * @return
 */
function error_fatal( $mask = null )
{
    if ( ! is_null($mask) )
    {
        $GLOBALS['error_fatal'] = $mask ;
    } elseif ( ! isset($GLOBALS['die_on']) )
    {
        $GLOBALS['error_fatal'] = 0 ;
    }
    return $GLOBALS['error_fatal'] ;
}
set_error_handler( 'my_error_handler', E_ALL ) ;
do_action("init");
#error_fatal(E_ALL^E_NOTICE); // will die on any error except E_NOTICE
/*
* $myFilter = new InputFilter($tags, $attributes, 0, 0); // Invoke it
* if (!empty($_GET))        $_GET        = $myFilter->process($_GET);
* if (!empty($_POST))       $_POST       = $myFilter->process($_POST);
* if (!empty($_COOKIE))    $_COOKIE     = $myFilter->process($_COOKIE);
* if (!empty($_SESSION))    $_SESSION     = $myFilter->process($_SESSION);
*/
?>