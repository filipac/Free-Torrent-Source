<?php
require_once("include/bittorrent.php");

registration_check("invitesystem",false,false);

$id = 0 + $_GET["id"];
$email = unesc(htmlspecialchars(trim($_POST["email"])));
$email = safe_email($email);
if (!$email)
    bark("You must enter an email address!");
if (!check_email($email))
	bark("Invalid email address!");

$body = htmlspecialchars(trim($_POST["body"]));
if(!$body)
	bark("Please add a personal message.");


// check if email addy is already in use
$a = (@mysql_fetch_row(@sql_query("select count(*) from users where email=".sqlesc($email)))) or die(mysql_error());
if ($a[0] != 0)
  bark("The e-mail address ".htmlspecialchars($email)." is already in use.");

$ret = sql_query("SELECT username FROM users WHERE id = ".sqlesc($id)) or sqlerr();
$arr = mysql_fetch_assoc($ret); 
  
$hash  = md5(mt_rand(1,10000));

$message = <<<EOD
Hi,

You have been invited to join the $SITENAME community by {$arr[username]}.
If you want to accept this invitation, you'll need to click this link:


$BASEURL/register.php?invitenumber=$hash


You'll need to accept the invitation within 24 hours, or else the link will become inactive.
We on $SITENAME hope that you'll accept the invitation and join our great community!

Personal message from {$arr[username]}:
$body


If you do not know the person who has invited you, please forward this email to $REPORTMAIL

------
Yours,
The $SITENAME Team.
EOD;

sent_mail($email,$SITENAME,$SITEEMAIL,"$SITENAME Invitation Confirmation",$message,"invitesignup",false);

sql_query("INSERT INTO invites (inviter, invitee, hash, time_invited) VALUES ('".mysql_real_escape_string($id)."', '".mysql_real_escape_string($email)."', '".mysql_real_escape_string($hash)."', " . sqlesc(get_date_time()) . ")");
sql_query("UPDATE users SET invites = invites - 1 WHERE id = ".mysql_real_escape_string($id)."") or sqlerr(__FILE__, __LINE__);

header("Refresh: 0; url=invite.php?id=".htmlspecialchars($id)."&type=new&sent=1");


?>  
  
  
    
