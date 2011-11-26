<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
/**
 * Ffactory
 *
 * @package Free Torrent Source
 * @author Filip
 * @copyright 2008
 * @version $Id$
 * @access public
 */
class Ffactory
{
  /**
   * Ffactory::xss_clean()
   *
   * @param mixed $var
   * @return
   */
    public function xss_clean( &$var )
    {
        static $preg_find = array( '#javascript#i', '#vbscript#i' ), $preg_replace =
            array( 'java script', 'vb script' ) ;

        $var = preg_replace( $preg_find, $preg_replace, HANDLE::htmlspecialchars_uni($var) ) ;
        return apply_filters("xss_clean",$var) ;
    }
  /**
   * Ffactory::local_user()
   *
   * @return
   */
    public function local_user()
    {
        return $_SERVER["SERVER_ADDR"] == $_SERVER["REMOTE_ADDR"] ;
    }
  /**
   * Ffactory::initadmin()
   *
   * @return
   */
    public function initadmin()
    {
        HANDLE::Freq( 'libs.administrator', 'admin' ) ;
    }
    
  /**
   * Ffactory::totaltime()
   *
   * @return
   */
    public function totaltime() {
	$mtime = explode(' ', microtime());
global $starttime;
$totaltime = $mtime[0] + $mtime[1] - $starttime;
return sprintf('%.3f', $totaltime);
}
  /**
   * Ffactory::executedinandqueries()
   *
   * @return
   */
	public function executedinandqueries() {
		
		return sprintf("%s <b> %.3f </b>%s",lang_executedin,ffactory::totaltime(),strtolower(lang_seconds)).sprintf(" %s <b>".($_SESSION["queries"] ? $_SESSION["queries"] : "0")."</b> %s!]",lang_with,lang_queries);
	}
  /**
   * Ffactory::license()
   *
   * @return
   */
	public function license() {
		return '';
	}
  /**
   * Ffactory::destroy_q()
   *
   * @return void
   */
	public function destroy_q() {
		unset($_SESSION['queries']);
	}
  /**
   * Ffactory::smallcopyright()
   *
   * @return
   */
	public function smallcopyright() {
		$yearnow = date("Y");
		$yearpast = $yearnow-1;
		return apply_filters("smallcopyright","(c) Free Torrent Source LLC $yearpast-$yearnow");
	} 
	
  /**
   * Ffactory::configoption()
   *
   * @param mixed $op
   * @param mixed $def
   * @return
   */
	static function configoption($op,$def) {
		return (!empty($op) ? $op : $def);
	}
	
  /**
   * Ffactory::admincss()
   *
   * @return void
   */
	public function admincss() {
		echo apply_filters("admincss",'<link rel="stylesheet" href="controlpanel.css" type="text/css">');
	}
  /**
   * Ffactory::cut()
   * 
   * @param mixed $str
   * @param integer $len
   * @return
   */
	public function cut($str,$len=10) {
    return (strlen($str)>$len ? substr($str,0,$len-4) .'...':$str);
	}
	public function lastxtorrentsshow() {

		global $showlastxtorrents;
if ($showlastxtorrents == "yes") {
//Start of Last X torrents mod
global $howmuchtorrents,$thowshow;

	echo'<BR>';
	collapses('lasttorrents',"<b>Last $howmuchtorrents Torrent Uploads</b>");
	if($thowshow == 'text') {
$sql = "SELECT * FROM torrents where visible='yes' ORDER BY added DESC LIMIT $howmuchtorrents";
$result = sql_query($sql) or die('No torrents found');
if( mysql_num_rows($result) != 0 )
{


print'<table width=100% border=1 cellspacing=0 cellpadding=5>';
print'<tr class=thead>';
print"<td class=subheader><center><b>Name</b></center></td>";
print'<td class=subheader><center><b>Seeder</b></center></td>';
print'<td class=subheader><center><b>Leecher</b></center></td>';
print'</tr>';

while( $row = mysql_fetch_assoc($result) )
{
	if(get_user_class() >= $arr["minclassread"]):
print'<tr>';
print'<a href="details.php?id=' . $row['id'] . '&hit=1"><td><a href="details.php?id=' . $row['id'] . '&hit=1"><b>' . $row['name'] . '</b></td></a>';
print'<td align=left>&nbsp;&nbsp;&nbsp;&nbsp;' . $row['seeders'] . '</td>';
print'<td align=left>&nbsp;&nbsp;&nbsp;&nbsp;' . $row['leechers'] . '</td>';

print'</tr>';
endif;
}
print'</table>';
}else echo'No torrents to show'; }
elseif($thowshow == 'withimg') {
	$sql = "SELECT * FROM torrents where visible='yes' AND imageurl != '' ORDER BY added DESC LIMIT $howmuchtorrents";
	$result = sql_query($sql) or die('No torrents found');
if( mysql_num_rows($result) != 0 )
{
	$i_count = $i_done = 0;
		echo '<table width=100%>
<tr>';
while( $t = mysql_fetch_assoc($result) ) {

	if ($i_count > 0 && $i_count % 4 == 0)
					{
						echo '
						</tr>
						<tr>';
					}
echo <<<eo
						<td align="center" class="tcat">
							<a href="$BASEURL/details.php?id=$t[id]"><img src="$t[imageurl]" width="125" height="125" alt="$t[name]" title="$t[name]"/></a>
						</td>

eo;
$i_count++;
}
echo '</tr>';
print '</table>';
}else echo'No torrents to show';
}
collapsee();
}
//End of Last X torrents mod
	}
	public function _is_writable() {
//will work in despite of Windows ACLs bug
//NOTE: use a trailing slash for folders!!!
//see http://bugs.php.net/bug.php?id=27609
//see http://bugs.php.net/bug.php?id=30931

    if ($path{strlen($path)-1}=='/') // recursively return a temporary file path
        return is__writable($path.uniqid(mt_rand()).'.tmp');
    else if (is_dir($path))
        return is__writable($path.'/'.uniqid(mt_rand()).'.tmp');
    // check tmp file for read/write capabilities
    $rm = file_exists($path);
    $f = @fopen($path, 'a');
    if ($f===false)
        return false;
    fclose($f);
    if (!$rm)
        unlink($path);
    return true;
	}
	
