<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
header( "Content-type: image/png" ) ;
$string = $_GET['string'] ;
$nr = ( isset($_GET['nr']) and ! empty($_GET['nr']) ) ? $_GET['nr'] : '1' ;
$im = imagecreatefrompng( "../pic/ranks/button$nr.png" ) ;
$orange = imagecolorallocate( $im, 87, 180, 235 ) ;
$px = ( imagesx($im) - 7.5 * strlen($string) ) / 2 ;
imagestring( $im, 3, $px, 3, $string, $orange ) ;
imagepng( $im ) ;
imagedestroy( $im ) ;
?>
