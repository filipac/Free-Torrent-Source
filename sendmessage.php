<?php
require "include/bittorrent.php";

loggedinorreturn();

parked();
$newpage = new page_verify();
$newpage->create('sendmessage');
global $usergroups;
if($usergroups['canpm'] == 'no') {
	ug();
}
// Standard Administrative PM Replies
$pm_std_reply[1] = "Read the bloody [url=".$BASEURL."/faq.php]FAQ[/url] and stop bothering me!";
$pm_std_reply[2] = "Die! Die! Die!";

// Standard Administrative PMs
$pm_template['1'] = array("Ratio warning","Hi,\n
You may have noticed, if you have visited the forum, that TB is disabling the accounts of all users with low share ratios.\n
I am sorry to say that your ratio is a little too low to be acceptable.\n
If you would like your account to remain open, you must ensure that your ratio increases dramatically in the next day or two, to get as close to 1.0 as possible.\n
I am sure that you will appreciate the importance of sharing your downloads.
You may PM any Moderator, if you believe that you are being treated unfairly.\n
Thank you for your cooperation.");
$pm_template['2'] = array("Avatar warning", "Hi,\n
You may not be aware that there are new guidelines on avatar sizes in the [url=".$BASEURL."/rules.php]rules[/url], in particular \"Resize
your images to a width of 150 px and a size of [b]no more than 150 KB[/b].\"\n
I'm sorry to say your avatar doesn't conform to them. Please change it as soon as possible.\n
We understand this may be an inconvenience to some users but feel it is in the community's best interest.\n
Thanks for the cooperation.");

// Standard Administrative MMs
$mm_template['1'] = $pm_template['1'];
$mm_template['2'] = array("Downtime warning","We'll be down for a few hours");
$mm_template['3'] = array("Change warning","The tracker has been updated. Read
the forums for details.");

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{						          ////////  MM  //
	if (get_user_class() < UC_MODERATOR)
		stderr("Error", "Permission denied");

  $n_pms = $_POST['n_pms'];
  $pmees = $_POST['pmees'];
  $auto = $_POST['auto'];

  if ($auto)
  	$body=$mm_template[$auto][1];

  stdhead("Send message", false);
	?>
  <table class=main width=100% border=0 cellspacing=0 cellpadding=0>
	<tr><td class=embedded><div align=center>
	<h1>Mass Message to <?=$n_pms?> user<?=($n_pms>1?"s":"")?>!</h1>
	<form method=post action=takemessage.php>
	<?php if ($_SERVER["HTTP_REFERER"]) { ?>
	<input type=hidden name=returnto value=<?=htmlentities($_SERVER["HTTP_REFERER"])?>>
	<?php } ?>
	<table border=1 cellspacing=0 cellpadding=5>
	<tr><td colspan="2"><div align="center">
	<textarea name=msg cols=80 rows=15><?=htmlspecialchars($body)?></textarea>
	</div></td></tr>
	<TR>
<TD colspan="2"><B>Subject:&nbsp;&nbsp;</B>
  <INPUT name="subject" type="text" size="70"></TD>
</TR>
	<tr><td colspan="2"><div align="center"><b>Comment:&nbsp;&nbsp;</b>
  <input name="comment" type="text" size="70">
	</div></td></tr>
  <tr><td><div align="center"><b>From:&nbsp;&nbsp;</b>
	<?=$CURUSER['username']?>
	<input name="sender" type="radio" value="self" checked>
	&nbsp; System
	<input name="sender" type="radio" value="system">
	</div></td>
  <td><div align="center"><b>Take snapshot:</b>&nbsp;<input name="snap" type="checkbox" value="1">
  </div></td></tr>
	<tr><td colspan="2" align=center><input type=submit value="Send it!" class=btn>
	</td></tr></table>
	<input type=hidden name=pmees value="<?=$pmees?>">
	<input type=hidden name=n_pms value=<?=$n_pms?>>
	</form><br><br>
	<form method=post action=<?=$_SERVER['PHP_SELF']?>>
	<table border=1 cellspacing=0 cellpadding=5>
	<tr><td>
	<b>Templates:</b>
	<select name="auto">
	<?php
	for ($i = 1; $i <= count($mm_template); $i++)	{
		echo "<option value=$i ".($auto == $i?"selected":"").
    		">".$mm_template[$i][0]."</option>\n";}
  ?>
	</select>
	<input type=submit value="Use" class=btn>
	</td></tr></table>
	<input type=hidden name=pmees value="<?=$pmees?>">
	<input type=hidden name=n_pms value=<?=$n_pms?>>
	</form></div></td></tr></table>
  <?php
} else {                                                        ////////  PM  //
	$receiver = $_GET["receiver"];
	int_check($receiver,true);

	$replyto = $_GET["replyto"];
	if ($replyto && !is_valid_id($replyto))
	  stderr("Error","Permission denied.");

	$auto = $_GET["auto"];
	$std = $_GET["std"];

	if (($auto || $std ) && get_user_class() < UC_MODERATOR)
	  stderr("Error","Permission denied.");

	$res = sql_query("SELECT * FROM users WHERE id=$receiver") or die(mysql_error());
	$user = mysql_fetch_assoc($res);
	if (!$user)
	  stderr("Error","No user with that ID.");

  if ($auto)
 		$body = $pm_std_reply[$auto];
  if ($std)
		$body = $pm_template[$std][1];

	if ($replyto)
	{
	  $res = sql_query("SELECT * FROM messages WHERE id=$replyto") or sqlerr();
	  $msga = mysql_fetch_assoc($res);
	  if ($msga["receiver"] != $CURUSER["id"])
	    stderr("Error","Permission denied.");
	  $res = sql_query("SELECT username FROM users WHERE id=" . $msga["sender"]) or sqlerr();
	  $usra = mysql_fetch_assoc($res);
	  $body .= "$msga[msg]\n-------- $usra[username] wrote: --------\n";
	  $subject = "Re: " . htmlspecialchars($msga['subject']);
	}
	stdhead("Send message", false);
?>
<!--     Preview pm (ajaX) v0.1    !-->
<!--     DO NOT EDIT BELOW!                            !-->
<script type="text/javascript" language="javascript">
   var http_request = false;
   function makePOSTRequest(url, parameters) {
      http_request = false;
      if (window.XMLHttpRequest) { // Mozilla, Safari,...
         http_request = new XMLHttpRequest();
         if (http_request.overrideMimeType) {
             // set type accordingly to anticipated content type
            //http_request.overrideMimeType('text/xml');
            http_request.overrideMimeType('text/html');
         }
      } else if (window.ActiveXObject) { // IE
         try {
            http_request = new ActiveXObject("Msxml2.XMLHTTP");
         } catch (e) {
            try {
               http_request = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {}
         }
      }
      if (!http_request) {
         alert('Cannot create XMLHTTP instance');
         return false;
      }
      
      http_request.onreadystatechange = alertContents;
      http_request.open('POST', url, true);
      http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      http_request.setRequestHeader("Content-length", parameters.length);
      http_request.setRequestHeader("Connection", "close");
      http_request.send(parameters);
   }

   function alertContents() {
      if (http_request.readyState == 4) {
         if (http_request.status == 200) {
            //alert(http_request.responseText);
            result = http_request.responseText;
            document.getElementById('previewpm').innerHTML = result;            
         } else {
            alert('There was a problem with the request. Please report this to administrator.');
         }
      }
   }
  
   function get(obj) {
      var poststr = "type=previewpm&msg=" + encodeURI( document.getElementById("msg").value );
      makePOSTRequest('page.php', poststr);
   }
</script>
<!-- Preview pm (ajaX) v0.1 !-->	

	<table class=main width=100% border=0 cellspacing=0 cellpadding=0>	
	<span name="previewpm" id="previewpm" align="left"></span>
	<tr><td class=embedded>	 
	<div align=center>
	<h1>Message to <a href=userdetails.php?id=<?=$receiver?>><?=$user["username"]?></a></h1>
	
	<form name=message method=post action=takemessage.php>
<?php if ($_GET["returnto"] || $_SERVER["HTTP_REFERER"]) { ?>
<input type=hidden name=returnto value="<?=htmlentities($_GET["returnto"]) ? htmlentities($_GET["returnto"]) : htmlentities($_SERVER["HTTP_REFERER"])?>">
<?php } ?>
<table class=message cellspacing=0 cellpadding=5>

<tr><td<?=$replyto?" colspan=2":""?>>
<?php
textbbcode("message","msg","$body",true);
?>
	
	</td></tr>
	<tr>
	<?php if ($replyto) { ?>
	<td align=center><input type=checkbox name='delete' value='yes' <?=$CURUSER['deletepms'] == 'yes'?"checked":""?>>Delete message you are replying to
	<input type=hidden name=origmsg value=<?=$replyto?>></td>
	<?php } ?>
	<td align=center><input type=checkbox name='save' value='yes' <?=$CURUSER['savepms'] == 'yes'?"checked":""?>>Save message to Sentbox</td></tr>
	<tr><td<?=$replyto?" colspan=2":""?> align=center><input type=submit value="Send it!" class=btn2>
	<input type=button class=btn2 name=button value=Preview  onclick="javascript:get(this.parentNode);">
	</td></tr>
	</table>
	<input type=hidden name=receiver value=<?=$receiver?>>
	</form>
<!--
  <?php
  if (get_user_class() >= UC_MODERATOR)
  {
  ?>
  	<br><br>
  	<form method=get action=<?=$_SERVER['PHP_SELF']?>>
	  <table border=1 cellspacing=0 cellpadding=5>
	  <tr><td>
	  <b>PM Templates:</b>
	  <select name="std"><?php
	  for ($i = 1; $i <= count($pm_template); $i++)
	  {
	    echo "<option value=$i ".($std == $i?"selected":"").
	      ">".$pm_template[$i][0]."</option>\n";
	  }?>
	  </select>
		<?php if ($_SERVER["HTTP_REFERER"]) { ?>
		<input type=hidden name=returnto value=<?=$_GET["returnto"]?$_GET["returnto"]:$_SERVER["HTTP_REFERER"]?>>
    <?php } ?>
  	<input type=hidden name=receiver value=<?=$receiver?>>
		<input type=hidden name=replyto value=<?=$replyto?>>
	  <input type=submit value="Use" class=btn>
	  </td></tr></table></form>
	<?php
  }
	?>
-->
 	</div></td></tr></table>
	<?php
}
stdfoot();
?>