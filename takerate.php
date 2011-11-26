<?php
require_once("include/bittorrent.php");




if (!isset($CURUSER))
	stderr("Error!","Must be logged in to vote");
	
//=== topic ratings
if ($_GET["rate_me"]){

if ($_GET["rate_me"])
$rate_me = (int)$_GET['rate_me'];
if ($rate_me <= 0 || $rate_me > 5)
bark("invalid rating number");

$topic_id = $_GET['topic_id'];
if(!is_valid_id($topic_id))
stderr("Error", "invalid topic id!");
$a = sql_query("SELECT locked FROM topics WHERE id = '$topic_id'");
$a = mysql_fetch_assoc($a);
if($a['locked'] == 'yes') 
stderr('Error','The topic is locked');

$res = mysql_query("SELECT topic, user FROM ratings WHERE topic = $topic_id AND user = $CURUSER[id]") or die(mysql_error());
$row = mysql_fetch_array($res);
if ($row[topic] >= 1)
bark("You have already rated this topic.");
if ($row[topic] == 0)
$res = mysql_query("UPDATE ratings SET rating = $rate_me WHERE topic = $topic_id AND user = $CURUSER[id]");
if (!$row) 
$res = mysql_query("INSERT INTO ratings (topic, user, rating, added) VALUES ($topic_id, " . $CURUSER["id"] . ", $rate_me, NOW())") or die(mysql_error());
mysql_query("UPDATE topics SET numratings = numratings + 1, ratingsum = ratingsum + $rate_me WHERE id = $topic_id"); 

$refererto = str_replace ('&amp;', '&', htmlentities($_SERVER["HTTP_REFERER"]));
$referer = ($_SERVER["HTTP_REFERER"] ? $refererto : "/forums/viewtopic.php?topicid=$topic_id");
header("Refresh: 0; url=$referer");
die;
}

if (!mkglobal("rating:id"))
	stderr("Error!","missing form data");

$id = 0 + $id;
if (!$id)
	stderr("Error!","invalid id");

$rating = 0 + $rating;
if ($rating <= 0 || $rating > 5)
	stderr("Error!","invalid rating");

$res = sql_query("SELECT owner FROM torrents WHERE id = ".mysql_real_escape_string($id));
$row = mysql_fetch_array($res);
if (!$row)
	stderr("Error!","no such torrent");

if ($row["owner"] == $CURUSER["id"])
	stderr("Error!","You can't vote on your own torrents.");

$res = sql_query("INSERT INTO ratings (torrent, user, rating, added) VALUES (".mysql_real_escape_string($id).", " . mysql_real_escape_string($CURUSER["id"]) . ", ".mysql_real_escape_string($rating).", NOW())");
if (!$res) {
	if (mysql_errno() == 1062)
		stderr("Error!","You have already rated this torrent.");
	else
		stderr("Error!",mysql_error());
}

sql_query("UPDATE torrents SET numratings = numratings + 1, ratingsum = ratingsum + ".mysql_real_escape_string($rating)." WHERE id = ".mysql_real_escape_string($id));
//===add karma
UserHandle::KPS("+","3.0",$CURUSER["id"]);
//===end
header("Refresh: 0; url=details.php?id=$id&rated=1");
?>