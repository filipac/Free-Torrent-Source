<?php

require "include/bittorrent.php";

loggedinorreturn();
if($usergroups['canfriendslist'] != 'yes') ug();
parked();
$userid = $_GET['id'];
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : 'flist');

if (!$userid)
	$userid = $CURUSER['id'];

if (!is_valid_id($userid))
	stderr("Error", "Invalid ID $userid.");

if ($userid != $CURUSER["id"])
	stderr("Error", "Access denied.");

$res = sql_query("SELECT * FROM users WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
$user = mysql_fetch_array($res) or stderr("Error", "No user with ID $userid.");

// action: add -------------------------------------------------------------

if ($action == 'add')
{
	$targetid = $_GET['targetid'];
	$type = $_GET['type'];

  if (!is_valid_id($targetid))
		stderr("Error", "Invalid ID $$targetid.");

  if ($type == 'friend')
  {
  	$table_is = $frag = 'friends';
    $field_is = 'friendid';
  }
	elseif ($type == 'block')
  {
		$table_is = $frag = 'blocks';
    $field_is = 'blockid';
  }
	else
		stderr("Error", "Unknown type $type");

  $r = sql_query("SELECT id FROM $table_is WHERE userid=$userid AND $field_is=$targetid") or sqlerr(__FILE__, __LINE__);
  if (mysql_num_rows($r) == 1)
		stderr("Error", "User ID $targetid is already in your $table_is list.");

	sql_query("INSERT INTO $table_is VALUES (0,$userid, $targetid)") or sqlerr(__FILE__, __LINE__);
  header("Location: $BASEURL/friends.php");
  die;
}

// action: delete ----------------------------------------------------------

if ($action == 'delete')
{
	$targetid = $_GET['targetid'];
	$sure = $_GET['sure'];
	$type = $_GET['type'];

  if (!is_valid_id($targetid))
		stderr("Error", "Invalid ID $userid.");

  if (!$sure)
    stderr("Delete $type","Do you really want to delete a $type? Click\n" .
    	"<a href=?id=$userid&action=delete&type=$type&targetid=$targetid&sure=1>here</a> if you are sure.",false);

  if ($type == 'friend')
  {
    sql_query("DELETE FROM friends WHERE userid=$userid AND friendid=$targetid") or sqlerr(__FILE__, __LINE__);
    if (mysql_affected_rows() == 0)
      stderr("Error", "No friend found with ID $targetid");
    $frag = "friends";
  }
  elseif ($type == 'block')
  {
    sql_query("DELETE FROM blocks WHERE userid=$userid AND blockid=$targetid") or sqlerr(__FILE__, __LINE__);
    if (mysql_affected_rows() == 0)
      stderr("Error", "No block found with ID $targetid");
    $frag = "blocks";
  }
  else
    stderr("Error", "Unknown type $type");

  header("Location: $BASEURL/friends.php");
  die;
}


// main body  -----------------------------------------------------------------
if($action == 'flist') {
stdhead("Personal lists for " . $user['username']);

if ($user["donor"] == "yes") $donor = "<td class=embedded><img src=pic/starbig.gif alt='Donor' style='margin-left: 4pt'></td>";
if ($user["warned"] == "yes") $warned = "<td class=embedded><img src=pic/warnedbig.gif alt='Warned' style='margin-left: 4pt'></td>";
fmenu();

print("<table class=main width=100% border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>");
print("<h2 align=left><a name=\"friends\">Friends list for $CURUSER[username]</a></h2>\n");

print("<table width=100% border=1 cellspacing=0 cellpadding=5>");

$i = 0;
$dt = gmtime() - 180;
$dt = sqlesc(get_date_time($dt));
$res = sql_query("SELECT f.friendid as id, u.username AS name, u.class, u.avatar, u.title, u.donor, u.warned, u.enabled, u.last_access, u.gender FROM friends AS f LEFT JOIN users as u ON f.friendid = u.id WHERE userid=$userid ORDER BY name") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($res) <= 0)
	echo "<em>No frieds yet</em>";
else
	while ($friend = mysql_fetch_array($res))
	{
    $avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($friend["avatar"]) : "");
		if (!$avatar)
			$avatar = "pic/default_avatar.gif";
		?>
		<tr>
		<td>

		<div>
		<div style="border-right: 1px dotted black; float: left; margin-right: 3px;">
		<a href="friends.php?id=<?=$CURUSER['id']?>&action=delete&type=friend&targetid=<?=$friend['id'];?>"><img src="pic/friends/remove.gif" alt="" border="0"></a>
		<br><a href="sendmessage.php?receiver=<?=$friend['id']?>"><img src="pic/friends/pm.png" alt="" border="0"></a>
		</div>

		<div style="float: right;">
		<img src="<?=$avatar;?>" height="40" width="40">
		</div>
		
		<?if($friend['gender'] == 'N/A') {?>
		N/A
		<?php }?>
		<?php if($friend['gender'] == 'Male') {?>
		<img src="pic/friends/Male.png" alt="Male" title="Male" border="0">
		<?php }?>
		<?php if($friend['gender'] == 'Female') {?>
		<img src="pic/friends/Female.png" alt="Male" title="Male" border="0">
		<?php }?>
		<?='<a href=userdetails.php?id='.$friend['id'].'>'.get_style($friend['class'],$friend['name']).'</a>'?> (<?
		if(!empty($friend['title']))
		echo $friend['title'];
		else
		echo get_user_class_name($friend['class']);
		?>) 
		<br>
		<?php
		if($friend['last_access']>$dt)
		$status = 'online.png';
		else
		$status = 'offline.png';
		?>
		<img src="pic/friends/<?=$status?>" border="0">
		<strong><?php
		echo 'Last Seen - '.$friend['last_access'] .
    	"(" . get_elapsed_time(sql_timestamp_to_unix_timestamp($friend[last_access])) . " ago)"
		?></strong>
		</div>
		</td>
		</tr>
		<?php
	}
