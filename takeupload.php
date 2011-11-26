<?php
require_once("include/benc.php");
require_once("include/bittorrent.php");
ini_set("upload_max_filesize",$max_torrent_size);


 
loggedinorreturn();
iplogger ();


if ($CURUSER["uploadpos"] == 'no')
die;

foreach(explode(":","descr:type:name") as $v) {
	if (!isset($_POST[$v]))
		bark("missing form data");
}

if (!isset($_FILES["file"]))
	bark("missing form data");

$f = $_FILES["file"];
$fname = unesc($f["name"]);
if (empty($fname))
    bark("Empty filename!");
if ($_POST['uplver'] == 'yes') {
$anonymous = "yes";
$anon = "Anonymous";
}
else {
$anonymous = "no";
$anon = $CURUSER["username"];
}
if ($_POST['free'] == 1) 
$free = 'yes';
else
$free = 'no';
if ($_POST['doubleupload'] == 1) 
$doubleupload = 'yes';
else
$doubleupload = 'no';
if ($_POST['sticky'] == 'yes')
$sticky = 'yes';
else
$sticky = 'no';	
$nfofile = $_FILES['nfo'];
$imageurl = !empty($_POST['imageurl']) ? $_POST['imageurl'] : '';
if(_youtube_mod_ == 'yes') {
	if (!empty($_POST['tube']))
$tube = unesc($_POST['tube']);
}
if ($nfofile['name'] != '') {

if ($nfofile['size'] == 0)
bark("0-byte NFO");

if ($nfofile['size'] > 65535)
bark("NFO is too big! Max 65,535 bytes.");

$nfofilename = $nfofile['tmp_name'];

if (@!is_uploaded_file($nfofilename))
bark("NFO upload failed");
}

$descr = unesc($_POST["descr"]);
if (!$descr)
  bark("You must enter a description!");

$catid = (0 + $_POST["type"]);
if (!is_valid_id($catid))
	bark("You must select a category to put the torrent in!");
	
if (!validfilename($fname))
	bark("Invalid filename!");
if (!preg_match('/^(.+)\.torrent$/si', $fname, $matches))
	bark("Invalid filename (not a .torrent).");
$shortfname = $torrent = $matches[1];
if (!empty($_POST["name"]))
	$torrent = unesc($_POST["name"]);

$tmpname = $f["tmp_name"];
if (!is_uploaded_file($tmpname))
	bark("eek");
if (!filesize($tmpname))
	bark("Empty file!");

$dict = bdec_file($tmpname, $max_torrent_size);
if (!isset($dict))
	bark("What the hell did you upload? This is not a bencoded file!");





list($ann, $info) = dict_check($dict, "announce(string):info");
list($dname, $plen, $pieces) = dict_check($info, "name(string):piece length(integer):pieces(string)");
global $privatep;
if($privatep == 'no'):
if (!in_array($ann, $announce_urls, 1))
{
	$aok=false;
	foreach($announce_urls as $au)
	{
		if($ann=="$au?passkey=$CURUSER[passkey]")  $aok=true;
	}
	if(!$aok)
		bark("Invalid announce url! Must be: " . $announce_urls[0] . "?passkey=$CURUSER[passkey]");
}
endif;

if (strlen($pieces) % 20 != 0)
	bark("invalid pieces");

$filelist = array();
$totallen = dict_get($info, "length", "integer");
if (isset($totallen)) {
	$filelist[] = array($dname, $totallen);
	$type = "single";
}
else {
	$flist = dict_get($info, "files", "list");
	if (!isset($flist))
		bark("missing both length and files");
	if (!count($flist))
		bark("no files");
	$totallen = 0;
	foreach ($flist as $fn) {
		list($ll, $ff) = dict_check($fn, "length(integer):path(list)");
		$totallen += $ll;
		$ffa = array();
		foreach ($ff as $ffe) {
			if ($ffe["type"] != "string")
				bark("filename error");
			$ffa[] = $ffe["value"];
		}
		if (!count($ffa))
			bark("filename error");
		$ffe = implode("/", $ffa);
		$filelist[] = array($ffe, $ll);
	}
	$type = "multi";
}
if($privatep == 'yes'):
$dict['value']['announce']=bdec(benc_str( $announce_urls[0]));  // change announce url to local
$dict['value']['info']['value']['private']=bdec('i1e');  // add private tracker flag
$dict['value']['info']['value']['source']=bdec(benc_str( "[$DEFAULTBASEURL] $SITENAME")); // add link for bitcomet users
unset($dict['value']['announce-list']); // remove multi-tracker capability
unset($dict['value']['nodes']); // remove cached peers (Bitcomet & Azareus)
$dict=bdec(benc($dict)); // double up on the becoding solves the occassional misgenerated infohash
list($ann, $info) = dict_check($dict, "announce(string):info");
endif;
$infohash = pack("H*", sha1($info["string"]));

// Replace punctuation characters with spaces

$torrent = str_replace("_", " ", $torrent);

$nfo = sqlesc(str_replace("\x0d\x0d\x0a", "\x0d\x0a", @file_get_contents($nfofilename)));

$ret = sql_query("INSERT INTO torrents (search_text, filename, owner, visible, anonymous, info_hash, name, size, numfiles, type, descr, ori_descr, category, save_as, added, last_action, nfo, imageurl, free, sticky, doubleupload".(_youtube_mod_ == 'yes' ? ', tube' : "").") VALUES (" .
		implode(",", array_map("sqlesc", array(searchfield("$shortfname $dname $torrent"), $fname, $CURUSER["id"], "no", $anonymous, $infohash, $torrent, $totallen, count($filelist), $type, $descr, $descr, 0 + $_POST["type"], $dname))) .
		", '" . get_date_time() . "', '" . get_date_time() . "', $nfo, '$imageurl', '$free', '$sticky', '$doubleupload'".(_youtube_mod_ == 'yes' ? ", '$tube'" : "").")");
