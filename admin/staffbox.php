<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;

  

ADMIN::check();
  $action = $HTTP_GET_VARS["action"];

///////////////////////////
//        SHOW PM'S        //
/////////////////////////

if (!$action) {

 if ((!ur::cstaff()) AND get_user_class() < UC_MODERATOR)
   stderr("Error", "Permission denied.");

     stdhead("Staff PM's");

$res = sql_query("SELECT count(id) FROM staffmessages") or die(mysql_error());
$row = mysql_fetch_array($res);

$url = " .$_SERVER[PHP_SELF]?";
$count = $row[0];
$perpage = 20;
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $url);

print("<h1 align=center>Staff PM's</h1>");

if ($count == 0) {
print("<h2>No messages yet!</h2>");
} 
else {

echo $pagertop;

      begin_main_frame();

print("<table width=765 border=1 cellspacing=0 cellpadding=5 align=center>\n");
print("
<tr>
<td class=colhead align=left>Subject</td>
<td class=colhead align=left>Sender</td>
<td class=colhead align=left>Added</td>
<td class=colhead align=left>Answered</td>
<td class=colhead align=center>Set Answered</td>
<td class=colhead align=left>Del</td>
</tr>
");
print("<form method=post action=?action=takecontactanswered>");

$res = sql_query("SELECT staffmessages.id, staffmessages.added, staffmessages.subject, staffmessages.answered, staffmessages.answeredby, staffmessages.sender, staffmessages.answer, users.username FROM staffmessages INNER JOIN users on staffmessages.sender = users.id ORDER BY id desc $limit");

while ($arr = mysql_fetch_assoc($res))
{
    if ($arr[answered])
    {
        $res3 = sql_query("SELECT username FROM users WHERE id=$arr[answeredby]");
        $arr3 = mysql_fetch_assoc($res3);
        $answered = "<font color=green><b>Yes - <a href=$BASEURL/userdetails.php?id=$arr[answeredby]><b>$arr3[username]</b></a> (<a href=$BASEURL/admin/staffbox.php?action=viewanswer&pmid=$arr[id]>View Answer</a>)</b></font>";
    }
    else
      $answered = "<font color=red><b>No</b></font>";

    $pmid = $arr["id"];

print("<tr>
<td><a href=$BASEURL/admin/staffbox.php?action=viewpm&pmid=$pmid><b>".htmlspecialchars($arr[subject])."</b></td>
<td><a href=$BASEURL/userdetails.php?id=$arr[sender]><b>$arr[username]</b></a></td>
<td>$arr[added]</td><td align=left>$answered</td>
<td><input type=\"checkbox\" name=\"setanswered[]\" value=\"" . $arr[id] . "\" /></td>
<td><a href=$BASEURL/admin/staffbox.php?action=deletestaffmessage&id=$arr[id]>Del</a></td>
</tr>\n
");
}

print("</table>\n");

print("<p align=right><input type=submit value=Confirm></p>");
print("</form>");

echo $pagerbottom;

      end_main_frame();
    }
     stdfoot();    
  }

         //////////////////////////
        //        VIEW PM'S        //
       //////////////////////////

if ($action == "viewpm")
 {

 if ((!ur::cstaff()) AND get_user_class() < UC_MODERATOR)
   stderr("Error", "Permission denied.");

$pmid = 0 + $_GET["pmid"];

$ress4 = sql_query("SELECT id, subject, sender, added, msg, answeredby, answered FROM staffmessages WHERE id=$pmid");
$arr4 = mysql_fetch_assoc($ress4);

$answeredby = $arr4["answeredby"];

$rast = sql_query("SELECT username FROM users WHERE id=$answeredby");
$arr5 = mysql_fetch_assoc($rast);

$senderr = "" . $arr4["sender"] . "";

if (is_valid_id($arr4["sender"]))
{
$res2 = sql_query("SELECT username FROM users WHERE id=" . $arr4["sender"]) or sqlerr();
$arr2 = mysql_fetch_assoc($res2);
$sender = "<a href='$BASEURL/userdetails.php?id=$senderr'>" . ($arr2["username"] ? $arr2["username"]:"[Deleted]") . "</a>";
}
else
$sender = "System";

$subject = htmlspecialchars($arr4["subject"]);

if ($arr4["answered"] == '0') {
$answered = "<font color=red><b>No</b></font>";
}
else {
$answered = "<font color=blue><b>Yes</b></font> by <a href=$BASEURL/userdetails.php?id=$answeredby>$arr5[username]</a> (<a href=$BASEURL/admin/staffbox.php?action=viewanswer&pmid=$pmid>Show Answer</a>)";
}

if ($arr4["answered"] == '0') {
$setanswered = "[<a href=$BASEURL/admin/staffbox.php?action=setanswered&id=$arr4[id]>Mark Answered</a>]";
}
else {
$setanswered = "";
}

$iidee = $arr4["id"];
   stdhead("Staff PM's");
   print("<table class=bottom width=730 border=0 cellspacing=0 cellpadding=10><tr><td class=embedded width=700>\n");
   print("<h1 align=center>Messages to staff</h1>\n");

    $elapsed = get_elapsed_time(sql_timestamp_to_unix_timestamp($arr4["added"]));

      print("<table width=100% border=1 cellspacing=0 cellpadding=10 style='margin-bottom: 10px'><tr><td class=text>\n");
      print("From <b>$sender</b> at\n" . $arr4["added"] . " ($elapsed ago) GMT\n");
      print("<br><br style='margin-bottom: -10px'><div align=left><b>Subject: <font color=darkred>$subject</b></font>
      &nbsp;&nbsp;<br><b>Answered:</b> $answered&nbsp;&nbsp;$setanswered</div>
      <br><table class=main width=730 border=1 cellspacing=0 cellpadding=10><tr><td class=staffpms>\n");
      print(format_comment($arr4["msg"]));
      print("</td></tr></table>\n");
      print("<table width=730 border=0><tr><td class=embedded>\n");
      print(($arr4["sender"] ? "<a href=$BASEURL/admin/staffbox.php?action=answermessage&receiver=" . $arr4["sender"] . "&answeringto=$iidee><b>Reply</b></a>" : "<font class=gray><b>Reply</b></font>") .
      " | <a href=$BASEURL/admin/staffbox.php?action=deletestaffmessage&id=" . $arr4["id"] . "><b>Delete</b></a></td>");

    print("</table></table>\n");
   print("</table>\n");
stdfoot();
 }

         //////////////////////////
        //        VIEW ANSWERS        //
       //////////////////////////

if ($action == "viewanswer")
 {

 if ((!ur::cstaff()) AND get_user_class() < UC_MODERATOR)
   stderr("Error", "Permission denied.");

$pmid = 0 + $_GET["pmid"];

$ress4 = sql_query("SELECT id, subject, sender, added, msg, answeredby, answered, answer FROM staffmessages WHERE id=$pmid");
$arr4 = mysql_fetch_assoc($ress4);

$answeredby = $arr4["answeredby"];

$rast = sql_query("SELECT username FROM users WHERE id=$answeredby");
$arr5 = mysql_fetch_assoc($rast);

if (is_valid_id($arr4["sender"]))
{
$res2 = sql_query("SELECT username FROM users WHERE id=" . $arr4["sender"]) or sqlerr();
$arr2 = mysql_fetch_assoc($res2);
$sender = "<a href=$BASEURL/userdetails.php?id=" . $arr4["sender"] . ">" . ($arr2["username"]?$arr2["username"]:"[Deleted]") . "</a>";
}
else
$sender = "System";

if ($arr4['subject'] == "") {
$subject = "No subject";
}

else {
$subject = "<a style='color: darkred' href=staffbox.php?action=viewpm&pmid=$pmid>".htmlspecialchars($arr4[subject])."</a>";
}
$iidee = $arr4["id"];

if ($arr4[answer] == "") {

$answer = "[b][i]This message has not been answered yet![/b][/i]";
}

else {

$answer = $arr4["answer"];
}

 stdhead("Staff PM's");

   print("<table class=bottom width=730 border=0 cellspacing=0 cellpadding=10><tr><td class=embedded width=700>\n");
   print("<h1 align=center>Viewing Answer</h1>\n");

      $elapsed = get_elapsed_time(sql_timestamp_to_unix_timestamp($arr4["added"]));

      print("<table width=100% border=1 cellspacing=0 cellpadding=10 style='margin-bottom: 10px'><tr><td class=text>\n");
      print("<b><a href=$BASEURL/userdetails.php?id=$answeredby>$arr5[username]</a></b> answered this message sent by $sender");
      print("<br><br style='margin-bottom: -10px'><div align=left><b>Subject: $subject</b>
      &nbsp;&nbsp;<br><b>Answer:</b></div>
      <br><table class=main width=730 border=1 cellspacing=0 cellpadding=10><tr><td class=staffpms>\n");
      print(format_comment($answer));

   print("</td></tr></table>\n");
   print("</table>\n");
   print("</table>\n");

 stdfoot();
 }
         //////////////////////////
        //        ANSWER MESSAGE        //
       //////////////////////////

if ($action == "answermessage") {

        if ((!ur::cstaff()) AND get_user_class() < UC_MODERATOR)
  stderr("Error", "Permission denied");

        $answeringto = $_GET["answeringto"];
        $receiver = 0 + $_GET["receiver"];

        int_check($receiver,true);

        $res = sql_query("SELECT * FROM users WHERE id=$receiver") or die(mysql_error());
        $user = mysql_fetch_assoc($res);

        if (!$user)
   stderr("Error", "No user with that ID.");

        $res2 = sql_query("SELECT * FROM staffmessages WHERE id=$answeringto") or die(mysql_error());
        $array = mysql_fetch_assoc($res2);

     stdhead("Answer to Staff PM", false);
        ?>
        <table class=main width=450 border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>
        <div align=center>
        <h2>Answering to <a href=<?=$BASEURL?>/admin/staffbox.php?action=viewpm&pmid=<?=$array['id']?>><i><?=htmlspecialchars($array["subject"])?></i></a> sent by <i><?=$user["username"]?></i></h2>

        <form method=post name=message action=?action=takeanswer>
        <?php if ($_GET["returnto"] || $_SERVER["HTTP_REFERER"]) { ?>
        <input type=hidden name=returnto value="<?=htmlentities($_GET["returnto"]) ? htmlentities($_GET["returnto"]) : htmlentities($_SERVER["HTTP_REFERER"])?>">
        <?php } ?>
        <table class=message cellspacing=0 cellpadding=5>
        <tr><td colspan=2>
        <?php
        textbbcode("message","msg","$body");
        ?></td></tr>
        <tr>
        <tr><td<?=$replyto?" colspan=2":""?> align=center><input type=submit value="Send it!" class=btn></td></tr>
        </table>
        <input type=hidden name=receiver value=<?=$receiver?>>
        <input type=hidden name=answeringto value=<?=$answeringto?>>
        </form>
  </div></td></tr></table>

<?php
     stdfoot();
}

         //////////////////////////
        //        TAKE ANSWER        //
       //////////////////////////
if ($action == "takeanswer") {
  if ($HTTP_SERVER_VARS["REQUEST_METHOD"] != "POST")
    stderr("Error", "Method");

    if ((!ur::cstaff()) AND get_user_class() < UC_MODERATOR)
   stderr("Error", "Permission denied");

     $receiver = 0 + $_POST["receiver"];
   $answeringto = $_POST["answeringto"];

   int_check($receiver,true);

          $userid = $CURUSER["id"];

   			$msg = trim($_POST["msg"]);

          $message = sqlesc($msg);

          $added = "'" . get_date_time() . "'";

   if (!$msg)
     stderr("Error","Please enter something!");

 sql_query("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES($userid, $userid, $receiver, $added, $message)") or sqlerr(__FILE__, __LINE__);

 sql_query("UPDATE staffmessages SET answer=$message WHERE id=$answeringto") or sqlerr(__FILE__, __LINE__);

 sql_query("UPDATE staffmessages SET answered='1', answeredby='$userid' WHERE id=$answeringto") or sqlerr(__FILE__, __LINE__);

        header("Location: staffbox.php?action=viewpm&pmid=$answeringto");
        die;
}
         //////////////////////////
        // DELETE STAFF MESSAGE        //
       //////////////////////////

if ($action == "deletestaffmessage") {

   $id = 0 + $_GET["id"];

    if (!is_numeric($id) || $id < 1 || floor($id) != $id)
    die;

          if ((!ur::cstaff()) AND get_user_class() < UC_MODERATOR)
          stderr("Error", "Permission denied.");

    sql_query("DELETE FROM staffmessages WHERE id=" . sqlesc($id)) or die();

  header("Location: $BASEURL/admin/staffbox.php");
}

         //////////////////////////
        // MARK AS ANSWERED        //
       //////////////////////////

if ($action == "setanswered") {

 if ((!ur::cstaff()) AND get_user_class() < UC_MODERATOR)
    stderr("Error", "Permission denied.");

$id = 0 + $_GET["id"];

sql_query ("UPDATE staffmessages SET answered=1, answeredby = $CURUSER[id] WHERE id = $id") or sqlerr();

header("Refresh: 0; url=staffbox.php?action=viewpm&pmid=$id");
}

         //////////////////////////
        // MARK AS ANSWERED #2        //
       //////////////////////////

if ($action == "takecontactanswered") {

 if ((!ur::cstaff()) AND get_user_class() < UC_MODERATOR)
    stderr("Error", "Permission denied.");

$res = sql_query ("SELECT id FROM staffmessages WHERE answered=0 AND id IN (" . implode(", ", $_POST[setanswered]) . ")");

while ($arr = mysql_fetch_assoc($res))
sql_query ("UPDATE staffmessages SET answered=1, answeredby = $CURUSER[id] WHERE id = $arr[id]") or sqlerr();

header("Refresh: 0; url=staffbox.php");
}

?>