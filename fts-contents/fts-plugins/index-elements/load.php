<?php
/**
@Plugin Name:Index Element - Server Load
@Plugin URL:http://freetosu.berlios.de
@Description:Shows server's load. Works best on LINUX!
@Author: FTS Team
@Author URL: http://www.freetosu.berlios.de
@version: 0.1
**/
function _index_element_load() {
	echo _br;
	collapses('trload','<b>Tracker Load</b>');
echo '<table width=100% border=0 cellspacing=5 cellpadding=10 style="border:none;"><tr style="border:none;"><td align=center style="border:none;">';
$time_start = getmicrotime();
$time = round(getmicrotime() - $time_start,4);
$percent = $time * 60;
$time = round(getmicrotime() - $time_start,4);
$percent = $time * 60;
echo "<div align=\"center\">Our Tracker Load: ($percent %)</div><table class=blocklist align=center border=0 width=400><tr><td style='padding: 0px; background-image: url(pic/loadbarbg.gif); background-repeat: repeat-x'>";
//TRACKER LOAD
if ($percent <= 70) $pic_base_url = "pic/loadbargreen.gif";
     elseif ($percent <= 90) $pic_base_url = "pic/loadbaryellow.gif";
      else $pic_base_url = "pic/loadbarred.gif";
           $width = $percent * 4;
echo "<img height=15 width=$width src=\"$pic_base_url\" alt='$percent%'></td></tr></table><br>";
"</center><br>";
if (isset($load))
print("<tr><td class=blocklist>10min load average (%)</td><td align=right>$load</td></tr>\n");
print("<br>");
$time = round(getmicrotime() - $time_start,4);
$percent = $time * 60;
echo "<div align=\"center\">Global Server Load (All websites on current host servers): ($percent %)</div><table class=main align=center border=0 width=400><tr><td style='padding: 0px; background-image: url(pic/loadbarbg.gif); background-repeat: repeat-x'>";
if ($percent <= 70) $pic_base_url = "pic/loadbargreen.gif";
  elseif ($percent <= 90) $pic_base_url = "pic/loadbaryellow.gif";
   else $pic_base_url = "pic/loadbarred.gif";
        $width = $percent * 4;
echo "<img height=15 width=$width src=\"$pic_base_url\" alt='$percent%'></td></tr></table></table>";
collapsee();
}
add_action("index_elements","_index_element_load")
?>