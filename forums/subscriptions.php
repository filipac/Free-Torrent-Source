<?php
ob_start("ob_gzhandler");
$rootpath = '../';
require_once($rootpath."include/bittorrent.php");

loggedinorreturn();
//parked(); //===== uncomment if you use the parked mod

$userid = $CURUSER["id"];

if (!is_valid_id($userid)) stderr("Error", "Invalid ID");

if (get_user_class()< UC_USER || ($CURUSER["id"] != $userid && get_user_class() < UC_MODERATOR))
stderr("Error", "Permission denied");


//=== Action: Delete subscription
if ($_GET["delete"]){
if (!isset($_POST[deletesubscription]) AND !isset($_GET['fid']))
stderr ("Error","Nothing selected");
if(!isset($_POST[deletesubscription])) {
	$fid = $_GET['fid'];
	mysql_query ("DELETE FROM subscriptions WHERE userid = $CURUSER[id] AND topicid='$fid' ");
	$todel = array();
$todel[] = "&sub=0";
$todel[] = "&sub=1";
$refer = str_replace($todel,'',$_SERVER['HTTP_REFERER']);
	header("Refresh: 0; url=$refer&sub=1");
	die();
}


$checked= $_POST['deletesubscription'];
foreach ($checked as $delete) {
mysql_query ("DELETE FROM subscriptions WHERE userid = $CURUSER[id] AND topicid='$delete' ");

}

header("Refresh: 0; url=$DEFAULTBASEURL/forums/subscriptions.php?deleted=1");
}
//===end
if (!$CURUSER){
print "<div align='center'><br><br><table width='100%' height='200' border='0' summary=''><tr><th height='30' align='center' valign='middle'>";
print "<h2><div align='center'>Sorry, you need to be logged in to use this feature.</div></h2></th></tr><tr><td valign='top'><div align='center'><br><br>";
print "You are not loggen in.<br><a href='login.php'>log in</a> - <a href='signup.php'>sign up</a></div></td></tr></table><br></div>";
}

if ($CURUSER){
$res = mysql_query("SELECT username, donor, warned, enabled FROM users WHERE id=$userid") or sqlerr(__FILE__, __LINE__);

if (mysql_num_rows($res) == 1)
{
$arr = mysql_fetch_assoc($res);

$subject = "<a href=userdetails.php?id=$userid><b>$arr[username]</b></a>" . get_user_icons($arr, true);
}
else
$subject = "unknown[$userid]";

$where_is = "p.userid = $userid AND f.minclassread <= " . $CURUSER['class'];
$order_is = "t.id DESC";
$from_is = "subscriptions AS p LEFT JOIN topics as t ON p.topicid = t.id LEFT JOIN forums AS f ON t.forumid = f.id LEFT JOIN readposts as r ON p.topicid = r.topicid AND p.userid = r.userid";
$select_is = "f.id AS f_id, f.name, t.id AS t_id, t.subject, t.lastpost, r.lastpostread, p.topicid";
$query = "SELECT $select_is FROM $from_is WHERE $where_is ORDER BY $order_is $limit";

$res = mysql_query($query) or sqlerr(__FILE__, __LINE__);


stdhead("Subscriptions");

print("<h4>Subscribed Forums for $subject</h4><p align=center>To be notified via PM when there is a new post, go to your <a class=altlink href=$BASEURL/usercp.php?action=personal>profile</a> and set <b><i>PM on Subscriptions</i></b> to yes</p><FORM action=\"subscriptions.php?delete=1\" method=\"post\">\n");

if ($_GET["deleted"]){
print ("<h1>subscription(s) Deleted</h1>");
}

//------ Print table

begin_main_frame('100%');

begin_frame('',false,'10','100%');

if (mysql_num_rows($res) == 0)
print("<p align=center><font size=\"+2\"><b>No Subscriptions Found</b></font></p><p>You are not yet subscribed to any forums...</p><p>To subscribe to a forum at <b>$SITENAME</b>, click the <b><i>Subscribe to this Forum</i></b> link at the top of the thread page.</p>");

while ($arr = mysql_fetch_assoc($res))
{

$topicid = $arr["t_id"];

$topicname = $arr["subject"];

$forumid = $arr["f_id"];

$forumname = $arr["name"];

$newposts = ($arr["lastpostread"] < $arr["lastpost"]) && $CURUSER["id"] == $userid;

$order_is = "p.id DESC";
$from_is = "posts AS p LEFT JOIN topics as t ON p.topicid = t.id LEFT JOIN forums AS f ON t.forumid = f.id";
$select_is = "t.id, p.*";
$where_is = "t.id = $topicid AND f.minclassread <= " . $CURUSER['class'];
$queryposts = "SELECT $select_is FROM $from_is WHERE $where_is ORDER BY $order_is $limit";
$res2 = mysql_query($queryposts) or sqlerr(__FILE__, __LINE__);
$arr2 = mysql_fetch_assoc($res2);

$postid = $arr2["id"];

$posterid = $arr2["userid"];

$queryuser = mysql_query("SELECT username FROM users WHERE id=$arr2[userid]");
$res3 = mysql_fetch_assoc($queryuser);

$added = $arr2["added"] . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr2["added"]))) . " ago)";

