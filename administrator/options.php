<?php
$rootpath = '../' ;
include $rootpath . 'include/bittorrent.php' ;
require $rootpath . 'fts-contents/wysiwyg/wysiwyg.php' ;
loggedinorreturn() ;
if ( ! ur::isadmin() )
{
    write_log( "User $CURUSER[username] tried to view the administration panel, but it was stopped because his usergroup doesn't have access there." ) ;
    die( 'You\'re to small, baby!<BR>Hacking attempt logged.' ) ;
}
$type = isset( $_POST['type'] ) ? $_POST['type'] : ( isset($_GET['type']) ? $_GET['type'] :
    '' ) ;
$allowed_actions = array( 'main', 'save_main', 'database', 'save_database',
    'smtp', 'save_smtp', 'template', 'save_template', 'tweak', 'save_tweak', 'mods',
    'save_mods', 'security', 'save_security', 'transfer', 'savetransfer', 'cache', 'savecache', 'reCAPTCHA', 'save_reCAPTCHA', 'payment', 'save_payment', 'pg', 'save_pg' ) ;
    
if ( ! in_array($type, $allowed_actions) ) $type = '' ;
$notice = "<p><table border=1 cellspacing=0 cellpadding=10 bgcolor=black width=100%><tr><td style='padding: 10px; background: black' class=text>
<font color=white><center>Before save the settings, please ensure that you have properly configured file and directory access permissions.</b>
</font></center></td></tr></table></p><table border=1 cellspacing=0 cellpadding=10 width=100%>" ;
function select ($selectname,$selectoptions = array(),$check) {
	$a = <<<ml
<SELECT NAME="$selectname">	
ml;
foreach ($selectoptions as $value => $name ) {
	$a .= "<OPTION VALUE=\"$value\" ".($check == $value ? 'SELECTED' : '' ).">$name</OPTION>";
}
$a .= "</SELECT>";
return $a;
}
echo '<link rel="stylesheet" href="css/ex.css" type="text/css">
<script src="js/dw_event.js" type="text/javascript"></script>
<script src="js/dw_viewport.js" type="text/javascript"></script>
<script src="js/dw_tooltip.js" type="text/javascript"></script>
<script src="js/dw_tooltip_aux.js" type="text/javascript"></script>
<script src="js/fts_vars.js" type="text/javascript"></script>
<style type="text/css">

#fixedtipdiv{
position:absolute;
padding: 2px;
border:1px solid black;
font:normal 12px Verdana;
line-height:18px;
z-index:100;
color:black;
}

</style>
';
echo <<<js
<script type="text/javascript">

/***********************************************
* Fixed ToolTip script- © Dynamic Drive (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit http://www.dynamicdrive.com/ for full source code
***********************************************/
		
var tipwidth='150px' //default tooltip width
var tipbgcolor='#E1E5F1'  //tooltip bgcolor
var disappeardelay=250  //tooltip disappear speed onMouseout (in miliseconds)
var vertical_offset="0px" //horizontal offset of tooltip from anchor link
var horizontal_offset="-3px" //horizontal offset of tooltip from anchor link

/////No further editting needed

var ie4=document.all
var ns6=document.getElementById&&!document.all

if (ie4||ns6)
document.write('<div id="fixedtipdiv" style="visibility:hidden;width:'+tipwidth+';background-color:'+tipbgcolor+'" ></div>')

function getposOffset(what, offsettype){
var totaloffset=(offsettype=="left")? what.offsetLeft : what.offsetTop;
var parentEl=what.offsetParent;
while (parentEl!=null){
totaloffset=(offsettype=="left")? totaloffset+parentEl.offsetLeft : totaloffset+parentEl.offsetTop;
parentEl=parentEl.offsetParent;
}
return totaloffset;
}


function showhide(obj, e, visible, hidden, tipwidth){
if (ie4||ns6)
dropmenuobj.style.left=dropmenuobj.style.top=-500
if (tipwidth!=""){
dropmenuobj.widthobj=dropmenuobj.style
dropmenuobj.widthobj.width=tipwidth
}
if (e.type=="click" && obj.visibility==hidden || e.type=="mouseover")
obj.visibility=visible
else if (e.type=="click")
obj.visibility=hidden
}

function iecompattest(){
return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
}

function clearbrowseredge(obj, whichedge){
var edgeoffset=(whichedge=="rightedge")? parseInt(horizontal_offset)*-1 : parseInt(vertical_offset)*-1
if (whichedge=="rightedge"){
var windowedge=ie4 && !window.opera? iecompattest().scrollLeft+iecompattest().clientWidth-15 : window.pageXOffset+window.innerWidth-15
dropmenuobj.contentmeasure=dropmenuobj.offsetWidth
if (windowedge-dropmenuobj.x < dropmenuobj.contentmeasure)
edgeoffset=dropmenuobj.contentmeasure-obj.offsetWidth
}
else{
var windowedge=ie4 && !window.opera? iecompattest().scrollTop+iecompattest().clientHeight-15 : window.pageYOffset+window.innerHeight-18
dropmenuobj.contentmeasure=dropmenuobj.offsetHeight
if (windowedge-dropmenuobj.y < dropmenuobj.contentmeasure)
edgeoffset=dropmenuobj.contentmeasure+obj.offsetHeight
}
return edgeoffset
}

function fixedtooltip(menucontents, obj, e, tipwidth){
if (window.event) event.cancelBubble=true
else if (e.stopPropagation) e.stopPropagation()
clearhidetip()
dropmenuobj=document.getElementById? document.getElementById("fixedtipdiv") : fixedtipdiv
dropmenuobj.innerHTML=menucontents

if (ie4||ns6){
showhide(dropmenuobj.style, e, "visible", "hidden", tipwidth)
dropmenuobj.x=getposOffset(obj, "left")
dropmenuobj.y=getposOffset(obj, "top")
dropmenuobj.style.left=dropmenuobj.x-clearbrowseredge(obj, "rightedge")+"px"
dropmenuobj.style.top=dropmenuobj.y-clearbrowseredge(obj, "bottomedge")+obj.offsetHeight+"px"
}
}

function hidetip(e){
if (typeof dropmenuobj!="undefined"){
if (ie4||ns6)
dropmenuobj.style.visibility="hidden"
}
}

function delayhidetip(){
if (ie4||ns6)
delayhide=setTimeout("hidetip()",disappeardelay)
}

function clearhidetip(){
if (typeof delayhide!="undefined")
clearTimeout(delayhide)
}

</script>
js;

