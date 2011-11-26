<?php

if (!defined('IN_TRACKER'))
    die('Hacking attempt!');
require_once ($rootpath . 'include/handle.php');
require_once ($rootpath . 'include/cache_init.php');
include_once ($rootpath . 'include/class_logger.php');
include_once ($rootpath . 'include/globalfunctions.php');
include_once ($rootpath . 'include/config.php');
include_once ($rootpath . 'include/html.php');
HANDLE::freq('libs.cron', 'cron');
HANDLE::freq('libs.anticheat', 'main');
HANDLE::Freq('libs.small_tools', 'ipvalidation', '.php');
HANDLE::Freq('libs.administrator','tools');
HANDLE::Freq('libs.antiflood','floodprotect');
HANDLE::Freq('libs.tools','functions_user_details','.php');
$_logger = new Logger();
register_shutdown_function(array($_logger, 'Save'));
HANDLE::checkins();
$CURUSER = userlogin();
require_once ($rootpath . 'include/cron-main.php');
if (basename($_SERVER['SCRIPT_FILENAME']) == 'index.php' OR basename($_SERVER['SCRIPT_FILENAME']) == 'shoutbox.php')
    register_shutdown_function("autoclean");
HANDLE::freq('libs.tools', 'user_rights');
$SUBSPATH = "fts-contents/subs";

include_once ($rootpath . 'include/libs/config/recaptcha.php');
include_once ($rootpath . 'include/libs/config/misc.php');
$a = mysql_fetch_assoc(mysql_query('SELECT id FROM usergroups ORDER BY id DESC LIMIT 1'));
$maximumclass = (int)$a['id'];
$mycl = (int)get_user_class();
if($mycl > $maximumclass)
error(12,"(Maximum is $maximumclass but you have $mycl. Are you a hacker?)");
if(!get("software_database_version"))
@update("software_database_version",VERSION);
/**
 * sql_query()
 *
 * @param mixed $query
 * @return
 */
function sql_query($query)
{
    $_SESSION['queries']++;
    global $_db;
    return $_db->query($query);
}
/**
 * EmailBanned()
 *
 * @param mixed $newEmail
 * @return
 */
function EmailBanned($newEmail)
{
    $newEmail = trim(strtolower($newEmail));
    $sql = sql_query("SELECT * FROM bannedemails") or sqlerr(__file__, __line__);
    $list = mysql_fetch_array($sql);
    $addresses = explode(' ', preg_replace("/[[:space:]]+/", " ", trim($list[value])));
    if (count($addresses) > 0) {
        foreach ($addresses as $email) {
            $email = trim(strtolower(ereg_replace('\.', '\\.', $email)));
            if (strstr($email, "@")) {
                if (ereg('^@', $email)) {
                    $email = ereg_replace('^@', '[@\\.]', $email);
                    if (ereg("$email$", $newEmail))
                        return true;
                }
            } elseif (ereg('@$', $email)) {
                if (ereg("^$email", $newEmail))
                    return true;
            } else {
                if (strtolower($email) == $newEmail)
                    return true;
            }
        }
    }
    return false;
}
/**
 * getage()
 *
 * @param mixed $year
 * @param mixed $month
 * @param mixed $day
 * @return
 */
function getage($year, $month, $day)
{
    $year_diff = date("Y") - ($year);
    $month_diff = date("m") - ($month);
    $day_diff = date("d") - ($day);
    if ($month_diff < 0)
        $year_diff--;
    elseif ($month_diff == 0 && $day_diff < 0)
        $year_diff--;
    return apply_filters("getage",$year_diff);
}
/**
 * redirect()
 *
 * @param mixed $url
 * @param string $message
 * @param string $title
 * @param integer $wait
 * @param bool $usephp
 * @param bool $withbaseurl
 * @return
 */
function redirect($url, $message = '', $title = '', $wait = 3, $usephp = false,
    $withbaseurl = true)
{
    global $SITENAME, $BASEURL, $defaulttemplate, $lang;
    if (empty($message))
        $message ="redirect";
    if (empty($title))
        $title = $SITENAME;
    $url = htmlspecialchars($url);
    $url = str_replace("&amp;", "&", $url);
    $url = ($withbaseurl ? $BASEURL . '/' . $url : $url);
    if ($usephp) {
        header("Location: $url");
        exit;
    }
    ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
<title><?= $title; ?></title>
<meta http-equiv="refresh" content="<?= $wait; ?>;URL=<?= $url; ?>" />
<link rel="stylesheet" href="<?= $BASEURL; ?>/fts-contents/styles/default.css" type="text/css" media="screen" />
</head>
<body>
<br />
<br />
<br />
<br />
<div style="margin: auto auto; width: 50%" align="center">
<table border="0" cellspacing="1" cellpadding="4" class="tborder">
<tr>
<td class="thead"><strong><a href=<?= $BASEURL; ?>><?= $title; ?></a></strong></td>
</tr>
<tr>
<td class="trow1" align="center"><p><font color=red><?= $message; ?></font></p></td>
</tr>
<tr>
<td class="trow2" align="right"><a href="<?= $url; ?>">
<span class="smalltext">Don't want to wait? Click here!</span></a></td>
</tr>
</table>
</div>
</body>
</html>
<?php
    ob_end_flush();
    exit;
}
/**
 * cache_check()
 *
 * @param string $file
 * @return
 */
function cache_check($file = 'cachefile')
{
    global $rootpath, $cache;
    $cachefile = $rootpath . $cache . '/' . $file . '.html';
    $cachetime = 60 * 60;
    if (file_exists($cachefile) && (time() - $cachetime < filemtime($cachefile))) {
        include ($cachefile);
        print ("<p align=center><font class=small>This page last updated " . date('Y-m-d H:i:s',
            filemtime($cachefile)) . "</font></p>");
        end_main_frame();
        stdfoot();
        exit;
    }
    ob_start();
}
/**
 * cache_save()
 *
 * @param string $file
 * @return
 */
function cache_save($file = 'cachefile')
{
    global $rootpath, $cache;
    $cachefile = $rootpath . $cache . '/' . $file . '.html';
    $fp = fopen($cachefile, 'w');
    fwrite($fp, ob_get_contents());
    fclose($fp);
    ob_end_flush();
}
/**
 * checkbrowser()
 *
 * @return
 */
function checkbrowser()
{
    unset($browser);
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'Gecko')) {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'Netscape')) {
            $browser = true;
        } else
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox')) {
                $browser = true;
            } else {
                $browser = true;
            }
    } else
        $browser = false;
    if (!$browser)
        print ("<p class=codemain align=justify>If you enable cookies and are still unable to log in, perhaps something happened to cause a problem with your login cookie. We suggest delete your cookies and trying again. To delete cookies in Internet Explorer, go to Tools > Internet Options... and click on the Delete Cookies button. Note that this will delete all cookies stored on your system for other sites as well.</b></p>");
}
/**
 * safe_email()
 *
 * @param mixed $email
 * @return
 */
function safe_email($email)
{
    $email = str_replace("<", "", $email);
    $email = str_replace(">", "", $email);
    $email = str_replace("\'", "", $email);
    $email = str_replace('\"', "", $email);
    $email = str_replace("\\\\", "", $email);
    return apply_filters("safe_email",$email);
}
/**
 * check_email()
 *
 * @param mixed $email
 * @return
 */
function check_email($email)
{
    if (ereg("^([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$",
        $email))
        return true;
    else
        return false;
}
/**
 * sent_mail()
 *
 * @param mixed $to
 * @param mixed $fromname
 * @param mixed $fromemail
 * @param mixed $subject
 * @param mixed $body
 * @param string $type
 * @param bool $showmsg
 * @param bool $multiple
 * @param string $multiplemail
 * @return
 */
function sent_mail($to, $fromname, $fromemail, $subject, $body, $type =
    "confirmation", $showmsg = true, $multiple = false, $multiplemail = '', $html = false)
{
    global $rootpath, $SITENAME, $SITEEMAIL, $smtptype, $smtp, $smtp_host, $smtp_port,
        $smtp_from, $smtpaddress, $smtpport, $accountname, $accountpassword;
    if ($smtptype == 'default') {
    	$headers = "From: $SITEEMAIL\r\n";
    	if($html)
		$headers .= "Content-type: text/html\r\n"; 
        @mail($to, $subject, $body, $headers, "-f$SITEEMAIL") or stderr("Error",
            "Unable to send mail. Please contact an administrator about this error.");
    } elseif ($smtptype == 'advanced') {
        if (strtoupper(substr(PHP_OS, 0, 3) == 'WIN')) {
            $eol = "\r\n";
            $windows = true;
        } elseif (strtoupper(substr(PHP_OS, 0, 3) == 'MAC'))
            $eol = "\r";
        else
            $eol = "\n";
        $mid = md5(IP::getip() . $fromname);
        $name = $_SERVER["SERVER_NAME"];
        $headers .= "From: $fromname <$fromemail>" . $eol;
        $headers .= "Reply-To: $fromname <$fromemail>" . $eol;
        $headers .= "Return-Path: $fromname <$fromemail>" . $eol;
        $headers .= "Message-ID: <$mid thesystem@$name>" . $eol;
        $headers .= "X-Mailer: PHP v" . phpversion() . $eol;
        $headers .= "MIME-Version: 1.0" . $eol;
        $headers .= "X-Sender: PHP" . $eol;
        if($html)
        	$headers .= "Content-type: text/html" . $eol;
        if ($multiple)
            $headers .= "Bcc: $multiplemail.$eol";
        if ($smtp == "yes") {
            ini_set('SMTP', $smtp_host);
            ini_set('smtp_port', $smtp_port);
            if ($windows)
                ini_set('sendmail_from', $smtp_from);
        }
        @mail($to, $subject, $body, $headers) or stderr("Error",
            "Unable to send mail. Please contact an administrator about this error.");
        ini_restore(SMTP);
        ini_restore(smtp_port);
        if ($windows)
            ini_restore(sendmail_from);
    } elseif ($smtptype == 'external') {
        require ($rootpath . 'include/smtp/smtp.lib.php');
        $mail = new smtp;
        $mail->debug(false);
        $mail->open($smtpaddress, $smtpport);
        $mail->auth($accountname, $accountpassword);
        $mail->from($SITEEMAIL);
        $mail->to($to);
        $mail->subject($subject);
        $mail->body($body);
        $mail->send();
        $mail->close();
    }
    if ($showmsg) {
        if ($type == "confirmation")
            stderr("Success", "A confirmation email has been mailed to <b>" .
                htmlspecialchars($to) . "</b>.\n" .
                "Please allow a few minutes for the mail to arrive.", false);
        else
            if ($type == "details")
                stderr("Success", "The new account details have been mailed to <b>" .
                    htmlspecialchars($to) . "</b>.\n" .
                    "Please allow a few minutes for the mail to arrive.", false);
    } else
        return true;
}
include 'class_failedlogin.php';
/**
 * warn_debug()
 *
 * @return
 */
function warn_debug()
{
}
/**
 * registration_check()
 *
 * @param string $type
 * @param bool $maxuserscheck
 * @param bool $ipcheck
 * @return
 */
function registration_check($type = "invitesystem", $maxuserscheck = true, $ipcheck = true)
{
    global $invitesystem, $registration, $maxusers, $SITENAME, $maxip;
    if ($type == "invitesystem") {
        if ($invitesystem == "off") {
            stderr("Sorry", "Invite System is currently disabled.");
        }
    }
    if ($type == "normal") {
        if ($registration == "off") {
            stderr("Sorry", "Registration is currently disabled.");
        }
    }
    if ($maxuserscheck) {
        $res = mysql_query("SELECT COUNT(*) FROM users") or sqlerr(__file__, __line__);
        $arr = mysql_fetch_row($res);
        if ($arr[0] >= $maxusers)
            stderr("Sorry", "The current user account limit has been reached. Inactive accounts are pruned all the time, please check back again later...");
    }
    if ($ipcheck) {
        $ip = IP::getip();
        $a = (@mysql_fetch_row(@mysql_query("select count(*) from users where ip='" .
            mysql_real_escape_string($ip) . "'"))) or sqlerr(__file__, __line__);
        if ($a[0] >= $maxip)
            stderr("Sorry", "The IP <b>" . htmlspecialchars($ip) .
                "</b> is Already being used on one or more accounts($a[0]).... No Dupe accounts allowed at <b>$SITENAME</b>.", false);
    }
    do_action("registration_check");
}
HANDLE::Freq('libs.image_verification', 'iv', '.php');
/**
 * maxslots()
 *
 * @return
 */
function maxslots()
{
    global $CURUSER, $maxdlsystem;
    $gigs = $CURUSER["uploaded"] / (1024 * 1024 * 1024);
    $ratio = (($CURUSER["downloaded"] > 0) ? ($CURUSER["uploaded"] / $CURUSER["downloaded"]) :
        1);
    if ($ratio < 0.5 || $gigs < 5)
        $max = 1;
    elseif ($ratio < 0.65 || $gigs < 6.5)
        $max = 2;
    elseif ($ratio < 0.8 || $gigs < 8)
        $max = 3;
    elseif ($ratio < 0.95 || $gigs < 9.5)
        $max = 4;
    else
        $max = 0;
    if ($maxdlsystem == "yes") {
        if ($CURUSER["class"] < UC_VIP) {
            if ($max > 0)
                print ("<font color=#1900D1>Slots: </font><font color=white><span class=smallfont>$max</span></font>");
            else
                print ("<font color=#1900D1>Slots: </font><font color=white><span class=smallfont>Unlimited</span></font>");
        } else
            print ("<font color=#1900D1>Slots: </font><font color=white><span class=smallfont>Unlimited</span></font>");
    } else
        print ("<font color=#1900D1>Slots: </font><font color=white><span class=smallfont>Unlimited</span></font>");
}
/**
 * dbconn()
 *
 * @param bool $autoclean
 * @return
 */
function dbconn($autoclean = false)
{
}
if(!function_exists("error")) {
/**
 * error()
 *
 * @param mixed $errorid
 * @param string $exmess
 * @return
 */
function error($errorid, $exmess = '')
{
    define('errorid', $errorid);
    if (!empty($exmess))
        define('exmess', $exmess);
    global $rootpath;
    include $rootpath . 'error.php';
    die;
}
}
/**
 * userlogin()
 *
 * @return
 */
function userlogin()
{
    global $SITE_ONLINE, $iplog1, $rootpath;
    unset($GLOBALS["CURUSER"]);
    $ip = IP::getip();
    $nip = ip2long($ip);
    $res = mysql_query("SELECT * FROM bans WHERE $nip >= first AND $nip <= last") or
        sqlerr(__file__, __line__);
    if (mysql_num_rows($res) > 0) {
        error(9);
    }
    global $_pg_enable;
    if($_pg_enable == 'yes'):
	$respg = mysql_query("SELECT * FROM peerguardian WHERE $nip >= first AND $nip <= last") or
        sqlerr(__file__, __line__);
    if (mysql_num_rows($respg) > 0) {
    	$peerg = mysql_fetch_assoc($respg);
    	
    	$text = "We come to belive that you are an Anti-P2P organization($peerg[comment]), so we banned your ip class.";
        error(13,$text);
    }
    endif;
    if (empty($_COOKIE["c_secure_pass"]) || empty($_COOKIE["c_secure_uid"]) || empty
        ($_COOKIE["c_secure_login"]))
        return;
    if ($_COOKIE["c_secure_login"] == base64("yeah"))
        if (empty($_SESSION["s_secure_uid"]) || empty($_SESSION["s_secure_pass"]))
            return;
    $b_id = base64($_COOKIE["c_secure_uid"], false);
    $id = 0 + $b_id;
    if (!$id || !is_valid_id($id) || strlen($_COOKIE["c_secure_pass"]) != 32)
        return;
    if ($_COOKIE["c_secure_login"] == base64("yeah"))
        if (strlen($_SESSION["s_secure_pass"]) != 32)
            return;
    $res = mysql_query("SELECT users.*, NOW() as ctime FROM users WHERE id = " .
        mysql_real_escape_string($id) .
        " AND enabled='yes' AND status = 'confirmed' LIMIT 1");
    $row = mysql_fetch_array($res);
    if (!$row)
        return;
    $sec = hash_pad($row["secret"]);
    global $sechash;
    if ($_COOKIE["c_secure_pass"] !== md5($row["passhash"] . $sechash))
        return;
    if ($_COOKIE["c_secure_login"] == base64("yeah"))
        if ($_SESSION["s_secure_pass"] !== md5($row["passhash"] . $sechash))
            return;
    if (!$row["passkey"]) {
        $passkey = md5($row['username'] . get_date_time() . $row['passhash']);
        mysql_query("UPDATE users SET passkey = " . sqlesc($passkey) . " WHERE id=" .
            mysql_real_escape_string($row["id"]));
    }
    if ($iplog1 == "yes") {
        if (($ip != $row["ip"]) && $row["ip"])
            mysql_query("INSERT INTO iplog (ip, userid, access) VALUES (" . sqlesc($row["ip"]) .
                ", " . $row["id"] . ", '" . $row["last_access"] . "')");
    }
    mysql_query("UPDATE users SET last_access='" . get_date_time() . "', ip=" .
        sqlesc($ip) . " WHERE id=" . mysql_real_escape_string($row["id"]));
    $row['ip'] = $ip;
    $usergroup = $row['class'];
    $get_group_data = sql_query('SELECT * FROM usergroups WHERE id = ' . sqlesc($usergroup));
    $group_data_results = mysql_fetch_array($get_group_data);
    $GLOBALS["usergroups"] = $group_data_results;
    $_defaults_ = array(
	"canpostintopics" => 'yes',
	);
    $add_args = parse_args(unserialize($GLOBALS["usergroups"]['args']));
   $GLOBALS["usergroups"] += $add_args;
    return $row;
}
/**
 * autoclean()
 *
 * @return
 */
function autoclean()
{
    global $autoclean_interval, $rootpath;
    $now = time();
    $docleanup = 0;
    $res = mysql_query("SELECT value_u FROM avps WHERE arg = 'lastcleantime'");
    $row = mysql_fetch_array($res);
    if (!$row) {
        mysql_query("INSERT INTO avps (arg, value_u) VALUES ('lastcleantime',$now)");
        return;
    }
    $ts = $row[0];
    if ($ts + $autoclean_interval > $now)
        return;
    mysql_query("UPDATE avps SET value_u=$now WHERE arg='lastcleantime' AND value_u = $ts");
    if (!mysql_affected_rows())
        return;
    if (!update('lastcron', $now)) {
        add('lastcron', $now);
    }
    docleanup();
}
/**
 * unesc()
 *
 * @param mixed $x
 * @return
 */
