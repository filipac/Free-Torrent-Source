<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;

ADMIN::check();
stdhead("Mass Mesaje", false);
?>
<table class=main width=100% border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>
<div align=center>
<h1>Mass Messager to all Staff members and users:</a></h1>
<form method=post action=takestaffmess.php>
<?php

if ($_GET["returnto"] || $_SERVER["HTTP_REFERER"])
{
?>
<input type=hidden name=returnto value="<?=htmlentities($_GET["returnto"]) ? htmlentities($_GET["returnto"]) : htmlentities($_SERVER["HTTP_REFERER"])?>">
<?php
}
?>
<table cellspacing=0 cellpadding=5>
<?php
if ($_GET["sent"] == 1) {
?>
<tr><td colspan=2><font color=red><b>The message has ben sent.</font></b></tr></td>
<?php
}
?>
<tr>
<td><b>Send to:</b><br>
  <table style="border: 0" width="100%" cellpadding="0" cellspacing="0">
  
  <?php
  javascript('check');
  unset($q);
  $q = mysql_query("SELECT id,title FROM usergroups ORDER BY id ASC") or die(mysql_error());
  $count = 0;
  echo '<tr>';
  while($qq = mysql_fetch_array($q)):
  if($count == 4)
  echo "</tr><tr>"
  ?>
  <td style="border: 0" width="20"><input type="checkbox" name="clases[]" value="<?=$qq['id']?>">
             </td>
             <td style="border: 0"><?=$qq['title']?></td>
  <?php $count++;endwhile;
  ?>
    <td style="border: 0" width="20"><input type="checkbox" name="checkall" onclick="checkUncheckAll(this);"/></td><td style="border: 0">Check ALL</td>
  </tr>
    </table>
  </td>
</tr>
<tr><td>Subject <input type=text name=subject size=75></tr></td>
<tr><td><textarea name=msg cols=80 rows=15><?=$body?></textarea></td></tr>
<tr>
<td colspan=1><div align="center"><b>Sender:&nbsp;&nbsp;</b>
<?=$CURUSER['username']?>
<input name="sender" type="radio" value="self" checked>
&nbsp; System
<input name="sender" type="radio" value="system">
</div></td></tr>
<tr><td colspan=1 align=center><input type=submit value="Send!" class=btn></td></tr>
</table>
<input type=hidden name=receiver value=<?=$receiver?>>
</form>

 </div></td></tr></table>
<br>
NOTE: Do not user BB codes. (NO HTML)
<?php
stdfoot();
?>