<?php
$rootpath = '../';
require_once ($rootpath . "forums/functions/fts.php");
require_once ($rootpath . "include/bittorrent.php");
$maxsubjectlength = 40;
$postsperpage = $CURUSER["postsperpage"];
if (!$postsperpage)
    $postsperpage = 30;
global $rootpath;



$highlight = htmlspecialchars(trim($_GET["highlight"]));



$topicid = 0 + $_REQUEST["topicid"];

$page = isset($_REQUEST["page"]) ? (int)$_REQUEST["page"] : false;

int_check($topicid, true);

$userid = $CURUSER["id"];


$res = sql_query("SELECT * FROM topics WHERE id=$topicid") or sqlerr(__file__,
    __line__);

$arr = mysql_fetch_assoc($res) or stderr("Forum error", "Topic not found");
if ($arr["numratings"] != 0)
    $rating = ROUND($arr["ratingsum"] / $arr["numratings"], 1);
$rpic = ratingpic($rating);
$locked = ($arr["locked"] == 'yes');
$subject = htmlspecialchars($arr["subject"]);
$sticky = $arr["sticky"] == "yes";
$forumid = $arr["forumid"];


sql_query("UPDATE topics SET views = views + 1 WHERE id=$topicid") or sqlerr(__file__,
    __line__);


$res = sql_query("SELECT * FROM forums WHERE id=$forumid") or sqlerr(__file__,
    __line__);

$arr = mysql_fetch_assoc($res) or die("Forum = NULL");

$forum = $arr["name"];

if ($CURUSER["class"] < $arr["minclassread"])
    stderr("Error", "You are not permitted to view this topic.");


$res = sql_query("SELECT COUNT(*) FROM posts WHERE topicid=$topicid") or sqlerr(__file__,
    __line__);

$arr = mysql_fetch_row($res);

$postcount = $arr[0];


$pagemenu = "<div id=navcontainer2 style=\"float: left; margin-bottom: 1px;\"><ul>\n";

$perpage = $postsperpage;

$pages = ceil($postcount / $perpage);

if ($page[0] == "p")
{
    $findpost = substr($page, 1);
    $res = sql_query("SELECT id FROM posts WHERE topicid=$topicid ORDER BY added ASC") or
        sqlerr(__file__, __line__);
    $i = 1;
    while ($arr = mysql_fetch_row($res))
    {
        if ($arr[0] == $findpost)
            break;
        ++$i;
    }
    $page = ceil($i / $perpage);
}

if ($page == "last")
    $page = $pages;
else
{
    if ($page < 1)
        $page = 1;
    elseif ($page > $pages)
        $page = $pages;
}

$offset = $page * $perpage - $perpage;
if ($page == 1)
    $pagemenu .= "<li><font class=gray><b>&lt;&lt; Prev</b></font></li>";

else
    $pagemenu .= "<li><a href=?topicid=$topicid&page=" . ($page - 1) .
        "><b>&lt;&lt; Prev</b></a></li>";

for ($i = 1; $i <= $pages; ++$i)
{
    if ($i == $page)
        $pagemenu .= "<li><a name=\"current\" class=\"current\"><b>$i</b></a></li>\n";

    else
        $pagemenu .= "<li><a href=?topicid=$topicid&page=$i><b>$i</b></a></li>\n";
}

if ($page == $pages)
    $pagemenu .= "<li><font class=gray><b>Next &gt;&gt;</b></font></li></ul></div>\n";

else
    $pagemenu .= "<li><a href=?topicid=$topicid&page=" . ($page + 1) .
        "><b>Next &gt;&gt;</b></a></li></ul></div>\n";


$res = sql_query("SELECT * FROM posts WHERE topicid=$topicid ORDER BY id LIMIT $offset,$perpage") or
    sqlerr(__file__, __line__);
print ("<p align=left style=\"color:gray;margin-bottom: 3px;\"><a href=index.php>$SITENAME Forums</a> / <a href=viewforum.php?forumid=$forumid>$forum</a> / $subject</p><span align=left style=\"margin-bottom: 3px;\">$pagemenu</span>");
switch ($_GET['sub'])
{
    case '0':
        echo '<p align=center class=success>You have been subscribed to this thread. To view your subscriptions, click <a href="subscriptions.php">here</a></p>';
        break;
    case '1':
        echo '<p align=center class=error>You have been unsubscribed to this thread. To view your subscriptions, click <a href="subscriptions.php">here</a></p>';
        break;
}

