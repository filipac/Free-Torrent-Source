<?php
echo '<link rel="stylesheet" type="text/css" href="controlpanel.css" />
<body>';
 define('MAGPIE_DIR', '../include/magpierss/');
 define('MAGPIE_CACHE_ON',false);
require_once(MAGPIE_DIR.'rss_fetch.inc');

$url = "http://freetosu.berlios.de/blog/feed";

if ( $url ) {
	$rss = fetch_rss( $url );
	echo "Channel: " . $rss->channel['title'] . "<p>";
	echo "<ul>";
	foreach ($rss->items as $item) {
		$href = $item['link'];
		$title = $item['title'];
		$desc = $item['description'];	
		echo "<li><a href=$href>$title</a><BR>$desc</li>";
	}
	echo "</ul>";
}
?>