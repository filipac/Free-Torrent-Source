<?php
ob_start();
define('IN_INSTALL', true);
define('IN_TRACKER', true);
define('THIS_ROOT_PATH', './');
define('ROOT_PATH', '../');
define('INSTALL_VERSION', 'v.3.2.1');
if(!include(ROOT_PATH.'include/version.php'))
die("Are ya' sure you uploaded all files? Maybe i am plind but i can't find the include/version.php file!!");
if(IS_BETA_FTS)
define('TRACKER_VERSION', 'FTS '.VERSION.' BETA');
else
define('TRACKER_VERSION', 'FTS '.VERSION.' FINAL');
define('TIMENOW', time());
//define('VERSION', '0.24b');
define('DATA_CHUNK_LENGTH', 16384); // How many chars are read per time
define('MAX_QUERY_LINES', 300); // How many lines may be considered to be one query (except text lines)
error_reporting(E_ERROR | E_WARNING | E_PARSE);
set_magic_quotes_runtime(0);
ignore_user_abort(1);
@set_time_limit(0);
@ini_set('auto_detect_line_endings', true);
require_once (THIS_ROOT_PATH . 'functions.php');
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ?
    $_GET['action'] : 'step0');
$allowed_actions = array('step0', 'step1', 'step2', 'step3', 'step4', 'step5',
    'step6', 'step7', 'step8', 'save_db', 'save_main', 'save_admin', 'save_smtp',
    'save_security', 'step9', 'savesettings_tweak', 'step10',
    'savesettings_template');
if (!in_array($action, $allowed_actions))
    $action = 'step0';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title><?= TRACKER_VERSION ?> INSTALLATION <?= INSTALL_VERSION ?></title>
