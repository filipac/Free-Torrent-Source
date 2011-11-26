<?php
DEFINE("version","v0.7");
define('IN_USERCP', true);
ob_start("ob_gzhandler");
require "include/bittorrent.php";

loggedinorreturn();
iplogger();
global $vkeysys;
if($vkeysys=='yes') { 
javascript('keyboard');
?>
<style>#keyboardInputMaster {
  position:absolute;
  border:2px groove #dddddd;
  color:#000000;
  background-color:#dddddd;
  text-align:left;
  z-index:1000000;
  width:auto;
}

#keyboardInputMaster thead tr th {
  text-align:left;
  padding:2px 5px 2px 4px;
  background-color:inherit;
  border:0px none;
}
#keyboardInputMaster thead tr th select,
#keyboardInputMaster thead tr th label {
  color:#000000;
  font:normal 11px Arial,sans-serif;
}
#keyboardInputMaster thead tr td {
  text-align:right;
  padding:2px 4px 2px 5px;
  background-color:inherit;
  border:0px none;
}
#keyboardInputMaster thead tr td span {
  padding:1px 4px;
  font:bold 11px Arial,sans-serif;
  border:1px outset #aaaaaa;
  background-color:#cccccc;
  cursor:pointer;
}
#keyboardInputMaster thead tr td span.pressed {
  border:1px inset #999999;
  background-color:#bbbbbb;
}

#keyboardInputMaster tbody tr td {
  text-align:left;
  margin:0px;
  padding:0px 4px 3px 4px;
}
#keyboardInputMaster tbody tr td div {
  text-align:center;
  position:relative;
  height:0px;
}
#keyboardInputMaster tbody tr td div#keyboardInputLayout {
  height:auto;
}
#keyboardInputMaster tbody tr td div#keyboardInputLayout table {
  height:20px;
  white-space:nowrap;
  width:100%;
  border-collapse:separate;
}
#keyboardInputMaster tbody tr td div#keyboardInputLayout table.keyboardInputCenter {
  width:auto;
  margin:0px auto;
}
#keyboardInputMaster tbody tr td div#keyboardInputLayout table tbody tr td {
  vertical-align:middle;
  padding:0px 5px 0px 5px;
  white-space:pre;
  font:normal 11px 'Lucida Console',monospace;
  border-top:1px solid #e5e5e5;
  border-right:1px solid #5d5d5d;
  border-bottom:1px solid #5d5d5d;
  border-left:1px solid #e5e5e5;
  background-color:#eeeeee;
  cursor:default;
}
#keyboardInputMaster tbody tr td div#keyboardInputLayout table tbody tr td.last {
  width:99%;
}
#keyboardInputMaster tbody tr td div#keyboardInputLayout table tbody tr td.alive {
  background-color:#ccccdd;
}
#keyboardInputMaster tbody tr td div#keyboardInputLayout table tbody tr td.target {
  background-color:#ddddcc;
}
#keyboardInputMaster tbody tr td div#keyboardInputLayout table tbody tr td.hover {
  border-top:1px solid #d5d5d5;
  border-right:1px solid #555555;
  border-bottom:1px solid #555555;
  border-left:1px solid #d5d5d5;
  background-color:#cccccc;
}
#keyboardInputMaster tbody tr td div#keyboardInputLayout table tbody tr td.pressed,
#keyboardInputMaster tbody tr td div#keyboardInputLayout table tbody tr td.dead {
  border-top:1px solid #555555;
  border-right:1px solid #d5d5d5;
  border-bottom:1px solid #d5d5d5;
  border-left:1px solid #555555;
  background-color:#cccccc;
}

#keyboardInputMaster tbody tr td div var {
  position:absolute;
  bottom:0px;
  right:0px;
  font:bold italic 11px Arial,sans-serif;
  color:#444444;
}

.keyboardInputInitiator {
  margin-left:3px;
  vertical-align:middle;
  cursor:pointer;
}</style><?php }
if($usergroups['canusercp'] != 'yes') ug();
$wherethisuser = where ($_SERVER["SCRIPT_FILENAME"],$CURUSER["id"]);


include "clientside/calendar/calendar.php";
$bday=new DHTML_Calendar('birthday');
print $bday->load_files();
$action = isset($_POST['action']) ? htmlspecialchars($_POST['action']) : (isset($_GET['action']) ? htmlspecialchars($_GET['action']) : '');
$type = isset($_POST['type']) ? htmlspecialchars($_POST['type']) : (isset($_GET['type']) ? htmlspecialchars($_GET['type']) : '');

