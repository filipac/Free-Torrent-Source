<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
/**
 * Logger
 *
 * @package Free Torrent Source
 * @author Filip
 * @copyright 2008
 * @version $Id$
 * @access public
 */
class Logger
{
    var $logs = array() ;
    var $cwd = '' ;

  /**
   * Logger::Logger()
   *
   * @return
   */
    function Logger()
    {
        global $rootpath ;
        $this->logs = array() ;
        $this->cwd = getcwd() . '/' ;
        #	set_error_handler(Array($this, 'error_handler'));
    }

  /**
   * Logger::error_handler()
   *
   * @param mixed $errno
   * @param mixed $errstr
   * @param mixed $errfile
   * @param mixed $errline
   * @param mixed $errcontext
   * @return
   */
    function error_handler( $errno, $errstr, $errfile, $errline, $errcontext )
    {
        if ( error_reporting() == 0 )
        {
            /*
            * dont log this error, the one who wrote that code knew what he wanted
            */
            return true ;
        }
        if ( $errno == E_STRICT || $errno == E_NOTICE )
        {
            return ;
        }
        $log = array( 'type' => 'php' ) ;
        $log['errno'] = $errno ;
        $log['errstr'] = $errstr ;
        if ( function_exists('debug_print_backtrace') )
        {
            $log['backtrace'] = debug_backtrace() ;
            unset( $log['backtrace'][0] ) ;
            $d = '' ;
            foreach ( $log['backtrace'] as $k => $p )
            {
                $d .= '#' . $k . '  ' . ( isset($p['class']) ? $p['class'] . $p['type'] : '' ) .
                    $p['function'] . '(' . ( isset($p['args']) ? (is_array($p['args']) ? implode(', ',
                    $p['args']) : $p['args']) : '' ) . ') called at [' . ( isset($p['file']) ? $p['file'] .
                    ':' . $p['line'] : 'PHP' ) . ']' . "\n" ;
            }
            $log['call_stack'] = $d ;
        }
        $log['FILE'] = $errfile ;
        $log['LINE'] = $errline ;
        $log['GET'] = $_GET ;
        $log['POST'] = $_POST ;
        $log['COOKIE'] = $_COOKIE ;
        $log['url'] = $_SERVER['REQUEST_URI'] ;
        $log['REMOTE']['HOST'] = $_SERVER['REMOTE_ADDR'] ;
        $log['REMOTE']['PORT'] = $_SERVER['REMOTE_PORT'] ;

        $this->write( $log ) ;
        return true ;
    }

  /**
   * Logger::write()
   *
   * @param mixed $text
   * @return
   */
    function write( $text )
    {
        $this->logs[] = array( 'added' => time(), 'txt' => $text ) ;
    }

  /**
   * Logger::Save()
   *
   * @return
   */
    function Save()
    {
        if ( ! $this->logs )
        {
            return ;
        }
        $f = $this->cwd . '' . date( 'Y-m-d' ) . '.log' ;
        $h = fopen( $f, 'w+' ) ;
        if ( ! $h )
        {
            return ;
        }
        flock( $h, LOCK_EX ) ;
        for ( $i = 0; $i < count($this->logs); $i++ )
        {
            fputs( $h, serialize($this->logs[$i]) . "\n" ) ;
        }

        flock( $h, LOCK_UN ) ;
        fclose( $h ) ;
    }
}
?>