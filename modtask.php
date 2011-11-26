<?php
require "include/bittorrent.php";

loggedinorreturn();

iplogger();



if (get_user_class() < UC_MODERATOR)
	puke();

$action = $_POST["action"];
if ($action == "confirmuser")
{
	$userid = $_POST["userid"];
	$confirm = $_POST["confirm"];
	mysql_query('UPDATE `users` SET `status` = \''.mysql_real_escape_string($confirm).'\', `info` = NULL WHERE `id` = '.mysql_real_escape_string($userid).' LIMIT 1;') or sqlerr(__FILE__, __LINE__);
	header("Location: $BASEURL/admin/unco.php?status=1");
	die;
}
if ($action == "edituser")
{	
	$warned = $_POST["warned"];
	$warnlength = 0 + $_POST["warnlength"];
	$warnpm = $_POST["warnpm"];
	$userid = $_POST["userid"];
	$title = $_POST["title"];
	$avatar = $_POST["avatar"];
	$signature = $_POST["signature"];
	$enabled = $_POST["enabled"];
	$uploadpos = $_POST["uploadpos"];
	$downloadpos = $_POST["downloadpos"];
	$downloaded = $_POST["downloaded"];
	$uploaded = $_POST["uploaded"];
	$privacy = $_POST["privacy"];
	$forumpost = $_POST["forumpost"];
	$email = $_POST["email"];
	$username = $_POST["username"];		
	$chpassword = $_POST["chpassword"];
	$passagain = $_POST["passagain"];
	
	if ($chpassword != "" AND $passagain != "") {
		unset($passupdate);
		$passupdate=false;
		
		if ($chpassword ==  $username OR strlen($chpassword) > 40 OR strlen($chpassword) < 6 OR $chpassword != $passagain)
			$passupdate=false;			
		else
			$passupdate=true;
	}	
	
	if ($passupdate) {
		$sec = mksecret();
		$passhash = md5($sec . $chpassword . $sec);
		$updateset[] = "secret = " . sqlesc($sec);
		$updateset[] = "passhash = " . sqlesc($passhash);
	}
	
	$donor = $_POST["donor"];	
	$donated = $_POST["donated"];	
	$modcomment = $_POST["modcomment"];	
	$support = $_POST["support"];
	$supportlang = $_POST["supportlang"];
	$supportfor = $_POST["supportfor"];	
	$class = 0 + $_POST["class"];
	if (!is_valid_id($userid) || !is_valid_user_class($class))
		stderr("Error", "Bad user ID or class ID.");
	$res = mysql_query("SELECT * FROM users WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
	$arr = mysql_fetch_assoc($res) or puke();
	$res2 = mysql_query("SELECT class FROM users WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
	$arr2 = mysql_fetch_assoc($res2) or puke();
	$res3 = mysql_query("SELECT minclasstoedit FROM usergroups WHERE id = ".sqlesc($arr2['class'])) or sqlerr(__FILE__, __LINE__);
	$minclasstoedit = mysql_fetch_assoc($res3) or puke();
	$curenabled = $arr["enabled"];
	$curparked = $arr["parked"];
	$curuploadpos = $arr["uploadpos"];
	$curdownloadpos = $arr["downloadpos"];
	$curforumpost = $arr["forumpost"];
	
		if ($donated != $arr[donated]) {
			$added = sqlesc(get_date_time());
			mysql_query("INSERT INTO funds (cash, user, added) VALUES ($donated, $userid, $added)") or sqlerr(__FILE__, __LINE__);
			$updateset[] = "donated = " . sqlesc($donated);
			$updateset[] = "total_donated = $arr[total_donated] + " . sqlesc($donated);
		}
		$curclass = $arr["class"];
		$curwarned = $arr["warned"];
		
		

		if ($curclass != $class)
		{		
			$what = ($class > $curclass ? "promoted" : "demoted");
			$msg = sqlesc("You have been $what to '" . get_user_class_name($class) . "' by $CURUSER[username].");
			$added = sqlesc(get_date_time());
			mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES(0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
			$updateset[] = "class = $class";
			$what = ($class > $curclass ? "Promoted" : "Demoted");
			$modcomment = gmdate("Y-m-d") . " - $what to '" . get_user_class_name($class) . "' by $CURUSER[username].\n". $modcomment;
		}
		
		if ($warned && $curwarned != $warned)
		{
			$updateset[] = "warned = " . sqlesc($warned);
			$updateset[] = "warneduntil = '0000-00-00 00:00:00'";

			if ($warned == 'no')
			{
				$modcomment = gmdate("Y-m-d") . " - Warning removed by $CURUSER[username].\n". $modcomment;
				$msg = sqlesc("Your warning have been removed by" . $CURUSER['username'] . ".");
			}

			$added = sqlesc(get_date_time());
			mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
		}
		elseif ($warnlength)
		{
			if ($warnlength == 255)
			{
				$modcomment = gmdate("Y-m-d") . " - Warned by " . $CURUSER['username'] . ".\nReason: $warnpm.\n". $modcomment;
				$msg = sqlesc("You have been [url=rules.php#warning]warned[/url] by $CURUSER[username]." . ($warnpm ? "\n\nReason: $warnpm" : ""));
				$updateset[] = "warneduntil = '0000-00-00 00:00:00'";
			}else{
				$warneduntil = get_date_time(gmtime() + $warnlength * 604800);
				$dur = $warnlength . " week" . ($warnlength > 1 ? "s" : "");
				$msg = sqlesc("You have been [url=rules.php#warning]warned[/url] for $dur by " . $CURUSER['username'] . "." . ($warnpm ? "\n\nReason: $warnpm" : ""));
				$modcomment = gmdate("Y-m-d") . " - Warned for $dur by " . $CURUSER['username'] .  ".\nReason: $warnpm.\n". $modcomment;
				$updateset[] = "warneduntil = '$warneduntil'";
			}

			$added = sqlesc(get_date_time());
			mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
			$updateset[] = "warned = 'yes', timeswarned = timeswarned+1, lastwarned=$added, warnedby=$CURUSER[id]";
		}
		
		if ($enabled != $curenabled)
		{
			if ($enabled == 'yes') {
				$nowdate = sqlesc(get_date_time());
				$modcomment = gmdate("Y-m-d") . " - Enabled by " . $CURUSER['username']. ".\n". $modcomment;			
				mysql_query("UPDATE users SET downloaded='100', uploaded='100,' last_access=$nowdate WHERE id = ".sqlesc($userid));
			} else {
				$modcomment = gmdate("Y-m-d") . " - Disabled by " . $CURUSER['username']. ".\n". $modcomment;		
			}
		}
		if ($privacy == "low" OR $privacy == "normal" OR $privacy == "strong")
			$updateset[] = "privacy = " . sqlesc($privacy);
		
		if ($_POST["resetkey"] == "yes")
		{
			$newpasskey = md5($arr['username'].get_date_time().$arr['passhash']);
			$updateset[] = "passkey = ".sqlesc($newpasskey);
		}
		
		if ($forumpost != $curforumpost)
		{
			if ($forumpost == 'yes')
			{
				$modcomment = gmdate("Y-m-d") . " - Posting enabled by " . $CURUSER['username'] . ".\n" . $modcomment;
				$msg = sqlesc("Your Posting rights have been given back by " . $CURUSER['username'] . ". You can post to forum again.");
				$added = sqlesc(get_date_time());
				mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
			}
			else
			{
				$modcomment = gmdate("Y-m-d") . " - Posting disabled by " . $CURUSER['username'] . ".\n" . $modcomment;
				$msg = sqlesc("Your Posting rights have been removed by " . $CURUSER['username'] . ", propably because of bad Atitdue or description.");
				$added = sqlesc(get_date_time());
				mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
			}
		}
		if ($uploadpos != $curuploadpos)
		{
			if ($uploadpos == 'yes')
			{
				$modcomment = gmdate("Y-m-d") . " - Upload enabled by " . $CURUSER['username'] . ".\n" . $modcomment;
				$msg = sqlesc("Your upload rights have been given back by " . $CURUSER['username'] . ". You can upload torrents again.");
				$added = sqlesc(get_date_time());
				mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
			}
			else
			{
				$modcomment = gmdate("Y-m-d") . " - Upload disabled by " . $CURUSER['username'] . ".\n" . $modcomment;
				$msg = sqlesc("Your upload rights have been removed by " . $CURUSER['username'] . ", propably because of bad torrent .nfo or description.");
				$added = sqlesc(get_date_time());
				mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
			}
		}
		if ($downloadpos != $curdownloadpos)
		{
			if ($downloadpos == 'yes')
			{
				$modcomment = gmdate("Y-m-d") . " - Download enabled by " . $CURUSER['username'] . ".\n" . $modcomment;
				$msg = sqlesc("Your download rights have been given back by " . $CURUSER['username'] . ". You can download torrents again.");
				$added = sqlesc(get_date_time());
				mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
			}
			else
			{
				$modcomment = gmdate("Y-m-d") . " - Download disabled by " . $CURUSER['username'] . ".\n" . $modcomment;
				$msg = sqlesc("Your download rights have been removed by " . $CURUSER['username'] . ", propably because of bad torrent .nfo or description.");
				$added = sqlesc(get_date_time());
				mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
			}
		} 
		
		$updateset[] = "enabled = " . sqlesc($enabled);
		$updateset[] = "uploadpos = " . sqlesc($uploadpos);
		$updateset[] = "downloadpos = " . sqlesc($downloadpos);
		$updateset[] = "downloaded = " . sqlesc($downloaded);
		$updateset[] = "uploaded = " . sqlesc($uploaded);
		$updateset[] = "forumpost = " . sqlesc($forumpost);
		$updateset[] = "email = " . sqlesc($email);
		$updateset[] = "username = " . sqlesc($username);
		$updateset[] = "donor = " . sqlesc($donor);
		$updateset[] = "avatar = " . sqlesc($avatar);
		$updateset[] = "signature = " . sqlesc($signature);
		$updateset[] = "title = " . sqlesc($title);
		$updateset[] = "modcomment = " . sqlesc($modcomment);
		$updateset[] = "support = " . sqlesc($support);
		$updateset[] = "supportfor = " . sqlesc($supportfor);
		$updateset[] = "supportlang = ".sqlesc($supportlang);
		mysql_query("UPDATE users SET  " . implode(", ", $updateset) . " WHERE id=$userid") or sqlerr(__FILE__, __LINE__);

		$returnto = htmlentities($_POST["returnto"]);
		header("Location: $BASEURL/$returnto");
		die;
}
puke();
?>