if ($i % 2 == 1)
print($friends);
print("</td></tr>\n");
print("</table></table>");
stdfoot();
die;
}
elseif($action == 'added') {
	stdhead();
	fmenu();
		print("<table class=main width=100% border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>");
print("<h2 align=left><a name=\"friendsadded\">Added you to friendslist</a></h2>\n");

print("<table width=100% border=1 cellspacing=0 cellpadding=5>");

$i = 0;

$friendadd = sql_query("SELECT f.userid AS id, u.username AS name, u.id AS uid, u.class, u.avatar, u.title, u.donor, u.warned, u.enabled, u.last_access, u.gender FROM friends AS f LEFT JOIN users AS u ON f.userid = u.id WHERE friendid = $CURUSER[id] ORDER BY name") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($friendadd) == 0)
	$friendsno = "<em>No friends yet</em>";
	
else 
	while ($friend = mysql_fetch_array($friendadd))
	{
    $title = $friend["title"];
		if (!$title)
	    $title = get_user_class_name($friend["class"]);
    $avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($friend["avatar"]) : "");
		if (!$avatar)
			$avatar = "pic/default_avatar.gif";

		?>
		<tr>
		<td>

		<div>
		<div style="border-right: 1px dotted black; float: left; margin-right: 3px;">
		<a href="sendmessage.php?receiver=<?=$friend['id']?>"><img src="pic/friends/pm.png" alt="" border="0"></a>
		</div>

		<div style="float: right;">
		<img src="<?=$avatar;?>" height="40" width="40">
		</div>
		
		<?php if($friend['gender'] == 'N/A') {?>
		N/A
		<?php }?>
		<?php if($friend['gender'] == 'Male') {?>
		<img src="pic/friends/Male.png" alt="Male" title="Male" border="0">
		<?php }?>
		<?php if($friend['gender'] == 'Female') {?>
		<img src="pic/friends/Female.png" alt="Female" title="Female" border="0">
		<?php }?>
		<?='<a href=userdetails.php?id='.$friend['id'].'>'.get_style($friend['class'],$friend['name']).'</a>'?> (<?
		if(!empty($friend['title']))
		echo $friend['title'];
		else
		echo get_user_class_name($friend['class']);
		?>) 
		<br>
		<?php 
		if($friend['last_access']>$dt)
		$status = 'online.png';
		else
		$status = 'offline.png';
		?>
		<img src="pic/friends/<?=$status?>" border="0">
		<strong><?php 
		echo 'Last Seen - '.$friend['last_access'] .
    	"(" . get_elapsed_time(sql_timestamp_to_unix_timestamp($friend[last_access])) . " ago)"
		?></strong>
		</div>
		</td>
		</tr>
		<?php 
	}
if ($i % 2 == 1)
	print("<td class=bottom width=50%>&nbsp;</td></tr></table>\n");
print($friendsno);
print("</td></tr></table></table><br>\n");
stdfoot();
die;
}
elseif($action == 'blocked') {
	stdhead();
	fmenu();
	$res = sql_query("SELECT b.blockid as id, u.username AS name, u.donor, u.warned, u.enabled, u.last_access FROM blocks AS b LEFT JOIN users as u ON b.blockid = u.id WHERE userid=$userid ORDER BY name") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($res) == 0)
	$blocks = "<em>Your blocked userlist is empty</em>";
else {
	$i = 0;
	$blocks = "<table width=100% cellspacing=0 cellpadding=0>";
	while ($block = mysql_fetch_array($res))
	{
		if ($i % 6 == 0)
			$blocks .= "<tr>";
    	$blocks .= "<td style='border: none; padding: 4px; spacing: 0px;'>[<font class=small><a href=friends.php?id=$userid&action=delete&type=block&targetid=" .
				$block['id'] . ">D</a></font>] <a href=userdetails.php?id=" . $block['id'] . "><b>" . $block['name'] . "</b></a>" .
				get_user_icons($block) . "</td>";
		if ($i % 6 == 5)
			$blocks .= "</tr>";
		$i++;
	}
	print("</table>\n");
}
print("<table class=main width=100% border=0 cellspacing=0 cellpadding=5><tr><td class=embedded>");
print("<h2 align=left><a name=\"blocks\">Blocked Users</a></h2></td></tr>");
print("<tr class=tableb><td style='padding: 10px;'>");
print("$blocks\n");
print("</td></tr></table>\n");
print("<p><a href=page.php?type=users><b>Find user/browse users list</b></a></p>");
stdfoot();
}
else {
die; }
?>