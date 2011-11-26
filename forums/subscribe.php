<?php
$rootpath = '../';
require_once($rootpath."include/bittorrent.php");

loggedinorreturn();



if (!isset($_GET[topicid]))
bark ("Failed ... No forum selected");


if ((get_row_count("subscriptions", "WHERE userid=$CURUSER[id] AND topicid = $_GET[topicid]")) > 0)
bark("Already subscribed to $_GET[topicid]");

mysql_query("INSERT INTO subscriptions (userid, topicid) VALUES ($CURUSER[id], $_GET[topicid])") or sqlerr(__FILE__,__LINE__);

$res = mysql_query("SELECT subject FROM `topics` WHERE id=$_GET[topicid]") or sqlerr(__FILE__, __LINE__);

$arr = mysql_fetch_assoc($res) or die("Bad forum id");

$forumname = $arr["subject"];
$todel = array();
$todel[] = "&sub=0";
$todel[] = "&sub=1";
$refer = str_replace($todel,'',$_SERVER['HTTP_REFERER']);
header("Location:$refer&sub=0");



?>