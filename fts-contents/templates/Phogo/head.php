<?php
$path_ = "$BASEURL/fts-contents/templates/Phogo/";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="en">
<head>
<?template::inhead($title);?>
<title><?= $title ?></title>
<link rel="stylesheet" href="<?=$BASEURL?>/fts-contents/templates/Phogo/Phogo.css" type="text/css">
<link rel="alternate" type="application/rss+xml" title="RSS" href="<?=$BASEURL?>/rss.php">
<link rel="shortcut icon" href="<?=$DEFAULTBASEURL;?>/favicon.ico" type="image/x-icon">
<script type="text/javascript" src="<?=$BASEURL?>/fts-contents/templates/Phogo/c_config.js"></script>
<script type="text/javascript" src="<?=$BASEURL?>/fts-contents/templates/Phogo/c_smartmenus.js"></script>
</head>
<body class="yui-skin-sam">
<table width="960px" align="center" border="0" cellspacing="0" cellpadding="0" style="background: transparent;">
<tr>
<td class="header" width="50%" background="<?=$path_;?>images/header-top.gif">
<a href="<?=$BASEURL;?>/"><img style="border: none" alt="<?=$SITENAME?>" title="<?=$SITENAME?>" src="<?=$path_;?>images/logo.gif" /></a>
</td>
</td>
</tr>
</table>
<!-- Top Navigation Menu for unregistered-->
<table width="960px" align="center" border="0" cellspacing="0" cellpadding="2">
  <td class="topnav" id="menu">
  <p>
  <?php if ($CURUSER) { ?>
  </p>
  <div class="clearFix" style="padding:0;" align="center">
    <ul id="Menu1" class="MM">
      <li><a class="navlink"href="<?=$BASEURL;?>/">Home</a></li>
      <li><a href="<?=$BASEURL;?>/browse.php">Torrents</a>
        <ul>
          <li><a href="<?=$BASEURL;?>/upload.php">Upload</a></li>
          <li><a href="<?=$BASEURL;?>/search.php">Search</a></li>
          <li><a href="<?=$BASEURL;?>/viewrequests.php">Requests</a></li>
          <li><a href="<?=$BASEURL;?>/viewoffers.php">Offers</a></li>
          <li><a href="<?=$BASEURL;?>/topten.php">TopTen</a></li>
          <li><a href="<?=$BASEURL;?>/mytorrents.php">My Torrents</a></li>
        </ul>
      </li>
      <li><a href="<?=$BASEURL;?>/forums">Forums</a>
        <ul>
          <li><a href="<?=$BASEURL;?>/forums/viewunread.php">New Posts</a></li>
          <li><a href="<?=$BASEURL;?>/forums/search.php">Search</a></li>
        </ul>
      </li>
<li><a href="<?=$BASEURL;?>/usercp.php">UserCP</a>
        <ul>
          <li><a href="<?=$BASEURL;?>/messages.php">Messages</a></li>
          <li><a href="<?=$BASEURL;?>/invite.php">Invites</a></li>
          <li><a href="<?=$BASEURL;?>/friends.php">Friends</a></li>
          <li><a href="<?=$BASEURL;?>/mybonus.php">Bounus</a></li>
          <li><a href="<?=$BASEURL;?>/viewrequests.php">My Requests</a></li>
          <li><a href="<?=$BASEURL;?>/members.php">Members</a></li>
        </ul>
</li>
<li><a href="<?=$BASEURL;?>/rules.php">Rules</a></li>
<li><a href="<?=$BASEURL;?>/faq.php">FAQ</a></li>
<li><a href="<?=$BASEURL;?>/staff.php">Staff</a>
  <ul>
    <li><a href="<?=$BASEURL;?>/contactstaff.php">Contact Staff</a></li>
    </ul>
</li>
<li><a href="#">Other</a>
  <ul>
    <li><a href="<?=$BASEURL;?>/topten.php">TopTen</a></li>
    <li><a href="<?=$BASEURL;?>/transfer.php">Ratio Transfer</a></li>
    <li><a href="<?=$BASEURL;?>/donate.php">Donate</a></li>
    <li><a href="<?=$BASEURL;?>/subs.php">Subtitles</a></li>
    <li><a href="<?=$BASEURL;?>/uploaderform.php">Uploader Application</a></li>
  </ul>
</li></td>
</table>
<?php } ?>
<!-- Sample menu definition -->
<!-- /////// Top Navigation Menu for unregistered-->

<!-- /////// some vars for the statusbar;o) //////// -->

