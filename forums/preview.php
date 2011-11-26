<?php
$rootpath = '../';
require_once($rootpath."include/bittorrent.php");

loggedinorreturn();
$body = $_POST['body'];
print ("<h2>Preview Post</h2>");
print("<table class=main width=100% border=1 cellspacing=0 cellpadding=10 align=left>\n");
print ("<tr><td align=left>".format_comment($body)."</tr></td></table><br /><br />");
?>