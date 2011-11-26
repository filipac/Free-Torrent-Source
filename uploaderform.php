<?php
/*Request to be Uploader v.0.5 (to be inspired: Request_to_be_Uploader By Fusion1981,laffin mod)*/
require "include/bittorrent.php";

loggedinorreturn();

define("VERSION","Request to be Uploader v.0.5");
define("forumid","4"); // Default 4 (STAFF FORUM, Only for Staff Team, Min.Read/Write/Access by MODERATOR)
$action = isset($_POST['action']) ? htmlspecialchars($_POST['action']) : (isset($_GET['action']) ? htmlspecialchars($_GET['action']) : 'showform');
$do = isset($_POST['do']) ? htmlspecialchars($_POST['do']) : (isset($_GET['do']) ? htmlspecialchars($_GET['do']) : '');
$allowed_actions = array('showform','sendform','staff');
if (!in_array($action, $allowed_actions))
	$action = 'showform';

$allowed_do = array('accept','deny');
if (!in_array($do, $allowed_do))
	$do = '';	
stdhead("Request to be Uploader - $SITENAME");
begin_main_frame();
$res = sql_query("SELECT userid FROM topics WHERE forumid = ".forumid." AND userid = ".sqlesc($CURUSER[id])) or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_row($res);
if ($arr) {
	stdmsg("Sorry!","No dublicate applications allowed. Your \"<b>Request to be Uploader</b>\" application currently awaiting moderation.",false);
	stdfoot();
	exit();
}

