<?php
require "include/bittorrent.php";

loggedinorreturn();
if (get_user_class() < UC_SYSOP)
stderr("Error", "Access denied.");
if ($HTTP_SERVER_VARS["REQUEST_METHOD"] == "POST")
{
if ($HTTP_POST_VARS["username"] == "" || $HTTP_POST_VARS["donated"] == "")
stderr("Error", "Missing form data.");
$username = sqlesc($HTTP_POST_VARS["username"]);
$donated = sqlesc($HTTP_POST_VARS["donated"]);

sql_query("UPDATE users SET donated=$donated WHERE username=$username") or sqlerr(__FILE__, __LINE__);
$res = sql_query("SELECT id FROM users WHERE username=$username");
$arr = mysql_fetch_row($res);
if (!$arr)
stderr("Error", "Unable to update account.");
header("Location: $BASEURL/userdetails.php?id=$arr[0]");
die;
}
stdhead("Update Users Donated Amounts");
?>
<h1>Update Users Donated Amounts</h1>
<form method=post action=donated.php>
<table border=1 cellspacing=0 cellpadding=5 width="100%">
<tr><td class=rowhead>User name</td><td><input type=text name=username size=40></td></tr>
<tr><td class=rowhead>Donated</td><td><input type=uploaded name=donated size=5></td></tr>
<tr><td colspan=2 align=center><input type=submit value="Okay" class=btn></td></tr>
</table>
</form>
<?php stdfoot(); ?>