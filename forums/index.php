<?php
$rootpath = '../';
require_once($rootpath."forums/functions/fts.php");
require_once($rootpath."include/bittorrent.php");
  

  loggedinorreturn();
  $wherethisuser = where ($_SERVER["SCRIPT_FILENAME"],$CURUSER["id"]);

  iplogger();
	parked();
if (isset($_GET["catchup"]))
    catch_up();

  //-------- Get forums
  	$forums = mysql_query("select id from forums");
	while ($forum = mysql_fetch_assoc($forums))
	{
		$postcount = 0;
		$topiccount = 0;
		$topics = mysql_query("select id from topics where forumid=$forum[id]");
		while ($topic = mysql_fetch_assoc($topics))
		{
			$res = mysql_query("select count(*) from posts where topicid=$topic[id]");
			$arr = mysql_fetch_row($res);
			$postcount += $arr[0];
			++$topiccount;
		}
		mysql_query("update forums set postcount=$postcount, topiccount=$topiccount where id=$forum[id]");
	}

  mysql_query("UPDATE users SET forum_access='" . get_date_time() . "' WHERE id={$CURUSER["id"]}");// or die(mysql_error());
  $forums2_res = mysql_query("SELECT * FROM overforums ORDER BY sort ASC") or sqlerr(__FILE__, __LINE__);

   stdhead("Forums");

      print("<p align=left style=\"color:gray;\">$SITENAME Forums</p>");
      

    while ($a = mysql_fetch_assoc($forums2_res))
        {
        $npost = 0;

    if (get_user_class() < $a["minclassview"])
      continue;

    $forid = $a["id"];

   $overforumname = $a["name"];
   ?>
   <table border="0" cellpadding="4" cellspacing="1" width=100%>

				<?collapses('forum-'.$forid,"<strong><a href=forumview.php?forid=$forid><b>$overforumname</b></a></strong>",'100',0,'class=thead','class=tcat','#ffffff','0');?>

				
			<?php
print("<table border=0 cellspacing=0 cellpadding=5 width=100%>\n");


                        $forums_res = mysql_query("SELECT * FROM forums WHERE forid=$forid ORDER BY sort ASC") or sqlerr(__FILE__, __LINE__);
?>
  	<tr class=thead>
						<td class="tcat" width="35">&nbsp;</td>
						<td class="tcat"><strong>Forum</strong></td>
						<td class="tcat" style="white-space: nowrap;" align="center" width="85"><strong>Topics</strong></td>
						<td class="tcat" style="white-space: nowrap;" align="center" width="85"><strong>Posts</strong></td>
						<td class="tcat" align="center" width="200"><strong>Last Post</strong></td>

					</tr>
					<?php
  while ($forums_arr = mysql_fetch_assoc($forums_res))
  {
  	
    if (get_user_class() < $forums_arr["minclassread"])
      continue;




    $forumid = $forums_arr["id"];

    $forumname = htmlspecialchars($forums_arr["name"]);

    $forumdescription = htmlspecialchars($forums_arr["description"]);

    $topiccount = number_format($forums_arr["topiccount"]);

    $postcount = number_format($forums_arr["postcount"]);


    $lastpostid = get_forum_last_post($forumid);


    $post_res = mysql_query("SELECT UNIX_TIMESTAMP(added) as utadded,topicid,userid FROM posts WHERE id=$lastpostid") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($post_res) == 1)
    {
      $post_arr = mysql_fetch_assoc($post_res) or die("Bad forum last_post");

      $lastposterid = $post_arr["userid"];

      $lastpostdate = display_date_time($post_arr["utadded"] , $CURUSER[tzoffset] );

      $lasttopicid = $post_arr["topicid"];

      $user_res = mysql_query("SELECT username,class FROM users WHERE id=$lastposterid") or sqlerr(__FILE__, __LINE__);

      $user_arr = mysql_fetch_assoc($user_res);
if(!empty($user_arr['class']) || !empty($user_arr['username']))
      $lastposter = get_style($user_arr['class'],$user_arr['username']);
else
$lastposter = '<s>Unkown</s>';

      $topic_res = mysql_query("SELECT subject FROM topics WHERE id=$lasttopicid") or sqlerr(__FILE__, __LINE__);

      $topic_arr = mysql_fetch_assoc($topic_res);

      $lasttopic = FFACTORY::cut(htmlspecialchars($topic_arr['subject']),28);

      $lastpost = "<nobr>$lastpostdate<br>" .
      "by <a href=$BASEURL/userdetails.php?id=$lastposterid><b>$lastposter</b></a><br>" .
      "in <a href=viewtopic.php?topicid=$lasttopicid&amp;page=p$lastpostid#$lastpostid><b>$lasttopic</b></a></nobr>";

      $r = mysql_query("SELECT lastpostread FROM readposts WHERE userid=$CURUSER[id] AND topicid=$lasttopicid") or sqlerr(__FILE__, __LINE__);

      $a = mysql_fetch_row($r);

      if ($a && $a[0] >= $lastpostid)
        $img = "off";
      else
        $img = "on";
    }
    else
    {
      $lastpost = "N/A";
      $img = "off";
    }
    global $rootpath;
    ?>


			<tr>
				<td class="trow1" align="center" valign="top">
					<?="<img src=".
    $rootpath."/forums/pic/$img.gif>";?>
				</td>
				<td class="trow1" valign="top">
					<strong><?="<a href=viewforum.php?forumid=$forumid><b>$forumname</b></a>"?><?
					echo ($CURUSER['class']>=UC_ADMINISTRATOR ? "<font class=small> ".
    	"[<a class=altlink href=editforum.php?forumid=$forumid>Edit</a>] ".
        "[<a class=altlink href=deleteforum.php?forumid=$forumid>Delete</a>]</font>" : "");?></strong>

					<div class="smalltext"><?=$forumdescription?></div>		
				</td>
				<td class="trow1" style="white-space: nowrap;" align="center" valign="top">
					<?=$topiccount?>
				</td>
				<td class="trow1" style="white-space: nowrap;" align="center" valign="top">

					<?=$postcount?>
				</td>
				<td class="trow1" style="white-space: nowrap;" align="right" valign="top">
					
		<span class="smalltext">
<?=$lastpost?>
		</span>

				</td>
			</tr>
			<?php /*
    print("<tr><td align=left><table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded style='padding-right: 5px'><img src=".
    $rootpath."/pic/$img.gif></td><td class=embedded><a href=viewforum.php?forumid=$forumid><b>$forumname</b></a>\n" .
    ($CURUSER['class']>=UC_ADMINISTRATOR ? "<font class=small> ".
    	"[<a class=altlink href=editforum.php?forumid=$forumid>Edit</a>] ".
        "[<a class=altlink href=deleteforum.php?forumid=$forumid>Delete</a>]</font>" : "").
    "<br>\n$forumdescription</td></tr></table></td><td align=right>$topiccount</td></td><td align=right>$postcount</td>" .
    "<td align=left>$lastpost</td></tr>\n");*/


  }
?>
</tbody>
			</table></table><br>
			<?php
  }
// End Table Mod
print("<BR>");
forum_stats();
print("<p align=center><a href=search.php><b>Search</b></a> | <a href=viewunread.php><b>View unread</b></a> | <a href=catchup.php><b>Mark all as read</b></a> ".($CURUSER['class'] >= UC_ADMINISTRATOR ? "| <a href=$BASEURL/admin/forummanage.php#add><b>Add Forum</b></a>":"")."</p>");
?>

<?php
stdfoot();
?>