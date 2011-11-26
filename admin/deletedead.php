<?php
$rootpath = '../';
require $rootpath."include/bittorrent.php";

ADMIN::check();

stdhead("Delete all Dead Torrents");


if($_GET['ja'] == 1){
mysql_query("DELETE FROM torrents WHERE visible='no'") or sqlerr(__FILE__, __LINE__);
echo "All Dead Torrents have been deleted!";
}
else{
echo "Are you sure to delete all dead torrents? <br><br><a href= 'deletedead.php?ja=1'>Yes</a>";
}

stdfoot();

?>