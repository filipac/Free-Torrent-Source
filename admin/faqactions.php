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
|   > FAQ Management actions
|   > Written by avataru
|   > Date started: 1/7/2005
|
+--------------------------------------------------------------------------
*/

$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;

ADMIN::check();

// ACTION: reorder - reorder sections and items
if ($_GET[action] == "reorder") {
 foreach($_POST[order] as $id => $position) sql_query("UPDATE `faq` SET `order`='$position' WHERE id='$id'") or sqlerr();
 header("Refresh: 0; url=faqmanage.php"); 
}

// ACTION: edit - edit a section or item
elseif ($_GET[action] == "edit" && isset($_GET[id])) {
 stdhead("FAQ Management");
 begin_main_frame();
 print("<h1 align=\"center\">Edit Section or Item</h1>");

 $res = sql_query("SELECT * FROM `faq` WHERE `id`='$_GET[id]' LIMIT 1");
 while ($arr = mysql_fetch_array($res, MYSQL_BOTH)) {
  $arr[question] = htmlspecialchars($arr[question]);
  $arr[answer] = htmlspecialchars($arr[answer]);
  if ($arr[type] == "item") {
   print("<form method=\"post\" action=\"faqactions.php?action=edititem\">");
   print("<table border=\"1\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\">\n");
   print("<tr><td>ID:</td><td>$arr[id] <input type=\"hidden\" name=\"id\" value=\"$arr[id]\" /></td></tr>\n");
   print("<tr><td>Question:</td><td><input style=\"width: 300px;\" type=\"text\" name=\"question\" value=\"$arr[question]\" /></td></tr>\n");
   print("<tr><td style=\"vertical-align: top;\">Answer:</td><td><textarea style=\"width: 300px; height=100px;\" name=\"answer\">$arr[answer]</textarea></td></tr>\n");
   if ($arr[flag] == "0") print("<tr><td>Status:</td><td><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #FF0000;\" selected=\"selected\">Hidden</option><option value=\"1\" style=\"color: #000000;\">Normal</option><option value=\"2\" style=\"color: #0000FF;\">Updated</option><option value=\"3\" style=\"color: #008000;\">New</option></select></td></tr>");
   elseif ($arr[flag] == "2") print("<tr><td>Status:</td><td><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #FF0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\">Normal</option><option value=\"2\" style=\"color: #0000FF;\" selected=\"selected\">Updated</option><option value=\"3\" style=\"color: #008000;\">New</option></select></td></tr>");
   elseif ($arr[flag] == "3") print("<tr><td>Status:</td><td><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #FF0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\">Normal</option><option value=\"2\" style=\"color: #0000FF;\">Updated</option><option value=\"3\" style=\"color: #008000;\" selected=\"selected\">New</option></select></td></tr>");
   else print("<tr><td>Status:</td><td><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #FF0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\" selected=\"selected\">Normal</option><option value=\"2\" style=\"color: #0000FF;\">Updated</option><option value=\"3\" style=\"color: #008000;\">New</option></select></td></tr>");
   print("<tr><td>Category:</td><td><select style=\"width: 300px;\" name=\"categ\" />");
   $res2 = sql_query("SELECT `id`, `question` FROM `faq` WHERE `type`='categ' ORDER BY `order` ASC");
   while ($arr2 = mysql_fetch_array($res2, MYSQL_BOTH)) {
    $selected = ($arr2[id] == $arr[categ]) ? " selected=\"selected\"" : "";
    print("<option value=\"$arr2[id]\"". $selected .">$arr2[question]</option>");
   }
   print("</td></tr>\n");
   print("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"edit\" value=\"Edit\" style=\"width: 60px;\"></td></tr>\n");
   print("</table>");
  }
  elseif ($arr[type] == "categ") {
   print("<form method=\"post\" action=\"faqactions.php?action=editsect\">");
   print("<table border=\"1\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\">\n");
   print("<tr><td>ID:</td><td>$arr[id] <input type=\"hidden\" name=\"id\" value=\"$arr[id]\" /></td></tr>\n");
   print("<tr><td>Title:</td><td><input style=\"width: 300px;\" type=\"text\" name=\"title\" value=\"$arr[question]\" /></td></tr>\n");
   if ($arr[flag] == "0") print("<tr><td>Status:</td><td><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #FF0000;\" selected=\"selected\">Hidden</option><option value=\"1\" style=\"color: #000000;\">Normal</option></select></td></tr>");
   else print("<tr><td>Status:</td><td><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #FF0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\" selected=\"selected\">Normal</option></select></td></tr>");
   print("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"edit\" value=\"Edit\" style=\"width: 60px;\"></td></tr>\n");
   print("</table>");
  }
 }

 end_main_frame();
 stdfoot();
}

// subACTION: edititem - edit an item
elseif ($_GET[action] == "edititem" && $_POST[id] != NULL && $_POST[question] != NULL && $_POST[answer] != NULL && $_POST[flag] != NULL && $_POST[categ] != NULL) {
 $question = addslashes($_POST[question]);
 $answer = addslashes($_POST[answer]);
 sql_query("UPDATE `faq` SET `question`='$question', `answer`='$answer', `flag`='$_POST[flag]', `categ`='$_POST[categ]' WHERE id='$_POST[id]'") or sqlerr();
 header("Refresh: 0; url=faqmanage.php"); 
}

