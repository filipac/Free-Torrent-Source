<?php
require_once("include/bittorrent.php");

loggedinorreturn();

parked();
stdhead("Confirm");
begin_main_frame();

$offcommentid = $_GET["offcommentid"];
$reportofferid = $_GET["reportofferid"];
$reqcommentid = $_GET["reqcommentid"];
$requestid = $_GET["reportrequestid"];
$takeuser = $_POST["user"];
$takecommentid = $_POST["commentid"];
$taketorrent = $_POST["torrent"];
$takeforumid = $_POST["forumid"];
$takeforumpost = (int)$_POST["forumpost"];
$takereason = mysql_real_escape_string( htmlspecialchars( trim($_POST["reason"]) ));

$takeoffcommentid = $_POST["takeoffcommentid"];
$takereportofferid = $_POST["takereportofferid"];
$takereqcommentid = $_POST["takereqcommentid"];
$takerequestid = $_POST["takerequestid"];
$user = $_GET["user"];
$commentid = $_GET["commentid"];
$torrent = $_GET["torrent"];
$forumid = $_GET["forumid"];
$forumpost = $_GET["forumpost"];


//////////OFFER #1 START//////////
if ((isset($takereportofferid)) && (isset($takereason)))
{
	int_check($takereportofferid);
	// Check if takereason is set
	if ($takereason == ''){
	stdmsg("Error","Missing Reason!");
	end_main_frame();
	die();
	}
$res = sql_query("SELECT id FROM reports WHERE addedby = ".mysql_real_escape_string($CURUSER[id])." AND votedfor= ".mysql_real_escape_string($takereportofferid)." AND type = 'offer'") or sqlerr(__FILE__,__LINE__);
if (mysql_num_rows($res) == 0)
{
	$date = sqlesc(get_date_time());
	
	sql_query("INSERT into reports (addedby,votedfor,type,reason,added) VALUES (".mysql_real_escape_string($CURUSER[id]).",".mysql_real_escape_string($takereportofferid).",'offer', '$takereason',$date)") or sqlerr(__FILE__,__LINE__);
	stdmsg("Message","Successfully reported!");
	end_main_frame();
	stdfoot();
	die();
}
else
{
	stdmsg("Error","You have already reported this offer!");
	end_main_frame();
	stdfoot();
	die();
}
}
//////////OFFER #1 END//////////

//////////REQUEST #1 START//////////
if ((isset($takerequestid)) && (isset($takereason)))
{
	int_check($takerequestid);
	// Check if takereason is set
	if ($takereason == ''){
	stdmsg("Error","Missing Reason!");
	end_main_frame();
	die();
	}
$res = sql_query("SELECT id FROM reports WHERE addedby = ".mysql_real_escape_string($CURUSER[id])." AND votedfor= ".mysql_real_escape_string($takerequestid)." AND type = 'request'") or sqlerr(__FILE__,__LINE__);
if (mysql_num_rows($res) == 0)
{
	$date = sqlesc(get_date_time());
	
	sql_query("INSERT into reports (addedby,votedfor,type,reason,added) VALUES (".mysql_real_escape_string($CURUSER[id]).",".mysql_real_escape_string($takerequestid).",'request', '$takereason',$date)") or sqlerr(__FILE__,__LINE__);
	stdmsg("Message","Successfully reported!");
	end_main_frame();
	stdfoot();
	die();
}
else
{
	stdmsg("Error","You have already reported this request!");
	end_main_frame();
	stdfoot();
	die();
}
}
//////////REQUEST #1 END//////////

//////////USER #1 START//////////
if ((isset($takeuser)) && (isset($takereason)))
{
	int_check($takeuser);
// Check if takereason is set
if ($takereason == ''){
stdmsg("Error","Missing Reason!");
end_main_frame();
die();
}
$res = sql_query("SELECT id FROM reports WHERE addedby = ".mysql_real_escape_string($CURUSER[id])." AND votedfor = ".mysql_real_escape_string($takeuser)." AND type = 'user'") or sqlerr(__FILE__,__LINE__);
if (mysql_num_rows($res) == 0)
{
$date = sqlesc(get_date_time());

sql_query("INSERT into reports (addedby,votedfor,type,reason,added) VALUES (".mysql_real_escape_string($CURUSER[id]).",".mysql_real_escape_string($takeuser).",'user', '$takereason',$date)") or sqlerr(__FILE__,__LINE__);
stdmsg("Message","Successfully reported!");
end_main_frame();
stdfoot();
die();
}
else
{
stdmsg("Error","You have already reported this user!");
end_main_frame();
stdfoot();
die();
}
}
//////////USER #1 END//////////

