<?php
ob_start();
$root = ".";
$rootpath = './';
require_once("$root/include/bittorrent.php");

loggedinorreturn();
$userid=$CURUSER[id];

require ("include/imdb/imdb.class.php");
require_once("include/imdb/imdbsearch.class.php");

$search = new imdbsearch ();
$search->setsearchname ($HTTP_GET_VARS["name"]);
echo "<HTML><HEAD><TITLE>Step 2</TITLE></HEAD><BODY>";

stdhead("Upload");

echo"<table><tr><td align=center class=colhead><b>Film or Video Torrent Upload</b></td></tr>".
"<tr><td valign=top align=center><br><b><font size=\"+1\">Step 2:</font></b>".
"<p align=center>Please click on the correct movie title from the list below:<br>".
"if you are not sure click the imdb link to check. it will open in a new window.</p>";

$results = $search->results ();
foreach ($results as $res) {

echo "<a class=altlink href=imdb.php?mid=";
echo $res->imdbid();
echo ">";
echo $res->title();
echo "(".$res->year().")";
echo "</a> ";
echo " [ <a href=\"http://us.imdb.com/title/tt";
echo $res->imdbid();
echo "\" target=_blank>imdb page</a> ]";
echo "<br>\n";
}
echo "<br><p align=center>if you do not see the movie in the list above click ".
"[ <a class=altlink href=$BASEURL/imdb/imdb.php?mid=>HERE</a> ] to skip this step.</p>".
"<br></td></tr></table>";

stdfoot();
?>