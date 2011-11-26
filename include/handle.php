<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
########################################################
#			FTS Handle Functions					   #
#			Do Not Edit								   #
########################################################
/**
 * HANDLE
 *
 * @package Free Torrent Source
 * @author Filip
 * @copyright 2008
 * @version $Id$
 * @access public
 */
class HANDLE
{
  /**
   * HANDLE::Freq()
   *
   * @param mixed $where
   * @param mixed $file
   * @param string $type
   * @param integer $ui
   * @return
   */
    public function Freq( $where, $file, $type = '_class.php', $ui = 0 )
    {
        if ( is_array($where) )
            die( 'HANDLE strict rules: \$where parameter must not be an array' ) ;
        $where = explode( '.', $where ) ;
        if ( ! is_array($where) )
            die( 'HANDLE error: cannot make an array' ) ;
        global $rootpath ;
        $pth = '' ;
        foreach ( $where as $where )
        {
            $pth .= $where . '/' ;
        }
        #echo $rootpath;
        include $rootpath . 'include/' . $pth . $file . $type ;
    }
  /**
   * HANDLE::dep()
   *
   * @param mixed $file
   * @return
   */
    public function dep( $file )
    {
        include $file ;
    }

  /**
   * HANDLE::hiderr()
   *
   * @return
   */
    public function hiderr()
    {
        return error_reporting( 0 ) ;
    }

  /**
   * HANDLE::showerr()
   *
   * @return
   */
    public function showerr()
    {
        return error_reporting( 1 ) ;
    }

  /**
   * HANDLE::checkins()
   *
   * @return
   */
    public function checkins()
    { 
        global $rootpath,$passChecK ;
        if ( file_exists($rootpath . 'install') && ! file_exists($rootpath .
            'install/install.lock') && !$passChecK )
            error( 8, '<BR>If you haven\'t installed FTS yet, click <a href="install">here</a> to install it.<BR>If you already installed FTS and you think this is an error, make an file named install.lock in install folder(or simply delete the install folder)' ) ;
    }

  /**
   * HANDLE::strip_magic_quotes()
   *
   * @param mixed $arr
   * @return
   */
    public function strip_magic_quotes( $arr )
    {
        foreach ( $arr as $k => $v )
        {
            if ( is_array($v) )
            {
                $arr[$k] = HANDLE::strip_magic_quotes( $v ) ;
            }
            else
            {
                $arr[$k] = stripslashes( $v ) ;
            }
        }

        return apply_filters("strip_magic_quotes",$arr) ;
    }

  /**
   * HANDLE::htmlspecialchars_uni()
   *
   * @param mixed $text
   * @param bool $entities
   * @return
   */
    public function htmlspecialchars_uni( $text, $entities = true )
    {
        return apply_filters("htmlspecialchars_uni",str_replace( // replace special html characters
            array('<', '>', '"'), array('&lt;', '&gt;', '&quot;'), preg_replace( // translates all non-unicode entities
            '/&(?!' . ($entities ? '#[0-9]+' : '(#[0-9]+|[a-z]+)') . ';)/si', '&', $text) )) ;
    }

  /**
   * HANDLE::cur_user_check()
   *
   * @param bool $redirect
   * @return
   */
    public function cur_user_check($redirect = true)
    {
        global $CURUSER ;
        if ( $CURUSER ) {
        	if(!$redirect)
            stderr( "Permission denied!", "You are already logged in!" ) ;
            else
            redirect('index.php','You are already logged in!','Permission denied!');
            }
    }
}
global $rootpath ;

/**
 * UserHandle
 *
 * @package Free Torrent Source
 * @author Filip
 * @copyright 2008
 * @version $Id$
 * @access public
 */
class UserHandle
{
  /**
   * UserHandle::KPS()
   *
   * @param string $type
   * @param string $point
   * @param string $id
   * @return
   */
    public function KPS( $type = "+", $point = "1.0", $id = "" )
    {
        global $bonus, $_db ;
        if ( $bonus == "enable" or $bonus == "disablesave" )
            $_db->query( "UPDATE users SET seedbonus = seedbonus$type$point WHERE id = " .
                sqlesc($id) ) or sqlerr( __file__, __line__ ) ;
        else
            return ;
    }
}
?>