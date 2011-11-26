<?php
error_reporting(E_ALL & ~E_NOTICE);

// ##################### INCLUDE FILES AND CLEAN POST DATA #####################

require "include/bittorrent.php";
HANDLE::Freq('libs.rss','rss');
loggedinorreturn();
define("RSSVERSION","v0.5");
$categories = (isset($_GET['categories'])) ? htmlspecialchars($_GET['categories']) :'all';
$feedtype = (isset($_GET['feedtype'])) ? htmlspecialchars($_GET['feedtype']) : 'details';
$timezone = (isset($_GET['timezone'])) ? htmlspecialchars($_GET['timezone']) :1;
$showrows = (isset($_GET['showrows'])) ? (int)$_GET['showrows'] : 10;
RSS::PrintRSS($timezone, $showrows, $feedtype, $categories);
?>