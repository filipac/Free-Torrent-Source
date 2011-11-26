<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */
if ( ! defined('IN_TRACKER') )
    die( 'Hacking attempt!' ) ;
define( 'TIMENOW', time() ) ;
$starttime = explode(' ', microtime());
$starttime = $starttime[1] + $starttime[0];
?>