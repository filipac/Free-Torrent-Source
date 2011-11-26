<?php
require_once("include/bittorrent.php");

loggedinorreturn();

stdhead("Reseed Request!");

begin_main_frame();

$reseedid = 0 + $_GET["reseedid"];
$owner = 0 + $_GET['owner'];


$res = sql_query("SELECT snatched.userid, snatched.torrentid, users.id FROM snatched inner join users on snatched.userid = users.id where snatched.finished = 'Yes' AND snatched.torrentid = $reseedid") or sqlerr();
$pn_msg = "User " . $CURUSER["username"] . " asked for a reseed on torrent $BASEURL/details.php?id=" . $reseedid . " !\nThank You!";
while($row = mysql_fetch_assoc($res)) {
send_message($row['userid'],$pn_msg,'Reseed request');
}

send_message($owner,$pn_msg,'Reseed request');
print("It worked :)");
end_main_frame();
stdfoot();
?>