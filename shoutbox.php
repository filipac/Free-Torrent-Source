<?php
/**
 * @description Fts ShoutBox backend. Here the messages are posted, edited and
 * all stuff regaring shoutbox are made here.  
 * @author Filip Pacurar
 * @version 2.2.1
 * @lastmodified 05.05.2008  
 **/  
 ## Require backend
require_once("include/bittorrent.php");


#Check if user is logged
loggedinorreturn();
#Create a good header
global $charset;
header("Content-Type: text/html; charset=$charset");


#=>>> Edit Shout: Start
if ($_GET['do'] == 'edit' && is_valid_id($_GET['id'])){
#	JsB::insertjq(1);
	echo <<<E
	    <script type="text/javascript">  
        $(document).ready(function() {  
            $('#shout').ajaxForm(function() { 
				$('#shoutbox').html('<b>Shout Edited!!(Wait 1 second)</b>');
				setTimeout("getShouts();", 1000)
				setTimeout("getWOL();", 1000) 
            }); 
        }); 
    </script>
E;
	$id = 0+$_GET['id'];
	$q = mysql_query("SELECT text FROM shoutbox WHERE id = $id");
	$q = mysql_fetch_assoc($q);
	echo("<form action=shoutbox.php method=post id=\"shout\"><input type=hidden name=do value=takeedit /><input type=hidden name=id value=$id />");
	echo '<textarea name=message cols=100 rows=10>'.$q['text'].'</textarea>';
	echo"<BR><input type=submit value=Edit />";
	echo"</form>";
	die;
}
elseif ($_POST['do'] == 'takeedit') {
	if(!empty($_POST['message'])) {
	$ch = mysql_query("UPDATE shoutbox SET text='$_POST[message]' WHERE id='$_POST[id]'") or die(mysql_error(__FILE__));
	if($ch) {
		header("Location: index.php");
?>
		<?php
	die;}
}
}
#=>>> Edit Shout: End | Write Shout: Start
elseif ($_GET["do"] == "shout") {
#=> Change the charset usign the iconv menthod.
$shout = iconv("$charset", "$charset", urldecode(decode_unicode_url($_GET["shout"])));
#=>>> Empty Shoutbox: Start
if ($shout == "/empty" && ur::ismod()) {
mysql_query("TRUNCATE TABLE shoutbox");
$message = '/notice The Shoutbox has been truncated by '.$CURUSER['username'];
mysql_query("INSERT INTO shoutbox (date, text, userid, username) VALUES (".implode(", ", array_map("sqlesc", array(time(), $message, '1','system'))).")") or sqlerr(__FILE__,__LINE__);
#die('The Shoutbox has been truncated');
}
if ($shout == "/prune" && ur::ismod()) {
mysql_query("TRUNCATE TABLE shoutbox");
$message = '/notice The Shoutbox has been truncated by '.$CURUSER['username'];
mysql_query("INSERT INTO shoutbox (date, text, userid, username) VALUES (".implode(", ", array_map("sqlesc", array(time(), $message, '1','system'))).")") or sqlerr(__FILE__,__LINE__);
#die('The Shoutbox has been truncated');
}
if ($shout == "/pruneshout" && ur::ismod()) {
mysql_query("TRUNCATE TABLE shoutbox");
$message = '/notice The Shoutbox has been truncated by '.$CURUSER['username'];
mysql_query("INSERT INTO shoutbox (date, text, userid, username) VALUES (".implode(", ", array_map("sqlesc", array(time(), $message, '1','system'))).")") or sqlerr(__FILE__,__LINE__);
#die('The Shoutbox has been truncated');
}
#=>>> Empty Shoutbox: End
#=>>> Help Command: Start
if ($shout == '/help') {
	?>
	<script>
	clear();
	</script>
	<?php
	echo "<div class=success>";
	if(ur::ismod())
	echo <<<HELP
<p>As an member of the staff, you have the folowing commands:</p>
	<p>If you want to make an notice - use the /notice command.</p>
<p>If you want to empty the whole shoutbox - use the /empty command</p>
<p>If you want to warn or unwarn an user - use the /warn and /unwarn commands</p>
<p>If you want to ban(disable) or unban(enable) an user - use the /ban and /unban commands</p>
<p>To delete all notices from the shout, use /deletenotice command</p>
HELP;
echo <<<HELP
<p>As an user, you have the folowing commands:</p>
<p>If you want to view this message in the shout, use the /help command</p>
<p>If you want to speak at 3rd person, use the /me command.</p>
HELP;
echo "</div>";
}
#=>>> Help Command: End
#=>>> Staff Functions: Start
if(preg_match("/\/warn (.*)/",$shout,$matches) && ur::ismod()) {
	if($CURUSER['username'] != $matches[1]) {
	$a = sql_query("SELECT id FROM users WHERE username = '$matches[1]' AND warned = 'no' LIMIT 1");
	if(mysql_num_rows($a) > 0) {
	$id = mysql_fetch_assoc($a);
	$id1 = $id['id'];
	$warn = sql_query("UPDATE users SET warned = 'yes' WHERE id = '$id1'");
	$message = 'You have been quick-warned(using shoutbox) by '.$CURUSER['username'].'!';
	send_message($id1,$message,'WARNED!');
	add_shout("User $matches[1] has been warned by $CURUSER[username]");
	}
	else {
					echo <<<E
<p class=error>No user with that username!! WARN <b>FAILED</b></p>
E;
	}
	}
	else {
			echo <<<E
<p class=error>You CANNOT WARN YOURSELF!! WARN <b>FAILED</b></p>
E;
	}
}
if(preg_match("/\/unwarn (.*)/",$shout,$matches) && ur::ismod()) {
	if($CURUSER['username'] != $matches[1]) {
	$a = sql_query("SELECT id FROM users WHERE username = '$matches[1]' AND warned = 'yes' LIMIT 1");
	if(mysql_num_rows($a) > 0) {
	$id = mysql_fetch_assoc($a);
	$id1 = $id['id'];
	$warn = sql_query("UPDATE users SET warned = 'no' WHERE id = '$id1'");
	$message = 'You have been quick-unwarned(using shoutbox) by '.$CURUSER['username'].'!';
	send_message($id1,$message,'UNWARNED!');
	add_shout("User $matches[1] has been unwarned by $CURUSER[username]"); }
	else {
							echo <<<E
<p class=error>No user with that username!! UNWARN <b>FAILED</b></p>
E;
	}
	}
	else {
			echo <<<E
<p class=error>You CANNOT UNWARN YOURSELF!! UNWARN <b>FAILED</b></p>
E;
	}
}
if(preg_match("/\/ban (.*)/",$shout,$matches) && ur::ismod()) {
	if($CURUSER['username'] != $matches[1]) {
	$a = sql_query("SELECT id FROM users WHERE username = '$matches[1]' AND enabled = 'yes' LIMIT 1");
	if(mysql_num_rows($a) > 0) {
	$id = mysql_fetch_assoc($a);
	$id1 = $id['id'];
	$warn = sql_query("UPDATE users SET enabled = 'no' WHERE id = '$id1'");
	$message = 'You have been quick-banned(using shoutbox) by '.$CURUSER['username'].'!';
	send_message($id1,$message,'BANNED!');
	add_shout("User $matches[1] has been banned by $CURUSER[username]");
	}
	else {
									echo <<<E
<p class=error>No user with that username!! BAN <b>FAILED</b></p>
E;
	}
	}else {
		echo <<<E
<p class=error>You cannot ban yourself!! BAN <b>FAILED</b></p>
E;
	}
}
if(preg_match("/\/deletenotice/",$shout,$matches) && ur::ismod()) {
	mysql_query("DELETE FROM shoutbox WHERE text LIKE '%/notice%'") or die(mysql_error());
}
if(preg_match("/\/unban (.*)/",$shout,$matches) && ur::ismod()) {
	if($CURUSER['username'] != $matches[1]) {
	$a = sql_query("SELECT id FROM users WHERE username = '$matches[1]' AND enabled = 'no' LIMIT 1");
	if(mysql_num_rows($a) > 0) {
	$id = mysql_fetch_assoc($a);
	$id1 = $id['id'];
	$warn = sql_query("UPDATE users SET enabled = 'yes' WHERE id = '$id1'");
	$message = 'You have been quick-unbanned(using shoutbox) by '.$CURUSER['username'].'!';
	send_message($id1,$message,'UNBanned!');
	add_shout("User $matches[1] has been unbanned by $CURUSER[username]"); }
	else {
		echo <<<E
<p class=error>No user with that username!! UNBAN <b>FAILED</b></p>
E;
	}
	}else {
		echo <<<E
<p class=error>You cannot unban yourself!! UNBAN <b>FAILED</b></p>
E;
	}
}
#=>>> Staff Functions: END
# Define the sender
$sender = $CURUSER["id"];
#Check if user is trying to type an staff command and stop thim
$shout = preg_replace("/\/empty/",'',$shout);
$shout = preg_replace("/\/ban (.*)/",'',$shout);
$shout = preg_replace("/\/unban (.*)/",'',$shout);
$shout = preg_replace("/\/warn (.*)/",'',$shout);
$shout = preg_replace("/\/unwarn (.*)/",'',$shout);
$shout = preg_replace("/\/help/",'',$shout);
$shout = preg_replace("/\/prune/",'',$shout);
$shout = preg_replace("/\/pruneshout/",'',$shout);
$shout = preg_replace("/\/deletenotice/",'',$shout);
if(!ur::ismod())
$shout = preg_replace("/\/notice/",'',$shout);
#END
#Check for empty shouts as we do not need them.
if (!empty($shout)) {
	global $___flood___,$usergroups;
	$___flood___->protect('last_shout','shout',$usergroups['antifloodtime']);
	#Start Message processing
if(preg_match("/\/notice/i",$shout) AND ur::ismod()) {
	$message = $shout;
	sql_query("INSERT INTO shoutbox (date, text, userid, username) VALUES (".implode(", ", array_map("sqlesc", array(time(), $message, '1','system'))).")") or sqlerr(__FILE__,__LINE__);
$___flood___->update('last_shout');
}else{
sql_query("INSERT INTO shoutbox (date, text, userid) VALUES (".implode(", ", array_map("sqlesc", array(time(), $shout, $sender))).")") or sqlerr(__FILE__,__LINE__);
$___flood___->update('last_shout');}
} else
print("<script>alert('message?!');</script>"); 
} elseif ($_GET["do"] == "delete" ) {
	$id = $_GET["id"];
$q = mysql_query("SELECT userid FROM shoutbox WHERE id = '$id'");
$q = mysql_fetch_assoc($q);
	 if(get_user_class() >= UC_MODERATOR OR $CURUSER['id']==$q['userid'] && is_valid_id($_GET["id"]))
mysql_query("DELETE FROM shoutbox WHERE id = $id") or sqlerr(__FILE__,__LINE__);
}


