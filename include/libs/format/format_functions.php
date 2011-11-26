<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
/**
 * display_date_time()
 *
 * @return
 */
function display_date_time($timestamp = 0)
{
if ($timestamp)
   return date("Y-m-d H:i:s", $timestamp);
else
   return gmdate("Y-m-d H:i:s");
}
/**
 * send_message()
 *
 * @return
 */
function send_message($receiver,$message,$subject = '') {
	$dt = sqlesc(get_date_time());
	$rec = sqlesc($receiver);
	$msg = sqlesc($message);
	$subject = sqlesc($subject);
	mysql_query("INSERT INTO messages (sender, receiver, added, msg, subject, poster) VALUES(0, $rec, $dt, $msg, $subject, 0)") or sqlerr(__FILE__, __LINE__);
}
/**
 * get_user_timezone()
 *
 * @return
 */
function get_user_timezone($id = '') {	

if(!isset($id) || empty($id) || !is_valid_id($id))
	return "2"; //Default timezone
	
$query = mysql_query("SELECT * FROM users WHERE id=".sqlesc($id)." LIMIT 1");
if (mysql_num_rows($query) != "0")
	{
		$kasutaja = mysql_fetch_array($query);
		if($kasutaja['dst'] == 'no')
		$gutz = $kasutaja[tzoffset];
		else
		$gutz = $kasutaja[tzoffset] + 60;
		return $gutz;
	}else
		return "2"; //Default timezone
}

/**
 * get_row_count() 
 *
 * @return
 */
function get_row_count($table, $suffix = "")
{
  if ($suffix)
    $suffix = " $suffix";
  ($r = mysql_query("SELECT COUNT(*) FROM $table$suffix")) or die(mysql_error());
  ($a = mysql_fetch_row($r)) or die(mysql_error());
  return $a[0];
}
/**
 * stdmsg()
 *
 * @return
 */
function stdmsg($heading, $text, $htmlstrip = TRUE)
{
    if ($htmlstrip) {
        $heading = htmlspecialchars(trim($heading));
        $text = htmlspecialchars(trim($text));
    }
    print("<table class=main width=100% border=0 cellpadding=0 cellspacing=0><tr><td class=embedded>\n");
        if ($heading)
            print("<h2>$heading</h2>\n");
    print("<table width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text>\n");
    print($text . "</td></tr></table></td></tr></table>\n");
}

/**
 * stderr()
 *
 * @return
 */
function stderr($heading, $text, $htmlstrip = TRUE, $head = true, $foot = true, $die = true)
{
	if ($head) stdhead();
	
	stdmsg($heading, $text, $htmlstrip);
	
	if ($foot) stdfoot();
	
	if ($die) die;
}

/**
 * sqlerr()
 *
 * @return
 */
function sqlerr($file = '', $line = '')
{
  print("<table border=0 bgcolor=blue align=left cellspacing=0 cellpadding=10 style='background: blue'>" .
    "<tr><td class=embedded><font color=white><h1>SQL Error</h1>\n" .
  "<b>" . mysql_error() . ($file != '' && $line != '' ? "<p>in $file, line $line</p>" : "") . "</b></font></td></tr></table>");
  die;
}

// Returns the current time in GMT in MySQL compatible format.
/**
 * get_date_time()
 *
 * @return
 */
function get_date_time($timestamp = 0)
{
	global $_COOKIE,$_SESSION;
if ($timestamp)
	return date("Y-m-d H:i:s", $timestamp);
else 
	return date("Y-m-d H:i:s");
}
/**
 * get_timezone_date_time()
 *
 * @return
 */
function get_timezone_date_time($timestamp = 0)
{
	global $_COOKIE,$_SESSION;
if ($timestamp)
	return date("Y-m-d H:i:s", $timestamp);
else {
   $idcookie = base64($_COOKIE["c_secure_uid"],false);
   if (!$idcookie)
   	$idcookie = base64($_SESSION["s_secure_uid"],false);
  	$gtdtz = get_user_timezone($idcookie);
   return gmdate("Y-m-d H:i:s", time() + (60 * $gtdtz));
	}
}

/**
 * encodehtml()
 *
 * @return
 */
function encodehtml($s, $linebreaks = true)
{
  $s = str_replace("<", "&lt;", str_replace("&", "&", $s));
  if ($linebreaks)
    $s = nl2br($s);
  return $s;
}

/**
 * get_dt_num()
 *
 * @return
 */
function get_dt_num()
{
  return gmdate("YmdHis");
}

/**
 * format_urls()
 *
 * @return
 */
function format_urls($s)
{
	return preg_replace(
    	"/(\A|[^=\]'\"a-zA-Z0-9])((http|ftp|https|ftps|irc):\/\/[^()<>\s]+)/i",
	    "\\1<a href=\"\\2\">\\2</a>", $s);
}
/**
 * _strlastpos()
 *
 * @return
 */
function _strlastpos ($haystack, $needle, $offset = 0)
{
	$addLen = strlen ($needle);
	$endPos = $offset - $addLen;
	while (true)
	{
		if (($newPos = strpos ($haystack, $needle, $endPos + $addLen)) === false) break;
		$endPos = $newPos;
	}
	return ($endPos >= 0) ? $endPos : false;
}
/**
 * format_quotes()
 *
 * @return
 */
function format_quotes($s)
{
	preg_match_all('/\\[quote.*?\\]/', $s, $result, PREG_PATTERN_ORDER);
$openquotecount = count($openquote = $result[0]);
   preg_match_all('/\\[\/quote\\]/', $s, $result, PREG_PATTERN_ORDER);
$closequotecount = count($closequote = $result[0]);

   if ($openquotecount != $closequotecount) return $s; // quote mismatch. Return raw string...

   // Get position of opening quotes
$openval = array();
   $pos = -1;

   foreach($openquote as $val)
 $openval[] = $pos = strpos($s,$val,$pos+1);

   // Get position of closing quotes
   $closeval = array();
   $pos = -1;

   foreach($closequote as $val)
    $closeval[] = $pos = strpos($s,$val,$pos+1);


   for ($i=0; $i < count($openval); $i++)
 if ($openval[$i] > $closeval[$i]) return $s; // Cannot close before opening. Return raw string...


	$s = str_replace("[quote]","<fieldset><legend> Quote </legend><br>",$s);
   $s = preg_replace("/\\[quote=(.+?)\\]/", "<fieldset><legend> Quote: \\1 </legend><br>", $s);
   $s = str_replace("[/quote]","</fieldset><br>",$s);
   return $s;
}
//-----------------------------------------------------------//
//---- Function for BBcode [hide] [/hide] ----//
//-----------------------------------------------------------//
//---- Start ----//
//-----------------------------------------------------------//
/**
 * get_user_id()
 *
 * @return
 */
function get_user_id() {
global $CURUSER;
return $CURUSER["id"];
}
//Forum
/**
 * forum_hide()
 *
 * @return
 */
function forum_hide($text) {
$html = "<div align=\"left\"><div style=\"width: 100%; overflow: auto\">"
."<table width=\"100%\" cellspacing=\"1\" cellpadding=\"3\" border=\"0\" align=\"center\" class=\"bgcolor4\">"
."<tr bgcolor=\"#99CCFF\"><td><font class=\"block-title\">Hide Text</font></td></tr><tr bgcolor=\"#00FF00\"><td>"
."<center>---- Leave the comment to see the latent text! ----</center></td></tr></table></div></div>";
$start_html = "<div align=\"left\"><div style=\"width: 85%; overflow: auto\">"
."<table width=\"100%\" cellspacing=\"1\" cellpadding=\"3\" border=\"0\" align=\"center\" class=\"bgcolor4\">"
."<tr bgcolor=\"#99CCFF\"><td><font class=\"block-title\">Hide Text</font></td></tr><tr bgcolor=\"#00FF00\"><td>";
$end_html = "</td></tr></table></div></div>";
$id = 0 + $_GET["topicid"];
$res = mysql_query("SELECT userid FROM posts WHERE topicid=".$id." and userid=".get_user_id()."");
while ($row = mysql_fetch_array($res)){
$_user = $row['userid'];
}
if (get_user_class() >= UC_MODERATOR or $_user)
$text = preg_replace("#\[hide\](.*?)\[/hide\]#si", "".$start_html."\\1".$end_html."", $text);
else
$text = preg_replace("#\[hide\](.*?)\[/hide\]#si", "".$html."", $text);

return $text;
}
//Comment
/**
 * comment_hide()
 *
 * @return
 */
