<?php
/**
 * @version 2.1 
 **/ 
require_once("include/bittorrent.php");
lang::load('viewrequests');	
  
loggedinorreturn();	 
parked();		 //=== uncomment if you use the parked mod 	

if($usergroups['canreq'] !='yes') ug();
if ($_GET["category"]){
$categ = isset($_GET['category']) ? (int)$_GET['category'] : 0;
if(!is_valid_id($categ))
stderr(str2, str3);
}
if ($_GET["requestorid"]){
$requestorid = 0 + htmlentities($_GET["requestorid"]);
#int_check($requestorid);
if (ereg("^[0-9]+$", !$requestorid))
stderr(str2, str3);  
}  
if ($_GET["id"]){
$id = 0 + htmlentities($_GET["id"]);
#int_check($id); 
if (ereg("^[0-9]+$", !$id))
stderr(str2, str3);  
}
//==== add request
if ($_GET["add_request"]){	
$add_request = 0 + $_GET["add_request"];
#int_check($add_request);
if($add_request != '1')
stderr(str2, str3);
if (get_user_class() < UC_POWER_USER)	 //=== requests for power users and above
	stderr(str4,sprintf(str5,$SITENAME),false);
stdhead(str6);
//=== only allow users with a ratio of at least .5 who have uploaded at least 10 gigs or VIP and above
if ($CURUSER)
{
  // ratio as a string

	if ($CURUSER["class"] < UC_VIP)
	{
	$gigsdowned = ($CURUSER["downloaded"]);
	if ($gigsdowned >= 10737418240){
	  $gigs = $CURUSER["uploaded"] / (1024*1024*1024);
	  $ratio = (($CURUSER["downloaded"] > 0) ? ($CURUSER["uploaded"] / $CURUSER["downloaded"]) : 0);
	  }
	}	  
//=== use this if you are using the Karma point system	
/*begin_frame("Request Rules",true);
 	print("To make a request you must have a ratio of at least<b> 0.5</b> AND have uploaded at least <b>10 GB</b>.<br>".
	" A request will also cost you <b><a class=altlink href=mybonus.php>5 Karma Points</a></b>....<br><br> In your particular case ".
	"<a class=altlink href=userdetails.php?id=" . $CURUSER['id'] . ">" . $CURUSER['username'] . "</a>, ");	
*/
//=== use this if you are NOT using the Karma point system	
begin_frame("Request Rules",true,'10','100%');
 	print(str7.
	sprintf(str8,$CURUSER['username']));	
$gigsupped = ($CURUSER["uploaded"]);
$ratio = (($CURUSER["downloaded"] > 0) ? ($CURUSER["uploaded"] / $CURUSER["downloaded"]) : 0); 
//===karma	  //=== uncomment this bit if you are using the karma system
/*
if ($CURUSER["seedbonus"] <5.0)
	  print("you do not have enough <a class=altlink href=mybonus.php>Karma Points</a> ...".
	  " you can not make requests.<p>To view all requests, click <a class=altlink href=viewrequests.php><b>here</b></a></p>\n<br><br>");
//===end	
*/
//=== if you are using the karma mod change this next line too
//elseif ($gigsupped < 10737418240)
if (get_user_class() < UC_VIP && $gigsupped < 10737418240)
	  print(str9.
	  str10);
elseif ($ratio < 0.5 && get_user_class() < UC_VIP){
	$byboth = $byratio && $byul;
	    print(
	      ($byboth ? str11 : "") .
	      ($byratio ? sprintf(str12,format_ratio($CURUSER["uploaded"],$CURUSER["downloaded"])) : "</b>") .
	      ($byboth ? str13 : "") .
	      ($byul ? sprintf(str14,round($gigs,2)) : "") . "" .
	      ($byboth ? "" : "") . str15 .
	      ($byboth ? "" : str16 . ($byratio ? sprintf(str17,round($gigs,2)) : sprintf(str18,format_ratio($CURUSER["uploaded"],$CURUSER["downloaded"])) ) . str19));
	}
else
	{
print(str20);
//===end check
print("<table border=1 width=100% cellspacing=0 cellpadding=5><tr><td class=colhead align=left>".str21.
"</td></tr><tr><td align=left class=clearalt6><form method=get action=browse.php>".
"<input type=text name=search size=40 value=\"".htmlspecialchars($searchstr)."\" />".str22." <select name=cat> <option value=0>".str23."</option>");
$cats = genrelist();
$catdropdown = "";
foreach ($cats as $cat) {
   $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
   if ($cat["id"] == $_GET["cat"])
   $catdropdown .= " selected=\"selected\"";
   $catdropdown .= ">" . htmlspecialchars($cat["name"]) . "</option>\n";
}
$deadchkbox = "<input type=\"checkbox\" name=\"incldead\" value=\"1\"";
if ($_GET["incldead"])
$deadchkbox .= " checked=\"checked\"";
$deadchkbox .= " /> including dead torrents\n";
print(" ".$catdropdown." </select> ".$deadchkbox." <input type=submit value=".str24." class=btn /></form></td></tr></table><br>\n");
print("<form method=post name=compose action=". $_SERVER[PHP_SELF] ."?new_request=1><a name=add id=add></a>".
"<table border=1 width=100% cellspacing=0 cellpadding=5><tr><td class=colhead align=left colspan=2>".str25.
"</td></tr>".
"<tr><td align=right class=clearalt6><b>".str26."</b></td><td align=left class=clearalt6><input type=text size=40 name=requesttitle>".
"<select name=category><option value=0>".str27."</option>\n");
$res2 = mysql_query("SELECT id, name FROM categories  order by name");
$num = mysql_num_rows($res2);
$catdropdown2 = "";
for ($i = 0; $i < $num; ++$i)
   {
 $cats2 = mysql_fetch_assoc($res2);  
 $catdropdown2 .= "<option value=\"" . $cats2["id"] . "\"";
 $catdropdown2 .= ">" . htmlspecialchars($cats2["name"]) . "</option>\n";
   }
print("".$catdropdown2." </select><br><tr><td align=right class=clearalt6 valign=top><b>".str28."</b></td><td align=left class=clearalt6>".
"<input type=text name=picture size=80><br>".str29."</td></tr>".
"<tr><td align=right class=clearalt6><b>".str30."</b></td><td align=left class=clearalt6>\n");
textbbcode("compose","body","$body");
print("</td></tr><tr><td align=center  class=clearalt6 colspan=2><input type=submit value='".str31."' class=btn></td></tr></form><br><br></table><br>\n");
}
}
$res = mysql_query("SELECT users.username, requests.id, requests.userid, requests.request, requests.added, uploaded, downloaded, categories.image, categories.name as cat FROM requests inner join categories on requests.cat = categories.id inner join users on requests.userid = users.id order by requests.id desc LIMIT 10") or sqlerr(__FILE__,__LINE__);
$num = mysql_num_rows($res);
print("<table border=1 width=100% cellspacing=0 cellpadding=5><tr><td class=colhead align=left width=50>".str32."</td>".
"<td class=colhead align=left width=425>".str33."</td><td class=colhead align=center>".str34."</td>".
"<td class=colhead align=center width=125>".str35."</td></tr>\n");
for ($i = 0; $i < $num; ++$i)
{ 
//=======change colors
		if($count == 0)
{
$count = $count+1;
$class = "clearalt6";
}
else
{
$count = 0;
$class = "clearalt7";
}
		//=======end
 $arr = mysql_fetch_assoc($res);
 {
$addedby = "<td style='padding: 0px' align=center class=$class><b><a href=userdetails.php?id=$arr[userid]>$arr[username]</a></b></td>";
 }
 print("<tr><td align=center class=$class><img src=pic//$arr[image]></td><td align=left class=$class><a href=viewrequests.php?id=$arr[id]&req_details=1><b>$arr[request]</b></a></td>" .
 "<td align=center class=$class>$arr[added]</td>".
   "$addedby</tr>\n");
}
print("<tr><td align=center colspan=4 class=clearalt6><form method=\"get\" action=viewrequests.php>".
"<input class=button type=\"submit\" value=\"".str36."\" class=btn /></form></td></tr></table>\n");
echo'</table>';
stdfoot();
die;
}
//=== end requests
//=== take new request 
if ($_GET["new_request"]){	
$new_request = 0 + $_GET["new_request"];
#int_check($new_request);
if($new_request != '1')
stderr(str2, str3);
$userid = 0 + $CURUSER["id"];
#int_check($userid);
if (ereg("^[0-9]+$", !$userid))
stderr(str2, str3);
$request = htmlentities($_POST["requesttitle"]);
if ($request == "")
 bark(str37);	
$cat = (0 + $_POST["category"]);
#int_check(array($cat,$_POST['category']));
if (!is_valid_id($cat))
 bark(str38);	   
$descrmain = unesc($_POST["body"]);
if (!$descrmain)
 bark(str39);	
if (!empty($_POST['picture'])){
$picture = unesc($_POST["picture"]);
if(!preg_match("/^http:\/\/[^\s'\"<>]+\.(jpg|gif|png)$/i", $picture))
stderr(str2, str40);
$pic = "[img]".$picture."[/img]\n";
}
$descr = "$pic";
$descr .= "$descrmain";
$userid = sqlesc($userid);
$request2 = sqlesc($request);
$descr = sqlesc($descr);
$cat = sqlesc($cat);
mysql_query("INSERT INTO requests (hits,userid, cat, request, descr, added) VALUES(1,$CURUSER[id], $cat, $request2, $descr, '" . get_date_time() . "')") or sqlerr(__FILE__,__LINE__);
$id = mysql_insert_id();
@mysql_query("INSERT INTO addedrequests VALUES(0, $id, $CURUSER[id])") or sqlerr(__FILE__,__LINE__);
//===add karma 	 //===  uncomment using karma mod
//mysql_query("UPDATE users SET seedbonus = seedbonus-5.0 WHERE id = $CURUSER[id]") or sqlerr(__FILE__, __LINE__);
//===end	
write_log("Request ($request) was added to the Request section by $CURUSER[username]");
if(duty('requests'))
add_shout("Request [url=viewrequests.php?id=$id&req_details=1][b]$request [/b][/url] was added by $CURUSER[username]");
redirect("viewrequests.php?id=$id&req_details=1","Your Request was added",'Request Added!');
} 
//===end take new request 
//=== request details 
if ($_GET["req_details"]){
$req_details = 0 + $_GET["req_details"];
#int_check($req_details);
if($req_details != '1')
stderr(str2, str3);
$id = 0+$_GET["id"]; 
stdhead(str41);
$res = mysql_query("SELECT * FROM requests WHERE id = $id") or sqlerr(__FILE__, __LINE__);
$num = mysql_fetch_array($res);	 
//$timezone = display_date_time($num["utadded"] , $CURUSER[tzoffset] );	 //=== use this line if you have the timezone mod
$timezone = $num["added"];	
$s = $num["request"];
begin_frame(str42." $s",true,'10','100%');
print("<table width=\"80%\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\"><tr><td align=center colspan=2 class=colhead>".
"<font size=\"+2\"><b>$s</b></font></td></tr>");
if ($num["descr"]){
$req_bb = format_comment($num["descr"]);
print("<tr><td align=left colspan=2 class=clearalt7 valign=top>$req_bb</td></tr>");
}
print("<tr><td align=right class=clearalt6><b>".str43."</b></td><td align=left class=clearalt6>$timezone</td></tr>");
$cres = mysql_query("SELECT username FROM users WHERE id=$num[userid]");
   if (mysql_num_rows($cres) == 1)
   {
     $carr = mysql_fetch_assoc($cres);
     $username = "$carr[username]";
   }
if ($CURUSER[id] == $num[userid] || get_user_class() >= UC_MODERATOR){
$edit = "[ <a class=altlink href=". $_SERVER[PHP_SELF] ."?id=$id&edit_request=1>".str44."</a> ]";
$delete = "[ <a class=altlink href=". $_SERVER[PHP_SELF] ."?id=$id&del_req=1&sure=0>".str45."</a> ]"; 
if ($num["filled"] == yes)
$reset = "[ <a class=altlink href=". $_SERVER[PHP_SELF] ."?id=$id&req_reset=1>".str46."</a> ]";
} 
print("<tr><td align=right class=clearalt6><b>".str47."</b></td><td align=left class=clearalt6>".
"<a class=altlink href=userdetails.php?id=$num[userid]>$username</a>  $edit  $delete </td></tr><tr><td align=right class=clearalt6>".
"<b>".str48."</b></td><td align=left class=clearalt6><a href=". $_SERVER[PHP_SELF] ."?id=$id&req_vote=1><b>".str49."</b></a>".
"</td></tr><tr><td align=right class=clearalt6><b>".str50."</b></td><td align=left class=clearalt6>".
"<form action=report.php?reportrequestid=$id method=\"post\"> ".str51." ".
"<input class=btn type=submit name=submit value=\"".str52."\"></form></td></tr>"); 
if ($num["filled"] == no)
{
print("<form method=post action=". $_SERVER[PHP_SELF] ."?requestid=$id&req_filled=1><tr><td align=right class=clearalt6 valign=top><b>".str53."</b></td>".
"<td class=clearalt6><input type=text size=80 name=filledurl value=''><br>".
str55." $BASEURL/details.php?id=<b>id</b> <br>[ ".str54." ]</td>".
"</tr></table><input type=submit value=\"".str56."\" class=btn></form>\n");
}
if ($num["filled"] == yes)
print("<tr><td align=right class=clearalt6 valign=top><b>".str57."</b></td><td class=clearalt6><a class=altlink href=$num[filledurl]><b>$num[filledurl]</b></a></td></tr></table>");	
//--- added comments

print("<tr><td class=embedded colspan=2><p><a name=startcomments></a></p>\n");
       $commentbar = "<p align=center><a class=index href=reqcomment.php?action=add&amp;tid=$id>".str65."</a></p>\n";
       $subres = mysql_query("SELECT COUNT(*) FROM comments WHERE request = $id");
       $subrow = mysql_fetch_array($subres);
       $count = $subrow[0];
print("</td></tr></table>"); 
if (!$count)
print("<h2>".str66."</h2>\n");
else {
 list($pagertop, $pagerbottom, $limit) = pager(20, $count, "viewrequests.php?id=$id&req_details=1&", array(lastpagedefault => 1));
$subres = mysql_query("SELECT comments.id, text, user, comments.added, editedby, editedat, avatar, warned, ".
                 "username, title, class, donor FROM comments LEFT JOIN users ON comments.user = users.id WHERE request = " .
                 "$id ORDER BY comments.id $limit") or sqlerr(__FILE__, __LINE__);
 $allrows = array();
 while ($subrow = mysql_fetch_array($subres))
         $allrows[] = $subrow;
 print($commentbar);
 print($pagertop);
 reqcommenttable($allrows);
 print($pagerbottom);
}
 print($commentbar); 
end_frame(); 
die;  
}
//=== end request details 
//=== added edit request
if ($_GET["edit_request"]) {
$edit_request = 0 + $_GET["edit_request"];
#int_check($edit_request);
if($edit_request != '1')
stderr(str2, str3);
$id = 0+$_GET["id"]; 
$res = mysql_query("SELECT * FROM requests WHERE id = $id") or sqlerr(__FILE__, __LINE__);
$num = mysql_fetch_array($res);	
//$timezone = display_date_time($num["utadded"] , $CURUSER[tzoffset] );	 //=== use this line if you have timezone mod
$timezone = $num["added"];
$s = $num["request"];
$id2 = $num["cat"];
if ($CURUSER["id"] != $num["userid"] && get_user_class() < UC_MODERATOR)
stderr(str2, str67);
$request = sqlesc($s);
$body = htmlspecialchars(unesc($num["descr"])); 
$res2 = mysql_query("SELECT name FROM categories WHERE id=$id2")or sqlerr(__FILE__, __LINE__);
$num2 = mysql_fetch_array($res2);
$name = $num2["name"];
$s2 = "<select name=\"category\"><option value=$id2> $name </option>\n";
$cats = genrelist();
foreach ($cats as $row)
$s2 .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";
$s2 .= "</select>\n";	
stdhead(str68);
print("<form method=post name=compose action=". $_SERVER[PHP_SELF] ."?id=$id&take_req_edit=1><a name=add id=add></a>".
"<table border=1 width=100% cellspacing=0 cellpadding=5><tr><td class=colhead align=left colspan=2><h1>".str69." ".
"<img src=pic//arrow_next.gif alt=\":\"> $s</h1></td><tr><tr><td align=right class=clearalt6><b>".str70."</b></td>".
"<td align=left class=clearalt6><input type=text size=40 name=requesttitle value=$request><b> ".str71."</b> $s2<br><tr>".
"<td align=right class=clearalt6 valign=top><b>".str72.":</b></td><td align=left class=clearalt6>".
"<input type=text name=picture size=80 value=''><br>(".str73.")".
"<tr><td align=right class=clearalt6><b>".str74."</b></td><td align=left class=clearalt6>\n");
textbbcode("compose","body","$body");
print("</td></tr>\n"); 
//=== if staff 
if (get_user_class() >= UC_MODERATOR){
print("<tr><td class=colhead align=left colspan=2>".str75.":</td></tr><tr><td align=right class=clearalt6><b>".str76.":</b>".
"</td><td class=clearalt6><input type=checkbox name=filled" . ($num[filled]  == "yes" ? " checked" : "") . "></td></tr><tr>".
"<td align=right class=clearalt6><b>".str77."</b></td><td class=clearalt6>".
"<input type=text size=40 value=$num[filledby] name=filledby></td></tr><tr><td align=right class=clearalt6>".
"<b>".str78."</b></td><td class=clearalt6><input type=text size=80 name=filledurl value=$num[filledurl]></td></tr>");
}
//===end  if staff
print("<tr><td align=center  class=clearalt6 colspan=2><input type=submit value='".str79."' class=btn></td></tr></form><br><br></table><br>\n"); 
stdfoot(); 
die;
}  
//===end added edit request	
//==== take req edit
if ($_GET["take_req_edit"]){
$take_req_edit = 0 + $_GET["take_req_edit"];
#int_check($take_req_edit);
if($take_req_edit != '1')
stderr(str2, str3);
$id = 0 + $_GET["id"];
#int_check($id); 
$res = mysql_query("SELECT userid FROM requests WHERE id = $id") or sqlerr(__FILE__, __LINE__);
$num = mysql_fetch_array($res);
if ($CURUSER["id"] != $num["userid"] && get_user_class() < UC_MODERATOR)
stderr(str2, str80);
$request = htmlentities($_POST["requesttitle"]);
if (!empty($_POST['picture'])){
$picture = unesc($_POST["picture"]);
if(!preg_match("/^http:\/\/[^\s'\"<>]+\.(jpg|gif|png)$/i", $picture))
stderr(str2, str81);
$pic = "[img]".$picture."[/img]\n";
}
$descr = "$pic";
$descr .= unesc($_POST["body"]);
if (!$descr)
  bark(str82);
$cat = (0 + $_POST["category"]);
#int_check($cat);
if (!is_valid_id($cat))
	bark(str83);
$request = sqlesc($request);
$descr = sqlesc($descr);
$cat = sqlesc($cat);
$filledby = htmlentities( 0 + $_POST["filledby"]);
#int_check($filledby);
$filled = $_POST["filled"];
if ($filled)
{
if (!is_valid_id($filledby))
	bark(str84);
$res = mysql_query("SELECT id FROM users WHERE id=".$filledby."");
if (mysql_num_rows($res) == 0)
       bark(str85);
       int_check($_POST['filledurl'],1,1,1,1);
$filledurl = htmlentities("$BASEURL/details.php?id=".$_POST['filledurl']);	
if(!preg_match("#^".preg_quote("$BASEURL/details.php?id=")."([0-9]{1,6})$#", $filledurl))
stderr(str2, sprintf(str86,$BASEURL),false); 
if (!$filledurl)
	bark(str87);
mysql_query("UPDATE requests SET cat=$cat, request=$request, descr=$descr, filledby=$filledby, filled ='yes', filledurl='$filledurl' WHERE id = $id") or sqlerr(__FILE__,__LINE__);
}
else
mysql_query("UPDATE requests SET cat=$cat, filledby = 0, request=$request, descr=$descr, filled = 'no' WHERE id = $id") or sqlerr(__FILE__,__LINE__);
header("Refresh: 0; url=viewrequests.php?id=$id&req_details=1");
}
//=== end take req edit	
//=== request filled 
if ($_GET["req_filled"]){ 
$req_filled = 0 + $_GET["req_filled"];
#int_check($req_filled);
if($req_filled != '1')
stderr(str2, str3);	 
if ($_GET["requestid"]){
$requestid = 0 + htmlentities($_GET["requestid"]);
#int_check($requestid); 
if (ereg("^[0-9]+$", !$requestid))
stderr(str2, str3);  
}
int_check($_POST['filledurl'],1,1,1,1);
$filledurl = htmlentities("$BASEURL/details.php?id=".$_POST['filledurl']);	
if(!preg_match("#^".preg_quote("$BASEURL/details.php?id=")."([0-9]{1,6})$#", $filledurl))
stderr(str2,sprintf(str86,$BASEURL) ,false);
stdhead(str88);
begin_main_frame('100%');
$res = mysql_query("SELECT users.username, requests.userid, requests.filled, requests.request FROM requests inner join users on requests.userid = users.id where requests.id = $requestid") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_assoc($res);
$res2 = mysql_query("SELECT username FROM users where id =" . $CURUSER[id]) or sqlerr(__FILE__, __LINE__);
$arr2 = mysql_fetch_assoc($res2);
if ($arr['filled']==no){
$msg = sprintf(str89,$arr[request],$arr2[username],$filledurl,$filledurl,"$BASEURL/viewrequests.php?id=$requestid&req_reset=1");
mysql_query ("UPDATE requests SET filled = 'Yes', filledurl = '$filledurl', filledby = $CURUSER[id] WHERE id = $requestid") or sqlerr(__FILE__, __LINE__);
//=== remove the next query if you DON'T have subject in your PM system and use the other one
mysql_query("INSERT INTO messages (poster, sender, receiver, added, msg, subject, location) VALUES(0, 0, $arr[userid], '" . get_date_time() . "', " . sqlesc($msg) . ", 'Request Filled', 1)") or sqlerr(__FILE__, __LINE__);
//===notify people who voted on request thanks CoLdFuSiOn :)
$res = mysql_query("SELECT `userid` FROM `addedrequests` WHERE `requestid` = $requestid AND userid != $arr[userid]") or sqlerr(__FILE__, __LINE__);
$pn_msg = sprintf(str90,$arr[request],$arr2[username],$filledurl,$filledurl);
$some_variable = '';
while($row = mysql_fetch_assoc($res)) {
//=== use this if you DO have subject in your PMs 
$some_variable .= "(0, 0, 'Request " . $arr[request] . " was just uploaded', $row[userid], '" . get_date_time() . "', '" . $pn_msg . "')";
//=== use this if you DO NOT have subject in your PMs 
//$some_variable .= "(0, 0, $row[userid], '" . get_date_time() . "', " . sqlesc($pn_msg) . ")";
}
//=== use this if you DO have subject in your PMs 
mysql_query("INSERT INTO messages (poster, sender, subject, receiver, added, msg) VALUES ".$some_variable."");
print("<table width=100%><tr><td class=colhead align=left><h1>Succex!</h1></td></tr><tr><td class=clearalt6 align=left>".
sprintf(str91,$requestid,$filledurl,$filledurl).
sprintf(str92,$arr[userid],$arr[username],$_SERVER[PHP_SELF],$requestid).
"</td></tr></table>");
}
else
{
print("<table width=100%><tr><td class=colhead align=left><h1>Succex!</h1></td></tr><tr><td class=clearalt6 align=left>".
sprintf(str91,$requestid,$filledurl,$filledurl).
sprintf(str92,$arr[userid],$arr[username],$_SERVER[PHP_SELF],$requestid).
"</td></tr></table>");
}
end_main_frame();
stdfoot();
die; 
}
//===end req filled	
//=== request reset
if ($_GET["req_reset"]){ 
$req_reset = 0 + $_GET["req_reset"];
#int_check($req_reset);
if($req_reset != '1')
stderr(str2, str3);
$requestid = htmlentities($_GET["id"]);
$requestid = 0 + $requestid;
#int_check($requestid); 
stdhead(str93);
begin_main_frame('100%');
$res = mysql_query("SELECT userid, filledby,filled FROM requests WHERE id =$requestid") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_assoc($res);
if (($CURUSER[id] == $arr[userid]) || (get_user_class() >= UC_MODERATOR) || ($CURUSER[id] == $arr[filledby]))
{
//===remove karma remove if not using karma system
 if ($arr['filled']=='yes')
 mysql_query("UPDATE users SET seedbonus = seedbonus-10.0 WHERE id = $arr[filledby]") or sqlerr(__FILE__, __LINE__);
 //===end
 @mysql_query("UPDATE requests SET filled='no', filledurl='', filledby='0' WHERE id =$requestid") or sqlerr(__FILE__, __LINE__);
print("<table width=100%><tr><td class=colhead align=left><h1>".str94."</h1></td></tr>".
"<tr><td class=clearalt6 align=left>".sprintf(str95,$requestid)."<br><br></td></tr></table>");
}
else{
print("<table width=100%><tr><td class=colhead align=left><h1>Error!</h1></td></tr><tr><td class=clearalt6 align=left>".
str96."<br><br></td></tr></table>");
}
end_main_frame(); 
stdfoot(); 
die;
}
//===end request reset
//=== vote for request
if ($_GET["req_vote"]){ 
$req_vote = 0 + $_GET["req_vote"];
#int_check($req_vote);
if($req_vote != '1')
stderr(str2, str3);
$requestid = 0 + $_GET["id"];
#int_check($requestid);
$userid = 0 + $CURUSER["id"];
#int_check($userid);
if (!is_valid_id($userid))
stderr(str2, str3); 
stdhead(str97);
$res = mysql_query("SELECT * FROM addedrequests WHERE requestid=$requestid and userid = $userid") or sqlerr(__FILE__,__LINE__);
$arr = mysql_fetch_assoc($res);
$voted = $arr;
if ($voted) {
print("<table width=100%><tr><td class=colhead align=left><h1>".str98."</h1></td></tr><tr><td class=clearalt6 align=left>".
"<p>".str99."</p>");
}
else
{ 
mysql_query("UPDATE requests SET hits = hits + 1 WHERE id=$requestid") or sqlerr(__FILE__,__LINE__);
@mysql_query("INSERT INTO addedrequests VALUES(0, $requestid, $userid)") or sqlerr(__FILE__,__LINE__);
print("<table width=100%><tr><td class=colhead align=left><h1>".str100."</h1></td></tr><tr><td class=clearalt6 align=left>".
"<p>".sprintf(str101,$requestid)."</p><p>".str102." <a class=altlink href=viewrequests.php?id=$requestid&req_details=1>".
"<b>".str103."</b></a></p><br><br></td></tr></table>");
}  
stdfoot(); 
die;
}
//=== end vote for request
//===  votes_view	
if ($_GET["votes_view"]){
$votes_view = 0 + $_GET["votes_view"];
#int_check($votes_view);
if($votes_view != '1')
stderr(str2, str3);
$requestid = 0 + $_GET["requestid"];
#int_check($requestid); 
if (!is_valid_id($requestid))
stderr(str2, str3);
$res2 = mysql_query("select count(addedrequests.id) from addedrequests inner join users on addedrequests.userid = users.id inner join requests on addedrequests.requestid = requests.id WHERE addedrequests.requestid =$requestid") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res2);
$count = $row[0];
$perpage = 25;
 list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] ."?" );
