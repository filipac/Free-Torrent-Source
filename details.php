<?php
ob_start("ob_gzhandler");
require_once("include/bittorrent.php");
loggedinorreturn();
$wherethisuser = where ($_SERVER["SCRIPT_FILENAME"],$CURUSER["id"]);

$id = 0 + $_GET["id"];

if (!isset($id) || !$id)
	die();

$res = sql_query("SELECT torrents.seeders, torrents.banned,".(_youtube_mod_ == 'yes' ? " torrents.tube," : "")." torrents.leechers, torrents.imageurl, torrents.filename, LENGTH(torrents.nfo) AS nfosz, UNIX_TIMESTAMP() - UNIX_TIMESTAMP(torrents.last_action) AS lastseed, torrents.numratings, torrents.name, IF(torrents.numratings < $minvotes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.numfiles, torrents.anonymous, categories.name AS cat_name, users.username FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id")
	or sqlerr();
$row = mysql_fetch_array($res);

$owned = $moderator = 0;
	if (get_user_class() >= UC_MODERATOR)
		$owned = $moderator = 1;
	elseif ($CURUSER["id"] == $row["owner"])
		$owned = 1;
//}

if (!$row || ($row["banned"] == "yes" && !$moderator)) {
	if(isset($_SERVER["HTTP_REFERER"]))
	redirect($_SERVER["HTTP_REFERER"],"No torrent with this ID","Error",3,0,0);
	else	
	redirect("browse.php","No torrent with this ID","Error",3,0,0);
}else {
	if ($_GET["hit"]) {
		sql_query("UPDATE torrents SET views = views + 1 WHERE id = $id");
		if ($_GET["tocomm"])
			header("Location: $BASEURL/details.php?id=$id&page=0#startcomments");
		elseif ($_GET["filelist"])
			header("Location: $BASEURL/details.php?id=$id&filelist=1#filelist");
		elseif ($_GET["toseeders"])
			header("Location: $BASEURL/details.php?id=$id&dllist=1#seeders");
		elseif ($_GET["todlers"])
			header("Location: $BASEURL/details.php?id=$id&dllist=1#leechers");
		else
			header("Location: $BASEURL/details.php?id=$id");
		exit();
	}

	if (!isset($_GET["page"])) {
		stdhead("Details for torrent \"" . $row["name"] . "\"");

		if ($CURUSER["id"] == $row["owner"] || get_user_class() >= UC_MODERATOR)
			$owned = 1;
		else
			$owned = 0;

		$spacer = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

		if ($_GET["uploaded"]) {
			print("<h2>Successfully uploaded!</h2>\n");
			print("<p>You can start seeding now. <b>Note</b> that the torrent won't be visible until you do that!</p>\n");
		}
		elseif ($_GET["edited"]) {
			print("<h2>Successfully edited!</h2>\n");
			if (isset($_GET["returnto"]))
				print("<p><b>Go back to <a href=\"" . htmlspecialchars($_GET["returnto"]) . "\">whence you came</a>.</b></p>\n");
		}
		elseif (isset($_GET["searched"])) {
			print("<h2>Your search for \"" . htmlspecialchars($_GET["searched"]) . "\" gave a single result:</h2>\n");
		}
		elseif ($_GET["rated"])
 print("<h2>Rating added!</h2>\n");
 
elseif ($_GET["thanks"]){

$userid = $CURUSER["id"];
$torrentid = $id;
$tsql = sql_query("SELECT COUNT(*) FROM thanks where torrentid=$torrentid and userid=$userid");
$trows = mysql_fetch_array($tsql);
$t_ab = $trows[0];
if ($t_ab >= "1")
{
print("<h3>Not a good idea! Thanks added already!</h3>\n");
}
else
{
$res = sql_query("INSERT INTO thanks (torrentid, userid) VALUES ($torrentid, $userid)");
//===add karma
UserHandle::KPS("+","3.0",$CURUSER["id"]);
//===end

print("<h3>Thanks added!</h3>\n");
}

} //===end thanks

$s=$row["name"];
		fancy($s);
                print("<table width=100% border=\"1\" cellspacing=\"0\" cellpadding=\"5\">\n");

		$url = "edit.php?id=" . $row["id"];
		if (isset($_GET["returnto"])) {
			$addthis = "&returnto=" . urlencode($_GET["returnto"]);
			$url .= $addthis;
			$keepget .= $addthis;
		}
		$editlink = "a href=\"$url\" class=\"sublink\"";

//		$s = "<b>" . htmlspecialchars($row["name"]) . "</b>";
//		if ($owned)
//			$s .= " $spacer<$editlink>[Edit torrent]</a>";
//		tr("Name", $s, 1);
	if ($CURUSER["id"] == $row["owner"])
		$CURUSER["downloadpos"] = "yes";
	global $usergroups;
	if ($CURUSER["downloadpos"] != "no" && $usergroups['candwd'] != 'no')
	{
		$ratio = (($CURUSER["downloaded"] > 0) ? ($CURUSER["uploaded"] / $CURUSER["downloaded"]) : 0);		
		$percentage = ($ratio * 100);
		print("<tr><td class=rowhead width=1%>Download</td><td width=99% align=left>");
		if (get_user_class() >= UC_VIP)
			print("<a class=\"index\" href=\"download.php?id=$id&name=" . rawurlencode($row["filename"]) . "\">" . htmlspecialchars($row["filename"]) . "</a>");
		else {
			$usid = (int)$CURUSER["id"];
			$rs = mysql_query("SELECT * FROM users WHERE id='$usid'") or sqlerr();
			$ar = mysql_fetch_assoc($rs);
			$gigs = $ar["downloaded"] / (1024*1024*1024);
			if (($gigs > "4")&&($ratio <= 0.4 and (!$owned|| 0) and ($CURUSER["downloaded"] <> 0)))
			{
				print("<p align=\"center\">");
				print("<font color=#ff0532><b><u>Download Privileges Removed Please Restart A Old Torrent To Improve Your Ratio!!</font><border=\"1\" cellpadding=\"10\" cellspacing=\"10\"></u></b>");
				print("<p><font color=#ff0532><b>Your ratio is ".number_format($ratio, 3)."</b></font> - meaning that you have only uploaded ");
				print(number_format($percentage, 3)." % ");
				print("of the amount you downloaded<p>It's important to maintain a good ");
				print("ratio because it helps to make downloads faster for all members </p>");
				print("<p><font color=#ff0532><b>Tip: </b></font>You can improve your ratio by leaving your torrent ");
				print("running after the download completes.<p>You must maintain a minimum ");
				print("ratio of <b>0.</b> or your download privileges will be removed<p align=\"center\">");            
				print("</td></tr>");
			}
			else
				if ($ratio <= 0.6 and (!$owned|| 0) and ($CURUSER["downloaded"] <> 0))
				{
					print("<p align=\"center\">");
					print("<font color=#ff0532><b><u>PAY ATTENTION TO YOUR RATIO</font><border=\"1\" cellpadding=\"10\" cellspacing=\"10\"></u></b>");
					print("<p><font color=#ff0532>Your ratio is ".number_format($ratio, 3)."</font> - meaning that you have only <font color=#ff0532>uploaded ");
					print(number_format($percentage, 3)." % </font>");
					print("of the amount you downloaded.<p>It's important to maintain a good ");
					print("ratio because it helps to make downloads faster for all members.</p>");
					print("<p><font color=#ff0532><b>Tip: </b></font>You can improve your ratio by leaving your torrent ");
					print("running after the download completes.<p>You must maintain a minimum ");
					print(" ratio of <b>0.4</b> or your download privileges will be removed.<p align=\"center\">");
					print("<a class=\"index\" href=\"download.php?id=$id&name=" . rawurlencode($row["filename"]) . "\">");
					print("<font color=#ff0532>> Click here to continue with your download <</a></font>");
					print("</td></tr>");
				}
				else
					print("<a class=\"index\" href=\"download.php?id=$id&name=" . rawurlencode($row["filename"]) . "\">" . htmlspecialchars($row["filename"]) . "</a>");
		}
	}else
		tr("Download", "You are not allowed to download.");

$descrip = $row["descr"];
if(!empty($row['imageurl']))
$descrip = "[img]$row[imageurl][/img]\n".$descrip;

	if (!empty($descrip))
			tr("Description", str_replace(array("\n", "  "), array("\n", "&nbsp; "), format_comment($descrip)), 1, null, null, ($moderator ? "id=\"descrTD\" style=\"cursor:default\" ondblclick=\"sndReq('action=edit_torrent_descr&torrent=".$_GET['id']."', 'descrTD')\"" : ""));
if (get_user_class() >= UC_POWER_USER && $row["nfosz"] > 0)
  print("<tr><td class=rowhead>NFO</td><td align=left><a href=viewnfo.php?id=$row[id]><b>View NFO</b></a> (" .
     mksize($row["nfosz"]) . ")</td></tr>\n");
     if(_youtube_mod_ == 'yes'):
     $ytl = str_replace("watch?v=", "v/", htmlspecialchars($row["tube"]));
     if (!empty($row["tube"]))
tr("Sample:", "<object width=\"425\" height=\"344\"><param name=\"movie\" value=\"$ytl&hl=en\"></param><embed src=\"$ytl&hl=en\" type=\"application/x-shockwave-flash\" width=\"425\" height=\"344\"></embed></object>", 1);
     endif;
		if ($row["visible"] == "no")
			tr("Visible", "<b>no</b> (dead)", 1);
       if ($moderator){
           tr("Banned", "<span id=banned>".$row["banned"].'</span>', 1,null,null,($moderator ? "id=\"bannedChange\" style=\"cursor:default\" ondblclick=\"sndReq('action=change_banned_torrent&torrent=".$_GET['id']."', 'bannedChange')\"" : ""));
       }
       if (isset($row["cat_name"])){
           tr("Type", $row["cat_name"], 0,null, null,( $moderator ? "id=\"typeChange\" style=\"cursor:default\" ondblclick=\"sndReq('action=change_type_torrent&torrent=".$_GET['id']."', 'typeChange')\"" : "" ) );
       } else{
           tr("Type", "(none selected)");
       }

		tr("Last&nbsp;seeder", "Last activity " . mkprettytime($row["lastseed"]) . " ago");
		tr("Size",mksize($row["size"]) . " (" . number_format($row["size"]) . " bytes)");
		$s = "";
		$s .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td valign=\"top\" class=embedded>";
		if (!isset($row["rating"])) {
			if ($minvotes > 1) {
				$s .= "none yet (needs at least $minvotes votes and has got ";
				if ($row["numratings"])
					$s .= "only " . $row["numratings"];
				else
					$s .= "none";
				$s .= ")";
			}
			else
				$s .= "No votes yet";
		}
		else {
			$rpic = ratingpic($row["rating"]);
			if (!isset($rpic))
				$s .= "invalid?";
			else
				$s .= "$rpic (" . $row["rating"] . " out of 5 with " . $row["numratings"] . " vote(s) total)";
		}
		$s .= "\n";
		$s .= "</td><td class=embedded>$spacer</td><td valign=\"top\" class=embedded>";
		if (!isset($CURUSER))
			$s .= "(<a href=\"login.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&nowarn=1\">Log in</a> to rate it)";
		else {
			$ratings = array(
					5 => "Kewl!",
					4 => "Pretty good",
					3 => "Decent",
					2 => "Pretty bad",
					1 => "Sucks!",
			);
			if (!$owned || $moderator) {
				$xres = sql_query("SELECT rating, added FROM ratings WHERE torrent = $id AND user = ".mysql_real_escape_string($CURUSER["id"])) or sqlerr(__FILE__, __LINE__);
				$xrow = mysql_fetch_array($xres);
				if ($xrow)
					$s .= "(you rated this torrent as \"" . $xrow["rating"] . " - " . $ratings[$xrow["rating"]] . "\")";
				else {
					ftsmenu2();
					$s .= '<span id="src_parent">Rate torrent
</span><div class=sample_attach id=src_child>';
					$s .= "<form method=\"post\" action=\"takerate.php\"><input type=\"hidden\" name=\"id\" value=\"$id\" />\n";
					$s .= "<select name=\"rating\">\n";
					$s .= "<option value=\"0\">(add rating)</option>\n";
					foreach ($ratings as $k => $v) {
						$s .= "<option value=\"$k\">$k - $v</option>\n";
					}
					$s .= "</select>\n";
					$s .= "<input class=button type=\"submit\" value=\"Vote!\" />";
					$s .= "</form>\n";
					$s .= '</div>
<script type="text/javascript">
at_attach("src_parent", "src_child", "click", "x", "pointer");
</script>';
				}
			}
		}
		$s .= "</td></tr></table>";
		
		tr("Rating", $s, 1);

		tr("Added", $row["added"]);
		tr("Views", $row["views"]);
		tr("Hits", $row["hits"]);
				print("<tr><td class=rowhead>Snatched</td><td align=left><a href=viewsnatches.php?id=$id><b>".$row["times_completed"]." x</b> time(s)</a> <--- Click Here to  all View Snatches </td></tr>\n");

global $tproghack;
if($tproghack == 'yes') {
//---------
// Progress Bar
//-------------

$seedersProgressbar = array();
$leechersProgressbar = array();
$resProgressbar = sql_query("SELECT p.seeder, p.to_go, t.size FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE  p.torrent = '$id'") or sqlerr();
$progressPerTorrent = 0;
$iProgressbar = 0;
while ($rowProgressbar = mysql_fetch_array($resProgressbar)) {
 $progressPerTorrent += sprintf("%.2f", 100 * (1 - ($rowProgressbar["to_go"] / $rowProgressbar["size"])));    
 $iProgressbar++;
}
if ($iProgressbar == 0)
$iProgressbar = 1;
$progressTotal = sprintf("%.2f", $progressPerTorrent / $iProgressbar);
tr("Progress", get_percent_completed_image(floor($progressTotal))." (".round($progressTotal)."%)", 1);

//---------
// END Progress Bar
//-----------------	
	}
		$keepget = "";
if($row['anonymous'] == 'yes') {

if (get_user_class() < UC_UPLOADER)
$uprow = "<i>Anonymous</i>";
else
$uprow = "<i>Anonymous</i> (<a href=userdetails.php?id=$row[owner]><b>$row[username]</b></a>)";
}
else {
$a = sql_query("SELECT class FROM users WHERE id = '$row[owner]' LIMIT 1");
$class = mysql_fetch_assoc($a);
$uprow = (isset($row["username"]) ? ("<a href=userdetails.php?id=" . $row["owner"] . "><b>" . get_style($class['class'],$row["username"]) . "</b></a>") : "<i>(unknown)</i>");
}
if ($owned)
			$uprow .= " $spacer<$editlink><b>[Edit this torrent]</b></a>";
		tr("Upped by", $uprow, 1);

tr("Report<br />Torrent", "Click <a href=report.php?torrent=$id><b><font color=#ff0532>here</font></b></a> to report this torrent to staff for violation of the rules", 1);

	#	if ($row["type"] == "multi") {
			if (!$_GET["filelist"])
				tr("Num files<br /><a href=\"details.php?id=$id&filelist=1$keepget#filelist\" class=\"sublink\">[See full list]</a>", $row["numfiles"] . " files", 1);
			else {
				tr("Num files", $row["numfiles"] . " files", 1);

				$s = "<table class=main border=\"1\" cellspacing=0 cellpadding=\"5\" width=\"100%\">\n";

				$subres = sql_query("SELECT * FROM files WHERE torrent = $id ORDER BY id");
$s.="<tr><td class=colhead>Path</td><td class=colhead align=right>Size</td></tr>\n";
				while ($subrow = mysql_fetch_array($subres)) {
					$s .= "<tr><td>" . $subrow["filename"] .
                            "</td><td align=\"right\">" . mksize($subrow["size"]) . "</td></tr>\n";
				}

				$s .= "</table>\n";
				tr("<a name=\"filelist\">File list</a><br /><a href=\"details.php?id=$id$keepget\" class=\"sublink\">[Hide list]</a>", $s, 1);
			}
	#	}

		if (!$_GET["dllist"]) {

			if ($row["seeders"] == 0) {
				tr("Ask for a reseed", "Click <a href=takereseed.php?reseedid=$id&owner=$row[owner]><b>here</b></a>.", 1);
			}
			tr("Peers<br /><a href=\"details.php?id=$id&dllist=1$keepget#seeders\" class=\"sublink\">[See full list]</a>", $row["seeders"] . " seeder(s), " . $row["leechers"] . " leecher(s) = " . ($row["seeders"] + $row["leechers"]) . " peer(s) total", 1);
		}
		else {
			$downloaders = array();
			$seeders = array();
			$subres = sql_query("SELECT seeder, finishedat, downloadoffset, uploadoffset, ip, port, uploaded, downloaded, to_go, UNIX_TIMESTAMP(started) AS st, connectable, agent, peer_id, UNIX_TIMESTAMP(last_action) AS la, userid FROM peers WHERE torrent = $id") or sqlerr();
			while ($subrow = mysql_fetch_array($subres)) {
				if ($subrow["seeder"] == "yes")
					$seeders[] = $subrow;
				else
					$downloaders[] = $subrow;
			}



			usort($seeders, "seed_sort");
			usort($downloaders, "leech_sort");

			tr("<a name=\"seeders\">Seeders</a><br /><a href=\"details.php?id=$id$keepget\" class=\"sublink\">[Hide list]</a>", dltable("Seeder(s)", $seeders, $row), 1);
			tr("<a name=\"leechers\">Leechers</a><br /><a href=\"details.php?id=$id$keepget\" class=\"sublink\">[Hide list]</a>", dltable("Leecher(s)", $downloaders, $row), 1);
			
			
		}
$torrentid = $_GET["id"];
         $thanks_sql = sql_query("SELECT * FROM thanks where torrentid=$torrentid");
    $thanks_all = mysql_numrows($thanks_sql);
    if ($thanks_all) {
    while($rows_t = mysql_fetch_array($thanks_sql)) {
    $thanks_userid = $rows_t["userid"];
    $user_sql = sql_query("SELECT * FROM users where id=$thanks_userid");
    $rows_a = mysql_fetch_array($user_sql);
    $username_t = $rows_a["username"];
    $thanksby =  $thanksby."<a href='userdetails.php?id=$thanks_userid'>$username_t</a>, ";
    }
    $t_userid = $CURUSER["id"];
    $tsql = sql_query("SELECT COUNT(*) FROM thanks where torrentid=$torrentid and userid=$t_userid");
    $trows = mysql_fetch_array($tsql);
    $t_ab = $trows[0];
    global $usergroups;
    	$canth = $usergroups['canth'] == 'no' ? 'disabled' : '';
    if ($t_ab == "0") {
    $thanksby = " <p align=right><form action=\"details.php?id=$torrentid&thanks=1\" method=\"post\">
<input class=button type=\"submit\" name=\"submit\" value=\"Say Thanks!\" $canth>
<input type=\"hidden\" name=\"torrentid\" value=\"$torrentid\">
</form></p>".$thanksby;
    }
    else {
    $thanksby = " <p align=right><form action=\"page.php\" method=\"post\"><input type=hidden name=type value=thanks>
<input class=button type=\"submit\" name=\"submit\" value=\"You Said Thanks!\" disabled>
<input type=\"hidden\" name=\"torrentid\" value=\"$torrentid\">
</form></p>".$thanksby;
    }
    }
    else {
    $thanksby = "
    <p align=right><form action=\"details.php?id=$torrentid&thanks=1\" method=\"post\">
<input class=button type=\"submit\" name=\"submit\" value=\"Say Thanks!\" $canth>
<input type=\"hidden\" name=\"torrentid\" value=\"$torrentid\">
</form></p>no thanks added yet
    ";
    }
        tr("Thanks by:",$thanksby,1);
        if (ur::ismod())
{
tr("Torrent Info", "<a href=\"page.php?type=torrent_info&id=$id\">Torrent Info</a>", 1);
}
        print("</table></p>\n");
	}
	else {
		stdhead("Comments for torrent \"" . $row["name"] . "\"");
		print("<h1>Comments for <a href=details.php?id=$id>" . $row["name"] . "</a></h1>\n");
//		print("<p><a href=\"details.php?id=$id\">Back to full details</a></p>\n");
	}
	$res3 = sql_query("select count(snatched.id) from snatched inner join users on snatched.userid = users.id inner join torrents on snatched.torrentid = torrents.id where snatched.finished='yes'AND snatched.torrentid =" . $_GET[id]) or die(mysql_error());
$row = mysql_fetch_array($res3);

$count = $row[0];
$perpage = 10;
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] . "?id=" . $_GET[id] . "&" );

