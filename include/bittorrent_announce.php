<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * @url http://freetosu.berlios.de
 * */   
session_cache_expire( 1440 ) ; // 24 hours
session_start() ;
define( 'IN_TRACKER', true ) ;
define( 'THIS_ROOT_PATH', './' ) ;
define( 'ROOT_PATH', './../' ) ;
define('INC_PATH', dirname (__FILE__));
ini_set("display_errors",true);
error_reporting(E_ALL);
if ( empty($rootpath) )
    $rootpath = THIS_ROOT_PATH ;
define("PLUGIN_DIR",$rootpath.'fts-contents/fts-plugins');
$deflang = 'english';
include_once ($rootpath . 'include/plug_functions.php');
include_once ($rootpath . 'include/class_sql.php');
include $rootpath . 'include/factory.php' ;
include_once ($rootpath . 'include/libs/config/database.php');
global $mysql_db,$mysql_host,$mysql_pass,$mysql_user;
$_db = new SQL($mysql_host,$mysql_user,$mysql_pass,$mysql_db);
include_once ($rootpath . 'include/libs/options/main_class.php');
include_once ($rootpath . 'include/plug.php');
include_plugins();
include_once ($rootpath . 'include/core_lang.php');
lang::load('global_strings');
include ( $rootpath . 'include/core.php' ) ;
require ( $rootpath . 'include/global.php' ) ;
require ( $rootpath . 'include/custom.php');
?>