<?php if ($CURUSER) { ?>

<!-- //////// start the statusbar ///////////// -->

</table>
<table align="center" cellpadding="4" cellspacing="0" border="0" style="width:960px"class="none">
<td class="tablea"><table align="center" style="width:100%" cellspacing="0" cellpadding="0" border="0">
<td class="none" align="left"><span class="smallfont">Welcome back, <b><a href="<?=$BASEURL;?>/userdetails.php?id=<?=$CURUSER['id']?>"><?=$CURUSER['username']?></a></b><?=$medaldon?><?=$warn?> <? if ($usergroups['canstaffpanel'] == 'yes') { ?> [<a href="<?=$BASEURL;?>/admin">Staff Panel</a>] <?}?> <? if ($usergroups['cansettingspanel'] == 'yes') { ?> [<a href="<?=$BASEURL;?>/administrator">Site Settings</a>] [<a href="<?=get_administrator_path(0);?>?page=ug">Usergroups</a>]<?}?> [<a href="<?=$BASEURL;?>/logout.php">logout</a>] [<a href="<?=$BASEURL;?>/forums/subscriptions.php">Forum Subscriptions</a>] Bonus: <a href="<?=$BASEURL;?>/mybonus.php"><?=number_format($CURUSER['seedbonus'], 1)?></a> <?=maxslots();?><br/>

    Ratio: <?=$ratio?>  <font color=green>Uploaded:</font> <font color=black><?=$uped?></font>  <font color=darkred>Downloaded:</font> <font color=black><?=$downed?></font>  Active Torrents: </span> <img alt="Torrents seeding" title="Torrents seeding" src="<?=$rootpath;?>pic/arrow_up.png"> <font color=black><span class="smallfont"><?=$activeseed?></span></font>  <img alt="Torrents leeching" title="Torrents leeching" src="<?=$rootpath;?>pic/arrow_down.png"> <font color=black><span class="smallfont"><?=$activeleech?></span></font>&nbsp;&nbsp;Connectable:&nbsp;<?=$connectable?></td>

    <td class="bottom" align="right"><span class="smallfont">The time is now: <span id=clock><?echo "$datum[hours]:$datum[minutes]";?></span><br/>

<?php

      if ($messages){
              print("<span class=smallfont><a href=$BASEURL/messages.php>$inboxpic</a> $messages ($unread New)</span>");

 if ($outmessages)
    print("<span class=smallfont>  <a href=$BASEURL/messages.php?action=viewmailbox&box=-1><imgstyle=border:none alt=sentbox title=sentbox src=".$rootpath."pic/mail_go.png></a> $outmessages</span>");

 else
    print("<span class=smallfont>  <a href=$BASEURL/messages.php?action=viewmailbox&box=-1><imgstyle=border:none alt=sentbox title=sentbox src=".$rootpath."pic/mail_go.png></a> 0</span>");
      }else{
             print("<span class=smallfont><a href=$BASEURL/messages.php><img style=border:none alt=inbox title=inbox src=".$rootpath."pic/mail.png></a> 0</span>");

 if ($outmessages)
    print("<span class=smallfont>  <a href=$BASEURL/messages.php?action=viewmailbox&box=-1><img style=border:none alt=sentbox title=sentbox src=".$rootpath."pic/mail_go.png></a> $outmessages</span>");

 else
    print("<span class=smallfont>  <a href=$BASEURL/messages.php?action=viewmailbox&box=-1><img style=border:none alt=sentbox title=sentbox src=".$rootpath."pic/mail_go.png></a> 0</span>");

      }
      print(" <a href=$BASEURL/friends.php><img style=border:none alt=Buddylist title=Buddylist src=".$rootpath."pic/group.png></a>");
      print(" <a href=$BASEURL/getrss.php><img style=border:none alt=Buddylist title='Get RSS' src=".$rootpath."pic/rss.png></a>");
?>

    </span>
<!-- clock hack -->
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
<!-- / clock hack -->
</span></td></table></table>
<?php } else {?>

<table align="center" cellpadding="4" cellspacing="0" border="0" style="width:960px"class="none">
<td class="tablea"><table align="center" style="width:100%" cellspacing="0" cellpadding="0" border="0">
<td class="error" align="center"><span>
You are not loged in! [ <a href="login.php">Login</a> | <a href="signup.php">Register</a> ]
</span></td></table></table>

<?php } ?>
<!-- /////////// here we go, with the menu //////////// -->

<?php

$w = "width=\"960px\"";
//if ($_SERVER["REMOTE_ADDR"] == $_SERVER["SERVER_ADDR"]) $w = "width=984";

?>
<table class="mainouter" align="center" <?=$w; ?> border="1" cellspacing="0" cellpadding="5">

<!------------- MENU ------------------------------------------------------------------------>

<?php $fn = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], "/") + 1); ?>



<td align="center" valign="top" class="outer" style="padding-top: 5px; padding-bottom: 5px" width=100%>
<?php
include $rootpath.'fts-contents/templates/Phogo/functions.php';
?>