$res3 = sql_query("select name from torrents where id = $_GET[id]");
$arr3 = mysql_fetch_assoc($res3);
$dt = gmtime() - 180;
$dt = sqlesc(get_date_time($dt));
collapses("snatch","<font color=white>Snatch Details for $arr3[name]</font>",'100',0,'class=thead','class=tcat');
#print("<h1 align=center>Snatch Details for <a href=details.php?id=$_GET[id]><b><font color=black>$arr3[name]</font></b></a></h1>\n");
print("<p align=center>The users at the top finished the download most recently</p>");

echo $pagertop;

print("<table  border=1 cellspacing=0 cellpadding=5 align=center width=100%>\n");
print("<tr><td class=colhead align=center>Username</td><td class=colhead align=center>Uploaded</td><td class=colhead align=center>Downloaded</td><td class=colhead align=center>Ratio</td><td class=colhead align=center>Completed</td><td class=colhead align=center>Last Action</td><td class=colhead align=center>Seeding</td><td class=colhead align=center>PM</td><td class=colhead align=center>Report User</td><td class=colhead align=left>On/Off</td></tr>");

$res = sql_query("select users.id, users.username, users.title, users.uploaded, users.downloaded, snatched.completedat, snatched. last_action, snatched.seeder, snatched.userid from snatched inner join users on snatched.userid = users.id inner join torrents on snatched.torrentid = torrents.id where snatched.finished='yes' AND snatched.torrentid =" . $_GET[id] . " ORDER BY snatched.id desc $limit");
$res2 = sql_query("select users.donor, users.enabled, users.warned, users.last_access, snatched.uploaded, snatched.downloaded, snatched.userid from snatched inner join users on snatched.userid = users.id inner join torrents on snatched.torrentid = torrents.id where snatched.finished='yes' AND snatched.torrentid =" . $_GET[id] . " ORDER BY snatched.id desc $limit");
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
 