	public function reset_cache($name = "",$type = "nonspecified") {
		if($name == '')
		trigger_error("Please specify the name of the cache you want to delete!",E_WARNING);
	switch($type) {
	case "nonspecified":
	trigger_error("Please specify the type of the cache you want to delete!",E_WARNING);
	break;
	case "databasevalue":
	global $rootpath;
	$dir = $rootpath.'fts-contents/cache';
	$file = $rootpath.'fts-contents/cache/value_mysql_'.$name.'.tmp';
	$file2 = 'value_mysql_'.$name.'.tmp';
	$fh = fopen($file, 'w') or die("can't open file");
	fclose($fh);
	
	dbv($name);
	break;
	}
	}
	
	public function ByteSize($bytes) 
    {
    $size = $bytes / 1024;
    if($size < 1024)
        {
        $size = number_format($size, 2);
        $size .= ' KB';
        } 
    else 
        {
        if($size / 1024 < 1024) 
            {
            $size = number_format($size / 1024, 2);
            $size .= ' MB';
            } 
        else if ($size / 1024 / 1024 < 1024)  
            {
            $size = number_format($size / 1024 / 1024, 2);
            $size .= ' GB';
            } 
        }
    return $size;
    }    
	public function pollshow() {
    global $CURUSER;
        unset ($pollexists);  
		$pollexists = true;
  // Get current poll
  $res = sql_query("SELECT * FROM polls ORDER BY added DESC LIMIT 1") or sqlerr();
  $arr = mysql_fetch_assoc($res);
  if (!$arr)
  	$pollexists = false;
  if ($pollexists) {
  $pollid = 0+$arr["id"];
  $userid = 0+$CURUSER["id"];
  $question = $arr["question"];
  $o = array($arr["option0"], $arr["option1"], $arr["option2"], $arr["option3"], $arr["option4"],
    $arr["option5"], $arr["option6"], $arr["option7"], $arr["option8"], $arr["option9"],
    $arr["option10"], $arr["option11"], $arr["option12"], $arr["option13"], $arr["option14"],
    $arr["option15"], $arr["option16"], $arr["option17"], $arr["option18"], $arr["option19"]);

  // Check if user has already voted
  $res = sql_query("SELECT * FROM pollanswers WHERE pollid=$pollid && userid=$userid") or sqlerr();
  $arr2 = mysql_fetch_assoc($res);

  echo '<BR>';
  collapses('poll',"<b>Polls</b>".(get_user_class() >= UC_MODERATOR ?
  
  	"<font class=small>- [<a class=altlink href=admin/makepoll.php?returnto=main><b>New</b></a>] - [<a class=altlink href=admin/makepoll.php?action=edit&pollid=$arr[id]&returnto=main><b>Edit</b></a>] - [<a class=altlink href=polls.php?action=delete&pollid=$arr[id]&returnto=main><b>Delete</b></a>]</font>" : ''
	));
	print("<table width=100% border=1 cellspacing=0 cellpadding=10><tr><td align=center>\n");
  print("<table class=main border=1 cellspacing=0 cellpadding=0><tr><td class=text>");
  print("<p align=center><b>$question</b></p>\n");
  if($usergroups["canvo"] != 'yes')
  $voted = $arr2;
  else
  $voted = true;  
  if ($voted)
  {
    // display results
    if ($arr["selection"])
      $uservote = $arr["selection"];
    else
      $uservote = -1;
		// we reserve 255 for blank vote.
    $res = sql_query("SELECT selection FROM pollanswers WHERE pollid=$pollid AND selection < 20") or sqlerr();

    $tvotes = mysql_num_rows($res);

    $vs = array(); // array of
    $os = array();

    // Count votes
    while ($arr2 = mysql_fetch_row($res))
      $vs[$arr2[0]] += 1;

    reset($o);
    for ($i = 0; $i < count($o); ++$i)
      if ($o[$i])
        $os[$i] = array($vs[$i], $o[$i]);



    // now os is an array like this: array(array(123, "Option 1"), array(45, "Option 2"))
    if ($arr["sort"] == "yes")
    	usort($os, srt);

    print("<table class=main width=100% border=0 cellspacing=0 cellpadding=0>\n");
    $i = 0;
    while ($a = $os[$i])
    {
      if ($i == $uservote)
        $a[1] .= "&nbsp;*";
      if ($tvotes == 0)
      	$p = 0;
      else
      	$p = round($a[0] / $tvotes * 100);
      if ($i % 2)
        $c = "";
      else
        $c = " bgcolor=#ECE9D8";
      print("<tr><td width=1% class=embedded$c><nobr>" . $a[1] . "&nbsp;&nbsp;</nobr></td><td width=99% class=embedded$c>" .
        "<img src=pic/bar_end.gif><img src=pic/bar.gif height=10 width=" . ($p * 3) .
        "><img src=pic/bar_end.gif> $p%</td></tr>\n");
      ++$i;
    }
    print("</table>\n");
	$tvotes = number_format($tvotes);
    print("<p align=center>Votes: $tvotes</p>\n");
  }
  else
  {
    print("<form method=post action=\"".$_SERVER['PHP_SELF']."\">\n");      $i = 0;
    while ($a = $o[$i])
    {
      print("<input type=radio name=choice value=$i>$a<br>\n");
      ++$i;
    }
    print("<br>");
    print("<input type=radio name=choice value=255>Blank vote (a.k.a. \"I just want to see the results!\")<br>\n");
    print("<p align=center><input type=submit value='Vote!' class=btn></p>");
  }
echo "</td></tr></table>";
if ($voted) {
  print("<p align=center><a href=polls.php>Previous polls</a></p>\n"); 
  }
  global $pollfid,$pollf;
  if($pollf == 'yes')
  print("<p align=center><a href=forums/viewforum.php?forumid=$pollfid>Poll Comments</a></p>\n");
echo "</td></tr></table>";
collapsee();
}
    }
    public function pollwatch() {
global $CURUSER;
        if ($_SERVER["REQUEST_METHOD"] == "POST")  
{
  $choice = $_POST["choice"];
  echo $choice;
  if ($CURUSER && $choice != "" && $choice < 256 && $choice == floor($choice))
  {
    $res = sql_query("SELECT * FROM polls ORDER BY added DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
    $arr = mysql_fetch_assoc($res) or die("No poll");
    $pollid = $arr["id"];
    $userid = $CURUSER["id"];
    $res = sql_query("SELECT * FROM pollanswers WHERE pollid=".mysql_real_escape_string($pollid)." && userid=".mysql_real_escape_string($userid)) or sqlerr(__FILE__, __LINE__);
    $arr = mysql_fetch_assoc($res);
    if ($arr)
    	stderr("Error","No dublicate votes allowed!");
    sql_query("INSERT INTO pollanswers VALUES(0, ".mysql_real_escape_string($pollid).", ".mysql_real_escape_string($userid).", ".mysql_real_escape_string($choice).")") or sqlerr(__FILE__, __LINE__);
    
    if (mysql_affected_rows() != 1)
      stderr("Error", "An error occured. Your vote has not been counted.");
    //===add karma
    UserHandle::KPS("+","2.0",$userid);
	//===end
    header("Location: $BASEURL/");
    die;
  }
  else
    stderr("Error", "Please select an option.");
}
    }
    public function shownews() {
    	collapses('news','<b>Recent news</b>'.(get_user_class() >= UC_ADMINISTRATOR ? ' - <font class=small>[<a class=altlink href=news.php><b>News page</b></a>]</font>' : ''));
     global $_c;
while(@$_c->save("index-news.tmp",get('cache_index_news'))) {
	if(NEWS_MODE == 'old'):
$res = sql_query("SELECT * FROM news WHERE ADDDATE(added, INTERVAL 45 DAY) > NOW() ORDER BY added DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0)
{
print("<ul>");
while($array = mysql_fetch_array($res))
{
$user = mysql_fetch_assoc(sql_query("SELECT username FROM users WHERE id = $array[userid]")) or sqlerr();
if ($news_flag == 0) {

print("<a href=\"javascript: klappe_news('a".$array['id']."')\"><img border=\"0\" src=\"pic/minus.gif\" id=\"pica".$array['id']."\" alt=\"Show/Hide\">&nbsp;" . gmdate("d.m.Y",strtotime($array['added'])) . " - " ."<b>". $array['title'] . "</b> <!--($user[username])--> </a>");
print("<div id=\"ka".$array['id']."\" style=\"display: block;\"> ".format_comment($array["body"],0)." </div> ");

$news_flag = 1;
}
else {

print("<a href=\"javascript: klappe_news('a".$array['id']."')\"><br><img border=\"0\" src=\"pic/plus.gif\" id=\"pica".$array['id']."\" alt=\"Show/Hide\">&nbsp;" . gmdate("d.m.Y",strtotime($array['added'])) . " - " ."<b>". $array['title'] . "</b></a>");
print("<div id=\"ka".$array['id']."\" style=\"display: none;\"> ".format_comment($array["body"],0)." </div> ");
}
if (get_user_class() >= UC_ADMINISTRATOR)
{
print(" <font size=\"-2\"> &nbsp; [<a class=altlink href=news.php?action=edit&newsid=" . $array['id'] . "&returnto=" . urlencode($_SERVER['PHP_SELF']) . "><b>E</b></a>]</font>");
print(" <font size=\"-2\">[<a class=altlink href=news.php?action=delete&newsid=" . $array['id'] . "&returnto=" . urlencode($_SERVER['PHP_SELF']) . "><b>D</b></a>]</font>"); }
print("<div id=\"ka".$array['id']."\" style=\"display: none;\"> ".format_comment($array["body"],0)."</div>");
}
print("</ul>\n");

}else {
	echo'No News';
}
print("<p align=center><font class=small>This content has been last updated ".date('Y-m-d H:i:s').". Cached every ".sec2hms(get('cache_index_news'))."</font></p>");
else:
$res = mysql_query("SELECT * FROM news WHERE ADDDATE(added, INTERVAL 45 DAY) > NOW() ORDER BY added DESC LIMIT 4") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0)
{
    print("<table width=100% border=0 cellspacing=0 cellpadding=10 style=\"border:0px;\"><tr style=\"border:0px;\"><td class=text style=\"border:0px;\">\n");
    while($array = mysql_fetch_array($res))
    {
        $user = mysql_fetch_array(mysql_query("SELECT username FROM users WHERE id = $array[userid]")) or sqlerr();
?>
<div align=center>
<table class=nobordermain width="100%" style="border:0px;" ><tr><td>
  <table class=nobordermain width="100%">
    <tr>
      <?php
      print("<td class=embedded width=100% colspan=2><b>" . gmdate("Y-m-d",strtotime($array['added'])) . " - " . $array['title'] . "</b>");
     if (get_user_class() >= UC_ADMINISTRATOR) {
    print(" - <font class=small>[<a class=altlink href=news.php?action=edit&newsid=" . $array['id'] . "><b>E</b></a>]</font>");
      print(" <font class=small>[<a class=altlink href=news.php?action=delete&newsid=" . $array['id'] . "><b>D</b></a>]</font>");
      }
      print("</td>");
      $_res = mysql_query("SELECT * FROM newscats WHERE id = '$array[cat]' LIMIT 1") or die(mysql_error());
      $catimg = mysql_fetch_assoc($_res);
      $catimg = $catimg['img'];
     # echo $catimg;
      ?>
    </tr>
    <tr>
    
      <td class=embedded width="5%"><img src="<?=$BASEURL?>/<?=$pic_base_url?>news/<?=$catimg?>" width=32 height=32></td>
      <?php
      print("<td class=embedded width=95%>" . format_comment($array['body'],0) . "</td>");
      ?>
    </tr>
    <tr>
      <td class=embedded width="100%" colspan="2">
      <?php
      print("<p align=right>- <a href=userdetails.php?id=" . $array['userid'] . ">$user[username]</a></p></td>");
      ?>
    </tr>
  </table>
  </td>
</tr>
</table>
</div>

<?php
  }
  print("<p align=center><font class=small>This content has been last updated ".date('Y-m-d H:i:s').". Cached every ".sec2hms(get('cache_index_news'))."</font></p>");
  print("</td></tr></table>\n");
}
endif;
}
collapsee();
    }
    public function showshout() {
//// Shoutbox
?>
<br>
<?collapses('fts_shoutbox','<b>Shoutbox</b>','100',0,'','','#000000','5');?>
<table class="main" cellspacing="0" cellpadding="5" width="100%" style="border:none;" border=0>


<tr style="border:none">
<td style="border:none">

<div id="shoutbox" style="overflow: auto; height: 300px; width: 100%; padding-top: 0cm">
<img src="pic/loader.gif" border="0" />
</div>

</td>
<script language=javascript>
function SmileIT(smile,form,text){
document.forms[form].elements[text].value = document.forms[form].elements[text].value+" "+smile+" ";
document.forms[form].elements[text].focus();
}
</script>
<td style="white-space: nowrap;border-left:thin dotted;border-top:none;border-right:none;" width="25%">
<table cellSpacing="1" cellPadding="3">
<tr>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':-)','shoutform','shout')">
<img border=0 src=pic/smilies/smile1.gif width="18" height="18"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':smile:','shoutform','shout')">
<img border=0 src=pic/smilies/smile2.gif width="18" height="18"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':-D','shoutform','shout')">
<img border=0 src=pic/smilies/grin.gif width="18" height="18"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':w00t:','shoutform','shout')">
<img border=0 src=pic/smilies/w00t.gif width="18" height="20"></a></td></tr><tr>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':-P','shoutform','shout')">
<img border=0 src=pic/smilies/tongue.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(';-)','shoutform','shout')">
<img border=0 src=pic/smilies/wink.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':-|','shoutform','shout')">
<img border=0 src=pic/smilies/noexpression.gif width="18" height="18"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':-/','shoutform','shout')">
<img border=0 src=pic/smilies/confused.gif width="18" height="18"></a></td></tr><tr>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':-(','shoutform','shout')">
<img border=0 src=pic/smilies/sad.gif width="18" height="18"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':\'-(','shoutform','shout')">
<img border=0 src=pic/smilies/cry.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':-O','shoutform','shout')">
<img border=0 src=pic/smilies/ohmy.gif width="18" height="18"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT('|-)','shoutform','shout')">
<img border=0 src=pic/smilies/sleeping.gif width="20" height="27"></a></td></tr><tr>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':innocent:','shoutform','shout')">
<img border=0 src=pic/smilies/innocent.gif width="18" height="22"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':unsure:','shoutform','shout')">
<img border=0 src=pic/smilies/unsure.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':closedeyes:','shoutform','shout')">
<img border=0 src=pic/smilies/closedeyes.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':cool:','shoutform','shout')">
<img border=0 src=pic/smilies/cool2.gif width="20" height="20"></a></td></tr><tr>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':thumbsdown:','shoutform','shout')">
<img border=0 src=pic/smilies/thumbsdown.gif width="27" height="18"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':blush:','shoutform','shout')">
<img border=0 src=pic/smilies/blush.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':yes:','shoutform','shout')">
<img border=0 src=pic/smilies/yes.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':no:','shoutform','shout')">
<img border=0 src=pic/smilies/no.gif width="20" height="20"></a></td></tr><tr>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':love:','shoutform','shout')">
<img border=0 src=pic/smilies/love.gif width="19" height="19"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':?:','shoutform','shout')">
<img border=0 src=pic/smilies/question.gif width="19" height="19"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':!:','shoutform','shout')">
<img border=0 src=pic/smilies/excl.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':idea:','shoutform','shout')">
<img border=0 src=pic/smilies/idea.gif width="19" height="19"></a></td></tr><tr>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':arrow:','shoutform','shout')">
<img border=0 src=pic/smilies/arrow.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':arrow2:','shoutform','shout')">
<img border=0 src=pic/smilies/arrow2.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':hmm:','shoutform','shout')">
<img border=0 src=pic/smilies/hmm.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':hmmm:','shoutform','shout')">
<img border=0 src=pic/smilies/hmmm.gif width="25" height="23"></a></td></tr><tr>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':huh:','shoutform','shout')">
<img border=0 src=pic/smilies/huh.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':rolleyes:','shoutform','shout')">
<img border=0 src=pic/smilies/rolleyes.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':kiss:','shoutform','shout')">
<img border=0 src=pic/smilies/kiss.gif width="18" height="18"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'><a href="javascript: SmileIT(':shifty:','shoutform','shout')">
<img border=0 src=pic/smilies/shifty.gif width="20" height="20"></a></td>
<td class=embedded style='padding: 2px; margin: 1px'>
</td>
</tr>
<td class=embedded style='padding: 2px; margin: 1px' colspan="4" align="center">

