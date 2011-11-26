<?php
ob_start("ob_gzhandler");
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;



/* CONFIG */
$maxratio = 0.3; // Maximum ratio to list. Standard = 0.4.
$mindownload = 10737418240; // "Downloaded more than". Standard = 10 GB.
$deleteclass = UC_ADMINISTRATOR; // Minimum and equal class that can delete users.
$drsource = false; // Set to true if you use DR-source. False if you use Official source. Setting is for PM's.
/* END CONFIG */

ADMIN::check();

if ($_GET['action'] == ""){

if ($_GET['godcomplex'] == "yes")
{
foreach ( $_POST as $key => $ertek )
{
if ( (strpos($key,'cb_') != 0) or ($ertek == -1) ) continue;
else
{
$username=substr($key,3);
}

if ($_POST['warn']){
$req="UPDATE users SET warned = 'yes', warneduntil = DATE_ADD(NOW(), INTERVAL ".(2 * 7)." DAY) WHERE id = '$ertek'";
$res=sql_query($req);
} else if ($_POST['delete'] && get_user_class() > $deleteclass)
{
$req="DELETE FROM users WHERE id = '$ertek'";
$res=sql_query($req);
// write_log("User $username was deleted by $CURUSER[username]");
}
if ($res == ''){print("<script language=\"javascript\">alert('No users Warned or Deleted!');</script>");}
}
}




stdhead("Ratio Under $maxratio/$mindownload byte Downloaded");
$mainquery = "SELECT id as userid, username, added, uploaded, downloaded, warned, uploaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS upspeed, downloaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS downspeed FROM users WHERE enabled = 'yes'";

$limit = 250;
$order = "added ASC";
$extrawhere = " AND uploaded / downloaded < $maxratio AND downloaded > $mindownload";
$r = sql_query($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
print("<a href=?action=sendpm>Send Mass PM to All Low Ratio Users</a>");
$mindownloadprint = mksize ($mindownload);
usertable_leechers($r, "Ratio Under $maxratio / $mindownloadprint Downloaded");
print("<a href=\"?select=all\">Select all</a> | <a href=\"?select=none\">Select none</a>");
print("<br /><input type=\"submit\" name=\"warn\" value=\"Warn selected\" onclick=\"return confirm('Warn all selected users?');\" />");
if( get_user_class() > $deleteclass ){
print("<input type=\"submit\" name=\"delete\" value=\"Delete selected\" onclick=\"return confirm('Are you bloody sure you want to delete all these users!?');\" />");
}
print("</form>");

$getlog = sql_query("SELECT id, user, date, UNIX_TIMESTAMP(date) as utadded FROM leecherspmlog LIMIT 10");
print("<br /><p>Leecher PM-Log.</p><p>");
print("<table border=1 cellspacing=0 cellpadding=5>\n");
print("<tr><td class=colhead>By User</td><td class=colhead>Date</td><td class=colhead>elapsed</td></tr>");
while($arr2 = mysql_fetch_assoc($getlog)){
$r2 = sql_query("SELECT username FROM users WHERE id=$arr2[user]") or sqlerr();
$a2 = mysql_fetch_assoc($r2);
$elapsed = get_elapsed_time($arr2["utadded"]);
print("<tr><td class=colhead><a href=$BASEURL/userdetails.php?id=$arr2[user]>$a2[username]</a></td><td class=colhead>$arr2[date]</td><td>$elapsed ago</td></tr>");
}
print("</table>");

}

if ($_GET['taking'] == "takepm"){
if ($HTTP_SERVER_VARS["REQUEST_METHOD"] == "POST"){
$dt = sqlesc(get_date_time());
$msg = $_POST['msg'];
if (!$msg)
stderr("Error","Please Type In Some Text");

$query = "SELECT id as userid, username, added, uploaded, downloaded, warned, uploaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS upspeed, downloaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS downspeed FROM users WHERE enabled = 'yes'";
$limit = 250;
$order = "added ASC";
//$order = "uploaded / downloaded ASC, downloaded DESC";
$extrawhere = " AND uploaded / downloaded < $maxratio AND downloaded > $mindownload";
$r = sql_query($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit") or sqlerr();

while($dat=mysql_fetch_assoc($r)){

if($drsource == "true"){
new_msg(0, $dat[userid], $msg); /* DR Source */
}else{
sql_query("INSERT INTO messages (sender, receiver, added, msg) VALUES (0,$dat[userid] , '" . get_date_time() . "', " . sqlesc($msg) .")") or sqlerr(__FILE__,__LINE__); /* Official Source */
}
}
sql_query("INSERT INTO leecherspmlog ( user , date ) VALUES ( $CURUSER[id], $dt)") or sqlerr(__FILE__,__LINE__);
header("Refresh: 0; url=leechers.php");


}
}

if ($_GET['action'] == "sendpm") {
stdhead("Users that are bad");
?>
<table class="main" width="737" border="0" cellspacing="0" cellpadding="0"><tr><td class="embedded">
<div align="center">
<h1>Mass Message to All Bad Users</a></h1>
<form method="post" action="leechers.php?taking=takepm">
<?php

if ($_GET["returnto"] || $_SERVER["HTTP_REFERER"])
{
?>
<input type=hidden name=returnto value="<?=htmlentities($_GET["returnto"]) ? htmlentities($_GET["returnto"]) : htmlentities($_SERVER["HTTP_REFERER"])?>">
<?php
}
//default message
$body = "You have been warned due to low ratio. You have two weeks to improve it. If you dont, you will be banned. Always check the needseed-function for a ratio-boost!

This is a system generated message. Account deletion is also system controlled, so get to it! wink.gif";
?>
<table cellspacing=0 cellpadding=5>
<tr>
<td>Send Mass Messege To All Bad Users<br>
<table style="border: 0" width="100%" cellpadding="0" cellspacing="0">
<tr>
<td style="border: 0">&nbsp;</td>
<td style="border: 0">&nbsp;</td>
</tr>
</table>
</td>
</tr>
<tr><td><textarea name=msg cols=120 rows=15><?=$body?></textarea></td></tr>
<tr>
<tr><td colspan=2 align=center><input type="submit" value="Send" class="btn"></td></tr>
</table>
<input type="hidden" name="receiver" value=<?=$receiver?>>
</form>

</div></td></tr></table>
<br>
NOTE: No HTML Code Allowed. (NO HTML)
<?php
}
stdfoot();
?>