//=======change colors
if($count2 == 0)
{
$count2 = $count2+1;
$class = "clearalt7";
}
else
{
$count2 = 0;
$class = "clearalt6";
}
//=======end

print("<p class=sub><table border=0 cellspacing=0 cellpadding=0 width=100%><tr><td class=colhead2 width=100%>
" .($newposts ? " <b><font color=red>NEW REPLY!</font></b>" : "")."<br><b>Forum: </b>
<a class=altlink href=viewforum.php?forumid=$forumid>$forumname</a>
<b>Topic: </b>
<a class=altlink href=viewtopic.php?topicid=$topicid>$topicname</a>
<b>Post: </b>
#<a class=altlink href=viewtopic.php?topicid=$topicid&page=p$postid#$postid>$postid</a><br>
<b>Last Post By:</b> <a class=altlink href=$BASEURL/userdetails.php?id=$posterid><b>$res3[username]</a> added:</b> $added </td>
<td class=colhead2 align=right width=100%>");

//=== delete subscription
if ($_GET[check] == "yes")
echo("<INPUT type=checkbox checked name=deletesubscription[] id=deletesubscription value=$topicid> ");
else
echo("<INPUT type=checkbox name=deletesubscription[] id=deletesubscription value=$topicid> ");
//=== end
print("<b>un-subscribe</b></td></tr></table></p>\n");


begin_table(true);

$body = format_comment($arr2["body"]);

if (is_valid_id($arr['editedby']))
{
$subres = mysql_query("SELECT username FROM users WHERE id=$arr[editedby]");
if (mysql_num_rows($subres) == 1)
{
$subrow = mysql_fetch_assoc($subres);
$body .= "<p><font size=1 class=small>Last edited by <a href=$BASEURL/userdetails.php?id=$arr[editedby]><b>$subrow[username]</b></a> at $arr[editedat] GMT</font></p>\n";
}
}

// print("<tr valign=top><td class=$class>" . CutName($body, 300) . "</td></tr>\n");
print("<tr valign=top><td class=$class>$body</td></tr>\n"); // use this line if you don't want to cut the post

end_table();
}
?>
<br><table width=100%><tr><td align=right class=colhead2><h1></h1>
<A class=altlink href="subscriptions.php?action=<? echo $_GET[action]; ?>&box=<? echo $_GET[box]; ?>&check=yes">select all</A> -
<A class=altlink href="subscriptions.php?action=<? echo $_GET[action]; ?>&box=<? echo $_GET[box]; ?>&check=no">un-select all</A>
<INPUT class=button type="submit" name="delete" value="Delete"> selected</td></tr></table> </form>

<?php
}//---end if $CURUSER

end_frame();

end_main_frame();

stdfoot();

die;


?>