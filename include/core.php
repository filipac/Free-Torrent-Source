<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */
if ( ! defined('IN_TRACKER') )
    die( 'Hacking attempt!' ) ;
if ( empty($rootpath) )
    $rootpath = THIS_ROOT_PATH ;
include_once ( $rootpath . 'include/time.php' ) ;
include_once ( $rootpath . 'include/load_uc.php' ) ;
set_magic_quotes_runtime( 0 ) ;
error_reporting( E_ALL & ~ E_NOTICE ) ;
ignore_user_abort( 1 ) ;
@set_time_limit( 0 ) ;
include_once ( $rootpath . 'include/version.php' ) ;    
include_once ( $rootpath . 'include/functions.php' ) ;
if ( $ctracker == "yes" )
    require_once ( $rootpath . 'include/ctracker.php' ) ;
/*require_once($rootpath . 'include/class.inputfilter_clean.php');*/
require_once ( $rootpath . 'include/class_page_check.php' ) ;
require_once ( $rootpath . 'include/init.php' ) ;

?>
