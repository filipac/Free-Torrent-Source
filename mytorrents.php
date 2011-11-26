<?php

require_once("include/bittorrent.php");



loggedinorreturn();

parked();
stdhead($CURUSER["username"] . "'s torrents");

$where = "WHERE owner = " . sqlesc($CURUSER["id"]) . " AND banned != 'yes'";
$res = sql_query("SELECT COUNT(*) FROM torrents $where");
$row = mysql_fetch_array($res);
$count = $row[0];

if (!$count) {
?>
<h1>No torrents</h1>
<p>You haven't uploaded any torrents yet, so there's nothing in this page.</p>
<?php
}
else {
	list($pagertop, $pagerbottom, $limit) = pager(20, $count, "mytorrents.php?");

	$res = sql_query("SELECT torrents.type, torrents.comments, torrents.leechers, torrents.seeders, IF(torrents.numratings < $minvotes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.id, categories.name AS cat_name, categories.image AS cat_pic, torrents.name, save_as, numfiles, added, size, views, visible, hits, times_completed, category FROM torrents LEFT JOIN categories ON torrents.category = categories.id $where ORDER BY id DESC $limit");

	print($pagertop);

	_torrents($res, "mytorrents");

	print($pagerbottom);
}

stdfoot();

?>