</table>
<center>
<p><a href="#" onclick='$("#sm").toggle("slow");$("#show").toggle();$("#hide").toggle(); return false;'><span id=show>Show</span><span id=hide style="display:none;">Hide</span> More Smilies</a></p>
<p><a href="#" onclick='$("#help").toggle("slow");$("#showad").toggle();$("#hidead").toggle(); return false;'><span id=showad>Show</span><span id=hidead style="display:none;">Hide</span> Help</a></p>  
</td>
</td>
</tr>
<tr>
<td style="white-space: nowrap; border-bottom:none;border-left:none;" width="85%" >
<?php
include 'include/class_browser.php';
$br = new Browser;
$browser = $br->Name;
print("<form action=\"shoutbox.php\" method=\"post\" name=\"shoutform\" onsubmit=\"return sendShout(this);\">");
if($browser == 'MSIE') {
?>
<input type="text" name="shout" style="width: 78%" MAXLENGTH="200"/>
<?php }else{?>
<input type="text" name="shout" style="width: 83%" MAXLENGTH="200"/>
<?php }?>
<style>
.ftscol:hover {
	color:red;
}
</style>
<input type="submit" value="Send" class="but"/>
<?php
ftsmenu2('250px');
$s = '';
					$s .= '<span id="src_parent"><input type="button"  value="Colors" class="but" />
