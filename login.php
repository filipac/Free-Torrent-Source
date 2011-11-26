<?php
require_once("include/bittorrent.php");

FLogin::failedloginscheck ();
HANDLE::cur_user_check () ;
stdhead("Login");
unset($returnto);
if (!empty($_GET["returnto"])) {
	$returnto = $_GET["returnto"];
	if (!$_GET["nowarn"]) {
		print("<h1>Not logged in!</h1>\n");
		print("<p><b>Error:</b> The page you tried to view can only be used when you're logged in.</p>\n");
	}
}
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
}</style><?php }?>

<form method="post" action="takelogin.php">
<?php $torep = array("[","]");?>
<p><?=checkbrowser ();?></p>
<?php collapses('login',"<table border=1 cellspacing=0 cellpadding=0 bgcolor=#333333 width=100%><tr><td style='padding:0px; background: #e0e0e0' class=text>
<font color=white>{icon}<center><b>Login to $SITENAME <font class=small>(".str_replace($torep,'',FLogin::remaining ())." Failed logins left)</font></b>
</font></center></td></tr></table>",'100',1);?>
<table border="0" cellpadding=0 width=100%>
<tr><td class="rowhead">Username:</td><td><input type="text" size="26" name="username" class="username" /></td></tr>
<tr><td class="rowhead">Password:</td><td><input type="password" size="26" name="password" class="password"/ <?if($vkeysys=='yes') echo'class="keyboardInput"'?>></td></tr>
<?php

show_image_code ();
if ($securelogin == "no")
	$sec = "DISABLED /";
elseif ($securelogin == "op")
	$sec = "/";
if ($securelogin == "yes")
echo"<input type=\"hidden\" name=\"securelogin\" value=\"yes\">";
?>
<tr><td class="rowhead"><input type="checkbox" name="logout" value="yes">Log me out after 15 minutes inactivity <?php if($securelogin != 'yes') {?><input type="checkbox" name="securelogin" value="yes" <?=$sec?>><?}?><b>Secure Login <?php if ($securelogin == "yes") echo 'Enabled';?></b></td>
<td align=left><input type="submit" value="Log in!" class="but"> <input type="reset" value="Reset" class="but"></td></tr>
</table>
<?php

if (isset($returnto))
	print("<input type=\"hidden\" name=\"returnto\" value=\"" . htmlspecialchars($returnto) . "\" />\n");
collapsee();
?>
</form>
<p><b>Note</b>: You need cookies enabled to log in.</p>
</p>Forget your password? Recover your password <a href="recover.php"><b>via email</b></a> or <a href="recoverhint.php"><b>via question</b></a></p>
<?php

stdfoot();

?>