<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;

ADMIN::check();
$id = 0 + $_POST['id'];
$name = $_POST['name'];
$img = $_POST['image'];
if(mysql_query("UPDATE newscats SET name = '$name',img='$img' WHERE id = '$id'"))
header("location:../news.php");
?>