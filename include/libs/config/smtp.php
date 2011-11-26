<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
if(!defined('IN_TRACKER'))
  die('Hacking attempt!');

@readconfig('SMTP');
#FFactory::configoption($SMTP[''],'')
$smtptype				=	FFactory::configoption($SMTP['smtptype'],'default');
$smtp_host			=	FFactory::configoption($SMTP['smtp_host'],'');
$smtp_port				=	FFactory::configoption($SMTP['smtp_port'],'25');
if (strtoupper(substr(PHP_OS,0,3)=='WIN'))
	$smtp_from		= FFactory::configoption($SMTP['smtp_from'],'');
$smtpaddress			= FFactory::configoption($SMTP['smtpaddress'],'');
$smtpport				= FFactory::configoption($SMTP['smtpport'],'25');
$accountname		= FFactory::configoption($SMTP['accountname'],'');
$accountpassword	= FFactory::configoption($SMTP['accountpassword'],'');
?>