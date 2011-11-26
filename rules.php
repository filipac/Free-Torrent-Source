<?php
ob_start("ob_gzhandler");
require "include/bittorrent.php";

stdhead("Rules");
cache_check ('rules');
//print("<td valign=top style=\"padding: 10px;\" colspan=2 align=center>");
begin_main_frame();
?>
<?php $res = sql_query("select * from rules order by id");
while ($arr=mysql_fetch_assoc($res)){
if ($arr["public"]=="yes"){
print("<table width=100% border=1 cellspacing=0 cellpadding=10>");
print("<h2>$arr[title]</h2><tr><td>\n");
print(format_comment($arr["text"]));
print("</td></tr>");
end_frame(); }
elseif($arr["public"]=="no" && $arr["class"]<=$CURUSER["class"]){
print("<br><table width=100% border=1 cellspacing=0 cellpadding=10>");
print("<h2>$arr[title]</h2><tr><td>\n");
print(format_comment($arr["text"]));
print("</td></tr>");
end_frame();
}
}
cache_save ('rules');
end_main_frame();
stdfoot(); 
?>