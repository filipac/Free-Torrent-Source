<?php
require_once("include/bittorrent.php");
lang::load('delete');
/**
 * bark()
 *
 * @param mixed $msg
 * @return
 */


if (!mkglobal("id"))
	bark(d2);

$id = 0 + $id;
if (!$id)
	die();


loggedinorreturn();

$res = sql_query("SELECT name,owner,seeders FROM torrents WHERE id = $id");
$row = mysql_fetch_array($res);
if (!$row)
	die();

if ($CURUSER["id"] != $row["owner"] && get_user_class() < UC_MODERATOR)
	bark(d3);

$rt = 0 + $_POST["reasontype"];

if (!is_int($rt) || $rt < 1 || $rt > 5)
	bark(sprintf(d4,$rt));

$r = $_POST["r"];
$reason = $_POST["reason"];

if ($rt == 1)
	$reasonstr = d5;
elseif ($rt == 2)
	$reasonstr = d6 . ($reason[0] ? (": " . trim($reason[0])) : "!");
elseif ($rt == 3)
	$reasonstr = d7 . ($reason[1] ? (": " . trim($reason[1])) : "!");
elseif ($rt == 4)
{
	if (!$reason[2])
		bark(d8);
  $reasonstr = sprintf(d9,$SITENAME) . trim($reason[2]);
}
else
{
	if (!$reason[3])
		bark(d10);
  $reasonstr = trim($reason[3]);
}

deletetorrent($id);

if ($CURUSER["anonymous"]=='yes'){
write_log("Torrent $id ($row[name]) was deleted by Anonymous ($reasonstr)");
}
else
{
write_log("Torrent $id ($row[name]) was deleted by $CURUSER[username] ($reasonstr)");
}

//===remove karma
UserHandle::KPS("-","15.0",$row["owner"]);
//===end

stdhead(d11);

if (isset($_POST["returnto"]))
	$ret = "<a href=\"" . htmlspecialchars($_POST["returnto"]) . "\">".d12."</a>";
else
	$ret = "<a href=\"./\">".d13."</a>";

?>
<h2><?=d14?></h2>
<p><?= $ret ?></p>
<?php

stdfoot();

?>