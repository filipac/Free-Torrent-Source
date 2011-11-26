<?php
while ($arr = mysql_fetch_assoc($res)):
      ++$pn;
      ++$postcount;
      unset($posterid,$title,$class,$res2);

      $postid = $arr["id"];
	  $_subject = $arr['subject'];
      $posterid = $arr["userid"];
      
      $today = date("Y-m-d");
$ago = get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]));
$ago = $ago == '1 day' ? 'Yesterday' : '';
$added = sql_timestamp_to_unix_timestamp($arr["added"]);
$checkadded = date("Y-m-d",$added);
if($today == $checkadded)
$added = 'Today '.date("H:i:s",$added);
elseif($ago == 'Yesterday')
$added = 'Yesterday '.date("H:i:s",$added);
else
$added = date("Y-m-d H:i:s",$added);
      $added = $added;

      //---- Get poster details
	$dt = gmtime() - 180;
	$dt = sqlesc(get_date_time($dt));
    $res2 = sql_query("SELECT username,class,avatar,donor,donated,title,enabled,warned,uploaded,downloaded,signature,last_access FROM users WHERE id=$posterid") or sqlerr(__FILE__, __LINE__);

      $arr2 = mysql_fetch_assoc($res2);
      $laccess = $arr2['last_access'];
      $uploaded = mksize($arr2["uploaded"]);
$downloaded = mksize($arr2["downloaded"]);
if ($arr2["downloaded"] > 0)

{

$ratio = $arr2['uploaded'] / $arr2['downloaded'];

$ratio = number_format($ratio, 3);

$color = get_ratio_color($ratio);

if ($color)

 $ratio = "<font color=$color>$ratio</font>";

}

else

if ($arr2["uploaded"] > 0)

    $ratio = "Inf.";

else

$ratio = "---";

$rem = sql_query("SELECT COUNT(*) FROM posts WHERE userid=" . $posterid) or sqlerr();
 $arr25 = mysql_fetch_row($rem);
 $forumposts = $arr25[0];
      
      $signature = $arr2[signature];
	  $signature = ($CURUSER["signatures"] == "yes" ? htmlspecialchars($arr2["signature"]) : "");
		if(!empty($arr2["class"]) || !empty($arr2["username"]))
      $postername = get_style($arr2["class"],$arr2["username"]);
      else
      $postername = '<s>Unkown</s>';

      if ($postername == "")
      {
        $by = "unknown[$posterid]";

        $avatar = "";
      }
      else
      {
//		if ($arr2["enabled"] == "yes")
	        $avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($arr2["avatar"]) : "");
//	    else
//			$avatar = "pic/disabled_avatar.gif";

        $title = $arr2["title"];

        if (!$title) {
        	if(!empty($arr2['class']))
        $class = get_user_class_name($arr2["class"]);
        else
        $class = "User";
		if(!empty($class))
		$title = $class;
		else
		$title = 'Unkown user'; 	
		}
          

if(!empty($arr2["class"]))
$uclass = get_user_class_name($arr2["class"]);
else
$uclass = 'User';

if($usergroups['canstaffpanel'] != 'no') 
fts_register_menu($postid,array("<a href=$BASEURL/userdetails.php?id=$posterid><b>Visit UserDetails</b></a>","<a href=$BASEURL/userdetails.php?id=$posterid#edit><b>Edit user</b></a>"));
else
fts_register_menu($postid,array("<a href=$BASEURL/userdetails.php?id=$posterid><b>Visit UserDetails</b></a>"));
$by = fts_show_menu($postid,"<b>$postername</b>") . ($arr2["donor"] == "yes" ? "<img src=".$rootpath.
"pic/star.png alt='Donor'>" : "") . ($arr2["enabled"] == "no" ? "<img src=".$rootpath.
"pic/disabled.png alt=\"This account is disabled\" style='margin-left: 2px'>" : ($arr2["warned"] == "yes" ? "<a href=rules.php#warning class=altlink><img src=".$rootpath."pic/warning.png alt=\"Warned\" border=0></a>" : "")) . " ";
      }

      if (!$avatar)
        $avatar = $rootpath."pic/default_avatar.gif";

      print("<a name=$postid>\n");

      if ($pn == $pc)
      {
        print("<a name=last>\n");
        if ($postid > $lpr)
          sql_query("UPDATE readposts SET lastpostread=$postid WHERE userid=$userid AND topicid=$topicid") or sqlerr(__FILE__, __LINE__);
      }

      #print("<p class=sub><table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded width=99%>#$postid by $by ($title) at $added");
	        

      #print("</td><td class=embedded width=1%><a href=#top><img src=pic/p_up.gif border=0 alt='Top'></a></td></tr>");

      #print("</table></p>\n");

     

      $body = format_comment($arr["body"],0);
   $nrpic = ($arr['iconid'] == 0 ? '1' : $arr['iconid']);
		$pic = $BASEURL."/forums/pic/icons/icon$nrpic.gif";   
		//---------------------------------
		//---- Search Highlight v0.1
		//---------------------------------
      	if ($highlight){
	      	$body = highlight($highlight,$body);
	      	}
		//---------------------------------
		//---- Search Highlight v0.1
		//---------------------------------

      if (is_valid_id($arr['editedby']))
      {
        $res3_ = sql_query("SELECT username FROM users WHERE id=$arr[editedby]");
        if (mysql_num_rows($res2) == 1)
        {
          $arr3_ = mysql_fetch_assoc($res3_);
          $body .= "<p><font size=1 class=small>Last edited by <a href=userdetails.php?id=$arr[editedby]><b>$arr3_[username]</b></a> at $arr[editedat] GMT</font></p>\n";
        }
      }
      
      if ($signature)
 	  $body .= "<p style='vertical-align:bottom'><br>____________________<br>" . format_comment($signature) . "</p>";
    
      "</td>";
