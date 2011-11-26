<?php
require "include/bittorrent.php";
lang::load('userdetails');
loggedinorreturn();

iplogger();
parked();

$id = 0 + $_GET["id"];
int_check($id,true);
if($id != $CURUSER['id'])
if($usergroups['canviewotherprofile'] != 'yes')
 ug();
$r = @sql_query("SELECT * FROM users WHERE id=".mysql_real_escape_string($id)) or sqlerr(__FILE__, __LINE__);
$user = mysql_fetch_array($r) or bark(str11);
if ($user["status"] == "pending")
	die;
if (!$_GET["hit"] && $CURUSER["id"] <> $user["id"]) {
  $res = mysql_query("SELECT added FROM userhits WHERE userid = $CURUSER[id] AND hitid = $id LIMIT 1") or sqlerr(); // *3
  $row = mysql_fetch_row($res); // *3
  if ($row[0] > get_date_time(gmtime() - 3600)) { // *3
        header("Location: $BASEURL$_SERVER[REQUEST_URI]&hit=1"); // *3
  } else { // *3
//  $hitnumber = $userhits + 1; // *1
        $hitnumber = $user["hits"] + 1; // *2
        mysql_query("UPDATE users SET hits = hits + 1 WHERE id = $id") or sqlerr(); // *2
        mysql_query("INSERT INTO userhits (userid, hitid, number, added) VALUES($CURUSER[id], $id, $hitnumber, '".get_date_time()."')") or sqlerr();
        header("Location: $BASEURL$_SERVER[REQUEST_URI]&hit=1");
  } // *3
}  
$r = sql_query("SELECT id, name, seeders, leechers, category FROM torrents WHERE owner=$id ORDER BY name") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($r) > 0)
{
	$torrents = "<table class=main border=1 cellspacing=0 cellpadding=5>\n" .
	"<tr><td class=colhead>".str12."</td><td class=colhead>".str13."</td><td class=colhead>".str14."</td><td class=colhead>".str15."</td></tr>\n";
	while ($a = mysql_fetch_assoc($r))
	{
		$r2 = sql_query("SELECT name, image FROM categories WHERE id=$a[category]") or sqlerr(__FILE__, __LINE__);
		$a2 = mysql_fetch_assoc($r2);
		$cat = "<img src=\"pic/$a2[image]\" alt=\"$a2[name]\">";
		$torrents .= "<tr><td style='padding: 0px'>$cat</td><td><a href=details.php?id=" . $a["id"] . "&hit=1><b>" . htmlspecialchars($a["name"]) . "</b></a></td>" .
		"<td align=right>$a[seeders]</td><td align=right>$a[leechers]</td></tr>\n";
	}
	$torrents .= "</table>";
}

if ($user["ip"] && (get_user_class() >= UC_MODERATOR || $user["id"] == $CURUSER["id"]))
{
	$r = sql_query("SELECT snatched.torrent_name as name, snatched.torrentid as id, snatched.torrent_category as category, snatched.uploaded, snatched.downloaded, snatched.completedat, snatched.last_action, torrents.seeders, torrents.leechers FROM snatched JOIN torrents ON torrents.id = snatched.torrentid WHERE snatched.finished='yes' AND userid=$id ORDER BY torrent_name") or sqlerr();
	if (mysql_num_rows($r) > 0)
	{
		$completed = "<table class=main border=1 cellspacing=0 cellpadding=3>\n" .
		"<tr><td class=colhead>".str16."</td><td class=colhead>".str17."</td><td class=colhead>".str18."</td><td class=colhead>".str19."</td><td class=colhead>".str20."</td><td class=colhead>".str21."</td><td class=colhead>".str22."</td><td class=colhead>".str23."</td><td class=colhead>".str24."</td></tr>\n";
		while ($a = mysql_fetch_assoc($r))
		{
			$r2 = sql_query("SELECT name, image FROM categories WHERE id=$a[category]") or sqlerr(__FILE__, __LINE__);
			$a2 = mysql_fetch_assoc($r2);

			if ($a["downloaded"] > 0)
			{
				$ratio = number_format($a["uploaded"] / $a["downloaded"], 3);
				$ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
			}
			else
				if ($a["uploaded"] > 0)
					$ratio = "Inf.";
				else
					$ratio = "---";
			$uploaded =mksize($a["uploaded"]);
			$downloaded = mksize($a["downloaded"]);
			$cat = "<img src=\"pic/$a2[image]\" alt=\"$a2[name]\">";
			$completed .= "<tr><td style='padding: 0px'>$cat</td><td><a href=details.php?id=" . $a["id"] . "&hit=1><b>" . htmlspecialchars($a["name"]) . "</b></a></td>" .
			"<td align=right>$a[seeders]</td><td align=right>$a[leechers]</td><td align=right>$uploaded</td><td align=right>$downloaded</td><td align=right>$ratio</td><td align=center>$a[completedat]</td><td align=center>$a[last_action]</td>\n";
		}
		$completed .= "</table>";
	}
	$ip = $user["ip"];
	$dom = @gethostbyaddr($user["ip"]);
	if ($dom == $user["ip"] || @gethostbyname($dom) != $user["ip"])
		$addr = $ip;
	else
	{
		$dom = strtoupper($dom);
		$domparts = explode(".", $dom);
		$domain = $domparts[count($domparts) - 2];
		if ($domain == "COM" || $domain == "CO" || $domain == "NET" || $domain == "NE" || $domain == "ORG" || $domain == "OR" )
			$l = 2;
		else
			$l = 1;
		$addr = "$ip ($dom)";
	}
}
if ($user[added] == "0000-00-00 00:00:00")
	$joindate = 'N/A';
