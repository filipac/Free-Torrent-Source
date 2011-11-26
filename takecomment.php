<?php
require "include/bittorrent.php";
loggedinorreturn();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
header ("Content-Type: text/html; charset=iso-8859-1");
$torrentid = 0 + $_POST["tid"];
if (empty($torrentid)) {
echo "<div id=class>No torrent id recived</div><script>
$.jGrowl('Error: No torrent id recived');
</script>";
die();
}
global $usergroups,$___flood___;
$___flood___->protect('last_comment','comment',$usergroups['antifloodtime']);
if (!is_valid_id($torrentid))
die("<div id=class>Invailed ID</div><script>
$.jGrowl('Error: Invailed ID');
</script>"); 
$res = sql_query("SELECT name FROM torrents WHERE id = $torrentid") or sqlerr(__FILE__,__LINE__);  $arr = mysql_fetch_array($res);
if (!$arr)
die("<div id=class>No vailed torrentid.</div><script>
$.jGrowl('Error: No vailed torrentid.');
</script>");


// Strip from HTML Tags and check if it not empty
$text = $_POST["text"];
if (!$text)
die("<div class=error>You need to enter text!</div><script>
$.jGrowl('Error: You need to enter text!');
</script>");

sql_query("INSERT INTO comments (user, torrent, added, text, ori_text) VALUES (" .
$CURUSER["id"] . ",$torrentid, '" . get_date_time() . "', " . sqlesc($text) .
"," . sqlesc($text) . ")");

sql_query("UPDATE torrents SET comments = comments + 1 WHERE id = $torrentid");
$ras = mysql_query("SELECT commentpm FROM users WHERE id = $arr[owner]") or sqlerr(__FILE__,__LINE__);
                 $arg = mysql_fetch_array($ras);

                 if($arg['commentpm'] == 'yes')
                    {
$added = sqlesc(get_date_time());
$subby = sqlesc("Someone has commented on your torrent");
$notifs = sqlesc("You have received a comment on your torrent [url=$DEFAULTBASEURL/details.php?id=$torrentid] " . $arr['name'] . "[/url].");
mysql_query("INSERT INTO messages (sender, receiver, subject, msg, added) VALUES(0, " . $arr['owner'] . ", $subby, $notifs, $added)") or sqlerr(__FILE__, __LINE__);
                     }  
UserHandle::KPS("+","0.5",$CURUSER["id"]);

// Update Last comment sent...
$___flood___->update('last_comment');
?>
<script>
$.jGrowl('Comment added!');
</script>
<?php
}
?>