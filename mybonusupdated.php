<?php
require_once('include/bittorrent.php');
session_start();
ob_start();

loggedinorreturn();

stdhead($CURUSER['username'] . "'s Bonus");

stdmsg("Updated!", "Your points has been updated! Please click <a href=mybonus.php target=_self>here</a> to continue.");
  stdfoot();
  exit;
  
?>