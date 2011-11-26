<?php
require_once("include/bittorrent.php");
logoutcookie();
logoutsession();
Header("Location: $BASEURL/");
?>