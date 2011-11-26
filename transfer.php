<?php
require "include/bittorrent.php" ;
lang::load('transfer');
dbconn( true ) ;
$can = get('transfer_enable');
$ugs = explode(',',get('transfer_usergroups'));
if ( ($can == 'no') || ($can == 'op' and !ur::ismod()) OR ($can == 'ug' and !in_array($CURUSER['class'],$ugs)))
    {ug();}
stdhead( lang1 ) ;
loggedinorreturn() ;

$act = htmlspecialchars( $_GET[act] ) ;




switch ( $act )
{

        //process and results page

    case history:
        if ( ! ur::ismod() )
        {
            echo lang3 ;
            continue ;
        }
        

        print ( "<p>\n" ) ;
        print ( "</p>\n" ) ;

        $page = 0 + $_GET['page'] ;
        $perpage = 100 ;

        $myidd = sqlesc( 0 + $CURUSER["id"]); //==== at least this

//=== better would be this:
$myidd =  0 + $CURUSER["id"];
if (!is_valid_id($myidd))
stderr("Error", "Bad ID!");
int_check(array($myidd));
        $res = mysql_query( "SELECT COUNT(*) FROM transferlog WHERE toid = $myidd OR fromid = $myidd " ) or
            sqlerr() ;
        $arr = mysql_fetch_row( $res ) ;
        $pages = floor( $arr[0] / $perpage ) ;
        if ( $pages * $perpage < $arr[0] ) ++$pages ;

        if ( $page < 1 ) $page = 1 ;
        else
            if ( $page > $pages ) $page = $pages ;

        for ( $i = 1; $i <= $pages; ++$i )
            if ( $i == $page ) $pagemenu .= "<b>$i</b>\n" ;
            else  $pagemenu .= "<a href=?$q&page=$i><b>$i</b></a>\n" ;

        if ( $page == 1 ) $browsemenu .= "<b><< ".lang5."</b>" ;
        else  $browsemenu .= "<a href=?$q&page=" . ( $page - 1 ) . "><b><< ".lang5."</b></a>" ;

        $browsemenu .= " " ;

        if ( $page == $pages ) $browsemenu .= "<b>".lang6." >></b>" ;
        else  $browsemenu .= "<a href=?$q&page=" . ( $page + 1 ) . "><b>".lang6." >></b></a>" ;


        $offset = ( $page * $perpage ) - $perpage ;

        $res = mysql_query( "SELECT * FROM transferlog WHERE toid = $myidd OR fromid = $myidd ORDER BY added DESC LIMIT $offset,$perpage" ) or
            sqlerr() ;
        $num = mysql_num_rows( $res ) ;
collapses("hist-transfer",lang26);
        print ( "<table border=1 cellspacing=0 cellpadding=5 width=100%>\n" ) ;
        print ( "<tr><td class=colhead align=left>".lang7."</td><td class=colhead>".lang8."</td><td class=colhead>".lang9."</td><td class=colhead align=left>".lang10."</td><td class=colhead>".lang11."</td></tr>\n" ) ;
        for ( $i = 0; $i < $num; ++$i )
        {
            $arr = mysql_fetch_assoc( $res ) ;
            $country = "<td align=center>---</td>" ;
            if ( $arr['added'] == '0000-00-00 00:00:00' ) $arr['added'] = '-' ;

            print ( "<tr><td align=left>" . getidfromusername($arr["toid"]) . "</td><td>" .
                getidfromusername($arr["fromid"]) . "</td><td>" . $arr["amountmb"] .
                "</td><td align=left>" . $arr["added"] . "</td><td align=left>" . $arr["comment"] .
                "</td></tr>\n" ) ;
        }
        print ( "</table>\n" ) ;
collapsee();

        print ( "<p align=center>$pagemenu<br>$browsemenu</p>" ) ;
echo "<p align=center><a href=transfer.php>".lang4."</a></p>" ;

        break ;

    case process:
        //check information
        $tusername = $_POST["tusername"] ;
        $tamount = $_POST["tamount"] ;
        $tsubmit = $_POST["tsubmit"] ;
        $tcomment = $_POST["tcomment"] ;
        $tamvalid = is_numeric( $tamount ) ;
        if ( ! $tamvalid )
        {
            stdmsg( lang12, lang13 ) ;
            exit ;
        }
        $tamounti = $tamount * 1024 * 1024 ;
        if ( $tamounti <= 0 )
        {
            stdmsg( lang12, lang14 ) ;
            exit ;
        }
        if ( $tsubmit == "" or $tusername == "" or $tamount == "" )
        {
            stdmsg( lang12, lang15 ) ;
            exit ;
        }

        $res = mysql_query( "SELECT username FROM users WHERE username = " . sqlesc($tusername) .
            " AND enabled = 'yes'" ) ;
        $row = mysql_fetch_array( $res ) ;
        if ( ! $row[0] )
        {
            stdmsg( lang12, sprintf(lang16,$tusername) ) ;
            exit ;
        }
        //check if transfer ratio is less than or equal to curent ratio
        if ( $CURUSER["uploaded"] < $tamounti )
        {
            stdmsg( lang12, lang17 ) ;
            exit ;
        }
        // transfer
        $myid = $CURUSER["id"] ;
        mysql_query( "UPDATE users SET uploaded = uploaded - " . sqlesc($tamounti) .
            " WHERE id = " . sqlesc($myid) ) or sqlerr( __file__, __line__ ) ;
        mysql_query( "UPDATE users SET uploaded = uploaded + " . sqlesc($tamounti) .
            " WHERE username = " . sqlesc($tusername) ) or sqlerr( __file__, __line__ ) ;

        //get target userid
        $rquery = mysql_query("SELECT id FROM users WHERE username = ".sqlesc($tusername)."") or sqlerr(__FILE__, __LINE__);
        $rres = mysql_fetch_assoc( $rquery ) ;
        $tusernameid = $rres["id"] ;

        // write to transfer log
        mysql_query( "INSERT INTO transferlog (added,fromid,toid,amountmb,comment) VALUES (NOW(),'" .
            $CURUSER["id"] . "','" . $tusernameid . "','" . $tamount . "','" . $tcomment .
            "')" ) or sqlerr( __file__, __line__ ) ;
        //send pm to rec


        $msg = sprintf(lang18,$CURUSER["username"],$tamount,$tcomment);
        mysql_query( "INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, '" .
            $tusernameid . "', NOW(), '$msg', 0)" ) or sqlerr( __file__, __line__ ) ;

        //write complete message
        #stdmsg( "Success", "You have ransferred $tamount megabytes of ratio to $tusername." ) ;
        redirect('transfer.php',sprintf(lang19,$tamount,$tusername));

        break ;

        //form
    default:
    collapses('transfer-transfer',lang20);
        echo "<form action=transfer.php?act=process method=post>" ;
        echo '<table border="0" cellspacing="0" cellpadding="5" width=100% style=\'border:none;\'>' ;
        #echo "<h1>".lang20."</h1>" ;
        echo "<tr style='border:none;'><td style='border:none;'>".lang21."</td><td style='border:none;'><input type=text size=50 name=tamount> ".lang22."</td></tr>" ;
        echo "<tr style='border:none;'><td style='border:none;'>".lang23."</td><td style='border:none;'><input type=text size=50 name=tusername></td></tr>" ;
        echo "<tr style='border:none;'><td style='border:none;'>".lang24."</td><td style='border:none;'><input type=text size=50 maxlength=185 name=tcomment></td></tr>" ;
        echo "<tr style='border:none;'><td colspan=2 align=center style='border:none;'><input class=btn type=submit name=tsubmit value=".lang25."></td></tr>" ;
        echo "</form>" ;
        echo "</table>" ;
        collapsee();
        if ( ur::ismod() ) echo
                "</br><p align=center><a href=transfer.php?act=history>".lang26."</a></p>" ;
        break ;

}

echo "</br></br>" ;
stdfoot() ;
?>