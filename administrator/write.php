<?php
$rootpath = '../';
require $rootpath."include/bittorrent.php";
include "func.php";
$content = $_POST['content'];
$write = @update("admin_note",$content);
header('location:main.php');
?>