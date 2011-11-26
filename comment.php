<?php

require_once("include/bittorrent.php");

$action = $_GET["action"];



loggedinorreturn();

parked();
if ($action == "add")
{
if ($_SERVER["REQUEST_METHOD"] == "POST")
 {
global $___flood___,$usergroups;
$___flood___->protect('last_comment','comment',$usergroups['antifloodtime'],1);


	$torrentid = 0 + $_POST["tid"];
	int_check($torrentid,true);

$res = sql_query("SELECT name, owner FROM torrents WHERE id = $torrentid") or sqlerr(__FILE__,__LINE__);
$arr = mysql_fetch_array($res);
if (!$arr)
 stderr("Error", "No torrent with ID.");

$text = trim($_POST["text"]);
if (!$text)
stderr("Error", "Comment body cannot be empty!");

sql_query("INSERT INTO comments (user, torrent, added, text, ori_text) VALUES (" .
    $CURUSER["id"] . ",$torrentid, '" . get_date_time() . "', " . sqlesc($text) .
     "," . sqlesc($text) . ")");

$newid = mysql_insert_id();

sql_query("UPDATE torrents SET comments = comments + 1 WHERE id = $torrentid");
$ras = sql_query("SELECT commentpm FROM users WHERE id = $arr[owner]") or sqlerr(__FILE__,__LINE__);
                 $arg = mysql_fetch_array($ras);

                 if($arg["commentpm"] == 'yes' && $CURUSER['id'] != $arr["owner"])
                    {
$added = sqlesc(get_date_time());
$notifs = sqlesc("You have received a comment on your torrent [url=$DEFAULTBASEURL/details.php?id=$torrentid] " . $arr['name'] . "[/url].");
sql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES(0, " . $arr['owner'] . ", $notifs, $added)") or sqlerr(__FILE__, __LINE__);
                     }

//===add karma
UserHandle::KPS("+","0.5",$CURUSER["id"]);
//===end

// Update Last comment sent...
$___flood___->update('last_comment');

header("Refresh: 0; url=details.php?id=$torrentid&viewcomm=$newid#comm$newid");

die;
}

	$torrentid = 0 + $_GET["tid"];
	int_check($torrentid,true);

$res = sql_query("SELECT name, owner FROM torrents WHERE id = $torrentid") or sqlerr(__FILE__,__LINE__);
$arr = mysql_fetch_array($res);
if (!$arr)
stderr("Error", "No torrent with ID.");

stdhead("Add a comment to \"" . $arr["name"] . "\"");

print("<h1>Add a comment to \"" . htmlspecialchars($arr["name"]) . "\"</h1>\n");
print("<p><form method=post name=\"compose\" action=\"comment.php?action=add\">\n");
print("<input type=\"hidden\" name=\"tid\" value=\"$torrentid\"/>\n");
print("<p align=center><table border=1 cellspacing=1>\n");
print("<tr><td align=center>\n");
textbbcode("compose","text",htmlspecialchars(unesc($arr["texxt"])));
print("</td></tr>\n");

 print("<tr><td align=center colspan=2><input type=submit value='".Okay."' class=btn></td></tr>\n");


$res = sql_query("SELECT comments.id, text, user, comments.added, editedby, editedat, avatar, warned, ".
                  "username, title, class, last_access, donor FROM comments LEFT JOIN users ON comments.user = users.id WHERE torrent = " .
                  "$torrentid ORDER BY comments.id LIMIT 5");

$allrows = array();
while ($row = mysql_fetch_array($res))
$allrows[] = $row;
echo '</table>';
if (count($allrows)) {
print("<h2>Most recent comments, in reverse order</h2>\n");
commenttable($allrows);
}

stdfoot();

die;
}
elseif ($action == "edit")
{
$commentid = 0 + $_GET["cid"];
int_check($commentid,true);

$res = sql_query("SELECT c.*, t.name FROM comments AS c JOIN torrents AS t ON c.torrent = t.id WHERE c.id=$commentid") or sqlerr(__FILE__,__LINE__);
$arr = mysql_fetch_array($res);
if (!$arr)
stderr("Error", "Invalid ID.");

if ($arr["user"] != $CURUSER["id"] && get_user_class() < UC_MODERATOR)
stderr("Error", "Permission denied.");

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
$text = $_POST["text"];
 $returnto =  htmlentities($_POST["returnto"]) ? htmlentities($_POST["returnto"]) : htmlentities($_SERVER["HTTP_REFERER"]);

if ($text == "")
 stderr("Error", "Comment body cannot be empty!");

$text = sqlesc($text);

$editedat = sqlesc(get_date_time());

sql_query("UPDATE comments SET text=$text, editedat=$editedat, editedby=$CURUSER[id] WHERE id=$commentid") or sqlerr(__FILE__, __LINE__);

if ($returnto)
 header("Location: $returnto");
else
 header("Location: $BASEURL/");      // change later ----------------------


die;
}

