<?php
require_once("include/bittorrent.php");

loggedinorreturn();
iplogger();
parked();
global $imdbupload;
if($imdbupload == 'yes') {
	global $usergroups;
if($usergroups['canup'] != 'yes') ug();
stdhead("Upload");
if ($CURUSER["uploadpos"] == 'no')
{
stdmsg("Sorry...", "You are not authorized to upload torrents.  (<a href=\"messages.php\">Read Inbox</a>)",false);

stdfoot();
exit;
}
$wherethisuser = where ($_SERVER["SCRIPT_FILENAME"],$CURUSER["id"]);
if ($_GET["imdb"]) {

$imdb = 0 + $_GET["imdb"];
if($imdb != '1'){
echo "<h2>Error</h2><p align=center> I smell a rat!</p>";
die();
}

print("<table cellspacing=0 cellpadding=5 width=960px>\n");
print("<tr><td colspan=2 align=center class=colhead><b>Film - Video Torrent Upload</b></td></tr>");
?>
<tr><td colspan=2 align=center><b><font size="+1">Step 1:</font></b>
<br>enter the film title below and click submit.<br><br>
<FORM ACTION="imdbsearch.php" METHOD=get>
<b>movie or video title:</b> <INPUT TYPE="text" NAME="name" SIZE=30 MAXLENGTH=50>
<INPUT class=btn TYPE="submit" VALUE="Submit">
</FORM><br></td></tr></table>
<?php
} //==end imdb start other
elseif ($_GET["other"]) {

$other = 0 + $_GET["other"];
if($other != '1'){
echo "<h2>Error</h2><p align=center> I smell a rat!</p>";
die();
}
upload_form();

}//=== end of other upload
else{
//=== start upload select type
?>
<div align=Center>
<p>The tracker's announce url is <b><?= $announce_urls[0] ?></b></p>
<b>Please select Upload Type:</b><br><br>

[ <a class=altlink href="upload.php?imdb=1"> Film / Video </a> ] -
[ <a class=altlink href="upload.php?other=1"> Other </a> ] <br><br>

<span style='font-size: x-small'>[ more options will appear ]</span>
</div>
<?php
}//=== end main page
stdfoot();
}else {
	global $usergroups;
if($usergroups['canup'] != 'yes') ug();
stdhead("Upload");
if ($CURUSER["uploadpos"] == 'no')
{
stdmsg("Sorry...", "You are not authorized to upload torrents.  (<a href=\"messages.php\">Read Inbox</a>)",false);

stdfoot();
exit;
}
upload_form();
stdfoot();
}
?>