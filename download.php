<?php
require_once("include/bittorrent.php");

loggedinorreturn();

parked();
@ini_set('zlib.output_compression', 'Off');
@set_time_limit(0);

if (@ini_get('output_handler') == 'ob_gzhandler' AND @ob_get_length() !== false)
{	// if output_handler = ob_gzhandler, turn it off and remove the header sent by PHP
	@ob_end_clean();
	header('Content-Encoding:');
}

$id = (int)$_GET["id"];
$name = $_GET["name"];

if (!$id)
	httperr();

$res = sql_query("SELECT name, filename, size, owner, nfo FROM torrents WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_assoc($res);

$fn = "$torrent_dir/$id.torrent";

if (!$row || !is_file($fn) || !is_readable($fn))
	httperr();

sql_query("UPDATE torrents SET hits = hits + 1 WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);

require_once "include/benc.php";

if (strlen($CURUSER['passkey']) != 32) {
	$CURUSER['passkey'] = md5($CURUSER['username'].get_date_time().$CURUSER['passhash']);
	sql_query("UPDATE users SET passkey=".sqlesc($CURUSER[passkey])." WHERE id=".sqlesc($CURUSER[id]));
}

$dict = bdec_file($fn, (1024*1024));
$dict['value']['announce']['value'] = "$BASEURL/announce.php?passkey=$CURUSER[passkey]";
$dict['value']['announce']['string'] = strlen($dict['value']['announce']['value']).":".$dict['value']['announce']['value'];
$dict['value']['announce']['strlen'] = strlen($dict['value']['announce']['string']);
// Remove multiple trackers from torrent
unset($dict['value']['announce-list']);
global $enablezipmode;
$usezip = $enablezipmode;
if ($usezip != 'yes')
{
    require_once('include/functions_browser.php');
    if (is_browser('ie'))
    {
        header("Pragma: public");
        header("Expires: 0"); // set expiration time
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Disposition: attachment; filename=".basename($row["filename"]).";");
        header("Content-Transfer-Encoding: binary");
    }
    else
    {
        header ("Expires: Tue, 1 Jan 1980 00:00:00 GMT");
        header ("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
        header ("Cache-Control: no-store, no-cache, must-revalidate");
        header ("Cache-Control: post-check=0, pre-check=0", false);
        header ("Pragma: no-cache");
        header ("X-Powered-By: ".VERSION." (c) ".date("Y")." ".$SITENAME."");
        header ("Accept-Ranges: bytes");
        header ("Connection: close");
        header ("Content-Transfer-Encoding: binary");
        header ("Content-Type: application/x-bittorrent");
        header ("Content-Disposition: attachment; filename=".basename($row["filename"]).";");
    }
    ob_implicit_flush(true);
    print(benc($dict));
}
else
{
    require_once('include/class_zip.php');
    $createZip = new createZip;
    $fil = strtolower(str_replace(" ","_",$fileContents2));
    $fileContents2 = 'This torrent was downloaded from '.$BASEURL;
    $fil = strtolower(str_replace(" ","_",str_replace("http://","",$fileContents2))).'.txt';
    global $zipnfo,$ziptxt;
    if($ziptxt == 'yes')
    $createZip -> addFile($fileContents2, $fil);
    $createZip -> addFile(benc($dict), $row["filename"]);
    if(!empty($row['nfo']) || strlen($row['nfo']) != 0)
    if($zipnfo == 'yes')
    $createZip -> addFile($row['nfo'], "nfo.nfo");
    $fileName = $row['filename'].'.zip';
    $fd = fopen ($cache."/".$fileName, "wb");
    $out = fwrite ($fd, $createZip -> getZippedfile());
    fclose ($fd);
    $createZip -> forceDownload($cache."/".$fileName);
}
?>