<?php
define('VERSIONcp','2.0 BY FR33BH');
$rootpath = '../';
include $rootpath.'include/bittorrent.php';
lang::load(create_page);

ADMIN::check();
stdhead(str1 .' :: Create page version '.VERSIONcp);
?>
<form action=<?=$BASEURL?>/takepage.php method=post>
<?=str2?>: <input type="text" name="pagename"><br>
<?=str3?>: <input type="text" name="pagetitle"><br>
<?=str4?>: <input type="checkbox" name="private" value="yes" /><br>
<?=str5?>:<br> <textarea rows=30 cols=100 name=content></textarea><br>
<input type="submit" value="<?=str6?>" />
 </form> <?php

stdfoot();
?>