function unesc($x)
{
    if (get_magic_quotes_gpc())
        return stripslashes($x);
    return $x;
}
/**
 * mksize()
 *
 * @param mixed $bytes
 * @return
 */
function mksize($bytes)
{
    if ($bytes < 1000 * 1024)
        return number_format($bytes / 1024, 2) . " KB";
    elseif ($bytes < 1000 * 1048576)
        return number_format($bytes / 1048576, 2) . " MB";
    elseif ($bytes < 1000 * 1073741824)
        return number_format($bytes / 1073741824, 2) . " GB";
    else
        return number_format($bytes / 1099511627776, 2) . " TB";
}
/**
 * mksizeint()
 *
 * @param mixed $bytes
 * @return
 */
function mksizeint($bytes)
{
    $bytes = max(0, $bytes);
    if ($bytes < 1000)
        return floor($bytes) . " B";
    elseif ($bytes < 1000 * 1024)
        return floor($bytes / 1024) . " kB";
    elseif ($bytes < 1000 * 1048576)
        return floor($bytes / 1048576) . " MB";
    elseif ($bytes < 1000 * 1073741824)
        return floor($bytes / 1073741824) . " GB";
    else
        return floor($bytes / 1099511627776) . " TB";
}
/**
 * deadtime()
 *
 * @return
 */
function deadtime()
{
    global $announce_interval;
    return time() - floor($announce_interval * 1.3);
}
/**
 * mkprettytime()
 *
 * @param mixed $s
 * @return
 */
function mkprettytime($s)
{
    if ($s < 0)
        $s = 0;
    $t = array();
    foreach (array("60:sec", "60:min", "24:hour", "0:day") as $x) {
        $y = explode(":", $x);
        if ($y[0] > 1) {
            $v = $s % $y[0];
            $s = floor($s / $y[0]);
        } else
            $v = $s;
        $t[$y[1]] = $v;
    }
    if ($t["day"])
        return $t["day"] . "d " . sprintf("%02d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
    if ($t["hour"])
        return sprintf("%d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
    return sprintf("%d:%02d", $t["min"], $t["sec"]);
}
/**
 * mkglobal()
 *
 * @param mixed $vars
 * @return
 */
function mkglobal($vars)
{
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
/**
 * validfilename()
 *
 * @param mixed $name
 * @return
 */
function validfilename($name)
{
    return preg_match('/^[^\0-\x1f:\\\\\/?*\xff#<>|]+$/si', $name);
}
/**
 * validemail()
 *
 * @param mixed $email
 * @return
 */
function validemail($email)
{
    return preg_match('/^[\w.-]+@([\w.-]+\.)+[a-z]{2,6}$/is', $email);
}
/**
 * sqlesc()
 *
 * @param mixed $value
 * @return
 */
function sqlesc($value)
{
    if (get_magic_quotes_gpc()) {
        $value = stripslashes($value);
    }
    if (!is_numeric($value)) {
        $value = "'" . mysql_real_escape_string($value) . "'";
    }
    return apply_filters("sqlesc",$value);
}
/**
 * sqlwildcardesc()
 *
 * @param mixed $x
 * @return
 */
function sqlwildcardesc($x)
{
    return str_replace(array("%", "_"), array("\\%", "\\_"),
        mysql_real_escape_string($x));
}
/**
 * urlparse()
 *
 * @param mixed $m
 * @return
 */
function urlparse($m)
{
    $t = $m[0];
    if (preg_match(',^\w+://,', $t))
        return "<a href=\"$t\">$t</a>";
    return "<a href=\"http://$t\">$t</a>";
}
/**
 * parsedescr()
 *
 * @param mixed $d
 * @param mixed $html
 * @return
 */
function parsedescr($d, $html)
{
    if (!$html) {
        $d = htmlspecialchars($d);
        $d = str_replace("\n", "\n<br>", $d);
    }
    return apply_filters("parsedescr","$d");
}
/**
 * where()
 *
 * @param string $scriptname
 * @param mixed $userid
 * @return
 */
function where($scriptname = "index", $userid)
{
    global $where;
    if ($where == "yes") {
        if (!is_valid_id($userid))
            die;
            $wheref = "$scriptname";
        $query = sprintf("UPDATE users SET page=" . sqlesc($wheref) . " WHERE id ='%s'",
            mysql_real_escape_string($userid));
        $result = mysql_query($query);
        if (!$result)
            sqlerr(__file__, __line__);
        else
            return $wheref;
    }
    return;
}
/**
 * menu()
 *
 * @param string $selected
 * @return
 */
function menu($selected = "home")
{
    global $BASEURL;
    $script_name = $_SERVER["SCRIPT_FILENAME"];
    if (preg_match("/index/i", $script_name)) {
        $selected = "home";
    } elseif (preg_match("/browse/i", $script_name)) {
        $selected = "browse";
    } elseif (preg_match("/viewrequests/i", $script_name) or preg_match("/viewoffers/i",
    $script_name) or preg_match("/offcomment/i", $script_name) or preg_match("/reqcomment/i",
        $script_name)) {
        $selected = "requests";
    } elseif (preg_match("/upload/i", $script_name)) {
        $selected = "upload";
    } elseif (preg_match("/usercp/i", $script_name)) {
        $selected = "usercp";
    } elseif (preg_match("/forums/i", $script_name)) {
        $selected = "forums";
    } elseif (preg_match("/topten/i", $script_name)) {
        $selected = "topten";
    } elseif (preg_match("/rules/i", $script_name)) {
        $selected = "rules";
    } elseif (preg_match("/faq/i", $script_name)) {
        $selected = "faq";
    } elseif (preg_match("/links/i", $script_name)) {
        $selected = "links";
    } elseif (preg_match("/staff/i", $script_name)) {
        $selected = "staff";
    } else
        $selected = "";
    print ("<div class=\"shadetabs\"><ul>");
    print ("<li" . ($selected == "home" ? " class=selected" : "") . "><a href=\"$BASEURL/index.php\" onMouseout=\"window.status=''; return true;\" onMouseOver=\"window.status='User CP Home'; return true;\">Home</a></li>");
    print ("<li" . ($selected == "browse" ? " class=selected" : "") . "><a href=\"$BASEURL/browse.php\" onMouseout=\"window.status=''; return true;\" onMouseOver=\"window.status='Browse Torrents'; return true;\">Browse</a></li>");
    print ("<li" . ($selected == "requests" ? " class=selected" : "") . "><a href=\"$BASEURL/viewrequests.php\" onMouseout=\"window.status=''; return true;\" onMouseOver=\"window.status='Search Torrents'; return true;\">Requests</a></li>");
    print ("<li" . ($selected == "upload" ? " class=selected" : "") . "><a href=\"$BASEURL/upload.php\" onMouseout=\"window.status=''; return true;\" onMouseOver=\"window.status='Upload Torrents'; return true;\">Upload</a></li>");
    print ("<li" . ($selected == "usercp" ? " class=selected" : "") . "><a href=\"$BASEURL/usercp.php\" onMouseout=\"window.status=''; return true;\" onMouseOver=\"window.status='User CP'; return true;\">User CP</a></li>");
    print ("<li" . ($selected == "forums" ? " class=selected" : "") . "><a href=\"$BASEURL/forums/\" onMouseout=\"window.status=''; return true;\" onMouseOver=\"window.status='Forums'; return true;\">Forums</a></li>");
    print ("<li" . ($selected == "topten" ? " class=selected" : "") . "><a href=\"$BASEURL/topten.php\" onMouseout=\"window.status=''; return true;\" onMouseOver=\"window.status='TOP 10'; return true;\">Top 10</a></li>");
    print ("<li" . ($selected == "rules" ? " class=selected" : "") . "><a href=\"$BASEURL/rules.php\" onMouseout=\"window.status=''; return true;\" onMouseOver=\"window.status='rules'; return true;\">Rules</a></li>");
    print ("<li" . ($selected == "faq" ? " class=selected" : "") . "><a href=\"$BASEURL/faq.php\" onMouseout=\"window.status=''; return true;\" onMouseOver=\"window.status='FAQ'; return true;\">FAQ</a></li>");
    print ("<li" . ($selected == "links" ? " class=selected" : "") . "><a href=\"$BASEURL/links.php\" onMouseout=\"window.status=''; return true;\" onMouseOver=\"window.status='Links'; return true;\">Links</a></li>");
    print ("<li" . ($selected == "staff" ? " class=selected" : "") . "><a href=\"$BASEURL/staff.php\" onMouseout=\"window.status=''; return true;\" onMouseOver=\"window.status='Staff'; return true;\">Staff</a></li>");
}
/**
 * ug()
 *
 * @param mixed $add
 * @return
 */
function ug($add = array())
{
    stderr('Error', apply_filters("ug","You do not have permission to access this page. This could be because of one of the following reasons:<BR>    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1.  Your account has either been suspended or you have been banned from accessing this resource.<BR>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. You do not have permission to access this page. Are you trying to access administrative pages or a resource that you shouldn\'t be? Check in the tracker rules that you are allowed to perform this action.<BR>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3. Your account may still be awaiting activation or moderation.<BR>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4. Feel free to contact us about this error message.
"), false,true,true,false);
die;
}
/**
 * fts_register_menu()
 *
 * @param mixed $id
 * @param mixed $c
 * @return
 */
function fts_register_menu($id, $c)
{
    if (is_array($c)) {
        echo "<script>linkset[$id]=''\n";
        foreach ($c as $content) {
            echo "linkset[$id]+='$content'\n";
        }
        echo "</script>";
    }
}
/**
 * fts_show_menu()
 *
 * @param mixed $id
 * @param mixed $text
 * @return
 */
function fts_show_menu($id, $text)
{
    return "<a href=\"#\" onClick=\"return false;\" onMouseover=\"showmenu(event,linkset[$id], '180px')\" onMouseout=\"delayhidemenu()\" style=\"text-decoration:none;\">$text</a>";
}
/**
 * ftsmenu()
 *
 * @return
 */
function ftsmenu()
{
?>
	<script>
var defaultMenuWidth="150px" //set default menu width.

var linkset=new Array()


////No need to edit beyond here

var ie5=document.all && !window.opera
var ns6=document.getElementById



function iecompattest(){
return (document.compatMode && document.compatMode.indexOf("CSS")!=-1)? document.documentElement : document.body
}

function showmenu(e, which, optWidth){
if (!document.all&&!document.getElementById)
return
clearhidemenu()
menuobj=ie5? document.all.popitmenu : document.getElementById("popitmenu")
menuobj.innerHTML=which
menuobj.style.width=(typeof optWidth!="undefined")? optWidth : defaultMenuWidth
menuobj.contentwidth=menuobj.offsetWidth
menuobj.contentheight=menuobj.offsetHeight
eventX=ie5? event.clientX : e.clientX
eventY=ie5? event.clientY : e.clientY
//Find out how close the mouse is to the corner of the window
var rightedge=ie5? iecompattest().clientWidth-eventX : window.innerWidth-eventX
var bottomedge=ie5? iecompattest().clientHeight-eventY : window.innerHeight-eventY
//if the horizontal distance isn't enough to accomodate the width of the context menu
if (rightedge<menuobj.contentwidth)
//move the horizontal position of the menu to the left by it's width
menuobj.style.left=ie5? iecompattest().scrollLeft+eventX-menuobj.contentwidth+"px" : window.pageXOffset+eventX-menuobj.contentwidth+"px"
else
//position the horizontal position of the menu where the mouse was clicked
menuobj.style.left=ie5? iecompattest().scrollLeft+eventX+"px" : window.pageXOffset+eventX+"px"
//same concept with the vertical position
if (bottomedge<menuobj.contentheight)
menuobj.style.top=ie5? iecompattest().scrollTop+eventY-menuobj.contentheight+"px" : window.pageYOffset+eventY-menuobj.contentheight+"px"
else
menuobj.style.top=ie5? iecompattest().scrollTop+event.clientY+"px" : window.pageYOffset+eventY+"px"
menuobj.style.visibility="visible"
return false
}

function contains_ns6(a, b) {
//Determines if 1 element in contained in another- by Brainjar.com
while (b.parentNode)
if ((b = b.parentNode) == a)
return true;
return false;
}

function hidemenu(){
if (window.menuobj)
menuobj.style.visibility="hidden"
}

function dynamichide(e){
if (ie5&&!menuobj.contains(e.toElement))
hidemenu()
else if (ns6&&e.currentTarget!= e.relatedTarget&& !contains_ns6(e.currentTarget, e.relatedTarget))
hidemenu()
}

function delayhidemenu(){
delayhide=setTimeout("hidemenu()",500)
}

function clearhidemenu(){
if (window.delayhide)
clearTimeout(delayhide)
}
</script>
<style type="text/css">

#popitmenu{
position: absolute;
background-color: white;
border:1px solid black;
font: normal 12px Verdana;
line-height: 18px;
z-index: 100;
visibility: hidden;
}

#popitmenu a{
text-decoration: none;
padding-left: 6px;
color: black;
display: block;
}

#popitmenu a:hover{ /*hover background color*/
background-color: #CCFF9D;
}

</style>
<?php
}
/**
 * ftsmenu2()
 *
 * @param string $width
 * @return
 */
function ftsmenu2($width = "150px")
{
?>
		    <script type="text/javascript" src="clientside/dropdown.js"></script>

<style type="text/css">

.sample_attach
{
  width: <?= $width; ?>;
  border: 1px solid black;
  background: #FFFFEE;
  padding: 0px 5px;
  font-weight: 900;
  color: #008000;
}

a.sample_attach
{
  display: block;
  border-bottom: none;
  text-decoration: none;
}

form.sample_attach
{
  position: absolute;
  visibility: hidden;
  border: 1px solid black;
  background: #FFFFEE;
  padding: 0px 5px 2px 5px;
}

</style>
<?php
}
/**
 * scripts()
 *
 * @return
 */
function scripts()
{
    global $CURUSER, $SITE_ONLINE, $FUNDS, $SITENAME, $SITEEMAIL, $BASEURL, $offlinemsg,
        $disablerightclick, $showversion, $autorefreshtime, $autorefresh, $leftmenu, $leftmenunl, $rootpath;
do_action("scripts");
javascript('collapse');
javascript('global');
JsB::insertjq(1);
JsB::growl();
if ($leftmenu == "yes" AND (!$CURUSER AND $leftmenunl != 'yes')) {
javascript('ssm'); 
javascript('ssmitems');
}
javascript('java_klappe');
javascript('ncode_imageresizer');  
javascript('resizevars');
if ($disablerightclick == "yes") {
javascript('rightclick');
}
}
/**
 * ads()
 *
 * @return
 */
function ads()
{
	global $DISABLE_ADS;
	if(isset($DISABLE_ADS) AND $DISABLE_ADS)
	return;
	global $BASEURL;
    define('rpt', './../');
    if (@dbv('ads')) {
        $ads = @dbv('ads');
        if (!empty($ads) AND $ads != "---") {
            print ('<table class="main" border="1" cellspacing="0" cellpadding="0" width="100%"><tr><td class="text"><div align="center">');
            echo $ads;
            print ('</table><BR>');
        }
    }
    do_action("ads");
}
/**
 * makevars()
 *
 * @return
 */
function makevars()
{
    global $BASEURL,$SITENAME,$CURUSER,$imageresizermode;
    if($CURUSER)
    $cu = 'true';
    else
    $cu = 'false';
    $lang_js_resizer1 = lang_js_resizer1;
	$lang_js_resizer2 = lang_js_resizer2;
	$lang_js_resizer3 = lang_js_resizer3;
    return apply_filters("makevars","<script type=\"text/javascript\">
var BASEURL = \"$BASEURL\";
var SITENAME = \"$SITENAME\";
var CURUSER = \"$cu\";
var CURUSERITEMS = new Array('$CURUSER[invites]','$CURUSER[seedbonus]','$CURUSER[id]');
var lang = new Array('$lang_js_resizer1','$lang_js_resizer2','$lang_js_resizer3');
var resizemode = \"$imageresizermode\";
</script>\n");
}
/**
 * stdhead()
 *
 * @param string $title
 * @param bool $msgalert
 * @param string $script
 * @return
 */
function stdhead($title = "", $msgalert = true, $script = "")
{
	do_action("stdhead_start");
    global $rootpath;
    include $rootpath . 'include/class_template.php';
    global $CURUSER, $SITE_ONLINE, $FUNDS, $SITENAME, $SITEEMAIL, $BASEURL, $offlinemsg,
        $disablerightclick, $showversion, $autorefreshtime, $autorefresh, $leftmenu, $template,
        $usergroups, $rootpath;
    if ($usergroups['isbanned'] == 'yes') {
        error(10);
    }
    if ($SITE_ONLINE == "no") {
        if (get_user_class() < UC_ADMINISTRATOR) {
            die("Site is down for maintenance, please check back again later... thanks<br>");
        } else {
            $offlinemsg = true;
        }
    }
    header("Content-Type: text/html; charset=iso-8859-1");
    if ($title == "")
        $title = $SITENAME . $showversion;
    else
        $title = "$SITENAME :: " . htmlspecialchars($title) . "$showversion";
    if ($msgalert && $CURUSER) {
        $res = mysql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] .
            " && unread='yes'") or die("OopppsY!");
        $arr = mysql_fetch_row($res);
        $unread = $arr[0];
    }
    if ($CURUSER) {
        $datum = getdate();
        $datum["hours"] = sprintf("%02.0f", $datum["hours"]);
        $datum["minutes"] = sprintf("%02.0f", $datum["minutes"]);
        $uped = mksize($CURUSER['uploaded']);
        $downed = mksize($CURUSER['downloaded']);
        if ($CURUSER["downloaded"] > 0) {
            $ratio = $CURUSER['uploaded'] / $CURUSER['downloaded'];
            $ratio = number_format($ratio, 3);
            $ratio = "$ratio";
        } else
            if ($CURUSER["uploaded"] > 0)
                $ratio = "Inf.";
            else
                $ratio = "---";
        if ($CURUSER['donor'] == "yes")
            $medaldon = "<img src=pic/star.png alt=donor title=donor>";
        if ($CURUSER['warned'] == "yes")
            $warn = "<img src=pic/warning.png alt=warned title=warned>";
        $res1 = mysql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] .
            " AND location<>0") or print (mysql_error());
        $arr1 = mysql_fetch_row($res1);
        $messages = $arr1[0];
        $res1 = mysql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] .
            " AND location=1 AND unread='yes'") or print (mysql_error());
        $arr1 = mysql_fetch_row($res1);
        $unread = $arr1[0];
        $res1 = mysql_query("SELECT COUNT(*) FROM messages WHERE sender=" . $CURUSER["id"] .
            " AND saved='yes'") or print (mysql_error());
        $arr1 = mysql_fetch_row($res1);
        $outmessages = $arr1[0];
        $res1 = mysql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] .
            " && unread='yes'") or die("OopppsY!");
        $arr1 = mysql_fetch_row($res1);
        $unread = $arr1[0];
        if ($unread)
            $inboxpic = "<img style=border:none alt=inbox title='inbox (new messages)' src=$BASEURL/pic/mail_open.png>";
        else
            $inboxpic = "<img style=border:none alt=inbox title='inbox (no new messages)' src=$BASEURL/pic/mail.png>";
        $res2 = mysql_query("SELECT COUNT(*) FROM peers WHERE userid=" . $CURUSER["id"] .
            " AND seeder='yes'") or print (mysql_error());
        $row = mysql_fetch_row($res2);
        $activeseed = $row[0];
        $res2 = mysql_query("SELECT COUNT(*) FROM peers WHERE userid=" . $CURUSER["id"] .
            " AND seeder='no'") or print (mysql_error());
        $row = mysql_fetch_row($res2);
        $activeleech = $row[0];
        $res3 = mysql_query("SELECT connectable FROM peers WHERE userid=" . sqlesc($CURUSER["id"]) .
            " LIMIT 1") or print (mysql_error());
        if ($row = mysql_fetch_row($res3)) {
            $connect = $row[0];
            if ($connect == "yes") {
                $connectable = "<b><font color=green><a title='Connectable = Yes'>Yes</a></font></b>";
            } else {
                $connectable = "<b><font color=red><a title='Connectable = No'>No</a></font></b>";
            }
        } else {
            $connectable = "waiting...";
        }
    }
    global $rootpath, $defaulttemplate;
    $template = !empty($CURUSER['skin']) ? $CURUSER['skin'] : $defaulttemplate;
    if (file_exists($rootpath . 'fts-contents/templates/' . $template . '/head.php'))
        include $rootpath . 'fts-contents/templates/' . $template . '/head.php';
    elseif (file_exists($rootpath . 'fts-contents/templates/' . $template .
        '/header.php'))
        include $rootpath . 'fts-contents/templates/' . $template . '/header.php';