</head>
<body>
<?php
if (file_exists(THIS_ROOT_PATH . 'install.lock')) {
    step("Installation ERROR!", "ERROR!", "*");
    die("<center>For security reasons, this installer is locked!<br>Please (via FTP) remove the 'install/install.lock' file before continue.</center>");
}
if ($action == 'save_db') {
   # step("Database Setup (DONE!)", "MYSQL Setup", "2");
    GetVar( array('mysql_host', 'mysql_user', 'mysql_pass', 'mysql_db') ) ;
    $DATABASE['mysql_host'] = $mysql_host ;
    $DATABASE['mysql_user'] = $mysql_user ;
    $DATABASE['mysql_pass'] = $mysql_pass ;
    $DATABASE['mysql_db'] = $mysql_db ;
    WriteConfig('DATABASE', $DATABASE);
    gotostep(3);

} elseif ($action == 'save_main') {
    step("Basic Tracker Setup (DONE!)", "Tracker Setup", "3");
            GetVar( array('site_online', 'BASEURL', 'SITEEMAIL', 'SITENAME') ) ;
    $MAIN['site_online'] = $site_online ;
    $MAIN['BASEURL'] = $BASEURL ;
    $MAIN['SITEEMAIL'] = $SITEEMAIL ;
    $MAIN['SITENAME'] = $SITENAME ;
    WriteConfig('MAIN', $MAIN);
     GetVar(array('defaulttemplate', 'charset', 'metadesc', 'metakeywords'));
    $TEMPLATE['defaulttemplate'] = $defaulttemplate;
    $TEMPLATE['charset'] = $charset;
    $TEMPLATE['metadesc'] = $metadesc;
    $TEMPLATE['metakeywords'] = $metakeywords;
    WriteConfig('TEMPLATE', $TEMPLATE);
	gotostep(4);
} elseif ($action == 'save_admin') {
    define("UC_STAFFLEADER", 7);
    readconfig('DATABASE');
    dbconn();
    step("Administrator Setup (DONE!)", "Admin Setup", "7");
    if (!mkglobal("wantusername:wantpassword:passagain:email"))
        die('Error, Please try again!');
    $email = htmlspecialchars(trim($email));
    $email = safe_email($email);
    if (!check_email($email))
        bark("Invalid email address!");
    $country = "75";
    int_check($country);
    $gender = htmlspecialchars(trim("Male"));
    $allowed_genders = array("Male", "Female", "male", "female");
    if (!in_array($gender, $allowed_genders, true))
        bark("Invalid Gender!");
    if (empty($wantusername) || empty($wantpassword) || empty($email) || empty($country) ||
        empty($gender))
        bark("Don't leave any fields blank.");

    if (strlen($wantusername) > 13)
        bark("Sorry, username is too long (max is 13 chars)");

    if ($wantpassword != $passagain)
        bark("The passwords didn't match! Must've typoed. Try again.");

    if (strlen($wantpassword) < 6)
        bark("Sorry, password is too short (min is 6 chars)");

    if (strlen($wantpassword) > 40)
        bark("Sorry, password is too long (max is 40 chars)");

    if ($wantpassword == $wantusername)
        bark("Sorry, password cannot be same as user name.");

    if (!validemail($email))
        bark("That doesn't look like a valid email address.");

    if (!validusername($wantusername))
        bark("Invalid username.");
    $a = (@mysql_fetch_row(@mysql_query("select count(*) from users where email='$email'"))) or
        sqlerr(__file__, __line__);
    if ($a[0] != 0)
        bark("The e-mail address " . htmlspecialchars($email) . " is already in use.");
    $res = mysql_query("SELECT COUNT(*) FROM users") or sqlerr(__file__, __line__);
    $arr = mysql_fetch_row($res);
    $secret = mksecret();
    $wantpasshash = md5($secret . $wantpassword . $secret);
    $editsecret = (!$arr[0] ? "" : mksecret());
    $ret = mysql_query("INSERT INTO users (username, passhash, secret, editsecret, email, country, gender, status, " .
        (!$arr[0] ? "class, " : "") . "added) VALUES (" . implode(",", array_map("sqlesc",
        array($wantusername, $wantpasshash, $secret, $editsecret, $email, $country, $gender,
        (!$arr[0] ? 'confirmed' : 'pending')))) . ", " . (!$arr[0] ?
        UC_STAFFLEADER . ", " : "") . "'" . get_date_time() . "')") or sqlerr(__file__,
        __line__);
    if (!$ret) {
        if (mysql_errno() == 1062)
            bark("Username already exists!");
        bark("borked");
    } else {
    	$msg = <<<msg
This email is only to confirm that the admin account was added. Thanks.	
msg;
    	@mail($email,'Check message!',$msg,"From: FTS\nContent-Type: text/html; charset=iso-8859-1");
		gotostep(6);
    }
} elseif ($action == 'step6') {
    step("Finish the Installation", "Finish", "FINAL");
    if ($FH = @fopen(THIS_ROOT_PATH . 'install.lock', 'w')) {
        @fwrite($FH, 'bleh', 4);
        @fclose($FH);

        @chmod(THIS_ROOT_PATH . 'install.lock', 0666);
        $msg = "<center>Although the installer is now locked (to re-install, remove the file 'install.lock'), for added security, please remove the install_fresh.php program before continuing.
			 <br><br>
			 <b>PLEASE REMEMBER TO GO THROUGH ALL THE SETTINGS IN ADMINISTRATOR PANEL AGAIN FOR SAFETY</b>
			 <b><a href='../login.php'>CLICK HERE TO LOGIN!</a></center>";
    } else {
        $msg = "<center>PLEASE REMOVE THE INSTALLER ('install_fresh.php') BEFORE CONTINUING!<br>Failure to do so will enable ANYONE to delete/change your tracker at any time!
				<br><br>
				<b>PLEASE REMEMBER TO GO THROUGH ALL THE SETTINGS IN ADMINISTRATOR PANEL AGAIN FOR SAFETY</b>
				<b><a href='../login.php'>CLICK HERE TO LOGIN!</a></center>";
    }
    print ("$msg");

} elseif ($action == 'step4') {
    step("SQL Dump. Powered by BigDump ver. 0.24b from 2006-06-25", "Sql", "4");
    include_once ('bigdump.php');
} elseif ($action == 'step0') {
    step("Welcome to the installation wizard for " . TRACKER_VERSION . ".",
        "Welcome Screen", "0");
?>

			<p>Welcome to the installation wizard for <?= TRACKER_VERSION ?>. This wizard will install and configure a copy of <?= TRACKER_VERSION ?> on your server.</p>
			<p>Now that you've uploaded <?= TRACKER_VERSION ?> files the database and settings need to be created and imported. Below is an outline of what is going to be completed during installation.</p>
			<ul>
				<li>Requirements checked,</li>
				<li>Configuration of database engine,</li>
				<li>Basic tracker (main and template) settings configured,</li>
				<li>SQL Import (automaticly, no interaction needed),</li>
				<li>Administrator Setup,</li>
				<li>Finish and Lock installation!</li>
			</ul>			

<?= TRACKER_VERSION ?>  requires PHP 4.1.2 or better and an MYSQL database.<br><br>

<b>You will also need the following information that your webhost can provide:</b><br>
Linux or Windows server. Note: This source tested on the Windows Server. (We recommend: Windows)<br>
MYSQL 3.23 or greater. Note: This source tested on Mysql 5 (We recommend: Mysql 5)<br>
PHP version 4.1 or greater. Note: This source tested on PHP 5 (We recommend: PHP 5)<br>
The Apache webserver (version 1.3 or greater.) Note: This source tested on Apache 2 (We recommend: Apache2).<br>
The ability to change directory permissions to 777 or to change ownership of directories to be owned by the webserver process.<br>
.htaccess Support! (see httpd.conf for:  AllowOverride all)<br>
Short Open Tag support (see php.ini for: short_open_tag = On)<br>
GD2 Support (see php.ini for: extension=php_gd2.dll)<br>
Mod Rewrite Support (see httpd.conf for: LoadModule rewrite_module modules/mod_rewrite.so)<br>
SMTP Server.<br>
And make sure that you have setup +followmylinks for your tracker directory if your apache version not support turn this option on via .htaccess<br>

<br><br>
After each step has successfully been completed, click Next or Continue button to move on to the next step.<br>
Click "<b>Next</b>" to start.
      <br><span class="darkred"><br><label><div align=right><a href="install_fresh.php?action=step1">Next Step</a></label></div></tr></td></div></table>
<?php
} elseif ($action == 'step1') {
    step("Requirements Check", "Req.Check", "1");
    include_once ('reqcheck.php');
} elseif ($action == 'step2') {
    step("Database Setup", "MYSQL Setup", "2");
    print ("<form method='post' action='" . $_SERVER["SCRIPT_NAME"] .
        "'><input type='hidden' name='action' value='save_db'>");
    echo '<table width=100%>';
    print ( et() . mh("Tracker Database Settings") ) ;
    tr( "Mysql Host? ", makehelp(L35)."<input type='text' size='45' name=mysql_host value = 'localhost'>\n",
        1, '', 'width=30%' ) ;
    tr( "Mysql User? ", makehelp(L36)."<input type='text' size='45' name=mysql_user value = ''>\n", 1 ) ;
    tr( "Mysql Password? ",
        makehelp(L37)."<input type='password' size='45' name=mysql_pass value = ''>\n",
        1 ) ;
    tr( "Mysql Database Name? ",
        makehelp(L38)."<input type='text' size='45' name=mysql_db value = ''>\n", 1 ) ;
    print ( et() . mh("Save Settings") ) ;
    tr( "Save settings",
        "<input type='submit' name='save' value='Save Database Settings [PRESS ONLY ONCE]'>\n",
        1 ) ;
    print ("</form>");

} elseif ($action == 'step3') {
    Readconfig(MAIN);
    step("Basic Tracker Setup", "Tracker Setup", "3");
    $shorthost = $_SERVER["HTTP_HOST"];
    $sh = preg_replace('/www./', '', $shorthost);
    print ("<form method='post' action='" . $_SERVER["SCRIPT_NAME"] .
        "'><input type='hidden' name='action' value='save_main'>");
    echo '<table width=100%>';
    #  GetVar( array('site_online', 'BASEURL', 'SITEEMAIL', 'SITENAME') ) ;
        echo mh('Main Settings');
    tr( "Tracker online? ", makehelp("L1").select('site_online',array('yes' => 'yes','no' => 'no'),$MAIN["site_online"]),
        1, '', 'width=50%' ) ;
    tr( "Base URL? ", "<input type='text' size='45' name=BASEURL value='" . ($_SERVER['HTTP_HOST'] ?
        'http://'.$_SERVER['HTTP_HOST'].'' : "http://" . $_SERVER["HTTP_HOST"] . "") .
        "'> <b><u>NO</u> a trailing slash (/) at the end!</b>\n", 1 ) ;
    tr( "Site EMAIL? ", makehelp(L27)."<input type='text' size='45' name=SITEEMAIL value='" . ($_SERVER["HTTP_HOST"] ?
        "noreply@".$_SERVER["HTTP_HOST"] : "noreply@" . $sh) . "'>\n", 1 ) ;
    tr( "Report EMAIL? ", makehelp(L28)."<input type='text' size='45' name=reportemail value='" .
        ($_SERVER["HTTP_HOST"] ? "report@".$_SERVER["HTTP_HOST"] : "report@" . $sh) .
        "'>\n", 1 ) ;
    tr( "Site Name? ", makehelp(L29)."<input type='text' size='45' name=SITENAME value='" . ($_SERVER["HTTP_HOST"] ?
        $_SERVER["HTTP_HOST"] : $sh) . "'>\n", 1 ) ;
        $template_dirs = dir_list('../fts-contents/templates');
    print (et() . mh("Main Template Settings"));
    echo '<tr><td class="heading" valign="top" align="right" width=50%>Please select default template of your tracker:</td>';
    echo '<td valign="top" align="left"><select name="defaulttemplate">';
    if (empty($template_dirs))
        $dirlist .= '<option value="">There is no template</option>';
    else {
        foreach ($template_dirs as $dir)
            $dirlist .= '<option value="' . $dir . '" ' . ($defaulttemplate == $dir ?
                'selected' : '') . '>' . $dir . '</option>';
    }
    echo $dirlist . '</select></td></tr>';
    tr("Character Set", "<input type='text' size='45' name=charset value='UTF-8'> Charset of the site<br /><a href=\"http://www.w3.org/International/O-charset-lang.html\" target=\"_blank\">Click here to find the charset of your language.</a>.\n",
        1);
    tr("Meta Description", "<input type='text' size='45' name=metadesc value='torrent site'> Description of your website:
	Helps your website's position in search engines..\n", 1);
    tr("Meta keywords ", "<input type='text' size='45' name=metakeywords value='torrent,site'> Type in keywords separated by commas that describe your website.<br />These keywords will help your site be listed in search engines.\n",
        1);
    print ( et() . mh("Save Settings") ) ;
    tr( "Save settings",
        "<input type='submit' name='save' value='Save Main Settings [PRESS ONLY ONCE]'>\n",
        1 ) ;
    print ("</form>");
} elseif ($action == 'step5') {
    step("Administrator Setup", "Admin Setup", "5");
    readconfig('DATABASE');
    dbconn();
    print ("<form method='post' action='" . $_SERVER["SCRIPT_NAME"] .
        "'><input type='hidden' name='action' value='save_admin'>");
    echo '<table width=100%>';
?>
	<tr><td class=rowhead>Desired username:</td><td align=left><input type="text" size="40" name="wantusername" /><br>
<font class=small>Allowed Characters: (a-z), (A-Z), (0-9)</font></td></tr>
<tr><td class=rowhead>Pick a password:</td><td align=left><input type="password" size="40" name="wantpassword" /></td></tr>
<tr><td class=rowhead>Enter password again:</td><td align=left><input type="password" size="40" name="passagain" /></td></tr>
<tr><td class=rowhead>Email address:</td><td align=left><input type="text" size="40" name="email" />
<tr><td colspan="2" align="center"><font color=red><b>All Fields are required!</b><p></font><input type=submit value="Sign up! (PRESS ONLY ONCE)" style='height: 25px'></td></tr></form>
<?php
}
print ("</DIV></DIV></DIV></DIV>".'<a href="http://freetosu.berlios.de"><img src="images/logo_small.png"></a>'."</BODY></HTML>");
ob_end_flush();
function mh($message = "", $bgcolor = "#81A2C4")
{
    $notice = "<table border=1 cellspacing=0 cellpadding=10 bgcolor=$bgcolor width=100%><tr><td style='padding: 10px; background: $bgcolor' class=text>
<font color=white><center><b>$message</b></b>
</font></center></td></tr></table><table border=1 cellspacing=0 cellpadding=10 width=100%>";
    return $notice;
}
function et()
{
    return "</table>";
}
?>