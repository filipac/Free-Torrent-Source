<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
/**
 * SQL
 *
 * @package Free Torrent Source
 * @author Filip
 * @copyright 2008
 * @version $Id$
 * @access public
 */
class SQL
{
    var $_query = null ;
    var $debugmode = 0 ;
    var $_queries = array() ;
    var $_link ;
    var $_res ;

  /**
   * SQL::SQL()
   *
   * @param mixed $host
   * @param mixed $user
   * @param mixed $pass
   * @param mixed $db_name
   * @return
   */
    function SQL( $host, $user, $pass, $db_name )
    {
        $this->_link = @mysql_connect( $host, $user, $pass ) ;
        if ( ! $this->_link )
        {
            switch ( mysql_errno() )
            {
                case 1040:
                case 2002:
                    if ( $_SERVER['REQUEST_METHOD'] == 'GET' )
                        error( 6 ) ;
                    else
                        error( 6 ) ;
                default:
                    error( 5, "mysql_connect: " . mysql_error() ) ;
            }
        }
        if ( ! mysql_select_db($db_name) )
            error( 5, 'mysql_select_db: ' . mysql_error() ) ;
        return true ;
    }

  /**
   * SQL::query()
   *
   * @param mixed $sql
   * @return
   */
    function query( $sql )
    {
        $this->_query = $sql ;
        if ( func_num_args() > 1 )
        {
            $params = func_get_args() ;
            unset( $params[0] ) ;
            preg_match_all( '/%\+?(?:(?:\'.)| |0)?-?\d*?(?:\.\d*)?([bcdeufFosxX])/', $sql, $ret ) ;
            foreach ( $ret[1] as $k => $type )
            {
                switch ( $type )
                {
                    case 'u':
                        if ( $params[$k + 1] < 0 )
                            $this->err( 'Not an unsigned integer', 0 ) ;
                    case 'd':
                        if ( ! is_numeric($params[$k + 1]) )
                            $this->err( 'Not an integer', 0 ) ;
                        $params[$k + 1] = ( int )$params[$k + 1] ;
                        break ;
                    case 'f':
                    case 'F':
                        if ( ! is_numeric($paraks[$k + 1]) )
                            $this->err( 'Not a float', 0 ) ;
                        $params[$k + 1] = ( float )$params[$k + 1] ;
                        break ;
                    case 's':
                        $params[$k + 1] = '\'' . $this->real_escape_string( $params[$k + 1] ) . '\'' ;
                }
            }
            $p = '' ;
            for ( $i = 1; $i <= count($params); $i++ )
            {
                $p .= ', $params[' . $i . ']' ;
            }
            $sql = eval( 'return sprintf("' . $sql . '"' . $p . ');' ) ;
        }
        $this->_query = $sql ;
        $this->_res = mysql_query( $sql, $this->_link ) ;
        if ( mysql_errno() != 0 && error_reporting() != 0 )
        {
            $this->err() ;
        }
        return $this->_res ;
    }

  /**
   * SQL::fetch_row()
   *
   * @param mixed $res
   * @return
   */
    function fetch_row( $res )
    {
        return mysql_fetch_row( $res ) ;
    }

  /**
   * SQL::fetch_assoc()
   *
   * @param mixed $res
   * @return
   */
    function fetch_assoc( $res )
    {
        return mysql_fetch_assoc( $res ) ;
    }

  /**
   * SQL::fetch_array()
   *
   * @param mixed $res
   * @return
   */
    function fetch_array( $res )
    {
        return mysql_fetch_array( $res ) ;
    }

  /**
   * SQL::result()
   *
   * @param mixed $res
   * @param mixed $num
   * @return
   */
    function result( $res, $num )
    {
        return mysql_result( $res, $num ) ;
    }

