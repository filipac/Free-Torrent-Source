<?php
require_once("include/bittorrent.php");

registration_check("normal");
FLogin::failedloginscheck ("Signup");
HANDLE::cur_user_check () ;


if ($iv == "yes") {
	global $reCAPTCHA_enable;
	$recap = ($reCAPTCHA_enable == 'yes' ? true : false);
	if(!$recap)
	check_code ($_POST['imagehash'], $_POST['imagestring']);
	else {
		global $rootpath;
		require_once($rootpath.'include/libs/recaptcha/recaptchalib.php');
$recap_public = @dbv('reCAPTCHA_publickey');
$recap_private = @dbv('reCAPTCHA_privatekey');
$privatekey = $recap_private;
$resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

if (!$resp->is_valid) {
  bark (recaptcha_error);
}

	}
}	

if (!mkglobal("wantusername:wantpassword:passagain:email:hintanswer:passhint:birthday"))
	die();
if(empty($birthday)) bark("Birthday cannot be empty");
$email = htmlspecialchars(trim($email));
$email = safe_email($email);
if (!check_email($email))
	bark("Invalid email address!");
	
if(EmailBanned($email))
    bark("This email address banned!");
	
$country = $_POST["country"];
	int_check($country);

$gender =  htmlspecialchars(trim($_POST["gender"])); 
$allowed_genders = array("Male","Female","male","female");
if (!in_array($gender, $allowed_genders, true))
	bark("Invalid Gender!");
	
if (empty($wantusername) || empty($wantpassword) || empty($email) || empty($country) || empty($gender) || empty($hintanswer) || empty($passhint))
	bark("Don't leave any fields blank.");
	
$hintanswer 	= trim ( htmlspecialchars ( $hintanswer ) ) ;
$passhint 		= trim ( htmlspecialchars ( $passhint ) ) ;

if (strlen($hintanswer) < 6)
	bark("Sorry, Hintanswer is too short (min is 6 chars)");
	
if (strlen($wantusername) > 12)
	bark("Sorry, username is too long (max is 12 chars)");

if ($wantpassword != $passagain)
	bark("The passwords didn't match! Must've typoed. Try again.");

if (strlen($wantpassword) < 6)
	bark("Sorry, password is too short (min is 6 chars)");

if (strlen($wantpassword) > 40)
	bark("Sorry, password is too long (max is 40 chars)");

if ($wantpassword == $wantusername)
	bark("Sorry, password cannot be same as user name.");
if(_ref_sys_ == 'yes'):
$referrer = 0;
  if (((!empty ($_POST['referrer']) AND validusername ($_POST['referrer']))))
  {
    ($r_query = mysql_query ('SELECT id FROM users WHERE enabled = \'yes\' AND username = ' . sqlesc ($_POST['referrer'])) OR sqlerr (__FILE__, 274));
    if (0 < mysql_num_rows ($r_query))
    {
      $referrer = mysql_result ($r_query, 0, 'id');
    }
  }
  endif;

if (!validemail($email))
	bark("That doesn't look like a valid email address.");

if (!validusername($wantusername))
	bark("Invalid username.");
	
// make sure user agrees to everything...
if ($_POST["rulesverify"] != "yes" || $_POST["faqverify"] != "yes" || $_POST["ageverify"] != "yes")
	stderr("Signup failed", "Sorry, you're not qualified to become a member of this site.");

// check if email addy is already in use
$a = (@mysql_fetch_row(@sql_query("select count(*) from users where email='".mysql_real_escape_string($email)."'"))) or sqlerr(__FILE__, __LINE__);
if ($a[0] != 0)
  bark("The e-mail address $email is already in use.");
  
/*
// do simple proxy check
if (isproxy())
	bark("You appear to be connecting through a proxy server. Your organization or ISP may use a transparent caching HTTP proxy. Please try and access the site on <a href=".$BASEURL.":81/signup.php>port 81</a> (this should bypass the proxy server). <p><b>Note:</b> if you run an Internet-accessible web server on the local machine you need to shut it down until the sign-up is complete.");

$res = sql_query("SELECT COUNT(*) FROM users") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_row($res);
*/