$res = mysql_query("SELECT shoutbox.*, users.username, users.class, users.gender FROM shoutbox INNER JOIN users ON shoutbox.userid = users.id ORDER BY id DESC LIMIT 0, 30") or sqlerr(__FILE__,__LINE__);
if (mysql_num_rows($res) == 0)
die();
global $shoutname;
while ($arr = mysql_fetch_array($res)) {

$comment=format_comment($arr["text"] );
if(preg_match("/\/notice/",$comment)) {
	$comment = preg_replace('/\/notice/','',$comment);
print("<b><span style=\"background-color:#ADCBE7;font-size:10px;\">[".strftime("%I:%M %p",$arr["date"])."] ".(get_user_class() >= UC_MODERATOR ? "<span onclick=\"deleteShout($arr[id]);\" style=\"cursor: pointer;\">[x]</span> <span onclick=\"editShout($arr[id]);\" style=\"cursor: pointer;\">[e]</span> " : "")."<b>$shoutname</b> - $comment</span></b><BR>\n");
}elseif(preg_match("/\/me (.*)/",$comment,$m)) {
	$comment = preg_replace('/\/me/','',$comment);
	$dateshow = false;
	if($dateshow)
print("<font color=gray>[".strftime("%I:%M %p",$arr["date"])."]</font> ".(get_user_class() >= UC_MODERATOR ? "<span onclick=\"deleteShout($arr[id]);\" style=\"cursor: pointer;\">[x]</span> <span onclick=\"editShout($arr[id]);\" style=\"cursor: pointer;\">[e]</span> " : $arr['userid'] == $CURUSER['id'] ? "<span onclick=\"deleteShout($arr[id]);\" style=\"cursor: pointer;\">[x]</span> <span onclick=\"editShout($arr[id]);\" style=\"cursor: pointer;\">[e]</span>" : '')." <b><a href=\"#\" onClick=\"SmileIT('$arr[username]:','shoutform','shout');return false;\"
>".get_user_class_color($arr["class"], $arr["username"], $arr["gender"])."</a></b> $comment<br/>\n"); 
	else
print(" <b>".get_user_class_color($arr["class"], $arr["username"], $arr["gender"])."</b> $comment".(get_user_class() >= UC_MODERATOR ? "<span onclick=\"deleteShout($arr[id]);\" style=\"cursor: pointer;\">[x]</span> <span onclick=\"editShout($arr[id]);\" style=\"cursor: pointer;\">[e]</span> " : $arr['userid'] == $CURUSER['id'] ? "<span onclick=\"deleteShout($arr[id]);\" style=\"cursor: pointer;\">[x]</span> <span onclick=\"editShout($arr[id]);\" style=\"cursor: pointer;\">[e]</span>" : '')._br."\n"); 
	}else
print("<font color=gray>[".strftime("%I:%M %p",$arr["date"])."]</font> ".(get_user_class() >= UC_MODERATOR ? "<span onclick=\"deleteShout($arr[id]);\" style=\"cursor: pointer;\">[x]</span> <span onclick=\"editShout($arr[id]);\" style=\"cursor: pointer;\">[e]</span> " : $arr['userid'] == $CURUSER['id'] ? "<span onclick=\"deleteShout($arr[id]);\" style=\"cursor: pointer;\">[x]</span> <span onclick=\"editShout($arr[id]);\" style=\"cursor: pointer;\">[e]</span>" : '')."<b><a href=\"userdetails.php?id=$arr[userid]\"onClick=\"SmileIT('$arr[username]:','shoutform','shout');return false;\"
>".get_user_class_color($arr["class"], $arr["username"], $arr["gender"])."</a></b> - $comment<br/>\n"); 

}
#END Message processing
#######		END		#######
?> 