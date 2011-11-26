<?php
function admin_table_start($title) {
	echo <<<ml
<table cellpadding="4" cellspacing="0" border="0" align="center" width="100%" class="tborder" id="cpform_table">
<tr>
	<td class="tcat" align="center" colspan="10">

		<b>$title</b>
	</td>
</tr>
<tr><td>
ml;
}
function admin_table_end() {
	echo <<<ml
	</td></tr>
</table>
ml;
}
function emptyp() {
	return "<p></p>";
}
function admin_special_start($title) {
	echo <<<ml
	<table cellpadding="4" cellspacing="0" border="0" align="center" width="100%" class="tborder" id="cpform_table">
<tr>
	<td class="tcat" align="center" colspan="10">

		<b>$title</b>
	</td>
</tr>
ml;
}
function admin_special_end() {
	echo "</table>";
}
function admin_special_extend_start() {
	echo "<tr class=alt2><td>";
}
function admin_special_extend_end() {
	echo "</td></tr>";
}
function version_check() { admin_special_start("Version Check"); global $rootpath; admin_special_extend_start();
     $cachefile = $rootpath."fts-contents/cache/staff-versioncheck.html";
     $cachetime = @get('cache_admin_vcheck'); // 5 minutes
     // Serve from the cache if it is younger than $cachetime
     if (file_exists($cachefile) && (time() - $cachetime
        < filemtime($cachefile))) 
     {
        include($cachefile);
        
     }else {
    
		ob_start();
	$lave = @file_get_contents("http://freetosu.sourceforge.net/lave.txt");
	$laveff = @file_get_contents("http://freetosu.sourceforge.net/laveff.txt");
		if(empty($lave)) {
		echo('<font color=red><b>There was a problem communicating with the version server. Please try again in a few minutes.</b></font>');
	} else {
	echo"Latest version of Free Torrent Source is $lave.";
	if(VERSION == $lave) {
		echo"<BR><font color=green>You are up-to-date. No update is necesary to Free Torrent Source.</font>";
	}elseif(VERSION < $lave) {
		echo"<BR><font color=red>A new version of Free Torrent Source is ready to download. Please upgrade. <a href=\"http://sourceforge.net/projects/freetosu/\" target=\"_blank\">Click here to download it.</a></font>";
	}
	elseif(VERSION > $lave) {
		echo('<BR><font color=darkred>You have a newer version of Free Torrent Source than the latest version. Maybe this is beta version?...'); }
	}
		if(empty($laveff)) {
		echo('<font color=red><b>There was a problem communicating with the Free Forums version server. Please try again in a few minutes.</b></font>');
	} else {
	echo"<hr><font color=black>Latest version of Free Forums is $laveff.</font>";
	global $rootpath;
	include_once $rootpath.'forums/functions/ver.php';
	if(FFver == $laveff) {
		echo"<BR><font color=green>You are up-to-date. No update is necesary to Free Forums.";
	}elseif(FFver < $laveff) {
		echo"<BR><font color=red>A new version of Free Forums is ready to download. Please upgrade. <a href=\"http://sourceforge.net/projects/freetosu/\" target=\"_blank\">Click here to download it.</a>";
	}
	elseif(FFver > $laveff) {
		echo('<BR><font color=darkred>You have a newer version of Free Forums than the latest version. Maybe this is beta version?...'); }
	}
		$fp = fopen($cachefile, 'w'); 
      // save the contents of output buffer to the file     
      fwrite($fp, ob_get_contents());
      // close the file
       fclose($fp); 
       // Send the output to the browser
       ob_flush();}
admin_special_extend_end();admin_special_end();echo emptyp();	
}
function form_start($action, $method) {
	echo "<form action=\"$action\" method=\"$method\">";
}
function form_end() {
	echo "</form>";
}
function get_count ($name, $where = '', $extra = '') {
	$res = sql_query('SELECT COUNT(*) as '.$name.' FROM '.$where.' '.($extra ? $extra : ''));
	list($info[$name]) = mysql_fetch_array($res);
	return $info[$name];
}

