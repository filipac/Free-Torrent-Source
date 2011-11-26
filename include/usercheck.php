<?php
$rootpath = '../';
include($rootpath.'include/bittorrent.php');
$type= $_GET['type'];
if($type == 'user') {
$useer = str_replace('\\','', htmlentities($_GET['user']));
if (empty($useer)) {
print('<font class="usercheck-taken">Cannot be empty <img src="pic/input_error.gif" /></font>');
die;	
}

$sql = "SELECT COUNT(*) FROM users WHERE username='{$useer}'";
  $result = mysql_query($sql);
  if (mysql_result($result, 0) > 0) {
  ?>
  <font class="usercheck-taken">Username is taken. Please choose another <img src="pic/input_error.gif" /></font>
  <?php
  }
  else {
  ?><font class="usercheck-available">Username is avaible. You can go to the next step. <img src="pic/input_true.gif" /></font><?php
  } }
?>