do_action("stdhead_end");
}
/**
 * cpfooter()
 *
 * @return
 */
function cpfooter()
{
    $referring_url = $_SERVER['HTTP_REFERER'];
    print ("<table class=bottom width=100% border=0 cellspacing=0 cellpadding=0><tr valign=top>\n");
    print ("<td class=bottom align=center><p><br><a href=$referring_url>Return to whence you came</a></td>\n");
    print ("</tr></table>\n");
}
/**
 * copyright()
 *
 * @param mixed $get_it
 * @return
 */
function copyright($get_it = none)
{
    static $found = false;
    if ($get_it === true)
        return $found;
    if ($get_it == 'none') {
        $found = true;
    }
    $yearpast = date(Y) - 1;
    $yearnow = date(Y);
    global $forumc;
    if ($forumc == 'show') {
        global $rootpath;
        include $rootpath . 'forums/functions/ver.php';
        echo "Powered by FF ".FFver."| FF &copy; $yearpast-$yearnow, Free Forums LLC  ";
    } else
        echo "Powered by FTS ".VERSION."| FTS &copy; $yearpast-$yearnow, Free Torrent Source LLC  ";
        do_action("copyright");
}
/**
 * stdfoot()
 *
 * @return
 */
function stdfoot()
{
	do_action("stdfoot_start");
    global $SITENAME, $BASEURL, $template;
    global $rootpath;
    $mtime = microtime();
    $mtime = explode(" ", $mtime);
    $mtime = $mtime[1] + $mtime[0];
    $tstart = $mtime;
    $mtime = microtime();
    $mtime = explode(" ", $mtime);
    $mtime = $mtime[1] + $mtime[0];
    $tend = $mtime;
    $totaltime = ($tend - $tstart);
    if (file_exists($rootpath . 'fts-contents/templates/' . $template . '/foot.php'))
        include $rootpath . 'fts-contents/templates/' . $template . '/foot.php';
    elseif (file_exists($rootpath . 'fts-contents/templates/' . $template .
        '/footer.php'))
        include $rootpath . 'fts-contents/templates/' . $template . '/footer.php';
        $check = true;
        global $FTSpass;
        if($check && !$FTSpass['copy']):
    if (!copyright(true)) {
        echo '
            <div style="text-align: center !important; display: block !important; visibility: visible !important; font-size: large !important; font-weight: bold; color: black !important; background-color: white !important;">
                Sorry, the copyright must be in the template.<br />
                Please notify this site\'s administrator that this site is missing the copyright message for <a href="http://sourceforge.net/projects/freetosu" style="color: black !important; font-size: large !important;">FTS</a> so they can rectify the situation. Display of copyright is a legal requirement.
            </div>';
    }
    endif;
    do_action("stdfoot_end");
}
/**
 * genbark()
 *
 * @param mixed $x
 * @param mixed $y
 * @return
 */
function genbark($x, $y)
{
    stdhead($y);
    print ("<h2>" . htmlspecialchars($y) . "</h2>\n");
    print ("<p>" . htmlspecialchars($x) . "</p>\n");
    stdfoot();
    exit();
}
/**
 * javaredirect()
 *
 * @param mixed $where
 * @param bool $echo
 * @return
 */
function javaredirect($where,$echo = true) {
	if(!$echo) {
		return <<<e
<script>
window.location = '$where';	
</script>	
e;
	}
	else {
	echo	<<<e
<script>
window.location = '$where';	
</script>	
e;
	}
}
/**
 * doredir()
 *
 * @param mixed $where
 * @param bool $echo
 * @return void
 */
function doredir($where,$echo = true) {
	if(headers_sent()) {
		javaredirect($where,$echo);
	}
	else
	header('location:'.$where);
}
/**
 * mksecret()
 *
 * @param integer $len
 * @return
 */
function mksecret($len = 20)
{
    $ret = "";
    for ($i = 0; $i < $len; $i++)
        $ret .= chr(mt_rand(0, 255));
    return apply_filters("mksecret",$ret);
}
/**
 * httperr()
 *
 * @param integer $code
 * @return
 */
function httperr($code = 404)
{
    header("HTTP/1.0 404 Not found");
    print ("<h1>Not Found</h1>\n");
    print ("<p>Sorry pal :(</p>\n");
    exit();
}
/**
 * gmtime()
 *
 * @return
 */
function gmtime()
{
    return strtotime(get_date_time());
}
/**
 * sessioncookie()
 *
 * @param mixed $id
 * @param mixed $passhash
 * @param bool $expires
 * @return
 */
function sessioncookie($id, $passhash, $expires = false)
{
    if ($expires)
        $GLOBALS[$sessioncacheexpire] = true;
    $_SESSION['s_secure_uid'] = base64($id);
    $_SESSION['s_secure_pass'] = $passhash;
    return apply_filters("sessioncookie",$sessioncacheexpire);
}
/**
 * logincookie()
 *
 * @param mixed $id
 * @param mixed $passhash
 * @param integer $updatedb
 * @param integer $expires
 * @param bool $securelogin
 * @return
 */
function logincookie($id, $passhash, $updatedb = 1, $expires = 0x7fffffff, $securelogin = true)
{
    if ($expires != 0x7fffffff)
        $expires = time() + 900;
    setcookie("c_secure_uid", base64($id), $expires, "/");
    setcookie("c_secure_pass", $passhash, $expires, "/");
    if ($securelogin)
        setcookie("c_secure_login", base64("yeah"), $expires, "/");
    else
        setcookie("c_secure_login", base64("nope"), $expires, "/");
    if ($updatedb)
        mysql_query("UPDATE users SET last_login = NOW() WHERE id = " .
            mysql_real_escape_string($id));
}
/**
 * logoutsession()
 *
 * @return
 */
function logoutsession()
{
    session_unset();
    session_destroy();
}
/**
 * logoutcookie()
 *
 * @return
 */
function logoutcookie()
{
    setcookie("c_secure_uid", "", 0x7fffffff, "/");
    setcookie("c_secure_pass", "", 0x7fffffff, "/");
    setcookie("c_secure_login", "", 0x7fffffff, "/");
    setcookie("staffpanel", "", 0x7fffffff, "/admin/");
    unset($_COOKIE['staffpanel']);
    setcookie("settingspanel", "", 0x7fffffff, "/administrator/");
    unset($_COOKIE['settingspanel']);
}  
/**
 * base64()
 *
 * @param mixed $string
 * @param bool $encode
 * @return
 */
function base64($string, $encode = true)
{
    if ($encode)
        return base64_encode($string);
    else
        return base64_decode($string);
}
/**
 * loggedinorreturn()
 *
 * @param bool $mainpage
 * @return
 */
function loggedinorreturn($mainpage = false)
{
    global $CURUSER, $BASEURL;
    if (!$CURUSER) {
        if ($mainpage)
            header("Location: $BASEURL/login.php");
        else {
            $to = $_SERVER["REQUEST_URI"];
            header("Location: $BASEURL/login.php?returnto=" . urlencode($to));
        }
        exit();
    }
}
/**
 * deletetorrent()
 *
 * @param mixed $id
 * @return
 */
function deletetorrent($id)
{
    global $torrent_dir;
    mysql_query("DELETE FROM torrents WHERE id = " . mysql_real_escape_string($id));
    mysql_query("DELETE FROM snatched WHERE torrentid = " . mysql_real_escape_string
        ($id));
    foreach (explode(".", "peers.files.comments.ratings") as $x)
        mysql_query("DELETE FROM $x WHERE torrent = " . mysql_real_escape_string($id));
    unlink("$torrent_dir/$id.torrent");
    do_action("deletetorrent");
}
/**
 * pager()
 *
 * @param mixed $rpp
 * @param mixed $count
 * @param mixed $href
 * @param mixed $opts
 * @return
 */
function pager($rpp, $count, $href, $opts = array())
{
    $pages = ceil($count / $rpp);
    if (!$opts["lastpagedefault"])
        $pagedefault = 0;
    else {
        $pagedefault = floor(($count - 1) / $rpp);
        if ($pagedefault < 0)
            $pagedefault = 0;
    }
    if (isset($_GET["page"])) {
        $page = 0 + $_GET["page"];
        if ($page < 0)
            $page = $pagedefault;
    } else
        $page = $pagedefault;
    $pager_next = "";
    $pager_prev = "";
    $mp = $pages - 1;
    if ($page >= 1) {
        $pager_prev .= "<li><a href=\"{$href}page=" . ($page - 1) . "\">&lt;&lt;</a></li>\n";
    } else
        $pager_prev .= "<li><a name=\"disabled\" class=\"disabled\">&lt;&lt;</a></li>\n";
    if ($page < $mp && $mp >= 0) {
        $pager_next .= "<li><a href=\"{$href}page=" . ($page + 1) . "\">&gt;&gt;</a></li>\n";
    } else
        $pager_next .= "<li><a name=\"disabled\" class=\"disabled\">&gt;&gt;</a></li>\n";
    $pager = "";
    $mp = $pages - 1;
    $as = "<b>&lt;&lt;&nbsp;Prev</b>";
    if ($page >= 1) {
        $pager .= "<a href=\"{$href}page=" . ($page - 1) . "\">";
        $pager .= $as;
        $pager .= "</a>";
    } else
        $pager .= $as;
    $pager .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    $as = "<b>Next&nbsp;&gt;&gt;</b>";
    if ($page < $mp && $mp >= 0) {
        $pager .= "<a href=\"{$href}page=" . ($page + 1) . "\">";
        $pager .= $as;
        $pager .= "</a>";
    } else
        $pager .= $as;
    if ($count) {
        $pagerarr = array();
        $dotted = 0;
        $dotspace = 2;
        $dotend = $pages - $dotspace;
        $curdotend = $page - $dotspace;
        $curdotstart = $page + $dotspace;
        for ($i = 0; $i < $pages; $i++) {
            if (($i >= $dotspace && $i <= $curdotend) || ($i >= $curdotstart && $i < $dotend)) {
                if (!$dotted)
                    $pagerarr[] = "<li><b><font color=red size=2>...</b></font></li>\n";
                $dotted = 1;
                continue;
            }
            $dotted = 0;
            $start = $i * $rpp + 1;
            $end = $start + $rpp - 1;
            if ($end > $count)
                $end = $count;
            $text = "$start&nbsp;-&nbsp;$end";
            if ($i != $page)
                $pagerarr[] = "<li><a href=\"{$href}page=$i\">$text</a></li>\n";
            else
                $pagerarr[] = "<li><a name=\"current\" class=\"current\">$text</a></li>\n";
        }
        $pagerstr = join("", $pagerarr);
        $pagertop = "<table width=100% class=none><tr class=none><td class=none><div id=navcontainer2><ul>$pager_prev $pagerstr $pager_next</ul></div></tr></td></table>\n";
        $pagerbottom = "<table width=100% class=none><tr class=none><td class=none><div id=navcontainer2><ul>$pager_prev $pagerstr $pager_next</ul></div></tr></td></table>\n";
    } else {
        $pagertop = "<p align=\"center\">$pager</p>\n";
        $pagerbottom = $pagertop;
    }
    $start = $page * $rpp;
    return array($pagertop, $pagerbottom, "LIMIT $start,$rpp");
}
/**
 * downloaderdata()
 *
 * @param mixed $res
 * @return
 */
function downloaderdata($res)
{
    $rows = array();
    $ids = array();
    $peerdata = array();
    while ($row = mysql_fetch_assoc($res)) {
        $rows[] = $row;
        $id = $row["id"];
        $ids[] = $id;
        $peerdata[$id] = array(downloaders => 0, seeders => 0, comments => 0);
    }
    if (count($ids)) {
        $allids = implode(",", $ids);
        $res = mysql_query("SELECT COUNT(*) AS c, torrent, seeder FROM peers WHERE torrent IN ($allids) GROUP BY torrent, seeder");
        while ($row = mysql_fetch_assoc($res)) {
            if ($row["seeder"] == "yes")
                $key = "seeders";
            else
                $key = "downloaders";
            $peerdata[$row["torrent"]][$key] = $row["c"];
        }
        $res = mysql_query("SELECT COUNT(*) AS c, torrent FROM comments WHERE torrent IN ($allids) GROUP BY torrent");
        while ($row = mysql_fetch_assoc($res)) {
            $peerdata[$row["torrent"]]["comments"] = $row["c"];
        }
    }
    return array($rows, $peerdata);
}
/**
 * commenttable()
 *
 * @param mixed $rows
 * @return
 */
function commenttable($rows)
{
    global $CURUSER;
    begin_main_frame('100%');
    begin_frame('', false, '10', '100%');
    $count = 0;
    foreach ($rows as $row) {
        print ("<p class=sub>#" . $row["id"] . " by ");
        if (isset($row["username"])) {
            $a = sql_query("SELECT class FROM users WHERE id = '$row[user]'");
            $class = mysql_fetch_assoc($a);
            $title = $row["title"];
            if ($title == "")
                $title = get_user_class_name($row["class"]);
            else
                $title = htmlspecialchars(trim($title));
            print ("<a name=comm" . $row["id"] . " href=userdetails.php?id=" . $row["user"] .
                "><b>" . get_style($class['class'], $row["username"]) . "</b></a>" . ($row["donor"] ==
                "yes" ? "<img src=pic/star.png alt='Donor'>" : "") . ($row["warned"] == "yes" ?
                "<img src=" . "pic/warning.png alt=\"Warned\">" : "") . " ($title)\n");
        } else
            print ("<a name=\"comm" . $row["id"] . "\"><i>(orphaned)</i></a>\n");
        print (" at " . $row["added"] . " GMT" . ($row["user"] == $CURUSER["id"] ||
            get_user_class() >= UC_MODERATOR ? "- [<a href=comment.php?action=edit&cid=$row[id]>Edit</a>]" :
            "") . (get_user_class() >= UC_MODERATOR ?
            "- [<a href=comment.php?action=delete&cid=$row[id]>Delete</a>]" : "") . ($row["editedby"] &&
            get_user_class() >= UC_MODERATOR ?
            "- [<a href=comment.php?action=vieworiginal&cid=$row[id]>View original</a>]" :
            "") . "</p>\n");
        $avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars(trim($row["avatar"])) :
            "");
        if (!$avatar)
            $avatar = "pic/default_avatar.gif";
        $text = format_comment($row["text"]);
        if ($row["editedby"])
            $text .= "<p><font size=1 class=small>Last edited by <a href=userdetails.php?id=$row[editedby]><b>$row[username]</b></a> at $row[editedat] GMT</font></p>\n";
            $text = apply_filters("comment_text",$text);
        begin_table(true);
        $dt = gmtime() - 180;
        $dt = sqlesc(get_date_time($dt));
        print ("<tr>\n");
        print ("<td align=center width=100 height=100 style='padding: 0px'><img width=100 height=100 src=$avatar></td>\n");
        print ("<td class=text valign=top>$text</td>\n");
        print ("</tr>\n");
        print ("<tr><td colspan=2> " . ("'" . $row['last_access'] . "'" > $dt ?
            "<img src=pic/user_online.gif border=0 alt=\"Online\">" :
            "<img src=pic/user_offline.gif border=0 alt=\"Offline\">") . "<a href=\"sendmessage.php?receiver=" .
            htmlspecialchars(trim($row["user"])) . "\"><img src=\"pic/pm.gif\" border=\"0\" alt=\"Send message to " .
            htmlspecialchars($row["username"]) . "\"></a> <a href=\"report.php?commentid=" .
            htmlspecialchars(trim($row["id"])) . "\"><img src=\"pic/report.gif\" border=\"0\" alt=\"Report this comment\"></a></td>");
        end_table();
    }
    end_frame();
    end_main_frame();
}
/**
 * searchfield()
 *
 * @param mixed $s
 * @return
 */
