<?php
$rootpath = '../';
require $rootpath."include/bittorrent.php";
include "func.php";
$content = !empty($_POST['content']) ? $_POST['content'] : "---";
$write = update("ads",$content);
Ffactory::reset_cache("ads",'databasevalue');
header('location:ads.php');
?>