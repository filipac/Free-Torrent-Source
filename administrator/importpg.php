<?php
// This script downloads/adds/updates peerguardian IP ranges in the bans table.
$rootpath = '../';
require $rootpath."include/bittorrent.php";
global $_pg_enable;
if($_pg_enable == 'no'):
redirect('administrator/options.php?type=pg','The PeerGuardian option is off.');
die;
endif;
global $_pg_server;

if (!ur::isadmin()) die;

$url = $_pg_server;

$f = @fopen($url, "r");
if (!$f)
  die("Cannot download: " . htmlspecialchars($url));

sql_query("DELETE FROM peerguardian") or sqlerr(__FILE__, __LINE__);

$uid = $CURUSER["id"];
$n = 0;
$o = 0;
$dt = sqlesc(get_date_time());
while (!feof($f))
{
	++$o;
	$s = rtrim(fgets($f));
	$i = strrpos($s, ":");
	if (!$i) continue;
	$comment = sqlesc("" . substr($s, 0, $i));
	$s = substr($s, $i + 1);
	$i = strpos($s, "-");
	$ipf = substr($s, 0, $i);
	$ipl = substr($s, $i + 1);
	$first = ip2long($ipf);
	$last = ip2long($ipl);
	if ($first == -1 || $last == -1) continue;
	$query = "INSERT INTO peerguardian (first, last, comment) VALUES($first, $last, $comment)";
	$res = sql_query($query) or sqlerr(__FILE__, __LINE__);
	if (mysql_affected_rows() != 1)
		die("Database insertion failed (" . htmlspecialchars($query) . ").");
	++$n;
}
$o -= $n;
redirect('administrator/options.php?type=pg',"Source: " . htmlspecialchars($url) . " $n ranges imported, $o line(s) was discarded.",'Success',5);
?>