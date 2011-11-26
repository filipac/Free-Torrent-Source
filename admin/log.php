<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;


ADMIN::check();
        
// delete items older than a week
$secs = 24 * 60 * 60;
stdhead("Site log");
sql_query("DELETE FROM sitelog WHERE " . gmtime() . " - UNIX_TIMESTAMP(added) > $secs") or sqlerr(__FILE__, __LINE__);

_ts('Search Log');
print("<form method=\"get\" action=searchlog.php>\n");
print("<input type=\"text\" name=\"query\" size=\"40\" value=\"" . htmlspecialchars($searchstr) . "\">\n");
print("<input type=submit value=" . SEARCH . " style='height: 20px' /></form>\n");
_te();

$res = sql_query("SELECT COUNT(*) FROM sitelog");
$row = mysql_fetch_array($res);
$count = $row[0];

$perpage = 20;

list($pagertop, $pagerbottom, $limit) = pager(50, $count, "log.php?");

$res = sql_query("SELECT added, txt FROM sitelog ORDER BY added DESC $limit") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) == 0)


  print("<b>Log is empty</b>\n");
else
{

//echo $pagertop;

  print("<p>&nbsp;</p><table border=1 cellspacing=0 cellpadding=5 width=100%>\n");
  print("<tr><td class=tabletitle align=left>Date</td><td class=tabletitle align=left>Time</td><td class=tabletitle align=left>Event</td></tr>\n");
  while ($arr = mysql_fetch_assoc($res))
  {
$color = 'black';
if (strpos($arr['txt'],'was uploaded by')) $color = "green";
if (strpos($arr['txt'],'was deleted by')) $color = "red";
if (strpos($arr['txt'],'was added to the Request section')) $color = "purple";
if (strpos($arr['txt'],'was edited by')) $color = "blue";
if (strpos($arr['txt'],'site settings updated by')) $color = "darkred";

      $date = substr($arr['added'], 0, strpos($arr['added'], " "));
      $time = substr($arr['added'], strpos($arr['added'], " ") + 1);
//   print("<tr><td>$date</td><td>$time</td><td align=left></td></tr>\n");

    print("<tr class=tableb><td>$date</td><td>$time</td><td align=left><font color='".$color."'>".$arr['txt']."</font></td></tr>\n");
  }
  print("</table>");
}
echo $pagerbottom;

print("<p>Times are in GMT.</p>\n");

stdfoot();

?>