</span><div class=sample_attach id=src_child>';
					$s .= <<<C
<img src="$BASEURL/pic/sbcolors/black.gif" onclick="javascript: SmileIT('[color=#000000][/color]','shoutform','shout');"/>
<img src="$BASEURL/pic/sbcolors/blue.gif" onclick="javascript: SmileIT('[color=#1818A0][/color]','shoutform','shout');"/>
<img src="$BASEURL/pic/sbcolors/green.gif" onclick="javascript: SmileIT('[color=#00FF00][/color]','shoutform','shout');"/>
<img src="$BASEURL/pic/sbcolors/orange.gif" onclick="javascript: SmileIT('[color=#FF8040][/color]','shoutform','shout');"/>
<img src="$BASEURL/pic/sbcolors/pink.gif" onclick="javascript: SmileIT('[color=#FF00FF][/color]','shoutform','shout');"/>
<img src="$BASEURL/pic/sbcolors/red.gif" onclick="javascript: SmileIT('[color=#FF0000][/color]','shoutform','shout');"/>
<img src="$BASEURL/pic/sbcolors/yellow.gif" onclick="javascript: SmileIT('[color=#FFFF00][/color]','shoutform','shout');"/>
<input type="button" class="but" style="font-weight:bold" value="Show on page" onclick='$("#colors").toggle("slow");'/>
C;
					$s .= '</div>
