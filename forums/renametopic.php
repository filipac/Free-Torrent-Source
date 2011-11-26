<?php
$rootpath = '../';
require_once($rootpath."forums/functions/fts.php");
require_once($rootpath."include/bittorrent.php");
  

  loggedinorreturn();
  $wherethisuser = where ($_SERVER["SCRIPT_FILENAME"],$CURUSER["id"]);

  iplogger();
	parked();
  	if (get_user_class() < UC_MODERATOR)
  	  die;

  	$topicid = 0+$_POST['topicid'];

  	int_check($topicid,true);

  	$subject = $_POST['subject'];

  	if ($subject == '')
  	  stderr('Error', 'You must enter a new title!');

  	$subject = sqlesc($subject);

  	sql_query("UPDATE topics SET subject=$subject WHERE id=$topicid") or sqlerr();

  	$returnto = $_POST['returnto'];

  	if ($returnto)
  	  header("Location: $returnto");

  	die;
  	?>