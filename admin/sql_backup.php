<?php
$rootpath = '../';
include $rootpath.'include/bittorrent.php';
ADMIN::check();
HANDLE::Freq('libs.backup.','class_sql','.php');
	$dir_name = 'backup' ;
	$fileSQL = 'test1' ;
	$day = date("j");
	$month = strtolower(date("M"));
	$year = date("Y");
	$hour = date("G");
	$minute = date("i");
	$extra = $day.'_'.$month.'_'.$year.'_'.$hour.'-'.$minute;
	$filename = 'backup_'.$extra.'.sql' ; 
	$mkBackup	= new db_backup($filename);
	include INC_PATH . '/libs/config/database.php';
	global $mysql_host, $mysql_user, $mysql_pass, $mysql_db;
	$mkBackup->Backup($mysql_host,"",$mysql_user,$mysql_pass,$mysql_db);
	stdhead("Backup Done");
	stdmsg("Done!",'You can find the file in include/backups_sql directory.');
	$test = opendir($rootpath."include/backups_sql");
	echo 'Backup files that already are in the directory: <BR>';
$already = dir_list($rootpath.'include/backups_sql');
foreach($already AS $a)
echo $a.'<BR>';
	stdfoot();
	?>