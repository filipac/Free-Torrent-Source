<?php
$rootpath = '../';
require $rootpath."include/bittorrent.php";
ADMIN::check();
stdhead("Latest 100 Users");

///////////for TBDEV.NET , posted by TripleH a.k.a Sharky
begin_frame("Latest 100 Users");

echo '<table width="640" border="0" align="center" cellpadding="2" cellspacing="0">';
echo "<tr><td class=colhead align=left>User</td><td class=colhead>Ratio</td><td class=colhead>IP</td><td class=colhead>Date Joined</td><td class=colhead>Last Access</td><td class=colhead>Download</td><td class=colhead>Upload</td></tr>";

$result = mysql_query ("SELECT * FROM users WHERE enabled = 'yes' AND status = 'confirmed' ORDER BY added DESC limit 100");
if ($row = mysql_fetch_array($result)) {
do {
if ($row["uploaded"] == "0") { $ratio = "inf"; }
elseif ($row["downloaded"] == "0") { $ratio = "inf"; }
else {
$ratio = number_format($row["uploaded"] / $row["downloaded"], 3);

$ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
}
echo "<tr><td><a href=userdetails.php?id=".$row["id"]."><b>".$row["username"]."</b></a></td><td><strong>".$ratio."</strong></td><td>".$row["ip"]."</td><td>".$row["added"]."</td><td>".$row["last_access"]."</td><td>".mksize($row["downloaded"])."</td><td>".mksize($row["uploaded"])."</td></tr>";


} while($row = mysql_fetch_array($result));
} else {print "<tr><td>Sorry, no records were found!</td></tr>";}
echo "</table>";
end_frame();
stdfoot();

?>