$res = mysql_query("select users.id as userid,users.username, users.downloaded,users.uploaded, requests.id as requestid, requests.request from addedrequests inner join users on addedrequests.userid = users.id inner join requests on addedrequests.requestid = requests.id WHERE addedrequests.requestid =$requestid $limit") or sqlerr(__FILE__,__LINE__);
stdhead("Voters");
$res2 = mysql_query("select request from requests where id=$requestid");
$arr2 = mysql_fetch_assoc($res2);
print("<h2>".str104." <a class=altlink href=viewrequests.php?id=$requestid&req_details=1><b>$arr2[request]</b></a></h2>");
print("<p class=success>".str105." <a class=altlink href=viewrequests.php?id=$requestid&req_vote=1><b>".str106."</b></a></p>");
if (mysql_num_rows($res) == 0)
 print("<p align=center class=error><b>".str107."</b></p>\n");
else
{
 print("<table border=1 cellspacing=0 cellpadding=5 width=100%>\n");
 print("<tr><td class=colhead>".str108."</td><td class=colhead align=left>".str109."</td><td class=colhead align=left>".str110."</td>".
   "<td class=colhead align=left>".str111."</td>\n");
 while ($arr = mysql_fetch_assoc($res))
 {
//=======change colors
		if($count2 == 0)
{
$count2 = $count2+1;
$class = "clearalt6";
}
else
{
$count2 = 0;
$class = "clearalt7";
}
if ($arr["downloaded"] > 0)
{
       $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
       $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
    }
    else
       if ($arr["uploaded"] > 0)
         $ratio = "Inf.";
 else
  $ratio = "---";
$uploaded =mksize($arr["uploaded"]);
$joindate = "$arr[added] (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"])) . ")";
$downloaded = mksize($arr["downloaded"]);
if ($arr["enabled"] == 'no')
 $enabled = "<font color = red>".str112."</font>";