function comment_hide($text) {
$html = "<div align=\"left\"><div style=\"width: 100%; overflow: auto\">"
."<table width=\"100%\" cellspacing=\"1\" cellpadding=\"3\" border=\"0\" align=\"center\" class=\"bgcolor4\">"
."<tr bgcolor=\"#99CCFF\"><td><font class=\"block-title\">Hide Text</font></td></tr><tr class=\"bgcolor1\"><td>"
."<center>---- Leave the comment to see the latent text! ----</center></td></tr></table></div></div>";
$start_html = "<div align=\"left\"><div style=\"width: 85%; overflow: auto\">"
."<table width=\"100%\" cellspacing=\"1\" cellpadding=\"3\" border=\"0\" align=\"center\" class=\"bgcolor4\">"
."<tr bgcolor=\"#99CCFF\"><td><font class=\"block-title\">Hide Text</font></td></tr><tr class=\"bgcolor1\"><td>";
$end_html = "</td></tr></table></div></div>";
$id = 0 + $_GET["id"];
$res = mysql_query("SELECT user FROM comments WHERE torrent=".$id." and user=".get_user_id()."");
while ($row = mysql_fetch_array($res)){
$_user = $row['user'];
}
if (get_user_class() >= UC_MODERATOR or $_user)
$text = preg_replace("#\[hide\](.*?)\[/hide\]#si", "".$start_html."\\1".$end_html."", $text);
else
$text = preg_replace("#\[hide\](.*?)\[/hide\]#si", "".$html."", $text);

return $text;
}
//-----------------------------------------------------------//
//---- End ----//
//-----------------------------------------------------------//
/**
 * hit_count()
 *
 * @return
 */
function hit_count() {}
/**
 * hit_end()
 *
 * @return
 */
function hit_end() {}
/**
 * get_administrator_path()
 *
 * @param integer $return
 * @return
 */
function get_administrator_path($return = 1) {
	global $BASEURL;
	if($return)
	return "$BASEURL/administrator/";
	else
	echo "$BASEURL/administrator/";
}

//---------------------------------
//---- Search Highlight v0.1
//---------------------------------

/**
 * highlight()
 *
 * @return
 */
function highlight($search,$subject,$hlstart="<b><font color=red>",$hlend="</font></b>"
) {
    
    $srchlen=strlen($search);    // lenght of searched string
    if ($srchlen==0) return $subject;
    $find = $subject;
    while ($find = stristr($find,$search)) {    // find $search text in $subject -case insensitiv
        $srchtxt = substr($find,0,$srchlen);    // get new search text
        $find=substr($find,$srchlen);
        $subject = str_replace($srchtxt,"$hlstart$srchtxt$hlend",$subject);    // highlight founded case insensitive search text
    }
    return $subject;
}

//---------------------------------
//---- Search Highlight v0.1
//---------------------------------

/**
 * get_user_class()
 *
 * @return
 */
function get_user_class()
{
  global $CURUSER;
  return $CURUSER["class"];
}


/**
 * get_style()
 *
 * @return
 */
function get_style($class,$username) {
$r3et = mysql_query("SELECT id,usernamestyle FROM usergroups WHERE id = $class LIMIT 1");
while($t = mysql_fetch_assoc($r3et)) {
	return str_replace('{u}',$username,$t['usernamestyle']);
}

}
/**
 * get_user_class_name()
 *
 * @return
 */
function get_user_class_name($class) {
	$reet = mysql_query("SELECT id,title FROM usergroups WHERE id = $class LIMIT 1");
	while($r = mysql_fetch_assoc($reet)) {
		return $r['title'];
	}
}
/**
 * insert_legend()
 *
 * @return
 */
function insert_legend($withframe = 1,$collapse = 0) {
$query  = "SELECT id,title FROM usergroups ORDER BY id ASC";
$result = mysql_query($query);
if($withframe)
begin_frame('Legend',true,10,'100%');
if($collapse)
collapses('legendindex','<b>Legend</b>');
while($row = mysql_fetch_array($result, MYSQL_ASSOC))
{
    echo get_style($row['id'],$row['title']).' | ';
}
if($collapse)
collapsee();
if($withframe)
end_frame(); 
}
/**
 * is_valid_user_class()
 *
 * @return
 */
function is_valid_user_class($class)
{
	$q = sql_query("SELECT id FROM usergroups ORDER BY id DESC LIMIT 1");
	$biggest = mysql_fetch_assoc($q);
	$q = sql_query("SELECT id FROM usergroups ORDER BY id ASC LIMIT 1");
	$smallest = mysql_fetch_assoc($q);
  return is_numeric($class) && floor($class) == $class && $class >= $smallest[id] && $class <= $biggest[id];
}

//----------------------------------
//---- Security function v0.1
//----------------------------------
/**
 * int_check()
 *
 * @return
 */
function int_check($value,$stdhead = false, $stdfood = true, $die = true, $log = true) {
	global $CURUSER;
	$msg = "Invalid ID Attempt: Username: ".$CURUSER["username"]." - UserID: ".$CURUSER["id"]." - UserIP : ".IP::getip();
	if ( is_array($value) ) {
        foreach ($value as $val) int_check ($val);
    } else {
	    if (!is_valid_id($value)) {
		    if ($stdhead) {
			    if ($log)
		    		write_log($msg);
		    	stderr("ERROR","Invalid ID! For security reason, we have been logged this action.");
	    }else {
			    Print ("<h2>Error</h2><table width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text>");
				Print ("Invalid ID! For security reason, we have been logged this action.</td></tr></table>");
				if ($log)
					write_log($msg);
	    	}	    	
			
		    if ($stdfood)
		    	stdfoot();
		    if ($die)
		    	die;
	    }	    	
	    else
	    	return true;
    }
}
//----------------------------------
//---- Security function v0.1
//----------------------------------

/**
 * is_valid_id()
 *
 * @return
 */
function is_valid_id($id)
{
  return is_numeric($id) && ($id > 0) && (floor($id) == $id);
}


  //-------- Begins a main frame

/**
 * begin_main_frame()
 *
 * @return
 */
  function begin_main_frame($width = '737')
  {
    print("<table class=main width=$width border=0 cellspacing=0 cellpadding=0>" .
      "<tr><td class=embedded>\n");
  }

  //-------- Ends a main frame

/**
 * end_main_frame()
 *
 * @return
 */
  function end_main_frame()
  {
    print("</td></tr></table>\n");
  }

/**
 * begin_frame()
 *
 * @return
 */
  function begin_frame($caption = "", $center = false, $padding = 10, $width='737')
  {
    $tdextra = "";
    
    if ($caption)
      print("<h2>$caption</h2>\n");

    if ($center)
      $tdextra .= " align=center";

    print("<table width=$width border=1 cellspacing=0 cellpadding=$padding><tr><td$tdextra>\n");

  }

/**
 * attach_frame()
 *
 * @return
 */
  function attach_frame($padding = 10)
  {
    print("</td></tr><tr><td style='border-top: 0px'>\n");
  }

/**
 * end_frame()
 *
 * @return
 */
  function end_frame()
  {
    print("</td></tr></table>\n");
  }

/**
 * begin_table()
 *
 * @return
 */
  function begin_table($fullwidth = false, $padding = 5)
  {
    $width = "";
    
    if ($fullwidth)
      $width .= " width=100%";
    print("<table class=main$width border=1 cellspacing=0 cellpadding=$padding>\n");
  }

/**
 * end_table()
 *
 * @return
 */
  function end_table()
  {
    print("</td></tr></table>\n");
  }

  //-------- Inserts a smilies frame
  //         (move to globals)

/**
 * insert_smilies_frame()
 *
 * @return
 */
  function insert_smilies_frame()
  {
    global $smilies, $BASEURL;

    begin_frame("Smilies", true);

    begin_table(false, 5);

    print("<tr><td class=colhead>Type...</td><td class=colhead>To make a...</td></tr>\n");

    while (list($code, $url) = each($smilies))
      print("<tr><td>$code</td><td><img src=$BASEURL/pic/smilies/$url></td>\n");

    end_table();

    end_frame();
  }
