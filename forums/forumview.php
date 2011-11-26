<?php
$rootpath = '../';
require_once($rootpath."forums/functions/fts.php");
require_once($rootpath."include/bittorrent.php");
  

  loggedinorreturn();
  $wherethisuser = where ($_SERVER["SCRIPT_FILENAME"],$CURUSER["id"]);

  iplogger();
	parked();

  $forid = 0+$_GET["forid"];
// - Bleaches Edits
  mysql_query("UPDATE users SET forum_access='" . get_date_time() . "' WHERE id={$CURUSER["id"]}");// or die(mysql_error());
  $forums_res = mysql_query("SELECT * FROM forums WHERE forid=$forid ORDER BY name") or sqlerr(__FILE__, __LINE__);


  //------ Get forum name

    $res = mysql_query("SELECT name FROM overforums WHERE id=$forid") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_assoc($res) or die;

    $forumname = $arr["name"];

  stdhead("Forums");


    print("<p align=left style=\"color:gray;\"><a href=index.php>$SITENAME Forums</a> / $forumname</p>");

  print("<table border=1 cellspacing=0 cellpadding=5 width=100%>\n");

  print("<tr><td class=colhead align=left>Forums</td><td class=colhead align=right>Topics</td>" .
  "<td class=colhead align=right>Posts</td>" .
  "<td class=colhead align=left>Last post</td></tr>\n");

  while ($forums_arr = mysql_fetch_assoc($forums_res))
  {
    if (get_user_class() < $forums_arr["minclassread"])
      continue;

    // Set forumid
    //mysql_query("UPDATE forums SET forumid=1") or sqlerr(__FILE__, __LINE__);

    //$forums_arr["Forumid"] = 1;

    //echo ($forum_arr["$forumid"]);
    //die('test');
    //$fid = $forums_arr["forid"];

    //if ($forums_arr["forid"] != $forid)
    // continue;


    $forumid = $forums_arr["id"];

    $forumname = htmlspecialchars($forums_arr["name"]);

    $forumdescription = htmlspecialchars($forums_arr["description"]);

    $topiccount = number_format($forums_arr["topiccount"]);

    $postcount = number_format($forums_arr["postcount"]);
/*
    while ($topicids_arr = mysql_fetch_assoc($topicids_res))
    {
      $topicid = $topicids_arr['id'];

      $postcount_res = mysql_query("SELECT COUNT(*) FROM posts WHERE topicid=$topicid") or sqlerr(__FILE__, __LINE__);

      $postcount_arr = mysql_fetch_row($postcount_res);

      $postcount += $postcount_arr[0];
    }

    $postcount = number_format($postcount);
*/
    // Find last post ID

    $lastpostid = get_forum_last_post($forumid);

    // Get last post info

    $post_res = mysql_query("SELECT UNIX_TIMESTAMP(added) as utadded,topicid,userid FROM posts WHERE id=$lastpostid") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($post_res) == 1)
    {
      $post_arr = mysql_fetch_assoc($post_res) or die("Bad forum last_post");

      $lastposterid = $post_arr["userid"];

      $lastpostdate = display_date_time($post_arr["utadded"] , $CURUSER[tzoffset] );

      $lasttopicid = $post_arr["topicid"];

      $user_res = mysql_query("SELECT username FROM users WHERE id=$lastposterid") or sqlerr(__FILE__, __LINE__);

      $user_arr = mysql_fetch_assoc($user_res);

      $lastposter = htmlspecialchars($user_arr['username']);

      $topic_res = mysql_query("SELECT subject FROM topics WHERE id=$lasttopicid") or sqlerr(__FILE__, __LINE__);

      $topic_arr = mysql_fetch_assoc($topic_res);

      $lasttopic = htmlspecialchars($topic_arr['subject']);

      $lastpost = "<nobr>$lastpostdate<br>" .
      "by <a href=userdetails.php?id=$lastposterid><b>$lastposter</b></a><br>" .
      "in <a href=viewtopic.php?topicid=$lasttopicid&amp;page=p$lastpostid#$lastpostid><b>$lasttopic</b></a></nobr>";

      $r = mysql_query("SELECT lastpostread FROM readposts WHERE userid=$CURUSER[id] AND topicid=$lasttopicid") or sqlerr(__FILE__, __LINE__);

      $a = mysql_fetch_row($r);

      if ($a && $a[0] >= $lastpostid)
        $img = "unlocked";
      else
        $img = "unlockednew";
    }
    else
    {
      $lastpost = "N/A";
      $img = "unlocked";
    }
    print("<tr><td align=left><table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded style='padding-right: 5px'><img src=".
    "/pic/$img.gif></td><td class=embedded><a href=viewforum.php?forumid=$forumid><b>$forumname</b></a>\n" .
    ($CURUSER['class']>=UC_ADMINISTRATOR ? "<font class=small> ".
    	"[<a class=altlink href=$BASEURL/forums/editforum.php?forumid=$forumid>Edit</a>] ".
        "[<a class=altlink href=$BASEURL/forums/deleteforum.php?forumid=$forumid>Delete</a>]</font>" : "").
    "<br>\n$forumdescription</td></tr></table></td><td align=right>$topiccount</td></td><td align=right>$postcount</td>" .
    "<td align=left>$lastpost</td></tr>\n");
  }
// End Table Mod
print("</table><BR>");
forum_stats();
stdfoot();
///////////////////////////////
die();
?>