JsB::preparecmenu('200', '0');
echo '<div class=ftsdialog id=rate' . $topicid . '>';
$contents = array("<a class=altlink href=$BASEURL/takerate.php?topic_id=$topicid&rate_me=5> 5 <img src=\"$rootpath/pic/5.gif\" alt=\"5 - tops\"></a>",
    "<a class=altlink href=$BASEURL/takerate.php?topic_id=$topicid&rate_me=4> 4 <img src=\"$rootpath/pic/4.gif\" alt=\"4 - great\"></a>",
    "<a class=altlink href=$BASEURL/takerate.php?topic_id=$topicid&rate_me=3> 3 <img src=\"$rootpath/pic/3.gif\" alt=\"3 - ok\"></a>",
    "<a class=altlink href=$BASEURL/takerate.php?topic_id=$topicid&rate_me=2> 2 <img src=\"$rootpath/pic/2.gif\" alt=\"2 - eh\"></a>",
    "<a class=altlink href=$BASEURL/takerate.php?topic_id=$topicid&rate_me=1> 1 <img src=\"$rootpath/pic/1.gif\" alt=\"1 - bad\"></a>");
echo <<< EOD
<table cellpadding="4" cellspacing="1" border="0" width="200">
		<tr>
			<td class="thead"><b>Rate Topic</b></td>
		</tr>
EOD;
foreach ($contents as $c)
    echo '<tr><td class=subheader>' . $c . '</td></tr>';
echo '</table></div>';
begin_main_frame('100%');
$db = mysql_query("SELECT * FROM subscriptions WHERE userid = $CURUSER[id] AND topicid='$topicid'");
if (mysql_num_rows($db) == 0)
    $subscriptions = "<a href=subscribe.php?topicid=$topicid>Subscribe to this Forum</a>";
else
    $subscriptions = "<a href=subscriptions.php?delete=true&fid=$topicid>Unsubscribe to this Forum</a>";
$reshaha = mysql_query("SELECT topic, user FROM ratings WHERE topic = $topicid AND user = $CURUSER[id]") or
    die(mysql_error);
$rowhaha = mysql_fetch_array($reshaha);
echo '<div class=ftsdialog id=options' . $topicid . '>';
echo <<< TABLE
<table cellpadding="4" cellspacing="1" border="0" width="200">
		<tr>
			<td class="thead"><b>Topic Options</b></td>
		</tr>
		<tr><td class=subheader>
		$subscriptions
		</td></tr><tr><td class=subheader>
		<a href='$BASEURL/forums/misc.php?action=print&topicid=$topicid'>Print This Thread</a>
		</td></tr>
TABLE;
echo '</table></div>';

print ("</p></td></tr></table>");


$pc = mysql_num_rows($res);

$pn = 0;

$r = sql_query("SELECT lastpostread FROM readposts WHERE userid=" . $CURUSER["id"] .
    " AND topicid=$topicid") or sqlerr(__file__, __line__);

$a = mysql_fetch_row($r);

$lpr = $a[0];

if (!$lpr)
    sql_query("INSERT INTO readposts (userid, topicid) VALUES($userid, $topicid)") or
        sqlerr(__file__, __line__);
$postcount = 0;
ftsmenu();
echo <<< eod
<table width="100%" border="0" cellspacing="0" cellpadding="4" style="clear: both;margin-top: -10px;">
		<tr>
			<td colspan="3" class="theadf" align="left">
				<p class="smalltext"><div style="float: right;">
eod;
if ($rowhaha[topic] >= 1)
    echo "<span class='subheader'><b><font color=black>You already rated this thread!</font></b></span>";
else
{
    echo JsB::showcmenu("rate$topicid", "<span class='subheader'>Rate Thread</span>");
}
echo "&nbsp;&nbsp;";
echo JsB::showcmenu("options$topicid",
    "<span class='subheader'>Thread Options</span>");
echo "&nbsp;" . $rpic;
echo "</div></p>
			</td>			
		</tr>";
include 'while.php';
echo "</table>" . $pagemenu; 
?>