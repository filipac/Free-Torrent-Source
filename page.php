<?php
$type = isset( $_POST['type'] ) ? $_POST['type'] : ( isset($_GET['type']) ? $_GET['type'] :
    '' ) ;
if($type=='torrent_info') {
	require "include/bittorrent.php";
	if(!ur::ismod())
	stderr('Error!','You are not in staff team');
require_once "include/benc.php";


 



loggedinorreturn();

if (get_user_class() < UC_MODERATOR)
{
	die("You don't have permission. ");
}

$id = (int)$_GET["id"];

if (!$id)
	httperr();

$res = sql_query("SELECT name FROM torrents WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_assoc($res);

$fn = "$torrent_dir/$id.torrent";

if (!$row || !is_file($fn) || !is_readable($fn))
	httperr();



// Standard html headers
stdhead("Torrent Info");
?>

<style type="text/css"><!--

/* list styles */
ul ul { margin-left: 15px; }
ul, li { padding: 0px; margin: 0px; list-style-type: none; color: #000; font-weight: normal;}
ul a, li a { color: #009; text-decoration: none; font-weight: normal; }
li { display: inline; } /* fix for IE blank line bug */
ul > li { display: list-item; }

li div.string  {padding: 3px;}
li div.integer {padding: 3px;}
li div.dictionary {padding: 3px;}
li div.list {padding: 3px;}
li div.string span.icon {color:#090;padding: 2px;}
li div.integer span.icon {color:#990;padding: 2px;}
li div.dictionary span.icon {color:#909;padding: 2px;}
li div.list span.icon {color:#009;padding: 2px;}

li span.title {font-weight: bold;}

--></style>

<?php

begin_main_frame();


// Heading
print("<div align=center><h1>$row[name]</h1>");

$dict = bdec_file($fn, (1024*1024));

// Start table
print("<table width=750 border=1 cellspacing=0 cellpadding=5><td>");

$dict['value']['info']['value']['pieces']['value'] = "0x".bin2hex(substr($dict['value']['info']['value']['pieces']['value'], 0, 25))."...";



echo "<ul id=colapse>";
print_array($dict,"*", "", "root");
echo "</ul>";

// End table
print("</td></table>");


?>


<script type="text/javascript" language="javascript1.2"><!--
var openLists = [], oIcount = 0;
function compactMenu(oID,oAutoCol,oPlMn,oMinimalLink) {
	if( !document.getElementsByTagName || !document.childNodes || !document.createElement ) { return; }
	var baseElement = document.getElementById( oID ); if( !baseElement ) { return; }
	compactChildren( baseElement, 0, oID, oAutoCol, oPlMn, baseElement.tagName.toUpperCase(), oMinimalLink && oPlMn );
}
function compactChildren( oOb, oLev, oBsID, oCol, oPM, oT, oML ) {
	if( !oLev ) { oBsID = escape(oBsID); if( oCol ) { openLists[oBsID] = []; } }
	for( var x = 0, y = oOb.childNodes; x < y.length; x++ ) { if( y[x].tagName ) {
		//for each immediate LI child
		var theNextUL = y[x].getElementsByTagName( oT )[0];
		if( theNextUL ) {
			//collapse the first UL/OL child
			theNextUL.style.display = 'none';
			//create a link for expanding/collapsing
			var newLink = document.createElement('A');
			newLink.setAttribute( 'href', '#' );
			newLink.onclick = new Function( 'clickSmack(this,' + oLev + ',\'' + oBsID + '\',' + oCol + ',\'' + escape(oT) + '\');return false;' );
			//wrap everything upto the child U/OL in the link
			if( oML ) { var theHTML = ''; } else {
				var theT = y[x].innerHTML.toUpperCase().indexOf('<'+oT);
				var theA = y[x].innerHTML.toUpperCase().indexOf('<A');
				var theHTML = y[x].innerHTML.substr(0, ( theA + 1 && theA < theT ) ? theA : theT );
				while( !y[x].childNodes[0].tagName || ( y[x].childNodes[0].tagName.toUpperCase() != oT && y[x].childNodes[0].tagName.toUpperCase() != 'A' ) ) {
					y[x].removeChild( y[x].childNodes[0] ); }
			}
			y[x].insertBefore(newLink,y[x].childNodes[0]);
			y[x].childNodes[0].innerHTML = oPM + theHTML.replace(/^\s*|\s*$/g,'');
			theNextUL.MWJuniqueID = oIcount++;
			compactChildren( theNextUL, oLev + 1, oBsID, oCol, oPM, oT, oML );
} } } }
function clickSmack( oThisOb, oLevel, oBsID, oCol, oT ) {
	if( oThisOb.blur ) { oThisOb.blur(); }
	oThisOb = oThisOb.parentNode.getElementsByTagName( unescape(oT) )[0];
	if( oCol ) {
		for( var x = openLists[oBsID].length - 1; x >= oLevel; x-=1 ) { if( openLists[oBsID][x] ) {
			openLists[oBsID][x].style.display = 'none'; if( oLevel != x ) { openLists[oBsID][x] = null; }
		} }
		if( oThisOb == openLists[oBsID][oLevel] ) { openLists[oBsID][oLevel] = null; }
		else { oThisOb.style.display = 'block'; openLists[oBsID][oLevel] = oThisOb; }
	} else { oThisOb.style.display = ( oThisOb.style.display == 'block' ) ? 'none' : 'block'; }
}
function stateToFromStr(oID,oFStr) {
	if( !document.getElementsByTagName || !document.childNodes || !document.createElement ) { return ''; }
	var baseElement = document.getElementById( oID ); if( !baseElement ) { return ''; }
	if( !oFStr && typeof(oFStr) != 'undefined' ) { return ''; } if( oFStr ) { oFStr = oFStr.split(':'); }
	for( var oStr = '', l = baseElement.getElementsByTagName(baseElement.tagName), x = 0; l[x]; x++ ) {
		if( oFStr && MWJisInTheArray( l[x].MWJuniqueID, oFStr ) && l[x].style.display == 'none' ) { l[x].parentNode.getElementsByTagName('a')[0].onclick(); }
		else if( l[x].style.display != 'none' ) { oStr += (oStr?':':'') + l[x].MWJuniqueID; }
	}
	return oStr;
}
function MWJisInTheArray(oNeed,oHay) { for( var i = 0; i < oHay.length; i++ ) { if( oNeed == oHay[i] ) { return true; } } return false; }
function selfLink(oRootElement,oClass,oExpand) {
	if(!document.getElementsByTagName||!document.childNodes) { return; }
	oRootElement = document.getElementById(oRootElement);
	for( var x = 0, y = oRootElement.getElementsByTagName('a'); y[x]; x++ ) {
		if( y[x].getAttribute('href') && !y[x].href.match(/#$/) && getRealAddress(y[x]) == getRealAddress(location) ) {
			y[x].className = (y[x].className?(y[x].className+' '):'') + oClass;
			if( oExpand ) {
				oExpand = false;
				for( var oEl = y[x].parentNode, ulStr = ''; oEl != oRootElement && oEl != document.body; oEl = oEl.parentNode ) {
					if( oEl.tagName && oEl.tagName == oRootElement.tagName ) { ulStr = oEl.MWJuniqueID + (ulStr?(':'+ulStr):''); } }
				stateToFromStr(oRootElement.id,ulStr);
} } } }
function getRealAddress(oOb) { return oOb.protocol + ( ( oOb.protocol.indexOf( ':' ) + 1 ) ? '' : ':' ) + oOb.hostname + ( ( typeof(oOb.pathname) == typeof(' ') && oOb.pathname.indexOf('/') != 0 ) ? '/' : '' ) + oOb.pathname + oOb.search; }

compactMenu('colapse',false,'');
//--></script>



<?php
// Standard html footers
end_main_frame();
stdfoot();
die;
}elseif($type == 'tags') {
	require "include/bittorrent.php";




stdhead("Tags");
begin_main_frame();
begin_frame("Tags");
$test = $_POST["test"];
?>
<p><b>The <?=$SITENAME?></b> forums supports a number of <i>BB tags</i> which you can embed to modify how your posts are displayed.</p>

<form method=post action="page.php">
<input type=hidden name=type value=tags>
<textarea name=test cols=60 rows=3><? print($test ? htmlspecialchars($test) : "")?></textarea>
<input type=submit value="Test this code!" style='height: 23px; margin-left: 5px'>
</form>
<?php

if ($test != "")
  print("<p><hr>" . format_comment($test) . "<hr></p>\n");

insert_tag(
	"Bold",
	"Makes the enclosed text bold.",
	"[b]<i>Text</i>[/b]",
	"[b]This is bold text.[/b]",
	""
);

insert_tag(
	"Italic",
	"Makes the enclosed text italic.",
	"[i]<i>Text</i>[/i]",
	"[i]This is italic text.[/i]",
	""
);

insert_tag(
	"Underline",
	"Makes the enclosed text underlined.",
	"[u]<i>Text</i>[/u]",
	"[u]This is underlined text.[/u]",
	""
);

insert_tag(
	"Color (alt. 1)",
	"Changes the color of the enclosed text.",
	"[color=<i>Color</i>]<i>Text</i>[/color]",
	"[color=blue]This is blue text.[/color]",
	"What colors are valid depends on the browser. If you use the basic colors (red, green, blue, yellow, pink etc) you should be safe."
);

insert_tag(
	"Color (alt. 2)",
	"Changes the color of the enclosed text.",
	"[color=#<i>RGB</i>]<i>Text</i>[/color]",
	"[color=#0000ff]This is blue text.[/color]",
	"<i>RGB</i> must be a six digit hexadecimal number."
);

insert_tag(
	"Size",
	"Sets the size of the enclosed text.",
	"[size=<i>n</i>]<i>text</i>[/size]",
	"[size=4]This is size 4.[/size]",
	"<i>n</i> must be an integer in the range 1 (smallest) to 7 (biggest). The default size is 2."
);

insert_tag(
	"Font",
	"Sets the type-face (font) for the enclosed text.",
	"[font=<i>Font</i>]<i>Text</i>[/font]",
	"[font=Impact]Hello world![/font]",
	"You specify alternative fonts by separating them with a comma."
);

insert_tag(
	"Hyperlink (alt. 1)",
	"Inserts a hyperlink.",
	"[url]<i>URL</i>[/url]",
	"[url]".$BASEURL."[/url]",
	"This tag is superfluous; all URLs are automatically hyperlinked."
);

insert_tag(
	"Hyperlink (alt. 2)",
	"Inserts a hyperlink.",
	"[url=<i>URL</i>]<i>Link text</i>[/url]",
	"[url=".$BASEURL."]".$SITENAME."[/url]",
	"You do not have to use this tag unless you want to set the link text; all URLs are automatically hyperlinked."
);

insert_tag(
	"Image (alt. 1)",
	"Inserts a picture.",
	"[img=<i>URL</i>]",
	"[img=$BASEURL/pic/logo.gif]",
	"The URL must end with <b>.gif</b>, <b>.jpg</b> or <b>.png</b>."
);

insert_tag(
	"Image (alt. 2)",
	"Inserts a picture.",
	"[img]<i>URL</i>[/img]",
	"[img]$BASEURL/pic/logo.gif[/img]",
	"The URL must end with <b>.gif</b>, <b>.jpg</b> or <b>.png</b>."
);

insert_tag(
	"Quote (alt. 1)",
	"Inserts a quote.",
	"[quote]<i>Quoted text</i>[/quote]",
	"[quote]The quick brown fox jumps over the lazy dog.[/quote]",
	""
);

insert_tag(
	"Quote (alt. 2)",
	"Inserts a quote.",
	"[quote=<i>Author</i>]<i>Quoted text</i>[/quote]",
	"[quote=John Doe]The quick brown fox jumps over the lazy dog.[/quote]",
	""
);

insert_tag(
	"List",
	"Inserts a list item.",
	"[*]<i>Text</i>",
	"[*] This is item 1\n[*] This is item 2",
	""
);

insert_tag(
	"Preformat",
	"Preformatted (monospace) text. Does not wrap automatically.",
	"[pre]<i>Text</i>[/pre]",
	"[pre]This is preformatted text.[/pre]",
	""
);

end_frame();
end_main_frame();
stdfoot();
die;
}elseif($type == 'porttest'){
	require "include/bittorrent.php";

loggedinorreturn();
stdhead('Port Test');
if ($CURUSER)
{
if ($_SERVER["REQUEST_METHOD"] == "POST")
$ip = $_POST["ip"];
else
$port=$_GET['ip'];

if ($_SERVER["REQUEST_METHOD"] == "POST")
$port = $_POST["port"];
else
$port=$_GET['port'];

if ($ip == "")
$ip = $CURUSER["ip"];

if ($port)
{
$fp = @fsockopen ($ip, $port, $errno, $errstr, 10);
if (!$fp) {

print ("<table width=40% id=torrenttable class=tableoutborder cellspacing=1 cellpadding=5><br><tr>".
"<td class=tabletitle align=center><b>Test porte</b></td></tr><tr><td class=tableb><font color=darkred><br><center><b>IP: $ip Port: $port closed!</b></center><br></font></td></tr><tr><td class=tableb><center><form><INPUT TYPE=\"BUTTON\" VALUE=\"New Test\" ONCLICK=\"window.location.href='page.php?type=porttest'\"></form></center></td></tr></table");
} else {

print ("<table width=40% id=torrenttable class=tableoutborder cellspacing=1 cellpadding=5><br><tr>".
"<td class=tabletitle align=center><b>Test porte</b></td></tr><tr><td class=tableb><font color=darkgreen><br><center><b>IP:$ip Port: $port open!</b></center><br></font></td></tr><tr><td class=tableb><center><form><INPUT TYPE=\"BUTTON\" VALUE=\"New Test\" ONCLICK=\"window.location.href='page.php?type=porttest'\"></form></center></td></tr></table>");
}
}

else
{
print("<table width=40% id=torrenttable class=tableoutborder cellspacing=1 cellpadding=5><br><tr>".
"<td class=tabletitle align=center><b>Port Test</b></td>".
"</tr>");
print("</table>");
print ("<form method=post action=page.php><input type=hidden name=type value=porttest>");
print ("<table width=\"40%\" border=1 cellspacing=0 cellpadding=5>");
print ("<tr><td class=tableb><center>Port number:<center></td><td class=tableb><center><input type=text name=port></center></td></tr>");
if (get_user_class() >= UC_MODERATOR){
print ("<tr><td class=tableb><center>IP (Left blank for yours):<center></td><td class=tableb><center><input type=text name=ip></center></td></tr>");
}
print ("<tr><td class=tableb></td><td class=tableb><center><input type=submit class=btn value='OK'></center></td></tr>");
print ("</form>");
print ("</table>");
}
}
stdfoot ();
}elseif($type == 'fastdelete') {
	require_once("include/bittorrent.php");


if (!mkglobal("id"))
    bark("missing form data");

$id = 0 + $id;
int_check($id);
$sure = $_GET["sure"];


loggedinorreturn();

$res = sql_query("SELECT name,owner,seeders FROM torrents WHERE id = $id");
$row = mysql_fetch_array($res);
if (!$row)
    die();

#if ($CURUSER["id"] != $row["owner"] && get_user_class() < UC_MODERATOR)
#    bark("You're not the owner! How did that happen?\n");
if (!ur::ismod())
    bark("You're not authorised to delete this torrent, only moderators can do that. Please contact one if this is your torrent and you want to delete it.\n");

if (!$sure)
    {
      stderr("Delete torrent", "Sanity check: You are about to delete a torrent. Click\n" .
      "<a href=page.php?type=fastdelete&id=$id&sure=1>here</a> if you are sure.",false);
    }
    
deletetorrent($id);
write_log("Torrent $id ($row[name]) was deleted by $CURUSER[username] ($reasonstr)\n");
header("Refresh: 0; url=browse.php");
}
elseif($type == 'ajax') {
	include "include/ajax.php";
}
elseif($type == 'ok') {
	require_once("include/bittorrent.php");



if (!mkglobal("typeok"))
	die();

if ($typeok == "adminactivate") {
	stdhead("User signup");
    stdmsg("Signup successful & Account Activation!",
    "Your account successfully created however Admin must validate new members before they are classified as registered members and are allowed to access site, thank you for your understanding.");
}
elseif ($typeok == "signup" && mkglobal("email")) {
	stdhead("User signup");
        stdmsg("Signup successful!",
	"A confirmation email has been sent to the address you specified (" . htmlspecialchars($email) . "). You need to read and respond to this email before you can use your account. If you don't do this, the new account will be deleted automatically after a few days.");
	stdfoot();
}
elseif ($typeok == "sysop") {
		stdhead("Sysop Account activation");
		print("<h1>Sysop Account successfully activated!</h1>\n");
	if (isset($CURUSER))
		print("<p>Your account has been activated! You have been automatically logged in. You can now continue to the <a href=\"/\"><b>main page</b></a> and start using your account.</p>\n");
	else
		print("<p>Your account has been activated! However, it appears that you could not be logged in automatically. A possible reason is that you disabled cookies in your browser. You have to enable cookies to use your account. Please do that and then <a href=\"login.php\">log in</a> and try again.</p>\n");
	stdfoot();
	}
elseif ($typeok == "confirmed") {
	stdhead("Already confirmed");
	print("<h1>Already confirmed</h1>\n");
	print("<p>This user account has already been confirmed. You can proceed to <a href=\"login.php\">log in</a> with it.</p>\n");
	stdfoot();
}
elseif ($typeok == "confirm") {
	if (isset($CURUSER)) {
		stdhead("Signup confirmation");
		print("<h1>Account successfully confirmed!</h1>\n");
		print("<p>Your account has been activated! You have been automatically logged in. You can now continue to the <a href=\"index.php\"><b>main page</b></a> and start using your account.</p>\n");
		print("<p>Before you start using $SITENAME we urge you to read the <a href=\"rules.php\"><b>RULES</b></a> and the <a href=\"faq.php\"><b>FAQ</b></a>.</p>\n");
		stdfoot();
	}
	else {
		stdhead("Signup confirmation");
		print("<h1>Account successfully confirmed!</h1>\n");
		print("<p>Your account has been activated! However, it appears that you could not be logged in automatically. A possible reason is that you disabled cookies in your browser. You have to enable cookies to use your account. Please do that and then <a href=\"login.php\">log in</a> and try again.</p>\n");
		stdfoot();
	}
}
else
	die();
}elseif($type == 'users') {
	require "include/bittorrent.php";
lang::load('users');

loggedinorreturn();
if($usergroups['canmemberlist'] != 'yes') ug();
parked();

$search = trim($_GET['search']);
$class = $_GET['class'];
if ($class == '-' || !is_valid_id($class))
  $class = '';

if ($search != '' || $class)
{
  $query = "username LIKE " . sqlesc("%$search%") . " AND status='confirmed'";
	if ($search)
		  $q = "search=" . htmlspecialchars($search);
}
else
{
	$letter = trim($_GET["letter"]);
  if (strlen($letter) > 1)
    die;

  if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz", $letter) === false)
    $letter = "a";
  $query = "username LIKE '$letter%' AND status='confirmed'";
  $q = "type=users&letter=$letter";
}

if ($class)
{
  $query .= " AND class=$class";
  $q .= ($q ? "&" : "") . "class=$class";
}

stdhead(str1);

print("<h1>".str1."</h1>\n");

print("<form method=get action=page.php><input type=hidden name=type value=users\n");
print(str2.": <input type=text size=30 name=search>\n");
print("<select name=class>\n");
print("<option value='-'>".str3."</option>\n");
for ($i = 0;;++$i)
{
	if ($c = get_user_class_name($i))
	  print("<option value=$i" . ($class && $class == $i ? " selected" : "") . ">$c</option>\n");
	else
	  break;
}
print("</select>\n");
print("<input type=submit value='".str4."'>\n");
print("</form>\n");

print("<p>\n");

for ($i = 97; $i < 123; ++$i)
{
	$l = chr($i);
	$L = chr($i - 32);
	if ($l == $letter)
    print("<b>$L</b>\n");
	else
    print("<a href=page.php?type=users&letter=$l><b>$L</b></a>\n");
}

print("</p>\n");
  
$page = $_GET['page'];
$perpage = 100;

$res = sql_query("SELECT COUNT(*) FROM users WHERE $query") or sqlerr();
$arr = mysql_fetch_row($res);
$pages = floor($arr[0] / $perpage);
if ($pages * $perpage < $arr[0])
  ++$pages;

if ($page < 1)
  $page = 1;
else
  if ($page > $pages)
    $page = $pages;

for ($i = 1; $i <= $pages; ++$i)
  if ($i == $page)
    $pagemenu .= "<b>$i</b>\n";
  else
    $pagemenu .= "<a href=?$q&page=$i><b>$i</b></a>\n";

if ($page == 1)
  $browsemenu .= "<b>&lt;&lt; ".str5."</b>";
else
  $browsemenu .= "<a href=?$q&page=" . ($page - 1) . "><b>&lt;&lt; ".str5."</b></a>";

$browsemenu .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

if ($page == $pages)
  $browsemenu .= "<b>".str6." &gt;&gt;</b>";
else
  $browsemenu .= "<a href=?$q&page=" . ($page + 1) . "><b>".str6." &gt;&gt;</b></a>";

print("<p>$browsemenu<br>$pagemenu</p>");

$offset = ($page * $perpage) - $perpage;

$country_sql = "concat('<img src=\"pic/flag/', countries.flagpic, '\" alt=\"', countries.name  ,'\">')";

$sql = sprintf('SELECT
        users.id     as  id,
        users.username as  username,
        users.donated as donated,
        users.donor as donor,
        users.warned as warned,
        users.class   as   class,
       IF (
         users.country >0, %s, \'---\'
       ) as country,
       IF (
         users.added = "0000-00-00 00:00:00", "-", users.added
       ) as added,
       IF (
         users.last_access = "0000-00-00 00:00:00", "-", users.last_access
       ) as last_access
       
       
       FROM users
       LEFT JOIN countries ON users.country = countries.id
       WHERE %s
       ORDER BY username LIMIT %u,%u',
       $country_sql, $query, $offset, $perpage);


$res = sql_query($sql) or sqlerr();


$num = mysql_num_rows($res);

print("<table border=1 cellspacing=0 cellpadding=5>\n");
print("<tr><td class=colhead align=left>".str7."</td><td class=colhead>".str8."</td><td class=colhead>".str9."</td><td class=colhead align=left>".str10."</td><td class=colhead>".str11."</td></tr>\n");
for ($i = 0; $i < $num; ++$i)
{
 $arr = mysql_fetch_assoc($res);
   
 echo '<tr><td align="left"><a href="userdetails.php?id='.$arr['id'].'"><b>'.$arr['username'].'</b></a>'.($arr['warned'] == 'yes' ? " <img src=pic/warning.png border=0 alt='".str12."'>" : "").' '.($arr['donated'] > 0 ? " <img src=pic/star.png border=0 alt='".str13."'>" : $arr['donor'] == 'yes' ?  " <img src=pic/star.png border=0 alt='".str13."'>" : "")." </td>" . '<td>'.$arr['added'].'</td><td>'.$arr['last_access'].'</td><td align="left">'. get_user_class_name($arr['class']) . '</td><td align="center">'.$arr['country'].'</td></tr>';
}

echo '</table><p>'.$pagemenu.'<br>'.$browsemenu.'</p>';

stdfoot();
die;
}elseif($type == 'takeflush') {
	require_once("include/bittorrent.php");




loggedinorreturn();

$id = 0 + $_GET['id'];
int_check($id,true);

if (get_user_class() >= UC_MODERATOR OR $CURUSER[id] == "$id")
{  
   $deadtime = deadtime();
   sql_query("DELETE FROM peers WHERE last_action < FROM_UNIXTIME($deadtime) AND userid=" . $id);
   $effected = mysql_affected_rows();
   if($effected == 0){
   header("Refresh: 2; $DEFAULTBASEURL/userdetails.php?id=$id");
   echo "".stdhead("Redirecting")."".stdmsg("Redirect","Please wait we redirecting you, because you have no ghost torrents....")."".stdfoot()."";
   }else{
   header("Refresh: 4; $DEFAULTBASEURL/userdetails.php?id=$id");
   echo "".stdhead("Redirecting")."".stdmsg("Success", "$effected ghost torrent" . ($effected ? "s" : "") . "where sucessfully cleaned. Please wait, we redirect you back")."".stdfoot()."";
   }
}  
else
{
   bark("You can only clean your own ghost torrents");
}
}elseif($type == 'thanks') {
require_once("include/bittorrent.php");


loggedinorreturn();
parked();

$userid = $CURUSER["id"];
$torrentid = (int) $_POST["torrentid"];

if (empty($torrentid)) {
stdmsg("Error", "?? ??????? ???? ????????!");
}

$ajax = $_POST["ajax"];
if ($ajax == "yes") {
sql_query("INSERT INTO thanks (torrentid, userid) VALUES ($torrentid, $userid)") or sqlerr(__FILE__,__LINE__);
$count_sql = sql_query("SELECT COUNT(*) FROM thanks WHERE torrentid = $torrentid");
$count_row = mysql_fetch_array($count_sql);
$count = $count_row[0];

if ($count == 0) {
$thanksby = "None Yet";
} else {
$thanked_sql = sql_query("SELECT thanks.userid, users.username FROM thanks INNER JOIN users ON thanks.userid = users.id WHERE thanks.torrentid = $torrentid");
while ($thanked_row = mysql_fetch_assoc($thanked_sql)) {
if (($thanked_row["userid"] == $CURUSER["id"]) || ($thanked_row["userid"] == $row["owner"]))
$can_not_thanks = true;
//list($userid, $username) = $thanked_row;
$userid = $thanked_row["userid"];
$username = $thanked_row["username"];
$thanksby .= "<a href=\"userdetails.php?id=$userid\">$username</a>, ";
}
if ($thanksby)
$thanksby = substr($thanksby, 0, -2);
}
$thanksby = "<div id=\"ajax\"><form action=\"thanks.php\" method=\"post\">
<input class=button type=\"submit\" name=\"submit\" onclick=\"send(); return false;\" value=\"Thanks\"".($can_not_thanks ? " disabled" : "").">
<input type=\"hidden\" name=\"torrentid\" value=\"$torrentid\">".$thanksby."
</form></div>";
header ("Content-Type: text/html; charset=utf8");
print $thanksby;
} else {
$res = sql_query("INSERT INTO thanks (torrentid, userid) VALUES ($torrentid, $userid)") or sqlerr(__FILE__,__LINE__);
header("Location: $DEFAULTBASEURL/details.php?id=$torrentid&thanks=1");
}
}elseif($type == 'checkuser') {
require "include/bittorrent.php";

loggedinorreturn();

iplogger();
parked();
lang::load('checkuser');
$newpage = new page_verify();
$newpage->create('invite');
$id = 0 + $_GET["id"];
int_check($id,true);

$r = @sql_query("SELECT * FROM users WHERE status = 'pending' AND id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
$user = mysql_fetch_array($r) or bark(c2);

if (get_user_class() < UC_MODERATOR) {
	if ($user[invited_by] != $CURUSER[id])
		bark(c3);
}

if ($user["gender"] == "Male") $gender = "<img src=".$pic_base_url."male.png alt='Male' style='margin-left: 4pt'>";
elseif ($user["gender"] == "Female") $gender = "<img src=".$pic_base_url."female.png alt='Female' style='margin-left: 4pt'>";
elseif ($user["gender"] == "N/A") $gender = "<img src=".$pic_base_url."na.gif alt='N/A' style='margin-left: 4pt'>";

if ($user[added] == "0000-00-00 00:00:00")
  $joindate = 'N/A';
else
  $joindate = "$user[added] (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($user["added"])) . " ago)";
  
$res = sql_query("SELECT name,flagpic FROM countries WHERE id=$user[country] LIMIT 1") or sqlerr();
if (mysql_num_rows($res) == 1)
{
  $arr = mysql_fetch_assoc($res);
  $country = "<td class=embedded><img src=pic/flag/$arr[flagpic] alt=\"$arr[name]\" style='margin-left: 8pt'></td>";
}

stdhead(sprintf(c4,$user["username"]));

$enabled = $user["enabled"] == 'yes';
print("<p><table class=main border=0 cellspacing=0 cellpadding=0>".
"<tr><td class=embedded><h1 style='margin:0px'>$user[username]" . get_user_icons($user, true) . "</h1></td>$country</tr></table></p><br>\n");

if (!$enabled)
  print("<p><b>".c5."</b></p>\n");
?>
<table width=100% border=1 cellspacing=0 cellpadding=5>
<tr><td class=rowhead width=1%><?=c6?></td><td align=left width=99%><?=$joindate;?></td></tr>
<tr><td class=rowhead width=1%><?=c7?></td><td align=left width=99%><?=$gender;?></td></tr>
<tr><td class=rowhead width=1%><?=c8?></td><td align=left width=99%><a href=mailto:<?=$user[email];?>><?=$user[email];?></a></td></tr>
<?php
if (get_user_class() >= UC_MODERATOR AND $user[ip] != '')
	print ("<tr><td class=rowhead width=1%>".c9."</td><td align=left width=99%>$user[ip]</td></tr>");
print("<form method=post action=page.php?type=takeconfirm&id=".htmlspecialchars($id).">");
print("<input type=hidden name=email value=$user[email]>");
print("<tr><td class=rowhead width=1%><input type=\"checkbox\" name=\"conusr[]\" value=\"" . $id . "\" checked/></td>");
print("<td align=left width=99%><input type=submit value='".c10."' style='height: 20px'></form></tr></td></table>");
stdfoot();
}elseif($type == 'takeconfirm') {
	require_once("include/bittorrent.php");

$newpage = new page_verify();
$newpage->check('invite');
$id =  isset($_POST['id']) ? 0+$_POST['id'] : (isset($_GET['id']) ? 0+$_GET['id'] : die());
int_check($id,true);
$email = unesc(htmlspecialchars(trim($_POST["email"])));
sql_query("UPDATE users SET status = 'confirmed', editsecret = '' WHERE id IN (" . implode(", ", $_POST[conusr]) . ") AND status='pending'");

$message = <<<EOD
Hello,

Your account has been confirmed. You can now visit

$BASEURL/login.php

and use your login information to login in. We hope you'll read the FAQ's and Rules before you start sharing files.

Good luck and have fun on $SITENAME!


If you do not know the person who has invited you, please forward this email to $REPORTMAIL
------
Yours,
The $SITENAME Team.
EOD;
sent_mail($email,$SITENAME,$SITEEMAIL,"$SITENAME Account Confirmation",$message,"invite confirm",false);

header("Refresh: 0; url=invite.php?id=".htmlspecialchars($CURUSER[id]));
}elseif($type == 'previewpm') {
	require_once("include/bittorrent.php");

loggedinorreturn();
$msg = $_POST['msg'];
print ("<h2>Preview PM</h2>");
print("<table class=main width=100% border=1 cellspacing=0 cellpadding=5>\n");
print ("<tr><td align=left>".format_comment($msg)."</tr></td></table><br /><br />");
}elseif($type=='smilies') {
	require "include/bittorrent.php";

loggedinorreturn();
stdhead();
begin_main_frame();
insert_smilies_frame();
end_main_frame();
stdfoot();
}elseif($type=='moresmilies') {
require_once("include/bittorrent.php");?>
<html><head>
<title>more clickable smilies</title>
</head>
<BODY BGCOLOR="#ffffff" TEXT="#000000" LINK="#000000" VLINK="#000000">


<script language=javascript>

function SmileIT(smile,form,text){
   window.opener.document.forms[form].elements[text].value = window.opener.document.forms[form].elements[text].value+" "+smile+" ";
   window.opener.document.forms[form].elements[text].focus();
   window.close();
}
</script>

<table class="lista" width="100%" cellpadding="1" cellspacing="1">
<tr>
<?php

while ((list($code, $url) = each($smilies))) {
  if ($count % 3==0)
     print("\n<tr>");

     print("\n\t<td class=\"lista\" align=\"center\"><a href=\"javascript: SmileIT('".str_replace("'","\'",$code)."','".$_GET["form"]."','".$_GET["text"]."')\"><img border=0 src=pic/smilies/".$url."></a></td>");
     $count++;

  if ($count % 3==0)
     print("\n</tr>");
}

while ((list($code, $url) = each($privatesmilies))) {
  if ($count % 3==0)
     print("\n<tr>");

     print("\n\t<td class=\"lista\" align=\"center\"><a href=\"javascript: SmileIT('".str_replace("'","\'",$code)."','".$_GET["form"]."','".$_GET["text"]."')\"><img border=0 src=pic/smilies/".$url."></a></td>");
     $count++;

  if ($count % 3==0)
     print("\n</tr>");
}

?>
</tr>
</table>
<div align="center">
 <a href="javascript: window.close()"><? echo CLOSE; ?></a>
</div><?php
}elseif($type == 'commentable') {
	$id = $_REQUEST['id'];
	require "include/bittorrent.php";
		$subres = sql_query("SELECT COUNT(*) FROM comments WHERE torrent = $id");
	$subrow = mysql_fetch_array($subres);
	$count = $subrow[0];
			list($pagertop, $pagerbottom, $limit) = pager(20, $count, "details.php?id=$id&", array(lastpagedefault => 1));

		$subres = sql_query("SELECT comments.id, text, user, comments.added, editedby, editedat, avatar, warned, ".
                  "username, title, class, last_access, donor FROM comments LEFT JOIN users ON comments.user = users.id WHERE torrent = " .
                  "$id ORDER BY comments.id $limit") or sqlerr(__FILE__, __LINE__);
		$allrows = array();
		while ($subrow = mysql_fetch_array($subres))
			$allrows[] = $subrow;
		
		print($commentbar);
		print($pagertop);

		commenttable($allrows);

		print($pagerbottom);
}
?>