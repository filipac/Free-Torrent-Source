<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;

ADMIN::check();

if ($HTTP_SERVER_VARS["REQUEST_METHOD"] == "POST")
{
 $username = trim($_POST["username"]);
   $res = sql_query("SELECT * FROM users WHERE username=" . sqlesc($username) . " ") or sqlerr();
$arr = mysql_fetch_assoc($res);


$id = $arr['id'];
$wantpassword=$username;
$secret = mksecret();
$wantpasshash = md5($secret . $wantpassword . $secret);
sql_query("UPDATE users SET passhash=".sqlesc($wantpasshash).", secret= ".sqlesc($secret)." where id=$id");
write_log("Password Reset For $username by $CURUSER[username]");
 if (mysql_affected_rows() != 1)
   stderr("Error", "Unable to RESET PASSWORD on this account.");
 stderr("Success", "The account <b>$username</b> Password Reset To <b>$wantpassword</b> please inform User of this change.",0);

}
stdhead("Reset User's Lost Password");
?>
<h1>Reset User's Lost Password</h1>
<table border=1 cellspacing=0 cellpadding=5>
<form method=post>
<tr><td class=rowhead>User name</td><td><input size=40 name=username></td></tr>

<tr><td colspan=2><input type=submit class=btn value='resetthisnow'></td></tr>
</form>
</table>
<?php
stdfoot();
?>