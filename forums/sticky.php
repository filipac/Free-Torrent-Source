<?php
$rootpath = '../';
require_once($rootpath."forums/functions/fts.php");
require_once($rootpath."include/bittorrent.php");
  

  loggedinorreturn();
  $wherethisuser = where ($_SERVER["SCRIPT_FILENAME"],$CURUSER["id"]);

  iplogger();
	parked();
    $topicid = 0 + $_POST["topicid"];

    if (!topicid || get_user_class() < UC_MODERATOR)
      die;

	$sticky = sqlesc($_POST["sticky"]);
    sql_query("UPDATE topics SET sticky=$sticky WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);

    header("Location: $_POST[returnto]");

    die;
    ?>