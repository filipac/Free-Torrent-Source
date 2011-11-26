<?php
/**
 * @description FTS Subtitle Section. Upload and download subtitles.  
 * @author Filip Pacurar
 * @version 1.2
 * @lastmodified 24.02.2008  
 **/  
error_reporting(0);
require "include/bittorrent.php";

loggedinorreturn();

stdhead("Subtitles");
$act = $_GET["act"];

$search = trim($HTTP_GET_VARS['search']);
$class = $HTTP_GET_VARS['class'];
if ($class == '-' || !is_valid_id($class))
$class = '';

if ($search != '' || $class)
{
$query = "title LIKE " . sqlesc("%$search%") . "";
if ($search)
$q = "search=" . htmlspecialchars($search);
}
else
{
$letter = trim($_GET["letter"]);
if (strlen($letter) > 1)
die;

if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz", $letter) === false)
$letter = "";
$query = "title LIKE '$letter%'";
$q = "letter=$letter";
}

if ($class)
{
$query .= " AND class=$class";
$q .= ($q ? "&amp;" : "") . "class=$class";
}

//kade gleda
//mysql_query("UPDATE users SET `kade` = '????????? DOX' where id = $CURUSER[id]");

if ($HTTP_SERVER_VARS["REQUEST_METHOD"] == "POST")
{
// if (get_user_class() < UC_UPLOADER)
if (get_user_class() < UC_USER)
die;

$file = $_FILES['file'];

if (!$file || $file["size"] == 0 || $file["name"] == "")
stderr("Error", "Nothing received! The selected file may have been too large.");

// if ($file["size"] > 165535)
if ($file["size"] > 1048576)
stderr("Error", "Subs is too big! Max 1,048,576 bytes.");

$accept_ext = array('sub' => sub, 'srt' => srt, 'zip' => zip, 'rar' => rar, 'ace' => ace, 'txt' => txt,
'SUB' => SUB, 'SRT' => SRT, 'ZIP' => ZIP, 'RAR' => RAR, 'ACE' => ACE, 'TXT' => TXT);
$ext_l = strrpos($file['name'], ".");
$ext = strtolower(substr($file['name'], $ext_l+1, strlen($file['name'])-($ext_l+1)));
if (!array_key_exists($ext, $accept_ext))
stderr("Error", "I am not allowed to save the file you send me :|");


if (file_exists("$SUBSPATH/$file[name]"))
stderr("Error", "A file with the name <b>$file[name]</b> already exists!");

$title = trim($HTTP_POST_VARS["title"]);
if ($title == "")
{
$title = substr($file["name"], 0, strrpos($file["name"], "."));
if (!$title)
$title = $file["name"];
//
$file["name"] = str_replace(" ", "_", htmlspecialchars("$file[name]"));
//
}

$ucd = $HTTP_POST_VARS["ucd"];
$cd = $HTTP_POST_VARS["cd"];
if ($cd != "")
{
if (!is_numeric($cd))
stderr("Error", "Bad CD, please try again!");
$ccd = $cd;
}else{
$ccd = $ucd;
}


$ufps = $HTTP_POST_VARS["ufps"];
$frame = $HTTP_POST_VARS["frame"];
if ($frame != "")
{
// if (!is_numeric($cd))
// stderr("Error", "Bad CD, please try again!");
$frames = $frame;
}else{
$frames = $ufps;
}
/*if (!is_numeric($frame))
stderr("Error", "Bad frames, please try again!");*/

$info = $HTTP_POST_VARS["info"];

$r = mysql_query("SELECT id FROM subs WHERE title=" . sqlesc($title)) or sqlesc();
if (mysql_num_rows($r) > 0)
stderr("Error", "A file with the title <b>" . htmlspecialchars($title) . "</b> already exists!");

$url = $HTTP_POST_VARS["url"];

if ($url != "")
if (substr($url, 0, 7) != "http://" && substr($url, 0, 6) != "ftp://")

stderr("Error", "The URL <b>" . htmlspecialchars($url) . "</b> does not seem to be valid.");

//
$file["name"] = str_replace(" ", "_", htmlspecialchars("$file[name]"));
//
if (!move_uploaded_file($file["tmp_name"], "$SUBSPATH/$file[name]"))

stderr("Error", "Failed to move uploaded file. You should contact an administrator about this error.");


setcookie("subsurl", $url, 0x7fffffff);


write_log("SUBS Name: $title CD's: " . $ccd . " Frame's: " . $frames . " Size: " . mksize($file["size"]) . " Uppedby: " . $CURUSER['username'], 'subsupload');
$msg_bt = "SUBS Name: $title CD's: " . $ccd . " Frame's: " . $frames . " Size: " . mksize($file["size"]) . " Uppedby: " . $CURUSER['username'] . " Download: $DEFAULTBASEURL/downloadsubs.php/".$file["name"]."";


$title = sqlesc($title);
$filename = sqlesc($file["name"]);
$cd = sqlesc($ccd);
$frame = sqlesc($frames);
$info = sqlesc($info);
$added = sqlesc(get_date_time());
$uppedby = $CURUSER["id"];
$size = $file["size"];
$url = sqlesc($url);
mysql_query("INSERT INTO subs (title, filename, cd, frame, comment, added, uppedby, size, url) VALUES($title, $filename, $cd, $frame, $info, $added, $uppedby, $size, $url)") or sqlerr();
echo <<<E
<script>window.location='subs.php';</script>
E;
die;
}

