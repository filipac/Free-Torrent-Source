<?php
require "include/bittorrent.php";
global $pic_base_url;
lang::load('staff');
stdhead(str1);
begin_main_frame('100%');

$ret = mysql_query("SELECT * FROM usergroups WHERE showonstaff = 'yes'ORDER BY id DESC");
while($varet = mysql_fetch_assoc($ret)) {
	$a = $varet['title'];
	$uid = $varet['id'];
?>
                <?php
                // Get current datetime
                $dt = gmtime() - 180;
                $dt = sqlesc(get_date_time($dt));
                // Search User Database for Moderators and above and display in alphabetical order
                $res = mysql_query("SELECT * FROM users WHERE class = $uid AND status='confirmed' ORDER BY username" ) or sqlerr();
                while ($arr = mysql_fetch_assoc($res))
                {
                	$land = sql_query("SELECT name,flagpic FROM countries WHERE id=$arr[country]") or sqlerr();
  $arr2 = mysql_fetch_assoc($land);
  $uname = get_style($arr['class'],$arr['username']);
                $staff_table[$arr['class']]=$staff_table[$arr['class']].
        	            "<td class=embedded><a class=altlink href=userdetails.php?id=$arr[id]>$uname</a><td class=embedded><img src=$pic_base_url/button_o".($arr[last_access]>$dt?"n":"ff")."line.gif></td>".
   		    "<td class=embedded><a href=sendmessage.php?receiver=$arr[id]>".
            "<img src=$pic_base_url/pm.gif border=0></a></td>".
            "<td class=embedded><img src=".$pic_base_url."/flag/$arr2[flagpic] border=0></td>\n";

                // Show 3 staff per row, separated by an empty column
                ++ $col[$arr['class']];
                if ($col[$arr['class']]<=0)
                $staff_table[$arr['class']]=$staff_table[$arr['class']]."<td class=embedded>&nbsp;</td>";
                else
                {
                $staff_table[$arr['class']]=$staff_table[$arr['class']]."</tr><tr height=15>";
                $col[$arr['class']]=1;
                }
                }
                ?>
<?php
collapses('staff-'.$a,"<b><font color=white>$a</b>",'100',0,'class=thead','class=tcat');
?>
                <table width=100% cellspacing=0 cellpadding=5>
<tr>
<td class=subheader width="70%"><?=str2?></td>
<td class=subheader width="10%"><?=str3?></td>
<td class=subheader width="10%"><?=str4?></td>
<td class=subheader width="10%"><?=str5?></td>
</tr>
                <tr height=15>
                <?=$staff_table[$uid];?>
                </tr>
                </table>
                <?collapsee();
                ?>
                <br>

<?php
}
#end_frame();
if (!$act) {
$dt = gmtime() - 180;
$dt = sqlesc(get_date_time($dt));
// LIST ALL FIRSTLINE SUPPORTERS
// Search User Database for Firstline Support and display in alphabetical order
$res = sql_query("SELECT * FROM users WHERE support='yes' AND status='confirmed' ORDER BY username LIMIT 10") or sqlerr();
while ($arr = mysql_fetch_assoc($res))
{
 $land = sql_query("SELECT name,flagpic FROM countries WHERE id=$arr[country]") or sqlerr();
 $arr2 = mysql_fetch_assoc($land);
 $firstline .= "<tr height=15><td class=embedded><a class=altlink href=userdetails.php?id=".$arr['id'].">".$arr['username']."</a></td>
 <td class=embedded> ".("'".$arr['last_access']."'">$dt?"<img src=".$pic_base_url."button_online.gif border=0 alt=\"online\">":"<img src=".$pic_base_url."button_offline.gif border=0 alt=\"offline\">" )."</td>".
 "<td class=embedded><a href=sendmessage.php?receiver=".$arr['id'].">"."<img src=".$pic_base_url."pm.gif border=0></a></td>".
 "<td class=embedded>".$arr['supportlang']."</td>".
 "<td class=embedded>".$arr['supportfor']."</td></tr>\n";
}

collapses('staff-fls',"<b><font color=white>".str6,'100',0,'class=thead','class=tcat');
?>

<table width=710 cellspacing=0>
<tr>
<td class=embedded colspan=11><?=str7?><br><br><br></td></tr>
<!-- Define table column widths -->
<tr>
<td class=embedded width="100"><b><?=str8?></b></td>&nbsp;
<td class=embedded align=center width="100"><b><?=str9?></b></td>&nbsp;&nbsp;&nbsp;

<td class=embedded align=center width="100"><b><?=str10?></b></td>&nbsp;
<td class=embedded align=center width="120"><b><?=str11?></b></td>&nbsp;
<td class=embedded width="200"><b><?=str12?></b></td>
</tr>


<tr>
<tr><td class=embedded colspan=11><hr color="#4040c0" size=1></td></tr>

<?=$firstline?>

</tr>
</table>
<?php
collapsee();
}
end_main_frame();
stdfoot();
?>