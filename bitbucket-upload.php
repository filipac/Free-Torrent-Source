<?php
require "include/bittorrent.php" ;
lang::load( "bitbucket-upload" ) ;

loggedinorreturn() ;

parked() ;
$maxfilesize = 2097152 ;
$imgtypes = array( null, 'gif', 'jpg', 'png', 'GIF', 'JPG', 'PNG' ) ;
$scaleh = 150 ; // set our height size desired
$scalew = 150 ; // set our width size desired

if ( $_SERVER["REQUEST_METHOD"] == "POST" )
{
    $file = $_FILES["file"] ;
    if ( ! isset($file) || $file["size"] < 1 )
        stderr( str1, str2 ) ;
    if ( $file["size"] > $maxfilesize )
        stderr( str1, str3 ) ;
    $pp = pathinfo( $filename = $file["name"] ) ;
    if ( $pp['basename'] != $filename )
        stderr( str1, str4 ) ;
    $tgtfile = "$bitbucket/$filename" ;
    if ( file_exists($tgtfile) )
        stderr( str1, sprintf(str5, htmlspecialchars($filename)), false ) ;

    $size = getimagesize( $file["tmp_name"] ) ;
    $height = $size[1] ;
    $width = $size[0] ;
    $it = $size[2] ;
    if ( $imgtypes[$it] == null || strtolower($imgtypes[$it]) != strtolower($pp['extension']) )
        stderr( str1, str6, false ) ;

    // Scale image to appropriate avatar dimensions
    $hscale = $height / $scaleh ;
    $wscale = $width / $scalew ;
    $scale = ( $hscale < 1 && $wscale < 1 ) ? 1 : ( $hscale > $wscale ) ? $hscale :
        $wscale ;
    $newwidth = floor( $width / $scale ) ;
    $newheight = floor( $height / $scale ) ;
    $orig = ( $it == 1 ) ? @imagecreatefromgif( $file["tmp_name"] ) : ( $it == 2 ) ?
        @imagecreatefromjpeg( $file["tmp_name"] ) : @imagecreatefrompng( $file["tmp_name"] ) ;
    if ( ! $orig )
        stderr( str7, sprintf(str8, $imgtypes[$it]) ) ;
    $thumb = imagecreatetruecolor( $newwidth, $newheight ) ;
    imagecopyresized( $thumb, $orig, 0, 0, 0, 0, $newwidth, $newheight, $width, $height ) ;
    $ret = ( $it == 1 ) ? imagegif( $thumb, $tgtfile ) : ( $it == 2 ) ? imagejpeg( $thumb,
        $tgtfile ) : imagepng( $thumb, $tgtfile ) ;

    $url = str_replace( " ", "%20", htmlspecialchars("$BASEURL/fts-contents/bitbucket/$filename") ) ;
    $name = sqlesc( $filename ) ;
    $added = sqlesc( get_date_time() ) ;
    if ( $_POST['public'] != 'yes' )
        $public = '"0"' ;
    else
        $public = '"1"' ;
    sql_query( "INSERT INTO bitbucket (owner, name, added, public) VALUES ($CURUSER[id], $name, $added, $public)" ) or
        sqlerr( __file__, __line__ ) ;
    #sql_query( "UPDATE users SET avatar = " . sqlesc($url) . " WHERE id = $CURUSER[id]" ) or
       # sqlerr( __file__, __line__ ) ;
    stderr( str9, sprintf(str10, $url, $url, '<a href=bitbucket-upload.php>') .
        "<br><br><img src=$url border=0><br><br>" . str11 . " " . ($width = $newwidth &&
        $height == $newheight ? str12 : sprintf(str13, $height, $width, $newheight, $newwidth)) .
        '.' . '', false ) ;
}
stdhead( str15 ) ;
?>
<h1><?= str15 ?></h1>
<form method="post" action="<?= $_SERVER[SCRIPT_NAME] ; ?>" enctype="multipart/form-data">
<?php
$disclaimer = sprintf( str16,
    $scaleh, $scalew ) . "
<br><br>" . sprintf( str17, number_format($maxfilesize) ) . "
" ;
collapses('bitbucket-rules','Rules');
print ( "$disclaimer" ) ;
collapsee();echo _br;
collapses('bitbucket-upload','Upload Form');
?>
<table border=0 cellspacing=0 cellpadding=5 width=100% style="border:0px;">
<?php
if ( ! is_writable("$bitbucket") )
    print ( "<tr style=\"border:0px;\"><td class=rowhead colspan=2><div class=alert>" . str18 .
        "</div></tr></td>" ) ;
?>
<tr style="border:0px;"><td class=rowhead style="border:0px;">File</td><td style="border:0px;"><input type="file" name="file" size="60"></td></tr>
<tr style="border:0px;"><td style="border:0px;" colspan=2 align=center><input type=checkbox name=public value=yes><?= str19 ?><input type="submit" value="<?= str20 ?>"></td></tr>
</table>
</form>
<?php
collapsee();
stdfoot() ;
?>