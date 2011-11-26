<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * @url http://freetosu.berlios.de
 * */  
HANDLE::Freq( 'libs.cache', 'cache' ) ; 
global $rootpath;
$path = $rootpath.'fts-contents/cache';
$_c = new Cache($path);
$c = new Cache ($path);
?>