function makehelp($code) {
	return <<<r
	<span style="float: right;" align="justify">
<img src="pics/help.png" class="showTip $code" /></span>
r;
}
function help($msg) {
	return <<<r
<span style="float: right;border:none;" align="justify" ><a href="javascript:;" onMouseover="fixedtooltip('$msg', this, event, '150px')" onMouseout="delayhidetip()" style="border:none;"><img style="border:none;" src="pics/help.png" /></a></span>
r;
}
if ( $type == 'main' )
{
    print ( '<link rel="stylesheet" type="text/css" href="controlpanel.css" /><body>' .
        "$notice" . '' ) ; ?>
<?php
    print ( "<form method='post' action='" . $_SERVER["SCRIPT_NAME"] .
        "'><input type='hidden' name='type' value='save_main'>" ) ;
    echo mh('Main Settings');
    tr( "Tracker online? ", makehelp("L1").select('site_online',array('yes' => 'yes','no' => 'no'),_value('SITE_ONLINE')),
        1, '', 'width=30%' ) ;
    tr( "Members Only? ", makehelp(L2).select('MEMBERSONLY',array('yes' => 'yes','no' => 'no'),_value('MEMBERSONLY')), 1, '', 'width=30%' ) ;
    tr( "Invites System Enabled? ", makehelp("L3").select('invitesystem',array('on' => 'On','off' => 'Off'),_value('INVITESYSTEM')),
        1 ) ;
    tr( "Initial Number of Invites?",
        makehelp('L4')."<input type='text' name=invite_count size=5 value="._value('invite_count').">",
        1 ) ;
    tr( "Invite Timeout?", makehelp('L5')."<input type='text' name=invite_timeout size=5 value="._value('invite_timeout').">",
        1 ) ;
    tr( "Registration System Enabled? ", makehelp('L6').select('registration',array("on" => 'On','off' => 'Off'),_value('registration')),
        1 ) ;
    tr( "Verification type?", makehelp('L7').select('verification',array('email' => 'Email','admin' => 'Admin','automatic' => 'Automaticly'),_value('verification')),
        1 ) ;
    
    tr( "Wait System Enabled?", makehelp('L8').select("waitsystem",array("yes" => "Yes","no" => "No"),_value('waitsystem')),
        1 ) ;
    tr( "Max. Concurrent Download Enabled?",
        makehelp(L9).select('maxdlsystem',array("yes" => "Yes","no" => "No"),_value('maxdlsystem')),
        1 ) ;
     tr( "Show news on index page?",
        help('You can choose to show or to hide the news in main page.').select('newsindex',array("yes" => "Show (yes)","no" => "Hide (no)"),_value('newsindex')),
        1 ) ;   
    tr( "Show Last x Forum Posts options",makehelp('L12').select('lastxfo',array('yes' => 'Show','no' => 'Hide'),_value('lastxfo'))."How many posts to show: <input type='text' size='10' name=howmuchforum value='" . (_value('howmuchforum') ?
        _value('howmuchforum') : $sh) .
        "'>",
        1 ) ;
    tr( "Show Last x Torrents options", makehelp('L13').select('lastxto',array('yes' => 'Show','no' => 'Hide'),_value('lastxto'))."How many torrents to show: <input type='text' size='10' name=howmuchtorrents value='" . (_value('howmuchtorrents') ?
        _value('howmuchtorrents') : $sh) .
        "'>"." Show: ".select('thowshow',array("text" => "As text","withimg" => "With Image"),_value('thowshow')), 1 ) ;
    tr( "Show Poll Box on index page?",
        help('You can choose to show or to hide the poll box on index page').select('pollindex',array("yes" => "Yes","no" => "No"),_value('pollindex')),
        1 ) ;
    tr( "Show Whats Goin On Box (on index page)?",
        help('You can choose to show or to hide the Whats going on box on index page').select('showgoindex',array("yes" => "Yes","no" => "No"),_value('showgoindex')),
        1 ) ;
    tr( "Show Whats Goin On Box (on forums page)?",
        makehelp('L16').select('showforumstats',array("yes" => "Yes","no" => "No"),_value('showforumstats')),
        1 ) ;
    tr( "Show Shoutbox?", makehelp('L17').select('showshoutbox',array("yes" => "Yes","no" => "No"),_value('showshoutbox')), 1 ) ;
    tr( "Show Statistics Box (on index page)?",
        help('You can choose to show or to hide the Statistics box on index page').select('statsindex',array("yes" => "Yes","no" => "No"),_value('statsindex')),
        1 ) ;
    tr( "Show Disclaimer Box (on index page)?",
        help('You can choose to show or to hide the Disclaimer box on index page').select('discindex',array("yes" => "Yes","no" => "No"),_value('discindex')),
        1 ) ;
    
    tr( "Max. Torrent Size? ",
        makehelp('L19')."<input type='text' size='45' name='max_torrent_size' value='" . (_value('max_torrent_size') ?
        _value('max_torrent_size') : "1000000") . "'>\n", 1 ) ;
    tr( "Announce Interval? ",
        makehelp(L20)."<input type='text' size='45' name=announce_interval value='" . (_value('announce_interval') ?
        _value('announce_interval') : "1800") . "'>\n", 1 ) ;
    tr( "Auto Clean Interval? ",
        makehelp(L21)."<input type='text' size='45' name=autoclean_interval value='" . (_value('autoclean_interval') ?
        _value('autoclean_interval') : "900") . "'>\n", 1 ) ;
    tr( "Signup Timeout? ",
        makehelp(L22)."<input type='text' size='45' name=signup_timeout value='" . (_value('signup_timeout') ?
        _value('signup_timeout') : "259200") . "'>\n", 1 ) ;
    tr( "Peer Limit? ", makehelp(L23)."<input type='text' size='45' name=PEERLIMIT value='" . (_value('PEERLIMIT') ?
        _value('PEERLIMIT') : "50000") . "'>\n", 1 ) ;
    tr( "Min. Votes? ", makehelp(L24)."<input type='text' size='45' name=minvotes value='" . (_value('minvotes') ?
        _value('minvotes') : "1") . "'>\n", 1 ) ;
    tr( "Max. Dead Torrent Time ? ",
       makehelp(L25) . "<input type='text' size='45' name=max_dead_torrent_time value='" . (_value('max_dead_torrent_time') ?
        _value('max_dead_torrent_time') : "21600") . "'>\n", 1 ) ;
    tr( "Max. Users? ", makehelp(L26)."<input type='text' size='45' name=maxusers value='" . (_value('maxusers') ?
        _value('maxusers') : "2500") .
        "'>\n", 1 ) ;
    tr( "Announce URL? ", "<input type='text' size='45' name=announce_urls value='" .
        (_value('announce_urls') ? _value('announce_urls') : "http://" . $_SERVER["HTTP_HOST"] .
        "/announce.php") . "'> It should be: http://" . $_SERVER["HTTP_HOST"] .
        "/announce.php\n", 1 ) ;
    tr( "Base URL? ", "<input type='text' size='45' name=BASEURL value='" . (_value('BASEURL') ?
        _value('BASEURL') : "http://" . $_SERVER["HTTP_HOST"] . "") .
        "'> It should be: http://" . $_SERVER["HTTP_HOST"] .
        " <b><u>NO</u> a trailing slash (/) at the end!</b>\n", 1 ) ;
    tr( "Default Base URL? ",
        "<input type='text' size='45' name=DEFAULTBASEURL value='" . (_value('DEFAULTBASEURL') ?
        _value('DEFAULTBASEURL') : "http://" . $_SERVER["HTTP_HOST"]) .
        "'> It should be: http://" . $_SERVER["HTTP_HOST"] .
        " <b><u>NO</u> trailing slash (/) at the end!</b>\n", 1 ) ;
    tr( "Site EMAIL? ", makehelp(L27)."<input type='text' size='45' name=SITEEMAIL value='" . (_value('SITEEMAIL') ?
        _value('SITEEMAIL') : "noreply@" . $sh) . "'>\n", 1 ) ;
    tr( "Report EMAIL? ", makehelp(L28)."<input type='text' size='45' name=reportemail value='" .
        (_value('reportemail') ? _value('reportemail') : "report@" . $sh) .
        "'>\n", 1 ) ;
    tr( "Site Name? ", makehelp(L29)."<input type='text' size='45' name=SITENAME value='" . (_value('SITENAME') ?
        _value('SITENAME') : $sh) . "'>\n", 1 ) ;
    tr( "Torrent Directory? ",
        makehelp(L30)."<input type='text' size='45' name=torrent_dir value='" . (_value('torrent_dir') ?
        _value('torrent_dir') : "fts-contents/torrents") .
        "'>\n", 1 ) ;
    tr( "Picture Directory? ",
        makehelp(L31)."<input type='text' size='45' name=pic_base_url value='" . (_value('pic_base_url') ?
        _value("pic_base_url") : "pic/") .
        "'>\n", 1 ) ;
    tr( "Category Directory? ",
        makehelp(L32)."<input type='text' size='45' name=table_cat value='" . (_value('table_cat') ? _value('table_cat') :
        "categories") . "'></b>\n",
        1 ) ;
    tr( "Bitbucket Directory? ",
        makehelp(L33)."<input type='text' size='45' name=bitbucket value='" . (_value('bitbucket') ? _value('bitbucket') :
        "fts-contents/bitbucket") . "'>\n",
        1 ) ;
    tr( "Cache Directory? ", makehelp(L34)."<input type='text' size='45' name=cache value='" . (_value('cache') ?
        _value('cache') : "fts-contents/cache") .
        "'>\n", 1 ) ;
    print ( et() . mh("Save Settings") ) ;
    tr( "Save settings",
        "<input type='submit' name='save' value='Save Main Settings [PRESS ONLY ONCE]'>\n",
        1 ) ;
    print ( "</form>" ) ;
} elseif ( $type == 'save_main' )
{
    GetVar( array('site_online', 'max_torrent_size', 'announce_interval',
        'signup_timeout', 'minvotes', 'max_dead_torrent_time', 'maxusers', 'torrent_dir',
        'announce_urls', 'BASEURL', 'DEFAULTBASEURL', 'MEMBERSONLY', 'PEERLIMIT',
        'SITEEMAIL', 'SITENAME', 'autoclean_interval', 'pic_base_url', 'table_cat',
        'reportemail', 'invitesystem', 'registration', 'showpolls', 'showstats',
        'showlastxforumposts', 'howmuchforum', 'showlastxtorrents', 'howmuchtorrents',
        'thowshow', 'showtrackerload', 'showwhatsgoinon', 'showshoutbox', 'waitsystem',
        'maxdlsystem', 'newsindex', 'showgoindex', 'pollindex', 'lastxfo', 'lastxto', 'statsindex', 'discindex', 'bitbucket', 'cache', 'showforumstats', 'verification',
        'invite_count', 'invite_timeout', 'clegend') ) ;
    $MAIN['site_online'] = $site_online ;
    $MAIN['max_torrent_size'] = $max_torrent_size ;
    $MAIN['announce_interval'] = $announce_interval ;
    $MAIN['signup_timeout'] = $signup_timeout ;
    $MAIN['minvotes'] = $minvotes ;
    $MAIN['max_dead_torrent_time'] = $max_dead_torrent_time ;
    $MAIN['maxusers'] = $maxusers ;
    $MAIN['torrent_dir'] = $torrent_dir ;
    $MAIN['announce_urls'] = $announce_urls ;
    $MAIN['BASEURL'] = $BASEURL ;
    $MAIN['DEFAULTBASEURL'] = $DEFAULTBASEURL ;
    $MAIN['MEMBERSONLY'] = $MEMBERSONLY ;
    $MAIN['PEERLIMIT'] = $PEERLIMIT ;
    $MAIN['SITEEMAIL'] = $SITEEMAIL ;
    $MAIN['SITENAME'] = $SITENAME ;
    $MAIN['autoclean_interval'] = $autoclean_interval ;
    $MAIN['pic_base_url'] = $pic_base_url ;
    $MAIN['table_cat'] = $table_cat ;
    $MAIN['reportemail'] = $reportemail ;
    $MAIN['invitesystem'] = $invitesystem ;
    $MAIN['registration'] = $registration ;
    $MAIN['showpolls'] = $showpolls ;
    $MAIN['showstats'] = $showstats ;
    $MAIN['showlastxforumposts'] = $showlastxforumposts ;
    $MAIN['howmuchforum'] = $howmuchforum ;
    $MAIN['showlastxtorrents'] = $showlastxtorrents ;
    $MAIN['howmuchtorrents'] = $howmuchtorrents ;
    $MAIN['thowshow'] = $thowshow ;
    $MAIN['showtrackerload'] = $showtrackerload ;
    $MAIN['showwhatsgoinon'] = $showwhatsgoinon ;
    $MAIN['showshoutbox'] = $showshoutbox ;
    $MAIN['waitsystem'] = $waitsystem ;
    $MAIN['maxdlsystem'] = $maxdlsystem ;
    $MAIN['newsindex'] = $newsindex;
    $MAIN['showgoindex'] = $showgoindex;
    $MAIN['pollindex'] = $pollindex;
    $MAIN['discindex'] = $discindex;
    $MAIN['lastxfo'] = $lastxfo;
    $MAIN['lastxto'] = $lastxto;
    $MAIN['statsindex'] = $statsindex;
    $MAIN['bitbucket'] = $bitbucket ;
    $MAIN['cache'] = $cache ;
    $MAIN['showforumstats'] = $showforumstats ;
    $MAIN['verification'] = $verification ;
    $MAIN['invite_count'] = $invite_count ;
    $MAIN['invite_timeout'] = $invite_timeout ;
    $MAIN['clegend'] = $clegend ;
    WriteConfig( 'MAIN', $MAIN ) ;
    $actiontime = date( "F j, Y, g:i a" ) ;
    write_log( "Tracker MAIN settings updated by $CURUSER[username]. $actiontime" ) ;
    redirect( "administrator/options.php?type=main",
        'You have succesfully modified MAIN settings at ' . $actiontime . '.', 'Success' ) ;
} elseif ( $type == 'database' )
{
    print ( '<link rel="stylesheet" type="text/css" href="controlpanel.css" /><body>' .
        "$notice" . '' ) ;
    print ( "<form method='post' action='" . $_SERVER["SCRIPT_NAME"] .
        "'><input type='hidden' name='type' value='save_database'>" ) ;
    print ( et() . mh("Tracker Database Settings") ) ;
    tr( "Mysql Host? ", makehelp(L35)."<input type='text' size='45' name=mysql_host value='" . ($DATABASE["mysql_host"] ?
        $DATABASE["mysql_host"] : "localhost") . "'>\n",
        1, '', 'width=30%' ) ;
    tr( "Mysql User? ", makehelp(L36)."<input type='text' size='45' name=mysql_user value='" . ($DATABASE["mysql_user"] ?
        $DATABASE["mysql_user"] : "root") . "'>\n", 1 ) ;
    tr( "Mysql Password? ",
        makehelp(L37)."<input type='password' size='45' name=mysql_pass value=''>\n",
        1 ) ;
    tr( "Mysql Database Name? ",
        makehelp(L38)."<input type='text' size='45' name=mysql_db value='" . ($DATABASE["mysql_db"] ?
        $DATABASE["mysql_db"] : "torrent") .
        "'>\n", 1 ) ;
    print ( et() . mh("Save Settings") ) ;
    tr( "Save settings",
        "<input type='submit' name='save' value='Save Database Settings [PRESS ONLY ONCE]'>\n",
        1 ) ;
    print ( "</form>" ) ;
} elseif ( $type == 'save_database' )
{
    GetVar( array('mysql_host', 'mysql_user', 'mysql_pass', 'mysql_db') ) ;
    $DATABASE['mysql_host'] = $mysql_host ;
    $DATABASE['mysql_user'] = $mysql_user ;
    $DATABASE['mysql_pass'] = $mysql_pass ;
    $DATABASE['mysql_db'] = $mysql_db ;
    WriteConfig( 'DATABASE', $DATABASE ) ;
    $actiontime = date( "F j, Y, g:i a" ) ;
    write_log( "Tracker database settings updated by $CURUSER[username]. $actiontime" ) ;
    redirect( "administrator/options.php?type=database",
        'You have succesfully modified DATABASE settings at ' . $actiontime . '.',
        'Success' ) ;
} elseif ( $type == 'smtp' )
{
    print ( '<link rel="stylesheet" type="text/css" href="controlpanel.css" /><body>' .
        "$notice" . '' ) ;
?>
<script src="<?= $rootpath ?>clientside/usableforms.js"></script>
<body onLoad="prepareForm()">
<table><tbody id="waitingRoom" 
      style="display: none"></tbody></table>
<form method='post'>
<input type='hidden' name='type' value='save_smtp'> 
<table width=900 class=alt1><tbody>
<tr>
  <td class="smtptype" align="right">Please select type of PHP Mail function?</td>
  <td>
    <input type="radio" name="smtptype" value="default"
      show="default" /> <font color=green>DEFAULT</font> (Use default PHP MAIL Function.)<br />
    <input type="radio" name="smtptype" value="advanced"
      show="advanced" /> <font color=blue>ADVANCED</font> (Use default PHP Mail Function with EXTRA headers, This function to help avoid spam-filters.)<br />
      <input type="radio" name="smtptype" value="external"
      show="external" /> <font color=red>EXTERNAL</font> (Use an external PHP Mail Script.)<br />
    
  </td>
</tr>
<tr relation="default">
<?php
    tr( "Save settings",
        "<input type='submit' name='save' value='Save SMTP Settings [PRESS ONLY ONCE]'>\n",
        1, "default" ) ;
?>
</tr>
<tr relation="advanced">
<?php
    tr( "SMTP Host? ", "<input type='text' size='45' name=smtp_host value='" . ($smtp_host ?
        $smtp_host : "localhost") . "'> Default: Localhost\n", 1, "advanced" ) ;
    tr( "SMTP Port? ", "<input type='text' size='45' name=smtp_port value='" . ($smtp_port ?
        $smtp_port : "25") . "'> Default: 25\n", 1, "advanced" ) ;
    if ( strtoupper(substr(PHP_OS, 0, 3) == 'WIN') ) tr( "SMTP sendmail from? ",
            "<input type='text' size='45' name=smtp_from value='" . ($smtp_from ? $smtp_from :
            "$SITEEMAIL") . "'> Default: $SITEEMAIL\n", 1, "advanced" ) ;
    else  print ( "<tr relation=\"advanced\"><td class=\"heading\" valign=\"top\" align=\"right\">Sendmail Path?</td><td valign=\"top\" align=left>Please setup your sendmail_path by editing php.ini</tr></td>" ) ;
    tr( "Save settings",
        "<input type='submit' name='save' value='Save SMTP Settings [PRESS ONLY ONCE]'> <b><u>WARNING:</u> Don't leave any fields blank!</b>\n",
        1, "advanced" ) ;
?>
</tr>
<tr relation="external">
<?php
    print ( "<tr relation=\"external\"><td align=right>Outgoing mail (SMTP) address:</td><td><input type=text name=smtpaddress size=40 " .
        ($smtpaddress ? "value=\"$smtpaddress\"" : "") .
        "> <b>hint:</b> smtp.yourisp.com</td></tr>" ) ;
    print ( "<tr relation=\"external\"><td align=right>Outgoing mail (SMTP) port:</td><td><input type=text name=smtpport size=40 " .
        ($smtpport ? "value=\"$smtpport\"" : "") . "> <b>hint:</b> 80</td></tr>" ) ;
    print ( "<tr relation=\"external\"><td align=right>Account Name:</td><td><input type=text name=accountname size=40 " .
        ($accountname ? "value=\"$accountname\"" : "") .
        "> <b>hint:</b> yourname@yourisp.com</td></tr>" ) ;
    print ( "<tr relation=\"external\"><td align=right>Account Password:</td><td><input type=password name=accountpassword size=40 " .
        ($accountpassword ? "value=\"$accountpassword\"" : "") .
        "> <b>hint:</b> your password goes here</td></tr>" ) ;
    tr( "Save settings",
        "<input type='submit' name='save' value='Save SMTP Settings [PRESS ONLY ONCE]'>  <b><u>WARNING:</u> Don't leave any fields blank!</b>\n",
        1, "external" ) ;
?>
</tr>
</tbody></table></td>
</tr>
</form>
<?php
    print ( "<script language=\"JavaScript\">
function openWindow() {
popupWin=window.open('$BASEURL/mailtest.php','mailtest','width=750,height=450,top=300,left=300')
}
</script>" ) ;
    print ( "<tr><td colspan=2>How can I test php mail? Its simple easy: Click <a href=\"javascript:openWindow();\">here</a>.</tr></td>" ) ;
} elseif ( $type == 'save_smtp' )
{
    GetVar( 'smtptype' ) ;
    if ( $smtptype == 'default' )
    {
        $SMTP['smtptype'] = $smtptype ;
    } elseif ( $smtptype == 'advanced' )
    {
        GetVar( array('smtp_host', 'smtp_port', 'smtp_from') ) ;
        $SMTP['smtptype'] = $smtptype ;
        $SMTP['smtp_host'] = $smtp_host ;
        $SMTP['smtp_port'] = $smtp_port ;
        if ( strtoupper(substr(PHP_OS, 0, 3) == 'WIN') ) $SMTP['smtp_from'] = $smtp_from ;
    } elseif ( $smtptype == 'external' )
    {
        GetVar( array('smtpaddress', 'smtpport', 'accountname', 'accountpassword') ) ;
        $SMTP['smtptype'] = $smtptype ;
        $SMTP['smtpaddress'] = $smtpaddress ;
        $SMTP['smtpport'] = $smtpport ;
        $SMTP['accountname'] = $accountname ;
        $SMTP['accountpassword'] = $accountpassword ;
    }
    WriteConfig( 'SMTP', $SMTP ) ;
    $actiontime = date( "F j, Y, g:i a" ) ;
    write_log( "Tracker SMTP settings updated by $CURUSER[username]. $actiontime" ) ;
    redirect( "administrator/options.php?type=smtp",
        'You have succesfully modified SMTP settings at ' . $actiontime . '.', 'Success' ) ;
} elseif ( $type == 'template' )
{
    readconfig( 'TEMPLATE' ) ;
    $template_dirs = dir_list( $rootpath . 'fts-contents/templates' ) ;
    print ( '<link rel="stylesheet" type="text/css" href="controlpanel.css" /><body>' .
        "$notice" . '' ) ;
    print ( "<form method='post' action='" . $_SERVER["SCRIPT_NAME"] .
        "'><input type='hidden' name='type' value='save_template'>" ) ;
    print ( et() . mh("Main Template Settings") ) ;
    tr( 'Set Default Template',
        '<a href="templatechoose.php">Click here to choose the default template of the site.</a>',
        1, '', 'width=30%') ;
    tr( 'Reset all themes to default',
        'Some users might have set-up a diferent theme as default theme in user cp. Click <a href=' .
        $BASEURL . '/admin/resetheme.php>here</a> to set all those themes to default.',
        1 ) ;
    tr( "Character Set", "<input type='text' size='45' name=charset value='" . ($TEMPLATE["charset"] ?
        $TEMPLATE["charset"] : "UTF-8") . "'> Charset of the site<br /><a href=\"http://www.w3.org/International/O-charset-lang.html\" target=\"_blank\">Click here to find the charset of your language.</a>.\n",
        1 ) ;
    tr( "Meta Description", "<input type='text' size='45' name=metadesc value='" . ($TEMPLATE["metadesc"] ?
        $TEMPLATE["metadesc"] : "") . "'> Description of your website:
	Helps your website's position in search engines..\n", 1 ) ;
    tr( "Meta keywords ", "<input type='text' size='45' name=metakeywords value='" .
        ($TEMPLATE["metakeywords"] ? $TEMPLATE["metakeywords"] : "") .
        "'> Type in keywords separated by commas that describe your website.<br />These keywords will help your site be listed in search engines.\n",
        1 ) ;
    print ( et() . mh("Save Settings") ) ;
    tr( "Save settings",
        "<input type='submit' name='save' value='Save Template Settings [PRESS ONLY ONCE]'>\n",
        1 ) ;
    print ( "</form>" ) ;
} elseif ( $type == 'save_template' )
{
    GetVar( array('defaulttemplate', 'charset', 'metadesc', 'metakeywords') ) ;
    $TEMPLATE['defaulttemplate'] = $defaulttemplate ;
    $TEMPLATE['charset'] = $charset ;
    $TEMPLATE['metadesc'] = $metadesc ;
    $TEMPLATE['metakeywords'] = $metakeywords ;
    WriteConfig( 'TEMPLATE', $TEMPLATE ) ;
    $actiontime = date( "F j, Y, g:i a" ) ;
    write_log( "Tracker TEMPLATE settings updated by $CURUSER[username]. $actiontime" ) ;
    redirect( "administrator/options.php?type=template",
        'You have succesfully modified TEMPLATE settings at ' . $actiontime . '.',
        'Success' ) ;
} elseif ( $type == 'tweak' )
{
    global $shoutname ;
    print ( '<link rel="stylesheet" type="text/css" href="controlpanel.css" /><body>' .
        "$notice" . '' ) ;
    print ( "<form method='post' action='" . $_SERVER["SCRIPT_NAME"] .
        "'><input type='hidden' name='type' value='save_tweak'>" ) ;
    print ( et() . mh('Tweak Options') ) ;
    tr( "Save User Location?", makehelp(L39).select('where',array("yes" => "Yes","no" => "No"),$TWEAK['where']),
        1 , '', 'width=30%') ;
    tr( "Save User IPs?", makehelp('L40').select('iplog1',array("yes" => "Yes","no" => "No"),$TWEAK['iplog1']),
        1 ) ;
    tr( "Store IPs?", makehelp('L41').select('iplog2',array("yes" => "Yes","no" => "No"),$TWEAK['iplog2']),
        1 ) ;
    tr( "Cracker Tracker Protection System Enabled?",
        makehelp('L42').select('ctracker',array("yes" => "Yes","no" => "No"),$TWEAK['ctracker']),
        1 ) ;
    tr( "Karma Bonus Point System (KPS) Enabled?",
        makehelp('L43').select('bonus',array("enable" => "Enable","disablesave" => "disable (BUT: save points, disable trade)","disable" => "disable (do not use KPS)"),$TWEAK['bonus']),
        1 ) ;
    tr( "Left Menu enabled?", makehelp('L45').select('leftmenu',array("yes" => "Yes","no" => "No"),$TWEAK['leftmenu']).' Enable Left Menu for Non-Logged in users '.select('leftmenunl',array("yes" => "Yes","no" => "No"),$TWEAK['leftmenunl']), 1 ) ;
    tr( "Split torrent by days", makehelp('L46').select('splitor',array("yes" => "Yes","no" => "No"),$TWEAK['splitor']),
        1 ) ;
    tr( "ShoutBot NickName", makehelp('L47')."<input type='text' size='45' name=shoutname value='" .
        ($shoutname ? $shoutname : "$SITENAME-Bot") . "'>", 1 ) ;
    tr( "ShoutBot Duties", makehelp('L48')."<input type='text' size='45' name=shoutduty value='" .
        ($shoutduty ? $shoutduty : "torrents,cleanups,requests,users,topics") . "'>", 1 ) ;
    tr( "Image resizer mode",
        help('Decide how image resizer should work.<BR><b>None</b> - Do not allow users to see the large image<br><b>Samewindow</b> - Open large image in the same window<br><b>Newwindow</b> - Open large image in a new window<br><b>Enlarge</b> - Enlarge the image').select('imageresizermode',array("none" => "None","samewindow" => "Samewindow","newwindow" => "Newwindow","enlarge" => "Enlarge"),$imageresizermode)."See help (the icon in the right)",
        1 ) ;
    print ( et() . mh("Save Settings") ) ;
    tr( "Save settings",
        "<input type='submit' name='save' value='Save Main Settings [PRESS ONLY ONCE]'>\n",
        1 ) ;
    print ( "</form>" ) ;
} elseif ( $type == 'save_tweak' )
{
    GetVar( array('where', 'iplog1', 'iplog2', 'ctracker', 'bonus', 'leftmenu', 'leftmenunl', 'splitor',
        'shoutname', 'shoutduty','imageresizermode') ) ;
    $TWEAK['where'] = $where ;
    $TWEAK['iplog1'] = $iplog1 ;
    $TWEAK['iplog2'] = $iplog2 ;
    $TWEAK['ctracker'] = $ctracker ;
    $TWEAK['bonus'] = $bonus ;
    $TWEAK['leftmenu'] = $leftmenu ;
    $TWEAK['leftmenunl'] = $leftmenunl ;  
    $TWEAK['shoutname'] = $shoutname ;
    $TWEAK['splitor'] = $splitor ;
    $TWEAK['shoutduty'] = $shoutduty ;
    $TWEAK['imageresizermode'] = $imageresizermode;
    WriteConfig( 'TWEAK', $TWEAK ) ;
    $actiontime = date( "F j, Y, g:i a" ) ;
    write_log( "Tracker TWEAK settings updated by $CURUSER[username]. $actiontime" ) ;
    redirect( "administrator/options.php?type=tweak",
        'You have succesfully modified TWEAK settings at ' . $actiontime . '.',
        'Success' ) ;
}
if ( $type == 'mods' )
{
    print ( '<link rel="stylesheet" type="text/css" href="controlpanel.css" /><body>' .
        "$notice" . '' ) ;
    print ( "<form method='post' action='" . $_SERVER["SCRIPT_NAME"] .
        "'><input type='hidden' name='type' value='save_mods'>" ) ;
    print ( et() . mh('Mods Settings') ) ;
    tr( "Enable upload page integrated with imdb",
        makehelp('L49').select('imdbupload',array("yes" => "Yes","no" => "No"),$MODS['imdbupload']),
        1 ) ;

    tr( "Enable torrent progress hack", makehelp('L50').select('tproghack',array("yes" => "Yes","no" => "No"),$MODS['tproghack']),
        1 ) ;
    tr( "Enable Referral System", makehelp('L61').select('referralsys',array("yes" => "Yes","no" => "No"),$MODS['referralsys']),
        1 ) ;
    tr( "Enable Zip-Torrent-Download Mod",
        help('This will create an zip file when you download an torrent and insert in it the torrent file and a txt file saying your site\\\'s url. Also, it will include torrent\\\'s nfo if it has one.').select('enablezipmode',array("yes" => "Yes","no" => "No"),$MODS['enablezipmode']) . 'Include TXT file '.select('ziptxt',array("yes" => "Yes","no" => "No"),$MODS['ziptxt']).' Include NFO File(if exists) '.select('zipnfo',array("yes" => "Yes","no" => "No"),$MODS['zipnfo']),
        1 ) ;
        
    tr( "Enable comment poll in forums hack", makehelp('L51').select('pollf',array("yes" => "Yes","no" => "No"),$MODS['pollf'])." Create forum theard in topic id # <input type='text' size='5' name=pollfid value='" . ($MODS["pollfid"] ?
        $MODS["pollfid"] : "") . "'>",
        1 ) ;
    tr( "Enable search cloud", makehelp('L52').select('searchcloud',array("yes" => "Yes","no" => "No"),$MODS['searchcloud']),
        1 ) ;
    tr( "News show mode", makehelp('L60').select('newsmode',array("old" => "Old mode(version 1.0.1 and below)","new" => "New mode(from version 1.0.2)"),$MODS['newsmode']),
        1 ) ;
    tr( "Enable YouTube to Torrents Mod", makehelp('L62').select('youtubemod',array("yes" => "Yes","no" => "No"),$MODS['youtubemod']),
        1 ) ;
    print ( et() . mh("Save Settings") ) ;
    tr( "Save settings",
        "<input type='submit' name='save' value='Save Mods Settings [PRESS ONLY ONCE]'>\n",
        1 ) ;
    print ( "</form>" ) ;
} elseif ( $type == 'save_mods' )
{
    GetVar( array('imdbupload', 'tproghack', 'pollf', 'pollfid', 'searchcloud','newsmode','referralsys','youtubemod','enablezipmode','ziptxt','zipnfo') ) ;
    $MODS['imdbupload'] = $imdbupload ;
    $MODS['tproghack'] = $tproghack ;
    $MODS['pollf'] = $pollf ;
    $MODS['pollfid'] = $pollfid ;
    $MODS['searchcloud'] = $searchcloud ;
    $MODS['newsmode'] = $newsmode;
    $MODS['referralsys'] = $referralsys;
    $MODS['youtubemod'] = $youtubemod;
    $MODS['enablezipmode'] = $enablezipmode;
    $MODS['ziptxt'] = $ziptxt;
    $MODS['zipnfo'] = $zipnfo;
    WriteConfig( 'MODS', $MODS ) ;
    $actiontime = date( "F j, Y, g:i a" ) ;
    write_log( "Tracker MODS settings updated by $CURUSER[username]. $actiontime" ) ;
    redirect( "administrator/options.php?type=mods",
        'You have succesfully modified MODS settings at ' . $actiontime . '.', 'Success' ) ;
} 
if ( $type == 'reCAPTCHA' )
{
    print ( '<link rel="stylesheet" type="text/css" href="controlpanel.css" /><body>' .
        "$notice" . '' ) ;
    print ( "<form method='post' action='" . $_SERVER["SCRIPT_NAME"] .
        "'><input type='hidden' name='type' value='save_reCAPTCHA'>" ) ;
    print ( et() . mh('Integration with reCAPTHCA') ) ;
    echo <<<online
<tr><td colspan=2>	
<b>reCAPTCHA</b> is a free CAPTCHA service that helps to digitize books.<br />
<b>reCAPTCHA</b> looks like this: <img src='http://recaptcha.net/images/smallCaptchaSpaceWithRoughAlpha.png'/><BR>
Please be aware, in order for reCAPTCHA to work on this domain, <b>you must obtain an Public Key and Private Key from <a href=https://admin.recaptcha.net/recaptcha/sites/>https://admin.recaptcha.net/recaptcha/sites/</a></b>.<BR>
<b>Also, for this to work the captcha system must be ON(see security settings)</b>
</td></tr>
online;
    tr( "Enable reCAPTCHA mod",
        help('Turn on or off the reCAPTCHA system').select('reCAPTCHA_enable',array("yes" => "Yes","no" => "No"),get('reCAPTCHA_enable')),
        1 ) ;
        
    tr( "Public Key", help('You must get this key from www.recaptcha.net site(see above for details)')."<input type='text' size='50' name=reCAPTCHA_publickey value='" . (get('reCAPTCHA_publickey') ?
        get('reCAPTCHA_publickey') : "") . "'>",
        1 ) ;
    tr( "Private Key", help('You must get this key from www.recaptcha.net site(see above for details)')."<input type='text' size='50' name=reCAPTCHA_privatekey value='" . (get('reCAPTCHA_privatekey') ?
        get('reCAPTCHA_privatekey') : "") . "'>",
        1 ) ;
    print ( et() . mh("Save Settings") ) ;
    tr( "Save settings",
        "<input type='submit' name='save' value='Save Mods Settings [PRESS ONLY ONCE]'>\n",
        1 ) ;
    print ( "</form>" ) ;
} elseif ( $type == 'save_reCAPTCHA' )
{
    GetVar( array('reCAPTCHA_enable','reCAPTCHA_publickey','reCAPTCHA_privatekey') ) ;
    if(!empty($reCAPTCHA_enable)) {
    update('reCAPTCHA_enable',$reCAPTCHA_enable);
    Ffactory::reset_cache("reCAPTCHA_enable",'databasevalue');
    }
    if(!empty($reCAPTCHA_publickey)) {
    update('reCAPTCHA_publickey',$reCAPTCHA_publickey);
    Ffactory::reset_cache("reCAPTCHA_publickey",'databasevalue');
    }
    if(!empty($reCAPTCHA_privatekey)) {
    update('reCAPTCHA_privatekey',$reCAPTCHA_privatekey);
    Ffactory::reset_cache("reCAPTCHA_privatekey",'databasevalue');
    }
    $actiontime = date( "F j, Y, g:i a" ) ;
    write_log( "Tracker reCAPTCHA settings updated by $CURUSER[username]. $actiontime" ) ;
    redirect( "administrator/options.php?type=reCAPTCHA",
        'You have succesfully modified reCAPTCHA settings at ' . $actiontime . '.', 'Success' ) ;
}
elseif ( $type == 'security' )
{
    print ( '<link rel="stylesheet" type="text/css" href="controlpanel.css" /><body>' .
        "$notice" . '' ) ;
    print ( "<form method='post' action='" . $_SERVER["SCRIPT_NAME"] .
        "'><input type='hidden' name='type' value='save_security'>" ) ;
    print ( et() . mh("Security Settings") ) ;
    tr( "Secure hash", makehelp('L63')."<input type='text' size='45' name=sechash value='" .
        ($SECURITY['sechash'] ? $SECURITY['sechash'] : "123456") . "'>", 1 ) ;
	tr( "Secure Login enable?", makehelp('L53').select('securelogin',array("yes" => "Yes","op" => "Optional (selectable by users)","no" => "No"),$SECURITY['securelogin']), 1 ) ;
    tr( "Private Tracker Patch enable?", makehelp('L54').select('privatep',array("yes" => "Yes","no" => "No"),$SECURITY['privatep']), 1 ) ;
    tr( "Image Verification enable?", makehelp('L55').select('iv',array("yes" => "Yes","no" => "No"),$SECURITY['iv']),
        1 ) ;
        #makehelp('L').select('',array("" => "","" => ""),$SECURITY[''])
    tr( "Disable right mouse click?",
        makehelp('L56').select('disablerightclick',array("yes" => "Yes","no" => "No"),$SECURITY['disablerightclick']),
        1 ) ;
    tr( "Enable Virtual Keyboard system?", makehelp('L57').select('vkeysys',array("yes" => "Yes","no" => "No"),$SECURITY['vkeysys']),
        1 ) ;
    tr( "Max. IPs? ", makehelp('L58')."<input type='text' size='7' name=maxip value='" . ($SECURITY["maxip"] ?
        $SECURITY["maxip"] : "1") . "'>\n", 1 ) ;
    tr( "Max. Login Attempts? ",
        makehelp('L59')."<input type='text' size='7' name=maxloginattempts value='" . ($SECURITY["maxloginattempts"] ?
        $SECURITY["maxloginattempts"] : "7") .
        "'>\n", 1 ) ;
    print ( et() . mh("Save Settings") ) ;
    tr( "Save settings",
        "<input type='submit' name='save' value='Save SECURITY Settings [PRESS ONLY ONCE]'>\n",
        1 ) ;
    print ( "</form>" ) ;
} elseif ( $type == 'save_security' )
{
    GetVar( array('securelogin', 'iv', 'maxip', 'maxloginattempts',
        'disablerightclick', 'vkeysys', 'privatep','sechash') ) ;
    $SECURITY['securelogin'] = $securelogin ;
    $SECURITY['iv'] = $iv ;
    $SECURITY['maxip'] = $maxip ;
    $SECURITY['maxloginattempts'] = $maxloginattempts ;
    $SECURITY['disablerightclick'] = $disablerightclick ;
    $SECURITY['vkeysys'] = $vkeysys ;
    $SECURITY['privatep'] = $privatep ;
    $SECURITY['sechash'] = $sechash;
    WriteConfig( 'SECURITY', $SECURITY ) ;
    $actiontime = date( "F j, Y, g:i a" ) ;
    write_log( "Tracker SECURITY settings updated by $CURUSER[username]. $actiontime" ) ;
    redirect( "administrator/options.php?type=security",
        'You have succesfully modified SECURITY settings at ' . $actiontime . '.',
        'Success' ) ;
} elseif ( $type == 'transfer' )
{
    $transfer_enable = get( 'transfer_enable' ) ;
    $transfer_usergroups = get( 'transfer_usergroups' ) ;
    print ( '<link rel="stylesheet" type="text/css" href="controlpanel.css" /><body>' .
        "$notice" . '' ) ;
    print ( "<form method='post' action='" . $_SERVER["SCRIPT_NAME"] .
        "'><input type='hidden' name='type' value='savetransfer'>" ) ;
    print ( et() . mh("Main Transfer Settings") ) ;
    tr( "Enable Ratio Transfer mod?",
        "yes <INPUT type='radio' name='transfer_enable'" . ($transfer_enable == "yes" ?
        " checked" : " checked") .
        " value='yes'> no <INPUT type='radio' name='transfer_enable'" . ($transfer_enable ==
        "no" ? " checked" : "") .
        " value='no'> For staff only <INPUT type='radio' name='transfer_enable'" . ($transfer_enable ==
        "op" ? " checked" : "") . " value='op'> Only some usergroups(will activate another setting!) <INPUT type='radio' name='transfer_enable'" . ($transfer_enable ==
        "ug" ? " checked" : "") . " value='ug'>\n", 1 ) ;
        if($transfer_enable == 'ug')
    tr("Allow only the following usergroups(enter id's) ","<input type='text' size='45' name=transfer_usergroups value='" . ($transfer_usergroups ? $transfer_usergroups : "1,2,3,4,5,6,7")."'> Separated by comma(,).Must pe acivated in the previous setting.\n", 1);
    print ( et() . mh("Save Settings") ) ;
    tr( "Save settings",
        "<input type='submit' name='save' value='Save TRANSFER Settings [PRESS ONLY ONCE]'>\n",
        1 ) ;
    print ( "</form>" ) ;
} elseif ( $type == 'savetransfer' )
{
    GetVar( array('transfer_enable','transfer_usergroups') ) ;
    if(!empty($transfer_enable)) {
    update( 'transfer_enable', $transfer_enable ) ;
    Ffactory::reset_cache("transfer_enable",'databasevalue');
    }
    if(!empty($transfer_usergroups)) {
    update( 'transfer_usergroups', $transfer_usergroups ) ;
    Ffactory::reset_cache("transfer_usergroups",'databasevalue');
    }
    $actiontime = date( "F j, Y, g:i a" ) ;
    write_log( "Tracker Transfer settings updated by $CURUSER[username]. $actiontime" ) ;
    redirect( "administrator/options.php?type=transfer",
        'You have succesfully modified TRANSFER settings at ' . $actiontime . '.',
        'Success' ) ;
} elseif ( $type == 'cache' )
{
	$cache_index_news = get('cache_index_news');
	$cache_index_stats = get('cache_index_stats');
	$cache_topten = get('cache_topten');
	$cache_admin_stats = get('cache_admin_stats');
	$cache_admin_vcheck = get('cache_admin_vcheck');
    print ( '<link rel="stylesheet" type="text/css" href="controlpanel.css" /><body>' .
        "$notice" . '' ) ;
    print ( "<form method='post' action='" . $_SERVER["SCRIPT_NAME"] .
        "'><input type='hidden' name='type' value='savecache'>" ) ;
    print ( et() . mh("Cache on index page!") ) ;
   tr("News Cache time","<input type='text' size='45' name=cache_index_news value='" . ($cache_index_news ? $cache_index_news : "600")."'> Time in seconds.\n", 1);
   tr("Statistics time","<input type='text' size='45' name=cache_index_stats value='" . ($cache_index_stats ? $cache_index_stats : "600")."'> Time in seconds.\n", 1);
   print ( et() . mh("Cache on top ten page!") ) ;
      tr("General cache time","<input type='text' size='45' name=cache_topten value='" . ($cache_topten ? $cache_topten : "3600")."'> Time in seconds.\n", 1);
      print ( et() . mh("Cache on staff page!") ) ;
      tr("Stats cache time","<input type='text' size='45' name=cache_admin_stats value='" . ($cache_admin_stats ? $cache_admin_stats : "900")."'> Time in seconds.\n", 1);
      tr("Version check cache time","<input type='text' size='45' name=cache_admin_vcheck value='" . ($cache_admin_vcheck ? $cache_admin_vcheck : "300")."'> Time in seconds.\n", 1);
    print ( et() . mh("Save Settings") ) ;
    tr( "Save settings",
        "<input type='submit' name='save' value='Save Cache Settings [PRESS ONLY ONCE]'>\n",
        1 ) ;
    print ( "</form>" ) ;
} elseif ( $type == 'savecache' )
{
    GetVar( array('cache_index_news','cache_index_stats','cache_topten','cache_admin_stats','cache_admin_vcheck') ) ;
    if(!empty($cache_index_news)) {
    update('cache_index_news',$cache_index_news);
    Ffactory::reset_cache("cache_index_news",'databasevalue');
    }
    if(!empty($cache_index_stats)) {
    update('cache_index_stats',$cache_index_stats);
    Ffactory::reset_cache("cache_index_stats",'databasevalue');
    }
    if(!empty($cache_topten)) {
    update('cache_topten',$cache_topten);
    Ffactory::reset_cache("cache_topten",'databasevalue');
    }
    if(!empty($cache_admin_stats)) {
    update('cache_admin_stats',$cache_admin_stats);
    Ffactory::reset_cache("cache_admin_stats",'databasevalue');
    }
    if(!empty($cache_admin_vcheck)) {
    update('cache_admin_vcheck',$cache_admin_vcheck);
    Ffactory::reset_cache("cache_admin_vcheck",'databasevalue');
    }
    $actiontime = date( "F j, Y, g:i a" ) ;
    write_log( "Tracker Cache settings updated by $CURUSER[username]. $actiontime" ) ;
    redirect( "administrator/options.php?type=cache",
        'You have succesfully modified Cache settings at ' . $actiontime . '.',
        'Success' ) ;
}elseif( $type == 'payment' ) {
	$payment_paypal_email = @get('payment_paypal_email');
    print ( '<link rel="stylesheet" type="text/css" href="controlpanel.css" /><body>' .
        "$notice" . '' ) ;
    print ( "<form method='post' action='" . $_SERVER["SCRIPT_NAME"] .
        "'><input type='hidden' name='type' value='save_payment'>" ) ;
    print ( et() . mh("General PAYPAL Settings") ) ;
tr( "Enable payments through paypal?", help('If you do not have an paypal account, you must choose no.').select('payment_paypal_enable',array("yes" => "Yes","no" => "No"),@get('payment_paypal_enable')),
        1 ) ;
   tr("PayPal E-Mail Address","<input type='text' size='45' name=payment_paypal_email value='" . ($payment_paypal_email ? $payment_paypal_email : "")."'> Your paypal email address where users will donate.\n", 1);
   tr("Donation Amounts","<input type='text' size='45' name=payment_paypal_amounts value='" . (get('payment_paypal_amounts') ? get('payment_paypal_amounts') : "5:10:15:20:25:30:50:100")."'> Enter amounts separated by \":\"(eg:5:10:15:20).\n", 1);
   tr("Curency","<input type='text' size='20' name=payment_paypal_curency value='" . (get('payment_paypal_curency') ? get('payment_paypal_curency') : "USD")."'> Enter your curency(eg: USD,EUR).\n", 1);
   print ( et() . mh("General Wire Transfer Settings") ) ;
   tr( "Enable payments through wire transfer?", help('You can choose if you want to show wire transfer details.').select('payment_wire_enable',array("yes" => "Yes","no" => "No"),@get('payment_wire_enable')),
        1 ) ;
	tr("Wire transfer details","<textarea name=payment_wire_details cols=85 rows=25 id=payment_wire_details>".get('payment_wire_details')."</textarea>",1);
	JsB::wysiwyg('payment_wire_details');
    print ( et() . mh("Save Settings") ) ;
    tr( "Save settings",
        "<input type='submit' name='save' value='Save Cache Settings [PRESS ONLY ONCE]'>\n",
        1 ) ;
    print ( "</form>" ) ;
}elseif( $type == 'save_payment' ) {
	GetVar( array('payment_paypal_email','payment_paypal_enable','payment_paypal_curency','payment_paypal_amounts','payment_wire_enable','payment_wire_details') ) ;
    if(!empty($payment_paypal_email)) {
    update('payment_paypal_email',$payment_paypal_email);
    Ffactory::reset_cache("payment_paypal_email",'databasevalue');
    }
    if(!empty($payment_paypal_enable)) {
    update('payment_paypal_enable',$payment_paypal_enable);
    Ffactory::reset_cache("payment_paypal_enable",'databasevalue');
    }
    if(!empty($payment_paypal_amounts)) {
    update('payment_paypal_amounts',$payment_paypal_amounts);
    Ffactory::reset_cache("payment_paypal_amounts",'databasevalue');
    }
    if(!empty($payment_paypal_curency)) {
    update('payment_paypal_curency',$payment_paypal_curency);
    Ffactory::reset_cache("payment_paypal_curency",'databasevalue');
    }
    if(!empty($payment_wire_enable)) {
    update('payment_wire_enable',$payment_wire_enable);
    Ffactory::reset_cache("payment_wire_enable",'databasevalue');
    }
    _u('payment_wire_details',b);
    Ffactory::reset_cache("payment_wire_details",'databasevalue');
    $actiontime = date( "F j, Y, g:i a" ) ;
    write_log( "Tracker Payment settings updated by $CURUSER[username]. $actiontime" ) ;
    redirect( "administrator/options.php?type=payment",
        'You have succesfully modified Payment settings at ' . $actiontime . '.',
        'Success' ) ;
}elseif( $type == 'pg' ) {
	$pgs = @get('pg_server');
    print ( '<link rel="stylesheet" type="text/css" href="controlpanel.css" /><body>' .
        "$notice" . '' ) ;
    print ( "<form method='post' action='" . $_SERVER["SCRIPT_NAME"] .
        "'><input type='hidden' name='type' value='save_pg'>" ) ;
    print ( et() . mh("General PeerGuardian Settings") ) ;
tr( "Enable PeerGuardian Service?", help('This service can protect you from Anti-P2P organizations').select('pg_enable',array("yes" => "Yes","no" => "No"),@get('pg_enable')),
        1 ) ;
   tr("PeerGuardian Server","<input type='text' size='45' name=pg_server value='" . ($pgs ? $pgs : "http://freetosu.sourceforge.net/pg.txt")."'> The official is http://freetosu.sourceforge.net/pg.txt .\n", 1);
   print ( et() . mh("Import data in PeerGuardian database") ) ;
       echo <<<online
<tr><td colspan=2>	
To get data from the server specified above and insert it in your database(the old data will be deleted), click <a href=importpg.php><b>here</b></a>
</td></tr>
online;
    print ( et() . mh("Save Settings") ) ;
    tr( "Save settings",
        "<input type='submit' name='save' value='Save Settings [PRESS ONLY ONCE]'>\n",
        1 ) ;
    print ( "</form>" ) ;
}elseif( $type == 'save_pg' ) {
	GetVar( array('pg_enable','pg_server') ) ;
    _u('pg_enable');
    _u('pg_server');
    $actiontime = date( "F j, Y, g:i a" ) ;
    write_log( "Tracker Payment settings updated by $CURUSER[username]. $actiontime" ) ;
    redirect( "administrator/options.php?type=pg",
        'You have succesfully modified PeerGuardian settings at ' . $actiontime . '.',
        'Success' ) ;
}
function mh( $message = "", $bgcolor = "#81A2C4" )
{
    $notice = "<table border=1 cellspacing=0 cellpadding=10 width=100%><tr><td style='padding: 10px;' class=tcat>
<font color=black><center><b>$message</b></b>
</font></center></td></tr></table><table border=1 cellspacing=0 cellpadding=10 width=100% class=alt1>" ;
    return $notice ;
}
function et()
{
    return "</table>" ;
}
function _u($n,$type = 'a') {
	if($type == 'a'){
	global $$n;
if(!empty($$n)) {
    update("$n",$$n);
    Ffactory::reset_cache("$n",'databasevalue');
    }
	}else{
	global $$n;
if(!empty($$n)) {
    update("$n",$$n);
    Ffactory::reset_cache("$n",'databasevalue');
    }
    else
    del($n);
	}	
}
?>