<script type="text/javascript">
at_attach("src_parent", "src_child", "hover", "x", "pointer");
</script>';
echo $s;
?>
<div id='colors' style='display: none;'>
<center>
<img src="<?=$BASEURL?>/pic/sbcolors/black.gif" onclick="javascript: SmileIT('[color=#000000][/color]','shoutform','shout');$('#colors').toggle('slow');"/>
<img src="<?=$BASEURL?>/pic/sbcolors/blue.gif" onclick="javascript: SmileIT('[color=#1818A0][/color]','shoutform','shout');$('#colors').toggle('slow');"/>
<img src="<?=$BASEURL?>/pic/sbcolors/green.gif" onclick="javascript: SmileIT('[color=#00FF00][/color]','shoutform','shout');$('#colors').toggle('slow');"/>
<img src="<?=$BASEURL?>/pic/sbcolors/orange.gif" onclick="javascript: SmileIT('[color=#FF8040][/color]','shoutform','shout');$('#colors').toggle('slow');"/>
<img src="<?=$BASEURLl?>/pic/sbcolors/pink.gif" onclick="javascript: SmileIT('[color=#FF00FF][/color]','shoutform','shout');$('#colors').toggle('slow');"/>
<img src="<?=$BASEURLl?>/pic/sbcolors/red.gif" onclick="javascript: SmileIT('[color=#FF0000][/color]','shoutform','shout');$('#colors').toggle('slow');"/>
<img src="<?=$BASEURL?>/pic/sbcolors/yellow.gif" onclick="javascript: SmileIT('[color=#FFFF00][/color]','shoutform','shout');$('#colors').toggle('slow');"/>
</center>
</div>
<div id='sm' style='display: none;'>
<script>
$(document).ready(function() {
	$("#sm").load("index.php?act=showall");
})
</script>
</div>
<div id='help' style='display: none;'>
<?if(ur::ismod()) {?>
<p>As an member of the staff, you have the folowing commands:</p>
	<p>If you want to make an notice - use the /notice command.</p>
<p>If you want to empty the whole shoutbox - use the /empty command</p>
<p>If you want to warn or unwarn an user - use the /warn and /unwarn commands</p>
<p>If you want to ban(disable) or unban(enable) an user - use the /ban and /unban commands</p>
<p>To delete all notices from the shout, use /deletenotice command</p>
<?php }?>
<p>As an user, you have the folowing commands:</p>
<p>If you want to view this message in the shout, use the /help command</p>
<p>If you want to speak at 3rd person, use the /me command.</p>
</div>
</td>
<td style="white-space: nowrap; border-bottom:none;border-top:none;border-right:none;" width="15%">
<input type="button" class="but" style="font-weight:bold" value="B" onclick="javascript: SmileIT('[b][/b]','shoutform','shout')"/>
<input type="button" class="but" style="font-weight:bold" value="I" onclick="javascript: SmileIT('[i][/i]','shoutform','shout')"/>
<input type="button" class="but" style="font-weight:bold" value="U" onclick="javascript: SmileIT('[u][/u]','shoutform','shout')"/>
<input type="button" value="Clear" class="but" onclick="sb_Clear()" />
</td>
</tr>
<?php
print("</table>");
print("</form>");

