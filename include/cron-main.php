<?php

/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */   
if ( ! defined('IN_TRACKER') )
    die( 'Hacking attempt!' ) ;

/**
 * docleanup()
 *
 * @return
 */
/**
 * externalcron
 *
 * @package Free Torrent Source
 * @author Filip
 * @copyright 2008
 * @version $Id$
 * @access public
 */
 class externalcron
{
  /**
   * externalcron::load()
   *
   * @param mixed $type
   * @return
   */
    function load( $type )
    {
        global $rootpath ;
        return include "cron_$type.php" ;
    }
}
function docleanup()
{
	
cron::stopint();
cron::MainCron();
cron::deadtime();
cron::torrentscron();
externalcron::load('failed_logins');
externalcron::load('invites');
externalcron::load('regimage');
externalcron::load('inactive');
externalcron::load('parked');
externalcron::load('forums');
externalcron::load('reports');
externalcron::load('removevip');
externalcron::load('poweruser');
externalcron::load('kps'); 
externalcron::load('stats');
externalcron::load('leechwarn');
cron::announce();
do_action("cron");
}
?>