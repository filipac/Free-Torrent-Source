<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
if(!defined('IN_TRACKER'))
  die('Hacking attempt!');

@readconfig('TWEAK');
$where				= FFactory::configoption($TWEAK['where'],'yes');
$iplog1				= FFactory::configoption($TWEAK['iplog1'],'yes');
$iplog2				= FFactory::configoption($TWEAK['iplog2'],'yes');
$ctracker 			= FFactory::configoption($TWEAK['ctracker'],'yes');
$bonus				= FFactory::configoption($TWEAK['bonus'],'enable');
$autorefresh		= FFactory::configoption($TWEAK['autorefresh'],'yes');
$autorefreshtime	= FFactory::configoption($TWEAK['autorefreshtime'],'60');
$leftmenu			= FFactory::configoption($TWEAK['leftmenu'],'yes');
$leftmenunl			= FFactory::configoption($TWEAK['leftmenunl'],'yes');
$shoutname			= FFactory::configoption($TWEAK['shoutname'],'BOT');
$shoutbot			= FFactory::configoption($TWEAK['shoutbot'],'yes'); 
$splitor			= FFactory::configoption($TWEAK['splitor'],'yes');
$shoutduty			= FFactory::configoption($TWEAK['shoutduty'],'torrents,cleanups,requests,users,topics');
$imageresizermode	= FFactory::configoption($TWEAK['imageresizermode'],'enlarge'); 
?>