  /**
   * SQL::errno()
   *
   * @return
   */
    function errno()
    {
        return mysql_errno() ;
    }
  /**
   * SQL::error()
   *
   * @return
   */
    function error()
    {
        return mysql_error() ;
    }
  /**
   * SQL::free_result()
   *
   * @param mixed $res
   * @return
   */
    function free_result( $res )
    {
        return mysql_free_result( $res ) ;
    }
  /**
   * SQL::insert_id()
   *
   * @return
   */
    function insert_id()
    {
        return mysql_insert_id() ;
    }
  /**
   * SQL::num_rows()
   *
   * @param mixed $res
   * @return
   */
    function num_rows( $res )
    {
        return mysql_num_rows( $res ) ;
    }
  /**
   * SQL::escape_string()
   *
   * @param mixed $s
   * @return
   */
    function escape_string( $s )
    {
        return mysql_escape_string( $s ) ;
    }
  /**
   * SQL::real_escape_string()
   *
   * @param mixed $s
   * @return
   */
    function real_escape_string( $s )
    {
        return mysql_real_escape_string( $s ) ;
    }
  /**
   * SQL::affected_rows()
   *
   * @return
   */
    function affected_rows()
    {
        return mysql_affected_rows() ;
    }
  /**
   * SQL::select_db()
   *
   * @param mixed $db
   * @return
   */
    function select_db( $db )
    {
        return mysql_select_db( $db ) ;
    }
  /**
   * SQL::err()
   *
   * @param string $errstr
   * @param integer $fatal
   * @return
   */
    function err( $errstr = '', $fatal = 1 )
    {
        if ( $this->debugmode )
        {
            print ( '<div style="border: 4px solid red; background-color: #eaeaea; color: black; padding: 20px; font-size: 12px;"><h2 style="color: red; font-size: 24px;">SQL Error</h2>You have an SQL error:<br/><pre>' .
                $this->_query . '</pre>. MySQL returned: ' . $this->error() . '</div>' ) ;
            die() ;
        }
        //$err = error_reporting();
        //error_reporting(0);
        //err logging function
        global $_logger, $CURUSER ;
        $log = array( 'type' => 'sql' ) ;
        $log['query'] = $this->_query ;
        if ( $errstr )
            $log['errstr'] = $errstr ;
        else
            $log['errstr'] = '[' . mysql_errno() . '] ' . mysql_error() ;

        if ( function_exists('debug_print_backtrace') )
        {
            $log['backtrace'] = debug_backtrace() ;
            //unset($log['backtrace'][0]);
            $d = '' ;
            foreach ( $log['backtrace'] as $k => $p )
            {
                $d .= '#' . $k . '  ' . ( isset($p['object']) ? get_class($p['object']) . '->' :
                    '' ) . $p['function'] . '(' . implode( ', ', $p['args'] ) . ') called at [' . ( isset
                    ($p['file']) ? $p['file'] . ':' . $p['line'] : 'PHP' ) . ']' . "\n" ;
            }
            $log['FILE'] = $log['backtrace'][1]['file'] ;
            $log['LINE'] = $log['backtrace'][1]['line'] ;
            unset( $log['backtrace'] ) ;
            $log['call_stack'] = $d ;
        }
        $log['url'] = $_SERVER['REQUEST_URI'] ;
        $log['REMOTE']['HOST'] = $_SERVER['REMOTE_ADDR'] ;
        $log['REMOTE']['PORT'] = $_SERVER['REMOTE_PORT'] ;
        $log['GET'] = $_GET ;
        $log['POST'] = $_POST ;
        $log['COOKIE'] = $_COOKIE ;
        $log['CURUSER'] = $CURUSER ;
        $_logger->write( $log ) ;

        if ( ! $fatal )
        {
            /* dont show to the user, just log it and go on */
            //error_reporting($err);
            return ;
        }
        global $BASEURL ;
        if ( ! ob_end_clean() )
        {
            /* the buffer couldn't be cleared.
            * show plain error - inside html code
            */
            print ( '<div style="border: 4px solid red; background-color: #eaeaea; color: black; padding: 20px; font-size: 12px;"><h2 style="color: red; font-size: 24px;">Error</h2>Hi there, we had an unexpected error. Please retry again in a few minutes. <br/><small style="color: #aaaaaa">The error has been logged</small></div>' ) ;
            $_logger->Save() ;
            die() ;
            return ;
        }
        /* show nice error */
        header( 'Content-encoding: plain' ) ;
        echo '<?xml version="1.0" encoding="UTF-8"?>' .
            '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' .
            '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">' . '<head>' .
            '<meta http-equiv="Content-Language" content="pl" />' .
            '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />' .
            '<link rel="stylesheet" type="text/css" href="' . $BASEURL .
            '/themes/exception.css" />
  			<!--[if lte IE 6]><link rel="stylesheet" type="text/css" href="' . $BASEURL .
            '/themes/exception_ie6.css" /><![endif]-->' . '<title>Houston, we\'ve got a problem.</title>' .
            '</head><body>' . '<div id="wrapper">
  					<div id="cell" style="background-image:url(\'' . $BASEURL .
            '/themes/error.gif\')">' . '<h2>We\'ve encountered some errors.</h2><p>Please sit back and have a short nap or cup of tea/coffee :) We\'ll be back shortly.</p><small>This error has been logged for further investigations.<br/>' .
            $errstr . '</small>' . '</div></div>' . '</body></html>' ;
        $_logger->Save() ;
        die() ;
    }
}
if(!function_exists("error")) {
/**
 * error()
 *
 * @param mixed $errorid
 * @param string $exmess
 * @return
 */
function error($errorid, $exmess = '')
{
    define('errorid', $errorid);
    if (!empty($exmess))
        define('exmess', $exmess);
    global $rootpath;
    include $rootpath . 'error.php';
    die;
}
}
?>