else
 $enabled = "<font color = green>".str113."</font>";
 print("<tr><td class=$class><a href=userdetails.php?id=$arr[userid]><b>$arr[username]</b></a></td><td align=left class=$class>$uploaded</td><td align=left class=$class>$downloaded</td><td align=left class=$class>$ratio</td></tr>\n");
 }
 print("</table>\n");
}
echo $pagerbottom;	
stdfoot();
die;
}
//===end votes_view	
//=== delete request user / staff
if ($_GET["del_req"]){
$del_req = 0 + $_GET["del_req"];
#int_check($del_req);
if($del_req != '1')
stderr(str2, str3);
$requestid = 0 + $_GET["id"];
#int_check($requestid);
$userid = 0 + $CURUSER["id"];
#int_check($userid);
if (!is_valid_id($userid))
stderr(str2, str3);
$res = mysql_query("SELECT * FROM requests WHERE id = $requestid") or sqlerr(__FILE__, __LINE__);
$num = mysql_fetch_array($res);
if ($userid != $num["userid"] && get_user_class() < UC_MODERATOR)
stderr(str2, str114);	
$sure = 0 + $_GET["sure"];
#int_check($sure);
 if ($sure == 0)
 stderr(str115, sprintf(str116,$_SERVER[PHP_SELF],$requestid),false);
elseif ($sure == 1){
mysql_query("DELETE FROM requests WHERE id=$requestid") or sqlerr(__FILE__,__LINE__);
mysql_query("DELETE FROM addedrequests WHERE requestid = $requestid") or sqlerr(__FILE__,__LINE__);
mysql_query("DELETE FROM comments WHERE request=$requestid") or sqlerr(__FILE__,__LINE__);
write_log("Request: $request ($num[request]) was deleted from the Request section by $CURUSER[username]");
header("Refresh: 0; url=viewrequests.php");
}
else
stderr(str2, str3);
}
//===end delete request user / staff 
//=== delete multi requests for staff
if ($_GET["staff_delete"]){
$staff_delete = 0 + $_GET["staff_delete"];
#int_check($staff_delete);
if($staff_delete != '1')
stderr(str2, str3);
if (get_user_class() >= UC_MODERATOR)
{
if (empty($_POST["delreq"]))
   bark(str117);
$do="DELETE FROM requests WHERE id IN (" . implode(", ", $_POST[delreq]) . ")";
$do2="DELETE FROM addedrequests WHERE requestid IN (" . implode(", ", $_POST[delreq]) . ")";
$do3="DELETE FROM comments WHERE request IN (" . implode(", ", $_POST[delreq]) . ")";
$res=mysql_query($do);
$res2=mysql_query($do2); 
$res3=mysql_query($do3);
}
else
{ 
bark(str118);}
header("Refresh: 0; url=viewrequests.php");
}
// end delete multi requests
//=== prolly not needed, but what the hell... basically stopping the page getting screwed up
if ($_GET["sort"]){
$sort = $_GET["sort"];
if($sort == 'votes' || $sort == 'cat' || $sort == 'request' || $sort == 'added')
$sort = $_GET["sort"]; 
else
stderr(str2, str3);  
}
if ($_GET["filter"]){
$sort = $_GET["filter"];
if($sort == 'true' || $sort == 'false')
$sort = $_GET["filter"]; 
else
stderr(str2, str3);
}
//=== end of prolly not needed, but what the hell :P
stdhead(str6);
begin_main_frame('100%');
print("<div align=center><table border=1 width=100% cellspacing=0 cellpadding=5><tr><td class=colhead align=center><font size=4>".str119."</font>\n</td></tr>".
"<tr><td align=center><p><a class=altlink href=". $_SERVER[PHP_SELF] ."?add_request=1>".str120."</a>&nbsp;&nbsp;<a class=altlink href=viewrequests.php?requestorid=$CURUSER[id]>".str121."</a></p>".
"<p><a class=altlink href=". $_SERVER[PHP_SELF] ."?category=" . $_GET[category] . "&sort=" . $_GET[sort] . "&filter=true>".str122."</a>");
//==== for mods only to make deleting filled requests simple... yeah, I'm lazy :P
if (get_user_class() >= UC_MODERATOR)
print(" - <a class=altlink href=". $_SERVER[PHP_SELF] ."?category=" . $_GET[category] . "&sort=" . $_GET[sort] . "&filter=false>Only Filled</a>");
print("</p><p>".str123."</p>");
$search = $_GET["search"];
$search = " AND requests.request like ".sqlesc('%'.$search.'%');
if ($sort == "votes")			  
$sort = " ORDER BY hits DESC";
elseif ($sort == "cat")
$sort = " ORDER BY cat ";
else if ($sort == "request")
$sort = " ORDER BY request ";
else if ($sort == "added")
$sort = " ORDER BY added ASC";
else
$sort = " ORDER BY added DESC";
if ($filter == "true")
$filter = " AND requests.filledby = '0' ";
elseif ($filter == "false")
$filter = " AND requests.filled = 'yes' ";
else
$filter = "";
if ($requestorid <> NULL)
       {
       if (($categ <> NULL) && ($categ <> 0))
 $categ = "WHERE requests.cat = " . $categ . " AND requests.userid = " . $requestorid;
       else
 $categ = "WHERE requests.userid = " . $requestorid;
       }
