<?php
////////////////////////////////////////////////////////
// Bonus Mod by TvRecall.org
// Bonus Mod Updated by devin
// Version 0.3
// Updated 01/05/2006
// under GPL-License
///////////////////////////////////////////////////////
/******************************************************
Total credit to TvRecall for writing this fine mod in the first place,
the mod has since been altered with code and input by:
devinkray - cddvdheaven - DRRRR - vlahdr - sherl0k - okiee - lords - XiaNYdE
dopeydwerg - WRK - Fantomax - porthos - dokty - Sir_SnuggleBunny - wicked
*******************************************************/
require_once('include/bittorrent.php');

loggedinorreturn();

parked();
if($usergroups['canka'] != 'yes') ug();
if ($bonus == "disable" && get_user_class() < UC_ADMINISTRATOR || $bonus == "disablesave" && get_user_class() < UC_ADMINISTRATOR)
	stderr("Sorry!","Karma Bonus Point System is currently disabled".($bonus == "disablesave" ? " <b>however your points still active</b>." : "."),false);

$action = htmlspecialchars($_GET['action']);
$do = htmlspecialchars($_GET['do']);
unset($msg);
if (isset($do)) {
if ($do == "upload")
	$msg = "<b>Congratulations!</b> $CURUSER[username] you have just increased your <b>Upload!</b>";
elseif ($do == "invite")
	$msg = "<b>Congratulations!</b> $CURUSER[username] you have got your self <b>3</b> new invites!";
elseif ($do == "vip") 
	$msg =  "<b>Congratulations!</b> $CURUSER[username] you have got yourself <b>VIP</b> Status for one month!";
elseif ($do == "vipfalse") 
	$msg =  "<b>ERROR</b> You have no permission.";
elseif ($do == "title")
	$msg = "<b>Congradulations!</b> $CURUSER[username] you are now known as <b>$CURUSER[title]</b>!";
elseif ($do == "transfer")
	$msg =  "$CURUSER[username]</b> you have spread the <b>Karma</b> well.";
else
	$msg = '';
}
stdhead($CURUSER['username'] . "'s Karma Bonus Page");
$bonus = number_format($CURUSER['seedbonus'], 1);
$userid = 0+$CURUSER['id'];
if($bonus != number_format($CURUSER['seedbonus'], 1)) {
echo "bad idea..."; 
die;
}
if (!$action) {
print("<table align=center width=80% border=\"1\" cellspacing=\"0\" cellpadding=\"5\">\n");
Print("<tr><td class=colhead colspan=4><h1><center>".strtoupper($SITENAME)." Karma Bonus Point System ".($msg ? "<br></center></h1><font color=yellow size=2><b><center>System Message: $msg</b></font>" : "")."</h1></center></td></tr>\n");
?>
<tr><td align=center colspan=4 class=clearalt6>Exchange your <a class=altlink href=mybonus.php>Karma Bonus Points</a> [ current <?echo "$bonus";?> ] for goodies!
<br><br>
[ <b>If no buttons appear, you have not earned enough bonus points to trade. </b>]
<br><br>
<?php

print("<tr><td class=colhead align=left>Option</td>".
"<td class=colhead align=left>Description</td>".
"<td class=colhead align=center>Points</td>".
"<td class=colhead align=center>Trade</td>".
"</tr>");

$res = sql_query("SELECT * from bonus WHERE id=id ORDER BY id ASC");

while ($gets = mysql_fetch_assoc($res))
{
//=======change colors
if($count == 0)
{
$count = $count+1;
$class = "clearalt7";
}
else
{
$count = 0;
$class = "clearalt6";
}
//=======end

$otheroption = "<table width=100%><tr><td class=$class><b>Username:</b><input type=text name=username size=20 maxlength=24></td><td class=$class> <b>to be given: </b><select name=bonusgift> <option value=100.0> 100.0</option> <option value=200.0> 200.0</option> <option value=300.0> 300.0</option> <option value=400.0> 400.0</option><option value=500.0> 500.0</option><option value=666.0> 666.0</option></select> Karma points!</td></tr></table>";
$otheroption_title = "<input type=text name=title size=30 maxlength=30>";

if($CURUSER['seedbonus'] < 999.0) {
print("<form action=mybonus.php?action=exchange method=post>\n");
if ($gets["id"]==5)
print("<tr><td class=$class align=center><b>".$gets["id"]."</b></td><td align='left' class=$class><h1>".$gets["bonusname"]."</h1>".$gets["description"]."<br><br>Enter the <b>Special Title</b> you would like to have $otheroption_title click Exchange! </td><td align='center' class=$class>".$gets["points"]."</td>");
else
if ($gets["id"]==7)
print("<tr><td class=$class align=center><b>".$gets["id"]."</b></td><td align='left' class=$class><h1>".$gets["bonusname"]."</h1>".$gets["description"]."<br><br>Enter the <b>username</b> of the person you would like to send karma to, and select how many points you want to send and click Exchange!<br>$otheroption</td><td align='center' class=$class>min.<br>".$gets["points"]."<br>max.<br>500</td>");
else
print("<tr><td class=$class align=center><b>".$gets["id"]."</b></td><td align='left' class=$class><h1>".$gets["bonusname"]."</h1>".$gets["description"]."</td><td align='center' class=$class>".$gets["points"]."</td>");

print("<input type=\"hidden\" name=\"bonus\" value=\"".$bonus."\">\n");
print("<input type=\"hidden\" name=\"userid\" value=\"".$userid."\">\n");
print("<input type=\"hidden\" name=\"points\" value=\"".$gets["points"]."\">\n");
print("<input type=\"hidden\" name=\"option\" value=\"".$gets["id"]."\">\n");
print("<input type=\"hidden\" name=\"art\" value=\"".$gets["art"]."\">\n");
if($bonus >= $gets["points"]) {
if ($gets["id"]==7)
print("<td class=$class><input class=button type=submit name=submit value=\"Karma Gift!\"></form></td>");
else
print("<td class=$class><input class=button type=submit name=submit value=\"Exchange!\"></form></td>");
} else {
print("<td class=$class align=center><b>more points needed</b></form></td>");
}
}
}
if($CURUSER['seedbonus'] > 999.0) {
print("<form action=mybonus.php?action=exchange method=post>\n");
print("<input type=\"hidden\" name=\"bonus\" value=\"".$bonus."\">\n");
print("<input type=\"hidden\" name=\"userid\" value=\"".$userid."\">\n");
print("<input type=\"hidden\" name=\"points\" value=\"".$gets["points"]."\">\n");
print("<input type=\"hidden\" name=\"option\" value=\"".$gets["id"]."\">\n");
print("<input type=\"hidden\" name=\"art\" value=\"gift_1\">\n");
print("<tr><td class=$class align=center><img src=pic/smilies/karma.gif alt=good_karma></td><td align='left' class=$class><b>Wow! That's a lot of Karma Points!!!</b><br>your only option now is to share the love! <img src=pic/smilies/friends.gif alt=\"smilie\"><br>With a Karma rating of over 1000.0 you can not use the regular trade options... in fact, you must share the love if you want to do anything at all!<br><br>Use this option to send another user some Karma points... Select how many points you want to send and click <b>Karma Gift!</b><br>$otheroption</td><td align='center' class=$class>100<br>to<br>666</td>");
print("<td class=$class valign=bottom><input class=button type=submit name=submit value=\"Karma Gift!\"></form></td>");
}


print("</table><br><br><br>");
?>

<table width=80%>
<tr><td class=colhead><h1>What the hell are these Karma Bonus points, and how do I get them?</h1></td></tr>
<tr><td >
- For every hour that you seed a torrent, you are awarded with 1 Karma Bonus Point... <br>
If you save up enough of them, you can trade them in for goodies like bonus GB(s) to your upload<br> stats,
getting more invites, or doing the real Karma booster... give them to another user!<br>
and yes! this is awarded on a per torrent basis even if there are no leechers on the Torrent you are seeding! <br>
<h1>Other things that will get you karma points:</h1>
<ul>
<li>uploading a new torrent = 15 points</li>
<li>comment on torrent = 5 points</li>
<li>saying thanks = 3 points</li>
<li>rating a torrent = 3 points</li>
<li>making a post = 1 point</li>
<li>starting a topic = 2 points</li>
<li>voting on poll = 2 point</li>
</ul>
<h1>Some things that will cost you karma points:</h1>
<ul>
<li>trading for invites</li>
<li>trading for upload credit</li>
<li>trading for a custom title</li>
<li>trading for one month VIP status</li>
<li>giving a gift of karma points to another user</li>
</ul>
<p>But keep in mind that everything that can get you karma can also be lost, <br>
ie:if you up a torrent then delete it, you will gain and then lose 10 points, <br>
making a post and having it deleted will do the same
<br><br>
... and there are other hidden bonus karma points all over the site.<br><br>
Yet another way to help out your ratio! </p>

<p>*please note, staff can give or take away points for breaking the rules, or doing good for the community.</p>
</td></tr></table>
<p align="center"><a class=altlink href=usercp.php>back to your profile</a></p>
</td></tr>
</table>
<?php
}
if ($action == "exchange") {

$userid = 0+$_POST["userid"];
$option = $_POST["option"];
$points = $_POST["points"];
$bonus = $_POST["bonus"];
$art = $_POST["art"];
//===custom title
$title = htmlentities($_POST["title"]);
$title = sqlesc($title);
//==gift for peeps with no more options
$usernamegift = $_POST["username"];
$res = sql_query("SELECT id,seedbonus FROM users WHERE username=" . sqlesc($usernamegift));
$arr = mysql_fetch_assoc($res);
$useridgift = $arr['id'];
$userseedbonus = $arr['seedbonus'];
$usernamegift = sqlesc($usernamegift);
$seedbonus=number_format($bonus-$points,1);

$bonuscomment = $CURUSER['bonuscomment'];
$upload = $CURUSER['uploaded'];
$bpoints = $CURUSER['seedbonus'];
$res = sql_query("SELECT * FROM bonus WHERE id=".sqlesc($option));
$bytes = mysql_fetch_assoc($res);
$invites = $CURUSER['invites'];
$inv = $invites+$bytes['menge'];

if($bpoints >= $points) {
//=== trade for upload
if($art == "traffic") {
	$bonus= $CURUSER['seedbonus'];
if($bonus < $points)
	die;

$up = $upload + $bytes['menge'];
$bonuscomment = gmdate("Y-m-d") . " - " .$points. " Points for upload bonus.\n " .$bonuscomment;
sql_query("UPDATE users SET uploaded = ".mysql_real_escape_string($upload)." + $bytes[menge], seedbonus = ".sqlesc($seedbonus).", bonuscomment = ".sqlesc($bonuscomment)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
redirect("mybonus.php?do=upload",'You have increased your upload');
}
//=== trade for one month VIP status ***note "SET class = '3'" change "3" to whatever your VIP class number is
elseif($art == "class") {
	if (get_user_class() >= UC_VIP) {
		stdmsg('No permission','Your class higher than VIP!');
		stdfoot();
		die;
	}
$vip_until = get_date_time(gmtime() + 28*86400);
$bonuscomment = gmdate("Y-m-d") . " - " .$points. " Points for 1 month VIP Status.\n " .htmlspecialchars($bonuscomment);
sql_query("UPDATE users SET class = '2', vip_added = 'yes', vip_until = ".sqlesc($vip_until).", seedbonus = ".sqlesc($seedbonus)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
redirect("mybonus.php?do=vip",'You are now VIP');
}
//=== trade for invites
elseif($art == "invite") {
$bonuscomment = gmdate("Y-m-d") . " - " .$points. " Points for invites.\n " .htmlspecialchars($bonuscomment);
sql_query("UPDATE users SET invites = ".sqlesc($inv).", seedbonus = ".sqlesc($seedbonus)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
redirect("mybonus.php?do=invite",'You have increased your invites');
}
//=== trade for special title
/**** the $words array are words that you DO NOT want the user to have... use to filter "bad words" & user class...
the user class is just for show, but what the hell tongue.gif Add more or edit to your liking.
*note if they try to use a restricted word, they will recieve the special title "I just wasted my karma" *****/
elseif($art == "title") {
$words = array("fuck", "shit", "Moderator", "Administrator", "Admin", "pussy", "Sysop", "cunt", "nigger", "VIP", "Super User", "Power User");
$title = str_replace($words, "I just wasted my karma", $title);
//if ($words)
//$title = "I just wasted my karma";
$bonuscomment = gmdate("Y-m-d") . " - " .$points. " Points for custom title. old title was ".htmlspecialchars(trim($CURUSER["title"]))." new title is $title\n " .htmlspecialchars($bonuscomment);
sql_query("UPDATE users SET title = $title, seedbonus = ".sqlesc($seedbonus)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
redirect("mybonus.php?do=title",'You have a new title now.');
}
elseif($art == "gift_1") {
//=== trade for giving the gift of karma
$points = $_POST["bonusgift"];
$bonus= $CURUSER['seedbonus'];
if($bonus >= $points){
$points= number_format($points,1);
$bonuscomment = gmdate("Y-m-d") . " - " .$points. " Points as gift to ".htmlspecialchars(trim($_POST["username"]))." .\n " .htmlspecialchars($bonuscomment);
$seedbonus=$bonus-$points;
$giftbonus1=$userseedbonus+$points;
if ($userid==$useridgift){
print("<table width=80%><tr><td class=colhead align=left colspan=2><h1>Huh?</h1></td></tr>");
print("<tr><td align=left><img src=pic/smilies/dwarf.gif alt=good_karma></td><td align=left><b>Not so fast there Mr. fancy pants!</b><br>$CURUSER[username]... you can not spread the karma to yourself...<br>If you want to spread the love, pick another user! <br><br> click to go back to your <a class=altlink href=mybonus.php>Karma Bonus Point</a> page.<br><br></td></tr></table>");
die;
}
if (!$useridgift){
print("<table width=80%><tr><td class=colhead align=left colspan=2><h1>Error</h1></td></tr>");
print("<tr><td align=left><img src=pic/smilies/dwarf.gif alt=good_karma></td><td align=left><b>Sorry $CURUSER[username]...</b><br> No User with that username <br><br> click to go back to your <a class=altlink href=mybonus.php>Karma Bonus Point</a> page.<br><br></td></tr></table>");
die;
}

sql_query("UPDATE users SET seedbonus =".sqlesc($seedbonus).", bonuscomment = ".sqlesc($bonuscomment)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
sql_query("UPDATE users SET seedbonus = ".sqlesc($giftbonus1)." WHERE id = ".sqlesc($useridgift));

//===send message
$subject = sqlesc("Someone Loves you"); //=== comment out this line if you do not have subject in your PM system
$added = sqlesc(get_date_time());
$msg = sqlesc("You have been given a gift of $points Karma points by ".$CURUSER['username']);
sql_query("INSERT INTO messages (sender, subject, receiver, msg, added) VALUES(0, $subject, $useridgift, $msg, $added)") or sqlerr(__FILE__, __LINE__);
//sql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES(0, ".mysql_real_escape_string($useridgift).", $msg, $added)") or sqlerr(__FILE__, __LINE__); //=== use this line if you do not have subject in your PM system and comment out the above query.
$usernamegift = unesc($_POST["username"]);
redirect("mybonus.php?do=transfer",'You have transfered points succesfully.');
}
else{
print("<table width=80%><tr><td class=colhead align=left colspan=2><h1>OUPS!</h1></td></tr>");
print("<tr><td align=left><img src=pic/smilies/cry.gif alt=oups></td><td align=left><b>Sorry </b>$CURUSER[username] you don't have enough Karma points!");
print("<br> go back to your <a class=altlink href=mybonus.php>Karma Bonus Point</a> page.<br><br></td></tr></table>");
}
}
}
}
stdfoot();
?>