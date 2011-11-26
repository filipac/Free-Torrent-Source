<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;



loggedinorreturn();

parked();
stdhead("Search Log Page");


$res = sql_query("SELECT * FROM sitelog WHERE txt LIKE '%$query%' ORDER BY txt DESC") or sqlerr();
$num = mysql_num_rows($res);

  print("<table border=1 cellspacing=0 cellpadding=5>\n");
  print("<tr><td class=tabletitle align=left>Date</td><td class=tabletitle align=left>Time</td><td class=tabletitle align=left>Event</td></tr>\n");
  while ($arr = mysql_fetch_assoc($res))
  {
$color = 'black';
if (strpos($arr['txt'],'was uploaded by')) $color = "green";
if (strpos($arr['txt'],'was deleted by')) $color = "red";
if (strpos($arr['txt'],'was added to the Request section')) $color = "purple";
if (strpos($arr['txt'],'was edited by')) $color = "blue";
      $date = substr($arr['added'], 0, strpos($arr['added'], " "));
      $time = substr($arr['added'], strpos($arr['added'], " ") + 1);
//   print("<tr><td>$date</td><td>$time</td><td align=left></td></tr>\n");

    print("<tr class=tableb><td>$date</td><td>$time</td><td align=left><font color='".$color."'>".$arr['txt']."</font></td></tr>\n");
  }
  print("</table>");


 
stdfoot();
die;

?>