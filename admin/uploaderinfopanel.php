<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;
ADMIN::check();
stdhead("Uploaders");

$query = "SELECT id, username, added, uploaded, downloaded, donor, warned FROM users WHERE class = 3";
$result = sql_query($query);
$num = mysql_num_rows($result); // how many uploaders
echo "<h2>Uploaders Info Panel</h2>";
echo "<p>We have " . $num . " uploaders</p>";

$zerofix = $num - 1; // remove one row because mysql starts at zero

if ($num > 0)
{
echo "<table cellpadding=4 align=center border=1>";
echo "<tr>";
echo "<td class=colhead>Num</td>";
echo "<td class=colhead>Username</td>";
echo "<td class=colhead>Upped / Downed</td>";
echo "<td class=colhead>Ratio</td>";
echo "<td class=colhead>Num torrents</td>";
echo "<td class=colhead>Last upload</td>";
echo "<td class=colhead>Send PM</td>";
echo "</tr>";

for ($i = 0; $i <= $zerofix; $i++)
{
$id = mysql_result($result, $i, "id");
$username = mysql_result($result, $i, "username");
$added = mysql_result($result, $i, "added");
$uploaded = mksize(mysql_result($result, $i, "uploaded"));
$downloaded = mksize(mysql_result($result, $i, "downloaded"));
$uploadedratio = mysql_result($result, $i, "uploaded");
$downloadedratio = mysql_result($result, $i, "downloaded");
$donor = mysql_result($result, $i, "donor");
$warned = mysql_result($result, $i, "warned");

// get uploader torrents activity
$upperquery = "SELECT added FROM torrents WHERE owner = $id";
$upperresult = sql_query($upperquery);

$torrentinfo = mysql_fetch_array($upperresult);

$numtorrents = mysql_num_rows($upperresult);

if ($downloaded > 0)
{
$ratio = $uploadedratio / $downloadedratio;
$ratio = number_format($ratio, 3);
$color = get_ratio_color($ratio);
if ($color)
$ratio = "<font color=$color>$ratio</font>";
}
else
if ($uploaded > 0)
$ratio = "Inf.";
else
$ratio = "---";

// get donor
if ($donor == "yes")
$star = "<img src=$BASEURL/pic/star.png>";
else
$star = "";

// get warned
if ($warned == "yes")
$klicaj = "<img src=$BASEURL/pic/warned8.gif>";
else
$klicaj = "";

$counter = $i + 1;

echo "<tr>";
echo "<td align=center>$counter.</td>";
echo "<td><a href=$BASEURL/userdetails.php?id=$id>$username</a> $star $klicaj</td>";
echo "<td>$uploaded / $downloaded</td>";
echo "<td>$ratio</td>";
echo "<td>$numtorrents torrents</td>";
if ($numtorrents > 0)
{
$lastadded = mysql_result($upperresult, $numtorrents - 1, "added");
echo "<td>" . get_elapsed_time(sql_timestamp_to_unix_timestamp($lastadded)) . " ago (" . gmdate("d. M Y",strtotime($lastadded)) . ")</td>";
}
else
echo "<td>---</td>";
echo "<td align=center><a href=$BASEURL/sendmessage.php?receiver=$id><img src=$BASEURL/pic/button_pm.gif></a></td>";

echo "</tr>";


}
echo "</table>";
}



stdfoot();

?>