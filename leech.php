<?php
require "include/bittorrent.php";
loggedinorreturn();

if (get_user_class() > UC_MODERATOR) {
stdhead("Leechers");
begin_main_frame();
// ===================================
$peasant = number_format(get_row_count("users", "WHERE class='0'"));
begin_frame("Leechers ($peasant)", true);
begin_table();
?>
<form method="post" action="takepeasant.php">
<tr><td class="colhead">ID</td><td class="colhead" align="left">Username</td><td class="colhead" align="left">e-mail</td><td class="colhead" align="left">Joined</td><td class="colhead" align="left">Added</td><td class="colhead">Del</td><td class="colhead">Ban</td></tr>
<?php

$res=sql_query("SELECT id,username,email,added FROM users WHERE class='1' ORDER BY id") or print(mysql_error());
// ------------------
while ($arr = @mysql_fetch_assoc($res)) {
echo "<tr><td>" . $arr[id] . "</td><td align=\"left\"><b><a href=userdetails.php?id=" . $arr[id] . ">". $arr[username] . "</b></td><td align=\"left\"><a href=mailto:" . $arr[email] . ">" . $arr[email] . "</a></td><td align=\"left\">" . $arr[added] . "</td><td align=\"left\">" . $arr[last_check1] . "</td><td><input type=\"checkbox\" name=\"delusr[]\" value=\"" . $arr[id] . "\" /></td><td><input type=\"checkbox\" name=\"delusr[]\" value=\"" . $arr[id] . "\" /></td></tr>";
}
?>
<tr><td colspan="7" align="right"><input type="submit" value="Delete!" /></td></tr>
<tr><td colspan="7" align="right"><input type="submit" value="Ban!" /></td></tr>
</form>
<?php
// ------------------
    end_table();
    end_frame();
// ===================================
end_main_frame();
stdfoot();
}
else {
stderr("Sorry", "Access denied!");
}
?>