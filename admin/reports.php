<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;



ADMIN::check();

parked();
ob_start("ob_gzhandler");


stdhead("Active Reports"); 
begin_main_frame('100%'); 
// =================================== 
begin_frame("Active Reports", true, '10', '100%'); 
begin_table(true);


$type = $_GET["type"];
if ($type == "user")
$where = " WHERE type = 'user'";
else if ($type == "torrent")
$where = " WHERE type = 'torrent'";
else if ($type == "forum")
$where = " WHERE type = 'forum'";
else if ($type == "comment")
$where = " WHERE type = 'comment'";
else
$where = "";

$res = sql_query("SELECT count(id) FROM reports $where") or die(mysql_error());
$row = mysql_fetch_array($res);

$count = $row[0];
$perpage = 225;
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] . "?type=" . $_GET["type"] . "&" );

print("<h1 align=center>Reports</h1>");

//echo $pagertop;

print("<table border=1 cellspacing=0 cellpadding=5 align=center width=100%>\n");
print("<tr><td class=colhead align=center>Added</td><td class=colhead align=center>Reported by</td><td class=colhead align=center>Reporting</td><td class=colhead align=center>Type</td><td class=colhead align=center>Reason</td><td class=colhead align=center>Dealt With</td><td class=colhead align=center>Dealt With</td>");

print("<form method=post action=takeupdate.php>");
$res = sql_query("SELECT reports.id, reports.dealtwith, reports.dealtby, reports.addedby, reports.votedfor, reports.votedfor_xtra, reports.reason, reports.type, reports.added, users.username FROM reports INNER JOIN users on reports.addedby = users.id $where ORDER BY id desc $limit");

while ($arr = mysql_fetch_assoc($res))
{
if ($arr[dealtwith])
{
$res3 = sql_query("SELECT username FROM users WHERE id=$arr[dealtby]");
$arr3 = mysql_fetch_assoc($res3);
$dealtwith = "<font color=green><b>Yes - <a href=userdetails.php?id=$arr[dealtby]><b>$arr3[username]</b></a></b></font>";
}
else
$dealtwith = "<font color=red><b>No</b></font>";
if ($arr[type] == "user")
{
$type = "userdetails";
$res2 = sql_query("SELECT username FROM users WHERE id=$arr[votedfor]");
$arr2 = mysql_fetch_assoc($res2);
$name = $arr2[username];
}
else if  ($arr[type] == "forum")
{
$type = "forums";
$res2 = sql_query("SELECT subject FROM topics WHERE id=$arr[votedfor]");
$arr2 = mysql_fetch_assoc($res2);
$subject = $arr2[subject];
}
else if ($arr[type] == "torrent")
{
$type = "details";
$res2 = sql_query("SELECT name FROM torrents WHERE id=$arr[votedfor]");
$arr2 = mysql_fetch_assoc($res2);
$name = $arr2[name];
if ($name == "")
$name = "<b>[Deleted]</b>";
}
else if  ($arr[type] == "comment")
{
$type = "details";
$res2 = sql_query("SELECT torrent, user FROM comments WHERE id=$arr[votedfor]");
$arr2 = mysql_fetch_assoc($res2);

$torrent = $arr2["torrent"];
$user_id = $arr2["user"];

$res_tn = sql_query("SELECT name FROM torrents WHERE id=$torrent");
$arr_tn = mysql_fetch_assoc($res_tn);

$torrent_name = $arr_tn[name];

$res_usr = sql_query("SELECT username FROM users WHERE id=$user_id");
$arr_usr = mysql_fetch_assoc($res_usr);

$comment_username=$arr_usr[username];
}

if ($arr[type] == "forum")
  { 
print("<tr><td>$arr[added]</td><td><a href=userdetails.php?id=$arr[addedby]><b>$arr[username]</b></a></td><td align=left><a href=$type.php?action=viewtopic&topicid=$arr[votedfor]&page=p#$arr[votedfor_xtra]><b>$subject</b></a></td><td align=left>$arr[type]</td><td align=left>$arr[reason]</td><td align=left>$dealtwith</td><td><input type=\"checkbox\" name=\"delreport[]\" value=\"" . $arr[id] . "\" /></td></tr>\n");
  
}
else if ($arr[type] == "comment")
{
print("<tr><td>$arr[added]</td><td><a href=userdetails.php?id=$arr[addedby]><b>$arr[username]</b></a></td><td align=left><a href=$type.php?id=$torrent&viewcomm=$arr[votedfor]#comm$arr[votedfor]><b>$comment_username</b></a></td><td align=left>$arr[type]</td><td align=left>$arr[reason]</td><td align=left>$dealtwith</td><td><input type=\"checkbox\" name=\"delreport[]\" value=\"" . $arr[id] . "\" /></td></tr>\n");
}
else {
print("<tr><td>$arr[added]</td><td><a href=userdetails.php?id=$arr[addedby]><b>$arr[username]</b></a></td><td align=left><a href=$type.php?id=$arr[votedfor]><b>$name</b></a></td><td align=left>$arr[type]</td><td align=left>$arr[reason]</td><td align=left>$dealtwith</td><td><input type=\"checkbox\" name=\"delreport[]\" value=\"" . $arr[id] . "\" /></td></tr>\n");
}
}
?> 
<tr><td colspan="7" align="right"><input type="submit" value="Confirm!" /></td></tr> 
</form> 
<?php

print("<tr><td align=center colspan=7><form method=\"get\" action=\"delreports.php#add\"><input type=\"submit\" value=\"Delete Reports\" style='height: 18px' /></form></td></tr>\n");

end_main_frame(); 
echo'</table></table>';
stdfoot(); 

?>