//////////TORRENT #1 START//////////
if ((isset($taketorrent)) && (isset($takereason)))
{
	int_check($taketorrent);
// Check if takereason is set
if ($takereason == ''){
stdmsg("Error","Missing Reason!");
end_main_frame();
die();
}
$res = sql_query("SELECT id FROM reports WHERE addedby = ".mysql_real_escape_string($CURUSER[id])." AND votedfor = ".mysql_real_escape_string($taketorrent)." AND type = 'torrent'") or sqlerr(__FILE__,__LINE__);
if (mysql_num_rows($res) == 0)
{
$date = sqlesc(get_date_time());

sql_query("INSERT into reports (addedby,votedfor,type,reason,added) VALUES (".mysql_real_escape_string($CURUSER[id]).",".mysql_real_escape_string($taketorrent).",'torrent', '$takereason',$date)") or sqlerr(__FILE__,__LINE__);
stdmsg("Message","Successfully reported!");
end_main_frame();
stdfoot();
die();
}
else
{
stdmsg("Error","You have already reported this torrent!");
end_main_frame();
stdfoot();
die();
}
}
//////////TORRENT #1 END//////////

//////////FORUM #1 START//////////
if ((isset($takeforumid)) && (isset($takereason)))
{
	int_check($takeforumid);
// Check if takereason is set
if ($takereason == ''){
stdmsg("Error","Missing Reason!");
end_main_frame();
stdfoot();
die();
}
$res = sql_query("SELECT id FROM reports WHERE addedby = ".mysql_real_escape_string($CURUSER[id])." AND votedfor= ".mysql_real_escape_string($takeforumid)." AND votedfor_xtra= ".mysql_real_escape_string($takeforumpost)." AND type = 'forum'") or sqlerr(__FILE__,__LINE__);
if (mysql_num_rows($res) == 0)
{
$date = sqlesc(get_date_time());

sql_query("INSERT into reports (addedby,votedfor,votedfor_xtra,type,reason,added) VALUES (".mysql_real_escape_string($CURUSER[id]).",".mysql_real_escape_string($takeforumid).",".mysql_real_escape_string($takeforumpost)." ,'forum', '$takereason',$date)") or sqlerr(__FILE__,__LINE__);
stdmsg("Message","Successfully reported!");
end_main_frame();
stdfoot();
die();
}
else
{
stdmsg("Error","You have already reported this forum post!");
end_main_frame();
stdfoot();
die();
}
}
//////////FORUM #1 END//////////

//////////COMMENT #1 START//////////
if ((isset($takecommentid)) && (isset($takereason)))
{
	int_check($takecommentid);
// Check if takereason is set
if ($takereason == ''){
stdmsg("Error","Missing Reason!");
end_main_frame();
stdfoot();
die();
}
$res = sql_query("SELECT id FROM reports WHERE addedby = ".mysql_real_escape_string($CURUSER[id])." AND votedfor= ".mysql_real_escape_string($takecommentid)." AND type = 'comment'") or sqlerr(__FILE__,__LINE__);
if (mysql_num_rows($res) == 0)
{
$date = sqlesc(get_date_time());

sql_query("INSERT into reports (addedby,votedfor,type,reason,added) VALUES (".mysql_real_escape_string($CURUSER[id]).",".mysql_real_escape_string($takecommentid).",'comment','$takereason',$date)") or sqlerr(__FILE__,__LINE__);
stdmsg("Message","Successfully reported!");
end_main_frame();
stdfoot();
die();
}
else
{
stdmsg("Error","You have already reported this comment!");
end_main_frame();
stdfoot();
die();
}
}
//////////COMMENT #1 END//////////

