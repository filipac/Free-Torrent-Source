<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
/**
 * template
 *
 * @package Free Torrent Source
 * @author Filip
 * @copyright 2008
 * @version $Id$
 * @access public
 */
class template
{
  /**
   * template::inhead()
   *
   * @param mixed $title
   * @return
   */
    public function inhead( $title )
    {
    	echo makevars();
    	echo '<link rel="stylesheet" href="'.$BASEURL.'/fts-contents/styles/extra.css" type="text/css">'."\n";
        scripts() ;
        global $VERSION,$BASEURL;
template::AddMetaTag('Generator',$VERSION->getLongVersion() . ' - ' . FFactory::smallcopyright());
global $charset, $metadesc, $metakeywords, $rootpath, $BASEURL ; 
echo <<<meta
<meta http-equiv="Content-Type" content="text/html;charset=$charset">
<meta name="revisit-after" content="3 days">
<meta name="robots" content="index, follow">
<meta name="description" content="$metadesc">
<meta name="keywords" content="$metakeywords">
<link rel="alternate" type="application/rss+xml" title="RSS" href="$BASEURL/rss.php">\n
meta;
javascript('menu');
    do_action("head");
	}
  /**
   * template::showdebug()
   *
   * @return
   */
    public function showdebug()
    {
    }

  /**
   * template::dynamiclock()
   *
   * @return
   */
    public function dynamiclock()
    {
?>
		<span id="clock">loading</span>
<script type="text/javascript">
function refrClock()
{
var d=new Date();
var s=d.getSeconds();
var m=d.getMinutes();
var h=d.getHours();
var day=d.getDay();
var date=d.getDate();
var month=d.getMonth();
var year=d.getFullYear();
var am_pm;
if (s<10) {s="0" + s}
if (m<10) {m="0" + m}
if (h>12) {h-=12;am_pm = "PM"}
else {am_pm="AM"}
if (h<10) {h="0" + h}
document.getElementById("clock").innerHTML=h + ":" + m + ":" + s + " " + am_pm;
setTimeout("refrClock()",1000);
}
refrClock();
</script>
<?php
    }
  /**
   * template::buddylistandrss()
   *
   * @return
   */
    public function buddylistandrss()
    {
        global $rootpath ;
        print ( " <a href=$BASEURL/friends.php><img style=border:none alt=Buddylist title=Buddylist src=" .
            $rootpath . "pic/buddylist.gif></a>" ) ;
        print ( " <a href=$BASEURL/getrss.php><img style=border:none alt=Buddylist title='Get RSS' src=" .
            $rootpath . "pic/rss.gif width=11 height=12></a>" ) ;
            do_action("theme_sidebar_icons");
    }
  /**
   * template::messages()
   *
   * @return
   */
    public function messages()
    {
        global $messages, $BASEURL, $inboxpic, $messages, $unread, $outmessages ;
        global $rootpath ;
        if ( $messages )
        {
            print ( "<span class=smallfont><a href=$BASEURL/messages.php>$inboxpic</a> $messages ($unread New)</span>" ) ;

            if ( $outmessages )
                print ( "<span class=smallfont>  <a href=$BASEURL/messages.php?action=viewmailbox&box=-1><img height=14px style=border:none alt=sentbox title=sentbox src=" .
                    $rootpath . "pic/pn_sentbox.gif></a> $outmessages</span>" ) ;

            else
                print ( "<span class=smallfont>  <a href=$BASEURL/messages.php?action=viewmailbox&box=-1><img height=14px style=border:none alt=sentbox title=sentbox src=" .
                    $rootpath . "pic/pn_sentbox.gif></a> 0</span>" ) ;
        }
        else
        {
            print ( "<span class=smallfont><a href=$BASEURL/messages.php><img height=14px style=border:none alt=inbox title=inbox src=" .
                $rootpath . "pic/pn_inbox.gif></a> 0</span>" ) ;

            if ( $outmessages )
                print ( "<span class=smallfont>  <a href=$BASEURL/messages.php?action=viewmailbox&box=-1><img height=14px style=border:none alt=sentbox title=sentbox src=" .
                    $rootpath . "pic/pn_sentbox.gif></a> $outmessages</span>" ) ;

            else
                print ( "<span class=smallfont>  <a href=$BASEURL/messages.php?action=viewmailbox&box=-1><img height=14px style=border:none alt=sentbox title=sentbox src=" .
                    $rootpath . "pic/pn_sentbox.gif></a> 0</span>" ) ;

        }
    }
  /**
   * template::leechwarn()
   *
   * @return
   */
    public function leechwarn()
    {
        if ( $CURUSER['downloaded'] > 0 )
        { // Make sure there is a download value

            // Set the ratio threshold based on user class
            switch ( get_user_class() )
            {
                case UC_USER:
                case UC_POWER_USER:
                    $ratio = 1.00 ;
                    break ;

                case UC_VIP:
                    $ratio = 0.30 ;
                    break ;

                case UC_UPLOADER:
                case UC_MODERATOR:
                    $ratio = 0.70 ;
                    break ;

                case UC_ADMINISTRATOR:
                case UC_SYSOP:
                case UC_STAFFLEADER:
                    $ratio = 0.00 ;
                    break ;
            }

            // Override ratio if donor, but only if existing ratio is higher than 0.70
            if ( $CURSUSER['donor'] == 'yes' && $ratio > 0.70 )
                $ratio = 0.70 ;

            // Do remember warned users they are warned and for how long... [by fedepeco]
            if ( $CURUSER['leechwarn'] == 'yes' )
            {
                $leechwarnuntil = $CURUSER['leechwarnuntil'] ;
                print ( "<p><table border=1 width=100% cellspacing=0 cellpadding=10 bgcolor=#8daff5 align=center><tr><td style='padding: 10px;'bgcolor=red align=center>\n" ) ;
                print ( "<b><font color=white align=center>You are now warned for having a low ratio. You need to get a 0.6 ratio for your warning be removed.<br>If you don't get it in " .
                    mkprettytime(strtotime($leechwarnuntil) - gmtime()) .
                    ", your account will be banned.</font></b>" ) ;
                print ( "</td></tr></table></p>\n" ) ;
                print ( "<br>\n" ) ;
            }
            // End MOD...
        }
    }
  /**
   * template::get()
   *
   * @return
   */
    public function get()
    {
        global $CURUSER ;
        return $CURUSER['username'] ;
    }