// subACTION: editsect - edit a section
elseif ($_GET[action] == "editsect" && $_POST[id] != NULL && $_POST[title] != NULL && $_POST[flag] != NULL) {
 $title = addslashes($_POST[title]);
 sql_query("UPDATE `faq` SET `question`='$title', `answer`='', `flag`='$_POST[flag]', `categ`='0' WHERE id='$_POST[id]'") or sqlerr();
 header("Refresh: 0; url=faqmanage.php"); 
}

// ACTION: delete - delete a section or item
elseif ($_GET[action] == "delete" && isset($_GET[id])) {
 if ($_GET[confirm] == "yes") {
  sql_query("DELETE FROM `faq` WHERE `id`='$_GET[id]' LIMIT 1") or sqlerr();
  header("Refresh: 0; url=faqmanage.php"); 
 }
 else {
  stdhead("FAQ Management");
  begin_main_frame();
  print("<h1 align=\"center\">Confirmation required</h1>");
  print("<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" width=\"95%\">\n<tr><td align=\"center\">Please click <a href=\"faqactions.php?action=delete&id=$_GET[id]&confirm=yes\">here</a> to confirm.</td></tr>\n</table>\n");
  end_main_frame();
  stdfoot();
 }
}

// ACTION: additem - add a new item
elseif ($_GET[action] == "additem" && $_GET[inid]) {
 stdhead("FAQ Management");
 begin_main_frame();
 print("<h1 align=\"center\">Add Item</h1>");
 print("<form method=\"post\" action=\"faqactions.php?action=addnewitem\">");
 print("<table border=\"1\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\">\n");
 print("<tr><td>Question:</td><td><input style=\"width: 300px;\" type=\"text\" name=\"question\" value=\"\" /></td></tr>\n");
 print("<tr><td style=\"vertical-align: top;\">Answer:</td><td><textarea style=\"width: 300px; height=100px;\" name=\"answer\"></textarea></td></tr>\n");
 print("<tr><td>Status:</td><td><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #FF0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\">Normal</option><option value=\"2\" style=\"color: #0000FF;\">Updated</option><option value=\"3\" style=\"color: #008000;\" selected=\"selected\">New</option></select></td></tr>");
 print("<tr><td>Category:</td><td><select style=\"width: 300px;\" name=\"categ\" />");
 $res = sql_query("SELECT `id`, `question` FROM `faq` WHERE `type`='categ' ORDER BY `order` ASC");
 while ($arr = mysql_fetch_array($res, MYSQL_BOTH)) {
  $selected = ($arr[id] == $_GET[inid]) ? " selected=\"selected\"" : "";
  print("<option value=\"$arr[id]\"". $selected .">$arr[question]</option>");
 }
 print("</td></tr>\n");
 print("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"edit\" value=\"Add\" style=\"width: 60px;\"></td></tr>\n");
 print("</table>");
}

// ACTION: addsection - add a new section
elseif ($_GET[action] == "addsection") {
 stdhead("FAQ Management");
 begin_main_frame();
 print("<h1 align=\"center\">Add Section</h1>");
 print("<form method=\"post\" action=\"faqactions.php?action=addnewsect\">");
 print("<table border=\"1\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\">\n");
 print("<tr><td>Title:</td><td><input style=\"width: 300px;\" type=\"text\" name=\"title\" value=\"\" /></td></tr>\n");
 print("<tr><td>Status:</td><td><select name=\"flag\" style=\"width: 110px;\"><option value=\"0\" style=\"color: #FF0000;\">Hidden</option><option value=\"1\" style=\"color: #000000;\" selected=\"selected\">Normal</option></select></td></tr>");
 print("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"edit\" value=\"Add\" style=\"width: 60px;\"></td></tr>\n");
 print("</table>");
}

// subACTION: addnewitem - add a new item to the db
elseif ($_GET[action] == "addnewitem" && $_POST[question] != NULL && $_POST[answer] != NULL && $_POST[flag] != NULL && $_POST[categ] != NULL) {
 $question = addslashes($_POST[question]);
 $answer = addslashes($_POST[answer]);
 $res = sql_query("SELECT MAX(`order`) FROM `faq` WHERE `type`='item' AND `categ`='$_POST[categ]'");
 while ($arr = mysql_fetch_array($res, MYSQL_BOTH)) $order = $arr[0] + 1;
 sql_query("INSERT INTO `faq` (`type`, `question`, `answer`, `flag`, `categ`, `order`) VALUES ('item', '$question', '$answer', '$_POST[flag]', '$_POST[categ]', '$order')") or sqlerr();
 header("Refresh: 0; url=faqmanage.php"); 
}

// subACTION: addnewsect - add a new section to the db
elseif ($_GET[action] == "addnewsect" && $_POST[title] != NULL && $_POST[flag] != NULL) {
 $title = addslashes($_POST[title]);
 $res = sql_query("SELECT MAX(`order`) FROM `faq` WHERE `type`='categ'");
 while ($arr = mysql_fetch_array($res, MYSQL_BOTH)) $order = $arr[0] + 1;
 sql_query("INSERT INTO `faq` (`type`, `question`, `answer`, `flag`, `categ`, `order`) VALUES ('categ', '$title', '', '$_POST[flag]', '0', '$order')") or sqlerr();
 header("Refresh: 0; url=faqmanage.php");
}

else header("Refresh: 0; url=faqmanage.php");
?>