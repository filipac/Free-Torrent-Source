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
 * This constant defines how a line break is made.
 **/ 
define ('_br','<br>');
/**
 * This constant is set to replace the $_SERVER['PHP_SELF'] variable...
 **/ 
define ('SERVER_PHP_SELF',$_SERVER["PHP_SELF"]);
/**
 * This constant is set to replace the php function mysql_error()
 **/
define ('MYSQL_ERROR',mysql_error());
/**
 * This constant is for security. DO NOT MODIFY!!! We have warned you!
 **/
define ('SEC_HASH', 'tesssssssssst');

do_action("custom");
?>