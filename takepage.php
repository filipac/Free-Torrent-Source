<?php
include'include/bittorrent.php';
lang::load('takepage');

if(get_user_class() != UC_STAFFLEADER)
die;
$pagename = $_POST['pagename'].'.php';
$pagetitle = $_POST['pagetitle'];
if($_POST['private'] == 'yes') $pp = true;
$content = $_POST['content'];
if(empty($pagename) || empty($pagtitle) || empty($content))
stderr('ERROR','Do not leave anything blank');
if($pp) 
$header = <<<HEADER
<?php
include'include/bittorrent.php';

loggedinorreturn();
stdhead('$pagetitle');
?>
HEADER;
else
$header = <<<HEADER
<?php
include'include/bittorrent.php';

stdhead('$pagetitle');
?>
HEADER;
$footer = <<<FOOTER
<?php
stdfoot();
?>
FOOTER;
$handle = fopen("$pagename",'w');
$write = fwrite($handle,$header.$content.$footer);
$close = fclose($handle);
if(!$write) {
	stderr(str1,str2);
}
else stderr(str3,sprintf(str4,$pagename,'<a href=\''.$pagename.'\'>','</a>'),false);
?>