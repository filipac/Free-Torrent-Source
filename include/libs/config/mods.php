<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
if(!defined('IN_TRACKER'))
  die('Hacking attempt!');

@readconfig('MODS');
$enablezipmode = FFactory::configoption($MODS['enablezipmode'],'yes');
$ziptxt = FFactory::configoption($MODS['ziptxt'],'yes');
$zipnfo = FFactory::configoption($MODS['zipnfo'],'yes');
$imdbupload = FFactory::configoption($MODS['imdbupload'],'yes');
$tproghack = FFactory::configoption($MODS['tproghack'],'yes');
$pollf 		= FFactory::configoption($MODS['pollf'],'no');
$pollfid	= FFactory::configoption($MODS['pollfid'],'1');
$searchcloud = FFactory::configoption($MODS['searchcloud'],'yes');
define('NEWS_MODE',FFactory::configoption($MODS['newsmode'],'new'));
define('_ref_sys_',FFactory::configoption($MODS['referralsys'],'yes'));
define('_youtube_mod_',FFactory::configoption($MODS['youtubemod'],'yes'));
?>