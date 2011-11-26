<?php
function register_notification_text($text) {
	return $GLOBALS['_texts'][] = $text;
}
function add_meta_tag($name,$value) {
	echo "<meta name=\"$name\" content=\"$value\" />";
}
/**
 * ReadConfig()
 *
 * @param mixed $configname
 * @return
 */
function ReadConfig($configname)
{
    if (strstr($configname, ',')) {
        $configlist = explode(',', $configname);
        foreach ($configlist as $key => $configname) {
            ReadConfig(trim($configname));
        }
    } else {
        $configname = basename($configname);
        global $rootpath;
        $path = $rootpath . 'config/' . $configname;
        if (!file_exists($path)) {
            return;
        }
        $fp = fopen($path, 'r');
        $content = '';
        while (!feof($fp)) {
            $content .= fread($fp, 102400);
        }
        fclose($fp);
        if (empty($content)) {
            return array();
        }
        $tmp = @unserialize($content);
        if (empty($tmp)) {
            stderr("ERROR", "<font color=red>Cannot a file [<b>" . htmlspecialchars($configname) .
                "</b>]!.</font><br><font color=blue>Before the setup starts, please ensure that you have properly configured file and directory access permissions. Please see below.</font><br><br>chmod 777 CONFIG (config directory).<br>chmod 777 CONFIG/main (the file which save the main settings).", false);
        }
        $GLOBALS[$configname] = $tmp;
        return true;
    }
}
/**
 * WriteConfig()
 *
 * @param mixed $configname
 * @param mixed $config
 * @return
 */
function WriteConfig($configname, $config)
{
    $configname = basename($configname);
    global $rootpath;
    $path = $rootpath . 'config/' . $configname;
    if (!file_exists($path) || !is_writable($path)) {
@fopen($path, 'w');
    }
    $data = @serialize($config);
    if (empty($data)) {
        stdmsg("ERROR", "<font color=red>Cannot serialize file [<b>" . htmlspecialchars
            ($configname) . "</b>]</font><br><font color=blue>Before the setup starts, please ensure that you have properly configured file and directory access permissions. Please see below.</font><br><br>chmod 777 CONFIG (config directory).<br>chmod 777 CONFIG/main (the file which save the main settings).", false);
    }
    $fp = @fopen($path, 'w');
    if (!$fp) {
        stdmsg("ERROR", "<font color=red>Cannot open file [<b>" . htmlspecialchars($configname) .
            "</b>] to save info!.</font><br><font color=blue>Before the setup starts, please ensure that you have properly configured file and directory access permissions. Please see below.</font><br><br>chmod 777 CONFIG (config directory).<br>chmod 777 CONFIG/main (the file which save the main settings).", false);
    }
    $Res = @fwrite($fp, $data);
    if (empty($Res)) {
        stdmsg("ERROR", "<font color=red>Cannot save info in file (error in serialisation) [<b>" .
            htmlspecialchars($configname) .
            "</b>] to save info!.</font><br><font color=blue>Before the setup starts, please ensure that you have properly configured file and directory access permissions. Please see below.</font><br><br>chmod 777 CONFIG (config directory).<br>chmod 777 CONFIG/main (the file which save the main settings).", false);
    }
    fclose($fp);
    return true;
}
/**
 * GetVar()
 *
 * @param mixed $name
 * @return
 */
function GetVar($name)
{
    if (is_array($name)) {
        foreach ($name as $var)
            GetVar($var);
    } else {
        if (!isset($_REQUEST[$name]))
            return false;
        if (get_magic_quotes_gpc()) {
            $_REQUEST[$name] = ssr($_REQUEST[$name]);
        }
        $GLOBALS[$name] = $_REQUEST[$name];
        return $GLOBALS[$name];
    }
}
/**
 * ssr()
 *
 * @param mixed $arg
 * @return
 */
function ssr($arg)
{
    if (is_array($arg)) {
        foreach ($arg as $key => $arg_bit) {
            $arg[$key] = ssr($arg_bit);
        }
    } else {
        $arg = stripslashes($arg);
    }
    return $arg;
}
function siteinfo($type, $echo = false) {
	switch($type):
	case "sitename":
	global $SITENAME;
	if(!$echo)
	return $SITENAME;
	else
	echo $SITENAME;
	break;
	case "baseurl":
	global $BASEURL;
	if(!$echo)
	return $BASEURL;
	else
	echo $BASEURL;
	break;
	case "maxusers":
	global $maxusers;
	if(!$echo)
	return $maxusers;
	else
	echo $maxusers;
	break;
	case "torrent_dir":
	global $torrent_dir;
	if(!$echo)
	return $torrent_dir;
	else
	echo $torrent_dir;
	break;
	case "defaultbaseurl":
	global $DEFAULTBASEURL;
	if(!$echo)
	return $DEFAULTBASEURL;
	else
	echo $DEFAULTBASEURL;
	break;
	case "membersonly":
	global $MEMBERSONLY;
	if(!$echo)
	return $MEMBERSONLY;
	else
	echo $MEMBERSONLY;
	break;
	case "peerlimit":
	global $PEERLIMIT;
	if(!$echo)
	return $PEERLIMIT;
	else
	echo $PEERLIMIT;
	break;
	case "mail":
	global $SITEEMAIL;
	if(!$echo)
	return $SITEEMAIL;
	else
	echo $SITEEMAIL;
	break;
	case "picbaseurl":
	global $pic_base_url;
	if(!$echo)
	return $pic_base_url;
	else
	echo $pic_base_url;
	break;
	case "reportemail":
	global $reportemail;
	if(!$echo)
	return $reportemail;
	else
	echo $reportemail;
	break;
	case "invitesystem":
	global $invitesystem;
	if(!$echo)
	return $invitesystem;
	else
	echo $invitesystem;
	break;
	case "registration":
	global $registration;
	if(!$echo)
	return $registration;
	else
	echo $registration;
	break;
	case "cache":
	global $cache;
	if(!$echo)
	return $cache;
	else
	echo $cache;
	break;
	case "site_online":
	global $SITE_ONLINE;
	if(!$echo)
	return $SITE_ONLINE;
	else
	echo $SITE_ONLINE;
	break;
	case "max_torrent_size":
	global $max_torrent_size;
	if(!$echo)
	return $max_torrent_size;
	else
	echo $max_torrent_size;
	break;
	case "announce_interval":
	global $announce_interval;
	if(!$echo)
	return $announce_interval;
	else
	echo $announce_interval;
	break;
	case "max_dead_torrent_time":
	global $max_dead_torrent_time;
	if(!$echo)
	return $max_dead_torrent_time;
	else
	echo $max_dead_torrent_time;
	break;
	case "defaulttemplate":
	global $defaulttemplate;
	if(!$echo)
	return $defaulttemplate;
	else
	echo $defaulttemplate;
	break;
	case "charset":
	global $charset;
	if(!$echo)
	return $charset;
	else
	echo $charset;
	break;
	case "metadesc":
	global $metadesc;
	if(!$echo)
	return $metadesc;
	else
	echo $metadesc;
	break;
	case "metakeywords":
	global $metakeywords;
	if(!$echo)
	return $metakeywords;
	else
	echo $metakeywords;
	break;
	endswitch;
}
?>