<?php
$rootpath = '../';
require_once($rootpath."forums/functions/fts.php");
require_once($rootpath."include/bittorrent.php");
  

  loggedinorreturn();
  $wherethisuser = where ($_SERVER["SCRIPT_FILENAME"],$CURUSER["id"]);

  iplogger();
	parked();
    $topicid = 0+$_GET["topicid"];
    $forumid = 0+$_GET["forumid"];

    if (!is_valid_id($topicid) || get_user_class() < UC_MODERATOR)
      die;

    $sure = 0+$_GET["sure"];

    if (!$sure)
    {
      stderr("Delete topic", "Sanity check: You are about to delete a topic. Click\n" .
      "<a href=deletetopic.php?topicid=$topicid&sure=1>here</a> if you are sure.",false);
    }

    sql_query("DELETE FROM topics WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);

    sql_query("DELETE FROM posts WHERE topicid=$topicid") or sqlerr(__FILE__, __LINE__);
    
    sql_query("DELETE FROM subscriptions WHERE topicid='$id'") or sqlerr(__FILE__, __LINE__);
    
    sql_query("DELETE FROM ratings WHERE topic='$id'") or sqlerr(__FILE__, __LINE__);
    
    //===remove karma
    UserHandle::KPS("-","2.0",$CURUSER["id"]);
	//===end

    header("Location: $BASEURL/forums/index.php");

    die;
  
  ?>