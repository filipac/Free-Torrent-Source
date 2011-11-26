<?php
include ('include/bittorrent_announce.php');
require_once ('include/benc.php');
function err( $msg )
{
    benc_resp( array('failure reason' => array(type => 'string', value => $msg)) ) ;
    exit() ;
}
function benc_resp( $d )
{
    benc_resp_raw( benc(array(type => 'dictionary', value => $d)) ) ;
}
function benc_resp_raw( $x )
{

    header( "Content-Type: text/plain" ) ;
    header( "Pragma: no-cache" ) ;

    if ( $_SERVER["HTTP_ACCEPT_ENCODING"] == "gzip" )
    {
        header( "Content-Encoding: gzip" ) ;
        echo gzencode( $x, 9, FORCE_GZIP ) ;
    }
    else
        echo $x ;
}
foreach (array("passkey", "info_hash", "peer_id", "event", "ip", "localip") as $x) {
    if (isset($_GET["$x"]))
        $GLOBALS[$x] = "" . $_GET[$x];
}
foreach (array("port", "downloaded", "uploaded", "left") as $x) {
    $GLOBALS[$x] = 0 + $_GET[$x];
}

if (strpos($passkey, "?")) {
    $tmp = substr($passkey, strpos($passkey, "?"));
    $passkey = substr($passkey, 0, strpos($passkey, "?"));
    $tmpname = substr($tmp, 1, strpos($tmp, "=") - 1);
    $tmpvalue = substr($tmp, strpos($tmp, "=") + 1);
    $GLOBALS[$tmpname] = $tmpvalue;

}

foreach (array("passkey", "info_hash", "peer_id", "port", "downloaded",
    "uploaded", "left") as $x)
    if (!isset($x))
        err("Missing key: $x");
foreach (array("info_hash", "peer_id") as $x)
    if (strlen($GLOBALS[$x]) != 20)
        err("Invalid $x (" . strlen($GLOBALS[$x]) . " - " . urlencode($GLOBALS[$x]) .
            ")");
if (strlen($passkey) != 32)
    err("Invalid passkey (" . strlen($passkey) . " - $passkey)");
$ip = ip::getip();
$rsize = 50;

foreach (array("num want", "numwant", "num_want") as $k) {
    if (isset($_GET[$k])) {
        $rsize = 0 + $_GET[$k];
        break;
    }
}

// BLOCK ACCESS WITH WEB BROWSERS AND CHEATS!

$agent = $_SERVER["HTTP_USER_AGENT"];
if (ereg("^Mozilla\/", $agent) || ereg("^Opera\/", $agent) || ereg("^Links ", $agent) ||
    ereg("^Lynx\/", $agent))
    err("torrent not registered with this tracker");

if (!$port || $port > 0xffff)
    err("invalid port");

if (!isset($event))
    $event = "";

$seeder = ($left == 0) ? "yes" : "no";

$valid = @mysql_query("SELECT COUNT(*) FROM users WHERE passkey=" . sqlesc($passkey)) or
    err(mysql_error());
$valid = @mysql_fetch_row($valid);

if ($valid[0] != 1)
    err("Invalid passkey! Re-download the .torrent from $BASEURL");

$res = mysql_query("SELECT id, name, category, banned, free, doubleupload, seeders + leechers AS numpeers, UNIX_TIMESTAMP(added) AS ts FROM torrents WHERE " .
    hash_where("info_hash", $info_hash));

$torrent = mysql_fetch_assoc($res);

if (!$torrent)
    err("torrent not registered with this tracker");

$torrentid = $torrent["id"];
$torrentname = $torrent["name"];
$torrentcategory = $torrent["category"];
$fields = "seeder, peer_id, ip, port, uploaded, downloaded, userid, last_action,  UNIX_TIMESTAMP(NOW()) AS nowts, UNIX_TIMESTAMP(prev_action) AS prevts";
$numpeers = $torrent["numpeers"];
$limit = "";
if ($numpeers > $rsize)
    $limit = "ORDER BY RAND() LIMIT $rsize";
