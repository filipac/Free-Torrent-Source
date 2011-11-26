<?php
//---------------------------------------------------------
//---- Optimize & Repair Database from Tracker
//---------------------------------------------------------
$rootpath = '../';
require $rootpath."include/bittorrent.php";


ADMIN::check();
$tables = "";
stdhead("Optimize and repair tables");
echo'<pre>';
$tablesshow = mysql_query("SHOW TABLES FROM `".$mysql_db."`");
while (list($table) = mysql_fetch_row($tablesshow)){
mysql_query("OPTIMIZE TABLE ".mysql_real_escape_string($table));
print("Optimizing table ".mysql_real_escape_string($table).'...OK<BR>');
mysql_query("REPAIR TABLE ".mysql_real_escape_string($table));
print("Repairing table ".mysql_real_escape_string($table).'...OK<BR>');
}
echo'</pre>';

echo'Database OPTIMIZED & Repaired!';
//---------------------------------------------
//---- END
//---------------------------------------------
?>