print("<tr$highlight><td align=center><a href=userdetails.php?id=$arr[userid]><b>$arr[username]</b></a></td><td align=center>$uploaded Global<br>$uploaded2 Torrent</td><td align=center>$downloaded Global<br>$downloaded2 Torrent</td><td align=center>$ratio Global<br>$ratio2 Torrent</td><td align=center>$arr[completedat]</td><td align=center>$arr[last_action]</td><td align=center>" . ($arr["seeder"] == "yes" ? "<b><font color=green>Yes</font>" : "<font color=red>No</font></b>") . "</td>
<td align=center><a href=sendmessage.php?receiver=$arr[userid]><img src=$pic_base_url/pm.gif border=0></a></td><td align=center><a href=report.php?user=$arr[userid]><img border=0 src=$pic_base_url/report.gif></a></td><td align=right>" . get_user_icons($arr2, true) .
"".("'".$arr2['last_access']."'">$dt?"<img src=pic/user_online.gif  border=0 alt=\"Online\">":"<img src=pic/user_offline.gif border=0 alt=\"Offline\">" )."</td>"."
</tr>\n");
}
print("</table>\n");

echo $pagerbottom;
collapsee();

	print("<p><a name=\"startcomments\"></a></p>\n");

	$commentbar = "<p align=center><a class=index href=comment.php?action=add&tid=$id>Add a comment</a></p>\n";
	$subres = sql_query("SELECT COUNT(*) FROM comments WHERE torrent = $id");
	$subrow = mysql_fetch_array($subres);
	$count = $subrow[0];
