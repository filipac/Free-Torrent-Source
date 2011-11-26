<?php
require "include/bittorrent.php";
loggedinorreturn();

if (get_user_class() < UC_MODERATOR)
stderr("Error", "No Access");


$userid = 0 + $_GET["id"];
if (!is_valid_id($userid)) stderr("Error", "Invalid ID");

$res = sql_query("SELECT username FROM users WHERE id = $userid") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) == 0)
stderr("Error", "User not found");

$arr = mysql_fetch_array($res);
$username = $arr["username"];

$page = 0 + $_GET["page"];
$perpage = 20;

$countrows = number_format(get_row_count("iplog", "WHERE userid =$userid")) + 1;
$order = $_GET['order'];

list($pagertop, $pagerbottom, $limit) = pager($perpage, $countrows, "iphistory.php?id=$userid&order=$order&");


if ($order == "ip")
$orderby = "ip DESC, access";
else
$orderby = "access DESC";

$query = "SELECT u.id, u.ip AS ip, last_access AS access FROM users as u WHERE u.id = $userid
UNION SELECT u.id, iplog.ip as ip, iplog.access as access FROM users AS u
RIGHT JOIN iplog on u.id = iplog.userid WHERE u.id = $userid ORDER BY $orderby $limit";

$res = sql_query($query) or sqlerr(__FILE__, __LINE__);

stdhead("IP History Log for $username");
begin_main_frame();

begin_frame("Historical IP addresses used by <a href=userdetails.php?id=$userid><b>$username</b></a>", True);

if ($countrows > $perpage)
echo $pagertop;

begin_table();
print("<tr>\n
<td class=colhead><a class=colhead href=\"" . $_SERVER['PHP_SELF'] . "?id=$userid&order=access\">Last access</a></td>\n
<td class=colhead><a class=colhead href=\"" . $_SERVER['PHP_SELF'] . "?id=$userid&order=ip\">IP</a></td>\n
<td class=colhead>Hostname</td>\n
</tr>\n");
while ($arr = mysql_fetch_array($res))
{
$addr = "";
$ipshow = "";
if ($arr["ip"])
{
$ip = $arr["ip"];
$dom = @gethostbyaddr($arr["ip"]);
if ($dom == $arr["ip"] || @gethostbyname($dom) != $arr["ip"])
$addr = "";
else
$addr = $dom;

$queryc = "SELECT COUNT(*) FROM
(
SELECT u.id FROM users AS u WHERE u.ip = " . sqlesc($ip) . "
UNION SELECT u.id FROM users AS u RIGHT JOIN iplog ON u.id = iplog.userid WHERE iplog.ip = " . sqlesc($ip) . "
GROUP BY u.id
) AS ipsearch";
$resip = sql_query($queryc) or sqlerr(__FILE__, __LINE__);
$arrip = mysql_fetch_row($resip);
$ipcount = $arrip[0];

$nip = ip2long($ip);
$banres = sql_query("SELECT COUNT(*) FROM bans WHERE $nip >= first AND $nip <= last") or sqlerr(__FILE__, __LINE__);
$banarr = mysql_fetch_row($banres);
if ($banarr[0] == 0)
if ($ipcount > 1)
$ipshow = "<b><a href=ipsearch.php?ip=". $arr['ip'] .">" . $arr['ip'] ."</a></b>";
else
$ipshow = "<a href=ipsearch.php?ip=". $arr['ip'] .">" . $arr['ip'] ."</a>";
else
$ipshow = "<a href='/testip.php?ip=" . $arr['ip'] . "'><font color='#FF0000'><b>" . $arr['ip'] . "</b></font></a>";
}
$date = display_date_time(sql_timestamp_to_unix_timestamp($arr["access"]) , $CURUSER[tzoffset] );
print("<tr><td>$date</td>\n");
print("<td>$ipshow</td>\n");
print("<td>$addr</td>\n");
}

end_table();

if ($countrows > $perpage)
echo $pagerbottom;

end_frame();


end_main_frame();
stdfoot();
die;
?>