else
	$joindate = "$user[added] (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($user["added"])) . " ago)";
$lastseen = $user["last_access"];
if ($lastseen == "0000-00-00 00:00:00")
	$lastseen = str25;
else
{
	$lastseen .= " (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($lastseen)) . " ago)";
}
  $res = sql_query("SELECT COUNT(*) FROM comments WHERE user=" . $user[id]) or sqlerr();
  $arr3 = mysql_fetch_row($res);
  $torrentcomments = $arr3[0];
  $res = sql_query("SELECT COUNT(*) FROM posts WHERE userid=" . $user[id]) or sqlerr();
  $arr3 = mysql_fetch_row($res);
  $forumposts = $arr3[0];

$res = sql_query("SELECT name FROM clientselect WHERE id=$user[clientselect] LIMIT 1") or sqlerr();

if (mysql_num_rows($res) == 1)
{
	$arr = mysql_fetch_assoc($res);
	$clientselect = "$arr[name]";
}


$res = sql_query("SELECT name,flagpic FROM countries WHERE id=$user[country] LIMIT 1") or sqlerr();
if (mysql_num_rows($res) == 1)
{
	$arr = mysql_fetch_assoc($res);
	$country = "<td class=embedded><img src=pic/flag/$arr[flagpic] alt=\"$arr[name]\" style='margin-left: 8pt'></td>";
}

$res = sql_query("SELECT name FROM downloadspeed WHERE id=$user[download] LIMIT 1") or sqlerr();
if (mysql_num_rows($res) == 1)
{
	$arr = mysql_fetch_assoc($res);
	$download = "<img src=pic/speed_down.png alt=\"".str26.": $arr[name]\" style='margin-left: 8pt'> $arr[name]";
}

$res = sql_query("SELECT name FROM uploadspeed WHERE id=$user[upload] LIMIT 1") or sqlerr();
if (mysql_num_rows($res) == 1)
{
	$arr = mysql_fetch_assoc($res);
	$upload  = "<img src=pic/speed_up.png alt=\"".str27.": $arr[name]\" style='margin-left: 8pt'> $arr[name]";
}

if ($user["gender"] == "Male")
	$gender = "<img src=".$pic_base_url."male.png alt='".str28."' style='margin-left: 4pt'>";
elseif ($user["gender"] == "Female")
	$gender = "<img src=".$pic_base_url."female.png alt='".str29."' style='margin-left: 4pt'>";
elseif ($user["gender"] == "N/A")
	$gender = "<img src=".$pic_base_url."na.gif alt='".str30."' style='margin-left: 4pt'>";

$res = sql_query("SELECT torrent,added,uploaded,downloaded,torrents.name as torrentname,categories.name as catname,size,image,category,seeders,leechers FROM peers LEFT JOIN torrents ON peers.torrent = torrents.id LEFT JOIN categories ON torrents.category = categories.id WHERE userid=$id AND seeder='no'") or sqlerr();
if (mysql_num_rows($res) > 0)
	$leeching = maketable($res);
$res = sql_query("SELECT torrent,added,uploaded,downloaded,torrents.name as torrentname,categories.name as catname,size,image,category,seeders,leechers FROM peers LEFT JOIN torrents ON peers.torrent = torrents.id LEFT JOIN categories ON torrents.category = categories.id WHERE userid=$id AND seeder='yes'") or sqlerr();
if (mysql_num_rows($res) > 0)
	$seeding = maketable($res);

