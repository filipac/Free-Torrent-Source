<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
/**
 * ur
 *
 * @package Free Torrent Source
 * @author Filip
 * @copyright 2008
 * @version $Id$2
 * @access public
 * @latmodified 24.02.2008 
 */
class ur
{
  /**
   * ur::cstaff()
   *
   * @return
   */
    public function cstaff()
    {
        global $usergroups ;
        if ( $usergroups['canstaffpanel'] != 'yes' )
            return false ;
        else
            return true ;
    }

  /**
   * ur::ismod()
   *
   * @return
   */
    public function ismod()
    {
        global $usergroups ;
        if ( $usergroups['canstaffpanel'] != 'yes' )
            return false ;
        else
            return true ;
    }
  /**
   * ur::isadmin()
   *
   * @return
   */
    public function isadmin()
    {
        global $usergroups ;
        if ( $usergroups['cansettingspanel'] != 'yes' )
            return false ;
        else
            return true ;
    }
}
?>