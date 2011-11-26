<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
if(!defined('IN_TRACKER'))
  die('Hacking attempt!');

@readconfig ('MAIN');
$SITE_ONLINE = FFactory::configoption($MAIN['site_online'],'yes');
$max_torrent_size = @FFactory::configoption($MAIN['maxtorrentsize'],'1000000');
$announce_interval = FFactory::configoption($MAIN['announce_interval'],'1800');
$signup_timeout = FFactory::configoption($MAIN['signup_timeout'],'259200');
$minvotes = FFactory::configoption($MAIN['minvotes'],'1');
$max_dead_torrent_time = FFactory::configoption($MAIN['max_dead_torrent_time'],'21600');
$maxusers = FFactory::configoption($MAIN['maxusers'],'2500');
$torrent_dir = FFactory::configoption($MAIN['torrent_dir'],'fts-contents/torrents');
$announce_urls = array();
$announce_urls[] = FFactory::configoption($MAIN['announce_urls'],"http://$_SERVER[HTTP_HOST]/announce.php");
$BASEURL = FFactory::configoption($MAIN['BASEURL'],"http://$_SERVER[HTTP_HOST]");
$DEFAULTBASEURL = FFactory::configoption($MAIN['DEFAULTBASEURL'],"http://$_SERVER[HTTP_HOST]");
$MEMBERSONLY =  FFactory::configoption($MAIN['MEMBERSONLY'],'yes');
$PEERLIMIT = FFactory::configoption($MAIN['PEERLIMIT'],'50000');
$SITEEMAIL =  FFactory::configoption($MAIN['SITEEMAIL'],"noreply@$_SERVER[HTTP_HOST]");
$SITENAME =  FFactory::configoption($MAIN['SITENAME'],'UN-NAMED');
$autoclean_interval = FFactory::configoption($MAIN['autoclean_interval'],900);
$pic_base_url = FFactory::configoption($MAIN['pic_base_url'],'pic/');
$table_cat =  FFactory::configoption($MAIN['table_cat'],'categories');
$reportemail = FFactory::configoption($MAIN['reportemail'],"report@$_SERVER[HTTP_HOST]");
$invitesystem = FFactory::configoption($MAIN['invitesystem'],'off');
$registration = FFactory::configoption($MAIN['registration'],'on');
$showpolls = FFactory::configoption($MAIN['showpolls'],'yes');
$showstats = FFactory::configoption($MAIN['showstats'],'yes');
$newsindex = FFactory::configoption($MAIN['newsindex'],'yes');
$showgoindex = FFactory::configoption($MAIN['showgoindex'],'no');
$pollindex = FFactory::configoption($MAIN['pollindex'],'yes');
$statsindex = FFactory::configoption($MAIN['statsindex'],'yes');
$discindex = FFactory::configoption($MAIN['discindex'],'yes');
$lastxfo = FFactory::configoption($MAIN['lastxfo'],'yes');
$lastxto = FFactory::configoption($MAIN['lastxto'],'yes');
$showlastxforumposts = FFactory::configoption($MAIN['showlastxforumposts'],'yes');
$howmuchforum = FFactory::configoption($MAIN['howmuchforum'],'5');
$showlastxtorrents = FFactory::configoption($MAIN['showlastxtorrents'],'yes');
$howmuchtorrents = FFactory::configoption($MAIN['howmuchtorrents'],'5');
$thowshow = FFactory::configoption($MAIN['thowshow'],'text');
$showtrackerload = FFactory::configoption($MAIN['showtrackerload'],'yes');
$showwhatsgoinon = FFactory::configoption($MAIN['showwhatsgoinon'],'yes');
$showshoutbox = FFactory::configoption($MAIN['showshoutbox'],'yes');
$clegend	= FFactory::configoption($MAIN['clegend'],'yes');
$waitsystem = FFactory::configoption($MAIN['waitsystem'],'yes');
$maxdlsystem = FFactory::configoption($MAIN['maxdlsystem'],'yes');
$bitbucket = FFactory::configoption($MAIN['bitbucket'],'fts-contents/bitbucket');
$cache = FFactory::configoption($MAIN['cache'],'fts-contents/cache');
$showforumstats = FFactory::configoption($MAIN['showforumstats'],'yes');
$verification = FFactory::configoption($MAIN['verification'],'automatic');
$invite_count = FFactory::configoption($MAIN['invite_count'],'12');
$invite_timeout = FFactory::configoption($MAIN['invite_timeout'],'1');
?>