$res = mysql_query("SELECT $fields FROM peers WHERE torrent = $torrentid");
    global $announce_interval;
	$resp = "d" . benc_str("interval") . "i" . $announce_interval . "e" . benc_str("peers") . "l";
unset($self);
while ($row = mysql_fetch_assoc($res)) {
    $row["peer_id"] = hash_pad($row["peer_id"]);

    if ($row["peer_id"] === $peer_id) {
        $userid = $row["userid"];
        $self = $row;
        continue;
    }
    $a = @mysql_query("SELECT class FROM users WHERE id = '$userid'");
    $a = @mysql_fetch_assoc($a);
    $b = @mysql_query("SELECT hasfreeleech FROM usergroups WHERE id = $a[class]");
    $b = @mysql_fetch_assoc($b);
    $hasfreeleech = $b['hasfreeleech'];


    $resp .= "d" . benc_str("ip") . benc_str($row["ip"]) . benc_str("peer id") .
        benc_str($row["peer_id"]) . benc_str("port") . "i" . $row["port"] . "e" . "e";
}

$resp .= "ee";
$selfwhere = "torrent = $torrentid AND " . hash_where("peer_id", $peer_id);

if (!isset($self)) {
    $res = mysql_query("SELECT $fields FROM peers WHERE $selfwhere");
    $row = mysql_fetch_assoc($res);
    if ($row) {
        $userid = $row["userid"];
        $self = $row;
    }
}

if (isset($headers["Cookie"]) || isset($headers["Accept-Language"]) || isset($headers["Accept-Charset"]))
    err("Anti-Cheater= You cannot use this agent");


$announce_wait = 10;
if (isset($self) && ($self['prevts'] > ($self['nowts'] - $announce_wait)))
    err('There is a minimum announce time of ' . $announce_wait . ' seconds');

if (!isset($self)) {
    $valid = @mysql_fetch_row(@mysql_query("SELECT COUNT(*) FROM peers WHERE torrent=$torrentid AND passkey=" .
        sqlesc($passkey)));
    if ($valid[0] >= 1 && $seeder == 'no')
        err("Connection limit exceeded! You may only leech from one location at a time.");
    if ($valid[0] >= 3 && $seeder == 'yes')
        err("Connection limit exceeded!");

    $rz = mysql_query("SELECT id, uploaded, downloaded, class, parked FROM users WHERE passkey=" .
        sqlesc($passkey) . " AND enabled = 'yes' ORDER BY last_access DESC LIMIT 1") or
        err("Tracker error 2");
    if ($MEMBERSONLY == "yes" && mysql_num_rows($rz) == 0)
        err("Unknown passkey. Please redownload the torrent from $BASEURL. a");
    $az = mysql_fetch_assoc($rz);
    $userid = 0 + $az["id"];
    if ($az["class"] < UC_VIP) {
        if ($waitsystem == "yes") {
            $gigs = $az["uploaded"] / (1024 * 1024 * 1024);
            $elapsed = floor((gmtime() - $torrent["ts"]) / 3600);
            $ratio = (($az["downloaded"] > 0) ? ($az["uploaded"] / $az["downloaded"]) : 1);
            if ($ratio < 0.5 || $gigs < 5)
                $wait = 48;
            elseif ($ratio < 0.65 || $gigs < 6.5)
                $wait = 24;
            elseif ($ratio < 0.8 || $gigs < 8)
                $wait = 12;
            elseif ($ratio < 0.95 || $gigs < 9.5)
                $wait = 6;
            else
                $wait = 0;
            if ($elapsed < $wait)
                err("Not authorized (" . ($wait - $elapsed) . "h) - READ THE FAQ!");
        }
        if ($maxdlsystem == "yes") {
            if ($ratio < 0.5 || $gigs < 5)
                $max = 1;
            elseif ($ratio < 0.65 || $gigs < 6.5)
                $max = 2;
            elseif ($ratio < 0.8 || $gigs < 8)
                $max = 3;
            elseif ($ratio < 0.95 || $gigs < 9.5)
                $max = 4;
            else
                $max = 0;
            if ($max > 0) {
                $res = mysql_query("SELECT COUNT(*) AS num FROM peers WHERE userid='$userid' AND seeder='no'") or
                    err("Tracker error 5");
                $row = mysql_fetch_assoc($res);
                if ($row['num'] >= $max)
                    err("Not authorized (You are downloading your maximum number of allowed torrents - $max)");
            }
        }
    }
} else {
    $upthis = max(0, $uploaded - $self["uploaded"]);
    if ($hasfreeleech == 'no')
        $downthis = max(0, $downloaded - $self["downloaded"]);
    else
        $downthis = 0;
    if ($torrent['free'] == 'yes')
        $downthis = 0;
    if ($torrent['doubleupload'] == 'yes')
        $upthis *= 2;
    if ($upthis > 0 || $downthis > 0)
        mysql_query("UPDATE users SET uploaded = uploaded + $upthis, downloaded = downloaded + $downthis WHERE id=$userid") or
            err("Tracker error 3");
}