stdhead("Edit comment to \"" . $arr["name"] . "\"");

print("<h1>Edit comment to \"" . htmlspecialchars($arr["name"]) . "\"</h1><p>\n");
print("<form method=\"post\" name=\"compose\"action=\"comment.php?action=edit&cid=$commentid\">\n");
print("<input type=\"hidden\" name=\"returnto\" value=\"" . htmlentities($_SERVER["HTTP_REFERER"]) . "\" />\n");
print("<input type=\"hidden\" name=\"cid\" value=\"$commentid\" />\n");
print("<p align=center><table border=1 cellspacing=1>\n");
print("<tr><td align=center>\n");
textbbcode("compose","text",htmlspecialchars(unesc($arr["text"])));
print("<tr><td align=center colspan=2><input type=submit value='".Okay."' class=btn></td></tr>\n");
stdfoot();

die;
}
elseif ($action == "delete")
{
if (get_user_class() < UC_MODERATOR)
stderr("Error", "Permission denied.");

$commentid = 0 + $_GET["cid"];
$sure = $_GET["sure"];
int_check($commentid,true);

if (!$sure)
{
$referer = $_SERVER["HTTP_REFERER"];
stderr("Delete comment", "You are about to delete a comment. Click\n" .
"<a href=?action=delete&cid=$commentid&sure=1" .
($referer ? "&returnto=" . urlencode($referer) : "") .
">here</a> if you are sure.",false);
}else {
	int_check($sure,true);
}


$res = sql_query("SELECT torrent,user FROM comments WHERE id=$commentid")  or sqlerr(__FILE__,__LINE__);
$arr = mysql_fetch_array($res);
if ($arr) {
$torrentid = $arr["torrent"];
$userpostid = $arr["user"];
}else{
	stderr("Error", "Invalid ID.");
}

sql_query("DELETE FROM comments WHERE id=$commentid") or sqlerr(__FILE__,__LINE__);
if ($torrentid && mysql_affected_rows() > 0)
sql_query("UPDATE torrents SET comments = comments - 1 WHERE id = $torrentid");

//===add karma
UserHandle::KPS("-","5.0",$userpostid);
//===end

$returnto = $_GET["returnto"] ? htmlentities($_GET["returnto"]) : htmlentities($_SERVER["HTTP_REFERER"]);

if ($returnto)
header("Location: $returnto");
else
header("Location: $BASEURL/");      // change later ----------------------


die;
}
elseif ($action == "vieworiginal")
{
if (get_user_class() < UC_MODERATOR)
stderr("Error", "Permission denied.");

$commentid = 0 + $_GET["cid"];
int_check($commentid,true);

$res = sql_query("SELECT c.*, t.name FROM comments AS c JOIN torrents AS t ON c.torrent = t.id WHERE c.id=$commentid") or sqlerr(__FILE__,__LINE__);
$arr = mysql_fetch_array($res);
if (!$arr)
stderr("Error", "Invalid ID.");

stdhead("Original comment");
print("<h1>Original contents of comment #$commentid</h1><p>\n");
print("<table width=100% border=1 cellspacing=0 cellpadding=5>");
print("<tr><td class=comment>\n");
echo format_comment(htmlspecialchars($arr["ori_text"]));
print("</td></tr></table>\n");


$returnto =  htmlentities($_SERVER["HTTP_REFERER"]);

// $returnto = "details.php?id=$torrentid&viewcomm=$commentid#$commentid";

if ($returnto)
print("<p><font size=small>(<a href=$returnto>back</a>)</font></p>\n");

stdfoot();

die;
}
else
stderr("Error", "Unknown action.");

die;
?>