function get_server_load()
{	
	if(strtolower(substr(PHP_OS, 0, 3)) === 'win')
	{
		return '<font color=red>Linux Only</font>';
	}
	elseif(@file_exists("/proc/loadavg"))
	{
		$load = @file_get_contents("/proc/loadavg");
		$serverload = explode(" ", $load);
		$serverload[0] = round($serverload[0], 4);
		if(!$serverload)
		{
			$load = @exec("uptime");
			$load = split("load averages?: ", $load);
			$serverload = explode(",", $load[1]);
		}
	}
	else
	{
		$load = @exec("uptime");
		$load = split("load averages?: ", $load);
		$serverload = explode(",", $load[1]);
	}
	$returnload = trim($serverload[0]);
	if(!$returnload)
	{
		$returnload = '<font color=red>Unknown</font>';
	}
	return $returnload;
}

function get_mysql_version(){		
	$query = sql_query("SELECT VERSION() as version");
	$ver = mysql_fetch_array($query);
	if($ver['version'])
	{
		$version = explode(".", $ver['version'], 3);
		$version = intval($version[0]).".".intval($version[1]).".".intval($version[2]);
	}
	return $version;
}
function error1($title='Error',$msg='Could not select storage folder') {
	stdhead();
	stdmsg ($title, $msg);
	stdfoot();
	exit;
}
function fts_number_format($number, $decimals = 0, $bytesize = false, $decimalsep = null, $thousandsep = null)
{
	global $vbulletin, $vbphrase;

	$type = '';

	if (empty($number))
	{
		return 0;
	}
	else if (preg_match('#^(\d+(?:\.\d+)?)(?>\s*)([mkg])b?$#i', trim($number), $matches))
	{
		switch(strtolower($matches[2]))
		{
			case 'g':
				$number = $matches[1] * 1073741824;
				break;
			case 'm':
				$number = $matches[1] * 1048576;
				break;
			case 'k':
				$number = $matches[1] * 1024;
				break;
			default:
				$number = $matches[1] * 1;
		}
	}

	if ($bytesize)
	{
		if ($number >= 1073741824)
		{
			$number = $number / 1073741824;
			$decimals = 2;
			$type = " $vbphrase[gigabytes]";
		}
		else if ($number >= 1048576)
		{
			$number = $number / 1048576;
			$decimals = 2;
			$type = " $vbphrase[megabytes]";
		}
		else if ($number >= 1024)
		{
			$number = $number / 1024;
			$decimals = 1;
			$type = " $vbphrase[kilobytes]";
		}
		else
		{
			$decimals = 0;
			$type = " $vbphrase[bytes]";
		}
	}

	if ($decimalsep === null)
	{
		$decimalsep = $vbulletin->userinfo['lang_decimalsep'];
	}
	if ($thousandsep === null)
	{
		$thousandsep = $vbulletin->userinfo['lang_thousandsep'];
	}

	return str_replace('_', '&nbsp;', number_format($number, $decimals, $decimalsep, $thousandsep)) . $type;
}
function admin_stats() {
	global $rootpath;
     $cachefile = $rootpath."fts-contents/cache/staff-statistics.html";
     $cachetime = @get('cache_admin_stats'); // 15 minutes
     // Serve from the cache if it is younger than $cachetime
     if (file_exists($cachefile) && (time() - $cachetime
        < filemtime($cachefile))) 
     {
        include($cachefile);

        
     }else {
		ob_start();
	
$phpversion = phpversion();
	$mysqlversion = get_mysql_version();
	$serverload = get_server_load();
	$totalusers = get_count('totalusers', 'users', 'WHERE status=\'confirmed\'');
	$timecut = time() - 86400;
	$newuserstoday = get_count('totalnewusers', 'users', 'WHERE UNIX_TIMESTAMP(added) > '.sqlesc($timecut));
	$pendingusers = get_count('pendingusers', 'users', 'WHERE status = \'pending\'');
	$todaycomments = get_count('todaycomments', 'comments', 'WHERE UNIX_TIMESTAMP(added) > '.sqlesc($timecut));
	$gd2support = (extension_loaded('gd') ? '<font color=green>Enabled</font>' : '<font color=red>Disabled</font>');
	$sessionsupport = (function_exists('session_save_path') ? '<font color=green>Enabled</font>' : '<font color=red>Disabled</font>');
	$todayvisits = get_count('todayvisits', 'users', 'WHERE UNIX_TIMESTAMP(last_access) > '.sqlesc($timecut));
	if (preg_match('#(Apache)/([0-9\.]+)\s#siU', $_SERVER['SERVER_SOFTWARE'], $wsregs))
{
	$webserver = "$wsregs[1] v$wsregs[2]";
	if (SAPI_NAME == 'cgi' OR SAPI_NAME == 'cgi-fcgi')
	{
		$addsapi = true;
	}
}
else if (preg_match('#Microsoft-IIS/([0-9\.]+)#siU', $SERVER['SERVER_SOFTWARE'], $wsregs))
{
	$webserver = "IIS v$wsregs[1]";
	$addsapi = true;
}
else if (preg_match('#Zeus/([0-9\.]+)#siU', $SERVER['SERVER_SOFTWARE'], $wsregs))
{
	$webserver = "Zeus v$wsregs[1]";
	$addsapi = true;
}
else if (strtoupper($_SERVER['SERVER_SOFTWARE']) == 'APACHE')
{
	$webserver = 'Apache';
	if (SAPI_NAME == 'cgi' OR SAPI_NAME == 'cgi-fcgi')
	{
		$addsapi = true;
	}
}
else
{
	$webserver = SAPI_NAME;
}

if ($addsapi)
{
	$webserver .= ' (' . SAPI_NAME . ')';
}
define('SAFEMODE', (@ini_get('safe_mode') == 1 OR strtolower(@ini_get('safe_mode')) == 'on') ? true : false);

$serverinfo = SAFEMODE ? "<br />Safe Mode" : '';
$serverinfo .= (ini_get('file_uploads') == 0 OR strtolower(ini_get('file_uploads')) == 'off') ? "<br />File Uploads Disabled" : '';
global $db;
if ($variables = mysql_query("SHOW VARIABLES LIKE 'max_allowed_packet'"))
{
	$maxpacket = $variables['Value'];
}
else
{
	$maxpacket = $vbphrase['n_a'];
}
$memorylimit = ini_get('memory_limit');
$mem = ($memorylimit AND $memorylimit != '-1') ? fts_number_format($memorylimit, 2, true) : "None";
	$str = '
	<tr>
	<td class="alt1"><div align="left"><b>Server Type</b></div></td>
	<td class="alt2"><div align="left"><b>'.PHP_OS . $serverinfo.'</b></div></td>
	<td class="alt1"><div align="left"><b>Web Server</b></div></td>
	<td class="alt2"><div align="left"><b>'.$webserver.'</b></div></td>
	</tr>
  <tr>
    <td class="alt1"><div align="left"><b>PHP Version</b></div></td>
    <td class="alt2"><div align="left">'.$phpversion.'</div></td>
    <td class="alt1"><div align="left"><b>Total Users</b></div></td>
    <td class="alt2"><div align="left">'.$totalusers.'</div></td>
  </tr>
  <tr>
  <td class=alt1><div align=left>PHP Memory Limit</div></td>
    <td class=alt2><div align=left>'.$mem .'</div></td>
    <td class="alt1"><div align="left"><b>Active Users Today</b></div></td>
    <td class="alt2"><div align="left">'.$todayvisits.'</div></td>
  </tr>
  <tr>
    <td class="alt1"><div align="left"><b>MYSQL Version</b></div></td>
    <td class="alt2"><div align="left">'.$mysqlversion.'</div></td>
    <td class="alt1"><div align="left"><b>New Users Today</b></div></td>
    <td class="alt2"><div align="left">'.$newuserstoday.'</div></td>
  </tr>
  <tr>
    <td class=alt1><div align=left>Mysql Packet Size</div></td>
	<td class=alt2><div align=left>'.fts_number_format($maxpacket, 2, 1).'</div></td>
    <td class="alt1"><div align="left"><b>Unconfirmed Users</b></div></td>
    <td class="alt2"><div align="left">'.$pendingusers.'</div></td>
  </tr>
   <tr>
    <td class="alt1"><div align="left"><b>Session Support</b></div></td>
    <td class="alt2"><div align="left">'.$sessionsupport.'</div></td>
    <td class="alt1"><div align="left"><b>GD2 Support</b></div></td>
    <td class="alt2"><div align="left">'.$gd2support.'</div></td>
  </tr>
    <tr>
    <td class="alt1"><div align="left"><b>New Comments Today</b></div></td>
    <td class="alt2"><div align="left">'.$todaycomments.'</div></td>
    <td class="alt1"><div align="left"><b> </b></div></td>
    <td class="alt2"><div align="left"> </div></td>
  </tr>
  ';
	
	echo $str;
	$fp = fopen($cachefile, 'w'); 
      // save the contents of output buffer to the file     
      fwrite($fp, ob_get_contents());
      // close the file
       fclose($fp); 
       // Send the output to the browser
       ob_flush();
	}
}
?>