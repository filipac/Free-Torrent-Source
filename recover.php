<?php
require "include/bittorrent.php";

FLogin::failedloginscheck ("Recover",true);



if ($_SERVER["REQUEST_METHOD"] == "POST")
{
  	if ($iv == "yes") {
	global $reCAPTCHA_enable;
	$recap = ($reCAPTCHA_enable == 'yes' ? true : false);
	if(!$recap)
	check_code ($_POST['imagehash'], $_POST['imagestring'],"recover.php",true);
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
  FLogin:: failedlogins(recaptcha_error,true);
}

	}
}
  $email = unesc(htmlspecialchars(trim($_POST["email"])));
  $email = safe_email($email);
  if (!$email)
   FLogin:: failedlogins("You must enter an email address!",true);
  if (!check_email($email))
  FLogin::	failedlogins("Invalid email address!",true);
  $res = sql_query("SELECT * FROM users WHERE email=" . sqlesc($email) . " LIMIT 1") or sqlerr(__FILE__, __LINE__);
  $arr = mysql_fetch_assoc($res) or FLogin::failedlogins("The email address was not found in the database.\n",true);

	$sec = mksecret();

  sql_query("UPDATE users SET editsecret=" . sqlesc($sec) . " WHERE id=" . sqlesc($arr["id"])) or sqlerr(__FILE__, __LINE__);
  if (!mysql_affected_rows())
	  stderr("Error", "Database error. Please contact an administrator about this.");

  $hash = md5($sec . $email . $arr["passhash"] . $sec);
  $ip = IP::getip() ;
  $body = <<<EOD
Hi,

Someone, hopefully you, requested that the password for the account
associated with this email address ($email) be reset.

The request originated from $ip.

If you did not do this ignore this email. Please do not reply.

Should you wish to confirm this request, please follow this link:

$DEFAULTBASEURL/recover.php?id={$arr["id"]}&secret=$hash

After you do this, your password will be reset and emailed back
to you.

------
Yours,
The $SITENAME Team.
EOD;

sent_mail($arr["email"],$SITENAME,$SITEEMAIL,"$SITENAME password reset confirmation",$body);

}
elseif($_GET)
{
	$id = 0 + $_GET["id"];
	$md5 = $_GET["secret"];

	if (!$id)
	  httperr();

	$res = sql_query("SELECT username, email, passhash, editsecret FROM users WHERE id = " . sqlesc($id)) or sqlerr(__FILE__, __LINE__);
	$arr = mysql_fetch_array($res) or httperr();

  $email = $arr["email"];

	$sec = hash_pad($arr["editsecret"]);
	if (preg_match('/^ *$/s', $sec))
	  httperr();
	if ($md5 != md5($sec . $email . $arr["passhash"] . $sec))
	  httperr();

	// generate new password;
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

  $newpassword = "";
  for ($i = 0; $i < 10; $i++)
    $newpassword .= $chars[mt_rand(0, strlen($chars) - 1)];

 	$sec = mksecret();

  $newpasshash = md5($sec . $newpassword . $sec);

	sql_query("UPDATE users SET secret=" . sqlesc($sec) . ", editsecret='', passhash=" . sqlesc($newpasshash) . " WHERE id=" . sqlesc($id)." AND editsecret=" . sqlesc($arr["editsecret"])) or sqlerr(__FILE__, __LINE__);

	if (!mysql_affected_rows())
		stderr("Error", "Unable to update user data. Please contact an administrator about this error.");

  $body = <<<EOD
Hi,

As per your request we have generated a new password for your account.

Here is the information we now have on file for this account:

    User name: {$arr["username"]}
    Password:  $newpassword

You may login at $DEFAULTBASEURL/login.php

------
Yours,
The $SITENAME Team.
EOD;

sent_mail($email,$SITENAME,$SITEEMAIL,"$SITENAME account details",$body,"details");

}
else
{
 	stdhead(); 	
	?>
	<h1>Recover lost user name or password.</h1>
	<p>Use the form below to have your password reset and your account details mailed back to you.</p>
  <p>(You will have to reply to a confirmation email.)</p>
  <p><b>Note: <?=$maxloginattempts;?></b> failed attempts in a row will result in banning your ip!</p>
	<form method="post" action="recover.php">
	<table border=1 cellspacing=0 cellpadding=10>
	<tr><td class=rowhead>Registered email</td>
	<td><input type="text" size="26" name="email"></td></tr>
	<?php
	show_image_code ();
	?>
	<tr><td colspan="2" align="right"><input type="submit" value="Recover It!" class="but"></td></tr>
	</table>
	<p>You have <b><?=FLogin::remaining ();?></b> remaining tries.</p>
	<?php
	stdfoot();
}
?>