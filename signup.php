<?php
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
require_once("include/bittorrent.php");

registration_check("normal");
FLogin::failedloginscheck ("Signup");
HANDLE::cur_user_check () ;
stdhead("Signup");
if(_ref_sys_ == 'yes')
$referrer = (isset ($_POST['ref']) ? htmlspecialchars_uni ($_POST['ref']) : (isset ($_GET['ref']) ? htmlspecialchars_uni ($_GET['ref']) : ''));
global $vkeysys;
?>
<script type="text/javascript" src="clientside/usercheck/usercheck.js"></script>
	<script type="text/javascript" src="clientside/signup/rounded-corners.js"></script>
	<script type="text/javascript" src="clientside/signup/form-field-tooltip.js"></script>
	<style>#DHTMLgoodies_formTooltipDiv{
	color:#FFF;
	font-family:arial;
	font-weight:bold;
	font-size:1.0em;
	line-height:120%;
}
.DHTMLgoodies_formTooltip_closeMessage{
	color:#FFF;
	font-weight:normal;
	font-size:0.8em;
}</style>
	<?php
if($vkeysys == 'yes') {
javascript('keyboard');
?>
	<style>
#keyboardInputMaster {
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
<style type="text/css">
.usercheck-available {
font-family: Verdana, Arial, Helvetica, sans-serif;
font-size: 10px;
font-weight: bold;
color: #339900;
}
.usercheck-taken {
font-family: Verdana, Arial, Helvetica, sans-serif;
font-size: 10px;
font-weight: bold;
color:red;
}
</style>
<script>
var site = "<?=$BASEURL;?>"
</script>
<!--
<table width=500 border=1 cellspacing=0 cellpadding=10><tr><td align=left>
<h2 align=center>Proxy check</h2>
<b><font color=red>Important - please read:</font></b> We do not accept users connecting through public proxies. When you
submit the form below we will check whether any commonly used proxy ports on your computer is open. If you have a firewall it may alert of you of port
scanning activity originating from <b>69.10.142.42</b> (torrentbits.org). This is only our proxy-detector in action.
<b>The check takes up to 30 seconds to complete, please be patient.</b> The IP address we will test is <b><?= $_SERVER["REMOTE_ADDR"]; ?></b>.
By proceeding with submitting the form below you grant us permission to scan certain ports on this computer.
</td></tr></table>
<p>
-->
<p>
<form method="post" action="takesignup.php">
<table border="1" cellspacing=0 cellpadding="10">
<?php
print("<tr><td align=center colspan=2><b>Note</b>: You need cookies enabled to sign up or log in.</td></tr>");
?>
<tr><td align="right" class="heading">Desired username:</td><td align=left><input type="text" size="40" name="wantusername" id="wantusername" onkeyup="registrercheck();" tooltiptext="Chose a username that you will remember. The username you write will be checked and will be told if you can use that name." /> <div id="userdiv"></div></td></tr>
<tr><td class=rowhead>Pick a password:</td><td align=left><input type="password" size="40" name="wantpassword" tooltiptext="Chose an password easy to remember but not too easy like '123456'. Try to not forget this." <?if($vkeysys=='yes') echo'class="keyboardInput"'?>/></td></tr>
<tr><td class=rowhead>Enter password again:</td><td align=left><input type="password" size="40" name="passagain" tooltiptext="The password you entered above." <?if($vkeysys=='yes') echo'class="keyboardInput"'?>/></td></tr>
<?php
$question = array(
'1' => 'What is your name of first school?',
'2' => 'What is your pet\'s name?',
'3' => 'What is your mothers maiden name?'
);

print("<tr><td class=rowhead>Question:</td><td><select name=passhint>");
foreach ($question as $v => $q){
print("<option value=\"$v\">$q</option>");
}
print("</select></td></tr>");
?>
<tr><td class=rowhead>Enter hint answer:</td><td align=left><input type="text" size="40" name="hintanswer" tooltiptext="This answer will be used to reset your password in case you forget it.<br> Make sure its something you will not forget!"/><br>
</td></tr>
<?php
show_image_code ();
?>
<tr><td class=rowhead>Email address:</td><td align=left><input type="text" size="40" name="email" tooltiptext="Enter a valid email because you need to confirm it.<BR>The email address must be valid."/>
<table width=250 border=0 cellspacing=0 cellpadding=0><tr><td class=embedded></td></tr>
</td></tr></table>
</td></tr>
<?php
include "clientside/calendar/calendar.php";
$bday=new DHTML_Calendar('birthday');
print $bday->load_files();
$countries = "<option value=72>---- None selected ----</option>n";
$ct_r = sql_query("SELECT id,name FROM countries ORDER BY name") or die;
while ($ct_a = mysql_fetch_array($ct_r))
$countries .= "<option value=$ct_a[id]" . ($CURUSER["country"] == $ct_a['id'] ? " selected" : "") . ">$ct_a[name]</option>n";
tr("Country", "<select name=country>n$countries</select>", 1);
tr('Birthday',$bday->make_input_field(),1);
 ?>
<tr><td class=rowhead>Gender</td><td align=left>
<input type=radio name=gender value=Male>Male <input type=radio name=gender value=Female>Female </td></tr>
<?php
if(_ref_sys_ == 'yes')
echo ("<tr><td class=\"rowhead\">Referrer</td><td align=\"left\"><input type=\"text\" size=\"40\" name=\"referrer\" value=\"$referrer\" />");
?>
<tr><td class=rowhead>Varification</td><td align=left><input type=checkbox name=rulesverify value=yes> I have read the site <a href=rules.php><u>rules</u></a> page.<br>
<input type=checkbox name=faqverify value=yes> I agree to read the <a href=faq.php><u>FAQ</u></a> before asking questions. <br>
<input type=checkbox name=ageverify value=yes> I am at least 13 years old.</td></tr>
<input type=hidden name=hash value=<?=$code?>>
<tr><td colspan="2" align="center"><font color=red><b>All Fields are required!</b><p></font><input type=submit value="Sign up! (PRESS ONLY ONCE)" style='height: 25px'></td></tr>
</table>
</form>
<script type="text/javascript">
var tooltipObj = new DHTMLgoodies_formTooltip();
tooltipObj.setTooltipPosition('right');
tooltipObj.setPageBgColor('#EEEEEE');
tooltipObj.setTooltipCornerSize(15);
tooltipObj.initFormFieldTooltip();
</script>
<?php
stdfoot(); 
?>