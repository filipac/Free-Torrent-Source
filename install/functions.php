<?php
# IMPORTANT: Do not edit below unless you know what you are doing!
if(!defined('IN_INSTALL'))
  die('Hacking attempt!');
  
  function validusername($username)
{
	if ($username == "")
	  return false;

	// The following characters are allowed in user names
	$allowedchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

	for ($i = 0; $i < strlen($username); ++$i)
	  if (strpos($allowedchars, $username[$i]) === false)
	    return false;

	return true;
}
function validemail($email) {
    return preg_match('/^[\w.-]+@([\w.-]+\.)+[a-z]{2,6}$/is', $email);
}
function mksecret($len = 20) {
    $ret = "";
    for ($i = 0; $i < $len; $i++)
        $ret .= chr(mt_rand(0, 255));
    return $ret;
}
function get_date_time ()
{
	return date("Y-m-d H:i:s");
}
function sqlerr($file = '', $line = '')
{
  print("<table border=0 bgcolor=blue align=left cellspacing=0 cellpadding=10 style='background: blue'>" .
    "<tr><td class=embedded><font color=white><h1>SQL Error</h1>\n" .
  "<b>" . mysql_error() . ($file != '' && $line != '' ? "<p>in $file, line $line</p>" : "") . "</b></font></td></tr></table>");
  die;
}
function sqlesc($value) {
    // Stripslashes
   if (get_magic_quotes_gpc()) {
       $value = stripslashes($value);
   }
   // Quote if not a number or a numeric string
   if (!is_numeric($value)) {
       $value = "'" . mysql_real_escape_string($value) . "'";
   }
   return $value;
}
function mkglobal($vars) {
    if (!is_array($vars))
        $vars = explode(":", $vars);
    foreach ($vars as $v) {
        if (isset($_GET[$v]))
            $GLOBALS[$v] = unesc($_GET[$v]);
        elseif (isset($_POST[$v]))
            $GLOBALS[$v] = unesc($_POST[$v]);
        else
            return 0;
    }
    return 1;
}
function unesc($x) {
    if (get_magic_quotes_gpc())
        return stripslashes($x);
    return $x;
}
function safe_email($email) { 	
	$email = str_replace("<","",$email); 
	$email = str_replace(">","",$email); 
	$email = str_replace("\'","",$email); 
	$email = str_replace('\"',"",$email); 
	$email = str_replace("\\\\","",$email); 
	return $email; 
}
function check_email ($email) {
	# Check EMail Function v.02 by xam!
	if(ereg("^([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$", $email)) 
		return true;
	else
		return false;
}
function bark($msg) {
	stdmsg("Signup Failed! (See Below)", $msg,false);
	exit;
}
function stdmsg($heading, $text, $htmlstrip = TRUE)
{
    if ($htmlstrip) {
        $heading = htmlspecialchars(trim($heading));
        $text = htmlspecialchars(trim($text));
    }
    print("<table class=main width=100% border=0 cellpadding=0 cellspacing=0><tr><td class=embedded>\n");
        if ($heading)
            print("<h2>$heading</h2>\n");
    print("<table width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text>\n");
    print($text . "</td></tr></table></td></tr></table>\n");
}
function int_check($value) {
	if ( is_array($value) ) {
        foreach ($value as $val) int_check ($val);
    } else {
	    if (!is_valid_id($value)) {
			bark("Invalid ID! For security reason, we have been logged this action.");	    }	    	
	    else
	    	return true;
    }
}
function is_valid_id($id)
{
  return is_numeric($id) && ($id > 0) && (floor($id) == $id);
}
  function dbconn()
{
    global $DATABASE;

    if (!@mysql_connect($DATABASE[mysql_host], $DATABASE[mysql_user], $DATABASE[mysql_pass]))
    {
	  switch (mysql_errno())
	  {
		case 1040:
		case 2002:
			if ($_SERVER[REQUEST_METHOD] == "GET")
				die("<html><head><meta http-equiv=refresh content=\"5 $_SERVER[REQUEST_URI]\"></head><body><table border=0 width=100% height=100%><tr><td><h3 align=center>The server load is very high at the moment. Retrying, please wait...</h3></td></tr></table></body></html>");
			else
				die("Too many users. Please press the Refresh button in your browser to retry.");
        default:
    	    die("[" . mysql_errno() . "] dbconn: mysql_connect: " . mysql_error());
      }
    }
    mysql_select_db($DATABASE[mysql_db])
        or die('dbconn: mysql_select_db: ' + mysql_error());

}
  function step ($text = '', $stepname = '', $stepnumber = '') {
	  ?><?php /*
	  <p><table border=1 cellspacing=0 cellpadding=10 bgcolor=black width=900 align=center><tr><td style='padding: 10px; background: black' class=text>
<font color=white><center><b><?=$text?></b> <div align=right>STEP: <?=$stepname?> (<?=$stepnumber?>/8)</div>
</font></center></td></tr></table></p>
<table border=1 cellspacing=0 cellpadding=10 width=900 align=center><tr><td style='padding: 10px;' class=text><div align=justify>*/
?>
<HEAD>
<META http-equiv=content-type content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" media="all" href="style.css" />
</HEAD>
<DIV id=ipbwrapper>
<DIV class=borderwrap>
<DIV class=formsubtitle><div align=right>STEP: <?=$stepname?> (<?=$stepnumber?>/5) </div><a href="http://freetosu.berlios.de"><img src="images/logo_small.png"></a><?=$text?></DIV>
<DIV class=row1>
<DIV class=postcolor 
style="PADDING-RIGHT: 4px; PADDING-LEFT: 4px; PADDING-BOTTOM: 4px; PADDING-TOP: 4px">
	  <?php
  }
  function ReadConfig ($configname) {
    if (strstr($configname, ',')) {
        $configlist = explode(',', $configname);
        foreach ($configlist as $key=>$configname) {
            ReadConfig(trim($configname));
        }
    } else {
        $configname = basename($configname);
        $path = ROOT_PATH.'config/'.$configname;
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
            if ($configname == 'XAM') {
                Header("Location: index.php");  				
                die; 
            }
            return array();
        }
        $tmp        = @unserialize($content);
        if (empty($tmp)) {
            if ($configname == 'XAM') {
                Header("Location: index.php");  				
                die;                
            }
            die("<font color=red>Cannot read file [<b>".htmlspecialchars($configname)."</b>]!.</font><br><font color=blue>Before the setup starts, please ensure that you have properly configured file and directory access permissions. Please see below.</font><br><br>chmod 777 CONFIG (config directory).<br>chmod 777 CONFIG/main (the file which save the main settings).");
        }
        $GLOBALS[$configname] = $tmp;
        return true;
    }
}

