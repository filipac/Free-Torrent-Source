<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */
if ( ! defined('IN_TRACKER') )
    die( 'Hacking attempt!' ) ;
# Define default classes
define( "UC_USER", 0 ) ;
define( "UC_POWER_USER", 1 ) ;
define( "UC_VIP", 2 ) ;
define( "UC_UPLOADER", 3 ) ;
define( "UC_MODERATOR", 4 ) ;
define( "UC_ADMINISTRATOR", 5 ) ;
define( "UC_SYSOP", 6 ) ;
define( "UC_STAFFLEADER", 7 ) ;
?>