$img = "<img src='".$BASEURL."/include/class_image.php?string=$uclass".($arr2['donated'] > '0' ? '&nr=2' : '')."' />";

      $stats = "<br>"."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Posts: $forumposts<br>"."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;UL: $uploaded <br>"."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DL: $downloaded<br>"."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ratio.: $ratio";
#	 print("<a name=$postid></a><tr valign=top><td width=25% align=left style='padding: 0px'><center>$by<BR>$title</center><br>"."&nbsp; " .
      # ($avatar ? "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img width=100 src=\"$avatar\">": ""). "<br><br><br>"."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RANK: $img$stats<br><br></td><td class=comment width=75%><div style='float:right;'>Post: #$postcount</div> $body</td></tr>\n");
#	print("<tr><td> ".
  #"$added <a href=#top><img src=".$rootpath."pic/p_up.gif border=0 alt='Top'></a></td>");
#	print("<td align=right>");
$buttons = "<button class=\"btns\" onclick=\"window.location='$BASEURL/sendmessage.php?receiver=".htmlspecialchars($posterid)."'\" alt=\"Send message to ".htmlspecialchars($postername)."\">PM ".$postername."</button> ";

	   $resq = sql_query("SELECT posts.*, users.username FROM posts JOIN users ON posts.userid = users.id WHERE posts.id=$postid") or sqlerr(__FILE__, __LINE__);

	   if (mysql_num_rows($resq) != 1)
	     stderr("Error", "No post with this ID");

	   $arrq = mysql_fetch_assoc($resq);
	   $quotea = json_encode(htmlspecialchars("Re: $arrq[subject]"));
	   $quoteb = json_encode("[quote=".htmlspecialchars($arrq["username"])."]$arrq[body][/quote]");
	   #quote.php?topicid=$topicid&postid=$postid
$arr = get_forum_access_levels($forumid) or die; global $usergroups;
if (get_user_class() >= $arr["write"])
	$maypost = true;
    if($locked AND !ur::ismod()) {
		unset($maypost);
		$maypost = false;
	}if ($usergroups['canpostintopics'] == 'no' or $usergroups['canpostintopics'] !=
    'yes')
{
    unset($maypost);
    $maypost = false;
}
if ($maypost)
$buttons .= ("<button class=\"btns\" onclick='".'$("#body").val('.$quoteb.').focus();'."' alt=\"Reply with Quote\">Reply with Quote</button>");
if ($maypost)
$buttons .= ("<button class=btns onclick='".'$("#body").val('.$quotea.').focus();'."' alt=\"Quick Reply\">Quick Reply</button>");
if ($maypost)
$buttons .= ("<button class=\"btns\" onclick=\"window.location='reply.php?topicid=$topicid'\" alt=\"Add Reply\" title=\"Add Reply\">Add Reply</button>");
				
if (get_user_class() >= UC_MODERATOR)
$buttons .= ("<button class=\"btns\" onclick=\"window.location='deletepost.php?postid=$postid'\" alt=\"Delete\" title=\"Delete\">Delete</button>");
	
if (($CURUSER["id"] == $posterid && !$locked) || get_user_class() >= UC_MODERATOR)
$buttons .= ("<button class=\"btns\" onclick=\"window.location='editpost.php?postid=$postid'\" alt=\"Edit\" title=\"Edit\">Edit</button>");
    $_avatar = ($avatar ? "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img width=100 src=\"$avatar\">": "");
    $onstatus = "'".$laccess."'">$dt?"<font color=green>Online</font>":"<font color=red>Offline</font>";
    $_subject = (!empty($_subject) ? $_subject : $subject);
    $_onpic = "'".$laccess."'">$dt?"<img src='$BASEURL/forums/pic/user_online.gif' alt='User is online' />":"<img src='$BASEURL/forums/pic/user_offline.gif' alt='User is offline' />";
    echo <<<eod
<!-- start: post# -->
			<tr>
				<td colspan="2" class="subheader" name="pid29285">
					<div style="float: right;">
						<strong>Post: #$pn</strong>
					</div>
					<div style="float: left;">
						$added
					</div>
				</td>
			</tr>
			<tr>
				<td class="trow1" style="text-align: left;" width="20%">					
				$by$_onpic<br/>
					
					$img<br/><br/>
					$_avatar<br/>
					Total posts: $forumposts<br/>
					Status: $onstatus<br/>
					UL: $uploaded<br/>
					DL: $downloaded<br/>
					Ratio: $ratio<br/>
					Country:<br/>
				</td>
				<td class="trow1" style="text-align: left;" valign="top" width="80%">
					<img src='$pic' />
					<span class="smalltext"><strong>$_subject</strong></span><hr />
					<div id="post_message_29285" style="display: inline;">$body</div>
					<div style="text-align: right; vertical-align: bottom;"></div>	
				</td>
			</tr>				
eod;
echo '<tr>
				<td class="trow1" width="15%" valign="middle" style="white-space: nowrap; text-align: center;">
				<input value="Top" onclick="self.scrollTo(0, 0); return false;" type="button" class="btns"/> '."<a href=\"$BASEURL/report.php?forumid=".htmlspecialchars($topicid)."&forumpost=".htmlspecialchars($postid)."\"><button class=\"btns\" onclick=\"window.location='report.php?topicid=$topicid'\" alt=\"Report\" title=\"Report\">Report</button>".'
				</td>
				<td class="trow1" style="text-align: center;" valign="top">
					<div style="float: right;">
					'.$buttons.'
					</div>
				</td>
			</tr>
		<!-- end: post# --></table><br /><table width="100%" border="0" cellspacing="0" cellpadding="4" style="clear: both;">
';
      
    endwhile;
    ?>