<?php
$rootpath = '../';
require_once($rootpath."forums/functions/fts.php");
require_once($rootpath."include/bittorrent.php");
  

  loggedinorreturn();
  $wherethisuser = where ($_SERVER["SCRIPT_FILENAME"],$CURUSER["id"]);

  iplogger();
	parked();
    $forumid = 0 + $_GET["forumid"];
    $confirmed = 0 + $_GET["confirmed"];

    if(!$forumid)
    	stderr("Error", "Forum ID not found.");
    if(!$confirmed)
    {
	    $rf = mysql_query("SELECT name FROM forums WHERE id=$forumid") or sqlerr(__FILE__, __LINE__);
	    $forum = mysql_fetch_assoc($rf);
        $rt = mysql_query("SELECT id FROM topics WHERE forumid=$forumid") or sqlerr(__FILE__, __LINE__);
        $topics = mysql_num_rows($rt);
        $posts = 0;
        while($topic = mysql_fetch_assoc($rt))
        {
	    	$rp = mysql_query("SELECT * FROM posts WHERE topicid=$topic[id]") or sqlerr(__FILE__, __LINE__);
	    	$posts += mysql_num_rows($rp);
        }
        stdhead("Delete forum");
        begin_main_frame();
        begin_frame("** WARNING! **");
        print("Deleting forum ID $forumid ($forum[name]) will also delete $posts posts in $topics topics. ".
        	"[<a class=altlink href=deleteforum.php?forumid=$forumid&confirmed=1>ACCEPT</a>] ".
            "[<a class=altlink href=index.php>CANCEL</a>]");
        end_frame();
        end_main_frame();
        stdfoot();
        die;
    }

    if ($CURUSER['class']>=UC_ADMINISTRATOR)
    {
	    $rt = mysql_query("SELECT id FROM topics WHERE forumid=$forumid") or sqlerr(__FILE__, __LINE__);
        while($topic = mysql_fetch_assoc($rt))
		    mysql_query("DELETE FROM posts WHERE topicid=$topic[id]") or sqlerr(__FILE__, __LINE__);
	    mysql_query("DELETE FROM topics WHERE forumid=$forumid") or sqlerr(__FILE__, __LINE__);
	    mysql_query("DELETE FROM forums WHERE id=$forumid") or sqlerr(__FILE__, __LINE__);
	    mysql_query("DELETE FROM ratings WHERE topic=$forumid") or sqlerr(__FILE__, __LINE__);
    	header("Location: $BASEURL/forums/index.php");
    }
    else
    	stderr("Error", "You are not authorised to perform this action!");
    die;
  
  ?>