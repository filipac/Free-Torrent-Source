<?php
ob_start();
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;

ADMIN::check();
mysql_connect($mysql_host,$mysql_user,$mysql_pass);
mysql_select_db($mysql_db);
stdhead("Groups");
print("<h1>Groups</h1>\n");
print("</br>");
print("<table width=70% border=1 cellspacing=0 cellpadding=2><tr><td align=center>\n");

///////////////////// D E L E T E C A T E G O R Y \\\\\\\\\\\\\\\\\\\\\\\\\\\\

$sure = $_GET['sure'];
if($sure == "yes") {
$delid = $_GET['delid'];
$query = "DELETE FROM countries WHERE id=" .sqlesc($delid) . " LIMIT 1";
$sql = sql_query($query);
echo("Group successfuly removed![ <a href='country.php'>back</a> ]");
end_frame();
stdfoot();
die();
}
$delid = $_GET['delid'];
$name = $_GET['country'];
if($delid > 0) {
echo("Do you really wish to remove this group? ($name) ( <strong><a href='". $_SERVER['PHP_SELF'] . "?delid=$delid&country=$name&sure=yes'>Yeah!</a></strong> / <strong><a href='". $_SERVER['PHP_SELF'] . "'>Nah</a></strong> )");
end_frame();
stdfoot();
die();

}

///////////////////// E D I T A C A T E G O R Y \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$edited = $_GET['edited'];
if($edited == 1) {
$id = $_GET['id'];
$country_name = $_GET['country_name'];
$country_flag = $_GET['country_flagpic'];
$query = "UPDATE countries SET
name = '$country_name',
flagpic = '$country_flagpic' WHERE id=".sqlesc($id);
$sql = sql_query($query);
if($sql) {
echo("<table class=main cellspacing=0 cellpadding=5 width=50%>");
echo("<tr><td><div align='center'><strong>Successfuly</strong>edited[ <a href='country.php'>back</a> ]</div></tr>");
echo("</table>");
end_frame();
stdfoot();
die();
}
}

$editid = $_GET['editid'];
$name = $_GET['name'];
$flagpic = $_GET['flagpic'];
if($editid > 0) {
echo("<form name='form1' method='get' action='" . $_SERVER['PHP_SELF'] . "'>");
echo("<table class=main cellspacing=0 cellpadding=5 width=50%>");
echo("<div align='center'><input type='hidden' name='edited' value='1'>Now you are editing group <strong>\"$name\"</strong></div>");
echo("<br>");
echo("<input type='hidden' name='id' value='$editid'<table class=main cellspacing=0 cellpadding=5 width=50%>");
echo("<tr><td>Group name: </td><td align='right'><input type='text' size=50 name='country_name' value='$name'></td></tr>");
echo("<tr><td>Group picture: </td><td align='right'><input type='text' size=50 name='country_flagpic' value='$flagpic'></td></tr>");
echo("<tr><td></td><td><div align='right'><input type='Submit'></div></td></tr>");
echo("</table></form>");
end_frame();
stdfoot();
die();
}

///////////////////// A D D A N E W C A T E G O R Y \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$add = $_GET['add'];
if($add == 'true') {
$country_name = $_GET['country_name'];
$country_flagpic = $_GET['country_flagpic'];
$query = "INSERT INTO countries SET
name = '$country_name',
flagpic = '$country_flagpic'";
$sql = sql_query($query);
if($sql) {
$success = TRUE;
} else {
$success = FALSE;
}
}
print("<strong>Add new group:</strong>");
print("<br />");
print("<br />");
echo("<form name='form1' method='get' action='" . $_SERVER['PHP_SELF'] . "'>");
echo("<table class=main cellspacing=0 cellpadding=5 width=50%>");
echo("<tr><td>Name: </td><td align='right'><input type='text' size=50 name='country_name'></td></tr>");
echo("<tr><td>Picture: </td><td align='right'><input type='text' size=50 name='country_flagpic'><input type='hidden' name='add' value='true'></td></tr>");
echo("<tr><td></td><td><div align='right'><input type='Submit'></div></td></tr>");
echo("</table>");
if($success == TRUE) {
print("<strong>Success!</strong>");
}
echo("<br>");
echo("</form>");

///////////////////// E X I S T I N G C A T E G O R I E S \\\\\\\\\\\\\\\\\\\\\\\\\\\\

print("<strong>Existing groups:</strong>");
print("<br />");
print("<br />");
echo("<table class=main cellspacing=0 cellpadding=5>");
echo("<td><b>ID</b></td><td><b>Name</b></td><td><b>Picture</b></td><td><b>Edit</b></td><td><b>Delete</b></td>");
$query = "SELECT * FROM countries WHERE 1=1";
$sql = sql_query($query);
while ($row = mysql_fetch_array($sql)) {
$id = $row['id'];
$name = $row['name'];
$flagpic = $row['flagpic'];
echo("<tr><td><strong>$id</strong> </td> <td><strong>$name</strong></td> <td><img src='$BASEURL/pic/flag/$flagpic' border='0' /></td> <td><a href='" . $PHP_SELF['$_SERVER'] . "country.php?editid=$id&name=$name&flagpic=$flagpic'><div align='center'><img src='$BASEURL/pic/multipage.gif' border='0' class=special /></a></div></td> <td><div align='center'><a href='" . $PHP_SELF['$_SERVER'] . "country.php?delid=$id&country=$name'><img src='$BASEURL/pic/warned2.gif' border='0' class=special align='center' /></a></div></td></tr>");
}

end_frame();
end_frame();
stdfoot();

?>