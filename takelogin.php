<?php
require_once("include/bittorrent.php");

if (!mkglobal("username:password"))
	die();


FLogin::failedloginscheck ();
HANDLE::cur_user_check () ;
lang::load("takelogin");

if ($iv == "yes"):
global $reCAPTCHA_enable;
if($reCAPTCHA_enable == 'yes') {
global $rootpath;
require_once($rootpath.'include/libs/recaptcha/recaptchalib.php');
$privatekey = @dbv('reCAPTCHA_privatekey');
$resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

if (!$resp->is_valid) {
	$ip = sqlesc( IP::getip() ) ;
        $added = sqlesc( get_date_time() ) ;
        $a = ( @mysql_fetch_row(@mysql_query("select count(*) from loginattempts where ip=$ip")) ) or
            sqlerr( __file__, __line__ ) ;
        if ( $a[0] == 0 )
            mysql_query( "INSERT INTO loginattempts (ip, added, attempts) VALUES ($ip, $added, 1)" ) or
                sqlerr( __file__, __line__ ) ;
        else
            mysql_query( "UPDATE loginattempts SET attempts = attempts + 1 where ip=$ip" ) or
                sqlerr( __file__, __line__ ) ;
  bark (str6);
}	
}else
	check_code ($_POST['imagehash'], $_POST['imagestring'],'login.php',true);
	endif;
$res = sql_query("SELECT id, passhash, secret, enabled FROM users WHERE username = " . sqlesc($username) . " AND status = 'confirmed'");
$row = mysql_fetch_array($res);

if (!$row)
FLogin::	failedlogins();

if ($row["passhash"] != md5($row["secret"] . $password . $row["secret"]))
FLogin::	failedlogins();

if ($row["enabled"] == "no")
	bark(str3);
global $sechash;
$passh = md5($row["passhash"].$sechash);

if ($_POST["logout"] == "yes"){
	if ($_POST["securelogin"] != "yes")
		logincookie($row["id"], $passh,1,15,false);
	else
		logincookie($row["id"], $passh,1,15);
	sessioncookie($row["id"], $passh,true);
}else {
	if ($_POST["securelogin"] != "yes")
		logincookie($row["id"], $passh,1,0x7fffffff,false);
	else
		logincookie($row["id"], $passh);
	sessioncookie($row["id"], $passh);
}
if (!empty($_POST["returnto"]))
	redirect("$BASEURL/$_POST[returnto]",str4,str5,3,false,false);
else
	redirect("$BASEURL/index.php",str4,str5,3,false,false);
?>