if (!$ret) {
	if (mysql_errno() == 1062)
		bark("torrent already uploaded!");
	bark("mysql puked: ".mysql_error());
}
$id = mysql_insert_id();

@sql_query("DELETE FROM files WHERE torrent = $id");
foreach ($filelist as $file) {
	@sql_query("INSERT INTO files (torrent, filename, size) VALUES ($id, ".sqlesc($file[0]).",".$file[1].")");
}
if($privatep == 'no')
move_uploaded_file($tmpname, "$torrent_dir/$id.torrent");
else {
	$fp = fopen("$torrent_dir/$id.torrent", "w");
if ($fp)
{
        @fwrite($fp, benc($dict), strlen(benc($dict)));
    fclose($fp);
}
}
//===add karma
UserHandle::KPS("+","15",$CURUSER["id"]);
//===end

if ($CURUSER["anonymous"]=='yes')
	write_log("Torrent $id ($torrent) was uploaded by Anonymous");
else
	write_log("Torrent $id ($torrent) was uploaded by $CURUSER[username]");

//===notify people who voted on offer thanks CoLdFuSiOn :)
if (isset($_POST['offer'])) {
$res = mysql_query("SELECT `userid` FROM `offervotes` WHERE `userid` != " . $CURUSER["id"] . " AND `offerid` = ". ($_POST['offer'] + 0)) or sqlerr(__FILE__, __LINE__);
$pn_msg = "The Offer you voted for: \"$torrent\" was uploaded by " . $CURUSER["username"] . ".\nYou can Download the Torrent [url=$DEFAULTBASEURL/details.php?id=$id&hit=1]here[/url]";

while($row = mysql_fetch_assoc($res)) {
//=== use this if you DO have subject in your PMs
$subject = "Offer $torrent was just uploaded";
//=== use this if you DO NOT have subject in your PMs
//$some_variable .= "(0, 0, $row[userid], '" . get_date_time() . "', " . sqlesc($pn_msg) . ")";

//=== use this if you DO have subject in your PMs
mysql_query("INSERT INTO messages (poster, sender, subject, receiver, added, msg) VALUES (0, 0, ".sqlesc($subject).", $row[userid], ".sqlesc(get_date_time()).", " . sqlesc($pn_msg) . ")") or sqlerr(__FILE__, __LINE__);
//=== use this if you do NOT have subject in your PMs
//mysql_query("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES ".$some_variable."") or sqlerr(__FILE__, __LINE__);
//===end
}
//=== delete all offer stuff
@mysql_query("DELETE FROM `offers` WHERE `id` = ". ($_POST['offer'] + 0));
@mysql_query("DELETE FROM `offervotes` WHERE `offerid` = ". ($_POST['offer'] + 0));
@mysql_query("DELETE FROM `comments` WHERE `offer` = ". ($_POST['offer'] + 0). "");
}
//=== end notify people who voted on offer

/* Email notifs */


$res = sql_query("SELECT name FROM categories WHERE id=$catid") or sqlerr(__FILE__,__LINE__);
$arr = mysql_fetch_assoc($res);
$cat = $arr["name"];
$res = sql_query("SELECT email FROM users WHERE enabled='yes' AND parked='no' AND status='confirmed' AND notifs LIKE '%[cat$catid]%' AND notifs LIKE '%[email]%'") or sqlerr(__FILE__, __LINE__);

$uploader = $anon;

$size = mksize($totallen);
$description = ($html ? strip_tags($descr) : $descr);

$body = <<<EOD
Hi,

A new torrent has been uploaded.

Name: $torrent
Size: $size
Category: $cat
Uploaded by: $uploader

Description
-------------------------------------------------------------------------------
$description
-------------------------------------------------------------------------------

You can use the URL below to download the torrent (you may have to login).

$DEFAULTBASEURL/details.php?id=$id&hit=1

------
Yours,
The $SITENAME Team.
EOD;
$to = "";
$nmax = 100; // Max recipients per message
$nthis = 0;
$ntotal = 0;
$total = mysql_num_rows($res);
while ($arr = mysql_fetch_row($res))
{
  if ($nthis == 0)
    $to = $arr[0];
  else
    $to .= "," . $arr[0];
  ++$nthis;
  ++$ntotal;
  if ($nthis == $nmax || $ntotal == $total)
  {
	  $sm = sent_mail("Multiple recipients <$SITEEMAIL>",$SITENAME,$SITEEMAIL,"$SITENAME New torrent - $torrent",$body,"torrent upload",false,true,$to);
    if (!$sm)
	  stderr("Error", "Your torrent has been been uploaded. DO NOT RELOAD THE PAGE!\n" .
	    "There was however a problem delivering the e-mail notifcations.\n" .
	    "Please let an administrator know about this error!\n");
    $nthis = 0;
  }
}
if($_POST['uplver'] == 'yes')
$usernn = 'Anonymous';
else
$usernn = $CURUSER['username'];
$message = "The torrent [url=$BASEURL/details.php?id=$id&hit=1]$torrent [/url]has been uploaded by $usernn :)";
if(duty('torrents'))
add_shout($message);
header("Location: $BASEURL/details.php?id=".htmlspecialchars($id)."&uploaded=1");
?>