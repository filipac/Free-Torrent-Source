<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;

lang::load("adduser");
ADMIN::check();
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	if ($_POST["username"] == "" || $_POST["password"] == "" || $_POST["email"] == "")
		stderr(str1, str3);
	if ($_POST["password"] != $_POST["password2"])
		stderr(str1, str4);
	$email = htmlspecialchars(trim($_POST["email"]));
	$email = safe_email($email);
	if (!check_email($email))
		stderr(str1,str5);
	$username = sqlesc($_POST["username"]);
	$password = $_POST["password"];
	$email = sqlesc($_POST["email"]);
	$secret = mksecret();
	$passhash = sqlesc(md5($secret . $password . $secret));
  	$secret = sqlesc($secret);

	sql_query("INSERT INTO users (added, last_access, secret, username, passhash, status, email) VALUES(NOW(), NOW(), $secret, $username, $passhash, 'confirmed', $email)") or sqlerr(__FILE__, __LINE__);
	$res = sql_query("SELECT id FROM users WHERE username=$username");
	$arr = mysql_fetch_row($res);
	if (!$arr)
		stderr(str1, str6);
	header("Location: $BASEURL/userdetails.php?id=".htmlspecialchars($arr[0]));
	die;
}
stdhead(str7);

?>
<h1><?=str7?></h1>
<form method=post action=adduser.php>
<table border=1 cellspacing=0 cellpadding=5>
<tr><td class=rowhead><?=str8?></td><td><input type=text name=username size=40></td></tr>
<tr><td class=rowhead><?=str9?></td><td><input type=password name=password size=40></td></tr>
<tr><td class=rowhead><?=str10?></td><td><input type=password name=password2 size=40></td></tr>
<tr><td class=rowhead><?=str11?></td><td><input type=text name=email size=40></td></tr>
<tr><td colspan=2 align=center><input type=submit value="<?=str12?>" class=btn></td></tr>
</table>
</form>
<?php stdfoot(); ?>