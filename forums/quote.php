<?php
$rootpath = '../';
require_once($rootpath."forums/functions/fts.php");
require_once($rootpath."include/bittorrent.php");
  

  loggedinorreturn();
  $wherethisuser = where ($_SERVER["SCRIPT_FILENAME"],$CURUSER["id"]);

  iplogger();
	parked();
		$topicid = 0+$_GET["topicid"];
		$res = sql_query("SELECT * FROM topics WHERE id=$topicid") or sqlerr(__file__,
    __line__);

$arr = mysql_fetch_assoc($res) or stderr("Forum error", "Topic not found");
$arr = get_forum_access_levels($arr['forumid']) or die;
if (get_user_class() < $arr["write"])
stderr('Error','You can\'t write in this topic!');
		int_check($topicid,true);

    stdhead("Post reply");

    begin_main_frame();

    insert_compose_frame($topicid, false, true);

    end_main_frame();

    stdfoot();

    die;
    ?>