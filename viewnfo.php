<?php
require "include/bittorrent.php";
lang::load('viewnfo');

loggedinorreturn();

parked();
$id = $_GET["id"];
if (get_user_class() < UC_POWER_USER || !is_valid_id($id))
die;

$r = sql_query("SELECT name,nfo FROM torrents WHERE id=$id") or sqlerr();
$a = mysql_fetch_assoc($r) or die("Puke");

//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

// view might be one of: "magic", "latin-1", "strict" or "fonthack"
$view = "";
if (isset($_GET["view"])) {
$view = unesc($_GET["view"]);
}
else {
$view = "magic"; // default behavior
}

$nfo = "";
if ($view == "latin-1" || $view == "fonthack") {
// Do not convert from ibm-437, read bytes as is.
// NOTICE: TBSource specifies Latin-1 encoding in include/bittorrent.php:
// stdhead()
$nfo = htmlentities(($a["nfo"]));
}
else {
// Convert from ibm-437 to html unicode entities.
// take special care of Swedish letters if in magic view.
$nfo = code($a["nfo"], $view == "magic");
}

stdhead();
print("<h1>".sprintf(str1,"<a href=details.php?id=$id>".htmlentities($a["name"])."</a>")."</h1>\n");

?>
<table border="1" cellspacing="0" cellpadding="10" align="center" width="100%">
<tr>
<td align="center" width="50%">
<a href="viewnfo.php?id=<?=$id?>&view=magic">
<b><?=str2?></b></a></td>
<td align="center" width="50%">
<a href="viewnfo.php?id=<?=$id?>&view=latin-1"><b><?=str3?></b></a></td>
</tr>
<tr>
<td colspan="3">
<table border=1 cellspacing=0 cellpadding=5><tr><td class=text>
<?php
// -- About to output NFO data
if ($view == "fonthack") {
// Please notice: MS LineDraw's glyphs are included in the Courier New font
// as of Courier New version 2.0, but uses the correct mappings instead.
// http://support.microsoft.com/kb/q179422/
print("<pre style=\"font-size:10pt; font-family: 'MS LineDraw', 'Terminal', monospace;\">");
}
else {
// IE6.0 need to know which font to use, Mozilla can figure it out in its own
// (windows firefox at least)
// Anything else than 'Courier New' looks pretty broken.
// 'Lucida Console', 'FixedSys'
print("<pre style=\"font-size:10pt; font-family: 'Courier New', monospace;\">");
}
// Writes the (eventually modified) nfo data to output, first formating urls.
print(format_urls($nfo));
print("</pre>\n");
?>
</td></tr></table>
</td>
</tr>
</table>
<?php
//error_reporting(0);
stdfoot();
?>