?>
<script type="text/javascript">
function send(){

var frm = document.comment;
var tid = '';
var text = '';

var value = $('textarea[name="text"]').attr('value');
text = value;
tid = <?=$id?>;
 $("#ajax").load("takecomment.php", {tid: tid,text: text}, function() {
     $('textarea[name="text"]').attr('value',"");
     $("#commentarea").load("page.php", {type: "commentable",id: tid});
 });
$("#loading-layer").ajaxStart(function(){
   $(this).show();
 });
 $("#loading-layer").ajaxStop(function(){
   $(this).hide();
 });
}
</script>
<div id="loading-layer" style="display:none;font-family: Verdana;font-size: 11px;width:200px;height:50px;background:#FFF;padding:10px;text-align:center;border:1px solid #000">
<div style="font-weight:bold" id="loading-layer-text">Loading. Please, wait...</div><br />
<img src="pic/loader.gif" border="0" />
</div>

<?php
	if (!$count) {
		print("<h2>No comments yet</h2>\n");
	}
	else {
		echo "<span id=commentarea>";
		list($pagertop, $pagerbottom, $limit) = pager(20, $count, "details.php?id=$id&", array(lastpagedefault => 1));

		$subres = sql_query("SELECT comments.id, text, user, comments.added, editedby, editedat, avatar, warned, ".
                  "username, title, class, last_access, donor FROM comments LEFT JOIN users ON comments.user = users.id WHERE torrent = " .
                  "$id ORDER BY comments.id $limit") or sqlerr(__FILE__, __LINE__);
		$allrows = array();
		while ($subrow = mysql_fetch_array($subres))
			$allrows[] = $subrow;
		
		print($commentbar);
		print($pagertop);

		commenttable($allrows);

		print($pagerbottom);
		echo "</span>";
	}
	?>
	<div id="ajax"></div>
	<script language=javascript>
