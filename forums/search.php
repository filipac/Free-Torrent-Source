<?php
$rootpath = '../';
require_once($rootpath."forums/functions/fts.php");
require_once($rootpath."include/bittorrent.php");
  

  loggedinorreturn();
  $wherethisuser = where ($_SERVER["SCRIPT_FILENAME"],$CURUSER["id"]);

  iplogger();
	parked();
	stdhead("Forum Search");
	unset($error);
	$error= false;
	$keywords = htmlspecialchars(trim($_GET["keywords"]));
	if ($keywords != "")
	{
		$perpage = 5;
		$page = max(1, 0 + $_GET["page"]);
		$extraSql 	= "body LIKE '%".mysql_real_escape_string($keywords)."%'";	
		$res = sql_query("SELECT COUNT(*) FROM posts WHERE $extraSql") or sqlerr(__FILE__, __LINE__);
		$arr = mysql_fetch_row($res);
		$hits = 0 + $arr[0];
		if ($hits == 0)
			$error = true;
		else
		{
			$pages = 0 + ceil($hits / $perpage);
			if ($page > $pages) $page = $pages;
			for ($i = 1; $i <= $pages; ++$i)
				if ($page == $i)
					$pagemenu1 .= "<font class=gray><b>$i</b></font>\n";
				else
					$pagemenu1 .= "<a href=\"search.php?keywords=$keywords&page=$i\"><b>$i</b></a>\n";
			if ($page == 1)
				$pagemenu2 = "<font class=gray><b>&lt;&lt; Prev</b></font>\n";
			else
				$pagemenu2 = "<a href=\"search.php?keywords=$keywords&page=" . ($page - 1) . "\"><b>&lt;&lt; Prev</b></a>\n";
			$pagemenu2 .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
			if ($page == $pages)
				$pagemenu2 .= "<font class=gray><b>Next &gt;&gt;</b></font>\n";
			else
				$pagemenu2 .= "<a href=\"search.php?keywords=$keywords&page=" . ($page + 1) . "\"><b>Next &gt;&gt;</b></a>\n";
			$offset = ($page * $perpage) - $perpage;
			$res = sql_query("SELECT id, topicid,userid,added FROM posts WHERE  $extraSql LIMIT $offset,$perpage") or sqlerr(__FILE__, __LINE__);
			$num = mysql_num_rows($res);
			print("<p>$pagemenu1<br>$pagemenu2</p>");
			print("<table border=1 cellspacing=0 cellpadding=5 width=100%>\n");
			print("<tr><td class=colhead>Post</td><td class=colhead align=left>Topic</td><td class=colhead align=left>Forum</td><td class=colhead align=left>Posted by</td></tr>\n");
			for ($i = 0; $i < $num; ++$i)
			{
				$post = mysql_fetch_assoc($res);
				$res2 = sql_query("SELECT forumid, subject FROM topics WHERE id=$post[topicid]") or
					sqlerr(__FILE__, __LINE__);
				$topic = mysql_fetch_assoc($res2);
				$res2 = sql_query("SELECT name,minclassread FROM forums WHERE id=$topic[forumid]") or
					sqlerr(__FILE__, __LINE__);
				$forum = mysql_fetch_assoc($res2);
				if ($forum["name"] == "" || $forum["minclassread"] > $CURUSER["class"])
				{
					--$hits;
					continue;
				}
				$res2 = sql_query("SELECT username FROM users WHERE id=$post[userid]") or
					sqlerr(__FILE__, __LINE__);
				$user = mysql_fetch_assoc($res2);
				if ($user["username"] == "")
					$user["username"] = "[$post[userid]]";
				//---------------------------------
				//---- Search Highlight v0.1
				//---------------------------------	
				print("<tr><td>$post[id]</td><td align=left><a href=viewtopic.php?highlight=$keywords&topicid=$post[topicid]&page=p$post[id]#$post[id]><b>" . htmlspecialchars($topic["subject"]) . "</b></a></td><td align=left><a href=viewforum.php?forumid=$topic[forumid]><b>" . htmlspecialchars($forum["name"]) . "</b></a><td align=left><b><a href=$BASEURL/userdetails.php?id=$post[userid]>$user[username]</a></b><br>at $post[added]</tr>\n");
				//---------------------------------
				//---- Search Highlight v0.1
				//---------------------------------
			}
			print("</table>\n");
			print("<p>$pagemenu2<br>$pagemenu1</p>");			
			$found ="[<b><font color=red> Found $hits post" . ($hits != 1 ? "s" : "")." </font></b> ]";
			
		}
	}
?>
<style type="text/css">
<!--
.search{
	background-image:url(pic/search.gif);
	background-repeat:no-repeat;
	width:579px;
	height:95px;
	margin:5px 0 5px 0;
	text-align:left;
}
.search_title{
	color:#0062AE;
	background-color:#DAF3FB;
	font-size:12px;
	font-weight:bold;
	text-align:left;
	padding:7px 0 0 15px;
}

.search_table {
  border-collapse: collapse;
  border: none;
   background-color: #ffffff; 
}
-->
</style>
<div class="search">
  <div class="search_title">Search on Forums <?=($error ? "[<b><font color=red> Nothing Found</font></b> ]" : $found)?></div>
  <div style="margin-left: 53px; margin-top: 13px;">
<form method="get" action="search.php" id="search_form" style="margin: 0pt; padding: 0pt; font-family: Tahoma,Arial,Helvetica,sans-serif; font-size: 11px;">
      <table border="0" cellpadding="0" cellspacing="0" width="512" class="search_table">
        <tbody>
          <tr>
          <td style="padding-bottom: 3px; border: 0;" valign="top">by keyword</td>
          </tr>
          <tr>
          <td style="padding-bottom: 3px; border: 0;" valign="top">			
			<input name="keywords" type="text" value="<?=$keywords?>" size="75" /></td>
            <td style="padding-bottom: 3px; border: 0;" valign="top"><input name="image" type="image" style="vertical-align: middle; padding-bottom: 7px; margin-left: 1px;" src="<?=$BASEURL?>/pic/search_button.gif" /></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php
	stdfoot();
	die;

?>