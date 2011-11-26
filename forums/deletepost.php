<?php
$rootpath = '../';
require_once($rootpath."forums/functions/fts.php");
require_once($rootpath."include/bittorrent.php");
  

  loggedinorreturn();
  $wherethisuser = where ($_SERVER["SCRIPT_FILENAME"],$CURUSER["id"]);

  iplogger();
	parked();
    $postid = 0+$_GET["postid"];

    $sure = 0+$_GET["sure"];

    if (get_user_class() < UC_MODERATOR || !is_valid_id($postid))
      die;

    //------- Get topic id

    $res = sql_query("SELECT topicid FROM posts WHERE id=$postid") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_row($res) or stderr("Error", "Post not found");

    $topicid = $arr[0];

    //------- We can not delete the post if it is the only one of the topic

    $res = sql_query("SELECT COUNT(*) FROM posts WHERE topicid=$topicid") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_row($res);

    if ($arr[0] < 2)
      stderr("Error", "Can't delete post; it is the only post of the topic. You should\n" .
      "<a href=deletetopic.php?topicid=$topicid&sure=1>delete the topic</a> instead.\n",false);


    //------- Get the id of the last post before the one we're deleting

    $res = sql_query("SELECT id FROM posts WHERE topicid=$topicid AND id < $postid ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
		if (mysql_num_rows($res) == 0)
			$redirtopost = "";
		else
		{
			$arr = mysql_fetch_row($res);
			$redirtopost = "&page=p$arr[0]#$arr[0]";
		}

    //------- Make sure we know what we do :-)

    if (!$sure)
    {
      stderr("Delete post", "Sanity check: You are about to delete a post. Click\n" .
      "<a href=?action=deletepost&postid=$postid&sure=1>here</a> if you are sure.",false);
    }

    //------- Delete post

    sql_query("DELETE FROM posts WHERE id=$postid") or sqlerr(__FILE__, __LINE__);

    //------- Update topic

    update_topic_last_post($topicid);
    
    //===remove karma
    UserHandle::KPS("-","1.0",$CURUSER["id"]);
	//===end

    header("Location: $BASEURL/forums/viewtopic.php?topicid=$topicid$redirtopost");

    die;
  
  ?>