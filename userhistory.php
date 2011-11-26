<?php
ob_start( "ob_gzhandler" ) ;
require "include/bittorrent.php" ;
lang::load( 'uhist' ) ;
loggedinorreturn() ;

parked() ;
$userid = $_GET["id"] ;
int_check( $userid, true ) ;

if ( get_user_class() < UC_POWER_USER || ($CURUSER["id"] != $userid &&
    get_user_class() < UC_MODERATOR) )
    stderr( str1, str2 ) ;

$action = htmlspecialchars( $_GET["action"] ) ;

//-------- Global variables

$perpage = 15 ;

//-------- Action: View posts

if ( $action == "viewposts" )
{
    $select_is = "COUNT(DISTINCT p.id)" ;

    $from_is = "posts AS p LEFT JOIN topics as t ON p.topicid = t.id LEFT JOIN forums AS f ON t.forumid = f.id" ;

    $where_is = "p.userid = $userid AND f.minclassread <= " . $CURUSER['class'] ;

    $order_is = "p.id DESC" ;

    $query = "SELECT $select_is FROM $from_is WHERE $where_is" ;

    $res = sql_query( $query ) or sqlerr( __file__, __line__ ) ;

    $arr = mysql_fetch_row( $res ) or stderr( str1, str3 ) ;

    $postcount = $arr[0] ;

    //------ Make page menu

    list( $pagertop, $pagerbottom, $limit ) = pager( $perpage, $postcount, $_SERVER["PHP_SELF"] .
        "?action=viewposts&id=$userid&" ) ;

    //------ Get user data

    $res = sql_query( "SELECT username, donor, warned, enabled FROM users WHERE id=$userid" ) or
        sqlerr( __file__, __line__ ) ;

    if ( mysql_num_rows($res) == 1 )
    {
        $arr = mysql_fetch_assoc( $res ) ;

        $subject = "<a href=userdetails.php?id=$userid><b><font color=black>$arr[username]</font></b></a>" .
            get_user_icons( $arr, true ) ;
    }
    else
        $subject = "unknown[$userid]" ;

    //------ Get posts

    $from_is = "posts AS p LEFT JOIN topics as t ON p.topicid = t.id LEFT JOIN forums AS f ON t.forumid = f.id LEFT JOIN readposts as r ON p.topicid = r.topicid AND p.userid = r.userid" ;

    $select_is = "f.id AS f_id, f.name, t.id AS t_id, t.subject, t.lastpost, r.lastpostread, p.*" ;

    $query = "SELECT $select_is FROM $from_is WHERE $where_is ORDER BY $order_is $limit" ;

    $res = sql_query( $query ) or sqlerr( __file__, __line__ ) ;

    if ( mysql_num_rows($res) == 0 )
        stderr( str1, str4 ) ;

    stdhead( str5 ) ;

    print ( "<h1>" . str6 . " $subject</h1>\n" ) ;

    if ( $postcount > $perpage )
        echo $pagertop ;

    //------ Print table

    begin_main_frame( '100%' ) ;

    begin_frame( '', 0, '10', '100%' ) ;

    while ( $arr = mysql_fetch_assoc($res) )
    {
        $postid = $arr["id"] ;

        $posterid = $arr["userid"] ;

        $topicid = $arr["t_id"] ;

        $topicname = $arr["subject"] ;

        $forumid = $arr["f_id"] ;

        $forumname = $arr["name"] ;

        $newposts = ( $arr["lastpostread"] < $arr["lastpost"] ) && $CURUSER["id"] == $userid ;

        $added = $arr["added"] . " GMT (" . ( get_elapsed_time(sql_timestamp_to_unix_timestamp
            ($arr["added"])) ) . " ago)" ;

        print ( "<p class=sub><table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>
	    $added&nbsp;--&nbsp;<b>" . str7 . ":&nbsp;</b>
	    <a href=$BASEURL/forums/viewforum.php?forumid=$forumid>$forumname</a>
	    &nbsp;--&nbsp;<b>" . str8 . ":&nbsp;</b>
	    <a href=$BASEURL/forums/viewtopic.php?topicid=$topicid>$topicname</a>
      &nbsp;--&nbsp;<b>" . str9 . ":&nbsp;</b>
      #<a href=$BASEURL/forums/viewtopic.php?topicid=$topicid&page=p$postid#$postid>$postid</a>" .
            ($newposts ? " &nbsp;<b>(<font color=red>" . str10 . "</font>)</b>" : "") .
            "</td></tr></table></p>\n" ) ;

        begin_table( true ) ;

        $body = format_comment( $arr["body"] ) ;

        if ( is_valid_id($arr['editedby']) )
        {
            $subres = sql_query( "SELECT username FROM users WHERE id=$arr[editedby]" ) ;
            if ( mysql_num_rows($subres) == 1 )
            {
                $subrow = mysql_fetch_assoc( $subres ) ;
                $body .= "<p><font size=1 class=small>" . str11 . " <a href=userdetails.php?id=$arr[editedby]><b>$subrow[username]</b></a> " .
                    str12 . " $arr[editedat] GMT</font></p>\n" ;
            }
        }

        print ( "<tr valign=top><td class=comment>$body</td></tr>\n" ) ;

        end_table() ;
    }

    end_frame() ;

    end_main_frame() ;

    if ( $postcount > $perpage )
        echo $pagerbottom ;

    stdfoot() ;

    die ;
}

