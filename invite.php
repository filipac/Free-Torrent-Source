<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
parked();
global $invitesystem;
if($invitesystem == 'off') stderr('Error!','Invite system is currently off!');
$newpage = new page_verify();
$newpage->create('invite');
$id = isset($_GET['id']) ? 0 + $_GET["id"] : $CURUSER['id'];
$type = unesc($_GET["type"]);

if ($CURUSER[id] != $id && get_user_class() < UC_ADMINISTRATOR && !is_valid_id($id))
	stderr("Sorry","Permission Denied!");

stdhead("Invites");

$res = sql_query("SELECT invites FROM users WHERE id = ".mysql_real_escape_string($id)) or sqlerr();
$inv = mysql_fetch_assoc($res);

if ($inv["invites"] != 1){
$_s = "s";
} else {
$_s = "";
}

if ($type == 'new'){
	if ($CURUSER[invites] <= 0) {
		stdmsg("Sorry","You have no invites left!");
		stdfoot();
		die;
	}
	$sent = htmlspecialchars($_GET['sent']);
	if ($sent == 1)
		$msg = "The invite code has been sent!";	
print("<form method=post action=takeinvite.php?id=".htmlspecialchars($id).">".
"<table border=1 width=737 cellspacing=0 cellpadding=5>$msg".
"<tr class=tabletitle><td colspan=2><b>Invite someone to join $SITENAME ($inv[invites] invitation$_s left)</b></td></tr>".
"<tr class=tableb><td width=15%>Email Address</td><td><input type=text size=40 name=email><br><font class=small>Email Address must be valid. The invite will receive an email about your invite.</font></td></tr>".
"<tr class=tableb><td>Message</td><td><textarea name=body rows=6 cols=80></textarea></td></tr>".
"<tr class=tableb><td align=center colspan=2><input type=submit value=Invite style='height: 20px'></td></tr>".
"</form></table>");

} else {

$rel = sql_query("SELECT COUNT(*) FROM users WHERE invited_by = ".mysql_real_escape_string($id)) or sqlerr(__FILE__, __LINE__);
$arro = mysql_fetch_row($rel);
$number = $arro[0];

$ret = sql_query("SELECT id, username, email, uploaded, downloaded, status, warned, enabled, donor, email FROM users WHERE invited_by = ".mysql_real_escape_string($id)) or sqlerr();
$num = mysql_num_rows($ret);

print("<form method=post action=page.php?type=takeconfirm&id=".htmlspecialchars($id)."><table border=1 width=737 cellspacing=0 cellpadding=5>".
"<tr class=tabletitle><td colspan=7><b>Current status of invites</b> ($number)</td></tr>");

if(!$num){
print("<tr class=tableb><td colspan=7>No invites yet.</tr>");
} else {

print("<tr class=tableb><td><b>Username</b></td><td><b>Email</b></td><td><b>Uploaded</b></td><td><b>Downloaded</b></td><td><b>Ratio</b></td><td><b>Status</b></td>");
if ($CURUSER[id] == $id || get_user_class() >= UC_SYSOP)
print("<td align=center><b>Confirm</b></td>");

print("</tr>");
for ($i = 0; $i < $num; ++$i)
{
  $arr = mysql_fetch_assoc($ret);
  if ($arr[status] == 'pending')
  $user = "<td align=left><a href=page.php?type=checkuser&id=$arr[id]>$arr[username]</a></td>";
  else
  $user = "<td align=left><a href=userdetails.php?id=$arr[id]>$arr[username]</a>" .($arr["warned"]  == "yes" ? "&nbsp;<img src=pic/warning.png border=0 alt='Warned'>" : "")."&nbsp;" .($arr["enabled"]  == "no" ? "&nbsp;<img src=pic/disabled.png border=0 alt='Disabled'>" : "")."&nbsp;" .($arr["donor"]  == "yes" ? "<img src=pic/star.png border=0 alt='Donor'>" : "")."</td>";

  if ($arr["downloaded"] > 0) {
      $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
      $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
      } else {
      if ($arr["uploaded"] > 0) {
      $ratio = "Inf.";
      }
      else {
      $ratio = "---";
      }
	  }
  if ($arr["status"] == 'confirmed')
      $status = "<a href=userdetails.php?id=$arr[id]><font color=#1f7309>Confirmed</font></a>";
      else
      $status = "<a href=page.php?type=checkuser&id=$arr[id]><font color=#ca0226>Pending</font></a>";	    	  
	  
print("<tr class=tableb>$user<td>$arr[email]</td><td>" . mksize($arr[uploaded]) . "</td><td>" . mksize($arr[downloaded]) . "</td><td>$ratio</td><td>$status</td>");
if ($CURUSER[id] == $id || get_user_class() >= UC_SYSOP){
print("<td align=center>");
if ($arr[status] == 'pending')
print("<input type=\"checkbox\" name=\"conusr[]\" value=\"" . $arr[id] . "\" />");
print("</td>");
}

print("</tr>");  	
} 
}
if ($CURUSER[id] == $id || get_user_class() >= UC_SYSOP){ 	
print("<input type=hidden name=email value=$arr[email]>");
print("<tr class=tableb><td colspan=7 align=right><input type=submit value='Confirm Users' style='height: 20px'></form></td></tr>");
if ($CURUSER[invites] <= 0)
	print("<tr class=tableb><td colspan=7 align=center><form method=post action=invite.php?id=".htmlspecialchars($id)."&type=new><input type=submit value='Invite Someone' style='height: 20px' disabled></form></td></tr>");
else
	print("<tr class=tableb><td colspan=7 align=center><form method=post action=invite.php?id=".htmlspecialchars($id)."&type=new><input type=submit value='Invite Someone' style='height: 20px'></form></td></tr>");	
}
print("</table><br>");

$rul = sql_query("SELECT COUNT(*) FROM invites WHERE inviter =".mysql_real_escape_string($id)) or sqlerr();
$arre = mysql_fetch_row($rul);
$number1 = $arre[0];


$rer = sql_query("SELECT invitee, hash, time_invited FROM invites WHERE inviter = ".mysql_real_escape_string($id)) or sqlerr();
$num1 = mysql_num_rows($rer);


print("<table border=1 width=737 cellspacing=0 cellpadding=5>".
"<tr class=tabletitle><td colspan=6><b>Current status of sent out invites</b> ($number1)</td></tr>");

if(!$num1){
print("<tr class=rowhead><td colspan=6>No invitations sent out at the moment.</tr>");
} else {

print("<tr class=rowhead><td><b>Email</b></td><td><b>Hash</b></td><td><b>Send Date</b></td></tr>");
for ($i = 0; $i < $num1; ++$i)
{
  $arr1 = mysql_fetch_assoc($rer);
  print("<tr class=rowhead><td>$arr1[invitee]<td>$arr1[hash]</td><td>$arr1[time_invited]</td></tr>");
}
}
print("</table>");

}
stdfoot();
die;
?>