function searchfield($s)
{
    return apply_filters("searchfield",preg_replace(array('/[^a-z0-9]/si', '/^\s*/s', '/\s*$/s', '/\s+/s'),
        array(" ", "", "", " "), $s));
}
/**
 * genrelist()
 *
 * @return
 */
function genrelist()
{
    $ret = array();
    $res = mysql_query("SELECT id, name FROM categories ORDER BY name");
    while ($row = mysql_fetch_array($res))
        $ret[] = $row;
    return apply_filters("genrelist",$ret);
}
/**
 * linkcolor()
 *
 * @param mixed $num
 * @return
 */
function linkcolor($num)
{
    if (!$num)
        return "red";
    return "green";
}
/**
 * ratingpic()
 *
 * @param mixed $num
 * @return
 */
function ratingpic($num)
{
    global $pic_base_url, $rootpath;
    $r = round($num * 2) / 2;
    if ($r < 1 || $r > 5)
        return;
    return "<img src=\"" . $rootpath . "pic/$r.gif\" border=\"0\" alt=\"rating: $num / 5\" />";
}
/**
 * writecomment()
 *
 * @param mixed $userid
 * @param mixed $comment
 * @return
 */
function writecomment($userid, $comment)
{
    $res = mysql_query("SELECT modcomment FROM users WHERE id = '$userid'") or
        sqlerr(__file__, __line__);
    $arr = mysql_fetch_assoc($res);
    $modcomment = gmdate("d-m-Y") . " - " . $comment . "" . ($arr[modcomment] != "" ?
        "\n\n" : "") . "$arr[modcomment]";
    $modcom = sqlesc($modcomment);
    return mysql_query("UPDATE users SET modcomment = $modcom WHERE id = '$userid'") or
        sqlerr(__file__, __line__);
}
/**
 * _torrents()
 *
 * @param mixed $res
 * @param string $variant
 * @return
 */
function _torrents($res, $variant = "index")
{
	do_action("_torrents_start");	
    global $pic_base_url, $CURUSER, $waitsystem;
    unset($wait);
    $browse_res = mysql_query("SELECT last_browse FROM users WHERE id=" . sqlesc($CURUSER[id]));
    $browse_arr = mysql_fetch_row($browse_res);
    $last_browse = $browse_arr[0];
    $time_now = gmtime();
    if ($last_browse > $time_now) {
        $last_browse = $time_now;
    }
    if ($CURUSER["class"] < UC_VIP) {
        if ($waitsystem == "yes") {
            $gigs = $CURUSER["uploaded"] / (1024 * 1024 * 1024);
            $ratio = (($CURUSER["downloaded"] > 0) ? ($CURUSER["uploaded"] / $CURUSER["downloaded"]) :
                0);
            if ($ratio < 0.5 || $gigs < 5)
                $wait = 48;
            elseif ($ratio < 0.65 || $gigs < 6.5)
                $wait = 24;
            elseif ($ratio < 0.8 || $gigs < 8)
                $wait = 12;
            elseif ($ratio < 0.95 || $gigs < 9.5)
                $wait = 6;
            else
                $wait = 0;
        }
    }
?>
<table border="1" cellspacing="0" cellpadding="3" width="100%">
<tr>

<?php


    $count_get = 0;
    foreach ($_GET as $get_name => $get_value) {
        $get_name = mysql_escape_string(strip_tags(str_replace(array("\"", "'"), array("",
            ""), $get_name)));
        $get_value = mysql_escape_string(strip_tags(str_replace(array("\"", "'"), array
            ("", ""), $get_value)));
        if ($get_name != "sort" && $get_name != "type") {
            if ($count_get > 0) {
                $oldlink = $oldlink . "&" . $get_name . "=" . $get_value;
            } else {
                $oldlink = $oldlink . $get_name . "=" . $get_value;
            }
            $count_get++;
        }
    }
    if ($count_get > 0) {
        $oldlink = $oldlink . "&";
    }
    if ($_GET['sort'] == "1") {
        if ($_GET['type'] == "desc") {
            $link1 = "asc";
        } else {
            $link1 = "desc";
        }
    }
    if ($_GET['sort'] == "2") {
        if ($_GET['type'] == "desc") {
            $link2 = "asc";
        } else {
            $link2 = "desc";
        }
    }
    if ($_GET['sort'] == "3") {
        if ($_GET['type'] == "desc") {
            $link3 = "asc";
        } else {
            $link3 = "desc";
        }
    }
    if ($_GET['sort'] == "4") {
        if ($_GET['type'] == "desc") {
            $link4 = "asc";
        } else {
            $link4 = "desc";
        }
    }
    if ($_GET['sort'] == "5") {
        if ($_GET['type'] == "desc") {
            $link5 = "asc";
        } else {
            $link5 = "desc";
        }
    }
    if ($_GET['sort'] == "6") {
        if ($_GET['type'] == "desc") {
            $link6 = "asc";
        } else {
            $link6 = "desc";
        }
    }
    if ($_GET['sort'] == "7") {
        if ($_GET['type'] == "desc") {
            $link7 = "asc";
        } else {
            $link7 = "desc";
        }
    }
    if ($_GET['sort'] == "8") {
        if ($_GET['type'] == "desc") {
            $link8 = "asc";
        } else {
            $link8 = "desc";
        }
    }
    if ($_GET['sort'] == "9") {
        if ($_GET['type'] == "desc") {
            $link9 = "asc";
        } else {
            $link9 = "desc";
        }
    }
    if ($link1 == "") {
        $link1 = "asc";
    }
    if ($link2 == "") {
        $link2 = "desc";
    }
    if ($link3 == "") {
        $link3 = "desc";
    }
    if ($link4 == "") {
        $link4 = "desc";
    }
    if ($link5 == "") {
        $link5 = "desc";
    }
    if ($link6 == "") {
        $link6 = "desc";
    }
    if ($link7 == "") {
        $link7 = "desc";
    }
    if ($link8 == "") {
        $link8 = "desc";
    }
    if ($link9 == "") {
        $link9 = "desc";
    }

?>
<td class="colhead" align="center"><font color=white>Type</font></td>
<td class="colhead" align="left"><a href="browse.php?<? print $oldlink; ?>sort=1&type=<? print
    $link1; ?>"><font color=white>Name</font></a> <font color=white>/</font> <a href="browse.php?<? print $oldlink; ?>sort=4&type=<? print
$link4; ?>"><font color=white>Added</font></a></td>
<?php
    if ($CURUSER["downloadpos"] != "no")
        print ("<td class=\"colhead\" align=\"left\"><font color=white>DL</td>");
    if ($wait) {
        print ("<td class=\"colhead\" align=\"center\"><font color=white>Wait</font></td>\n");
    }
    if ($variant == "mytorrents") {
        print ("<td class=\"colhead\" align=\"center\"><font color=white>Edit</font></td>\n");
        print ("<td class=\"colhead\" align=\"center\"><font color=white>Visible</font></td>\n");
    }

?>
<td class="colhead" align="right"><a href="browse.php?<? print $oldlink; ?>sort=2&type=<? print
    $link2; ?>"><img src=pic/files.gif border=0 alt=files></a></td>
<td class="colhead" align="right"><a href="browse.php?<? print $oldlink; ?>sort=3&type=<? print
    $link3; ?>"><img src=pic/comments.gif border=0 alt=comments></a></td>
<td class="colhead" align="right"><a href="browse.php?<? print $oldlink; ?>sort=7&type=<? print
    $link7; ?>"><img src=pic/seeders.gif border=0 alt=seeders></a></td>
<td class="colhead" align="right"><a href="browse.php?<? print $oldlink; ?>sort=8&type=<? print
    $link8; ?>"><img src=pic/leechers.gif border=0 alt=seeders></a></td>
<!--<td class="colhead" align="center">Rating</td>-->
<!--<td class="colhead" align="center">TTL</td>-->
<?php global $tproghack;
    if ($tproghack == 'yes') { ?>
<td class="colhead" align="center"><font color=white>Av.Progress</font></td>
<?php } ?>
<!--<td class="colhead" align="center">T.Speed</td>-->

<td class="colhead" align="center"><a href="browse.php?<?php print $oldlink; ?>sort=5&type=<? print
    $link5; ?>"><font color=white>Size</font></a> <font color=white>/</font> <a href="browse.php?<?php print $oldlink; ?>sort=6&type=<? print
$link6; ?>"><font color=white>Snatched</font></a></td>

<!--
<td class="colhead" align=right>Views</td>
<td class="colhead" align=right>Hits</td>
-->
<?php

    if ($variant == "index")
        print ("<td class=\"colhead\" align=center><a href=\"browse.php?{$oldlink}sort=9&type={$link9}\"><font color=white>Uploader</a></td>\n");

?>
<?php
do_action("_torrents_tr1");
global $usergroups;
 if ($usergroups['candeletetorrent'] == 'yes') { ?>
  <td class="colhead" align="center"><font color=white>Action</font></td>
<?php } ?>
<?php
	print ("</tr>\n");
    global $splitor;
    while ($row = mysql_fetch_assoc($res)) {
        if ($splitor == 'yes') {
            $day_added = $row['added'];
            $day_show = strtotime($day_added);
            $thisdate = date('Y-m-d', $day_show);
            if ($thisdate == $prevdate) {
                $cleandate = '';
            } else {
                $day_added = 'Torrents added ' . date('l, j. M', strtotime($row['added']));
                $cleandate = "<tr><td colspan=15><b>$day_added</b></td></tr>\n";
            }
            $prevdate = $thisdate;
            if (!$_GET['sort'] && !$_GET['d']) {
                echo $cleandate . "\n";
            }
        }
        $id = $row["id"];
        print ("<tr>\n");
        print ("<td align=center width=16 height=16 style='padding: 0px'>");
        if (isset($row["cat_name"])) {
            print ("<a href=\"browse.php?cat=" . $row["category"] . "\">");
            if (isset($row["cat_pic"]) && $row["cat_pic"] != "")
                print ("<img border=\"0\" src=\"$pic_base_url" . $row["cat_pic"] . "\" alt=\"" .
                    $row["cat_name"] . "\" />");
            else
                print ($row["cat_name"]);
            print ("</a>");
        } else
            print ("-");
        print ("</td>\n");
        $dispname = htmlspecialchars(trim($row["name"]));
        $sticky = ($row[sticky] == "yes" ?
            "<img src='pic/sticky.gif' border='0' alt='sticky'>" : "");
		$double = ($row[doubleupload] == "yes" ?
            "<img src='pic/x2.gif' border='0' alt='double upload'>" : "");            
        $count_dispname = strlen($dispname);
        $max_lenght_of_torrent_name = "35";
        if ($count_dispname > $max_lenght_of_torrent_name) {
            $short_torrent_name_alt = "title=\"$dispname\"";
            $dispname = substr($dispname, 0, $max_lenght_of_torrent_name) . "...";
        } else
            $short_torrent_name_alt = "title=\"$dispname\"";
        print ("<td align=left><a $short_torrent_name_alt href=\"details.php?");
        if ($variant == "mytorrents")
            print ("returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&");
        print ("id=$id");
        if ($variant == "index")
            print ("&hit=1");
        $thisisfree = ($row[free] == "yes" ? "<img src='pic/freedownload.gif' />" : "");
        $timezone = $row["added"];
        $added = sql_timestamp_to_unix_timestamp($row["added"]);
        if ($added >= $last_browse)
            print ("\"><b>$dispname $sticky</b></a> <img src=pic/new.png border=0> $thisisfree$double <br>" .
                str_replace(" ", "<br />", $timezone) . "</div>");
        else
            print ("\"><b>$dispname $sticky</b></a> $thisisfree$double <br>" . str_replace(" ",
                "&nbsp;", $timezone) . "</div>");
        if ($variant == "index")
            if ($CURUSER["downloadpos"] != "no")
                print ("<td align=\"right\"><a href=\"download.php?id=$id&name=" . rawurlencode
                    ($row["filename"]) . "\"><img src=pic/dl.png border=0 alt=Download></a></td>\n");
        if ($wait) {
            $elapsed = floor((gmtime() - strtotime($row["added"])) / 3600);
            if ($elapsed < $wait) {
                $color = dechex(floor(127 * ($wait - $elapsed) / 48 + 128) * 65536);
                print ("<td align=center><nobr><a href=\"faq.php#46\"><font color=\"$color\">" .
                    number_format($wait - $elapsed) . " h</font></a></nobr></td>\n");
            } else
                print ("<td align=center><nobr>None</nobr></td>\n");
        }
        if ($variant == "mytorrents")
            print ("<td align=\"center\"><a href=\"edit.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]) .
                "&id=" . $row["id"] . "\">edit</a>\n");
        print ("</td>\n");
        if ($variant == "mytorrents") {
            print ("<td align=\"right\">");
            if ($row["visible"] == "no")
                print ("<b>no</b>");
            else
                print ("yes");
            print ("</td>\n");
        }
        if ($row["type"] == "single")
            print ("<td align=\"center\">" . $row["numfiles"] . "</td>\n");
        else {
            if ($variant == "index")
                print ("<td align=\"center\"><b><a href=\"details.php?id=$id&hit=1&filelist=1\">" .
                    $row["numfiles"] . "</a></b></td>\n");
            else
                print ("<td align=\"center\"><b><a href=\"details.php?id=$id&filelist=1#filelist\">" .
                    $row["numfiles"] . "</a></b></td>\n");
        }
        if (!$row["comments"])
            print ("<td align=\"center\">" . $row["comments"] . "</td>\n");
        else {
            if ($variant == "index")
                print ("<td align=\"center\"><b><a href=\"details.php?id=$id&hit=1&tocomm=1\">" .
                    $row["comments"] . "</a></b></td>\n");
            else
                print ("<td align=\"center\"><b><a href=\"details.php?id=$id&page=0#startcomments\">" .
                    $row["comments"] . "</a></b></td>\n");
        }
        if ($row["seeders"]) {
            if ($variant == "index") {
                if ($row["leechers"])
                    $ratio = $row["seeders"] / $row["leechers"];
                else
                    $ratio = 1;
                print ("<td align=center><b><a href=details.php?id=$id&hit=1&toseeders=1><font color=" .
                    get_slr_color($ratio) . ">" . $row["seeders"] . "</font></a></b></td>\n");
            } else
                print ("<td align=\"center\"><b><a class=\"" . linkcolor($row["seeders"]) . "\" href=\"details.php?id=$id&dllist=1#seeders\">" .
                    $row["seeders"] . "</a></b></td>\n");
        } else
            print ("<td align=\"center\"><span class=\"" . linkcolor($row["seeders"]) . "\">" .
                $row["seeders"] . "</span></td>\n");
        if ($row["leechers"]) {
            if ($variant == "index")
                print ("<td align=center><b><a href=details.php?id=$id&hit=1&todlers=1>" .
                    number_format($row["leechers"]) . (isset($peerlink) ? "</a>" : "") . "</b></td>\n");
            else
                print ("<td align=\"center\"><b><a class=\"" . linkcolor($row["leechers"]) . "\" href=\"details.php?id=$id&dllist=1#leechers\">" .
                    $row["leechers"] . "</a></b></td>\n");
        } else
            print ("<td align=\"center\">0</td>\n");
        if ($tproghack == 'yes') {
            $seedersProgressbar = array();
            $leechersProgressbar = array();
            $resProgressbar = mysql_query("SELECT p.seeder, p.to_go, t.size FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE  p.torrent = '$id'") or
                sqlerr();
            $progressPerTorrent = 0;
            $iProgressbar = 0;
            while ($rowProgressbar = mysql_fetch_array($resProgressbar)) {
                $progressPerTorrent += sprintf("%.2f", 100 * (1 - ($rowProgressbar["to_go"] / $rowProgressbar["size"])));
                $iProgressbar++;
            }
            if ($iProgressbar == 0)
                $iProgressbar = 1;
            $progressTotal = sprintf("%.2f", $progressPerTorrent / $iProgressbar);
            $picProgress = get_percent_completed_image(floor($progressTotal)) . " <br>(" .
                round($progressTotal) . "%)";
            print ("<td align=center>$picProgress</td>\n");
        }
        $_s = "";
        if ($row["times_completed"] != 1)
            $_s = "s";
        print ("<td align=center>" . str_replace(" ", "&nbsp;", mksize($row["size"])) .
            "<br><a href=viewsnatches.php?id=$row[id]><b>" . number_format($row["times_completed"]) .
            " x </b>time$_s</a></td>\n");
        if ($variant == "index") {
            if ($row["anonymous"] == "yes") {
                print ("<td align=center><i>[Anonymous]</i></td>\n");
            } else {
                $q = sql_query("SELECT class FROM users WHERE id = '$row[owner]' LIMIT 1");
                $q = mysql_fetch_assoc($q);
                $row["username"] = get_style($q['class'], $row["username"]);
                print ("<td align=center>" . (isset($row["username"]) ? ("<a href=userdetails.php?id=" .
                    $row["owner"] . "><b>" . $row["username"] . "</b></a>") : "<i>(unknown)</i>") .
                    "</td>\n");
            }
        }
        do_action("_torrents_tr2");
        if ($usergroups['candeletetorrent'] == 'yes') {
            print ("<td align=center><a href=\"page.php?type=fastdelete&id=$row[id]\"><b>D</b></a>\n");
            print (" / <a href=\"edit.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]) .
                "&id=" . $row["id"] . " alt=edit\"><b>E</b></a></td>\n");
            
        }print ("</tr>\n");
    }
    print ("</table><P><img src=pic/freedownload.gif border=0> <b>Free download</b> (only upload stats are recorded!</P><P><img src=pic/x2.gif border=0> <b>Double Upload</b> (upload stats are recorded double!)</P>\n");
    do_action("_torrents_end");
    return $rows;
}
/**
 * add_shout()
 *
 * @param string $m
 * @return
 */
function add_shout($m = '')
{
    $message = "/notice " . $m;
    mysql_query("INSERT INTO shoutbox (date, text, userid, username) VALUES (" .
        implode(", ", array_map("sqlesc", array(time(), $message, '1', 'system'))) . ")") or
        sqlerr(__file__, __line__);
}
/**
 * iplogger()
 *
 * @return
 */
