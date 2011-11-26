<?php
  require "include/bittorrent.php";
	  
	  
$newpage = new page_verify();
$newpage->check('sendmessage');

  if ($_SERVER["REQUEST_METHOD"] != "POST")
    stderr("Error", "Permission Denied!");
    
  loggedinorreturn();
  
  $n_pms = $_POST["n_pms"];
  if ($n_pms)
  {  			                                                      //////  MM  ///
    if (get_user_class() < UC_MODERATOR)
	  stderr("Error", "Permission denied");

    $msg = trim($_POST["msg"]);
		if (!$msg)
	  	stderr("Error","Please enter something!");

    $sender_id = ($_POST['sender'] == 'system' ? 0 : $CURUSER['id']);
     
	$from_is = unesc($_POST['pmees']);
	     
	// Change
	$subject = trim($_POST['subject']);
	     
	$query = "INSERT INTO messages (sender, receiver, added, msg, subject, location, poster) ". "SELECT $sender_id, u.id, '" . get_date_time() . "', " .
	
	sqlesc($msg) . ", " . sqlesc($subject) . ", 1, $sender_id " . $from_is;
	// End of Change

    sql_query($query) or sqlerr(__FILE__, __LINE__);
    $n = mysql_affected_rows();

    $comment = $_POST['comment'];
    $snapshot = $_POST['snap'];

    // add a custom text or stats snapshot to comments in profile
    if ($comment || $snapshot)
    {
	    $res = sql_query("SELECT u.id, u.uploaded, u.downloaded, u.modcomment ".$from_is) or sqlerr(__FILE__, __LINE__);
	    if (mysql_num_rows($res) > 0)
	    {
	      $l = 0;
	      while ($user = mysql_fetch_array($res))
	      {
	        unset($new);
	        $old = $user['modcomment'];
	        if ($comment)
	          $new = $comment;
	        if ($snapshot)
	        {
	          $new .= ($new?"\n":"") .
	            "MMed, " . gmdate("Y-m-d") . ", " .
	            "UL: " . mksizegb($user['uploaded']) . ", " .
	            "DL: " . mksizegb($user['downloaded']) . ", " .
	            "r: " . ratios($user['uploaded'],$user['downloaded'], False) . " - " .
	            ($_POST['sender'] == "system"?"System":$CURUSER['username']);
	        }
	      	$new .= $old?("\n".$old):$old;
		      sql_query("UPDATE users SET modcomment = " . sqlesc($new) . " WHERE id = " . $user['id'])
		        or sqlerr(__FILE__, __LINE__);
	  	    if (mysql_affected_rows())
	    	    $l++;
	      }
	    }
    }
  }
  else
  {               																							//////  PM  ///
  	$receiver = $_POST["receiver"];
	  $origmsg = $_POST["origmsg"];
	  $save = $_POST["save"];
 	  $returnto = $_POST["returnto"];
 	  
 	  // Anti Flood Code
       // This code ensures that a member can only send one PM per minute.
global $___flood___,$usergroups;
$___flood___->protect("last_pm",'PM',$usergroups['antifloodtime'],1);
	  if (!is_valid_id($receiver) || ($origmsg && !is_valid_id($origmsg)))
	  	stderr("Error","Invalid ID");

		$msg = trim($_POST["msg"]);
		if (!$msg)
		  stderr("Error","Please enter something!");
		     
		// Change
		$save = ($save == 'yes') ? "yes" : "no";
		// End of Change
		     
		$res = sql_query("SELECT username,parked,email,acceptpms, notifs, UNIX_TIMESTAMP(last_access) as la FROM users WHERE id=$receiver") or sqlerr(__FILE__, __LINE__);
		$user = mysql_fetch_assoc($res);
	  if (!$user)
	    stderr("Error", "No user with this ID");

	  //Make sure recipient wants this message
		if (get_user_class() < UC_MODERATOR)
		{
			if ($user["parked"] == "yes")
     			stderr("Refused", "This account is parked.");
    	if ($user["acceptpms"] == "yes")
	    {
	      $res2 = sql_query("SELECT * FROM blocks WHERE userid=$receiver AND blockid=" . $CURUSER["id"]) or sqlerr(__FILE__, __LINE__);
	      if (mysql_num_rows($res2) == 1)
	        stderr("Refused", "This user has blocked PMs from you.");
	    }
	    elseif ($user["acceptpms"] == "friends")
	    {
	      $res2 = sql_query("SELECT * FROM friends WHERE userid=$receiver AND friendid=" . $CURUSER["id"]) or sqlerr(__FILE__, __LINE__);
	      if (mysql_num_rows($res2) != 1)
	        stderr("Refused", "This user only accepts PMs from users in his friends list.");
	    }
	    elseif ($user["acceptpms"] == "no")
	      stderr("Refused", "This user does not accept PMs.");
	  }

		$subject = trim($_POST['subject']);
		sql_query("INSERT INTO messages (poster, sender, receiver, added, msg, subject, saved, location) VALUES(" . $CURUSER["id"] . ", " . $CURUSER["id"] . ",
		
		$receiver, '" . get_date_time() . "', " . sqlesc($msg) . ", " . sqlesc($subject) . ", " . sqlesc($save) . ", 1)") or sqlerr(__FILE__, __LINE__);
		$msgid=mysql_insert_id();
		$date=get_date_time();
	  // Update Last PM sent...
       $___flood___->update('last_pm');

	  // Send notification email. 
		$mystring = $user['notifs'];
		$findme  = '[pm]';
		$pos = strpos($mystring, $findme);
		if ($pos === false)
			$sm = false;
		else
			$sm = true;
	
	  if ($sm)
	  {
		  	    
	    $username = trim($CURUSER["username"]);
	    $msg_receiver = trim($user["username"]);
	    
$body = <<<EOD
		Dear $msg_receiver,
		
		You have received a PM.
		
		Sender	: $username
		Subject	: $subject
		Date		: $date
		
		You can use the URL below to view the message (you may have to login).
		
		$DEFAULTBASEURL/messages.php?action=viewmessage&id=$msgid
		
		------
		Yours,
		The $SITENAME Team.
EOD;

sent_mail($user["email"],$SITENAME,$SITEEMAIL,"$SITENAME You have received a PM from " . $username . "!",$body,"sendmessage",false);
   
	  }
	  $delete = $_POST["delete"];

	  if ($origmsg)
	  {
      if ($delete == "yes")
      {
	      // Make sure receiver of $origmsg is current user
	      $res = sql_query("SELECT * FROM messages WHERE id=$origmsg") or sqlerr(__FILE__, __LINE__);
	      if (mysql_num_rows($res) == 1)
	      {
	        $arr = mysql_fetch_assoc($res);
	        if ($arr["receiver"] != $CURUSER["id"])
	          stderr("w00t","This shouldn't happen.");
	         if ($arr["saved"] == "no")
				  sql_query("DELETE FROM messages WHERE id=$origmsg") or sqlerr(__FILE__, __LINE__);
			elseif ($arr["saved"] == "yes")
				  sql_query("UPDATE messages SET location = '0' WHERE id=$origmsg") or sqlerr(__FILE__, __LINE__);

	      }
      }
   	  if (!$returnto)
   	  	$returnto = "$BASEURL/messages.php";
	  }

    if ($returnto)
    {
      header("Location: $returnto");
      die;
    }

	  stdhead();
	  stdmsg("Succeeded", (($n_pms > 1) ? "$n messages out of $n_pms were" : "Message was").
	    " successfully sent!" . ($l ? " $l profile comment" . (($l>1) ? "s were" : " was") . " updated!" : ""));
	}
	stdfoot();
	exit;
?>