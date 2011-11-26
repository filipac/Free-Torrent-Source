<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
/**
 * lang
 *
 * @package Free Torrent Source
 * @author Filip
 * @copyright 2008
 * @version $Id$
 * @access public
 * @lastmodified 24.02.2008 
 */
class lang
{
  /**
   * lang::load()
   *
   * @param mixed $page
   * @return
   */
    function load( $page )
    {
        global $deflang, $rootpath ;
        return @include ( $rootpath . "include/language/$deflang/$page.lang.php" ) ;
    }
  /**
   * lang::makedef()
   *
   * @param mixed $howmuch
   * @param string $type
   * @return
   */
    function makedef( $howmuch, $type = 'str' )
    {
        for ( $i = 1; $i <= $howmuch; $i++ )
        {
            echo "define('" . $type . "" . $i . "','');" . '<BR>' ;
        }
    }
}
?>