<?php
/*
+--------------------------------------------------------------------------
|   MySQL driven FAQ version 1.1 Beta
|   ========================================
|   by avataru
|   (c) 2002 - 2005 avataru
|   http://www.avataru.net
|   ========================================
|   Web: http://www.avataru.net
|   Release: 1/9/2005 1:03 AM
|   Email: avataru@avataru.net
|   Tracker: http://www.sharereactor.ro
+---------------------------------------------------------------------------
|
|   > FAQ public page
|   > Written by avataru
|   > Date started: 1/7/2005
|
+--------------------------------------------------------------------------
*/
ob_start("ob_gzhandler");
require "include/bittorrent.php";

//credits for the debug class go to the IPB team (was taken from IPB's index.php)

$Debug = new Debug;
$Debug->startTimer();
stdhead("FAQ");
cache_check ('faq');
begin_main_frame();

begin_frame("Welcome to $SITENAME!");
?>
Our goal is not to become another Bytemonsoon or Suprnova (not dizzying either of them though).
The goal is to provide the absolutely latest stuff. Therefore, only specially authorised users have
permission to upload torrents. If you have access to 0-day stuff do not hesitate to
<a class=altlink href=staff.php>contact</a> us!<br>
<br>
This is a private tracker, and you have to register before you can get full access to the site.
Before you do anything here at <?PHP echo $SITENAME ?> we suggest you read the <a class=altlink href=rules.php>rules</a>!
There are only a few rules to abide by, but we do enforce them!<br>
<br>
Before you go any further you should read the <?PHP echo $SITENAME ?> <a class=altlink href=useragreement.php>user agreement</a>.
<?php
end_frame();

$res = sql_query("SELECT `id`, `question`, `flag` FROM `faq` WHERE `type`='categ' ORDER BY `order` ASC");
while ($arr = mysql_fetch_array($res, MYSQL_BOTH)) {
 $faq_categ[$arr[id]][title] = $arr[question];
 $faq_categ[$arr[id]][flag] = $arr[flag];
}

$res = sql_query("SELECT `id`, `question`, `answer`, `flag`, `categ` FROM `faq` WHERE `type`='item' ORDER BY `order` ASC");
while ($arr = mysql_fetch_array($res, MYSQL_BOTH)) {
 $faq_categ[$arr[categ]][items][$arr[id]][question] = $arr[question];
 $faq_categ[$arr[categ]][items][$arr[id]][answer] = $arr[answer];
 $faq_categ[$arr[categ]][items][$arr[id]][flag] = $arr[flag];
}

if (isset($faq_categ)) {
// gather orphaned items
 foreach ($faq_categ as $id => $temp) {
  if (!array_key_exists("title", $faq_categ[$id])) {
   foreach ($faq_categ[$id][items] as $id2 => $temp) {
    $faq_orphaned[$id2][question] = $faq_categ[$id][items][$id2][question];
	$faq_orphaned[$id2][answer] = $faq_categ[$id][items][$id2][answer];
    $faq_orphaned[$id2][flag] = $faq_categ[$id][items][$id2][flag];
    unset($faq_categ[$id]);
   }
  }
 }

 begin_frame("Contents");
 foreach ($faq_categ as $id => $temp) {
  if ($faq_categ[$id][flag] == "1") {
   print("<ul>\n<li><a href=\"#". $id ."\"><b>". $faq_categ[$id][title] ."</b></a>\n<ul>\n");
   if (array_key_exists("items", $faq_categ[$id])) {
    foreach ($faq_categ[$id][items] as $id2 => $temp) {
	 if ($faq_categ[$id][items][$id2][flag] == "1") print("<li><a href=\"#". $id2 ."\" class=\"altlink\">". $faq_categ[$id][items][$id2][question] ."</a></li>\n");
	 elseif ($faq_categ[$id][items][$id2][flag] == "2") print("<li><a href=\"#". $id2 ."\" class=\"altlink\">". $faq_categ[$id][items][$id2][question] ."</a> <img src=\"pic/updated.png\" alt=\"Updated\" width=\"46\" height=\"11\" align=\"absbottom\"></li>\n");
	 elseif ($faq_categ[$id][items][$id2][flag] == "3") print("<li><a href=\"#". $id2 ."\" class=\"altlink\">". $faq_categ[$id][items][$id2][question] ."</a> <img src=\"pic/new.png\" alt=\"New\" width=\"27\" height=\"11\" align=\"absbottom\"></li>\n");
    }
   }
   print("</ul>\n</li>\n</ul>\n<br />\n");
  }
 }
 end_frame();

 foreach ($faq_categ as $id => $temp) {
  if ($faq_categ[$id][flag] == "1") {
   $frame = $faq_categ[$id][title] ." - <a href=\"#top\">Top</a>";
   begin_frame($frame);
   print("<a name=\"#". $id ."\" id=\"". $id ."\"></a>\n");
   if (array_key_exists("items", $faq_categ[$id])) {
    foreach ($faq_categ[$id][items] as $id2 => $temp) {
	 if ($faq_categ[$id][items][$id2][flag] != "0") {
      print("<br />\n<b>". $faq_categ[$id][items][$id2][question] ."</b><a name=\"#". $id2 ."\" id=\"". $id2 ."\"></a>\n<br />\n");
      print("<br />\n". $faq_categ[$id][items][$id2][answer] ."\n<br /><br />\n");
	 }
    }
   }
   end_frame();
  }
 }

}

end_frame();
$bmark = $Debug->endTimer();

print("<p align=\"center\"><font size=\"1\" color=\"#0000FF\"><b>FAQ Management script by <a href=\"http://www.sharereactor.ro\">avataru</a></b> - Page generated in $bmark seconds.</font></p>");
cache_save ('faq');
end_main_frame();
stdfoot();
?>