function iplogger()
{
    global $HTTP_SERVER_VARS, $CURUSER, $iplog2;
    if ($iplog2 == "yes") {
        $ip = IP::getip();
        $res = mysql_query("SELECT * FROM ips WHERE ip = '" . mysql_real_escape_string($ip) .
            "' AND userid = " . mysql_real_escape_string($CURUSER[id])) or die(mysql_error());
        if (mysql_num_rows($res) == 0) {
            mysql_query("INSERT INTO ips(userid,ip) VALUES ('" . mysql_real_escape_string($CURUSER[id]) .
                "', '" . mysql_real_escape_string($ip) . "')") or die(mysql_error());
        }
        
    }
    
}
/**
 * fancy()
 *
 * @param mixed $text
 * @param integer $withp
 * @return
 */
function fancy($text, $withp = 0)
{
        echo apply_filters("fancy",($withp ? "<p>" : "")."<table border=1 cellspacing=0 cellpadding=10 bgcolor=$bgcolor width=100% style='width:100%'><tr width=100%><td style='padding: 10px; background: #81A2C4; width:100%' class=text>
<font color=white><center><b>$text</b>
</font></center></td></tr></table>".($withp ? "</p>" : "")); 
}
/**
 * get_extension()
 *
 * @param mixed $file
 * @return
 */
function get_extension($file)
{
    return strtolower(substr(strrchr($file, "."), 1));
}
/**
 * dir_list()
 *
 * @param mixed $dir
 * @param integer $istemplate
 * @return
 */
function dir_list($dir, $istemplate = 0)
{
    global $rootpath;
    $dl = array();
    $ext = '';
    if (!file_exists($dir))
        error1();
    if ($hd = opendir($dir)) {
        while ($sz = readdir($hd)) {
            $ext = get_extension($sz);
            if ($istemplate) {
                if (preg_match("/^\./", $sz) == 0 && $ext != 'php' && file_exists($rootpath .
                    "fts-contents/templates/$sz/info_template.xml") && $sz !=
                    'administrator-templates')
                    $dl[] = $sz;
            } else {
                if (preg_match("/^\./", $sz) == 0 && $ext != 'php')
                    $dl[] = $sz;
            }
        }
        closedir($hd);
        asort($dl);
        return $dl;
    } else
        error1('', 'Couldn\'t open storage folder! Please check the path.');
}
/**
 * javascript()
 *
 * @param mixed $scr
 * @return
 */
function javascript($scr)
{
    if (is_array($scr)) {
        foreach ($src as $s);
        javascript($s);
    } else {
        global $rootpath;
        echo '<script type="text/javascript" src="' . $rootpath . 'clientside/' . $scr .
            '.js" charset="UTF-8"></script>'."\n";
    }
}
/**
 * cssload()
 *
 * @param mixed $css
 * @return
 */
function cssload($css)
{
    if (is_array($css)) {
        foreach ($css as $c)
            ;
        javascript($c);
    } else {
        global $BASEURL;
        echo '<link rel="stylesheet" href="' . $BASEURL . '/' . $css .
            '.css" type="text/css" media="screen" />';
    }
}
/**
 * hash_pad()
 *
 * @param mixed $hash
 * @return
 */
function hash_pad($hash)
{
    return apply_filters("hash_pad",str_pad($hash, 20));
}
/**
 * hash_where()
 *
 * @param mixed $name
 * @param mixed $hash
 * @return
 */
function hash_where($name, $hash)
{
    $shhash = preg_replace('/ *$/s', "", $hash);
    return "($name = " . sqlesc($hash) . " OR $name = " . sqlesc($shhash) . ")";
}
/**
 * get_user_icons()
 *
 * @param mixed $arr
 * @param bool $big
 * @return
 */
function get_user_icons($arr, $big = false)
{
    if ($big) {
        $donorpic = "starbig.gif";
        $leechwarnpic = "warnedbig.gif";
        $warnedpic = "warnedbig3.gif";
        $disabledpic = "disabledbig.gif";
        $style = "style='margin-left: 4pt'";
    } else {
        $donorpic = "star.png";
        $leechwarnpic = "warning.png";
        $warnedpic = "warned3.gif";
        $disabledpic = "disabled.png";
        $style = "style=\"margin-left: 2pt\"";
    }
    $pics = $arr["donor"] == "yes" ? "<img src=pic/$donorpic alt='Donor' border=0 $style>" :
        "";
    if ($arr["enabled"] == "yes")
        $pics .= ($arr["leechwarn"] == "yes" ? "<img src=pic/$leechwarnpic alt=\"Leechwarned\" border=0 $style>" :
            "") . ($arr["warned"] == "yes" ? "<img src=pic/$warnedpic alt=\"Warned\" border=0 $style>" :
            "");
    else
        $pics .= "<img src=pic/$disabledpic alt=\"Disabled\" border=0 $style>\n";
    return apply_filters("get_user_icons",$pics);
}
/**
 * get_percent_completed_image()
 *
 * @param mixed $p
 * @return
 */
function get_percent_completed_image($p)
{
    $maxpx = "45";
    if ($p == 0)
        $progress = "<img src=\"pic/progbar-rest.gif\" height=9 width=" . ($maxpx) .
            " />";
    if ($p == 100)
        $progress = "<img src=\"pic/progbar-green.gif\" height=9 width=" . ($maxpx) .
            " />";
    if ($p >= 1 && $p <= 30)
        $progress = "<img src=\"pic/progbar-red.gif\" height=9 width=" . ($p * ($maxpx /
            100)) . " /><img src=\"pic/progbar-rest.gif\" height=9 width=" . ((100 - $p) * ($maxpx /
            100)) . " />";
    if ($p >= 31 && $p <= 65)
        $progress = "<img src=\"pic/progbar-yellow.gif\" height=9 width=" . ($p * ($maxpx /
            100)) . " /><img src=\"pic/progbar-rest.gif\" height=9 width=" . ((100 - $p) * ($maxpx /
            100)) . " />";
    if ($p >= 66 && $p <= 99)
        $progress = "<img src=\"pic/progbar-green.gif\" height=9 width=" . ($p * ($maxpx /
            100)) . " /><img src=\"pic/progbar-rest.gif\" height=9 width=" . ((100 - $p) * ($maxpx /
            100)) . " />";
    return "<img src=\"pic/bar_left.gif\" />" . $progress . "<img src=\"pic/bar_right.gif\" />";
}
/**
 * parked()
 *
 * @return
 */
function parked()
{
    global $CURUSER;
    if ($CURUSER["parked"] == "yes")
        stderr("Access Denied!", "Your account is parked.");
}
/**
 * quote_smart()
 *
 * @param mixed $value
 * @return
 */
function quote_smart($value)
{
    if (get_magic_quotes_gpc()) {
        $value = stripslashes($value);
    }
    if (!is_numeric($value)) {
        $value = "'" . mysql_real_escape_string($value) . "'";
    }
    return apply_filters("quote_smart",$value);
}
/**
 * sec2hms()
 *
 * @param mixed $sec
 * @param bool $padHours
 * @return
 */
function sec2hms($sec, $padHours = false)
{
    $hms = "";
    $hours = intval(intval($sec) / 3600);
    $hms .= ($padHours) ? str_pad($hours, 2, "0", STR_PAD_LEFT) . ':' : $hours . ':';
    $minutes = intval(($sec / 60) % 60);
    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT) . ':';
    $seconds = intval($sec % 60);
    $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
    return $hms;
}
/**
 * auto_enter_cheater()
 *
 * @param mixed $userid
 * @param mixed $rate
 * @param mixed $upthis
 * @param mixed $diff
 * @param mixed $torrentid
 * @param mixed $client
 * @param mixed $ip
 * @param mixed $last_up
 * @return
 */
function auto_enter_cheater($userid, $rate, $upthis, $diff, $torrentid, $client,
    $ip, $last_up)
{
    mysql_query("INSERT INTO cheaters (added, userid, client, rate, beforeup, upthis, timediff, userip, torrentid) VALUES('" .
        get_date_time() . "', $userid, '$client', '$rate', '$last_up', '$upthis', '$diff', '$ip', '$torrentid')") or
        sqlerr(__file__, __line__);
}
/**
 * duty()
 *
 * @param mixed $check
 * @return
 */
function duty($check) {
	global $shoutduty,$shoutbot;
	if($shoutbot == 'yes')
	return eregi("$check",$shoutduty);
	else
	return false;
}
/**
 * _value()
 *
 * @param mixed $name
 * @return
 */
function _value($name) {
	global $$name;
	if(is_array($$name)){
		foreach($$name as $n)
		return $n;
	}
	else
	return $$name;
}
#bridge start
  function gzip ($use = false)
  {
    $gzipcompress = 'no';
    if ((((($gzipcompress == 'yes' OR $use) AND @extension_loaded ('zlib')) AND @ini_get ('zlib.output_compression') != '1') AND @ini_get ('output_handler') != 'ob_gzhandler'))
    {
      @ob_start ('ob_gzhandler');
    }

  }
#bridge end
function my_datee ($format, $stamp = '', $offset = '', $ty = 1)
  {
    global $CURUSER;
    global $lang;
    global $dateformat;
    global $timeformat;
    global $regdateformat;
    global $timezoneoffset;
    global $dstcorrection;
    if (empty ($stamp))
    {
      $stamp = time ();
    }
    else
    {
      if (strstr ($stamp, '-'))
      {
        $stamp = sql_timestamp_to_unix_timestamp ($stamp);
      }
    }

    if ((!$offset AND $offset != '0'))
    {
      if (($CURUSER['id'] != 0 AND array_key_exists ('tzoffset', $CURUSER)))
      {
        $offset = $CURUSER['tzoffset'];
        $dstcorrection = $CURUSER['dst'];
      }
      else
      {
        $offset = $timezoneoffset;
        $dstcorrection = $dstcorrection;
      }

      if ($dstcorrection == 'yes')
      {
        ++$offset;
        if (my_substrr ($offset, 0, 1) != '-')
        {
          $offset = '+' . $offset;
        }
      }
    }

    if ($offset == '-')
    {
      $offset = 0;
    }

    $date = gmdate ($format, $stamp + $offset * 3600);
    if (($dateformat == $format AND $ty))
    {
      $stamp = time ();
      $todaysdate = gmdate ($format, $stamp + $offset * 3600);
      $yesterdaysdate = gmdate ($format, $stamp - 86400 + $offset * 3600);
      if ($todaysdate == $date)
      {
        $date ="today";
      }
      else
      {
        if ($yesterdaysdate == $date)
        {
          $date ="yesterday";
        }
      }
    }

    return apply_filters("my_datee",$date);
  }
/**
 * bark()
 *
 * @param mixed $msg
 * @return
 */
function bark($msg) { 
stdhead("Failed");
stdmsg("Failed", apply_filters("barkmsg",$msg));
 stdfoot();
 exit;
}
  function my_substrr ($string, $start, $length = '')
  {
    if (function_exists ('mb_substr'))
    {
      if ($length != '')
      {
        $cut_string = mb_substr ($string, $start, $length);
      }
      else
      {
        $cut_string = mb_substr ($string, $start);
      }
    }
    else
    {
      if ($length != '')
      {
        $cut_string = substr ($string, $start, $length);
      }
      else
      {
        $cut_string = substr ($string, $start);
      }
    }

    return $cut_string;
  }  
/**
 * format_ratio()
 *
 * @param mixed $up
 * @param mixed $down
 * @param bool $color
 * @return
 */
	function format_ratio($up,$down, $color = True)
	{
		if ($down > 0)
		{
			$r = number_format($up / $down, 2);
    	if ($color)
				$r = "<font color=".get_ratio_color($r).">$r</font>";
		}
		else
			if ($up > 0)
	  		$r = "'Inf.'";
	  	else
	  		$r = "'---'";
		return apply_filters("format_ratio",$r);
	}
	/**
 * reqcommenttable()
 *
 * @param mixed $rows
 * @return
 */
function reqcommenttable($rows)
{
       global $CURUSER, $HTTP_SERVER_VARS;
       begin_main_frame('100%');
       begin_frame('',0,'10','100%');
       $count = 0;
       foreach ($rows as $row)
       {	
//=======change colors
		if($count2 == 0)
{
$count2 = $count2+1;
$class = "clearalt6";
}
else
{
$count2 = 0;
$class = "clearalt7";
}	   
print("<br>");
		begin_table(true);
		print("<tr><td class=colhead colspan=2><p class=sub><a name=comment_" . $row["id"] . ">#" . $row["id"] . "</a> by: ");
   if (isset($row["username"]))
 {
 $username = $row["username"];
 $ratres = mysql_query("SELECT uploaded, downloaded from users where username='$username'");
       $rat = mysql_fetch_array($ratres);
 if ($rat["downloaded"] > 0)
{
$ratio = $rat['uploaded'] / $rat['downloaded'];
$ratio = number_format($ratio, 3);
$color = get_ratio_color($ratio);
if ($color)
$ratio = "<font color=$color>$ratio</font>";
}
else
if ($rat["uploaded"] > 0)
    $ratio = "Inf.";
else
$ratio = "---";
         $title = $row["title"];
         if ($title == "")
   $title = get_user_class_name($row["class"]);
         else
   $title = htmlspecialchars($title);
       print("<a name=comm". $row["id"] .
               " href=userdetails.php?id=" . $row["user"] . "><b>" .
               htmlspecialchars($row["username"]) . "</b></a>" . ($row["donor"] == "yes" ? "<img src=pic/star.png alt='Donor'>" : "") . ($row["warned"] == "yes" ? "<img src=".
             "pic/warning.png alt=\"Warned\">" : "") . "<font size=\"-3\"> ($title) (ratio: $ratio)\n");
 }
 else
 print("<a name=\"comm" . $row["id"] . "\"><i>".str58."</i></a>\n");  
 print(" at " . $row["added"] . " GMT</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .																					
         ($row["user"] == $CURUSER["id"] || get_user_class() >= UC_MODERATOR ? "[ <a href=reqcomment.php?action=edit&amp;cid=$row[id]>".str59."</a> ]" : "") .																																																						
         (get_user_class() >= UC_MODERATOR ? "  [ <a href=reqcomment.php?action=delete&amp;cid=$row[id]>".str60."</a> ] " : "") .
         ($row["editedby"] && get_user_class() >= UC_MODERATOR ? "" : "") . " [ <a href=userdetails.php?id=" . $row["user"] . ">".str61."</a> ] [ <a href=sendmessage.php?receiver=" . $row["user"] . ">".str62."</a> ] [ <a href=report.php?reqcommentid=$row[id]>".str63."</a> ]</p>\n");
 $avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($row["avatar"]) : "");
 if (!$avatar)
         $avatar = "pic/default_avatar.gif"; 
 $text = apply_filters("reqcomment_text",$row["text"]);
 $text = format_comment($text);
   if ($row["editedby"])
$text .= "<p><font size=1 class=small>".str64." <a href=userdetails.php?id=$row[editedby]><b>$row[username]</b></a>  $row[editedat] GMT</font></p>\n";
print("</td></tr><tr valign=top><td align=center width=150 class=$class><img width=150 src=$avatar></td><td class=$class>$text</td></tr>\n");
end_table();
}
end_frame();
end_main_frame();
}
/**
 * offcommenttable()
 *
 * @param mixed $rows
 * @return
 */
function offcommenttable($rows)
{
       global $CURUSER, $HTTP_SERVER_VARS;
       begin_main_frame('100%');
       begin_frame('',0,'10','100%');
       $count = 0;
 
       foreach ($rows as $row)
       { 
	   //=======change colors
if($count2 == 0)
{
$count2 = $count2+1;
$class = "clearalt6";
}
else
{
$count2 = 0;
$class = "clearalt7";
}	
print("<br>");
		begin_table(true);
		print("<tr><td class=colhead colspan=2><p class=sub><a name=comment_" . $row["id"] . ">#" . $row["id"] . "</a> by: ");
   if (isset($row["username"]))
 {
 $username = $row["username"];
 $ratres = mysql_query("SELECT uploaded, downloaded from users where username='$username'");
       $rat = mysql_fetch_array($ratres);
 if ($rat["downloaded"] > 0)
{
$ratio = $rat['uploaded'] / $rat['downloaded'];
$ratio = number_format($ratio, 3);
$color = get_ratio_color($ratio);
if ($color)
$ratio = "<font color=$color>$ratio</font>";
}
else
if ($rat["uploaded"] > 0)
$ratio = "Inf.";
else
$ratio = "---";

   $title = $row["title"];
         if ($title == "")
   $title = get_user_class_name($row["class"]);
         else
   $title = htmlspecialchars($title);
       print("<a name=comm". $row["id"] .
               " href=userdetails.php?id=" . $row["user"] . "><b>" .
               htmlspecialchars($row["username"]) . "</b></a>" . ($row["donor"] == "yes" ? "<img src=pic/star.png alt='Donor'>" : "") . ($row["warned"] == "yes" ? "<img src=".
             "pic/warning.png alt=\"Warned\">" : "") . " ($title) (ratio: $ratio)\n");
 }
 else
print("<a name=\"comm" . $row["id"] . "\"><i>(orphaned)</i></a>\n");

 print(" at " . $row["added"] . " GMT&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .
         ($row["user"] == $CURUSER["id"] || get_user_class() >= UC_MODERATOR ? "[ <a href=offcomment.php?action=edit&amp;cid=$row[id]>Edit</a> ] " : "") .
         (get_user_class() >= UC_MODERATOR ? "[ <a href=offcomment.php?action=delete&amp;cid=$row[id]>Delete</a> ]" : "") .
         ($row["editedby"] && get_user_class() >= UC_MODERATOR ? "" : "") . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[ <a href=userdetails.php?id=" . $row["user"] . ">Profile</a> ] [ <a href=sendmessage.php?receiver=" . $row["user"] . ">PM</a> ] [ <a href=report.php?offcommentid=$row[id]>Report</a> ]</p>\n");
 $avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($row["avatar"]) : "");
 if (!$avatar)
 $avatar = "pic/default_avatar.gif";
	 $text = apply_filters("offcomment_text",$row["text"]);
 $text = format_comment($text);
   if ($row["editedby"])
$text .= "<p><font size=1 class=small>Edited by <a href=userdetails.php?id=$row[editedby]><b>$row[username]</b></a>  $row[editedat] GMT</font></p>\n";
print("</td></tr><tr valign=top><td align=center width=150 class=$class><img width=150 src=$avatar></td><td class=$class>$text</td></tr>\n");
end_table();
}
end_frame();
end_main_frame();
}
// code: Takes a string and does a IBM-437-to-HTML-Unicode-Entities-conversion.
// swedishmagic specifies special behavior for Swedish characters.
// Some Swedish Latin-1 letters collide with popular DOS glyphs. If these
// characters are between ASCII-characters (a-zA-Z and more) they are
// treated like the Swedish letters, otherwise like the DOS glyphs.
/**
 * code()
 *
 * @param mixed $ibm_437
 * @param bool $swedishmagic
 * @return
 */