stdhead(str31." " . $user["username"]);
$enabled = $user["enabled"] == 'yes';
print("<p><table class=main border=0 cellspacing=0 cellpadding=0>".
"<tr><td class=embedded><h1 style='margin:0px'>$user[username]" . get_user_icons($user, true) . "</h1></td>$country</tr></table></p>\n");

if (!$enabled)
	print("<p><b>".str32."</b></p>\n");
elseif ($CURUSER["id"] <> $user["id"])
{
	$r = sql_query("SELECT id FROM friends WHERE userid=$CURUSER[id] AND friendid=$id") or sqlerr(__FILE__, __LINE__);
	$friend = mysql_num_rows($r);
	$r = sql_query("SELECT id FROM blocks WHERE userid=$CURUSER[id] AND blockid=$id") or sqlerr(__FILE__, __LINE__);
	$block = mysql_num_rows($r);

	if ($friend)
		print("<p>(<a href=friends.php?action=delete&type=friend&targetid=$id>".str33."</a>)</p>\n");
	elseif($block)
		print("<p>(<a href=friends.php?action=delete&type=block&targetid=$id>".str34."</a>)</p>\n");
	else
	{
		print("<p>(<a href=friends.php?action=add&type=friend&targetid=$id>".str35."</a>)");
		print(" - (<a href=friends.php?action=add&type=block&targetid=$id>".str36."</a>)</p>\n");
	}
}
begin_main_frame('100%');
?>
<table width=100% border=1 cellspacing=0 cellpadding=5>
<?php
if (($user["privacy"] != "strong") OR (get_user_class() >= UC_MODERATOR) OR $CURUSER[id] == $user[id]){
	if ($CURUSER[id] == $user[id] || get_user_class() >= UC_ADMINISTRATOR)
		print("<h2>".str37." <a href=page.php?type=takeflush&id=$CURUSER[id]>".str38."</a></h2>\n");  

if ($CURUSER[id] == $user[id] || get_user_class() >= UC_ADMINISTRATOR)
	if ($user["invites"] <= 0)
		print("<tr><td class=rowhead width=1%>".str39."</td><td align=left width=99%>".str40."</td></tr>\n");
	else
		print("<tr><td class=rowhead width=1%>".str39."</td><td align=left width=99%><a href=invite.php?id=$user[id]>$user[invites]</a></td></tr>\n");
else
	if ($CURUSER[id] != $user[id] || get_user_class() != UC_ADMINISTRATOR)
		if ($user["invites"] <= 0)
			print("<tr><td class=rowhead width=1%>".str39."</td><td align=left width=99%>".str40."</td></tr>\n");
		else
			print("<tr><td class=rowhead width=1%>".str39."</td><td align=left width=99%>$user[invites]</td></tr>\n");
	if ($user["invited_by"] > 0) {
		$get_inveter_name = sql_query("SELECT username FROM users WHERE id = ".sqlesc($user[invited_by])) or sqlerr(__FILE__, __LINE__);
		$inviter_name = mysql_fetch_assoc($get_inveter_name);
		print("<tr><td class=rowhead>".str41."</td><td align=left><a href=userdetails.php?id=$user[invited_by]>$inviter_name[username]</a></td></tr>\n"); 
	}
?>
<tr><td class=rowhead width=1%><?=str42?></td><td align=left width=99%><?=$joindate?></td></tr>
<tr><td class=rowhead><?=str43?></td><td align=left><?=$lastseen?></td>
<?php
if ($where == "yes" AND ur::ismod()) {
?>
	<tr><td class=rowhead><?=str44?></td><td align=left><?=$user["page"];?></td>
	</tr>
<?php
}
if (get_user_class() >= UC_MODERATOR OR $user["privacy"] == "low") {
	print("<tr><td class=rowhead>".str45."</td><td align=left><a href=mailto:$user[email]>$user[email]</a></td></tr>\n");
}
?>
<?php if(!empty($user["icq"]) || !empty($user["msn"]) || !empty($user["aim"]) || !empty($user["yahoo"]) || !empty($user["skype"])) {?>
<tr>
<td class=rowhead><b><?=str46?></b></td><td class=tablea align=left>
<?php
if ($user["icq"])
    print("<img src=$BASEURL/pic/contact/icq.gif  alt=icq border=0 /> $user[icq] <br>\n");
if ($user["msn"])
    print("<img src=$BASEURL/pic/contact/msn.gif alt=msn border=0 /> $user[msn]<br>\n");
if ($user["aim"])
    print("<img src=$BASEURL/pic/contact/aim.gif alt=aim border=0 /> $user[aim]<br>\n");
if ($user["yahoo"])
    print("<img src=$BASEURL/pic/contact/yahoo.gif alt=yahoo border=0 /> $user[yahoo]<br>\n");
if ($user["skype"])
    print("<img src=$BASEURL/pic/contact/skype.gif alt-skype border=0 /> $user[skype]\n");
?>  
</td>
</tr>
<?php } ?>
<?php
if (get_user_class() >= UC_MODERATOR) {
	print("<tr><td class=rowhead>Profile views</td><td align=left><a href=userhits.php?id=$id>".number_format($user["hits"])."</a></td></tr>\n");  
	$ip_res = sql_query("SELECT * FROM ips WHERE userid = $id") or die(mysql_error());

	print("<tr><td class=rowhead>".str47."</td><td align=left>");
	while ($arr = mysql_fetch_assoc($ip_res)) {
		echo " $arr[ip] ::";
	}
	print("</td></tr>\n");

	if ($addr)
		print("<tr><td class=rowhead>".str48."</td><td align=left>$addr</td></tr>\n");

	$resip = sql_query("SELECT ip FROM iplog WHERE userid =$id GROUP BY ip") or sqlerr(__FILE__, __LINE__);
	$iphistory = mysql_num_rows($resip);

	if ($iphistory > 0)
		print("<tr><td class=rowhead>".str49."</td><td align=left>".str50." <b><a href=iphistory.php?id=" . $user['id'] . ">" . $iphistory. " ".str51."</a></b></td></tr>\n");

}
if ($user["clientselect"] > 0)
	print("<tr><td class=rowhead>".str52."</td><td align=left>$clientselect</td></tr>\n");

?>
<tr><td class=rowhead><?=str53?></td><td align=left><?=mksize($user["uploaded"])?></td></tr>
<tr><td class=rowhead><?=str54?></td><td align=left><?=mksize($user["downloaded"])?></td></tr>
<?php

if ($user["downloaded"] > 0)
{
  $sr = $user["uploaded"] / $user["downloaded"];
  if ($sr >= 4)
    $s = "w00t";
  else if ($sr >= 2)
    $s = "grin";
  else if ($sr >= 1)
    $s = "smile1";
  else if ($sr >= 0.5)
    $s = "noexpression";
  else if ($sr >= 0.25)
    $s = "sad";
  else
    $s = "cry";
  $sr = floor($sr * 1000) / 1000;
  $sr = "<table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded><font color=" . get_ratio_color($sr) . ">" . number_format($sr, 3) . "</font></td><td class=embedded>&nbsp;&nbsp;<img src=pic/smilies/$s.gif></td></tr></table>";
  print("<tr><td class=rowhead style='vertical-align: middle'>".str55."</td><td align=left valign=center style='padding-top: 1px; padding-bottom: 0px'>$sr</td></tr>\n");  
}
  if ($user["download"] && $user["upload"])
	print("<tr><td class=rowhead>".str56."</td><td align=left>$download $upload</td></tr>\n");
print("<tr><td class=rowhead>".str57."</td><td align=left>$gender</td></tr>\n");

if ($user['donated'] > 0 && (get_user_class() >= UC_MODERATOR || $CURUSER["id"] == $user["id"]))
  print("<tr><td class=rowhead>".str58."</td><td align=left>$".htmlspecialchars($user[donated])."</td></tr>\n");

if ($user["avatar"])
	print("<tr><td class=rowhead>".str59."</td><td align=left><img src=\"" . htmlspecialchars(trim($user["avatar"])) . "\"></td></tr>\n");
	$uclass = get_user_class_name($user['class']);
print("<tr><td class=rowhead>".str60."</td><td align=left><img src='include/class_image.php?string=$uclass".($user["donated"] > "0" ? '&nr=2' : '')."' /> ".($user[title]!=="" ? "&nbsp;(Title: ".htmlspecialchars(trim($user["title"])).")" : "" )."</td></tr>\n");


print("<tr><td class=rowhead>Torrent&nbsp;comments</td>");
if ($torrentcomments && (($user["class"] >= UC_POWER_USER && $user["id"] == $CURUSER["id"]) || get_user_class() >= UC_MODERATOR))
	print("<td align=left><a href=admin/userhistory.php?action=viewcomments&id=$id>$torrentcomments</a></td></tr>\n");
else
	print("<td align=left>$torrentcomments</td></tr>\n");

print("<tr><td class=rowhead>Forum&nbsp;posts</td>");
if ($forumposts && (($user["class"] >= UC_POWER_USER && $user["id"] == $CURUSER["id"]) || get_user_class() >= UC_MODERATOR)) {
	print("<td align=left><a href=admin/userhistory.php?action=viewposts&id=$id>$forumposts</a></td></tr>\n");	
	print("<tr><td align=right><b>Karma Points:</b></td><td colspan=2 align=left>" . htmlspecialchars($user[seedbonus]) . "</tr>\n");	
}
else
	print("<td align=left>$forumposts</td></tr>\n");	
	
if ($torrents)
print("<tr valign=top><td class=rowhead width=20%>Uploaded Torrents</td><td align=left width=90%><a href=\"javascript: klappe_news('a".$array['id']."')\"><img border=\"0\" src=\"pic/plus.gif\" id=\"pica".$array['id']."\" alt=\"Show/Hide\"></a>   <u>[Show/Hide]</u><div id=\"ka".$array['id']."\" style=\"display: none;\"><p>$torrents</div></td></tr>\n");

if ($seeding)
print("<tr valign=top><td class=rowhead width=20%>Current Seeds</td><td align=left width=90%><a href=\"javascript: klappe_news('a1".$array['id']."')\"><img border=\"0\" src=\"pic/plus.gif\" id=\"pica1".$array['id']."\" alt=\"Show/Hide\"></a>   <u>[Show/Hide]</u><div id=\"ka1".$array['id']."\" style=\"display: none;\"><p>$seeding</div></td></tr>\n");

if ($leeching)
print("<tr valign=top><td class=rowhead width=20%>Current Leechs</td><td align=left width=90%><a href=\"javascript: klappe_news('a2".$array['id']."')\"><img border=\"0\" src=\"pic/plus.gif\" id=\"pica2".$array['id']."\" alt=\"Show/Hide\"></a>   <u>[Show/Hide]</u><div id=\"ka2".$array['id']."\" style=\"display: none;\"><p>$leeching</div></td></tr>\n");

if ($completed)
print("<tr valign=top><td class=rowhead width=20%>Completed Torrents</td><td align=left width=90%><a href=\"javascript: klappe_news('a3".$array['id']."')\"><img border=\"0\" src=\"pic/plus.gif\" id=\"pica3".$array['id']."\" alt=\"Show/Hide\"></a>   <u>[Show/Hide]</u><div id=\"ka3".$array['id']."\" style=\"display: none;\"><p>$completed</div></td></tr>\n");
if ($user["info"])
 print("<tr valign=top><td align=left colspan=2 class=text bgcolor=#F4F4F0>" . format_comment($user["info"]) . "</td></tr>\n");
}else {
	print("<tr valign=top><td align=left colspan=2 class=text bgcolor=#F4F4F0><font color=blue>Sorry, public access denied by $user[username]. He/She want to protect his details.</font></td></tr>\n");
}
if ($CURUSER["id"] != $user["id"])
	if (get_user_class() >= UC_MODERATOR)
  	$showpmbutton = 1;
	elseif ($user["acceptpms"] == "yes")
	{
		$r = sql_query("SELECT id FROM blocks WHERE userid=$user[id] AND blockid=$CURUSER[id]") or sqlerr(__FILE__,__LINE__);
		$showpmbutton = (mysql_num_rows($r) == 1 ? 0 : 1);
	}
	elseif ($user["acceptpms"] == "friends")
	{
		$r = sql_query("SELECT id FROM friends WHERE userid=$user[id] AND friendid=$CURUSER[id]") or sqlerr(__FILE__,__LINE__);
		$showpmbutton = (mysql_num_rows($r) == 1 ? 1 : 0);
	}
print("<tr><td colspan=2 align=center>");
if ($showpmbutton)
	print("<a href=sendmessage.php?receiver=".htmlspecialchars($user[id])."><img src=pic/pm.gif border=0></a>");
		
print("<a href=report.php?user=".htmlspecialchars($user[id])."><img src=pic/report.gif border=0></a>");
$q = sql_query("SELECT minclasstoedit,maxclasstoedit FROM usergroups WHERE id = $user[class]");
$q = mysql_fetch_assoc($q);
if($q['minclasstoedit'] <= get_user_class() AND $q['maxclasstoedit'] >= get_user_class())
echo "<button class=\"btns\" onclick=\"window.location='admin/edituser.php?id=$user[id]'\">Edit</button>";
print("</tr></td>");
print("</table>\n");

end_main_frame();
stdfoot();
?>