//////////REQ.COMMENT #1 START//////////
if ((isset($takereqcommentid)) && (isset($takereason)))
{
	int_check($takereqcommentid);
// Check if takereason is set
if ($takereason == ''){
stdmsg("Error","Missing Reason!");
end_main_frame();
stdfoot();
die();
}
$res = sql_query("SELECT id FROM reports WHERE addedby = ".mysql_real_escape_string($CURUSER[id])." AND votedfor= ".mysql_real_escape_string($takereqcommentid)." AND type = 'reqcomment'") or sqlerr(__FILE__,__LINE__);
if (mysql_num_rows($res) == 0)
{
$date = sqlesc(get_date_time());

sql_query("INSERT into reports (addedby,votedfor,type,reason,added) VALUES (".mysql_real_escape_string($CURUSER[id]).",".mysql_real_escape_string($takereqcommentid).",'reqcomment','$takereason',$date)") or sqlerr(__FILE__,__LINE__);
stdmsg("Message","Successfully reported!");
end_main_frame();
stdfoot();
die();
}
else
{
stdmsg("Error","You have already reported this comment!");
end_main_frame();
stdfoot();
die();
}
}
//////////REQ.COMMENT #1 END//////////

//////////OFFER.COMMENT #1 START//////////
if ((isset($takeoffcommentid)) && (isset($takereason)))
{
	int_check($takeoffcommentid);
// Check if takereason is set
if ($takereason == ''){
stdmsg("Error","Missing Reason!");
end_main_frame();
stdfoot();
die();
}
$res = sql_query("SELECT id FROM reports WHERE addedby = ".mysql_real_escape_string($CURUSER[id])." AND votedfor= ".mysql_real_escape_string($takeoffcommentid)." AND type = 'offercomment'") or sqlerr(__FILE__,__LINE__);
if (mysql_num_rows($res) == 0)
{
$date = sqlesc(get_date_time());

sql_query("INSERT into reports (addedby,votedfor,type,reason,added) VALUES (".mysql_real_escape_string($CURUSER[id]).",".mysql_real_escape_string($takeoffcommentid).",'offercomment','$takereason',$date)") or sqlerr(__FILE__,__LINE__);
stdmsg("Message","Successfully reported!");
end_main_frame();
stdfoot();
die();
}
else
{
stdmsg("Error","You have already reported this comment!");
end_main_frame();
stdfoot();
die();
}
}
//////////OFFER.COMMENT #1 END//////////


//////////USER #2 START//////////
if (isset($user))
{
	int_check($user);
	if ($user == $CURUSER[id]) {
		stdmsg("Sorry","You can not report yourself!");
		end_main_frame();
		stdfoot();
		die;
	}
$res = sql_query("SELECT username, class FROM users WHERE id=".mysql_real_escape_string($user)) or sqlerr(__FILE__,__LINE__);
if (mysql_num_rows($res) == 0)
{
stdmsg("Error","Invalid User Id!");
end_main_frame();
stdfoot();
die();
}

$arr = mysql_fetch_assoc($res);
if ($arr["class"] > 6)
{
stdmsg("Sorry","You can not report sysops!");
end_main_frame();
stdfoot();
die();
}

else
{
print("<h2>Are you sure you would like to report this user <a href=userdetails.php?id=".htmlspecialchars($user)."><b><font color=#ff0532>".htmlspecialchars($arr[username])."</font></b></a>? to the Staff for violation of the rules</h2><p></p>");
print("<p>Please note, this is <b>not</b> to be used to report leechers, we have scripts in place to deal with them!</p>");
print("<b>Reason</b><font color=#ff0532> is (required): </font><form method=post action=report.php><input type=hidden name=user value=".htmlspecialchars($user)."><input type=text size=100 name=reason><p></p><input type=submit class=btn value=Confirm >&nbsp;&nbsp;<u><b><font color=#ff0532> PLEASE Note! </font></u>&nbsp;&nbsp;If no reason is Given you will get a Warning yourself.</b></form>");
}
}
//////////USER #2 END//////////

