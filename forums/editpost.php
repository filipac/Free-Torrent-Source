<?php
$rootpath = '../';
require_once($rootpath."forums/functions/fts.php");
require_once($rootpath."include/bittorrent.php");
  

  loggedinorreturn();
  $wherethisuser = where ($_SERVER["SCRIPT_FILENAME"],$CURUSER["id"]);

  iplogger();
	parked();
    $postid = 0+$_GET["postid"];

    int_check($postid,true);

    $res = sql_query("SELECT * FROM posts WHERE id=$postid") or sqlerr(__FILE__, __LINE__);

		if (mysql_num_rows($res) != 1)
			stderr("Error", "No post with this ID");

		$arr = mysql_fetch_assoc($res);

    $res2 = sql_query("SELECT locked FROM topics WHERE id = " . $arr["topicid"]) or sqlerr(__FILE__, __LINE__);
		$arr2 = mysql_fetch_assoc($res2);

 		if (mysql_num_rows($res) != 1)
			stderr("Error", "No topic associated with this post ID");

		$locked = ($arr2["locked"] == 'yes');

    if (($CURUSER["id"] != $arr["userid"] || $locked) && get_user_class() < UC_MODERATOR)
      stderr("Error", "Denied!");

    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
    	$body = $_POST['body'];

    	if ($body == "")
    	  stderr("Error", "Body cannot be empty!");

      $body = sqlesc($body);

      $editedat = sqlesc(get_date_time());

      sql_query("UPDATE posts SET body=$body, editedat=$editedat, editedby=$CURUSER[id] WHERE id=$postid") or sqlerr(__FILE__, __LINE__);

		$returnto = $_POST["returnto"];

			if ($returnto != "")
			{
				$returnto .= "&page=p$postid#$postid";
				header("Location: $returnto");
			}
			else
				stderr("Success", "Post was edited successfully.");
    }

    stdhead();

    print("<h1>Edit Post</h1>\n");
       
   print("<form name=edit method=post action=editpost.php?postid=$postid>\n");
       
   print("<input type=hidden name=returnto value=\"" . htmlspecialchars($HTTP_SERVER_VARS["HTTP_REFERER"]) . "\">\n");

   print("<p align=center><table class=main border=1 cellspacing=0 cellpadding=5>\n");

   print("<tr><td class=rowhead>Body</td><td align=left style='padding: 0px'>");
       
   textbbcode("edit","body",htmlspecialchars(unesc($arr["body"])));
       
   print("</td></tr>\n");
       
   print("<tr><td align=center colspan=2><input type=submit value='".Okay."' class=btn2></td></tr>\n");

   print("</table>\n</p>");

   print("</form>\n");
       
       stdfoot();

  	die;
  
  ?>