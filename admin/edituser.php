<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;
if(!ur::ismod())
ug();
lang::load('userdetails');
$id = 0 + $_GET["id"];
int_check($id,true);
if($id != $CURUSER['id'])
if($usergroups['canviewotherprofile'] != 'yes')
 ug();
$r = @sql_query("SELECT * FROM users WHERE id=".mysql_real_escape_string($id)) or sqlerr(__FILE__, __LINE__);
$user = mysql_fetch_array($r) or bark(str11);
$enabled = $user["enabled"] == 'yes';
if ($user["status"] == "pending")
	die;

$q = sql_query("SELECT minclasstoedit,maxclasstoedit FROM usergroups WHERE id = $user[class]");
$q = mysql_fetch_assoc($q);
#echo $q['minclasstoedit'].'|'.get_user_class();
if ($usergroups['canstaffpanel'] == 'yes' && get_user_class() >= $q['minclasstoedit'] && get_user_class() <= $q['maxclasstoedit'] && get_user_class() != $user['class'] && $CURUSER['id'] != $user['id'])
{
		stdhead('Edit User');
	print("<a name=edit></a>");
	begin_frame("Edit User $user[username]", true);
	print("<form method=post action=../modtask.php>\n");
	print("<input type=hidden name='action' value='edituser'>\n");
	print("<input type=hidden name='userid' value='$id'>\n");
	print("<input type=hidden name='returnto' value='".htmlentities("userdetails.php?id=$id")."'>\n");
	print("<table class=main border=1 cellspacing=0 cellpadding=5>\n");
	print("<tr><td class=rowhead>Title</td><td colspan=2 align=left><input type=text size=60 name=title value=\"" . htmlspecialchars(trim($user[title])) . "\"></tr>\n");
	$avatar = htmlspecialchars(trim($user["avatar"]));

	print("<tr><td class=rowhead>Privacy Level</td><td colspan=2 align=left>");
	print("<input type=checkbox name=privacy value=low".($user["privacy"] == "low" ? " checked" : "").">Low ");
	print("<input type=checkbox name=privacy value=normal".($user["privacy"] == "normal" ? " checked" : "").">Normal ");
	print("<input type=checkbox name=privacy value=strong".($user["privacy"] == "strong" ? " checked" : "").">Strong");
	print("</tr>\n");
	print("<tr><td class=rowhead>Avatar&nbsp;URL</td><td colspan=2 align=left><input type=text size=60 name=avatar value=\"$avatar\"></tr>\n");
	$signature = htmlspecialchars(trim($user["signature"]));
	print("<tr><td class=rowhead>Signature&nbsp;URL</td><td colspan=2 align=left><input type=text size=60 name=signature value=\"$signature\"></tr>\n");
	if (get_user_class() >= UC_ADMINISTRATOR)
		print("<input type=hidden name=donor value=$user[donor]>\n");
	else
       {
			print("<tr><td class=rowhead>Donor</td><td colspan=2 align=left><input type=radio name=donor value=yes" .($user["donor"] == "yes" ? " checked" : "").">Yes <input type=radio name=donor value=no" .($user["donor"] == "no" ? " checked" : "").">No</td></tr>\n");
       }
	if (get_user_class() >= UC_ADMINISTRATOR)
	{
		print("<tr><td class=rowhead>Donated</td><td colspan=2 align=left><input type=text size=5 name=donated value=\"" . htmlspecialchars($user[donated]) . "\"></tr>\n");
	}else
		print("<input type=hidden name=donated value=\"" . htmlspecialchars($user[donated]) . "\">\n");

	if (get_user_class() == UC_MODERATOR && $user["class"] > UC_VIP)
		print("<input type=hidden name=class value=$user[class]>\n");
	else
	{
		if(get_user_class() == '7') {
		$query = sql_query("SELECT id FROM usergroups WHERE minclasstopr <= '$CURUSER[class]' ORDER BY id ASC") ;
		if(mysql_num_rows($query) > '1') {
		print("<tr><td class=rowhead>Class</td><td colspan=2 align=left><select name=class>\n");
		while($i = mysql_fetch_array($query))
			print("<option value=$i[id]" . ($user["class"] == $i['id'] ? " selected" : "") . ">$prefix" . get_user_class_name($i[id]) . "\n");
		print("</select></td></tr>\n");
		}else
		print("<input type=hidden name=class value=$user[class]>\n");
		}else {
				print("<tr><td class=rowhead>Tracker Class</td><td colspan=2 align=left><select name=class id=specialboxs>\n");
		if ($CURUSER['class'] == UC_MODERATOR)
			$maxclass = UC_UPLOADER;
		else
			$maxclass = $CURUSER['class'] - 1;
		for ($i = 0; $i <= $maxclass; ++$i) {
			$class = get_user_class_name($i);
			if(!empty($class))
			print("<option value=$i" . ($user["class"] == $i ? " selected" : "") . ">" . get_user_class_name($i) . "\n");
			}
		print("</select></td></tr>\n");
	} }
	$supportfor = htmlspecialchars($user["supportfor"]);
	$supportlang = htmlspecialchars($user["supportlang"]);
	print("<tr><td class=rowhead>Support</td><td colspan=2 align=left><input type=radio name=support value=yes" .($user["support"] == "yes" ? " checked" : "").">Yes <input type=radio name=support value=no" .($user["support"] == "no" ? " checked" : "").">No</td></tr>\n");
	print("<tr><td class=rowhead>Support Language:</td><td colspan=2 align=left><input type=text name=supportlang value='$supportlang'></td></tr>\n");
	print("<tr><td class=rowhead>Support for:</td><td colspan=2 align=left><textarea cols=60 rows=6 name=supportfor>$supportfor</textarea></td></tr>\n");

	$modcomment = htmlspecialchars($user["modcomment"]);
	print("<tr><td class=rowhead>Comment</td><td colspan=2 align=left><textarea cols=60 rows=6 name=modcomment>$modcomment</textarea></td></tr>\n");    
	$bonuscomment = htmlspecialchars($user["bonuscomment"]);
	print("<tr><td class=rowhead>Seeding Karma</td><td colspan=2 align=left><textarea cols=60 rows=6 name=bonuscomment READONLY>$bonuscomment</textarea></td></tr>\n");
    $warned = $user["warned"] == "yes";
	$warned = $user["warned"] == "yes";
            
	print("<tr><td class=rowhead" . (!$warned ? " rowspan=4" : " rowspan=2") . ">Warning<br>System<br><br><font size=1><i>(Bad behavior)</i></font></td><td align=left width=20% class=\"row1\">" . ( $warned ? "<input name=warned value='yes' type=radio checked>Yes<input name=warned value='no' type=radio>No" : "Not warned." ) ."</td>");

	if ($warned)
	{
		$warneduntil = $user['warneduntil'];
		if ($warneduntil == '0000-00-00 00:00:00')
			print("<td align=center class=\"row1\">(Arbitrary duration)</td></tr>\n");
		else
		{
			print("<td align=left class=\"row1\">Until $warneduntil");
			print("<br>(" . mkprettytime(strtotime($warneduntil) - gmtime()) . " to go)</td></tr>\n");
		}
		
	}else{

		print("<td class=\"row1\">Warn for <select name=warnlength>\n");
		print("<option value=0>------</option>\n");
		print("<option value=1>1 week</option>\n");
		print("<option value=2>2 weeks</option>\n");
		print("<option value=4>4 weeks</option>\n");
		print("<option value=8>8 weeks</option>\n");
		print("<option value=255>Unlimited</option>\n");
		print("</select></td></tr>\n");
		print("<tr><td align=left class=\"row1\">Reason of warning:</td><td class=\"row1\"><input type=text size=60 name=warnpm></td></tr>");
	}
  
	$elapsedlw = get_elapsed_time(sql_timestamp_to_unix_timestamp($user["lastwarned"]));
	print("<tr><td class=\"row1\">Times Warned</td><td align=left class=\"row1\">$user[timeswarned]</td></tr>\n");

	if ($user["timeswarned"] == 0)
	{
		print("<tr><td class=\"row1\">Last Warning</td><td align=left class=\"row1\">This user hasn't been warned yet.</td></tr>\n");
	}else{
		if ($user["warnedby"] != "System")
		{
			$res = sql_query("SELECT id, username, warnedby FROM users WHERE id = " . $user['warnedby'] . "") or sqlerr(__FILE__,__LINE__);
			$arr = mysql_fetch_assoc($res);
			$warnedby = "<br>[by <u><a href=userdetails.php?id=".$arr['id'].">".$arr['username']."</u></a>]";
		}else{
			$warnedby = "<br>[by System]";
			print("<tr><td class=\"row1\">Last Warning</td><td align=left class=\"row1\"$user[lastwarned] (until $elapsedlw)   $warnedby</td></tr>\n");
		}
	}


	$leechwarn = $user["leechwarn"] == "yes";
	print("<tr><td class=rowhead>Auto-Warning<br><font size=1><i>(Low Ratio)</i></font></td>");
	  
	if ($leechwarn)
	{
		print("<td align=left class=\"row1\"><font color=red>¡WARNED!</font></td>\n");
		$leechwarnuntil = $user['leechwarnuntil'];
		if ($leechwarnuntil != '0000-00-00 00:00:00')
		{
		print("<td align=left class=\"row1\">Until $leechwarnuntil");
		print("<br>(" . mkprettytime(strtotime($leechwarnuntil) - gmtime()) . " to go)</td></tr>\n");
		}else{
		print("<td align=left class=\"row1\"><i>For UNLIMITED time...</i></td></tr>\n");
		}
	}else{
		print("<td class=\"row1\" colspan=\"2\">Not warned.</td></tr>\n");
	}
	 print("<tr><td class=rowhead>Enabled</td><td colspan=2 align=left><input name=enabled value='yes' type=radio" . ($enabled ? " checked" : "") . ">Yes <input name=enabled value='no' type=radio" . (!$enabled ? " checked" : "") . ">No</td></tr>\n");
	 
	print("<tr><td class=rowhead>Forum Post possible?</td><td colspan=2 align=left><input type=radio name=forumpost value=yes" .($user["forumpost"]=="yes" ? " checked" : "") . ">Yes <input type=radio name=forumpost value=no" .($user["forumpost"]=="no" ? " checked" : "") . ">No</td></tr>\n");
	 print("<tr><td class=rowhead>Upload possible??</td><td colspan=2 align=left><input type=radio name=uploadpos value=yes" .($user["uploadpos"]=="yes" ? " checked" : "") . ">Yes <input type=radio name=uploadpos value=no" .($user["uploadpos"]=="no" ? " checked" : "") . ">No</td></tr>\n");
	print("<tr><td class=rowhead>Download possible??</td><td colspan=2 align=left><input type=radio name=downloadpos value=yes" .($user["downloadpos"]=="yes" ? " checked" : "") . ">Yes <input type=radio name=downloadpos value=no" .($user["downloadpos"]=="no" ? " checked" : "") . ">No</td></tr>\n");
	 print("</td></tr>");
	if (ur::ismod())
	{
	 print("<form method=post action=changeusername.php>\n");
	 print("<tr><td class=rowhead>Change Username</td><td colspan=2 align=left><input type=text size=25 name=username value=\"" . htmlspecialchars($user[username]) . "\"></tr>\n");
	}
	if (ur::ismod())
	{
	 print("<form method=post action=changemail.php>\n");
	 print("<tr><td class=rowhead>Change E-mail</td><td colspan=2 align=left><input type=text size=80 name=email value=\"" . htmlspecialchars($user[email]) . "\"></tr>\n");
	}
	if (ur::ismod())
	{
		print("<tr><td class=rowhead>Change Password</td><td colspan=2 align=left><input type=\"password\" name=\"chpassword\" size=\"50\" /></td></tr>\n");
		print("<tr><td class=rowhead>Repeat Password</td><td colspan=2 align=left><input type=\"password\" name=\"passagain\" size=\"50\" /></td></tr>\n");
	}
	if (ur::ismod())
	{
		
		print("<tr><td class=rowhead>Amount Uploaded</td><td colspan=2 align=left><input type=text size=60 name=uploaded value=\"" . htmlspecialchars($user[uploaded]) . "\"></tr>\n");
		print("<tr><td class=rowhead>Amount Downloaded</td><td colspan=2 align=left><input type=text size=60 name=downloaded value=\"" . 
		htmlspecialchars($user[downloaded]) . "\"></tr>\n");
		print("<tr><td class=rowhead>Passkey</td><td colspan=2 align=left><input name=resetkey value=yes type=checkbox> Reset passkey</td></tr>\n");
	}
	
		print("<tr><td colspan=3 align=center><input type=submit class=btn value='Okay'></td></tr></form>\n");
		print("</table>\n");
		
		print("</table>\n");
	 if (ur::ismod())
	{
		begin_frame("Delete User", true);
		javascript('todger');
		$username = htmlspecialchars($user["username"]);
		print(" <form method=post action=delacctadmin.php name=deluser>
		<tr><td class=rowhead><input name=username size=10 value=". $username ." type=hidden>
		<input name=delenable type=checkbox onClick=\"if (this.checked) {enabledel();}else{disabledel();}\"><input name=submit type=submit class=btn value=\"Delete\" disabled></td></tr></form>");
	}
		end_frame();
		stdfoot();
}else
ug();
?>