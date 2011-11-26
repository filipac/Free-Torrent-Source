<?php

require "include/bittorrent.php";


loggedinorreturn();

parked();
lang::load('viewsnatches');
ob_start("ob_gzhandler");
$id = $_GET["id"];
	int_check($id,true);

stdhead(str1);

begin_main_frame("100%");

$res3 = sql_query("select count(snatched.id) from snatched inner join users on snatched.userid = users.id inner join torrents on snatched.torrentid = torrents.id where snatched.finished='yes'AND snatched.torrentid = ".sqlesc($id)) or die(mysql_error());
$row = mysql_fetch_array($res3);

$count = $row[0];
$perpage = 10;
#list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["SCRIPT_NAME"] . "?id=" . htmlspecialchars($id) . "&" );

$res3 = sql_query("select name from torrents where id = ".sqlesc($id));
$arr3 = mysql_fetch_assoc($res3);
$dt = gmtime() - 180;
$dt = sqlesc(get_date_time($dt));


print("<p align=center>".str3."</p>");

#echo $pagertop;
fancy(str2." <a href=details.php?id=".htmlspecialchars($id)."><b>$arr3[name]</b></a>");
print("<table border=1 cellspacing=0 cellpadding=1 align=center width=100%>\n");
print("<tr><td class=colhead align=center>".str4."</td><td class=colhead align=center>".str5."</td><td class=colhead align=center>".str6."</td><td class=colhead align=center>".str7."</td><td class=colhead align=center>".str8."</td><td class=colhead align=center>".str9."</td><td class=colhead align=center>".str10."</td><td class=colhead align=center>".str11."</td><td class=colhead align=center>".str12."</td><td class=colhead align=center>".str13."</td></tr>");

$res = sql_query("select users.id, users.username, users.title, users.uploaded, users.downloaded, snatched.completedat, snatched. last_action, snatched.seeder, snatched.userid from snatched inner join users on snatched.userid = users.id inner join torrents on snatched.torrentid = torrents.id where snatched.finished='yes' AND snatched.torrentid =" . sqlesc($id) . " ORDER BY snatched.id desc");
$res2 = sql_query("select users.donor, users.enabled, users.warned, users.last_access, snatched.uploaded, snatched.downloaded, snatched.userid from snatched inner join users on snatched.userid = users.id inner join torrents on snatched.torrentid = torrents.id where snatched.finished='yes' AND snatched.torrentid =" . sqlesc($id) . " ORDER BY snatched.id desc");
while ($arr = mysql_fetch_assoc($res))
{
$arr2 = mysql_fetch_assoc($res2);
//start Global
if ($arr["downloaded"] > 0)
{
$ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
$ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
}
else
if ($arr["uploaded"] > 0)
$ratio = "Inf.";
else
$ratio = "---";
$uploaded =mksize($arr["uploaded"]);
$downloaded = mksize($arr["downloaded"]);
//start torrent
if ($arr2["downloaded"] > 0)
{
$ratio2 = number_format($arr2["uploaded"] / $arr2["downloaded"], 3);
$ratio2 = "<font color=" . get_ratio_color($ratio2) . ">$ratio2</font>";
}
else
if ($arr2["uploaded"] > 0)
$ratio2 = "Inf.";
else
$ratio2 = "---";
$uploaded2 =mksize($arr2["uploaded"]);
$downloaded2 = mksize($arr2["downloaded"]);
//end
$highlight = $CURUSER["id"] == $arr["id"] ? " bgcolor=#00A527" : "";

print("<tr$highlight><td align=center><a href=userdetails.php?id=$arr[userid]><b>$arr[username]</b></a></td><td align=center>$uploaded Global<br>$uploaded2 Torrent</td><td align=center>$downloaded Global<br>$downloaded2 Torrent</td><td align=center>$ratio Global<br>$ratio2 Torrent</td><td align=center>$arr[completedat]</td><td align=center>$arr[last_action]</td><td align=center>" . ($arr["seeder"] == "yes" ? "<b><font color=green>".str14."</font>" : "<font color=red>".str15."</font></b>") . "</td>
<td align=center><a href=sendmessage.php?receiver=$arr[userid]><img src=$pic_base_url/pm.gif border=0></a></td><td align=center><a href=report.php?user=$arr[userid]><img border=0 src=$pic_base_url/report.gif></a></td><td align=right>" . get_user_icons($arr2, true) .
"".("'".$arr2['last_access']."'">$dt?"<img src=pic/user_online.gif  border=0 alt=\"Online\">":"<img src=pic/user_offline.gif border=0 alt=\"Offline\">" )."</td>"."
</tr>\n");
}
print("</table></table>\n");

#echo $pagerbottom;

stdfoot();

?>