<?php
require_once("include/bittorrent.php");
$id = 0 + $_GET["id"];
$md5 = $_GET["secret"];

if (!$id)
	httperr();



$res = sql_query("SELECT passhash, editsecret, status FROM users WHERE id = ".sqlesc($id));
$row = mysql_fetch_array($res);

if (!$row)
	httperr();

if ($row["status"] != "pending") {
	header("Refresh: 0; url=page.php?type=ok&typeok=confirmed");
	exit();
}

$sec = hash_pad($row["editsecret"]);
if ($md5 != md5($sec))
	httperr();

sql_query("UPDATE users SET status='confirmed', editsecret='' WHERE id=".sqlesc($id)." AND status='pending'");

if (!mysql_affected_rows())
	httperr();
	global $sechash;
if ($securelogin == "yes")
	logincookie($id, md5($row["passhash"].$sechash),1,0x7fffffff,true);
else
	logincookie($id, md5($row["passhash"].$sechash));
sessioncookie($id, md5($row["passhash"].$sechash));
header("Refresh: 0; url=page.php?type=ok&typeok=confirm");
?>