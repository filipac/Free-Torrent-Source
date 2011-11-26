<?php
ob_start() ;
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;
lang::load( 'category' ) ;
ADMIN::check();
mysql_connect( $mysql_host, $mysql_user, $mysql_pass ) ;
mysql_select_db( $mysql_db ) ;
stdhead( str2 ) ;
print ( "<h1>" . str3 . "</h1>\n" ) ;
print ( "</br>" ) ;
print ( "<table width=70% border=1 cellspacing=0 cellpadding=2><tr><td align=center>\n" ) ;

///////////////////// D E L E T E C A T E G O R Y \\\\\\\\\\\\\\\\\\\\\\\\\\\\

$sure = $_GET['sure'] ;
if ( $sure == "yes" )
{
    $delid = $_GET['delid'] ;
    $query = "DELETE FROM categories WHERE id=" . sqlesc( $delid ) . " LIMIT 1" ;
    $sql = sql_query( $query ) ;
    echo ( str4 ) ;
    end_frame() ;
    stdfoot() ;
    die() ;
}
$delid = $_GET['delid'] ;
$name = $_GET['cat'] ;
if ( $delid > 0 )
{
    echo ( sprintf(str5, $name, "<strong><a href='" . $_SERVER['PHP_SELF'] .
        "?delid=$delid&cat=$name&sure=yes'>", "</a></strong>", "<strong><a href='" . $_SERVER['PHP_SELF'] .
        "'>", "</a></strong>") ) ;
    end_frame() ;
    stdfoot() ;
    die() ;

}

///////////////////// E D I T A C A T E G O R Y \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$edited = $_GET['edited'] ;
if ( $edited == 1 )
{
    $id = $_GET['id'] ;
    $cat_name = $_GET['cat_name'] ;
    $cat_img = $_GET['cat_img'] ;
    $query = "UPDATE categories SET
name = '$cat_name',
image = '$cat_img' WHERE id=" . sqlesc( $id ) ;
    $sql = sql_query( $query ) ;
    if ( $sql )
    {
        echo ( "<table class=main cellspacing=0 cellpadding=5 width=50%>" ) ;
        echo ( "<tr><td><div align='center'>" . str6 . "</div></tr>" ) ;
        echo ( "</table>" ) ;
        end_frame() ;
        stdfoot() ;
        die() ;
    }
}

$editid = $_GET['editid'] ;
$name = $_GET['name'] ;
$img = $_GET['img'] ;
if ( $editid > 0 )
{
    echo ( "<form name='form1' method='get' action='" . $_SERVER['PHP_SELF'] . "'>" ) ;
    echo ( "<table class=main cellspacing=0 cellpadding=5 width=50%>" ) ;
    echo ( "<div align='center'><input type='hidden' name='edited' value='1'>" .
        str7 . " <strong>\"$name\"</strong></div>" ) ;
    echo ( "<br>" ) ;
    echo ( "<input type='hidden' name='id' value='$editid'<table class=main cellspacing=0 cellpadding=5 width=50%>" ) ;
    echo ( "<tr><td>" . str8 .
        ": </td><td align='right'><input type='text' size=50 name='cat_name' value='$name'></td></tr>" ) ;
    echo ( "<tr><td>" . str9 .
        ": </td><td align='right'><input type='text' size=50 name='cat_img' value='$img'></td></tr>" ) ;
    echo ( "<tr><td></td><td><div align='right'><input type='Submit'></div></td></tr>" ) ;
    echo ( "</table></form>" ) ;
    end_frame() ;
    stdfoot() ;
    die() ;
}

///////////////////// A D D A N E W C A T E G O R Y \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$add = $_GET['add'] ;
if ( $add == 'true' )
{
    $cat_name = $_GET['cat_name'] ;
    $cat_img = $_GET['cat_img'] ;
    $query = "INSERT INTO categories SET name = " . sqlesc( $cat_name ) .
        ", image = " . sqlesc( $cat_img ) . "" ;
    $sql = sql_query( $query ) or die( mysql_error() ) ;
    if ( $sql )
    {
        $success = true ;
    }
    else
    {
        $success = false ;
    }
}
print ( "<strong>" . str10 . "</strong>" ) ;
print ( "<br />" ) ;
print ( "<br />" ) ;
echo ( "<form name='form1' method='get' action='" . $_SERVER['PHP_SELF'] . "'>" ) ;
echo ( "<table class=main cellspacing=0 cellpadding=5 width=50%>" ) ;
echo ( "<tr><td>" . str11 .
    ": </td><td align='right'><input type='text' size=50 name='cat_name'></td></tr>" ) ;
echo ( "<tr><td>" . str12 .
    ": </td><td align='right'><input type='text' size=50 name='cat_img'><input type='hidden' name='add' value='true'></td></tr>" ) ;
echo ( "<tr><td></td><td><div align='right'><input type='Submit'></div></td></tr>" ) ;
echo ( "</table>" ) ;
if ( $success == true )
{
    print ( "<strong>" . str13 . "</strong>" ) ;
}
echo ( "<br>" ) ;
echo ( "</form>" ) ;

///////////////////// E X I S T I N G C A T E G O R I E S \\\\\\\\\\\\\\\\\\\\\\\\\\\\

print ( "<strong>" . str14 . ":</strong>" ) ;
print ( "<br />" ) ;
print ( "<br />" ) ;
echo ( "<table class=main cellspacing=0 cellpadding=5>" ) ;
echo ( "<td>" . str15 . ":</td><td>" . str16 . ":</td><td>" . str17 .
    ":</td><td>" . str18 . ":</td><td>" . str19 . ":</td><td>" . str20 . ":</td>" ) ;
$query = "SELECT * FROM categories WHERE 1=1" ;
$sql = sql_query( $query ) ;
while ( $row = mysql_fetch_array($sql) )
{
    $id = $row['id'] ;
    $name = $row['name'] ;
    $img = $row['image'] ;
    echo ( "<tr><td><strong>$id</strong> </td> <td><strong>$name</strong></td> <td><img src='$BASEURL/pic/$img' border='0' /></td><td><div align='center'><a href='$BASEURL/browse.php?cat=$id'><img src='$BASEURL/pic/viewnfo.gif' border='0' class=special /></a></div></td> <td><a href='category.php?editid=$id&name=$name&img=$img'><div align='center'><img src='$BASEURL/pic/multipage.gif' border='0' class=special /></a></div></td> <td><div align='center'><a href='$BASEURL/admin/category.php?delid=$id&cat=$name'><img src='$BASEURL/pic/warned2.gif' border='0' class=special align='center' /></a></div></td></tr>" ) ;
}

end_frame() ;
end_frame() ;
stdfoot() ;

?>