if (get_user_class() >= UC_POWER_USER)
// if (get_user_class() >= UC_USER)
{
$delete = $HTTP_GET_VARS["delete"];
if (is_valid_id($delete))
{
$r = mysql_query("SELECT filename,uppedby FROM subs WHERE id=$delete") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($r) == 1)
{
$a = mysql_fetch_assoc($r);
if (get_user_class() >= UC_MODERATOR || $a["uppedby"] == $CURUSER["id"])
{
mysql_query("DELETE FROM subs WHERE id=$delete") or sqlerr(__FILE__, __LINE__);
if (!unlink("$SUBSPATH/$a[filename]"))
stderr("Warning", "Unable to unlink file: <b>$a[filename]</b>. You should contact an administrator about this error.");

write_log("SUBS delete id $delete name $a[filename] by ".$CURUSER['username'], 'subsdelete');
}
}
}
}

collapses('searchsubs','Search Subtitles');
print("<form method=get action=?>\n");
print("Search: <input type=text size=30 name=search>\n");
print("<input type=submit class=btn value='Search'>\n");
print("</form>\n");
for ($i = 97; $i < 123; ++$i)
{
$l = chr($i);
$L = chr($i - 32);
if ($l == $letter)
print("<b>$L</b>\n");
else
print("<a href=?letter=$l><b>$L</b></a>\n");
}
collapsee();
echo '<BR>';
if (get_user_class() >= UC_UPLOADER)
//if (get_user_class() >= UC_UPLOADER || get_user_class() == UC_XTREME_USER)
{
$url = $HTTP_COOKIE_VARS["subsurl"];
// $maxfilesize = ini_get("upload_max_filesize");
$maxfilesize = "1,048,576 bytes";
begin_main_frame('100%');
?>
<div align=center>
<?php
$size = mysql_query("select sum(size) as size from subs");
$row5 = mysql_fetch_array($size);
$size = $row5['size'];
#begin_frame("Upload Subtitles - total uploaded ".mksize($size)."", true,'10','100%');
collapses('uploadsubs',"Upload Subtitles - total uploaded ".mksize($size)."");
?></div><?php
//print("<p><b>Please upload only English dox!</b></p>\n");
print("<form enctype=multipart/form-data method=post action=?>\n");
print("<table class=main border=1 cellspacing=0 cellpadding=5 width=100%>\n");
print("<tr><td class=rowhead>Title</td><td colspan=3 align=left><input type=text name=title size=60><br>(Optional, taken from file name if not specified.)</td></tr>\n");
print("<tr><td class=rowhead>File</td><td colspan=3 align=left><input type=file name=file size=45><br>(Maximum file size: $maxfilesize.)</td></tr>\n");
print("<tr><td class=rowhead>CD's</td><td align=left>
<select class=select name=ucd>
<option value=1 selected>1</option>
<option value=2>2</option>
<option value=3>3</option>
<option value=4>4</option>
<option value=5>5</option>
<option value=6>6</option>
</select>
<input type=text name=cd size=4><br>Default: 1 (Optional)</td>
<td class=rowhead><div align=left>Frames</div></td><td align=left>
<select class=select name=ufps>
<option value=15.000>15.000</option>
<option value=20.000>20.000</option>
<option value=23.976 selected>23.976</option>
<option value=24.000>24.000</option>
<option value=25.000>25.000</option>
<option value=29.970>29.970</option>
<option value=30.000>30.000</option>
</select>
<input type=text name=frame size=4><br>Default: 23.976 (Optional)</td></tr>\n");
print("<tr><td class=rowhead>Format<br>Subtitles</td><td colspan=3>
<label><input type=checkbox name=subcheckbox value=1 checked>SUB/TXT</label>
<label><input type=checkbox name=srtcheckbox value=1>SRT</label>
<label><input type=checkbox name=othercheckbox value=1>Other</label>
</td></tr>\n");
print("<tr><td class=rowhead>Info</td><td colspan=3><textarea name=info cols=60 rows=5 lang=bg wrap=virtual></textarea></td></tr>\n");
print("<tr><td class=rowhead>Site</td><td colspan=3><input type=text name=url maxlength=255 size=60 value=''></td></tr>\n");

/*
print("<tr><td class=rowhead>Download URL</td><td align=left><input type=text name=url size=60 value=\"$url\"><br><table width=340 ".
"class=main border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>(Optional, specifies a primary FTP/HTTP download location. You can substitute ".
"the file name part with an asterisk (*)</b>, e.g. http://www.mysite.com/files/*)</td>&...\n");
*/
print("<tr><td colspan=4 align=center><input type=submit value='Upload file' class=btn> <input type=reset value=Reset class=btn></td></tr>\n");
print("</table>\n");
print("</form>\n");
#end_frame();
collapsee();

end_main_frame();

}
/*
$size = mysql_query("select sum(size) as size from dox");
$row5 = mysql_fetch_array($size);
$size = $row5['size'];
print("<h1>Subs and size ".mksize($size)."</h1>\n");
*/
$res = mysql_query("SELECT * FROM subs ORDER BY added DESC") or sqlerr();
if (mysql_num_rows($res) == 0)
print("<p>Sorry, nothing here pal</p>");
else
{

$page = $_GET['page'];
$perpage = 25;

$res = mysql_query("SELECT COUNT(*) FROM subs WHERE $query") or sqlerr();
$arr = mysql_fetch_row($res);
$num = $arr[0];

if ($page == 0)
$page = 1;

$first = ($page * $perpage) - $perpage + 1;

$last = $first + $perpage - 1;

if ($last > $num)
$last = $num;

$pages = floor($num / $perpage);

if ($perpage * $pages < $num)
++$pages;

//------ Build menu
$menu = "<p align=center><b>\n";
$lastspace = false;
for ($i = 1; $i <= $pages; ++$i)
{
if ($i == $page)
$menu .= "<font class=gray>$i</font>\n";
elseif ($i > 3 && ($i < $pages - 2) && ($page - $i > 3 || $i - $page > 3))
{
if ($lastspace)
continue;
$menu .= "... \n";
$lastspace = true;
}
else
{
// $menu .= "<a href=?action=viewforum&forumid=$forumid&page=$i>$i</a>\n";
$menu .= "<a href=?page=$i>$i</a>\n";
$lastspace = false;
}
if ($i < $pages)
$menu .= "</b>|<b>\n";
}
$menu .= "<br>\n";
if ($page == 1)
$menu .= "<font class=gray>&lt;&lt; Prev</font>";
else
$menu .= "<a href=?page=" . ($page - 1) . ">&lt;&lt; Prev</a>";
$menu .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
if ($last == $num)
$menu .= "<font class=gray>Next &gt;&gt;</font>";
else
$menu .= "<a href=?page=" . ($page + 1) . ">Next &gt;&gt;</a>";
$menu .= "</b></p>\n";

print("<p>$menu</p>");

$offset = $first - 1;
$i = 0;
$res = mysql_query("SELECT * FROM subs WHERE $query ORDER BY id DESC LIMIT $offset,$perpage")
or sqlerr();

print("<p><table border=1 cellspacing=0 cellpadding=5 width=100%>\n");
print("<tr><td class=colhead align=left>Title</td><td class=colhead>CD's</td><td class=colhead>Frame's</td><td class=colhead>Date</td><td class=colhead>Time</td>" .
"<td class=colhead>Size</td><td class=colhead>Hits</td><td class=colhead>Upped by</td></tr>\n");
//
$mod = get_user_class() >= UC_MODERATOR;
// $mod = get_user_class() >= UC_USER;

while ($arr = mysql_fetch_assoc($res))
{
$r = mysql_query("SELECT username FROM users WHERE id=$arr[uppedby]") or sqlerr();
$a = mysql_fetch_assoc($r);


$d=$arr[comment];


// $title = "<td align=left><a href=downloadsubs.php/$arr[filename]><b>" . htmlspecialchars($arr["title"]) . "</b></a>" .
$title = "<td align=left><a href=\"downloadsubs.php/$arr[filename]\" onmouseover=\"if(popup_mode){overlib('<table class=ol_fgClass><tr><td width=340><b>Format:</b>lalalal<br><b>Info:</b>". ($d) ."&hellip;<br><b>Site:</b> ". $arr["url"] ."</td></tr></table>', WIDTH, 350, DELAY, 400, CAPTION, '<font color=black>". $arr["title"] ."</font>');}\" onmouseout=\"return nd();\"><b>" . htmlspecialchars($arr["title"]) . "</b></a>" .
($mod || $arr["uppedby"] == $CURUSER["id"] ? " <font size=1 class=small><a href=?delete=$arr[id]>[Delete]</a></font>" : "") ."</td>\n";
$cd = "<td>" . $arr["cd"] . "</td>\n";
if ($arr["frame"] != "")
{$frame = "<td>" . $arr["frame"] . "</td>\n";}
else
{$frame = "<td>n/a</td>\n";}
$added = "<td>" . substr($arr["added"], 0, 10) . "</td><td>" . substr($arr["added"], 10) . "</td>\n";
$size = "<td>" . mksize($arr['size']) . "</td>\n";
$hits = "<td>" . number_format($arr['hits']) . "</td>\n";
$uppedby = "<td><a href=userdetails.php?id=$arr[uppedby]><b>$a[username]</b></a></td>\n";
print("<tr>$title$cd$frame$added$size$hits$uppedby</tr>\n");
$i++;
}
print("</table></p>\n");
print("<p>$menu</p>");
}


//time

$time_start = microtime(1);
for ($i=0; $i < 1000; $i++) {
//mach nichts,1000 mal
}
$time_end = microtime(1);
$time = $time_end - $time_start;
#echo "Page generated in $time seconds\n";

//time end

stdfoot();
?>