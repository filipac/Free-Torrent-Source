<?php

require "include/bittorrent.php";

if ($HTTP_SERVER_VARS["REQUEST_METHOD"] != "POST")
 stderr("Error", "Method");

   

    loggedinorreturn();
	
       $msg = trim($_POST["msg"]);
       $subject = trim($_POST["subject"]);

       if (!$msg)
    stderr("Error","Please enter something!");

       if (!$subject)
    stderr("Error","You need to define subject!");

     $added = "'" . get_date_time() . "'";
     $userid = $CURUSER['id'];
     $message = sqlesc($msg);
     $subject = sqlesc($subject);
     
       // Anti Flood Code
       // This code ensures that a member can only send one PM per minute.
global $___flood___,$usergroups;
$___flood___->protect('last_staffmsg','PM to STAFF',$usergroups['antifloodtime'],1);
 sql_query("INSERT INTO staffmessages (sender, added, msg, subject) VALUES($userid, $added, $message, $subject)") or sqlerr(__FILE__, __LINE__);
 // Update Last PM sent...
 $___flood___->update('last_staffmsg');

       if ($_POST["returnto"])
 {
   header("Location: " . htmlentities($_POST["returnto"]));
   die;
 }

  stdhead();
  stdmsg("Succeeded", "Message was succesfully sent!");
       
       stdfoot();
       exit;
?>