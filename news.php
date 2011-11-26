<?php
require "include/bittorrent.php";
loggedinorreturn();

if (get_user_class() < UC_ADMINISTRATOR)
stderr("Error", "Permission denied.");

$action = $_GET["action"];

$cat = "<option value=mystatus>---None selected---</option>n";
$cat_r = mysql_query("SELECT * from newscats ORDER BY name") or die;
while ($cat_a = mysql_fetch_array($cat_r))
$cat .= "<option value=$cat_a[img]" . ($arr["img"] == $cat_a['img'] ? " selected" : "") . ">$cat_a[name]</option>\n";

// Delete News Item //////////////////////////////////////////////////////
if ($action == 'delete')

{

$newsid = $_GET["newsid"];

if (!is_valid_id($newsid))

stderr("Error","Invalid news item ID - Code 1.");

$returnto = $_GET["returnto"];

$sure = $_GET["sure"];

if (!$sure)

stderr("Delete news item","Do you really want to delete a news item? Click " .

"<a href=?action=delete&newsid=$newsid&returnto=$returnto&sure=1>here</a> if you are sure.",0);

mysql_query("DELETE FROM news WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);

if ($returnto != "")

header("Location: $returnto");

else

$warning = "News item was deleted successfully.";

}



// Add News Item /////////////////////////////////////////////////////////



if ($action == 'add')

{



$body = $_POST["body"];

if (!$body)

stderr("Error","The news item cannot be empty!");



$title = $_POST['title'];

if (!$title)

stderr("Error","The news title cannot be empty!");



$added = $_POST["added"];

if (!$added)

$added = sqlesc(get_date_time());



$cat = $_POST["cat"];

if (!$cat)

stderr("Error","The news category cannot be empty!");



mysql_query("INSERT INTO news (userid, added, body, title, cat) VALUES (".

$CURUSER['id'] . ", $added, " . sqlesc($body) . ", " . sqlesc($title) . ", " . sqlesc($cat) . ")") or sqlerr(__FILE__, __LINE__);

if (mysql_affected_rows() == 1)

$warning = "News item was added successfully.";

else

stderr("Error","Something weird just happened.");

}



// Edit News Item ////////////////////////////////////////////////////////



if ($action == 'edit')

{



$newsid = $_GET["newsid"];



if (!is_valid_id($newsid))

stderr("Error","Invalid news item ID - Code 2.");



$res = mysql_query("SELECT * FROM news WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);



if (mysql_num_rows($res) != 1)

stderr("Error", "No news item with ID $newsid.");



$arr = mysql_fetch_array($res);



if ($_SERVER['REQUEST_METHOD'] == 'POST')

{

$body = $_POST['body'];



if ($body == "")

stderr("Error", "Body cannot be empty!");



$title = $_POST['title'];



if ($title == "")

stderr("Error", "Title cannot be empty!");



$cat = $_POST['cat'];



if ($cat == "")

stderr("Error", "Category cannot be empty!");


$body = sqlesc($body);



$editedat = sqlesc(get_date_time());



mysql_query("UPDATE news SET body=$body, title='$title', cat='$cat' WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);



$returnto = $_POST['returnto'];



if ($returnto != "")

header("Location: $returnto");

else

$warning = "News item was edited successfully.";

}

else

{

$returnto = $_GET['returnto'];

stdhead();
$cat1 = mysql_fetch_assoc(mysql_query("SELECT cat FROM news WHERE id = '$newsid'"));
$cat1 = $cat1['cat'];

print("<h1>Edit News Item</h1>");

print("<form method=post action=?action=edit&newsid=$newsid>");

print("<table border=1 cellspacing=0 cellpadding=5>");

print("<input type=hidden name=returnto value=$returnto>");

print("<tr><td style='padding: 10px'>Title: <input type=text name=title value=" . htmlspecialchars($arr["title"]) . ">");

print("<br><img src=pic/spacer.gif width=100 height=1><br>");
$cat_r = mysql_query("SELECT * from newscats ORDER BY id") or die;
while ($cat_a = mysql_fetch_array($cat_r)):
$catsel .= "<option value='$cat_a[id]' ".($cat1 == $cat_a['id'] ? "SELECTED" : "").">$cat_a[name]</option>\n";
endwhile;
print("Cat: <select name=cat>$catsel</select><br>");

print("<br><img src=pic/spacer.gif width=100 height=1><br>");

print("<textarea name=body cols=145 rows=5 style='border: 0px'>" . htmlspecialchars($arr["body"]) . "</textarea>");

print("<p align=center><input type=submit value='Okay' class=btn></p>");

print("</td></tr></table>");

print("</form>");

stdfoot();

die;

}

}



// Other Actions and followup ////////////////////////////////////////////



stdhead("Site news");
?>
<div id="editcat" style="display:none;">
<?php
begin_frame("Edit Categories");
$r1 = mysql_query("SELECT * FROM newscats") or die(mysql_error());
while($r = mysql_fetch_assoc($r1)):
?>
<?="<a href=javascript:; onclick=\"$('#editcat$r[id]').show('slow');\">$r[name]</a>"?>
<div id="editcat<?=$r['id']?>" style="display:none;">
<form action="admin/editcat.php" method="post">
<input type='hidden' name='id' value='<?=$r['id'];?>' > 
    Name: <input type="text" name="name" id=name value="<?=$r['name'];?>"> <br>
	Image(the image has to be placed in <?=$pic_base_url?>news directory: <input type="text" name="image" id=image value="<?=$r['img'];?>"> <br> 
    <input type="submit" value="Edit Category" /> [<a href=javascript:; onclick="$('#editcat<?=$r['id']?>').hide('slow');">Cancel edit</a>] [<a href=admin/deletencat.php?id=<?=$r['id']?>>Delete Category</a>]
</form>
</div><br>
<?php
endwhile;
end_frame();
?>
</div>
<?php
print("<h1>Submit News Item</h1>");
if ($warning)

print("<p><font size=-3>($warning)</font></p>");
?>
<div id="addcat" style="display:none;">
<?php
javascript('jquery.select');
begin_frame('Add News Category',1,'10','100%');
?>
<form id="AddCat" action="admin/addncat.php" method="post"> 
    Name: <input type="text" name="name" id=name /> <br>
	Image(the image has to be placed in <?=$pic_base_url?>news directory: <input type="text" name="image" id=image /> <br> 
    <input type="submit" value="Submit Category" /> 
</form>
<script type="text/javascript">  
$(document).ready(function() {  
    $('#AddCat').ajaxForm(function() {
            	$('#addcat').hide('slow'); 
				var name = jQuery.fieldValue($("#name")[0]); 
                var image = jQuery.fieldValue($("#image")[0]);
                var myOptions = {
	image : name
}
$("#cats").addOption(myOptions, true);
            }); 
});
    </script> 
<a href=javascript:; onclick= "$('#addcat').hide('fast');">Cancel</a>
<?php
end_frame();
?>
</div><br><br>
<?php
print("<form method=post action=?action=add>");

print("<table border=1 cellspacing=0 cellpadding=5>");

print("<tr><td style='padding: 10px'>Title: <input type=text name=title><br>");

print("<br><img src=pic/spacer.gif width=100 height=1><br>");
$cat_r = mysql_query("SELECT * from newscats ORDER BY id") or die;
while ($cat_a = mysql_fetch_array($cat_r)):
$catsel .= "<option value='$cat_a[id]'>$cat_a[name]</option>\n";
endwhile;
print("Cat: <select name=cat id=cats>$catsel</select> [<a href=javascript:; onclick= \"$('#addcat').show('fast');\">Add Category</a>] [<a href=javascript:; onclick= \"$('#editcat').show('fast');\">Edit Categories</a>]<br>");

print("<br><img src=pic/spacer.gif width=100 height=1><br>");

print("<textarea name=body cols=141 rows=5 style='border: 0px'></textarea>");

print("<br><br><div align=center><input type=submit value='Okay' class=btn></div></td></tr>");

print("</table></form><br><br>");



$res = mysql_query("SELECT * FROM news ORDER BY added DESC") or sqlerr(__FILE__, __LINE__);



if (mysql_num_rows($res) > 0)

{





begin_main_frame();

begin_frame();



while ($arr = mysql_fetch_array($res))

{

$newsid = $arr["id"];

$body = $arr["body"];

$title = $arr["title"];

$cat = $arr["cat"];

$userid = $arr["userid"];

$added = $arr["added"] . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]))) . " ago)";



$res2 = mysql_query("SELECT username, donor FROM users WHERE id = $userid") or sqlerr(__FILE__, __LINE__);

$arr2 = mysql_fetch_array($res2);



$postername = $arr2["username"];



if ($postername == "")

$by = "unknown[$userid]";

else

$by = "<a href=userdetails.php?id=$userid><b>$postername</b></a>" .

($arr2["donor"] == "yes" ? "<img src=pic/star.png alt='Donor'>" : "");



print("<p class=sub><table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>");

print("$added --- by $by");

print(" - [<a href=?action=edit&newsid=$newsid><b>Edit</b></a>]");

print(" - [<a href=?action=delete&newsid=$newsid><b>Delete</b></a>]");

print("</td></tr></table></p>");



begin_table(true);

print("<tr valign=top><td class=comment><b>$title</b><br>$body</td></tr>");

end_table();

}

end_frame();

end_main_frame();

}

else

stdmsg("Sorry", "No news available!");

stdfoot();

die;

?>