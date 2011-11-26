<?php
require "include/bittorrent.php";

loggedinorreturn();

$rootpath = './';
$version = "SMTP CHECK v.0.4";
$action = isset($_POST['action']) ? htmlspecialchars($_POST['action']) : 'showform';
$type = ($_POST['sendtype'] ? htmlspecialchars($_POST['sendtype']) : '');
if (get_user_class() < UC_ADMINISTRATOR)
	bark2("Access denied.");

$header = <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>$version</title>
<link rel="stylesheet" href="/fts-contents/styles/default.css" type="text/css">
</head>
<body><p>
<table border=1 cellspacing=0 cellpadding=10 bgcolor=black width=100%><tr><td style='padding: 10px; background: black' class=text>
<font color=white><center>$version</font></center></td></tr></table></p>
<table border=1 cellspacing=0 cellpadding=10 width=100%>
EOD;
$footer = "</table></body></html>";
$testmail = <<<EOD
Hi,

If you see this message, your SMTP function works great.

Have a nice day.
EOD;

$success = "$header <tr><td>No error found however this does not mean the mail arrived 100%. Use debug mode to see more results if you use external mail function.</tr></td> $footer";

if ($action == "sendmailextra") {
	DEFINE("smtpaddress", "$_POST[smtpaddress]", true);
	DEFINE("smtpport", "$_POST[smtpport]", true);
	DEFINE("accountname", "$_POST[accountname]", true);
	DEFINE("accountpassword", "$_POST[accountpassword]", true);
	DEFINE("email", "".trim(htmlspecialchars($_POST[email]))."", true);
	send_test_mail_extra("$SITEEMAIL",email,"$SITENAME SMTP Testing Mail","$testmail", "".($_POST["debug"] == "yes" ? "yes" : "no")."");    
	unset($action);
}
if ($action == "sendmail")
{
	$email = htmlspecialchars(trim($_POST['email']));
	$email = safe_email($email);
	if (!check_email($email))
		bark2("Invalid email address!");
	if ($type == "sendtypeextra") {
		print("$header");
		print("<form method=post action=mailtest.php>");
		print("<input type=hidden name=action value=sendmailextra>");
		print("<input type=hidden name=email value='$email'>");
		print("<tr><td align=right>Outgoing mail (SMTP) address:</td><td><input type=text name=smtpaddress size=40> <b>hint:</b> smtp.yourisp.com</td></tr>");
		print("<tr><td align=right>Outgoing mail (SMTP) port:</td><td><input type=text name=smtpport size=40> <b>hint:</b> 80</td></tr>");
		print("<tr><td align=right>Account Name:</td><td><input type=text name=accountname size=40> <b>hint:</b> yourname@yourisp.com</td></tr>");
		print("<tr><td align=right>Account Password:</td><td><input type=password name=accountpassword size=40> <b>hint:</b> your password goes here</td></tr>");
		print("<tr><td align=right>Debug Mode?:</td><td><input type=radio name=debug value=yes>yes <input type=radio name=debug value=no checked>no &nbsp;&nbsp;&nbsp;&nbsp;<b>hint:</b> set 'yes' to see more results after you click on the send button</td></tr>");
		print("<tr><td align=right><b><u>WARNING:</u> Don't leave any fields blank!</b></td><td><input type=submit name=send value='Send test mail (PRESS ONLY ONCE)'></form></td></tr>");
		print ("$footer");
	}
	if ($type == "sendtypedefault") {
		send_test_mail_default($email,$SITENAME,$SITEEMAIL,"$SITENAME SMTP Testing Mail",$testmail);
		print ("$success");	
	}
	unset($action);
}

if ($action == "showform") {
	print("$header");
	print("<form method='post' action='mailtest.php'>");
	print("<input type='hidden' name='action' value='sendmail'>");
	print("<tr><td align=right>Enter an email address to send a test mail:</td><td><input type='text' name='email' size=35> <b>hint:</b> yourname@hotmail.com</td></tr>");
	print("<tr><td align=right>Select send method:</td><td><input type='radio' name='sendtype' value='sendtypedefault' checked>Use default PHP mail function.  <input type='radio' name='sendtype' value='sendtypeextra'>Use external mail function (Your ISP or Your Host).</td></tr>");
	print("<tr><td align=right><b><u>WARNING:</u> Don't leave any fields blank!</b></td><td><input type='submit' name='sendmail' value='Send test mail (PRESS ONLY ONCE)'></form></td></tr>");
	print ("$footer");
	unset($action);
}



?>