$uagent = $_SERVER['HTTP_USER_AGENT'];
$bua = mysql_query("SELECT agent FROM banned_agent") or err('Tracker error (1)');
while ($nea = mysql_fetch_array($bua)) {
    $n = $nea['agent'];
    $nr = preg_replace("/\//", "\/", $n);
    $neadle = "/\b$nr\b/i";
    if (preg_match($neadle, $uagent))
        err("Banned Client, Please goto $BASEURL for a list of acceptable clients");
}
if (ereg("^BitTorrent\/S-", $agent))
    err("Shadow's Experimental Client is Banned. Please use uTorrent.");
if (ereg("^ABC\/ABC", $agent))
    err("ABC is Banned. Please use uTorrent.");
if (ereg("^Python-urllib\/2.4", $agent))
    err("Banned Client. Please use uTorrent.");

#getoutofmysite($peer_id);
$dt = gmtime() - 180;
$dt = sqlesc(get_date_time($dt));

$updateset = array();

if ($event == "stopped") {
    if (isset($self)) {
        mysql_query("UPDATE snatched SET seeder = 'no', connectable='no' WHERE torrent = $torrentid AND userid = $userid");
        mysql_query("DELETE FROM peers WHERE $selfwhere");
        if (mysql_affected_rows()) {
            if ($self["seeder"] == "yes")
                $updateset[] = "seeders = seeders - 1";
            else
                $updateset[] = "leechers = leechers - 1";
        }
    }
} else {
    if ($event == "completed") {
        mysql_query("UPDATE torrent_hit SET  completed  = 'yes' WHERE id = $torrentid AND uid = $userid");
        mysql_query("UPDATE snatched SET  finished  = 'yes', completedat = $dt WHERE torrent = $torrentid AND userid = $userid");
        $updateset[] = "times_completed = times_completed + 1";

    }

    if (isset($self)) {
        $res = mysql_query("SELECT uploaded, downloaded FROM snatched WHERE torrent = $torrentid AND userid = $userid");
        $row = mysql_fetch_array($res);
        $sockres = @pfsockopen($ip, $port, $errno, $errstr, 5);
        if (!$sockres)
            $connectable = "yes";
        else {
            $connectable = "yes";
            @fclose($sockres);
        }
        $downloaded2 = $downloaded - $self["downloaded"];
        $uploaded2 = $uploaded - $self["uploaded"];
        mysql_query("UPDATE snatched SET uploaded = uploaded+$uploaded2, downloaded = downloaded+$downloaded2, port = $port, connectable = '$connectable', agent= " .
            sqlesc($agent) . ", to_go = $left, last_action = $dt, seeder = '$seeder' WHERE torrent = $torrentid AND userid = $userid");
        $prev_action = sqlesc($self['last_action']);

        mysql_query("UPDATE peers SET uploaded = $uploaded, downloaded = $downloaded, to_go = $left, last_action = NOW(), prev_action = $prev_action, seeder = '$seeder'" .
            ($seeder == "yes" && $self["seeder"] != $seeder ? ", finishedat = " . time() :
            "") . " WHERE $selfwhere");
        if (mysql_affected_rows() && $self["seeder"] != $seeder) {
            if ($seeder == "yes") {
                $updateset[] = "seeders = seeders + 1";
                $updateset[] = "leechers = leechers - 1";
            } else {
                $updateset[] = "seeders = seeders - 1";
                $updateset[] = "leechers = leechers + 1";
            }
        }
    } else {
        if ($az["parked"] == "yes")
            err("Error, your account is parked!");

      #  if (portblacklisted($port))
       #     err("Port $port is blacklisted.");
        else {
            $sockres = @pfsockopen($ip, $port, $errno, $errstr, 5);
            if (!$sockres) {
                $connectable = "yes";
                if ($nc == "yes")
                    err("ERROR - Your client are not connectable! Check your Port-configuration or ask an administartor or search on forums.");
            } else {
                $connectable = "yes";
                @fclose($sockres);
            }
        }

        $res = mysql_query("SELECT torrent, userid FROM snatched WHERE torrent = $torrentid AND userid = $userid");
        $check = mysql_fetch_assoc($res);
        if (!$check)
            mysql_query("INSERT INTO snatched (torrent, torrentid, userid, port, startdat, last_action, agent, torrent_name, torrent_category) VALUES ($torrentid, $torrentid, $userid, $port, $dt, $dt, " .
                sqlesc($agent) . ", " . sqlesc($torrentname) . ", $torrentcategory)");
        $ret = mysql_query("INSERT INTO peers (connectable, torrent, peer_id, ip, port, uploaded, downloaded, to_go, started, last_action, seeder, userid, agent, uploadoffset, downloadoffset, passkey) VALUES ('$connectable', $torrentid, " .
            sqlesc($peer_id) . ", " . sqlesc($ip) . ", $port, $uploaded, $downloaded, $left, NOW(), NOW(), '$seeder', $userid, " .
            sqlesc($agent) . ", $uploaded, $downloaded, " . sqlesc($passkey) . ")");
        if ($ret) {
            if ($seeder == "yes")
                $updateset[] = "seeders = seeders + 1";
            else
                $updateset[] = "leechers = leechers + 1";
        }
    }
}