//-------- Action: View comments

if ( $action == "viewcomments" )
{
    $select_is = "COUNT(*)" ;

    // LEFT due to orphan comments
    $from_is = "comments AS c LEFT JOIN torrents as t
	            ON c.torrent = t.id" ;

    $where_is = "c.user = $userid" ;
    $order_is = "c.id DESC" ;

    $query = "SELECT $select_is FROM $from_is WHERE $where_is ORDER BY $order_is" ;

    $res = sql_query( $query ) or sqlerr( __file__, __line__ ) ;

    $arr = mysql_fetch_row( $res ) or stderr( str1, str13 ) ;

    $commentcount = $arr[0] ;

    //------ Make page menu

    list( $pagertop, $pagerbottom, $limit ) = pager( $perpage, $commentcount, $_SERVER["PHP_SELF"] .
        "?action=viewcomments&id=$userid&" ) ;

    //------ Get user data

    $res = sql_query( "SELECT username, donor, warned, enabled FROM users WHERE id=$userid" ) or
        sqlerr( __file__, __line__ ) ;

    if ( mysql_num_rows($res) == 1 )
    {
        $arr = mysql_fetch_assoc( $res ) ;

        $subject = "<a href=userdetails.php?id=$userid><b><font color=black>$arr[username]</font></b></a>" .
            get_user_icons( $arr, true ) ;
    }
    else
        $subject = "unknown[$userid]" ;

    //------ Get comments

    $select_is = "t.name, c.torrent AS t_id, c.id, c.added, c.text" ;

    $query = "SELECT $select_is FROM $from_is WHERE $where_is ORDER BY $order_is $limit" ;

    $res = sql_query( $query ) or sqlerr( __file__, __line__ ) ;

    if ( mysql_num_rows($res) == 0 )
        stderr( str1, str13 ) ;

    stdhead( str14 ) ;

    print ( "<h1>".str15." $subject</h1>\n" ) ;

    if ( $commentcount > $perpage )
        echo $pagertop ;

    //------ Print table

    begin_main_frame( '100%' ) ;

    begin_frame( '', 0, '10', '100%' ) ;

    while ( $arr = mysql_fetch_assoc($res) )
    {

        $commentid = $arr["id"] ;

        $torrent = $arr["name"] ;

        // make sure the line doesn't wrap
        if ( strlen($torrent) > 55 )
            $torrent = substr( $torrent, 0, 52 ) . "..." ;

        $torrentid = $arr["t_id"] ;

        //find the page; this code should probably be in details.php instead

        $subres = sql_query( "SELECT COUNT(*) FROM comments WHERE torrent = $torrentid AND id < $commentid" ) or
            sqlerr( __file__, __line__ ) ;
        $subrow = mysql_fetch_row( $subres ) ;
        $count = $subrow[0] ;
        $comm_page = floor( $count / 20 ) ;
        $page_url = $comm_page ? "&page=$comm_page" : "" ;

        $added = $arr["added"] . " GMT (" . ( get_elapsed_time(sql_timestamp_to_unix_timestamp
            ($arr["added"])) ) . " ago)" ;

        print ( "<p class=sub><table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>" .
            "$added&nbsp;---&nbsp;<b>".str16.":&nbsp;</b>" . ($torrent ? ("<a href=details.php?id=$torrentid&tocomm=1&hit=1>$torrent</a>") :
            " [Deleted] ") . "&nbsp;---&nbsp;<b>".str17.":&nbsp;</b>#<a href=details.php?id=$torrentid&tocomm=1&hit=1$page_url>$commentid</a>
	  </td></tr></table></p>\n" ) ;

        begin_table( true ) ;

        $body = format_comment( $arr["text"] ) ;

        print ( "<tr valign=top><td class=comment>$body</td></tr>\n" ) ;

        end_table() ;
    }

    end_frame() ;

    end_main_frame() ;

    if ( $commentcount > $perpage )
        echo $pagerbottom ;

    stdfoot() ;

    die ;
}

//-------- Handle unknown action

if ( $action != "" )
    stderr( str18, str19 ) ;

//-------- Any other case

stderr( str18, str20 ) ;

?>