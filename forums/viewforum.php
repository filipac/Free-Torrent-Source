<?php
$rootpath = '../';
require_once($rootpath."forums/functions/fts.php");
require_once($rootpath."include/bittorrent.php");
  

  loggedinorreturn();
  $wherethisuser = where ($_SERVER["SCRIPT_FILENAME"],$CURUSER["id"]);

  iplogger();
	parked();
    $forumid = 0+$_GET["forumid"];

    int_check($forumid,true);

    $page = 0+$_GET["page"];

    $userid = 0+$CURUSER["id"];

    //------ Get forum name

    $res = sql_query("SELECT name, minclassread FROM forums WHERE id=$forumid") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_assoc($res) or die;

    $forumname = $arr["name"];

    if (get_user_class() < $arr["minclassread"])
      die("Not permitted");

    //------ Page links

    //------ Get topic count

    $perpage = $CURUSER["topicsperpage"];
	if (!$perpage) $perpage = 20;

    $res = sql_query("SELECT COUNT(*) FROM topics WHERE forumid=$forumid") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_row($res);

    $num = $arr[0];

    if ($page == 0)
      $page = 1;

    $first = ($page * $perpage) - $perpage + 1;

    $last = $first + $perpage - 1;

    if ($last > $num)
      $last = $num;

    $pages = floor($num / $perpage);

    if ($perpage * $pages < $num)
      ++$pages;

    //------ Build menu

    $menu = "<div id=navcontainer2><ul>\n";

    $lastspace = false;
    if ($page == 1)
      $menu .= "<li><font class=gray>&lt;&lt; Prev</font></li>";

    else
      $menu .= "<li><a href=?forumid=$forumid&page=" . ($page - 1) . ">&lt;&lt; Prev</a></li>";
    for ($i = 1; $i <= $pages; ++$i)
    {
    	if ($i == $page)
        $menu .= "<li><a name=\"current\" class=\"current\"><b>$i</b></a></li>\n";

      elseif ($i > 3 && ($i < $pages - 2) && ($page - $i > 3 || $i - $page > 3))
    	{
    		if ($lastspace)
    		  continue;

  		  $menu .= "... \n";

     		$lastspace = true;
    	}

      else
      {
        $menu .= "<li><a href=?forumid=$forumid&page=$i>$i</a></li>\n";

        $lastspace = false;
      }
      #if ($i < $pages)
        #$menu .= "</b>|<b>\n";
    }

    #$menu .= "<br>\n";



   # $menu .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

    if ($last == $num)
      $menu .= "<li><font class=gray>Next &gt;&gt;</font></li>";

    else
      $menu .= "<li><a href=?forumid=$forumid&page=" . ($page + 1) . ">Next &gt;&gt;</a></li>";

    $menu .= "</b></ul></div><BR>\n";

    $offset = $first - 1;

    //------ Get topics data

    $topicsres = sql_query("SELECT * FROM topics WHERE forumid=$forumid ORDER BY sticky, lastpost DESC LIMIT $offset,$perpage") or
      stderr("SQL Error", mysql_error());

    stdhead("View Forum :: ".$forumname);
    print("<p align=left style=\"color:gray;\"><a href=index.php>$SITENAME Forums</a> / $forumname</p>");

    $numtopics = mysql_num_rows($topicsres);

    if ($numtopics > 0)
    {
      print($menu.'<BR><BR>');

      print("<table border=1 cellspacing=0 cellpadding=5 width=100%>");

      ?>
      <table>
	<tr>
			<td  colspan="6">
				<div>
					<strong><?=$forumname?></strong>
				</div>
			</td>
		</tr>

		<tr class="thead">
			<td class="tcat" align="center" width="1%" colspan="2"></td>

			<td class="tcat" align="left" width="50%"><span class="smalltext"><strong>Subject</strong></span></td>
			<td class="tcat" align="left" width="1%"><span class="smalltext"><strong>Last Post</strong></span></td>
			<td class="tcat" align="center" width="1%"><span class="smalltext"><strong>Replies</strong></span></td>
			<td class="tcat" align="center" width="1%"><span class="smalltext"><strong>Views</strong></span></td>			
		</tr>
		<?php

      print("</tr>\n");

      while ($topicarr = mysql_fetch_assoc($topicsres))
      {
        $topicid = $topicarr["id"];

        $topic_userid = $topicarr["userid"];

        $topic_views = $topicarr["views"];

		$views = number_format($topic_views);

        $locked = $topicarr["locked"] == "yes";

        $sticky = $topicarr["sticky"] == "yes";

        //---- Get reply count

        $res = sql_query("SELECT COUNT(*) FROM posts WHERE topicid=$topicid") or sqlerr(__FILE__, __LINE__);

        $arr = mysql_fetch_row($res);

        $posts = $arr[0];

        $replies = max(0, $posts - 1);

        $tpages = floor($posts / $postsperpage);

        if ($tpages * $postsperpage != $posts)
          ++$tpages;

        if ($tpages > 1)
        {
          $topicpages = " (<img src=".$rootpath."pic/multipage.gif>";
          
          if ($tpages > 6)
          {
            for ($i = 1; $i <= 3; ++$i)
              $topicpages .= " <a href=viewtopic.php?topicid=$topicid&page=$i>$i</a>";
            
            $topicpages .= " .. ";
            
            for ($i = ($tpages - 2); $i <= $tpages; ++$i)
              $topicpages .= " <a href=viewtopic.php?topicid=$topicid&page=$i>$i</a>";
          }
          else
          {
            for ($i = 1; $i <= $tpages; ++$i)
              $topicpages .= " <a href=viewtopic.php?topicid=$topicid&page=$i>$i</a>";
      }
          $topicpages .= ")";
        }
        else
          $topicpages = "";

        //---- Get userID and date of last post
//BUG:Resolved last post date to show corectly
        $res = sql_query("SELECT *,UNIX_TIMESTAMP(added) as utadded FROM posts WHERE topicid=$topicid ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);

        $arr = mysql_fetch_assoc($res);

        $lppostid = 0 + $arr["id"];

        $lpuserid = 0 + $arr["userid"];

        $lpadded = "<nobr>" . display_date_time($arr["utadded"] , $CURUSER[tzoffset] ) . "</nobr>";

        //------ Get name of last poster

        $res = sql_query("SELECT * FROM users WHERE id=$lpuserid") or sqlerr(__FILE__, __LINE__);

        if (mysql_num_rows($res) == 1)
        {
          $arr = mysql_fetch_assoc($res);
$arr[username] = get_style($arr['class'],$arr['username']);
          $lpusername = "<a href=$BASEURL/userdetails.php?id=$lpuserid style=\"text-decoration:none;\"><b>$arr[username]</b></a>";
        }
        else
          $lpusername = "unknown[$topic_userid]";

        //------ Get author

        $res = sql_query("SELECT username,class FROM users WHERE id=$topic_userid") or sqlerr(__FILE__, __LINE__);

        if (mysql_num_rows($res) == 1)
        {
          $arr = mysql_fetch_assoc($res);
$arr[username] = get_style($arr['class'],$arr[username]);
          $lpauthor = "<a href=$BASEURL/userdetails.php?id=$topic_userid><b>$arr[username]</b></a>";
        }
        else
          $lpauthor = "unknown[$topic_userid]";

        //---- Print row

        $r = sql_query("SELECT lastpostread FROM readposts WHERE userid=$userid AND topicid=$topicid") or sqlerr(__FILE__, __LINE__);

        $a = mysql_fetch_row($r);

        $new = !$a || $lppostid > $a[0];

        $topicpic = ($locked ? "offlock" : ($new ? "on" : "off"));
        $nrpic = ($topicarr['iconid'] == 0 ? '1' : $topicarr['iconid']);
		$pic = $BASEURL."/forums/pic/icons/icon$nrpic.gif";
        $subject = ($sticky ? "Sticky: " : "") . "<a href=viewtopic.php?topicid=$topicid><b>" .
        encodehtml($topicarr["subject"]) . "</b></a>$topicpages";

        print("<tr><td align=center><img src=".$rootpath."forums/pic/$topicpic.gif></td><td align=center><img src=\"$pic\"></td><td align=left><table border=0 cellspacing=0 cellpadding=0><tr>" .
        "<td class=embedded style='padding-right: 5px'>" .
        "</td><td class=embedded align=left>\n" .
        "$subject<BR>$lpauthor</td></tr></table></td><td>$lpadded<br>by&nbsp;$lpusername <a href=\"$BASEURL/forums/viewtopic.php?topicid=$topicid&page=last#last\"><img src=\"".$rootpath."forums/pic/lastpost.gif\" style=\"border:none;\"></a></td><td align=right>$replies</td>\n" .
        "<td align=right>$views</td>\n");

        print("</tr>\n");
      } // while

      print("</table>\n");

      print($menu.'<BR><BR>');

    } // if
    else
      print("<p align=center class=error>No topics found</p>\n");
/*
    print("<fieldset><legend>Forum Legend</legend><table class=main border=0 cellspacing=0 cellpadding=0><tr valing=center>\n");

    print("<td class=embedded><img src=".$rootpath."pic/unlockednew.gif style='margin-right: 5px'></td><td class=embedded>New posts</td>\n");

    print("<td class=embedded><img src=".$rootpath."pic/locked.gif style='margin-left: 10px; margin-right: 5px'>" .
    "</td><td class=embedded>Locked topic</td>\n");

    print("</tr></table></fieldset>\n");
*/?>
	<table class="tborder" border="0" cellpadding="4" cellspacing="1" align="center" width="100%">
		<tbody>
			<tr>
				<td class="trow1">
					<table width="100%" align="center">
						<tbody>
							<tr>
								<td align="center" style="padding: 10px 0px 10px 0px; margin: 0px 0px 0px 0px;">

									<img src="pic/on.gif" alt="New posts" title="New posts" class="inlineimg"> <span class="smalltext">New posts</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<img src="pic/off.gif" alt="No new posts" title="No new posts" class="inlineimg"> <span class="smalltext">No new posts</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<img src="pic/offlock.gif" alt="Thread is closed" title="Thread is closed" class="inlineimg"> <span class="smalltext">Thread is closed</span>									
								</td>
							</tr>
						</tbody>

					</table>
				</td>
			</tr>
		</tbody>
	</table>

<?php
    $arr = get_forum_access_levels($forumid) or die;

    $maypost = get_user_class() >= $arr["write"] && get_user_class() >= $arr["create"];

    if (!$maypost)
      print("<p><i>You are not permitted to start new topics in this forum.</i></p>\n");

    print("<p><table border=0 class=main cellspacing=0 cellpadding=0><tr>\n");

    print("<td class=embedded><input type=button value='View unread' class=btns onclick=\"goto('viewunread.php');\"></form></td>\n");

    if ($maypost)
      print("<td class=embedded><input type=button value='New topic' class=btns onclick=\"goto('newtopic.php?forumid=$forumid');\"></form></td>\n");

    print("</tr></table></p>\n");

    insert_quick_jump_menu($forumid);

    stdfoot();

    die;
  