/**
 * all_smilies()
 *
 * @return
 */
  function all_smilies() {
  	global $smilies,$BASEURL;
  	$i = 0;
	while (list($code, $url) = each($smilies)) {
		$smiliesperrow = 10 ;
    print ( ($i && $i % $smiliesperrow == 0) ? "<BR>" : "" ) ;
      print('<img src='.$BASEURL.'/pic/smilies/'.$url.' onclick="SmileIT(\''.$code.'\',\'shoutform\',\'shout\')">');
	  $i++; }
	  }

/**
 * sql_timestamp_to_unix_timestamp()
 *
 * @return
 * Last changed: 11.04.2008 
 */
function sql_timestamp_to_unix_timestamp($s)
{
  return strtotime($s);
}

/**
 * get_ratio_color()
 *
 * @return
 */
  function get_ratio_color($ratio)
  {
    if ($ratio < 0.1) return "#ff0000";
    if ($ratio < 0.2) return "#ee0000";
    if ($ratio < 0.3) return "#dd0000";
    if ($ratio < 0.4) return "#cc0000";
    if ($ratio < 0.5) return "#bb0000";
    if ($ratio < 0.6) return "#aa0000";
    if ($ratio < 0.7) return "#990000";
    if ($ratio < 0.8) return "#880000";
    if ($ratio < 0.9) return "#770000";
    if ($ratio < 1) return "#660000";
    return "#000000";
  }

/**
 * get_slr_color()
 *
 * @return
 */
  function get_slr_color($ratio)
  {
    if ($ratio < 0.025) return "#ff0000";
    if ($ratio < 0.05) return "#ee0000";
    if ($ratio < 0.075) return "#dd0000";
    if ($ratio < 0.1) return "#cc0000";
    if ($ratio < 0.125) return "#bb0000";
    if ($ratio < 0.15) return "#aa0000";
    if ($ratio < 0.175) return "#990000";
    if ($ratio < 0.2) return "#880000";
    if ($ratio < 0.225) return "#770000";
    if ($ratio < 0.25) return "#660000";
    if ($ratio < 0.275) return "#550000";
    if ($ratio < 0.3) return "#440000";
    if ($ratio < 0.325) return "#330000";
    if ($ratio < 0.35) return "#220000";
    if ($ratio < 0.375) return "#110000";
    return "#000000";
  }

/**
 * write_log()
 *
 * @return
 */
function write_log($text)
{
  $text = sqlesc($text);
  $added = sqlesc(get_date_time());
  mysql_query("INSERT INTO sitelog (added, txt) VALUES($added, $text)") or sqlerr(__FILE__, __LINE__);
}

/**
 * get_elapsed_time()
 *
 * @return
 */
function get_elapsed_time($ts)
{
  $mins = floor((gmtime() - $ts) / 60);
  $hours = floor($mins / 60);
  $mins -= $hours * 60;
  $days = floor($hours / 24);
  $hours -= $days * 24;
  $weeks = floor($days / 7);
  $days -= $weeks * 7;
  $t = "";
  if ($weeks > 0)
    return "$weeks week" . ($weeks > 1 ? "s" : "");
  if ($days > 0)
    return "$days day" . ($days > 1 ? "s" : "");
  if ($hours > 0)
    return "$hours hour" . ($hours > 1 ? "s" : "");
  if ($mins > 0)
    return "$mins min" . ($mins > 1 ? "s" : "");
  return "< 1 min";
}

/**
 * textbbcode()
 *
 * @return
 */