if ($action == 'staff'){
	if (get_user_class() < UC_MODERATOR){
		stdmsg("Error","Permission denied!");
		stdfoot();
		exit();
	}
	$dt = sqlesc(get_date_time());
	$subject = sqlesc("Uploader Request!");
	$userid = 0+$_GET['id'];
	$topicid = 0+$_GET['topicid'];
		int_check(array($userid,$topicid));
		$res = sql_query("SELECT modcomment FROM users WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
		while ($row = mysql_fetch_assoc($res)) {
			$mystring =  $row['modcomment'];
			$findme  = 'Upload Application';
			$pos = strpos($mystring, $findme);
			if ($pos === false) {
			   continue;
			} else {
				if (get_user_class() < UC_SYSOP){
				   stdmsg("Sorry!","This uploader application has already accepted by someone else. No action required.");
				   stdfoot();
					exit();
				}
			}
		}
		$res2 = sql_query("SELECT id FROM users WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
		$arr = mysql_fetch_row($res2);
		if (!$arr) {
			stdmsg("Sorry!","No user found!");
			stdfoot();
			exit();
		}		
		
	if ($do == 'accept') {
		$updatepost = sql_query("SELECT * FROM posts WHERE topicid = ".sqlesc($topicid)." AND userid = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
		$acceptmsg = "		
		[color=DarkRed][size=4]ACTION: Application accepted by [b]".$CURUSER["username"]."[/b][/color][/size]";
		while ($row = mysql_fetch_assoc($updatepost))
			sql_query("UPDATE posts SET body = CONCAT(body,".sqlesc($acceptmsg."\n").") WHERE topicid = ".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
		
		$modcomment = gmdate("Y-m-d") . " - Upload Application: Accepted by $CURUSER[username].";
		$mq="UPDATE users SET uploadpos='yes',class='".UC_UPLOADER."',modcomment=CONCAT(modcomment,".sqlesc($modcomment."\n").") WHERE id=".$userid;
		sql_query($mq);		
    	$msg = sqlesc("Congrats, You have been accepted as a new Uploader!.\n");    	
    	sql_query("INSERT INTO messages (sender, receiver, added, subject, msg, poster) VALUES(0, $userid, $dt, $subject, $msg, 0)") or sqlerr(__FILE__, __LINE__);
		stdmsg("Success","Now we have a new uploader :)");
		stdfoot();
		exit();
	}elseif ($do == 'deny') {
		$updatepost = sql_query("SELECT * FROM posts WHERE topicid = ".sqlesc($topicid)." AND userid = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
		$acceptmsg = "		
		[color=DarkRed][size=4]ACTION: Application denied by [b]".$CURUSER["username"]."[/b][/color][/size]";
		while ($row = mysql_fetch_assoc($updatepost))
			sql_query("UPDATE posts SET body = CONCAT(body,".sqlesc($acceptmsg."\n").") WHERE topicid = ".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
		$modcomment = gmdate("Y-m-d") . " - Upload Application: Denied by $CURUSER[username].";		
		$mq="UPDATE users SET  uploadpos='yes',class='".UC_USER."',modcomment=CONCAT(modcomment,".sqlesc($modcomment."\n").") WHERE id=".$userid;
     	sql_query($mq);
		$msg = sqlesc("Sorry, You have been denied as a new Uploader.\n");
		sql_query("INSERT INTO messages (sender, receiver, added, subject, msg, poster) VALUES(0, $userid, $dt, $subject, $msg, 0)") or sqlerr(__FILE__, __LINE__);
		stdmsg("Success","Uploader request denied.");
		stdfoot();
		exit();
	}
}elseif ($action == 'sendform'){
	if ($_POST['rbseed'] != '1' || $_POST['rbstime'] != '1' | $_POST['agree'] != 'yes') {
		stdmsg("Form failed!", "Sorry, you're not qualified to become an uploader of this site.");
		stdfoot();
		exit();
	}
	$userid = 0+$_POST['userid'];
	$username = trim($_POST['username']);
	$joindate = trim($_POST['joined']);
	$ratio = trim($_POST['ratio']);
	$upk = trim($_POST['upk']);
	$plan = trim($_POST['plans']);
	$comment = trim($_POST['comment']);
	$subject = sqlesc("Request to be Uploader: $username");
	if (empty($plan) || empty($comment)) {
		stdmsg("Form failed!","Don't leave any fields blank!");
		stdfoot();
		exit();
	}
	sql_query("INSERT INTO topics (userid, forumid, subject) VALUES($userid, ".forumid.", $subject)") or sqlerr(__FILE__, __LINE__);
	$topicid = mysql_insert_id() or stderr("Error", "No topic ID returned!");
	$added = "'" . get_date_time() . "'";
    $body = "
    My name is: [b]".$username."[/b]
    My Joindate is: [b]".$joindate."[/b]
    My Ratio is at or above 1.0: [b]".$ratio."[/b]
    I meet or exceed $upreq GB uploaded transfer: [b]".$upk."[/b]
    
    [b]Content I plan on uploading:[/b] [quote=".$username."]".$plan."[/quote]
    [b]What is my upload speed and Why I should be given upload access:[/b] [quote=".$username."]".$comment."[/quote]
    
    [b]Staff Actions:[/b]
    Click [url=$BASEURL/userdetails.php?id=$userid][b]here[/b][/url] to see userdetails.
    Click [url=$BASEURL/uploaderform.php?id=$userid&do=accept&action=staff&topicid=$topicid][b]here[/b][/url] to accept this request.
    Click [url=$BASEURL/uploaderform.php?id=$userid&do=deny&action=staff&topicid=$topicid][b]here[/b][/url] to deny this request.
";
        
    sql_query("INSERT INTO posts (topicid, userid, added, body) " .
    "VALUES($topicid, $userid, $added, ".sqlesc($body).")") or sqlerr(__FILE__, __LINE__);

    $postid = mysql_insert_id() or die("Post id n/a");
    //------ Update topic last post
    update_topic_last_post($topicid);
    stdmsg("Success","Your application has been successfully sent to the review board.");
    stdfoot();
    exit();
}elseif ($action == 'showform') {	
	if ($CURUSER["downloaded"] > 0)
		$ratio = $CURUSER['uploaded'] / $CURUSER['downloaded'];
	else if ($CURUSER["uploaded"] > 0)
		$ratio = 1;
	else
		$ratio = 0;
$upreq = 5;
$upreqn = $upreq * 1073741824;
$upreqm=$CURUSER['uploaded']>=$upreqn;
print("<h2>Request to be Uploader Form</h2>");
print("<table border='1' cellspacing='0' cellpadding='5' width='737'>");
?>
<form method='post' action='uploaderform.php'>
<input type='hidden' name='action' value='sendform'>
<input name='userid' type='hidden' value='<?=$CURUSER[id]?>'>
<input name='username' type='hidden' value='<?=$CURUSER[username]?>'>
<?php
tr("User","&nbsp;&nbsp;".$CURUSER['username'],1);
tr("Joined Date","&nbsp;&nbsp;<input name='joined' type='hidden' value='".$CURUSER['added']."'>".$CURUSER['added'],1);
tr("My Ratio is at or above 1.0","&nbsp;&nbsp;<input name='ratio' type='hidden' value='".($ratio>=1?"ok":"not ok")."'>".($ratio>=1?"<font color=green>Yes</font>":"<font color=red>No</font>"),1);
$upreqm=$CURUSER['uploaded']>=$upreqn;
tr("I meet or exceed ". $upreq ." GB uploaded transfer.","&nbsp;&nbsp;<input name='upk' type='hidden' value='".($upreqm?"yes":"no")."'>".($upreqm?"<font color=green>Yes</font>":"<font color=red>No</font>"),1);
tr("Content I plan on uploading<br>(not restricted to)","<textarea name='plans' cols='100' rows='2' wrap='VIRTUAL'></textarea>",1);
tr("What is my upload speed and Why I should be given upload access?","<textarea name='comment' cols='100' rows='2' wrap='VIRTUAL'></textarea>",1);
?>
<tr><td colspan='2' align='left'>
<p><b>I know how to seed (including the creation of torrent files) torrents?</b><br>
<input type='radio' name='rbseed' value='1'>
 Yes<br>
 <input name='rbseed' type='radio' value='0' checked>
 No</p>
 </td></tr>
 <tr><td colspan='2' align='left'>
 <p><b>I understand that I am to seed torrents for at least 24 hours, or at least two other leechers have become seeders.</b><br>
<input type='radio' name='rbstime' value='1'>
Yes<br>
<input name='rbstime' type='radio' value='0' checked>
No</p>
 </td></tr>
 <tr><td colspan='2' align='left'>
 <p><b>General rules for uploaders (Read before you press Submit)</b><br>
 <ul>
 <li>Pre-release stuff should be labeled with an *ALPHA* or *BETA* tag.</li>
 <li>Make sure your torrents are well-seeded for at least 24 hours.</li>
  <li>Stay active! You risk being demoted if you have no active torrents.</li>
  </ul></p>
 </td></tr>
<tr><td colspan='2' align='center'><input type='checkbox' name='agree' value='yes'><b>I have read and agree to the rules.</b><br>
<input type='submit' name='Submit' value='Send Application [PRESS ONLY ONCE]'> </table> </form>
</td></tr>
<?php
}
end_main_frame();
stdfoot();
?>