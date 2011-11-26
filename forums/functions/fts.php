<?php
  function forum_stats () {
	  global $showforumstats,$pic_base_url;
  if ($showforumstats == "yes") {
$forum_t = gmtime() - 180; //you can change this value to whatever span you want
$forum_t = sqlesc(get_date_time($forum_t));
$res = sql_query("SELECT id, username, class, warned, donor FROM users WHERE forum_access >= $forum_t ORDER BY forum_access DESC") or print(mysql_error());
while ($arr = mysql_fetch_assoc($res))
{
if ($forumusers) $forumusers .= ",\n";
$arr['username'] = get_style($arr['class'],$arr['username']);
$donator = $arr["donor"] === "yes";
if ($donator)
$forumusers .= "<nobr>";
$warned = $arr["warned"] === "yes";
if ($donator)
$forumusers .= "<nobr>";
if ($CURUSER)
$forumusers .= "<a href=userdetails.php?id={$arr["id"]}><b>{$arr["username"]}</b></a>";
else
$forumusers .= "<b>{$arr["username"]}</b>";
if ($donator)
$forumusers .= "<img src=".$rootpath."{$pic_base_url}star.png alt='Donated {$$arr["donor"]}'></nobr>";
if ($warned)
$forumusers .= "<img src=".$rootpath."{$pic_base_url}warning.png alt='Warned {$$arr["warned"]}'></nobr>";
}
$forumusers = explode(',',$forumusers);
if (!$forumusers)
$forumusers = "There have been no active users in the last 15 minutes.";
collapses('ftsstatus',"<font color=white><b>$SITENAME Forum Stats</b></font>",'100',0,'class=thead','class=tcat');
?>
<table width=100% border=0 cellspacing=0 cellpadding=5><tr>
<td class="colhead" align="left">Active Forum Users</td></tr>
</tr><td class=text>
<center><?insert_legend(0)?></center>
<hr/>
<?php
foreach($forumusers as $fts) {
	$perrow = 17 ;
	echo $fts;
	print ( ($i && $i % $perrow == 0) ? "<br>" : "" ) ;
	$i++;
}
?>
</td></tr></table>
<?php
$q1 = mysql_query("SELECT birthday FROM users WHERE id = 1");
$q = mysql_fetch_assoc($q1);
$a = $q['birthday'];
$aa = @ereg_replace("\d{1,4}","",$a);
print("<table width=100% border=1 cellspacing=0 cellpadding=5>\n");

print("<tr><td class=colhead>Stats</td></tr>\n");

$registered = number_format(get_row_count("users", "WHERE enabled = 'yes'"));
$donated = number_format(get_row_count("users", "WHERE donated > '0'"));

$a = @mysql_fetch_assoc(@sql_query("SELECT id,username FROM users WHERE status='confirmed' ORDER BY id DESC LIMIT 1")) or die(mysql_error());
if ($CURUSER)
$latestuser = "<a href=userdetails.php?id=" . $a["id"] . ">" . $a["username"] . "</a>";
else
$latestuser = $a['username'];

$forumusers = number_format(get_row_count("users", "WHERE UNIX_TIMESTAMP(" . get_dt_num() . ") - UNIX_TIMESTAMP(forum_access) < 1200"));

$topiccount = sql_query("select sum(topiccount) as topiccount from forums");
$row1 = mysql_fetch_array($topiccount);
$topiccount = $row1[topiccount];

$postcount = sql_query("select sum(postcount) as postcount from forums");
$row2 = mysql_fetch_array($postcount);
$postcount = $row2[postcount];
$today = date("m-d");
print("<tr><td align=left>
&raquo;&nbsp;Our members have made <b><font color=#0000FF>" . $postcount . "</font></b> posts in <b><font color=#0000FF>" . $topiccount . "</font></b> topics,<BR>
&raquo;&nbsp;We have <b><font color=#0000FF>" . $registered . "</font></b> users,<BR>
&raquo;&nbsp;We have <b><font color=#0000FF>" . $donated . "</font></b> donors,<BR>
&raquo;&nbsp;Our newest member is <b>" . $latestuser . "</b>, <BR>
&raquo;&nbsp;<b><font color=#0000FF>" . $forumusers . "</font></b> online users in forum now.<BR></td></tr>\n");

print("</table>");
collapsee();
}
}

  function catch_up()
  {
	//die("This feature is currently unavailable.");
    global $CURUSER;

    $userid = 0+$CURUSER["id"];

    $res = sql_query("SELECT id, lastpost FROM topics") or sqlerr(__FILE__, __LINE__);

    while ($arr = mysql_fetch_assoc($res))
    {
      $topicid = 0+$arr["id"];

      $postid = $arr["lastpost"];

      $r = sql_query("SELECT id,lastpostread FROM readposts WHERE userid=$userid and topicid=$topicid") or sqlerr(__FILE__, __LINE__);

      if (mysql_num_rows($r) == 0)
        sql_query("INSERT INTO readposts (userid, topicid, lastpostread) VALUES($userid, $topicid, $postid)") or sqlerr(__FILE__, __LINE__);

      else
      {
        $a = mysql_fetch_assoc($r);

        if ($a["lastpostread"] < $postid)
          sql_query("UPDATE readposts SET lastpostread=$postid WHERE id=" . $a["id"]) or sqlerr(__FILE__, __LINE__);
      }
    }
    redirect('forums','All posts are now marked as read','OK');
  }

  //-------- Returns the minimum read/write class levels of a forum

  function get_forum_access_levels($forumid)
  {
    $res = sql_query("SELECT minclassread, minclasswrite, minclasscreate FROM forums WHERE id=".mysql_real_escape_string($forumid)) or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) != 1)
      return false;

    $arr = mysql_fetch_assoc($res);

    return array("read" => $arr["minclassread"], "write" => $arr["minclasswrite"], "create" => $arr["minclasscreate"]);
  }

  //-------- Returns the forum ID of a topic, or false on error

  function get_topic_forum($topicid)
  {
    $res = sql_query("SELECT forumid FROM topics WHERE id=".mysql_real_escape_string($topicid)) or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) != 1)
      return false;

    $arr = mysql_fetch_row($res);

    return $arr[0];
  }
  $forumc = 'show';

  //-------- Returns the ID of the last post of a forum



  function get_forum_last_post($forumid)
  {
    $res = sql_query("SELECT lastpost FROM topics WHERE forumid=".mysql_real_escape_string($forumid)." ORDER BY lastpost DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_row($res);

    $postid = $arr[0];

    if ($postid)
      return $postid;

    else
      return 0;
  }

  //-------- Inserts a quick jump menu

  function insert_quick_jump_menu($currentforum = 0)
  {
    print("<p align=center><form method=get action=viewforum.php name=jump>\n");


    print("Quick jump: ");

    print("<select name=forumid onchange=\"if(this.options[this.selectedIndex].value != -1){ forms['jump'].submit() }\">\n");

    $res = sql_query("SELECT * FROM forums ORDER BY name") or sqlerr(__FILE__, __LINE__);

    while ($arr = mysql_fetch_assoc($res))
    {
      if (get_user_class() >= $arr["minclassread"])
        print("<option value=" . $arr["id"] . ($currentforum == $arr["id"] ? " selected>" : ">") . $arr["name"] . "\n");
    }

    print("</select>\n");

    print("<input type=submit value='Go!' class='btn'>\n");

    print("</form>\n</p>");
  }

  //-------- Inserts a compose frame

  function insert_compose_frame($id, $newtopic = true, $quote = false)
  {
    global $maxsubjectlength, $CURUSER, $BASEURL;
?>
<!--     Preview forum post (ajaX) v0.2    !-->
<!--     DO NOT EDIT BELOW!                            !-->
<script type="text/javascript" language="javascript">
   var http_request = false;
   function makePOSTRequest(url, parameters) {
      http_request = false;
      if (window.XMLHttpRequest) { // Mozilla, Safari,...
         http_request = new XMLHttpRequest();
         if (http_request.overrideMimeType) {
             // set type accordingly to anticipated content type
            //http_request.overrideMimeType('text/xml');
            http_request.overrideMimeType('text/html');
         }
      } else if (window.ActiveXObject) { // IE
         try {
            http_request = new ActiveXObject("Msxml2.XMLHTTP");
         } catch (e) {
            try {
               http_request = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {}
         }
      }
      if (!http_request) {
         alert('Cannot create XMLHTTP instance');
         return false;
      }
      
      http_request.onreadystatechange = alertContents;
      http_request.open('POST', url, true);
      http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      http_request.setRequestHeader("Content-length", parameters.length);
      http_request.setRequestHeader("Connection", "close");
      http_request.send(parameters);
   }

   function alertContents() {
      if (http_request.readyState == 4) {
         if (http_request.status == 200) {
            //alert(http_request.responseText);
            result = http_request.responseText;
            document.getElementById('preview').innerHTML = result;            
         } else {
            alert('There was a problem with the request. Please report this to administrator.');
         }
      }
   }
  
   function get(obj) {
      var poststr = "body=" + encodeURI( document.getElementById("body").value );
      makePOSTRequest('preview.php', poststr);
   }
</script>
<!-- Preview forum post (ajaX) v0.2 !-->
<?

    if ($newtopic)
    {
      $res = sql_query("SELECT name FROM forums WHERE id=".mysql_real_escape_string($id)) or sqlerr(__FILE__, __LINE__);

      $arr = mysql_fetch_assoc($res) or die("Bad forum id");

      $forumname = $arr["name"];

      print("<p align=center>New topic in <a href=viewforum.php?forumid=$id>$forumname</a> forum</p>\n");
    }
    else
    {
      $res = sql_query("SELECT * FROM topics WHERE id=".mysql_real_escape_string($id)) or sqlerr(__FILE__, __LINE__);

      $arr = mysql_fetch_assoc($res) or stderr("Forum error", "Topic not found.");

      $subject = htmlspecialchars($arr["subject"]);

      print("<p align=center>Reply to topic: <a href=viewtopic.php?topicid=$id>$subject</a></p>");
    }

    print ("<span name=\"preview\" id=\"preview\"></span>");
    
    begin_frame("Compose", true);

    print("<form method=post name=\"compose\" action=post.php>\n");

    if ($newtopic)
      print("<input type=hidden name=forumid value=$id>\n");

    else
      print("<input type=hidden name=topicid value=$id>\n");

    begin_table();

    if ($newtopic)
      print("<tr><td class=rowhead>Subject</td>" .
        "<td align=left style='padding: 0px'><input type=text size=100 maxlength=$maxsubjectlength name=subject " .
        "style='border: 0px; height: 19px'></td></tr>\n");
    else
    print("<tr><td class=rowhead>Subject</td>" .
        "<td align=left style='padding: 0px'><input type=text size=100 maxlength=$maxsubjectlength name=subject " .
        "style='border: 0px; height: 19px' value=\"Re: $subject\"></td></tr>\n");

    if ($quote)
    {
       $postid = 0+$_GET["postid"];
       int_check($postid);

	   $res = sql_query("SELECT posts.*, users.username FROM posts JOIN users ON posts.userid = users.id WHERE posts.id=$postid") or sqlerr(__FILE__, __LINE__);

	   if (mysql_num_rows($res) != 1)
	     stderr("Error", "No post with this ID");

	   $arr = mysql_fetch_assoc($res);
    }

    print("<tr><td class=rowhead>Body</td><td align=left style='padding: 0px'>");
   textbbcode("compose","body",($quote?(("[quote=".htmlspecialchars($arr["username"])."]".htmlspecialchars(unesc($arr["body"]))."[/quote]")):""));
   if($newtopic):
   ?>
   			<tr>
				<td class="trow2" with="20%">
					<strong>Post Icons: </strong>
				</td>
				<td class="trow2">
					
		<div style="padding: 3px;">

			<table border="0" cellpadding="1" cellspacing="1" width="95%">
				<tbody>
					<tr>
						<td colspan="15" class="none"><div style="margin-bottom: 3px;"><b>You may choose an icon for your message from the following list:</b><hr></div></td>
					</tr>
					<tr>
		</tr><tr>
				<td class="none"><input name="iconid" value="16" type="radio"></td>

				<td width="12%" class="none"><img src="pic/icons/icon16.gif" border="0"></td>
				<td class="none"><input name="iconid" value="19" type="radio"></td>
				<td width="12%" class="none"><img src="pic/icons/icon19.gif" border="0"></td>
				<td class="none"><input name="iconid" value="7" type="radio"></td>
				<td width="12%" class="none"><img src="pic/icons/icon7.gif" border="0"></td>
				<td class="none"><input name="iconid" value="27" type="radio"></td>
				<td width="12%" class="none"><img src="pic/icons/icon27.gif" border="0"></td>
				<td class="none"><input name="iconid" value="6" type="radio"></td>
				<td width="12%" class="none"><img src="pic/icons/icon6.gif" border="0"></td>

				<td class="none"><input name="iconid" value="18" type="radio"></td>
				<td width="12%" class="none"><img src="pic/icons/icon18.gif" border="0"></td>
				<td class="none"><input name="iconid" value="2" type="radio"></td>
				<td width="12%" class="none"><img src="pic/icons/icon2.gif" border="0"></td></tr><tr>
				<td class="none"><input name="iconid" value="14" type="radio"></td>
				<td width="12%" class="none"><img src="pic/icons/icon14.gif" border="0"></td>
				<td class="none"><input name="iconid" value="17" type="radio"></td>
				<td width="12%" class="none"><img src="pic/icons/icon17.gif" border="0"></td>
				<td class="none"><input name="iconid" value="26" type="radio"></td>

				<td width="12%" class="none"><img src="pic/icons/icon26.gif" border="0"></td>
				<td class="none"><input name="iconid" value="8" type="radio"></td>
				<td width="12%" class="none"><img src="pic/icons/icon8.gif" border="0"></td>
				<td class="none"><input name="iconid" value="11" type="radio"></td>
				<td width="12%" class="none"><img src="pic/icons/icon11.gif" border="0"></td>
				<td class="none"><input name="iconid" value="21" type="radio"></td>
				<td width="12%" class="none"><img src="pic/icons/icon21.gif" border="0"></td>
				<td class="none"><input name="iconid" value="3" type="radio"></td>
				<td width="12%" class="none"><img src="pic/icons/icon3.gif" border="0"></td></tr><tr>

				<td class="none"><input name="iconid" value="9" type="radio"></td>
				<td width="12%" class="none"><img src="pic/icons/icon9.gif" border="0"></td>
				<td class="none"><input name="iconid" value="12" type="radio"></td>
				<td width="12%" class="none"><img src="pic/icons/icon12.gif" border="0"></td>
				<td class="none"><input name="iconid" value="4" type="radio"></td>
				<td width="12%" class="none"><img src="pic/icons/icon4.gif" border="0"></td>
				<td class="none"><input name="iconid" value="5" type="radio"></td>
				<td width="12%" class="none"><img src="pic/icons/icon5.gif" border="0"></td>
				<td class="none"><input name="iconid" value="10" type="radio"></td>

				<td width="12%" class="none"><img src="pic/icons/icon10.gif" border="0"></td>
				<td class="none"><input name="iconid" value="15" type="radio"></td>
				<td width="12%" class="none"><img src="pic/icons/icon15.gif" border="0"></td>
				<td class="none"><input name="iconid" value="20" type="radio"></td>
				<td width="12%" class="none"><img src="pic/icons/icon20.gif" border="0"></td></tr><tr>
				<td class="none"><input name="iconid" value="13" type="radio"></td>
				<td width="12%" class="none"><img src="pic/icons/icon13.gif" border="0"></td>
				<td class="none"><input name="iconid" value="24" type="radio"></td>
				<td width="12%" class="none"><img src="pic/icons/icon24.gif" border="0"></td>

				<td class="none"><input name="iconid" value="25" type="radio"></td>
				<td width="12%" class="none"><img src="pic/icons/icon25.gif" border="0"></td>
				<td class="none"><input name="iconid" value="22" type="radio"></td>
				<td width="12%" class="none"><img src="pic/icons/icon22.gif" border="0"></td>
				<td class="none"><input name="iconid" value="23" type="radio"></td>
				<td width="12%" class="none"><img src="pic/icons/icon23.gif" border="0"></td>
					<td class="none"><input name="iconid" value="0" type="radio" checked="checked"></td>
					<td width="12%" class="none"><b>No Icon</b></td>

					</tr>
				</tbody>
			</table>
		</div>
		
				</td>
			</tr>
			<?php
			endif;
   print("<tr><td colspan=2 align=center><input type=submit class=btn2 value=Submit>\n");
   print("<input type=button class=btn2 name=button value=Preview  onclick=\"javascript:get(this.parentNode);\">");
   print("</td></tr>");   
   print("</td></tr>");

   end_table();

   print("</form>\n");

   

   end_frame();end_frame();
   if ($newtopic)
    {
      $res = sql_query("SELECT name FROM forums WHERE id=".mysql_real_escape_string($id)) or sqlerr(__FILE__, __LINE__);

      $arr = mysql_fetch_assoc($res) or die("Bad forum id");

      $forumname = $arr["name"];

      $addi = "<a href=?action=viewforum&forumid=$id>$forumname</a> forum</p>\n";
    }
    else
    {
      $res = sql_query("SELECT * FROM topics WHERE id=".mysql_real_escape_string($id)) or sqlerr(__FILE__, __LINE__);

      $arr = mysql_fetch_assoc($res) or stderr("Forum error", "Topic not found.");

      $subject = htmlspecialchars($arr["subject"]);

     $addi = "<a href=?action=viewtopic&topicid=$id>$subject</a> topic";
    }
       
       print("</form>\n");

		print("<p align=center><a href=$BASEURL/page.php?type=tags target=_blank>Tags</a> | <a href=$BASEURL/page.php?type=smilies target=_blank>Smilies</a></p>\n");

    

    //------ Get 10 last posts if this is a reply

    if (!$newtopic)
    {
      $postres = sql_query("SELECT * FROM posts WHERE topicid=".mysql_real_escape_string($id)." ORDER BY id DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);

      begin_frame("10 last posts, in reverse order");

      while ($post = mysql_fetch_assoc($postres))
      {
        //-- Get poster details

        $userres = sql_query("SELECT * FROM users WHERE id=" . $post["userid"] . " LIMIT 1") or sqlerr(__FILE__, __LINE__);

        $user = mysql_fetch_assoc($userres);

      	$avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($user["avatar"]) : "");
//	    $avatar = $user["avatar"];

        if (!$avatar)
          $avatar = "".$BASEURL."/pic/default_avatar.gif";

        print("<p class=sub>#" . $post["id"] . " by " . $user["username"] . " at " . display_date_time($post["utadded"] , $CURUSER[tzoffset] ) . " GMT</p>");
    

        begin_table(true);

        print("<tr ><td height=100 width=100 align=center style='padding: 0px'>" . ($avatar ? "<img height=100 width=100 src=$avatar>" : "").
          "</td><td class=comment valign=top>" . format_comment($post["body"]) . "</td></tr>\n");

        end_table();

      }

      end_frame();

    }

  insert_quick_jump_menu();

  }

  //-------- Global variables
	global $CURUSER;
  $maxsubjectlength = 40;
  $postsperpage = $CURUSER["postsperpage"];
	if (!$postsperpage) $postsperpage = 25;
	?>