else if ($categ == 0)
       $categ = '';
else
       $categ = "WHERE requests.cat = " . $categ;
$res = mysql_query("SELECT count(requests.id) FROM requests inner join categories on requests.cat = categories.id inner join users on requests.userid = users.id  $categ $filter $search") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);
$count = $row[0];
$perpage = 25;
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] ."?" . "category=" . $_GET[category] . "&sort=" . $_GET["sort"] . "&" );
print("<center>");
$res = mysql_query("SELECT users.downloaded, users.uploaded, users.username, requests.filled, requests.filledby, requests.id, requests.userid, requests.request, requests.added, requests.hits, requests.filledurl, categories.image, categories.name as cat FROM requests inner join categories on requests.cat = categories.id inner join users on requests.userid = users.id  $categ $filter $search $sort $limit") or sqlerr(__FILE__, __LINE__);
$num = mysql_num_rows($res);
print("<div align=center><form method=get action=viewrequests.php><select name=category><option value=0>".str124."</option>");
$cats = genrelist();
$catdropdown = "";
foreach ($cats as $cat) {
   $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
   $catdropdown .= ">" . htmlspecialchars($cat["name"]) . "</option>\n";
}
print("$catdropdown</select><input type=submit align=center value=\"view only selected\" class=btn>\n");
print("</form><br><form method=get action=viewrequests.php><b>".str125." </b><input type=text id=specialboxg name=search>".
"<input class=btn type=submit align=center value=".str126."></form></td></tr></table><br /><br>");
?>
<script language = "Javascript">
<!-- 
var form='viewreq'
function SetChecked(val,chkName) {
dml=document.forms[form];
len = dml.elements.length;
var i=0;
for( i=0 ; i<len ; i++) {
if (dml.elements[i].name==chkName) {
dml.elements[i].checked=val;
}
}
}
// -->
</script>
<?php
print("<form method=post name=viewreq action=viewrequests.php?staff_delete=1 onSubmit=\"return ValidateForm(this,'delreq')\">".
"<table border=1 width=100% cellspacing=0 cellpadding=5><tr><td class=colhead align=left width=50><a class=altlink href=". $_SERVER[PHP_SELF] ."?category=" . $_GET[category] . "&filter=" . $_GET[filter] . "&sort=cat>Type</a></td>".
"<td class=colhead align=center><a class=altlink href=". $_SERVER[PHP_SELF] ."?category=" . $_GET[category] . "&filter=" . $_GET[filter] . "&sort=request>".str127."</a></td>".
"<td class=colhead align=center width=150><a class=altlink href=" . $_SERVER[PHP_SELF] ."?category=" . $_GET[category] . "&filter=" . $_GET[filter] . "&sort=added>".str128."</a></td>".
"<td class=colhead align=center>".str129."</td><td class=colhead align=center>".str130."</td><td class=colhead align=center>".str131."</td>".
"<td class=colhead align=center><a class=altlink href=" . $_SERVER[PHP_SELF] . "?category=" . $_GET[category] . "&filter=" . $_GET[filter] . "&sort=votes>".str132."</a></td>");
if (get_user_class() >= UC_MODERATOR)
print("<td class=colhead align=center>".str133."</td>");
print("</tr>\n");
if (!$num)
	echo '<tr><td colspan='.(get_user_class() >= UC_MODERATOR ? '8' : '7').' align=center><b>'.str134.'</b></td></tr>';
