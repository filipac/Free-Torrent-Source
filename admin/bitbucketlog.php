<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;
lang::load( 'bitbucketlog' ) ;

ADMIN::check();
$bucketpath = $bitbucket ;
if ( get_user_class() >= UC_MODERATOR )
{
    $delete = $HTTP_GET_VARS["delete"] ;
    if ( is_valid_id($delete) )
    {
        $r = sql_query( "SELECT name,owner FROM bitbucket WHERE id=" .
            mysql_real_escape_string($delete) ) or sqlerr( __file__, __line__ ) ;
        if ( mysql_num_rows($r) == 1 )
        {
            $a = mysql_fetch_assoc( $r ) ;
            if ( get_user_class() >= UC_MODERATOR || $a["owner"] == $CURUSER["id"] )
            {
                sql_query( "DELETE FROM bitbucket WHERE id=" . mysql_real_escape_string($delete) ) or
                    sqlerr( __file__, __line__ ) ;
                    sql_query("UPDATE users SET avatar = \"$BASEURL/pic/default_avatar.gif\" WHERE avatar = \"$BASEURL/$bucketpath/$a[name]\"");
                if ( ! unlink("../$bucketpath/$a[name]") )
                    stderr( str3, sprintf(str4, $a[name]), false ) ;
            }
        }
    }
}
stdhead( str5 ) ;
$res = sql_query( "SELECT count(*) FROM bitbucket" ) or die( mysql_error() ) ;
$row = mysql_fetch_array( $res ) ;
$count = $row[0] ;
$perpage = 10 ;
list( $pagertop, $pagerbottom, $limit ) = pager( $perpage, $count, $_SERVER["PHP_SELF"] .
    "?out=" . $_GET["out"] . "&" ) ;
print ( "<h1>" . str5 . "</h1>\n" ) ;
print ( sprintf(str6, $count) ) ;
echo $pagertop ;
$res = sql_query( "SELECT * FROM bitbucket ORDER BY added DESC $limit" ) or
    sqlerr( __file__, __line__ ) ;
if ( mysql_num_rows($res) == 0 )
    print ( "<b>" . str7 . "</b>\n" ) ;
else
{
    print ( "<table align='center' border='0' cellspacing='0' cellpadding='5'>\n" ) ;
    while ( $arr = mysql_fetch_assoc($res) )
    {
        $r2 = sql_query( "SELECT username FROM users WHERE id=$arr[owner]" ) or sqlerr() ;
        $a2 = mysql_fetch_assoc( $r2 ) ;
        $date = substr( $arr['added'], 0, strpos($arr['added'], " ") ) ;
        $time = substr( $arr['added'], strpos($arr['added'], " ") + 1 ) ;
        $name = $arr["name"] ;
        list( $width, $height, $type, $attr ) = getimagesize( "$BASEURL/$bitbucket/$name" ) ;
        $url = str_replace( " ", "%20", htmlspecialchars("$BASEURL/$bitbucket/$name") ) ;
        print ( "<tr>" ) ;
        print ( "<td><center><a href=$url><img src=$url border=0 onLoad='SetSize(this, 400)'></a></center>" ) ;
        print ( str8 . "  <b><a href=userdetails.php?id=$arr[owner]>$a2[username]</a></b><br>" ) ;
        print ( "(#$arr[id]) " . str9 . ": $name ($width&nbsp;x&nbsp;$height)" ) ;
        if ( get_user_class() >= UC_MODERATOR )
            print ( " <b><a href=?delete=$arr[id]>" . str10 . "</a></b><br>" ) ;
        print ( str11 . " $date $time" ) ;
        print ( "</tr>" ) ;
    }
    print ( "</table>" ) ;
}
echo $pagerbottom ;
stdfoot() ;
?>