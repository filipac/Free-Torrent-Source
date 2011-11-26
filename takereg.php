<?php
require_once("include/bittorrent.php");

registration_check();
FLogin::failedloginscheck ("Invite signup");
HANDLE::cur_user_check () ;
if ($iv == "yes") {
	global $reCAPTCHA_enable;
	$recap = ($reCAPTCHA_enable == 'yes' ? true : false);
	if(!$recap)
check_code ($_POST['imagehash'], $_POST['imagestring'], 'register.php?invitenumber='.htmlspecialchars($_POST['hash']));
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




$inviter =  $_POST["inviter"];
	int_check($inviter);
$code = unesc($_POST["hash"]);
$ip = IP::getip();


$res = sql_query("SELECT username FROM users WHERE id = $inviter") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_assoc($res);
$invusername = $arr[username];

if (!mkglobal("wantusername:wantpassword:passagain:email:hintanswer:passhint"))
	die();

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
	bark("The passwords didn't match! Try again.");

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

// make sure user agrees to everything...
if ($HTTP_POST_VARS["rulesverify"] != "yes" || $HTTP_POST_VARS["faqverify"] != "yes" || $HTTP_POST_VARS["ageverify"] != "yes")
	stderr("Signup failed", "Sorry, you're not qualified to become a member of this site.");

// check if email addy is already in use
$a = (@mysql_fetch_row(@sql_query("select count(*) from users where email='$email'"))) or sqlerr(__FILE__, __LINE__);
if ($a[0] != 0)
  bark("The e-mail address ".htmlspecialchars($email)." is already in use.");
  
/*
// do simple proxy check
if (isproxy())
	bark("You appear to be connecting through a proxy server. Your organization or ISP may use a transparent caching HTTP proxy. Please try and access the site on <a href=".$BASEURL.":81/signup.php>port 81</a> (this should bypass the proxy server). <p><b>Note:</b> if you run an Internet-accessible web server on the local machine you need to shut it down until the sign-up is complete.");
*/

$secret = mksecret();
$wantpasshash = md5($secret . $wantpassword . $secret);
$editsecret = mksecret();

$ret = sql_query("INSERT INTO users (username, passhash, secret, editsecret, email, country, gender, hintanswer, passhint, status, added, invites, invited_by) VALUES (" .
		implode(",", array_map("sqlesc", array($wantusername, $wantpasshash, $secret, $editsecret, $email, $country, $gender, $hintanswer, $passhint, 'pending'))) .
		",'" . get_date_time() . "', '$invite_count', '$inviter')") or sqlerr(__FILE__, __LINE__);

if (!$ret) {
	if (mysql_errno() == 1062)
		bark("Username already exists!");
	bark("Database In Distress");
}

$id = mysql_insert_id();
$subj = sqlesc("Welcome to $SITENAME.");
$msg = sqlesc("Welcome To $SITENAME, $wantusername.\nBefore you start downloading, please take a moment to read this:\n\n- [url=$BASEURL/faq.php]FAQ's[/URL]\n- [url=$BASEURL/rules.php]Rules[/url]\n\n" .
              "This site works with share ratio's. This is your uploaded/downloaded amount. Every user starts with a 1.00 ratio and we expect from you\n" .
			  "that you try to keep it like that. If you violate this too much, you'll recieve a warning. After 3 warnings it's set, and you'll get banned from\n" .
			  "the server. So please try to keep a 0.50-1.00 ratio or higher. Also try to seed your torrent as long as possible, so our great community can enjoy\n" .
			  "the same vid's you enjoy.You can also upload and seed your own torrents.\n\n" .
			  "For requests we have our own request section. You can request anything you want, but check if noone has requested it before you by using\n" .
			  "the search function. Our forum needs seperate registration, so if you want to be part of our forum community, you have to register for that seperatly.\n\n" .
			  "If you have questions after this short intro, PM one of the staff members or post in the forum.\n\nI wish you a good time on $SITENAME and keep sharing!\n\nThe $SITENAME Staff.");
@sql_query("INSERT INTO messages (sender, receiver, subject, added, msg, poster) VALUES(0, $id, $subj, '" . get_date_time() . "', $msg, 0)") or sqlerr(__FILE__, __LINE__);

$psecret = md5($editsecret);
$ip = IP::getip() ;
$body = <<<EOD
Hi,

You have requested a new user account on $SITENAME and you have
specified this address ($email) as user contact.

Your account is awaiting confirmation from your inviter.
As long as your account isn't confirmed, you can't login to the site.

Account info:
Username: $wantusername
Password: $wantpassword

If your account isn't being confirmed within 24 hrs, your account will be deleted.
Please read the RULES and FAQ before you start using $SITENAME.

Please Note: If you did not register for $SITENAME, please forward this email to $REPORTMAIL

------
Yours,
The $SITENAME Team.
EOD;

sent_mail($email,$SITENAME,$SITEEMAIL,"$SITENAME Registration Confirmation",$body,"invitesignup",false);

sql_query("DELETE FROM invites WHERE hash = '".mysql_real_escape_string($code)."'");
header("Refresh: 0; url=page.php?type=ok&typeok=signup&email=" . urlencode($email));
?>