for ($i = 0; $i < $num; ++$i)
{
//=======change colors
		if($count2 == 0)
{
$count2 = $count2+1;
$class = "clearalt6";
}
else
{
$count2 = 0;
$class = "clearalt7";
}
$arr = mysql_fetch_assoc($res);
if ($arr["downloaded"] > 0)
   {
     $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 2);
     $ratio = "<font color=" . get_ratio_color($ratio) . "><b>$ratio</b></font>";
   }
   else if ($arr["uploaded"] > 0)
       $ratio = "Inf.";
   else
       $ratio = "---";
$res2 = mysql_query("SELECT username from users where id=" . $arr[filledby]);
$arr2 = mysql_fetch_assoc($res2);  
if ($arr2[username])
       $filledby = $arr2[username];
else
       $filledby = " ";      
$addedby = "<td  class=$class align=center><a href=userdetails.php?id=$arr[userid]><b>$arr[username] ($ratio)</b></a></td>";
$filled = $arr[filled];
if ($filled =="yes")
       $filled = "<a href=$arr[filledurl]><font color=green><b>".str113."</b></font></a>";
else
       $filled = "<a href=viewrequests.php?id=$arr[id]&req_details=1><font color=red><b>".str112."</b></font></a>";
 print("<tr><td align=center class=$class><img src=pic//$arr[image]></td>" .
 "<td align=left class=$class><a href=". $_SERVER[PHP_SELF] ."?id=$arr[id]&req_details=1><b>$arr[request]</b></a></td>".
 "<td align=center class=$class>$arr[added]</td>$addedby<td class=$class>$filled</td>".
 "<td class=$class><a href=userdetails.php?id=$arr[filledby]><b>$arr2[username]</b></a></td>".
 "<td class=$class><a href=viewrequests.php?requestid=$arr[id]&votes_view=1><b>$arr[hits]</b></a></td>");
 if (get_user_class() >= UC_MODERATOR)
 print("<td class=$class><input type=checkbox name=\"delreq[]\" value=\"" . $arr[id] . "\" /></td>");
 print("</tr>\n");
}
if (get_user_class() >= UC_MODERATOR)
print("<tr><td class=colhead colspan=8 align=right><a class=altlink href=\"javascript:SetChecked(1,'delreq[]')\">".
"select all</a> - <a class=altlink href=\"javascript:SetChecked(0,'delreq[]')\">un-select all</a>".
" <input type=submit value=\"".str135."\" class=btn></td></tr>");
print("</table>\n");
echo $pagerbottom;	
print("</center>");
end_main_frame();
stdfoot();
die;
?>