function SmileIT(smile,form,text){
   document.forms[form].elements[text].value = document.forms[form].elements[text].value+" "+smile+" ";
   document.forms[form].elements[text].focus();
}
</script>
<?php
if($usergroups[canpc] == 'yes') {
	print("<BR>");
	JsB::bbedit();
	collapses('quickreply','<font color=white><b>Quick Comment</b></font>','100',0,'class=thead','class=tcat');
	print ("<table style='border:1px solid #000000;' width=100%><tr>".
  "<td style='padding:10px;text-align:center;'><p><br />".
  "<form name=comment method=\"post\" action=\"takecomment.php\">".
  "<center><textarea name=\"text\" rows=\"4\" cols=\"90\" class=\"markItUp\"></textarea></center>".
  "<input type=\"hidden\" name=\"tid\" value=\"$id\"/><br />");
  ?>
  <center><a href="javascript: SmileIT(';-)','comment','text')"><img src=pic/smilies/wink.gif width="20" height="20" border=0></a><a href="javascript: SmileIT(':-P','comment','text')"><img src=pic/smilies/tongue.gif width="20" height="20" border=0></a><a href="javascript: SmileIT(':-)','comment','text')"><img border=0 src=pic/smilies/smile1.gif></a><a href="javascript: SmileIT(':w00t:','comment','text')"><img border=0 src=pic/smilies/w00t.gif></a><a href="javascript: SmileIT(':-D','comment','text')"><img border=0 src=pic/smilies/grin.gif></a><a href="javascript: SmileIT(':lol:','comment','text')"><img border=0 src=pic/smilies/laugh.gif></a><a href="javascript: SmileIT(':-/','comment','text')"><img border=0 src=pic/smilies/confused.gif></a><a href="javascript: SmileIT(':-(','comment','text')"><img border=0 src=pic/smilies/sad.gif></a><a href="javascript: SmileIT(':-O','comment','text')"><img src=pic/smilies/ohmy.gif border=0></a><a href="javascript: SmileIT('8-)','comment','text')"><img src=pic/smilies/cool1.gif width="18" height="18" border=0></a><a href="javascript: SmileIT(':sly:','comment','text')"><img src=pic/smilies/sly.gif width="18" height="18" border=0></a><a href="javascript: SmileIT(':greedy:','comment','text')"><img src=pic/smilies/greedy.gif width="18" height="18" border=0></a><a href="javascript: SmileIT(':weirdo:','comment','text')"><img src=pic/smilies/weirdo.gif width="18" height="18" border=0></a><a href="javascript: SmileIT(':sneaky:','comment','text')"><img src=pic/smilies/sneaky.gif width="18" height="18" border=0></a><a href="javascript: SmileIT(':shit:','comment','text')"><img src=pic/smilies/shit.gif width="18" height="18" border=0></a><a href="javascript: SmileIT(':?:','comment','text')"><img src=pic/smilies/question.gif width="18" height="18" border=0></a><a href="javascript: SmileIT(':!:','comment','text')"><img src=pic/smilies/excl.gif width="18" height="18" border=0></a></center>
  <?php
  print("<input class=button type=\"submit\" onClick=\"send(); return false;\" class=btn value=\"Submit\" name=\"submitb\" id=\"submitb\" />".
"</form></p></td></tr></table>");
collapsee();
	print($commentbar);}
}

stdfoot();
?>