//////////TORRENT #2 START//////////
if (isset($torrent))
{
	int_check($torrent);
$res = sql_query("SELECT name FROM torrents WHERE id=".mysql_real_escape_string($torrent));

if (mysql_num_rows($res) == 0)
{
stdmsg("Error","Invalid Torrent Id!");
end_main_frame();
stdfoot();
die();
}
$arr = mysql_fetch_array($res);
print("<h2>Are you sure you would like to report this torrent <a href=details.php?id=".htmlspecialchars($torrent)."><b><font color=#ff0532>".htmlspecialchars($arr[name])."</font></b></a>? to the Staff for violation of the rules</h2><p></p>");
print("<p>Please note, this is <b>not</b> to be used to report leechers, we have scripts in place to deal with them!</p>");
print("<b>Reason</b><font color=#ff0532> is (required): </font><form method=post action=report.php><input type=hidden name=torrent value=".htmlspecialchars($torrent)."><input type=text size=100 name=reason><p></p><input type=submit class=btn value=Confirm>&nbsp;&nbsp;<u><b><font color=#ff0532> PLEASE Note! </font></u>&nbsp;&nbsp;If no reason is Given you will get a Warning yourself.</b></form>"); }
//////////TORRENT #2 END//////////

//////////FORUM #2 START//////////
if (isset($forumid) && isset($forumpost))
{
	int_check(array("$forumid","$forumpost"));
$res = sql_query("SELECT subject FROM topics WHERE id=".mysql_real_escape_string($forumid));

if (mysql_num_rows($res) == 0)
{
stdmsg("Error","Invalid Forum Id!");
end_main_frame();
stdfoot();
die();
}
$arr = mysql_fetch_array($res);
print("<h2>Are you sure you would like to report the following forum post <a href=$BASEURL/forums/viewtopic.php?topicid=".htmlspecialchars($forumid)."&page=p#".htmlspecialchars($forumpost)."><b><font color=#ff0532>".htmlspecialchars($arr[subject])."</font></b></a>? to the Staff for violation of the rules</h2><p></p>");
print("<b>Reason</b><font color=#ff0532> is (required): </font><form method=post action=report.php><input type=hidden name=forumid value=".htmlspecialchars($forumid)."><input type=hidden name=forumpost value=".htmlspecialchars($forumpost)."><input type=text size=100 name=reason><p></p><input type=submit class=btn value=Confirm>&nbsp;&nbsp;<u><b><font color=#ff0532> PLEASE Note! </font></u>&nbsp;&nbsp;If no reason is Given you will get a Warning yourself.</b></form>"); }
//////////FORUM #2 END//////////

//////////COMMENT #2 START//////////
if (isset($commentid))
{
	int_check($commentid);
$res = sql_query("SELECT id FROM comments WHERE id=".mysql_real_escape_string($commentid));
if (mysql_num_rows($res) == 0)
{
stdmsg("Error","Invalid Comment Id!");
end_main_frame();
stdfoot();
die();
}
$arr = mysql_fetch_array($res);
print("<h2>Are you sure you would like to report the following comment <a href=comment.php?action=edit&cid=".htmlspecialchars($commentid)."><b><font color=#ff0532>ID - ".htmlspecialchars($commentid)."</font></b></a>? to the Staff for violation of the rules</h2><p></p>");
print("<b>Reason</b><font color=#ff0532> is (required): </font><form method=post action=report.php><input type=hidden name=commentid value=".htmlspecialchars($commentid)."><input type=text size=100 name=reason><p></p><input type=submit class=btn value=Confirm>&nbsp;&nbsp;<u><b><font color=#ff0532> PLEASE Note! </font></u>&nbsp;&nbsp;If no reason is Given you will get a Warning yourself.</b></form>");
}
//////////COMMENT #2 END//////////

//////////REQ.COMMENT #2 START//////////
if (isset($reqcommentid))
{
	int_check($reqcommentid);
$res = sql_query("SELECT id FROM comments WHERE request = 1 AND id=".mysql_real_escape_string($reqcommentid));
if (mysql_num_rows($res) == 0)
{
stdmsg("Error","Invalid Req.Comment Id!");
end_main_frame();
stdfoot();
die();
}
$arr = mysql_fetch_array($res);
print("<h2>Are you sure you would like to report the following req.comment <a href=viewrequests.php?><b><font color=#ff0532>ID - ".htmlspecialchars($reqcommentid)."</font></b></a>? to the Staff for violation of the rules</h2><p></p>");
print("<b>Reason</b><font color=#ff0532> is (required): </font><form method=post action=report.php><input type=hidden name=takereqcommentid value=".htmlspecialchars($reqcommentid)."><input type=text size=100 name=reason><p></p><input type=submit class=btn value=Confirm>&nbsp;&nbsp;<u><b><font color=#ff0532> PLEASE Note! </font></u>&nbsp;&nbsp;If no reason is Given you will get a Warning yourself.</b></form>");
}
//////////REQ.COMMENT #2 END//////////

