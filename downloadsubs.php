<?php
require "include/bittorrent.php";
dbconn();
if (!$CURUSER)
{
Header("Location: $BASEURL/");
die;
}
$filename = substr($HTTP_SERVER_VARS["PATH_INFO"], strrpos($HTTP_SERVER_VARS["PATH_INFO"], "/") + 1);
if (!$filename)
die("File name missing\n");
if (get_user_class() < UC_POWER_USER && filesize("$SUBSPATH/$filename") > 1024*1024)
die("Sorry, you need to be a power user or higher to download files larger than 1.00 MB.\n");
$filename = sqlesc($filename);
$res = mysql_query("SELECT * FROM subs WHERE filename=$filename") or sqlerr();
$arr = mysql_fetch_assoc($res);
if (!$arr)
die("Not found\n");
mysql_query("UPDATE subs SET hits=hits+1 WHERE id=$arr[id]") or sqlerr(__FILE, __LINE__);
$file = "$SUBSPATH/$arr[filename]";
if (!is_file($file))
die("File not found\n");
$f = fopen($file, "rb");
if (!$f)
die("Cannot open file\n");
header("Content-Length: " . filesize($file));
header("Content-Type: application/octet-stream");
do
{
$s = fread($f, 4096);
print($s);
} while (!feof($f));
//closefile($f);
exit;
?>