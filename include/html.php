<?php
function _ts($title) {
	print("<table border=1 cellspacing=0 width=100% cellpadding=5>\n");
print("<tr><td class=tabletitle align=left>$title</td></tr>\n");
print("<tr><td class=tableb align=left>");
}
function _te() {
	print("</td></tr></table>\n");
}
?>