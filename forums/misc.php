<?php
/**
 * @package Free Forums
 * @author Filip Pacurar
 * @module Misc tools  
 **/
# Include backend ==>
$starttime = explode(' ', microtime());
$starttime = $starttime[1] + $starttime[0];
$rootpath = '../' ;
require_once ( $rootpath . "forums/functions/fts.php" ) ;
require_once ( $rootpath . "include/bittorrent.php" ) ;
lang::load("forums_misc");
loggedinorreturn() ;

iplogger() ;
parked() ;
# Start script
$action = isset( $_POST['action'] ) ? $_POST['action'] : ( isset($_GET['action']) ? $_GET['action'] : '' ) ;
if ( $action == 'print' )
{
    $topicid = 0 + $_GET["topicid"] ;
    int_check( $topicid, true ) ;
    $res = sql_query( "SELECT * FROM topics WHERE id=$topicid" ) or sqlerr( __file__,
        __line__ ) ;
    $arr = mysql_fetch_assoc( $res ) or stderr( str1, str2 ) ;
    $forumid = $arr["forumid"] ;
    $res = sql_query( "SELECT * FROM forums WHERE id=$forumid" ) or sqlerr( __file__,
        __line__ ) ;

    $arr = mysql_fetch_assoc( $res ) or die( str3 ) ;
    $vers = VERSION ;
    $year = date( "Y" ) ;
    $copy = str4.' '.base64("RnJlZSBUb3JyZW50IFNvdXJjZSB2LjEuMC4xICZjb3B5OyBGVFMgMjAwOA",0);
$lang[0] = str5;
$lang[1] = str6;
$lang[2] = str7;
    echo <<< A
    	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" />
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-9" />
	<style type="text/css">
	<!--
	td, p, li, div
	{
		font: 10pt verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif;
	}
	.smalltext
	{
		font-size: 11px;
	}
	-->
	</style>
	<title>$copy</title>		
	</head>
	<body>

	<br />
	<table border="0" cellspacing="6" cellpadding="5" width=100%>
	<tr>
		<td>
		<script type="text/javascript">
			var action_message = "$lang[0]";
			document.write("<span style=\"float: right\"><form><input type=\"button\" "+"value=\""+action_message+"\" onClick=\"window.print()\" class=\"smalltext\"></form></span>");
		</script>
		<b>$lang[1]: <a href="$BASEURL/forums/viewtopic.php?topicid=$topicid">$BASEURL/forums/viewtopic.php?topicid=$topicid</a></b>
		<hr></td>

	</tr>
A;
    $res = sql_query( "SELECT * FROM posts WHERE topicid=$topicid ORDER BY id" ) or
        sqlerr( __file__, __line__ ) ;
    while ( $arr = mysql_fetch_assoc($res) ):
$added = sql_timestamp_to_unix_timestamp($arr["added"]);

$added = date("d-m-Y h:i A",$added);
      $added = $added;
        $postid = $arr["id"] ;

        $posterid = $arr["userid"] ;
        $res2 = sql_query( "SELECT username,class,avatar,donor,donated,title,enabled,warned,uploaded,downloaded,signature,last_access FROM users WHERE id=$posterid" ) or
            sqlerr( __file__, __line__ ) ;

        $arr2 = mysql_fetch_assoc( $res2 ) ;
        $body = format_comment($arr["body"],0);
        echo <<< PRINT
	
		<tr>
			<td>
				<span class="smalltext"><strong>$lang[2] $arr2[username] - $added</strong></span><hr />
			</td>
		</tr>
		<tr>
			<td>

				<span style="font-size: medium;">$body</span> <br />
</td>

		</tr>
PRINT;
    endwhile;
    
    echo "</table>
	</body>
	</html>" ;
	$mtime = explode(' ', microtime());
$totaltime = $mtime[0] + $mtime[1] - $starttime;
printf('<center>'.str8.' %.3f seconds.</center>', $totaltime);
}
else
{
    die ;
}
?>