  /**
   * template::messagealertbox()
   *
   * @param mixed $u
   * @return
   */
    public function messagealertbox( $u )
    {
        global $CURUSER ;
        if ( $CURUSER['pmbox'] == 'no' )
            return ;
        if($u == 0)
        return;
?>
			<?php  
        JsB::tboxcss() ;
#        JsB::insertjq() ;
        JsB::inserthickbox() ;
?>  
<div id="hiddenModalContent" style="display:none">
<p>
<?= "You have $u new message" . ( $u > 1 ? "s" : "" ) . "!" ?>
</p>
<p style="text-align:center"><input type="submit" id="Ok" value="&nbsp;&nbsp;Go To Messages&nbsp;&nbsp;" onclick="tb_remove();window.location = BASEURL + '/messages.php';" /> <input type="submit" id="Login" value="&nbsp;&nbsp;Remind me Later&nbsp;&nbsp;" onclick="tb_remove();" /></p>
</div>
<?php if ( basename($_SERVER['SCRIPT_FILENAME']) != 'messages.php' )
        { ?>
<script>
$(document).ready(function(){ tb_show('Reminder', '#TB_inline?height=300&width=400&inlineId=hiddenModalContent', null); });
</script>
<?php }
    }
    public function AddMetaTag($name,$content) {
		echo <<<META
<meta name="$name" content="$content">\n
META;
	}
	public function notifications() {
		global $CURUSER,$unread,$offlinemsg;
	if($CURUSER['downloaded'] > 0) { // Make sure there is a download value

// Set the ratio threshold based on user class
switch (get_user_class())
 {
   case UC_USER:
   case UC_POWER_USER: $ratio = 1.00;
   break;

   case UC_VIP: $ratio = 0.30;
   break;

   case UC_UPLOADER: 
   case UC_MODERATOR: $ratio = 0.70;
   break;

   case UC_ADMINISTRATOR:
   case UC_SYSOP: 
   case UC_STAFFLEADER: $ratio = 0.00;
   break;
 }
// Override ratio if donor, but only if existing ratio is higher than 0.70
if($CURSUSER['donor']=='yes' && $ratio > 0.70) $ratio = 0.70;

// Do remember warned users they are warned and for how long... [by fedepeco]
if ($CURUSER['leechwarn'] == 'yes') {
$leechwarnuntil = $CURUSER['leechwarnuntil'];
print("<p><table border=1 width=100% cellspacing=0 cellpadding=10 bgcolor=#8daff5 align=center><tr><td style='padding: 10px;'bgcolor=red align=center>\n");
print("<b><font color=white align=center>You are now warned for having a low ratio. You need to get a 0.6 ratio for your warning be removed.<br>If you don't get it in " . mkprettytime(strtotime($leechwarnuntil) - gmtime()) . ", your account will be banned.</font></b>");
print("</td></tr></table></p>\n");
print("<br>\n");
}
// End MOD...
}
if ($unread)
{
  $texts[] = "<a href=$BASEURL/messages.php>You have $unread new message" . ($unread > 1 ? "s" : "") . "! Click here to read.</a>";
}

if ($CURUSER) {
	$rel = sql_query("SELECT COUNT(*) FROM users WHERE status = 'pending' AND invited_by = ".mysql_real_escape_string($CURUSER[id])) or sqlerr(__FILE__, __LINE__);
	$arro = mysql_fetch_row($rel);
	$number = $arro[0];
	if ($number > 0)
	{
	 $texts[] = "<b><a href=$BASEURL/invite.php?id=$CURUSER[id]><font color=red>Your friend".($number > 1 ? "s" : "")." ($number) awaiting confirmation from you!</font></a></b>";
	}
}
if ($offlinemsg)
{
	$settings_script_name = substr($_SERVER[SCRIPT_FILENAME], -12 , 12);
	if ($settings_script_name != "settings.php" AND $settings_script_name != "announce.php") {	
		if(ur::ismod())	
	$texts[] = "<font color=red><b>WARNING!!!</b>:</font> The website is currently offline! Click <a href=$BASEURL/admin/settings.php>here</a> to change settings.";
	}
}
if (get_user_class() > UC_MODERATOR)
{
$resa = mysql_query("select count(id) as numreports from reports WHERE dealtwith=0");
$arra = mysql_fetch_assoc($resa);
$numreports = $arra[numreports];
if ($numreports){
$texts[] = "<a href=$BASEURL/admin/reports.php>There is $numreports new report" . ($numreports > 1 ? "s" : "") . "!</a>";
}

	$rese = mysql_query("SELECT COUNT(id) as nummessages from staffmessages WHERE answered='no'");
	$arre = mysql_fetch_assoc($rese);
	$nummessages = $arre[nummessages];
	if ($nummessages > 0) {
	$texts[] = "<a href=$BASEURL/admin/staffbox.php>There is $nummessages new staff message" . ($nummessages > 1 ? "s" : "") . "!</a>";
	}	
}
ads();
do_action("notifications");
if(!empty($texts)) {
	print("<div class=success align=left>\n");
foreach($texts as $t) {
 echo $t.'<BR>';

} 	print("</div>\n"); }
template::texts();
	}
	public function texts() {
		global $_texts;
		if(!empty($_texts)) {
	print("<div class=success align=left>\n");
foreach($_texts as $t) {
 echo $t.'<BR>';

} 	print("</div>\n"); }
	}
	public function add_message() {
		
	}
}
?>