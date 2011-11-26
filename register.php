<?php
require_once("include/bittorrent.php");

registration_check();
FLogin::failedloginscheck ("Invite signup");
HANDLE::cur_user_check () ;
$code = $HTTP_GET_VARS["invitenumber"];

$nuIP = IP::getip(); 
  $dom = @gethostbyaddr($nuIP); 
  if ($dom == $nuIP || @gethostbyname($dom) != $nuIP) 
    $dom = ""; 
  else 
  { 
    $dom = strtoupper($dom); 
    preg_match('/^(.+)\.([A-Z]{2,3})$/', $dom, $tldm); 
    $dom = $tldm[2]; 
  }

$sq = sprintf("SELECT inviter FROM invites WHERE hash ='%s'",
		mysql_real_escape_string($code));
$res = sql_query($sq) or sqlerr(__FILE__, __LINE__);
$inv = mysql_fetch_assoc($res);  
$inviter = htmlspecialchars($inv["inviter"]);
stdhead("Signup");

print("<p><table border=1 cellspacing=0 cellpadding=10 width=100%>");
if (!$inv){
print("<tr class=main><td align=left><b>Hey! You aren't invited. Bugger off....</b></td></tr>");
} else {
?>
<p>
<form method="post" action="takereg.php">
<input type="hidden" name="inviter" value="<?=$inviter;?>">
<table border="1" cellspacing=0 cellpadding="10">
<?php
print("<tr><td align=center colspan=2><b>Note</b>: You need cookies enabled to sign up or log in.</td></tr>");
?>
<tr><td class=rowhead>Desired username:</td><td align=left><input type="text" size="40" name="wantusername" /><br>
<font class=small>Allowed Characters: (a-z), (A-Z), (0-9)</font></td></tr>
<tr><td class=rowhead>Pick a password:</td><td align=left><input type="password" size="40" name="wantpassword" /></td></tr>
<tr><td class=rowhead>Enter password again:</td><td align=left><input type="password" size="40" name="passagain" /></td></tr>
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
<tr><td class=rowhead>Enter hint answer:</td><td align=left><input type="text" size="40" name="hintanswer" /><br>
<font class=small>This answer will be used to reset your password in case you forget it.<br> Make sure its something you will not forget!
</font></td></tr>
<?php
show_image_code ();
?>
<tr><td class=rowhead>Email address:</td><td align=left><input type="text" size="40" name="email" />
<table width=250 border=0 cellspacing=0 cellpadding=0><tr><td class=embedded><font class=small>The email address must be valid.</td></tr>
</font></td></tr></table>
</td></tr>
<? $countries = "<option value=72>---- None selected ----</option>n";
$ct_r = sql_query("SELECT id,name FROM countries ORDER BY name") or die;
while ($ct_a = mysql_fetch_array($ct_r))
$countries .= "<option value=$ct_a[id]" . ($CURUSER["country"] == $ct_a['id'] ? " selected" : "") . ">$ct_a[name]</option>n";
tr("Country", "<select name=country>n$countries</select>", 1); ?>
<tr><td class=rowhead>Gender</td><td align=left>
<input type=radio name=gender value=Male>Male <input type=radio name=gender value=Female>Female </td></tr>
<tr><td class=rowhead>Varification</td><td align=left><input type=checkbox name=rulesverify value=yes> I have read the site <a href=rules.php><u>rules</u></a> page.<br>
<input type=checkbox name=faqverify value=yes> I agree to read the <a href=faq.php><u>FAQ</u></a> before asking questions. <br>
<input type=checkbox name=ageverify value=yes> I am at least 13 years old.</td></tr>
<input type=hidden name=hash value=<?=$code?>>
<tr><td colspan="2" align="center"><font color=red><b>All Fields are required!</b><p></font><input type=submit value="Sign up! (PRESS ONLY ONCE)" style='height: 25px'></td></tr>
</table>
</form>
<?php
stdfoot();
}
?>