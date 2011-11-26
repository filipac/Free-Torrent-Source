<?php
if($CURUSER['downloaded'] > 0) { // Make sure there is a download value

// Set the ratio threshold based on user class
switch (get_user_class())
 {
   case UC_USER:
   case UC_POWER_USER: $ratio = 1.00;
   break;

   case UC_VIP: $ratio = 0.30;
   break;

   case UC_UPLOADER: 
   case UC_MODERATOR: $ratio = 0.70;
   break;

   case UC_ADMINISTRATOR:
   case UC_SYSOP: 
   case UC_STAFFLEADER: $ratio = 0.00;
   break;
 }
// Override ratio if donor, but only if existing ratio is higher than 0.70
if($CURSUSER['donor']=='yes' && $ratio > 0.70) $ratio = 0.70;

// Do remember warned users they are warned and for how long... [by fedepeco]
if ($CURUSER['leechwarn'] == 'yes') {
$leechwarnuntil = $CURUSER['leechwarnuntil'];
print("<p><table border=1 width=100% cellspacing=0 cellpadding=10 bgcolor=#8daff5 align=center><tr><td style='padding: 10px;'bgcolor=red align=center>\n");
print("<b><font color=white align=center>You are now warned for having a low ratio. You need to get a 0.6 ratio for your warning be removed.<br>If you don't get it in " . mkprettytime(strtotime($leechwarnuntil) - gmtime()) . ", your account will be banned.</font></b>");
print("</td></tr></table></p>\n");
print("<br>\n");
}
// End MOD...
}
if ($unread)
{
  $texts[] = "<a href=$BASEURL/messages.php>You have $unread new message" . ($unread > 1 ? "s" : "") . "! Click here to read.</a>";
}

if ($CURUSER) {
	$rel = sql_query("SELECT COUNT(*) FROM users WHERE status = 'pending' AND invited_by = ".mysql_real_escape_string($CURUSER[id])) or sqlerr(__FILE__, __LINE__);
	$arro = mysql_fetch_row($rel);
	$number = $arro[0];
	if ($number > 0)
	{
	 $texts[] = "<b><a href=$BASEURL/invite.php?id=$CURUSER[id]><font color=red>Your friend".($number > 1 ? "s" : "")." ($number) awaiting confirmation from you!</font></a></b>";
	}
}
if ($offlinemsg)
{
	$settings_script_name = substr($_SERVER[SCRIPT_FILENAME], -12 , 12);
	if ($settings_script_name != "settings.php" AND $settings_script_name != "announce.php") {	
		if(ur::ismod())	
	$texts[] = "<font color=red><b>WARNING!!!</b>:</font> The website is currently offline! Click <a href=$BASEURL/admin/settings.php>here</a> to change settings.";
	}
}
if (get_user_class() > UC_MODERATOR)
{
$resa = mysql_query("select count(id) as numreports from reports WHERE dealtwith=0");
$arra = mysql_fetch_assoc($resa);
$numreports = $arra[numreports];
if ($numreports){
$texts[] = "<a href=$BASEURL/admin/reports.php>There is $numreports new report" . ($numreports > 1 ? "s" : "") . "!</a>";
}

	$rese = mysql_query("SELECT COUNT(id) as nummessages from staffmessages WHERE answered='no'");
	$arre = mysql_fetch_assoc($rese);
	$nummessages = $arre[nummessages];
	if ($nummessages > 0) {
	$texts[] = "<a href=$BASEURL/admin/staffbox.php>There is $nummessages new staff message" . ($nummessages > 1 ? "s" : "") . "!</a>";
	}	
}
ads();
do_action("notifications");
if(!empty($texts)) {
	print("<div class=success align=left>\n");
foreach($texts as $t) {
 echo $t.'<BR>';

} 	print("</div>\n"); }
template::messagealertbox($unread);
template::texts();
?>