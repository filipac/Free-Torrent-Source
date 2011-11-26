<?php
$rootpath = "../";
//error_reporting( E_ALL );
require "bittorrent.php";

lang::load('ajax');
$a = $_GET['action'];
if( $a == 'edit_torrent_descr' ){
// check for valid ID
if( !isset( $_GET['torrent'] ) || !is_numeric( $_GET['torrent'] ) ){
print( a1 );
die();
}
// get the torrent description
$descr_req = mysql_query("SELECT `descr`, `owner` FROM `torrents` WHERE `id` = '".$_GET['torrent']."'") or sqlerr(__FILE__,__LINE__);
$descr = mysql_fetch_assoc( $descr_req );
// make sure user is owner of torrent
if( $CURUSER['id'] != $descr['owner'] ){
print( a2 );
die();
}
print( "<textarea rows=\"20\" cols=\"90\" style=\"border:0px\" onblur=\"if(confirm('Save changes to torrent description?')==true){sndReq('action=save_torrent_descr&torrent=".$_GET['torrent']."&descr='+escape(this.value), 'descrTD')}\">".$descr['descr']."</textarea>" );
} elseif( $a == 'save_torrent_descr' ){
// check for valid ID
if( !isset( $_GET['torrent'] ) || !is_numeric( $_GET['torrent'] ) ){
print( a1 );
die();
}
// get the torrent description
$descr_req = mysql_query("SELECT `owner` FROM `torrents` WHERE `id` = '".$_GET['torrent']."'") or sqlerr(__FILE__,__LINE__);
$descr = mysql_fetch_assoc( $descr_req );
// make sure user is owner of torrent
if( $CURUSER['id'] != $descr['owner'] ){
print( a2 );
die();
}
$upd_sql = "UPDATE `torrents` SET `descr` = '".$_GET['descr']."' WHERE `id` = '".$_GET['torrent']."'";
mysql_query($upd_sql) or sqlerr(__FILE__,__LINE__);
print( format_comment( $_GET['descr']  ) );
} elseif( $a == 'change_banned_torrent' ){
//check valid torrent
if( !isset( $_GET['torrent'] ) || !is_numeric( $_GET['torrent'] ) ){
print( a1 );
die();
}
// check is mod or higher
if( (!ur::cstaff()) AND get_user_class() < UC_MODERATOR ){
print( a2 );
die();
}
// create the select
print( "<select onchange=\"if(confirm('".a9."')==true){sndReq('action=save_banned_torrent&torrent=".$_GET['torrent']."&banned='+this.selectedIndex, 'bannedChange')}\">
<option value=\"\" selected=\"selected\">".a3."</option>
<option value=\"1\">".a4."</option>
<option value=\"0\">".a5."</option>
</select>
");
} elseif( $a == 'save_banned_torrent' ){
//check valid torrent
if( !isset( $_GET['torrent'] ) || !is_numeric( $_GET['torrent'] ) ){
print( a1 );
die();
}
// check is mod or higher
if( (!ur::cstaff()) AND get_user_class() < UC_MODERATOR ){
print( a2 );
die();
}
// convert $_GET['banned'] to 'yes' or 'no'
switch( $_GET['banned'] ){
case 1 : $state = 'yes'; break;
case 2 : $state = 'no'; break;
default : $state = 'no'; break;
}
// do the SQL
mysql_query("UPDATE `torrents` SET `banned` = '".$state."' WHERE `id` = '".$_GET['torrent']."' LIMIT 1") or sqlerr(__FILE__,__LINE__);
// print the outcome
print( $state );
} elseif( $a == 'change_type_torrent' ){
//check valid torrent
if( !isset( $_GET['torrent'] ) || !is_numeric( $_GET['torrent'] ) ){
print( a1 );
die();
}
// check is mod or higher
if((!ur::cstaff()) AND  get_user_class() < UC_MODERATOR ){
print( a2 );
die();
}
// create the select
print("<select onchange=\"if(confirm('".a8."')==true){sndReq('action=save_type_torrent&torrent=".$_GET['torrent']."&type='+this.options[this.selectedIndex].value, 'typeChange')}\">");
$cats = genrelist();
print("<option value=\"\">".a6."</option>\n");
foreach ($cats as $row){
print("<option value=\"".$row["id"]."\">".htmlspecialchars($row["name"])."</option>\n");
}
print("</select>\n");
} elseif( $a == 'save_type_torrent' ){
//check valid torrent
if( !isset( $_GET['torrent'] ) || !is_numeric( $_GET['torrent'] ) ){
print( a1 );
die();
}
// check is mod or higher
if((!ur::cstaff()) AND  get_user_class() < UC_MODERATOR ){
print( a2 );
die();
}
// do the SQL
mysql_query("UPDATE `torrents` SET `category` = '".$_GET['type']."' WHERE `id` = '".$_GET['torrent']."' LIMIT 1") or sqlerr(__FILE__,__LINE__);
// get the category in text form
$cats_res = mysql_query("SELECT `name` FROM `categories` WHERE `id` = '".$_GET['type']."'");
$cat = mysql_result( $cats_res, 0, 0 );
// print the outcome
print( $cat );
}
?>