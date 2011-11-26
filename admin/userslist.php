<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;
ADMIN::check();

$res = sql_query("SELECT COUNT(*) FROM users WHERE class='0'") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);
$count = $row[0];

$perpage = 100;

list($pagertop, $pagerbottom, $limit) = pager(50, $count, "userslist.php?");
stdhead("UsersList");
if (mysql_num_rows($res) == 0)
begin_main_frame();
// ===================================
$users = number_format(get_row_count("users", "WHERE class='0'"));
begin_frame("Users List ($users)", true);
begin_table(true);
echo $pagerbottom;
?>
<form method="post" action="takeuserslist.php">
<tr><td class="colhead">ID</td><td class="colhead" align="left">Username</td><td class="colhead" align="left">e-mail</td><td class="colhead" align="left">Joined</td><td class="colhead" align="center">DELETE</td><td class="colhead" align="center">BAN</td><td class="colhead" align="center">UNBAN</td></tr>
<?php

$res=sql_query("SELECT id,username,email,added,enabled,warned FROM users WHERE class='0' ORDER BY id DESC $limit") or sqlerr(__FILE__, __LINE__);
// ------------------
while ($arr = @mysql_fetch_assoc($res)) {
echo "<tr><td>" . $arr[id] . "</td><td align=\"left\"><b><a href=$BASEURL/userdetails.php?id=" . $arr[id] . ">". $arr[username] . "</b> ".($arr[enabled] == "no" ? "<font color=red>(banned)</font>" : "")."</td><td align=\"left\"><a href=mailto:" . $arr[email] . ">" . $arr[email] . "</a></td><td align=\"left\">" . $arr[added] . "</td><td align=\"center\"><input type=\"checkbox\" name=\"delusr[]\" value=\"" . $arr[id] . "\" /></td><td align=\"center\"><input type=\"checkbox\" name=\"banusr[]\" value=\"" . $arr[id] . "\" /></td><td align=\"center\"><input type=\"checkbox\" name=\"unbanusr[]\" value=\"" . $arr[id] . "\" /></td></tr>";
}
?>
<tr><td colspan="7" align="right"><input name="do" type="submit" value="do it!" /></td></tr>
</form>
<?php
// ------------------
  end_table();
  end_frame();
// ===================================
end_main_frame();
stdfoot();
?>