function WriteConfig ($configname, $config) {
    $configname = basename($configname);
    $path = ROOT_PATH.'config/'.$configname;
    if (!file_exists($path) || !is_writable($path)) {
@fopen($path, 'w');
    }
    $data = @serialize($config);
    if (empty($data)) {
        die("<font color=red>Cannot serialize file [<b>".htmlspecialchars($configname)."</b>]</font><br><font color=blue>Before the setup starts, please ensure that you have properly configured file and directory access permissions. Please see below.</font><br><br>chmod 777 CONFIG (config directory).<br>chmod 777 CONFIG/main (the file which save the main settings).");
    }
    $fp = @fopen ($path, 'w');
    if (!$fp) {
        die("<font color=red>Cannot open file [<b>".htmlspecialchars($configname)."</b>] to save info!.</font><br><font color=blue>Before the setup starts, please ensure that you have properly configured file and directory access permissions. Please see below.</font><br><br>chmod 777 CONFIG (config directory).<br>chmod 777 CONFIG/main (the file which save the main settings).");
    }
    $Res = @fwrite($fp, $data);
    if (empty($Res)) {
        die("<font color=red>Cannot save info in file (error in serialisation) [<b>".htmlspecialchars($configname)."</b>] to save info!.</font><br><font color=blue>Before the setup starts, please ensure that you have properly configured file and directory access permissions. Please see below.</font><br><br>chmod 777 CONFIG (config directory).<br>chmod 777 CONFIG/main (the file which save the main settings).");
    }
    fclose($fp);
    return true;
}

function GetVar ($name) {
    if ( is_array($name) ) {
        foreach ($name as $var) GetVar ($var);
    } else {
        if ( !isset($_REQUEST[$name]) )
            return false;
        if ( get_magic_quotes_gpc() ) {
            $_REQUEST[$name] = ssr($_REQUEST[$name]);
        }
        $GLOBALS[$name] = $_REQUEST[$name];
        return $GLOBALS[$name];
    }
}

function ssr ($arg) {
    if (is_array($arg)) {
        foreach ($arg as $key=>$arg_bit) {
            $arg[$key] = ssr($arg_bit);
        }
    } else {
        $arg = stripslashes($arg);
    }
    return $arg;
}
/**
 * tr()
 *
 * @param mixed $x
 * @param mixed $y
 * @param integer $noesc
 * @param string $relation
 * @param string $params1
 * @param string $params2 
 * @return
 */
function tr($x, $y, $noesc = 0, $relation = '', $params1 = '', $params2 = '')
{
    if ($noesc)
        $a = $y;
    else {
        $a = htmlspecialchars($y);
        $a = str_replace("\n", "<br />\n", $a);
    }
    print ("<tr" . ($relation ? " relation = \"$relation\"" : "") . "><td class=\"heading\" valign=\"top\" align=\"right\" $params1>$x</td><td valign=\"top\" " .
        $params2 . " align=left>$a</td></tr>\n");
}
function get_extension($file)
{
	return strtolower(substr(strrchr($file, "."), 1));
}

function dir_list($dir)
{
	$dl = array();  
	$ext = '';
	if (!file_exists($dir))
		error1();
	if ($hd = opendir($dir))
	{
		while ($sz = readdir($hd)) { 
		$ext = get_extension($sz);
		if (preg_match("/^\./",$sz) == 0 && $ext != 'php')
			$dl[] = $sz;
		}
		closedir($hd);
		asort($dl);
		return $dl;
	}else
		error1('','Couldn\'t open storage folder! Please check the path.');
}
function select ($selectname,$selectoptions = array(),$check) {
	$a = <<<ml
<SELECT NAME="$selectname">	
ml;
foreach ($selectoptions as $value => $name ) {
	$a .= "<OPTION VALUE=\"$value\" ".($check == $value ? 'SELECTED' : '' ).">$name</OPTION>";
}
$a .= "</SELECT>";
return $a;
}
?>

<link rel="stylesheet" href="css/ex.css" type="text/css">
<script src="js/dw_event.js" type="text/javascript"></script>
<script src="js/dw_viewport.js" type="text/javascript"></script>
<script src="js/dw_tooltip.js" type="text/javascript"></script>
<script src="js/dw_tooltip_aux.js" type="text/javascript"></script>
<script src="js/fts_vars.js" type="text/javascript"></script>
<?php

function makehelp($code) {
	return <<<r
	<span style="float: right;" align="justify">
<img src="pics/help.png" class="showTip $code" /></span>
r;
}
function gotostep($nr) {
	echo <<<e
<script>window.location = "install_fresh.php?action=step$nr";</script>	
e;
}
?>