function textbbcode($form,$text,$content="",$message=false,$extratextarea="") {
	global $subject;
?>
<script language=javascript>
var b_open = 0;
var i_open = 0;
var u_open = 0;
var color_open = 0;
var list_open = 0;
var quote_open = 0;
var html_open = 0;
var left_open = 0;
var center_open = 0;
var right_open = 0;  

var myAgent = navigator.userAgent.toLowerCase();
var myVersion = parseInt(navigator.appVersion);

var is_ie = ((myAgent.indexOf("msie") != -1) && (myAgent.indexOf("opera") == -1));
var is_nav = ((myAgent.indexOf('mozilla')!=-1) && (myAgent.indexOf('spoofer')==-1)
&& (myAgent.indexOf('compatible') == -1) && (myAgent.indexOf('opera')==-1)
&& (myAgent.indexOf('webtv') ==-1) && (myAgent.indexOf('hotjava')==-1));

var is_win = ((myAgent.indexOf("win")!=-1) || (myAgent.indexOf("16bit")!=-1));
var is_mac = (myAgent.indexOf("mac")!=-1);
var bbtags = new Array();
function cstat() {
var c = stacksize(bbtags);
if ( (c < 1) || (c == null) ) {c = 0;}
if ( ! bbtags[0] ) {c = 0;}
document.<?=$form?>.tagcount.value = "Close last, Open tags "+c;
}
function stacksize(thearray) {
for (i = 0; i < thearray.length; i++ ) {
if ( (thearray[i] == "") || (thearray[i] == null) || (thearray == 'undefined') ) {return i;}
}
return thearray.length;
}
function pushstack(thearray, newval) {
arraysize = stacksize(thearray);
thearray[arraysize] = newval;
}
function popstackd(thearray) {
arraysize = stacksize(thearray);
theval = thearray[arraysize - 1];
return theval;
}
function popstack(thearray) {
arraysize = stacksize(thearray);
theval = thearray[arraysize - 1];
delete thearray[arraysize - 1];
return theval;
}
function closeall() {
if (bbtags[0]) {
while (bbtags[0]) {
tagRemove = popstack(bbtags)
if ( (tagRemove != 'color') ) {
doInsert("[/"+tagRemove+"]", "", false);
eval("document.<?=$form?>." + tagRemove + ".value = ' " + tagRemove + " '");
eval(tagRemove + "_open = 0");
} else {
doInsert("[/"+tagRemove+"]", "", false);
}
cstat();
return;
}
}
document.<?=$form?>.tagcount.value = "Close last, Open tags 0";
bbtags = new Array();
document.<?=$form?>.<?=$text?>.focus();
}
function add_code(NewCode) {
document.<?=$form?>.<?=$text?>.value += NewCode;
document.<?=$form?>.<?=$text?>.focus();
}
function alterfont(theval, thetag) {
if (theval == 0) return;
if(doInsert("[" + thetag + "=" + theval + "]", "[/" + thetag + "]", true)) pushstack(bbtags, thetag);
document.<?=$form?>.color.selectedIndex = 0;
cstat();
}
function tag_url() {
var FoundErrors = '';
var enterURL = prompt("You must enter a URL", "http://");
var enterTITLE = prompt("You must enter a title", "");
if (!enterURL || enterURL=="") {FoundErrors += " " + "You must enter a URL,";}
if (!enterTITLE) {FoundErrors += " " + "You must enter a title";}
if (FoundErrors) {alert("Error!"+FoundErrors);return;}
doInsert("[url="+enterURL+"]"+enterTITLE+"[/url]", "", false);
}
function tag_list() {
var FoundErrors = '';
var enterTITLE = prompt("Enter item of the list. For end of the list, press 'cancel' or leave the next field empty ", "");
if (!enterTITLE) {FoundErrors += " " + "Enter item of the list. For end of the list, press 'cancel' or leave the next field empty";}
if (FoundErrors) {alert("Error!"+FoundErrors);return;}
doInsert("[*]"+enterTITLE+"", "", false);

}
function tag_image() {
var FoundErrors = '';
var enterURL = prompt("You must enter a full image URL", "http://");
if (!enterURL || enterURL=="http://") {
alert("Error!"+"You must enter a full image URL");
return;
}
doInsert("[img]"+enterURL+"[/img]", "", false);
}
function tag_email() {
var emailAddress = prompt("You must enter a E-mail", "");
if (!emailAddress) {
alert("Error!"+"You must enter a E-mail");
return;
}
doInsert("[email]"+emailAddress+"[/email]", "", false);
}
function doInsert(ibTag, ibClsTag, isSingle)
{
var isClose = false;
var obj_ta = document.<?=$form?>.<?=$text?>;
if ( (myVersion >= 4) && is_ie && is_win) {
if(obj_ta.isTextEdit){
obj_ta.focus();
var sel = document.selection;
var rng = sel.createRange();
rng.colapse;
if((sel.type == "Text" || sel.type == "None") && rng != null){
if(ibClsTag != "" && rng.text.length > 0)
ibTag += rng.text + ibClsTag;
else if(isSingle) isClose = true;
rng.text = ibTag;
}
}
else{
if(isSingle) isClose = true;
obj_ta.value += ibTag;
}
} else {
if(isSingle) isClose = true;
obj_ta.value += ibTag;
}
obj_ta.focus();
// obj_ta.value = obj_ta.value.replace(/ /, " ");
return isClose;
}
function em(theSmilie)
{
doInsert(" " + theSmilie + " ", "", false);
}

function winop()
{<?global $BASEURL;?>
	var BASEURL = '<?=$BASEURL;?>'
windop = window.open(BASEURL + "/page.php?type=moresmilies&form=<?=$form?>&text=<?=$text?>","mywin","height=500,width=450,resizable=no,scrollbars=yes");
}

function simpletag(thetag)
{
var tagOpen = eval(thetag + "_open"); 
if (tagOpen == 0) {
if(doInsert("[" + thetag + "]", "[/" + thetag + "]", true))
{
eval(thetag + "_open = 1");
eval("document.<?=$form?>." + thetag + ".value += '*'");
pushstack(bbtags, thetag);
cstat();
}
}
else {
lastindex = 0;
for (i = 0; i < bbtags.length; i++ ) {
if ( bbtags[i] == thetag ) {
lastindex = i;
}
}

while (bbtags[lastindex]) {
tagRemove = popstack(bbtags);
doInsert("[/" + tagRemove + "]", "", false)
if ((tagRemove != 'COLOR') ){
eval("document.<?=$form?>." + tagRemove + ".value = ' " + tagRemove + " '");
eval(tagRemove + "_open = 0");
}
}
cstat();
}
}
</script>
<?php
print("<table width=100% cellspacing=0 cellpadding=5 border=0>\n");
?>

</tr>
<?php
if ($message) {
	?>
	<TR>
<TD align=left colspan=2><B>Subject:&nbsp;&nbsp;</B>
  <INPUT name="subject" type="text" size="102" value="<?=$subject?>"></TD>
</TR>
<?php
}
JsB::bbedit();
?>
<tr>
<td align=left><textarea name="<?=$text?>" id="<?=$text?>" class="markItUp" rows="20" cols="85" <?=$extratextarea?>><? echo $content; ?></textarea>
</td>
<td>
<table cellSpacing="1" cellPadding="3">
<tr>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':-)')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/smile1.gif width="18" height="18"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':smile:')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/smile2.gif width="18" height="18"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':-D')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/grin.gif width="18" height="18"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':w00t:')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/w00t.gif width="18" height="20"></a></td></tr><tr>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':-P')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/tongue.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(';-)')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/wink.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':-|')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/noexpression.gif width="18" height="18"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':-/')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/confused.gif width="18" height="18"></a></td></tr><tr>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':-(')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/sad.gif width="18" height="18"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':\'-(')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/cry.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':-O')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/ohmy.gif width="18" height="18"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em('|-)')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/sleeping.gif width="20" height="27"></a></td></tr><tr>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':innocent:')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/innocent.gif width="18" height="22"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':unsure:')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/unsure.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':closedeyes:')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/closedeyes.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':cool:')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/cool2.gif width="20" height="20"></a></td></tr><tr>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':thumbsdown:')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/thumbsdown.gif width="27" height="18"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':blush:')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/blush.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':yes:')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/yes.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':no:')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/no.gif width="20" height="20"></a></td></tr><tr>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':love:')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/love.gif width="19" height="19"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':?:')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/question.gif width="19" height="19"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':!:')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/excl.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':idea:')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/idea.gif width="19" height="19"></a></td></tr><tr>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':arrow:')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/arrow.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':arrow2:')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/arrow2.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':hmm:')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/hmm.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':hmmm:')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/hmmm.gif width="25" height="23"></a></td></tr><tr>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':huh:')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/huh.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':rolleyes:')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/rolleyes.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':kiss:')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/kiss.gif width="18" height="18"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: em(':shifty:')">
<img border=0 src=<?=$BASEURL?>/pic/smilies/shifty.gif width="20" height="20"></a></td></tr>
<td class=embedded style='padding: 2px; margin: 1px' colspan="4" align="center">
</head>
<body bgcolor="#EDEDED" text="#000000" link="#000000" topmargin="0" leftmargin="0">
</table>
<center>
<a href="javascript:winop();">More Smiles</a>
</td></tr></table>
</td>
<?php
}
/**
 * _f()
 *
 * @param mixed $text
 * @param bool $strip_html
 * @param bool $xssclean
 * @return
 */
function _f($text, $strip_html = true, $xssclean = true) {
	return format_comment($text,$strip_html,$xssclean);}

/**
 * dosql()
 *
 * @param mixed $sql
 * @return
 */
function dosql($sql) {
	return mysql_query($sql);
}
/**
 * boundary()
 *
 * @param mixed $w
 * @param mixed $s
 * @return
 */
function boundary($w,$s) {
	return preg_match("/\b$w\b/i", $s);
}
/**
 * Example: get XHTML from a given Textile-markup string ($string)
 *
 *        $textile = new Textile;
 *        echo $textile->TextileThis($string);
 *
 */

/*
$Id: classTextile.php 216 2006-10-17 22:31:53Z zem $
$LastChangedRevision: 216 $
*/

/*

_____________
T E X T I L E

A Humane Web Text Generator

Version 2.0

Copyright (c) 2003-2004, Dean Allen <dean@textism.com>
All rights reserved.

Thanks to Carlo Zottmann <carlo@g-blog.net> for refactoring
Textile's procedural code into a class framework

Additions and fixes Copyright (c) 2006 Alex Shiels http://thresholdstate.com/

_____________
L I C E N S E

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

* Redistributions of source code must retain the above copyright notice,
  this list of conditions and the following disclaimer.

* Redistributions in binary form must reproduce the above copyright notice,
  this list of conditions and the following disclaimer in the documentation
  and/or other materials provided with the distribution.

* Neither the name Textile nor the names of its contributors may be used to
  endorse or promote products derived from this software without specific
  prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
POSSIBILITY OF SUCH DAMAGE.

_________
U S A G E

Block modifier syntax:

    Header: h(1-6).
    Paragraphs beginning with 'hn. ' (where n is 1-6) are wrapped in header tags.
    Example: h1. Header... -> <h1>Header...</h1>

    Paragraph: p. (also applied by default)
    Example: p. Text -> <p>Text</p>

    Blockquote: bq.
    Example: bq. Block quotation... -> <blockquote>Block quotation...</blockquote>

    Blockquote with citation: bq.:http://citation.url
    Example: bq.:http://textism.com/ Text...
    ->  <blockquote cite="http://textism.com">Text...</blockquote>

    Footnote: fn(1-100).
    Example: fn1. Footnote... -> <p id="fn1">Footnote...</p>

    Numeric list: #, ##
    Consecutive paragraphs beginning with # are wrapped in ordered list tags.
    Example: <ol><li>ordered list</li></ol>

    Bulleted list: *, **
    Consecutive paragraphs beginning with * are wrapped in unordered list tags.
    Example: <ul><li>unordered list</li></ul>

Phrase modifier syntax:

           _emphasis_   ->   <em>emphasis</em>
           __italic__   ->   <i>italic</i>
             *strong*   ->   <strong>strong</strong>
             **bold**   ->   <b>bold</b>
         ??citation??   ->   <cite>citation</cite>
       -deleted text-   ->   <del>deleted</del>
      +inserted text+   ->   <ins>inserted</ins>
        ^superscript^   ->   <sup>superscript</sup>
          ~subscript~   ->   <sub>subscript</sub>
               @code@   ->   <code>computer code</code>
          %(bob)span%   ->   <span class="bob">span</span>

        ==notextile==   ->   leave text alone (do not format)

       "linktext":url   ->   <a href="url">linktext</a>
 "linktext(title)":url  ->   <a href="url" title="title">linktext</a>

           !imageurl!   ->   <img src="imageurl" />
  !imageurl(alt text)!  ->   <img src="imageurl" alt="alt text" />
    !imageurl!:linkurl  ->   <a href="linkurl"><img src="imageurl" /></a>

ABC(Always Be Closing)  ->   <acronym title="Always Be Closing">ABC</acronym>


Table syntax:

    Simple tables:

        |a|simple|table|row|
        |And|Another|table|row|

        |_. A|_. table|_. header|_.row|
        |A|simple|table|row|

    Tables with attributes:

        table{border:1px solid black}.
        {background:#ddd;color:red}. |{}| | | |


Applying Attributes:

    Most anywhere Textile code is used, attributes such as arbitrary css style,
    css classes, and ids can be applied. The syntax is fairly consistent.

    The following characters quickly alter the alignment of block elements:

        <  ->  left align    ex. p<. left-aligned para
        >  ->  right align       h3>. right-aligned header 3
        =  ->  centred           h4=. centred header 4
        <> ->  justified         p<>. justified paragraph

    These will change vertical alignment in table cells:

        ^  ->  top         ex. |^. top-aligned table cell|
        -  ->  middle          |-. middle aligned|
        ~  ->  bottom          |~. bottom aligned cell|

    Plain (parentheses) inserted between block syntax and the closing dot-space
    indicate classes and ids:

        p(hector). paragraph -> <p class="hector">paragraph</p>

        p(#fluid). paragraph -> <p id="fluid">paragraph</p>

        (classes and ids can be combined)
        p(hector#fluid). paragraph -> <p class="hector" id="fluid">paragraph</p>

    Curly {brackets} insert arbitrary css style

        p{line-height:18px}. paragraph -> <p style="line-height:18px">paragraph</p>

        h3{color:red}. header 3 -> <h3 style="color:red">header 3</h3>

    Square [brackets] insert language attributes

        p[no]. paragraph -> <p lang="no">paragraph</p>

        %[fr]phrase% -> <span lang="fr">phrase</span>

    Usually Textile block element syntax requires a dot and space before the block
    begins, but since lists don't, they can be styled just using braces

        #{color:blue} one  ->  <ol style="color:blue">
        # big                   <li>one</li>
        # list                  <li>big</li>
                                <li>list</li>
                               </ol>

    Using the span tag to style a phrase

        It goes like this, %{color:red}the fourth the fifth%
              -> It goes like this, <span style="color:red">the fourth the fifth</span>

*/