function code($ibm_437, $swedishmagic = false) {
$table437 = array("\200", "\201", "\202", "\203", "\204", "\205", "\206", "\207",
"\210", "\211", "\212", "\213", "\214", "\215", "\216", "\217", "\220",
"\221", "\222", "\223", "\224", "\225", "\226", "\227", "\230", "\231",
"\232", "\233", "\234", "\235", "\236", "\237", "\240", "\241", "\242",
"\243", "\244", "\245", "\246", "\247", "\250", "\251", "\252", "\253",
"\254", "\255", "\256", "\257", "\260", "\261", "\262", "\263", "\264",
"\265", "\266", "\267", "\270", "\271", "\272", "\273", "\274", "\275",
"\276", "\277", "\300", "\301", "\302", "\303", "\304", "\305", "\306",
"\307", "\310", "\311", "\312", "\313", "\314", "\315", "\316", "\317",
"\320", "\321", "\322", "\323", "\324", "\325", "\326", "\327", "\330",
"\331", "\332", "\333", "\334", "\335", "\336", "\337", "\340", "\341",
"\342", "\343", "\344", "\345", "\346", "\347", "\350", "\351", "\352",
"\353", "\354", "\355", "\356", "\357", "\360", "\361", "\362", "\363",
"\364", "\365", "\366", "\367", "\370", "\371", "\372", "\373", "\374",
"\375", "\376", "\377");

$tablehtml = array("&#x00c7;", "&#x00fc;", "&#x00e9;", "&#x00e2;", "&#x00e4;",
"&#x00e0;", "&#x00e5;", "&#x00e7;", "&#x00ea;", "&#x00eb;", "&#x00e8;",
"&#x00ef;", "&#x00ee;", "&#x00ec;", "&#x00c4;", "&#x00c5;", "&#x00c9;",
"&#x00e6;", "&#x00c6;", "&#x00f4;", "&#x00f6;", "&#x00f2;", "&#x00fb;",
"&#x00f9;", "&#x00ff;", "&#x00d6;", "&#x00dc;", "&#x00a2;", "&#x00a3;",
"&#x00a5;", "&#x20a7;", "&#x0192;", "&#x00e1;", "&#x00ed;", "&#x00f3;",
"&#x00fa;", "&#x00f1;", "&#x00d1;", "&#x00aa;", "&#x00ba;", "&#x00bf;",
"&#x2310;", "&#x00ac;", "&#x00bd;", "&#x00bc;", "&#x00a1;", "&#x00ab;",
"&#x00bb;", "&#x2591;", "&#x2592;", "&#x2593;", "&#x2502;", "&#x2524;",
"&#x2561;", "&#x2562;", "&#x2556;", "&#x2555;", "&#x2563;", "&#x2551;",
"&#x2557;", "&#x255d;", "&#x255c;", "&#x255b;", "&#x2510;", "&#x2514;",
"&#x2534;", "&#x252c;", "&#x251c;", "&#x2500;", "&#x253c;", "&#x255e;",
"&#x255f;", "&#x255a;", "&#x2554;", "&#x2569;", "&#x2566;", "&#x2560;",
"&#x2550;", "&#x256c;", "&#x2567;", "&#x2568;", "&#x2564;", "&#x2565;",
"&#x2559;", "&#x2558;", "&#x2552;", "&#x2553;", "&#x256b;", "&#x256a;",
"&#x2518;", "&#x250c;", "&#x2588;", "&#x2584;", "&#x258c;", "&#x2590;",
"&#x2580;", "&#x03b1;", "&#x00df;", "&#x0393;", "&#x03c0;", "&#x03a3;",
"&#x03c3;", "&#x03bc;", "&#x03c4;", "&#x03a6;", "&#x0398;", "&#x03a9;",
"&#x03b4;", "&#x221e;", "&#x03c6;", "&#x03b5;", "&#x2229;", "&#x2261;",
"&#x00b1;", "&#x2265;", "&#x2264;", "&#x2320;", "&#x2321;", "&#x00f7;",
"&#x2248;", "&#x00b0;", "&#x2219;", "&#x00b7;", "&#x221a;", "&#x207f;",
"&#x00b2;", "&#x25a0;", "&#x00a0;");
$s = htmlspecialchars($ibm_437);


// 0-9, 11-12, 14-31, 127 (decimalt)
$control =
array("\000", "\001", "\002", "\003", "\004", "\005", "\006", "\007",
"\010", "\011", /*"\012",*/ "\013", "\014", /*"\015",*/ "\016", "\017",
"\020", "\021", "\022", "\023", "\024", "\025", "\026", "\027",
"\030", "\031", "\032", "\033", "\034", "\035", "\036", "\037",
"\177");

/* Code control characters to control pictures.
http://www.unicode.org/charts/PDF/U2400.pdf
(This is somewhat the Right Thing, but looks crappy with Courier New.)
$controlpict = array("&#x2423;","&#x2404;");
$s = str_replace($control,$controlpict,$s); */

// replace control chars with space - feel free to fix the regexp smile.gif
/*echo "[a\\x00-\\x1F]";
//$s = ereg_replace("[ \\x00-\\x1F]", " ", $s);
$s = ereg_replace("[ \000-\037]", " ", $s); */
$s = str_replace($control," ",$s);




if ($swedishmagic){
$s = str_replace("\345","\206",$s); // Code windows "a" to dos.
$s = str_replace("\344","\204",$s); // Code windows "ä" to dos.
$s = str_replace("\366","\224",$s); // Code windows "ö" to dos.
// $s = str_replace("\304","\216",$s); // Code windows "Ä" to dos.
//$s = "[ -~]\\xC4[a-za-z]";

// couldn't get ^ and $ to work, even through I read the man-pages,
// i'm probably too tired and too unfamiliar with posix regexps right now.
$s = ereg_replace("([ -~])\305([ -~])", "\\1\217\\2", $s); // A
$s = ereg_replace("([ -~])\304([ -~])", "\\1\216\\2", $s); // Ä
$s = ereg_replace("([ -~])\326([ -~])", "\\1\231\\2", $s); // Ö

$s = str_replace("\311", "\220", $s); // É
$s = str_replace("\351", "\202", $s); // é
}

$s = str_replace($table437, $tablehtml, $s);
return apply_filters("code",$s);
}
function maketable($res)
{
	$ret = "<table class=main border=1 cellspacing=0 cellpadding=5>" .
	"<tr><td class=colhead align=center>".str2."</td><td class=colhead>".str3."</td><td class=colhead align=center>".str4."</td><td class=colhead align=center>".str5."</td><td class=colhead align=right>".str6."</td><td class=colhead align=right>".str7."</td><td class=colhead align=center>".str8."</td>\n" .
	"<td class=colhead align=center>".str9."</td><td class=colhead align=center>".str10."</td></tr>\n";
	while ($arr = mysql_fetch_assoc($res))
	{
		if ($arr["downloaded"] > 0)
		{
			$ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
			$ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
		}
		else
			if ($arr["uploaded"] > 0)
				$ratio = "Inf.";
			else
				$ratio = "---";
		$catimage = htmlspecialchars($arr["image"]);
		$catname = htmlspecialchars($arr["catname"]);
		$ttl = (28*24) - floor((gmtime() - sql_timestamp_to_unix_timestamp($arr["added"])) / 3600);
		if ($ttl == 1) $ttl .= "<br>hour"; else $ttl .= "<br>hours";
			$size = str_replace(" ", "<br>", mksize($arr["size"]));
		$uploaded = str_replace(" ", "<br>", mksize($arr["uploaded"]));
		$downloaded = str_replace(" ", "<br>", mksize($arr["downloaded"]));
		$seeders = number_format($arr["seeders"]);
		$leechers = number_format($arr["leechers"]);
		$ret .= "<tr><td style='padding: 0px'><img src=\"pic/$catimage\" alt=\"$catname\" width=42 height=42></td>\n" .
		"<td><a href=details.php?id=$arr[torrent]&hit=1><b>" . htmlspecialchars($arr["torrentname"]) .
		"</b></a></td><td align=center>$ttl</td><td align=center>$size</td><td align=right>$seeders</td><td align=right>$leechers</td><td align=center>$uploaded</td>\n" .
		"<td align=center>$downloaded</td><td align=center>$ratio</td></tr>\n";
	}
	$ret .= "</table>\n";
	return $ret;
}
function usercpmenu ($selected = "home") {
	global $BASEURL;
	print ("<div class=\"shadetabs\"><ul>");
	print ("<li" . ($selected == "home" ? " class=selected" : "") . "><a href=\"$BASEURL/usercp.php\" onMouseout=\"window.status=''; return true;\" onMouseOver=\"window.status='User CP Home'; return true;\">User CP Home</a></li>");
	print ("<li" . ($selected == "personal" ? " class=selected" : "") . "><a href=\"$BASEURL/usercp.php?action=personal\"  onMouseout=\"window.status=''; return true;\" onMouseOver=\"window.status='Personal Settings'; return true;\">Personal Settings</a></li>");
	print ("<li" . ($selected == "tracker" ? " class=selected" : "") . "><a href=\"$BASEURL/usercp.php?action=tracker\"  onMouseout=\"window.status=''; return true;\" onMouseOver=\"window.status='Tracker Settings'; return true;\">Tracker Settings</a></li>");
	print ("<li" . ($selected == "forum" ? " class=selected" : "") . "><a href=\"$BASEURL/usercp.php?action=forum\"  onMouseout=\"window.status=''; return true;\" onMouseOver=\"window.status='Forum Settings'; return true;\">Forum Settings</a></li>");
	print ("<li" . ($selected == "security" ? " class=selected" : "") . "><a href=\"$BASEURL/usercp.php?action=security\"  onMouseout=\"window.status=''; return true;\" onMouseOver=\"window.status='Security Settings'; return true;\">Security Settings</a></li>");
	if(_ref_sys_ == 'yes')
	print ("<li" . ($selected == "ref" ? " class=selected" : "") . "><a href=\"$BASEURL/usercp.php?action=referral\"  onMouseout=\"window.status=''; return true;\" onMouseOver=\"window.status='Referral URL'; return true;\">Referral URL</a></li>");
	print ("</ul></div>");
}
function getimagewidth ($imagewidth, $imageheight)
    {
    while (($imagewidth > 150) or ($imageheight > 150))
        {
        $imagewidth=150;
        $imageheight=150;
        }
    return $imagewidth;
    }
function getimageheight ($imagewidth, $imageheight)
    {
    while (($imagewidth > 150) or ($imageheight > 150))
        {
        $imagewidth=150;
        $imageheight=150;
        }
    return $imageheight;
    }
