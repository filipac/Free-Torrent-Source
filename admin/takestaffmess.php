<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;
if ($HTTP_SERVER_VARS["REQUEST_METHOD"] != "POST")
	stderr("Error", "Permission denied!");

ADMIN::check();

$sender_id = ($_POST['sender'] == 'system' ? 0 : (int)$CURUSER['id']);
$dt = sqlesc(get_date_time());
$msg = trim($_POST['msg']);
if (!$msg)
	stderr("Error","Don't leave any fields blank.");
$updateset = $_POST['clases'];
if (is_array($updateset)) {
	foreach ($updateset as $class) {
		if (!is_valid_id($class) && $class != 0)
			stderr("Error","Invalid Class");
	}
}else{
	if (!is_valid_id($updateset) && $updateset != 0)
		stderr("Error","Invalid Class");
}
$subject = trim($_POST['subject']);
$query = sql_query("SELECT id FROM users WHERE class IN (".implode(",", $updateset).")");
while($dat=mysql_fetch_assoc($query))
{
	sql_query("INSERT INTO messages (sender, receiver, added,  subject, msg) VALUES ($sender_id, $dat[id], $dt, " . sqlesc($subject) .", " . sqlesc($msg) .")") or sqlerr(__FILE__,__LINE__);
}

header("Refresh: 0; url=staffmess.php?sent=1");
?>