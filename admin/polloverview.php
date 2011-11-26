<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;
 
 ADMIN::check();

function cleanit($array, $index, $maxlength)
  {
    if (isset($array["{$index}"]))
    {
       $input = substr($array["{$index}"], 0, $maxlength);
       $input = mysql_real_escape_string($input);
       return ($input);
    }
    return NULL;
  }
  
 //$action = $_GET["action"];
 $pollid = cleanit($_GET, "id", 2);
 //$returnto = $_GET["returnto"];
 
   
 stdhead("Polls Overview");
 
 if (!(isset($_GET['id']))) {
   
$sql = sql_query("SELECT id, added, question FROM polls ORDER BY id DESC") or sqlerr();
//$sql = db_query("SELECT id, added, question FROM polls ORDER BY id DESC");
print("<h1>Polls Overview</h1>\n");

print("<p><table width=100% border=1 cellspacing=0 cellpadding=5><tr>\n" . 
 "<td class=colhead align=center>ID</td><td class=colhead>Added</td><td class=colhead>Question</td></tr>\n");
 
 if (mysql_num_rows($sql) == 0) {
  print("<tr><td colspan=2>Sorry...There are no users that voted!</td></tr></table>");
  stdfoot();
  exit;
  }
  
 while ($poll = mysql_fetch_assoc($sql))
 {
  $added = gmdate("Y-m-d h-i-s",strtotime($poll['added'])) . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($poll["added"]))) . " ago)";
  print("<tr><td align=center><a href=\"polloverview.php?id={$poll['id']}\">{$poll['id']}</a></td><td>{$added}</td><td><a href=\"polloverview.php?id={$poll['id']}\">{$poll['question']}</a></td></tr>\n");
  
 }
 
 print("</table>\n");
 
} else {

if (isset($_GET['id'])) {
   
$sql = sql_query("SELECT * FROM polls WHERE id = {$pollid} ORDER BY id DESC") or sqlerr();
print("<h1>Polls Overview</h1>\n");

print("<p><table width=100% border=1 cellspacing=0 cellpadding=5><tr>\n" . 
 "<td class=colhead align=center>ID</td><td class=colhead>Added</td><td class=colhead>Question</td></tr>\n");
 
 if (mysql_num_rows($sql) == 0) {
  print("<tr><td colspan=2>Sorry...There are no polls with that ID!</td></tr></table>");
  stdfoot();
  exit;
  }
  
 while ($poll = mysql_fetch_assoc($sql))
 {
  $o = array($poll["option0"], $poll["option1"], $poll["option2"], $poll["option3"], $poll["option4"],
   $poll["option5"], $poll["option6"], $poll["option7"], $poll["option8"], $poll["option9"],
   $poll["option10"], $poll["option11"], $poll["option12"], $poll["option13"], $poll["option14"],
   $poll["option15"], $poll["option16"], $poll["option17"], $poll["option18"], $poll["option19"]);
   
  $added = gmdate("Y-m-d h-i-s",strtotime($poll['added'])) . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($poll["added"]))) . " ago)";
  print("<tr><td align=center><a href=\"polloverview.php?id={$poll['id']}\">{$poll['id']}</a></td><td>{$added}</td><td><a href=\"polloverview.php?id={$poll['id']}\">{$poll['question']}</a></td></tr>\n");
  
 }
 
 print("</table><br>\n");
 
 print("<h1>Poll Questions</h1><br>\n");
 print("<table width=100% border=1 cellspacing=0 cellpadding=5><tr><td class=colhead>Option No</td><td class=colhead>Questions</td></tr>\n");
 foreach($o as $key=>$value) {
  if($value != "")
  print("<tr><td>{$key}</td><td>{$value}</td></tr>\n");
  }
 print("</table>\n");
 //print_r($o);
 
 $sql2 = sql_query("SELECT pollanswers. * , users.username FROM pollanswers LEFT JOIN users ON users.id = pollanswers.userid WHERE pollid = {$pollid} AND selection < 20 ORDER  BY users.id DESC ") or sqlerr();
 
 print("<h1>Polls User Overview</h1>\n");

print("<p><table width=100% border=1 cellspacing=0 cellpadding=5><tr>\n" . 
 "<td class=colhead align=center>UserID</td><td class=colhead>Selection</td></tr>\n");
 
 if (mysql_num_rows($sql2) == 0) {
  print("<tr><td colspan=2>Sorry...There are no users that voted!</td></tr></table>");
  stdfoot();
  exit;
  }
  
 while ($useras = mysql_fetch_assoc($sql2))
 {
  $username  = ($useras['username'] ? $useras['username'] : "Unknown");
  //$useras['selection']--;
  print("<tr><td>{$username}</td><td>{$o[$useras['selection']]}</td></tr>\n");
 }
 print("</table>\n");

}
} 
stdfoot();
?>