function form($name) {
	return print("<form method=post action=usercp.php><input type=hidden name=action value=".htmlspecialchars($name)."><input type=hidden name=type value=save>");
}
function submit() {
	return tr("Save Settings", "<input type=submit value=\"Save Settings! (PRESS ONLY ONCE)\"></form>",1);
}
function format_tz($a)
{
	$h = floor($a);
	$m = ($a - floor($a)) * 60;
	return ($a >= 0?"+":"-") . (strlen(abs($h)) > 1?"":"0") . abs($h) .
		":" . ($m==0?"00":$m);
}
function priv($name, $descr) {
	global $CURUSER;
	if ($CURUSER["privacy"] == $name)
		return "<input type=\"radio\" name=\"privacy\" value=\"".htmlspecialchars($name)."\" checked=\"checked\" /> ".htmlspecialchars($descr);
	else
		return "<input type=\"radio\" name=\"privacy\" value=\"".htmlspecialchars($name)."\" /> ".htmlspecialchars($descr);
}
function goback ($text = "go back",$where = "-1") {
	print("<tr><td colspan=2 class=\"heading\" valign=\"top\" align=\"center\">\n");
	print ("<A HREF=\"javascript:history.go(".htmlspecialchars($where).")\">".htmlspecialchars($text)."</A></td></tr>\n");
}
function update_topic_last_post($topicid)
  {
    $res = sql_query("SELECT id FROM posts WHERE topicid=".sqlesc($topicid)." ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
    $arr = mysql_fetch_row($res) or die("No post found");
    $postid = $arr[0];

    sql_query("UPDATE topics SET lastpost=$postid WHERE id=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
  }
/**
 * getidfromusername()
 *
 * @param mixed $uname
 * @return
 */
function getidfromusername( $uname )
{
    //get target userid
    $zquery = mysql_query( "SELECT username,id FROM users WHERE id = '$uname'" ) or
        sqlerr( __file__, __line__ ) ;
    $zres = mysql_fetch_assoc( $zquery ) ;
    $rresult = $zres["username"] ;
    if ( ! $rresult )
    {
        $rresult = lang2 ;
    }
    return $rresult ;
}
function usertable_r100($res, $frame_caption)
{
global $CURUSER;
begin_frame($frame_caption, true);
begin_table();
?>

<p>A list of users with a ratio above 100. (VIP class and under)<br>
Sorted by join date (oldest first).</p>
<tr>
<td class=colhead align=left>User</td>
<td class=colhead>Uploaded</td>
<td class=colhead>Downloaded</td>
<td class=colhead align=right>Ratio</td>
<td class=colhead align=left>Joined</td>
</tr>
<?php
$num = 0;
while ($a = mysql_fetch_assoc($res))
{
++$num;
$highlight = $CURUSER["id"] == $a["userid"] ? " bgcolor=#BBAF9B" : "";
if ($a["downloaded"])
{
$ratio = $a["uploaded"] / $a["downloaded"];
$color = get_ratio_color($ratio);
$ratio = number_format($ratio, 2);
if ($color)
$ratio = "<font color=$color>$ratio</font>";
}
else
$ratio = "Inf.";
print("<tr$highlight><td align=left$highlight><a href=$BASEURL/userdetails.php?id=" .
$a["userid"] . "><b>" . $a["username"] . "</b>" .
"</td><td align=right$highlight>" . mksize($a["uploaded"]) .
"</td><td align=right$highlight>" . mksize($a["downloaded"]) .
"</td><td align=right$highlight>" . $ratio .
"</td><td align=left>" . gmdate("Y-m-d",strtotime($a["added"])) . " (" .
get_elapsed_time(sql_timestamp_to_unix_timestamp($a["added"])) . " ago)</td></tr>");
}
end_table();
end_frame();
}
function usertable_leechers($res, $frame_caption)
{
global $CURUSER;
begin_frame($frame_caption, true);
begin_table();
?>
<tr>
<td class="colhead" align="left">User</td>
<td class="colhead">Uploaded</td>
<td class="colhead">Downloaded</td>
<td class="colhead" align="right">Ratio</td>
<td class="colhead" align="left">Joined</td>
<td class="colhead" align="center">x</td>
</tr>
<?php

$cba='';
if ( isset($_GET["select"]) )
{
$select=$_GET["select"];
if ( $select == 'all' ) $cba='checked';
elseif ( $select =='none' ) $cba='';
}

$num = 0;
print("<form method=\"post\" action=\"leechers.php?godcomplex=yes\">");
while ($a = mysql_fetch_assoc($res))
{
foreach ($a as $key => $ertek )

++$num;
$highlight = $CURUSER["id"] == $a["userid"] ? " bgcolor=#BBAF9B" : "";
if ($a["downloaded"])
{
$ratio = $a["uploaded"] / $a["downloaded"];
$color = get_ratio_color($ratio);
$ratio = number_format($ratio, 2);
if ($color)
$ratio = "<font color=$color>$ratio</font>";
}
else
$ratio = "Inf.";
print("<tr class=row1 $highlight><td align=left$highlight><a href=$BASEURL/userdetails.php?id=" .
$a["userid"] . "><strong>" . $a["username"] . "</strong></a>");

if($a["warned"] == "yes"){print("<img src=\"$BASEURL/pic/warning.png\" />");}

print("</td><td class=row1 align=right $highlight>" . mksize($a["uploaded"]) .
"</td><td class=row1 align=right $highlight>" . mksize($a["downloaded"]) .
"</td><td class=row1 align=right $highlight>" . $ratio .
"</td><td class=row1 align=left>" . gmdate("Y-m-d",strtotime($a["added"])) . " (" .
get_elapsed_time(sql_timestamp_to_unix_timestamp($a["added"])) . " ago)</td>
<td><input type=checkbox name=\"cb_" . $a["username"] . "\" value=\"" . $a["userid"] . "\" " . $cba . " /></td>
</tr>");
}
end_table();
end_frame();
}
function usertable($res, $frame_caption)
  {
  	global $CURUSER;
    begin_frame($frame_caption, true, '10', '100%');
    begin_table(1);
?>
<tr>
<td class=colhead>Rank</td>
<td class=colhead align=left>User</td>
<td class=colhead>Uploaded</td>
<td class=colhead align=left>UL speed</td>
<td class=colhead>Downloaded</td>
<td class=colhead align=left>DL speed</td>
<td class=colhead align=right>Ratio</td>
<td class=colhead align=left>Joined</td>

</tr>
<?php
    $num = 0;
    while ($a = mysql_fetch_assoc($res))
    {
      ++$num;
      $highlight = $CURUSER["id"] == $a["userid"] ? " bgcolor=#BBAF9B" : "";
      if ($a["downloaded"])
      {
        $ratio = $a["uploaded"] / $a["downloaded"];
        $color = get_ratio_color($ratio);
        $ratio = number_format($ratio, 2);
        if ($color)
          $ratio = "<font color=$color>$ratio</font>";
      }
      else
        $ratio = "Inf.";
      print("<tr$highlight><td align=center>$num</td><td align=left$highlight><a href=userdetails.php?id=" .
      		$a["userid"] . "><b>" . $a["username"] . "</b>" .
      		"</td><td align=right$highlight>" . mksize($a["uploaded"]) .
					"</td><td align=right$highlight>" . mksize($a["upspeed"]) . "/s" .
         	"</td><td align=right$highlight>" . mksize($a["downloaded"]) .
      		"</td><td align=right$highlight>" . mksize($a["downspeed"]) . "/s" .
      		"</td><td align=right$highlight>" . $ratio .
      		"</td><td align=left>" . gmdate("Y-m-d",strtotime($a["added"])) . " (" .
      		get_elapsed_time(sql_timestamp_to_unix_timestamp($a["added"])) . " ago)</td></tr>");
    }
    end_table();
    end_frame();
  }
function _torrenttable($res, $frame_caption)
  {
    begin_frame($frame_caption, true, '10', '100%');
    begin_table(1);
?>
<tr>
<td class=colhead align=center>Rank</td>
<td class=colhead align=left>Name</td>
<td class=colhead align=right>Sna.</td>
<td class=colhead align=right>Data</td>
<td class=colhead align=right>Se.</td>
<td class=colhead align=right>Le.</td>
<td class=colhead align=right>To.</td>
<td class=colhead align=right>Ratio</td>
</tr>
<?php
    $num = 0;
    while ($a = mysql_fetch_assoc($res))
    {
      ++$num;
      if ($a["leechers"])
      {
        $r = $a["seeders"] / $a["leechers"];
        $ratio = "<font color=" . get_ratio_color($r) . ">" . number_format($r, 2) . "</font>";
      }
      else
        $ratio = "Inf.";
      print("<tr><td align=center>$num</td><td align=left><a href=details.php?id=" . $a["id"] . "&hit=1><b>" .
        $a["name"] . "</b></a></td><td align=right>" . number_format($a["times_completed"]) .
				"</td><td align=right>" . mksize($a["data"]) . "</td><td align=right>" . number_format($a["seeders"]) .
        "</td><td align=right>" . number_format($a["leechers"]) . "</td><td align=right>" . ($a["leechers"] + $a["seeders"]) .
        "</td><td align=right>$ratio</td>\n");
    }
    end_table();
    end_frame();
  }

  function countriestable($res, $frame_caption, $what)
  {
    global $CURUSER;
    begin_frame($frame_caption, true, '10', '100%');
    begin_table(1);
?>
<tr>
<td class=colhead>Rank</td>
<td class=colhead align=left>Country</td>
<td class=colhead align=right><?=$what?></td>
</tr>
<?php
  	$num = 0;
		while ($a = mysql_fetch_assoc($res))
		{
	    ++$num;
	    if ($what == "Users")
	      $value = number_format($a["num"]);
	    elseif ($what == "Uploaded")
	      $value = mksize($a["ul"]);
	    elseif ($what == "Average")
	    	$value = mksize($a["ul_avg"]);
 	    elseif ($what == "Ratio")
 	    	$value = number_format($a["r"],2);
	    print("<tr><td align=center>$num</td><td align=left><table border=0 class=main cellspacing=0 cellpadding=0><tr><td class=embedded>".
	      "<img align=center src=pic/flag/$a[flagpic]></td><td class=embedded style='padding-left: 5px'><b>$a[name]</b></td>".
	      "</tr></table></td><td align=right>$value</td></tr>\n");
	  }
    end_table();
    end_frame();
  }
function peerstable($res, $frame_caption)
  {
    begin_frame($frame_caption, true, '10', '100%');
    begin_table(1);

		print("<tr><td class=colhead>Rank</td><td class=colhead>Username</td><td class=colhead>Upload rate</td><td class=colhead>Download rate</td></tr>");

		$n = 1;
		while ($arr = mysql_fetch_assoc($res))
		{
      $highlight = $CURUSER["id"] == $arr["userid"] ? " bgcolor=#BBAF9B" : "";
			print("<tr><td$highlight>$n</td><td$highlight><a href=userdetails.php?id=" . $arr["userid"] . "><b>" . $arr["username"] . "</b></td><td$highlight>" . mksize($arr["uprate"]) . "/s</td><td$highlight>" . mksize($arr["downrate"]) . "/s</td></tr>\n");
			++$n;
		}

    end_table();
    end_frame();
  }
function dict_check($d, $s) {
	if ($d["type"] != "dictionary")
		bark("not a dictionary");
	$a = explode(":", $s);
	$dd = $d["value"];
	$ret = array();
	foreach ($a as $k) {
		unset($t);
		if (preg_match('/^(.*)\((.*)\)$/', $k, $m)) {
			$k = $m[1];
			$t = $m[2];
		}
		if (!isset($dd[$k]))
			bark("dictionary is missing key(s)");
		if (isset($t)) {
			if ($dd[$k]["type"] != $t)
				bark("invalid entry in dictionary");
			$ret[] = $dd[$k]["value"];
		}
		else
			$ret[] = $dd[$k];
	}
	return $ret;
}
function dict_get($d, $k, $t) {
	if ($d["type"] != "dictionary")
		bark("not a dictionary");
	$dd = $d["value"];
	if (!isset($dd[$k]))
		return;
	$v = $dd[$k];
	if ($v["type"] != $t)
		bark("invalid dictionary entry type");
	return $v["value"];
}
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
function isportopen($port)
{
	$sd = @fsockopen($_SERVER["REMOTE_ADDR"], $port, $errno, $errstr, 1);
	if ($sd)
	{
		fclose($sd);
		return true;
	}
	else
		return false;
}
function isproxy()
{
	$ports = array(80, 88, 1075, 1080, 1180, 1182, 2282, 3128, 3332, 5490, 6588, 7033, 7441, 8000, 8080, 8085, 8090, 8095, 8100, 8105, 8110, 8888, 22788);
	for ($i = 0; $i < count($ports); ++$i)
		if (isportopen($ports[$i])) return true;
	return false;
}
/**
 * getmicrotime()
 *
 * @return
 */
function getmicrotime(){
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
    }
/**
 * decode_unicode_url()
 *
 * @param mixed $str
 * @return
 */
function decode_unicode_url($str) {
$res = '';

$i = 0;
$max = strlen($str) - 6;
while ($i <= $max) {
$character = $str[$i];
if ($character == '%' && $str[$i + 1] == 'u') {
$value = hexdec(substr($str, $i + 2, 4));
$i += 6;

if ($value < 0x0080) // 1 byte: 0xxxxxxx
$character = chr($value);
else if ($value < 0x0800) // 2 bytes: 110xxxxx 10xxxxxx
$character =
chr((($value & 0x07c0) >> 6) | 0xc0)
. chr(($value & 0x3f) | 0x80);
else // 3 bytes: 1110xxxx 10xxxxxx 10xxxxxx
$character =
chr((($value & 0xf000) >> 12) | 0xe0)
. chr((($value & 0x0fc0) >> 6) | 0x80)
. chr(($value & 0x3f) | 0x80);
} else
$i++;

$res .= $character;
}

return $res . substr($str, $i);
}
/**
 * get_user_class_color()
 *
 * @param mixed $class
 * @param mixed $username
 * @param mixed $gender
 * @return
 */
function get_user_class_color($class, $username, $gender)
{
	return get_style($class,$username);
}
function clean ($data) {
	$data = trim(strval($data));  
	$data = str_replace(chr(0), '', $data);
	return apply_filters("clean",$data);
}
function fix_url($url)
{
	$url = htmlspecialchars($url);
	$f[0] = '&amp;';	
	$f[1] = ' ';
	$r[0] = '&';	
	$r[1] = '&nbsp;';
	return apply_filters("fix_url",str_replace($f, $r, $url));
}
function srt($a,$b)
    {
      if ($a[0] > $b[0]) return -1;
      if ($a[0] < $b[0]) return 1;
      return 0;
    }
function print_array($array, $offset_symbol = "|--", $offset = "", $parent = "")
{
  if (!is_array($array))
  {
    echo "[$array] is not an array!<BR>";
    return;
  }
 
  reset($array);


	switch($array['type'])
	{
		case "string":
			printf("<li><div class=string> - <span class=icon>[STRING]</span> <span class=title>[%s]</span> <span class=length>(%d)</span>: <span class=value>%s</span></div></li>",$parent,$array['strlen'],$array['value']);
			break;
		case "integer":
			printf("<li><div class=integer> - <span class=icon>[INT]</span> <span class=title>[%s]</span> <span class=length>(%d)</span>: <span class=value>%s</span></div></li>",$parent,$array['strlen'],$array['value']);
			break;
		case "list":
			printf("<li><div class=list> + <span class=icon>[LIST]</span> <span class=title>[%s]</span> <span class=length>(%d)</span></div>",$parent,$array['strlen']);
			echo "<ul>";
			print_array($array['value'], $offset_symbol, $offset.$offset_symbol);
			echo "</ul></li>";
			break;
		case "dictionary":
			printf("<li><div class=dictionary> + <span class=icon>[DICT]</span> <span class=title>[%s]</span> <span class=length>(%d)</span></div>",$parent,$array['strlen']);
			while (list($key, $val) = each($array))
			{
				if (is_array($val))
				{
					echo "<ul>";
					print_array($val, $offset_symbol, $offset.$offset_symbol,$key);
					echo "</ul>";
				}
			}
			echo "</li>";

			break;
		default:
			  while (list($key, $val) = each($array))
			  {
			    if (is_array($val))
			    {
			      //echo $offset;
			      print_array($val, $offset_symbol, $offset, $key);
			    }
			  }			
			break;
	
	}
 
}
function insert_tag($name, $description, $syntax, $example, $remarks)
{
	$result = format_comment($example);
	print("<p class=sub><b>$name</b></p>\n");
	print("<table class=main width=100% border=1 cellspacing=0 cellpadding=5>\n");
	print("<tr valign=top><td width=25%>Description:</td><td>$description\n");
	print("<tr valign=top><td>Syntax:</td><td><tt>$syntax</tt>\n");
	print("<tr valign=top><td>Example:</td><td><tt>$example</tt>\n");
	print("<tr valign=top><td>Result:</td><td>$result\n");
	if ($remarks != "")
		print("<tr><td>Remarks:</td><td>$remarks\n");
	print("</table>\n");
}
function puke($text = "w00t")
{
	stderr("w00t", logged);
}
function insertJumpTo($selected = 0)
{
global $CURUSER;
$res = sql_query('SELECT * FROM pmboxes WHERE userid=' . sqlesc($CURUSER['id']) . ' ORDER BY boxnumber'); ?>
<FORM action="messages.php" method="get">
<INPUT type="hidden" name="action" value="viewmailbox">Jump to: <SELECT name="box">
<OPTION value="1"<?=($selected == PM_INBOX ? " selected" : "")?>>Inbox</OPTION>
<OPTION value="-1"<?=($selected == PM_SENTBOX ? " selected" : "")?>>Sentbox</OPTION><?
while ($row = mysql_fetch_assoc($res))
{
if ($row['boxnumber'] == $selected)
{
echo("<OPTION value=\"" . $row['boxnumber'] . "\" selected>" . $row['name'] . "</OPTION>\n");
}
else
{
echo("<OPTION value=\"" . $row['boxnumber'] . "\">" . $row['name'] . "</OPTION>\n");
}
}
?></SELECT> <INPUT type="submit" value="Go"></FORM><?
}
function bark2($msg) {
	global $header, $footer;
	print("$header");
	print("<tr><td align=right><font color=red><b>ERROR:</b></font></td><td><b>$msg</b></font> <A HREF=\"javascript:history.go(-1)\"> [<b><u>Go Bac</u></b>k]</A></tr></td>");
	print("$footer");
	exit;
}
function send_test_mail_extra ($from = '', $to = '', $subject = '', $body = '', $debug = 'no') {
	global $rootpath,$success,$SITEEMAIL;	
	require ($rootpath . 'include/smtp/smtp.lib.php');
	
$mail = new smtp;
if ($debug == 'yes')
	$mail->debug(true);
else
	$mail->debug(false);
$mail->open(smtpaddress, smtpport);
$mail->auth(accountname, accountpassword);
$mail->from($SITEEMAIL);
$mail->to($to);
$mail->subject($subject);
$mail->body($body);
$mail->send();
$mail->close();
//print("".smtpaddress." ".smtpport." ".accountname." ".accountpassword."");
print("$success");
}
function send_test_mail_default($to,$fromname,$fromemail,$subject,$body) {
	global $SITENAME,$SITEEMAIL,$smtp,$smtp_host,$smtp_port,$smtp_from;
	# Send Mail Function v.03 (This function to help avoid spam-filters.)
	# Is the OS Windows or Mac or Linux?
	if (strtoupper(substr(PHP_OS,0,3)=='WIN')) {
		$eol="\r\n";
		$windows = true;
	}
	elseif (strtoupper(substr(PHP_OS,0,3)=='MAC'))
		$eol="\r";
	else
		$eol="\n"; 
	$mid = md5(IP::getip() . $fromname);
	$name = $_SERVER["SERVER_NAME"];
	$headers .= "From: $fromname <$fromemail>".$eol;	
	$headers .= "Reply-To: $fromname <$fromemail>".$eol;
	$headers .= "Return-Path: $fromname <$fromemail>".$eol;
	$headers .= "Message-ID: <$mid thesystem@$name>".$eol;
	$headers .= "X-Mailer: PHP v".phpversion().$eol; 
    $headers .= "MIME-Version: 1.0".$eol; 
    $headers .= "X-Sender: PHP".$eol;        
    if ($multiple)
    	$headers .= "Bcc: $multiplemail.$eol";
	if ($smtp == "yes") {    	
		ini_set('SMTP', $smtp_host);
		ini_set('smtp_port', $smtp_port);
		if ($windows)
			ini_set('sendmail_from', $smtp_from);
		}
			
    	@mail($to,$subject,$body,$headers) or bark2("Unable to send mail. Please check your SMTP settings or contact your host!");    
    	
    	ini_restore(SMTP); 
		ini_restore(smtp_port); 
		if ($windows)
			ini_restore(sendmail_from); 
    
}
function add_link($url, $title, $description = "")
{
  $text = "<a class=altlink href=$url>$title</a>";
  if ($description)
    $text = "$text - $description";
  print("<li>$text</li>\n");
}
/**
* Draws a random number of lines on the image.
*
* @param resource The image.
*/
function draw_lines(&$im) {
global $img_width, $img_height;

for($i = 10; $i < $img_width; $i += 10) {
$color = imagecolorallocate($im, rand(150, 255), rand(150, 255), rand(150, 255));
imageline($im, $i, 0, $i, $img_height, $color);
}
for($i = 10; $i < $img_height; $i += 10) {
$color = imagecolorallocate($im, rand(150, 255), rand(150, 255), rand(150, 255));
imageline($im, 0, $i, $img_width, $i, $color);
}
}

/**
* Draws a random number of circles on the image.
*
* @param resource The image.
*/
function draw_circles(&$im) {
global $img_width, $img_height;

$circles = $img_width*$img_height / 100;
for($i = 0; $i <= $circles; $i++) {
$color = imagecolorallocate($im, rand(180, 255), rand(180, 255), rand(180, 255));
$pos_x = rand(1, $img_width);
$pos_y = rand(1, $img_height);
$circ_width = ceil(rand(1, $img_width)/2);
$circ_height = rand(1, $img_height);
imagearc($im, $pos_x, $pos_y, $circ_width, $circ_height, 0, rand(200, 360), $color);
}
}

/**
* Draws a random number of dots on the image.
*
* @param resource The image.
*/
function draw_dots(&$im) {
global $img_width, $img_height;

$dot_count = $img_width*$img_height/5;
for($i = 0; $i <= $dot_count; $i++) {
$color = imagecolorallocate($im, rand(200, 255), rand(200, 255), rand(200, 255));
imagesetpixel($im, rand(0, $img_width), rand(0, $img_height), $color);
}
}

/**
* Draws a random number of squares on the image.
*
* @param resource The image.
*/
function draw_squares(&$im)
{
global $img_width, $img_height;

$square_count = 30;
for($i = 0; $i <= $square_count; $i++) {
$color = imagecolorallocate($im, rand(150, 255), rand(150, 255), rand(150, 255));
$pos_x = rand(1, $img_width);
$pos_y = rand(1, $img_height);
$sq_width = $sq_height = rand(10, 20);
$pos_x2 = $pos_x + $sq_height;
$pos_y2 = $pos_y + $sq_width;
imagefilledrectangle($im, $pos_x, $pos_y, $pos_x2, $pos_y2, $color);
}
}

/**
* Writes text to the image.
*
* @param resource The image.
* @param string The string to be written
*/
function draw_string(&$im, $string) {
global $use_ttf, $min_size, $max_size, $min_angle, $max_angle, $ttf_fonts, $img_height, $img_width;

$spacing = $img_width / my_strlen($string);
$string_length = my_strlen($string);
for($i = 0; $i < $string_length; $i++) {
// Using TTF fonts
if($use_ttf) {
// Select a random font size
$font_size = rand($min_size, $max_size);

// Select a random font
$font = array_rand($ttf_fonts);
$font = $ttf_fonts[$font];

// Select a random rotation
$rotation = rand($min_angle, $max_angle);

// Set the colour
$r = rand(0, 200);
$g = rand(0, 200);
$b = rand(0, 200);
$color = imagecolorallocate($im, $r, $g, $b);

// Fetch the dimensions of the character being added
$dimensions = imageftbbox($font_size, $rotation, $font, $string[$i], array());
$string_width = $dimensions[2] - $dimensions[0];
$string_height = $dimensions[3] - $dimensions[5];

// Calculate character offsets
//$pos_x = $pos_x + $string_width + ($string_width/4);
$pos_x = $spacing / 4 + $i * $spacing;
$pos_y = ceil(($img_height-$string_height/2));

if($pos_x + $string_width > $img_width) {
$pos_x = $pos_x - ($pos_x - $string_width);
}

// Draw a shadow
$shadow_x = rand(-3, 3) + $pos_x;
$shadow_y = rand(-3, 3) + $pos_y;
$shadow_color = imagecolorallocate($im, $r+20, $g+20, $b+20);
imagefttext($im, $font_size, $rotation, $shadow_x, $shadow_y, $shadow_color, $font, $string[$i], array());

// Write the character to the image
imagefttext($im, $font_size, $rotation, $pos_x, $pos_y, $color, $font, $string[$i], array());
} else {
// Get width/height of the character
$string_width = imagefontwidth(5);
$string_height = imagefontheight(5);

// Calculate character offsets
$pos_x = $spacing / 4 + $i * $spacing;
$pos_y = $img_height / 2 - $string_height -10 + rand(-3, 3);

// Create a temporary image for this character
if(gd_version() >= 2) {
$temp_im = imagecreatetruecolor(15, 20);
} else {
$temp_im = imagecreate(15, 20);
}
$bg_color = imagecolorallocate($temp_im, 255, 255, 255);
imagefill($temp_im, 0, 0, $bg_color);
imagecolortransparent($temp_im, $bg_color);

// Set the colour
$r = rand(0, 200);
$g = rand(0, 200);
$b = rand(0, 200);
$color = imagecolorallocate($temp_im, $r, $g, $b);

// Draw a shadow
$shadow_x = rand(-1, 1);
$shadow_y = rand(-1, 1);
$shadow_color = imagecolorallocate($temp_im, $r+50, $g+50, $b+50);
imagestring($temp_im, 5, 1+$shadow_x, 1+$shadow_y, $string[$i], $shadow_color);

imagestring($temp_im, 5, 1, 1, $string[$i], $color);

// Copy to main image
imagecopyresized($im, $temp_im, $pos_x, $pos_y, 0, 0, 40, 55, 15, 20);
imagedestroy($temp_im);
}
}
}

/**
* Obtain the version of GD installed.
*
* @return float Version of GD
*/
function gd_version() {
static $gd_version;

if($gd_version) {
return $gd_version;
}
if(!extension_loaded('gd')) {
return;
}

ob_start();
phpinfo(8);
$info = ob_get_contents();
ob_end_clean();
$info = stristr($info, 'gd version');
preg_match('/\d/', $info, $gd);
$gd_version = $gd[0];

return $gd_version;
}
/**
 * fmenu()
 *
 * @return
 */
function fmenu () {
	global $action;
	$selected = $action;
	print ("<div class=\"shadetabs\"><ul>");
	print ("<li" . ($selected == "flist" ? " class=selected" : "") . "><a href=\"friends.php?action=flist\">My Friends</a></li>");
	print ("<li" . ($selected == "added" ? " class=selected" : "") . "><a href=\"friends.php?action=added\">Added you to friends list</a></li>");
	print ("<li" . ($selected == "blocked" ? " class=selected" : "") . "><a href=\"friends.php?action=blocked\">Blocked users</a></li>");
	print("</ul></div>");
}
class Debug {
    function startTimer() {
		global $starttime;
        $mtime = microtime ();
        $mtime = explode (' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $starttime = $mtime;
    }
    function endTimer() {
        global $starttime;
        $mtime = microtime ();
        $mtime = explode (' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $endtime = $mtime;
        $totaltime = round (($endtime - $starttime), 5);
        return $totaltime;
    }
}
/**
 * getagent()
 *
 * @param mixed $httpagent
 * @param string $peer_id
 * @return
 */
function getagent($httpagent, $peer_id="")
{
if (preg_match("/^Azureus ([0-9]+\.[0-9]+\.[0-9]+\.[0-9]\_B([0-9][0-9|*])(.+)$)/", $httpagent, $matches))
return "Azureus/$matches[1]";
elseif (preg_match("/^Azureus ([0-9]+\.[0-9]+\.[0-9]+\.[0-9]\_CVS)/", $httpagent, $matches))
return "Azureus/$matches[1]";
elseif (preg_match("/^Java\/([0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches))
return "Azureus/<2.0.7.0";
elseif (preg_match("/^Azureus ([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches))
return "Azureus/$matches[1]";
elseif (preg_match("/BitTorrent\/S-([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
return "Shadow's/$matches[1]";
elseif (preg_match("/BitTorrent\/U-([0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches))
return "UPnP/$matches[1]";
elseif (preg_match("/^BitTor(rent|nado)\\/T-(.+)$/", $httpagent, $matches))
return "BitTornado/$matches[2]";
elseif (preg_match("/^BitTornado\\/T-(.+)$/", $httpagent, $matches))
return "BitTornado/$matches[1]";
elseif (preg_match("/^BitTorrent\/ABC-([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
return "ABC/$matches[1]";
elseif (preg_match("/^ABC ([0-9]+\.[0-9]+(\.[0-9]+)*)\/ABC-([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
return "ABC/$matches[1]";
elseif (preg_match("/^Python-urllib\/.+?, BitTorrent\/([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
return "BitTorrent/$matches[1]";
elseif (preg_match("/^BitTorrent\/brst(.+)/", $httpagent, $matches))
return "Burst";
elseif (preg_match("/^RAZA (.+)$/", $httpagent, $matches))
return "Shareaza/$matches[1]";
elseif (preg_match("/Rufus\/([0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches))
return "Rufus/$matches[1]";
elseif (preg_match("/^Python-urllib\\/([0-9]+\\.[0-9]+(\\.[0-9]+)*)/", $httpagent, $matches))
return "G3 Torrent";
elseif (preg_match("/MLDonkey\/([0-9]+).([0-9]+).([0-9]+)*/", $httpagent, $matches))
return "MLDonkey/$matches[1].$matches[2].$matches[3]";
elseif (preg_match("/ed2k_plugin v([0-9]+\\.[0-9]+).*/", $httpagent, $matches))
return "eDonkey/$matches[1]";
elseif (preg_match("/uTorrent\/([0-9]+)([0-9]+)([0-9]+)([0-9A-Z]+)/", $httpagent, $matches))
return "µTorrent/$matches[1].$matches[2].$matches[3].$matches[4]";
elseif (preg_match("/CT([0-9]+)([0-9]+)([0-9]+)([0-9]+)/", $peer_id, $matches))
return "cTorrent/$matches[1].$matches[2].$matches[3].$matches[4]";
elseif (preg_match("/Transmission\/([0-9]+).([0-9]+)/", $httpagent, $matches))
return "Transmission/$matches[1].$matches[2]";
elseif (preg_match("/KT([0-9]+)([0-9]+)([0-9]+)([0-9]+)/", $peer_id, $matches))
return "KTorrent/$matches[1].$matches[2].$matches[3].$matches[4]";
elseif (preg_match("/rtorrent\/([0-9]+\\.[0-9]+(\\.[0-9]+)*)/", $httpagent, $matches))
return "rTorrent/$matches[1]";
elseif (preg_match("/^ABC\/Tribler_ABC-([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
return "Tribler/$matches[1]";
elseif (preg_match("/^BitsOnWheels( |\/)([0-9]+\\.[0-9]+).*/", $httpagent, $matches))
return "BitsOnWheels/$matches[2]";
elseif (preg_match("/BitTorrentPlus\/(.+)$/", $httpagent, $matches))
return "BitTorrent Plus!/$matches[1]";
elseif (ereg("^Deadman Walking", $httpagent))
return "Deadman Walking";
elseif (preg_match("/^eXeem( |\/)([0-9]+\\.[0-9]+).*/", $httpagent, $matches))
return "eXeem$matches[1]$matches[2]";
elseif (preg_match("/^libtorrent\/(.+)$/", $httpagent, $matches))
return "libtorrent/$matches[1]";
elseif (substr($peer_id, 0, 12) == "d0c")
return "Mainline";
elseif (substr($peer_id, 0, 1) == "M")
return "Mainline/Decoded";
elseif (substr($peer_id, 0, 3) == "-BB")
return "BitBuddy";
elseif (substr($peer_id, 0, 8) == "-AR1001-")
return "Arctic Torrent/1.2.3";
elseif (substr($peer_id, 0, 6) == "exbc\08")
return "BitComet/0.56";
elseif (substr($peer_id, 0, 6) == "exbc\09")
return "BitComet/0.57";
elseif (substr($peer_id, 0, 6) == "exbc\0:")
return "BitComet/0.58";
elseif (substr($peer_id, 0,4) == "-BC0")
return "BitComet/0.".substr($peer_id,5,2);
elseif (substr($peer_id, 0, 7) == "exbc\0L")
return "BitLord/1.0";
elseif (substr($peer_id, 0, 7) == "exbcL")
return "BitLord/1.1";
elseif (substr($peer_id, 0, 3) == "346")
return "TorrenTopia";
elseif (substr($peer_id, 0, 8) == "-MP130n-")
return "MooPolice";
elseif (substr($peer_id, 0, 8) == "-SZ2210-")
return "Shareaza/2.2.1.0";
elseif (ereg("^0P3R4H", $httpagent))
return "Opera BT Client";
elseif (substr($peer_id, 0, 6) == "A310--")
return "ABC/3.1";
elseif (ereg("^XBT Client", $httpagent))
return "XBT Client";
elseif (ereg("^BitTorrent\/BitSpirit$", $httpagent))
return "BitSpirit";
elseif (ereg("^DansClient", $httpagent))
return "XanTorrent";

else
return "Unknown";
}
/**
 * dltable()
 *
 * @param mixed $name
 * @param mixed $arr
 * @param mixed $torrent
 * @return
 */
function dltable($name, $arr, $torrent)
{

	global $CURUSER;
	$s = "<b>" . count($arr) . " $name</b>\n";
	if (!count($arr))
		return $s;
	$s .= "\n";
	$s .= "<table width=100% class=main border=1 cellspacing=0 cellpadding=5>\n";
	$s .= "<tr><td class=colhead>User/IP</td>" .
          "<td class=colhead align=center>Connectable</td>".
          "<td class=colhead align=right>Uploaded</td>".
          "<td class=colhead align=right>Rate</td>".
          "<td class=colhead align=right>Downloaded</td>" .
          "<td class=colhead align=right>Rate</td>" .
          "<td class=colhead align=right>Ratio</td>" .
          "<td class=colhead align=right>Complete</td>" .
          "<td class=colhead align=right>Connected</td>" .
          "<td class=colhead align=right>Idle</td>" .
          "<td class=colhead align=left>Client</td></tr>\n";
	$now = time();
	$moderator = (isset($CURUSER) && get_user_class() >= UC_MODERATOR);
$mod = get_user_class() >= UC_MODERATOR;
	foreach ($arr as $e) {


                // user/ip/port
// check if anyone has this ip
($unr = sql_query("SELECT id, username, privacy, warned, donor FROM users WHERE id=$e[userid] ORDER BY last_access DESC LIMIT 1")) or die;
$una = mysql_fetch_array($unr);
if ($una["privacy"] == "strong") continue;
++$num;

$highlight = $CURUSER["id"] == $una["id"] ? " bgcolor=#BBAF9B" : "";
$s .= "<tr$highlight>\n";
//$s .= "<tr>\n";
if ($una["username"]) {

if (get_user_class() >= UC_MODERATOR || $torrent['anonymous'] != 'yes' || $e['userid'] != $torrent['owner']) {
// $s .= "<td><a href=userdetails.php?id=$e[userid]><b>$una[username]</b></a></td>\n";
$s .= "<td><a href=userdetails.php?id=$e[userid]><b>$una[username]</b></a>" . ($una["donor"] == "yes" ? "<img src=".
"pic/star.png alt='Donor'>" : "") . ($una["enabled"] == "no" ? "<img src=".
"pic/disabled.png alt=\"This account is disabled\" style='margin-left: 2px'>" : ($una["warned"] == "yes" ? "<a href=rules.php#warning class=altlink><img src=pic/warning.png alt=\"Warned\" border=0></a>" : ""));
}
elseif (get_user_class() >= UC_MODERATOR || $torrent['anonymous'] = 'yes') {
$s .= "<td><i>Anonymous</i></a></td>\n";
}
}
else
$s .= "<td>(unknown)</td>\n";
		$secs = max(1, ($now - $e["st"]) - ($now - $e["la"]));
		$revived = $e["revived"] == "yes";
        $s .= "<td align=center>" . ($e[connectable] == "yes" ? "Yes" : "<font color=red>No</font>") . "</td>\n";
		$s .= "<td align=right>" . mksize($e["uploaded"]) . "</td>\n";
		$s .= "<td align=right><nobr>" . mksize(($e["uploaded"] - $e["uploadoffset"]) / $secs) . "/s</nobr></td>\n";
		$s .= "<td align=right>" . mksize($e["downloaded"]) . "</td>\n";
		if ($e["seeder"] == "no")
			$s .= "<td align=right><nobr>" . mksize(($e["downloaded"] - $e["downloadoffset"]) / $secs) . "/s</nobr></td>\n";
		else
			$s .= "<td align=right><nobr>" . mksize(($e["downloaded"] - $e["downloadoffset"]) / max(1, $e["finishedat"] - $e[st])) .	"/s</nobr></td>\n";
                if ($e["downloaded"])
				{
                  $ratio = floor(($e["uploaded"] / $e["downloaded"]) * 1000) / 1000;
                    $s .= "<td align=\"right\"><font color=" . get_ratio_color($ratio) . ">" . number_format($ratio, 3) . "</font></td>\n";
				}
	               else
                  if ($e["uploaded"])
                    $s .= "<td align=right>Inf.</td>\n";
                  else
                    $s .= "<td align=right>---</td>\n";
		$s .= "<td align=right>" . sprintf("%.2f%%", 100 * (1 - ($e["to_go"] / $torrent["size"]))) . "</td>\n";
		$s .= "<td align=right>" . mkprettytime($now - $e["st"]) . "</td>\n";
		$s .= "<td align=right>" . mkprettytime($now - $e["la"]) . "</td>\n";
		$s .= "<td align=left>" . htmlspecialchars(getagent($e["agent"], $e["peer_id"])) . "</td>\n";
		$s .= "</tr>\n";
	}
	$s .= "</table>\n";
	return $s;
}
/**
 * hex_esc()
 *
 * @param mixed $matches
 * @return
 */
function hex_esc($matches) {
	return sprintf("%02x", ord($matches[0]));
}
/**
 * leech_sort()
 *
 * @param mixed $a
 * @param mixed $b
 * @return
 */
			function leech_sort($a,$b) {
                                if ( isset( $_GET["usort"] ) ) return seed_sort($a,$b);				
                                $x = $a["to_go"];
				$y = $b["to_go"];
				if ($x == $y)
					return 0;
				if ($x < $y)
					return -1;
				return 1;
			}
/**
 * seed_sort()
 *
 * @param mixed $a
 * @param mixed $b
 * @return
 */
			function seed_sort($a,$b) {
				$x = $a["uploaded"];
				$y = $b["uploaded"];
				if ($x == $y)
					return 0;
				if ($x < $y)
					return 1;
				return -1;
			}
function upload_form($name = "",$desc = "", $image = "") {
	global $torrent_dir,$max_torrent_size,$announce_urls,$CURUSER;
	?>
	<div align=Center>
<form enctype="multipart/form-data" action="takeupload.php" method="post" name="upload">
<input type="hidden" name="MAX_FILE_SIZE" value="<?=$max_torrent_size?>" />
<table border="1" cellspacing="0" cellpadding="10" width="100%">
<tr><td style='padding: 10px; background: black' class='text' colspan='2'>
<font color=white><center>The tracker's announce URL is: <b><?= $announce_urls[0] ?></b>
<?php
if(!is_writable($torrent_dir))
	print("<br><br><b>ATTENTION</b>: Torrent directory isn't writable. Please contact the administrator about this problem!");
if(!$max_torrent_size)
	print("<br><br><b>ATTENTION</b>: Max. Torrent Size not set. Please contact the administrator about this problem!");
?>
</font></center></td></tr>
<?php

tr("Torrent file", "<input type=file name=file size=80>\n", 1);
tr("Torrent name", "<input type=\"text\" name=\"name\" size=\"80\" value=\"$name\"/><br />(Taken from filename if not specified. <b>Please use descriptive names.</b>)\n", 1);
tr("NFO file", "<input type=file name=nfo size=80><br>(<b>Optional.</b> Can only be viewed by power users. </b> insert only file ending to <b>.nfo</b>)\n", 1);
tr("Torrent Image URL", "<input type=\"text\" name=\"imageurl\" size=\"80\" value=\"$image\"/><br />(<b>Optionaly </b> but try to fill it.)\n", 1);
if(_youtube_mod_ == 'yes')
tr("YouTube Video Link", "<input type=\"text\" name=\"tube\" size=\"80\" /><br />For Samples Should be in the format of http://www.youtube.com/watch?v=TYxbGgeeVmI...t;\n", 1);
print("<tr><td class=rowhead style='padding: 3px'>Description</td><td>");
textbbcode("upload","descr",$desc);
print("</td></tr>\n");

$s = "<select name=\"type\">\n<option value=\"0\">(choose one)</option>\n";

$cats = genrelist();
foreach ($cats as $row)
	$s .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";

$s .= "</select>\n";
tr("Type", $s, 1);
//==== offer dropdown for offer mod  from code by S4NE
$res66 = mysql_query("SELECT id, name, allowed FROM offers WHERE userid = $CURUSER[id] ORDER BY name ASC") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res66) > 0) {
$offer = "<select name=offer><option value=0>Your Offers</option>";
while($row66 = mysql_fetch_array($res66)) {
if ($row66["allowed"] == 'allowed')
$offer .= "<option value=\"" . $row66["id"] . "\">" . htmlspecialchars($row66["name"]) . "</option>";
}
$offer .= "</select>";
tr("Offer", $offer."<br> If you are uploading one of your offers please select it here so the voters will be notified." , 1);
}
//===end
tr("Show uploader", "<input type=checkbox name=uplver value=yes>Don't show my username in 'Uploaded By' field in browse.", 1);
	if(ur::ismod()) {
		tr("Free download", "<input type='checkbox' name='free'" . (($row["free"] == "yes") ? " checked='checked'" : "" ) . " value='1' /> Free download (only upload stats are recorded)", 1);
		tr("Double Upload", "<input type='checkbox' name='doubleupload'" . (($row["doubleupload"] == "yes") ? " checked='checked'" : "" ) . " value='1' /> Double Upload (upload stats are recorded double)", 1);
		tr("Sticky", "<input type='checkbox' name='sticky'" . (($row["sticky"] == "yes") ? " checked='checked'" : "" ) . " value='yes' />Set sticky this torrent!", 1);
}
?>
<tr><td align="center" colspan="2"><b>I read the rules before this uploading.</b> <input type="submit" class=btn value="Upload" /></td></tr>
</table>
</form></div>
<?php
}
function _index_smilies() {
	$act = $_GET['act'];
if($act=='showall') {
	all_smilies();
	die;
}
}
function parse_args( $args, $defaults = '' ) {
      if ( is_object( $args ) )
          $r = get_object_vars( $args );
      elseif ( is_array( $args ) )
          $r =& $args;
      else
          fts_parse_str( $args, $r );
  
      if ( is_array( $defaults ) )
          return array_merge( $defaults, $r );
      return $r;
}
function fts_parse_str( $string, &$array ) {
      parse_str( $string, $array );
      if ( get_magic_quotes_gpc() )
          $array = stripslashes_deep( $array );
      $array = $array;
}
function stripslashes_deep($value) {
       $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
       return $value;
}
			if($CURUSER)
			where($_SERVER['PHP_SELF'],$CURUSER['id']);
			add_action("index_top","_index_smilies");
?>