//////////OFFER.COMMENT #2 START//////////
if (isset($offcommentid))
{
	int_check($offcommentid);
$res = sql_query("SELECT id FROM comments WHERE offer = 1 AND id=".mysql_real_escape_string($offcommentid));
if (mysql_num_rows($res) == 0)
{
stdmsg("Error","Invalid offer Offer.Comment Id!");
end_main_frame();
stdfoot();
die();
}
$arr = mysql_fetch_array($res);
print("<h2>Are you sure you would like to report the following Offer.Comment <a href=viewoffers.php><b><font color=#ff0532>ID - ".htmlspecialchars($arr[id])."</font></b></a>? to the Staff for violation of the rules</h2><p></p>");
print("<b>Reason</b><font color=#ff0532> is (required): </font><form method=post action=report.php><input type=hidden name=takeoffcommentid value=".htmlspecialchars($arr[id])."><input type=text size=100 name=reason><p></p><input type=submit class=btn value=Confirm>&nbsp;&nbsp;<u><b><font color=#ff0532> PLEASE Note! </font></u>&nbsp;&nbsp;If no reason is Given you will get a Warning yourself.</b></form>");
}
//////////OFFER.COMMENT #2 END//////////

//////////OFFER #2 START//////////
if (isset($reportofferid))
{
	int_check($reportofferid);
$res = sql_query("SELECT id,name FROM offers WHERE id=".mysql_real_escape_string($reportofferid));
if (mysql_num_rows($res) == 0)
{
stdmsg("Error","Invalid offer Id!");
end_main_frame();
stdfoot();
die();
}
$arr = mysql_fetch_array($res);
print("<h2>Are you sure you would like to report the following offer <a href=viewoffers.php?id=$arr[id]&off_details=1><b><font color=#ff0532>ID - ".htmlspecialchars($arr[id])."</font></b></a>? to the Staff for violation of the rules</h2><p></p>");
print("<b>Reason</b><font color=#ff0532> is (required): </font><form method=post action=report.php><input type=hidden name=takereportofferid value=".htmlspecialchars($arr[id])."><input type=text size=100 name=reason><p></p><input type=submit class=btn value=Confirm>&nbsp;&nbsp;<u><b><font color=#ff0532> PLEASE Note! </font></u>&nbsp;&nbsp;If no reason is Given you will get a Warning yourself.</b></form>");
}
//////////OFFERT #2 END//////////

//////////REQUEST #2 START//////////
if (isset($requestid))
{
	int_check($requestid);
$res = sql_query("SELECT id,request FROM requests WHERE id=".mysql_real_escape_string($requestid));
if (mysql_num_rows($res) == 0)
{
stdmsg("Error","Invalid Request Id!");
end_main_frame();
stdfoot();
die();
}
$arr = mysql_fetch_array($res);
print("<h2>Are you sure you would like to report the following request <a href=viewrequests.php?id=$arr[id]&req_details=1><b><font color=#ff0532>ID - ".$arr["id"]."</font></b></a>? to the Staff for violation of the rules</h2><p></p>");
print("<b>Reason</b><font color=#ff0532> is (required): </font><form method=post action=report.php><input type=hidden name=takerequestname value='".$arr["request"]."'<input type=hidden name=takerequestid value='".$arr["id"]."'><input type=text size=100 name=reason><p></p><input type=submit class=btn value=Confirm>&nbsp;&nbsp;<u><b><font color=#ff0532> PLEASE Note! </font></u>&nbsp;&nbsp;If no reason is Given you will get a Warning yourself.</b></form>");
}
//////////REQUEST #2 END//////////

if ((!isset($user)) && (!isset($offcommentid)) && (!isset($reqcommentid)) && (!isset($reportofferid)) && (!isset($requestid)) && (!isset($torrent)) && (!isset($forumid)) && (!isset($forumpost)) && (!isset($commentid)))
	stdmsg("Error","Missing Reason!");

end_main_frame();
stdfoot();
?>