// define these before including this file to override the standard glyphs
@define('txt_quote_single_open',  '&#8216;');
@define('txt_quote_single_close', '&#8217;');
@define('txt_quote_double_open',  '&#8220;');
@define('txt_quote_double_close', '&#8221;');
@define('txt_apostrophe',         '&#8217;');
@define('txt_prime',              '&#8242;');
@define('txt_prime_double',       '&#8243;');
@define('txt_ellipsis',           '&#8230;');
@define('txt_emdash',             '&#8212;');
@define('txt_endash',             '&#8211;');
@define('txt_dimension',          '&#215;');
@define('txt_trademark',          '&#8482;');
@define('txt_registered',         '&#174;');
@define('txt_copyright',          '&#169;');

class Textile
{
    var $hlgn;
    var $vlgn;
    var $clas;
    var $lnge;
    var $styl;
    var $cspn;
    var $rspn;
    var $a;
    var $s;
    var $c;
    var $pnct;
    var $rel;
    var $fn;
    
    var $shelf = array();
    var $restricted = false;
    var $noimage = false;
    var $lite = true;
    var $url_schemes = array();
    var $glyph = array();
    var $hu = '';
    
    var $ver = '2.0.0';
    var $rev = '$Rev: 216 $';

// -------------------------------------------------------------
    function Textile()
    {
        $this->hlgn = "(?:\<(?!>)|(?<!<)\>|\<\>|\=|[()]+(?! ))";
        $this->vlgn = "[\-^~]";
        $this->clas = "(?:\([^)]+\))";
        $this->lnge = "(?:\[[^]]+\])";
        $this->styl = "(?:\{[^}]+\})";
        $this->cspn = "(?:\\\\\d+)";
        $this->rspn = "(?:\/\d+)";
        $this->a = "(?:{$this->hlgn}|{$this->vlgn})*";
        $this->s = "(?:{$this->cspn}|{$this->rspn})*";
        $this->c = "(?:{$this->clas}|{$this->styl}|{$this->lnge}|{$this->hlgn})*";

        $this->pnct = '[\!"#\$%&\'()\*\+,\-\./:;<=>\?@\[\\\]\^_`{\|}\~]';
        $this->urlch = '[\w"$\-_.+!*\'(),";\/?:@=&%#{}|\\^~\[\]`]';

        $this->url_schemes = array('http','https','ftp','mailto');

        $this->btag = array('bq', 'bc', 'notextile', 'pre', 'h[1-6]', 'fn\d+', 'p');

        $this->glyph = array(
           'quote_single_open'  => txt_quote_single_open,
           'quote_single_close' => txt_quote_single_close,
           'quote_double_open'  => txt_quote_double_open,
           'quote_double_close' => txt_quote_double_close,
           'apostrophe'         => txt_apostrophe,
           'prime'              => txt_prime,
           'prime_double'       => txt_prime_double,
           'ellipsis'           => txt_ellipsis,
           'emdash'             => txt_emdash,
           'endash'             => txt_endash,
           'dimension'          => txt_dimension,
           'trademark'          => txt_trademark,
           'registered'         => txt_registered,
           'copyright'          => txt_copyright,
        );

        if (defined('hu'))
            $this->hu = hu;

    }

// -------------------------------------------------------------
    function TextileThis($text, $lite='', $encode='', $noimage='', $strict='', $rel='')
    {
        if ($rel)
           $this->rel = ' rel="'.$rel.'" ';
        $this->lite = $lite;
        $this->noimage = $noimage;

        if ($encode) {
         $text = $this->incomingEntities($text);
            $text = str_replace("x%x%", "&#38;", $text);
            return $text;
        } else {

            if(!$strict) {
                $text = $this->cleanWhiteSpace($text);
            }

            $text = $this->getRefs($text);

            if (!$lite) {
                $text = $this->block($text);
            }

            $text = $this->retrieve($text);

                // just to be tidy
            $text = str_replace("<br />", "<br />\n", $text);

            return $text;
        }
    }

// -------------------------------------------------------------
    function TextileRestricted($text, $lite=1, $noimage=1, $rel='nofollow')
    {
        $this->restricted = true;
        $this->lite = $lite;
        $this->noimage = $noimage;
        if ($rel)
           $this->rel = ' rel="'.$rel.'" ';

            // escape any raw html
            $text = $this->encode_html($text, 0);

            $text = $this->cleanWhiteSpace($text);
            $text = $this->getRefs($text);

            if ($lite) {
                $text = $this->blockLite($text);
            }
            else {
                $text = $this->block($text);
            }

            $text = $this->retrieve($text);

                // just to be tidy
            $text = str_replace("<br />", "<br />\n", $text);

            return $text;
    }

// -------------------------------------------------------------
    function pba($in, $element = "") // "parse block attributes"
    {
        $style = '';
        $class = '';
        $lang = '';
        $colspan = '';
        $rowspan = '';
        $id = '';
        $atts = '';

        if (!empty($in)) {
            $matched = $in;
            if ($element == 'td') {
                if (preg_match("/\\\\(\d+)/", $matched, $csp)) $colspan = $csp[1];
                if (preg_match("/\/(\d+)/", $matched, $rsp)) $rowspan = $rsp[1];
            }

            if ($element == 'td' or $element == 'tr') {
                if (preg_match("/($this->vlgn)/", $matched, $vert))
                    $style[] = "vertical-align:" . $this->vAlign($vert[1]) . ";";
            }

            if (preg_match("/\{([^}]*)\}/", $matched, $sty)) {
                $style[] = rtrim($sty[1], ';') . ';';
                $matched = str_replace($sty[0], '', $matched);
            }

            if (preg_match("/\[([^]]+)\]/U", $matched, $lng)) {
                $lang = $lng[1];
                $matched = str_replace($lng[0], '', $matched);
            }

            if (preg_match("/\(([^()]+)\)/U", $matched, $cls)) {
                $class = $cls[1];
                $matched = str_replace($cls[0], '', $matched);
            }

            if (preg_match("/([(]+)/", $matched, $pl)) {
                $style[] = "padding-left:" . strlen($pl[1]) . "em;";
                $matched = str_replace($pl[0], '', $matched);
            }

            if (preg_match("/([)]+)/", $matched, $pr)) {
                // $this->dump($pr);
                $style[] = "padding-right:" . strlen($pr[1]) . "em;";
                $matched = str_replace($pr[0], '', $matched);
            }

            if (preg_match("/($this->hlgn)/", $matched, $horiz))
                $style[] = "text-align:" . $this->hAlign($horiz[1]) . ";";

            if (preg_match("/^(.*)#(.*)$/", $class, $ids)) {
                $id = $ids[2];
                $class = $ids[1];
            }

            if ($this->restricted)
                return ($lang)    ? ' lang="'    . $lang            .'"':'';

            return join('',array(
                ($style)   ? ' style="'   . join("", $style) .'"':'',
                ($class)   ? ' class="'   . $class           .'"':'',
                ($lang)    ? ' lang="'    . $lang            .'"':'',
                ($id)      ? ' id="'      . $id              .'"':'',
                ($colspan) ? ' colspan="' . $colspan         .'"':'',
                ($rowspan) ? ' rowspan="' . $rowspan         .'"':''
            ));
        }
        return '';
    }

// -------------------------------------------------------------
    function hasRawText($text)
    {
        // checks whether the text has text not already enclosed by a block tag
        $r = trim(preg_replace('@<(p|blockquote|div|form|table|ul|ol|pre|h\d)[^>]*?>.*</\1>@s', '', trim($text)));
        $r = trim(preg_replace('@<(hr|br)[^>]*?/>@', '', $r));
        return '' != $r;
    }

// -------------------------------------------------------------
    function table($text)
    {
        $text = $text . "\n\n";
        return preg_replace_callback("/^(?:table(_?{$this->s}{$this->a}{$this->c})\. ?\n)?^({$this->a}{$this->c}\.? ?\|.*\|)\n\n/smU",
           array(&$this, "fTable"), $text);
    }

// -------------------------------------------------------------
    function fTable($matches)
    {
        $tatts = $this->pba($matches[1], 'table');

        foreach(preg_split("/\|$/m", $matches[2], -1, PREG_SPLIT_NO_EMPTY) as $row) {
            if (preg_match("/^($this->a$this->c\. )(.*)/m", ltrim($row), $rmtch)) {
                $ratts = $this->pba($rmtch[1], 'tr');
                $row = $rmtch[2];
            } else $ratts = '';

                $cells = array();
            foreach(explode("|", $row) as $cell) {
                $ctyp = "d";
                if (preg_match("/^_/", $cell)) $ctyp = "h";
                if (preg_match("/^(_?$this->s$this->a$this->c\. )(.*)/", $cell, $cmtch)) {
                    $catts = $this->pba($cmtch[1], 'td');
                    $cell = $cmtch[2];
                } else $catts = '';

                $cell = $this->graf($this->span($cell));

                if (trim($cell) != '')
                    $cells[] = "\t\t\t<t$ctyp$catts>$cell</t$ctyp>";
            }
            $rows[] = "<tr$ratts>" . join("", $cells) . ($cells ? "" : "") . "</tr>";
            unset($cells, $catts);
        }
        return "<table$tatts>" . join("", $rows) . "</table>";
    }

// -------------------------------------------------------------
    function lists($text)
    {
        return preg_replace_callback("/^([#*]+$this->c .*)$(?![^#*])/smU", array(&$this, "fList"), $text);
    }

// -------------------------------------------------------------
    function fList($m)
    {
        $text = explode("\n", $m[0]);
        foreach($text as $line) {
            $nextline = next($text);
            if (preg_match("/^([#*]+)($this->a$this->c) (.*)$/s", $line, $m)) {
                list(, $tl, $atts, $content) = $m;
                $nl = '';
                if (preg_match("/^([#*]+)\s.*/", $nextline, $nm))
                	$nl = $nm[1];
                if (!isset($lists[$tl])) {
                    $lists[$tl] = true;
                    $atts = $this->pba($atts);
                    $line = "\t<" . $this->lT($tl) . "l$atts>\n\t\t<li>" . $this->graf($content);
                } else {
                    $line = "\t\t<li>" . $this->graf($content);
                }

                if(strlen($nl) <= strlen($tl)) $line .= "</li>";
                foreach(array_reverse($lists) as $k => $v) {
                    if(strlen($k) > strlen($nl)) {
                        $line .= "\n\t</" . $this->lT($k) . "l>";
                        if(strlen($k) > 1)
                            $line .= "</li>";
                        unset($lists[$k]);
                    }
                }
            }
            $out[] = $line;
        }
        return join("\n", $out);
    }

// -------------------------------------------------------------
    function lT($in)
    {
        return preg_match("/^#+/", $in) ? 'o' : 'u';
    }

// -------------------------------------------------------------
    function doPBr($in)
    {
        return preg_replace_callback('@<(p)([^>]*?)>(.*)(</\1>)@s', array(&$this, 'doBr'), $in);
    }

// -------------------------------------------------------------
    function doBr($m)
    {
        $content = preg_replace("@(.+)(?<!<br>|<br />)\n(?![#*\s|])@", '$1<br />', $m[3]);
        return '<'.$m[1].$m[2].'>'.$content.$m[4];
    }

// -------------------------------------------------------------
    function block($text)
    {
        $find = $this->btag;
        $tre = join('|', $find);

        $text = explode("\n\n", $text);

        $tag = 'p';
        $atts = $cite = $graf = $ext  = '';

        foreach($text as $line) {
            $anon = 0;
            if (preg_match("/^($tre)($this->a$this->c)\.(\.?)(?::(\S+))? (.*)$/s", $line, $m)) {
                // last block was extended, so close it
                if ($ext)
                    $out[count($out)-1] .= $c1;
                // new block
                list(,$tag,$atts,$ext,$cite,$graf) = $m;
                list($o1, $o2, $content, $c2, $c1) = $this->fBlock(array(0,$tag,$atts,$ext,$cite,$graf));

                // leave off c1 if this block is extended, we'll close it at the start of the next block
                if ($ext)
                    $line = $o1.$o2.$content.$c2;
                else
                    $line = $o1.$o2.$content.$c2.$c1;
            }
            else {
                // anonymous block
                $anon = 1;
                if ($ext or !preg_match('/^ /', $line)) {
                    list($o1, $o2, $content, $c2, $c1) = $this->fBlock(array(0,$tag,$atts,$ext,$cite,$line));
                    // skip $o1/$c1 because this is part of a continuing extended block
                    if ($tag == 'p') {
                        $line = $content;
                    }
                    else {
                        $line = $o2.$content.$c2;
                    }
                }
                else {
                   $line = $this->graf($line);
                }
            }

            $line = $this->doPBr($line);
            $line = preg_replace('/<br>/', '<br />', $line);

            if ($ext and $anon)
                $out[count($out)-1] .= "\n".$line;
            else
                $out[] = $line;

            if (!$ext) {
                $tag = 'p';
                $atts = '';
                $cite = '';
                $graf = '';
            }
        }
        if ($ext) $out[count($out)-1] .= $c1;
        return join("\n\n", $out);
    }



// -------------------------------------------------------------
    function fBlock($m)
    {
        // $this->dump($m);
        list(, $tag, $atts, $ext, $cite, $content) = $m;
        $atts = $this->pba($atts);

        $o1 = $o2 = $c2 = $c1 = '';

        if (preg_match("/fn(\d+)/", $tag, $fns)) {
            $tag = 'p';
            $fnid = empty($this->fn[$fns[1]]) ? $fns[1] : $this->fn[$fns[1]];
            $atts .= ' id="fn' . $fnid . '"';
            if (strpos($atts, 'class=') === false)
                $atts .= ' class="footnote"';
            $content = '<sup>' . $fns[1] . '</sup> ' . $content;
        }

        if ($tag == "bq") {
            $cite = $this->checkRefs($cite);
            $cite = ($cite != '') ? ' cite="' . $cite . '"' : '';
            $o1 = "\t<blockquote$cite$atts>\n";
            $o2 = "\t\t<p$atts>";
            $c2 = "</p>";
            $c1 = "\n\t</blockquote>";
        }
        elseif ($tag == 'bc') {
            $o1 = "<pre$atts>";
            $o2 = "<code$atts>";
            $c2 = "</code>";
            $c1 = "</pre>";
            $content = $this->shelve($this->encode_html(rtrim($content, "\n")."\n"));
        }
        elseif ($tag == 'notextile') {
            $content = $this->shelve($content);
            $o1 = $o2 = '';
            $c1 = $c2 = '';
        }
        elseif ($tag == 'pre') {
            $content = $this->shelve($this->encode_html(rtrim($content, "\n")."\n"));
            $o1 = "<pre$atts>";
            $o2 = $c2 = '';
            $c1 = "</pre>";
        }
        else {
            $o2 = "\t<$tag$atts>";
            $c2 = "</$tag>";
          }

        $content = $this->graf($content);

        return array($o1, $o2, $content, $c2, $c1);
    }

// -------------------------------------------------------------
    function graf($text)
    {
        // handle normal paragraph text
        if (!$this->lite) {
            $text = $this->noTextile($text);
            
        }

        $text = $this->links($text);
        if (!$this->noimage)
            $text = $this->image($text);

        if (!$this->lite) {
            $text = $this->lists($text);
            $text = $this->table($text);
        }

        $text = $this->span($text);
        $text = $this->footnoteRef($text);
        $text = $this->glyphs($text);
        return rtrim($text, "\n");
    }

// -------------------------------------------------------------
    function span($text)
    {
        $qtags = array('\*\*','\*','\?\?','-','__','_','%','\+','~','\^');
        $pnct = ".,\"'?!;:";

        foreach($qtags as $f) {
            $text = preg_replace_callback("/
                (?:^|(?<=[\s>$pnct])|([{[]))
                ($f)(?!$f)
                ({$this->c})
                (?::(\S+))?
                ([^\s$f]+|\S[^$f\n]*[^\s$f\n])
                ([$pnct]*)
                $f
                (?:$|([\]}])|(?=[[:punct:]]{1,2}|\s))
            /x", array(&$this, "fSpan"), $text);
        }
        return $text;
    }

// -------------------------------------------------------------
    function fSpan($m)
    {
        $qtags = array(
            '*'  => 'strong',
            '**' => 'b',
            '??' => 'cite',
            '_'  => 'em',
            '__' => 'i',
            '-'  => 'del',
            '%'  => 'span',
            '+'  => 'ins',
            '~'  => 'sub',
            '^'  => 'sup',
        );

        list(,, $tag, $atts, $cite, $content, $end) = $m;
        $tag = $qtags[$tag];
        $atts = $this->pba($atts);
        $atts .= ($cite != '') ? 'cite="' . $cite . '"' : '';

        $out = "<$tag$atts>$content$end</$tag>";

//      $this->dump($out);

        return $out;

    }

// -------------------------------------------------------------
    function links($text)
    {
        return preg_replace_callback('/
            (?:^|(?<=[\s>.$pnct\(])|([{[])) # $pre
            "                            # start
            (' . $this->c . ')           # $atts
            ([^"]+)                      # $text
            \s?
            (?:\(([^)]+)\)(?="))?        # $title
            ":
            ('.$this->urlch.'+)          # $url
            (\/)?                        # $slash
            ([^\w\/;]*)                  # $post
            (?:([\]}])|(?=\s|$|\)))
        /Ux', array(&$this, "fLink"), $text);
    }

// -------------------------------------------------------------
    function fLink($m)
    {
        list(, $pre, $atts, $text, $title, $url, $slash, $post) = $m;

        $url = $this->checkRefs($url);

        $atts = $this->pba($atts);
        $atts .= ($title != '') ? ' title="' . $this->encode_html($title) . '"' : '';

        if (!$this->noimage)
            $text = $this->image($text);

        $text = $this->span($text);
        $text = $this->glyphs($text);

        $url = $this->relURL($url);

        $out = '<a href="' . $this->encode_html($url . $slash) . '"' . $atts . $this->rel . '>' . $text . '</a>' . $post;

        // $this->dump($out);
        return $this->shelve($out);

    }

// -------------------------------------------------------------
    function getRefs($text)
    {
        return preg_replace_callback("/(?<=^|\s)\[(.+)\]((?:http:\/\/|\/)\S+)(?=\s|$)/U",
            array(&$this, "refs"), $text);
    }

// -------------------------------------------------------------
    function refs($m)
    {
        list(, $flag, $url) = $m;
        $this->urlrefs[$flag] = $url;
        return '';
    }

// -------------------------------------------------------------
    function checkRefs($text)
    {
        return (isset($this->urlrefs[$text])) ? $this->urlrefs[$text] : $text;
    }

// -------------------------------------------------------------
    function relURL($url)
    {
        $parts = parse_url($url);
        if ((empty($parts['scheme']) or @$parts['scheme'] == 'http') and
             empty($parts['host']) and
             preg_match('/^\w/', @$parts['path']))
            $url = $this->hu.$url;
        if ($this->restricted and !empty($parts['scheme']) and
              !in_array($parts['scheme'], $this->url_schemes))
            return '#';
        return $url;
    }

// -------------------------------------------------------------
    function image($text)
    {
        return preg_replace_callback("/
            (?:[[{])?          # pre
            \!                 # opening !
            (\<|\=|\>)??       # optional alignment atts
            ($this->c)         # optional style,class atts
            (?:\. )?           # optional dot-space
            ([^\s(!]+)         # presume this is the src
            \s?                # optional space
            (?:\(([^\)]+)\))?  # optional title
            \!                 # closing
            (?::(\S+))?        # optional href
            (?:[\]}]|(?=\s|$)) # lookahead: space or end of string
        /Ux", array(&$this, "fImage"), $text);
    }

// -------------------------------------------------------------
    function fImage($m)
    {
        list(, $algn, $atts, $url) = $m;
        $atts  = $this->pba($atts);
        $atts .= ($algn != '')  ? ' align="' . $this->iAlign($algn) . '"' : '';
        $atts .= (isset($m[4])) ? ' title="' . $m[4] . '"' : '';
        $atts .= (isset($m[4])) ? ' alt="'   . $m[4] . '"' : ' alt=""';
        $size = @getimagesize($url);
        if ($size) $atts .= " $size[3]";

        $href = (isset($m[5])) ? $this->checkRefs($m[5]) : '';
        $url = $this->checkRefs($url);

        $url = $this->relURL($url);

        $out = array(
            ($href) ? '<a href="' . $href . '">' : '',
            '<img src="' . $url . '"' . $atts . ' />',
            ($href) ? '</a>' : ''
        );

        return join('',$out);
    }

// -------------------------------------------------------------
    function code($text)
    {
        $text = $this->doSpecial($text, '<code>', '</code>', 'fCode');
        $text = $this->doSpecial($text, '@', '@', 'fCode');
        $text = $this->doSpecial($text, '<pre>', '</pre>', 'fPre');
        return $text;
    }

// -------------------------------------------------------------
    function fCode($m)
    {
      @list(, $before, $text, $after) = $m;
      if ($this->restricted)
          // $text is already escaped
            return $before.$this->shelve('<code>'.$text.'</code>').$after;
      else
            return $before.$this->shelve('<code>'.$this->encode_html($text).'</code>').$after;
    }

// -------------------------------------------------------------
    function fPre($m)
    {
      @list(, $before, $text, $after) = $m;
      if ($this->restricted)
          // $text is already escaped
            return $before.'<pre>'.$this->shelve($text).'</pre>'.$after;
      else
            return $before.'<pre>'.$this->shelve($this->encode_html($text)).'</pre>'.$after;
    }
// -------------------------------------------------------------
    function shelve($val)
    {
        $i = uniqid(rand());
        $this->shelf[$i] = $val;
        return $i;
    }

// -------------------------------------------------------------
    function retrieve($text)
    {
        if (is_array($this->shelf))
            do {
                $old = $text;
                $text = strtr($text, $this->shelf);
             } while ($text != $old);

        return $text;
    }

// -------------------------------------------------------------
// NOTE: deprecated
    function incomingEntities($text)
    {
        return preg_replace("/&(?![#a-z0-9]+;)/i", "x%x%", $text);
    }

// -------------------------------------------------------------
// NOTE: deprecated
    function encodeEntities($text)
    {
        return (function_exists('mb_encode_numericentity'))
        ?    $this->encode_high($text)
        :    htmlentities($text, ENT_NOQUOTES, "utf-8");
    }

// -------------------------------------------------------------
// NOTE: deprecated
    function fixEntities($text)
    {
        /*  de-entify any remaining angle brackets or ampersands */
        return str_replace(array("&gt;", "&lt;", "&amp;"),
            array(">", "<", "&"), $text);
    }

// -------------------------------------------------------------
    function cleanWhiteSpace($text)
    {
        $out = str_replace("\r\n", "\n", $text);
        $out = preg_replace("/\n{3,}/", "\n\n", $out);
        $out = preg_replace("/\n *\n/", "\n\n", $out);
        $out = preg_replace('/"$/', "\" ", $out);
        return $out;
    }

// -------------------------------------------------------------
    function doSpecial($text, $start, $end, $method='fSpecial')
    {
      return preg_replace_callback('/(^|\s|[[({>])'.preg_quote($start, '/').'(.*?)'.preg_quote($end, '/').'(\s|$|[\])}])?/ms',
            array(&$this, $method), $text);
    }

// -------------------------------------------------------------
    function fSpecial($m)
    {
        // A special block like notextile or code
      @list(, $before, $text, $after) = $m;
        return $before.$this->shelve($this->encode_html($text)).$after;
    }

// -------------------------------------------------------------
    function noTextile($text)
    {
         $text = $this->doSpecial($text, '<notextile>', '</notextile>', 'fTextile');
         return $this->doSpecial($text, '==', '==', 'fTextile');

    }

// -------------------------------------------------------------
    function fTextile($m)
    {
        @list(, $before, $notextile, $after) = $m;
        #$notextile = str_replace(array_keys($modifiers), array_values($modifiers), $notextile);
        return $before.$this->shelve($notextile).$after;
    }

// -------------------------------------------------------------
    function footnoteRef($text)
    {
        return preg_replace('/\b\[([0-9]+)\](\s)?/Ue',
            '$this->footnoteID(\'\1\',\'\2\')', $text);
    }

// -------------------------------------------------------------
    function footnoteID($id, $t)
    {
        if (empty($this->fn[$id]))
            $this->fn[$id] = uniqid(rand());
        $fnid = $this->fn[$id];
        return '<sup class="footnote"><a href="#fn'.$fnid.'">'.$id.'</a></sup>'.$t;
    }

// -------------------------------------------------------------
    function glyphs($text)
    {
        // fix: hackish
        $text = preg_replace('/"\z/', "\" ", $text);
        $pnc = '[[:punct:]]';

        $glyph_search = array(
            '/(\w)\'(\w)/',                                      // apostrophe's
            '/(\s)\'(\d+\w?)\b(?!\')/',                          // back in '88
            '/(\S)\'(?=\s|'.$pnc.'|<|$)/',                       //  single closing
            '/\'/',                                              //  single opening
            '/(\S)\"(?=\s|'.$pnc.'|<|$)/',                       //  double closing
            '/"/',                                               //  double opening
            '/\b([A-Z][A-Z0-9]{2,})\b(?:[(]([^)]*)[)])/',        //  3+ uppercase acronym
            '/\b([A-Z][A-Z\'\-]+[A-Z])(?=[\s.,\)>])/',           //  3+ uppercase
            '/\b( )?\.{3}/',                                     //  ellipsis
            '/(\s?)--(\s?)/',                                    //  em dash
            '/\s-(?:\s|$)/',                                     //  en dash
            '/(\d+)( ?)x( ?)(?=\d+)/',                           //  dimension sign
            '/\b ?[([]TM[])]/i',                                 //  trademark
            '/\b ?[([]R[])]/i',                                  //  registered
            '/\b ?[([]C[])]/i',                                  //  copyright
         );

        extract($this->glyph, EXTR_PREFIX_ALL, 'txt');

        $glyph_replace = array(
            '$1'.$txt_apostrophe.'$2',           // apostrophe's
            '$1'.$txt_apostrophe.'$2',           // back in '88
            '$1'.$txt_quote_single_close,        //  single closing
            $txt_quote_single_open,              //  single opening
            '$1'.$txt_quote_double_close,        //  double closing
            $txt_quote_double_open,              //  double opening
            '<acronym title="$2">$1</acronym>',  //  3+ uppercase acronym
            '<span class="caps">$1</span>',      //  3+ uppercase
            '$1'.$txt_ellipsis,                  //  ellipsis
            '$1'.$txt_emdash.'$2',               //  em dash
            ' '.$txt_endash.' ',                 //  en dash
            '$1$2'.$txt_dimension.'$3',          //  dimension sign
            $txt_trademark,                      //  trademark
            $txt_registered,                     //  registered
            $txt_copyright,                      //  copyright
         );

         $text = preg_split("/(<.*>)/U", $text, -1, PREG_SPLIT_DELIM_CAPTURE);
         foreach($text as $line) {
             if (!preg_match("/<.*>/", $line)) {
                 $line = preg_replace($glyph_search, $glyph_replace, $line);
             }
              $glyph_out[] = $line;
         }
         return join('', $glyph_out);
    }

// -------------------------------------------------------------
    function iAlign($in)
    {
        $vals = array(
            '<' => 'left',
            '=' => 'center',
            '>' => 'right');
        return (isset($vals[$in])) ? $vals[$in] : '';
    }

// -------------------------------------------------------------
    function hAlign($in)
    {
        $vals = array(
            '<'  => 'left',
            '='  => 'center',
            '>'  => 'right',
            '<>' => 'justify');
        return (isset($vals[$in])) ? $vals[$in] : '';
    }

// -------------------------------------------------------------
    function vAlign($in)
    {
        $vals = array(
            '^' => 'top',
            '-' => 'middle',
            '~' => 'bottom');
        return (isset($vals[$in])) ? $vals[$in] : '';
    }

// -------------------------------------------------------------
// NOTE: deprecated
    function encode_high($text, $charset = "UTF-8")
    {
        return mb_encode_numericentity($text, $this->cmap(), $charset);
    }

// -------------------------------------------------------------
// NOTE: deprecated
    function decode_high($text, $charset = "UTF-8")
    {
        return mb_decode_numericentity($text, $this->cmap(), $charset);
    }

// -------------------------------------------------------------
// NOTE: deprecated
    function cmap()
    {
        $f = 0xffff;
        $cmap = array(
            0x0080, 0xffff, 0, $f);
        return $cmap;
    }

// -------------------------------------------------------------
    function encode_html($str, $quotes=1)
    {
        $a = array(
            '&' => '&#38;',
            '<' => '&#60;',
            '>' => '&#62;',
        );
        if ($quotes) $a = $a + array(
            "'" => '&#39;',
            '"' => '&#34;',
        );

        return strtr($str, $a);
    }

// -------------------------------------------------------------
    function textile_popup_help($name, $helpvar, $windowW, $windowH)
    {
        return ' <a target="_blank" href="http://www.textpattern.com/help/?item=' . $helpvar . '" onclick="window.open(this.href, \'popupwindow\', \'width=' . $windowW . ',height=' . $windowH . ',scrollbars,resizable\'); return false;">' . $name . '</a><br />';

        return $out;
    }

// -------------------------------------------------------------
// NOTE: deprecated
    function txtgps($thing)
    {
        if (isset($_POST[$thing])) {
            if (get_magic_quotes_gpc()) {
                return stripslashes($_POST[$thing]);
            }
            else {
                return $_POST[$thing];
            }
        }
        else {
            return '';
        }
    }

// -------------------------------------------------------------
// NOTE: deprecated
    function dump()
    {
        foreach (func_get_args() as $a)
            echo "\n<pre>",(is_array($a)) ? print_r($a) : $a, "</pre>\n";
    }

// -------------------------------------------------------------

    function blockLite($text)
    {
        $this->btag = array('bq', 'p');
        return $this->block($text."\n\n");
    }


} // end class
?>