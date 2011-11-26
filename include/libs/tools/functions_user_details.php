<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
class user {
	function upload_speed($uid = 0) {
		global $user;
		$res = sql_query("SELECT name FROM uploadspeed WHERE id=$user[upload] LIMIT 1") or sqlerr();
if (mysql_num_rows($res) == 1)
{
	$arr = mysql_fetch_assoc($res);
	$upload  = "<img src=pic/speed_up.png alt=\"".str27.": $arr[name]\" style='margin-left: 8pt'> $arr[name]";
}
$res = sql_query("SELECT name FROM downloadspeed WHERE id=$user[download] LIMIT 1") or sqlerr();
if (mysql_num_rows($res) == 1)
{
	$arr = mysql_fetch_assoc($res);
	$download = "<img src=pic/speed_down.png alt=\"".str26.": $arr[name]\" style='margin-left: 8pt'> $arr[name]";
}
print("<tr><td class=rowhead>".str56."</td><td align=left>$download $upload</td></tr>\n");
	}
}