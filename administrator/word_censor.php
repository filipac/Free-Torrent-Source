<?php
$rootpath = '../';
include $rootpath.'include/bittorrent.php';
loggedinorreturn();
if(!ur::isadmin()) {
	write_log("User $CURUSER[username] tried to view the administration panel, but it was stopped because his usergroup doesn't have access there.");
die('You\'re to small, baby!<BR>Haccking attempt logged.');
}
$action = isset($_POST['act']) ? $_POST['act'] : (isset($_GET['act']) ? $_GET['act'] : '');
if(empty($action)) {
	print('<link rel="stylesheet" type="text/css" href="controlpanel.css" />');
print ("<form method='post' action='".$_SERVER["SCRIPT_NAME"]."'><input type='hidden' name='act' value='save'>");
	print ("<textarea rows=20 cols=90 name=words>".$WORDCENSOR['words']."</textarea>");
	print ("<BR>Separate words by |(eg: word1|word2)");
	print ("<BR><center><input type=submit value=Save>");
	}elseif($action == 'save') {
			GetVar(array('words'));
	$WC['words'] = $words;
	WriteConfig('WORDCENSOR', $WC);
	redirect('administrator/word_censor.php','Word list has been updated','Success');
	die;
	}
	else die;
	?>