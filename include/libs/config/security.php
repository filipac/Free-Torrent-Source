<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
if(!defined('IN_TRACKER'))
  die('Hacking attempt!');

@readconfig('SECURITY');
#FFactory::configoption($SECURITY[''],'')
$sechash = FFactory::configoption($SECURITY['sechash'],'123456');
$securelogin			= FFactory::configoption($SECURITY['securelogin'],'yes');
$iv						= FFactory::configoption($SECURITY['iv'],'no');
$maxip 					= FFactory::configoption($SECURITY['maxip'],'2');
$maxloginattempts 	= FFactory::configoption($SECURITY['maxloginattempts'],'7');
$disablerightclick		= FFactory::configoption($SECURITY['disablerightclick'],'yes');
$vkeysys = FFactory::configoption($SECURITY['vkeysys'],'yes');
$privatep = FFactory::configoption($SECURITY['privatep'],'no');
?>