<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
if(!defined('IN_TRACKER'))
  die('Hacking attempt!');

@readconfig('TEMPLATE');
$defaulttemplate = FFactory::configoption($TEMPLATE['defaulttemplate'],'ANDiTKO');
$charset = FFactory::configoption($TEMPLATE['charset'],'UTF-8');
$metadesc = FFactory::configoption($TEMPLATE['metadesc'],'desc');
$metakeywords = FFactory::configoption($TEMPLATE['metakeywords'],'k');
?>