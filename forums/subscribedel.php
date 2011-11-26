<?php
$rootpath = '../';
require_once($rootpath."include/bittorrent.php");


loggedinorreturn();

if (!isset($_POST[subscriptions]))
bark ("Nothing selected");

$res2 = mysql_query ("SELECT id, userid FROM subscriptions WHERE id IN (" . implode(", ", $_POST[subscriptions]) . ")") or sqlerr(__FILE__, __LINE__);

while ($arr = mysql_fetch_assoc($res2))
{
if (($arr[userid] == $CURUSER[id]) || (get_user_class() > 3))
mysql_query ("DELETE FROM subscriptions WHERE id = $arr[id]") or sqlerr(__FILE__, __LINE__);
else
bark("That wasn't your subscribed thread to delete!");
}

header("Refresh: 0; url=" . $_SERVER['HTTP_REFERER']);
?>