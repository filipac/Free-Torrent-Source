<?php
include'include/bittorrent.php';

$action = $_GET ['act'];
if($action == 'listen') {
	$string = $_GET['string'];
	include'include/class_wave.php';
				$query = "SELECT * FROM regimages WHERE imagehash= ".sqlesc($string);
		$sql = sql_query($query);
		$regimage = mysql_fetch_array($sql);
		$imagestring = $regimage['imagestring'];
		for($i=0;$i<strlen($imagestring);$i++)
		{
			$newstring .= $space.$imagestring[$i];
			$space = " ";
		}
	$imagestring = strtolower(str_replace(' ','',$newstring));
	createWaveFile($imagestring);
	die;
}
?>