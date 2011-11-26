<?php
$rootpath = '../';
require_once($rootpath."forums/functions/fts.php");
require_once($rootpath."include/bittorrent.php");
  

  loggedinorreturn();
  $wherethisuser = where ($_SERVER["SCRIPT_FILENAME"],$CURUSER["id"]);

  iplogger();
	parked();
    $forumid = 0+$_POST["forumid"];

    $topicid = 0+$_GET["topicid"];

    if (!is_valid_id($forumid) || !is_valid_id($topicid) || get_user_class() < UC_MODERATOR)
      die;

    // Make sure topic and forum is valid

    $res = @sql_query("SELECT minclasswrite FROM forums WHERE id=$forumid") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) != 1)
      stderr("Error", "Forum not found.");

    $arr = mysql_fetch_row($res);

    if (get_user_class() < $arr[0])
      die;

    $res = @sql_query("SELECT forumid FROM topics WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);
   if (mysql_num_rows($res) != 1)
     stderr("Error", "Topic not found.");
   $arr = mysql_fetch_row($res);
   $old_forumid=$arr[0];

   // get posts count
   $res = sql_query("SELECT COUNT(id) AS nb_posts FROM posts WHERE topicid=$topicid") or sqlerr(__FILE__, __LINE__);
   if (mysql_num_rows($res) != 1)
     stderr("Error", "Couldn't get posts count.");
   $arr = mysql_fetch_row($res);
   $nb_posts = $arr[0];

   // move topic
   if ($old_forumid != $forumid)
   {
     @sql_query("UPDATE topics SET forumid=$forumid WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);
     // update counts
     @sql_query("UPDATE forums SET topiccount=topiccount-1, postcount=postcount-$nb_posts WHERE id=$old_forumid") or sqlerr(__FILE__, __LINE__);
     @sql_query("UPDATE forums SET topiccount=topiccount+1, postcount=postcount+$nb_posts WHERE id=$forumid") or sqlerr(__FILE__, __LINE__);
   }

    // Redirect to forum page

    header("Location: $BASEURL/forums/viewforum.php?forumid=$forumid");

    die;
  
  ?>