?>

<div id="loading-layer" name="loading-layer" style="position: absolute; display:none; left:300px; top:110px;width:200px;height:60px;background:#FFF;padding:10px;text-align:center;border:1px solid #000"><div style="font-weight:bold" id="loading-layer-text" class="small">Loading</div><img src="pic/loader.gif" border="0" /></div>

<script language="javascript" type="text/javascript" src="clientside/ajax.js"></script>
<script type="text/javascript">
<!--
function sendShout(formObj) {

Shout = formObj.shout.value

if (Shout.replace(/ /g, '') == '') {
$('#shoutbox').html('<b>Message cannot be empty!</b>');
				setTimeout("getShouts();", 3000);
				setTimeout("getWOL();", 3000) ;
return false
}

sb_Clear();

var ajax = new tbdev_ajax();
ajax.onShow ('');
//ajax.onShow = function() { };
var varsString = "";
ajax.requestFile = "shoutbox.php";
ajax.setVar("do", "shout");
ajax.setVar("shout", escape(Shout));
ajax.method = 'GET';
ajax.element = 'shoutbox';
ajax.sendAJAX(varsString);

return false
}

function getShouts() {
	if(!window.opera)
$("#loading-layer").show();
var ajax = new tbdev_ajax();
ajax.onShow = function() {};
ajax.onLoaded = function() {
	$("#loading-layer").hide();
};
var varsString = "";
ajax.requestFile = "shoutbox.php";
ajax.method = 'GET';
ajax.element = 'shoutbox';
ajax.sendAJAX(varsString);

getshouts = setTimeout('getShouts()', 30000);

return false

}

function getWOL() {

var ajax = new tbdev_ajax();
ajax.onShow = function() { 
};
var varsString = "";
ajax.requestFile = "shoutbox.php";
ajax.method = 'GET';
ajax.setVar("wol", 1);
ajax.element = 'wol';
ajax.sendAJAX(varsString);

getwql = setTimeout('getWOL()', 30000);

return false

}

function sb_Clear() {
document.forms["shoutform"].shout.value = ''
return true;
}

function deleteShout(id) {
$("#loading-layer").show();

var ajax = new tbdev_ajax();
ajax.onLoaded = function() {
	$("#loading-layer").hide();
};
var varsString = "";
ajax.requestFile = "shoutbox.php";
ajax.setVar("do", "delete");
ajax.setVar("id", id);
ajax.method = 'GET';
ajax.element = 'shoutbox';
ajax.sendAJAX(varsString);


return false

}

function editShout(id) {
	//var edit = window.open( "<?=$BASEURL;?>/shoutbox.php?do=edit&id=" + id, "myWindow", 
//"status = 1, height = 300, width = 300, resizable = 0" );
clearTimeout(getshouts);
	$("#shoutbox").load("shoutbox.php?do=edit&id=" + id + "");
}
function clear() {
clearTimeout(getshouts);	
}

getShouts();

getWOL();

