<?php
$rootpath = '../';
require $rootpath."include/bittorrent.php";

ADMIN::check(6);

if (isset($_POST["forumid"]))
mysql_query("UPDATE forums SET name =" . sqlesc($_POST["name"]) . ", sort =" . sqlesc($_POST["sort"]) . ", description =" . sqlesc($_POST["description"]) . ", minclassread =" . sqlesc($_POST["minclassread"]) . ", minclasswrite =" . sqlesc($_POST["minclasswrite"]) . ", minclasscreate =" . sqlesc($_POST["minclasscreate"]) . " WHERE id = $_POST[forumid]") or sqlerr();

if (isset($_POST["categoryid"]))
mysql_query("UPDATE categories SET name =" . sqlesc($_POST["name"]) . ", image =" . sqlesc($_POST["image"]) . " WHERE id = $_POST[categoryid]") or sqlerr();

if (isset($_POST["newforum"]))
mysql_query("INSERT INTO forums (name, sort, description, minclassread, minclasswrite, minclasscreate) VALUES (" . sqlesc($_POST["name"]) . ", " . sqlesc($_POST["sort"]) . ", " . sqlesc($_POST["description"]) . ", " . sqlesc($_POST["minclassread"]) . ", ". sqlesc($_POST["minclasswrite"]) . ", " . sqlesc($_POST["minclasscreate"]) . ")") or sqlerr();

if (isset($_POST["newcategory"]))
mysql_query("INSERT INTO categories (name, image) VALUES (" . sqlesc($_POST["name"]) . ", " . sqlesc($_POST["image"]) . ")") or sqlerr();


$resforums = mysql_query("SELECT * FROM forums ORDER BY sort") or sqlerr();
$rescategories = mysql_query("SELECT * FROM categories ORDER BY name") or sqlerr();

stdhead("Admin Stuff");
begin_main_frame('100%');

print("<a name='forums'>");
print("<h2>Forum Admin</h2>");

print("<table border=1 cellspacing=0 cellpadding=5>\n<tr><td class=colhead>Name</td><td class=colhead>Sort</td><td class=colhead>Description</td><td class=colhead>Read</td><td class=colhead>Write</td><td class=colhead>Create</td><td class=colhead>Change</td>\n");

while ($arr = mysql_fetch_assoc($resforums))
print("<form name=$arr[id] method=post action=adminstuff.php><tr><input type=hidden name=forumid value=\"$arr[id]\"><td><input type=text name=name value=\"$arr[name]\"></td><td><input type=text name=sort size=1 value=\"$arr[sort]\"></td><td><input type=text size=100 name=description value=\"$arr[description]\"></td><td><input type=text name=minclassread size=1 value=\"$arr[minclassread]\"></td><td><input type=text name=minclasswrite size=1 value=\"$arr[minclasswrite]\"></td><td><input type=text size=1 name=minclasscreate value=\"$arr[minclasscreate]\"></td><td><input type=submit value=Change></form></td></tr>\n");

print("</table><p></p><table border=1 cellspacing=0 cellpadding=5>\n<tr><td class=colhead>Name</td><td class=colhead>Sort</td><td class=colhead>Description</td><td class=colhead>Read</td><td class=colhead>Write</td><td class=colhead>Create</td><td class=colhead>Create</td>\n");
print("<tr><form name=newforum method=post action=adminstuff.php><input type=hidden name=newforum value=true><td><input type=text name=name></td><td><input type=text name=sort size=1></td><td><input type=text size=100 name=description></td><td><input type=text name=minclassread size=1></td><td><input type=text name=minclasswrite size=1></td><td><input type=text size=1 name=minclasscreate></td><td><input type=submit value=Create></form></td></tr>");
print("</table><p></p>");

print("<a name='categories'>");
print("<h2>Category Admin</h2>");

print("<table border=1 cellspacing=0 cellpadding=5>\n<tr><td class=colhead>Name</td><td class=colhead>Image Name</td><td class=colhead>Pic</td><td class=colhead>Change</td>\n");

while ($arr = mysql_fetch_assoc($rescategories))
print("<form name=$arr[id] method=post action=adminstuff.php><tr><input type=hidden name=categoryid value=\"$arr[id]\"><td><input type=text name=name value=\"$arr[name]\"></td><td><input type=text name=image value=\"$arr[image]\"></td><td><img src=$BASEURL/pic/$arr[image]></td><td><input type=submit value=Change></form></td></tr>\n");
print("</table><p></p><table border=1 cellspacing=0 cellpadding=5>\n<tr><td class=colhead>Name</td><td class=colhead>Image Name</td><td class=colhead>Create</td>\n");
print("<tr><form name=newcategory method=post action=adminstuff.php><input type=hidden name=newcategory value=true><td><input type=text name=name></td><td><input type=text name=image></td><td><input type=submit value=Create></form></td></tr>");
print("</table><p></p>");

cpfooter();
end_main_frame();
stdfoot();

?>