$allowed_actions = array("personal","tracker","forum","security","referral");
if ($action)
	if (!in_array($action, $allowed_actions))
		stderr("Error", "Invalid Action");
	else {
		switch ($action) {
case "referral":
stdhead("Control Panel ".version."");
usercpmenu ("ref");
print ("<table border=0 cellspacing=0 cellpadding=5 width=100%>");
echo "You can give your friends the following url to earn some upload credits:<BR>";
echo <<<a
<a href="$BASEURL/signup.php?ref=$CURUSER[username]">$BASEURL/signup.php?ref=$CURUSER[username]</a>
a;
stdfoot();
die;
break;
case "personal":
stdhead("Control Panel ".version." - Personal Settings",true,"textarealimiter");

$countries = "<option value=0>---- None selected ----</option>\n";
$ct_r = sql_query("SELECT id,name FROM countries ORDER BY name") or die;
while ($ct_a = mysql_fetch_array($ct_r))
  $countries .= "<option value=".htmlspecialchars($ct_a[id])."" . (htmlspecialchars($CURUSER["country"]) == htmlspecialchars($ct_a['id']) ? " selected" : "") . ">".htmlspecialchars($ct_a[name])."</option>\n";
  
$downloadspeed = "<option value=0>---- None selected ----</option>\n";
$ds_a = sql_query("SELECT id,name FROM downloadspeed ORDER BY id") or die;
while ($ds_b = mysql_fetch_array($ds_a))
$downloadspeed .= "<option value=".htmlspecialchars($ds_b[id])."" . (htmlspecialchars($CURUSER["download"]) == htmlspecialchars($ds_b['id']) ? " selected" : "") . ">".htmlspecialchars($ds_b[name])."</option>\n";

$uploadspeed = "<option value=0>---- None selected ----</option>\n";
$us_a = sql_query("SELECT id,name FROM uploadspeed ORDER BY id") or die;
while ($us_b = mysql_fetch_array($us_a))
$uploadspeed .= "<option value=".htmlspecialchars($us_b[id])."" . (htmlspecialchars($CURUSER["upload"]) == htmlspecialchars($us_b['id']) ? " selected" : "") . ">".htmlspecialchars($us_b[name])."</option>\n"; 

ksort($tzs);
reset($tzs);
while (list($key, $val) = each($tzs)) {
if ($CURUSER["tzoffset"] == $key) {
   $timezone .= "<option value=\"".htmlspecialchars($key)."\" selected>".htmlspecialchars($val)."</option>\n";
} else {
   $timezone .= "<option value=\"".htmlspecialchars($key)."\">".htmlspecialchars($val)."</option>\n";
}
}

$clientselect = "<option value=0>---- None selected ----</option>n";
$cl_r = sql_query("SELECT id,name FROM clientselect ORDER BY name") or die;
while ($cl_a = mysql_fetch_array($cl_r))
$clientselect .= "<option value=".htmlspecialchars($cl_a[id])."" . (htmlspecialchars($CURUSER["clientselect"]) == htmlspecialchars($cl_a['id']) ? " selected" : "") . ">".htmlspecialchars($cl_a[name])."</option>n";  

$ra=mysql_query("SELECT * FROM `bitbucket` WHERE `public` = '1'");
$options='';
while ($sor=mysql_fetch_array($ra))
    {
    $text.='<option value="'.$BASEURL.'/fts-contents/bitbucket/'.$sor["name"].'">'.$sor["name"].'</option>
    ';
	}
    
usercpmenu ("personal");
print ("<table border=0 cellspacing=0 cellpadding=5 width=100%>");
if ($type == 'save') {
	$updateset = array();
	$parked = $_POST["parked"];
	$acceptpms = $_POST["acceptpms"];
	$deletepms = ($_POST["deletepms"] != "" ? "yes" : "no");
	$savepms = ($_POST["savepms"] != "" ? "yes" : "no");	
	$commentpm = $_POST["commentpm"];
	$subscription_pm = $_POST["subscription_pm"];
	$gender = $_POST["gender"];
	$country = $_POST["country"];
	$download = $_POST["download"];
	$upload = $_POST["upload"];
	$tzoffset = $_POST["tzoffset"];
	$dst = $_POST["dst"];
	$clientselect = $_POST["clientselect"];
	$bday = $_POST['birthday'];
		$avatar = $_POST["avatar"];
	if(preg_match("#^(http:\/\/[a-z0-9\-]+?\.([a-z0-9\-]+\.)*[a-z]+\/.*?\.(gif|jpg|png)$)#is", $avatar) && !eregi(".php",$avatar) && !eregi(".js",$avatar) && !eregi(".cgi",$avatar) OR eregi("http://localhost",$avatar)) {
        $avatar = htmlspecialchars( trim( $avatar ) );
        $updateset[] = "avatar = " . sqlesc($avatar);
    }
	$info = htmlspecialchars( trim($_POST["info"]) );
	$updateset[] = "birthday = ".sqlesc($bday) ;
	$updateset[] = "parked = " . sqlesc($parked);
	$updateset[] = "acceptpms = " . sqlesc($acceptpms);
	$updateset[] = "deletepms = " . sqlesc($deletepms);
	$updateset[] = "savepms = " . sqlesc($savepms);
	$updateset[] = "commentpm = " . sqlesc($commentpm);
	$updateset[] = "subscription_pm = " . sqlesc($subscription_pm);
	$updateset[] = "gender = " . sqlesc($gender);
	if (is_valid_id($country))
		$updateset[] = "country = " . sqlesc($country);
	if (is_valid_id($download))
		$updateset[] = "download =  " . sqlesc($download);
	if (is_valid_id($upload))
		$updateset[] = "upload =  " . sqlesc($upload);
	$updateset[] = "tzoffset = " . sqlesc($tzoffset);
	$updateset[] = "dst = " . sqlesc($dst);
	if (is_valid_id($clientselect))
		$updateset[] = "clientselect = " . sqlesc($clientselect);
	
	$icq = unesc($_POST["icq"]);
if (strlen($icq) > 10)
    bark("Sorry, Namber icq too long  (Max - 10)");
$updateset[] = "icq = " . sqlesc($icq);

$msn = unesc($_POST["msn"]);
if (strlen($msn) > 30)
    bark("Sorry, Yours msn too long  (Max - 30)");
$updateset[] = "msn = " . sqlesc($msn);

$aim = unesc($_POST["aim"]);
if (strlen($aim) > 30)
    bark("Sorry, Yours aim too long  (Max - 30)");
$updateset[] = "aim = " . sqlesc($aim);

$yahoo = unesc($_POST["yahoo"]);
if (strlen($yahoo) > 30)
    bark("Sorry, Yours yahoo too long   (Max - 30)");
$updateset[] = "yahoo = " . sqlesc($yahoo);

$skype = unesc($_POST["skype"]);
if (strlen($skype) > 20)
    bark("Sorry, Yours skype too long  (Max - 20)");
$updateset[] = "skype = " . sqlesc($skype);

$updateset[] = "pmbox = " . sqlesc($_POST['pmbox']);
	
	$updateset[] = "info = " . sqlesc($info);		
	
		$user = $CURUSER["id"];
		$query = sprintf("UPDATE users SET " . implode(",", $updateset) . " WHERE id ='%s'",
		mysql_real_escape_string($user));
		$result = sql_query($query);
		if (!$result)
			sqlerr(__FILE__,__LINE__);
		else
			header("Location: usercp.php?action=personal&type=saved");
		
}elseif ($type == 'saved')
	print("<tr><td colspan=2 class=\"heading\" valign=\"top\" align=\"center\"><font color=red>Saved!</font></td></tr>\n");
form ("personal");
   tr("Account parked",
"<input type=radio name=parked" . ($CURUSER["parked"] == "yes" ? " checked" : "") . " value=yes>yes
<input type=radio name=parked" .  ($CURUSER["parked"] == "no" ? " checked" : "") . " value=no>no
<br><font class=small size=1>You can park your account to prevent it from being deleted because of inactivity if you go away on for example a vacation. When the account has been parked limits are put on the account, for example you cannot use the tracker and browse some of the pages.</font>"
,1);
if(!empty($CURUSER['birthday'])) {
$age = explode('-',$CURUSER['birthday']);
tr('Your Age',getage($age[0],$age[1],$age[2])); }
tr('Birthday',$bday->make_input_field(array(),
array(
'value'       => $CURUSER['birthday']
)
),1);
tr("Accept PMs",
"<input type=radio name=acceptpms" . ($CURUSER["acceptpms"] == "yes" ? " checked" : "") . " value=yes>All (except blocks)
<input type=radio name=acceptpms" .  ($CURUSER["acceptpms"] == "friends" ? " checked" : "") . " value=friends>Friends only
<input type=radio name=acceptpms" .  ($CURUSER["acceptpms"] == "no" ? " checked" : "") . " value=no>Staff only"
,1);

tr("Delete PMs", "<input type=checkbox name=deletepms" . ($CURUSER["deletepms"] == "yes" ? " checked" : "") . "> (Default value for \"Delete PM on reply\")",1);
tr("Save PMs", "<input type=checkbox name=savepms" . ($CURUSER["savepms"] == "yes" ? " checked" : "") . "> (Default value for \"Save PM to Sentbox\")",1);
print("<tr class=tableb><td><b>PM on Comments</b></td>" .
"<td align=left><input type=radio name=commentpm" . ($CURUSER["commentpm"] == "yes" ? " checked" : "") . " value=yes>yes" .
"<input type=radio name=commentpm" .  ($CURUSER["commentpm"] == "no" ? " checked" : "") . " value=no>no" .
"<br><i><font class=small size=1><b>Note:</b> When somone comments you on a torrent you uploaded you will be notified.</i>" .
" <i>This default is yes.</i></font>");
tr("PM on Subscriptions ", "<input type=radio name=subscription_pm" . ($CURUSER["subscription_pm"] == "yes" ? " checked" : "") . " value=yes>yes" .
"<input type=radio name=subscription_pm" . ($CURUSER["subscription_pm"] == "no" ? " checked" : "") . " value=no>no<br> When someone posts in a subscribed thread, you will be PMed.",1);
tr("Show a window when i have a new message ", "<input type=radio name=pmbox" . ($CURUSER["pmbox"] == "yes" ? " checked" : "") . " value=yes>yes" .
"<input type=radio name=pmbox" . ($CURUSER["pmbox"] == "no" ? " checked" : "") . " value=no>no<br> When you have a new message, show a cool window to remind you that. Note that if you enable this, you won't see an reminder in page, only this BOX.",1);
tr("Gender",
"<input type=radio name=gender" . ($CURUSER["gender"] == "N/A" ? " checked" : "") . " value=N/A>N/A
<input type=radio name=gender" . ($CURUSER["gender"] == "Male" ? " checked" : "") . " value=Male>Male
<input type=radio name=gender" .  ($CURUSER["gender"] == "Female" ? " checked" : "") . " value=Female>Female"
,1);
tr("Country", "<select name=country>\n$countries\n</select>",1);
print("<tr><td colspan=\"2\" align=left><b>Youre Internet Speed</b></td></tr>\n");
tr("Download", "<select name=download>\n$downloadspeed\n</select>",1);
tr("Upload", "<select name=upload>\n$uploadspeed\n</select>",1);
tr("Time zone", "<select name=tzoffset>\n$timezone\n</select> Enable DST? <input type=radio name=dst" . ($CURUSER["dst"] == "yes" ? " checked" : "") . " value=yes> Yes <input type=radio name=dst" . ($CURUSER["dst"] == "no" ? " checked" : "") . " value=no> No<br />Be sure to select the correct time zone and be aware of Daylight Savings Time.(In the toolbar/forum/inbox)",1);
tr("BT Client", "<select name=clientselect>$clientselect</select>",1);
tr("Avatar URL", "<img src=".($CURUSER["avatar"] ? "'$CURUSER[avatar]'" : "'$BASEURL/pic/default_avatar.gif'")." name='avatarimg' onload=\"NcodeImageResizer.createOn(this);\"><br>
  <select name=savatar OnChange=javascript:document.forms[0].avatarimg.src=this.value;this.form.avatar.value=this.value;>
  <option value='$CURUSER[avatar]'>Choose an avatar</option>
  <option value='$BASEURL/pic/default_avatar.gif'>Nothing</option>
  $text
  </select><input name=avatar size=70 value=\"" . HANDLE::htmlspecialchars_uni($CURUSER["avatar"]) .
  "\"><br>\nWidth should be 150 pixels (will be resized if necessary). If you need a host for the picture, try the <a href=bitbucket-upload.php>bitbucket</a>.",1);
  print("<tr><td class=\"tablecat\" colspan=\"2\" align=left><b>Contacts</b></td></tr>\n");

tr(" ", "    <table cellSpacing=\"3\" cellPadding=\"0\" width=\"100%\" border=\"0\">
            <tr>
        <td style=\"font-size: 11px; font-style: normal; font-variant: normal; font-weight: normal; font-family: verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif\" colspan=2>
        If you want, that other visitors could contact quickly you, specify the data in following systems of fast messages</td>
      </tr>
      <tr>
        <td style=\"font-size: 11px; font-style: normal; font-variant: normal; font-weight: normal; font-family: verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif\">
        Namber ICQ<br>
        <img alt src=pic/contact/icq.gif width=\"17\" height=\"17\">
        <input maxLength=\"30\" size=\"25\" name=\"icq\" value=\"" . htmlspecialchars($CURUSER["icq"]) . "\" ></td>
        <td style=\"font-size: 11px; font-style: normal; font-variant: normal; font-weight: normal; font-family: verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif\">
        Name in AIM<br>
        <img alt src=pic/contact/aim.gif width=\"17\" height=\"17\">
        <input maxLength=\"30\" size=\"25\" name=\"aim\" value=\"" . htmlspecialchars($CURUSER["aim"]) . "\" ></td>
      </tr>
      <tr>
        <td style=\"font-size: 11px; font-style: normal; font-variant: normal; font-weight: normal; font-family: verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif\">
        Youre MSN<br>
        <img alt src=pic/contact/msn.gif width=\"17\" height=\"17\">
        <input maxLength=\"50\" size=\"25\" name=\"msn\" value=\"" . htmlspecialchars($CURUSER["msn"]) . "\" ></td>
        <td style=\"font-size: 11px; font-style: normal; font-variant: normal; font-weight: normal; font-family: verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif\">
        Name in Yahoo!<br>
        <img alt src=pic/contact/yahoo.gif width=\"17\" height=\"17\">
        <input maxLength=\"30\" size=\"25\" name=\"yahoo\" value=\"" . htmlspecialchars($CURUSER["yahoo"]) . "\" ></td>
      </tr>
      <tr>
        <td style=\"font-size: 11px; font-style: normal; font-variant: normal; font-weight: normal; font-family: verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif\">
        Name in Skype<br>
        <img alt src=pic/contact/skype.gif width=\"17\" height=\"17\">
        <input maxLength=\"32\" size=\"25\" name=\"skype\" value=\"" . htmlspecialchars($CURUSER["skype"]) . "\" ></td>
        <td style=\"font-size: 11px; font-style: normal; font-variant: normal; font-weight: normal; font-family: verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif\">
         </td>
      </tr>
    </table>",1);
    ?>
    <style type="text/css">

.progress{
	width: 1px;
	height: 14px;
	color: white;
	font-size: 12px;
  overflow: hidden;
	background-color: red;
	padding-left: 5px;
}

</style>
<script type="text/JavaScript">
function textCounter(field,counter,maxlimit,linecounter) {
	// text width//
	var fieldWidth =  parseInt(field.offsetWidth);
	var charcnt = field.value.length;        

	// trim the extra text
	if (charcnt > maxlimit) { 
		field.value = field.value.substring(0, maxlimit);
	}

	else { 
	// progress bar percentage
	var percentage = parseInt(100 - (( maxlimit - charcnt) * 100)/maxlimit) ;
	document.getElementById(counter).style.width =  parseInt((fieldWidth*percentage)/100)+"px";
	document.getElementById(counter).innerHTML="Limit: "+percentage+"%"
	// color correction on style from CCFFF -> CC0000
	setcolor(document.getElementById(counter),percentage,"background-color");
	}
}

function setcolor(obj,percentage,prop){
	obj.style[prop] = "rgb(80%,"+(100-percentage)+"%,"+(100-percentage)+"%)";
}

</script>
<?php
tr("Info", "<textarea name=\"info\" cols=\"60\" rows=\"4\" onKeyDown=\"textCounter(this,'progressbar1',225)\" onKeyUp=\"textCounter(this,'progressbar1',225)\" onFocus=\"textCounter(this,'progressbar1',225)\">" . HANDLE::htmlspecialchars_uni($CURUSER["info"]) . "</textarea><br>Displayed on your public page. May contain <a href=page.php?type=tags target=_new>BB codes</a> (Max. <b>225</b> characters)
<div id=\"progressbar1\" class=\"progress\"></div>
<script>textCounter(document.getElementById(\"maxcharfield\"),\"progressbar1\",225)</script>
", 1);

submit();
print("</table>");
stdfoot();
   die;
   break;
case "tracker":
stdhead("Control Panel ".version." - Tracker Settings");
usercpmenu ("tracker");
print ("<table border=0 cellspacing=0 cellpadding=5 width=100%>");
form ("tracker");
if ($type == 'save') {
	$updateset = array();
$pmnotif = $_POST["pmnotif"];
$emailnotif = $_POST["emailnotif"];
$notifs = ($pmnotif == 'yes' ? "[pm]" : "");
$notifs .= ($emailnotif == 'yes' ? "[email]" : "");
$r = sql_query("SELECT id FROM categories") or sqlerr();
$rows = mysql_num_rows($r);
for ($i = 0; $i < $rows; ++$i)
{
	$a = mysql_fetch_assoc($r);
	if ($_POST["cat$a[id]"] == 'yes')
	  $notifs .= "[cat$a[id]]";
}
$stylesheet = $_POST["template"];

$updateset[] = "notifs = " . sqlesc($notifs);
  $updateset[] = "skin = " . sqlesc($stylesheet);
$updateset[] = "torrentsperpage = " . min(100, 0 + $_POST["torrentsperpage"]);

		$user = $CURUSER["id"];
		$query = sprintf("UPDATE users SET " . implode(",", $updateset) . " WHERE id ='%s'",
		mysql_real_escape_string($user));
		$result = sql_query($query);
		if (!$result)
			sqlerr(__FILE__,__LINE__);
		else
			header("Location: usercp.php?action=tracker&type=saved");
}elseif ($type == 'saved')
	print("<tr><td colspan=2 class=\"heading\" valign=\"top\" align=\"center\"><font color=red>Saved!</font></td></tr>\n");

tr("Email notification", "<input type=checkbox name=pmnotif" . (strpos($CURUSER['notifs'], "[pm]") !== false ? " checked" : "") . " value=yes> Notify me when I have received a PM<br>\n" .
	 "<input type=checkbox name=emailnotif" . (strpos($CURUSER['notifs'], "[email]") !== false ? " checked" : "") . " value=yes> Notify me when a torrent is uploaded in one of <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; my default browsing categories.\n"
   , 1);
$r = sql_query("SELECT id,name FROM categories ORDER BY name") or sqlerr();
//$categories = "Default browsing categories:<br>\n";
if (mysql_num_rows($r) > 0)
{
	$categories .= "<table><tr>\n";
	$i = 0;
	while ($a = mysql_fetch_assoc($r))
	{
	  $categories .=  ($i && $i % 2 == 0) ? "</tr><tr>" : "";
	  $categories .= "<td class=bottom style='padding-right: 5px'><input name=cat$a[id] type=\"checkbox\" " . (strpos($CURUSER['notifs'], "[cat$a[id]]") !== false ? " checked" : "") . " value='yes'>&nbsp;" . htmlspecialchars($a["name"]) . "</td>\n";
	  ++$i;
	}
	$categories .= "</tr></table>\n";
}
tr("Browse default<br>categories",$categories,1);
$template_dirs =  dir_list($rootpath.'fts-contents/templates');
echo '<tr><td class="heading" valign="top" align="right">Template:</td>';
	echo '<td valign="top" align="left"><select name="template">';
	if (empty($template_dirs))
		$dirlist .= '<option value="">There is no template</option>';
	else {
		foreach ($template_dirs as $dir) {
			if(empty($CURUSER['skin']))
			$dirlist .= '<option value="'.$dir.'" '.($defaulttemplate == $dir ? 'selected' : '').'>'.$dir.'</option>';
			else
			$dirlist .= '<option value="'.$dir.'" '.($CURUSER['skin'] == $dir ? 'selected' : '').'>'.$dir.'</option>';
			}
	}
	echo $dirlist.'</select></td></tr>';
tr("Torrents per page", "<input type=text size=10 name=torrentsperpage value=$CURUSER[torrentsperpage]> (0=use default setting)",1);
submit();
print("</table>");
stdfoot();
   die;
   break;
case "forum":
stdhead("Control Panel ".version." - Forum Settings",true,"textarealimiter");
usercpmenu ("forum");
print ("<table border=0 cellspacing=0 cellpadding=5 width=100%>");
form ("forum");
if ($type == 'save') {
	$updateset = array();
	$avatars = ($_POST["avatars"] != "" ? "yes" : "no");	
	$signatures = ($_POST["signatures"] != "" ? "yes" : "no");
	$signature = htmlspecialchars( trim($_POST["signature"]) );
	
	$updateset[] = "topicsperpage = " . min(100, 0 + $_POST["topicsperpage"]);
	$updateset[] = "postsperpage = " . min(100, 0 + $_POST["postsperpage"]);
	$updateset[] = "avatars = " . sqlesc($avatars);
	$updateset[] = "signatures = " . sqlesc($signatures);
	$updateset[] = "signature = " . sqlesc($signature);
		
		$user = $CURUSER["id"];
		$query = sprintf("UPDATE users SET " . implode(",", $updateset) . " WHERE id ='%s'",
		mysql_real_escape_string($user));
		$result = sql_query($query);
		if (!$result)
			sqlerr(__FILE__,__LINE__);
		else
			header("Location: usercp.php?action=forum&type=saved");
}elseif ($type == 'saved')
	print("<tr><td colspan=2 class=\"heading\" valign=\"top\" align=\"center\"><font color=red>Saved!</font></td></tr>\n");

tr("Topics per page", "<input type=text size=10 name=topicsperpage value=$CURUSER[topicsperpage]> (0=use default setting)",1);
tr("Posts per page", "<input type=text size=10 name=postsperpage value=$CURUSER[postsperpage]> (0=use default setting)",1);
tr("View avatars", "<input type=checkbox name=avatars" . ($CURUSER["avatars"] == "yes" ? " checked" : "") . "> (Low bandwidth users might want to turn this off)",1);
tr("View Signatures", "<input type=checkbox name=signatures" . ($CURUSER["signatures"] == "yes" ? " checked" : "") . "> (Low bandwidth users might want to turn this off)",1);
?>
    <style type="text/css">

.progress{
	width: 1px;
	height: 14px;
	color: white;
	font-size: 12px;
  overflow: hidden;
	background-color: red;
	padding-left: 5px;
}

</style>
<script type="text/JavaScript">
function textCounter(field,counter,maxlimit,linecounter) {
	// text width//
	var fieldWidth =  parseInt(field.offsetWidth);
	var charcnt = field.value.length;        

	// trim the extra text
	if (charcnt > maxlimit) { 
		field.value = field.value.substring(0, maxlimit);
	}

	else { 
	// progress bar percentage
	var percentage = parseInt(100 - (( maxlimit - charcnt) * 100)/maxlimit) ;
	document.getElementById(counter).style.width =  parseInt((fieldWidth*percentage)/100)+"px";
	document.getElementById(counter).innerHTML="Limit: "+percentage+"%"
	// color correction on style from CCFFF -> CC0000
	setcolor(document.getElementById(counter),percentage,"background-color");
	}
}

function setcolor(obj,percentage,prop){
	obj.style[prop] = "rgb(80%,"+(100-percentage)+"%,"+(100-percentage)+"%)";
}

</script>
<?php
print("<tr class=\"heading\" valign=\"top\" align=\"right\"><td><b>Forum-Signature</b></td><td valign=\"top\" align=left><textarea name=\"signature\" cols=\"70\" rows=\"4\" onKeyDown=\"textCounter(this,'progressbar1',225)\" onKeyUp=\"textCounter(this,'progressbar1',225)\" onFocus=\"textCounter(this,'progressbar1',225)\">" . HANDLE::htmlspecialchars_uni($CURUSER[signature]) . "</textarea><br>Max. 225 characters. Max Image Size 500x100.
<div id=\"progressbar1\" class=\"progress\"></div>
<script>textCounter(document.getElementById(\"maxcharfield\"),\"progressbar1\",225)</script>
</td></tr>\n");
submit();
print("</table>");
stdfoot();   
   die;
   break;
case "security":
stdhead("Control Panel ".version." - Security Settings");
usercpmenu ("security");
print ("<table border=0 cellspacing=0 cellpadding=5 width=100%>");
if ($type == 'save') {
	print("<form method=post action=usercp.php><input type=hidden name=action value=security><input type=hidden name=type value=confirm>");
	$resetpasskey = $_POST["resetpasskey"];
	$email = mysql_real_escape_string( htmlspecialchars( trim($_POST["email"]) ));
	$chpassword = $_POST["chpassword"];
	$passagain = $_POST["passagain"];
	$privacy = $_POST["privacy"];
	if ($resetpasskey == 1)
		print("<input type=\"hidden\" name=\"resetpasskey\" value=\"1\">");
	print("<input type=\"hidden\" name=\"email\" value=\"$email\">");
	print("<input type=\"hidden\" name=\"chpassword\" value=\"$chpassword\">");
	print("<input type=\"hidden\" name=\"passagain\" value=\"$passagain\">");
	print("<input type=\"hidden\" name=\"privacy\" value=\"$privacy\">");
	Print("<tr><td class=\"heading\" valign=\"top\" align=\"center\">Security Check</td><td valign=\"top\" align=left><input type=password name=oldpassword ".($vkeysys == 'yes' ? "class='keyboardInput'" : "")."><br><font class=small><b>Note:</b> In order to change your security settings, you must enter your current password!</font></td></tr>\n");
	submit();
	stdfoot();
	die;
}elseif ($type == 'confirm') {
	$oldpassword = $_POST['oldpassword'];
	if (!$oldpassword){
		print("<tr><td colspan=2 class=\"heading\" valign=\"top\" align=\"center\"><font color=red>Please enter your password first!</font></td></tr>\n");
		goback();
		stdfoot();
		die;
	}elseif ($CURUSER["passhash"] != md5($CURUSER["secret"] . $oldpassword . $CURUSER["secret"])){
		print("<tr><td colspan=2 class=\"heading\" valign=\"top\" align=\"center\"><font color=red>You have entered a wrong password!</font></td></tr>\n");
		goback();
		stdfoot();
		die;
	}else
		$updateset = array();
		$changedemail = 0;
		$passupdated = 0;
		$privacyupdated = 0;
		$resetpasskey = $_POST["resetpasskey"];
		$email = mysql_real_escape_string( htmlspecialchars( trim($_POST["email"]) ));
		$chpassword = $_POST["chpassword"];
		$passagain = $_POST["passagain"];
		$privacy = $_POST["privacy"];
		
if ($chpassword != "") {
	if ($chpassword == $CURUSER["username"]) {
		print("<tr><td colspan=2 class=\"heading\" valign=\"top\" align=\"center\"><font color=red>Sorry, password cannot be same as user name.</font></td></tr>\n");
		goback("go back", "-2");
		stdfoot();
		die;
	}
	if (strlen($chpassword) > 40) {
		print("<tr><td colspan=2 class=\"heading\" valign=\"top\" align=\"center\"><font color=red>Sorry, password is too long (max is 40 chars)</font></td></tr>\n");
		goback("go back", "-2");
		stdfoot();
		die;
		}
	if (strlen($chpassword) < 6) {
		print("<tr><td colspan=2 class=\"heading\" valign=\"top\" align=\"center\"><font color=red>Sorry, password is too short (min is 6 chars)</font></td></tr>\n");
		goback("go back", "-2");
		stdfoot();
		die;
		}
	if ($chpassword != $passagain) {
		print("<tr><td colspan=2 class=\"heading\" valign=\"top\" align=\"center\"><font color=red>The passwords didn't match. Try again.</font></td></tr>\n");
		goback("go back", "-2");
		stdfoot();
		die;
		}

	$sec = mksecret();
	$passhash = md5($sec . $chpassword . $sec);
	$updateset[] = "secret = " . sqlesc($sec);
	$updateset[] = "passhash = " . sqlesc($passhash);
	if ($securelogin == "yes")
		logincookie($CURUSER["id"], md5($passhash.$_SERVER["REMOTE_ADDR"]),1,0x7fffffff,true);
	else
		logincookie($CURUSER["id"], md5($passhash.$_SERVER["REMOTE_ADDR"]));
	sessioncookie($CURUSER["id"], md5($passhash.$_SERVER["REMOTE_ADDR"]));
	$passupdated = 1;
}
if ($email != $CURUSER["email"]) {
	if (!validemail($email)){
		print("<tr><td colspan=2 class=\"heading\" valign=\"top\" align=\"center\"><font color=red>That doesn't look like a valid email address.</font></td></tr>\n");
		goback("go back", "-2");
		stdfoot();
		die;
		}
	$r = sql_query("SELECT id FROM users WHERE email=" . sqlesc($email)) or sqlerr();
	if (mysql_num_rows($r) > 0){
		print("<tr><td colspan=2 class=\"heading\" valign=\"top\" align=\"center\"><font color=red>The e-mail address is already in use.</font></td></tr>\n");
		goback("go back", "-2");
		stdfoot();
		die;
		}
	$changedemail = 1;
}
if ($resetpasskey == 1) {
			$passkey = md5($CURUSER['username'].get_date_time().$CURUSER['passhash']);
			$updateset[] = "passkey = " . sqlesc($passkey);
	}
if ($changedemail == 1) {
	$sec = mksecret();
	$hash = md5($sec . $email . $sec);
	$obemail = urlencode($email);
	$updateset[] = "editsecret = " . sqlesc($sec);
	$body = <<<EOD
You have requested that your user profile (username {$CURUSER["username"]})
on $SITENAME should be updated with this email address ($email) as
user contact.

If you did not do this, please ignore this email. The person who entered your
email address had the IP address {$_SERVER["REMOTE_ADDR"]}. Please do not reply.

To complete the update of your user profile, please follow this link:

$DEFAULTBASEURL/confirmemail.php/{$CURUSER["id"]}/$hash/$obemail

Your new email address will appear in your profile after you do this. Otherwise
your profile will remain unchanged.
------
Yours,
The $SITENAME Team.
EOD;

sent_mail($email,$SITENAME,$SITEEMAIL,"$SITENAME profile change confirmation",$body,"profile change",false);

}
if ($privacy != "normal" && $privacy != "low" && $privacy != "strong")
	die("whoops");

$updateset[] = "privacy = " . sqlesc($privacy);	
$privacyupdated = 1;

		$user = $CURUSER["id"];
		$query = sprintf("UPDATE users SET " . implode(",", $updateset) . " WHERE id ='%s'",
		mysql_real_escape_string($user));
		$result = sql_query($query);
		if (!$result)
			sqlerr(__FILE__,__LINE__);
		else
			$to = "usercp.php?action=security&type=saved";
		if ($changedemail == 1)
			$to .= "&mail=1";
		if ($resetpasskey == 1)
			$to .= "&passkey=1";
		if ($passupdated == 1)
			$to .= "&password=1";
		if ($privacyupdated == 1)
			$to .= "&privacy=1";
			
			header("Location: $to");
}
elseif ($type == 'saved')
	print("<tr><td colspan=2 class=\"heading\" valign=\"top\" align=\"center\"><font color=red>Saved!".($_GET["mail"] == "1" ? " (Confirmation email has been sent!)" : "")." ".($_GET["passkey"] == "1" ? " (The passkey has been updated!)" : "")." ".($_GET["password"] == "1" ? " (Your password has been changed!)" : "")." ".($_GET["privacy"] == "1" ? " (Your privacy level has been updated!)" : "")."</font></td></tr>\n");
form ("security");
if($usergroups['canrp'] == 'yes')
tr("Reset passkey","<input type=checkbox name=resetpasskey value=1 /><br><font class=small><b>Note:</b> In order to reset your current passkey, any active torrents must<br> be downloaded again to continue leeching/seeding.</font>", 1);
else
tr("Reset passkey",'Access denied. Contact an administrator.');
tr("Email address", "<input type=\"text\" name=\"email\" size=50 value=\"" . htmlspecialchars($CURUSER["email"]) . "\" /> <br><font class=small><b>Note:</b> In order to change your email address, you will receive another<br>confirmation email to your new address.</font>", 1);
tr("Change password", "<input type=\"password\" name=\"chpassword\" size=\"50\" ".($vkeysys == 'yes' ? "class='keyboardInput'" : "")."/>", 1);
tr("Type password again", "<input type=\"password\" name=\"passagain\" size=\"50\" ".($vkeysys == 'yes' ? "class='keyboardInput'" : "")."/>", 1);
tr("Privacy level",  priv("normal", "Normal") . " " . priv("low", "Low (email address will be shown)") . " " . priv("strong", "Strong (no info will be made available)"), 1);
submit();
print("</table>");
stdfoot();   
   die;
   break;
	}
}
//Rating Results
$res = sql_query("SELECT COUNT(*) FROM ratings WHERE user=" . $CURUSER["id"]) or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);
//Rating Results

//Comment Results
$res = sql_query("SELECT COUNT(*) FROM comments WHERE user=" . $CURUSER["id"]) or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);
//Comment Results

//Join Date
if ($CURUSER[added] == "0000-00-00 00:00:00")
  $joindate = 'N/A';
else
  $joindate = "$CURUSER[added] (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($CURUSER["added"])) . " ago)";
//Join Date

//Forum Posts
$res = sql_query("SELECT COUNT(*) FROM posts WHERE userid=" . $CURUSER[id]) or sqlerr(__FILE__, __LINE__);
$arr3 = mysql_fetch_row($res);
$forumposts = $arr3[0];
if ($forumposts)
{
	$seconds3 = mkprettytime(strtotime("now") - strtotime($CURUSER["added"]));
	$days = explode("d ", $seconds3);
	if(sizeof($days) > 1) {
		$dayposts   = round(($forumposts / $days[0]), 1);
	}
	$postcount = sql_query("SELECT sum(postcount) AS postcount FROM forums");
	$seconds = round($postcount/$forumposts, 3);	
}
//Forum Posts
stdhead("Control Panel ".version." - Home");
usercpmenu ();
?>
<table border="0" cellspacing="0" cellpadding="5" width=100%>
<?php
tr("Join date", $joindate, 1);
tr("Email address", $CURUSER["email"], 1);
if(!empty($CURUSER['birthday'])) {
$age = explode('-',$CURUSER['birthday']);
tr('Age',getage($age[0],$age[1],$age[2])); }
if ($CURUSER["avatar"])
	tr("Avatar", "<img src=\"" . $CURUSER["avatar"] . "\" border=0>", 1);
tr("Passkey", $CURUSER["passkey"], 1);
tr("Invitations","<a href=invite.php?id=$CURUSER[id]>$CURUSER[invites]</a>",1);
tr("Karma Points", "<a href=mybonus.php>$CURUSER[seedbonus]</a>", 1);
tr("Ratings submitted", $row[0], 1);
tr("Written comments", $row[0], 1);
if ($forumposts)
	tr("Forum Posts", "$forumposts ($dayposts posts per day / $seconds% of total forum posts)", 1);
?>
</table>
<table border=0 cellspacing=0 cellpadding=5 width=100%>
<?php
print("<td class=tabletitle><b>Recently Read Topics</b></td>");
?>
</table>
<?php
print("<table border=0 cellspacing=0 cellpadding=5 width=100%><tr>".
"<td class=colhead align=left>Topic Title</td>".
"<td class=colhead align=left>Replies</td>".
"<td class=colhead align=left>Topic Starter</td>".
"<td class=colhead align=left>Views</td>".

"<td class=colhead align=left>Last Post</td>".
"</tr>");
  $res = sql_query("SELECT * FROM readposts INNER JOIN topics ON topics.id = readposts.topicid WHERE readposts.userid = $CURUSER[id] ORDER BY readposts.id DESC LIMIT 5") or sqlerr();
  while ($topicarr = mysql_fetch_assoc($res))
{
$topicid = $topicarr["id"];
$topic_title = $topicarr["subject"];
$topic_userid = $topicarr["userid"];
$topic_views = $topicarr["views"];
$views = number_format($topic_views);

/// GETTING TOTAL NUMBER OF POSTS ///
$res = sql_query("SELECT COUNT(*) FROM posts WHERE topicid=$topicid") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_row($res);
$posts = $arr[0];
$replies = max(0, $posts - 1);

/// GETTING USERID AND DATE OF LAST POST ///
$res = sql_query("SELECT * FROM posts WHERE topicid=$topicid ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_assoc($res);
$postid = 0 + $arr["id"];
$userid = 0 + $arr["userid"];
$added = "<nobr>" . $arr["added"] . "</nobr>";

/// GET NAME OF LAST POSTER ///
$res = sql_query("SELECT id, username FROM users WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) == 1) {
$arr = mysql_fetch_assoc($res);
$username = "<a href=userdetails.php?id=$userid><b>$arr[username]</b></a>";
}
else
$username = "Unknown[$topic_userid]";

/// GET NAME OF THE AUTHOR ///
$res = sql_query("SELECT username FROM users WHERE id=$topic_userid") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) == 1) {
$arr = mysql_fetch_assoc($res);
$author = "<a href=userdetails.php?id=$topic_userid><b>$arr[username]</b></a>";
}
else
$author = "Unknown[$topic_userid]";

/// GETTING THE LAST INFO AND MAKE THE TABLE ROWS ///
$r = sql_query("SELECT lastpostread FROM readposts WHERE userid=$userid AND topicid=$topicid") or sqlerr(__FILE__, __LINE__);
$a = mysql_fetch_row($r);
$new = !$a || $postid > $a[0];
$subject = "<a href=$BASEURL/forums/viewtopic.php?topicid=$topicid><b>" . encodehtml($topicarr["subject"]) . "</b></a>";

print("<tr class=tableb><td style='padding-right: 3px'>$subject</td>".
"<td align=right>$replies</td>" .
"<td align=left>$author</td>" .
"<td align=right>$views</td>".

"<td align=left width=20%>$added<br>by&nbsp;$username</td>");


                }
?>
  </table>
</td>
</tr>
<?php
stdfoot();
?>