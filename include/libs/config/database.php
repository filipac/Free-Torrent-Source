<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
if(!defined('IN_TRACKER'))
  die('Hacking attempt!');

@readconfig('DATABASE');
$mysql_host 	= FFactory::configoption($DATABASE['mysql_host'],'localhost');
$mysql_user 	= FFactory::configoption($DATABASE['mysql_user'],'root');
$mysql_pass 	= FFactory::configoption($DATABASE['mysql_pass'],'123456');
$mysql_db 		= FFactory::configoption($DATABASE['mysql_db'],'db');
?>