$secret = mksecret();
$wantpasshash = md5($secret . $wantpassword . $secret);
$editsecret = ($verification == 'admin' ? '' : mksecret());

$ret = sql_query("INSERT INTO users (username, passhash, secret, editsecret, email, country, gender, hintanswer, passhint, status, class, invites, added, birthday) VALUES (" .
		implode(",", array_map("sqlesc", array($wantusername, $wantpasshash, $secret, $editsecret, $email, $country, $gender, $hintanswer, $passhint, 'pending'))).
		", '0', '$invite_count', '". get_date_time() ."','".$birthday."')");

if (!$ret) {
	if (mysql_errno() == 1062)
		bark("Username already exists!");
	bark("Sorry, mysql error. Please contact the administrator about this error.");
}

$id = mysql_insert_id();
if(_ref_sys_ == 'yes'):
if ((0 < $id AND 0 < $referrer))
  {
    $credit = 1073741824;
    (mysql_query ('' . 'UPDATE users SET uploaded = uploaded + ' . $credit . ' WHERE id = \'' . $referrer . '\'') OR sqlerr (__FILE__, 300));
    $credit_transformed = FFactory::ByteSize($credit);
    send_message($referrer,sprintf(lang_ref_message, $credit_transformed, $wantusername));
  }
  endif;
$dt = sqlesc(get_date_time());
$subject = sqlesc("Welcome to $SITENAME!");
$msg = sqlesc("Congratulations ".htmlspecialchars($wantusername).",\n\nYou are now a member of $SITENAME,\nWe would like to take this opportunity to say hello and welcome to $SITENAME!\n\nPlease be sure to read the Rules: ($DEFAULTBASEURL/rules.php) and the Faq: ($DEFAULTBASEURL/faq.php#dl8)\n and be sure to stop by the Forums: ($DEFAULTBASEURL/forums/) and say Hello!\n\nEnjoy your Stay.\nThe Staff of $SITENAME ");
sql_query("INSERT INTO messages (sender, receiver, subject, added, msg, poster) VALUES(0, $id, $subject, $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__);
if(duty('userbot')):
$cntry = sql_query("SELECT name FROM countries WHERE id = '$country' LIMIT 1");
$cntry = mysql_fetch_assoc($cntry);
add_shout("Welcome our latest user: $wantusername ($cntry[name])");
endif;
//write_log("User account $id ($wantusername) was created");

$psecret = md5($editsecret);
$ip = IP::getip() ;
$usern = htmlspecialchars($wantusername);
$body = <<<EOD
Hi $usern,

You have requested a new user account on $SITENAME and you have
specified this address ($email) as user contact.

If you did not do this, please ignore this email. The person who entered your
email address had the IP address $ip. Please do not reply.

To confirm your user registration, you have to follow this link:

$DEFAULTBASEURL/confirm.php?id=$id&secret=$psecret

After you do this, you will be able to use your new account. If you fail to
do this, you account will be deleted within a few days. We urge you to read
the RULES and FAQ before you start using $SITENAME.

Please Note: If you did not register for $SITENAME, please forward this email to $REPORTMAIL

------
Yours,
The $SITENAME Team.
EOD;
	
if ($verification == 'automatic'){
	stdhead();
	stdmsg("Finish signup!", "Please click <a href=\"$DEFAULTBASEURL/confirm.php?id=$id&secret=$psecret\">here</a> to finish signup, thanks!",false);
	stdfoot();
	exit;
	
}elseif ($verification == 'admin')
	header("Location: $DEFAULTBASEURL/page.php?type=ok&typeok=adminactivate");
else {
	sent_mail($email,$SITENAME,$SITEEMAIL,"$SITENAME user registration confirmation",$body,"signup",false);             
	header("Location: $DEFAULTBASEURL/page.php?type=ok&typeok=signup&email=" . urlencode($email));
}
?>