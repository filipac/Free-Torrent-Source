<?php
$rootpath = '../';
require_once($rootpath."forums/functions/fts.php");
require_once($rootpath."include/bittorrent.php");
  

  loggedinorreturn();
  $wherethisuser = where ($_SERVER["SCRIPT_FILENAME"],$CURUSER["id"]);

  iplogger();
	parked();
    $forumid = 0+$_GET["forumid"];
$arr = get_forum_access_levels($forumid) or die;
if (get_user_class() < $arr["create"])
stderr('Error','You can\'t create new topics here!');
    int_check($forumid,true);

    stdhead("New topic");

    begin_main_frame();

    insert_compose_frame($forumid);

    end_main_frame();

    stdfoot();

    die;
  
  ?>