if ($seeder == "yes") {
    if ($torrent["banned"] != "yes")
        $updateset[] = "visible = 'yes'";
    $updateset[] = "last_action = NOW()";
}

if (count($updateset))
    mysql_query("UPDATE torrents SET " . join(",", $updateset) . " WHERE id = $torrentid");

$select_client = mysql_query("SELECT * FROM clientselect WHERE name='" . $agent .
    "'");
if (mysql_num_rows($select_client) == 0) {
    $insert = @mysql_query("INSERT INTO clientselect (name) VALUES ('" . $agent .
        "')");
    $client_id = mysql_insert_id();
} else {
    $client_row = @mysql_fetch_array($select_client);
    $client_id = $client_row['id'];
}

$agent_user_add = @mysql_query("UPDATE users SET clientselect='" . $client_id .
    "' where id='" . $userid . "'");
benc_resp_raw($resp);

if ($uploaded > 0 || $downloaded > 0) {
    $upthis = max(0, $uploaded - @$self["uploaded"]);
    $downthis = max(0, $downloaded - @$self["downloaded"]);
    mysql_query("UPDATE anti_cheat SET uploaded = uploaded + $upthis, downloaded = downloaded + $downthis WHERE user_id = $userid AND torrent_id = $torrentid");
    if (mysql_affected_rows() == 0) {
        mysql_query("INSERT INTO anti_cheat (user_id, torrent_id, uploaded, downloaded ) VALUES ( $userid, $torrentid, $upthis, $downthis )");
    }
}
if(!function_exists("getallheaders")) {
	function emu_getallheaders() {
   foreach($_SERVER as $name => $value)
       if(substr($name, 0, 5) == 'HTTP_')
           $headers[substr($name, 5)] = $value;
   return $headers;
}
}
?>