-->
</script>
<?php
collapsee();
}
	public function whatsgoingon() {
		$a = @mysql_fetch_assoc(@sql_query("SELECT id,username FROM users WHERE status='confirmed' ORDER BY id DESC LIMIT 1")) or sqlerr(__FILE__, __LINE__);
if ($CURUSER)
  $latestuser = "<a href=userdetails.php?id=" . $a["id"] . ">" . $a["username"] . "</a>";
else
  $latestuser = $a['username'];



//stats
$dt24 = gmtime() - 86400;
$arr = mysql_fetch_assoc(sql_query("SELECT * FROM avps WHERE arg='last24'")) or $no24=true;
$res=sql_query("SELECT * FROM users WHERE last_access >= '". get_date_time($dt24). "' ORDER BY username") or sqlerr(__FILE__, __LINE__);
$totalonline24 = mysql_num_rows($res);

$_ss24 = ($totalonline24 != 1) ? 's':'';

$last24record = get_date_time($arr["value_u"]);
$last24 = $arr["value_i"];
if ($no24 || $totalonline24 > $last24 )
{
$last24 = $totalonline24;

$period = strtotime(gmdate("Y-m-d H:i:s"));
sql_query(($no24 ? 'INSERT':'UPDATE'). " avps SET value_i = $last24 , value_u = $period ". ($no24 ? ", arg='last24'":"WHERE arg='last24'")) or sqlerr();
}
while ($arr = mysql_fetch_assoc($res))
{
if ($activeusers24) $activeusers24 .= ",\n";

   $arr["username"] = get_style($arr['class'],$arr['username']);

if($donator = $arr["donor"] === "yes");
$activeusers24 .= "<nobr>";
if ($warned = $arr["warned"] === "yes")
$activeusers24 .= "<nobr>";
if ($CURUSER)
$activeusers24 .= "<a href=userdetails.php?id={$arr["id"]}><b>{$arr["username"]}</b></a>";
else
$activeusers24 .= "<b>{$arr["username"]}</b>";
if ($donator)
$activeusers24 .= "<img src={$pic_base_url}star.png alt='Donated {$$arr["donor"]}'></nobr>";
if ($warned)
$activeusers24 .= "<img src={$pic_base_url}warning.png alt='Warned {$$arr["warned"]}'></nobr>";
}

if (!$activeusers24)
$activeusers24 = "There have been no active users in the last 24 hours.";
$dt = gmtime() - 180;
$dt = sqlesc(get_date_time($dt));
$res = sql_query("SELECT id, username, class FROM users WHERE last_access >= $dt ORDER BY username") or print(mysql_error());
while ($arr = mysql_fetch_assoc($res))
{
	
  if ($activeusers) $activeusers .= ",\n";
  $arr['username'] = get_style($arr['class'],$arr['username']);
  
  $dispclass = get_user_class_name($arr['class']);
  $donator = $arr["donated"] > 0;
  if ($donator)
    $activeusers .= "<nobr>";
  if ($CURUSER)
    $activeusers .= "<a href=userdetails.php?id=" . $arr["id"] . "><b>" . $arr["username"] . "</b></a>";
  else
    $activeusers .= "<b>$arr[username]</b>";
  if ($donator)
    $activeusers .= "<img src=pic/star.png alt='Donated $$arr[donated]'></nobr>";
}
if (!$activeusers)
  $activeusers = "There have been no active users in the last 15 minutes.";
	echo _br;
	global $BASEURL;
	collapses('whatisgon','<b>Whats goin on?</b>');
$activeusers24 = explode(",",$activeusers24);
$activeusers = explode(",",$activeusers);
?>
<center>
<?=$totalonline24?> Member<?=$_ss24?> has visited during the last 24 hours <a href="javascript:klappe_news('u24')"><img border="0" src='<?$BASEURL?>/pic/plus.gif' id="pica0"/></a> <br>
<div id="ku24" style="display: none;">
<?php
$i = 0 ;
foreach($activeusers24 as $fts) {
	$perrow = 17 ;
	echo $fts;
	print ( ($i && $i % $perrow == 0) ? "<br>" : "" ) ;
	$i++;
}
?>
</div>
Welcome to our newest member, <b><?=$latestuser?></b>!<br>
Active users 	<a href="javascript:klappe_news('a0')"><img border="0" src='<?$BASEURL?>/pic/plus.gif' id="pica0"/></a>
				<div id="ka0" style="display: none;">
					<p align="justify">
					<?php
					foreach($activeusers as $fts) {
	$perrow = 17 ;
	echo $fts;
	print ( ($i && $i % $perrow == 0) ? "<br>" : "" ) ;
	$i++;
}
					?></p>
				</div>
</center>

<?php
collapsee();
	}
	public function lastxforumshow() {
		//////////////////////////////////////////////// LAST X FORUM POSTS
global $howmuchforum,$CURUSER;
if ($CURUSER)
{
	echo '<BR>';
	collapses('lastforum',"<b>Last $howmuchforum Forum Posts</b>");
print("<table width=100% border=0 cellspacing=0 cellpadding=5><tr class=thead>".
"<td align=left class=subheader><b>Topic Title</b></td>".
"<td align=center class=subheader><b>Views</b></td>".
"<td align=center class=subheader><b>Author</b></td>".
"<td align=left class=subheader><b>Posted At</b></td>".
"</tr>");

$res = sql_query("SELECT posts.id AS pid, posts.topicid, posts.userid AS userpost, posts.added, topics.id AS tid, topics.subject, topics.forumid, topics.lastpost, topics.views, forums.name, forums.minclassread, forums.topiccount, users.username
FROM posts, topics, forums, users, users AS topicposter
WHERE posts.topicid = topics.id AND topics.forumid = forums.id AND posts.userid = users.id AND topics.userid = topicposter.id AND minclassread <=" . $CURUSER["class"] . "
ORDER BY posts.added DESC
LIMIT $howmuchforum");
while ($postsx = mysql_fetch_assoc($res))
{
	if(get_user_class() >= $arr["minclassread"]):
$q = mysql_query("SELECT class FROM users WHERE id='$postsx[userpost]'");
$q = mysql_fetch_assoc($q);
$postsx["username"] = get_style($q['class'],$postsx["username"]);
print("<tr><td><a href=\"forums/viewtopic.php?topicid={$postsx["tid"]}&page=p{$postsx["pid"]}#{$postsx["pid"]}\"><b>{$postsx["subject"]}</b></a><br />in <a href=\"forums/viewforum.php?forumid={$postsx["forumid"]}\">{$postsx["name"]}</a></td>".
"<td align=center>{$postsx["views"]}</td>".
"<td align=center><a href=userdetails.php?id={$postsx["userpost"]}><b>{$postsx["username"]}</b></a></td>".
"<td>{$postsx["added"]}</td></tr>");
endif;
}
print("</table>");
collapsee();
}
	}
public function stats() {
collapses('statsindex','<b>Tracker Statistics</b>');
global $_c, $c, $maxusers;  
while(@$_c->save("index-statistics.tmp",get('cache_index_stats'))) {
	$registered = number_format(get_row_count("users"));
$unverified = number_format(get_row_count("users", "WHERE status='pending'"));
$donated = number_format(get_row_count("users", "WHERE donor = 'yes'"));
$torrents = number_format(get_row_count("torrents"));
$dead = number_format(get_row_count("torrents", "WHERE visible='no'"));
$totaldownloaded = mksize($row["totaldl"]);
$totaluploaded = mksize($row["totalul"]);
$r = sql_query("SELECT value_u FROM avps WHERE arg='seeders'") or sqlerr(__FILE__, __LINE__);
$a = mysql_fetch_row($r);
$seeders = 0 + $a[0];
$r = sql_query("SELECT value_u FROM avps WHERE arg='leechers'") or sqlerr(__FILE__, __LINE__);
$a = mysql_fetch_row($r);
$leechers = 0 + $a[0];
$seeders = number_format(get_row_count("peers", "WHERE seeder='yes'"));
$leechers = number_format(get_row_count("peers", "WHERE seeder='no'"));
$peers = number_format($seeders + $leechers);
if ($leechers == 0)
$ratio = 0;
else
$ratio = round($seeders / $leechers * 100);
$warnedu = number_format(get_row_count("users", "WHERE warned='yes'"));
$disabled = number_format(get_row_count("users", "WHERE enabled='no'"));
$VIP    = number_format(get_row_count("users", "WHERE class='2'"));

$result = sql_query("SELECT SUM(downloaded) AS totaldl, SUM(uploaded) AS totalul FROM users") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_assoc($result);
$totaldownloaded = $row["totaldl"];
$totaluploaded = $row["totalul"];
$totaldata = $totaldownloaded+$totaluploaded;
     ?>
<table width="100%" class="main" border="0" cellspacing="0" cellpadding="10" style="border:none;">
  <tr>
    <td class="rowhead"><div align="right">Registered Users / Limit</div></td>
    <td class="rowhead"><div align="right"><b><?=$registered?> / <?=$maxusers?></b></div></td>
    <td class="rowhead"><div align="right">Unconfirmed users <img src=pic/exclamation.png></div></td>
    <td class="rowhead"><div align="right"><b><?=$unverified?></b></div></td>
    <td class="rowhead"><div align="right">Torrents</div></td>
    <td class="rowhead"><div align="right"><b><?=$torrents?></b></div></td>
    <td class="rowhead"><div align="right">Dead Torrents </div></td>
    <td class="rowhead"><div align="right"><b><?=$dead?></b></div></td>
  </tr>
  <tr>
    <td class="rowhead"><div align="right">Warned Users <img src= pic/warning.png></div></td>
    <td class="rowhead"><div align="right"><b><?=$warnedu?></b></div></td>
    <td class="rowhead"><div align="right">Banned Users <img src= pic/disabled.png></div></td>
    <td class="rowhead"><div align="right"><b><?=$disabled?></b></div></td>
    <td class="rowhead"><div align="right">Seeders</div></td>
    <td class="rowhead"><div align="right"><?=$seeders?></div></td>
    <td class="rowhead"><div align="right">Leechers</div></td>
    <td class="rowhead"><div align="right"><?=$leechers?></div></td>
  </tr>
  <tr>
    <td class="rowhead"><div align="right">VIP's <img src= pic/star.png></div></td>
    <td class="rowhead"><div align="right"><?=$VIP?></div></td>
    <td class="rowhead"><div align="right">Donor's <img src= pic/star.png></div></td>
    <td class="rowhead"><div align="right"><?=$donated?></div></td>
    <td class="rowhead"><div align="right">Peers</div></td>
    <td class="rowhead"><div align="right"><?=$peers?></div></td>
    <td class="rowhead"><div align="right">Seeder/leecher ratio (%)</div></td>
    <td class="rowhead"><div align="right"><?=$ratio?></div></td>
  </tr>
  <tr>
    <td colspan="2" class="rowhead"><div align="right">Total downloaded</div></td>
    <td colspan="2" class="rowhead"><div align="center"><?=mksize($totaldownloaded)?></div></td>
    <td colspan="2" class="rowhead"><div align="right">Total uploaded</div></td>
    <td colspan="2" class="rowhead"><div align="center"><?=mksize($totaluploaded)?></div></td>
  </tr>
  </table>
  <?php
print("<p align=center><font class=small>This content has been last updated ".date('Y-m-d H:i:s').". Cached every ".sec2hms(get('cache_index_stats'))."</font></p>");
}
       collapsee();
	}
}
global $rootpath ;
include $rootpath . 'include/javascript_bridge.php' ;
?>